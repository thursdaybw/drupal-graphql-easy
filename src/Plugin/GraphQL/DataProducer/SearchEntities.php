<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_easy\Wrappers\SearchConnection;
use Drupal\search_api\Entity\Index;

/**
 * @DataProducer(
 *   id = "im_search_entities",
 *   name = @Translation("Search entities"),
 *   description = @Translation("Loads a list of entities via Search API."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Search connection")
 *   ),
 *   consumes = {
 *     "entity_type" = @ContextDefinition("any",
 *       label = @Translation("Entity type"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class SearchEntities extends DataProducerPluginBase {

  public function resolve(string $entity_type, RefinableCacheableDependencyInterface $metadata) {
    $query = Index::load('default')->query();
    $query->addCondition('search_api_datasource', 'entity:' . $entity_type)
      ->addCondition($entity_type . '_deleted', FALSE)
      ->execute();

    $metadata->mergeCacheMaxAge(-1);

    return new SearchConnection($query);
  }

}
