<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for entity_id fields.
 *
 * @ResolverPlugin(
 *   id = "id",
 *   label="ID"
 * )
 */
class IdResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('entity_id')
      ->map('entity', $this->builder->fromParent());
  }

}
