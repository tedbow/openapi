<?php

namespace Drupal\Tests\openapi\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;
use Drupal\rest\RestResourceConfigInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests requests schemata routes.
 *
 * @group OpenAPI
 */
class RequestTest extends BrowserTestBase {

  /**
   * Set to TRUE to run this test to generate expectation files.
   *
   * The test will be marked as a fail when generating test files.
   */
  const GENERATE_EXPECTATION_FILES = TRUE;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'user',
    'field',
    'filter',
    'text',
    'node',
    'taxonomy',
    'serialization',
    'hal',
    'schemata',
    'schemata_json_schema',
    'openapi',
    'rest',
    'jsonapi',
  ];

  /**
   * The REST resource config storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $resourceConfigStorage;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    if (!NodeType::load('camelids')) {
      // Create a "Camelids" node type.
      NodeType::create([
        'name' => 'Camelids',
        'type' => 'camelids',
      ])->save();
    }

    // Create a "Camelids" vocabulary.
    $vocabulary = Vocabulary::create([
      'name' => 'Camelids',
      'vid' => 'camelids',
    ]);
    $vocabulary->save();

    $entity_types = ['node', 'taxonomy_term'];
    foreach ($entity_types as $entity_type) {
      // Add access-protected field.
      FieldStorageConfig::create([
        'entity_type' => $entity_type,
        'field_name' => 'field_test_' . $entity_type,
        'type' => 'text',
      ])
        ->setCardinality(1)
        ->save();
      FieldConfig::create([
        'entity_type' => $entity_type,
        'field_name' => 'field_test_' . $entity_type,
        'bundle' => 'camelids',
      ])
        ->setLabel('Test field')
        ->setTranslatable(FALSE)
        ->save();
    }

    $enable_entity_types = [
      'node' => ['GET', 'POST', 'PATCH', 'DELETE'],
      'user' => ['GET'],
      'taxonomy_vocabulary' => ['GET'],
    ];
    foreach ($enable_entity_types as $entity_type_id => $methods) {
      foreach ($methods as $method) {
        $this->enableService("entity:$entity_type_id", $method);
      }
    }

    $this->resourceConfigStorage = $this->container->get('entity_type.manager')->getStorage('rest_resource_config');
    if (!$this->resourceConfigStorage) {
      $this->fail('wtf');
    }

    $this->container->get('router.builder')->rebuild();
    $this->drupalLogin($this->drupalCreateUser(['access openapi api docs']));
  }

  /**
   * Tests schemata requests.
   */
  public function testRequests() {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $this->container->get('entity_type.manager');

    foreach (['rest', 'jsonapi'] as $api_module) {

      $this->requestSchema($api_module);
      /*foreach ($entity_type_manager->getDefinitions() as $entity_type_id => $entity_type) {
        $this->requestSchema($format, $entity_type_id);
        if ($bundle_type = $entity_type->getBundleEntityType()) {
          $bundles = $entity_type_manager->getStorage($bundle_type)->loadMultiple();
          foreach ($bundles as $bundle) {
            $this->requestSchema($format, $entity_type_id, $bundle->id());
          }
        }
      }*/
    }

    if (static::GENERATE_EXPECTATION_FILES) {
      $this->fail('Expectation fails generated. Tests not run.');
    }

  }

  /**
   * Makes schema request and checks the response.
   *
   * @param string $format
   *   The described format.
   * @param string $entity_type_id
   *   Then entity type.
   * @param string|null $bundle_name
   *   The bundle name or NULL.
   */
  protected function requestSchema($api_module, array $options = []) {
    $get_options = [
      'query' => [
        '_format' => 'json',
        'options' => $options,
      ],
    ];
    $contents = $this->drupalGet("openapi/$api_module", $get_options);
    $this->assertSession()->statusCodeEquals(200);

    $file_name = __DIR__ . "/../../expectations/$api_module";
    if ($options) {
      $file_name .= "." . implode('.', $options);
    }
    $file_name .= '.json';
    //$this->assertFalse(empty($contents), "Content not empty for $format, $entity_type_id, $bundle_name");

    if (static::GENERATE_EXPECTATION_FILES) {
      $this->saveExpectationFile($file_name, $contents);
      // Response assertion is not performed when generating expectation
      // files.
      return;
    }

    // Compare decoded json to so that failure will indicate which element is
    // incorrect.
    $expected = json_decode(file_get_contents($file_name), TRUE);
    //$expected['id'] = str_replace('{base_url}', $this->baseUrl, $expected['id']);
    $decoded_response = json_decode($contents, TRUE);

    $this->assertEquals($expected, $decoded_response, "The response matches expected file: $file_name");
  }

  /**
   * Saves an expectation file.
   *
   * @param string $file_name
   *   The file name of the expectation file.
   * @param string $contents
   *   The JSON response contents.
   *
   * @see \Drupal\Tests\schemata\Functional\RequestTest::GENERATE_EXPECTATION_FILES
   */
  protected function saveExpectationFile($file_name, $contents) {
    $decoded_response = json_decode($contents, TRUE);
    // Replace the base url because will be different for different
    // environments.
    //$decoded_response['id'] = str_replace($this->baseUrl, '{base_url}', $decoded_response['id']);
    $re_encode = json_encode($decoded_response, JSON_PRETTY_PRINT);
    file_put_contents($file_name, $re_encode);
  }

  /**
   * Enables the REST service interface for a specific entity type.
   *
   * @param string|false $resource_type
   *   The resource type that should get REST API enabled or FALSE to disable all
   *   resource types.
   * @param string $method
   *   The HTTP method to enable, e.g. GET, POST etc.
   * @param string|array $format
   *   (Optional) The serialization format, e.g. hal_json, or a list of formats.
   * @param array $auth
   *   (Optional) The list of valid authentication methods.
   */
  protected function enableService($resource_type, $method = 'GET', $format = 'json', array $auth = ['basic_auth']) {
    if ($resource_type) {
      // Enable REST API for this entity type.
      $resource_config_id = str_replace(':', '.', $resource_type);
      // get entity by id
      /** @var \Drupal\rest\RestResourceConfigInterface $resource_config */
      $resource_config = $this->resourceConfigStorage->load($resource_config_id);
      if (!$resource_config) {
        $resource_config = $this->resourceConfigStorage->create([
          'id' => $resource_config_id,
          'granularity' => RestResourceConfigInterface::METHOD_GRANULARITY,
          'configuration' => [],
        ]);
      }
      $configuration = $resource_config->get('configuration');

      if (is_array($format)) {
        for ($i = 0; $i < count($format); $i++) {
          $configuration[$method]['supported_formats'][] = $format[$i];
        }
      }
      else {

        $configuration[$method]['supported_formats'][] = $format;
      }

      foreach ($auth as $auth_provider) {
        $configuration[$method]['supported_auth'][] = $auth_provider;
      }

      $resource_config->set('configuration', $configuration);
      $resource_config->save();
    }
    else {
      foreach ($this->resourceConfigStorage->loadMultiple() as $resource_config) {
        $resource_config->delete();
      }
    }
    //$this->rebuildCache();
  }

}
