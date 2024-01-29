<?php

namespace Drupal\azure_entity_moderation_webform\Element;

use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a webform element for a telephone element.
 *
 * @FormElement("azure_entity_moderation")
 */
class AzureEntityModerationElement extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo() + ['#theme' => 'webform_azure_entity_moderation'];
    return $info;
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {

    $element = [];

    $element['value'] = [
      '#type' => 'number',
      '#title' => t('Moderation Result'),
      '#min' => 0,
      '#max' => 1,
      '#step' => 0.001,
      '#default_value' => 0,
      '#size' => 5,
    ];
    if (\Drupal::currentUser()->hasPermission('manually set azure moderation value')) {
      $element['override'] = [
        '#type' => 'checkbox',
        '#default_value' => 1,
        '#title' => t('Override API value'),
      ];
    }
    return $element;
  }

}
