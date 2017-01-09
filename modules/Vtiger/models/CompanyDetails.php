<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

/**
 * CompanyDetails Record Model class
 */
class Vtiger_CompanyDetails_Model extends Vtiger_Base_Model
{

	/**
	 * Function to get the Company Logo
	 * @return Vtiger_Image_Model instance
	 */
	public function getLogo()
	{
		$logoName = decode_html($this->get('logoname'));
		$logoModel = new Vtiger_Image_Model();
		if (!empty($logoName)) {
			$companyLogo = [];
			$companyLogo['imagepath'] = "storage/Logo/$logoName";
			$companyLogo['alt'] = $companyLogo['imagename'] = $logoName;
			$companyLogo['title'] = vtranslate('LBL_COMPANY_LOGO_TITLE');
			$logoModel->setData($companyLogo);
		}
		return $logoModel;
	}

	/**
	 * Function to get the instance of the CompanyDetails model for a given organization id
	 * @param <Number> $id
	 * @return Vtiger_CompanyDetails_Model instance
	 */
	public static function getInstanceById($id = 1)
	{
		if (\App\Cache::has('organizationDetails', $id)) {
			$row = \App\Cache::get('organizationDetails', $id);
		} else {
			$row = (new \App\Db\Query())->from('vtiger_organizationdetails')
				->where(['organization_id' => $id])
				->one();
			\App\Cache::save('organizationDetails', $id, $row, \App\Cache::LONG);
		}
		$companyDetails = new self();
		if ($row) {
			$companyDetails->setData($row);
		}
		return $companyDetails;
	}
}
