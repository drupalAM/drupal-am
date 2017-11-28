<?php

namespace Drupal\better_normalizers;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Alter the container to include our own normalizers.
 */
class BetterNormalizersServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Add a normalizer service for file entities.
    $service_definition = new Definition('Drupal\better_normalizers\Normalizer\FileEntityNormalizer', array(
      new Reference('hal.link_manager'),
      new Reference('entity.manager'),
      new Reference('module_handler'),
    ));
    // The priority must be higher than that of
    // serializer.normalizer.file_entity.hal in hal.services.yml.
    $service_definition->addTag('normalizer', array('priority' => 30));
    $container->setDefinition('serializer.normalizer.entity.file_entity', $service_definition);

    // Add a normalizer service for file fields.
    $service_definition = new Definition('Drupal\better_normalizers\Normalizer\FileItemNormalizer', array(
      new Reference('hal.link_manager'),
      new Reference('serializer.entity_resolver'),
    ));
    // Supersede EntityReferenceItemNormalizer.
    $service_definition->addTag('normalizer', array('priority' => 20));
    $container->setDefinition('serializer.normalizer.entity_reference_item.file_entity', $service_definition);

    $modules = $container->getParameter('container.modules');
    if (isset($modules['menu_link_content'])) {
      // Add a normalizer service for menu-link-content entities.
      $service_definition = new Definition('Drupal\better_normalizers\Normalizer\MenuLinkContentNormalizer', array(
        new Reference('hal.link_manager'),
        new Reference('entity.manager'),
        new Reference('module_handler'),
        new Reference('serializer.normalizer.entity_reference_item.hal'),
      ));
      // The priority must be higher than that of
      // serializer.normalizer.entity.hal in hal.services.yml, but lower than
      // better_normalizers.normalizer.menu_link_content.hal.
      $service_definition->addTag('normalizer', array('priority' => 30));
      $container->setDefinition('serializer.normalizer.menu_link_content.hal', $service_definition);
    }

  }

}
