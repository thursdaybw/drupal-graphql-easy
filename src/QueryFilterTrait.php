<?php

namespace Drupal\graphql_easy;

trait QueryFilterTrait {

  private function filterStringToConditions(&$queryFragment, $filterArray) {

    if (!is_array($filterArray[0])) {
      // Filter customisation to allow querying of deeper fields.
      $replacements = [
        '/agency\.id/' => 'agency.entity:im_government_entity.name',
        '/category\.id/' => 'category.entity:taxonomy_term.name',
        '/supplier\.id/' => 'supplier.entity:im_supplier.name',
        '/\.id/' => '',
      ];
      $key = preg_replace(array_keys($replacements), $replacements, $filterArray[0]);
      $value = $filterArray[2];
      $operator = $filterArray[1];

      switch($operator) {
        case 'contains': {
          $operator = 'LIKE';
          $value = '%' . $value . '%';
          break;
        }
        case 'notcontains': {
          $operator = 'NOT LIKE';
          $value = '%' . $value . '%';
          break;
        }
        case 'startswith': {
          $operator = 'LIKE';
          $value = $value . '%';
          break;
        }
        case 'endswith': {
          $operator = 'LIKE';
          $value = '%' . $value;
          break;
        }
      }
      return $queryFragment->condition($key, $value, $operator);
    }

    if ($filterArray[1] === 'and') {
      $conditionGroup = $queryFragment->andConditionGroup();
    }
    else {
      $conditionGroup = $queryFragment->orConditionGroup();
    }

    foreach ($filterArray as $key => $filter) {
      // Skip the ands and ors.
      if ($key % 2 === 1) {
        continue;
      }
      $this->filterStringToConditions($conditionGroup, $filter);
    }
    $queryFragment->condition($conditionGroup);
  }

}
