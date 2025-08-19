<?php
/*
Plugin Name: JARV Barcodes Manager
Plugin URI: https://jarvismora.com/hub-jarvis/jarv-barcodes-manager
Description: Gestor y generador de códigos de barra EAN-13 (y otros en el futuro) para proyectos bajo Hub JARVIS.
Version: 1.0.0
Author: Jarvis Legacy LLC
Author URI: https://jarvismora.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: jarv-barcodes-manager
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// =======================
// Definir constantes base
// =======================
define( 'JARVBM_VERSION', '1.0.0' );
define( 'JARVBM_PATH', plugin_dir_path( __FILE__ ) );
define( 'JARVBM_URL', plugin_dir_url( __FILE__ ) );
define( 'JARVBM_BASENAME', plugin_basename(__FILE__) );

// =======================
// Cargar archivos requeridos
// =======================
$required_files = [
    'includes/jarvbm-admin-menu.php',
    'includes/jarvbm-functions.php',
    'includes/jarvbm-database.php',
];

foreach ( $required_files as $file ) {
    $path = JARVBM_PATH . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    } else {
        error_log("JARV Barcodes Manager: archivo faltante - $file");
    }
}

// =======================
// Hook de activación
// =======================
register_activation_hook( __FILE__, function() {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_codes = $wpdb->prefix . 'jarvbm_codes';
    $sql_codes = "CREATE TABLE $table_codes (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        ean13 VARCHAR(20) NOT NULL,
        prefix_country VARCHAR(10) DEFAULT NULL,
        prefix_jarvcode VARCHAR(10) DEFAULT NULL,
        type_product VARCHAR(10) DEFAULT NULL,
        client_type VARCHAR(10) DEFAULT NULL,
        status VARCHAR(20) DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY ean13 (ean13)
    ) $charset_collate;";

    dbDelta($sql_codes);
});

// =======================
// Hook de desactivación
// =======================
register_deactivation_hook( __FILE__, function() {
    // Aquí puedes limpiar opciones o desregistrar cronjobs si se crean
});

// =======================
// Encolar assets admin
// =======================
add_action('admin_enqueue_scripts', 'jarvbm_enqueue_admin_assets');
function jarvbm_enqueue_admin_assets($hook) {
    $screen = get_current_screen();
    if ( ! $screen || $screen->id !== 'jarvis_page_jarvbm-settings' ) {
        return;
    }

    // CSS y JS personalizados
    wp_enqueue_style(
        'jarvbm-admin-style',
        JARVBM_URL . 'assets/css/jarvbm-admin.css',
        [],
        JARVBM_VERSION
    );

    wp_enqueue_script(
        'jarvbm-admin-script',
        JARVBM_URL . 'assets/js/jarvbm-admin.js',
        ['jquery'],
        JARVBM_VERSION,
        true
    );

    wp_localize_script('jarvbm-admin-script', 'jarvbmAdminData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('jarvbm_admin_nonce')
    ]);
}

// =======================
// Menú principal Hub JARVIS + Submenú del plugin
// =======================
add_action( 'admin_menu', 'JARVBM_register_admin_menu' );
function JARVBM_register_admin_menu() {
    $menu_slug = 'jarvis-legacy';

    global $submenu;
    if ( ! isset( $submenu[ $menu_slug ] ) ) {
        add_menu_page(
            'Hub JARVIS',
            'Hub JARVIS',
            'manage_options',
            $menu_slug,
            '__return_null',
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyMCAyMCI+PHBhdGggZmlsbD0iI2ZmZiIgZD0iTTEwLjA0IDIwYy0yLjA1IDAtNC4xLjAxLTYuMTUgMEMxLjU5IDE5Ljk4LjAyIDE4LjQyLjAxIDE2LjEzIDAgMTIuMDQgMCA3Ljk2LjAxIDMuODguMDIgMS41NiAxLjU2LjAzIDMuODguMDIgNy45NiAwIDEyLjAzIDAgMTYuMS4wMWMyLjM3LjAxIDMuODkgMS41NiAzLjg5IDMuOTUuMDEgMy45NiAwIDcuOTIgMCAxMS44Ny0uMDEgMi43LTEuNDcgNC4xNS00LjE1IDQuMTYtMS45MyAwLTMuODYgMC01Ljc5IDBabTguNzktMTBjMC0yLjEzLjAxLTMuOTkgMC02LjEzLS4wMS0xLjYxLTEuMzQtMi43My0yLjkxLTIuNzMtNC4yOS0uMDEtNy41NC0uMDEtMTEuODMgMC0xLjU3IDAtMi45NCAxLjMyLTIuOTUgMi45MS0uMDIgNC4yNy0uMDIgNy41IDAgMTEuNzcgMCAxLjYxIDEuMjggMy4wNiAyLjkxIDMuMDYgNC4yMyAwIDcuNzQgMCAxMS45NiAwIDEuNjQgMCAyLjgyLTEuMjIgMi44Mi0yLjg1LjAxLTIuMTMgMC0zLjkgMC02LjAzWiIvPjxwYXRoIGZpbGw9IiNmZmYiIGQ9Ik0xNi4yIDUuNGwtLjgxLjMzYy0uOTcgMi41LTIuNzMgNy4yOC0zLjYgOS40Ny0uMTQuMzUtLjM2LjQyLS43LjI4LTIuMzMtLjk0LTQuNjYtMS44Ni02Ljk4LTIuODEtLjE1LS4wNi0uMzQtLjI3LS4zNC0uNCAwLS4xNC4yLS4zMy4zNS0uNC41MS0uMjUgMS4wNS0uNDYgMS41OC0uNjkuNTMtLjIzIDEuMDUtLjQ3IDEuNTktLjY3LjE1LS4wNi40LS4wNC41Mi4wNi4wOS4wNy4wOC4zMy4wNC40OC0uMDguMjgtLjIxLjU1LS4zMi44My0uMjQuNjItLjIyLjY4LjM3LjkyLjM5LjE2Ljc5LjMyIDEuMTkuNDguNTQuMjEuNjQuMTguODUtLjM2LjQzLTEuMDYgMS41Mi00LjE0IDIuMi01Ljg4bC0xLjA4LjQzLS40LTEuMDEgMy42MS0xLjQ2LjI0LS4xIDEuMjgtLjUyLjQgMS4wMVoiLz48L3N2Zz4=',
            2
        );
    }

    add_submenu_page(
        $menu_slug,
        'JARV Barcodes Manager',
        'Barcodes Manager',
        'manage_options',
        'jarvbm-settings',
        'JARVBM_render_admin_page'
    );
}
