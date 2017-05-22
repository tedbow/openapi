<?php

namespace Drupal\openapi\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\openapi\OpenApiGenerator\OpenApiRestGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller class for returning RESTs OpenAPI specification.
 */
class RestSpecificationController implements ContainerInjectionInterface {

  /**
   * The OpenAPI generator.
   *
   * @var \Drupal\openapi\OpenApiGenerator\OpenApiRestGenerator
   */
  protected $restGenerator;

  /**
   * RestSpecificationController constructor.
   *
   * @param \Drupal\openapi\OpenApiGenerator\OpenApiRestGenerator $rest_generator
   *   The OpenAPI generator.
   */
  public function __construct(OpenApiRestGenerator $rest_generator) {
    $this->restGenerator = $rest_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('openapi.generator.rest')
    );
  }

  /**
   * Gets the OpenAPI output in JSON format.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function getEntitiesSpecification() {
    $spec = $this->restGenerator->getSpecification(['resource_types' => 'entities']);
    return new JsonResponse($spec);
  }

  /**
   * Gets the OpenAPI output in JSON format for a specific bundle.
   *
   * This is need for openapi_doc module.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function getEntityBundleSpecification($entity_type_id, $bundle_name) {
    return new JsonResponse($this->restGenerator->getSpecification([
      'entity_type_id' => $entity_type_id,
      'bundle_name' => $bundle_name
    ]));
  }

}
