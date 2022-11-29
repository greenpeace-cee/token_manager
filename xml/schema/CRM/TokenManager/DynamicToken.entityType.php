<?php
// This file declares a new entity type. For more details, see "hook_civicrm_entityTypes" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
return [
  [
    'name' => 'DynamicToken',
    'class' => 'CRM_TokenManager_DAO_DynamicToken',
    'table' => 'civicrm_dynamic_token',
  ],
];
