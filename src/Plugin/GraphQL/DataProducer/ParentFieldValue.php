<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "parent_field_value",
 *   name = @Translation("Parent field value"),
 *   description = @Translation("Get parent field for use with Devexteme TreeView."),
 *   produces = @ContextDefinition("string",
 *     label = @Translation("Field value")
 *   ),
 *   consumes = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity")
 *     ),
 *     "field_name" = @ContextDefinition("string",
 *       label = @Translation("Field name")
 *     ),
 *   }
 * )
 */
class ParentFieldValue extends DataProducerPluginBase {

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return mixed
   */
  public function resolve(EntityInterface $entity, string $field_name) {
    $field = $entity->get($field_name);
    if ($field->access('view')) {
      $parent = $field->target_id;
      if ($parent === NULL) {
        // Make the parent the root node of the tree
        return 0;
      }
      return $parent;
    } else {
      return NULL;
    }
  }

}
