<?php

namespace Drupal\openapi\Tests;

use Drupal\Core\Url;

/**
 * Test resource discovery using Open API.
 *
 * @group openapi
 */
class OpenApiRestResourceTest extends OpenApiTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system',
    'node',
    'taxonomy',
    'serialization',
    'rest',
    'openapi',
    'schemata',
  ];

  /**
   * Tests discovery of resources using Open API specification.
   */
  public function testResourceDiscovery() {
    $user = $this->drupalCreateUser(['access openapi api docs']);
    $this->drupalLogin($user);;

    $url = Url::fromRoute('openapi.rest')->setRouteParameter('_format', 'json');
    $this->assertHttpResponse($url, 'GET', 200, $this->getExpectedEntities(), 'Resource list correct');

    $entity_type_bundles = [
      'node' => ['article'],
    ];
    foreach ($entity_type_bundles as $entity_type => $bundles) {
      foreach ($bundles as $bundle) {
        $url = Url::fromRoute('openapi.rest',
          [],
          [
            'options' => [
              'entity_type_id' => $entity_type,
              'bundle_name' => $bundle,
            ],
          ]
        )->setRouteParameter('_format', 'json');
        $this->assertHttpResponse($url, 'GET', 200, $this->getExpectedBundle($entity_type, $bundle), 'Bundle Resource list correct');
      }
    }
  }

  /**
   * Gets the expected entity resources.
   *
   * @return array
   *   The expected resources for all entities.
   */
  protected function getExpectedEntities() {
    return [
      'swagger' => '2.0',
      'schemes' =>
        [
          0 => 'http',
        ],
      'info' =>
        [
          'description' => '@todo update',
          'title' => 'Drupal - API',
          'version' => 'No API version',
        ],
      'paths' =>
        [
          '/node/{node}' =>
            [
              'get' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'node',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The nid,id, of the node.',
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      200 =>
                        [
                          'description' => 'successful operation',
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node',
                            ],
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Get a Content',
                  'operationId' => 'entity:node:GET',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [],
                ],
              'patch' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'node',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The nid,id, of the node.',
                        ],
                      2 =>
                        [
                          'name' => 'body',
                          'in' => 'body',
                          'description' => 'The Content object',
                          'required' => TRUE,
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node',
                            ],
                        ],
                      3 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Patch a Content',
                  'operationId' => 'entity:node:PATCH',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                      0 =>
                        [
                          'csrf_token' =>[],
                        ],
                    ],
                ],
              'delete' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'node',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The nid,id, of the node.',
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      201 =>
                        [
                          'description' => 'Entity deleted',
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Delete a Content',
                  'operationId' => 'entity:node:DELETE',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                      0 =>
                        [
                          'csrf_token' => [],
                        ],
                    ],
                ],
            ],
          '/entity/node' =>
            [
              'post' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'body',
                          'in' => 'body',
                          'description' => 'The Content object',
                          'required' => TRUE,
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node',
                            ],
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      201 =>
                        [
                          'description' => 'Entity created',
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node',
                            ],
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Post a Content',
                  'operationId' => 'entity:node:POST',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                      0 =>
                        [
                          'csrf_token' =>
                            [
                            ],
                        ],
                    ],
                ],
            ],
          '/entity/taxonomy_vocabulary/{taxonomy_vocabulary}' =>
            [
              'get' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'taxonomy_vocabulary',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The vid,id, of the taxonomy_vocabulary.',
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      200 =>
                        [
                          'description' => 'successful operation',
                          'schema' =>
                            [
                              '$ref' => '#/definitions/taxonomy_vocabulary',
                            ],
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'taxonomy_vocabulary',
                    ],
                  'summary' => 'Get a Taxonomy vocabulary',
                  'operationId' => 'entity:taxonomy_vocabulary:GET',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                    ],
                ],
            ],
          '/user/{user}' =>
            [
              'get' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'user',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The uid,id, of the user.',
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      200 =>
                        [
                          'description' => 'successful operation',
                          'schema' =>
                            [
                              '$ref' => '#/definitions/user',
                            ],
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'user',
                    ],
                  'summary' => 'Get a User',
                  'operationId' => 'entity:user:GET',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                    ],
                ],
            ],
        ],
      'host' => \Drupal::request()->getHost(),
      'basePath' => \Drupal::request()->getBasePath(),
      'securityDefinitions' =>
        [
          'csrf_token' =>
            [
              'type' => 'apiKey',
              'name' => 'X-CSRF-Token',
              'in' => 'header',
            ],
          'basic_auth' =>
            [
              'type' => 'basic',
            ],
        ],
      'tags' =>
        [
          0 =>
            [
              'name' => 'node',
              'description' => 'Entity type: Content',
              'x-entity-type' => 'node',
            ],
          1 =>
            [
              'name' => 'taxonomy_vocabulary',
              'description' => 'Entity type: Taxonomy vocabulary',
              'x-entity-type' => 'taxonomy_vocabulary',
            ],
          2 =>
            [
              'name' => 'user',
              'description' => 'Entity type: User',
              'x-entity-type' => 'user',
            ],
        ],
      'definitions' =>
        [
          'node' =>
            [
              'type' => 'object',
              'title' => 'node Schema',
              'description' => 'Describes the payload for \'node\' entities.',
              'properties' =>
                [
                  'nid' =>
                    [
                      'type' => 'array',
                      'title' => 'ID',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'Integer value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'uuid' =>
                    [
                      'type' => 'array',
                      'title' => 'UUID',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                  'format' => 'uuid',
                                  'maxLength' => 128,
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'vid' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision ID',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'Integer value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Language',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Language code',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'type' =>
                    [
                      'type' => 'array',
                      'title' => 'Content type',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'target_id' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Content type ID',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'target_id',
                            ],
                        ],
                      'minItems' => 1,
                      'maxItems' => 1,
                    ],
                  'title' =>
                    [
                      'type' => 'array',
                      'title' => 'Title',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                  'maxLength' => 255,
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'minItems' => 1,
                      'maxItems' => 1,
                    ],
                  'uid' =>
                    [
                      'type' => 'array',
                      'title' => 'Authored by',
                      'description' => 'The username of the content author.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'target_id' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'User ID',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'target_id',
                            ],
                          'title' => 'User',
                          'description' => 'The referenced entity',
                        ],
                      'maxItems' => 1,
                    ],
                  'status' =>
                    [
                      'type' => 'array',
                      'title' => 'Publishing status',
                      'description' => 'A boolean indicating whether the node is published.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => TRUE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'created' =>
                    [
                      'type' => 'array',
                      'title' => 'Authored on',
                      'description' => 'The time that the node was created.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'changed' =>
                    [
                      'type' => 'array',
                      'title' => 'Changed',
                      'description' => 'The time that the node was last edited.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'promote' =>
                    [
                      'type' => 'array',
                      'title' => 'Promoted to front page',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => TRUE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'sticky' =>
                    [
                      'type' => 'array',
                      'title' => 'Sticky at top of lists',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => FALSE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_timestamp' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision timestamp',
                      'description' => 'The time that the current revision was created.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_uid' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision user ID',
                      'description' => 'The user ID of the author of the current revision.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'target_id' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'User ID',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'target_id',
                            ],
                          'title' => 'User',
                          'description' => 'The referenced entity',
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_log' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision log message',
                      'description' => 'Briefly describe the changes you have made.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => '',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_translation_affected' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision translation affected',
                      'description' => 'Indicates if the last edit of a translation belongs to current revision.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'default_langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Default translation',
                      'description' => 'A flag indicating whether this is the default translation.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => TRUE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                ],
              'required' =>
                [
                  0 => 'type',
                  1 => 'title',
                ],
              'discriminator' => 'type',
            ],
          'node:page' =>
            [
              'allOf' =>
                [
                  0 =>
                    [
                      '$ref' => '#/definitions/node',
                    ],
                  1 =>
                    [
                      'type' => 'object',
                      'title' => 'node:page Schema',
                      'description' => 'Describes the payload for \'node\' entities of the \'page\' bundle.',
                      'properties' =>
                        [
                          'body' =>
                            [
                              'type' => 'array',
                              'title' => 'Body',
                              'items' =>
                                [
                                  'type' => 'object',
                                  'properties' =>
                                    [
                                      'value' =>
                                        [
                                          'type' => 'string',
                                          'title' => 'Text',
                                        ],
                                      'format' =>
                                        [
                                          'type' => 'string',
                                          'title' => 'Text format',
                                        ],
                                      'summary' =>
                                        [
                                          'type' => 'string',
                                          'title' => 'Summary',
                                        ],
                                    ],
                                  'required' =>
                                    [
                                      0 => 'value',
                                    ],
                                ],
                              'maxItems' => 1,
                            ],
                        ],
                      'required' =>
                        [
                          0 => 'type',
                          1 => 'title',
                        ],
                    ],
                ],
            ],
          'node:resttest' =>
            [
              'allOf' =>
                [
                  0 =>
                    [
                      '$ref' => '#/definitions/node',
                    ],
                  1 =>
                    [
                      'type' => 'object',
                      'title' => 'node:resttest Schema',
                      'description' => 'Describes the payload for \'node\' entities of the \'resttest\' bundle.',
                      'properties' =>
                        [
                          'body' =>
                            [
                              'type' => 'array',
                              'title' => 'Body',
                              'items' =>
                                [
                                  'type' => 'object',
                                  'properties' =>
                                    [
                                      'value' =>
                                        [
                                          'type' => 'string',
                                          'title' => 'Text',
                                        ],
                                      'format' =>
                                        [
                                          'type' => 'string',
                                          'title' => 'Text format',
                                        ],
                                      'summary' =>
                                        [
                                          'type' => 'string',
                                          'title' => 'Summary',
                                        ],
                                    ],
                                  'required' =>
                                    [
                                      0 => 'value',
                                    ],
                                ],
                              'maxItems' => 1,
                            ],
                        ],
                      'required' =>
                        [
                          0 => 'type',
                          1 => 'title',
                        ],
                    ],
                ],
            ],
          'taxonomy_vocabulary' =>
            [
              'type' => 'object',
              'title' => 'taxonomy_vocabulary Schema',
              'description' => 'Describes the payload for taxonomy_vocabulary entities.',
            ],
          'user' =>
            [
              'type' => 'object',
              'title' => 'user Schema',
              'description' => 'Describes the payload for \'user\' entities.',
              'properties' =>
                [
                  'uid' =>
                    [
                      'type' => 'array',
                      'title' => 'User ID',
                      'description' => 'The user ID.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'Integer value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'uuid' =>
                    [
                      'type' => 'array',
                      'title' => 'UUID',
                      'description' => 'The user UUID.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                  'format' => 'uuid',
                                  'maxLength' => 128,
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Language code',
                      'description' => 'The user language code.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Language code',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'preferred_langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Preferred language code',
                      'description' => 'The user\'s preferred language code for receiving emails and viewing the site.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Language code',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'preferred_admin_langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Preferred admin language code',
                      'description' => 'The user\'s preferred language code for viewing administration pages.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Language code',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => '',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'name' =>
                    [
                      'type' => 'array',
                      'title' => 'Name',
                      'description' => 'The name of this user.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                  'maxLength' => 255,
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'minItems' => 1,
                      'maxItems' => 1,
                    ],
                  'pass' =>
                    [
                      'type' => 'array',
                      'title' => 'Password',
                      'description' => 'The password of this user (hashed).',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'The hashed password',
                                  'maxLength' => 255,
                                ],
                              'existing' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Existing password',
                                ],
                              'pre_hashed' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Determines if a password needs hashing',
                                ],
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'mail' =>
                    [
                      'type' => 'array',
                      'title' => 'Email',
                      'description' => 'The email of this user.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Email',
                                  'format' => 'email',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => '',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'timezone' =>
                    [
                      'type' => 'array',
                      'title' => 'Timezone',
                      'description' => 'The timezone of this user.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                  'maxLength' => 32,
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'status' =>
                    [
                      'type' => 'array',
                      'title' => 'User status',
                      'description' => 'Whether the user is active or blocked.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => FALSE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'created' =>
                    [
                      'type' => 'array',
                      'title' => 'Created',
                      'description' => 'The time that the user was created.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'changed' =>
                    [
                      'type' => 'array',
                      'title' => 'Changed',
                      'description' => 'The time that the user was last edited.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'access' =>
                    [
                      'type' => 'array',
                      'title' => 'Last access',
                      'description' => 'The time that the user last accessed the site.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => 0,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'login' =>
                    [
                      'type' => 'array',
                      'title' => 'Last login',
                      'description' => 'The time that the user last logged in.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => 0,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'init' =>
                    [
                      'type' => 'array',
                      'title' => 'Initial email',
                      'description' => 'The email address used for initial account creation.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Email',
                                  'format' => 'email',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => '',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'roles' =>
                    [
                      'type' => 'array',
                      'title' => 'Roles',
                      'description' => 'The roles the user has.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'target_id' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Role ID',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'target_id',
                            ],
                        ],
                    ],
                  'default_langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Default translation',
                      'description' => 'A flag indicating whether this is the default translation.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => TRUE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                ],
              'required' =>
                [
                  0 => 'name',
                ],
            ],
        ],
    ];
  }

  /**
   * Gets expected resource for a specific bundle.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   *
   * @return array
   *   The resource list for the bundle.
   */
  protected function getExpectedBundle($entity_type, $bundle) {
    $bundles['node']['article'] = [
      'swagger' => '2.0',
      'schemes' =>
        [
          0 => 'http',
        ],
      'info' =>
        [
          'description' => '@todo update',
          'title' => 'Drupal - API',
          'version' => 'No API version',
        ],
      'paths' =>
        [
          '/node/{node}' =>
            [
              'get' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'node',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The nid,id, of the node.',
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      200 =>
                        [
                          'description' => 'successful operation',
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node:article',
                            ],
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Get a Content',
                  'operationId' => 'entity:node:GET',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                    ],
                ],
              'patch' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'node',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The nid,id, of the node.',
                        ],
                      2 =>
                        [
                          'name' => 'body',
                          'in' => 'body',
                          'description' => 'The Content object',
                          'required' => TRUE,
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node:article',
                            ],
                        ],
                      3 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Patch a Content',
                  'operationId' => 'entity:node:PATCH',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                      0 =>
                        [
                          'csrf_token' =>
                            [
                            ],
                        ],
                    ],
                ],
              'delete' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'node',
                          'in' => 'path',
                          'required' => TRUE,
                          'type' => 'string',
                          'description' => 'The nid,id, of the node.',
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      201 =>
                        [
                          'description' => 'Entity deleted',
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Delete a Content',
                  'operationId' => 'entity:node:DELETE',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                      0 =>
                        [
                          'csrf_token' =>
                            [
                            ],
                        ],
                    ],
                ],
            ],
          '/entity/node' =>
            [
              'post' =>
                [
                  'parameters' =>
                    [
                      0 =>
                        [
                          'name' => '_format',
                          'in' => 'query',
                          'type' => 'string',
                          'enum' =>
                            [
                              0 => 'json',
                            ],
                          'required' => TRUE,
                          'description' => 'Request format',
                          'default' => 'json',
                        ],
                      1 =>
                        [
                          'name' => 'body',
                          'in' => 'body',
                          'description' => 'The Content object',
                          'required' => TRUE,
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node:article',
                            ],
                        ],
                      2 =>
                        [
                          'name' => 'X-CSRF-Token',
                          'type' => 'string',
                          'in' => 'header',
                          'required' => TRUE,
                        ],
                    ],
                  'responses' =>
                    [
                      201 =>
                        [
                          'description' => 'Entity created',
                          'schema' =>
                            [
                              '$ref' => '#/definitions/node:article',
                            ],
                        ],
                      400 =>
                        [
                          'description' => 'Bad request',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'error' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Bad data',
                                    ],
                                ],
                            ],
                        ],
                      500 =>
                        [
                          'description' => 'Internal server error.',
                          'schema' =>
                            [
                              'type' => 'object',
                              'properties' =>
                                [
                                  'message' =>
                                    [
                                      'type' => 'string',
                                      'example' => 'Internal server error.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                  'tags' =>
                    [
                      0 => 'node',
                    ],
                  'summary' => 'Post a Content',
                  'operationId' => 'entity:node:POST',
                  'schemes' =>
                    [
                      0 => 'http',
                    ],
                  'security' =>
                    [
                      0 =>
                        [
                          'csrf_token' =>
                            [
                            ],
                        ],
                    ],
                ],
            ],
        ],
      'host' => \Drupal::request()->getHost(),
      'basePath' => \Drupal::request()->getBasePath(),
      'securityDefinitions' =>
        [
          'csrf_token' =>
            [
              'type' => 'apiKey',
              'name' => 'X-CSRF-Token',
              'in' => 'header',
            ],
          'basic_auth' =>
            [
              'type' => 'basic',
            ],
        ],
      'tags' =>
        [
          0 =>
            [
              'name' => 'node',
              'description' => 'Entity type: Content',
              'x-entity-type' => 'node',
            ],
          1 =>
            [
              'name' => 'taxonomy_vocabulary',
              'description' => 'Entity type: Taxonomy vocabulary',
              'x-entity-type' => 'taxonomy_vocabulary',
            ],
          2 =>
            [
              'name' => 'user',
              'description' => 'Entity type: User',
              'x-entity-type' => 'user',
            ],
        ],
      'definitions' =>
        [
          'node' =>
            [
              'type' => 'object',
              'title' => 'node Schema',
              'description' => 'Describes the payload for \'node\' entities.',
              'properties' =>
                [
                  'nid' =>
                    [
                      'type' => 'array',
                      'title' => 'ID',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'Integer value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'uuid' =>
                    [
                      'type' => 'array',
                      'title' => 'UUID',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                  'format' => 'uuid',
                                  'maxLength' => 128,
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'vid' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision ID',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'Integer value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Language',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Language code',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'type' =>
                    [
                      'type' => 'array',
                      'title' => 'Content type',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'target_id' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Content type ID',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'target_id',
                            ],
                        ],
                      'minItems' => 1,
                      'maxItems' => 1,
                    ],
                  'title' =>
                    [
                      'type' => 'array',
                      'title' => 'Title',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                  'maxLength' => 255,
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'minItems' => 1,
                      'maxItems' => 1,
                    ],
                  'uid' =>
                    [
                      'type' => 'array',
                      'title' => 'Authored by',
                      'description' => 'The username of the content author.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'target_id' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'User ID',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'target_id',
                            ],
                          'title' => 'User',
                          'description' => 'The referenced entity',
                        ],
                      'maxItems' => 1,
                    ],
                  'status' =>
                    [
                      'type' => 'array',
                      'title' => 'Publishing status',
                      'description' => 'A boolean indicating whether the node is published.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => TRUE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'created' =>
                    [
                      'type' => 'array',
                      'title' => 'Authored on',
                      'description' => 'The time that the node was created.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'changed' =>
                    [
                      'type' => 'array',
                      'title' => 'Changed',
                      'description' => 'The time that the node was last edited.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'promote' =>
                    [
                      'type' => 'array',
                      'title' => 'Promoted to front page',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => TRUE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'sticky' =>
                    [
                      'type' => 'array',
                      'title' => 'Sticky at top of lists',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => FALSE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_timestamp' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision timestamp',
                      'description' => 'The time that the current revision was created.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'number',
                                  'title' => 'Timestamp value',
                                  'format' => 'utc-millisec',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_uid' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision user ID',
                      'description' => 'The user ID of the author of the current revision.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'target_id' =>
                                [
                                  'type' => 'integer',
                                  'title' => 'User ID',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'target_id',
                            ],
                          'title' => 'User',
                          'description' => 'The referenced entity',
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_log' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision log message',
                      'description' => 'Briefly describe the changes you have made.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'string',
                                  'title' => 'Text value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => '',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'revision_translation_affected' =>
                    [
                      'type' => 'array',
                      'title' => 'Revision translation affected',
                      'description' => 'Indicates if the last edit of a translation belongs to current revision.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                  'default_langcode' =>
                    [
                      'type' => 'array',
                      'title' => 'Default translation',
                      'description' => 'A flag indicating whether this is the default translation.',
                      'items' =>
                        [
                          'type' => 'object',
                          'properties' =>
                            [
                              'value' =>
                                [
                                  'type' => 'boolean',
                                  'title' => 'Boolean value',
                                ],
                            ],
                          'required' =>
                            [
                              0 => 'value',
                            ],
                        ],
                      'default' =>
                        [
                          0 =>
                            [
                              'value' => TRUE,
                            ],
                        ],
                      'maxItems' => 1,
                    ],
                ],
              'required' =>
                [
                  0 => 'type',
                  1 => 'title',
                ],
              'discriminator' => 'type',
            ],
          'node:article' =>
            [
              'allOf' =>
                [
                  0 =>
                    [
                      '$ref' => '#/definitions/node',
                    ],
                  1 =>
                    [
                      'type' => 'object',
                      'title' => 'node Schema',
                      'description' => 'Describes the payload for node entities.',
                    ],
                ],
            ],
        ],
    ];
    return $bundles[$entity_type][$bundle];
  }

}
