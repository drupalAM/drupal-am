<?php

namespace Drupal\entityqueue\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\entityqueue\EntityQueueInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * Returns responses for Views UI routes.
 */
class EntityQueueUIController extends ControllerBase {

  /**
   * Provides a list of all the subqueues of an entity queue.
   *
   * @param \Drupal\entityqueue\EntityQueueInterface $entity_queue
   *   The entity queue.
   *
   * @return array
   *   A render array.
   */
  public function subqueueList(EntityQueueInterface $entity_queue) {
    $list_builder = $this->entityTypeManager()->getListBuilder('entity_subqueue');
    $list_builder->setQueueId($entity_queue->id());

    return $list_builder->render();
  }

  /**
   * Returns a form to add a new subqeue.
   *
   * @param \Drupal\entityqueue\EntityQueueInterface $entity_queue
   *   The queue this subqueue will be added to.
   *
   * @return array
   *   The entity subqueue add form.
   */
  public function addForm(EntityQueueInterface $entity_queue) {
    $subqueue = $this->entityTypeManager()->getStorage('entity_subqueue')->create(['queue' => $entity_queue->id()]);
    return $this->entityFormBuilder()->getForm($subqueue);
  }

  /**
   * Calls a method on an entity queue and reloads the listing page.
   *
   * @param \Drupal\entityqueue\EntityQueueInterface $entity_queue
   *   The view being acted upon.
   * @param string $op
   *   The operation to perform, e.g., 'enable' or 'disable'.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse|\Symfony\Component\HttpFoundation\RedirectResponse
   *   Either returns a rebuilt listing page as an AJAX response, or redirects
   *   back to the listing page.
   */
  public function ajaxOperation(EntityQueueInterface $entity_queue, $op, Request $request) {
    // Perform the operation.
    $entity_queue->$op()->save();

    // If the request is via AJAX, return the rendered list as JSON.
    if ($request->request->get('js')) {
      $list = $this->entityTypeManager()->getListBuilder('entity_queue')->render();
      $response = new AjaxResponse();
      $response->addCommand(new ReplaceCommand('#entity-queue-list', $list));
      return $response;
    }

    // Otherwise, redirect back to the page.
    return $this->redirect('entity.entity_queue.collection');
  }

}
