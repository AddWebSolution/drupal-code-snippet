<?php

namespace Drupal\xe_currency_conversion\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\xe_currency_conversion\XEImporter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for querying the XE Currency Conversion API.
 */
class XeCurrencyConversionQueryForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The XE importer service.
   *
   * @var \Drupal\xe_currency_conversion\XEImporter
   */
  protected $xeImporter;

  /**
   * Constructs a new XeCurrencyConversionQueryForm object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\xe_currency_conversion\XEImporter $xeImporter
   *   The XE importer service.
   */
  public function __construct(StateInterface $state, XEImporter $xeImporter) {
    $this->state = $state;
    $this->xeImporter = $xeImporter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('xe_currency_conversion.importer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xe_currency_conversion_query_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $help_message = '<p>Manually trigger a batch XE Currency Conversion import/update process. All <em>XE</em> content will be updated.</p>';
    $form['help'] = [
      '#markup' => $help_message,
    ];

    $last_synced = $this->state->get('xe_currency_conversion_last_synced', FALSE);
    $form['last_update'] = [
      '#markup' => '</br><div>' . ($last_synced ? 'Last updated on ' . date('l jS F o \a\t h:i:sa', $last_synced) :
        'You have not yet synced from the xe feed.') . '</div></br>',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import Currencies'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $batch = [
      'title' => $this->t('Importing Currencies'),
      'operations' => [
        [
          [$this, 'importCurrencies'],
          [],
        ],
      ],
      'finished' => [$this, 'finished'],
    ];

    batch_set($batch);
  }

  /**
   * Import currencies method.
   *
   * @param mixed $context
   *   The batch context.
   *
   * @throws \Exception
   */
  public function importCurrencies(&$context) {
    $importer = \Drupal::service('xe_currency_conversion.importer');
    $importer->import();
  }

  /**
   * Batch finished callback.
   *
   * @param bool $success
   *   Whether the batch process was successful.
   * @param array $results
   *   The results of the batch process.
   */
  public function finished($success, $results) {

    if ($success) {
      if (is_array($results)) {
        if (!empty($results)) {
          $message = $this->t('One currency processed.', ['@count' => count($results)]);
        }
        else {
          $message = $this->t('No currencies processed.');
        }
      }
      else {
        $message = $this->t('Finished with an error.');
      }

      $this->messenger()->addStatus($message);
    }
    else {
      $this->messenger()->addError($this->t('Batch process failed.'));
    }
  }

}
