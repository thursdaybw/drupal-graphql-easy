<?php

namespace Drupal\graphql_easy\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Preset resolver plugin manager.
 */
class GraphQlResolverManager extends DefaultPluginManager {

  /**
   * Constructs a new PresetResolverManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler
  ) {
    parent::__construct(
      'Plugin/GraphQL/Resolver',
      $namespaces,
      $module_handler,
      'Drupal\graphql_easy\Plugin\GraphQL\Resolver\ResolverPluginInterface',
      'Drupal\graphql_easy\Annotation\GraphQL\ResolverPlugin'
    );

    $this->alterInfo('graphql_easy_preset_resolver_info');
    $this->setCacheBackend($cache_backend, 'graphql_easy_preset_resolver_plugins');
  }

}
