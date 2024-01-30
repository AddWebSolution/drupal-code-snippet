<?php

namespace Drupal\pim_import_csv\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\pim_import_csv\PimCsvProcessor;

/**
 * Provides a form for CSV file upload.
 */
class PimImportCsvForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The product data service.
   *
   * @var \Drupal\pim_import_csv\Service\PimCsvProcessor
   */
  protected $productDataService;

  /**
   * PimImportCSVForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *   The entity type manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\pim_import_csv\PimCsvProcessor $productDataService
   *   The product csv preprocessor service.
   */
  public function __construct(
    EntityTypeManagerInterface $entityManager,
    MessengerInterface $messenger,
    PimCsvProcessor $productDataService
  ) {
    $this->entityManager = $entityManager;
    $this->messenger = $messenger;
    $this->productDataService = $productDataService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('messenger'),
      $container->get('pim_import_csv.product_data_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pim_import_csv_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['download_button'] = [
      '#type' => 'link',
      '#title' => $this->t('Download CSV Template'),
      '#url' => Url::fromRoute('pim_import_csv.download_template'),
      '#attributes' => [
        'class' => ['button'],
      ],
    ];

    $form['csv_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload CSV File'),
      '#description' => $this->t('Upload a CSV file for import.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#upload_location' => 'public://csv_files/',
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save and Import'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the file ID from the form submission.
    $file_ids = $form_state->getValue('csv_file');
    // Ensure that we have a file ID.
    if (!empty($file_ids)) {
      // Load the file entity.
      $file = $this->entityManager->getStorage('file')->load($file_ids[0]);
      if ($file) {
        // Get the URI of the file.
        $file_uri = $file->getFileUri();
        // Parse the CSV file.
        $csv_rows = $this->productDataService->parseCsvFile($file_uri);
        // Separate the header row from the data rows.
        $header_row = array_shift($csv_rows);
        if (!empty($csv_rows)) {
          // Batch process.
          $batch = [
            'operations' => [],
            'finished' => [$this, 'batchFinished'],
            'title' => $this->t('PIM CSV Import'),
            'init_message' => $this->t('Starting node creation or updation process.'),
            'progress_message' => $this->t('Processed @current out of @total values.'),
            'error_message' => $this->t('Error occurred during node creation/updation process.'),
          ];
          foreach ($csv_rows as $row) {
            // Map header values to keys for the current data row.
            $mapped_data = array_combine($header_row, $row);
            // Check if "product_sku" is a key in the mapped data.
            if (isset($mapped_data['product_sku'])) {
              $batch['operations'][] = [
                [$this, 'processBatchRow'],
                [$mapped_data],
              ];
            }
          }
          batch_set($batch);
          // Display a success message.
          $this->messenger->addMessage($this->t('CSV file processed. Nodes created or Updated.'));
        }
        else {
          // Display a error message.
          $this->messenger->addError($this->t('Uploaded File is empty'));
        }
      }
      else {
        // Display an error message if the file could not be loaded.
        $this->messenger->addError($this->t('Error loading the uploaded CSV file.'));
      }
    }
    else {
      // Display an error message if no file was uploaded.
      $this->messenger->addError($this->t('No file uploaded.'));
    }
  }

  /**
   * Batch operation callback for processing each row.
   */
  public function processBatchRow($mapped_data, &$context) {
    // Check if "product_sku" is a key in the mapped data.
    if (isset($mapped_data['product_sku'])) {
      $product_sku = $mapped_data['product_sku'];
      // Check if a node with the same SKU already exists.
      $query = $this->entityManager->getStorage('node')->getQuery()
        ->accessCheck(FALSE)
        ->condition('type', 'product')
        ->condition('field_product_code', $product_sku);
      $existing_nids = $query->execute();

      if (!empty($existing_nids)) {
        // Node with the same SKU exists, update the existing node.
        $existing_nid = reset($existing_nids);
        $node = $this->entityManager->getStorage('node')->load($existing_nid);
        $this->productDataService->setNodeFieldValues($node, $mapped_data);
      }
      else {
        // Create a new node if it doesn't exist.
        $node = Node::create([
          'type' => 'product',
        ]);
        $this->productDataService->setNodeFieldValues($node, $mapped_data);
      }
      // Update the batch progress.
      $context['message'] = $this->t('Processed SKU @sku', ['@sku' => $product_sku]);
    }
  }

  /**
   * Batch finished callback.
   */
  public static function batchFinished($success, $results, $operations) {
    if ($success) {
      \Drupal::messenger()->addStatus(t('Batch import completed successfully.'));
    }
    else {
      \Drupal::messenger()->addError(t('Batch import encountered errors. Check the logs for more information.'));
    }
  }

}
