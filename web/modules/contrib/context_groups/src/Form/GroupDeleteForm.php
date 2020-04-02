<?php

namespace Drupal\context_groups\Form;

use Drupal\context\ContextManager;
use Drupal\context\Entity\Context;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class GroupDeleteForm.
 *
 * @package Drupal\context_groups\Form
 */
class GroupDeleteForm extends ConfirmFormBase {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The Context module context manager.
   *
   * @var \Drupal\context\ContextManager
   */
  protected $contextManager;

  /**
   * The context group that is being removed.
   *
   * @var array
   */
  protected $contextGroup;

  /**
   * The context from which the context group is being removed.
   *
   * @var Context
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManager $entity_type_manager, ContextManager $contextManager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->contextManager = $contextManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('context.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'context-group-delete-form';
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    // TODO: Implement getCancelUrl() method.
  }

  /**
   * Returns the question to ask the user.
   *
   * @return string
   *   The form question. The page title will be set to this value.
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to remove the "@label" group?', [
      '@label' => $this->contextGroup['label'],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $context = NULL, $context_group = NULL) {
    $this->context = $this->entityTypeManager->getStorage('context')->load($context);
    $group = $this->context->getThirdPartySetting('context_groups', $context_group);

    $this->contextGroup = $group;
    $form = parent::buildForm($form, $form_state);

    // Submit the form with AJAX if possible.
    $form['actions']['submit']['#ajax'] = [
      'callback' => '::submitFormAjax',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->context->unsetThirdPartySetting('context_groups', $this->contextGroup['name']);

    // If this group was parent of another group or block, update it's parent.
    // Check groups.
    $list_of_groups = $this->context->getThirdPartySettings('context_groups');
    foreach ($list_of_groups as $group) {
      if ($group['parent'] == $this->contextGroup['name']) {
        $group['parent'] = '';
        $this->context->setThirdPartySetting('context_groups', $group['name'], $group);
      }
    }

    // Check blocks in context.
    $blocks = $this->context->getReactions()->get('blocks');
    $list_of_blocks = $blocks->getBlocks();
    foreach ($list_of_blocks as $uid => $block_plugin) {
      $configuration = $block_plugin->getConfiguration();
      if (isset($configuration['parent_wrapper']['parent']) && $configuration['parent_wrapper']['parent'] == $this->contextGroup['name']) {
        $configuration['parent_wrapper']['parent'] = '';
        $blocks->updateBlock($uid, $configuration);
      }
    }

    $this->context->save();
  }

  /**
   * Handle when the form is submitted trough AJAX.
   *
   * @return AjaxResponse
   *   AjaxResponse object.
   */
  public function submitFormAjax() {
    $contextForm = $this->contextManager->getForm($this->context, 'edit');

    $response = new AjaxResponse();

    $response->addCommand(new CloseModalDialogCommand());
    $response->addCommand(new ReplaceCommand('#context-reactions', $contextForm['reactions']));

    return $response;
  }

}
