<?php


namespace Drupal\openapi_docs\Controller;


use Drupal\Core\Url;
use Drupal\openapi\OpenApiGenerator\RestInspectionTrait;

class SwaggerUIRestController extends SwaggerUIController {

  use RestInspectionTrait;

  /**
   * The Swagger UI page.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle_name
   *   The bundle.
   *
   * @return array The Swagger UI render array.
   *   The Swagger UI render array.
   */
  public function bundleResource($entity_type = NULL, $bundle_name = NULL) {
    $json_url = Url::fromRoute(
      'openapi.rest.bundle',
      [
        'entity_type_id' => $entity_type,
        'bundle_name' => $bundle_name,
      ]
    );
    $build = $this->swaggerUI($json_url);
    return $build;
  }

  /**
   * List all REST Doc pages.
   */
  public function listResources() {
    $return['pages_heading'] = [
      '#type' => 'markup',
      '#markup' => '<h2>' . $this->t('Documentation Pages') . '</h2>',
    ];
    $return['other_resources'] = [
      '#type' => 'link',
      '#url' => Url::fromRoute('openapi.swaggerUI.rest.non_entity'),
      '#title' => $this->t('Non bundle resources'),
    ];

    foreach ($this->getRestEnabledEntityTypes() as $entity_type_id => $entity_type) {
      if ($bundle_type = $entity_type->getBundleEntityType()) {
        $bundle_storage = $this->entityTypeManager()
          ->getStorage($bundle_type);
        /** @var \Drupal\Core\Config\Entity\ConfigEntityBundleBase[] $bundles */
        $bundles = $bundle_storage->loadMultiple();
        $bundle_links = [];
        foreach ($bundles as $bundle_name => $bundle) {
          $bundle_links[$bundle_name] = [
            'title' => $bundle->label(),
            'url' => Url::fromRoute('openapi.swaggerUI.rest.bundle', [
              'entity_type' => $entity_type_id,
              'bundle_name' => $bundle_name,
            ]),
          ];
        }
        $return[$entity_type_id] = [
          '#theme' => 'links',
          '#links' => $bundle_links,
          '#heading' => [
            'text' => $this->t('@entity_type bundles', ['@entity_type' => $entity_type->getLabel()]),
            'level' => 'h3',
          ],
        ];
      }
    }
    return $return;
  }

  /**
   * Creates documentations page for non-entity resources.
   *
   * @return array
   *   Render array for documentations page.
   */
  public function nonEntityResources() {
    $json_url = Url::fromRoute(
      'openapi.non_entities'
    );
    $build = $this->swaggerUI($json_url);
    return $build;
  }

}
