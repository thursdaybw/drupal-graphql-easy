<?php

declare(strict_types = 1);

namespace Drupal\graphql_easy\Wrappers\Response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\GraphQL\Response\Response;

/**
 * Type of response used when an item is returned.
 */
class EntityResponse extends Response {

  /**
   * The entity to be served.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $entity;

  /**
   * Sets the content.
   *
   * @param \Drupal\Core\Entity\EntityInterface|null $entity
   *   The article to be served.
   */
  public function setEntity(?EntityInterface $entity): void {
    $this->entity = $entity;
  }

  /**
   * Gets the article to be served.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The article to be served.
   */
  public function entity(): ?EntityInterface {
    return $this->entity;
  }

}
