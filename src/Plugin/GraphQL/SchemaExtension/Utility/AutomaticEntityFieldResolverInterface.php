<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\SchemaExtension\Utility;

use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql_easy\Plugin\GraphQlResolverManager;

/**
 * Provides the ability to generate graphql producers.
 *
 * @package Drupal\graphql_easy\Plugin\GraphQL\SchemaExtension
 */
interface AutomaticEntityFieldResolverInterface {

  /**
   * GraphQlResolver constructor.
   *
   * @param \Drupal\graphql_easy\Plugin\GraphQlResolverManager $presetResolverPluginManager
   *   Plugin manager for Preset Resolver plugins.
   * @param array $schemaExtensionPluginDefinition
   *   Resolver config of this schema extension.
   */
  public function __construct(
    GraphQlResolverManager $presetResolverPluginManager,
    array $schemaExtensionPluginDefinition
  );

  /**
   * Generate and add the resolvers to the GraphQL registry.
   */
  public function execute(): void;

  /**
   * Sets the GraphQL Schema registry property so we can add to it.
   *
   * @param \Drupal\graphql\GraphQL\ResolverRegistryInterface $resolver_registry
   *   GraphQL schema registry.
   */
  public function setGraphQlResolverRegistry(ResolverRegistryInterface $resolver_registry): void;

  /**
   * Register a resolver plugin for a single field.
   *
   * @param string $graphql_object_type
   *   The GraphQL object of this field.
   * @param string $preset_resolver_name
   *   The resolver plugin that supplies the resolver.
   * @param string $graphql_field_name
   *   The field name as specified in graphql.
   *
   * @return \Drupal\graphql_easy\Plugin\GraphQL\SchemaExtension\Utility\AutomaticEntityFieldResolver
   *   Return $this so we can chain commands.
   */
  public function addFieldResolver(string $graphql_object_type, string $preset_resolver_name, string $graphql_field_name): AutomaticEntityFieldResolver;

  /**
   * Register a resolver plugin for multiple fields.
   *
   * @param string $graphql_object_type
   *   The GraphQL object of this field.
   * @param array $graphql_field_names_to_resolver_plugin_id_map
   *   An array of preset_resolver plugin ids keyeb by graphql fieldname.
   *
   * @return \Drupal\graphql_easy\Plugin\GraphQL\SchemaExtension\Utility\AutomaticEntityFieldResolver
   *   Return $this so we can chain commands.
   */
  public function addFieldsResolvers(string $graphql_object_type, array $graphql_field_names_to_resolver_plugin_id_map): AutomaticEntityFieldResolver;

  /**
   * Set the map of drupal to graphql field names.
   *
   * @param array $drupal_to_graphql_field_name_map
   *   Array of drupal field name keys mapped to graphql fieldname values.
   *
   * @return \Drupal\graphql_easy\Plugin\GraphQL\SchemaExtension\Utility\AutomaticEntityFieldResolver
   *   Returns this instance so we can chain commands.
   */
  public function addFieldNameOverrides(array $drupal_to_graphql_field_name_map): AutomaticEntityFieldResolver;

}
