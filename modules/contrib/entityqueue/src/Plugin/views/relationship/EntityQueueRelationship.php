<?php

namespace Drupal\entityqueue\Plugin\views\relationship;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entityqueue\Entity\EntityQueue;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\relationship\EntityReverse;
use Drupal\views\ViewExecutable;

/**
 * A relationship handler for entity queues.
 *
 * @ingroup views_relationship_handlers
 *
 * @ViewsRelationship("entity_queue")
 */
class EntityQueueRelationship extends EntityReverse implements CacheableDependencyInterface {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['limit_queue'] = ['default' => NULL];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $queues = EntityQueue::loadMultipleByTargetType($this->getEntityType());
    $options = array();
    foreach ($queues as $queue) {
      $options[$queue->id()] = $queue->label();
    }

    $form['limit_queue'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Limit to a specific entity queue'),
      '#options' => $options,
      '#default_value' => $this->options['limit_queue'],
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    // Add an extra condition to limit results based on the queue selection.
    if ($this->options['limit_queue']) {
      $this->definition['extra'][] = [
        'field' => 'bundle',
        'value' => $this->options['limit_queue'],
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();

    if ($this->options['limit_queue']) {
      $queue = EntityQueue::load($this->options['limit_queue']);
      $dependencies[$queue->getConfigDependencyKey()][] = $queue->getConfigDependencyName();
    }

    return $dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = [];

    if ($this->options['limit_queue']) {
      $queue = EntityQueue::load($this->options['limit_queue']);
      $tags = $queue->getCacheTags();
    }

    return $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Add a 'where' condition if needed.
    if (!empty($this->definition['extra'])) {
      $bundles = [];

      // Future-proofing: support any number of selected bundles.
      foreach ($this->definition['extra'] as $extra) {
        if ($extra['field'] == 'bundle') {
          $bundles[] = $extra['value'];
        }
      }
      if (count($bundles) > 0) {
        $this->definition['join_extra'][] = [
          'field' => 'bundle',
          'value' => $bundles,
        ];
      }
    }

    // Now - let's build the query.
    parent::query();
  }

}
