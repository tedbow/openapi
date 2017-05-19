<?php

namespace Drupal\openapi\OpenApiGenerator;

/**
 * Generates OpenAPI Spec
 */
interface OpenApiGeneratorInterface {


  /**
   * Generates OpenAPI specification
   *
   * @param array $options
   *   The options for the specification generation.
   * @return array
   */
  public function generateSpecification($options);

  /**
   * @return string
   */
  public function getBasePath();

  /**
   * @return array
   */
  public function getSecurityDefinitions();

  /**
   * @return array
   */
  public function getTags();

  /**
   * @return array
   */
  public function getPaths();


}
