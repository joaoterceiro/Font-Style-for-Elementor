/**
 * Scripts para a administração do Color Palette Manager
 */
(function($) {
  'use strict';

  // Variáveis globais
  let nextPrimaryIndex = 0;
  let nextSecondaryIndex = 0;
  let nextGrayIndex = 0;

  // Inicializar quando o documento estiver pronto
  $(document).ready(function() {
      // Inicializar as abas
      initTabs();
      
      // Inicializar seletores de cores
      initColorPickers();
      
      // Configurar índices de cores
      setupColorIndices();
      
      // Manipuladores de eventos
      setupEventHandlers();
  });

  /**
   * Inicializa a navegação por abas
   */
  function initTabs() {
      // Clicar em uma aba
      $('.cpm-tab-link').click(function(e) {
          e.preventDefault();
          
          const tabId = $(this).attr('data-tab');
          
          // Ativar a aba clicada e desativar as outras
          $('.cpm-tab-link').removeClass('active');
          $(this).addClass('active');
          
          // Mostrar o conteúdo da aba ativa e esconder os outros
          $('.cpm-tab-content').removeClass('active');
          $('#' + tabId).addClass('active');
          
          // Atualizar a URL com o hash
          window.location.hash = tabId;
      });
      
      // Verificar se há um hash na URL
      if (window.location.hash) {
          const hash = window.location.hash.substring(1);
          const $tab = $('.cpm-tab-link[data-tab="' + hash + '"]');
          
          if ($tab.length) {
              $tab.click();
          }
      }
  }

  /**
   * Inicializa os seletores de cores
   */
  function initColorPickers() {
      // Inicializar os seletores de cores existentes
      $('.color-picker').wpColorPicker({
          change: function(event, ui) {
              const color = ui.color.toString();
              $(this).val(color);
              $(this).closest('.cpm-color-item').find('.cpm-color-preview').css('background-color', color);
          }
      });
  }

  /**
   * Configurar os índices para cores novas
   */
  function setupColorIndices() {
      // Encontrar o maior índice para cores primárias
      $('#primary-colors-container .cpm-color-item').each(function() {
          const index = parseInt($(this).attr('data-index'), 10);
          if (index >= nextPrimaryIndex) {
              nextPrimaryIndex = index + 1;
          }
      });
      
      // Encontrar o maior índice para cores secundárias
      $('#secondary-colors-container .cpm-color-item').each(function() {
          const index = parseInt($(this).attr('data-index'), 10);
          if (index >= nextSecondaryIndex) {
              nextSecondaryIndex = index + 1;
          }
      });
      
      // Encontrar o maior índice para tons de cinza
      $('#gray-tones-container .cpm-color-item').each(function() {
          const index = parseInt($(this).attr('data-index'), 10);
          if (index >= nextGrayIndex) {
              nextGrayIndex = index + 1;
          }
      });
  }

  /**
   * Configurar manipuladores de eventos
   */
  function setupEventHandlers() {
      // Adicionar cor
      $('.cpm-add-color').click(function() {
          const containerType = $(this).data('container');
          addNewColor(containerType);
      });
      
      // Remover cor (delegação de eventos)
      $(document).on('click', '.cpm-remove-color', function() {
          $(this).closest('.cpm-color-item').fadeOut(300, function() {
              $(this).remove();
          });
      });
      
      // Atualizar preview ao mudar o nome da cor
      $(document).on('input', '.cpm-color-name', function() {
          // Pode adicionar ações adicionais, se necessário
      });
      
      // Salvar configurações
      $('#cpm-save-settings').click(function() {
          saveSettings();
      });
  }

  /**
   * Adiciona uma nova cor a um contêiner
   * 
   * @param {string} containerType Tipo de contêiner (primary-colors-container, secondary-colors-container, gray-tones-container)
   */
  function addNewColor(containerType) {
      let index = 0;
      let defaultColor = '#333333';
      
      // Escolher o índice apropriado baseado no tipo de contêiner
      if (containerType === 'primary-colors-container') {
          index = nextPrimaryIndex++;
          defaultColor = '#FF0874'; // Rosa padrão
      } else if (containerType === 'secondary-colors-container') {
          index = nextSecondaryIndex++;
          defaultColor = '#0088CC'; // Azul padrão
      } else if (containerType === 'gray-tones-container') {
          index = nextGrayIndex++;
          defaultColor = '#666666'; // Cinza padrão
      }
      
      // Criar o HTML para o novo item de cor
      const newColorHTML = `
      <div class="cpm-color-item" data-index="${index}">
          <div class="cpm-color-preview" style="background-color: ${defaultColor};"></div>
          <div class="cpm-color-inputs">
              <input type="text" class="cpm-color-name" value="" placeholder="${cpmSettings.i18n.colorName}" />
              <input type="text" class="cpm-color-value color-picker" value="${defaultColor}" data-default-color="${defaultColor}" />
          </div>
          <div class="cpm-color-actions">
              <button type="button" class="button cpm-remove-color">${cpmSettings.i18n.removeColor}</button>
          </div>
      </div>
      `;
      
      // Adicionar o novo item ao contêiner
      const $newColor = $(newColorHTML).appendTo('#' + containerType);
      
      // Inicializar o seletor de cores para o novo item
      $newColor.find('.color-picker').wpColorPicker({
          change: function(event, ui) {
              const color = ui.color.toString();
              $(this).val(color);
              $(this).closest('.cpm-color-item').find('.cpm-color-preview').css('background-color', color);
          }
      });
  }

  /**
   * Salva as configurações via AJAX
   */
  function saveSettings() {
      // Coletar dados de cores primárias
      const primaryColors = [];
      $('#primary-colors-container .cpm-color-item').each(function() {
          const name = $(this).find('.cpm-color-name').val();
          const color = $(this).find('.cpm-color-value').val();
          
          if (name && color) {
              primaryColors.push({
                  name: name,
                  color: color
              });
          }
      });
      
      // Coletar dados de cores secundárias
      const secondaryColors = [];
      $('#secondary-colors-container .cpm-color-item').each(function() {
          const name = $(this).find('.cpm-color-name').val();
          const color = $(this).find('.cpm-color-value').val();
          
          if (name && color) {
              secondaryColors.push({
                  name: name,
                  color: color
              });
          }
      });
      
      // Coletar dados de tons de cinza
      const grayTones = [];
      $('#gray-tones-container .cpm-color-item').each(function() {
          const name = $(this).find('.cpm-color-name').val();
          const color = $(this).find('.cpm-color-value').val();
          
          if (name && color) {
              grayTones.push({
                  name: name,
                  color: color
              });
          }
      });
      
      // Exibir indicador de salvamento
      const $saveButton = $('#cpm-save-settings');
      const $message = $('#cpm-save-message');
      
      $saveButton.prop('disabled', true).text('Salvando...');
      $message.removeClass('success error').addClass('hidden');
      
      // Enviar dados via AJAX
      $.ajax({
          url: cpmSettings.ajaxUrl,
          type: 'POST',
          data: {
              action: 'cpm_save_settings',
              nonce: cpmSettings.nonce,
              primary_colors: primaryColors,
              secondary_colors: secondaryColors,
              gray_tones: grayTones
          },
          success: function(response) {
              $saveButton.prop('disabled', false).text('Salvar Alterações');
              
              if (response.success) {
                  $message.text(response.data.message).removeClass('hidden').addClass('success');
                  
                  // Esconder a mensagem após 3 segundos
                  setTimeout(function() {
                      $message.fadeOut(500, function() {
                          $(this).addClass('hidden').removeClass('success error').show();
                      });
                  }, 3000);
              } else {
                  $message.text(response.data.message).removeClass('hidden').addClass('error');
              }
          },
          error: function() {
              $saveButton.prop('disabled', false).text('Salvar Alterações');
              $message.text('Erro ao salvar configurações. Por favor, tente novamente.').removeClass('hidden').addClass('error');
          }
      });
  }

})(jQuery);