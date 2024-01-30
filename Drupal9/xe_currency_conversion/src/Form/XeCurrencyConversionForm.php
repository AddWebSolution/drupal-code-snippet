<?php

namespace Drupal\xe_currency_conversion\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for the xe currency configuration page.
 */
class XeCurrencyConversionForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xe_currency_conversion_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['xe_currency_conversion.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('xe_currency_conversion.settings');

    $form['xe_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Currency Conversion Configuration'),
      '#description' => $this->t('Configuration settings for Currency Layer currency conversion.'),
    ];

    $form['xe_settings']['xe_currency_conversion_client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Id'),
      '#description' => $this->t('Enter the API key for the feed.'),
      '#default_value' => $config->get('xe_currency_conversion_client_id'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save settings'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('xe_currency_conversion.settings');
    $config->set('xe_currency_conversion_client_id', $form_state->getValue('xe_currency_conversion_client_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
