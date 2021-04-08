<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for data providers implementing parents.
 */
abstract class ParentsBase extends DataProducerPluginBase implements ContainerFactoryPluginInterface {
  /**
   * Comment moved inside this abstract class so it doesn't get called:
   *
   * For data produces that provide 'parents' functionality, extend this class
   * define the annotations as per normal for graph ql data produces but supply
   * an addition `entity_type_name = "my_entity_type_name"` annotation.
   *
   * Extend this class, annotate like any other graphql data producer but also
   * provide the entity type name.
   * Example:
   * @code
   * @DataProducer(
   *   id = "im_category_parents",
   *   name = @Translation("Parent IDs"),
   *   description = @Translation("Get an array of parent IDs for GovermentEntity entities."),
   *   produces = @ContextDefinition("array", label =
   *     @Translation("Array of parent IDs")
   *   ),
   *   consumes = {
   *     "entity" = @ContextDefinition("entity",
   *       label = @Translation("Entity")
   *     ),
   *   },
   *   entity_type_name = "taxonomy_term"
   * )
   * @endcode
   */

  /**
   * Entity type storage as specified by entity_type_name annotation.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The result to be returned by this dataproducers resolve() method.
   *
   * @var array
   */
  protected $result = [];

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
      $entity_storage = $container->get('entity_type.manager')->getStorage($plugin_definition['entity_type_name'])
    );
  }

  /**
   * Constructor for ParentsBase abstract data producer.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $pluginId
   *   The plugin id.
   * @param mixed $pluginDefinition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $entityStorage
   *   The entity storage for the relevant entity type.
   *
   * @codeCoverageIgnore
   */
  public function __construct(
    array $configuration,
    $pluginId,
    $pluginDefinition,
    EntityStorageInterface $entityStorage
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->storage = $entityStorage;
  }

  /**
   * Implement the resolve method that's dynamically called from the base class.
   *
   * \Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase::resolveField.
   *
   * Difficult to document here when it's not documented in the base class..
   * it's not even defined!! it's just called magically.. I'll be submitting
   * a merge request on graphql to define that as an abstract method.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   This entity comes from somewhere.. no docs on the base class :shrug:.
   *
   * @return mixed
   *   Returns something. The base class does silly things.
   */
  public function resolve(EntityInterface $entity) {
    $field = $entity->get('parent');
    if ($field->access('view')) {
      $this->recursivelyAddParentsToResult($entity);
      return $this->result;
    }
    else {
      return NULL;
    }
  }

  /**
   * Traverse the entity's parents hierachy adding each parent to the result.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to upwards from.
   */
  private function recursivelyAddParentsToResult(EntityInterface $entity) {
    $parentId = $entity->parent->target_id;

    if ($parentId !== '0') {
      $this->result[] = $parentId;
      $this->recursivelyAddParentsToResult($this->storage->load($parentId));
    }
  }

}
