<?php

namespace Drupal\context_groups;

use Drupal\context\Entity\Context;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContextGroupsManager.
 *
 * @package Drupal\context_groups
 */
class ContextGroupsManager {

  /**
   * Theme handler service.
   *
   * @var ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * {@inheritdoc}
   */
  public function __construct(ThemeHandlerInterface $themeHandler) {
    $this->themeHandler = $themeHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('theme_handler')
    );
  }

  /**
   * Get list of context groups from specified context.
   *
   * @param Context $context
   *   Context object.
   * @param FormStateInterface $form_state
   *   FormState object.
   *
   * @return object
   *   Parameters regarding groups in th context.
   */
  public function getParams(Context $context, FormStateInterface $form_state) {
    // Selected theme.
    $theme = $this->getCurrentTheme($form_state);

    // Get all context groups.
    $groups = $context->getThirdPartySettings('context_groups');

    if (empty($groups)) {
      return FALSE;
    }

    // Filter context groups to only that, which belongs to selected theme.
    $group_list[''] = t('None');
    foreach ($groups as $group_name => $value) {
      if ($value['theme'] == $theme) {
        $group_list[$group_name] = $value['label'];
        $group_data[$group_name] = $value;
        $group_data[$group_name]['id'] = $group_name;
      }
    }

    if (!isset($group_data)) {
      return FALSE;
    }
    $params = new stdClass();
    $params->groups = $group_data;
    $params->group_list = $group_list;

    // Gather groups by regions.
    $params->groups_by_region = [];
    foreach ($params->groups as $group) {
      $params->groups_by_region['region-' . $group['region']][] = $group;
    }

    return $params;
  }

  /**
   * Get current selected theme in reactions.
   *
   * @param FormStateInterface $form_state
   *   FormState object.
   *
   * @return string
   *   Return selected theme.
   */
  public function getCurrentTheme(FormStateInterface $form_state) {
    if (!empty($form_state->getUserInput()['reactions']['blocks']['theme'])) {
      $theme = $form_state->getUserInput()['reactions']['blocks']['theme'];
    }
    else {
      $theme = $this->themeHandler->getDefault();
    }
    return $theme;
  }

  /**
   * Get all parents of an element.
   *
   * @param array $groups
   *   Array of all groups.
   * @param string $group
   *   Machine name of the group.
   * @param array $parents
   *   Parents of group.
   *
   * @return array
   *   Return all parents for group in hierarchic order.
   */
  public function getAllParentsForGroup(array $groups, $group, array $parents = []) {
    if (empty($group)) {
      return array_reverse($parents);
    }
    else {
      $parents[] = $group;
      return $this->getAllParentsForGroup($groups, $groups[$group]['parent'], $parents);
    }
  }

}
