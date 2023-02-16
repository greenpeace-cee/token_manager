<?php

class CRM_TokenManager_DynamicTokenVariables_Entities_Activity extends CRM_TokenManager_DynamicTokenVariables_Entities_Base {

  /**
   * @return string[]
   */
  public static function getMethodVariableMap(): array {
    return [
      'activityCaseId' => 'getActivityCaseId',
    ];
  }

  /**
   * @param $activityId
   *
   * @return null|int
   */
  public function getActivityCaseId($activityId) {
    if (empty($activityId)) {
      return NULL;
    }

    $caseActivity = \Civi\Api4\CaseActivity::get()
      ->addSelect('case_id')
      ->addWhere('activity_id', '=', $activityId)
      ->setLimit(1)
      ->execute()
      ->first();

    if (empty($caseActivity)) {
      return NULL;
    }

    return $caseActivity['case_id'];
  }

}
