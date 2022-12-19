<?php

class CRM_TokenManager_DynamicTokenVariables_Entities_Base {

  protected $entityId;
  protected $tokenValue;

  /**
   * @param $entityId
   * @param $tokenValue
   */
  public function __construct($entityId, $tokenValue) {
    $this->entityId = $entityId;
    $this->tokenValue = $tokenValue;
  }

  /**
   * @return array
   */
  public function getVariables(): array {
    $variables = [];

    $methodVariableMap = static::getMethodVariableMap();
    foreach ($methodVariableMap as $variableName => $methodName) {
      if (CRM_TokenManager_Utils_String::isStringContains('$' . $variableName, $this->tokenValue)) {
        $variables[$variableName] = $this->$methodName($this->entityId);
      }
    }

    return $variables;
  }

  /**
   * @return array
   */
  public static function getMethodVariableMap(): array {
    return [];
  }

}
