<?php

namespace Drupal\share_everywhere\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Path\PathValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Provides a settings form for Share Everywhere module.
 */
class ShareEverywhereConfigForm extends ConfigFormBase {
  /**
   * The module Handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The entity type Bundle Information.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The entity display Repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * The entity field Manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The path Validator.
   *
   * @var \Drupal\Core\Path\PathValidator
   */
  protected $pathValidator;

  /**
   * The render cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $renderCache;

  /**
   * Constructs a \Drupal\user\ShareEverywhereConfigForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module Handler.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle information.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   *   The entity display Repository.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field Manager.
   * @param \Drupal\Core\Path\PathValidator $path_validator
   *   The path Validator.
   * @param \Drupal\Core\Cache\CacheBackendInterface $render_cache
   *   The render cache.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, EntityTypeBundleInfoInterface $entity_type_bundle_info, EntityDisplayRepositoryInterface $entity_display_repository, EntityFieldManagerInterface $entity_field_manager, PathValidator $path_validator, CacheBackendInterface $render_cache) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityDisplayRepository = $entity_display_repository;
    $this->entityFieldManager = $entity_field_manager;
    $this->pathValidator = $path_validator;
    $this->renderCache = $render_cache;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_display.repository'),
      $container->get('entity_field.manager'),
      $container->get('path.validator'),
      $container->get('cache.render')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'share_everywhere.config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['share_everywhere.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('share_everywhere.settings');
    $entity_bundles = $this->entityTypeBundleInfo->getBundleInfo('node');
    $entity_view_modes = $this->entityDisplayRepository->getViewModes('node');
    $view_modes = [];
    $content_types = [];
    $commerce_product = $this->moduleHandler->moduleExists('commerce_product');

    if ($commerce_product) {
      $product_entity_bundles = $this->entityTypeBundleInfo->getBundleInfo('commerce_product');
      $product_entity_view_modes = $this->entityDisplayRepository->getViewModes('commerce_product');
      $product_types = [];
      $product_view_modes = [];
    }

    $form['buttons'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Share Everywhere Buttons'),
      '#description' => $this->t('Enable/disable individual buttons.'),
    ];
    $form['buttons']['title'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'clearfix',
        ],
      ],
    ];
    $form['buttons']['title']['title_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config->get('title'),
    ];
    $form['buttons']['title']['display_title'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display title'),
      '#default_value' => $config->get('display_title'),
    ];

    foreach ($config->get('buttons') as $key => $button) {
      $form['buttons'][$key] = [
        '#type' => 'checkbox',
        '#parents' => ['buttons', $key],
        '#title' => $button['name'],
        '#default_value' => $button['enabled'],
      ];
    }
    $form['display'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Display settings'),
      '#description' => $this->t('Configure where the Share Everywhere module should appear.'),
    ];
    $form['display']['style'] = [
      '#type' => 'radios',
      '#title' => $this->t('Style'),
      '#options' => [
        'share_everywhere' => $this->t('Share Everywhere'),
        'custom' => $this->t('Custom'),
      ],
      '#description' => $this->t('Select the style of the buttons.'),
      '#default_value' => $config->get('style'),
    ];
    $form['display']['libraries'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Libraries'),
      '#options' => [
        'include_css' => $this->t('Include default CSS.'),
        'include_js' => $this->t('Include default JavaScript.'),
      ],
      '#description' => $this->t('Select which libraries to include.'),
      '#default_value' => [$config->get('include_css'), $config->get('include_js')],
      '#states' => [
        'visible' => [
          'input[name="style"]' => ['value' => 'custom'],
        ],
      ],
    ];
    $form['display']['collapsible'] = [
      '#type' => 'radios',
      '#title' => $this->t('Collapsible'),
      '#options' => [
        0 => $this->t('Always open'),
        1 => $this->t('Collapsible'),
      ],
      '#description' => $this->t('Select whether the buttons should be always visible or their container can be closed and opened.'),
      '#default_value' => $config->get('collapsible'),
      '#states' => [
        'visible' => [
          ['input[name="style"]' => ['value' => 'share_everywhere']],
          'or',
          ['input[name="libraries[include_js]"]' => ['checked' => TRUE]],
        ],
      ],
    ];
    $form['display']['location'] = [
      '#type' => 'radios',
      '#title' => $this->t('Location'),
      '#options' => [
        'content' => $this->t('Node content'),
        'links' => $this->t('Links area'),
      ],
      '#description' => $this->t('Select the location where the buttons will appear.'),
      '#default_value' => $config->get('location'),
    ];
    $form['display']['alignment'] = [
      '#type' => 'radios',
      '#title' => $this->t('Alignment'),
      '#options' => [
        'left' => $this->t('Align left'),
        'right' => $this->t('Align right'),
      ],
      '#description' => $this->t('Select which side of the page will the buttons appear.'),
      '#default_value' => $config->get('alignment'),
    ];
    $form['display']['visibility'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Visibility'),
    ];
    $form['display']['content'] = [
      '#type' => 'details',
      '#title' => $this->t('Content types'),
      '#group' => 'visibility',
    ];
    $form['display']['views'] = [
      '#type' => 'details',
      '#title' => $this->t('View modes'),
      '#group' => 'visibility',
    ];

    foreach ($entity_view_modes as $mode => $mode_info) {
      $view_modes[$mode] = $mode_info['label'];
    }
    foreach ($entity_bundles as $bundle => $bundle_info) {
      if ($config->get('view_modes.' . $bundle) == NULL) {
        $config->set('view_modes.' . $bundle, ['full' => 'full'])->save();
      }
      $form['display']['views'][$bundle . '_options'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('%label View Modes', ['%label' => $bundle_info['label']]),
        '#description' => $this->t('Select which view modes the Share Everywhere module should appear on for %label nodes.', ['%label' => $bundle_info['label']]),
        '#options' => $view_modes,
        '#default_value' => $config->get('view_modes.' . $bundle),
      ];

      $content_types[$bundle] = $bundle_info['label'];
    }
    $form['display']['content']['content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Content types'),
      '#description' => $this->t('Select which content types the Share Everywhere module should appear on.'),
      '#options' => $content_types,
      '#default_value' => $config->get('content_types'),
    ];

    if ($commerce_product) {
      $form['display']['product'] = [
        '#type' => 'details',
        '#title' => $this->t('Product types'),
        '#group' => 'visibility',
      ];
      $form['display']['product_views'] = [
        '#type' => 'details',
        '#title' => $this->t('Product view modes'),
        '#group' => 'visibility',
      ];
      foreach ($product_entity_view_modes as $mode => $mode_info) {
        $product_view_modes[$mode] = $mode_info['label'];
      }
      foreach ($product_entity_bundles as $bundle => $bundle_info) {
        if ($config->get('product_view_modes.' . $bundle) == NULL) {
          $config->set('product_view_modes.' . $bundle, ['full' => 'full'])->save();
        }
        $form['display']['product_views'][$bundle . '_options'] = [
          '#type' => 'checkboxes',
          '#title' => $this->t('%label View Modes', ['%label' => $bundle_info['label']]),
          '#description' => $this->t('Select which view modes the Share Everywhere module should appear on for %label products.', ['%label' => $bundle_info['label']]),
          '#options' => $product_view_modes,
          '#default_value' => $config->get('product_view_modes.' . $bundle),
        ];

        $product_types[$bundle] = $bundle_info['label'];
      }
      $form['display']['product']['product_types'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Product types'),
        '#description' => $this->t('Select which product types the Share Everywhere module should appear on.'),
        '#options' => $product_types,
        '#default_value' => $config->get('product_types'),
      ];
    }

    $form['display']['request_path'] = [
      '#type' => 'details',
      '#title' => $this->t('Pages'),
      '#group' => 'visibility',
    ];
    $pages = is_array($config->get('restricted_pages.pages')) ? implode("\r\n", $config->get('restricted_pages.pages')) : $config->get('restricted_pages.pages');
    $form['display']['request_path']['pages'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Pages'),
      '#default_value' => $pages,
      '#description' => $this->t("Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. An example path is /user/* for every user page. &lt;front&gt; is the front page."),
    ];
    $form['display']['request_path']['type'] = [
      '#type' => 'radios',
      '#options' => ['show' => $this->t('Show for the listed pages'), 'hide' => $this->t('Hide for the listed pages')],
      '#default_value' => $config->get('restricted_pages.type'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $pages = $form_state->getValue('pages');
    if (!empty($pages)) {
      $pages = explode("\r\n", $pages);
      foreach ($pages as $page) {
        if (array_search('*', preg_split('/\//', $page, NULL, PREG_SPLIT_NO_EMPTY)) === FALSE) {
          if (!$this->pathValidator->isValid($page)) {
            $form_state->setErrorByName('pages', $this->t('One or more of the restricted pages does not exist. Please specify existing paths in the correct form.'));
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('share_everywhere.settings');
    $entity_types = $this->entityTypeBundleInfo->getBundleInfo('node');
    $form_values = $form_state->getValues();
    $current_location = $config->get('location');
    $new_location = $form_values['location'];
    $commerce_product = $this->moduleHandler->moduleExists('commerce_product');

    if (($current_location == 'content' || $new_location == 'content') && $current_location != $new_location) {
      $this->entityFieldManager->clearCachedFieldDefinitions();
    }

    foreach ($form_values['buttons'] as $key => $value) {
      $config->set('buttons.' . $key . '.enabled', $value)->save();
    }

    $config->set('title', $form_values['title_text'])
      ->set('display_title', $form_values['display_title'])
      ->set('style', $form_values['style'])
      ->set('include_css', $form_values['libraries']['include_css'])
      ->set('include_js', $form_values['libraries']['include_js'])
      ->set('collapsible', $form_values['collapsible'])
      ->set('location', $form_values['location'])
      ->set('alignment', $form_values['alignment'])
      ->set('content_types', $form_values['content_types'])
      ->save();

    if (!empty($entity_types)) {
      foreach ($entity_types as $key => $entity_type) {
        $config->set('view_modes.' . $key, $form_values[$key . '_options'])->save();
      }
    }

    if ($commerce_product) {
      $config->set('product_types', $form_values['product_types'])->save();
      $entity_types = $this->entityTypeBundleInfo->getBundleInfo('commerce_product');
      if (!empty($entity_types)) {
        foreach ($entity_types as $key => $entity_type) {
          $config->set('product_view_modes.' . $key, $form_values[$key . '_options'])->save();
        }
      }
    }

    if (!empty(trim($form_values['pages']))) {
      $pages = explode("\r\n", $form_values['pages']);
      $config->set('restricted_pages.pages', $pages)
        ->set('restricted_pages.type', $form_values['type'])
        ->save();
    }
    else {
      $config->set('restricted_pages.pages', '')->save();
    }

    $this->renderCache->deleteAll();
    parent::submitForm($form, $form_state);
  }

}
