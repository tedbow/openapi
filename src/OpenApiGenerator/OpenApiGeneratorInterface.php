<?php

namespace Drupal\openapi\OpenApiGenerator;

/**
 * Generates OpenAPI Spec
 */
interface OpenApiGeneratorInterface {

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

  /**
   * Generates OpenAPI specification
   *
   * @param array $options
   *   The options for the specification generation.
   * @return array
   */
  public function getSpecification();

  public function getDefinitions();


}
