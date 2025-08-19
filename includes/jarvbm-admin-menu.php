<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function JARVBM_render_admin_page() {
    ?>
    <div class="wrap jarvbm-wrap">
        <h1 class="jarvbm-title">📦 JARV Barcodes Manager</h1>
        <p class="jarvbm-subtitle">Gestor y generador de códigos de barra EAN-13 para clientes y proyectos internos.</p>

        <nav class="nav-tab-wrapper jarvbm-tabs">
            <a href="#general" class="nav-tab nav-tab-active" data-tab="general">⚙️ General</a>
            <a href="#generate" class="nav-tab" data-tab="generate">➕ Generar Códigos</a>
            <a href="#list" class="nav-tab" data-tab="list">📋 Lista de Códigos</a>
        </nav>

        <div class="jarvbm-tab-content">

            <!-- TAB: GENERAL -->
            <div id="jarvbm-tab-general" class="jarvbm-tab-panel active">
                <h2>Configuración General</h2>
                <form method="post" action="options.php">
                    <?php
                        settings_fields('jarvbm_options_group');
                        do_settings_sections('jarvbm-settings');
                        submit_button('Guardar Configuración');
                    ?>
                </form>
            </div>

            <!-- TAB: GENERATE -->
            <div id="jarvbm-tab-generate" class="jarvbm-tab-panel">
                <h2>Generar Nuevo Código</h2>
                <form id="jarvbm-generate-form" method="post" action="">
                    <?php wp_nonce_field('jarvbm_generate_code', 'jarvbm_nonce'); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="prefix_country">Prefijo País</label></th>
                            <td><input type="text" name="prefix_country" id="prefix_country" placeholder="Ej: 877" required /></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="product_type">Tipo de Producto</label></th>
                            <td>
                                <select name="product_type" id="product_type">
                                    <option value="0">0 - Servicio base</option>
                                    <option value="1">1 - Servicio extra</option>
                                    <option value="2">2 - Evento</option>
                                    <option value="3">3 - Producto simple</option>
                                    <option value="4">4 - Producto variable</option>
                                    <option value="5">5 - Producto agrupado</option>
                                    <option value="6">6 - Reservable</option>
                                    <option value="7">7 - Lotería</option>
                                    <option value="8">8 - Otros</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="client_type">Tipo de Cliente</label></th>
                            <td>
                                <select name="client_type" id="client_type">
                                    <option value="1">Cliente A</option>
                                    <option value="2">Cliente B</option>
                                    <option value="3">Cliente C</option>
                                    <option value="4">Cliente D</option>
                                    <option value="5">Cliente E</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <button type="submit" class="button button-primary">Generar Código</button>
                    </p>
                </form>
            </div>

            <!-- TAB: LIST -->
            <div id="jarvbm-tab-list" class="jarvbm-tab-panel">
                <h2>Lista de Códigos Generados</h2>
                <div id="jarvbm-codes-list">
                    <p>📊 Aquí mostraremos la tabla de códigos generados desde la base de datos.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
