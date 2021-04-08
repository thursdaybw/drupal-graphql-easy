<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\SchemaExtension\Utility;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql_easy\Plugin\GraphQlResolverManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AutomaticEntityFieldResolver.
 *
 * Reads the base definitions of fields for the entity type specified in the
 * resolver config, ignores fields explicitly exluded with addRestrictedField()
 * and adds GraphQL resolvers for each.
 *
 * @package Drupal\graphql_easy\Plugin\GraphQL\SchemaExtension\Utility
 */
class AutomaticEntityFieldResolver implements AutomaticEntityFieldResolverInterface {

  /**
   * The registry.
   *
   * @var \Drupal\graphql\GraphQL\ResolverRegistryInterface
   */
  protected ResolverRegistryInterface $resolverRegistry;

  /**
   * Array of config for our resolver.
   *
   * @var array
   */
  protected array $schemaExtensionPluginDefinition;

  /**
   * Map of graphql field name keys to drupal field name values.
   *
   * @var array
   */
  private array $drupalToGraphqlFieldNameMap = [];

  /**
   * PresetResolver Plugin Manager.
   *
   * @var \Drupal\graphql_easy\Plugin\GraphQlResolverManager
   */
  private GraphQlResolverManager $presetResolverManager;

  /**
   * List of fields to process, keyed by preset resolver plugin name.
   *
   * @var array
   */
  private array $fieldsToBeProcessedByType;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    GraphQlResolverManager $presetResolverPluginManager,
    array $schemaExtensionPluginDefinition
  ) {
    $this->presetResolverManager           = $presetResolverPluginManager;
    $this->schemaExtensionPluginDefinition = $schemaExtensionPluginDefinition;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $resolver_config): AutomaticEntityFieldResolver {
    $graphql_resolver_manager = $container->get('plugin.manager.graphql_resolver');

    return new static(
      $graphql_resolver_manager,
      $resolver_config
    );

  }

  /**
   * {@inheritdoc}
   */
  public function addFieldResolver(string $graphql_object_type, string $preset_resolver_name, string $graphql_field_name): AutomaticEntityFieldResolver {
    $this->fieldsToBeProcessedByType[$graphql_object_type][$preset_resolver_name][] = $graphql_field_name;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldsResolvers(string $graphql_object_type, array $graphql_field_names_to_resolver_plugin_id_map): AutomaticEntityFieldResolver {
    foreach ($graphql_field_names_to_resolver_plugin_id_map as $graphql_field_name => $preset_resolver_name) {
      $this->fieldsToBeProcessedByType[$graphql_object_type][$preset_resolver_name][] = $graphql_field_name;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldNameOverrides(array $drupal_to_graphql_field_name_map): AutomaticEntityFieldResolver {
    $this->drupalToGraphqlFieldNameMap = array_merge($this->drupalToGraphqlFieldNameMap, $drupal_to_graphql_field_name_map);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setGraphQlResolverRegistry(ResolverRegistryInterface $resolver_registry): void {
    $this->resolverRegistry = $resolver_registry;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(): void {
    foreach ($this->fieldsToBeProcessedByType as $graphql_object_type => $fields) {
      foreach ($fields as $preset_resolver_plugin_name => $graphql_field_names) {
        foreach ($graphql_field_names as $graphql_field_name) {
          $this->processFieldByPlugins($graphql_object_type, $preset_resolver_plugin_name, $graphql_field_name);
        }
      }
    }
  }

  /**
   * Return the graphql field name for this drupal field name.
   *
   * @param string $graphql_field_name
   *   The name of this field as defined in drupal.
   *
   * @return string
   *   Return the graphql field name for this drupal field name.
   */
  private function getDrupalFieldNameFromGraphqlFieldName(string $graphql_field_name): string {
    if (!in_array($graphql_field_name, $this->drupalToGraphqlFieldNameMap)) {
      return $graphql_field_name;
    }
    else {
      if ($key = array_search($graphql_field_name, $this->drupalToGraphqlFieldNameMap)) {
        return $key;
      }
    }
  }

  /**
   * Add a resolver of the right type for the field via a presetResolverPlugin.
   *
   * @param string $graphql_type
   *   GraphQl Query type. like Mutation, Query or ContractConnection etc.
   * @param string $preset_resolver_plugin_name
   *   The name of the preset resolver plugin providing this resolver.
   * @param string $graphql_field_name
   *   The field name as in graphql.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  private function processFieldByPlugins(string $graphql_type, string $preset_resolver_plugin_name, string $graphql_field_name): void {
    $plugin_instance = $this->presetResolverManager->createInstance($preset_resolver_plugin_name);
    $resolver        = $plugin_instance->getResolver($this->schemaExtensionPluginDefinition, $this->getDrupalFieldNameFromGraphqlFieldName($graphql_field_name));

    $this->resolverRegistry->addFieldResolver($graphql_type, $graphql_field_name,
      $resolver
    );
  }

}
