<?php

namespace Drupal\default_content_deploy\Commands;

use Drupal\default_content_deploy\DeployManager;
use Drupal\default_content_deploy\Exporter;
use Drupal\default_content_deploy\Importer;
use Drush\Commands\DrushCommands;

/**
 * Class DefaultContentDeployCommands.
 *
 * @package Drupal\default_content_deploy\Commands
 */
class DefaultContentDeployCommands extends DrushCommands {

  /**
   * DCD Exporter.
   *
   * @var \Drupal\default_content_deploy\Exporter
   */
  private $exporter;

  /**
   * DCD Importer.
   *
   * @var \Drupal\default_content_deploy\Importer
   */
  private $importer;

  /**
   * Default deploy content manager.
   *
   * @var \Drupal\default_content_deploy\DeployManager
   */
  protected $deployManager;

  /**
   * DefaultContentDeployCommands constructor.
   *
   * @param \Drupal\default_content_deploy\Exporter $exporter
   *   DCD Exporter.
   * @param \Drupal\default_content_deploy\Importer $importer
   *   DCD Importer.
   * @param \Drupal\default_content_deploy\DeployManager $deploy_manager
   *   DCD manager.
   */
  public function __construct(Exporter $exporter, Importer $importer, DeployManager $deploy_manager) {
    $this->exporter = $exporter;
    $this->importer = $importer;
    $this->deployManager = $deploy_manager;
  }

  /**
   * Exports a single entity or group of entities.
   *
   * @param $entity_type
   *   The entity type to export. If a wrong content entity type is entered,
   *   module displays a list of all content entity types.
   * @param array $options
   *   An associative array of options whose values come
   *   from cli, aliases, config, etc.
   *
   * @command default-content-deploy:export
   *
   * @option entity_id The ID of the entity to export.
   * @option bundle Write out the exported bundle of entity
   * @option skip_entities The ID of the entity to skip.
   * @option force-update Deletes configurations files that are not used on the site.
   * @option folder Path to the export folder.
   * @usage drush dcde node
   *   Export all nodes
   * @usage drush dcde node --folder='../content'
   *   Export all nodes from the specified folder.
   * @usage drush dcde node --bundle=page
   *   Export all nodes with bundle page
   * @usage drush dcde node --bundle=page,article --entity_id=2,3,4
   *   Export all nodes with bundle page or article plus nodes with entities id
   *   2, 3 and 4.
   * @usage drush dcde node --bundle=page,article --skip_entities=5,7
   *   Export all nodes with bundle page or article and skip nodes with entity
   *   id 5 and 7.
   * @usage drush dcde node --skip_entities=5,7
   *   Export all nodes and skip nodes with entity id 5 and 7.
   * @validate-module-enabled default_content
   * @aliases dcde,default-content-deploy-export
   *
   * @throws \Exception
   *
   */
  public function contentDeployExport($entity_type, array $options = ['entity_id' => NULL, 'bundle' => NULL, 'skip_entities' => NULL, 'force-update'=> FALSE, 'folder' => self::OPT]) {
    try {
      $entity_ids = $this->processingArrayOption($options['entity_id']);
      $skip_ids = $this->processingArrayOption($options['skip_entities']);

      $this->exporter->setEntityTypeId($entity_type);
      $this->exporter->setEntityBundle($options['bundle']);
      $this->exporter->setFolder($options['folder']);
      $this->exporter->setMode('default');
      $this->exporter->setForceUpdate($options['force-update']);

      if ($entity_ids) {
        $this->exporter->setEntityIds($entity_ids);
      }

      if ($skip_ids) {
        $this->exporter->setSkipEntityIds($skip_ids);
      }

      $result = $this->exporter->export()->getResult();
      $this->displayExportResult($result);
    }
    catch (\InvalidArgumentException $e) {
      $content_entity_list = $this->getAvailableEntityTypes();

      $this->logger->error($e->getMessage());
      $this->logger->notice(dt('List of available content entity types:@types', [
        '@types' => PHP_EOL . $content_entity_list,
      ]));
    }
  }

  /**
   * Exports a single entity with references.
   *
   * @param string $entity_type
   *   The entity type to export. If a wrong content entity
   *   type is entered, module displays a list of all content entity types.
   * @param array $options
   *   An associative array of options whose values come
   *   from cli, aliases, config, etc.
   *
   * @command default-content-deploy:export-with-references
   *
   * @option entity_id The ID of the entity to export.
   * @option bundle Write out the exported bundle of entity
   * @option skip_entities The ID of the entity to skip.
   * @option force-update Deletes configurations files that are not used on the site.
   * @option folder Path to the export folder.
   * @usage drush dcder node
   *   Export all nodes with references
   * @usage drush dcder node  --folder='../content'
   *   Export all nodes with references from the specified folder.
   * @usage drush dcder node --bundle=page
   *   Export all nodes with references with bundle page
   * @usage drush dcder node --bundle=page,article --entity_id=2,3,4
   *   Export all nodes with references with bundle page or article plus nodes
   *   with entitiy id 2, 3 and 4.
   * @usage drush dcder node --bundle=page,article --skip_entities=5,7
   *   Export all nodes with references with bundle page or article and skip
   *   nodes with entity id 5 and 7.
   * @usage drush dcder node --skip_entities=5,7
   *   Export all nodes and skip nodes with references with entity id 5 and 7.
   * @validate-module-enabled default_content
   * @aliases dcder,default-content-deploy-export-with-references
   *
   * @throws \Exception
   */
  public function contentDeployExportWithReferences($entity_type, array $options = ['entity_id' => NULL, 'bundle' => NULL, 'skip_entities' => NULL, 'force-update'=> FALSE, 'folder' => self::OPT]) {
    try {
      $entity_ids = $this->processingArrayOption($options['entity_id']);
      $skip_ids = $this->processingArrayOption($options['skip_entities']);

      $this->exporter->setEntityTypeId($entity_type);
      $this->exporter->setEntityBundle($options['bundle']);
      $this->exporter->setFolder($options['folder']);
      $this->exporter->setMode('reference');
      $this->exporter->setForceUpdate($options['force-update']);

      if ($entity_ids) {
        $this->exporter->setEntityIds($entity_ids);
      }

      if ($skip_ids) {
        $this->exporter->setSkipEntityIds($skip_ids);
      }

      $result = $this->exporter->export()->getResult();
      $this->displayExportResult($result);
    }
    catch (\InvalidArgumentException $e) {
      $content_entity_list = $this->getAvailableEntityTypes();

      $this->logger->error($e->getMessage());
      $this->logger->notice(dt('List of available content entity types:@types', [
        '@types' => PHP_EOL . $content_entity_list,
      ]));
    }
  }

  /**
   * Exports a whole site content.
   *
   * Config directory will be emptied
   * and all content of all entities will be exported.
   *
   * Use 'drush dcd-entity-list' for list of all content entities
   * on this system. You can exclude any entity type from export.
   *
   * @param array $options
   *   An associative array of options.
   *
   * @command default-content-deploy:export-site
   *
   * @option add_entity_type DEPRECATED. Will be removed in beta. The dcdes
   *   command exports all entity types.
   * @option force-update Deletes configurations files that are not used on the site.
   * @option folder Path to the export folder.
   * @option skip_entity_type The entity types to skip.
   *   Use 'drush dcd-entity-list' for list of all content entities.
   * @usage drush dcdes
   *   Export complete website.
   * @usage drush dcdes --folder='../content'
   *   Export complete website from the specified folder.
   * @usage drush dcdes --skip_entity_type=node,user
   *   Export complete website but skip nodes and users.
   * @validate-module-enabled default_content
   * @aliases dcdes,default-content-deploy-export-site
   *
   * @throws \Exception
   */
  public function contentDeployExportSite(array $options = ['skip_entity_type' => NULL, 'force-update'=> FALSE, 'folder' => self::OPT]) {
    $skip_ids = $this->processingArrayOption($options['skip_entity_type']);

    $this->exporter->setFolder($options['folder']);
    $this->exporter->setMode('all');
    $this->exporter->setForceUpdate($options['force-update']);

    if ($skip_ids) {
      $this->exporter->setSkipEntityIds($skip_ids);
    }

    $result = $this->exporter->export()->getResult();
    $this->displayExportResult($result);
  }

  /**
   * Import all the content defined in a content directory.
   *
   * @param array $options
   *   An associative array of options whose values come
   *   from cli, aliases, config, etc.
   *
   * @command default-content-deploy:import
   *
   * @option force-override
   *   All existing content will be overridden to the state
   *   defined in a content directory.
   * @option preserve-password
   *   Preserve existing user password.
   * @usage drush dcdi
   *   Import content. Existing older content with matching UUID will be
   *   updated. Newer content and existing content with different UUID will be
   *   ignored.
   * @usage drush dcdi --folder='../content'
   *   Import content from the specified folder.
   * @usage drush dcdi --force-override
   *   All existing content will be overridden (locally updated default content
   *   will be reverted to the state defined in a content directory).
   * @usage drush dcdi --verbose
   *   Print detailed information about importing entities.
   * @validate-module-enabled default_content
   * @aliases dcdi,default-content-deploy-import
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function contentDeployImport(array $options = ['force-override' => FALSE, 'folder' => self::OPT]) {
    // Perform read only update.
    $this->importer->setForceOverride($options['force-override']);
    $this->importer->setFolder($options['folder']);
    $this->importer->prepareForImport();
    $this->displayImportResult();

    if (!$this->isAllSkip() && $this->io()->confirm(dt('Do you really want to continue?'))) {
      $this->importer->import();
      $this->io()->success(dt('Content has been imported.'));
    }
  }

  /**
   * Get UUID of entity.
   *
   * @param $entity_type
   *   Entity type ID.
   * @param $id
   *   ID of entity.
   *
   * @return string
   *   UUID value.
   *
   * @command default-content-deploy:uuid-info
   * @usage drush dcd-uuid-info node 1
   *   Displays the current UUID value of this entity.
   * @validate-module-enabled default_content
   * @aliases dcd-uuid-info,default-content-deploy-uuid-info,dcd-uuid
   *
   * @throws \Exception
   */
  public function entityUuidInfo($entity_type, $id) {
    return $this->deployManager->getEntityUuidById($entity_type, $id);
  }

  /**
   * List current content entity types.
   *
   * @command default-content-deploy:entity-list
   * @usage drush dcd-entity-list
   *   Displays all current content entity types.
   * @aliases dcd-entity-list,default-content-deploy-entity-list
   */
  public function contentEntityList() {
    $content_entity_list = $this->getAvailableEntityTypes();

    $this->output()->writeln($content_entity_list);
  }

  /**
   * Display info before/after export.
   *
   * @param $result
   *   Array with entity types.
   */
  private function displayExportResult($result) {
    foreach ($result as $entity_type => $value) {
      $this->logger->notice(dt('Exported @count entities of the "@entity_type" entity type.', [
        '@count' => count($value),
        '@entity_type' => $entity_type, MB_CASE_TITLE,
      ]));
    }
  }

  /**
   * Display info before/after import.
   */
  private function displayImportResult() {
    $result = $this->importer->getResult();
    $array_column = array_column($result, 'status');
    $count = array_count_values($array_column);

    $this->output()->writeln(dt('- created: @count', [
      '@count' => $count['create'] ?: 0,
    ]));

    $this->output()->writeln(dt('- updated: @count', [
      '@count' => $count['update'] ?: 0,
    ]));

    $this->output()->writeln(dt('- skipped: @count', [
      '@count' => $count['skip'] ?: 0,
    ]));
  }

  /**
   * Is update or create not exist.
   *
   * @return bool
   */
  private function isAllSkip() {
    $result = $this->importer->getResult();
    $array_column = array_column($result, 'status');
    $count = array_count_values($array_column);
    $create_count =  $count['create'] ?: 0;
    $update_count = $count['update'] ?: 0;

    return ($create_count == 0 && $update_count == 0);
  }

  /**
   * Helper for processing array drush options.
   *
   * @param $option
   *   Drush option.
   *
   * @return array|null
   *   Processed value or NULL.
   */
  private function processingArrayOption($option) {
    if (!is_null($option) && $option != FALSE) {
      $array = explode(',', $option);
    }
    else {
      return NULL;
    }

    return $array;
  }

  /**
   * Convert the Available entity list to a readable form.
   *
   * @return string
   */
  private function getAvailableEntityTypes() {
    $function = function ($machine_name, $label) {
      return sprintf("%s (%s)", $machine_name, $label);
    };

    $entity_types = $this->deployManager->getContentEntityTypes();
    $map = array_map($function, array_keys($entity_types), $entity_types);

    return implode(PHP_EOL, $map);
  }

}
