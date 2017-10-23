<?php

namespace Drupal\linkicon\Plugin\Field\FieldFormatter;

use Drupal\Core\Url;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\linkicon\LinkIconManagerInterface;

/**
 * Plugin implementation of the 'linkicon' formatter.
 *
 * @FieldFormatter(
 *   id = "linkicon",
 *   label = @Translation("Link icon, based on title"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkIconFormatter extends LinkFormatter {

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The linkicon service.
   *
   * @var \Drupal\linkicon\LinkIconManagerInterface.
   */
  protected $linkIconManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('path.validator'),
      $container->get('renderer'),
      $container->get('linkicon.manager')
    );
  }

  /**
   * Constructs a new LinkIconFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator service.
   * @param \Drupal\linkicon\LinkIconManagerInterface $linkicon_manager
   *   The linkicon service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, PathValidatorInterface $path_validator, RendererInterface $renderer, LinkIconManagerInterface $linkicon_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $path_validator);
    $this->renderer        = $renderer;
    $this->linkIconManager = $linkicon_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'rel'                    => '',
      'target'                 => '',
      'linkicon_prefix'        => 'icon',
      'linkicon_wrapper_class' => '',
      'linkicon_load'          => FALSE,
      'linkicon_vertical'      => FALSE,
      'linkicon_style'         => '',
      'linkicon_color'         => '',
      'linkicon_tooltip'       => FALSE,
      'linkicon_maxlength'     => 60,
      'linkicon_no_text'       => FALSE,
      'linkicon_position'      => '',
      'linkicon_link'          => FALSE,
      'linkicon_global_title'  => '',
      'linkicon_size'          => '',
      'linkicon_bundle'        => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);
    $settings = $this->getSettings();

    // Predefined titles are supposed to be controlled.
    if (isset($elements['trim_length'])) {
      unset($elements['trim_length']);
    }

    $elements['opening'] = [
      '#type'   => 'item',
      '#markup' => '<h3>' . $this->t('If your theme has no icon font library, define one <a href=":url" target="_blank">here</a>.', array(':url' => Url::fromRoute('linkicon.settings')->toString())) . '</h3>',
    ];

    $elements['linkicon_prefix'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Icon prefix class'),
      '#default_value' => $settings['linkicon_prefix'],
      '#required'      => TRUE,
      '#description'   => $this->t('A "prefix" or "namespace", e.g.: icon (Fontello), fa (FontAwesome), st-icon (Stackicons), genericon, fonticon, etc. <br />If the link title is <em>Facebook</em>, it will create classes: <em>icon icon-facebook</em> for Fontello, or <em>fa fa-facebook</em> for FontAwesome > 3. <br />The individual icon class itself is based on the link text key matching the pattern: icon-KEY, or fa-KEY.'),

    ];

    $elements['linkicon_wrapper_class'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Additional wrapper class'),
      '#default_value' => $settings['linkicon_wrapper_class'],
      '#description'   => $this->t('Additional wrapper class for the entire icon list apart from <strong>item-list item-list--linkicon</strong>.'),
    ];

    $elements['linkicon_link'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Add the classes to the A tag.'),
      '#default_value' => $settings['linkicon_link'],
      '#description'   => $this->t('By default linkicon adds additional SPAN tag to hold the icon, enable this to add the classes to the A tag instead. This is all about DIY.'),
      '#states'        => [
        'visible' => [
          ':input[name*="linkicon_load"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $elements['linkicon_load'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Allow linkicon to provide CSS assets'),
      '#default_value' => $settings['linkicon_load'],
      '#description'   => $this->t('Otherwise, DIY accordingly.'),
      '#states'        => [
        'visible' => [
          ':input[name*="linkicon_link"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $elements['linkicon_vertical'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Vertical'),
      '#default_value' => $settings['linkicon_vertical'],
      '#description'   => $this->t('By default, icons are displayed inline. Check to make icons stacked vertically.'),
      '#states'        => [
        'visible' => [
          ':input[name*="linkicon_load"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $elements['linkicon_style'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Icon style'),
      '#default_value' => $settings['linkicon_style'],
      '#options'       => [
        'round'    => $this->t('Round'),
        'round-2'  => $this->t('Round 2'),
        'round-5'  => $this->t('Round 5'),
        'round-8'  => $this->t('Round 8'),
        'round-10' => $this->t('Round 10'),
        'square'   => $this->t('Square'),
        'button'   => $this->t('Button'),
      ],
      '#empty_option' => $this->t('- None -'),
      '#states'       => [
        'visible' => [
          ':input[name*="linkicon_load"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('Button is more prominent if the title text is not hidden over the background color.'),
    ];

    $elements['linkicon_color'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Icon background color'),
      '#default_value' => $settings['linkicon_color'],
      '#options'       => [
        'grey'   => $this->t('Grey'),
        'dark'   => $this->t('Dark'),
        'purple' => $this->t('Purple'),
        'orange' => $this->t('Orange'),
        'blue'   => $this->t('Blue'),
        'lime'   => $this->t('Lime'),
        'red'    => $this->t('Red'),
      ],
      '#empty_option' => $this->t('- None -'),
      '#states'       => [
        'visible' => [
          ':input[name*="linkicon_load"]' => ['checked' => TRUE],
        ],
      ],
      '#description' => $this->t('Basic background color. You should do proper theming to suit your design better, and disable all this.'),
    ];

    $elements['linkicon_tooltip'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Display title as tooltip'),
      '#default_value' => $settings['linkicon_tooltip'],
      '#states'        => [
        'visible' => [
          ':input[name*="linkicon_load"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $elements['linkicon_no_text'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Visually hide the title text'),
      '#default_value' => $settings['linkicon_no_text'],
      '#states'        => [
        'visible' => [
          ':input[name*="linkicon_load"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $elements['linkicon_maxlength'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('The title and tooltip maxlength'),
      '#description'   => $this->t('Limit the amount of characters if using token replacement for the title and tootip as defined at the widget settings, default to 60 characters.'),
      '#default_value' => $settings['linkicon_maxlength'],
      '#size'          => 6,
      '#maxlength'     => 3,
    ];

    $elements['linkicon_global_title'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Override title with a generic link title'),
      '#description'   => $this->t('If provided, the link title will be overriden with this text, e.g.: Visit the site, View Demo. Token is supported.'),
      '#default_value' => $settings['linkicon_global_title'],
      '#states'        => [
        'visible' => [
          [':input[name*="linkicon_tooltip"]' => ['checked' => FALSE]],
          [':input[name*="linkicon_no_text"]' => ['checked' => FALSE]],
        ],
      ],
    ];

    $elements['linkicon_position'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Icon position to the title text.'),
      '#default_value' => $settings['linkicon_position'],
      '#description'   => $this->t('By default icon is before the text - Left.'),
      '#options'       => [
        'bottom' => $this->t('Bottom'),
        'right'  => $this->t('Right'),
        'top'    => $this->t('Top'),
      ],
      '#empty_option' => $this->t('Left'),
      '#states'       => [
        'visible' => [
          [':input[name*="linkicon_load"]' => ['checked' => TRUE]],
          [':input[name*="linkicon_no_text"]' => ['checked' => FALSE]],
          [':input[name*="linkicon_link"]' => ['checked' => FALSE]],
        ],
      ],
    ];

    $icon_sizes = [
      'small'   => $this->t('Small'),
      'medium'  => $this->t('Medium'),
      'large'   => $this->t('Large'),
      'xlarge'  => $this->t('X-large'),
      'xxlarge' => $this->t('Xx-large'),
    ];

    $elements['linkicon_size'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Icon font size'),
      '#default_value' => $settings['linkicon_size'],
      '#options'       => $icon_sizes,
      '#empty_option'  => $this->t('Default'),
      '#states'        => [
        'visible' => [
          ':input[name*="linkicon_load"]' => ['checked' => TRUE],
        ],
      ],
    ];

    // Build a preview.
    if (function_exists('icon_bundles')) {
      $providers = icon_providers();

      $provider_options = [];
      foreach ($providers as $provider) {
        $provider_options[$provider['name']] = $provider['title'];
      }

      if ($provider_options) {
        $elements['linkicon_bundle'] = [
          '#type'          => 'select',
          '#title'         => $this->t('Icon module integration'),
          '#options'       => $provider_options,
          '#empty_option'  => $this->t('- None -'),
          '#default_value' => $settings['linkicon_bundle'],
          '#description'   => $this->t('The above icon providers modules are detected. You can choose which icon CSS file to load with this module. <br>Make sure that you have a working module that loads relevant CSS accordingly. <br>Known working modules as of this writing: fontawesome, and icomoon.'),
          '#states' => [
            'visible' => [
              ':input[name*="linkicon_link"]' => ['checked' => FALSE],
            ],
          ],
        ];
      }
    }

    $has_icon_path = $this->linkIconManager->getSetting('font');

    if (!empty($settings['linkicon_bundle']) || $has_icon_path) {
      $icon_previews = [];
      $linkicon_item = [
        '#theme'      => 'linkicon_item',
        '#position'   => $settings['linkicon_position'],
        '#title'      => 'Twitter',
        '#attributes' => [
          'class' => [
            'linkicon__icon',
            $settings['linkicon_prefix'],
            $settings['linkicon_prefix'] . '-twitter',
          ],
        ],
        '#icon_bundle' => $settings['linkicon_bundle'] ? $settings['linkicon_bundle'] : FALSE,
        '#icon_name'   => 'twitter',
        '#icon_prefix' => Html::escape($settings['linkicon_prefix']),
      ];

      $icon = $this->renderer->render($linkicon_item);

      $tooltip = '';
      if ($settings['linkicon_tooltip']) {
        $tooltip = ' data-title="Twitter"';
      }

      foreach ($icon_sizes as $key => $size) {
        $is_active = $key == $settings['linkicon_size'] ? ' active' : '';
        $icon_previews[] = ['#markup' => '<a class="linkicon__item linkicon--' . $key . $is_active . '" href="#"' . $tooltip . '>' . $icon . '</a>'];
      }

      $preview = [
        '#theme'       => 'linkicon',
        '#linkicon_id' => 'linkicon-preview',
        '#items'       => $icon_previews,
        '#config'      => [
          'style'         => $settings['linkicon_style'],
          'color'         => $settings['linkicon_color'],
          'no_text'       => $settings['linkicon_no_text'],
          'position'      => $settings['linkicon_position'],
          'tooltip'       => $settings['linkicon_tooltip'],
          'wrapper_class' => $settings['linkicon_wrapper_class'],
          'load'          => $settings['linkicon_load'],
        ],
      ];

      if ($settings['linkicon_load']) {
        if ($has_icon_path) {
          $elements['#attached']['library'][] = 'linkicon/linkicon.font';
        }
        $elements['#attached']['library'][] = 'linkicon/linkicon';
      }

      $elements['linkicon_size_preview'] = [
        '#type'   => 'item',
        '#markup' => $this->renderer->render($preview),
        '#states' => [
          'visible' => [
            ':input[name*="linkicon_link"]' => ['checked' => FALSE],
          ],
        ],
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $settings = $this->getSettings();

    if (!empty($settings['rel'])) {
      $summary[] = $this->t('Add rel="@rel"', ['@rel' => $settings['rel']]);
    }
    if (!empty($settings['target'])) {
      $summary[] = $this->t('Open link in new window');
    }

    $summary[] = $this->t('Prefix class: <em>@linkicon_prefix</em>.', [
      '@linkicon_prefix' => $settings['linkicon_prefix'],
    ]);

    if (isset($settings['linkicon_link'])) {
      $summary[] = t('Icon classes at A tag: <em>@linkicon_link</em>.', [
        '@linkicon_link' => $settings['linkicon_link'] ? t('Yes') : t('No'),
      ]);
    }

    $summary[] = $this->t('Module CSS: <em>@linkicon_load</em>. Wrapper: <em>@linkicon_wrapper_class</em>. Style: <em>@linkicon_style</em>. Bg: <em>@linkicon_color</em>.', [
      '@linkicon_load' => $settings['linkicon_load'] ? $this->t('Yes') : $this->t('No'),
      '@linkicon_wrapper_class' => $settings['linkicon_wrapper_class'] ? $settings['linkicon_wrapper_class'] : $this->t('None'),
      '@linkicon_vertical' => $settings['linkicon_vertical'] ? $this->t('Vertical') : $this->t('Horizontal'),
      '@linkicon_style' => $settings['linkicon_style'] ? $settings['linkicon_style'] : $this->t('None'),
      '@linkicon_color' => $settings['linkicon_color'] ? $settings['linkicon_color'] : $this->t('None'),
    ]);

    if ($settings['linkicon_load']) {
      $summary[] = $this->t('Size: <em>@linkicon_size</em>. No text: <em>@linkicon_no_text</em>. Tooltip: <em>@linkicon_tooltip</em>', [
        '@linkicon_size' => $settings['linkicon_size'],
        '@linkicon_no_text' => $settings['linkicon_no_text'] ? $this->t('Yes') : $this->t('No'),
        '@linkicon_tooltip' => $settings['linkicon_tooltip'] ? $this->t('Yes') : $this->t('No'),
      ]);

      if (empty($settings['linkicon_no_text'])) {
        $summary[] = $this->t('Use global title: <em>@linkicon_global_title</em>. <br>Icon position: <em>@linkicon_position</em>.', [
          '@linkicon_global_title' => $settings['linkicon_global_title'] ? $settings['linkicon_global_title'] : $this->t('No'),
          '@linkicon_position' => $settings['linkicon_position'] ? $settings['linkicon_position'] : $this->t('Left'),
        ]);
      }
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareView(array $entities_items) {
    // @todo drop 'predefined' for the new integer: LINKICON_PREDEFINED.
    if ($this->getFieldSetting('title') == LINKICON_PREDEFINED || $this->getFieldSetting('title') == 'predefined') {
      $settings = $this->getFieldSettings();
      if (!empty($settings['title_predefined'])) {
        $titles   = $this->linkIconManager->extractAllowedValues($settings['title_predefined']);
        $tooltips = $this->linkIconManager->extractAllowedValues($settings['title_predefined'], TRUE);

        foreach ($entities_items as $items) {
          $new_values = [];
          foreach ($items as $item) {
            $values = $item->getValue();
            $new_values['display_title'] = isset($titles[$values['title']]) ? $titles[$values['title']] : '';
            if (isset($tooltips[$values['title']]) && $tooltips[$values['title']]) {
              $new_values['tooltip'] = $tooltips[$values['title']];
            }
            $merged_values = array_merge($values, $new_values);
            $item->setValue($merged_values);
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element      = [];
    $contents     = [];
    $entity       = $items->getEntity();
    $entity_type  = $entity->getEntityTypeId();
    $bundle       = $entity->bundle();
    $id           = $entity->id();
    $settings     = $this->getSettings();
    $field_name   = $this->fieldDefinition->getName();
    $language_interface = \Drupal::languageManager()->getCurrentLanguage();

    foreach ($items as $delta => $item) {
      $attributes = ['class' => ['linkicon__item']];
      $prefix_class = Html::escape($settings['linkicon_prefix']);

      // Linkicon requires both link text and URL available with proper
      // validation during input, no need extra checks.
      $icon_name     = $item->title;
      $display_title = isset($item->display_title) ? $item->display_title : $icon_name;
      $tooltip       = isset($item->tooltip) ? $item->tooltip : $display_title;
      $is_a_classes  = isset($settings['linkicon_link']) && $settings['linkicon_link'];

      // @todo recheck if any icon class has any silly Uppercase.
      $icon_class = Html::cleanCssIdentifier(Unicode::strtolower($prefix_class . '-' . $icon_name));

      // If title is overriden with a generic one, be sure the $icon_class is
      // not overridden.
      if (!empty($settings['linkicon_global_title']) && empty($settings['linkicon_no_text'])) {
        $display_title = $settings['linkicon_global_title'];
      }

      // The link title/content.
      // Tokenized text is sanitized by default, the rest is twig-autoescaped,
      // see #2296163.
      $token         = \Drupal::token();
      $display_title = $token->replace($display_title, [$entity_type => $entity], ['langcode' => $language_interface->getId()]);
      $tooltip       = $token->replace($tooltip, [$entity_type => $entity], ['langcode' => $language_interface->getId()]);
      $maxlength     = $settings['linkicon_maxlength'] ?: 60;
      $icon_element  = [
        '#theme'       => 'linkicon_item',
        '#position'    => $settings['linkicon_position'],
        '#title'       => Unicode::truncate($display_title, $maxlength, TRUE, TRUE),
        '#icon_bundle' => $settings['linkicon_bundle'] ? $settings['linkicon_bundle'] : FALSE,
        '#icon_name'   => $icon_name,
        '#icon_prefix' => $prefix_class,
        '#settings'    => $settings,
        '#attributes'  => [
          'class' => [
            'linkicon__icon',
            $prefix_class,
            $icon_class,
          ],
          'aria-hidden' => 'true',
        ],
      ];

      // The link/A tag.
      $url = $this->buildUrl($item);
      $options = $url->getOptions();

      if ($is_a_classes) {
        $attributes['class'][] = $prefix_class;
        $attributes['class'][] = $icon_class;
      }

      // Our pure CSS3 tooltip depends on data-title.
      if ($settings['linkicon_tooltip']) {
        $attributes['data-title'] = Unicode::truncate($tooltip, $maxlength, TRUE, TRUE);
      }

      // Merge with core options: rel and target.
      if (isset($options['attributes']) && $options['attributes']) {
        $options['attributes'] += $attributes;
      }
      else {
        $options['attributes'] = $attributes;
      }

      // We are done, pass it over to link to do its job.
      // @todo https://www.drupal.org/node/2350519
      // No need to SafeMarkup $icon_element, as all is already sanitized above.
      $contents[$delta] = [
        '#type'    => 'link',
        '#title'   => $icon_element,
        '#url'     => $url,
        '#options' => $options,
      ];
    }

    // The UL and item-list DIV wrapper tags.
    // Build own wrapper for greater control.
    if ($contents) {
      $linkicon_id = Html::cleanCssIdentifier("{$entity_type}-{$bundle}-{$field_name}-{$id}");
      $element = [
        '#theme'       => 'linkicon',
        '#linkicon_id' => 'linkicon-' . $linkicon_id,
        '#items'       => $contents,
        '#config'      => [
          'style'         => $settings['linkicon_style'],
          'color'         => $settings['linkicon_color'],
          'size'          => $settings['linkicon_size'],
          'no_text'       => $settings['linkicon_no_text'],
          'position'      => $settings['linkicon_position'],
          'tooltip'       => $settings['linkicon_tooltip'],
          'vertical'      => $settings['linkicon_vertical'],
          'wrapper_class' => $settings['linkicon_wrapper_class'],
          'load'          => $settings['linkicon_load'],
        ],
        '#attributes' => [
          'id' => 'linkicon-' . $linkicon_id,
        ],
      ];

      // Attached our assets if so configured.
      if ($this->linkIconManager->getSetting('font')) {
        $element['#attached']['library'][] = 'linkicon/linkicon.font';
      }
      if ($settings['linkicon_load']) {
        $element['#attached']['library'][] = 'linkicon/linkicon';
      }
    }

    return $element;
  }

}
