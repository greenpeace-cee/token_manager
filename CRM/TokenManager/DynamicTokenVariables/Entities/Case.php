<?php

class CRM_TokenManager_DynamicTokenVariables_Entities_Case extends CRM_TokenManager_DynamicTokenVariables_Entities_Base {

  /**
   * @return string[]
   */
  public static function getMethodVariableMap(): array {
    return [
      'caseRelationContacts' => 'getCaseRelationContacts',
    ];
  }

  public function getCaseRelationContacts($caseId): array {
    $relatedContactsData = [];

    $relationships = \Civi\Api4\Relationship::get(FALSE)
      ->addSelect('*', 'custom.*', 'contact.*', 'email.*', 'phone.*', 'relationship_type_id:name')
      ->addJoin('Contact AS contact', 'INNER', ['contact_id_a', '=', 'contact.id'])
      ->addJoin('Email AS email', 'LEFT', ['contact_id_a', '=', 'email.contact_id'], ['email.is_primary', '=', 1])
      ->addJoin('Phone AS phone', 'LEFT', ['contact_id_a', '=', 'phone.contact_id'], ['phone.is_primary', '=', 1])
      ->addWhere('case_id', '=', $caseId)
      ->addWhere('is_active', '=', TRUE)
      ->execute();

    foreach ($relationships as $item) {
      $preparedItem = [];
      foreach ($item as $fieldName => $value) {
        $newFieldName = str_replace(['.', ':'], '_', $fieldName);
        $preparedItem[$newFieldName] = $value;
      }

      $relatedContactsData[] = $preparedItem;
    }

    return $relatedContactsData;
  }


}
