<?php

namespace Civi\TokenManager;

use CRM_TokenManager_DynamicTokenVariables_Manager;
use CRM_Core_Smarty;

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
    $originalSmartySetting = $tokenProcessor->context['smarty'] ?? FALSE;
    $tokenProcessor->context['smarty'] = TRUE;

    foreach ($e->getRows() as $row) {
      $dynamicTokens = \Civi\Api4\DynamicToken::get(FALSE)
        ->execute();
      foreach ($dynamicTokens as $dynamicToken) {
        $variables = CRM_TokenManager_DynamicTokenVariables_Manager::getVariables(['caseId' => $row->context['caseId'], 'activityId' => $row->context['activityId']], $dynamicToken['value']);
        if (!empty($variables)) {
          $smarty = CRM_Core_Smarty::singleton();
          $smarty->pushScope($variables);
        }

        $tokenProcessor->addMessage($dynamicToken['entity_name'] . '.' . $dynamicToken['field_name'], $dynamicToken['value'], 'text/plain');
        $tokenProcessor->evaluate();
        $row->tokens(
          $dynamicToken['entity_name'],
          $dynamicToken['field_name'],
          $row->render($dynamicToken['entity_name'] . '.' . $dynamicToken['field_name'])
        );
        if (!empty($dynamicToken['smarty_variable_name'])) {
          // expose token as smarty variable
          $tokenProcessor->context['smartyTokenAlias'][$dynamicToken['smarty_variable_name']] = $dynamicToken['entity_name'] . '.' . $dynamicToken['field_name'];
        }
      }
    }
    $tokenProcessor->context['smarty'] = $originalSmartySetting;
    $tokenProcessor->tokenManagerEvaluating = FALSE;
  }

}
