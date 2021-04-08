<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for parent field on terms.
 *
 * @ResolverPlugin(
 *   id = "mutation_update",
 *   label="Mutation update"
 * )
 */
class MutationUpdateResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('im_update_entity')
      ->map('entity_type', $this->builder->fromValue($resolver_config['entity_type']))
      ->map('id', $this->builder->fromArgument('id'))
      ->map('data', $this->builder->fromArgument('data'));
  }

}
