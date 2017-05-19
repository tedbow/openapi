<?php

namespace Drupal\openapi\OpenApiGenerator;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class OpenApiGeneratorBase
 * @package Drupal\openapi\OpenApiGenerator
 */
abstract class OpenApiGeneratorBase implements OpenApiGeneratorInterface {

  use StringTranslationTrait;
  /**
   * The Field Manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * The Schemata SchemaFactory.
   *
   * @var \Drupal\openapi_json_schema\SchemaFactory
   */
  protected $schemaFactory;

  /**
   * The serializer.
   *
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * Creates the 'info' portion of the API.
   *
   * @return array
   *   The info elements.
   */
  protected function getInfo() {
    $site_name = \Drupal::config('system.site')->get('name');
    return [
      'description' => '@todo update',
      'title' => $this->t('@site - API', ['@site' => $site_name]),
      'version' => 'No API version',
    ];
  }
}
