<?php

namespace Drupal\drupalam_default_content;

use Drupal\default_content\Event\DefaultContentEvents;
use Drupal\default_content\Event\ImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class DrupalAmDefaultContentSubscriber implements EventSubscriberInterface {

  public function  onContentImport(ImportEvent $event){

    $this->AddContentToEntityqueue('case_study', 'homepage_case_study');
    $this->AddContentToEntityqueue('hero_slide', 'homepage_hero_slider');
    $this->AddUsersToFeaturedCommunityMembersEntityqueue();
  }

  private function AddContentToEntityqueue($node_type, $entityqueue) {
    $nids = \Drupal::entityQuery('node')->condition('type', $node_type)->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
    $entityqueue = \Drupal\entityqueue\Entity\EntitySubqueue::load($entityqueue);
    $entityqueue->set('items',$nodes);
    $entityqueue->save();
  }

  private function AddUsersToFeaturedCommunityMembersEntityqueue() {
    $users =  \Drupal\user\Entity\User::loadMultiple();

    // Unset admin users and set user status to 'active'
    foreach ($users as $key => $user) {
      if ($user->uid->value == 0 || $user->uid->value == 1) {
        unset($users[$key]);
      }
      else {
        $user->status->value = 1;
        $user->save();
      }
    }

    $entityqueue = \Drupal\entityqueue\Entity\EntitySubqueue::load('featured_community_members');
    $entityqueue->set('items', $users);
    $entityqueue->save();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [DefaultContentEvents::IMPORT => ['onContentImport']];
  }
}
