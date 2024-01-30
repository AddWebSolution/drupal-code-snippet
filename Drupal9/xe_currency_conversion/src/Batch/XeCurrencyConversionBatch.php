<?php

namespace Drupal\xe_currency_conversion\Batch;

use Drupal\Core\Messenger\MessengerInterface;

/**
 * Batch setup function.
 */
class XeCurrencyConversionBatch {

  /**
   * The tasks to be processed.
   *
   * @var array
   */
  protected $tasks;

  /**
   * The current progress of the batch.
   *
   * @var int
   */
  protected $progress = 0;

  /**
   * The maximum progress of the batch.
   *
   * @var int
   */
  protected $max = 0;

  /**
   * The current item being processed.
   *
   * @var int
   */
  protected $currentItem = 0;

  /**
   * The message to display during processing.
   *
   * @var string
   */
  protected $message = '';

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Batch setup function.
   *
   * @param array $tasks
   *   An array of tasks to be processed.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   *
   * @return \Drupal\Core\Messenger\MessengerInterface
   *   The messenger service.
   */
  public function __construct(array $tasks, MessengerInterface $messenger) {
    $this->tasks = $tasks;
    $this->max = count($this->tasks);
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function execute($batch, array &$context) {
    $this->tasks = $batch['#tasks'];
    $this->max = count($this->tasks);

    if (!empty($this->tasks)) {
      foreach ($this->tasks as $task) {
        if ($task) {
          $this->progress++;
          $this->currentItem++;

          // Call the import method from XEImporter class.
          $importer = \Drupal::service('xe_currency_conversion.importer');
          $importer->import($task->symbol);
        }
      }
    }

    // Update progress.
    $context['finished'] = $this->progress / $this->max;

    $context['message'] = t('Processed @current of @total currencies.', [
      '@current' => $this->currentItem,
      '@total' => $this->max,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public static function finished($success, $results, $operations) {
    if ($success) {
      if (!empty($results)) {
        $message = t('Processed @count currencies.', ['@count' => count($results)]);
      }
      else {
        $message = t('No currencies processed.');
      }
    }
    else {
      $message = t('Finished with an error.');
    }

    // Use \Drupal::messenger() instead of $this->messenger.
    \Drupal::messenger()->addStatus($message);
  }

}
