document.addEventListener("DOMContentLoaded", function () {
    const contenedor = document.getElementById("tr-app");

    if (!contenedor || typeof L === "undefined") {
        return;
    }

    // 1. Configuración de idioma español para Leaflet Routing Machine
    if (L.Routing && L.Routing.Localization) {
        L.Routing.Localization["es"] = {
            directions: {
                N: "norte",
                NE: "noreste",
                E: "este",
                SE: "sureste",
                S: "sur",
                SW: "suroeste",
                W: "oeste",
                NW: "noroeste",
                SlightRight: "levemente a la derecha",
                Right: "a la derecha",
                SharpRight: "pronunciado a la derecha",
                SlightLeft: "levemente a la izquierda",
                Left: "a la izquierda",
                SharpLeft: "pronunciado a la izquierda",
                Uturn: "cambio de sentido"
            },
            instructions: {
                Head: ["Dirígete al {dir}", " por {path}"],
                Continue: ["Continúa hacia el {dir}", " por {path}"],
                TurnAround: ["Da la vuelta"],
                WaypointReached: ["Punto intermedio alcanzado"],
                Roundabout: ["En la rotonda, toma la {exitStr} salida hacia {path}"],
                DestinationReached: ["Has llegado a tu destino"],
                Fork: ["En la bifurcación, toma {modifier} hacia {path}"],
                Merge: ["Incorpórate {modifier} a {path}"],
                OnRamp: ["Toma el ramal {modifier} hacia {path}"],
                OffRamp: ["Toma la salida {modifier} hacia {path}"],
                EndOfRoad: ["Al final de la calle, gira {modifier} hacia {path}"],
                Onto: "hacia {path}"
            },
            formatOrder: function (n) {
                return n + "ª";
            }
        };
    }

    // 2. Inicialización del mapa en Toledo
    const mapa = L.map("tr-mapa").setView([39.858, -4.023], 15);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mapa);

    // 3. Variables de estado
    const lugares = (typeof trDatos !== "undefined" && trDatos.lugares) ? trDatos.lugares : [];
    const osrmUrl = (typeof trDatos !== "undefined" && trDatos.osrmUrl) ? trDatos.osrmUrl : "http://localhost:5000/route/v1";
    const marcadores = new Map();

    let seleccionActual = null;
    let controlRuta = null;
    let puntoOrigen = null;   // { nombre, lat, lng, tipo, id }
    let puntoDestino = null;  // { nombre, lat, lng, tipo, id }
    let modoSeleccionMapa = null; // 'origen' | 'destino' | null

    // Referencias a elementos del DOM
    const resultados = document.getElementById("tr-resultados");
    const detalle = document.getElementById("tr-detalle");

    const tipoSelect = document.getElementById("tr-tipo");
    const zonaSelect = document.getElementById("tr-zona");
    const precioSelect = document.getElementById("tr-precio");
    const valoracionSelect = document.getElementById("tr-valoracion");

    const selectOrigen = document.getElementById("tr-ruta-origen");
    const selectDestino = document.getElementById("tr-ruta-destino");
    const btnInvertir = document.getElementById("tr-btn-invertir");
    const btnTrazar = document.getElementById("tr-btn-trazar");
    const btnLimpiar = document.getElementById("tr-btn-limpiar");
    const rutaInfo = document.getElementById("tr-ruta-info");

    // Icono personalizado para los puntos de ruta (A = verde, B = rojo)
    function crearIconoRuta(tipo, letra) {
        return L.divIcon({
            className: "tr-custom-marker",
            html: `<div class="tr-pin-ruta tr-pin-${tipo}"><span>${letra}</span></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -28]
        });
    }

    // Muestra u oculta mensajes de información / avisos de ruta
    function mostrarMensajeRuta(texto, esAlerta = false) {
        if (!rutaInfo) return;
        if (!texto) {
            rutaInfo.style.display = "none";
            rutaInfo.textContent = "";
            rutaInfo.classList.remove("tr-alerta");
            return;
        }
        rutaInfo.textContent = texto;
        rutaInfo.style.display = "block";
        if (esAlerta) {
            rutaInfo.classList.add("tr-alerta");
        } else {
            rutaInfo.classList.remove("tr-alerta");
        }
    }

    // 4. Población de los selectores de Origen y Destino
    function poblarSelectores() {
        if (!selectOrigen || !selectDestino) return;

        lugares.forEach(function (lugar) {
            const optOri = document.createElement("option");
            optOri.value = lugar.id;
            optOri.textContent = lugar.nombre;
            selectOrigen.appendChild(optOri);

            const optDest = document.createElement("option");
            optDest.value = lugar.id;
            optDest.textContent = lugar.nombre;
            selectDestino.appendChild(optDest);
        });
    }
    poblarSelectores();

    // 5. Cálculo y trazado de ruta con OSRM
    function trazarRuta() {
        if (!puntoOrigen || !puntoDestino) {
            if (!puntoOrigen && !puntoDestino) {
                mostrarMensajeRuta("Por favor, selecciona un origen y un destino.", true);
            } else if (!puntoOrigen) {
                mostrarMensajeRuta("Falta indicar el punto de origen.", true);
            } else {
                mostrarMensajeRuta("Falta indicar el punto de destino.", true);
            }
            return;
        }

        if (controlRuta) {
            mapa.removeControl(controlRuta);
            controlRuta = null;
        }

        mostrarMensajeRuta("Calculando mejor ruta con OSRM...");

        try {
            controlRuta = L.Routing.control({
                waypoints: [
                    L.latLng(puntoOrigen.lat, puntoOrigen.lng),
                    L.latLng(puntoDestino.lat, puntoDestino.lng)
                ],
                router: L.Routing.osrmv1({
                    serviceUrl: osrmUrl,
                    profile: "driving",
                    useHints: false,
                    language: "es"
                }),
                formatter: new L.Routing.Formatter({
                    language: "es",
                    units: "metric"
                }),
                routeWhileDragging: true,
                showAlternatives: true,
                collapsible: true,
                lineOptions: {
                    styles: [{ color: "#38bdf8", opacity: 0.9, weight: 6 }]
                },
                createMarker: function (i, wp) {
                    const esOrigen = (i === 0);
                    const punto = esOrigen ? puntoOrigen : puntoDestino;
                    const etiqueta = esOrigen ? "Origen" : "Destino";
                    const letra = esOrigen ? "A" : "B";
                    const clase = esOrigen ? "origen" : "destino";

                    return L.marker(wp.latLng, {
                        icon: crearIconoRuta(clase, letra),
                        draggable: true
                    }).bindPopup(`<b>${etiqueta}:</b> ${punto.nombre}`);
                }
            }).addTo(mapa);

            controlRuta.on("routesfound", function (e) {
                const ruta = e.routes[0];
                const distanciaKm = (ruta.summary.totalDistance / 1000).toFixed(1);
                const minutos = Math.round(ruta.summary.totalTime / 60);
                mostrarMensajeRuta(`✓ Ruta trazada: ${distanciaKm} km (~${minutos} min en coche).`);
            });

            controlRuta.on("routingerror", function (err) {
                console.error("Error al calcular ruta OSRM:", err);
                mostrarMensajeRuta("No se pudo calcular la ruta. Verifica que el contenedor OSRM está activo.", true);
            });

            controlRuta.on("waypointschanged", function (e) {
                // Si el usuario arrastra un marcador sobre el mapa, actualizamos las coordenadas
                const wps = e.waypoints;
                if (wps && wps.length >= 2) {
                    if (wps[0].latLng) {
                        puntoOrigen.lat = wps[0].latLng.lat;
                        puntoOrigen.lng = wps[0].latLng.lng;
                    }
                    if (wps[1].latLng) {
                        puntoDestino.lat = wps[1].latLng.lat;
                        puntoDestino.lng = wps[1].latLng.lng;
                    }
                }
            });

        } catch (error) {
            console.error(error);
            mostrarMensajeRuta("Error al inicializar la ruta.", true);
        }
    }

    // 6. Asignadores de Origen y Destino
    function establecerOrigen(punto, actualizarSelect = true) {
        puntoOrigen = punto;
        if (actualizarSelect && selectOrigen) {
            if (punto.id) {
                selectOrigen.value = String(punto.id);
            } else if (punto.tipo === "gps") {
                selectOrigen.value = "gps";
            } else {
                selectOrigen.value = "mapa";
            }
        }

        if (puntoDestino) {
            trazarRuta();
        } else {
            mostrarMensajeRuta(`🟢 Origen fijado: "${punto.nombre}". Ahora elige un destino.`);
        }
    }

    function establecerDestino(punto, actualizarSelect = true) {
        puntoDestino = punto;
        if (actualizarSelect && selectDestino) {
            if (punto.id) {
                selectDestino.value = String(punto.id);
            } else {
                selectDestino.value = "mapa";
            }
        }

        if (puntoOrigen) {
            trazarRuta();
        } else {
            mostrarMensajeRuta(`🏁 Destino fijado: "${punto.nombre}". Ahora elige un origen.`);
        }
    }

    // 7. Funcionalidad de Geolocalización (GPS del usuario)
    function activarGPS() {
        if (!navigator.geolocation) {
            mostrarMensajeRuta("Tu navegador no soporta geolocalización.", true);
            return;
        }

        mostrarMensajeRuta("Obteniendo tu ubicación por GPS...");

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const punto = {
                    tipo: "gps",
                    nombre: "Mi ubicación actual",
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude
                };
                establecerOrigen(punto, false);
                mapa.flyTo([punto.lat, punto.lng], 16);
            },
            function (err) {
                console.warn("Error geolocalización:", err);
                mostrarMensajeRuta("No se pudo obtener tu ubicación (permiso denegado o sin cobertura).", true);
                if (selectOrigen) selectOrigen.value = "";
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // 8. Eventos de los selectores
    if (selectOrigen) {
        selectOrigen.addEventListener("change", function () {
            const val = this.value;
            if (!val) {
                puntoOrigen = null;
                mostrarMensajeRuta("");
                return;
            }

            if (val === "gps") {
                activarGPS();
            } else if (val === "mapa") {
                modoSeleccionMapa = "origen";
                mostrarMensajeRuta("Haz clic en cualquier punto del mapa para fijar el ORIGEN.");
            } else {
                const lugar = lugares.find(l => l.id === Number(val));
                if (lugar) {
                    establecerOrigen({
                        id: lugar.id,
                        tipo: "lugar",
                        nombre: lugar.nombre,
                        lat: lugar.lat,
                        lng: lugar.lng
                    }, false);
                }
            }
        });
    }

    if (selectDestino) {
        selectDestino.addEventListener("change", function () {
            const val = this.value;
            if (!val) {
                puntoDestino = null;
                mostrarMensajeRuta("");
                return;
            }

            if (val === "mapa") {
                modoSeleccionMapa = "destino";
                mostrarMensajeRuta("Haz clic en cualquier punto del mapa para fijar el DESTINO.");
            } else {
                const lugar = lugares.find(l => l.id === Number(val));
                if (lugar) {
                    establecerDestino({
                        id: lugar.id,
                        tipo: "lugar",
                        nombre: lugar.nombre,
                        lat: lugar.lat,
                        lng: lugar.lng
                    }, false);
                }
            }
        });
    }

    // Botón Invertir Puntos (⇄)
    if (btnInvertir) {
        btnInvertir.addEventListener("click", function () {
            if (!puntoOrigen && !puntoDestino) {
                mostrarMensajeRuta("Selecciona al menos un punto para intercambiar.", true);
                return;
            }

            const temp = puntoOrigen;
            puntoOrigen = puntoDestino;
            puntoDestino = temp;

            // Actualizar selectores
            if (selectOrigen) {
                selectOrigen.value = puntoOrigen ? (puntoOrigen.id ? String(puntoOrigen.id) : (puntoOrigen.tipo || "mapa")) : "";
            }
            if (selectDestino) {
                selectDestino.value = puntoDestino ? (puntoDestino.id ? String(puntoDestino.id) : (puntoDestino.tipo || "mapa")) : "";
            }

            if (puntoOrigen && puntoDestino) {
                trazarRuta();
            } else {
                mostrarMensajeRuta("Puntos intercambiados.");
            }
        });
    }

    // Botón Calcular Ruta
    if (btnTrazar) {
        btnTrazar.addEventListener("click", trazarRuta);
    }

    // Botón Limpiar Ruta
    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", function () {
            if (controlRuta) {
                mapa.removeControl(controlRuta);
                controlRuta = null;
            }
            puntoOrigen = null;
            puntoDestino = null;
            modoSeleccionMapa = null;

            if (selectOrigen) selectOrigen.value = "";
            if (selectDestino) selectDestino.value = "";

            mostrarMensajeRuta("");
        });
    }

    // 9. Clic directo en el mapa para marcar origen o destino
    mapa.on("click", function (e) {
        const coords = e.latlng;

        // Si el usuario seleccionó "Clicar en el mapa" desde el selector:
        if (modoSeleccionMapa === "origen") {
            modoSeleccionMapa = null;
            establecerOrigen({
                tipo: "mapa",
                nombre: `Punto en mapa (${coords.lat.toFixed(4)}, ${coords.lng.toFixed(4)})`,
                lat: coords.lat,
                lng: coords.lng
            }, true);
            return;
        }

        if (modoSeleccionMapa === "destino") {
            modoSeleccionMapa = null;
            establecerDestino({
                tipo: "mapa",
                nombre: `Punto en mapa (${coords.lat.toFixed(4)}, ${coords.lng.toFixed(4)})`,
                lat: coords.lat,
                lng: coords.lng
            }, true);
            return;
        }

        // Si hace clic en un espacio libre del mapa, abre un popup interactivo
        const contenedorPopup = document.createElement("div");
        contenedorPopup.className = "tr-popup-click";

        const titulo = document.createElement("strong");
        titulo.textContent = "Punto en el mapa";

        const cajaBtns = document.createElement("div");
        cajaBtns.className = "tr-popup-btns";

        const btnPopOrigen = document.createElement("button");
        btnPopOrigen.type = "button";
        btnPopOrigen.className = "tr-btn-pop-origen";
        btnPopOrigen.textContent = "🟢 Salir desde aquí";
        btnPopOrigen.addEventListener("click", function () {
            mapa.closePopup();
            establecerOrigen({
                tipo: "mapa",
                nombre: `Punto (${coords.lat.toFixed(4)}, ${coords.lng.toFixed(4)})`,
                lat: coords.lat,
                lng: coords.lng
            }, true);
        });

        const btnPopDestino = document.createElement("button");
        btnPopDestino.type = "button";
        btnPopDestino.className = "tr-btn-pop-destino";
        btnPopDestino.textContent = "🏁 Llegar aquí";
        btnPopDestino.addEventListener("click", function () {
            mapa.closePopup();
            establecerDestino({
                tipo: "mapa",
                nombre: `Punto (${coords.lat.toFixed(4)}, ${coords.lng.toFixed(4)})`,
                lat: coords.lat,
                lng: coords.lng
            }, true);
        });

        cajaBtns.appendChild(btnPopOrigen);
        cajaBtns.appendChild(btnPopDestino);
        contenedorPopup.appendChild(titulo);
        contenedorPopup.appendChild(cajaBtns);

        L.popup()
            .setLatLng(coords)
            .setContent(contenedorPopup)
            .openOn(mapa);
    });

    // 10. Muestra la ficha del lugar seleccionado
    function mostrarDetalle(lugar) {
        seleccionActual = lugar.id;
        detalle.replaceChildren();

        const titulo = document.createElement("h2");
        titulo.textContent = lugar.nombre;

        const tipo = document.createElement("span");
        tipo.className = "tr-tipo";
        tipo.textContent = {
            turismo: "Lugar turístico",
            restaurante: "Restaurante",
            copas: "Bar de copas"
        }[lugar.tipo] || "Establecimiento";

        const direccion = document.createElement("p");
        direccion.textContent = "Dirección: " + lugar.direccion;

        const valoracion = document.createElement("p");
        valoracion.textContent = "Valoración: " + lugar.valoracion + " / 5";

        const precio = document.createElement("p");
        precio.textContent = lugar.precio === 0
            ? "Precio: gratis"
            : "Precio orientativo: " + lugar.precio + " €";

        const descripcion = document.createElement("p");
        descripcion.textContent = lugar.descripcion;

        // Botones rápidos para fijar como origen o destino
        const cajaAcciones = document.createElement("div");
        cajaAcciones.className = "tr-acciones-lugar";

        const btnFijarOrigen = document.createElement("button");
        btnFijarOrigen.type = "button";
        btnFijarOrigen.className = "tr-btn-origen";
        btnFijarOrigen.textContent = "🟢 Salir desde aquí";
        btnFijarOrigen.addEventListener("click", function () {
            establecerOrigen({
                id: lugar.id,
                tipo: "lugar",
                nombre: lugar.nombre,
                lat: lugar.lat,
                lng: lugar.lng
            }, true);
        });

        const btnFijarDestino = document.createElement("button");
        btnFijarDestino.type = "button";
        btnFijarDestino.className = "tr-btn-destino";
        btnFijarDestino.textContent = "🏁 Llegar aquí";
        btnFijarDestino.addEventListener("click", function () {
            establecerDestino({
                id: lugar.id,
                tipo: "lugar",
                nombre: lugar.nombre,
                lat: lugar.lat,
                lng: lugar.lng
            }, true);
        });

        cajaAcciones.appendChild(btnFijarOrigen);
        cajaAcciones.appendChild(btnFijarDestino);

        detalle.append(
            titulo,
            tipo,
            direccion,
            valoracion,
            precio,
            descripcion,
            cajaAcciones
        );

        const marcador = marcadores.get(lugar.id);
        if (marcador) {
            mapa.flyTo(marcador.getLatLng(), 16);
            marcador.openPopup();
        }
    }

    // 11. Crea los marcadores de los lugares turísticos
    lugares.forEach(function (lugar) {
        const marcador = L.marker([lugar.lat, lugar.lng]);
        marcador.bindPopup(`<b>${lugar.nombre}</b><br>${lugar.direccion}`);

        marcador.on("click", function () {
            mostrarDetalle(lugar);
        });

        marcadores.set(lugar.id, marcador);
    });

    // 12. Filtros de búsqueda
    function aplicarFiltros() {
        const tipo = tipoSelect ? tipoSelect.value : "todos";
        const zona = zonaSelect ? zonaSelect.value : "todas";
        const precio = precioSelect ? precioSelect.value : "todos";
        const valoracion = valoracionSelect ? Number(valoracionSelect.value) : 0;

        resultados.replaceChildren();

        let visibles = 0;
        let limites = [];
        const idsVisibles = new Set();

        lugares.forEach(function (lugar) {
            const coincideTipo = tipo === "todos" || lugar.tipo === tipo;
            const coincideZona = zona === "todas" || lugar.zona === zona;
            const coincidePrecio = precio === "todos" || lugar.precio <= Number(precio);
            const coincideValoracion = lugar.valoracion >= valoracion;

            const coincide = coincideTipo && coincideZona && coincidePrecio && coincideValoracion;
            const marcador = marcadores.get(lugar.id);

            if (coincide) {
                idsVisibles.add(lugar.id);
                marcador.addTo(mapa);
                visibles++;

                limites.push([lugar.lat, lugar.lng]);

                const boton = document.createElement("button");
                boton.type = "button";
                boton.className = "tr-lugar";
                boton.textContent = lugar.nombre;

                boton.addEventListener("click", function () {
                    mostrarDetalle(lugar);
                });

                resultados.appendChild(boton);
            } else {
                mapa.removeLayer(marcador);
            }
        });

        if (seleccionActual !== null && !idsVisibles.has(seleccionActual)) {
            seleccionActual = null;
            detalle.replaceChildren();

            const mensaje = document.createElement("p");
            mensaje.textContent = "Selecciona un lugar para ver su información y trazar rutas.";
            detalle.appendChild(mensaje);
        }

        if (limites.length > 0) {
            mapa.fitBounds(limites, {
                padding: [30, 30],
                maxZoom: 16
            });
        }

        if (visibles === 0) {
            const mensaje = document.createElement("p");
            mensaje.textContent = "No hay lugares con esos filtros.";
            resultados.appendChild(mensaje);
        }
    }

    const btnAplicar = document.getElementById("tr-aplicar");
    if (btnAplicar) {
        btnAplicar.addEventListener("click", aplicarFiltros);
    }

    // Inicialización de filtros y ajuste de mapa
    aplicarFiltros();

    window.setTimeout(function () {
        mapa.invalidateSize();
    }, 200);
});