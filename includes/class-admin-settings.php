<?php
/**
 * Configurações de administração do plugin
 *
 * @package FontStyleElementor
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe para configurações de administração do plugin
 */
class FSE_Admin_Settings {

    /**
     * Inicializa as configurações do admin
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    }

    /**
     * Adiciona página de menu ao painel
     */
    public function add_admin_menu() {
        add_submenu_page(
            'elementor',
            esc_html__( 'Font Style Settings', 'font-style-for-elementor' ),
            esc_html__( 'Font Style', 'font-style-for-elementor' ),
            'manage_options',
            'font-style-for-elementor',
            array( $this, 'display_settings_page' )
        );
    }

    /**
     * Registra as configurações do plugin
     */
    public function register_settings() {
        register_setting(
            'fse_settings_group',
            'fse_settings',
            array( $this, 'sanitize_settings' )
        );

        add_settings_section(
            'fse_general_section',
            esc_html__( 'Configurações Gerais', 'font-style-for-elementor' ),
            array( $this, 'general_section_callback' ),
            'font-style-for-elementor'
        );

        add_settings_field(
            'fse_enabled',
            esc_html__( 'Habilitar Estilos', 'font-style-for-elementor' ),
            array( $this, 'enabled_field_callback' ),
            'font-style-for-elementor',
            'fse_general_section'
        );
    }

    /**
     * Enfileira estilos para o admin
     *
     * @param string $hook Hook atual.
     */
    public function enqueue_admin_styles( $hook ) {
        if ( 'elementor_page_font-style-for-elementor' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'fse-admin-styles',
            FSE_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            FSE_VERSION
        );
    }

    /**
     * Callback para a descrição da seção geral
     */
    public function general_section_callback() {
        echo '<p>' . esc_html__( 'Configure os estilos de fonte para o Elementor.', 'font-style-for-elementor' ) . '</p>';
    }

    /**
     * Callback para o campo de habilitar
     */
    public function enabled_field_callback() {
        $settings = get_option( 'fse_settings', array() );
        $enabled = isset( $settings['enabled'] ) ? $settings['enabled'] : 'yes';
        ?>
        <select name="fse_settings[enabled]">
            <option value="yes" <?php selected( $enabled, 'yes' ); ?>><?php esc_html_e( 'Sim', 'font-style-for-elementor' ); ?></option>
            <option value="no" <?php selected( $enabled, 'no' ); ?>><?php esc_html_e( 'Não', 'font-style-for-elementor' ); ?></option>
        </select>
        <p class="description"><?php esc_html_e( 'Habilitar ou desabilitar os estilos de fonte.', 'font-style-for-elementor' ); ?></p>
        <?php
    }

    /**
     * Sanitiza as configurações antes de salvar
     *
     * @param array $input Dados de entrada.
     * @return array
     */
    public function sanitize_settings( $input ) {
        $sanitized_input = array();

        if ( isset( $input['enabled'] ) ) {
            $sanitized_input['enabled'] = sanitize_text_field( $input['enabled'] );
        }

        return $sanitized_input;
    }

    /**
     * Exibe a página de configurações
     */
    public function display_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap fse-settings-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'fse_settings_group' );
                do_settings_sections( 'font-style-for-elementor' );
                submit_button();
                ?>
            </form>
            
            <div class="fse-preview-section">
                <h2><?php esc_html_e( 'Pré-visualização dos Estilos', 'font-style-for-elementor' ); ?></h2>
                
                <h3><?php esc_html_e( 'Tamanhos de Fonte', 'font-style-for-elementor' ); ?></h3>
                <div class="fse-preview-grid">
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Display Large</span>
                        <div class="fse-preview-display-large">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Display Small</span>
                        <div class="fse-preview-display-small">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">H1</span>
                        <div class="fse-preview-h1">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">H2</span>
                        <div class="fse-preview-h2">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">H3</span>
                        <div class="fse-preview-h3">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">H4</span>
                        <div class="fse-preview-h4">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">H5</span>
                        <div class="fse-preview-h5">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">H6</span>
                        <div class="fse-preview-h6">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Text XL</span>
                        <div class="fse-preview-text-xl">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Text LG</span>
                        <div class="fse-preview-text-lg">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Text MD</span>
                        <div class="fse-preview-text-md">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Text SM</span>
                        <div class="fse-preview-text-sm">Aa</div>
                    </div>
                </div>
                
                <h3><?php esc_html_e( 'Pesos de Fonte', 'font-style-for-elementor' ); ?></h3>
                <div class="fse-preview-grid">
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Regular</span>
                        <div class="fse-preview-font-regular">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Medium</span>
                        <div class="fse-preview-font-medium">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Semi Bold</span>
                        <div class="fse-preview-font-semibold">Aa</div>
                    </div>
                    <div class="fse-preview-item">
                        <span class="fse-preview-label">Bold</span>
                        <div class="fse-preview-font-bold">Aa</div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}