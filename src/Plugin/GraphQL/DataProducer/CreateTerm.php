<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\DataProducer;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\graphql_easy\Wrappers\Response\EntityResponse;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates a new term.
 *
 * @DataProducer(
 *   id = "im_create_term",
 *   name = @Translation("Create Term"),
 *   description = @Translation("Creates a new taxonomy term."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Term")
 *   ),
 *   consumes = {
 *     "vocabulary" = @ContextDefinition("any",
 *       label = @Translation("Vocabulary")
 *     ),
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Entity field data")
 *     )
 *   }
 * )
 */
class CreateTerm extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Id of the current drupal user.
   *
   * @var int
   */
  private int $currentUserId;

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
   * @param \Drupal\Core\Entity\EntityFieldManager $entityFieldManager
   *   Drupal's entity field manager.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    AccountProxyInterface $currentUser,
    EntityFieldManager $entityFieldManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUserId = $currentUser->id();
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * Creates a taxonomy term.
   *
   * @param string $vocabulary
   *   The taxonomy vocabulary
   * @param array $data
   *   The incoming field/property values.
   *
   * @return \Drupal\graphql_easy\Wrappers\Response\EntityResponse
   */
  public function resolve(string $vocabulary, array $data) {
    $response = new EntityResponse();
    try {
      // Use the Drupal value key for entity references.
      $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions('taxonomy_term', $vocabulary);
      foreach ($data as $key => $datum) {
        if ($fieldDefinitions[$key]->getType() === 'entity_reference') {
          $data[$key]['target_id'] = $data[$key]['id'];
          unset($data[$key]['id']);
        }
      }

      $data['vid'] = $vocabulary;

      $term = Term::create($data);

      if ($term->access('create')) {
        $term->setNewRevision();
        $term->setRevisionUserId($this->currentUserId);
        $term->save();
        $response->setEntity($term);
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
