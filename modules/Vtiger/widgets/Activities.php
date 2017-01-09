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

class Vtiger_Activities_Widget extends Vtiger_Basic_Widget
{

	public $allowedModules = ['Accounts', 'Leads', 'Contacts', 'Vendors', 'OSSEmployees', 'Campaigns', 'HelpDesk', 'Project', 'ServiceContracts', 'SSalesProcesses', 'SQuoteEnquiries', 'SRequirementsCards', 'SCalculations', 'SQuotes', 'SSingleOrders', 'SRecurringOrders'];

	public function getUrl()
	{
		return 'module=' . $this->Module . '&view=Detail&record=' . $this->Record . '&mode=getActivities&page=1&type=current&limit=' . $this->Data['limit'];
	}

	public function getConfigTplName()
	{
		return 'ActivitiesConfig';
	}

	public function getWidget()
	{
		$widget = [];
		$model = Vtiger_Module_Model::getInstance('Calendar');
		if ($model->isPermitted('DetailView')) {
			$this->Config['url'] = $this->getUrl();
			$this->Config['tpl'] = 'Activities.tpl';
			$widget = $this->Config;
		}
		return $widget;
	}
}
