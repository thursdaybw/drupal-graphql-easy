<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for entity remove fields.
 *
 * @ResolverPlugin(
 *   id = "mutation_entity_remove",
 *   label="Mutation: Entity remove"
 * )
 */
class MutationEntityRemoveResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('im_remove_entity')
      ->map('entity_type', $this->builder->fromValue($resolver_config['entity_type']))
      ->map('id', $this->builder->fromArgument('id'));
  }

}
