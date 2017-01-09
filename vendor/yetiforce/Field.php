<?php
namespace App;

/**
 * Field basic class
 * @package YetiForce.App
 * @license licenses/License.html
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
class Field
{

	/**
	 * Function gets the list of fields that the user has permissions to
	 * @param int $tabId Module ID
	 * @param type $readOnly Read/preview only fields
	 * @return array
	 */
	public static function getFieldsPermissions($tabId, $readOnly = true)
	{
		Log::trace('Entering ' . __METHOD__ . ": $tabId");
		if (Cache::has(__METHOD__ . User::getCurrentUserId(), $tabId)) {
			$fields = Cache::get(__METHOD__ . User::getCurrentUserId(), $tabId);
		} else {
			$query = (new \App\Db\Query())
				->select('vtiger_field.*, vtiger_profile2field.readonly,vtiger_profile2field.visible')
				->from('vtiger_field')
				->innerJoin('vtiger_profile2field', 'vtiger_profile2field.fieldid = vtiger_field.fieldid')
				->innerJoin('vtiger_def_org_field', 'vtiger_def_org_field.fieldid = vtiger_field.fieldid')
				->where([
					'vtiger_field.tabid' => (int) $tabId,
					'vtiger_profile2field.visible' => 0,
					'vtiger_def_org_field.visible' => 0,
					'vtiger_field.presence' => [0, 2]])
				->groupBy('vtiger_field.fieldid,vtiger_profile2field.readonly,vtiger_profile2field.visible');
			$profileList = \App\User::getCurrentUserModel()->getProfiles();
			if ($profileList) {
				$query->andWhere(['vtiger_profile2field.profileid' => $profileList]);
			}
			$fields = $query->all();
			Cache::save(__METHOD__ . User::getCurrentUserId(), $tabId, $fields);
		}
		if ($readOnly) {
			return $fields;
		}
		foreach ($fields as $key => &$field) {
			if ($field['readonly']) {
				unset($fields[$key]);
			}
		}
		return $fields;
	}

	private static $fieldPermissionCacheRead = [];
	private static $fieldPermissionCacheWrite = [];

	/**
	 * Function checks field permissions by field name or field id
	 * @param int|string $tabMix Module ID or module name
	 * @param int|string $fieldMix Field ID or field name
	 * @param boolean $readOnly Read/preview only fields
	 * @return boolean
	 */
	public static function getFieldPermission($tabMix, $fieldMix, $readOnly = true)
	{
		$tabId = $tabMix;
		if (!is_numeric($tabMix)) {
			$tabId = Module::getModuleId($tabMix);
		}
		Log::trace('Entering ' . __METHOD__ . ": $tabId,$fieldMix");
		if ($readOnly && isset(static::$fieldPermissionCacheRead[$tabId][$fieldMix])) {
			return static::$fieldPermissionCacheRead[$tabId][$fieldMix];
		} elseif (!$readOnly && isset(static::$fieldPermissionCacheWrite[$tabId][$fieldMix])) {
			return static::$fieldPermissionCacheWrite[$tabId][$fieldMix];
		}
		$fields = static::getFieldsPermissions($tabId, $readOnly);
		$key = is_numeric($fieldMix) ? 'fieldid' : 'fieldname';
		foreach ($fields as &$field) {
			if ($field[$key] === $fieldMix) {
				$permission = !($field['visible']);
				if ($readOnly) {
					static::$fieldPermissionCacheRead[$tabId][$fieldMix] = $permission;
					static::$columnPermissionCacheRead[$tabId][$field['columnname']] = $permission;
				} else {
					static::$fieldPermissionCacheWrite[$tabId][$fieldMix] = $permission;
					static::$columnPermissionCacheWrite[$tabId][$field['columnname']] = $permission;
				}
				return $permission;
			}
		}
		if ($readOnly) {
			static::$fieldPermissionCacheRead[$tabId][$fieldMix] = false;
		} else {
			static::$fieldPermissionCacheWrite[$tabId][$fieldMix] = false;
		}
		return false;
	}

	private static $columnPermissionCacheRead = [];
	private static $columnPermissionCacheWrite = [];

	/**
	 * Function checks field permissions by column name
	 * @param int|string $tabMix Module ID or module name
	 * @param string $columnName Field ID or field name
	 * @param boolean $readOnly Read/preview only fields
	 * @return boolean
	 */
	public static function getColumnPermission($tabMix, $columnName, $readOnly = true)
	{
		$tabId = $tabMix;
		if (!is_numeric($tabMix)) {
			$tabId = Module::getModuleId($tabMix);
		}
		Log::trace('Entering ' . __METHOD__ . ": $tabId,$columnName");
		if ($readOnly && isset(static::$columnPermissionCacheRead[$tabId][$columnName])) {
			return static::$columnPermissionCacheRead[$tabId][$columnName];
		} elseif (!$readOnly && isset(static::$columnPermissionCacheWrite[$tabId][$columnName])) {
			return static::$columnPermissionCacheWrite[$tabId][$columnName];
		}
		$fields = static::getFieldsPermissions($tabId, $readOnly);
		foreach ($fields as &$field) {
			if ($field['columnname'] === $columnName) {
				$permission = !($field['visible']);
				if ($readOnly) {
					static::$columnPermissionCacheRead[$tabId][$columnName] = $permission;
					static::$fieldPermissionCacheRead[$tabId][$field['fieldname']] = $permission;
				} else {
					static::$columnPermissionCacheWrite[$tabId][$columnName] = $permission;
					static::$fieldPermissionCacheWrite[$tabId][$field['fieldname']] = $permission;
				}
				return $permission;
			}
		}
		if ($readOnly) {
			static::$columnPermissionCacheRead[$tabId][$columnName] = false;
		} else {
			static::$columnPermissionCacheWrite[$tabId][$columnName] = false;
		}
		return false;
	}

	/**
	 * Get field module relation
	 * @param string $moduleName
	 * @param string|boolean $relatedModule
	 * @return array
	 */
	public static function getFieldModuleRel($moduleName, $relatedModule = false)
	{
		if (Cache::has('getFieldModuleRelByModule', $moduleName)) {
			$filedsRel = Cache::get('getFieldModuleRelByModule', $moduleName);
		} else {
			$filedsRel = (new \App\Db\Query())->from('vtiger_fieldmodulerel')->where(['module' => $moduleName])->all();
			Cache::save('getFieldModuleRelByModule', $moduleName, $filedsRel, Cache::LONG);
		}
		if ($relatedModule) {
			foreach ($filedsRel as &$filedRel) {
				if ($filedRel['relmodule'] === $relatedModule) {
					return $filedRel;
				}
			}
		}
		return $filedsRel;
	}

	/**
	 * Get fields from relation by relation Id
	 * @param int $relationId
	 * @return string[]
	 */
	public static function getFieldsFromRelation($relationId)
	{
		if (empty($relationId)) {
			return [];
		}
		if (Cache::has('getFieldsFromRelation', $relationId)) {
			$fields = Cache::get('getFieldsFromRelation', $relationId);
		} else {
			$fields = (new \App\Db\Query())->select(['fieldname'])->from('vtiger_relatedlists_fields')
					->where(['relation_id' => $relationId])->column();
			Cache::save('getFieldsFromRelation', $relationId, $fields, Cache::LONG);
		}
		return $fields;
	}
}
