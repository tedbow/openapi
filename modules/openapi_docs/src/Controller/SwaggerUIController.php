<?php

namespace Drupal\openapi_docs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for the Swagger UI page callbacks.
 */
class SwaggerUIController extends ControllerBase {

  /**
   * Constructs a new SwaggerController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }



  /**
   * Creates render array for documentation page for a given resource url.
   *
   * @param \Drupal\Core\Url $json_url
   *   The resource file needed to create the documentation page.
   *
   * @return array
   *   The render array.
   */
  protected function swaggerUI(Url $json_url) {
    $json_url->setOption('query', ['_format' => 'json']);
    $build = [
      '#theme' => 'swagger_ui',
      '#attached' => [
        'library' => [
          'openapi_docs/swagger_ui_integration',
          'openapi_docs/swagger_ui',
        ],
        'drupalSettings' => [
          'openapi' => [
            'swagger_json_url' => $json_url->toString(),
          ],
        ],
      ],
    ];
    return $build;
  }

  /**
   * Creates documentations page for non-entity resources.
   *
   * @return array
   *   Render array for documentations page.
   */
  public function openApiResources() {
    $json_url = Url::fromRoute(
      'openapi.jsonapi'
    );
    $build = $this->swaggerUI($json_url);
    return $build;
  }

}
