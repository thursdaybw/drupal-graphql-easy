<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;

/**
 * @DataProducer(
 *   id = "im_parse_date_input",
 *   name = @Translation("Parse date input"),
 *   description = @Translation("Translate the date string from the frontend."),
 *   produces = @ContextDefinition("string",
 *     label = @Translation("Output")
 *   ),
 *   consumes = {
 *     "input" = @ContextDefinition("any",
 *       label = @Translation("Input")
 *     ),
 *     "field_path" = @ContextDefinition("string",
 *       label = @Translation("Field path")
 *     ),
 *   }
 * )
 */
class ParseDateInput extends DataProducerPluginBase {

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return mixed
   */
  public function resolve($input, string $field_path) {
    $date = new \DateTime($input[$field_path]);
    $input[$field_path] = $date->format('Y-m-d');
    return $input;
  }

}
