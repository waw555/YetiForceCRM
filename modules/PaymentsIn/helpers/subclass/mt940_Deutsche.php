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
vimport('~~modules/PaymentsIn/helpers/mt940.php');

class mt940_Deutsche extends mt940
{

	public function parse()
	{
		$tab = $this->prepareFile();
		foreach ($tab as $line)
			$this->parseLine($line);
	}

	protected function parseLine($line)
	{
		$tag = substr($line, 1, strpos($line, ':', 1) - 1);
		$value = trim(substr($line, strpos($line, ':', 1) + 1));
		switch ($tag) {
			case '20':
				$this->refNumber = $value;
				break;
			case '25':
				$this->accountNumber = $value;
				break;
			case '28C':
				$this->extractNumber = $value;
				break;
			case 'NS':
				$code = substr($value, 0, 2);
				if ($code == '22')
					$this->ownerName = substr($value, 2);
				else if ($code == '23')
					$this->accountName = substr($value, 2);
				break;
			case '60F':
				$this->openBalance = $this->parseBalance($value);
				break;
			case '62F':
				$this->closeBalance = $this->parseBalance($value);
				break;
			case '64':
				$this->availableBalance = $this->parseBalance($value);
				break;
			case '61':
				self::parseOperation($value);
				break;
			case '86':
				if ($this->_lastTag == '61')
					$this->parseTransaction($value);
				else
					$this->info .= $value;
				break;
			default:
				break;
		}
		$this->_lastTag = $tag;
	}

	protected function parseTransaction($value)
	{
		$transaction = array(
			'code' => substr($value, 0, 3),
			'typeCode' => '',
			'number' => '',
			'title' => '',
			'contName' => '',
			'contAccount' => ''
		);
		$transaction['title'] = $value;
		/*
		  $delimiter = substr($value, 3, 1);
		  $tab = explode($delimiter, substr($value, 4));
		  foreach($tab as $line) {
		  $subTag = substr($line, 0, 2);
		  $subVal = substr($line, 2);
		  switch($subTag) {
		  case '00':
		  $transaction['typeCode'] = $subVal;
		  break;
		  case '10':
		  $transaction['number'] = $subVal;
		  break;
		  case '20':
		  case '21':
		  case '22':
		  case '23':
		  case '24':
		  case '25':
		  case '26':
		  $transaction['title'] .= $subVal;
		  break;
		  case '27':
		  case '28':
		  case '29':
		  $transaction['contName'] .= $subVal;
		  break;
		  case '38':
		  $transaction['contAccount'] = $subVal;
		  break;
		  default:
		  break;
		  }
		  } */
		$this->operations[count($this->operations) - 1]['details'] = $transaction;
	}

	protected function parseOperation($value)
	{
		$this->operations[] = array(
			'date' => substr($value, 0, 2) . '-' . substr($value, 2, 2) . '-' . substr($value, 4, 2),
			'accountDate' => substr($value, 6, 2) . '-' . substr($value, 8, 2),
			'indicator' => substr($value, 10, 1),
			//'third_letter_currency_code' => substr($value, 11, 1),
			'amount' => substr($value, 11, strpos($value, ',') - 8)
		);
	}
}

?>
