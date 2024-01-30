<?php

namespace Drupal\azure_entity_moderation_webform\Service;

/**
 * Provides a service for formatting Azure-related data.
 */
class AzureFormatter {

  /**
   * Color map array.
   *
   * 'color' values correspond to RGB.
   */
  const COLOR_MAP = [
    ['fraction' => 0.0, 'color' => [255, 0, 0]],
    ['fraction' => 0.5, 'color' => [125, 125, 125]],
    ['fraction' => 1, 'color' => [0, 255, 0]],
  ];

  const SENTIMENT_LEVELS = [
    [0, 'negative'],
    [0.333, 'neutral'],
    [0.666, 'positive'],
  ];

  /**
   * AzureFormatter constructor.
   */
  public function __construct() {

  }

  /**
   * Calculate color based on fraction.
   *
   * @param float $fraction
   *   Fraction for which the color needs to be calculated (0-1).
   *
   * @return string
   *   Color representation as RGB.
   */
  public function getColorFormat($fraction) {
    for ($i = 1; $i < count(self::COLOR_MAP); $i++) {
      if ($fraction < self::COLOR_MAP[$i]['fraction']) {
        break;
      }
    }

    $lower = self::COLOR_MAP[$i - 1];
    $upper = self::COLOR_MAP[$i];
    $range = $upper['fraction'] - $lower['fraction'];
    $rangePct = ($fraction - $lower['fraction']) / $range;
    $pctLower = 1 - $rangePct;
    $pctUpper = $rangePct;
    $color = [
      floor($lower['color'][0] * $pctLower + $upper['color'][0] * $pctUpper),
      floor($lower['color'][1] * $pctLower + $upper['color'][1] * $pctUpper),
      floor($lower['color'][2] * $pctLower + $upper['color'][2] * $pctUpper),
    ];
    return 'rgb(' . implode(',', $color) . ')';
  }

  /**
   * Calculate the sentiment level (1 - 3 scale).
   *
   * @param float $value
   *   Sentiment level returned by Azure text analysis.
   *
   * @return int
   *   Sentiment level as an integer.
   */
  public function getNumberFormat($value) {
    for ($i = 0; $i < count(self::SENTIMENT_LEVELS); $i++) {
      if (
        $value >= self::SENTIMENT_LEVELS[$i][0] && (
          !isset(self::SENTIMENT_LEVELS[$i + 1]) ||
          $value < self::SENTIMENT_LEVELS[$i + 1][0]
        )
      ) {
        return self::SENTIMENT_LEVELS[$i][1];
      }
    }
  }

  /**
   * Get text format based on sentiment value.
   *
   * @param mixed $value
   *   The sentiment value.
   *
   * @return string
   *   The formatted text.
   */
  public function getTextFormat($value) {
    $levelMappings = [
      'negative' => 'Negative',
      'neutral' => 'Neutral',
      'positive' => 'Positive',
    ];

    $level = $this->getNumberFormat($value);
    return $levelMappings[$level];
  }

}
