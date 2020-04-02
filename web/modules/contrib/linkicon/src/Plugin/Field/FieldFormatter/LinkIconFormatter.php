<?php

namespace Drupal\linkicon\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
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

  use LinkIconFormatterTrait;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The linkicon service.
   *
   * @var \Drupal\linkicon\LinkIconManagerInterface
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
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, PathValidatorInterface $path_validator, RendererInterface $renderer, LinkIconManagerInterface $linkicon_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $path_validator);
    $this->renderer        = $renderer;
    $this->linkIconManager = $linkicon_manager;
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
    $element     = $contents = [];
    $entity      = $items->getEntity();
    $entity_type = $entity->getEntityTypeId();
    $bundle      = $entity->bundle();
    $id          = $entity->id();
    $field_name  = $this->fieldDefinition->getName();
    $language    = \Drupal::languageManager()->getCurrentLanguage();
    $config      = $this->linkIconManager->simplifySettings($this->getSettings());

    foreach ($items as $delta => $item) {
      $attributes = ['class' => ['linkicon__item']];
      $prefix_class = Html::escape($config['prefix']);

      // Linkicon requires both link text and URL available with proper
      // validation during input, no need extra checks.
      $icon_name     = $item->title;
      $display_title = isset($item->display_title) ? $item->display_title : $icon_name;
      $tooltip       = isset($item->tooltip) ? $item->tooltip : $display_title;
      $icon_class    = Html::cleanCssIdentifier(Unicode::strtolower($prefix_class . '-' . $icon_name));

      // If title is overriden with a generic one, be sure the $icon_class is
      // not overridden.
      if (!empty($config['global_title']) && empty($config['no_text'])) {
        $display_title = $config['global_title'];
      }

      // The link title/content.
      // Tokenized text is sanitized by default, the rest is twig-autoescaped,
      // see #2296163.
      $token         = \Drupal::token();
      $display_title = $token->replace($display_title, [$entity_type => $entity], ['langcode' => $language->getId()]);
      $tooltip       = $token->replace($tooltip, [$entity_type => $entity], ['langcode' => $language->getId()]);
      $maxlength     = $config['maxlength'] ?: 60;
      $icon_element  = [
        '#theme'     => 'linkicon_item',
        '#title'     => Unicode::truncate($display_title, $maxlength, TRUE, TRUE),
        '#icon_name' => $icon_name,
        '#settings'  => $config,
      ];

      // The link/A tag.
      $url = $this->buildUrl($item);
      $options = $url->getOptions();

      // Without icon, displaying title only.
      if (!empty($config['link'])) {
        $attributes['class'][] = $prefix_class;
        $attributes['class'][] = $icon_class;
      }

      // Our pure CSS3 tooltip depends on data-title.
      if ($config['tooltip']) {
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
        '#config'      => $config,
      ];

      // Attached our assets if so configured.
      if ($this->linkIconManager->getSetting('font')) {
        $element['#attached']['library'][] = 'linkicon/linkicon.font';
      }
      if ($config['load']) {
        $element['#attached']['library'][] = 'linkicon/linkicon';
      }

      $info = [
        '#title' => $this->fieldDefinition->getLabel(),
        '#label_display' => $this->label,
        '#view_mode' => $this->viewMode,
        '#language' => $items->getLangcode(),
        '#field_name' => $field_name,
        '#field_type' => $this->fieldDefinition->getType(),
        '#field_translatable' => $this->fieldDefinition->isTranslatable(),
        '#entity_type' => $entity_type,
        '#bundle' => $bundle,
        '#is_multiple' => $this->fieldDefinition->getFieldStorageDefinition()->isMultiple(),
      ];

      $element = array_merge($info, $element);
    }

    return $element;
  }

}
