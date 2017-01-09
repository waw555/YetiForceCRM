<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): YetiForce.com
 * ********************************************************************************** */

class Settings_Vtiger_CompanyDetailsFieldSave_Action extends Settings_Vtiger_Basic_Action
{

	public function process(Vtiger_Request $request)
	{

		Settings_Vtiger_CompanyDetails_Model::addNewField($request);
		\App\Log::trace('Settings_Vtiger_CompanyFieldSave_Action::process - Add field started');
	}

	public static function addFieldToModule($field)
	{


		$fileName = 'modules/Settings/Vtiger/models/CompanyDetails.php';
		$fileExists = file_exists($fileName);
		if ($fileExists) {
			require_once($fileName);
			$fileContent = file_get_contents($fileName);
			$placeToAdd = "'website' => 'text',";
			$newField = "'$field' => 'text',";
			if (self::parse_data($placeToAdd, $fileContent)) {
				$fileContent = str_replace($placeToAdd, $placeToAdd . PHP_EOL . '	' . $newField, $fileContent);
			} else {
				if (self::parse_data('', $fileContent)) {
					$fileContent = str_replace('', '', $fileContent);
				}
				$fileContent = $fileContent . PHP_EOL . $placeToAdd . PHP_EOL . '	' . $newField . PHP_EOL . ');';
			}
			\App\Log::trace('Settings_Vtiger_SaveCompanyField_Action::addFieldToModule - add line to modules/Settings/Vtiger/models/CompanyDetails.php ');
		} else {
			\App\Log::trace('Settings_Vtiger_SaveCompanyField_Action::addFieldToModule - File does not exist');
			return false;
		}

		$filePointer = fopen($fileName, 'w');
		fwrite($filePointer, $fileContent);
		fclose($filePointer);

		return true;
	}

	public static function parse_data($a, $b)
	{
		$resp = false;
		if ($b != '' && strstr($b, $a) !== false) {
			$resp = true;
		}
		return $resp;
	}
}
