<?php

namespace Drupal\Tests\openapi\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\openapi_test\Entity\OpenApiTestEntityType;
use Drupal\rest\Entity\RestResourceConfig;
use Drupal\rest\RestResourceConfigInterface;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests requests OpenAPI routes.
 *
 * @group OpenAPI
 */
class RequestTest extends BrowserTestBase {

  /**
   * Set to TRUE to run this test to generate expectation files.
   *
   * The test will be marked as a fail when generating test files.
   */
  const GENERATE_EXPECTATION_FILES = FALSE;

  /**
   * List of required array keys for response schema.
   */
  const EXPECTED_STRUCTURE = [
    'swagger' => 'swagger',
    'schemes' => 'schema',
    'info' => [
      'description' => 'description',
      'version' => 'version',
      'title' => 'title',
    ],
    'host' => 'host',
    'basePath' => 'basePath',
    'securityDefinitions' => 'securityDefinitions',
    'tags' => 'tags',
    'definitions' => 'definitions',
    'paths' => 'paths',
  ];

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'user',
    'field',
    'filter',
    'text',
    'taxonomy',
    'serialization',
    'hal',
    'schemata',
    'schemata_json_schema',
    'openapi',
    'rest',
    'openapi_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    /*
     * @TODO: The below configuration/setup should be shipped as part of the
     * test resources sub module.
     */
    if (!OpenApiTestEntityType::load('camelids')) {
      // Create a "Camelids" bundle.
      OpenApiTestEntityType::create([
        'label' => 'Camelids',
        'id' => 'camelids',
      ])->save();
    }

    // Create a "Camelids" vocabulary.
    $vocabulary = Vocabulary::create([
      'name' => 'Camelids',
      'vid' => 'camelids',
    ]);
    $vocabulary->save();

    $entity_types = ['openapi_test_entity', 'taxonomy_term'];
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
      'openapi_test_entity' => ['GET', 'POST', 'PATCH', 'DELETE'],
      'openapi_test_entity_type' => ['GET'],
      'user' => ['GET'],
      'taxonomy_term' => ['GET', 'POST', 'PATCH', 'DELETE'],
      'taxonomy_vocabulary' => ['GET'],
    ];
    foreach ($enable_entity_types as $entity_type_id => $methods) {
      foreach ($methods as $method) {
        $this->enableRestService("entity:$entity_type_id", $method);
      }
    }
    $this->container->get('router.builder')->rebuild();
    $this->drupalLogin($this->drupalCreateUser([
      'access openapi api docs',
      'access content',
    ]));

    // @todo Add JSON API to $modules
    //   Currently this will not work because the new bundles are not picked up
    //   in \Drupal\jsonapi\Routing\Routes::routes().
    $this->container->get('module_installer')->install(['jsonapi']);
  }

  /**
   * Tests OpenAPI requests.
   */
  public function testRequests() {
    $option_sets = [
      'openapi_test_entity' => [
        'entity_type_id' => 'openapi_test_entity',
      ],
      'openapi_test_entity_type' => [
        'entity_type_id' => 'openapi_test_entity_type',
      ],
      'openapi_test_entity_camelids' => [
        'entity_type_id' => 'openapi_test_entity',
        'bundle_name' => 'camelids',
      ],
      'taxonomy_term' => [
        'entity_type_id' => 'taxonomy_term',
      ],
      'taxonomy_term_camelids' => [
        'entity_type_id' => 'taxonomy_term',
        'bundle_name' => 'camelids',
      ],
      'user' => [
        'entity_type_id' => 'user',
      ],
    ];
    foreach (['rest', 'jsonapi'] as $api_module) {
      // Make request with no options to produce full result.
      $this->requestOpenApiJson($api_module);
      // Test the output using various options.
      foreach ($option_sets as $options) {
        $this->requestOpenApiJson($api_module, $options);
      }
    }

    if (static::GENERATE_EXPECTATION_FILES) {
      $this->fail('Expectation files generated. Change \Drupal\Tests\openapi\Functional\RequestTest::GENERATE_EXPECTATION_FILES to FALSE to run tests.');
    }

  }

  /**
   * Makes OpenAPI request and checks the response.
   *
   * @param string $api_module
   *   The API module being tested. Either 'rest' or 'jsonapi'.
   * @param array $options
   *   The query options for generating the OpenAPI output.
   */
  protected function requestOpenApiJson($api_module, array $options = []) {
    $get_options = [
      'query' => [
        '_format' => 'json',
        'options' => $options,
      ],
    ];
    $response = $this->drupalGet("openapi/$api_module", $get_options);
    $decoded_response = json_decode($response, TRUE);
    $this->assertSession()->statusCodeEquals(200);

    // Test the the first tier schema has the expected keys.
    $structure_keys = array_keys(static::EXPECTED_STRUCTURE);
    $response_keys = array_keys($decoded_response);
    $missing = array_diff($structure_keys, $response_keys);
    $this->assertTrue(empty($missing), 'Schema missing expected key(s): ' . implode(', ', $missing));

    // Test that the required info block keys exist in the response.
    $structure_info_keys = array_keys(static::EXPECTED_STRUCTURE['info']);
    $response_keys = array_keys($decoded_response['info']);
    $missing_info = array_diff($structure_info_keys, $response_keys);
    $this->assertTrue(empty($missing_info), 'Schema info missing expected key(s): ' . implode(', ', $missing_info));

    // Test that schemes is not empty.
    $this->assertTrue(!empty($decoded_response['schemes']), 'Schema for ' . $api_module . ' should define at least one scheme.');

    // Test basePath and host.
    $host = parse_url($this->baseUrl, PHP_URL_HOST);
    $this->assertEquals($host, $decoded_response['host'], 'Schema has invalid host.');
    $basePath = $this->getBasePath();
    $response_basePath = $decoded_response['basePath'];
    $this->assertEquals($basePath, substr($response_basePath, 0, strlen($basePath)), 'Schema has invalid basePath.');
    $routeBase = ($api_module === 'jsonapi') ? 'jsonapi' : '';
    $response_routeBase = substr($response_basePath, strlen($basePath));
    // Verify that with the subdirectory removed, that the basePath is correct.
    $this->assertEquals($routeBase, ltrim($response_routeBase, '/'), 'Route base path is invalid.');

    /*
     * Tags for rest schema define 'x-entity-type' to reference the entity type
     * associated with the entity. This value should exist in the definitions.
     *
     * @TODO: Add x-entity-type to JsonAPI schema.
     * @TODO: Convert the property to x-definition and to be a reference.
     *
     * @TODO: Provide all entity types as definitions.
     * NOTE: Currently not all entity types are provided as definitions. As a
     * result, the below test is subject to failure, and has been disabled.
     */
    $tags = $decoded_response['tags'];
    if ($api_module === "rest" && FALSE) {
      $definitions = $decoded_response['definitions'];
      foreach ($tags as $tag) {
        if (isset($tag['x-entity-type'])) {
          $type_id = $tag['x-entity-type'];
          $this->assertTrue(isset($definitions[$type_id]), 'The \'x-entity-type\' ' . $type_id . ' is invalid for ' . $tag['name'] . '.');
        }
      }
    }

    // @TODO: Test paths for valid tags, schema, security, and definitions.
    $paths = &$decoded_response['paths'];
    foreach ($paths as $path => &$methods) {
      foreach ($methods as $method => &$method_schema) {
        // Ensure all tags are defined.
        $tag_names = array_column($tags, 'name');
        $missing_tags = array_diff($method_schema['tags'], $tag_names);
        $this->assertTrue(empty($missing_tags), 'Method ' . $method . ' for ' . $path . ' has invalid tag(s): ' . implode(', ', $missing_tags));

        // The schemes index is currently not present for jsonapi.
        // @TODO: Define allowed schemes for jsonapi.
        if (isset($method_schema['schemes'])) {
          // Ensure all request schemes are defined.
          $missing_schemas = array_diff($method_schema['schemes'], $decoded_response['schemes']);
          $this->assertTrue(empty($missing_schemas), 'Method ' . $method . ' for ' . $path . ' has invalid scheme(s): ' . implode(', ', $missing_schemas));
        }

        // The security index is currently not present for jsonapi.
        // @TODO: Define allowed security methods for jsonapi.
        if (isset($method_schema['security'])) {
          foreach ($method_schema['security'] as $security_definitions) {
            $security_types = array_keys($security_definitions);
            $response_security_types = array_keys($decoded_response['securityDefinitions']);
            $missing_security_types = array_diff($security_types, $response_security_types);
            $this->assertTrue(empty($missing_security_types), 'Method ' . $method . ' for ' . $path . ' has invalid security type(s): ' . implode(', ', $missing_security_types));
          };
        }

        // Remove all tested properties from method schema.
        unset($method_schema['tags']);
        unset($method_schema['schemes']);
        unset($method_schema['security']);
      }
    }

    // Strip response down to only untested properties.
    $root_keys = ['definitions', 'paths'];
    foreach (array_diff(array_keys($decoded_response), $root_keys) as $remove) {
      unset($decoded_response[$remove]);
    }

    // Build file name.
    $file_name = __DIR__ . "/../../expectations/$api_module";
    if ($options) {
      $file_name .= "." . implode('.', $options);
    }
    $file_name .= '.json';
    if (static::GENERATE_EXPECTATION_FILES) {
      $this->saveExpectationFile($file_name, $decoded_response);
      // Response assertion is not performed when generating expectation
      // files.
      return;
    }
    // Load expected value and test remaining schema.
    $expected = json_decode(file_get_contents($file_name), TRUE);

    $this->nestedKsort($expected);
    $this->nestedKsort($decoded_response);
    $this->assertEquals($expected, $decoded_response, "The does not response matches expected file: $file_name");
  }

  /**
   * Saves an expectation file.
   *
   * @param string $file_name
   *   The file name of the expectation file.
   * @param array $decoded_response
   *   The decoded JSON response.
   *
   * @see \Drupal\Tests\openapi\Functional\RequestTest::GENERATE_EXPECTATION_FILES
   */
  protected function saveExpectationFile($file_name, array $decoded_response) {
    // Remove the base path from the start of the string, if present.
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
  protected function enableRestService($resource_type, $method = 'GET', $format = 'json', array $auth = ['basic_auth']) {
    if ($resource_type) {
      // Enable REST API for this entity type.
      $resource_config_id = str_replace(':', '.', $resource_type);
      // get entity by id
      /** @var \Drupal\rest\RestResourceConfigInterface $resource_config */
      $resource_config = RestResourceConfig::load($resource_config_id);
      if (!$resource_config) {
        $resource_config = RestResourceConfig::create([
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
      foreach (RestResourceConfig::loadMultiple() as $resource_config) {
        $resource_config->delete();
      }
    }
  }

  /**
   * Gets the base path to be used in OpenAPI.
   *
   * @return string
   *   The base path.
   */
  protected function getBasePath() {
    $path = rtrim(parse_url($this->baseUrl, PHP_URL_PATH), '/');

    // OpenAPI spec states that the base path must start with a '/'.
    if (strlen($path) == 0) {
      // For a zero length string, set it to minimal value.
      $path = "/";
    }
    elseif (substr($path, 0, 1) !== '/') {
      // Prepend a slash to any other string that don't have one.
      $path = '/' . $path;
    }
    return $path;
  }

  /**
   * Sorts a nested array with ksort().
   *
   * @param array $array
   *   The nested array to sort.
   */
  protected function nestedKsort(array &$array) {
    if ($this->isAssocArray($array)) {
      ksort($array);
    }
    else {
      usort($array, function ($a, $b) {
        if (isset($a['name']) && isset($b['name'])) {
          return strcmp($a['name'], $b['name']);
        }
        return -1;
      });
    }

    foreach ($array as &$item) {
      if (is_array($item)) {
        $this->nestedKsort($item);
      }
    }
  }

  protected function isAssocArray(array $arr) {
    if (array() === $arr) {
      return FALSE;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
  }

}
