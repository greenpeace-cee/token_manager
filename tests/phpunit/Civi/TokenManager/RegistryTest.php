<?php
namespace Civi\TokenManager;

use CRM_TokenManager_ExtensionUtil as E;
use Civi\Test\CiviEnvBuilder;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class RegistryTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  /**
   * Setup used when HeadlessInterface is implemented.
   *
   * Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
   *
   * @link https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
   *
   * @return \Civi\Test\CiviEnvBuilder
   *
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless(): CiviEnvBuilder {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp():void {
    parent::setUp();
  }

  public function tearDown():void {
    parent::tearDown();
  }

  public function testTokenReplaced() {
    \Civi\Api4\DynamicToken::create(FALSE)
      ->addValue('entity_name', 'foo')
      ->addValue('field_name', 'bar')
      ->addValue('value', 'Hello {capture assign="gender"}{contact.gender}{/capture}{if $gender == "Female"}Ms.{/if} {contact.first_name}!')
      ->execute();

    $contact = \Civi\Api4\Contact::create(FALSE)
      ->addValue('contact_type:name', 'Individual')
      ->addValue('first_name', 'Jane')
      ->addValue('last_name', 'Doe')
      ->addValue('gender_id:name', 'Female')
      ->execute()
      ->single();

    $tokenProcessor = new \Civi\Token\TokenProcessor(\Civi::dispatcher(), [
      'controller' => __CLASS__,
      'smarty' => FALSE,
      'schema' => ['contactId'],
    ]);
    $tokenProcessor->addMessage('body_text', '{foo.bar}', 'text/plain');
    $tokenProcessor->addRow(['contactId' => $contact['id']]);
    $tokenProcessor->evaluate();
    foreach ($tokenProcessor->getRows() as $row) {
      $this->assertEquals('Hello Ms. Jane!', $row->render('body_text'));
    }
  }

}
