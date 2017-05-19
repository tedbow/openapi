<?php

namespace Drupal\openapi\OpenApiGenerator;

use Drupal\rest\Plugin\rest\resource\EntityResource;
use Drupal\rest\RestResourceConfigInterface;

/**
 * Common functions for inspecting REST resources.
 */
trait RestInspectionTrait {

  /**
   * Gets entity types that are enabled for rest.
   *
   * @param string $entity_type_id
   *   The entity type id.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
   *   Entity types that are enabled.
   */
  protected function getRestEnabledEntityTypes($entity_type_id = NULL) {
    $entity_types = [];
    $resource_configs = $this->getResourceConfigs();

    foreach ($resource_configs as $id => $resource_config) {
      if ($entity_type = $this->getEntityType($resource_config)) {
        if (!$entity_type_id || $entity_type->id() == $entity_type_id) {
          $entity_types[$entity_type->id()] = $entity_type;
        }
      }
    }
    return $entity_types;
  }

  /**
   * Gets the REST config resources.
   *
   * @param string $entity_type
   *   The entity type to filter by if any.
   *
   * @return \Drupal\rest\RestResourceConfigInterface[]
   *   The REST config resources.
   */
  protected function getResourceConfigs($entity_type = NULL) {
    if ($entity_type) {
      $resource_configs[] = $this->entityTypeManager->getStorage('rest_resource_config')->load("entity.$entity_type");
    }
    else {
      $resource_configs = $this->entityTypeManager->getStorage('rest_resource_config')->loadMultiple();
    }
    return $resource_configs;
  }

  /**
   * Gets the entity type if any for REST resource.
   *
   * @param \Drupal\rest\RestResourceConfigInterface $resource_config
   *   The REST config resource.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   *   The Entity Type or null.
   */
  protected function getEntityType(RestResourceConfigInterface $resource_config) {
    if ($this->isEntityResource($resource_config)) {
      $resource_plugin = $resource_config->getResourcePlugin();
      return $this->entityTypeManager->getDefinition($resource_plugin->getPluginDefinition()['entity_type']);
    }
    return NULL;
  }

  /**
   * Determines if an REST resource is for an entity.
   *
   * @param \Drupal\rest\RestResourceConfigInterface $resource_config
   *   The REST config resource.
   *
   * @return bool
   *   True if the resource represents a Drupal entity.
   */
  protected function isEntityResource(RestResourceConfigInterface $resource_config) {
    $resource_plugin = $resource_config->getResourcePlugin();
    return $resource_plugin instanceof EntityResource;
  }

}
