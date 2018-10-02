<?php

namespace Drupal\entityqueue\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;

/**
 * Provides the entity subqueue delete confirmation form.
 */
class EntitySubqueueDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    // Point to the parent queue entity.
    return $this->entity->queue->entity->urlInfo('subqueue-list');
  }

  /**
   * {@inheritdoc}
   */
  protected function getRedirectUrl() {
    return $this->getCancelUrl();
  }

}
