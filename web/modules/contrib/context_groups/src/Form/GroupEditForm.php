<?php

namespace Drupal\context_groups\Form;

use Drupal\context\ContextManager;
use Drupal\context\Entity\Context;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\StatusMessages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class GroupEditForm.
 *
 * @package Drupal\context_groups\Form
 */
class GroupEditForm extends FormBase {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * ContextManager definition.
   *
   * @var ContextManager
   */
  protected $contextManager;

  /**
   * Context object.
   *
   * @var Context
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManager $entity_type_manager, ContextManager $context_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->contextManager = $context_manager;
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
    return 'context-group-edit-form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $context = NULL, $context_group = NULL) {
    $this->context = $this->entityTypeManager->getStorage('context')->load($context);
    $group = $this->context->getThirdPartySetting('context_groups', $context_group);

    // Group name field.
    $form['group_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group name'),
      '#required' => TRUE,
      '#default_value' => $group['label'],
      '#description' => $this->t('machine name: @machine_name', ['@machine_name' => $group['name']]),
    ];

    $form['name'] = [
      '#type' => 'hidden',
      '#value' => $group['name'],
    ];

    $form['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class'),
      '#descriptions' => $this->t('Set classes to group. Separate multiple classes with space.'),
      '#default_value' => $group['class'],
    ];

    $form['context_id'] = [
      '#type' => 'hidden',
      '#value' => $context,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#ajax' => [
        'callback' => '::submitFormAjax',
        'wrapper' => 'context-group-edit-form',
        'effect' => 'fade',
      ],
    ];

    return $form;
  }

  /**
   * Handle when the form is submitted through AJAX.
   *
   * @return AjaxResponse
   *   AjaxResponce object.
   */
  public function submitFormAjax(&$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    if ($form_state->getErrors()) {
      $output[] = StatusMessages::renderMessages(NULL);
      $output[] = $form;

      // Ajax commands.
      $response->addCommand(new RemoveCommand('.messages.messages--error'));
      $response->addCommand(new ReplaceCommand('[id^="context-group-edit-form"]', $output));
    }
    else {
      $response->addCommand(new CloseModalDialogCommand());
      $contextForm = $this->contextManager->getForm($this->context, 'edit');
      $response->addCommand(new ReplaceCommand('#context-reactions', $contextForm['reactions']));
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Load current context.
    $cid = $form_state->getValue('context_id');
    $context = $this->entityTypeManager->getStorage('context')->load($cid);

    $context_groups = $context->getThirdPartySettings('context_groups');
    $data = $context_groups[$form_state->getValue('name')];

    // Update context group settings.
    $data['label'] = $form_state->getValue('group_name');
    $data['class'] = $form_state->getValue('class');

    // Save group to context.
    $context->setThirdPartySetting('context_groups', $form_state->getValue('name'), $data);
    // Save context.
    $context->save();
  }

}
