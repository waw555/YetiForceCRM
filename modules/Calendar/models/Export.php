<?php
/* {[The file is published on the basis of YetiForce Public License that can be found in the following directory: licenses/License.html]} */
vimport('modules.Calendar.iCal.iCalendar_rfc2445');
vimport('modules.Calendar.iCal.iCalendar_components');
vimport('modules.Calendar.iCal.iCalendar_properties');
vimport('modules.Calendar.iCal.iCalendar_parameters');

class Calendar_Export_Model extends Vtiger_Export_Model
{

	/**
	 * Function that generates Export Query based on the mode
	 * @param Vtiger_Request $request
	 * @return string export query
	 */
	public function getExportQuery(Vtiger_Request $request)
	{
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		return $moduleModel->getExportQuery('', '');
	}

	/**
	 * Function returns the export type - This can be extended to support different file exports
	 * @param Vtiger_Request $request
	 * @return string
	 */
	public function getExportContentType()
	{
		return 'text/calendar';
	}

	/**
	 * Function exports the data based on the mode
	 * @param Vtiger_Request $request
	 */
	public function exportData(Vtiger_Request $request)
	{
		$moduleModel = Vtiger_Module_Model::getInstance($request->getModule());
		$moduleModel->setEventFieldsForExport();
		$moduleModel->setTodoFieldsForExport();

		$query = $this->getExportQuery($request);
		$fileName = $request->get('filename');
		$this->output($request, $query->createCommand()->query(), $moduleModel, $fileName);
	}

	/**
	 * Function that create the exported file
	 * @param Vtiger_Request $request
	 * @param <Array> $dataReader
	 * @param Vtiger_Module_Model $moduleModel
	 */
	public function output($request, $dataReader, $moduleModel, $fileName, $toFile = false)
	{
		$adb = PearDatabase::getInstance();
		$timeZone = new iCalendar_timezone;
		$timeZoneId = explode('/', date_default_timezone_get());

		if (!empty($timeZoneId[1])) {
			$zoneId = $timeZoneId[1];
		} else {
			$zoneId = $timeZoneId[0];
		}

		$timeZone->add_property('TZID', $zoneId);
		$timeZone->add_property('TZOFFSETTO', date('O'));

		if (date('I') == 1) {
			$timeZone->add_property('DAYLIGHTC', date('I'));
		} else {
			$timeZone->add_property('STANDARDC', date('I'));
		}

		$myiCal = new iCalendar;
		$myiCal->add_component($timeZone);

		while ($row = $dataReader->read()) {
			$eventFields = $row;
			$id = $eventFields['activityid'];
			$type = $eventFields['activitytype'];
			if ($type != 'Task') {
				$temp = $moduleModel->get('eventFields');
				foreach ($temp as $fieldName => $access) {
					/* Priority property of ical is Integer
					 * http://kigkonsult.se/iCalcreator/docs/using.html#PRIORITY
					 */
					if ($fieldName == 'priority') {
						$priorityMap = array('High' => '1', 'Medium' => '2', 'Low' => '3');
						$priorityval = $eventFields[$fieldName];
						$icalZeroPriority = 0;
						if (array_key_exists($priorityval, $priorityMap))
							$temp[$fieldName] = $priorityMap[$priorityval];
						else
							$temp[$fieldName] = $icalZeroPriority;
					}
					else {
						$temp[$fieldName] = $eventFields[$fieldName];
					}
				}
				$temp['id'] = $id;

				$iCalTask = new iCalendar_event;
				$iCalTask->assign_values($temp);

				$iCalAlarm = new iCalendar_alarm;
				$iCalAlarm->assign_values($temp);
				$iCalTask->add_component($iCalAlarm);
			} else {
				$temp = $moduleModel->get('todoFields');
				foreach ($temp as $fieldName => $access) {
					if ($fieldName == 'priority') {
						$priorityMap = array('High' => '1', 'Medium' => '2', 'Low' => '3');
						$priorityval = $eventFields[$fieldName];
						$icalZeroPriority = 0;
						if (array_key_exists($priorityval, $priorityMap))
							$temp[$fieldName] = $priorityMap[$priorityval];
						else
							$temp[$fieldName] = $icalZeroPriority;
					} else
						$temp[$fieldName] = $eventFields[$fieldName];
				}
				$iCalTask = new iCalendar_todo;
				$iCalTask->assign_values($temp);
			}
			$myiCal->add_component($iCalTask);
		}
		if ($toFile) {
			return $myiCal->serialize();
		} else {
			$exportType = $this->getExportContentType();
			// Send the right content type and filename
			header("Content-type: $exportType");
			header("Content-Disposition: attachment; filename={$fileName}.ics");
			echo $myiCal->serialize();
		}
	}
}
