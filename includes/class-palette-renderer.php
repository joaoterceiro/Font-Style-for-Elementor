<?php
/**
 * Classe de renderização de paletas de cores
 *
 * @package ColorPaletteManager
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe para renderização de paletas de cores
 */
class CPM_Palette_Renderer {

    /**
     * Inicializa o renderizador
     */
    public function init() {
        // Adicionar shortcode para renderização da paleta
        add_shortcode( 'color_palette', array( $this, 'render_palette_shortcode' ) );
        
        // Adicionar bloco Gutenberg
        add_action( 'init', array( $this, 'register_blocks' ) );
        
        // Adicionar widget para o Elementor
        add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_elementor_widget' ) );
    }

    /**
     * Renderiza o shortcode da paleta de cores
     *
     * @param array $atts Atributos do shortcode.
     * @return string
     */
    public function render_palette_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'type' => 'all', // all, primary, secondary, gray
            ),
            $atts,
            'color_palette'
        );
        
        return $this->render_palette( $atts['type'] );
    }

    /**
     * Renderiza a paleta de cores
     *
     * @param string $type Tipo de paleta (all, primary, secondary, gray).
     * @return string
     */
    public function render_palette( $type = 'all' ) {
        $settings = get_option( 'cpm_settings', array() );
        $output = '<div class="cpm-palette-container">';
        
        // Adicionar cores primárias
        if ( 'all' === $type || 'primary' === $type ) {
            if ( ! empty( $settings['primary_colors'] ) ) {
                $output .= '<div class="cpm-palette-section">';
                $output .= '<h3>' . esc_html__( 'Cores Primárias', 'color-palette-manager' ) . '</h3>';
                $output .= '<div class="cpm-color-grid">';
                
                foreach ( $settings['primary_colors'] as $color ) {
                    if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                        $output .= $this->render_color_swatch( $color['name'], $color['color'] );
                    }
                }
                
                $output .= '</div>'; // .cpm-color-grid
                $output .= '</div>'; // .cpm-palette-section
            }
        }
        
        // Adicionar cores secundárias
        if ( 'all' === $type || 'secondary' === $type ) {
            if ( ! empty( $settings['secondary_colors'] ) ) {
                $output .= '<div class="cpm-palette-section">';
                $output .= '<h3>' . esc_html__( 'Cores Secundárias', 'color-palette-manager' ) . '</h3>';
                $output .= '<div class="cpm-color-grid">';
                
                foreach ( $settings['secondary_colors'] as $color ) {
                    if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                        $output .= $this->render_color_swatch( $color['name'], $color['color'] );
                    }
                }
                
                $output .= '</div>'; // .cpm-color-grid
                $output .= '</div>'; // .cpm-palette-section
            }
        }
        
        // Adicionar tons de cinza
        if ( 'all' === $type || 'gray' === $type ) {
            if ( ! empty( $settings['gray_tones'] ) ) {
                $output .= '<div class="cpm-palette-section">';
                $output .= '<h3>' . esc_html__( 'Tons de Cinza', 'color-palette-manager' ) . '</h3>';
                $output .= '<div class="cpm-color-grid">';
                
                foreach ( $settings['gray_tones'] as $color ) {
                    if ( ! empty( $color['name'] ) && ! empty( $color['color'] ) ) {
                        $output .= $this->render_color_swatch( $color['name'], $color['color'] );
                    }
                }
                
                $output .= '</div>'; // .cpm-color-grid
                $output .= '</div>'; // .cpm-palette-section
            }
        }
        
        $output .= '</div>'; // .cpm-palette-container
        
        return $output;
    }

    /**
     * Renderiza uma amostra de cor individual
     *
     * @param string $name Nome da cor.
     * @param string $color Valor da cor.
     * @return string
     */
    private function render_color_swatch( $name, $color ) {
        // Determinar se a cor é clara ou escura para ajustar o texto
        $is_light = $this->is_light_color( $color );
        $text_class = $is_light ? 'cpm-color-dark-text' : 'cpm-color-light-text';
        
        $output = '<div class="cpm-color-swatch">';
        $output .= '<div class="cpm-color-sample" style="background-color: ' . esc_attr( $color ) . ';">';
        $output .= '<div class="cpm-color-info ' . esc_attr( $text_class ) . '">';
        $output .= '<div class="cpm-color-name">' . esc_html( $name ) . '</div>';
        $output .= '<div class="cpm-color-value">' . esc_html( $color ) . '</div>';
        $output .= '</div>'; // .cpm-color-info
        $output .= '</div>'; // .cpm-color-sample
        $output .= '</div>'; // .cpm-color-swatch
        
        return $output;
    }

    /**
     * Verifica se uma cor é clara (para ajustar o texto)
     *
     * @param string $color Valor da cor em hexadecimal.
     * @return boolean
     */
    private function is_light_color( $color ) {
        // Remover o # se presente
        $color = str_replace( '#', '', $color );
        
        // Converter para RGB
        $r = hexdec( substr( $color, 0, 2 ) );
        $g = hexdec( substr( $color, 2, 2 ) );
        $b = hexdec( substr( $color, 4, 2 ) );
        
        // Calcular o brilho (fórmula YIQ)
        $yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;
        
        // Retornar true se a cor for clara (YIQ >= 128)
        return $yiq >= 128;
    }

    /**
     * Registra blocos Gutenberg
     */
    public function register_blocks() {
        // Verificar se o Gutenberg está ativo
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        
        // Registrar o script do bloco
        wp_register_script(
            'color-palette-block',
            CPM_PLUGIN_URL . 'assets/js/blocks.js',
            array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ),
            CPM_VERSION,
            true
        );
        
        // Passar dados para o script
        wp_localize_script( 'color-palette-block', 'cpmBlockData', array(
            'settings' => get_option( 'cpm_settings', array() ),
            'i18n' => array(
                'blockTitle' => __( 'Paleta de Cores', 'color-palette-manager' ),
                'blockDescription' => __( 'Exibe sua paleta de cores personalizada.', 'color-palette-manager' ),
                'typeLabel' => __( 'Tipo de Paleta', 'color-palette-manager' ),
                'typeAll' => __( 'Todas as Cores', 'color-palette-manager' ),
                'typePrimary' => __( 'Cores Primárias', 'color-palette-manager' ),
                'typeSecondary' => __( 'Cores Secundárias', 'color-palette-manager' ),
                'typeGray' => __( 'Tons de Cinza', 'color-palette-manager' ),
            ),
        ) );
        
        // Registrar o estilo do bloco
        wp_register_style(
            'color-palette-block-editor',
            CPM_PLUGIN_URL . 'assets/css/blocks-editor.css',
            array( 'wp-edit-blocks' ),
            CPM_VERSION
        );
        
        // Registrar o estilo frontend do bloco
        wp_register_style(
            'color-palette-block-style',
            CPM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CPM_VERSION
        );
        
        // Registrar o bloco
        register_block_type( 'color-palette-manager/palette', array(
            'editor_script' => 'color-palette-block',
            'editor_style' => 'color-palette-block-editor',
            'style' => 'color-palette-block-style',
            'render_callback' => array( $this, 'render_block' ),
            'attributes' => array(
                'type' => array(
                    'type' => 'string',
                    'default' => 'all',
                ),
            ),
        ) );
    }

    /**
     * Renderiza o bloco Gutenberg
     *
     * @param array $attributes Atributos do bloco.
     * @return string
     */
    public function render_block( $attributes ) {
        return $this->render_palette( $attributes['type'] );
    }

    /**
     * Registra widget para o Elementor
     */
    public function register_elementor_widget() {
        // Verificar se o Elementor está ativo
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }
        
        // Incluir o arquivo da classe do widget
        require_once CPM_PLUGIN_DIR . 'includes/widgets/class-elementor-palette-widget.php';
        
        // Registrar o widget
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \CPM_Elementor_Palette_Widget() );
    }
}