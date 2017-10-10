<?php

namespace Drupal\share_everywhere;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Path\AliasManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a ShareEverywhereService service.
 */
class ShareEverywhereService implements ShareEverywhereServiceInterface {
  use StringTranslationTrait;

  /**
   * The config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The path alias manager.
   *
   * @var Drupal\Core\Path\AliasManager
   */
  protected $aliasManager;

  /**
   * Constructs an ShareEverywhereService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The Configuration Factory.
   * @param \Drupal\Core\Path\AliasManager $alias_manager
   *   The path alias manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AliasManager $alias_manager) {
    $this->configFactory = $config_factory;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function build($url, $id) {
    global $base_url;
    $config = $this->configFactory->get('share_everywhere.settings');
    $module_path = drupal_get_path('module', 'share_everywhere');
    $build = ['#theme' => 'share_everywhere'];
    $buttons = [];
    $library = [];

    switch ($config->get('alignment')) {
      case 'left':
        $build['#attributes']['class'] = [
          'se-align-left',
        ];
        break;

      case 'right':
        $build['#attributes']['class'] = [
          'se-align-right',
        ];
        break;
    }

    foreach ($config->get('buttons') as $key => $button) {
      if ($key == 'facebook_like' && $button['enabled']) {
        $build['#facebook_like'] = [
          '#theme' => 'se_facebook_like',
          '#url' => $url,
        ];
        array_push($build['#attributes']['class'], 'se-has-like');
      }
      elseif ($button['enabled']) {
        $buttons[$key] = [
          '#theme' => 'se_' . $key,
          '#url' => $url,
        ];

        if ($key != 'facebook_like' && $config->get('style') == 'share_everywhere') {
          $buttons[$key]['#content'] = [
            '#type' => 'html_tag',
            '#tag' => 'img',
            '#attributes' => [
              'src' => $base_url . '/' . $module_path . '/img/' . $button['image'],
              'title' => $this->t($button['title']),
              'alt' => $this->t($button['title']),
            ],
          ];
        }
        elseif ($config->get('style') == 'custom') {
          $buttons[$key]['#content'] = $this->t($button['name']);
        }
      }
    }
    $build['#buttons'] = $buttons;
    $build['#se_links_id'] = 'se-links-' . $id;

    if ($config->get('display_title')) {
      $build['#title'] = $this->t($config->get('title'));
    }

    $build['#share_icon'] = [
      'id' => 'se-trigger-' . $id,
      'src' => $base_url . '/' . $module_path . '/img/' . $config->get('share_icon.image'),
      'alt' => $this->t($config->get('share_icon.alt')),
    ];

    if (!$config->get('collapsible')) {
      $build['#share_icon']['class'] = 'se-disabled';
    }

    if ($config->get('style') == 'share_everywhere') {
      if ($config->get('collapsible')) {
        $library = [
          'share_everywhere/share_everywhere.css',
          'share_everywhere/share_everywhere.js',
        ];
      }
      else {
        $library = [
          'share_everywhere/share_everywhere.css',
        ];
      }
    }
    elseif ($config->get('style') == 'custom') {
      if ($config->get('include_css')) {
        $library = [
          'share_everywhere/share_everywhere.css',
        ];
      }

      if ($config->get('include_js') && $config->get('collapsible')) {
        array_push($library, 'share_everywhere/share_everywhere.js');
      }
    }

    if (!$config->get('collapsible') || ($config->get('style') == 'custom' && !$config->get('include_js'))) {
      $build['#is_active'] = 'se-active';
    }
    elseif ($config->get('collapsible')) {
      $build['#is_active'] = 'se-inactive';
    }

    if (!empty($library)) {
      $build['#attached'] = [
        'library' => $library,
      ];
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function isRestricted($view_mode, $current_path) {
    $config = $this->configFactory->get('share_everywhere.settings');

    switch ($view_mode) {
      case 'search_result':
      case 'search_index':
      case 'rss':
        return TRUE;
    }

    $restricted_pages = $config->get('restricted_pages.pages');

    if (is_array($restricted_pages) && !empty($restricted_pages)) {
      $restriction_type = $config->get('restricted_pages.type');
      $current_path_alias = $this->aliasManager->getPathByAlias($current_path);
      $current_path_alias = preg_split('/\//', $current_path_alias, NULL, PREG_SPLIT_NO_EMPTY);
      $current_path = preg_split('/\//', $current_path, NULL, PREG_SPLIT_NO_EMPTY);

      switch ($restriction_type) {
        case 'show':
          if ($restricted_pages[0] == '*' && !isset($restricted_pages[1])) {
            break;
          }
          else {
            foreach ($restricted_pages as $page) {
              $page = preg_split('/\//', $page, NULL, PREG_SPLIT_NO_EMPTY);

              if ($page[0] == '<front>') {
                $page = [];
              }
              elseif (($wildcard = array_search('*', $page)) !== FALSE) {
                unset($current_path[$wildcard]);
                unset($current_path_alias[$wildcard]);
                unset($page[$wildcard]);
              }

              if (empty($this->arrayDiff($current_path, $page)) || empty($this->arrayDiff($current_path_alias, $page))) {
                break 2;
              }
            }
          }
          return TRUE;

        case 'hide':
          if ($restricted_pages[0] == '*' && !isset($restricted_pages[1])) {
            return TRUE;
          }
          else {
            foreach ($restricted_pages as $page) {
              $page = preg_split('/\//', $page, NULL, PREG_SPLIT_NO_EMPTY);

              if ($page[0] == '<front>') {
                $page = [];
              }
              elseif (($wildcard = array_search('*', $page)) !== FALSE) {
                unset($current_path[$wildcard]);
                unset($current_path_alias[$wildcard]);
                unset($page[$wildcard]);
              }

              if (empty($this->arrayDiff($current_path, $page)) || empty($this->arrayDiff($current_path_alias, $page))) {
                return TRUE;
              }
            }
          }
          break;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function arrayDiff(array $a, array $b) {
    $intersect = array_intersect($a, $b);
    return array_merge(array_diff($a, $intersect), array_diff($b, $intersect));
  }

}
