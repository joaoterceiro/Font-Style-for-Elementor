<?php
/**
 * Color Palette Manager
 *
 * @package           ColorPaletteManager
 * @author            João DSGN
 * @copyright         2025 João DSGN
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Color Palette Manager
 * Plugin URI:        https://example.com/color-palette-manager
 * Description:       Configure e gerencie a paleta de cores do seu projeto
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            João DSGN
 * Author URI:        https://example.com
 * Text Domain:       color-palette-manager
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/color-palette-manager
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes
define( 'CPM_VERSION', '1.0.0' );
define( 'CPM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CPM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CPM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Carregar arquivos de tradução
 */
function cpm_load_textdomain() {
    load_plugin_textdomain( 'color-palette-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'cpm_load_textdomain' );

/**
 * Carregar a classe principal
 */
function cpm_init() {
    require_once CPM_PLUGIN_DIR . 'includes/class-color-palette-manager.php';
    require_once CPM_PLUGIN_DIR . 'includes/class-admin-settings.php';
    require_once CPM_PLUGIN_DIR . 'includes/class-palette-renderer.php';
    
    // Iniciar o plugin
    $color_palette_manager = new CPM_Color_Palette_Manager();
    $color_palette_manager->init();
    
    // Iniciar as configurações de admin
    $admin_settings = new CPM_Admin_Settings();
    $admin_settings->init();
    
    // Iniciar o renderizador de paletas
    $palette_renderer = new CPM_Palette_Renderer();
    $palette_renderer->init();
}
add_action( 'plugins_loaded', 'cpm_init', 20 );

/**
 * Registrar a ativação do plugin
 */
function cpm_activate() {
    // Criar configurações padrão se necessário
    if ( ! get_option( 'cpm_settings' ) ) {
        $default_settings = array(
            'primary_colors' => array(
                array('name' => 'Pura', 'color' => '#FF0874'),
            ),
            'secondary_colors' => array(),
            'gray_tones' => array(),
        );
        update_option( 'cpm_settings', $default_settings );
    }
    
    // Limpar o cache de regras de reescrita
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cpm_activate' );

/**
 * Registrar a desativação do plugin
 */
function cpm_deactivate() {
    // Limpar o cache de regras de reescrita
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cpm_deactivate' );

/**
 * Registrar a desinstalação do plugin
 */
function cpm_uninstall() {
    // Remover todas as opções relacionadas ao plugin
    delete_option( 'cpm_settings' );
}
register_uninstall_hook( __FILE__, 'cpm_uninstall' );