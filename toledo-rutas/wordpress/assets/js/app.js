document.addEventListener("DOMContentLoaded", function () {
    const contenedor = document.getElementById("tr-app");

    // Si esta página no contiene nuestra aplicación, no hacemos nada.
    if (!contenedor || typeof L === "undefined") {
        return;
    }

    const mapa = L.map("tr-mapa").setView(
        [39.858, -4.023],
        15
    );

    // Mapa base de OpenStreetMap.
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mapa);

    const lugares = trDatos.lugares;
    const marcadores = new Map();
    let seleccionActual = null;

    const resultados = document.getElementById("tr-resultados");
    const detalle = document.getElementById("tr-detalle");

    const tipoSelect = document.getElementById("tr-tipo");
    const zonaSelect = document.getElementById("tr-zona");
    const precioSelect = document.getElementById("tr-precio");
    const valoracionSelect = document.getElementById("tr-valoracion");

    // Muestra la ficha del lugar seleccionado.
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

        detalle.append(
            titulo,
            tipo,
            direccion,
            valoracion,
            precio,
            descripcion
        );

        const marcador = marcadores.get(lugar.id);

        if (marcador) {
            mapa.flyTo(marcador.getLatLng(), 17);
            marcador.openPopup();
        }
    }

    // Crea un marcador por cada establecimiento.
    lugares.forEach(function (lugar) {
        const marcador = L.marker([lugar.lat, lugar.lng]);

        marcador.bindPopup(lugar.nombre);

        marcador.on("click", function () {
            mostrarDetalle(lugar);
        });

        marcadores.set(lugar.id, marcador);
    });

    // Aplica los filtros y actualiza el mapa y la lista.
    function aplicarFiltros() {
        const tipo = tipoSelect.value;
        const zona = zonaSelect.value;
        const precio = precioSelect.value;
        const valoracion = Number(valoracionSelect.value);

        resultados.replaceChildren();

        let visibles = 0;
        let limites = [];
        const idsVisibles = new Set();

        lugares.forEach(function (lugar) {
            const coincideTipo =
                tipo === "todos" || lugar.tipo === tipo;

            const coincideZona =
                zona === "todas" || lugar.zona === zona;

            const coincidePrecio =
                precio === "todos" ||
                lugar.precio <= Number(precio);

            const coincideValoracion =
                lugar.valoracion >= valoracion;

            const coincide =
                coincideTipo &&
                coincideZona &&
                coincidePrecio &&
                coincideValoracion;

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

        if (
            seleccionActual !== null &&
            !idsVisibles.has(seleccionActual)
        ) {
            seleccionActual = null;
            detalle.replaceChildren();

            const mensaje = document.createElement("p");
            mensaje.textContent ="Selecciona un lugar para ver su información.";

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

    document.getElementById("tr-aplicar").addEventListener(
        "click",
        aplicarFiltros
    );

    // Mostramos todos los establecimientos al iniciar.
    aplicarFiltros();

    // Leaflet necesita recalcular el tamaño cuando cambia el diseño.
    window.setTimeout(function () {
        mapa.invalidateSize();
    }, 200);
});