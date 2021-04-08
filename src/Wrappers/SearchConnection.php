<?php

namespace Drupal\graphql_easy\Wrappers;

use Drupal\search_api\Query\QueryInterface;
use GraphQL\Deferred;

class SearchConnection {

  /**
   * @var QueryInterface
   */
  protected $query;

  /**
   * QueryConnection constructor.
   *
   * @param
   */
  public function __construct(QueryInterface $query) {
    $this->query = $query;
  }

  /**
   * @return int
   */
  public function total() {
    return $this->query->getResults()->getResultCount();
  }

  /**
   * @return array|\GraphQL\Deferred
   */
  public function items() {
    $results = $this->query->getResults()->getResultItems();
    if (empty($results)) {
      return [];
    }

    $results = array_keys($results);
    preg_match('/(\w*)\//', $results[0], $matches);
    $type = $matches[1];
    $ids = [];
    foreach($results as $result) {
      preg_match('/\/([0-9]*)/', $result, $matches);
      $ids[] = $matches[1];
    }

    $buffer = \Drupal::service('graphql_easy.buffer.entity');
    $callback = $buffer->add($type, $ids);
    return new Deferred(function () use ($callback) {
      return $callback();
    });
  }

}
