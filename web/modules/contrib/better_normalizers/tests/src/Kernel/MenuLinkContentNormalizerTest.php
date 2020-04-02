<?php

namespace Drupal\Tests\better_normalizers\Kernel;

use Drupal\better_normalizers\Normalizer\MenuLinkContentNormalizer;
use Drupal\KernelTests\KernelTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Tests menu link content normalizer.
 *
 * @group better_normalizer
 */
class MenuLinkContentNormalizerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'better_normalizers',
    'serialization',
    'menu_link_content',
    'hal',
    'node',
    'user',
    'text',
    'system',
    'link',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('menu_link_content');
  }

  /**
   * Tests prepare passengers event.
   */
  public function testMenuLinkNormalizer() {
    $node_type = NodeType::create([
      'type' => 'article',
      'name' => 'Article',
    ]);
    $node_type->save();
    $node = Node::create([
      'type' => 'article',
      'title' => 'Some node',
      'status' => 1,
      'uid' => 1,
    ]);

    $node->save();
    $parent = MenuLinkContent::create([
      'title' => 'A front page menu link',
      'link' => [['uri' => 'internal:/']],
      'menu_name' => 'tools',
    ]);
    $parent->save();
    $link = MenuLinkContent::create([
      'title' => 'A menu link to a node',
      'link' => [['uri' => 'entity:node/' . $node->id()]],
      'menu_name' => 'tools',
      'parent' => 'menu_link_content:' . $parent->uuid(),
    ]);
    $link->save();
    $serializer = $this->container->get('serializer');
    $link_manager = $this->container->get('hal.link_manager');
    $mock_field_uri = $link_manager->getRelationUri('menu_link_content', 'menu_link_content', MenuLinkContentNormalizer::PSUEDO_FIELD_NAME, []);
    $parent_field_uri = $link_manager->getRelationUri('menu_link_content', 'menu_link_content', MenuLinkContentNormalizer::PSUEDO_PARENT_FIELD_NAME, []);
    $node_url = $node->toUrl('canonical', ['absolute' => TRUE])->setRouteParameter('_format', 'hal_json')->toString();
    $parent_url = $parent->toUrl('canonical', ['absolute' => TRUE])->setRouteParameter('_format', 'hal_json')->toString();
    $context['included_fields'] = ['uuid'];
    $embedded = $serializer->normalize($node, 'hal_json', $context);
    $embedded_parent = $serializer->normalize($parent, 'hal_json', $context);
    $normalized = $serializer->normalize($link, 'hal_json');
    $this->assertEquals($node->uuid(), $normalized['link'][0]['target_uuid']);
    $this->assertEquals([$embedded], $normalized['_embedded'][$mock_field_uri]);
    $this->assertEquals([['href' => $node_url]], $normalized['_links'][$mock_field_uri]);
    $this->assertEquals([$embedded_parent], $normalized['_embedded'][$parent_field_uri]);
    $this->assertEquals([['href' => $parent_url]], $normalized['_links'][$parent_field_uri]);
    $this->assertEquals('menu_link_content:' . $parent->uuid(), $normalized['parent'][0]['value']);

    // Now we switch the URI to something else but it should still go back to
    // the same node.
    $normalized['link'][0]['link'] = 'entity:node/' . ($node->id() + 1);
    $parent_id = $link->getParentId();
    // Delete the menu link in order to create it again and test denormalize().
    $link->delete();

    /** @var \Drupal\menu_link_content\MenuLinkContentInterface $denormalized */
    $denormalized = $serializer->denormalize($normalized, MenuLinkContent::class, 'hal_json');
    $denormalized->save();
    $denormalized_url = $denormalized->getUrlObject();
    $this->assertEquals($node->toUrl()->getRouteParameters(), $denormalized_url->getRouteParameters());
    $this->assertEquals($node->toUrl()->getRouteName(), $denormalized_url->getRouteName());
    $this->assertEquals($parent_id, $denormalized->getParentId());
  }

}
