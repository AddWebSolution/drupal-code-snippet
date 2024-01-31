<?php

namespace Drupal\xe_currency_conversion\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for configuring XE currency conversion settings.
 *
 * @package Drupal\xe_currency_conversion\Form
 */
class XeCurrencyConversionSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['xe_currency_conversion.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xe_currency_conversion_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('xe_currency_conversion.settings');

    $form['xe_currency_conversion_client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client API ID'),
      '#default_value' => $config->get('xe_currency_conversion_client_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('xe_currency_conversion.settings')
      ->set('xe_currency_conversion_client_id', $form_state->getValue('xe_currency_conversion_client_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
