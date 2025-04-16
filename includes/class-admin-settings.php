<?php
/**
 * Configurações de administração do plugin
 *
 * @package ColorPaletteManager
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe para configurações de administração do plugin
 */
class CPM_Admin_Settings {

    /**
     * Inicializa as configurações do admin
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // AJAX handlers para salvar configurações
        add_action( 'wp_ajax_cpm_save_settings', array( $this, 'ajax_save_settings' ) );
    }

    /**
     * Adiciona página de menu ao painel
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Paleta de Cores', 'color-palette-manager' ),
            __( 'Paleta de Cores', 'color-palette-manager' ),
            'manage_options',
            'color-palette-manager',
            array( $this, 'display_settings_page' ),
            'dashicons-art',
            30
        );
        
        add_submenu_page(
            'color-palette-manager',
            __( 'Configurações', 'color-palette-manager' ),
            __( 'Configurações', 'color-palette-manager' ),
            'manage_options',
            'color-palette-manager',
            array( $this, 'display_settings_page' )
        );
        
        add_submenu_page(
            'color-palette-manager',
            __( 'Documentação', 'color-palette-manager' ),
            __( 'Documentação', 'color-palette-manager' ),
            'manage_options',
            'color-palette-manager-docs',
            array( $this, 'display_documentation_page' )
        );
    }

    /**
     * Registra as configurações do plugin
     */
    public function register_settings() {
        register_setting(
            'cpm_settings_group',
            'cpm_settings',
            array( $this, 'sanitize_settings' )
        );
    }

    /**
     * Handler AJAX para salvar configurações
     */
    public function ajax_save_settings() {
        // Verificar nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'cpm_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Erro de segurança. Por favor, atualize a página e tente novamente.', 'color-palette-manager' ) ) );
            exit;
        }
        
        // Verificar permissões
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Você não tem permissão para realizar esta ação.', 'color-palette-manager' ) ) );
            exit;
        }
        
        // Processar dados
        $settings = array();
        
        // Processar cores primárias
        if ( isset( $_POST['primary_colors'] ) && is_array( $_POST['primary_colors'] ) ) {
            $settings['primary_colors'] = array();
            
            foreach ( $_POST['primary_colors'] as $color ) {
                if ( isset( $color['name'] ) && isset( $color['color'] ) ) {
                    $settings['primary_colors'][] = array(
                        'name' => sanitize_text_field( wp_unslash( $color['name'] ) ),
                        'color' => sanitize_hex_color( wp_unslash( $color['color'] ) ),
                    );
                }
            }
        }
        
        // Processar cores secundárias
        if ( isset( $_POST['secondary_colors'] ) && is_array( $_POST['secondary_colors'] ) ) {
            $settings['secondary_colors'] = array();
            
            foreach ( $_POST['secondary_colors'] as $color ) {
                if ( isset( $color['name'] ) && isset( $color['color'] ) ) {
                    $settings['secondary_colors'][] = array(
                        'name' => sanitize_text_field( wp_unslash( $color['name'] ) ),
                        'color' => sanitize_hex_color( wp_unslash( $color['color'] ) ),
                    );
                }
            }
        }
        
        // Processar tons de cinza
        if ( isset( $_POST['gray_tones'] ) && is_array( $_POST['gray_tones'] ) ) {
            $settings['gray_tones'] = array();
            
            foreach ( $_POST['gray_tones'] as $color ) {
                if ( isset( $color['name'] ) && isset( $color['color'] ) ) {
                    $settings['gray_tones'][] = array(
                        'name' => sanitize_text_field( wp_unslash( $color['name'] ) ),
                        'color' => sanitize_hex_color( wp_unslash( $color['color'] ) ),
                    );
                }
            }
        }
        
        // Salvar configurações
        update_option( 'cpm_settings', $settings );
        
        wp_send_json_success( array( 'message' => __( 'Configurações salvas com sucesso!', 'color-palette-manager' ) ) );
        exit;
    }

    /**
     * Sanitiza as configurações antes de salvar
     *
     * @param array $input Dados de entrada.
     * @return array
     */
    public function sanitize_settings( $input ) {
        $sanitized_input = array();
        
        // Sanitizar cores primárias
        if ( isset( $input['primary_colors'] ) && is_array( $input['primary_colors'] ) ) {
            $sanitized_input['primary_colors'] = array();
            
            foreach ( $input['primary_colors'] as $color ) {
                if ( isset( $color['name'] ) && isset( $color['color'] ) ) {
                    $sanitized_input['primary_colors'][] = array(
                        'name' => sanitize_text_field( $color['name'] ),
                        'color' => sanitize_hex_color( $color['color'] ),
                    );
                }
            }
        }
        
        // Sanitizar cores secundárias
        if ( isset( $input['secondary_colors'] ) && is_array( $input['secondary_colors'] ) ) {
            $sanitized_input['secondary_colors'] = array();
            
            foreach ( $input['secondary_colors'] as $color ) {
                if ( isset( $color['name'] ) && isset( $color['color'] ) ) {
                    $sanitized_input['secondary_colors'][] = array(
                        'name' => sanitize_text_field( $color['name'] ),
                        'color' => sanitize_hex_color( $color['color'] ),
                    );
                }
            }
        }
        
        // Sanitizar tons de cinza
        if ( isset( $input['gray_tones'] ) && is_array( $input['gray_tones'] ) ) {
            $sanitized_input['gray_tones'] = array();
            
            foreach ( $input['gray_tones'] as $color ) {
                if ( isset( $color['name'] ) && isset( $color['color'] ) ) {
                    $sanitized_input['gray_tones'][] = array(
                        'name' => sanitize_text_field( $color['name'] ),
                        'color' => sanitize_hex_color( $color['color'] ),
                    );
                }
            }
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
        
        $settings = get_option( 'cpm_settings', array() );
        ?>
        <div class="wrap cpm-settings-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <div class="cpm-tabs-wrapper">
                <nav class="cpm-tabs-nav">
                    <a href="#cores-primarias" class="cpm-tab-link active" data-tab="cores-primarias">
                        <span class="dashicons dashicons-palette"></span>
                        <?php esc_html_e( 'Cores Primárias', 'color-palette-manager' ); ?>
                    </a>
                    <a href="#cores-secundarias" class="cpm-tab-link" data-tab="cores-secundarias">
                        <span class="dashicons dashicons-color-picker"></span>
                        <?php esc_html_e( 'Cores Secundárias', 'color-palette-manager' ); ?>
                    </a>
                    <a href="#tons-de-cinza" class="cpm-tab-link" data-tab="tons-de-cinza">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        <?php esc_html_e( 'Tons de Cinza', 'color-palette-manager' ); ?>
                    </a>
                    <a href="#como-usar" class="cpm-tab-link" data-tab="como-usar">
                        <span class="dashicons dashicons-info"></span>
                        <?php esc_html_e( 'Como Usar', 'color-palette-manager' ); ?>
                    </a>
                </nav>
                
                <div class="cpm-tabs-content">
                    <!-- Cores Primárias -->
                    <div id="cores-primarias" class="cpm-tab-content active">
                        <h2><?php esc_html_e( 'Cores Primárias', 'color-palette-manager' ); ?></h2>
                        <p><?php esc_html_e( 'Configure as cores primárias do seu projeto.', 'color-palette-manager' ); ?></p>
                        
                        <div class="cpm-colors-container" id="primary-colors-container">
                            <?php if ( ! empty( $settings['primary_colors'] ) ) : ?>
                                <?php foreach ( $settings['primary_colors'] as $index => $color ) : ?>
                                    <div class="cpm-color-item" data-index="<?php echo esc_attr( $index ); ?>">
                                        <div class="cpm-color-preview" style="background-color: <?php echo esc_attr( $color['color'] ); ?>;"></div>
                                        <div class="cpm-color-inputs">
                                            <input type="text" class="cpm-color-name" value="<?php echo esc_attr( $color['name'] ); ?>" placeholder="<?php esc_attr_e( 'Nome da Cor', 'color-palette-manager' ); ?>" />
                                            <input type="text" class="cpm-color-value color-picker" value="<?php echo esc_attr( $color['color'] ); ?>" data-default-color="<?php echo esc_attr( $color['color'] ); ?>" />
                                        </div>
                                        <div class="cpm-color-actions">
                                            <button type="button" class="button cpm-remove-color"><?php esc_html_e( 'Remover', 'color-palette-manager' ); ?></button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="cpm-color-item" data-index="0">
                                    <div class="cpm-color-preview" style="background-color: #FF0874;"></div>
                                    <div class="cpm-color-inputs">
                                        <input type="text" class="cpm-color-name" value="Pura" placeholder="<?php esc_attr_e( 'Nome da Cor', 'color-palette-manager' ); ?>" />
                                        <input type="text" class="cpm-color-value color-picker" value="#FF0874" data-default-color="#FF0874" />
                                    </div>
                                    <div class="cpm-color-actions">
                                        <button type="button" class="button cpm-remove-color"><?php esc_html_e( 'Remover', 'color-palette-manager' ); ?></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cpm-color-actions-main">
                            <button type="button" class="button button-secondary cpm-add-color" data-container="primary-colors-container">
                                <span class="dashicons dashicons-plus"></span>
                                <?php esc_html_e( 'Adicionar Cor', 'color-palette-manager' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Cores Secundárias -->
                    <div id="cores-secundarias" class="cpm-tab-content">
                        <h2><?php esc_html_e( 'Cores Secundárias', 'color-palette-manager' ); ?></h2>
                        <p><?php esc_html_e( 'Configure as cores secundárias do seu projeto.', 'color-palette-manager' ); ?></p>
                        
                        <div class="cpm-colors-container" id="secondary-colors-container">
                            <?php if ( ! empty( $settings['secondary_colors'] ) ) : ?>
                                <?php foreach ( $settings['secondary_colors'] as $index => $color ) : ?>
                                    <div class="cpm-color-item" data-index="<?php echo esc_attr( $index ); ?>">
                                        <div class="cpm-color-preview" style="background-color: <?php echo esc_attr( $color['color'] ); ?>;"></div>
                                        <div class="cpm-color-inputs">
                                            <input type="text" class="cpm-color-name" value="<?php echo esc_attr( $color['name'] ); ?>" placeholder="<?php esc_attr_e( 'Nome da Cor', 'color-palette-manager' ); ?>" />
                                            <input type="text" class="cpm-color-value color-picker" value="<?php echo esc_attr( $color['color'] ); ?>" data-default-color="<?php echo esc_attr( $color['color'] ); ?>" />
                                        </div>
                                        <div class="cpm-color-actions">
                                            <button type="button" class="button cpm-remove-color"><?php esc_html_e( 'Remover', 'color-palette-manager' ); ?></button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cpm-color-actions-main">
                            <button type="button" class="button button-secondary cpm-add-color" data-container="secondary-colors-container">
                                <span class="dashicons dashicons-plus"></span>
                                <?php esc_html_e( 'Adicionar Cor', 'color-palette-manager' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Tons de Cinza -->
                    <div id="tons-de-cinza" class="cpm-tab-content">
                        <h2><?php esc_html_e( 'Tons de Cinza', 'color-palette-manager' ); ?></h2>
                        <p><?php esc_html_e( 'Configure os tons de cinza do seu projeto.', 'color-palette-manager' ); ?></p>
                        
                        <div class="cpm-colors-container" id="gray-tones-container">
                            <?php if ( ! empty( $settings['gray_tones'] ) ) : ?>
                                <?php foreach ( $settings['gray_tones'] as $index => $color ) : ?>
                                    <div class="cpm-color-item" data-index="<?php echo esc_attr( $index ); ?>">
                                        <div class="cpm-color-preview" style="background-color: <?php echo esc_attr( $color['color'] ); ?>;"></div>
                                        <div class="cpm-color-inputs">
                                            <input type="text" class="cpm-color-name" value="<?php echo esc_attr( $color['name'] ); ?>" placeholder="<?php esc_attr_e( 'Nome da Cor', 'color-palette-manager' ); ?>" />
                                            <input type="text" class="cpm-color-value color-picker" value="<?php echo esc_attr( $color['color'] ); ?>" data-default-color="<?php echo esc_attr( $color['color'] ); ?>" />
                                        </div>
                                        <div class="cpm-color-actions">
                                            <button type="button" class="button cpm-remove-color"><?php esc_html_e( 'Remover', 'color-palette-manager' ); ?></button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cpm-color-actions-main">
                            <button type="button" class="button button-secondary cpm-add-color" data-container="gray-tones-container">
                                <span class="dashicons dashicons-plus"></span>
                                <?php esc_html_e( 'Adicionar Cor', 'color-palette-manager' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Como Usar -->
                    <div id="como-usar" class="cpm-tab-content">
                        <h2><?php esc_html_e( 'Como Usar a Paleta de Cores', 'color-palette-manager' ); ?></h2>
                        
                        <div class="cpm-how-to-use">
                            <div class="cpm-how-to-use-section">
                                <h3><?php esc_html_e( 'Configurando sua Paleta', 'color-palette-manager' ); ?></h3>
                                <ol>
                                    <li><?php esc_html_e( 'Adicione suas cores primárias, secundárias e tons de cinza nas respectivas abas.', 'color-palette-manager' ); ?></li>
                                    <li><?php esc_html_e( 'Para cada cor, forneça um nome descritivo e selecione o valor hexadecimal usando o seletor de cores.', 'color-palette-manager' ); ?></li>
                                    <li><?php esc_html_e( 'Clique no botão "Salvar Alterações" para aplicar suas configurações.', 'color-palette-manager' ); ?></li>
                                </ol>
                            </div>
                            
                            <div class="cpm-how-to-use-section">
                                <h3><?php esc_html_e( 'Usando no Elementor', 'color-palette-manager' ); ?></h3>
                                <ol>
                                    <li><?php esc_html_e( 'Ao editar uma página ou template com o Elementor, suas cores personalizadas estarão disponíveis no seletor de cores.', 'color-palette-manager' ); ?></li>
                                    <li><?php esc_html_e( 'As cores estarão organizadas nas categorias "Primárias", "Secundárias" e "Tons de Cinza".', 'color-palette-manager' ); ?></li>
                                    <li><?php esc_html_e( 'Você pode usar essas cores para textos, fundos, bordas e outros elementos.', 'color-palette-manager' ); ?></li>
                                </ol>
                            </div>
                            
                            <div class="cpm-how-to-use-section">
                                <h3><?php esc_html_e( 'Usando no Tema Customizer', 'color-palette-manager' ); ?></h3>
                                <ol>
                                    <li><?php esc_html_e( 'Acesse Aparência > Personalizar no menu do WordPress.', 'color-palette-manager' ); ?></li>
                                    <li><?php esc_html_e( 'Na seção "Paleta de Cores", você encontrará suas cores personalizadas.', 'color-palette-manager' ); ?></li>
                                    <li><?php esc_html_e( 'Você pode ajustar as cores diretamente no Customizer e ver as alterações em tempo real.', 'color-palette-manager' ); ?></li>
                                </ol>
                            </div>
                            
                            <div class="cpm-how-to-use-section">
                                <h3><?php esc_html_e( 'Usando via CSS', 'color-palette-manager' ); ?></h3>
                                <p><?php esc_html_e( 'Suas cores são disponibilizadas como variáveis CSS que você pode usar em seu tema ou personalizações de CSS:', 'color-palette-manager' ); ?></p>
                                <pre><code>/* Exemplo de uso de variáveis de cor */
.meu-elemento {
    background-color: var(--primary-nome-da-cor);
    color: var(--secondary-nome-da-cor);
    border: 1px solid var(--gray-nome-da-cor);
}</code></pre>
                                <p><?php esc_html_e( 'Substitua "nome-da-cor" pelo nome sanitizado da cor (em minúsculas, sem espaços ou caracteres especiais).', 'color-palette-manager' ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="cpm-submit-container">
                <button type="button" id="cpm-save-settings" class="button button-primary">
                    <?php esc_html_e( 'Salvar Alterações', 'color-palette-manager' ); ?>
                </button>
                <div id="cpm-save-message" class="hidden"></div>
            </div>
        </div>
        <?php
    }

    /**
     * Exibe a página de documentação
     */
    public function display_documentation_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap cpm-documentation-wrap">
            <h1><?php esc_html_e( 'Documentação da Paleta de Cores', 'color-palette-manager' ); ?></h1>
            
            <div class="cpm-documentation-content">
                <div class="cpm-doc-section">
                    <h2><?php esc_html_e( 'Introdução', 'color-palette-manager' ); ?></h2>
                    <p><?php esc_html_e( 'O plugin Color Palette Manager permite que você crie e gerencie uma paleta de cores consistente para seu site WordPress. Com ele, você pode definir cores primárias, secundárias e tons de cinza, que serão disponibilizados para uso no Elementor, no Customizer do WordPress e via variáveis CSS.', 'color-palette-manager' ); ?></p>
                </div>
                
                <div class="cpm-doc-section">
                    <h2><?php esc_html_e( 'Configurando sua Paleta de Cores', 'color-palette-manager' ); ?></h2>
                    
                    <h3><?php esc_html_e( 'Cores Primárias', 'color-palette-manager' ); ?></h3>
                    <p><?php esc_html_e( 'As cores primárias são as cores principais do seu projeto ou marca. Geralmente, incluem 1-3 cores que representam sua identidade visual.', 'color-palette-manager' ); ?></p>
                    <div class="cpm-doc-steps">
                        <div class="cpm-doc-step">
                            <h4><?php esc_html_e( '1. Acessar a Aba de Cores Primárias', 'color-palette-manager' ); ?></h4>
                            <p><?php esc_html_e( 'Na página "Paleta de Cores", selecione a aba "Cores Primárias".', 'color-palette-manager' ); ?></p>
                            <div class="cpm-doc-image">
                                <img src="<?php echo esc_url( CPM_PLUGIN_URL . 'assets/images/primary-tab.png' ); ?>" alt="<?php esc_attr_e( 'Aba de Cores Primárias', 'color-palette-manager' ); ?>">
                            </div>
                        </div>
                        
                        <div class="cpm-doc-step">
                            <h4><?php esc_html_e( '2. Adicionar Nova Cor', 'color-palette-manager' ); ?></h4>
                            <p><?php esc_html_e( 'Clique no botão "Adicionar Cor" para incluir uma nova cor primária.', 'color-palette-manager' ); ?></p>
                            <div class="cpm-doc-image">
                                <img src="<?php echo esc_url( CPM_PLUGIN_URL . 'assets/images/add-color.png' ); ?>" alt="<?php esc_attr_e( 'Adicionar Nova Cor', 'color-palette-manager' ); ?>">
                            </div>
                        </div>
                        
                        <div class="cpm-doc-step">
                            <h4><?php esc_html_e( '3. Configurar a Cor', 'color-palette-manager' ); ?></h4>
                            <p><?php esc_html_e( 'Digite um nome descritivo para a cor e selecione o valor hexadecimal usando o seletor de cores.', 'color-palette-manager' ); ?></p>
                            <div class="cpm-doc-image">
                                <img src="<?php echo esc_url( CPM_PLUGIN_URL . 'assets/images/color-picker.png' ); ?>" alt="<?php esc_attr_e( 'Configurar a Cor', 'color-palette-manager' ); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <h3><?php esc_html_e( 'Cores Secundárias e Tons de Cinza', 'color-palette-manager' ); ?></h3>
                    <p><?php esc_html_e( 'O processo para configurar cores secundárias e tons de cinza é semelhante ao das cores primárias. Basta navegar até a aba correspondente e seguir os mesmos passos.', 'color-palette-manager' ); ?></p>
                </div>
                
                <div class="cpm-doc-section">
                    <h2><?php esc_html_e( 'Usando sua Paleta de Cores', 'color-palette-manager' ); ?></h2>
                    
                    <h3><?php esc_html_e( 'No Elementor', 'color-palette-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Suas cores personalizadas estarão disponíveis no seletor de cores do Elementor, organizadas por categoria.', 'color-palette-manager' ); ?></p>
                    <div class="cpm-doc-image">
                        <img src="<?php echo esc_url( CPM_PLUGIN_URL . 'assets/images/elementor-colors.png' ); ?>" alt="<?php esc_attr_e( 'Cores no Elementor', 'color-palette-manager' ); ?>">
                    </div>
                    
                    <h3><?php esc_html_e( 'No Customizer do WordPress', 'color-palette-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Acesse Aparência > Personalizar e encontre a seção "Paleta de Cores" para ajustar suas cores.', 'color-palette-manager' ); ?></p>
                    <div class="cpm-doc-image">
                        <img src="<?php echo esc_url( CPM_PLUGIN_URL . 'assets/images/customizer-colors.png' ); ?>" alt="<?php esc_attr_e( 'Cores no Customizer', 'color-palette-manager' ); ?>">
                    </div>
                    
                    <h3><?php esc_html_e( 'Via CSS', 'color-palette-manager' ); ?></h3>
                    <p><?php esc_html_e( 'Você pode usar suas cores como variáveis CSS em seu tema ou personalizações:', 'color-palette-manager' ); ?></p>
                    <pre><code>/* Exemplo de uso de variáveis de cor */
.meu-elemento {
    background-color: var(--primary-nome-da-cor);
    color: var(--secondary-nome-da-cor);
    border: 1px solid var(--gray-nome-da-cor);
}</code></pre>
                </div>
                
                <div class="cpm-doc-section">
                    <h2><?php esc_html_e( 'Perguntas Frequentes', 'color-palette-manager' ); ?></h2>
                    
                    <div class="cpm-doc-faq">
                        <div class="cpm-doc-question">
                            <h3><?php esc_html_e( 'Posso exportar minha paleta de cores?', 'color-palette-manager' ); ?></h3>
                            <p><?php esc_html_e( 'Atualmente, não oferecemos uma função de exportação direta. No entanto, você pode copiar as configurações do banco de dados do WordPress usando plugins de backup ou exportação.', 'color-palette-manager' ); ?></p>
                        </div>
                        
                        <div class="cpm-doc-question">
                            <h3><?php esc_html_e( 'As cores são responsivas?', 'color-palette-manager' ); ?></h3>
                            <p><?php esc_html_e( 'Sim, as cores são aplicadas consistentemente em todos os dispositivos. As variáveis CSS funcionam em todos os navegadores modernos.', 'color-palette-manager' ); ?></p>
                        </div>
                        
                        <div class="cpm-doc-question">
                            <h3><?php esc_html_e( 'Posso usar formatos de cor além de hexadecimal?', 'color-palette-manager' ); ?></h3>
                            <p><?php esc_html_e( 'No momento, o plugin suporta apenas valores hexadecimais. Planejamos adicionar suporte para RGB, RGBA e HSL em versões futuras.', 'color-palette-manager' ); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="cpm-doc-section">
                    <h2><?php esc_html_e( 'Suporte', 'color-palette-manager' ); ?></h2>
                    <p><?php esc_html_e( 'Se você tiver alguma dúvida ou precisar de ajuda, entre em contato conosco através do nosso site ou envie um e-mail para support@example.com.', 'color-palette-manager' ); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
}