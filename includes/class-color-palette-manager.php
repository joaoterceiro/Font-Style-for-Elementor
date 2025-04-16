<?php
/**
 * Classe principal do plugin Color Palette Manager
 *
 * @package ColorPaletteManager
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe principal que gerencia as paletas de cores
 */
class CPM_Color_Palette_Manager {

    /**
     * Inicializa o plugin
     */
    public function init() {
        // Registrar os estilos e scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );
        
        // Adicionar paletas de cores ao tema customizer
        add_action( 'customize_register', array( $this, 'add_customizer_settings' ) );
        
        // Adicionar cores à página de edição do Elementor
        add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'add_elementor_colors' ) );
    }

    /**
     * Registra e enfileira os assets para o frontend
     */
    public function register_frontend_assets() {
        wp_register_style(
            'color-palette-manager-frontend',
            CPM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CPM_VERSION
        );
        
        wp_enqueue_style( 'color-palette-manager-frontend' );
        
        // Gerar estilos CSS dinâmicos com base nas cores configuradas
        $this->generate_dynamic_css();
    }

    /**
     * Registra e enfileira os assets para o admin
     * 
     * @param string $hook Hook atual da página admin.
     */
    public function register_admin_assets( $hook ) {
        // Carregar apenas nas páginas do plugin
        if ( 'toplevel_page_color-palette-manager' !== $hook && 'settings_page_color-palette-manager-docs' !== $hook ) {
            return;
        }

        wp_register_style(
            'color-palette-manager-admin',
            CPM_PLUGIN_URL . 'assets/css/admin.css',
            array( 'wp-color-picker' ),
            CPM_VERSION
        );
        
        wp_enqueue_style( 'color-palette-manager-admin' );
        
        wp_register_script(
            'color-palette-manager-admin',
            CPM_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ),
            CPM_VERSION,
            true
        );
        
        wp_localize_script( 'color-palette-manager-admin', 'cpmSettings', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'cpm_nonce' ),
            'i18n' => array(
                'colorName' => __( 'Nome da Cor', 'color-palette-manager' ),
                'colorValue' => __( 'Valor Hexadecimal', 'color-palette-manager' ),
                'addColor' => __( 'Adicionar Cor', 'color-palette-manager' ),
                'removeColor' => __( 'Remover', 'color-palette-manager' ),
            ),
        ) );
        
        wp_enqueue_script( 'color-palette-manager-admin' );
    }

    /**
     * Gera CSS dinâmico com base nas cores configuradas
     */
    private function generate_dynamic_css() {
        $settings = get_option( 'cpm_settings', array() );
        
        $css = ':root {';
        
        // Adicionar cores primárias
        if ( ! empty( $settings['primary_colors'] ) ) {
            foreach ( $settings['primary_colors'] as $index => $color ) {
                if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                    $var_name = sanitize_title( $color['name'] );
                    $css .= "--primary-{$var_name}: {$color['color']};";
                }
            }
        }
        
        // Adicionar cores secundárias
        if ( ! empty( $settings['secondary_colors'] ) ) {
            foreach ( $settings['secondary_colors'] as $index => $color ) {
                if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                    $var_name = sanitize_title( $color['name'] );
                    $css .= "--secondary-{$var_name}: {$color['color']};";
                }
            }
        }
        
        // Adicionar tons de cinza
        if ( ! empty( $settings['gray_tones'] ) ) {
            foreach ( $settings['gray_tones'] as $index => $color ) {
                if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                    $var_name = sanitize_title( $color['name'] );
                    $css .= "--gray-{$var_name}: {$color['color']};";
                }
            }
        }
        
        $css .= '}';
        
        wp_add_inline_style( 'color-palette-manager-frontend', $css );
    }

    /**
     * Adiciona configurações ao Customizer do WordPress
     * 
     * @param WP_Customize_Manager $wp_customize Objeto do customizer.
     */
    public function add_customizer_settings( $wp_customize ) {
        $wp_customize->add_section( 'cpm_colors_section', array(
            'title'       => __( 'Paleta de Cores', 'color-palette-manager' ),
            'description' => __( 'Gerencie a paleta de cores do seu site.', 'color-palette-manager' ),
            'priority'    => 30,
        ) );
        
        $settings = get_option( 'cpm_settings', array() );
        
        // Adicionar configurações das cores primárias
        if ( ! empty( $settings['primary_colors'] ) ) {
            foreach ( $settings['primary_colors'] as $index => $color ) {
                if ( ! empty( $color['name'] ) ) {
                    $setting_id = 'cpm_primary_' . sanitize_title( $color['name'] );
                    
                    $wp_customize->add_setting( $setting_id, array(
                        'default'    => $color['color'],
                        'type'       => 'option',
                        'capability' => 'edit_theme_options',
                        'transport'  => 'postMessage',
                    ) );
                    
                    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(
                        'label'    => sprintf( __( 'Primária: %s', 'color-palette-manager' ), $color['name'] ),
                        'section'  => 'cpm_colors_section',
                        'settings' => $setting_id,
                    ) ) );
                }
            }
        }
        
        // Adicionar configurações das cores secundárias
        if ( ! empty( $settings['secondary_colors'] ) ) {
            foreach ( $settings['secondary_colors'] as $index => $color ) {
                if ( ! empty( $color['name'] ) ) {
                    $setting_id = 'cpm_secondary_' . sanitize_title( $color['name'] );
                    
                    $wp_customize->add_setting( $setting_id, array(
                        'default'    => $color['color'],
                        'type'       => 'option',
                        'capability' => 'edit_theme_options',
                        'transport'  => 'postMessage',
                    ) );
                    
                    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(
                        'label'    => sprintf( __( 'Secundária: %s', 'color-palette-manager' ), $color['name'] ),
                        'section'  => 'cpm_colors_section',
                        'settings' => $setting_id,
                    ) ) );
                }
            }
        }
    }

    /**
     * Adiciona as cores configuradas ao Elementor
     */
    public function add_elementor_colors() {
        $settings = get_option( 'cpm_settings', array() );
        $colors = array();
        
        // Adicionar cores primárias
        if ( ! empty( $settings['primary_colors'] ) ) {
            foreach ( $settings['primary_colors'] as $color ) {
                if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                    $colors[] = array(
                        'title' => sprintf( __( 'Primária: %s', 'color-palette-manager' ), $color['name'] ),
                        'slug' => 'primary-' . sanitize_title( $color['name'] ),
                        'color' => $color['color'],
                    );
                }
            }
        }
        
        // Adicionar cores secundárias
        if ( ! empty( $settings['secondary_colors'] ) ) {
            foreach ( $settings['secondary_colors'] as $color ) {
                if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                    $colors[] = array(
                        'title' => sprintf( __( 'Secundária: %s', 'color-palette-manager' ), $color['name'] ),
                        'slug' => 'secondary-' . sanitize_title( $color['name'] ),
                        'color' => $color['color'],
                    );
                }
            }
        }
        
        // Adicionar tons de cinza
        if ( ! empty( $settings['gray_tones'] ) ) {
            foreach ( $settings['gray_tones'] as $color ) {
                if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                    $colors[] = array(
                        'title' => sprintf( __( 'Cinza: %s', 'color-palette-manager' ), $color['name'] ),
                        'slug' => 'gray-' . sanitize_title( $color['name'] ),
                        'color' => $color['color'],
                    );
                }
            }
        }
        
        // Adicionar as cores ao Elementor usando script inline
        if ( ! empty( $colors ) ) {
            $script = 'jQuery(document).ready(function($) {';
            $script .= 'if (window.elementor && window.elementor.settings && window.elementor.settings.editorPreferences) {';
            $script .= 'var colorPickerOptions = window.elementor.settings.editorPreferences.model.get("ui_theme") === "dark" ? window.elementorCommon.config.ui.theme.dark : window.elementorCommon.config.ui.theme.light;';
            $script .= 'if (colorPickerOptions && colorPickerOptions.colors) {';
            $script .= 'var customColors = ' . json_encode( $colors ) . ';';
            $script .= 'customColors.forEach(function(colorData) {';
            $script .= 'colorPickerOptions.colors[colorData.slug] = colorData.color;';
            $script .= '});';
            $script .= '}';
            $script .= '}';
            $script .= '});';
            
            wp_add_inline_script( 'elementor-editor', $script );
        }
    }
}