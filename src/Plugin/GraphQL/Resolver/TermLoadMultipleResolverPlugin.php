<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for loading multiple entities fields.
 *
 * @ResolverPlugin(
 *   id = "term_load_multiple",
 *   label="Term load multiple"
 * )
 */
class TermLoadMultipleResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('im_query_terms')
      ->map('vocabulary', $this->builder->fromValue($resolver_config['bundle']))
      ->map('skip', $this->builder->fromArgument('skip'))
      ->map('take', $this->builder->fromArgument('take'))
      ->map('sort', $this->builder->fromArgument('sort'))
      ->map('filter', $this->builder->fromArgument('filter'));
  }

}
