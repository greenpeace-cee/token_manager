<?php

namespace Civi\TokenManager;

class Registry {

  public static function register(\Civi\Token\Event\TokenRegisterEvent $e) {
    $dynamicTokens = \Civi\Api4\DynamicToken::get(FALSE)
      ->execute();
    foreach ($dynamicTokens as $dynamicToken) {
      $e->entity($dynamicToken['entity_name'])
        ->register($dynamicToken['field_name'], $dynamicToken['description']);
    }
  }

  public static function evaluate(\Civi\Token\Event\TokenValueEvent $e) {
    $tokenProcessor = $e->getTokenProcessor();
    if (!empty($tokenProcessor->tokenManagerEvaluating)) {
      return;
    }
    $tokenProcessor->tokenManagerEvaluating = TRUE;
    foreach ($e->getRows() as $row) {
      $dynamicTokens = \Civi\Api4\DynamicToken::get(FALSE)
        ->execute();
      foreach ($dynamicTokens as $dynamicToken) {
        $tokenProcessor->addMessage($dynamicToken['entity_name'] . '.' . $dynamicToken['field_name'], $dynamicToken['value'], 'text/plain');
        $tokenProcessor->evaluate();
        $row->tokens(
          $dynamicToken['entity_name'],
          $dynamicToken['field_name'],
          $row->render($dynamicToken['entity_name'] . '.' . $dynamicToken['field_name'])
        );
      }
    }
    $tokenProcessor->tokenManagerEvaluating = FALSE;
  }

}
