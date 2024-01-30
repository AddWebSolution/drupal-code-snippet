<?php

namespace Drupal\xe_currency_conversion;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\State\StateInterface;

/**
 * Provides XE currency conversion functionality.
 *
 * @package Drupal\xe_currency_conversion
 */
class XEImporter {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new XEImporter object.
   *
   * @param \Drupal\Core\Queue\QueueFactory $queueFactory
   *   The queue factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(QueueFactory $queueFactory = NULL, ConfigFactoryInterface $configFactory = NULL, StateInterface $state = NULL, MessengerInterface $messenger = NULL, Connection $database = NULL) {
    $this->queueFactory = $queueFactory;
    $this->configFactory = $configFactory;
    $this->state = $state;
    $this->messenger = $messenger;
    $this->database = $database ?: \Drupal::database();
  }

  /**
   * Imports xe.com currency rate data.
   *
   * @param bool $update
   *   Whether or not the import is an update.
   * @param bool $batch
   *   Whether or not batch API should be used for import.
   *
   * @return int|array
   *   A response of the last sync time or an array of
   *   tasks if called from a batch process.
   */
  public function import($update = FALSE, $batch = FALSE) {
    // Retrieve the client id if it has been specified.
    $client_id = $this->configFactory->get('xe_currency_conversion.settings')->get('xe_currency_conversion_client_id');

    if ($client_id) {
      $url = XE_CURRENCY_CONVERSION_BASE_URL . 'live?access_key=' . $client_id . '&source=GBP&date=' . date("Y-m-d");
      // Grab the JSON feed.
      $json = file_get_contents($url);

      if (trim($json) != '') {
        $feed = json_decode($json, TRUE);

        if (isset($feed['success']) && $feed['success'] == TRUE) {
          $response = $this->parseAndSave($feed, $update, $batch);

          // Now save the last sync time.
          $this->state->set('xe_currency_conversion_last_synced', REQUEST_TIME);
          if (!$update) {
            $this->state->set('xe_currency_conversion_first_sync_done', TRUE);
          }
        }
        else {
          $error_message = !empty($feed['error']) ? $feed['error']['code'] . '. ' . $feed['error']['info'] : '';
          $this->addMessage('Currency Conversion - error in feed ' . $error_message, 'error');
        }
      }

      // Called from batch function - return tasks.
      if (is_array($response) && $batch) {
        return $response;
      }

      // Return the last sync time.
      return $this->state->get('xe_currency_conversion_last_synced');
    }
  }

  /**
   * Parses and saves currency conversion rates.
   *
   * @param array $feed
   *   The JSON feed.
   * @param bool $update
   *   Whether or not the current import is an update.
   * @param bool $batch
   *   Whether or not batch API should be used for import.
   *
   * @return array|null
   *   An array of tasks if called from a batch process, otherwise null.
   */
  protected function parseAndSave(array $feed, $update = FALSE, $batch = FALSE) {
    if (!$batch) {
      $queue = $this->queueFactory->get('xe_currency_conversion');
    }
    else {
      $tasks = [];
    }

    $client_id = $this->configFactory->get('xe_currency_conversion.settings')->get('xe_currency_conversion_client_id');

    if ($client_id) {
      // Query the list endpoint to add pretty names to the currencies.
      $url = XE_CURRENCY_CONVERSION_BASE_URL . 'list?access_key=' . $client_id;
      $json = file_get_contents($url);
      $currency_code_name = json_decode($json, TRUE);
      // Loop through the currencies in the feed.
      foreach ($feed['quotes'] as $currencies => $rate) {
        $currency = str_replace($feed['source'], '', $currencies);
        $currency_name = $currency_code_name['currencies'][$currency];

        if (!empty($currency) && !empty($currency_name)) {
          $item = new \stdClass();

          $item->name = $currency_name;
          $item->symbol = $currency;
          $item->inverse = 1 / $rate;
          $item->rate = $rate;

          // Save data.
          if (!empty($item)) {
            // Update the data if the symbol is already available.
            $existing_item = xe_currency_conversion_load_by_symbol($item->symbol);

            if (!empty($existing_item)) {
              if (!empty($existing_item['symbol']) && $existing_item['symbol'] > 0) {
                $item->symbol = $existing_item['symbol'];

                $this->database->update('xe_currency_conversion')
                  ->fields([
                    'name' => $item->name,
                    'rate' => $item->rate,
                    'inverse' => $item->inverse,
                  ])
                  ->condition('symbol', $item->symbol)
                  ->execute();
              }
            }
            else {
              // Insert the data if the symbol is not available.
              $this->database->insert('xe_currency_conversion')
                ->fields([
                  'name' => $item->name,
                  'symbol' => $item->symbol,
                  'rate' => $item->rate,
                  'inverse' => $item->inverse,
                ])
                ->execute();
            }
          }

          if ($update) {
            // Check if the item already exists.
            $existing_item = xe_currency_conversion_load_by_symbol($item->symbol);

            if (!empty($existing_item)) {
              if (!empty($existing_item['crid']) && $existing_item['crid'] > 0) {
                $item->crid = $existing_item['crid'];
              }
            }
          }

          if (!$batch) {
            // Queue the item to be saved.
            $queue->createItem($item);
          }
          else {
            $tasks[] = $item;
          }
        }
      }

      // Called from a batch process - return tasks.
      if (!empty($tasks) && $batch) {
        return $tasks;
      }
      else {
        // Return null if not in a batch process.
        return NULL;
      }
    }
    else {
      // Return null if not in a batch process.
      return NULL;
    }
  }

}
