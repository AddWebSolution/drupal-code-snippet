<?php

namespace Drupal\azure_entity_moderation_webform\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElement\WebformCompositeBase;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'webform_example_element' element.
 *
 * @WebformElement(
 *   id = "azure_entity_moderation",
 *   label = @Translation("Azure entity moderation element"),
 *   description = @Translation("Provides an Azure entity moderation element."),
 *   category = @Translation("Azure elements"),
 *   composite = TRUE,
 *   states_wrapper = TRUE,
 * )
 */
class WebformAzureEntityModerationElement extends WebformCompositeBase {

  const SENTIMENT_LEVELS = [
    [0, 'negative'],
    [0.333, 'neutral'],
    [0.666, 'positive'],
  ];

  /**
   * Azure Formatter service.
   *
   * @var \Drupal\azure_entity_moderation_webform\Service\AzureFormatter
   */
  private $azureFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->azureFormatter = $container->get('azure_entity_moderation_webform.azure_formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultProperties() {

    // @see \Drupal\webform\Plugin\WebformElementBase::getDefaultProperties
    // @see \Drupal\webform\Plugin\WebformElementBase::getDefaultBaseProperties
    return parent::getDefaultProperties() + [
      'display_settings' => 'text',
      'moderated_elements' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function formatHtmlItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);
    $displayFormat = $this->getElementProperty($element, 'display_settings');
    if ($displayFormat == 'color') {
      $rgb = $this->azureFormatter->getColorFormat($value['value']);
      $lines[] = '<div class="moderation-value" data-color="' . $rgb . '">' . $value['value'] . '</div>';
    }
    elseif ($displayFormat == 'number') {
      $sentiment = $this->azureFormatter->getNumberFormat($value['value']);
      $lines[] = '<div class="moderation-value2"' . $sentiment . '</div>';
    }
    else {
      $text = $this->azureFormatter->getTextFormat($value['value']);
      $lines[] = '<div class="moderation-value1"' . $text . '</div>';
    }

    return $lines;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatTextItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);
    if ($value == NULL) {
      return;
    }
    return $this->azureFormatter->getTextFormat($value['value']);
  }

  /**
   * {@inheritdoc}
   */
  public function formatText(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    return $this->formatTextItemValue($element, $webform_submission, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function formatHtml(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {

    $value = $this->getValue($element, $webform_submission, $options);
    if ($value == NULL) {
      return;
    }
    $color = $this->azureFormatter->getColorFormat($value['value']);
    $element = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => '',
      '#attributes' => [
        'class' => [
          'azure-sentiment-level-color',
        ],
        'style' => "background-color: $color; display: block; width: 10px; height: 10px; border-radius: 50%;",
        'title' => $value['value'],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['composite']['display_settings'] = [
      '#type' => 'select',
      '#title' => $this->t('Display format'),
      '#description' => $this->t("Select the format of the entity moderation result."),
      '#options' => [
        'text' => $this->t('Text'),
        'number' => $this->t('Number'),
        'color' => $this->t('Color'),
      ],
    ];

    return $form;
  }

}
