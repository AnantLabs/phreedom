<?php
// +-----------------------------------------------------------------+
// |                   PhreeBooks Open Source ERP                    |
// +-----------------------------------------------------------------+
// | Copyright (c) 2008, 2009, 2010, 2011 PhreeSoft, LLC             |
// | http://www.PhreeSoft.com                                        |
// +-----------------------------------------------------------------+
// | This program is free software: you can redistribute it and/or   |
// | modify it under the terms of the GNU General Public License as  |
// | published by the Free Software Foundation, either version 3 of  |
// | the License, or any later version.                              |
// |                                                                 |
// | This program is distributed in the hope that it will be useful, |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of  |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the   |
// | GNU General Public License for more details.                    |
// +-----------------------------------------------------------------+
//  Path: /modules/import_bank/classes/import_bank.php
//
require_once(DIR_FS_MODULES . 'phreebooks/classes/gen_ledger.php');
gen_pull_language('phreebooks');
require(DIR_FS_MODULES  . 'phreebooks/functions/phreebooks.php');

class impbanking extends journal {
	protected $_questionposts = QUESTION_POSTS;
	protected $_accounttype;
	protected $_creditamount;
	protected $_debitamount;
	protected $_totalamount;
	protected $_description;
	protected $_firstjid;
	public    $_succes = false;
	public    $bank_account_fields = array('bank_account');
	public    $iban_fields = array();
	
	 public function __construct($ouwer_bank_account_number, $post_date, $other_bank_account_number, $credit_amount, $debit_amount, $description, $bank_gl_acct, $other_bank_account_iban){
	 	global $db, $messageStack,$currencies;
	 	$messageStack->debug("\n\n*************** Start Processing Import Payment *******************");
	 	$temp = $db->Execute("describe " . TABLE_CONTACTS);
	 	while (!$temp->EOF) {
	 		if(strripos($temp->fields['Field'],'bank_account')!==false) $bank_account_fields[]=$temp->fields['Field'];
	 		if(strripos($temp->fields['Field'],'iban')!==false)         $this->iban_fields[]=$temp->fields['Field'];
			$temp->MoveNext();
		}
	 	$this->bank_account_fields = $bank_account_fields;
	 	if ($ouwer_bank_account_number <> '') {
			$ouwer_bank = ltrim($ouwer_bank_account_number,0);
		 	If($ouwer_bank == ''){
				$messageStack->add(TEXT_BIMP_ERMSG1 , 'error');
				//TEXT_IMP_ERMSG19 = ouwer bank account is null
				return false;
				exit;
			}
		 	$sql ="select id, description from " . TABLE_CHART_OF_ACCOUNTS. " where description like '%".$ouwer_bank."%'";
			$result = $db->Execute($sql);
			If($result->RecordCount()== 0){
				//TEXT_IMP_ERMSG20 = two or more gl accounts with the description :
				$messageStack->add(TEXT_BIMP_ERMSG5 .' '. $ouwer_bank, 'error');
				return false;
				exit;
			}
			if (!$result->RecordCount()> 1){
				//TEXT_IMP_ERMSG20 = two or more gl accounts with the description :
				$messageStack->add(TEXT_BIMP_ERMSG2 .' '. $ouwer_bank, 'error');
				return false;
				exit;
			}
			$this->gl_acct_id 			= $result->fields['id'];
		}else{
			If($bank_gl_acct == ''){
				$messageStack->add(TEXT_BIMP_ERMSG1 , 'error');
				//TEXT_IMP_ERMSG19 = ouwer bank account is null
				return false;
				exit;
			}
			$this->gl_acct_id 			= $bank_gl_acct;
		}
		$this->_description			= $description;
		$this->_creditamount		= $currencies->clean_value($credit_amount);
		$this->_debitamount			= $currencies->clean_value($debit_amount);
		$this->_totalamount			= $this->_debitamount + $this->_creditamount ;
		$this->post_date           	= gen_db_date($post_date);
		$this->period              	= gen_calculate_period($this->post_date,false);
		$this->admin_id            	= $_SESSION['admin_id'];
		If ($this->find_contact( $other_bank_account_number, $other_bank_account_iban )){	
			$this->find_right_invoice();
		}else{
			$this->proces_mutation();
		}
	$messageStack->debug("\n\n*************** End Processing Import Payment *******************");
	}
	
	private function find_contact($other_bank_account_number, $other_bank_account_iban){
		global $db, $messageStack;
		$criteria = false;
		$messageStack->debug("\n trying to find a contact ");
		If($other_bank_account_number =='' && $other_bank_account_iban ==''){
			$messageStack->debug("\n there is no other bank account number or iban. can not find contact. ");
			return false;
		}
		if($other_bank_account_iban != '' && count($this->iban_fields) > 0 ){
			$criteria = '(' . implode(' = "' . $other_bank_account_iban . '" or ', $this->iban_fields) . ' = "' . $other_bank_account_iban . '")';
		}   
		if($other_bank_account_number != '' && $criteria == false){
			if(count($this->bank_account_fields) > 0){
				$criteria = '(' . implode(' = "' . ltrim($other_bank_account_number,0) . '" or ', $this->bank_account_fields) . ' = "' . ltrim($other_bank_account_number,0) . '")';
			}else{
				if($other_bank_account_iban != '' )  $messageStack->debug("\n iban field is supplied but cant find it in the contact fields. If you want it add it in the contacts module administration ");
				if($other_bank_account_number != '') $messageStack->debug("\n the other bank account field is supplied but cant find it in the contact fields. If you want it add it in the contacts module administration");
				return false;
			}
		} 
		
		$sql ="SELECT * FROM ". TABLE_CONTACTS ." WHERE (`type` ='v' or `type`='c' ) and ".$criteria;
		$result1 = $db->Execute($sql);
		if(!$result1->RecordCount()== 0){
			$messageStack->debug("\n found a costumer or vender with the bankaccountnumber ". ltrim($other_bank_account_number,0));
			if (!$result1->RecordCount()> 1){
				//TEXT_IMP_ERMSG17 = two or more accounts with the same account
				$messageStack->add(TEXT_BIMP_ERMSG4 . $other_bank_account_number, 'error');
				return false;
			}
			$result2 = $db->Execute("SELECT * FROM ". TABLE_ADDRESS_BOOK ." WHERE (`type` ='vm' or `type`='vb' or `type` ='cm' or `type` ='cb' ) and `ref_id` = '" . $result1->fields['id']."'");
			$this->bill_short_name     	= $result1->fields['id'];
			$this->bill_acct_id        	= $result1->fields['id'];
			$this->bill_addres_id		= $result1->fields['id'];
			$this->_accounttype 		= $result1->fields['type'];
			$this->bill_address_id     	= $result2->fields['address_id'];
			$this->bill_primary_name	= $result2->fields['primary_name'];
			$this->bill_contact        	= $result2->fields['contact'];
			$this->bill_address1       	= $result2->fields['address1'];
			$this->bill_address2       	= $result2->fields['address2'];
			$this->bill_city_town      	= $result2->fields['city_town'];
			$this->bill_state_province 	= $result2->fields['state_province'];
			$this->bill_postal_code    	= $result2->fields['postal_code'];
			$this->bill_country_code 	= $result2->fields['country_code']; 
			$this->id					= '';
			return true;
		}
		$messageStack->debug("\n was unable to find a 1 matching contact. ");
		return false;
	}
	
	private function find_right_invoice(){
		global $db, $messageStack, $currencies;
		$messageStack->debug("\n trying to find the right invoice");
		$open_invoices = fill_paid_invoice_array(0, $this->bill_short_name ,$this->_accounttype);
		$invoice_number ='';
		$invoice_id 	='';
		foreach ($open_invoices['invoices'] as $key => $invoice) {
			//when we find a invoice we book a payment to it
			if ((string)$invoice['total_amount']-$invoice['amount_paid'] == (string)str_replace('.', ',', $this->_totalamount))	{
				$messageStack->debug("\n Found the matching invoice purchase_invoice_id ".$invoice['purchase_invoice_id'].' id '.$invoice['id']);
				$invoice_number = $invoice['purchase_invoice_id'];
				$invoice_id 	= $invoice['id'];
			}
		}
		
		if ($invoice_number){$messageStack->debug("\n posting payment to invoice ".$invoice_number);}
		else{ $messageStack->debug("\n posting payment as a deposit ");}
		if ($this->_accounttype =='c'){
			$this->_firstjid 			= 18; 
			$gl_acct_id         		= AR_DEFAULT_GL_ACCT ;
			$this->purchase_invoice_id 	= 'DP' . $this->post_date; 
			$this->description			= sprintf(TEXT_JID_ENTRY, constant('ORD_TEXT_18_C_WINDOW_TITLE'));
			define('GL_TYPE','pmt');
		}else{
			$this->_firstjid 			= 20;
			$gl_acct_id         		= AP_DEFAULT_PURCHASE_ACCOUNT ;
			$result 					= $db->Execute("select next_check_num from " . TABLE_CURRENT_STATUS);
			$this->description			= sprintf(TEXT_JID_ENTRY, constant('ORD_TEXT_20_V_WINDOW_TITLE'));
			define('GL_TYPE','chk');
		}
		$this->journal_id = $this->_firstjid;
		$this->total_amount = $this->_totalamount;
		$this->journal_rows[0] = array(
			'so_po_item_ref_id'		=> $invoice_id, 	
			'gl_type'               => GL_TYPE,
			'gl_account'            => $gl_acct_id,
			'serialize_number'      => $invoice_number,
			'post_date'             => $this->post_date,
			'debit_amount'     		=> $this->_debitamount,	
			'credit_amount'			=> $this->_creditamount,
		  	'description'    		=> $this->_description,	
		);
		$this->journal_rows[1] = array(	
			'gl_type'               => 'ttl',
			'gl_account'            => $this->gl_acct_id,
			'post_date'             => $this->post_date,
			'debit_amount'     		=> $this->_creditamount,	
			'credit_amount'			=> $this->_debitamount,
		  	'description'    		=> $this->_description,	
		);
		$this->journal_main_array = $this->build_journal_main_array();
		$this->validate_purchase_invoice_id();
		if(!$this->Post()){
			return false;
			exit;	
		}
		$this->increment_purchase_invoice_id();	
		if(!$invoice_id ==''){
			$messageStack->debug("\n closing invoice. ".$invoice_number);
			$this->close_so_po($invoice_id, true);
		}else{// make credit inv
			$messageStack->debug("\n Making credit invoice. ");
			$this->journal_id          	= ($this->_firstjid == 20) ? 7 : 13;  
			$this->gl_acct_id          	= $gl_acct_id;
			$this->id				 	= '';
			$this->description			= GENERAL_JOURNAL_7_DESC;
			$this->total_amount 		= $this->_totalamount;
			$this->journal_rows[0] = array(
				'gl_type'               => 'por',
				'gl_account'            => $gl_acct_id,
				'serialize_number'      => $invoice_number,
				'post_date'             => $this->post_date,
				'debit_amount'     		=> $this->_debitamount,	
				'credit_amount'			=> $this->_creditamount,	
			);
			$this->journal_rows[1] = array(	
				'id'					=> '',
				'gl_type'               => 'ttl',
				'gl_account'            => $this->gl_acct_id,
				'post_date'             => $this->post_date,
				'debit_amount'     		=> $this->_creditamount,	
				'credit_amount'			=> $this->_debitamount,
			  	'description'    		=> $this->_description,	
			);
			$this->journal_main_array = $this->build_journal_main_array();
			$this->validate_purchase_invoice_id();
			if(!$this->Post()){
				return false;
				exit;	
			}
			$this->increment_purchase_invoice_id();
		}	
		$this->_succes = true;
		return true;
	}
	
	private function proces_mutation(){
		global $db, $messageStack;
		$messageStack->debug("\n this wil be posted as a journal ");
		$sql ="select gl_account from " . TABLE_JOURNAL_ITEM. " where description = '".$this->_description."' and not gl_account='".$this->gl_acct_id."' and not gl_account='".$this->_questionposts."'";
		$result = $db->Execute($sql);
		$gl_account =$this->_questionposts;
		If(!$result->RecordCount()== 0){
			$result->EOF;
			if(!$result->fields['gl_acount']==''){
				$gl_account =$result->fields['gl_acount'];
			}
		}
		$this->id					= '';
		$this->journal_id 			= 2;
		$this->description			= GL_ENTRY_TITLE;
		$this->total_amount 		= $this->_totalamount;
		$this->journal_rows[0] = array(
			'gl_account'            => $gl_account,
			'post_date'             => $this->post_date,
			'debit_amount'     		=> $this->_debitamount,	
			'credit_amount'			=> $this->_creditamount,
		  	'description'    		=> $this->_description,	
		);
		$this->journal_rows[1] = array(	
			'gl_account'            => $this->gl_acct_id,
			'post_date'             => $this->post_date,
			'debit_amount'     		=> $this->_creditamount,	
			'credit_amount'			=> $this->_debitamount,
		  	'description'    		=> $this->_description,	
		);
		$this->journal_main_array = $this->build_journal_main_array();
		if(!$this->Post()){
			$messageStack->debug("\n unable to post the journal e error was returned by the class journal");
			return false;
			exit;	
		}
		$this->_succes = true;
	}
	
}