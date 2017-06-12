<?php

namespace Drupal\openapi_docs\Controller;


/**
 * Swagger UI controller for JSON API documentation.
 */
class SwaggerUIJsonApiController extends SwaggerUIControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getJsonGeneratorRoute() {
    return 'openapi.jsonapi';
  }

}
