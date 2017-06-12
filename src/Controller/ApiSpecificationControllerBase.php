<?php

namespace Drupal\openapi\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\openapi\OpenApiGenerator\OpenApiGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiSpecificationControllerBase
 * @package Drupal\openapi\Controller
 */
abstract class ApiSpecificationControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\openapi\OpenApiGenerator\OpenApiGeneratorInterface
   */
  protected $generator;

  /**
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * JsonApiSpecificationController constructor.
   *
   * @param \Drupal\openapi\OpenApiGenerator\OpenApiGeneratorInterface $generator
   *   The OpenAPI generator.
   */
  public function __construct(OpenApiGeneratorInterface $generator, RequestStack $request) {
    $this->generator = $generator;
    $this->request = $request;
  }

  /**
   * Gets the OpenAPI output in JSON format.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function getSpecification() {
    $options = $this->request->getCurrentRequest()->get('options', []);
    $spec = $this->generator->getSpecification($options);
    return new JsonResponse($spec);
  }

}
