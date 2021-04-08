<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\GraphQL\Resolver\Composite;

/**
 * Provides a preset resolver for parent field on terms.
 *
 * @ResolverPlugin(
 *   id = "term_parent",
 *   label="Term parent"
 * )
 */
class TermParentResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): Composite {
    return $this->builder->compose(
      $this->builder->produce('entity_reference')
        ->map('entity', $this->builder->fromParent())
        ->map('field', $this->builder->fromValue('parent')),
      $this->builder->callback(
        function ($array) {
          return $array[0];
        }
      )
    );
  }

}
