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
  public function generateDocs($api_module) {
    $options = \Drupal::request()->get('options', []);
    $build = [
      '#theme' => 'redoc',
      '#openapi_url' => Url::fromRoute("openapi.$api_module", [], ['query' => ['_format' => 'json', 'options' => $options]])->setAbsolute()->toString(),
    ];
    return $build;
  }

}
