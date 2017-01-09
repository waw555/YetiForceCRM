<?php
namespace App;

/**
 * Text parser class
 * @package YetiForce.App
 * @license licenses/License.html
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
class TextParser
{

	/** @var array Examples of supported variables */
	public static $variableExamples = [
		'LBL_ORGANIZATION_NAME' => '$(organization : organizationname)$',
		'LBL_ORGANIZATION_LOGO' => '$(organization : mailLogo)$',
		'LBL_EMPLOYEE_NAME' => '$(employee : last_name)$',
		'LBL_CRM_DETAIL_VIEW_URL' => '$(record : CrmDetailViewURL)$',
		'LBL_PORTAL_DETAIL_VIEW_URL' => '$(record : PortalDetailViewURL)$',
		'LBL_RECORD_ID' => '$(record : RecordId)$',
		'LBL_RECORD_LABEL' => '$(record : RecordLabel)$',
		'LBL_LIST_OF_CHANGES_IN_RECORD' => '(record: ChangesListChanges)',
		'LBL_LIST_OF_NEW_VALUES_IN_RECORD' => '(record: ChangesListValues)',
		'LBL_RECORD_COMMENT' => '$(record : Comments 5)$, $(record : Comments)$',
		'LBL_RELETED_RECORD_LABEL' => '$(reletedRecord : parent_id|email1|Accounts)$, $(reletedRecord : parent_id|email1)$',
		'LBL_OWNER_EMAIL' => '$(reletedRecord : assigned_user_id|email1|Users)$',
	];

	/** @var array Variables for entity modules */
	public static $variableGeneral = [
		'LBL_CURRENT_DATE' => '$(general : CurrentDate)$',
		'LBL_CURRENT_TIME' => '$(general : CurrentTime)$',
		'LBL_BASE_TIMEZONE' => '$(general : BaseTimeZone)$',
		'LBL_USER_TIMEZONE' => '$(general : UserTimeZone)$',
		'LBL_SITE_URL' => '$(general : SiteUrl)$',
		'LBL_PORTAL_URL' => '$(general : PortalUrl)$',
		'LBL_TRANSLATE' => '$(translate : Accounts|LBL_COPY_BILLING_ADDRESS)$, $(translate : LBL_SECONDS)$',
	];

	/** @var array Variables for entity modules */
	public static $variableEntity = ['CrmDetailViewURL', 'PortalDetailViewURL', 'RecordId', 'RecordLabel', 'ChangesListChanges', 'ChangesListValues', 'Comments'];

	/** @var string[] List of available functions */
	protected static $baseFunctions = ['general', 'translate', 'record', 'reletedRecord', 'organization', 'employee', 'params'];

	/** @var int Record id */
	protected $record;

	/** @var string Module name */
	protected $moduleName;

	/** @var \Vtiger_Record_Model Record model */
	protected $recordModel;

	/** @var string Content */
	protected $content;

	/** @var string Rwa content */
	protected $rawContent;
	protected $withoutTranslations = false;
	protected $language;
	protected $params;

	public static function getOrganizationVar()
	{
		$companyDetails = \Vtiger_CompanyDetails_Model::getInstanceById();
		$fields = $companyDetails->getKeys();
		$fields[] = 'mailLogo';
		$fields[] = 'loginLogo';
		return $fields;
	}

	/**
	 * Get instanace by record id
	 * @param int $record Record id
	 * @param string $moduleName Module name
	 * @return \self
	 */
	public static function getInstanceById($record, $moduleName)
	{
		$class = get_called_class();
		$instance = new $class();
		$instance->record = $record;
		$instance->moduleName = $moduleName;
		$instance->recordModel = \Vtiger_Record_Model::getInstanceById($record, $moduleName);
		return $instance;
	}

	/**
	 * Get instanace by record model
	 * @param \Vtiger_Record_Model $recordModel
	 * @return \self
	 */
	public static function getInstanceByModel(\Vtiger_Record_Model $recordModel)
	{
		$class = get_called_class();
		$instance = new $class();
		$instance->record = $recordModel->getId();
		$instance->moduleName = $recordModel->getModuleName();
		$instance->recordModel = $recordModel;
		return $instance;
	}

	/**
	 * Get clean instanace
	 * @param string $moduleName Module name
	 * @return \self
	 */
	public static function getInstance($moduleName = '')
	{
		$class = get_called_class();
		$instance = new $class();
		if ($moduleName) {
			$instance->moduleName = $moduleName;
		}
		return $instance;
	}

	/**
	 * Set without translations
	 * @param string $content
	 * @return $this
	 */
	public function withoutTranslations($type = true)
	{
		$this->withoutTranslations = $type;
		return $this;
	}

	/**
	 * Set language
	 * @param string $name
	 * @return $this
	 */
	public function setLanguage($name = true)
	{
		$this->language = $name;
		return $this;
	}

	/**
	 * Set additional params
	 * @param array $params
	 * @return $this
	 */
	public function setParams($params)
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * Set content
	 * @param string $content
	 * @return $this
	 */
	public function setContent($content)
	{
		$this->rawContent = $this->content = $content;
		return $this;
	}

	/**
	 * Get content 
	 */
	public function getContent($trim = false)
	{
		return $trim ? trim($this->content) : $this->content;
	}

	/**
	 * Text parse function
	 * @return $this
	 */
	public function parse()
	{
		if (empty($this->content)) {
			return $this;
		}
		if (isset($this->language)) {
			$courentLanguage = \Vtiger_Language_Handler::$language;
			\Vtiger_Language_Handler::$language = $this->language;
		}
		$this->content = preg_replace_callback('/\$\((\w+) : ([\w\s\|]+)\)\$/', function ($matches) {
			list($fullText, $function, $params) = $matches;

			if (in_array($function, static::$baseFunctions)) {
				return $this->$function($params);
			}
			return '';
		}, $this->content);
		if ($courentLanguage) {
			\Vtiger_Language_Handler::$language = $courentLanguage;
		}
		return $this;
	}

	/**
	 * Text parse function
	 * @return $this
	 */
	public function parseTranslations()
	{
		if (isset($this->language)) {
			$courentLanguage = \Vtiger_Language_Handler::$language;
			\Vtiger_Language_Handler::$language = $this->language;
		}
		$this->content = preg_replace_callback('/\$\(translate : ([\w\s\|]+)\)\$/', function ($matches) {
			list($fullText, $params) = $matches;
			return $this->translate($params);
		}, $this->content);
		if ($courentLanguage) {
			\Vtiger_Language_Handler::$language = $courentLanguage;
		}
		return $this;
	}

	/**
	 * Parsing translations
	 * @param string $params
	 * @return string
	 */
	protected function translate($params)
	{
		if (strpos($params, '|') === false) {
			return Language::translate($params);
		}
		$aparams = explode('|', $params);
		$moduleName = array_shift($aparams);
		if (Module::getModuleId($moduleName) !== false) {
			return Language::translate(ltrim($params, "$moduleName|"), $moduleName, $this->language);
		}
		return Language::translate($params);
	}

	/**
	 * Parsing organization detail
	 * @param string $fieldName
	 * @return string
	 */
	protected function organization($fieldName)
	{
		if ($fieldName === 'mailLogo' || $fieldName === 'loginLogo') {
			$fieldName = ($fieldName === 'mailLogo') ? 'logoname' : 'panellogoname';
			$logoName = \Vtiger_CompanyDetails_Model::getInstanceById()->get($fieldName);
			$url = \AppConfig::main('site_URL');
			$logoTitle = Language::translate('LBL_COMPANY_LOGO_TITLE');
			return "<img class=\"organizationLogo\" src=\"$url/storage/Logo/$logoName\" title=\"$logoTitle\" alt=\"$logoTitle\">";
		}
		return \Vtiger_CompanyDetails_Model::getInstanceById()->get($fieldName);
	}

	/**
	 * Parsing employee detail
	 * @param string $fieldName
	 * @return mixed
	 */
	protected function employee($fieldName)
	{
		$currentUserModel = \Users_Record_Model::getCurrentUserModel();
		$userId = $currentUserModel->getId();
		if (Cache::has('TextParserEmployeeDetail', $userId . $fieldName)) {
			return Cache::get('TextParserEmployeeDetail', $userId . $fieldName);
		}
		if (Cache::has('TextParserEmployeeDetailRows', $userId)) {
			$employee = Cache::get('TextParserEmployeeDetailRows', $userId);
		} else {
			$employee = (new Db\Query())->select(['crmid'])->from('vtiger_crmentity')->where(['deleted' => 0, 'setype' => 'OSSEmployees', 'smownerid' => $userId])
					->limit(1)->scalar();
			Cache::save('TextParserEmployeeDetailRows', $userId, $employee, Cache::LONG);
		}
		$value = '';
		if ($employee) {
			$reletedRecordModel = \Vtiger_Record_Model::getInstanceById($employee, 'OSSEmployees');
			$value = static::getInstanceByModel($reletedRecordModel)->record($fieldName);
		}
		Cache::save('TextParserEmployeeDetail', $userId . $fieldName, $value, Cache::LONG);
		return $value;
	}

	/**
	 * Parsing general data
	 * @param string $key
	 * @return mixed
	 */
	protected function general($key)
	{
		switch ($key) {
			case 'CurrentDate':
				return (new \DateTimeField(null))->getDisplayDate();
			case 'CurrentTime' : return \Vtiger_Util_Helper::convertTimeIntoUsersDisplayFormat(date('h:i:s'));
			case 'SiteUrl' : return \AppConfig::main('site_URL');
			case 'PortalUrl' : return \AppConfig::main('PORTAL_URL');
			case 'BaseTimeZone' : return \DateTimeField::getDBTimeZone();
		}
		return $key;
	}

	/**
	 * Parsing record data
	 * @param string $key
	 * @return mixed
	 */
	protected function record($key, $isPermitted = true)
	{
		if (!isset($this->recordModel) || ($isPermitted && !Privilege::isPermitted($this->moduleName, 'DetailView', $this->record))) {
			return '';
		}
		if ($this->recordModel->has($key)) {
			$fieldModel = $this->recordModel->getModule()->getField($key);
			if (!$fieldModel || !$this->useValue($fieldModel, $this->moduleName)) {
				return '';
			}
			return $this->recordDisplayValue($this->recordModel->get($key), $fieldModel);
		}
		switch ($key) {
			case 'CrmDetailViewURL' :
				return \AppConfig::main('site_URL') . 'index.php?module=' . $this->moduleName . '&view=Detail&record=' . $this->record;
			case 'PortalDetailViewURL' :
				$recorIdName = 'id';
				if ($this->moduleName === 'HelpDesk') {
					$recorIdName = 'ticketid';
				} elseif ($this->moduleName === 'Faq') {
					$recorIdName = 'faqid';
				} elseif ($this->moduleName === 'Products') {
					$recorIdName = 'productid';
				}
				return \AppConfig::main('PORTAL_URL') . '/index.php?module=' . $this->moduleName . '&action=index&' . $recorIdName . '=' . $this->record;
			case 'ModuleName' : return $this->moduleName;
			case 'RecordId' : return $this->record;
			case 'RecordLabel' : return $this->recordModel->getName();
			case 'ChangesListChanges':
				foreach ($this->recordModel->getPreviousValue() as $fieldName => $oldValue) {
					$fieldModel = $this->recordModel->getModule()->getField($fieldName);
					if (!$fieldModel) {
						continue;
					}
					$oldValue = $this->recordDisplayValue($oldValue, $fieldModel);
					$currentValue = $this->recordDisplayValue($this->recordModel->get($fieldName), $fieldModel);
					if ($this->withoutTranslations !== true) {
						$value .= Language::translate($fieldModel->getFieldLabel(), $this->moduleName, $this->language) . ' ';
						$value .= Language::translate('LBL_FROM') . " $oldValue " . Language::translate('LBL_TO') . " $currentValue" . PHP_EOL;
					} else {
						$value .= "$(translate: $this->moduleName|{$fieldModel->getFieldLabel()})$ $(translate: LBL_FROM)$ $oldValue $(translate: LBL_TO)$ " .
							$currentValue . PHP_EOL;
					}
				}
				return $value;
			case 'ChangesListValues':
				foreach ($this->recordModel->getPreviousValue() as $fieldName => $oldValue) {
					$fieldModel = $this->recordModel->getModule()->getField($fieldName);
					if (!$fieldModel) {
						continue;
					}
					$currentValue = $this->recordDisplayValue($this->recordModel->get($fieldName), $fieldModel);
					if ($this->withoutTranslations !== true) {
						$value .= Language::translate($fieldModel->getFieldLabel(), $this->moduleName, $this->language) . ": $currentValue" . PHP_EOL;
					} else {
						$value .= "$(translate: $this->moduleName|{$fieldModel->getFieldLabel()})$: $currentValue" . PHP_EOL;
					}
				}
				return $value;
			default:
				if (strpos($key, ' ') !== false) {
					list($key, $params) = explode(' ', $key);
				}
				switch ($key) {
					case 'Comments': return $this->getComments($params);
				}
				break;
		}
		return '';
	}

	/**
	 * Parsing record data
	 * @param string $params
	 * @return mixed
	 */
	protected function reletedRecord($params)
	{
		list($fieldName, $reletedField, $reletedModule) = explode('|', $params);
		if (!isset($this->recordModel) ||
			!\Users_Privileges_Model::isPermitted($this->moduleName, 'DetailView', $this->record) ||
			$this->recordModel->isEmpty($fieldName)) {
			return '';
		}
		$reletedId = $this->recordModel->get($fieldName);
		if ($reletedModule === 'Users') {
			$userRecordModel = \Users_Privileges_Model::getInstanceById($reletedId);
			return static::getInstanceByModel($userRecordModel)->record($reletedField, false);
		}
		$moduleName = Record::getType($reletedId);
		if (!empty($moduleName)) {
			if (($reletedModule && $reletedModule !== $moduleName)) {
				return '';
			}
		}
		$reletedRecordModel = \Vtiger_Record_Model::getInstanceById($reletedId, $moduleName);
		return static::getInstanceByModel($reletedRecordModel)->record($reletedField);
	}

	/**
	 * Get record display value
	 * @param mixed $value
	 * @param \Vtiger_Field_Model $fieldModel
	 * @return string
	 */
	protected function recordDisplayValue($value, \Vtiger_Field_Model $fieldModel)
	{
		if ($value === '' || !$fieldModel->isViewEnabled()) {
			return '-';
		}
		if ($this->withoutTranslations !== true) {
			return $fieldModel->getDisplayValue($value, $this->record, $this->recordModel, true);
		}
		switch ($fieldModel->getFieldDataType()) {
			case 'boolean':
				$value = ($value === 1) ? 'LBL_YES' : 'LBL_NO';
				break;
			case 'multipicklist':
				$value = explode(' |##| ', $value);
				$trValue = [];
				$countValue = count($value);
				for ($i = 0; $i < $countValue; $i++) {
					$trValue[] = "$(translate : $this->moduleName|{$value[$i]})$";
				}
				if (is_array($trValue)) {
					$trValue = implode(' |##| ', $trValue);
				}
				$value = str_ireplace(' |##| ', ', ', $trValue);
				break;
			case 'picklist':
				$value = "$(translate : $this->moduleName|$value)$";
				break;
			case 'time':
				$userModel = Users_Privileges_Model::getCurrentUserModel();
				$value = DateTimeField::convertToUserTimeZone(date('Y-m-d') . ' ' . $value)->format('H:i:s');
				if ($userModel->get('hour_format') === '12') {
					if ($value) {
						list($hours, $minutes, $seconds) = explode(':', $value);
						$format = '$(translate : PM)$';
						if ($hours > 12) {
							$hours = (int) $hours - 12;
						} else if ($hours < 12) {
							$format = '$(translate : AM)$';
						}
						//If hours zero then we need to make it as 12 AM
						if ($hours == '00') {
							$hours = '12';
							$format = '$(translate : AM)$';
						}
						$value = "$hours:$minutes $format";
					} else {
						$value = '';
					}
				}
				break;
			case 'tree':
				$template = $fieldModel->getFieldParams();
				$row = Fields\Tree::getValueByTreeId($template, $value);
				$parentName = '';
				$name = '';
				if ($row) {
					if ($row['depth'] > 0) {
						$parenttrre = $row['parenttrre'];
						$pieces = explode('::', $parenttrre);
						end($pieces);
						$parent = prev($pieces);
						$parentRow = Fields\Tree::getValueByTreeId($template, $parent);
						$parentName = "($(translate : $this->moduleName|{$parentRow['name']})$) ";
					}
					$name = $parentName . "$(translate : $this->moduleName|{$row['name']})$";
				}
				break;
			default:
				return $fieldModel->getDisplayValue($value, $this->record, $this->recordModel, true);
				break;
		}
		return "$(translate : $value)$";
	}

	/**
	 * Get last comments
	 * @param int|bool $limit
	 * @return string
	 */
	protected function getComments($limit = false)
	{
		$query = (new \App\Db\Query())->select(['commentcontent'])->from('vtiger_modcomments')->where(['related_to' => $this->record])->orderBy(['modcommentsid' => SORT_DESC]);
		if ($limit) {
			$query->limit($limit);
		}
		$commentsList = '';
		foreach ($query->column() as $comment) {
			if ($comment != '') {
				$commentsList .= '<br><br>' . nl2br($comment);
			}
		}
		return ltrim($commentsList, '<br><br>');
	}

	/**
	 * Check if this content can be used
	 * @param \Vtiger_Field_Model $fieldModel
	 * @param string $moduleName
	 * @return boolean
	 */
	protected function useValue($fieldModel, $moduleName)
	{
		return true;
	}

	/**
	 * Parsing params
	 * @param string $params
	 * @return string
	 */
	protected function params($params)
	{
		if (isset($this->params[$params])) {
			return $this->params[$params];
		}
		return '';
	}
}
