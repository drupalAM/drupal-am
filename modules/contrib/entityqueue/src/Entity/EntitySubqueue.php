<?php

namespace Drupal\entityqueue\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\entityqueue\EntityQueueInterface;
use Drupal\entityqueue\EntitySubqueueInterface;
use Drupal\user\UserInterface;

/**
 * Defines the EntitySubqueue entity class.
 *
 * @ContentEntityType(
 *   id = "entity_subqueue",
 *   label = @Translation("Entity subqueue"),
 *   bundle_label = @Translation("Entity queue"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\entityqueue\Form\EntitySubqueueForm",
 *       "delete" = "\Drupal\entityqueue\Form\EntitySubqueueDeleteForm",
 *       "edit" = "Drupal\entityqueue\Form\EntitySubqueueForm"
 *     },
 *     "access" = "Drupal\entityqueue\EntitySubqueueAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\entityqueue\EntitySubqueueListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   base_table = "entity_subqueue",
 *   entity_keys = {
 *     "id" = "name",
 *     "bundle" = "queue",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "uid" = "uid"
 *   },
 *   bundle_entity_type = "entity_queue",
 *   field_ui_base_route = "entity.entity_queue.edit_form",
 *   permission_granularity = "bundle",
 *   links = {
 *     "edit-form" = "/admin/structure/entityqueue/{entity_queue}/{entity_subqueue}",
 *     "delete-form" = "/admin/structure/entityqueue/{entity_queue}/{entity_subqueue}/delete",
 *     "collection" = "/admin/structure/entityqueue/{entity_queue}/list",
 *   },
 *   constraints = {
 *     "QueueSize" = {}
 *   }
 * )
 */
class EntitySubqueue extends ContentEntityBase implements EntitySubqueueInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function access($operation = 'view', AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($operation == 'create') {
      return parent::access($operation, $account, $return_as_object);
    }

    return \Drupal::entityTypeManager()
      ->getAccessControlHandler($this->entityTypeId)
      ->access($this, $operation, $account, $return_as_object);
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    /** @var \Drupal\entityqueue\EntityQueueInterface $queue */
    $queue = $this->getQueue();
    $max_size = $queue->getMaximumSize();
    $act_as_queue = $queue->getActAsQueue();

    $items = $this->get('items')->getValue();
    $number_of_items = count($items);

    // Remove extra items from the front of the queue if the maximum size is
    // exceeded.
    if ($act_as_queue && $number_of_items > $max_size) {
      $items = array_slice($items, -$max_size);

      $this->set('items', $items);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getQueue() {
    return $this->get('queue')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setQueue(EntityQueueInterface $queue) {
    $this->set('queue', $queue->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the subqueue.'))
      ->setReadOnly(TRUE)
      // In order to work around the InnoDB 191 character limit on utf8mb4
      // primary keys, we set the character set for the field to ASCII.
      ->setSetting('is_ascii', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The subqueue UUID.'))
      ->setReadOnly(TRUE);

    $fields['queue'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Queue'))
      ->setDescription(t('The queue (bundle) of this subqueue.'))
      ->setSetting('target_type', 'entity_queue')
      ->setReadOnly(TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 191)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -10,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['items'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Items'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      // This setting is overridden per bundle (queue) in
      // static::bundleFieldDefinitions(), but we need to default to a target
      // entity type that uses strings IDs, in order to allow both integers and
      // strings to be stored by the default entity reference field storage.
      ->setSetting('target_type', 'entity_subqueue')
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'entity_reference_label',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language'))
      ->setDescription(t('The subqueue language code.'))
      ->setDisplayOptions('view', array(
        'type' => 'hidden',
      ))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 2,
      ));

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The username of the subqueue author.'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\entityqueue\Entity\EntitySubqueue::getCurrentUserId');

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the subqueue was created.'))
      ->setDefaultValueCallback('Drupal\entityqueue\Entity\EntitySubqueue::getDefaultCreatedTime');

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the subqueue was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    // Change the target type of the 'items' field to the one defined by the
    // parent queue (i.e. bundle).
    if ($queue = EntityQueue::load($bundle)) {
      $fields['items'] = clone $base_field_definitions['items'];
      $fields['items']->setSettings($queue->getEntitySettings());
      return $fields;
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->getEntityKey('uid');
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return array(\Drupal::currentUser()->id());
  }

  /**
   * Default value callback for 'created' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getDefaultCreatedTime() {
    return REQUEST_TIME;
  }

  /**
   * {@inheritdoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    $url = parent::toUrl($rel, $options);

    // The 'entity_queue' parameter is needed by the subqueue routes, so we need
    // to add it manually.
    $url->setRouteParameter('entity_queue', $this->bundle());

    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate() {
    $tags = [];

    // Use the cache tags of the entity queue.
    // @todo Allow queue handlers to control this?
    if ($queue = $this->getQueue()) {
      $tags = Cache::mergeTags(parent::getCacheTagsToInvalidate(), $queue->getCacheTags());

      // Sadly, Views handlers have no way of influencing the cache tags of the
      // views result cache plugins, so we have to invalidate the target entity
      // type list tag.
      // @todo Reconsider this when https://www.drupal.org/node/2710679 is fixed.
      $target_entity_type = $this->entityTypeManager()->getDefinition($this->getQueue()->getTargetEntityTypeId());
      $tags = Cache::mergeTags($tags, $target_entity_type->getListCacheTags());
    }

    return $tags;
  }

}
