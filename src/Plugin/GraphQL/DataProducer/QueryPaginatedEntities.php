<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_easy\QueryFilterTrait;
use Drupal\graphql_easy\Wrappers\QueryConnection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DataProducer(
 *   id = "im_query_paginated_entities",
 *   name = @Translation("Load entities"),
 *   description = @Translation("Loads a list of entities."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Entity connection")
 *   ),
 *   consumes = {
 *     "entity_type" = @ContextDefinition("any",
 *       label = @Translation("Entity type")
 *     ),
 *     "skip" = @ContextDefinition("integer",
 *       label = @Translation("Skip"),
 *       required = FALSE,
 *     ),
 *     "take" = @ContextDefinition("integer",
 *       label = @Translation("Take"),
 *       required = FALSE,
 *     ),
 *     "sort" = @ContextDefinition("any",
 *       label = @Translation("Sort"),
 *       required = FALSE,
 *     ),
 *     "filter" = @ContextDefinition("string",
 *       label = @Translation("Filter"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class QueryPaginatedEntities extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  use QueryFilterTrait;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * {@inheritdoc}
   *
   * @codeCoverageIgnore
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * government entities constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $pluginId
   *   The plugin id.
   * @param mixed $pluginDefinition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *
   * @codeCoverageIgnore
   */
  public function __construct(
    array $configuration,
    $pluginId,
    $pluginDefinition,
    EntityTypeManagerInterface $entityManager
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->entityManager = $entityManager;
  }

  /**
   * @return \Drupal\graphql_easy\Wrappers\QueryConnection
   *   A query connection.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function resolve(string $entity_type, ?int $skip, ?int $take, ?array $sort, ?string $filter, RefinableCacheableDependencyInterface $metadata) {
    $storage = $this->entityManager->getStorage($entity_type);
    $type = $storage->getEntityType();
    $query = $storage->getQuery()
      ->condition('deleted', FALSE)
      ->currentRevision();

    if (!is_null($skip) && !is_null($take)) {
      $query->range($skip, $take);
    }

    if ($sort !== NULL) {
      foreach ($sort as $index) {
        $query->sort($index['selector'], $index['desc'] ? 'DESC' : 'FALSE');
      }
    }
    else {
      $query->sort($type->getKey('label'));
    }

    if ($filter !== NULL) {
      $this->filterStringToConditions($query, json_decode($filter));
    }

//    $query->condition('vid', 'category');

    $metadata->addCacheTags($type->getListCacheTags());
    $metadata->addCacheContexts($type->getListCacheContexts());

    return new QueryConnection($query);
  }

}
