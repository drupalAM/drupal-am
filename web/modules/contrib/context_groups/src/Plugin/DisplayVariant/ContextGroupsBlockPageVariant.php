<?php

namespace Drupal\context_groups\Plugin\DisplayVariant;

use Drupal\context\ContextManager;
use Drupal\context_groups\ContextGroupsManager;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\context\Plugin\DisplayVariant\ContextBlockPageVariant;

/**
 * Provides a page display variant that decorates the main content with blocks.
 *
 * @see \Drupal\Core\Block\MainContentBlockPluginInterface
 * @see \Drupal\Core\Block\MessagesBlockPluginInterface
 *
 * @PageDisplayVariant(
 *   id = "context_groups_block_page",
 *   admin_label = @Translation("Page with blocks")
 * )
 */
class ContextGroupsBlockPageVariant extends ContextBlockPageVariant {

  /**
   * Context groups manager.
   *
   * @var ContextGroupsManager
   */
  protected $contextGroupsManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ContextManager $contextManager, ContextGroupsManager $contextGroupsManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $contextManager);
    $this->contextGroupsManager = $contextGroupsManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('context.manager'),
      $container->get('context_groups.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();

    $rendered_context_groups = $this->getNonEmptyContextGroups($build);

    // Get current context.
    $contexts = $this->contextManager->getActiveContexts();
    foreach ($contexts as $context) {
      $groups = $context->getThirdPartySettings('context_groups');

      // Add context groups to build array.
      foreach ($groups as $key => $data) {

        // If group has no content, don't display it.
        if (!in_array($key, $rendered_context_groups)) {
          continue;
        }

        if (empty($data['parent'])) {
          // If group has no parents.
          $old_data = isset($build[$data['region']][$key]) ? $build[$data['region']][$key] : [];
          $new_data = [
            '#theme' => 'context_groups',
            '#attributes' => [
              'class' => ['context-groups context-groups-' . $key . ' ' . $data['class']],
            ],
            '#weight' => $data['weight'],
            '#context_id' => $context->getName(),
            '#context_group' => $key,
          ];
          $group_content = array_merge_recursive($old_data, $new_data);
          $build[$data['region']][$key] = $group_content;
        }
        else {
          // If group has parents.
          $build_parent = &$build[$data['region']];
          foreach ($data['all_parents'] as $value) {
            $build_parent = &$build_parent[$value];
          }
          $old_data = isset($build_parent[$key]) ? $build_parent[$key] : [];
          $new_data = [
            '#theme' => 'context_groups',
            '#attributes' => [
              'class' => ['context-groups context-groups-' . $key . ' ' . $data['class']],
            ],
            '#weight' => $data['weight'],
            '#context_id' => $context->getName(),
            '#context_group' => $key,
          ];
          $merge = array_merge_recursive($old_data, $new_data);
          $build_parent[$key] = $merge;
        }
      }
    }

    // Move blocks inside context groups.
    foreach (Element::children($build) as $region_name) {
      foreach ($build[$region_name] as $key => $block) {
        // If block is not a context group, edit his position if needed.
        if (isset($block['#type']) && $block['#type'] == 'container') {
          continue;
        }
        // If block has a parent, move it.
        if (!empty($block['#configuration']['all_parents'])) {
          $build_parent = &$build[$region_name];
          foreach ($block['#configuration']['all_parents'] as $value) {
            $build_parent = &$build_parent[$value];
          }
          // Place block in correct context group.
          $build_parent[$key] = $block;
          // Unset original block.
          unset($build[$region_name][$key]);
        }
      }
    }
    return $build;
  }

  /**
   * Get non empty context groups.
   *
   * @param array $build
   *   Render array.
   *
   * @return array
   *   Returns array of context groups which should be rendered.
   */
  private function getNonEmptyContextGroups(array $build) {
    $not_empty_context_groups = [];
    foreach (Element::children($build) as $region_name) {
      foreach ($build[$region_name] as $block) {
        if (!empty($block['#configuration']['all_parents'])) {
          $not_empty_context_groups = array_unique(array_merge($block['#configuration']['all_parents'], $not_empty_context_groups));
        }
      }
    }
    return $not_empty_context_groups;
  }

}
