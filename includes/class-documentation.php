<?php
/**
 * Classe de documentação do plugin
 *
 * @package FontStyleElementor
 */

// Se este arquivo for chamado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe para gerenciar a documentação do plugin
 */
class FSE_Documentation {

    /**
     * Inicializa a documentação
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_documentation_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_documentation_styles' ) );
    }

    /**
     * Adiciona página de documentação ao painel
     */
    public function add_documentation_page() {
        // Corrigindo para usar 'elementor' como página pai
        add_submenu_page(
            'elementor',
            esc_html__( 'Font Style Documentation', 'font-style-for-elementor' ),
            esc_html__( 'Font Style Docs', 'font-style-for-elementor' ),
            'manage_options', // Alterado para gerenciar_opções para garantir permissões corretas
            'font-style-elementor-docs', // Modificado o slug para evitar conflitos
            array( $this, 'display_documentation_page' )
        );
    }

    /**
     * Enfileira estilos para a página de documentação
     *
     * @param string $hook Hook atual.
     */
    public function enqueue_documentation_styles( $hook ) {
        // Verificar se estamos na página de documentação correta
        if ( 'elementor_page_font-style-elementor-docs' !== $hook ) {
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
     * Exibe a página de documentação
     */
    public function display_documentation_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Você não tem permissões suficientes para acessar esta página.', 'font-style-for-elementor' ) );
        }
        ?>
        <div class="wrap fse-documentation-wrap">
            <h1><?php esc_html_e( 'Font Style for Elementor - Documentação', 'font-style-for-elementor' ); ?></h1>
            
            <div class="fse-documentation-content">
                <div class="fse-doc-section">
                    <h2><?php esc_html_e( 'Introdução', 'font-style-for-elementor' ); ?></h2>
                    <p><?php esc_html_e( 'O plugin Font Style for Elementor oferece uma maneira fácil de aplicar estilos de fonte responsivos e consistentes em todos os elementos do Elementor. Com classes de alta especificidade, você pode garantir que seus estilos de fonte sejam aplicados corretamente em todo o seu site.', 'font-style-for-elementor' ); ?></p>
                </div>
                
                <div class="fse-doc-section">
                    <h2><?php esc_html_e( 'Como Usar', 'font-style-for-elementor' ); ?></h2>
                    <p><?php esc_html_e( 'Após instalar e ativar o plugin, você terá acesso a novas opções de estilo no painel avançado de configurações do Elementor. Siga os passos abaixo para aplicar os estilos de fonte:', 'font-style-for-elementor' ); ?></p>
                    
                    <div class="fse-doc-steps">
                        <div class="fse-doc-step">
                            <h3><?php esc_html_e( 'Passo 1: Edite um Widget', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'Abra o editor do Elementor e selecione qualquer widget ou seção que deseja estilizar.', 'font-style-for-elementor' ); ?></p>
                            <?php if ( file_exists( FSE_PLUGIN_DIR . 'assets/images/step1.jpg' ) ) : ?>
                            <div class="fse-doc-image">
                                <img src="<?php echo esc_url( FSE_PLUGIN_URL . 'assets/images/step1.jpg' ); ?>" alt="<?php esc_attr_e( 'Editar Widget', 'font-style-for-elementor' ); ?>">
                            </div>
                            <?php else : ?>
                            <div class="fse-doc-image-placeholder">
                                <p><?php esc_html_e( 'Imagem: Selecione um widget no editor do Elementor', 'font-style-for-elementor' ); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="fse-doc-step">
                            <h3><?php esc_html_e( 'Passo 2: Acesse as Configurações Avançadas', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'No painel de edição, navegue até a aba "Avançado" e localize a seção "Estilos de Fonte".', 'font-style-for-elementor' ); ?></p>
                            <?php if ( file_exists( FSE_PLUGIN_DIR . 'assets/images/step2.jpg' ) ) : ?>
                            <div class="fse-doc-image">
                                <img src="<?php echo esc_url( FSE_PLUGIN_URL . 'assets/images/step2.jpg' ); ?>" alt="<?php esc_attr_e( 'Acessar Configurações Avançadas', 'font-style-for-elementor' ); ?>">
                            </div>
                            <?php else : ?>
                            <div class="fse-doc-image-placeholder">
                                <p><?php esc_html_e( 'Imagem: Navegue até a aba Avançado', 'font-style-for-elementor' ); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="fse-doc-step">
                            <h3><?php esc_html_e( 'Passo 3: Selecione os Estilos de Fonte', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'Escolha o tamanho e o peso da fonte desejados nos menus suspensos.', 'font-style-for-elementor' ); ?></p>
                            <?php if ( file_exists( FSE_PLUGIN_DIR . 'assets/images/step3.jpg' ) ) : ?>
                            <div class="fse-doc-image">
                                <img src="<?php echo esc_url( FSE_PLUGIN_URL . 'assets/images/step3.jpg' ); ?>" alt="<?php esc_attr_e( 'Selecionar Estilos de Fonte', 'font-style-for-elementor' ); ?>">
                            </div>
                            <?php else : ?>
                            <div class="fse-doc-image-placeholder">
                                <p><?php esc_html_e( 'Imagem: Selecione os estilos de fonte', 'font-style-for-elementor' ); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="fse-doc-step">
                            <h3><?php esc_html_e( 'Passo 4: Salve as Alterações', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'Clique em "Atualizar" ou "Publicar" para aplicar os estilos ao seu site.', 'font-style-for-elementor' ); ?></p>
                            <?php if ( file_exists( FSE_PLUGIN_DIR . 'assets/images/step4.jpg' ) ) : ?>
                            <div class="fse-doc-image">
                                <img src="<?php echo esc_url( FSE_PLUGIN_URL . 'assets/images/step4.jpg' ); ?>" alt="<?php esc_attr_e( 'Salvar Alterações', 'font-style-for-elementor' ); ?>">
                            </div>
                            <?php else : ?>
                            <div class="fse-doc-image-placeholder">
                                <p><?php esc_html_e( 'Imagem: Clique em Atualizar/Publicar', 'font-style-for-elementor' ); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="fse-doc-section">
                    <h2><?php esc_html_e( 'Tamanhos de Fonte Disponíveis', 'font-style-for-elementor' ); ?></h2>
                    <div class="fse-doc-table">
                        <table>
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Nome', 'font-style-for-elementor' ); ?></th>
                                    <th><?php esc_html_e( 'Tamanho Desktop', 'font-style-for-elementor' ); ?></th>
                                    <th><?php esc_html_e( 'Tamanho Tablet', 'font-style-for-elementor' ); ?></th>
                                    <th><?php esc_html_e( 'Tamanho Mobile', 'font-style-for-elementor' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Display Large</td>
                                    <td>80px</td>
                                    <td>64px</td>
                                    <td>48px</td>
                                </tr>
                                <tr>
                                    <td>Display Small</td>
                                    <td>72px</td>
                                    <td>56px</td>
                                    <td>40px</td>
                                </tr>
                                <tr>
                                    <td>H1</td>
                                    <td>64px</td>
                                    <td>48px</td>
                                    <td>36px</td>
                                </tr>
                                <tr>
                                    <td>H2</td>
                                    <td>56px</td>
                                    <td>40px</td>
                                    <td>32px</td>
                                </tr>
                                <tr>
                                    <td>H3</td>
                                    <td>48px</td>
                                    <td>32px</td>
                                    <td>24px</td>
                                </tr>
                                <tr>
                                    <td>H4</td>
                                    <td>40px</td>
                                    <td>32px</td>
                                    <td>24px</td>
                                </tr>
                                <tr>
                                    <td>H5</td>
                                    <td>32px</td>
                                    <td>24px</td>
                                    <td>20px</td>
                                </tr>
                                <tr>
                                    <td>H6</td>
                                    <td>24px</td>
                                    <td>20px</td>
                                    <td>18px</td>
                                </tr>
                                <tr>
                                    <td>Text XL</td>
                                    <td>18px</td>
                                    <td>18px</td>
                                    <td>16px</td>
                                </tr>
                                <tr>
                                    <td>Text LG</td>
                                    <td>16px</td>
                                    <td>16px</td>
                                    <td>16px</td>
                                </tr>
                                <tr>
                                    <td>Text MD</td>
                                    <td>14px</td>
                                    <td>14px</td>
                                    <td>14px</td>
                                </tr>
                                <tr>
                                    <td>Text SM</td>
                                    <td>12px</td>
                                    <td>12px</td>
                                    <td>12px</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="fse-doc-section">
                    <h2><?php esc_html_e( 'Pesos de Fonte Disponíveis', 'font-style-for-elementor' ); ?></h2>
                    <div class="fse-doc-table">
                        <table>
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Nome', 'font-style-for-elementor' ); ?></th>
                                    <th><?php esc_html_e( 'Valor', 'font-style-for-elementor' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Regular</td>
                                    <td>400</td>
                                </tr>
                                <tr>
                                    <td>Medium</td>
                                    <td>500</td>
                                </tr>
                                <tr>
                                    <td>Semi Bold</td>
                                    <td>600</td>
                                </tr>
                                <tr>
                                    <td>Bold</td>
                                    <td>700</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="fse-doc-section">
                    <h2><?php esc_html_e( 'Dicas Avançadas', 'font-style-for-elementor' ); ?></h2>
                    
                    <div class="fse-doc-tip">
                        <h3><?php esc_html_e( 'Combinando Tamanho e Peso', 'font-style-for-elementor' ); ?></h3>
                        <p><?php esc_html_e( 'Você pode combinar diferentes tamanhos e pesos para criar estilos de fonte personalizados. Por exemplo, use o tamanho "H1" com o peso "Semi Bold" para criar um título principal distinto.', 'font-style-for-elementor' ); ?></p>
                    </div>
                    
                    <div class="fse-doc-tip">
                        <h3><?php esc_html_e( 'Aplicação Seletiva', 'font-style-for-elementor' ); ?></h3>
                        <p><?php esc_html_e( 'Os estilos podem ser aplicados em diferentes níveis: seções inteiras, colunas, contêineres ou widgets individuais. Escolha o nível de aplicação de acordo com a necessidade do seu design.', 'font-style-for-elementor' ); ?></p>
                    </div>
                    
                    <div class="fse-doc-tip">
                        <h3><?php esc_html_e( 'Consistência', 'font-style-for-elementor' ); ?></h3>
                        <p><?php esc_html_e( 'Para manter a consistência no seu site, defina um padrão de uso para cada estilo. Por exemplo, use sempre "H1" para títulos de página, "H2" para seções principais, etc.', 'font-style-for-elementor' ); ?></p>
                    </div>
                </div>
                
                <div class="fse-doc-section">
                    <h2><?php esc_html_e( 'Perguntas Frequentes', 'font-style-for-elementor' ); ?></h2>
                    
                    <div class="fse-doc-faq">
                        <div class="fse-doc-question">
                            <h3><?php esc_html_e( 'O plugin afeta o desempenho do meu site?', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'Não, o plugin foi desenvolvido para ser leve e eficiente. Ele adiciona apenas um pequeno arquivo CSS ao seu site, que é carregado apenas quando necessário.', 'font-style-for-elementor' ); ?></p>
                        </div>
                        
                        <div class="fse-doc-question">
                            <h3><?php esc_html_e( 'Posso personalizar os tamanhos de fonte?', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'Atualmente, os tamanhos são predefinidos para garantir consistência e responsividade. Em versões futuras, planejamos adicionar opções para personalização dos tamanhos.', 'font-style-for-elementor' ); ?></p>
                        </div>
                        
                        <div class="fse-doc-question">
                            <h3><?php esc_html_e( 'Os estilos funcionam com todos os temas?', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'Sim, o plugin foi projetado para funcionar com qualquer tema compatível com o Elementor. As classes têm alta especificidade para garantir que seus estilos sejam aplicados corretamente.', 'font-style-for-elementor' ); ?></p>
                        </div>
                        
                        <div class="fse-doc-question">
                            <h3><?php esc_html_e( 'Posso usar os estilos em textos em diferentes idiomas?', 'font-style-for-elementor' ); ?></h3>
                            <p><?php esc_html_e( 'Sim, os estilos funcionam com qualquer idioma suportado pelo seu site WordPress.', 'font-style-for-elementor' ); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="fse-doc-section">
                    <h2><?php esc_html_e( 'Suporte', 'font-style-for-elementor' ); ?></h2>
                    <p><?php esc_html_e( 'Se você tiver alguma dúvida ou precisar de suporte, entre em contato conosco através do nosso site ou envie um e-mail para support@example.com.', 'font-style-for-elementor' ); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
}