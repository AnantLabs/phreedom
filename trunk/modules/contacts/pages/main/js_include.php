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
//  Path: /modules/contacts/pages/main/js_include.php
//
?>
<script type="text/javascript">
<!--
// pass any php variables generated during pre-process that are used in the javascript functions.
// Include translations here as well.
// Insert javscript file references here.
// js contact
<?php echo $contact_js; ?>
// js pmt
<?php echo $js_pmt_array; ?>
// js actions
<?php echo $js_actions; ?>

function init() {
  SetDisabled();

<?php if ($include_template == 'template_main.php') {
 	echo '  document.getElementById("search_text").focus();'  . chr(10); 
  	echo '  document.getElementById("search_text").select();' . chr(10); 
  }
?>
  if (window.extra_init) { extra_init() } // hook for additional initializations
}

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  <?php if (!$auto_type && ($action == 'edit' || $action == 'update' || $action == 'new')) { ?> // if showing the edit/update detail form
  var acctId = document.getElementById('short_name').value;
    if (acctId == '') {
      error_message += "<?php echo ACT_JS_SHORT_NAME; ?>";
	  error = 1;
    }
  <?php } ?>

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}

// Insert other page specific functions here.
function changeOptions() {
	LoadDefaults();
	SetDisabled();
}

function LoadDefaults() {
  if (document.contacts.special_terms[0].checked) {
	document.getElementById('early_percent').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_PERCENT"); ?>';
	document.getElementById('early_days').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_DAYS"); ?>';
	document.getElementById('standard_days').value = '<?php echo constant($terms_type . "_NUM_DAYS_DUE"); ?>';
  } else if (document.contacts.special_terms[1].checked) {
	document.getElementById('early_percent').value = '';
	document.getElementById('early_days').value = '';
	document.getElementById('standard_days').value = '';
  } else if (document.contacts.special_terms[2].checked) {
	document.getElementById('early_percent').value = '';
	document.getElementById('early_days').value = '';
	document.getElementById('standard_days').value = '';
  } else if (document.contacts.special_terms[3].checked) {
	document.getElementById('early_percent').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_PERCENT"); ?>';
	document.getElementById('early_days').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_DAYS"); ?>';
	document.getElementById('standard_days').value = '<?php echo constant($terms_type . "_NUM_DAYS_DUE"); ?>';
  } else if (document.contacts.special_terms[4].checked) {
	document.getElementById('early_percent').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_PERCENT"); ?>';
	document.getElementById('early_days').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_DAYS"); ?>';
	document.getElementById('standard_days').value = '';
  } else if (document.contacts.special_terms[5].checked) {
	document.getElementById('early_percent').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_PERCENT"); ?>';
	document.getElementById('early_days').value = '<?php echo constant($terms_type . "_PREPAYMENT_DISCOUNT_DAYS"); ?>';
	document.getElementById('standard_days').value = '';
  }
  document.contacts.elements['due_date'].value = '';
}

function SetDisabled() {
  if (!document.contacts.special_terms) return;
  if (document.contacts.special_terms[1].checked) {
	document.getElementById('early_percent').disabled = true;
	document.getElementById('early_days').disabled    = true;
	document.getElementById('standard_days').disabled = true;
	document.contacts.elements['due_date'].disabled   = true;
  } else if (document.contacts.special_terms[2].checked) {
	document.getElementById('early_percent').disabled = true;
	document.getElementById('early_days').disabled    = true;
	document.getElementById('standard_days').disabled = true;
	document.contacts.elements['due_date'].disabled   = true;
  } else if (document.contacts.special_terms[3].checked) {
	document.getElementById('early_percent').disabled = false;
	document.getElementById('early_days').disabled    = false;
	document.getElementById('standard_days').disabled = false;
	document.contacts.elements['due_date'].disabled   = true;
  } else if (document.contacts.special_terms[4].checked) {
	document.getElementById('early_percent').disabled = false;
	document.getElementById('early_days').disabled    = false;
	document.getElementById('standard_days').disabled = true;
	document.contacts.elements['due_date'].disabled   = false;
  } else if (document.contacts.special_terms[5].checked) {
	document.getElementById('early_percent').disabled = false;
	document.getElementById('early_days').disabled    = false;
	document.getElementById('standard_days').disabled = true;
	document.contacts.elements['due_date'].disabled   = true;
  } else {
	document.getElementById('early_percent').disabled = true;
	document.getElementById('early_days').disabled    = true;
	document.getElementById('standard_days').disabled = true;
	document.contacts.elements['due_date'].disabled   = true;
  }
}

function addressRecord(address_id, primary_name, contact, address1, address2, city_town, state_province, postal_code, country_code, telephone1, telephone2, telephone3, telephone4, email, website, notes) {
	this.address_id     = address_id;
	this.primary_name   = primary_name;
	this.contact        = contact;
	this.address1       = address1;
	this.address2       = address2;
	this.city_town      = city_town;
	this.state_province = state_province;
	this.postal_code    = postal_code;
	this.country_code   = country_code;
	this.telephone1     = telephone1;
	this.telephone2     = telephone2;
	this.telephone3     = telephone3;
	this.telephone4     = telephone4;
	this.email          = email;
	this.website        = website;
	this.notes          = notes;
}

function pmtRecord(id, hint, name, card_num, exp_month, exp_year, cvv2) {
	this.id        = id;
	this.hint      = hint;
	this.name      = name;
	this.card_num  = card_num;
	this.exp_month = exp_month;
	this.exp_year  = exp_year;
	this.cvv2      = cvv2;
}

function editRow(type, id) {
  // replace form fields with field data
  document.getElementById(type+'_address_id').value     = id;
  document.getElementById(type+'_primary_name').value   = addBook[id].primary_name;
  document.getElementById(type+'_contact').value        = addBook[id].contact;
  document.getElementById(type+'_address1').value       = addBook[id].address1;
  document.getElementById(type+'_address2').value       = addBook[id].address2;
  document.getElementById(type+'_city_town').value      = addBook[id].city_town;
  document.getElementById(type+'_state_province').value = addBook[id].state_province;
  document.getElementById(type+'_postal_code').value    = addBook[id].postal_code;
  document.getElementById(type+'_country_code').value   = addBook[id].country_code;
  document.getElementById(type+'_telephone1').value     = addBook[id].telephone1;
  document.getElementById(type+'_telephone2').value     = addBook[id].telephone2;
  document.getElementById(type+'_telephone3').value     = addBook[id].telephone3;
  document.getElementById(type+'_telephone4').value     = addBook[id].telephone4;
  document.getElementById(type+'_email').value          = addBook[id].email;
  document.getElementById(type+'_website').value        = addBook[id].website;
  document.getElementById(type+'_notes').value          = addBook[id].notes;
} 

function editPmtRow(index) {
  document.getElementById('payment_id').value        = js_pmt_array[index].id;
  document.getElementById('payment_cc_name').value   = js_pmt_array[index].name;
  document.getElementById('payment_cc_number').value = js_pmt_array[index].card_num;
  document.getElementById('payment_exp_month').value = js_pmt_array[index].exp_month;
  document.getElementById('payment_exp_year').value  = js_pmt_array[index].exp_year;
  document.getElementById('payment_cc_cvv2').value   = js_pmt_array[index].cvv2;
} 

function clearForm(type) {
  document.getElementById(type+'_address_id').value     = 0;
  document.getElementById(type+'_primary_name').value   = '';
  document.getElementById(type+'_contact').value        = '';
  document.getElementById(type+'_address1').value       = '';
  document.getElementById(type+'_address2').value       = '';
  document.getElementById(type+'_city_town').value      = '';
  document.getElementById(type+'_state_province').value = '';
  document.getElementById(type+'_postal_code').value    = '';
  document.getElementById(type+'_country_code').value   = '<?php echo COMPANY_COUNTRY; ?>';
  document.getElementById(type+'_telephone1').value     = '';
  document.getElementById(type+'_telephone2').value     = '';
  document.getElementById(type+'_telephone3').value     = '';
  document.getElementById(type+'_telephone4').value     = '';
  document.getElementById(type+'_email').value          = '';
  document.getElementById(type+'_website').value        = '';
  document.getElementById(type+'_notes').value          = '';
}

function clearPmtForm() {
  document.getElementById('payment_id').value                = 0;
  document.getElementById('payment_cc_name').value           = '';
  document.getElementById('payment_cc_number').value         = '';
  document.getElementById('payment_exp_month').selectedIndex = 0;
  document.getElementById('payment_exp_year').selectedIndex  = 0;
  document.getElementById('payment_cc_cvv2').value           = '';
}

function removeRow(type, id) {
  document.getElementById('del_add_id').value += ',' + id; // add to the list to delete
  document.getElementById(type + '_table').deleteRow(document.getElementById('tr_'+id).rowIndex);
}

function removePmtRow(id) {
  document.getElementById('del_pmt_id').value += ',' + id; // add to the list to delete
  document.getElementById('pmt_table').deleteRow(document.getElementById('trp_'+id).rowIndex);
}

function loadContacts() {
//  var guess = document.getElementById('dept_rep_id').value;
  var guess = document.getElementById('dept_rep_id').value;
//  document.getElementById('dept_rep_id').options[0].text = guess;
  if (guess.length < 3) return;
  $.ajax({
    type: "GET",
    contentType: "application/json; charset=utf-8",
    url: 'index.php?module=contacts&page=ajax&op=load_contact_info&guess='+guess,
    dataType: ($.browser.msie) ? "text" : "xml",
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
    },
	success: fillContacts
  });
}

// ajax response handler call back function
function fillContacts(sXml) {
  var xml = parseXml(sXml);
  if (!xml) return;
  while (document.getElementById('comboseldept_rep_id').options.length) document.getElementById('comboseldept_rep_id').remove(0);
  var iIndex = 0;
  $(xml).find("guesses").each(function() {
	newOpt = document.createElement("option");
	newOpt.text = $(this).find("guess").text() ? $(this).find("guess").text() : '<?php echo TEXT_FIND; ?>';
	document.getElementById('comboseldept_rep_id').options.add(newOpt);
	document.getElementById('comboseldept_rep_id').options[iIndex].value = $(this).find("id").text();
	if (!fActiveMenu) cbMmenuActivate('dept_rep_id', 'combodivdept_rep_id', 'comboseldept_rep_id', 'imgNamedept_rep_id');
	document.getElementById('dept_rep_id').focus();
	iIndex++;
  });
}

function editContact(id) {
  $.ajax({
    type: "GET",
    contentType: "application/json; charset=utf-8",
    url: 'index.php?module=contacts&page=ajax&op=load_contact&fID=fillContact&cID='+id,
    dataType: ($.browser.msie) ? "text" : "xml",
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert ("Ajax Error: " + XMLHttpRequest.responseText + "\nTextStatus: " + textStatus + "\nErrorThrown: " + errorThrown);
    },
	success: fillContactFields
  });
}

function fillContactFields(sXml) {
  var xml = parseXml(sXml);
  if (!xml) return;
  $(xml).find("Contact").each(function() {
	insertValue('i_id',             $(this).find("id").text());
	insertValue('i_short_name',     $(this).find("short_name").text());
	insertValue('i_contact_middle', $(this).find("contact_middle").text());
	insertValue('i_contact_first',  $(this).find("contact_first").text());
	insertValue('i_contact_last',   $(this).find("contact_last").text());
	insertValue('i_account_number', $(this).find("account_number").text());
	insertValue('i_gov_id_number',  $(this).find("gov_id_number").text());
  });
  $(xml).find("BillAddress").each(function() {
    var type = $(this).find("type").text();
	if (type.substr(1, 1) == 'm') {
	  insertValue('im_address_id',     $(this).find("address_id").text());
	  insertValue('im_primary_name',   $(this).find("primary_name").text());
	  insertValue('im_contact',        $(this).find("contact").text());
	  insertValue('im_telephone1',     $(this).find("telephone1").text());
	  insertValue('im_telephone2',     $(this).find("telephone2").text());
	  insertValue('im_telephone3',     $(this).find("telephone3").text());
	  insertValue('im_telephone4',     $(this).find("telephone4").text());
	  insertValue('im_email',          $(this).find("email").text());
	  insertValue('im_website',        $(this).find("website").text());
	  insertValue('im_address1',       $(this).find("address1").text());
	  insertValue('im_address2',       $(this).find("address2").text());
	  insertValue('im_city_town',      $(this).find("city_town").text());
	  insertValue('im_state_province', $(this).find("state_province").text());
	  insertValue('im_postal_code',    $(this).find("postal_code").text());
	  insertValue('im_country_code',   $(this).find("country_code").text());
	  insertValue('im_notes',          $(this).find("notes").text());
	}
  });
}

function clearContactForm() {
  insertValue('i_id',              '');
  insertValue('i_short_name',      '');
  insertValue('i_contact_middle',  '');
  insertValue('i_contact_first',   '');
  insertValue('i_contact_last',    '');
  insertValue('i_account_number',  '');
  insertValue('i_gov_id_number',   '');
  insertValue('im_address_id',     '');
  insertValue('im_primary_name',   '');
  insertValue('im_contact',        '');
  insertValue('im_telephone1',     '');
  insertValue('im_telephone2',     '');
  insertValue('im_telephone3',     '');
  insertValue('im_telephone4',     '');
  insertValue('im_email',          '');
  insertValue('im_website',        '');
  insertValue('im_address1',       '');
  insertValue('im_address2',       '');
  insertValue('im_city_town',      '');
  insertValue('im_state_province', '');
  insertValue('im_postal_code',    '');
  insertValue('im_country_code',   '');
  insertValue('im_notes',          '');
}

function copyContactAddress(type) {
  insertValue('im_primary_name',   document.getElementById(type+'m_primary_name').value);
  insertValue('im_contact',        document.getElementById(type+'m_contact').value);
  insertValue('im_telephone1',     document.getElementById(type+'m_telephone1').value);
  insertValue('im_telephone2',     document.getElementById(type+'m_telephone2').value);
  insertValue('im_telephone3',     document.getElementById(type+'m_telephone3').value);
  insertValue('im_telephone4',     document.getElementById(type+'m_telephone4').value);
  insertValue('im_email',          document.getElementById(type+'m_email').value);
  insertValue('im_website',        document.getElementById(type+'m_website').value);
  insertValue('im_address1',       document.getElementById(type+'m_address1').value);
  insertValue('im_address2',       document.getElementById(type+'m_address2').value);
  insertValue('im_city_town',      document.getElementById(type+'m_city_town').value);
  insertValue('im_state_province', document.getElementById(type+'m_state_province').value);
  insertValue('im_postal_code',    document.getElementById(type+'m_postal_code').value);
  insertValue('im_country_code',   document.getElementById(type+'m_country_code').value);
}

function addCRMRow() {
  var newCell;
  var cell;
  var newRow = document.getElementById('crm_notes').insertRow(-1);
  var rowCnt = newRow.rowIndex;
  cell  = '<td align="center">';
// Hidden fields
  cell += '<input type="hidden" name="im_note_id_'+rowCnt+'" id="im_note_id_'+rowCnt+'" value="" />';
  cell += buildIcon(icon_path+'16x16/emblems/emblem-unreadable.png', '<?php echo TEXT_DELETE; ?>', 'onclick="if (confirm(\'<?php echo TEXT_CRM_DELETE_MSG; ?>\')) removeCRMRow('+rowCnt+');"') + '</td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell = '<td class="main"><input type="text" name="crm_date_'+rowCnt+'" id="crm_date_'+rowCnt+'" value="<?php echo gen_locale_date(date('Y-m-d')); ?>" /></td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell = '<td nowrap="nowrap" class="main"><select name="crm_act_'+rowCnt+'" id="crm_act_'+rowCnt+'"></select></td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  cell = '<td class="main"><textarea name="crm_note_'+rowCnt+'" id="crm_note_'+rowCnt+'" cols="50" rows="1" maxlength="255"></textarea></td>';
  newCell = newRow.insertCell(-1);
  newCell.innerHTML = cell;
  // populate the drop down
  if (actions_list) buildDropDown('crm_act_'+rowCnt, actions_list, '');
  return rowCnt;
}

function removeCRMRow(id) {
  document.getElementById('del_crm_note').value += ',' + id; // add to the list to delete
  document.getElementById('crm_notes').deleteRow(document.getElementById('trn_'+id).rowIndex);
}

// -->
</script>