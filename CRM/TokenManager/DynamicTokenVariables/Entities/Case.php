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
    $relationships = \Civi\Api4\Relationship::get(FALSE)
      ->addWhere('case_id', '=', $caseId)
      ->execute();

    $contactIds = [];

    foreach ($relationships as $relationship) {
      $contactIds[] = (int) $relationship['contact_id_b'];
      $contactIds[] = (int) $relationship['contact_id_a'];
    }

    $contactIds = array_unique($contactIds);

    try {
      $contacts = civicrm_api3('Contact', 'get', [
        'sequential' => 1,
        'return' => ["id", "display_name", "addressee_display", "email", "phone", "last_name", "first_name"],
        'id' => ['IN' => $contactIds],
        'options' => ['limit' => 0],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    $relatedContacts = [];

    foreach ($contacts['values'] as $contact) {
      $relatedContacts[] = [
        'contact_id' => $contact['contact_id'],
        'display_name' => $contact['display_name'],
        'first_name' => !empty($contact['first_name']) ? $contact['first_name'] : '',
        'last_name' => !empty($contact['last_name']) ? $contact['last_name'] : '',
        'addressee_display' => !empty($contact['addressee_display']) ? $contact['addressee_display'] : '',
        'email' => !empty($contact['email']) ? $contact['email'] : '',
        'phone' => !empty($contact['phone']) ? $contact['phone'] : '',
      ];
    }

    return $relatedContacts;
  }


}
