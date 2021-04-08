<?php

namespace Drupal\graphql_easy\Annotation\GraphQL;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Preset resolver item annotation object.
 *
 * @see \Drupal\graphql_easy\Plugin\GraphQlResolverManager
 * @see plugin_api
 *
 * @Annotation
 */
class ResolverPlugin extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
