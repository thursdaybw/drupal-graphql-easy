<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_easy\Wrappers\Response\EntityResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new entity.
 *
 * @DataProducer(
 *   id = "im_create_entity",
 *   name = @Translation("Create Entity"),
 *   description = @Translation("Creates a new entity."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Entity")
 *   ),
 *   consumes = {
 *     "entity_type" = @ContextDefinition("any",
 *       label = @Translation("Entity type")
 *     ),
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Entity field data")
 *     )
 *   }
 * )
 */
class CreateEntity extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Id of the current drupal user.
   *
   * @var int
   */
  private int $currentUserId;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Entity field manager service.
   *
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
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   Drupal's account proxy.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Drupal's entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManager $entityFieldManager
   *   Drupal's entity field manager.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    AccountProxyInterface $currentUser,
    EntityTypeManagerInterface $entityTypeManager,
    EntityFieldManager $entityFieldManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUserId = $currentUser->id();
    $this->entityTypeManager = $entityTypeManager;
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * Creates a government entity.
   *
   * @param string $entity_type
   *   The entity type.
   * @param array $data
   *   The incoming government entity values.
   *
   * @return \Drupal\graphql_easy\Wrappers\Response\EntityResponse
   *   The entity response.
   */
  public function resolve(string $entity_type, array $data) {
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
      $entity = $entityClass::create($data);

      if ($entity->access('create')) {
        $entity->setNewRevision();
        $entity->setRevisionUserId($this->currentUserId);
        $entity->save();
        $response->setEntity($entity);
      }
      else {
        $response->addViolation('Not allowed.');
      }
    }
    catch (\Exception $exception) {
      $response->addViolation($exception->getMessage());
    }
    return $response;
  }

}
