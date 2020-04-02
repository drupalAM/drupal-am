<?php

namespace Drupal\better_normalizers\Normalizer;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\hal\LinkManager\LinkManagerInterface;
use Drupal\hal\Normalizer\ContentEntityNormalizer;

/**
 * Normalizer for File entity.
 */
class FileEntityNormalizer extends ContentEntityNormalizer {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = 'Drupal\file\FileInterface';

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public function __construct(LinkManagerInterface $link_manager, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, EntityTypeRepositoryInterface $entity_type_repository, EntityFieldManagerInterface $entity_field_manager, FileSystemInterface $file_system) {
    parent::__construct($link_manager, $entity_type_manager, $module_handler, $entity_type_repository, $entity_field_manager);
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($entity, $format = NULL, array $context = array()) {
    $data = parent::normalize($entity, $format, $context);
    if (!isset($context['included_fields']) || in_array('data', $context['included_fields'])) {
      // Save base64-encoded file contents to the "data" property.
      $file_data = base64_encode(file_get_contents($entity->getFileUri()));
      $data += array(
        'data' => array(array('value' => $file_data)),
      );
    }
    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function denormalize($data, $class, $format = NULL, array $context = array()) {
    // Avoid 'data' being treated as a field.
    $file_data = $data['data'][0]['value'];
    unset($data['data']);
    // Decode and save to file.
    $file_contents = base64_decode($file_data);
    $entity = parent::denormalize($data, $class, $format, $context);
    $dirname = $this->fileSystem->dirname($entity->getFileUri());
    $this->fileSystem->prepareDirectory($dirname, FileSystemInterface::CREATE_DIRECTORY);
    if ($uri = $this->fileSystem->saveData($file_contents, $entity->getFileUri())) {
      $entity->setFileUri($uri);
    }
    else {
      throw new \RuntimeException(sprintf('Failed to write %s.', $entity->getFilename()));
    }
    return $entity;
  }

}
