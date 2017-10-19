<?php

namespace Drupal\lightgallery\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\image\Plugin\Field\FieldType\ImageItem;
use Drupal\lightgallery\Field\FieldInterface;
use Drupal\lightgallery\Field\FieldLightgalleryImageStyle;
use Drupal\lightgallery\Field\FieldThumbImageStyle;
use Drupal\lightgallery\Field\FieldTitleSource;
use Drupal\lightgallery\Field\FieldUseThumbs;
use Drupal\lightgallery\Group\GroupInterface;
use Drupal\lightgallery\Group\GroupsEnum;
use Drupal\lightgallery\Manager\LightgalleryManager;
use Drupal\lightgallery\Optionset\LightgalleryOptionset;

/**
 * @FieldFormatter(
 *   id = "lightgallery",
 *   label = @Translation("Lightgallery"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class LightgalleryFormatter extends FileFormatterBase {


  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $default_settings = array();
    $lightgallery_groups = GroupsEnum::toArray();

    foreach ($lightgallery_groups as $lightgallery_group) {
      $default_settings[$lightgallery_group] = array();
    }
    return $default_settings + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $fields_settings = LightgalleryManager::getSettingFields();
    /**
     * @var FieldInterface $field
     * @var GroupInterface $group
     */
    foreach ($fields_settings as $field) {
      $group = $field->getGroup();
      if (empty($element[$group->getName()])) {
        // Attach group to form.
        $element[$group->getName()] = array(
          '#type' => 'details',
          '#title' => $group->getTitle(),
          '#open' => $group->isOpen(),
        );
      }

      if ($field->appliesToFieldFormatter()) {
        // Attach field to group and form.
        $element[$group->getName()][$field->getName()] = array(
          '#type' => $field->getType(),
          '#title' => $this->t($field->getTitle()),
          '#default_value' => isset($this->settings[$group->getName()][$field->getName()]) ? $this->settings[$group->getName()][$field->getName()] : $field->getDefaultValue(),
          '#description' => $this->t($field->getDescription()),
          '#required' => $field->isRequired(),
        );

        if (!empty($field->getOptions())) {
          // Set field options.
          if (is_callable($field->getOptions())) {
            $element[$group->getName()][$field->getName()]['#options'] = call_user_func($field->getOptions());
          }
        }
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $image_styles = LightgalleryManager::getImageStyles();
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);

    $thumb_image_style = new FieldThumbImageStyle();
    $lightgallery_image_style = new FieldLightgalleryImageStyle();
    $use_thumbnails = new FieldUseThumbs();
    $title_source = new FieldTitleSource();


    if (isset($image_styles[$this->settings[$lightgallery_image_style->getGroup()
        ->getName()][$lightgallery_image_style->getName()]])) {
      $summary[] = t('Lightgallery image style: @style',
        array(
          '@style' => $image_styles[$this->settings[$lightgallery_image_style->getGroup()
            ->getName()][$lightgallery_image_style->getName()]]
        ));
    }
    else {
      $summary[] = t('Lightgallery image style: Original image');
    }

    if (isset($image_styles[$this->settings[$thumb_image_style->getGroup()
        ->getName()][$thumb_image_style->getName()]])) {
      $summary[] = t('Thumbnail image style: @style',
        array(
          '@style' => $image_styles[$this->settings[$thumb_image_style->getGroup()
            ->getName()][$thumb_image_style->getName()]]
        ));
    }
    else {
      $summary[] = t('Thumbnail image style: Original image');
    }

    $summary[] = ($this->settings[$use_thumbnails->getGroup()
      ->getName()][$use_thumbnails->getName()]) ? t('Use thumbs in gallery: Yes') : t('Use thumbs in gallery: No');

    $summary[] = !empty($this->settings[$title_source->getGroup()
      ->getName()][$title_source->getName()]) ? t('Value used as title: @title', array(
      '@title' => $this->settings[$title_source->getGroup()
        ->getName()][$title_source->getName()]
    )) : t('Value used as title: none');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /**
     * @var ImageItem $item
     */
    $item_list = array();

    $files = $this->getEntitiesToView($items, $langcode);
    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $item_list;
    }

    // Init lightgallery image style field.
    $lightgallery_image_style_field = new FieldLightgalleryImageStyle();
    // Fetch lightgallery image style.
    $lightgallery_image_style = $this->settings[$lightgallery_image_style_field->getGroup()
      ->getName()][$lightgallery_image_style_field->getName()];
    // Init thumb image style field.
    $thumb_image_style_field = new FieldThumbImageStyle();
    // Fetch thumb image style.
    $thumb_image_style = $this->settings[$thumb_image_style_field->getGroup()
      ->getName()][$thumb_image_style_field->getName()];
    // Init title source field.
    $title_source_field = new FieldTitleSource();
    $title_source = $this->settings[$title_source_field->getGroup()
      ->getName()][$title_source_field->getName()];


    foreach ($files as $file) {
      if ($uri = $file->getFileUri()) {
        // The reffering item is the image.
        $item = $file->_referringItem;
        // Load image urls.
        if (!empty($lightgallery_image_style)) {
          $item_detail['slide'] = $item_detail['thumb'] = ImageStyle::load($lightgallery_image_style)
            ->buildUrl($uri);
        }
        else {
          $item_detail['slide'] = $item_detail['thumb'] = file_create_url($uri);
        }

        // If image styles are different, also load thumb.
        if ($thumb_image_style != $lightgallery_image_style) {
          if (!empty($thumb_image_style)) {
            // load thumb url.
            $item_detail['thumb'] = ImageStyle::load($thumb_image_style)
              ->buildUrl($uri);
          }
          else {
            $item_detail['thumb'] = file_create_url($uri);
          }
        }

        if (!empty($title_source) && !empty($item->{$title_source})) {
          // Set title of slide.
          $item_detail['title'] = ['#markup' => Xss::filterAdmin($item->{$title_source})];
        }
      }

      $item_list[] = $item_detail;
    }


    // Flatten settings array.
    $options = LightgalleryManager::flattenArray($this->settings);
    // Set unique id, so that multiple instances on one page can be created.
    $unique_id = uniqid();
    // Load libraries.
    $lightgallery_optionset = new LightgalleryOptionset($options);
    $lightgallery_manager = new LightgalleryManager($lightgallery_optionset);
    // Build render array.
    $content = array(
      '#theme' => 'lightgallery',
      '#items' => $item_list,
      '#id' => $unique_id,
      '#attached' => $lightgallery_manager->loadLibraries($unique_id),
    );

    return $content;


  }

}
