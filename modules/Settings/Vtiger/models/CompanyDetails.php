<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Settings_Vtiger_CompanyDetails_Model extends Settings_Vtiger_Module_Model
{

	STATIC $logoSupportedFormats = array('jpeg', 'jpg', 'png', 'gif', 'pjpeg', 'x-png');
	public $baseTable = 'vtiger_organizationdetails';
	public $baseIndex = 'organization_id';
	public $listFields = array('organizationname');
	public $nameFields = array('organizationname');
	public $logoPath = 'storage/Logo/';
	public $fields = array(
		'organizationname' => 'text',
		'logoname' => 'text',
		'logo' => 'file',
		'panellogoname' => 'text',
		'panellogo' => 'file',
		'address' => 'textarea',
		'city' => 'text',
		'state' => 'text',
		'code' => 'text',
		'country' => 'text',
		'phone' => 'text',
		'fax' => 'text',
		'email' => 'text',
		'website' => 'text',
		'vatid' => 'text',
		'id1' => 'text',
		'id2' => 'text',
		'height_panellogo' => 'text',
	);
	public $heights = array(
		'256', '192', '128', '96', '64', '32', '16'
	);

	/**
	 * Function to get Edit view Url
	 * @return string Url
	 */
	public function getEditViewUrl()
	{
		return 'index.php?module=Vtiger&parent=Settings&view=CompanyDetailsEdit';
	}

	/**
	 * Function to get CompanyDetails Menu item
	 * @return menu item Model
	 */
	public function getMenuItem()
	{
		$menuItem = Settings_Vtiger_MenuItem_Model::getInstance('LBL_COMPANY_DETAILS');
		return $menuItem;
	}

	/**
	 * Function to get Index view Url
	 * @return string URL
	 */
	public function getIndexViewUrl()
	{
		$menuItem = $this->getMenuItem();
		return 'index.php?module=Vtiger&parent=Settings&view=CompanyDetails&block=' . $menuItem->get('blockid') . '&fieldid=' . $menuItem->get('fieldid');
	}

	/**
	 * Function to get fields
	 * @return <Array>
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * Function to get heights
	 * @return <Array>
	 */
	public function getHeights()
	{
		return $this->heights;
	}

	/**
	 * Function to get Logo path to display
	 * @return string path
	 */
	public function getLogoPath($name)
	{
		$logoPath = $this->logoPath;
		$handler = @opendir($logoPath);
		$logoName = $this->get($name);
		if ($logoName && $handler) {
			while ($file = readdir($handler)) {
				if ($logoName === $file && in_array(str_replace('.', '', strtolower(substr($file, -4))), self::$logoSupportedFormats) && $file != "." && $file != "..") {
					closedir($handler);
					return $logoPath . $logoName;
				}
			}
		}
		return '';
	}

	/**
	 * Function to save the logoinfo
	 */
	public function saveLogo($name)
	{
		$uploadDir = ROOT_DIRECTORY . DIRECTORY_SEPARATOR . $this->logoPath;
		$logoName = $uploadDir . \App\Fields\File::sanitizeUploadFileName($_FILES[$name]['name']);
		move_uploaded_file($_FILES[$name]['tmp_name'], $logoName);
		copy($logoName, $uploadDir . 'application.ico');
	}

	/**
	 * Function to save the Company details
	 */
	public function save()
	{
		$db = PearDatabase::getInstance();
		$id = $this->get('id');
		$fieldsList = $this->getFields();
		unset($fieldsList['logo']);
		$tableName = $this->baseTable;

		if ($id) {
			$params = array();

			$query = "UPDATE $tableName SET ";
			foreach ($fieldsList as $fieldName => $fieldType) {
				$query .= " $fieldName = ?, ";
				array_push($params, $this->get($fieldName));
			}
			$query .= " logo = NULL WHERE organization_id = ?";

			array_push($params, $id);
		} else {
			$params = $this->getData();

			$query = "INSERT INTO $tableName (";
			foreach ($fieldsList as $fieldName => $fieldType) {
				$query .= " $fieldName,";
			}
			$query .= " organization_id) VALUES (" . generateQuestionMarks($params) . ", ?)";

			array_push($params, $db->getUniqueID($this->baseTable));
		}
		\App\Cache::staticDelete('organizationDetails', $id);
		$db->pquery($query, $params);
	}

	/**
	 * Function to get the instance of Company details module model
	 * @return <Settings_Vtiger_CompanyDetais_Model> $moduleModel
	 */
	public static function getInstance($name = 'Settings:Vtiger')
	{
		$moduleModel = new self();
		$db = PearDatabase::getInstance();

		$result = $db->pquery("SELECT * FROM vtiger_organizationdetails", array());
		if ($db->num_rows($result) == 1) {
			$moduleModel->setData($db->query_result_rowdata($result));
			$moduleModel->set('id', $moduleModel->get('organization_id'));
		}

		$moduleModel->getFields();
		return $moduleModel;
	}

	/**
	 * @var array(string => string) 
	 */
	private static $settings = [];

	/**
	 * @param string $fieldname 
	 * @return string 
	 */
	public static function getSetting($fieldname)
	{
		$db = PearDatabase::getInstance();
		if (!self::$settings) {
			self::$settings = $db->getRow($db->query('SELECT * FROM vtiger_organizationdetails'));
		}
		return self::$settings[$fieldname];
	}

	public static function addNewField(Vtiger_Request $request)
	{
		$db = App\Db::getInstance();
		$newField = self::newFieldValidation($request->get('fieldName'));
		$response = new Vtiger_Response();
		$moduleName = 'Settings:' . $request->getModule();
		if ($newField != false) {
			$tableSchema = $db->getTableSchema('vtiger_organizationdetails', true);
			$columnNames = $tableSchema->getColumnNames();
			if (in_array($newField, $columnNames)) {
				$response->setResult(array('success' => false, 'message' => vtranslate('LBL_ADDING_ERROR', $moduleName)));
				\App\Log::trace("Settings_Vtiger_SaveCompanyField_Action::process - column $newField exist in table vtiger_organizationdetails");
			} else {
				$db->createCommand()->addColumn('vtiger_organizationdetails', $newField, 'string(255)')->execute();
				Settings_Vtiger_CompanyDetailsFieldSave_Action::addFieldToModule($newField);
				$response->setResult(array('success' => true, 'message' => vtranslate('LBL_ADDED_COMPANY_FIELD', $moduleName)));
				\App\Log::trace("Settings_Vtiger_SaveCompanyField_Action::process - add column $newField in table vtiger_organizationdetails");
			}
		} else {
			$response->setResult(array('success' => false, 'message' => vtranslate('LBL_FIELD_NOT_VALID', $moduleName)));
		}
		$response->emit();
	}

	public static function newFieldValidation($field)
	{
		$field = trim($field);
		$length = strlen($field);
		$field = str_replace(' ', '_', $field);
		$field = strtolower($field);
		if ('' == $field) {
			return false;
		}
		if (preg_match('/[^a-z_A-Z]+/', $field, $matches)) {
			return false;
		}
		if ($length > 25) {
			return false;
		}
		return $field;
	}
}
