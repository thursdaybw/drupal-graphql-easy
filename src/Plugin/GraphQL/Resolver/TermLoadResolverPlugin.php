<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for plain_field_value fields.
 *
 * @ResolverPlugin(
 *   id = "term_load",
 *   label="Term load"
 * )
 */
class TermLoadResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('entity_load')
      ->map('type', $this->builder->fromValue('taxonomy_term'))
      ->map('id', $this->builder->fromArgument('id'))
      ->map('bundles', $this->builder->fromValue([$resolver_config['bundle']]));
  }

}
