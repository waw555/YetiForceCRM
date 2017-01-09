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

class OSSProjectTemplates_GenerateFromWidgetInProject_Action extends Vtiger_Action_Controller
{

	public function checkPermission(Vtiger_Request $request)
	{
		return;
	}

	public function process(Vtiger_Request $request)
	{
		require_once 'modules/OSSProjectTemplates/helpers/GenerateRecords.php';

		$id = $request->get('id_tpl');
		$relId = $request->get('rel_id');

		$generator = new GenerateRecords();
		$generator->generate($id, $relId);

		header("Location: index.php?module=Project&view=List");
	}
}
