<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "plain_field_value",
 *   name = @Translation("Plain field value"),
 *   description = @Translation("Get stored value from any field by key."),
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
 *     "storage_key" = @ContextDefinition("string",
 *       label = @Translation("Storage key"),
 *       required = FALSE,
 *       default_value = "value"
 *     ),
 *   }
 * )
 */
class PlainFieldValue extends DataProducerPluginBase {

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return mixed
   */
  public function resolve(EntityInterface $entity, $field_name, $storage_key = 'value') {
    $field = $entity->get($field_name);
    if ($field->access('view')) {
      $value = $field->getValue();
      if (!empty($value)) {
        return $value[0][$storage_key];
      }
    }
    return NULL;
  }

}
