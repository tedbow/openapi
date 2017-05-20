<?php


namespace Drupal\openapi\OpenApiGenerator;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Routing\Route;

class OpenApiJsonapiGenerator extends OpenApiGeneratorBase {

  const JSON_API_UUID_CONVERTER = 'paramconverter.jsonapi.entity_uuid';
  public function getBasePath() {
    return parent::getBasePath() . '/jsonapi';
  }

  public function getPaths() {
    $routes = $this->getJsonApiRoutes();
    $api_paths = [];
    foreach ($routes as $route_name => $route) {
      $entity_type_id = $route->getRequirement('_entity_type');
      $bundle_name = $route->getRequirement('_bundle');
      $api_path = [];
      $methods = $route->getMethods();
      foreach ($methods as $method) {
        $method = strtolower($method);
        $path_method = [];
        $path_method['parameters'] = $this->getMethodParameters($route, $method);
        $path_method['tags'] = ["$entity_type_id:$bundle_name"];
        $api_path[$method] = $path_method;

      }
      $api_paths[$route->getPath()] = $api_path;

    }
    return $api_paths;
  }

  /**
   * @return \Symfony\Component\Routing\Route[]
   */
  protected function getJsonApiRoutes() {
    $all_routes = $this->routingProvider->getAllRoutes();
    $jsonapi_reroutes = [];
    foreach ($all_routes as $route_name => $route) {
      if ($route->getOption('_is_jsonapi')) {
        $jsonapi_reroutes[$route_name] = $route;
      }
    }
    return $jsonapi_reroutes;
  }

  /**
   * Get the parameters array for a method on a route.
   *
   * @param \Symfony\Component\Routing\Route $route
   * @param string $method
   */
  protected function getMethodParameters(Route $route, $method) {
    $parameters = [];
    if ($route->hasOption('parameters')) {
      $entity_type_id = $route->getRequirement('_entity_type');
      $bundle_name = $route->getRequirement('_bundle');
      foreach ($route->getOption('parameters') as $parameter_name => $parameter_info) {
        $parameter = [
          'name' => $parameter_name,
          'required' => TRUE,
          'in' => 'path',
        ];
        if ($parameter_info['converter'] == static::JSON_API_UUID_CONVERTER) {
          $parameter['type'] = 'uuid';
          $parameter['description'] = $this->t('The uuid of the @entity @bundle',
            [
              '@entity' => $entity_type_id,
              '@bundle' => $bundle_name,
            ]
          );
        }
        $parameters[] = $parameter;
      }
    }
    else {
      if ($method == 'get') {
        // If no route parameters and GET then this is collection route.
        // @todo Add descriptions or link to documentation.
        $parameters[] = [
          'name' => 'filter',
          'in' => 'query',
          'type' => 'array',
          'required' => FALSE,
          'description' => '@todo Explain filtering: https://www.drupal.org/docs/8/modules/json-api/collections-filtering-sorting-and-paginating',
        ];
        $parameters[] = [
          'name' => 'sort',
          'in' => 'query',
          'type' => 'array',
          'required' => FALSE,
          'description' => '@todo Explain sorting: https://www.drupal.org/docs/8/modules/json-api/collections-filtering-sorting-and-paginating',
        ];
        $parameters[] = [
          'page' => 'sort',
          'in' => 'query',
          'type' => 'array',
          'required' => FALSE,
          'description' => '@todo Explain sorting: https://www.drupal.org/docs/8/modules/json-api/collections-filtering-sorting-and-paginating',
        ];
      }
    }
    return $parameters;
  }

  public function getDefinitions() {
    $definitions = [];
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type) {
      if ($bundle_type = $entity_type->getBundleEntityType()) {
        $bundle_storage = $this->entityTypeManager->getStorage($bundle_type);
        $bundles = $bundle_storage->loadMultiple();
        foreach ($bundles as $bundle_name => $bundle) {
          $definitions["{$entity_type->id()}:$bundle_name"] = $this->getJsonSchema('api_json', $entity_type->id(), $bundle_name);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTags() {
    $tags = [];
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type) {
      if ($bundle_type = $entity_type->getBundleEntityType()) {
        $bundle_storage = $this->entityTypeManager->getStorage($bundle_type);
        $bundles = $bundle_storage->loadMultiple();
        foreach ($bundles as $bundle_name => $bundle) {
          $tags[] = [
            'name' => $this->getBundleTag($entity_type, $bundle),
            'description' => $this->t('Entity type: @entity_type, Bundle: @bundle',
              [
                '@entity_type' => $entity_type->id(),
                '@bundle' => $bundle->id(),
              ]
            ),
          ];
        }
      }
      else {
        $tags[] = [
          'name' => $this->getBundleTag($entity_type, $entity_type),
          'description' => $this->t('Entity type: @entity_type, Bundle: @bundle',
            [
              '@entity_type' => $entity_type->id(),
              '@bundle' => $entity_type->id(),
            ]
          ),
        ];
      }
    }
    return $tags;
  }

  /**
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   * @param \Drupal\Core\Entity\EntityTypeInterface $bundle
   * @return string
   */
  protected function getBundleTag(EntityTypeInterface $entity_type, $bundle) {
    return $entity_type->id() . ':' . $bundle->id();
  }

}
