<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for plain_field_value fields.
 *
 * @ResolverPlugin(
 *   id = "entity_load",
 *   label="Entity load"
 * )
 */
class EntityLoadResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('entity_load')
      ->map('type', $this->builder->fromValue($resolver_config['entity_type']))
      ->map('id', $this->builder->fromArgument('id'));
  }

}
