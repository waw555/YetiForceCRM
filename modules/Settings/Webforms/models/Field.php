<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Settings_Webforms_Field_Model extends Vtiger_Field_Model
{

	/**
	 * Function get field is viewable or not
	 * @return boolean true/false
	 */
	public function isViewable()
	{
		return true;
	}

	/**
	 * Function to get instance of Field by using array of data
	 * @param <Array> $rowData
	 * @return <Settings_Webforms_Field_Model> FieldModel
	 */
	static public function getInstanceByRow($rowData)
	{
		$fieldModel = new self();
		foreach ($rowData as $name => $value) {
			$fieldModel->set($name, $value);
		}
		return $fieldModel;
	}

	/**
	 * Function to check whether this field editable or not
	 * @return boolean true/false
	 */
	public function isEditable()
	{
		if (($this->getName() === 'publicid') || ($this->getName() === 'posturl')) {
			return false;
		}
		return true;
	}

	public function isReadOnly()
	{
		if ($this->getName() === 'name') {
			return $this->get('readonly');
		}
		return false;
	}

	/**
	 * Function to get the value of a given property
	 * @param string $propertyName
	 * @return <Object>
	 * @throws Exception
	 */
	public function get($propertyName)
	{
		if ($propertyName == 'fieldvalue' && $this->name == 'roundrobin_userid') {
			$value = str_replace('&quot;', '"', $this->$propertyName);
			return json_decode($value, true);
		}
		return parent::get($propertyName);
	}

	/**
	 * Function to get Picklist values
	 * @return <Array> Picklist values
	 */
	public function getPicklistValues($skipCheckingRole = false)
	{
		if ($this->getName() === 'targetmodule') {
			return Settings_Webforms_Module_Model::getsupportedModulesList();
		}
		return array();
	}

	public function getDisplayValue($value, $record = false, $recordInstance = false, $rawText = false)
	{
		if ($this->getName() === 'enabled') {
			$moduleName = 'Settings:Webforms';
			if ($value) {
				return vtranslate('LBL_ACTIVE', $moduleName);
			}
			return vtranslate('LBL_INACTIVE', $moduleName);
		}
		return parent::getDisplayValue($value, $record, $recordInstance, $rawText);
	}

	public function getPermissions($readOnly = true)
	{
		return true;
	}

	/**
	 * Function which will check if empty piclist option should be given
	 */
	public function isEmptyPicklistOptionAllowed()
	{
		return false;
	}
}
