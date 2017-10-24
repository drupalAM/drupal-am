<?php

/**
 * @file
 * Altering display for reaction table.
 */

use Drupal\Component\Serialization\Json;
use Drupal\context\Entity\Context;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

/**
 * Alter the reaction table display.
 */
function context_groups_reaction_ui_display_form_alter(&$form, FormStateInterface $form_state) {

  // Current context.
  $context = $form_state->getFormObject()->getEntity()->getOriginalId();
  $context = \Drupal::entityTypeManager()->getStorage('context')->load($context);

  // Get groups from the context.
  $params = \Drupal::service('context_groups.manager')->getParams($context, $form_state);

  // If there is no groups in context, we don't need to alter the display.
  if (!$params) {
    return;
  }

  // Get all regions from selected theme.
  $theme = \Drupal::service('context_groups.manager')->getCurrentTheme($form_state);
  $regions = system_region_list($theme);

  // Saved context blocks inside context.
  $context_blocks = $context->getReactions()->get('blocks')->getBlocks();
  $context_blocks_keys = array_keys($context_blocks->getConfiguration());

  $form['#context_groups'] = array_keys($params->group_list);

  $table = &$form['reaction-blocks']['options']['blocks']['blocks'];

  $original_table = $form['reaction-blocks']['options']['blocks']['blocks'];
  unset($form['reaction-blocks']['options']['blocks']['blocks']);

  $table = [];
  $table = [
    '#type' => 'field_ui_table',
    '#header' => [
      'Name',
      'Category',
      'Unique',
      'Weight',
      'Region',
      'Parent',
      'Operations',
    ],
    '#attributes' => ['id' => 'context-groups-draggable'],
    '#tabledrag' => [
      [
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'field-weight',
      ],
      [
        'action' => 'match',
        'relationship' => 'parent',
        'group' => 'field-parent',
      ],
    ],
  ];

  // Add regions to table.
  $table['#regions'] = context_groups_add_regions_to_table($original_table, $regions, $context_blocks_keys);

  $region_keys = array_keys($regions);
  $form['#attached']['drupalSettings']['context_groups_regions'] = $region_keys;

  // Place blocks in region.
  foreach ($context_blocks as $block) {
    $configuration = $block->getConfiguration();

    // Set parent default.
    $parent_default = isset($configuration['parent_wrapper']['parent'])
      ? $configuration['parent_wrapper']['parent']
      : '';

    $operations = [
      'edit' => [
        'title' => t('Edit'),
        'url' => Url::fromRoute('context.reaction.blocks.block_edit', [
          'context' => $context->id(),
          'reaction_id' => 'blocks',
          'block_id' => $configuration['uuid'],
        ], [
          'query' => [
            'theme' => $theme,
          ],
        ]),
        'attributes' => [
          'class' => ['use-ajax'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 1000,
          ]),
        ],
      ],
      'delete' => [
        'title' => t('Delete'),
        'url' => Url::fromRoute('context.reaction.blocks.block_delete', [
          'context' => $context->id(),
          'block_id' => $configuration['uuid'],
        ]),
        'attributes' => [
          'class' => ['use-ajax'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 1000,
          ]),
        ],
      ],
    ];

    $table[$configuration['uuid']] = [
      '#attributes' => [
        'class' => ['draggable', 'tabledrag-leaf'],
        'data-region' => 'region-' . $configuration['region'],
      ],
      '#region_callback' => 'context_groups_region_callback',
      'title' => [
        '#markup' => $block->label(),
      ],
      'category' => [
        '#markup' => $block->getPluginDefinition()['category'],
      ],
      'unique' => [
        '#markup' => $configuration['unique'] ? t('Yes') : t('No'),
      ],
      'weight' => [
        '#type' => 'textfield',
        '#default_value' => isset($configuration['weight']) ? $configuration['weight'] : 0,
        '#size' => 3,
        '#title' => 'Weight for row',
        '#title_display' => 'invisible',
        '#attributes' => [
          'class' => ['field-weight'],
        ],
      ],
      'region' => [
        '#type' => 'select',
        '#title' => t('Region'),
        '#title_display' => 'invisible',
        '#default_value' => $configuration['region'],
        '#options' => $regions,
        '#attributes' => [
          'class' => ['block-region-select', 'block-region-' . $configuration['region']],
        ],
      ],
      'parent_wrapper' => [
        'parent' => [
          '#type' => 'select',
          '#attributes' => ['class' => ['field-parent']],
          '#options' => $params->group_list,
          '#default_value' => $parent_default,
        ],
      ],
      'operations' => [
        '#type' => 'operations',
        '#links' => $operations,
      ],
    ];
  }

  context_groups_add_groups_to_table($table, $params, $context, $regions);
  $form['reaction-blocks']['options']['blocks']['blocks'] = $table;
}

/**
 * Add context groups to the reaction table.
 *
 * @param array $table
 *   Reference to reaction table.
 * @param object $params
 *   List of parameters from context groups.
 * @param Context $context
 *   Context object.
 * @param array $regions
 *   List of regions.
 */
function context_groups_add_groups_to_table(array &$table, $params, Context $context, array $regions) {

  foreach ($params->groups_by_region as $group_array) {
    foreach ($group_array as $context_group) {
      $operations = [];
      if (Drupal::currentUser()->hasPermission('create context groups')) {
        $operations = [
          '#type' => 'operations',
          '#links' => [
            'edit' => [
              'title' => t('Edit'),
              'url' => Url::fromRoute('context_groups.group_edit_form', [
                'context' => $context->id(),
                'context_group' => $context_group['name'],
              ]),
              'attributes' => [
                'class' => ['use-ajax'],
                'data-dialog-type' => 'modal',
                'data-dialog-options' => Json::encode([
                  'width' => 500,
                ]),
              ],
            ],
            'delete' => [
              'title' => t('Delete'),
              'url' => Url::fromRoute('context_groups.group_delete_form', [
                'context' => $context->id(),
                'context_group' => $context_group['name'],
              ]),
              'attributes' => [
                'class' => ['use-ajax'],
                'data-dialog-type' => 'modal',
                'data-dialog-options' => Json::encode([
                  'width' => 1000,
                ]),
              ],
            ],
          ],
        ];
      }

      $table[$context_group['id']] = [
        '#attributes' => [
          'class' => ['draggable context-group'],
          'data-region' => 'region-' . $context_group['region'],
          'data-group-id' => $context_group['id'],
        ],
        '#region_callback' => 'context_groups_region_callback',
        'title' => [
          '#markup' => '' . $context_group['label'] . '',
        ],
        'category' => [],
        'unique' => [],
        'weight' => [
          '#type' => 'textfield',
          '#default_value' => $context_group['weight'],
          '#size' => 3,
          '#title' => 'Weight for row',
          '#title_display' => 'invisible',
          '#attributes' => [
            'class' => ['field-weight'],
          ],
        ],
        'region' => [
          '#type' => 'select',
          '#title' => t('Region'),
          '#title_display' => 'invisible',
          '#default_value' => $context_group['region'],
          '#options' => $regions,
          '#attributes' => [
            'class' => ['block-region-select', 'block-region-' . $context_group['region']],
          ],
        ],
        'parent_wrapper' => [
          'parent' => [
            '#type' => 'select',
            '#attributes' => ['class' => ['field-parent']],
            '#options' => $params->group_list,
            '#default_value' => $context_group['parent'],
          ],
        ],
        'operations' => $operations,
      ];
    }
  }

}

/**
 * Add regions to Context reaction table.
 *
 * @param array $table
 *   Context reaction table.
 * @param array $regions
 *   Regions of currently selected theme.
 * @param array $context_blocks
 *   Blocks added to the context.
 *
 * @return array
 *   Return array of regions, where we can drag/drop groups/blocks.
 */
function context_groups_add_regions_to_table(array &$table, array $regions, array $context_blocks) {
  $region_keys = array_keys($regions);

  if (!$table) {
    return FALSE;
  }

  $drag_regions = [];
  foreach (Element::children($table) as $child) {
    $region_name = str_replace('region-', '', $child);

    // Add draggable region, unset region element from original reaction table.
    if (in_array($region_name, $region_keys)) {
      $drag_regions[$child] = [
        'title' => $table[$child]['title']['#markup'],
      ];
      $table[$child]['#attributes']['data-region'] = $child;
      unset($table[$child]);
    }

    // Context block element.
    if (in_array($child, $context_blocks)) {
      $table[$child]['#attributes']['data-region'] = 'region-' . $table[$child]['region']['#default_value'];
    }
    $table[$child]['#region_callback'] = 'context_groups_region_callback';
  }
  $table['#regions'] = $drag_regions;

  return $drag_regions;
}

/**
 * Get region for table row.
 *
 * @param array $row
 *   Table row.
 *
 * @return string
 *   Return region header.
 */
function context_groups_region_callback(array $row) {
  return $row['#attributes']['data-region'];
}
