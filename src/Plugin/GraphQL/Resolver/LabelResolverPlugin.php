<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerProxy;

/**
 * Provides a preset resolver for plain_field_value fields.
 *
 * @ResolverPlugin(
 *   id = "label",
 *   label="Label"
 * )
 */
class LabelResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): DataProducerProxy {
    return $this->builder->produce('entity_label')
      ->map('entity', $this->builder->fromParent());
  }

}
