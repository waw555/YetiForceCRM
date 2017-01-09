<?php
/* +***********************************************************************************************************************************
 * The contents of this file are subject to the YetiForce Public License Version 1.1 (the "License"); you may not use this file except
 * in compliance with the License.
 * Software distributed under the License is distributed on an "AS IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or implied.
 * See the License for the specific language governing rights and limitations under the License.
 * The Original Code is YetiForce.
 * The Initial Developer of the Original Code is YetiForce. Portions created by YetiForce are Copyright (C) www.yetiforce.com. 
 * All Rights Reserved.
 * *********************************************************************************************************************************** */

class Settings_OSSProjectTemplates_CreateTemplate_Action extends Settings_Vtiger_Index_Action
{

	public function process(Vtiger_Request $request)
	{

		$baseModuleName = $request->get('base_module');
		$db = App\Db::getInstance();
		$parentTplId = $request->get('parent_tpl_id');

		if (!$parentTplId) {
			$parentTplId = 0;
		}

		$settingsModuleModel = Settings_Vtiger_Module_Model::getInstance('Settings:OSSProjectTemplates');
		$fieldTab = $settingsModuleModel->getConfigurationForModule($baseModuleName);
		$fieldTab['tpl_name'] = '';
		$lastTplId = $this->getLastTplId($baseModuleName);
		$lastTplId++;

		if ($fieldTab && count($fieldTab)) {
			foreach ($fieldTab as $key => $value) {
				$valField = $request->get($key);
				$db->createCommand()->insert('vtiger_oss_project_templates', [
					'fld_name' => $key,
					'fld_val' => is_array($valField) ? App\Json::encode($valField) : $valField,
					'id_tpl' => $lastTplId,
					'parent' => $parentTplId,
					'module' => $baseModuleName
				])->execute();
				$dateDayInterval = $request->get($key . '_day');
				$dateDayIntervalType = $request->get($key . '_day_type');

				if ($dateDayInterval) {
					$db->createCommand()->insert('vtiger_oss_project_templates', [
						'fld_name' => $key . '_day',
						'fld_val' => $dateDayInterval,
						'id_tpl' => $lastTplId,
						'parent' => $parentTplId,
						'module' => $baseModuleName
					])->execute();
				}
				if ($dateDayIntervalType) {
					$db->createCommand()->insert('vtiger_oss_project_templates', [
						'fld_name' => $key . '_day_type',
						'fld_val' => $dateDayIntervalType,
						'id_tpl' => $lastTplId,
						'parent' => $parentTplId,
						'module' => $baseModuleName
					])->execute();
				}
			}
		}

		$backView = $request->get('back_view');
		$backIdTpl = $request->get('parent_tpl_id');

		header("Location: index.php?module=OSSProjectTemplates&parent=Settings&view=" . $backView . '&tpl_id=' . $backIdTpl);
	}

	public function getLastTplId($moduleName)
	{
		return (new \App\Db\Query())->select(['id_tpl'])
			->from('vtiger_oss_project_templates')
			->orderBy(['id_tpl' => SORT_DESC])
			->limit(1)->scalar();
	}
}
