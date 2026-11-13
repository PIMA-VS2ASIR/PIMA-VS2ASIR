<?php
/**
 * Plugin Name: Toledo Rutas
 * Description: Aplicación turística interactiva con mapa y establecimientos.
 * Version: 1.2.0
 * Author: Proyecto ASIR
 */

if (!defined('ABSPATH')) {
    exit;
}


/**
 * ==========================================================
 * DATOS DE LA APLICACIÓN
 * ==========================================================
 */

function tr_obtener_datos_aplicacion() {

    return array(

        'lugares' => array(
        array(
            'id' => 1,
            'nombre' => 'Catedral de Toledo',
            'tipo' => 'turismo',
            'zona' => 'centro',
            'precio' => 0,
            'valoracion' => 4.8,
            'lat' => 39.8579,
            'lng' => -4.0232,
            'descripcion' => 'Uno de los grandes monumentos del casco histórico de Toledo.',
            'direccion' => 'Plaza del Ayuntamiento, Toledo'
        ),
        array(
            'id' => 2,
            'nombre' => 'Plaza de Zocodover',
            'tipo' => 'turismo',
            'zona' => 'centro',
            'precio' => 0,
            'valoracion' => 4.7,
            'lat' => 39.8589,
            'lng' => -4.0226,
            'descripcion' => 'Una plaza emblemática y un buen punto de partida para explorar la ciudad.',
            'direccion' => 'Plaza de Zocodover, Toledo'
        ),
        array(
            'id' => 3,
            'nombre' => 'Restaurante Adolfo',
            'tipo' => 'restaurante',
            'zona' => 'centro',
            'precio' => 45,   // REVISAR precio
            'valoracion' => 4.5,   // REVISAR valoración
            'lat' => 39.85764,   // VERIFICAR coordenadas
            'lng' => -4.02447,   // VERIFICAR coordenadas
            'descripcion' => 'Restaurante clásico toledano de cocina de autor y cocina tradicional con bodega propia.',
            'direccion' => 'Calle Granada 6, Toledo'
        ),
        array(
            'id' => 4,
            'nombre' => 'Cervecería La Abadía',
            'tipo' => 'copas',
            'zona' => 'centro',
            'precio' => 15,   // REVISAR precio
            'valoracion' => 4.3,   // REVISAR valoración
            'lat' => 39.860000,   // VERIFICAR coordenadas
            'lng' => -4.022940,   // VERIFICAR coordenadas
            'descripcion' => 'Taberna con mucho ambiente en la plaza de San Nicolás, muy popular para tomar algo por la tarde-noche.',
            'direccion' => 'Plaza de San Nicolás 3, Toledo'
        ),
        array(
            'id' => 5,
            'nombre' => 'Parque de las Tres Culturas',
            'tipo' => 'naturaleza',
            'zona' => 'este',
            'precio' => 0,
            'valoracion' => 4.3,
            'lat' => 39.87322,   // VERIFICAR coordenadas
            'lng' => -4.03108,   // VERIFICAR coordenadas
            'descripcion' => 'Uno de los parques más amplios de Toledo, con zonas deportivas, auditorio y áreas de paseo.',
            'direccion' => 'Parque de las Tres Culturas, Toledo'
        ),
        array(
            'id' => 6,
            'nombre' => 'Cerro del Bú',
            'tipo' => 'naturaleza',
            'zona' => 'norte',
            'precio' => 0,
            'valoracion' => 4.4,
            'lat' => 39.85304,   // VERIFICAR coordenadas
            'lng' => -4.01872,   // VERIFICAR coordenadas
            'descripcion' => 'Cerro con vistas panorámicas de la ciudad, ideal para rutas a pie.',
            'direccion' => 'Cerro del Bú, Toledo'
        ),
        array(
            'id' => 7,
            'nombre' => 'Paseo de San Eugenio',
            'tipo' => 'naturaleza',
            'zona' => 'ribera',
            'precio' => 0,
            'valoracion' => 4.5,
            'lat' =>  39.8724758,   // VERIFICAR coordenadas
            'lng' => -4.0242174,   // VERIFICAR coordenadas
            'descripcion' => 'Paseo junto al Tajo, tranquilo y con buenas vistas del casco histórico.',
            'direccion' => 'Paseo de San Eugenio, Toledo'
        ),
        array(
            'id' => 8,
            'nombre' => 'Restaurante Locum',
            'tipo' => 'restaurante',
            'zona' => 'centro',
            'precio' => 35,   // REVISAR precio
            'valoracion' => 4.4,   // REVISAR valoración
            'lat' => 39.8565,   // VERIFICAR coordenadas
            'lng' => -4.0262,   // VERIFICAR coordenadas
            'descripcion' => 'Cocina toledana actualizada en pleno casco histórico.',
            'direccion' => 'Calle Locum 6, Toledo'
        ),
        array(
            'id' => 9,
            'nombre' => 'Alfileritos 24',
            'tipo' => 'restaurante',
            'zona' => 'centro',
            'precio' => 30,   // REVISAR precio
            'valoracion' => 4.4,   // REVISAR valoración
            'lat' => 39.8588,   // VERIFICAR coordenadas
            'lng' => -4.0252,   // VERIFICAR coordenadas
            'descripcion' => 'Restaurante de cocina de mercado en una de las calles principales del casco.',
            'direccion' => 'Calle Alfileritos 24, Toledo'
        ),
        array(
            'id' => 10,
            'nombre' => 'Bar Ludeña',
            'tipo' => 'copas',
            'zona' => 'centro',
            'precio' => 15,   // REVISAR precio
            'valoracion' => 4.2,   // REVISAR valoración
            'lat' => 39.8587,   // VERIFICAR coordenadas
            'lng' => -4.0262,   // VERIFICAR coordenadas
            'descripcion' => 'Bar de tapas y cañas con terraza en la plaza de la Magdalena.',
            'direccion' => 'Plaza de la Magdalena 10, Toledo'
        ),
        array(
            'id' => 11,
            'nombre' => 'Hotel Cardenal',
            'tipo' => 'hotel',
            'zona' => 'centro',
            'precio' => 90,   // REVISAR precio
            'valoracion' => 4.4,   // REVISAR valoración
            'lat' => 39.8628,   // VERIFICAR coordenadas
            'lng' => -4.0204,   // VERIFICAR coordenadas
            'descripcion' => 'Hotel histórico junto a la Puerta de Bisagra, con jardines.',
            'direccion' => 'Paseo de Recaredo 24, Toledo'
        ),
        array(
            'id' => 12,
            'nombre' => 'Hotel Santa Isabel',
            'tipo' => 'hotel',
            'zona' => 'centro',
            'precio' => 80,   // REVISAR precio
            'valoracion' => 4.3,   // REVISAR valoración
            'lat' => 39.8569,   // VERIFICAR coordenadas
            'lng' => -4.0249,   // VERIFICAR coordenadas
            'descripcion' => 'Hotel con encanto, a pocos pasos de la Catedral.',
            'direccion' => 'Calle Santa Isabel 24, Toledo'
        ),
        array(
            'id' => 13,
            'nombre' => 'Hotel Pintor El Greco',
            'tipo' => 'hotel',
            'zona' => 'centro',
            'precio' => 85,   // REVISAR precio
            'valoracion' => 4.3,   // REVISAR valoración
            'lat' => 39.8559,   // VERIFICAR coordenadas
            'lng' => -4.0280,   // VERIFICAR coordenadas
            'descripcion' => 'Hotel ubicado en el antiguo barrio judío, junto al Museo del Greco.',
            'direccion' => 'Calle Alamillos del Tránsito 13, Toledo'
        ),
        array(
            'id' => 14,
            'nombre' => 'Museo de Santa Cruz',
            'tipo' => 'museo',
            'zona' => 'centro',
            'precio' => 0,   // REVISAR precio
            'valoracion' => 4.5,   // REVISAR valoración
            'lat' => 39.8591,   // VERIFICAR coordenadas
            'lng' => -4.0205,   // VERIFICAR coordenadas
            'descripcion' => 'Museo de Bellas Artes y arqueología en un edificio renacentista junto a Zocodover.',
            'direccion' => 'Calle Miguel de Cervantes 3, Toledo'
        ),
        array(
            'id' => 15,
            'nombre' => 'Museo del Greco',
            'tipo' => 'museo',
            'zona' => 'centro',
            'precio' => 3,   // REVISAR precio
            'valoracion' => 4.4,   // REVISAR valoración
            'lat' => 39.8555,   // VERIFICAR coordenadas
            'lng' => -4.0285,   // VERIFICAR coordenadas
            'descripcion' => 'Museo dedicado al pintor y a la Toledo de su época, en el barrio judío.',
            'direccion' => 'Paseo del Tránsito s/n, Toledo'
        ),
        array(
            'id' => 16,
            'nombre' => 'Alcázar de Toledo (Museo del Ejército)',
            'tipo' => 'museo',
            'zona' => 'centro',
            'precio' => 5,   // REVISAR precio
            'valoracion' => 4.5,   // REVISAR valoración
            'lat' => 39.8581,   // VERIFICAR coordenadas
            'lng' => -4.0206,   // VERIFICAR coordenadas
            'descripcion' => 'Fortaleza que domina el casco histórico y alberga el Museo del Ejército.',
            'direccion' => 'Calle Unión 2, Toledo'
        ),
        array(
            'id' => 17,
            'nombre' => 'Monasterio de San Juan de los Reyes',
            'tipo' => 'turismo',
            'zona' => 'centro',
            'precio' => 3,   // REVISAR precio
            'valoracion' => 4.6,   // REVISAR valoración
            'lat' => 39.8577,   // VERIFICAR coordenadas
            'lng' => -4.0316,   // VERIFICAR coordenadas
            'descripcion' => 'Monasterio franciscano de estilo gótico isabelino con un claustro espectacular.',
            'direccion' => 'Calle de los Reyes Católicos 17, Toledo'
        )
    ),

        /*
         * URL base de nuestro proxy.
         *
         * Ya no usamos:
         * http://localhost:8080
         *
         * WordPress genera automáticamente la URL correcta.
         */
        'osrmUrl' => untrailingslashit(
            rest_url('toledo-rutas/v1/enrutar/route/v1')
        )
    );
}


/**
 * ==========================================================
 * CARGA DE CSS Y JAVASCRIPT
 * ==========================================================
 */

function tr_cargar_recursos() {

    /*
     * CDN correcta para Leaflet y Leaflet Routing Machine.
     */
    $domain = 'https://cdnjs.cloudflare.com';


    /*
     * Leaflet.
     */
    $leaflet_css =
        $domain .
        '/ajax/libs/leaflet/1.9.4/leaflet.css';

    $leaflet_js =
        $domain .
        '/ajax/libs/leaflet/1.9.4/leaflet.js';


    /*
     * Leaflet Routing Machine.
     */
    $lrm_css =
        $domain .
        '/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.css';

    $lrm_js =
        $domain .
        '/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.js';


    /*
     * Leaflet CSS.
     */
    wp_enqueue_style(
        'tr-leaflet',
        $leaflet_css,
        array(),
        '1.9.4'
    );


    /*
     * Leaflet JS.
     */
    wp_enqueue_script(
        'tr-leaflet',
        $leaflet_js,
        array(),
        '1.9.4',
        true
    );


    /*
     * Leaflet Routing Machine CSS.
     */
    wp_enqueue_style(
        'tr-lrm-css',
        $lrm_css,
        array('tr-leaflet'),
        '3.2.12'
    );


    /*
     * Leaflet Routing Machine JS.
     */
    wp_enqueue_script(
        'tr-lrm-js',
        $lrm_js,
        array('tr-leaflet'),
        '3.2.12',
        true
    );


    /*
     * CSS de nuestra aplicación.
     */
    wp_enqueue_style(
        'tr-app',
        plugin_dir_url(__FILE__) . 'assets/css/app.css',
        array(
            'tr-leaflet',
            'tr-lrm-css'
        ),
        '1.2.0'
    );


    /*
     * JavaScript de nuestra aplicación.
     */
    wp_enqueue_script(
        'tr-app',
        plugin_dir_url(__FILE__) . 'assets/js/app.js',
        array(
            'tr-leaflet',
            'tr-lrm-js'
        ),
        '1.2.0',
        true
    );


    /*
     * Datos PHP que necesita app.js.
     */
    $datos = tr_obtener_datos_aplicacion();


    /*
     * Creamos la variable global:
     *
     * trDatos
     *
     * antes de cargar app.js.
     */
    wp_add_inline_script(
        'tr-app',
        'var trDatos = ' . wp_json_encode($datos) . ';',
        'before'
    );
}

add_action(
    'wp_enqueue_scripts',
    'tr_cargar_recursos'
);


/**
 * ==========================================================
 * SHORTCODE
 * ==========================================================
 *
 * Uso:
 *
 * [toledo_rutas]
 */

function tr_mostrar_aplicacion() {

    ob_start();

    ?>

    <div id="tr-app">

        <!-- ==================================================
             FILTROS
        =================================================== -->

        <aside class="tr-filtros">

            <h2>Explora Toledo</h2>


            <label for="tr-tipo">
                Tipo de ruta
            </label>

            <select id="tr-tipo">

                <option value="todos">
                    Todos los lugares
                </option>

                <option value="turismo">
                    Rutas turísticas
                </option>

                <option value="naturaleza">
                    Parques y paseos
                </option>

                <option value="restaurante">
                    Restaurantes
                </option>

                <option value="hotel">
                    Hoteles
                </option>

                <option value="museo">
                    Museos
                </option>

                <option value="copas">
                    Bares de copas
                </option>

            </select>


            <label for="tr-zona">
                Zona
            </label>

            <select id="tr-zona">

                <option value="todas">
                    Todas las zonas
                </option>

                <option value="centro">
                    Casco histórico
                </option>

                <option value="este">
                    Zona este
                </option>

                <option value="norte">
                    Zona norte
                </option>

                <option value="ribera">
                    Ribera del Tajo
                </option>

            </select>


            <label for="tr-precio">
                Precio máximo
            </label>

            <select id="tr-precio">

                <option value="todos">
                    Cualquier precio
                </option>

                <option value="0">
                    Gratis
                </option>

                <option value="15">
                    Hasta 15 €
                </option>

                <option value="25">
                    Hasta 25 €
                </option>

                <option value="50">
                    Hasta 50 €
                </option>

                <option value="100">
                    Hasta 100 €
                </option>

            </select>


            <label for="tr-valoracion">
                Valoración mínima
            </label>

            <select id="tr-valoracion">

                <option value="0">
                    Cualquiera
                </option>

                <option value="4">
                    4 estrellas
                </option>

                <option value="4.5">
                    4,5 estrellas
                </option>

            </select>


            <button
                type="button"
                id="tr-aplicar">

                Aplicar filtros

            </button>


            <h3>
                Lugares encontrados
            </h3>


            <div id="tr-resultados"></div>

        </aside>


        <!-- ==================================================
             MAPA
        =================================================== -->

        <section class="tr-mapa-contenedor">

            <div
                id="tr-mapa"
                style="width: 100%; height: 500px; min-height: 500px;"
                aria-label="Mapa turístico de Toledo">
            </div>

        </section>


        <!-- ==================================================
             PANEL DERECHO
        =================================================== -->

        <aside
            class="tr-ficha"
            aria-live="polite">


            <!-- ==============================================
                 PLANIFICADOR DE RUTA
            =============================================== -->

            <div
                id="tr-planificador"
                class="tr-modulo-ruta">


                <h2>
                    🧭 Planificador de Ruta
                </h2>


                <!-- MODO -->

                <div class="tr-grupo-campo">

                    <label for="tr-ruta-modo">
                        🚲 Modo de Transporte
                    </label>

                    <select id="tr-ruta-modo">

                        <option value="driving">
                            🚗 Coche
                        </option>

                        <option value="bicycle">
                            🚲 Bicicleta
                        </option>

                        <option value="foot">
                            🚶 Caminando
                        </option>

                    </select>

                </div>


                <!-- ORIGEN -->

                <div class="tr-grupo-campo">

                    <label for="tr-ruta-origen">
                        🟢 Punto de Origen
                    </label>

                    <select id="tr-ruta-origen">

                        <option value="">
                            -- Elige punto de origen --
                        </option>

                        <option value="gps">
                            📍 Mi ubicación actual (GPS)
                        </option>

                        <option value="mapa">
                            🗺️ Clicar punto en el mapa...
                        </option>

                    </select>

                </div>


                <!-- INVERTIR -->

                <div class="tr-fila-invertir">

                    <button
                        type="button"
                        id="tr-btn-invertir"
                        class="tr-btn-secundario"
                        title="Intercambiar origen y destino">

                        ⇅ Invertir puntos

                    </button>

                </div>


                <!-- DESTINO -->

                <div class="tr-grupo-campo">

                    <label for="tr-ruta-destino">
                        🏁 Punto de Destino
                    </label>

                    <select id="tr-ruta-destino">

                        <option value="">
                            -- Elige punto de destino --
                        </option>

                        <option value="gps">
                            📍 Mi ubicación actual (GPS)
                        </option>

                        <option value="mapa">
                            🗺️ Clicar punto en el mapa...
                        </option>

                    </select>

                </div>


                <!-- BOTONES -->

                <div class="tr-acciones-ruta">

                    <button
                        type="button"
                        id="tr-btn-trazar"
                        class="tr-btn-principal">

                        🧭 Calcular Ruta

                    </button>


                    <button
                        type="button"
                        id="tr-btn-limpiar"
                        class="tr-btn-peligro"
                        title="Borrar la ruta">

                        ✕ Limpiar

                    </button>

                </div>


                <!-- INFORMACIÓN DE LA RUTA -->

                <div
                    id="tr-ruta-info"
                    class="tr-ruta-info"
                    style="display: none;">
                </div>

            </div>


            <hr class="tr-separador">


            <!-- ==============================================
                 FICHA DEL LUGAR
            =============================================== -->

            <div id="tr-detalle">

                <h2>
                    Selecciona un lugar
                </h2>

                <p>
                    Pulsa un marcador del mapa para ver su información
                    y trazar rutas hacia o desde él.
                </p>

            </div>

        </aside>

    </div>

    <?php

    return ob_get_clean();
}


add_shortcode(
    'toledo_rutas',
    'tr_mostrar_aplicacion'
);


/**
 * ==========================================================
 * PROXY OSRM
 * ==========================================================
 *
 * Recibe las peticiones de Leaflet Routing Machine
 * y las envía al contenedor OSRM correspondiente.
 */

function tr_registrar_proxy_osrm() {

    register_rest_route(

        'toledo-rutas/v1',

        '/enrutar/route/v1/(?P<modo>driving|bicycle|foot)/(?P<coordenadas>[^/]+)',

        array(

            'methods' => 'GET',

            'callback' => 'tr_callback_proxy_osrm',

            'permission_callback' => '__return_true'

        )
    );
}


add_action(
    'rest_api_init',
    'tr_registrar_proxy_osrm'
);


/**
 * Callback del proxy OSRM.
 */

function tr_callback_proxy_osrm($request) {


    /**
     * ------------------------------------------------------
     * 1. MODO DE TRANSPORTE
     * ------------------------------------------------------
     */

    $modo = sanitize_text_field(
        $request->get_param('modo')
    );


    /**
     * ------------------------------------------------------
     * 2. COORDENADAS
     * ------------------------------------------------------
     *
     * Ejemplo:
     *
     * -4.0232,39.8579;-4.0226,39.8589
     */

    $coordenadas = sanitize_text_field(
        $request->get_param('coordenadas')
    );


    /**
     * ------------------------------------------------------
     * 3. CONTENEDORES OSRM
     * ------------------------------------------------------
     */

    $aliases = array(

        'driving' =>
            'http://osrm-car:5000',

        'bicycle' =>
            'http://osrm-bike:5000',

        'foot' =>
            'http://osrm-foot:5000'

    );


    /**
     * ------------------------------------------------------
     * 4. VALIDAR EL MODO
     * ------------------------------------------------------
     */

    if (!isset($aliases[$modo])) {

        return new WP_REST_Response(

            array(
                'error' =>
                    'Modo de transporte no válido'
            ),

            400
        );
    }


    /**
     * ------------------------------------------------------
     * 5. VALIDAR COORDENADAS
     * ------------------------------------------------------
     */

    if (empty($coordenadas)) {

        return new WP_REST_Response(

            array(
                'error' =>
                    'No se han recibido coordenadas'
            ),

            400
        );
    }


    /*
     * Validar que las coordenadas tengan el formato:
     * longitud,latitud;longitud,latitud
     */
    $pares = explode(';', $coordenadas);

    if (count($pares) < 2 || count($pares) > 25) {

        return new WP_REST_Response(

            array(
                'error' =>
                    'Número de coordenadas no válido'
            ),

            400
        );
    }


    foreach ($pares as $par) {

        $coordenada = explode(',', $par);

        if (
            count($coordenada) !== 2 ||
            !is_numeric($coordenada[0]) ||
            !is_numeric($coordenada[1])
        ) {

            return new WP_REST_Response(

                array(
                    'error' =>
                        'Formato de coordenadas no válido'
                ),

                400
            );
        }


        $lng = (float) $coordenada[0];
        $lat = (float) $coordenada[1];


        if (
            $lng < -180 ||
            $lng > 180 ||
            $lat < -90 ||
            $lat > 90
        ) {

            return new WP_REST_Response(

                array(
                    'error' =>
                        'Coordenadas fuera de rango'
                ),

                400
            );
        }
    }


    /**
     * ------------------------------------------------------
     * 6. SELECCIONAR CONTENEDOR
     * ------------------------------------------------------
     */

    $base_url = $aliases[$modo];


    /**
     * ------------------------------------------------------
     * 7. PARÁMETROS OSRM
     * ------------------------------------------------------
     */

    $parametros = array(

        'alternatives' =>
            $request->get_param('alternatives')
                ?: 'false',

        'steps' =>
            $request->get_param('steps')
                ?: 'true',

        'geometries' =>
            $request->get_param('geometries')
                ?: 'polyline',

        'overview' =>
            $request->get_param('overview')
                ?: 'full'

    );


    /**
     * ------------------------------------------------------
     * 8. CONSTRUIR URL INTERNA DE OSRM
     * ------------------------------------------------------
     *
     * Ejemplo coche:
     *
     * http://osrm-car:5000/
     * route/v1/driving/
     * -4.0232,39.8579;-4.0226,39.8589
     */

    $url_final =
        $base_url .
        '/route/v1/' .
        $modo .
        '/' .
        $coordenadas .
        '?' .
        http_build_query($parametros);


    /**
     * ------------------------------------------------------
     * 9. PETICIÓN A OSRM
     * ------------------------------------------------------
     */

    $max_intentos = 3;
    $respuesta = null;


    for (
        $intento = 1;
        $intento <= $max_intentos;
        $intento++
    ) {

        $respuesta = wp_remote_get(

            $url_final,

            array(
                'timeout' => 15
            )
        );


        if (!is_wp_error($respuesta)) {
            break;
        }


        if ($intento < $max_intentos) {

            /*
             * Esperamos 250 ms antes de volver a intentarlo.
             * Esto ayuda si OSRM todavía está terminando de arrancar.
             */
            usleep(250000);
        }
    }


    /**
     * ------------------------------------------------------
     * 10. ERROR DE CONEXIÓN
     * ------------------------------------------------------
     */

    if (is_wp_error($respuesta)) {

        return new WP_REST_Response(

            array(
                'error' =>
                    'No se pudo conectar con el motor OSRM'
            ),

            502
        );
    }


    /**
     * ------------------------------------------------------
     * 11. CÓDIGO HTTP DE OSRM
     * ------------------------------------------------------
     */

    $codigo_http =
        wp_remote_retrieve_response_code(
            $respuesta
        );


    /**
     * ------------------------------------------------------
     * 12. RESPUESTA JSON
     * ------------------------------------------------------
     */

    $cuerpo =
        wp_remote_retrieve_body(
            $respuesta
        );


    $datos =
        json_decode(
            $cuerpo,
            true
        );


    /**
     * ------------------------------------------------------
     * 13. COMPROBAR JSON
     * ------------------------------------------------------
     */

    if ($datos === null) {

        return new WP_REST_Response(

            array(
                'error' =>
                    'OSRM devolvió una respuesta no válida'
            ),

            502
        );
    }


    /**
     * ------------------------------------------------------
     * 14. DEVOLVER RESPUESTA A LEAFLET
     * ------------------------------------------------------
     */

    return new WP_REST_Response(
        $datos,
        $codigo_http
    );
}


/**
 * ==========================================================
 * ACTIVACIÓN DEL PLUGIN
 * ==========================================================
 */

function tr_activar_plugin_rutas() {

    /*
     * Registramos temporalmente la ruta REST.
     */
    tr_registrar_proxy_osrm();


    /*
     * Actualizamos las reglas de WordPress.
     */
    flush_rewrite_rules();
}


register_activation_hook(
    __FILE__,
    'tr_activar_plugin_rutas'
);