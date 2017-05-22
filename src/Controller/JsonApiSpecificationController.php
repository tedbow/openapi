<?php

namespace Drupal\openapi\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller class for returning JSON API OpenAPI specification.
 */
class JsonApiSpecificationController implements ContainerInjectionInterface {

  /**
   * @var \Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator
   */
  protected $generator;

  /**
   * JsonApiSpecificationController constructor.
   *
   * @param \Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator $generator
   *   The OpenAPI generator.
   */
  public function __construct(OpenApiJsonapiGenerator $generator) {
    $this->generator = $generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('openapi.generator.jsonapi')
    );
  }

  /**
   * Gets the OpenAPI output in JSON format.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function getSpecification() {
    $spec = $this->generator->getSpecification();
    return new JsonResponse($spec);
  }

}
