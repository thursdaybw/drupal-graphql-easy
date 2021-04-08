<?php

namespace Drupal\graphql_easy\Plugin\GraphQL\Schema;

use Drupal\graphql\Plugin\GraphQL\Schema\ComposableSchema;

/**
 * @Schema(
 *   id = "intermedium",
 *   name = "Intermedium GraphQL Schema",
 *   extensions = "intermedium",
 * )
 */
class BaseSchema extends ComposableSchema {

  /**
   * {@inheritdoc}
   */
  protected function getSchemaDefinition() {
    return <<<GQL
      type Schema {
        query: Query
      }

      type Query

      type Mutation

      scalar Violation

      input EntityReference {
        id: Int!
      }

      input SortInput {
        selector: String!
        desc: Boolean!
      }

GQL;

  }

}
