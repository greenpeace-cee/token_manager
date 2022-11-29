<?php
// phpcs:disable
use CRM_TokenManager_ExtensionUtil as E;
// phpcs:enable

class CRM_TokenManager_BAO_DynamicToken extends CRM_TokenManager_DAO_DynamicToken {

  /**
   * Create a new DynamicToken based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_TokenManager_DAO_DynamicToken|NULL
   */
  /*
  public static function create($params) {
    $className = 'CRM_TokenManager_DAO_DynamicToken';
    $entityName = 'DynamicToken';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }
  */

}
