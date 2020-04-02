<?php

namespace Drupal\context_groups\EventSubscriber;

use Drupal\context\Plugin\ContextReaction\Blocks;
use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\context\ContextManager;

/**
 * Class BlockPageDisplayVariantSubscriber.
 *
 * @package Drupal\context_groups
 */
class BlockPageDisplayVariantSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\context\ContextManager definition.
   *
   * @var \Drupal\context\ContextManager
   */
  protected $contextManager;

  /**
   * Constructor.
   *
   * @param ContextManager $context_manager
   *   Context manager.
   */
  public function __construct(ContextManager $context_manager) {
    $this->contextManager = $context_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[RenderEvents::SELECT_PAGE_DISPLAY_VARIANT][] = ['onSelectPageDisplayVariant'];
    return $events;
  }

  /**
   * Selects the context groups block page display variant.
   *
   * @param PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event) {
    // Activate the context groups block page display variant.
    foreach ($this->contextManager->getActiveReactions() as $reaction) {
      if ($reaction instanceof Blocks) {
        $event->setPluginId('context_groups_block_page');
        break;
      }
    }
  }

}
