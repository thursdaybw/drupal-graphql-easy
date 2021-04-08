<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_easy\Wrappers\Response\EntityResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Updates a new article entity.
 *
 * @DataProducer(
 *   id = "im_update_entity",
 *   name = @Translation("Update Entity"),
 *   description = @Translation("Updates an entity's values."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Entity")
 *   ),
 *   consumes = {
 *     "entity_type" = @ContextDefinition("any",
 *       label = @Translation("Entity type")
 *     ),
 *     "id" = @ContextDefinition("any",
 *       label = @Translation("Entity id")
 *     ),
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Entity field values")
 *     )
 *   }
 * )
 */
class UpdateEntity extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * CreateEntity constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $currentUser,
    EntityTypeManager $entityTypeManager,
    EntityFieldManager $entityFieldManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * Updates an entity.
   *
   * @param string $entity_type
   * @param int $id
   * @param array $data
   *
   * @return \Drupal\graphql_easy\Wrappers\Response\EntityResponse
   */
  public function resolve(string $entity_type, int $id, array $data) {
    $response = new EntityResponse();
    try {

      // Use the Drupal value key for entity references.
      $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions($entity_type, $entity_type);
      foreach ($data as $key => $datum) {
        if ($fieldDefinitions[$key]->getType() === 'entity_reference') {
          $data[$key]['target_id'] = $data[$key]['id'];
          unset($data[$key]['id']);
        }
      }

      /* @var \Drupal\Core\Entity\EntityInterface $entityClass */
      $entityClass = $this->entityTypeManager->getDefinition($entity_type)
        ->getClass();
      $entity = $entityClass::load($id);

      if ($entity->access('update')) {
        foreach ($data as $key => $datum) {
          $entity->set($key, $datum);
        }
        $entity->setNewRevision();
        $entity->setRevisionUserId($this->currentUser->id());
        $entity->save();
        $response->setEntity($entity);
      } else {
        $response->addViolation('Not allowed.');
      }
    } catch (\Exception $exception) {
      $response->addViolation($exception->getMessage());
    }
    return $response;
  }

}
