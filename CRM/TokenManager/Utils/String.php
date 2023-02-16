<?php

class CRM_TokenManager_Utils_String {

  /**
   * @param $searchByString
   * @param $targetString
   * @return bool
   */
  public static function isStringContains($searchByString, $targetString): bool {
    $pos = strpos($targetString, $searchByString);

    if ($pos === false) {
      return false;
    }

    return true;
  }

}
