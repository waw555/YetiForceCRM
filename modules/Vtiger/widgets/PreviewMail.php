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

class Vtiger_PreviewMail_Widget extends Vtiger_Basic_Widget
{

	public $allowedModules = ['PreviewMail'];
	public $dbParams = array('relatedmodule' => 'Emails');

	public function getUrl()
	{
		return 'module=' . $this->Module . '&view=sview&noloadlibs=true&record=' . $this->Record;
	}

	public function getWidget()
	{
		$this->Config['url'] = $this->getUrl();
		return $this->Config;
	}

	public function getConfigTplName()
	{
		return 'PreviewMailConfig';
	}
}
