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
class XeCurrencyConversionService {

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
    $this->database = $database;
  }

  /**
   * Imports xe.com currency rate data.
   */
  public function importData() {
    $client_id = $this->configFactory->get('xe_currency_conversion.settings')->get('xe_currency_conversion_client_id');

    if ($client_id) {
      // Construct the URL for the XE.com API.
      $url = XE_CURRENCY_CONVERSION_BASE_URL . 'live?access_key=' . $client_id . '&source=GBP&date=' . date("Y-m-d");

      // Fetch the JSON data from the API.
      $json = file_get_contents($url);

      if (trim($json) != '') {
        $feed = json_decode($json, TRUE);

        if (isset($feed['success']) && $feed['success'] == TRUE) {
          // Call the parseAndSave method to handle the data.
          $this->parseAndSave($feed);
        }
        else {
          $error_message = !empty($feed['error']) ? $feed['error']['code'] . '. ' . $feed['error']['info'] : '';
          $this->addMessage('Currency Conversion - error in feed ' . $error_message, 'error');
        }
      }
    }
  }

  /**
   * Parses and saves the currency data.
   *
   * @param array $feed
   *   The currency data feed.
   */
  protected function parseAndSave(array $feed) {
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
            $existing_item = $this->database->select('xe_currency_conversion', 'xc')
              ->fields('xc')
              ->condition('symbol', $item->symbol)
              ->execute()
              ->fetchAssoc();

            if ($existing_item) {
              $this->database->update('xe_currency_conversion')
                ->fields([
                  'name' => $item->name,
                  'rate' => $item->rate,
                  'inverse' => $item->inverse,
                ])
                ->condition('symbol', $item->symbol)
                ->execute();
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
        }
      }
    }
  }

  /**
   * Adds a message using the messenger service.
   *
   * @param string $message
   *   The message to add.
   * @param string $type
   *   The type of the message (e.g., 'status' or 'error').
   */
  protected function addMessage($message, $type = 'status') {
    // Add a message using the messenger service.
    $this->messenger->addMessage($message, $type);
  }

}
