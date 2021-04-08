<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for plain_field_value fields.
 *
 * @ResolverPlugin(
 *   id = "plain_field_value",
 *   label="Plain field value"
 * )
 */
class PlainFieldValueResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('plain_field_value')
      ->map('entity', $this->builder->fromParent())
      ->map('field_name', $this->builder->fromValue($drupal_field_name));
  }

}
