<?php

namespace Drupal\template_whisperer\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\template_whisperer\TemplateWhispererManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Template Whisperer' condition.
 *
 * @Condition(
 *   id = "template_whisperer",
 *   label = @Translation("Template Whisperer"),
 *   context = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   }
 * )
 */
class TemplateWhisperer extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The Template Manager.
   *
   * @var \Drupal\template_whisperer\TemplateWhispererManager
   */
  protected $twManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityStorageInterface $entity_storage, array $configuration, $plugin_id, $plugin_definition, TemplateWhispererManager $template_whisperer_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->twManager = $template_whisperer_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity_type.manager')->getStorage('node_type'),
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.template_whisperer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'template_whisperer/block';

    $options = $this->twManager->getList();

    $form['suggestions'] = [
      '#type'          => 'checkboxes',
      '#title'         => $this->t('When the node has the following Suggestion(s)'),
      '#default_value' => $this->configuration['suggestions'],
      '#options'       => array_map('\Drupal\Component\Utility\Html::escape', $options),
      '#description'   => $this->t('Select suggestion(s) to enforce only on those selected. If none are selected, all suggestion will be allowed.'),
    ];

    $form = parent::buildConfigurationForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'suggestions' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['suggestions'] = array_filter($form_state->getValue('suggestions'));
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   */
  public function evaluate() {
    $suggestions = $this->configuration['suggestions'];
    if (empty($suggestions) && !$this->isNegated()) {
      return TRUE;
    }

    $node = $this->getContextValue('node');
    $node_suggestions = $this->twManager->suggestionsFromEntity($node);

    // NOTE: The context system handles negation for us.
    return count(array_intersect($suggestions, $node_suggestions)) > 0;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // Use the suggestion labels. They will be sanitized below.
    $templates = $this->configuration['suggestions'];
    if (count($templates) > 1) {
      $templates = implode(', ', $templates);
    }
    else {
      $templates = reset($templates);
    }

    if ($this->isNegated()) {
      return $this->t('The node template is not @template', ['@template' => $templates]);
    }
    else {
      return $this->t('The node template is @template', ['@template' => $templates]);
    }
  }

}
