<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for link fields.
 *
 * @ResolverPlugin(
 *   id = "link",
 *   label="Link"
 * )
 */
class LinkResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('plain_field_value')
      ->map('entity', $this->builder->fromParent())
      ->map('field_name', $this->builder->fromValue('link'))
      ->map('storage_key', $this->builder->fromValue('uri'));
  }

}
