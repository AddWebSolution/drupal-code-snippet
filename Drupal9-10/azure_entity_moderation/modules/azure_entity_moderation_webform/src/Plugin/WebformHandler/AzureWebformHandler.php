<?php

namespace Drupal\azure_entity_moderation_webform\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform submission test handler.
 *
 * @WebformHandler(
 *   id = "azure",
 *   label = @Translation("Azure Entity Moderation"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Tests webform submission handler behaviors."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class AzureWebformHandler extends WebformHandlerBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The webform submission conditions validator.
   *
   * @var \Drupal\webform\WebformSubmissionConditionsValidatorInterface
   */
  protected $conditionsValidator;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The text analytics service.
   *
   * @var \Drupal\azure_text_analytics_api\Service\TextAnalytics
   */
  protected $textAnalytics;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->conditionsValidator = $container->get('webform_submission.conditions_validator');
    $instance->textAnalytics = $container->get('azure_text_analytics_api.text_analytics');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    $submissionData = $webform_submission->getData();
    // Check if the webform has an azure entity moderation element.
    $hasAzureElement = FALSE;
    foreach ($form['elements'] as $key => $element) {
      if (isset($element['#type']) && $element['#type'] == 'azure_entity_moderation') {
        $hasAzureElement = TRUE;
        $azureFieldKey = $key;
        break;
      }
    }

    // Check if the API override is true.
    if ($hasAzureElement && $submissionData[$azureFieldKey]['override'] != '1') {
      $documents = [];
      $id = 1;
      // Loop over webform elements to fetch the keys of text & textarea fields.
      foreach ($form['elements'] as $key => $element) {
        if (!is_array($element) || !isset($element['#type'])) {
          continue;
        }
        if ($element['#type'] == 'textarea' || $element['#type'] == 'textfield') {
          $documents[$id++] = [
            'text' => $submissionData[$key],
          ];
        }
      }

      if (!empty($documents)) {
        $value = $this->analyzeDocuments($documents);
        $submissionData[$azureFieldKey]['value'] = round($value, 3);
        $webform_submission->setData($submissionData);
      }
    }
  }

  /**
   * Helper function to analyze output of the textAnalytics service.
   *
   * @param array $documents
   *   The documents to analyze.
   *
   * @return bool|float|int
   *   The analyzed value.
   */
  protected function analyzeDocuments(array $documents) {
    $value = FALSE;
    if (!empty($documents)) {
      // Detect language.
      $detectedLangs = $this->textAnalytics->languages($documents);
      if (isset($detectedLangs['documents'])) {
        foreach ($detectedLangs['documents'] as $item) {
          $documents[$item['id']]['language'] = $item['detectedLanguages'][0]['iso6391Name'];
        }
      }
      // Get sentiment.
      $result = $this->textAnalytics->sentiment($documents);
      if (empty($result['errors'])) {
        $value = 0;
        // Calculate weighted average.
        $total_length = 0;
        foreach ($documents as $id => $data) {
          if (isset($result['documents'][$id - 1])) {
            $length = strlen($data['text']);
            $total_length += $length;
            $value += $result['documents'][$id - 1]['score'] * $length;
          }
        }
        if ($value > 0) {
          $value /= $total_length;
        }
      }
    }
    return $value;
  }

}
