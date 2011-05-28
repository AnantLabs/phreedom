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
//  Path: /modules/phreeform/custom/classes/statement_builder.php
//
gen_pull_language('phreebooks');
require_once(DIR_FS_MODULES . 'phreebooks/functions/phreebooks.php'); // needed to calculate terms

class statement_builder {
  function statement_builder() {
	// List the special fields as an array to substitute out for the sql, must match from the selection menu generation
	$this->special_field_array = array('balance_0', 'balance_30', 'balance_60', 'balance_90');
  }

  function load_query_results($tableKey = 'id', $tableValue = 0) {
	global $db, $FieldListings, $report;
	if (!$tableValue) return false;
	$this->bill_acct_id = $tableValue;
	// fetch the main contact information, only one record
	$sql = "select c.id, c.type, c.short_name, c.special_terms, 
		a.primary_name, a.contact, a.address1, a.address2, a.city_town, a.state_province, a.postal_code, 
		a.country_code, a.telephone1, a.telephone2, a.telephone3, a.telephone4, a.email, a.website 
	  from " . TABLE_CONTACTS . " c inner join " . TABLE_ADDRESS_BOOK . " a on c.id = a.ref_id 
	  where c.id = " . $this->bill_acct_id . " and a.type like '%m'";
	$result = $db->Execute($sql);
//echo 'Contact sql = ' . $sql . '<br />returned ' . $result->RecordCount() . '<br /><br />';
	while (list($key, $value) = each($result->fields)) $this->$key = db_prepare_input($value);

	// calculate the starting balance
	$dates = gen_build_sql_date($report->datedefault, $report->datefield);
	$sql = "select sum(i.credit_amount) as credit, sum(i.debit_amount) as debit 
		from " . TABLE_JOURNAL_MAIN . " m inner join " . TABLE_JOURNAL_ITEM . " i on m.id = i.ref_id
		where m.bill_acct_id = " . $this->bill_acct_id . " and m.post_date < '" . $dates['start_date'] . "' 
		  and m.journal_id in (6, 7, 12, 13) and i.gl_type = 'ttl'";
	$result = $db->Execute($sql);
//echo 'Starting bal sql = ' . $sql . '<br />returned ' . $result->RecordCount() . '<br /><br />';
	$this->prior_balance = ($result->RecordCount()) ? ($result->fields['debit'] - $result->fields['credit']) : 0;
	$sql = "select sum(i.credit_amount) as credit, sum(i.debit_amount) as debit 
		from " . TABLE_JOURNAL_MAIN . " m inner join " . TABLE_JOURNAL_ITEM . " i on m.id = i.ref_id
		where m.bill_acct_id = " . $this->bill_acct_id . " and m.post_date < '" . $dates['start_date'] . "' 
		  and m.journal_id in (18, 20) and i.gl_type in ('pmt', 'chk')";
	$result = $db->Execute($sql);
	$this->prior_balance -= ($result->RecordCount()) ? ($result->fields['credit'] - $result->fields['debit']) : 0;

	// fetch journal history based on date criteria
	$strDates = str_replace('post_date', 'm.post_date', $dates['sql']);
	$this->line_items = array();
	$sql = "select m.post_date, m.journal_id, i.debit_amount, i.credit_amount, m.purchase_invoice_id, 
	  m.purch_order_id, i.gl_type 
	  from " . TABLE_JOURNAL_MAIN . " m inner join " . TABLE_JOURNAL_ITEM . " i on m.id = i.ref_id 
	  where m.bill_acct_id = " . $this->bill_acct_id;
	if ($strDates) $sql .= " and " . $strDates;
//		$sql .= " and m.journal_id in (6, 7, 12, 13, 18, 20) and i.gl_type in ('dsc', 'ttl') order by m.post_date";
	$sql .= " and m.journal_id in (6, 7, 12, 13, 18, 20) and i.gl_type in ('ttl', 'dsc') order by m.post_date";
	$result = $db->Execute($sql);
//echo 'History sql = ' . $sql . '<br />returned ' . $result->RecordCount() . '<br /><br />';
	$this->statememt_total = 0;
	while (!$result->EOF) {
	  $reverse    = in_array($result->fields['journal_id'], array(6, 7, 12, 13)) ? true : false;
	  $line_desc  = defined('GEN_ADM_TOOLS_J' . str_pad($result->fields['journal_id'], 2, '0', STR_PAD_LEFT)) ? constant('GEN_ADM_TOOLS_J' . str_pad($result->fields['journal_id'], 2, '0', STR_PAD_LEFT)) : $result->fields['journal_id'];
	  $terms_date = calculate_terms_due_dates($result->fields['post_date'], $this->special_terms);
	  $credit     = ($reverse) ? $result->fields['debit_amount']  : $result->fields['credit_amount'];
	  $debit      = ($reverse) ? $result->fields['credit_amount'] : $result->fields['debit_amount'];
	  if (in_array($result->fields['journal_id'], array(7, 13)) || $result->fields['gl_type'] == 'dsc') { // special case for credit memos and discounts
		$temp   = $debit;
		$debit  = -$credit;
		$credit = -$temp;
		if ($result->fields['gl_type'] == 'dsc') $line_desc = TEXT_DISCOUNT;
	  }
	  $this->line_items[] = array(
		'journal_desc'        => $line_desc,
		'purchase_invoice_id' => $result->fields['purchase_invoice_id'],
		'purch_order_id'      => $result->fields['purch_order_id'],
		'post_date'           => $result->fields['post_date'],
		'due_date'            => $terms_date['net_date'],
		'credit_amount'       => $credit,
		'debit_amount'        => $debit,
	  );
	  $this->statememt_total += $credit - $debit;
	  $result->MoveNext();
	}

	// convert particular values indexed by id to common name
	if ($this->rep_id) {
		$sql = "select short_name from " . TABLE_CONTACTS . " where id = " . $this->rep_id;
		$result = $db->Execute($sql);
		$this->rep_id = $result->fields['display_name'];
	} else {
		$this->rep_id = '';
	}
	$this->balance_due = $this->prior_balance + $this->statememt_total;
	if ($this->type == 'v') { // invert amount for vendors for display purposes
		$this->prior_balance = -$this->prior_balance; 
		$this->balance_due   = -$this->balance_due;
	}
//echo 'prior balance = ' . $this->prior_balance . ' and bal due = ' . $this->balance_due . '<br />';
	$this->post_date = date(DATE_FORMAT);

	// sequence the results per Prefs[Seq]
	$output = array();
	foreach ($report->fieldlist as $OneField) { // check for a data field and build sql field list
	  if ($OneField->type == 'Data') { // then it's data field, include it
		$field = $OneField->boxfield[0]->fieldname;
		$output[] = $this->$field;
	  }
	}
	// return results
	return $output;
  }

  function load_table_data($fields = '') {
	// fill the return data array
	$output = array();
	if (is_array($this->line_items) && is_array($fields)) {
	  foreach ($this->line_items as $key => $row) {
		$row_data = array();
		foreach ($fields as $idx => $element) {
		  $row_data['r' . $idx] = $this->line_items[$key][$element->fieldname];
		}
		$output[] = $row_data;
	  }
	}
	return $output;
  }

  function load_total_results($Params) {
	return $this->balance_due;
  }

  function load_text_block_data($Params) {
	$TextField = '';
	foreach($Params as $Temp) {
	  $fieldname = $Temp->fieldname;
	  $TextField .= AddSep($this->$fieldname, $Temp->processing);
	}
	return $TextField;
  }

  function build_selection_dropdown() {
	// build user choices for this class with the current and newly established fields
	$output = array();
	$output[] = array('id' => '',                    'text' => TEXT_NONE);
	$output[] = array('id' => 'id',                  'text' => RW_SB_RECORD_ID);
	$output[] = array('id' => 'journal_id',          'text' => RW_SB_JOURNAL_ID);
	$output[] = array('id' => 'post_date',           'text' => RW_SB_POST_DATE);
	$output[] = array('id' => 'purchase_invoice_id', 'text' => RW_SB_INV_NUM);
	$output[] = array('id' => 'short_name',          'text' => RW_SB_CUSTOMER_ID);
	$output[] = array('id' => 'bill_acct_id',        'text' => RW_SB_CUSTOMER_RECORD);
	$output[] = array('id' => 'account_number',      'text' => RW_SB_ACCOUNT_NUMBER);
	$output[] = array('id' => 'rep_id',              'text' => RW_SB_SALES_REP);
	$output[] = array('id' => 'terms',               'text' => RW_SB_TERMS);
	$output[] = array('id' => 'primary_name',        'text' => RW_SB_BILL_PRIMARY_NAME);
	$output[] = array('id' => 'contact',             'text' => RW_SB_BILL_CONTACT);
	$output[] = array('id' => 'address1',            'text' => RW_SB_BILL_ADDRESS1);
	$output[] = array('id' => 'address2',            'text' => RW_SB_BILL_ADDRESS2);
	$output[] = array('id' => 'city_town',           'text' => RW_SB_BILL_CITY);
	$output[] = array('id' => 'state_province',      'text' => RW_SB_BILL_STATE);
	$output[] = array('id' => 'postal_code',         'text' => RW_SB_BILL_ZIP);
	$output[] = array('id' => 'country_code',        'text' => RW_SB_BILL_COUNTRY);
	$output[] = array('id' => 'telephone1',          'text' => RW_SB_BILL_TELE1);
	$output[] = array('id' => 'telephone2',          'text' => RW_SB_BILL_TELE2);
	$output[] = array('id' => 'telephone4',          'text' => RW_SB_BILL_FAX);
	$output[] = array('id' => 'email',               'text' => RW_SB_BILL_EMAIL);
	$output[] = array('id' => 'website',             'text' => RW_SB_BILL_WEBSITE);
	// special calculated fields
	$output[] = array('id' => 'prior_balance',       'text' => RW_SB_PRIOR_BALANCE);
	$output[] = array('id' => 'balance_due',         'text' => RW_SB_BALANCE_DUE);
	return $output;
  }

  function build_table_drop_down() { // build the drop down choices
	$output = array();
	$output[] = array('id' => '',                    'text' => TEXT_NONE);
	$output[] = array('id' => 'journal_desc',        'text' => RW_SB_JOURNAL_DESC);
	$output[] = array('id' => 'purchase_invoice_id', 'text' => RW_SB_INV_NUM);
	$output[] = array('id' => 'purch_order_id',      'text' => RW_SB_PO_NUM);
	$output[] = array('id' => 'post_date',           'text' => TEXT_POST_DATE);
	$output[] = array('id' => 'due_date',            'text' => RW_SB_DUE_DATE);
	$output[] = array('id' => 'credit_amount',       'text' => RW_SB_PMT_RCVD);
	$output[] = array('id' => 'debit_amount',        'text' => RW_SB_INV_TOTAL);
	$output[] = array('id' => 'total_amount',        'text' => RW_SB_BALANCE_DUE);
	return $output;
  }

}
?>