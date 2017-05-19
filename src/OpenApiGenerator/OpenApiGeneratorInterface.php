<?php

namespace Drupal\openapi\OpenApiGenerator;

/**
 * Generates OpenAPI Spec
 */
interface OpenApiGeneratorInterface {


  /**
   * Generates OpenAPI specification
   */
  public function generateSpecification();

}
