<?php

class CRM_TokenManager_DynamicTokenVariables_Manager {

  /**
   * @param $entities
   * @param $tokenValue
   * @return array
   */
  public static function getVariables($entities, $tokenValue): array {
    $variables = [];

    if (!empty($entities['activityId'])) {
      $activityVariables = (new CRM_TokenManager_DynamicTokenVariables_Entities_Activity($entities['activityId'], $tokenValue))->getVariables();
      $variables = array_merge($variables, $activityVariables);
    }

    if (!empty($entities['caseId'])) {
      $caseVariables = (new CRM_TokenManager_DynamicTokenVariables_Entities_Case($entities['caseId'], $tokenValue))->getVariables();
      $variables = array_merge($variables, $caseVariables);
    }

    return $variables;
  }

  public static function getAvailableVariables() {
    return [
      'activity' => array_keys(CRM_TokenManager_DynamicTokenVariables_Entities_Activity::getMethodVariableMap()),
      'case' => array_keys(CRM_TokenManager_DynamicTokenVariables_Entities_Case::getMethodVariableMap()),
    ];
  }

}
