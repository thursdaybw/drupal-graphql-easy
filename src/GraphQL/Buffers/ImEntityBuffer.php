<?php

namespace Drupal\graphql_easy\GraphQL\Buffers;

use Drupal\graphql\GraphQL\Buffers\EntityBuffer;

class ImEntityBuffer extends EntityBuffer {

  /**
   * {@inheritdoc}
   */
  public function resolveBufferArray(array $buffer) {
    $type = reset($buffer)['type'];
    $ids = array_map(function (\ArrayObject $item) {
      return (array) $item['id'];
    }, $buffer);

    $ids = call_user_func_array('array_merge', $ids);
    $ids = array_values(array_unique($ids));

    // Load the buffered entities.
    $entities = $this->entityTypeManager
      ->getStorage($type)
      ->loadMultiple($ids);

    return array_map(function ($item) use ($entities) {
      if (is_array($item['id'])) {
        return array_reduce($item['id'], function ($carry, $current) use ($entities) {

          // We overrode the parent class so we could add an access check here.
          if (!empty($entities[$current]) && $entities[$current]->access('view')) {
            array_push($carry, $entities[$current]);
          }

          return $carry;
        }, []);
      }

      return isset($entities[$item['id']]) ? $entities[$item['id']] : NULL;
    }, $buffer);
  }

}
