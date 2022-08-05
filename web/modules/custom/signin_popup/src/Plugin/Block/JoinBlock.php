<?php

namespace Drupal\signin_popup\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a Copyright block.
 *
 * @Block(
 *   id = "join_now_block",
 *   admin_label = @Translation("Join now"),
 * )
 */
class JoinBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var AccountInterface $account
   */
  protected $account;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $account, LanguageManager $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->account = $account;
    $this->languageManager   = $language_manager;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $roles = \Drupal::currentUser()->getRoles();
    $is_editor = FALSE;
    if (in_array('editor', $roles) || in_array('administrator', $roles)) {
      $is_editor = TRUE;
    }
    return [
      '#theme' => 'join_now_block',
      '#is_anonymous' => $this->account->isAnonymous(),
      '#is_editor' => $is_editor,
      '#langcode' => $this->languageManager->getCurrentLanguage()->getId(),
    ];
  }
}
