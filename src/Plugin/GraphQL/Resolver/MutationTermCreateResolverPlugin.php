<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for term create mutation fields.
 *
 * @ResolverPlugin(
 *   id = "mutation_term_create",
 *   label="Mutation: term create"
 * )
 */
class MutationTermCreateResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('im_create_entity')
      ->map('entity_type', $this->builder->fromValue($resolver_config['entity_type']))
      ->map('data', $this->builder->fromArgument('data'));
  }

}
