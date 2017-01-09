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

class SumTimeProject
{

	public $name = 'FL_TOTAL_TIME_H';
	public $sequence = 9;
	public $reference = 'OSSTimeControl';

	public function process($instance)
	{
		
		\App\Log::trace("Entering SumTimeProject::process() method ...");
		$sum_time = vtlib\Functions::decimalTimeFormat($instance->get('sum_time'));
		\App\Log::trace("Exiting SumTimeProject::process() method ...");
		return $sum_time['short'];
	}
}
