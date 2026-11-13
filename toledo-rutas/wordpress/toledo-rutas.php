<?php
/**
 * Plugin Name: Toledo Rutas
 * Description: Aplicación turística interactiva con mapa y establecimientos.
 * Version: 1.0.0
 * Author: Proyecto ASIR
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Carga los estilos y scripts de nuestra aplicación.
 */
function tr_cargar_recursos() {
    // 1. Leaflet Base
    wp_enqueue_style(
        'tr-leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        array(),
        '1.9.4'
    );

    wp_enqueue_script(
        'tr-leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
        array(),
        '1.9.4',
        true
    );

    // 2. Leaflet Routing Machine (para conectar con OSRM)
    wp_enqueue_style(
        'tr-lrm-css',
        'https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css',
        array('tr-leaflet'),
        '3.2.12'
    );

    wp_enqueue_script(
        'tr-lrm-js',
        'https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js',
        array('tr-leaflet'),
        '3.2.12',
        true
    );

    // 3. Estilos y lógica propios
    wp_enqueue_style(
        'tr-app',
        plugin_dir_url(__FILE__) . 'assets/css/app.css',
        array('tr-leaflet', 'tr-lrm-css'),
        '1.0.0'
    );

    wp_enqueue_script(
        'tr-app',
        plugin_dir_url(__FILE__) . 'assets/js/app.js',
        array('tr-leaflet', 'tr-lrm-js'),
        '1.0.0',
        true
    );

    $lugares = array(
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
            'nombre' => 'Restaurante de prueba',
            'tipo' => 'restaurante',
            'zona' => 'centro',
            'precio' => 25,
            'valoracion' => 4.3,
            'lat' => 39.8569,
            'lng' => -4.0250,
            'descripcion' => 'Establecimiento ficticio para probar los filtros y la ficha del mapa.',
            'direccion' => 'Casco histórico, Toledo'
        ),
        array(
            'id' => 4,
            'nombre' => 'Bar de prueba',
            'tipo' => 'copas',
            'zona' => 'centro',
            'precio' => 15,
            'valoracion' => 4.1,
            'lat' => 39.8600,
            'lng' => -4.0208,
            'descripcion' => 'Bar ficticio para practicar las rutas de ocio nocturno.',
            'direccion' => 'Centro, Toledo'
        ),
        array(
            'id' => 5,
            'nombre' => 'Estación de Tren (AVE Toledo)',
            'tipo' => 'turismo',
            'zona' => 'centro',
            'precio' => 0,
            'valoracion' => 4.6,
            'lat' => 39.8628,
            'lng' => -4.0192,
            'descripcion' => 'Estación ferroviaria histórica de Toledo, un punto de partida habitual.',
            'direccion' => 'Paseo de la Rosa, s/n, Toledo'
        ),
        array(
            'id' => 6,
            'nombre' => 'Mirador del Valle',
            'tipo' => 'turismo',
            'zona' => 'centro',
            'precio' => 0,
            'valoracion' => 4.9,
            'lat' => 39.8492,
            'lng' => -4.0256,
            'descripcion' => 'La panorámica más emblemática y fotografiada de la ciudad y el río Tajo.',
            'direccion' => 'Ctra. de Circunvalación, Toledo'
        )
    );

    // Pasamos tanto los lugares como la URL de tu contenedor OSRM
    wp_localize_script(
        'tr-app',
        'trDatos',
        array(
            'lugares' => $lugares,
            'osrmUrl' => 'http://localhost:5000/route/v1' // Endpoint del contenedor Docker de OSRM
        )
    );
}
add_action('wp_enqueue_scripts', 'tr_cargar_recursos');

/**
 * Shortcode que muestra la aplicación turística.
 */
function tr_mostrar_aplicacion() {
    ob_start();
    ?>
    <div id="tr-app">
        <aside class="tr-filtros">
            <h2>Explora Toledo</h2>

            <label for="tr-tipo">Tipo de ruta</label>
            <select id="tr-tipo">
                <option value="todos">Todos los lugares</option>
                <option value="turismo">Rutas turísticas</option>
                <option value="restaurante">Restaurantes</option>
                <option value="copas">Bares de copas</option>
            </select>

            <label for="tr-zona">Zona</label>
            <select id="tr-zona">
                <option value="todas">Todas las zonas</option>
                <option value="centro">Casco histórico</option>
            </select>

            <label for="tr-precio">Precio máximo</label>
            <select id="tr-precio">
                <option value="todos">Cualquier precio</option>
                <option value="0">Gratis</option>
                <option value="15">Hasta 15 €</option>
                <option value="25">Hasta 25 €</option>
            </select>

            <label for="tr-valoracion">Valoración mínima</label>
            <select id="tr-valoracion">
                <option value="0">Cualquiera</option>
                <option value="4">4 estrellas</option>
                <option value="4.5">4,5 estrellas</option>
            </select>

            <button type="button" id="tr-aplicar">
                Aplicar filtros
            </button>

            <h3>Lugares encontrados</h3>
            <div id="tr-resultados"></div>
        </aside>

        <section class="tr-mapa-contenedor">
            <div id="tr-mapa" aria-label="Mapa turístico de Toledo"></div>
        </section>

        <aside class="tr-ficha" aria-live="polite">
            <div id="tr-planificador" class="tr-modulo-ruta">
                <h2>🧭 Planificador de Ruta</h2>

                <div class="tr-grupo-campo">
                    <label for="tr-ruta-origen">🟢 Punto de Origen</label>
                    <select id="tr-ruta-origen">
                        <option value="">-- Elige punto de origen --</option>
                        <option value="gps">📍 Mi ubicación actual (GPS)</option>
                        <option value="mapa">🗺️ Clicar punto en el mapa...</option>
                    </select>
                </div>

                <div class="tr-fila-invertir">
                    <button type="button" id="tr-btn-invertir" class="tr-btn-secundario" title="Intercambiar origen y destino">
                        ⇅ Invertir puntos
                    </button>
                </div>

                <div class="tr-grupo-campo">
                    <label for="tr-ruta-destino">🏁 Punto de Destino</label>
                    <select id="tr-ruta-destino">
                        <option value="">-- Elige punto de destino --</option>
                        <option value="mapa">🗺️ Clicar punto en el mapa...</option>
                    </select>
                </div>

                <div class="tr-acciones-ruta">
                    <button type="button" id="tr-btn-trazar" class="tr-btn-principal">🚗 Calcular Ruta</button>
                    <button type="button" id="tr-btn-limpiar" class="tr-btn-peligro" title="Borrar la ruta">✕ Limpiar</button>
                </div>

                <div id="tr-ruta-info" class="tr-ruta-info" style="display: none;"></div>
            </div>

            <hr class="tr-separador">

            <div id="tr-detalle">
                <h2>Selecciona un lugar</h2>
                <p>Pulsa un marcador del mapa para ver su información y trazar rutas hacia o desde él.</p>
            </div>
        </aside>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('toledo_rutas', 'tr_mostrar_aplicacion');