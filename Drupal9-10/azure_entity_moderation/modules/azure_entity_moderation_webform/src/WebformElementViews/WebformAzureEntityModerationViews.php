<?php

namespace Drupal\azure_entity_moderation_webform\WebformElementViews;

use Drupal\webform\Plugin\WebformElementInterface;
use Drupal\webform_views\WebformElementViews\WebformElementViewsAbstract;

/**
 * Webform views handler for checkboxes webform element.
 */
class WebformAzureEntityModerationViews extends WebformElementViewsAbstract {

  /**
   * {@inheritdoc}
   */
  public function getElementViewsData(WebformElementInterface $element_plugin, array $element) {
    $views_data = parent::getElementViewsData($element_plugin, $element);

    $views_data['field'] = [
      'id' => 'webform_submission_azure_moderation_field',
      'real field' => 'value',
      'multiple' => FALSE,
    ];
    $views_data['filter'] = [
      'id' => 'webform_submission_azure_moderation_filter',
      'real field' => 'value',
      'multiple' => FALSE,
    ];
    $views_data['sort'] = [
      'id' => 'webform_submission_field_numeric_sort',
      'real field' => 'value',
    ];

    return $views_data;
  }

}
