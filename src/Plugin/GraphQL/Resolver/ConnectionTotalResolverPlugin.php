<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\GraphQL\Resolver\Callback as graphql_callback;
use Drupal\graphql_easy\Wrappers\QueryConnection;

/**
 * Provides a preset resolver for total fields on connection queries.
 *
 * @ResolverPlugin(
 *   id = "connection_total",
 *   label="Connection total"
 * )
 */
class ConnectionTotalResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): graphql_callback {
    return $this->builder->callback(function (QueryConnection $connection) {
      return $connection->total();
    });
  }

}
