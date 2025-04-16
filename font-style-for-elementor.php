<?php
/**
 * Font Style for Elementor
 *
 * @package           FontStyleElementor
 * @author            João DSGN
 * @copyright         2025 João DSGN
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Font Style for Elementor
 * Plugin URI:        https://example.com/font-style-for-elementor
 * Description:       Tamanhos de fonte com alta especificidade e responsivas para elementor
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            João DSGN
 * Author URI:        https://example.com
 * Text Domain:       font-style-for-elementor
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/font-style-for-elementor
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes
define( 'FSE_VERSION', '1.0.0' );
define( 'FSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FSE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Carregar arquivos de tradução
 */
function fse_load_textdomain() {
    load_plugin_textdomain( 'font-style-for-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'fse_load_textdomain' );

/**
 * Verificar se o Elementor está ativo
 */
function fse_check_elementor_dependency() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'fse_elementor_notice' );
        return false;
    }
    return true;
}

/**
 * Aviso de dependência do Elementor
 */
function fse_elementor_notice() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    $elementor = 'elementor/elementor.php';

    if ( ! is_plugin_active( $elementor ) ) {
        if ( ! is_plugin_installed( $elementor ) ) {
            $install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Link para instalar o Elementor */
                esc_html__( '%1$s requer o plugin %2$s para funcionar. %3$s', 'font-style-for-elementor' ),
                '<strong>' . esc_html__( 'Font Style for Elementor', 'font-style-for-elementor' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'font-style-for-elementor' ) . '</strong>',
                '<a href="' . esc_url( $install_url ) . '">' . esc_html__( 'Instalar Elementor', 'font-style-for-elementor' ) . '</a>'
            );
        } else {
            $activate_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $elementor . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $elementor );
            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Link para ativar o Elementor */
                esc_html__( '%1$s requer o plugin %2$s para funcionar. %3$s', 'font-style-for-elementor' ),
                '<strong>' . esc_html__( 'Font Style for Elementor', 'font-style-for-elementor' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'font-style-for-elementor' ) . '</strong>',
                '<a href="' . esc_url( $activate_url ) . '">' . esc_html__( 'Ativar Elementor', 'font-style-for-elementor' ) . '</a>'
            );
        }
        echo '<div class="error"><p>' . wp_kses_post( $message ) . '</p></div>';
    }
}

/**
 * Verifica se um plugin está instalado
 *
 * @param string $basename Nome do arquivo do plugin.
 * @return bool
 */
function is_plugin_installed( $basename ) {
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();
    return isset( $plugins[ $basename ] );
}

/**
 * Carregar a classe principal
 */
function fse_init() {
    if ( fse_check_elementor_dependency() ) {
        require_once FSE_PLUGIN_DIR . 'includes/class-font-style-elementor.php';
        require_once FSE_PLUGIN_DIR . 'includes/class-admin-settings.php';
        require_once FSE_PLUGIN_DIR . 'includes/class-documentation.php';
        
        // Iniciar o plugin
        $font_style_elementor = new FSE_Font_Style_Elementor();
        $font_style_elementor->init();
        
        // Iniciar as configurações de admin
        $admin_settings = new FSE_Admin_Settings();
        $admin_settings->init();
        
        // Iniciar a documentação
        $documentation = new FSE_Documentation();
        $documentation->init();
    }
}
add_action( 'plugins_loaded', 'fse_init', 20 );

/**
 * Registrar a ativação do plugin
 */
function fse_activate() {
    // Criar configurações padrão se necessário
    if ( ! get_option( 'fse_settings' ) ) {
        $default_settings = array(
            'enabled' => 'yes',
        );
        update_option( 'fse_settings', $default_settings );
    }
    
    // Limpar o cache de regras de reescrita
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fse_activate' );

/**
 * Registrar a desativação do plugin
 */
function fse_deactivate() {
    // Limpar o cache de regras de reescrita
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fse_deactivate' );