<?php

namespace Drupal\default_content_deploy;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Path\AliasStorageInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\default_content\Importer as DCImporter;
use Drupal\default_content\ScannerInterface;
use Drupal\hal\LinkManager\LinkManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * A service for handling import of default content.
 *
 * The importContent() method is almost duplicate of
 *   \Drupal\default_content\Importer::importContent with injected code for
 *   content update. We are waiting for better DC code structure in a future.
 */
class Importer extends DCImporter {

  /**
   * Flag for enable/disable writing operations.
   *
   * @var bool
   */
  protected $writeEnable;

  /**
   * Flag if some known file normalizer is installed.
   *
   * @var bool
   */
  protected $fileEntityEnabled;

  /**
   * Deploy manager.
   *
   * @var \Drupal\default_content_deploy\DeployManager
   */
  protected $deployManager;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * The default_content_deploy logger channel.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $logger;

  /**
   * The path alias storage service.
   *
   * @var \Drupal\Core\Path\AliasStorageInterface
   */
  private $pathAliasStorage;

  public $result;

  /**
   * @var object[]
   */
  private $files;

  private $folder;

  private $dataToImport = [];

  /**
   * Is remove changes of an old content.
   *
   * @var bool
   */
  protected $forceOverride;

  /**
   * Constructs the default content deploy manager.
   *
   * @param \Symfony\Component\Serializer\Serializer $serializer
   *   The serializer service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\hal\LinkManager\LinkManagerInterface $link_manager
   *   The link manager service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\default_content\ScannerInterface $scanner
   *   The file scanner.
   * @param string $link_domain
   *   Defines relation domain URI for entity links.
   * @param \Drupal\Core\Session\AccountSwitcherInterface $account_switcher
   *   The account switcher.
   * @param \Drupal\default_content_deploy\DeployManager $deploy_manager
   *   Deploy manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Psr\Log\LoggerInterface $logger
   *   Logger.
   * @param \Drupal\Core\Path\AliasStorageInterface $path_alias_storage
   *   The path alias storage service.
   */
  public function __construct(Serializer $serializer, EntityTypeManagerInterface $entity_type_manager, LinkManagerInterface $link_manager, EventDispatcherInterface $event_dispatcher, ScannerInterface $scanner, $link_domain, AccountSwitcherInterface $account_switcher, DeployManager $deploy_manager, ModuleHandlerInterface $module_handler, LoggerInterface $logger, AliasStorageInterface $path_alias_storage) {
    parent::__construct($serializer, $entity_type_manager, $link_manager, $event_dispatcher, $scanner, $link_domain, $account_switcher);

    $this->deployManager = $deploy_manager;
    $this->moduleHandler = $module_handler;
    $this->logger = $logger;
    $this->pathAliasStorage = $path_alias_storage;
    $this->fileEntityEnabled = (
      $this->moduleHandler->moduleExists('file_entity') ||
      $this->moduleHandler->moduleExists('better_normalizers')
    );
  }

  /**
   * Is remove changes of an old content.
   *
   * @param bool $is_override
   *
   * @return \Drupal\default_content_deploy\Importer
   */
  public function setForceOverride(bool $is_override) {
    $this->forceOverride = $is_override;
    return $this;
  }

  /**
   * Set directory to import.
   *
   * @param string $folder
   *   The content folder.
   *
   * @return \Drupal\default_content_deploy\Importer
   */
  public function setFolder($folder) {
    $this->folder = $folder;
    return $this;
  }

  /**
   * Get directory to import.
   *
   * @return string
   *   The content folder.
   *
   * @throws \Exception
   */
  protected function getFolder() {
    $folder = $this->folder ?: $this->deployManager->getContentFolder();
    return $folder;
  }

  /**
   * Get Imported data result.
   *
   * @return array
   */
  public function getResult() {
    return $this->dataToImport;
  }

  /**
   * Import url aliases.
   *
   * @return array
   *   Return number of imported or skipped aliases.
   *
   * @throws \Exception
   */
  public function importUrlAliases() {
    $count = 0;
    $skipped = 0;
    $file = $this->deployManager->getContentFolder() . '/'
      . Exporter::ALIAS_NAME . '/'
      . Exporter::ALIAS_NAME . '.json';

    if (!file_destination($file, FILE_EXISTS_ERROR)) {
      $aliases = file_get_contents($file, TRUE);
      $path_aliases = Json::decode($aliases);

      foreach ($path_aliases as $alias) {
        if (!$this->pathAliasStorage->aliasExists($alias['alias'], $alias['langcode'])) {
          if ($this->writeEnable !== FALSE) {
            $this->pathAliasStorage->save($alias['source'], $alias['alias'], $alias['langcode']);
            $count++;
          }
        }
        else {
          $skipped++;
        }
      }
    }

    return ['imported' => $count, 'skipped' => $skipped];
  }

  /**
   * Import data from JSON and create new entities, or update existing.
   *
   * @return $this
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Exception
   */
  public function prepareForImport() {
    $this->files = $this->scanner->scan($this->getFolder());

    foreach ($this->files as $file) {
      $uuid = str_replace('.json', '', $file->name);

      if (!$this->dataToImport[$uuid]) {
        $this->decodeFile($file);
      }
    }

    return $this;
  }

  /**
   * Import to entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function import() {
    $files = $this->dataToImport;

    if (PHP_SAPI === 'cli') {
      $root_user = $this->entityTypeManager->getStorage('user')->load(1);
      $this->accountSwitcher->switchTo($root_user);
    }

    foreach ($files as $file) {
      if ($file['status'] != 'skip') {
        $entity_type = $file['entity_type_id'];
        $class = $this->entityTypeManager->getDefinition($entity_type)->getClass();

        /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
        $entity = $this->serializer->denormalize($file['data'], $class, 'hal_json', ['request_method' => 'POST']);
        $entity->enforceIsNew($file['is_new']);
        $entity->save();
      }
    }

    if (PHP_SAPI === 'cli') {
      $this->accountSwitcher->switchBack();
    }
  }

  /**
   * Load entity by UUID.
   *
   * @param string $entity_type
   *   Name of entity type.
   * @param string $uuid
   *   UUID of the entity to load.
   *
   * @return bool|\Drupal\Core\Entity\Entity
   *   Loaded entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function loadEntityByUuid($entity_type, $uuid) {
    $entityStorage = $this->entityTypeManager->getStorage($entity_type);
    $entities = $entityStorage->loadByProperties(['uuid' => $uuid]);

    if (!empty($entities)) {
      return reset($entities);
    }

    return FALSE;
  }

  /**
   * Prepare file to import.
   *
   * @param $file
   *
   * @return $this
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Exception
   */
  protected function decodeFile($file) {
    $parsed_data = $this->parseFile($file);
    $decode = $this->serializer->decode($parsed_data, 'hal_json');
    $references = $this->getReferences($decode);

    if ($references) {
      foreach ($references as $reference) {
        $this->decodeFile($reference);
      }
    }

    $this->addToImport($decode);
    $this->editEntityData($decode);

    return $this;
  }

  /**
   * Get Entity type ID by File path.
   *
   * @param $file
   *
   * @return string|string[]
   * @throws \Exception
   */
  private function getEntityTypeByFile($file) {
    $path_to_folder = str_replace($file->name, '', $file->uri);
    $entity_type_folder = str_replace($this->getFolder(), '', $path_to_folder);
    $entity_type_id = str_replace('/', '', $entity_type_folder);

    return $entity_type_id;
  }

  /**
   * Get all reference by entity array content.
   *
   * @param array $content
   *
   * @return array
   */
  private function getReferences(array $content) {
    $references = [];

    if ($content['_embedded']) {
      foreach ($content['_embedded'] as $link) {
        foreach ($link as $reference) {
          $uuid = $reference['uuid'][0]['value'];
          $path = $this->getPathToFileByName($uuid);

          if ($path) {
            $references[] = $this->files[$path];
          }
        }
      }
    }

    return $references;
  }

  /**
   * Get path to file by Name.
   *
   * @param $name
   *
   * @return false|int|string
   */
  private function getPathToFileByName($name) {
    $array_column = array_column($this->files, 'name', 'uri');
    $path = array_search($name . '.json', $array_column);

    return $path;
  }

  /**
   * @param $decode
   *
   * @return $this
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function editEntityData($decode) {
    $uuid = $decode['uuid'][0]['value'];
    $entity_type_id = $this->dataToImport[$uuid]['entity_type_id'];
    $entity_type_object = $this->entityTypeManager->getDefinition($entity_type_id);
    $entity = $this->loadEntityByUuid($entity_type_id, $uuid);
    $status = 'create';
    $is_new = TRUE;

    if ($entity) {
      $key = $entity_type_object->getKey('id');
      $decode[$key][0]['value'] = $entity->id();
      $changed_time_file = strtotime($decode['changed'][0]['value']);
      $changed_time_entity = $entity->getChangedTime();
      $is_new = FALSE;
      $status = 'update';

      if (!$this->forceOverride && $changed_time_file <= $changed_time_entity) {
        $status = 'skip';
      }
    }

    // Ignore revision.
    unset($decode[$entity_type_object->getKey('revision')]);

    $this->dataToImport[$uuid]['is_new'] = $is_new;
    $this->dataToImport[$uuid]['status'] = $status;
    $this->dataToImport[$uuid]['data'] = $decode;

    return $this;
  }

  /**
   * @param $decode
   *
   * @return $this
   * @throws \Exception
   */
  private function addToImport($decode) {
    $uuid = $decode['uuid'][0]['value'];
    $file_path = $this->getPathToFileByName($uuid);
    $entity_type_id = $this->getEntityTypeByFile($this->files[$file_path]);

    $this->dataToImport[$uuid] = [
      'data' => $decode,
      'entity_type_id' => $entity_type_id,
    ];

    return $this;
  }

}
