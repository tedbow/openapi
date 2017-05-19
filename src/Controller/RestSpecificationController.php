<?php


namespace Drupal\openapi\Controller;


use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\openapi\OpenApiGenerator\OpenApiRestGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestSpecificationController implements ContainerInjectionInterface {

  /**
   * @var \Drupal\openapi\OpenApiGenerator\OpenApiRestGenerator
   */
  protected $restGenerator;

  /**
   * RestSpecificationController constructor.
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

  public function getEntitiesSpecification() {
    $spec = $this->restGenerator->getSpecification(['resource_types' => 'entities']);
    return new JsonResponse($spec);
  }

  public function getEntityBundleSpecification($entity_type_id, $bundle_name) {
    return new JsonResponse($this->restGenerator->getSpecification(['entity_type_id' => $entity_type_id, 'bundle_name' => $bundle_name]));
  }
}
