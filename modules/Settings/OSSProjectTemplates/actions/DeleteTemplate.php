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

class Settings_OSSProjectTemplates_DeleteTemplate_Action extends Settings_Vtiger_Index_Action
{

	public function process(Vtiger_Request $request)
	{
		$baseModuleName = $request->get('base_module');
		$idTpl = $request->get('tpl_id');
		\App\Db::getInstance()->createCommand()
			->delete('vtiger_oss_project_templates', ['or', ['parent' => $idTpl], ['id_tpl' => $idTpl]])
			->execute();
		$backView = $request->get('back_view');
		$backIdTpl = $request->get('parent_tpl_id');
		header("Location: index.php?module=OSSProjectTemplates&parent=Settings&&view=" . $backView . '&tpl_id=' . $backIdTpl);
	}
}
