<?php

namespace Drupal\openapi\OpenApiGenerator;

/**
 * Generates OpenAPI Spec.
 *
 * @todo Is this interface needed? Could this just contain getSpecification()?
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
   * Generates OpenAPI specification.
   *
   * @param array $options
   *   The options for the specification generation.
   *
   * @return array
   *   The specification output.
   */
  public function getSpecification(array $options = []);

  public function getDefinitions();


}
