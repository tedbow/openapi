<?php

namespace Drupal\openapi\Tests;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\rest\Tests\RESTTestBase;
use Drupal\serialization\Encoder\JsonEncoder;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\Serializer\Serializer;

/**
 * Base class for OpenAPI tests.
 */
abstract class OpenApiTestBase extends RESTTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultFormat = 'json';

  /**
   * {@inheritdoc}
   */
  protected $defaultAuth = ['cookie'];

  /**
   * The serializer.
   *
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * The cookie jar.
   *
   * @var \GuzzleHttp\Cookie\CookieJar
   */
  protected $guzzleCookies;

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
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->defaultFormat = 'json';
    $this->defaultMimeType = 'application/json';
    $this->guzzleCookies = new CookieJar();
    $encoders = [new JsonEncoder()];
    $this->serializer = new Serializer([], $encoders);

    $this->drupalCreateContentType(['type' => 'page', 'name' => 'Page']);

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
  }

  /**
   * Executes a login HTTP request.
   *
   * @param string $name
   *   The username.
   * @param string $pass
   *   The user password.
   * @param string $format
   *   The format to use to make the request.
   *
   * @return \Psr\Http\Message\ResponseInterface The HTTP response.
   *   The HTTP response.
   */
  protected function loginRequest($name, $pass, $format = 'json') {
    $user_login_url = Url::fromRoute('user.login.http')
      ->setRouteParameter('_format', $format)
      ->setAbsolute();

    $request_body = [];
    if (isset($name)) {
      $request_body['name'] = $name;
    }
    if (isset($pass)) {
      $request_body['pass'] = $pass;
    }

    $result = \Drupal::httpClient()->post($user_login_url->toString(), [
      'body' => $this->serializer->encode($request_body, $format),
      'headers' => [
        'Accept' => "application/$format",
      ],
      'http_errors' => FALSE,
      'guzzle_cookies' => $this->guzzleCookies,
    ]);
    return $result;
  }

  /**
   * Asserts that HTTP response has expected response code and response body.
   *
   * @param \Drupal\Core\Url $url
   *   The Url object.
   * @param string $method
   *   The HTTP method.
   * @param int $status_code
   *   The status code.
   * @param array $expected_result
   *   The expected result, un-encoded.
   * @param string $message
   *   The message to display if body is not expected result.
   */
  protected function assertHttpResponse(Url $url, $method, $status_code, $expected_result, $message = '') {
    $response = $this->httpRequest($url, $method);
    $this->assertResponse($status_code);
    if ($expected_result !== NULL) {
      $data = Json::decode($response);
      $this->assertEqual($data, $expected_result, $message);
    }
  }

}
