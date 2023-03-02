<?php

namespace Civi\TokenManager;

use Civi\Token\TokenProcessor;
use CRM_Core_Smarty;
use CRM_TokenManager_DynamicTokenVariables_Manager;

class Registry {

  public static function register(\Civi\Token\Event\TokenRegisterEvent $e) {
    $context = $e->getTokenProcessor()->context;
    foreach (self::getFilteredTokenTasks($e->getTokenProcessor()) as $task) {
      $result = civicrm_api3('Sqltask', 'execute', [
        'id' => $task,
        'input_val' => json_encode([
          'action' => 'list',
          'context' => $context,
        ]),
      ]);
      if (!empty($result['values']['token_list'])) {
        $tokenList = json_decode($result['values']['token_list'], TRUE);
        foreach ($tokenList as $entityName => $field) {
          foreach ($field as $fieldName => $description) {
            $e->entity($entityName)
              ->register($fieldName, $description);
          }
        }
      }
    }

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

    $context = $tokenProcessor->context;
    $taskList = self::getFilteredTokenTasks($tokenProcessor);
    foreach ($e->getRows() as $rowId => $row) {
      foreach ($taskList as $task) {
        $result = civicrm_api3('Sqltask', 'execute', [
          'id' => $task,
          'input_val' => json_encode([
            'action' => 'eval',
            'context' => $context,
            'rowContext' => $tokenProcessor->rowContexts[$rowId],
          ]),
        ]);
        if (!empty($result['values']['token_values'])) {
          $tokenList = json_decode($result['values']['token_values'], TRUE);
          foreach ($tokenList as $entityName => $field) {
            foreach ($field as $fieldName => $value) {
              $row->tokens($entityName, $fieldName, $value);
            }
          }
        }
      }
    }

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

  public static function getFilteredTokenTasks(TokenProcessor $tokenProcessor) {
    $taskMap = \Civi::settings()->get('token_manager_sql_tasks') ?? [];

    $filteredTasks = [];
    $context = $tokenProcessor->context;
    foreach ($taskMap as $expression => $tasks) {
      if ($expression != '*' && empty(\JmesPath\Env::search($expression, $context))) {
        continue;
      }
      $filteredTasks = array_merge($filteredTasks, $tasks);
    }
    return $filteredTasks;
  }

}
