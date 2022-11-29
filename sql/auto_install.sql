-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the existing tables - this section generated from drop.tpl
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_dynamic_token`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * civicrm_dynamic_token
-- *
-- *******************************************************/
CREATE TABLE `civicrm_dynamic_token` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique DynamicToken ID',
  `entity_name` varchar(255) NOT NULL COMMENT 'Entity name of the token',
  `field_name` varchar(255) NOT NULL COMMENT 'Field name of the token',
  `smarty_variable_name` varchar(255) NULL COMMENT 'Optional Smarty variable name of the token',
  `value` varchar(1000) NOT NULL COMMENT 'Value of the Token',
  `description` varchar(1000) NULL COMMENT 'Description of the Token',
  PRIMARY KEY (`id`)
)
ENGINE=InnoDB;
