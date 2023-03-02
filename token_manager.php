<?php

require_once 'token_manager.civix.php';
require_once __DIR__ . '/vendor/autoload.php';
// phpcs:disable
use CRM_TokenManager_ExtensionUtil as E;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function token_manager_civicrm_config(&$config): void {
  _token_manager_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function token_manager_civicrm_install(): void {
  _token_manager_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function token_manager_civicrm_enable(): void {
  _token_manager_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function token_manager_civicrm_entityTypes(&$entityTypes): void {
  _token_manager_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Add token services to the container.
 *
 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
 */
function token_manager_civicrm_container(ContainerBuilder $container) {
  $container->addResource(new FileResource(__FILE__));
  $container->findDefinition('dispatcher')->addMethodCall('addListener',
    ['civi.token.list', ['\Civi\TokenManager\Registry', 'register'], -100]
  )->setPublic(TRUE);
  $container->findDefinition('dispatcher')->addMethodCall('addListener',
    ['civi.token.eval', ['\Civi\TokenManager\Registry', 'evaluate'], -100]
  )->setPublic(TRUE);
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function token_manager_civicrm_navigationMenu(&$menu) {
  _token_manager_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
    'label' => E::ts('Custom token list'),
    'name' => 'civicrm_token-manager_dynamic_tokens',
    'url' => 'civicrm/token-manager/dynamic-tokens',
    'permission' => 'administer CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
    'icon' => 'crm-i fa-file-text',
  ]);

  _token_manager_civix_navigationMenu($menu);
}
