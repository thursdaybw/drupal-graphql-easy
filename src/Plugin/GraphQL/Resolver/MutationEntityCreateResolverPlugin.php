<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for mutation entity create fields.
 *
 * @ResolverPlugin(
 *   id = "mutation_entity_create",
 *   label="Mutation: Entity create"
 * )
 */
class MutationEntityCreateResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('im_create_term')
      ->map('vocabulary', $this->builder->fromValue($resolver_config['bundle']))
      ->map('data', $this->builder->fromArgument('data'));
  }

}
