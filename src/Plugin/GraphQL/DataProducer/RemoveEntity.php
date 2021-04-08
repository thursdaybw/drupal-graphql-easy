<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_easy\Wrappers\Response\EntityResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Removes an entity.
 *
 * @DataProducer(
 *   id = "im_remove_entity",
 *   name = @Translation("Removes Entity"),
 *   description = @Translation("Removes an entity."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Entity")
 *   ),
 *   consumes = {
 *     "entity_type" = @ContextDefinition("any",
 *       label = @Translation("Entity type")
 *     ),
 *     "id" = @ContextDefinition("any",
 *       label = @Translation("Entity id")
 *     )
 *   }
 * )
 */
class RemoveEntity extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * UpdateEntity constructor.
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
    EntityTypeManager $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Creates a government entity.
   *
   * @param array $data
   *   The title of the job.
   *
   * @return EntityResponse
   *
   * @throws \Exception
   */
  public function resolve(string $entity_type, int $id) {
    $response = new EntityResponse();
    try {

      /* @var \Drupal\Core\Entity\EntityInterface $entityClass */
      $entityClass = $this->entityTypeManager->getDefinition($entity_type)
        ->getClass();
      $entity = $entityClass::load($id);

      if ($entity->access('update')) {
        $entity->set('deleted', TRUE);
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
