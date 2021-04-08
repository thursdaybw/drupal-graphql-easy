<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\GraphQL\Resolver\Callback as graphql_callback;
use Drupal\graphql_easy\Wrappers\Response\EntityResponse;

/**
 * Provides a preset resolver for items field on connection queries.
 *
 * @ResolverPlugin(
 *   id = "entity_response",
 *   label="Entity response"
 * )
 */
class EntityResponseResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): graphql_callback {
    return $this->builder->callback(function (EntityResponse $response) {
      return $response->entity();
    });
  }

}
