<?php


namespace Drupal\openapi\OpenApiGenerator;


class OpenApiJsonapiGenerator extends OpenApiGeneratorBase {
  public function getBasePath() {
    return parent::getBasePath() . '/jsonapi';
  }

}
