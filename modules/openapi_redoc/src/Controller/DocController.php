<?php

namespace Drupal\openapi_redoc\Controller;

use Drupal\Core\Url;

/**
 * Provides callback for generating docs page.
 */
class DocController {

  /**
   * Generates the doc page.
   *
   * @return array
   *   A render array.
   */
  public function generateDocs() {
    $options = \Drupal::request()->get('options', []);
    $build = [
      '#theme' => 'redoc',
      '#url' => Url::fromRoute('openapi.jsonapi', [], ['query' => ['_format' => 'json', 'options' => $options]])->setAbsolute()->toString(),
    ];
    return $build;
  }

}
