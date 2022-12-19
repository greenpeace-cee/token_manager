<?php

use Civi\Api4\DynamicToken;
use CRM_TokenManager_ExtensionUtil as E;

class CRM_TokenManager_Page_DynamicTokens extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('Dynamic Tokens'));
    $preparedDynamicTokens = [];
    $dynamicTokens = DynamicToken::get()->setLimit(0)->execute();

    foreach ($dynamicTokens as $dynamicToken) {
      $api4Params = 'where=' . urlencode('[["id"') . ',' . urlencode('"="') . ','. urlencode('"' . $dynamicToken['id'] . '"]]');

      $preparedDynamicTokens[] = [
        'id' => $dynamicToken['id'],
        'entity_name' => $dynamicToken['entity_name'],
        'field_name' => $dynamicToken['field_name'],
        'smarty_variable_name' => $dynamicToken['smarty_variable_name'],
        'value' => $dynamicToken['value'],
        'description' => $dynamicToken['description'],
        'update_api_link' => $this->getLink('civicrm/api4#/explorer/DynamicToken/update', $api4Params),
        'get_api_link' => $this->getLink('civicrm/api4#/explorer/DynamicToken/get', $api4Params),
        'delete_api_link' => $this->getLink('civicrm/api4#/explorer/DynamicToken/delete', $api4Params),
      ];
    }

    $this->assign('tokens', $preparedDynamicTokens);
    $this->assign('availableVariables', CRM_TokenManager_DynamicTokenVariables_Manager::getAvailableVariables());
    parent::run();
  }

  private function getLink($path, $api4Params) {
    return CRM_Utils_System::url(
      $path . '?' . $api4Params,
      '',
      NULL,
      NULL,
      FALSE,
      TRUE,
      FALSE
    );
  }

}
