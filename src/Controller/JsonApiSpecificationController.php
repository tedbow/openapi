<?php


namespace Drupal\openapi\Controller;


use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonApiSpecificationController implements ContainerInjectionInterface {

  /**
   * @var \Drupal\openapi\OpenApiGenerator\OpenApiJsonapiGenerator
   */
  protected $generator;

  /**
   * JsonApiSpecificationController constructor.
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

  public function getSpecification() {
    $spec = $this->generator->getSpecification();
    return new JsonResponse($spec);
  }

}
