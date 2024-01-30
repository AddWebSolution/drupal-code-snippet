<?php

namespace Drupal\azure_entity_moderation_webform\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ResultRow;
use Drupal\webform_views\Plugin\views\field\WebformSubmissionField;

/**
 * Webform submission composite field.
 *
 * @ViewsField("webform_submission_azure_moderation_field")
 */
class WebformSubmissionAzureModerationField extends WebformSubmissionField {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var \Drupal\webform\WebformSubmissionInterface $webform_submission */
    $webform_submission = $this->getEntity($values);
    if ($webform_submission && $webform_submission->access('view')) {
      $webform = $webform_submission->getWebform();

      // Get format and element key.
      $format = $this->options['webform_element_format'];
      $element_key = $this->definition['webform_submission_field'];

      // Get element and element handler plugin.
      $element = $webform->getElement($element_key, TRUE);
      if (!$element) {
        return [];
      }

      // Set the format.
      $element['#format'] = $format;

      // Get element handler and get the element's HTML render array.
      $element_handler = $this->webformElementManager->getElementInstance($element);
      $options = [];
      if ($format == 'color') {
        return $element_handler->formatHtml($element, $webform_submission, $options);
      }

      return $element_handler->formatText($element, $webform_submission, $options);
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['webform_element_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Format'),
      '#description' => $this->t('Specify how to format this value.'),
      '#options' => [
        'text' => t('Text'),
        'color' => t('Color'),
      ],
      '#default_value' => $this->options['webform_element_format'] ?: 'text',
    ];

    $form['webform_element_format']['#access'] = !empty($form['webform_element_format']['#options']);
  }

}
