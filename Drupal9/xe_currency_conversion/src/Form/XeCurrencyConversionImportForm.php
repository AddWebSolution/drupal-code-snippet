<?php

namespace Drupal\xe_currency_conversion\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\xe_currency_conversion\XeCurrencyConversionService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import form for XE Currency Conversion.
 */
class XeCurrencyConversionImportForm extends FormBase {

  /**
   * The XE Currency Conversion service.
   *
   * @var \Drupal\xe_currency_conversion\XeCurrencyConversionService
   */
  protected $xeCurrencyConversionService;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Class constructor.
   *
   * @param \Drupal\xe_currency_conversion\XeCurrencyConversionService $xeCurrencyConversionService
   *   The XE Currency Conversion service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The Messenger service.
   */
  public function __construct(XeCurrencyConversionService $xeCurrencyConversionService, MessengerInterface $messenger) {
    $this->xeCurrencyConversionService = $xeCurrencyConversionService;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('xe_currency_conversion.service'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xe_currency_conversion_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import Data'),
    ];

    // Add a Back button to navigate to the config form.
    $form['back_to_config'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back to Configuration'),
      '#submit' => ['::backToConfigForm'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->xeCurrencyConversionService->importData();
    $this->messenger->addMessage($this->t('Data imported successfully.'));
  }

  /**
   * Custom submit handler to redirect to the config form.
   */
  public function backToConfigForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('xe_currency_conversion.config_form');
  }

}
