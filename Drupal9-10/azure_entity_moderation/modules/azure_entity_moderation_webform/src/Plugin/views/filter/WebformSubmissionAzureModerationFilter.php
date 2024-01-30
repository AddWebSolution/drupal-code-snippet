<?php

namespace Drupal\azure_entity_moderation_webform\Plugin\views\filter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\webform_views\Plugin\views\WebformSubmissionTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Select filter based on value of a webform submission.
 *
 * @ViewsFilter("webform_submission_azure_moderation_filter")
 */
class WebformSubmissionAzureModerationFilter extends InOperator {

  use WebformSubmissionTrait;

  /**
   * Denote the option of "all" options.
   *
   * @var string
   *   The constant representing "all" options.
   */
  const ALL = 'all';

  /**
   * The value form type.
   *
   * @var string
   *   The form type for the value.
   */

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * WebformSubmissionFieldFilter constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);
    $form['value']['#required'] = FALSE;
    unset($form['value']['#options'][self::ALL]);
  }

  /**
   * {@inheritdoc}
   */
  public function showValueForm(&$form, FormStateInterface $form_state) {
    parent::showValueForm($form, $form_state);
    $form['value']['#options'] = [self::ALL => $this->valueOptions[self::ALL]] + $form['value']['#options'];
  }

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      $this->valueOptions = [
        'positive' => $this->t('Positive'),
        'neutral' => $this->t('Neutral'),
        'negative' => $this->t('Negative'),
      ];
    }
    return $this->valueOptions;
  }

  /**
   * {@inheritdoc}
   */
  public function acceptExposedInput($input) {
    $accept = parent::acceptExposedInput($input);
    $identifier = $this->options['expose']['identifier'];
    if ($input[$identifier] == self::ALL) {
      return FALSE;
    }
    return $accept;
  }

  /**
   * {@inheritdoc}
   */
  protected function opSimple() {
    if (!$this->value || !isset($this->value[0])) {
      return;
    }
    $value = $this->value[0];
    $this->ensureMyTable();
    if ($value == 'positive') {
      $this->query->addWhere($this->options['group'], "$this->tableAlias.$this->realField", 0.666, '>=');
    }
    elseif ($value == 'neutral') {
      $this->query->addWhere($this->options['group'], "$this->tableAlias.$this->realField", 0.333, '>');
      $this->query->addWhere($this->options['group'], "$this->tableAlias.$this->realField", 0.666, '<');
    }
    else {
      $this->query->addWhere($this->options['group'], "$this->tableAlias.$this->realField", 0.333, '<=');
    }
  }

}
