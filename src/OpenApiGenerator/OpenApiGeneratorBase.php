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
   * @var \Drupal\schemata\SchemaFactory
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

  /**
   * {@inheritdoc}
   */
  public function getSpecification($options = []) {
    $spec = [
      'swagger' => "2.0",
      'schemes' => ['http'],
      'info' => $this->getInfo(),
      'host' => \Drupal::request()->getHost(),
      'basePath' => $this->getBasePath(),
      'securityDefinitions' => $this->getSecurityDefinitions(),
      'tags' => $this->getTags(),
      'definitions' => $this->getDefinitions(),
    ];
    return $spec;
  }

  /**
   * {@inheritdoc}
   */
  public function getBasePath() {
    return \Drupal::request()->getBasePath();
  }

  /**
   * Fix default field value as zero instead of FALSE.
   *
   * @param array $value
   *   JSON Schema field value.
   */
  protected function fixDefaultFalse(&$value) {
    if (isset($value['type']) && $value['type'] == 'array'
      && is_array($value['items']['properties'])
    ) {
      foreach ($value['items']['properties'] as $property_key => $property) {
        if ($property['type'] == 'boolean') {
          if (isset($value['default'][0][$property_key]) && empty($value['default'][0][$property_key])) {
            $value['default'][0][$property_key] = FALSE;
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTags() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getPaths() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSecurityDefinitions() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    return [];
  }

}
