<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Import_Queue_Action extends Vtiger_Action_Controller
{

	static $IMPORT_STATUS_NONE = 0;
	static $IMPORT_STATUS_SCHEDULED = 1;
	static $IMPORT_STATUS_RUNNING = 2;
	static $IMPORT_STATUS_HALTED = 3;
	static $IMPORT_STATUS_COMPLETED = 4;

	public function __construct()
	{
		
	}

	public function process(Vtiger_Request $request)
	{
		return;
	}

	public static function add($request, $user)
	{
		$db = PearDatabase::getInstance();

		if (!vtlib\Utils::CheckTable('vtiger_import_queue')) {
			vtlib\Utils::CreateTable(
				'vtiger_import_queue', "(importid INT NOT NULL PRIMARY KEY,
								userid INT NOT NULL,
								tabid INT NOT NULL,
								field_mapping TEXT,
								default_values TEXT,
								merge_type INT,
								merge_fields TEXT,
								type tinyint(1),
								temp_status INT default 0)", true);
		}

		if ($request->get('is_scheduled')) {
			$temp_status = self::$IMPORT_STATUS_SCHEDULED;
		} else {
			$temp_status = self::$IMPORT_STATUS_NONE;
		}

		$db->pquery('INSERT INTO vtiger_import_queue VALUES(?,?,?,?,?,?,?,?,?)', array($db->getUniqueID('vtiger_import_queue'),
			$user->id,
			\App\Module::getModuleId($request->get('module')),
			\App\Json::encode($request->get('field_mapping')),
			\App\Json::encode($request->get('default_values')),
			$request->get('merge_type'),
			\App\Json::encode($request->get('merge_fields')),
			$request->get('createRecordsByModel'),
			$temp_status));
	}

	public static function remove($importId)
	{
		$db = PearDatabase::getInstance();
		if (vtlib\Utils::CheckTable('vtiger_import_queue')) {
			$db->pquery('DELETE FROM vtiger_import_queue WHERE importid=?', array($importId));
		}
	}

	public static function removeForUser($user)
	{
		$db = PearDatabase::getInstance();
		if (vtlib\Utils::CheckTable('vtiger_import_queue')) {
			$db->pquery('DELETE FROM vtiger_import_queue WHERE userid=?', array($user->id));
		}
	}

	public static function getUserCurrentImportInfo($user)
	{
		$db = PearDatabase::getInstance();

		if (vtlib\Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $db->pquery('SELECT * FROM vtiger_import_queue WHERE userid=? LIMIT 1', array($user->id));

			if ($queueResult && $db->num_rows($queueResult) > 0) {
				$rowData = $db->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getImportInfo($module, $user)
	{
		$db = PearDatabase::getInstance();

		if (vtlib\Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $db->pquery('SELECT * FROM vtiger_import_queue WHERE tabid=? && userid=?', array(\App\Module::getModuleId($module), $user->id));

			if ($queueResult && $db->num_rows($queueResult) > 0) {
				$rowData = $db->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getImportInfoById($importId)
	{
		$db = PearDatabase::getInstance();

		if (vtlib\Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $db->pquery('SELECT * FROM vtiger_import_queue WHERE importid=?', array($importId));

			if ($queueResult && $db->num_rows($queueResult) > 0) {
				$rowData = $db->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getAll($temp_status = false)
	{
		$db = PearDatabase::getInstance();

		$query = 'SELECT * FROM vtiger_import_queue';
		$params = array();
		if ($temp_status !== false) {
			$query .= ' WHERE temp_status = ?';
			array_push($params, $temp_status);
		}
		$result = $db->pquery($query, $params);

		$noOfImports = $db->num_rows($result);
		$scheduledImports = array();
		for ($i = 0; $i < $noOfImports; ++$i) {
			$rowData = $db->raw_query_result_rowdata($result, $i);
			$scheduledImports[$rowData['importid']] = self::getImportInfoFromResult($rowData);
		}
		return $scheduledImports;
	}

	static function getImportInfoFromResult($rowData)
	{
		return [
			'id' => $rowData['importid'],
			'module' => \App\Module::getModuleName($rowData['tabid']),
			'field_mapping' => \App\Json::decode($rowData['field_mapping']),
			'default_values' => \App\Json::decode($rowData['default_values']),
			'merge_type' => $rowData['merge_type'],
			'merge_fields' => \App\Json::decode($rowData['merge_fields']),
			'user_id' => $rowData['userid'],
			'type' => $rowData['type'],
			'temp_status' => $rowData['temp_status']
		];
	}

	static function updateStatus($importId, $temp_status)
	{
		$db = PearDatabase::getInstance();
		$db->pquery('UPDATE vtiger_import_queue SET temp_status=? WHERE importid=?', array($temp_status, $importId));
	}
}
