<?php
/**
 * Classe principal do plugin Font Style for Elementor
 *
 * @package FontStyleElementor
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe principal que gerencia os estilos de fonte para o Elementor
 */
class FSE_Font_Style_Elementor {

    /**
     * Inicializa o plugin
     */
    public function init() {
        // Registrar os estilos
        add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
        
        // Adicionar classes ao Elementor
        add_action( 'elementor/element/after_section_end', array( $this, 'add_font_style_section' ), 10, 3 );
    }

    /**
     * Registra e enfileira os estilos CSS
     */
    public function register_styles() {
        $settings = get_option( 'fse_settings', array() );
        
        // Verificar se os estilos estão habilitados
        if ( isset( $settings['enabled'] ) && 'yes' === $settings['enabled'] ) {
            wp_register_style(
                'font-style-for-elementor',
                FSE_PLUGIN_URL . 'assets/css/font-styles.css',
                array(),
                FSE_VERSION
            );
            
            wp_enqueue_style( 'font-style-for-elementor' );
        }
    }

    /**
     * Adiciona seção de estilo de fonte aos widgets do Elementor
     *
     * @param object $element Elemento atual.
     * @param string $section_id ID da seção.
     * @param array  $args Argumentos.
     */
    public function add_font_style_section( $element, $section_id, $args ) {
        if ( 'section_advanced' === $section_id ) {
            $element->start_controls_section(
                'section_fse_font_styles',
                array(
                    'label' => esc_html__( 'Estilos de Fonte', 'font-style-for-elementor' ),
                    'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
                )
            );

            $element->add_control(
                'fse_font_size',
                array(
                    'label'   => esc_html__( 'Tamanho da Fonte', 'font-style-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'options' => array(
                        ''               => esc_html__( 'Padrão', 'font-style-for-elementor' ),
                        'display-large'  => esc_html__( 'Display Large - 80px', 'font-style-for-elementor' ),
                        'display-small'  => esc_html__( 'Display Small - 72px', 'font-style-for-elementor' ),
                        'heading-h1'     => esc_html__( 'H1 - 64px', 'font-style-for-elementor' ),
                        'heading-h2'     => esc_html__( 'H2 - 56px', 'font-style-for-elementor' ),
                        'heading-h3'     => esc_html__( 'H3 - 48px', 'font-style-for-elementor' ),
                        'heading-h4'     => esc_html__( 'H4 - 40px', 'font-style-for-elementor' ),
                        'heading-h5'     => esc_html__( 'H5 - 32px', 'font-style-for-elementor' ),
                        'heading-h6'     => esc_html__( 'H6 - 24px', 'font-style-for-elementor' ),
                        'text-xl'        => esc_html__( 'Texto XL - 18px', 'font-style-for-elementor' ),
                        'text-lg'        => esc_html__( 'Texto LG - 16px', 'font-style-for-elementor' ),
                        'text-md'        => esc_html__( 'Texto MD - 14px', 'font-style-for-elementor' ),
                        'text-sm'        => esc_html__( 'Texto SM - 12px', 'font-style-for-elementor' ),
                    ),
                    'selectors' => array(
                        '{{WRAPPER}}' => '{{VALUE}}',
                    ),
                    'prefix_class' => '',
                )
            );

            $element->add_control(
                'fse_font_weight',
                array(
                    'label'   => esc_html__( 'Peso da Fonte', 'font-style-for-elementor' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => '',
                    'options' => array(
                        ''             => esc_html__( 'Padrão', 'font-style-for-elementor' ),
                        'font-regular' => esc_html__( 'Regular - 400', 'font-style-for-elementor' ),
                        'font-medium'  => esc_html__( 'Medium - 500', 'font-style-for-elementor' ),
                        'font-semibold' => esc_html__( 'Semi Bold - 600', 'font-style-for-elementor' ),
                        'font-bold'    => esc_html__( 'Bold - 700', 'font-style-for-elementor' ),
                    ),
                    'selectors' => array(
                        '{{WRAPPER}}' => '{{VALUE}}',
                    ),
                    'prefix_class' => '',
                )
            );

            $element->add_control(
                'fse_note',
                array(
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'raw' => esc_html__( 'Nota: Estes estilos aplicam alta especificidade e são totalmente responsivos.', 'font-style-for-elementor' ),
                    'content_classes' => 'elementor-descriptor',
                )
            );

            $element->end_controls_section();
        }
    }
}