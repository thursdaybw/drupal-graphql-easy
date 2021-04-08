<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Resolver;

use Drupal\graphql\GraphQL\Resolver\Composite;

/**
 * Provides a preset resolver for entity reference fields.
 *
 * @ResolverPlugin(
 *   id = "entity_reference",
 *   label = "Entity Reference"
 * )
 */
class EntityReferenceResolverPlugin extends ResolverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function getResolver($resolver_config, $drupal_field_name): Composite {
    $base_field_definitions = \Drupal::getContainer()->get('entity_field.manager')->getFieldDefinitions($resolver_config['entity_type'], $resolver_config['bundle']);

    $cardinality = $base_field_definitions[$drupal_field_name]->getCardinality();

    if ($cardinality === 1) {
      $getArrayFunction = function ($array) {
        return $array[0];
      };
    }
    else {
      $getArrayFunction = function ($array) {
        return $array;
      };
    }

    return $this->builder->compose(
      $this->builder->produce('entity_reference')
        ->map('entity', $this->builder->fromParent())
        ->map('field', $this->builder->fromValue($drupal_field_name)),
      $this->builder->callback($getArrayFunction)
    );
  }

}
