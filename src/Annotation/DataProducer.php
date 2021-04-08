<?php

namespace Drupal\graphql_easy\Annotation;

use Drupal\graphql\Annotation\DataProducer as GraphQlDataProducer;

/**
 * Annotation for data producer plugins.
 *
 * @Annotation
 * @codeCoverageIgnore
 */
class DataProducer extends GraphQlDataProducer {

  /**
   * The entity type name.
   *
   * @var string
   */

  public $entity_type_name;

}
