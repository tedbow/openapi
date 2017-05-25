<?php

namespace Drupal\openapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Controller class for returning JSON API OpenAPI specification.
 */
class JsonApiSpecificationController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator
   */
  protected $generator;

  /**
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * JsonApiSpecificationController constructor.
   *
   * @param \Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator $generator
   *   The OpenAPI generator.
   */
  public function __construct(OpenApiJsonapiGenerator $generator, RequestStack $request) {
    $this->generator = $generator;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('openapi.generator.jsonapi'),
      $container->get('request_stack')
    );
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
