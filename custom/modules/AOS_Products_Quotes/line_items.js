/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2016 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for  technical reasons, the Appropriate Legal Notices must
 * display the words  "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

var lineno;
var prodln = 0;
var servln = 0;
var groupn = 0;
var group_ids = {};


/**
 * Load Line Items
 */

function insertLineItems(product,group){

  var type = 'product_';
  var ln = 0;
  var current_group = 'lineItems';
  var gid = product.group_id;

  if(typeof group_ids[gid] === 'undefined'){
    current_group = insertGroup();
    group_ids[gid] = current_group;
    
    for(var g in group){
      if(document.getElementById('group'+current_group + g) !== null){
        document.getElementById('group'+current_group + g).value = group[g];
      }
    }
  } else {
    current_group = group_ids[gid];
  }

  if(product.product_id != '0' && product.product_id !== ''){
    ln = insertProductLine('product_group'+current_group,current_group, product);
    type = 'product_';
  } else {
    ln = insertServiceLine('service_group'+current_group,current_group);
    type = 'service_';
  }

  for(var p in product){
    if(document.getElementById(type + p + ln) !== null){
      if(product[p] !== '' && isNumeric(product[p]) && p != 'product_id' && p != 'name' && p != "part_number"){
        document.getElementById(type + p + ln).value = format2Number(product[p]);
      } else {
        document.getElementById(type + p + ln).value = product[p];
      }
    }
  }
  //document.getElementById('group'+ln+'catbtn').className = "hide";
  //calculateLine(ln,type);

}


/**
 * Insert product line
 */

function insertProductLine(tableid, groupid, product) {

  if(!enable_groups){
    tableid = "product_group0";
  }

  if (document.getElementById(tableid + '_head') !== null) {
    document.getElementById(tableid + '_head').style.display = "";
  }

  var vat_hidden = document.getElementById("vathidden").value;
  var discount_hidden = document.getElementById("discounthidden").value;

  sqs_objects["product_name[" + prodln + "]"] = {
    "form": "EditView",
    "method": "query",
    "modules": ["AOS_Products"],
    "group": "or",
    "field_list": ["name", "id", "part_number", "cost", "price", "description", "currency_id"],
    "populate_list": ["product_name[" + prodln + "]", "product_product_id[" + prodln + "]", "product_part_number[" + prodln + "]", "product_product_cost_price[" + prodln + "]", "product_product_list_price[" + prodln + "]", "product_item_description[" + prodln + "]", "product_currency[" + prodln + "]"],
    "required_list": ["product_id[" + prodln + "]"],
    "conditions": [{
      "name": "name",
      "op": "like_custom",
      "end": "%",
      "value": ""
    }],
    "order": "name",
    "limit": "30",
    "post_onblur_function": "formatListPrice(" + prodln + ");",
    "no_match_text": "No Match"
  };
  sqs_objects["product_part_number[" + prodln + "]"] = {
    "form": "EditView",
    "method": "query",
    "modules": ["AOS_Products"],
    "group": "or",
    "field_list": ["part_number", "name", "id","cost", "price","description","currency_id"],
    "populate_list": ["product_part_number[" + prodln + "]", "product_name[" + prodln + "]", "product_product_id[" + prodln + "]",  "product_product_cost_price[" + prodln + "]", "product_product_list_price[" + prodln + "]", "product_item_description[" + prodln + "]", "product_currency[" + prodln + "]"],
    "required_list": ["product_id[" + prodln + "]"],
    "conditions": [{
      "name": "part_number",
      "op": "like_custom",
      "end": "%",
      "value": ""
    }],
    "order": "name",
    "limit": "30",
    "post_onblur_function": "formatListPrice(" + prodln + ");",
    "no_match_text": "No Match"
  };

  tablebody = document.createElement("tbody");
  tablebody.id = "product_body" + prodln;
  document.getElementById(tableid).appendChild(tablebody);


  var x = tablebody.insertRow(-1);
  x.id = 'product_line' + prodln;

  var b = x.insertCell(0);
  b.innerHTML = "<b>"+SUGAR.language.get(module_sugar_grp1, 'LBL_PRODUCT_NAME')+': '+product.name+"</b><input class='sqsEnabled product_name' autocomplete='off' type='hidden' name='product_name[" + prodln + "]' id='product_name" + prodln + "' maxlength='50' value='' title='' tabindex='116' value=''><input type='hidden' name='product_product_id[" + prodln + "]' id='product_product_id" + prodln + "'  maxlength='50' value=''><br/>";

  if (typeof currencyFields !== 'undefined'){

    currencyFields.push("product_product_list_price" + prodln);
    currencyFields.push("product_product_cost_price" + prodln);

  }

  b.innerHTML += "<input type='hidden' name='product_product_unit_price[" + prodln + "]' id='product_product_unit_price" + prodln + "' maxlength='50' value='' title='' tabindex='116' readonly='readonly' onblur='calculateLine(" + prodln + ",\"product_\");' onblur='calculateLine(" + prodln + ",\"product_\");' />";
  b.innerHTML += "<input type='hidden' name='product_vat_amt[" + prodln + "]' id='product_vat_amt" + prodln + "' value='0' class='product_vat_amt_text' />";
  b.innerHTML += "<input type='hidden' name='product_product_discount[" + prodln + "]' id='product_product_discount" + prodln + "' value='0' title=''class='product_discount_text' /><input type='hidden' name='product_product_discount_amount[" + prodln + "]' id='product_product_discount_amount" + prodln + "' value='0'  />";
  b.innerHTML += "<input type='hidden' name='product_discount[" + prodln + "]' id='product_discount" + prodln + "' value='Percentage'/>";
  b.innerHTML += "<input type='hidden' name='product_vat[" + prodln + "]' id='product_vat" + prodln + "' value='0' </input>";
  b.innerHTML += "<input type='hidden' name='product_product_qty[" + prodln + "]' id='product_product_qty" + prodln + "'  value='1' class='product_qty'>";
  b.innerHTML += "<input class='product_part_number' type='hidden' name='product_part_number[" + prodln + "]' id='product_part_number" + prodln + "' value='"+product.part_number+"'>";
  b.innerHTML += "<input type='hidden' name='product_product_list_price[" + prodln + "]' id='product_product_list_price" + prodln + "' value='"+product.price+"' /><input type='hidden' name='product_product_cost_price[" + prodln + "]' id='product_product_cost_price" + prodln + "' value='"+product.cost+"'  />";
  b.innerHTML += "<input type='hidden' name='product_product_total_price[" + prodln + "]' id='product_product_total_price" + prodln + "' maxlength='50' value='' title='' tabindex='116' readonly='readonly' /><input type='hidden' name='product_group_number[" + prodln + "]' id='product_group_number" + prodln + "' value='"+groupid+"'>";
  
  b.innerHTML += "<b>"+SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_MODEL')+': '+product.charge_model_id_c_picklist+"</b><input type='hidden' name='product_charge_model_id_c[" + prodln + "]' id='product_charge_model_id_c" + prodln + "' value='"+product.charge_model_id_c+"'><br/>";
  b.innerHTML += "<b>"+SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_TYPE')+': '+product.charge_type_id_c_picklist+"</b><input type='hidden' name='product_charge_type_id_c[" + prodln + "]' id='product_charge_type_id_c" + prodln + "' value='"+product.charge_type_id_c+"'><br/>";
  b.innerHTML += "<b>"+SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_TYPE')+': '+product.uom_c+"</b><input type='hidden' name='product_uom_c[" + prodln + "]' id='product_uom_c" + prodln + "' value='"+product.uom_c+"'>";

  b.colSpan = "9";
  
  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("product_product_unit_price" + prodln);
  }

  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("product_product_total_price" + prodln);
  }
  
  /*
  var c = x.insertCell(1);
  c.colSpan = "2";
  c.innerHTML = "<b>"+SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_MODEL')+': '+product.charge_model_id_c_picklist+"</b><input type='hidden' name='product_charge_model_id_c[" + prodln + "]' id='product_charge_model_id_c" + prodln + "' value='"+product.charge_model_id_c+"'><br/>";
  c.innerHTML += "<b>"+SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_TYPE')+': '+product.charge_type_id_c_picklist+"</b><input type='hidden' name='product_charge_type_id_c[" + prodln + "]' id='product_charge_type_id_c" + prodln + "' value='"+product.charge_type_id_c+"'><br/>";
  c.innerHTML += "<b>"+SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_TYPE')+': '+product.uom_c+"</b><input type='hidden' name='product_uom_c[" + prodln + "]' id='product_uom_c" + prodln + "' value='"+product.uom_c+"'>";

  var d = x.insertCell(2);
  d.colSpan = "2";
  d.innerHTML = "<b>"+product.charge_type_id_c_picklist+"</b><input type='hidden' name='product_charge_type_id_c[" + prodln + "]' id='product_charge_type_id_c" + prodln + "' value='"+product.charge_type_id_c+"'>";

  var e = x.insertCell(3);
  e.colSpan = "2";
  e.innerHTML = "<b>"+product.uom_c+"</b><input type='hidden' name='product_uom_c[" + prodln + "]' id='product_uom_c" + prodln + "' value='"+product.uom_c+"'>";
  */
  var h = x.insertCell(1);
  h.innerHTML = "<input type='hidden' name='product_currency[" + prodln + "]' id='product_currency" + prodln + "' value=''><input type='hidden' name='product_deleted[" + prodln + "]' id='product_deleted" + prodln + "' value='0'><input type='hidden' name='product_id[" + prodln + "]' id='product_id" + prodln + "' value=''><button type='button' id='product_delete_line" + prodln + "' class='button product_delete_line hide' value='" + SUGAR.language.get(module_sugar_grp1, 'LBL_REMOVE_PRODUCT_LINE') + "' tabindex='116' onclick='markLineDeleted(" + prodln + ",\"product_\")'><span class=\"suitepicon suitepicon-action-clear\"></span></button><br>";

  enableQS(true);
  //QSFieldsArray["EditView_product_name"+prodln].forceSelection = true;

  var arrPriceBook = JSON.parse(product.charge_price_table_c);
  
  var chargePriceLineItem = '' 
  chargePriceLineItem += insertChargePriceLineHeader();
    
  var count = 1;
  for (var key in arrPriceBook) {
    if (arrPriceBook.hasOwnProperty(key)) {
      var chargePrice = arrPriceBook[key];
      chargePrice.ending_unit = chargePrice.end_unit;
      chargePrice.amount = chargePrice.list_price;
      chargePriceLineItem += insertChargePriceLine(chargePrice, count, product.product_id);
    }
    count = count + 1;
  }
  
  if(chargePriceLineItem != '') {
    var charges = tablebody.insertRow(-1);
    chargePriceLineItem = "<div style='width:80%;margin:0px auto;'>"+ chargePriceLineItem +"</div>";
    charges.id = 'product_charges_line' + prodln;
    var ch = charges.insertCell(0);
    ch.colSpan = "9";
    ch.style.color = "rgb(68,68,68)";
    ch.style.background = "rgb(245,235,240)";
    ch.innerHTML = chargePriceLineItem;
  }
  
  var y = tablebody.insertRow(-1);
  y.id = 'product_note_line' + prodln;

  var h1 = y.insertCell(0);
  h1.colSpan = "4";
  h1.style.color = "rgb(68,68,68)";
  h1.innerHTML = "<span style='vertical-align: top;' class='product_item_description_label'>" + SUGAR.language.get(module_sugar_grp1, 'LBL_PRODUCT_DESCRIPTION') + " :&nbsp;&nbsp;</span>";
  h1.innerHTML += "<textarea tabindex='116' name='product_item_description[" + prodln + "]' id='product_item_description" + prodln + "' rows='2' cols='23' class='product_item_description'></textarea>&nbsp;&nbsp;";

  var i = y.insertCell(1);
  i.colSpan = "5";
  i.style.color = "rgb(68,68,68)";
  i.innerHTML = "<span style='vertical-align: top;' class='product_description_label'>"  + SUGAR.language.get(module_sugar_grp1, 'LBL_PRODUCT_NOTE') + " :&nbsp;</span>";
  i.innerHTML += "<textarea tabindex='116' name='product_description[" + prodln + "]' id='product_description" + prodln + "' rows='2' cols='23' class='product_description'></textarea>&nbsp;&nbsp;"

  addToValidate('EditView','product_product_id'+prodln,'id',true,"Please choose a product");

  addAlignedLabels(prodln, 'product');

  prodln++;

  return prodln - 1;
}

function insertCategoryProductLine(tableid, groupid, products) {
  
  if(!enable_groups){
    tableid = "product_group0";
  }

  if (document.getElementById(tableid + '_head') !== null) {
    document.getElementById(tableid + '_head').style.display = "";
  }
 
  var vat_hidden = document.getElementById("vathidden").value;
  var discount_hidden = document.getElementById("discounthidden").value;

  sqs_objects["product_name[" + prodln + "]"] = {
    "form": "EditView",
    "method": "query",
    "modules": ["AOS_Products"],
    "group": "or",
    "field_list": ["name", "id", "part_number", "cost", "price", "description", "currency_id"],
    "populate_list": ["product_name[" + prodln + "]", "product_product_id[" + prodln + "]", "product_part_number[" + prodln + "]", "product_product_cost_price[" + prodln + "]", "product_product_list_price[" + prodln + "]", "product_item_description[" + prodln + "]", "product_currency[" + prodln + "]"],
    "required_list": ["product_id[" + prodln + "]"],
    "conditions": [{
      "name": "name",
      "op": "like_custom",
      "end": "%",
      "value": ""
    }],
    "order": "name",
    "limit": "30",
    "post_onblur_function": "formatListPrice(" + prodln + ");",
    "no_match_text": "No Match"
  };
  
  tablebody = document.createElement("tbody");
  tablebody.id = "product_body" + prodln;
  tablebody.style.padding = "5px";
  
  document.getElementById(tableid).appendChild(tablebody);


  var x = tablebody.insertRow(-1);
  x.id = 'product_line' + prodln;

  var b = x.insertCell(0);
  b.colSpan = "3";
  b.innerHTML = "<b>"+products.name+"</b><input class='sqsEnabled product_name' autocomplete='off' type='hidden' name='product_name[" + prodln + "]' id='product_name" + prodln + "' maxlength='50' value='"+products.name+"' title='' tabindex='116' value=''><input type='hidden' name='product_product_id[" + prodln + "]' id='product_product_id" + prodln + "'  maxlength='50' value='"+products.id+"'>";

 /* var c = x.insertCell(1);
  c.innerHTML = "";
  c.colSpan = "2";*/
  if (typeof currencyFields !== 'undefined'){

    currencyFields.push("product_product_list_price" + prodln);
    currencyFields.push("product_product_cost_price" + prodln);

  }

  //var d = x.insertCell(5);
  //d.innerHTML = "<input type='text' name='product_product_discount[" + prodln + "]' id='product_product_discount" + prodln + "'  maxlength='50' value='' title='' tabindex='116' onblur='calculateLine(" + prodln + ",\"product_\");' onblur='calculateLine(" + prodln + ",\"product_\");' class='product_discount_text'><input type='hidden' name='product_product_discount_amount[" + prodln + "]' id='product_product_discount_amount" + prodln + "' value=''  />";
  //d.innerHTML += "<select tabindex='116' name='product_discount[" + prodln + "]' id='product_discount" + prodln + "' onchange='calculateLine(" + prodln + ",\"product_\");' class='product_discount_amount_select'>" + discount_hidden + "</select>";

  //var e = x.insertCell(2);
  b.innerHTML += "<input type='hidden' name='product_product_unit_price[" + prodln + "]' id='product_product_unit_price" + prodln + "' maxlength='50' value='' title='' tabindex='116' readonly='readonly' />";
  b.innerHTML += "<input type='hidden' name='product_vat_amt[" + prodln + "]' id='product_vat_amt" + prodln + "' value='0' class='product_vat_amt_text' />";
  b.innerHTML += "<input type='hidden' name='product_product_discount[" + prodln + "]' id='product_product_discount" + prodln + "' value='0' title=''class='product_discount_text' /><input type='hidden' name='product_product_discount_amount[" + prodln + "]' id='product_product_discount_amount" + prodln + "' value='0'  />";
  b.innerHTML += "<input type='hidden' name='product_discount[" + prodln + "]' id='product_discount" + prodln + "' value='Percentage'/>";
  b.innerHTML += "<input type='hidden' name='product_vat[" + prodln + "]' id='product_vat" + prodln + "' value='0' </input>";
  b.innerHTML += "<input type='hidden' name='product_product_qty[" + prodln + "]' id='product_product_qty" + prodln + "'  value='1' class='product_qty'>";
  b.innerHTML += "<input class='product_part_number' type='hidden' name='product_part_number[" + prodln + "]' id='product_part_number" + prodln + "' value='"+products.part_number+"'>";
  b.innerHTML += "<input type='hidden' name='product_product_list_price[" + prodln + "]' id='product_product_list_price" + prodln + "' value='"+products.price+"' /><input type='hidden' name='product_product_cost_price[" + prodln + "]' id='product_product_cost_price" + prodln + "' value='"+products.cost+"'  />";
  b.innerHTML += "<input type='hidden' name='product_product_total_price[" + prodln + "]' id='product_product_total_price" + prodln + "' maxlength='50' value='' title='' tabindex='116' readonly='readonly' /><input type='hidden' name='product_group_number[" + prodln + "]' id='product_group_number" + prodln + "' value='"+groupid+"'>";
  //e.colSpan = "2";
  
  var c = x.insertCell(1);
  c.colSpan = "2";
  c.innerHTML = "<b>"+products.charge_model_id_c_picklist+"</b><input type='hidden' name='product_charge_model_id_c[" + prodln + "]' id='product_charge_model_id_c" + prodln + "' value='"+products.charge_model_id_c+"'>";

  var d = x.insertCell(2);
  d.colSpan = "2";
  d.innerHTML = "<b>"+products.charge_type_id_c_picklist+"</b><input type='hidden' name='product_charge_type_id_c[" + prodln + "]' id='product_charge_type_id_c" + prodln + "' value='"+products.charge_type_id_c+"'>";

  var e = x.insertCell(3);
  e.colSpan = "2";
  e.innerHTML = "<b>"+products.uom_c+"</b><input type='hidden' name='product_uom_c[" + prodln + "]' id='product_uom_c" + prodln + "' value='"+products.uom_c+"'>";


  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("product_product_unit_price" + prodln);
  }

  //var f = x.insertCell(7);
  //f.innerHTML = "<input type='text' name='product_vat_amt[" + prodln + "]' id='product_vat_amt" + prodln + "' maxlength='250' value='' title='' tabindex='116' readonly='readonly' class='product_vat_amt_text'>";
  //f.innerHTML += "<select tabindex='116' name='product_vat[" + prodln + "]' id='product_vat" + prodln + "' onchange='calculateLine(" + prodln + ",\"product_\");' class='product_vat_amt_select'>" + vat_hidden + "</select>";

  //if (typeof currencyFields !== 'undefined'){
  // currencyFields.push("product_vat_amt" + prodln);
  //}
  //var g = x.insertCell(3);
  //g.innerHTML = "<input type='text' name='product_product_total_price[" + prodln + "]' id='product_product_total_price" + prodln + "' maxlength='50' value='' title='' tabindex='116' readonly='readonly' /><input type='hidden' name='product_group_number[" + prodln + "]' id='product_group_number" + prodln + "' value='"+groupid+"'>";

  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("product_product_total_price" + prodln);
  }
  var h = x.insertCell(4);
  h.innerHTML = "<input type='hidden' name='product_currency[" + prodln + "]' id='product_currency" + prodln + "' value='"+products.currency_id+"'><input type='hidden' name='product_deleted[" + prodln + "]' id='product_deleted" + prodln + "' value='0'><input type='hidden' name='product_id[" + prodln + "]' id='product_id" + prodln + "' value=''><button type='button' id='product_delete_line" + prodln + "' class='button product_delete_line hide' value='" + SUGAR.language.get(module_sugar_grp1, 'LBL_REMOVE_PRODUCT_LINE') + "' tabindex='116' onclick='markLineDeleted(" + prodln + ",\"product_\")'><span class=\"suitepicon suitepicon-action-clear\"><br>";


  enableQS(true);
  //QSFieldsArray["EditView_product_name"+prodln].forceSelection = true;
  
  var arrPriceBook = products.pricebook;
  
  var chargePriceLineItem = '' 
  if((arrPriceBook != 'undefined' || (typeof arrPriceBook != 'undefined')) && Array.isArray(arrPriceBook)){
    chargePriceLineItem += insertChargePriceLineHeader();
    for (var k = 0; k < arrPriceBook.length; k++) {
        var chargePrice = arrPriceBook[k];
        var count = k+1;
        chargePriceLineItem += insertChargePriceLine(chargePrice, count, products.id);
    }
  }
  
  if(chargePriceLineItem != '') {
    var charges = tablebody.insertRow(-1);
    chargePriceLineItem = "<div style='width:80%;margin:0px auto;'>"+ chargePriceLineItem +"</div>";
    charges.id = 'product_charges_line' + prodln;
    var ch = charges.insertCell(0);
    ch.colSpan = "9";
    ch.style.color = "rgb(68,68,68)";
    ch.style.background = "rgb(245,235,240)";
    ch.innerHTML = chargePriceLineItem;
  }
  
  var y = tablebody.insertRow(-1);
  y.id = 'product_note_line' + prodln;

  var h1 = y.insertCell(0);
  h1.colSpan = "5";
  h1.style.color = "rgb(68,68,68)";
  h1.style.padding = "0px 0px 10px 0px";
  h1.innerHTML = "<span style='vertical-align: top;' class='product_item_description_label'>" + SUGAR.language.get(module_sugar_grp1, 'LBL_PRODUCT_DESCRIPTION') + " :&nbsp;&nbsp;</span>";
  h1.innerHTML += "<textarea tabindex='116' name='product_item_description[" + prodln + "]' id='product_item_description" + prodln + "' rows='2' cols='23' class='product_item_description'>"+products.description+"</textarea>&nbsp;&nbsp;";

  var i = y.insertCell(1);
  i.colSpan = "5";
  i.style.color = "rgb(68,68,68)";
  i.style.padding = "0px 0px 10px 0px";
  i.innerHTML = "<span style='vertical-align: top;' class='product_description_label'>"  + SUGAR.language.get(module_sugar_grp1, 'LBL_PRODUCT_NOTE') + " :&nbsp;</span>";
  i.innerHTML += "<textarea tabindex='116' name='product_description[" + prodln + "]' id='product_description" + prodln + "' rows='2' cols='23' class='product_description'></textarea>&nbsp;&nbsp;"

  addToValidate('EditView','product_product_id'+prodln,'id',true,"Please choose a product");

  addAlignedLabels(prodln, 'product');
  //calculateLine(prodln,"product_");
  prodln++;

  return prodln - 1;
}

var insertChargePriceLineHeader = function() {
    var chargePriceLineItem = '<div class="pricetable_body" style="width:100%;float:left;overflow:hidden;background: #c5e9fd;overflow: hidden;padding: 5px;">';
    chargePriceLineItem += '<div class="priceitem">';
    chargePriceLineItem += '<div style="width:20%;float:left;"><b>'+SUGAR.language.get(module_sugar_grp1, 'LBL_SR_NO')+'</b></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><b>'+SUGAR.language.get(module_sugar_grp1, 'LBL_FROM')+'</b></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><b>'+SUGAR.language.get(module_sugar_grp1, 'LBL_TO')+'</b></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><b>'+SUGAR.language.get(module_sugar_grp1, 'LBL_LIST_PRICE')+'</b></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><b>'+SUGAR.language.get(module_sugar_grp1, 'LBL_PRICE_FORMAT')+'</b></div>';
    chargePriceLineItem += '</div>';
    chargePriceLineItem += '</div>';
    
    return chargePriceLineItem;
}

var insertChargePriceLine = function(chargePrice, k, chargeId) {
   var chargePriceLineItem = '<div class="pricetable_body" style="width:100%;float:left;overflow:hidden;padding: 5px;">';
    chargePriceLineItem += '<div class="priceitem">';
    chargePriceLineItem += '<div style="width:20%;float:left;"><input name="product_priceTables_'+chargeId+'['+k+'][id]" value="'+chargePrice.id+'" type="hidden"><input name="product_priceTables_'+chargeId+'['+k+'][productid]" value="'+chargeId+'" type="hidden">';
    chargePriceLineItem += '<span class="tier_value">'+k+'</span></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><input class="tier_value" name="product_priceTables_'+chargeId+'['+k+'][tiervalue]" oldvalue="'+chargePrice.tier_price+'" value="'+chargePrice.tier_price+'" type="hidden">';
    chargePriceLineItem += '<input format="true" class="formtext" extname="From" title="Starting Unit" name="product_priceTables_'+chargeId+'['+k+'][starting_unit]" value="'+chargePrice.starting_unit+'" style="width: 120px;" type="text"></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><input format="true" class="formtext" extname="To" title="Ending Unit" name="product_priceTables_'+chargeId+'['+k+'][end_unit]" value="'+chargePrice.ending_unit+'" style="width: 120px;" type="text"></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><span class="listprice">';
    chargePriceLineItem += '<input format="true" extname="price" name="product_priceTables_'+chargeId+'['+k+'][list_price]" value="'+chargePrice.amount+'" class="formtext" style="width: 120px;" type="text">';
    //chargePriceLineItem += '&nbsp;'+chargePrice.currency_id+'&nbsp;';
    chargePriceLineItem += '</span></div>';
    chargePriceLineItem += '<div style="width:20%;float:left;"><select name="product_priceTables_'+chargeId+'['+k+'][price_format]" class="formselect">';
    var selectedFlatfee = '';
    var selectedPerUnit = '';
    
    if(chargePrice.price_format == 'FlatFee'){
        selectedFlatfee = 'selected="selected"';
    } else if(chargePrice.price_format == 'PerUnit'){
        selectedPerUnit = 'selected="selected"';
    }
    chargePriceLineItem += '<option value="FlatFee" '+selectedFlatfee+'>Flat Fee</option>';
    chargePriceLineItem += '<option value="PerUnit" '+selectedPerUnit+'>Per Unit</option>';
    chargePriceLineItem += '</select></div>';
    chargePriceLineItem += '</div>';
    chargePriceLineItem += '</div>';
    
    return chargePriceLineItem;
}

var addAlignedLabels = function(ln, type) {
  if(typeof type == 'undefined') {
    type = 'product';
  }
  if(type != 'product' && type != 'service') {
    console.error('type could be "product" or "service" only');
  }
  var labels = [];
  $('tr#'+type+'_head td').each(function(i,e){
    if(type=='product' && $(e).attr('colspan')>1) {
      for(var i=0; i<parseInt($(e).attr('colspan')); i++) {
        if(i==0) {
          labels.push($(e).html());
        } else {
          labels.push('');
        }
      }
    } else {
      labels.push($(e).html());
    }
  });
  $('tr#'+type+'_line'+ln+' td').each(function(i,e){
    $(e).prepend('<span class="alignedLabel">'+labels[i]+'</span>');
  });
}


/**
 * Open product popup
 */
function openProductPopup(ln){

  lineno=ln;
  var popupRequestData = {
    "call_back_function" : "setProductReturn",
    "form_name" : "EditView",
    "field_to_name_array" : {
      "id" : "product_product_id" + ln,
      "name" : "product_name" + ln,
      "description" : "product_item_description" + ln,
      "part_number" : "product_part_number" + ln,
      "cost" : "product_product_cost_price" + ln,
      "price" : "product_product_list_price" + ln,
      "currency_id" : "product_currency" + ln
    }
  };

  open_popup('AOS_Products', 800, 850, '', true, true, popupRequestData);

}

/**
 * Open product popup
 */
function openProductCategoryPopup(ln){

  lineno=ln;
  var popupRequestData = {
    "call_back_function" : "setProductCategoryReturn",
    "form_name" : "EditView",
    "field_to_name_array" : {
      "id" : "group"+ln+"category_id",
      "name" : "group"+ln+"name",
    }
  };

  open_popup('AOS_Product_Categories', 800, 850, '', true, true, popupRequestData);
}

function setProductCategoryReturn(popupReplyData){
  set_return(popupReplyData);
  categoryKey = "group"+lineno+"category_id",
  categoryId = popupReplyData.name_to_value_array[categoryKey].replace(/&amp;/gi, '&').replace(/&lt;/gi, '<').replace(/&gt;/gi, '>').replace(/&#039;/gi, '\'').replace(/&quot;/gi, '"');
  set_productLineItems(categoryId);
  tableid = "product_group"+lineno;
 }

function set_productLineItems(categoryId) {
    $.ajax({
            url: 'index.php?entryPoint=getCategoryProducts',
            type: 'GET',
            contentType: 'JSON',
            data: {category_id: categoryId},
            success: function (response) {
                tableid = "product_group"+lineno;
                document.getElementById("group"+lineno+"category_id").value = categoryId;
                document.getElementById('group'+lineno+'catbtn').className = "hide";
                
                var jsonData = JSON.parse(response);
                for (var i = 0; i < jsonData.length; i++) {
                     var products = jsonData[i];
                     insertCategoryProductLine(tableid, lineno, products);
                     //formatListPrice(lineno);
                 }
            },
            error: function(response) {
                alert('No Products found for selected category');
            }
        });
}

function setProductReturn(popupReplyData){
  set_return(popupReplyData);
  formatListPrice(lineno);
}

function formatListPrice(ln){

  if (typeof currencyFields !== 'undefined'){
    var product_currency_id = -99;
    if(document.getElementById('product_currency' + ln) != null){
        product_currency_id = document.getElementById('product_currency' + ln).value;
    }
    
    product_currency_id = product_currency_id ? product_currency_id : -99;//Assume base currency if no id
    var product_currency_rate = get_rate(product_currency_id);
    var dollar_product_price = ConvertToDollar(document.getElementById('product_product_list_price' + ln).value, product_currency_rate);
    document.getElementById('product_product_list_price' + ln).value = format2Number(ConvertFromDollar(dollar_product_price, lastRate));
    var dollar_product_cost = ConvertToDollar(document.getElementById('product_product_cost_price' + ln).value, product_currency_rate);
    document.getElementById('product_product_cost_price' + ln).value = format2Number(ConvertFromDollar(dollar_product_cost, lastRate));
  }
  else
  {
    document.getElementById('product_product_list_price' + ln).value = format2Number(document.getElementById('product_product_list_price' + ln).value);
    document.getElementById('product_product_cost_price' + ln).value = format2Number(document.getElementById('product_product_cost_price' + ln).value);
  }

  //calculateLine(ln,"product_");
}


/**
 * Insert Service Line
 */

function insertServiceLine(tableid, groupid) {

  if(!enable_groups){
    tableid = "service_group0";
  }
  if (document.getElementById(tableid + '_head') !== null) {
    document.getElementById(tableid + '_head').style.display = "";
  }

  var vat_hidden = document.getElementById("vathidden").value;
  var discount_hidden = document.getElementById("discounthidden").value;

  tablebody = document.createElement("tbody");
  tablebody.id = "service_body" + servln;
  document.getElementById(tableid).appendChild(tablebody);

  var x = tablebody.insertRow(-1);
  x.id = 'service_line' + servln;

  var a = x.insertCell(0);
  a.colSpan = "4";
  a.innerHTML = "<textarea name='service_name[" + servln + "]' id='service_name" + servln + "'  cols='64' title='' tabindex='116' class='service_name'></textarea><input type='hidden' name='service_product_id[" + servln + "]' id='service_product_id" + servln + "'  maxlength='50' value='0'>";

  var a1 = x.insertCell(1);
  a1.innerHTML = "<input type='text' name='service_product_list_price[" + servln + "]' id='service_product_list_price" + servln + "' maxlength='50' value='' title='' tabindex='116'   onblur='calculateLine(" + servln + ",\"service_\");' class='service_list_price'>";

  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("service_product_list_price" + servln);
  }

  var a2 = x.insertCell(2);
  a2.innerHTML = "<input type='text' name='service_product_discount[" + servln + "]' id='service_product_discount" + servln + "'  maxlength='50' value='' title='' tabindex='116' onblur='calculateLine(" + servln + ",\"service_\");' onblur='calculateLine(" + servln + ",\"service_\");' class='service_discount_text'><input type='hidden' name='service_product_discount_amount[" + servln + "]' id='service_product_discount_amount" + servln + "' value=''/>";
  a2.innerHTML += "<select tabindex='116' name='service_discount[" + servln + "]' id='service_discount" + servln + "' onchange='calculateLine(" + servln + ",\"service_\");' class='service_discount_select'>" + discount_hidden + "</select>";

  var b = x.insertCell(3);
  b.innerHTML = "<input type='text' name='service_product_unit_price[" + servln + "]' id='service_product_unit_price" + servln + "' maxlength='50' value='' title='' tabindex='116'   onblur='calculateLine(" + servln + ",\"service_\");' class='service_unit_price'>";

  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("service_product_unit_price" + servln);
  }
  var c = x.insertCell(4);
  c.innerHTML = "<input type='text' name='service_vat_amt[" + servln + "]' id='service_vat_amt" + servln + "' maxlength='250' value='' title='' tabindex='116' readonly='readonly' class='service_vat_text'>";
  c.innerHTML += "<select tabindex='116' name='service_vat[" + servln + "]' id='service_vat" + servln + "' onchange='calculateLine(" + servln + ",\"service_\");' class='service_vat_select'>" + vat_hidden + "</select>";
  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("service_vat_amt" + servln);
  }

  var e = x.insertCell(5);
  e.innerHTML = "<input type='text' name='service_product_total_price[" + servln + "]' id='service_product_total_price" + servln + "' maxlength='50' value='' title='' tabindex='116' readonly='readonly' class='service_total_price'><input type='hidden' name='service_group_number[" + servln + "]' id='service_group_number" + servln + "' value='"+ groupid +"'>";

  if (typeof currencyFields !== 'undefined'){
    currencyFields.push("service_product_total_price" + servln);
  }
  var f = x.insertCell(6);
  f.innerHTML = "<input type='hidden' name='service_deleted[" + servln + "]' id='service_deleted" + servln + "' value='0'><input type='hidden' name='service_id[" + servln + "]' id='service_id" + servln + "' value=''><button type='button' class='button service_delete_line' id='service_delete_line" + servln + "' value='" + SUGAR.language.get(module_sugar_grp1, 'LBL_REMOVE_PRODUCT_LINE') + "' tabindex='116' onclick='markLineDeleted(" + servln + ",\"service_\")'><span class=\"suitepicon suitepicon-action-clear\"></span></button><br>";

  addAlignedLabels(servln, 'service');

  servln++;

  return servln - 1;
}


/**
 * Insert product Header
 */

function insertProductHeader(tableid){
  tablehead = document.createElement("thead");
  tablehead.id = tableid +"_head";
  tablehead.style.display="none";
  document.getElementById(tableid).appendChild(tablehead);

  var x=tablehead.insertRow(-1);
  x.id='product_head';
 
  var b=x.insertCell(0);
  b.style.color="rgb(68,68,68)";
  b.colSpan = "3";
  b.style.background = "rgb(228, 228, 228)";
  b.style.padding = "5px";
  b.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_PRODUCT_NAME');
  
  var ac=x.insertCell(1);
  ac.style.color="rgb(68,68,68)";
  ac.colSpan = "2";
  ac.style.background = "rgb(228, 228, 228)";
  ac.style.padding = "5px";
  ac.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_MODEL');
  
  var d=x.insertCell(2);
  d.style.color="rgb(68,68,68)";
  d.colSpan = "2";
  d.style.background = "rgb(228, 228, 228)";
  d.style.padding = "5px";
  d.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_CHARGE_TYPE');
  
  var e=x.insertCell(3);
  e.style.color="rgb(68,68,68)";
  e.colSpan = "2";
  e.style.background = "rgb(228, 228, 228)";
  e.style.padding = "5px";
  e.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_UOM');

  var h=x.insertCell(4);
  h.style.color="rgb(68,68,68)";
  h.style.background = "rgb(228, 228, 228)";
  h.style.padding = "5px";
  h.innerHTML='&nbsp;';
}


/**
 * Insert service Header
 */

function insertServiceHeader(tableid){
  tablehead = document.createElement("thead");
  tablehead.id = tableid +"_head";
  tablehead.style.display="none";
  document.getElementById(tableid).appendChild(tablehead);

  var x=tablehead.insertRow(-1);
  x.id='service_head';

  var a=x.insertCell(0);
  a.colSpan = "4";
  a.style.color="rgb(68,68,68)";
  a.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_SERVICE_NAME');

  var b=x.insertCell(1);
  b.style.color="rgb(68,68,68)";
  b.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_SERVICE_LIST_PRICE');

  var c=x.insertCell(2);
  c.style.color="rgb(68,68,68)";
  c.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_SERVICE_DISCOUNT');

  var d=x.insertCell(3);
  d.style.color="rgb(68,68,68)";
  d.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_SERVICE_PRICE');

  var e=x.insertCell(4);
  e.style.color="rgb(68,68,68)";
  e.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_VAT_AMT');

  var f=x.insertCell(5);
  f.style.color="rgb(68,68,68)";
  f.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_TOTAL_PRICE');

  var g=x.insertCell(6);
  g.style.color="rgb(68,68,68)";
  g.innerHTML='&nbsp;';
}

/**
 * Insert Group
 */

function insertGroup()
{

  if(!enable_groups && groupn > 0){
    return;
  }
  var tableBody = document.createElement("tr");
  tableBody.id = "group_body"+groupn;
  tableBody.className = "group_body";
  document.getElementById('lineItems').appendChild(tableBody);

  var a=tableBody.insertCell(0);
  a.colSpan="100";
  var table = document.createElement("table");
  table.id = "group"+groupn;
  table.className = "group";

  table.style.whiteSpace = 'nowrap';

  a.appendChild(table);

  tableheader = document.createElement("thead");
  table.appendChild(tableheader);
  var header_row=tableheader.insertRow(-1);

  if(enable_groups){
    var header_cell = header_row.insertCell(0);
    header_cell.scope="row";
    header_cell.colSpan="8";
    header_cell.innerHTML=SUGAR.language.get(module_sugar_grp1, 'LBL_GROUP_NAME')+":&nbsp;&nbsp;<input name='group_name[]' id='"+ table.id +"name' maxlength='255'  title='' tabindex='120' type='text' readonly class='group_name' value=''><input type='hidden' name='group_id[]' id='"+ table.id +"id' value=''><input type='hidden' name='group_group_number[]' id='"+ table.id +"group_number' value='"+groupn+"'><button title='" + SUGAR.language.get('app_strings', 'LBL_SELECT_BUTTON_TITLE') + "' accessKey='" + SUGAR.language.get('app_strings', 'LBL_SELECT_BUTTON_KEY') + "' type='button' tabindex='121' class='button product_category_button' id='"+ table.id +"catbtn' value='" + SUGAR.language.get('app_strings', 'LBL_SELECT_BUTTON_LABEL') + "' name='btnCategory' onclick='openProductCategoryPopup(" + groupn + ");'><span class=\"suitepicon suitepicon-action-select\"></span></button>";
    header_cell.innerHTML +="<input type='hidden' name='group_category_id[]' id='"+ table.id +"category_id' value=''>";
    //header_cell.innerHTML = SUGAR.language.get(module_sugar_grp1, 'LBL_CATEGORY_NAME')+":&nbsp;&nbsp;<input class='sqsEnabled product_category' autocomplete='off' type='text' name='product_category[" + groupn + "]' id='product_category" + groupn + "' maxlength='50' value='' title='' tabindex='120' value=''><input type='hidden' name='product_category_id[" + groupn + "]' id='product_category_id" + groupn + "'  maxlength='50' value=''><button title='" + SUGAR.language.get('app_strings', 'LBL_SELECT_BUTTON_TITLE') + "' accessKey='" + SUGAR.language.get('app_strings', 'LBL_SELECT_BUTTON_KEY') + "' type='button' tabindex='121' class='button product_category_button' value='" + SUGAR.language.get('app_strings', 'LBL_SELECT_BUTTON_LABEL') + "' name='btnCategory' onclick='openProductCategoryPopup(" + groupn + ");'><span class=\"suitepicon suitepicon-action-select\"></span></button>";

    var header_cell_del = header_row.insertCell(1);
    header_cell_del.scope="row";
    header_cell_del.colSpan="2";
    header_cell_del.innerHTML="<span title='" + SUGAR.language.get(module_sugar_grp1, 'LBL_DELETE_GROUP') + "' style='float: right;'><a style='cursor: pointer;' id='deleteGroup' tabindex='116' onclick='markGroupDeleted("+groupn+")' class='delete_group'><span class=\"suitepicon suitepicon-action-clear\"></span></a></span><input type='hidden' name='group_deleted[]' id='"+ table.id +"deleted' value='0'>";
  }

  var productTableHeader = document.createElement("thead");
  table.appendChild(productTableHeader);
  var productHeader_row=productTableHeader.insertRow(-1);
  var productHeader_cell = productHeader_row.insertCell(0);
  productHeader_cell.colSpan="100";
  var productTable = document.createElement("table");
  productTable.id = "product_group"+groupn;
  productTable.className = "product_group";
  productHeader_cell.appendChild(productTable);

  /*insertProductHeader(productTable.id);

  var serviceTableHeader = document.createElement("thead");
  table.appendChild(serviceTableHeader);
  var serviceHeader_row=serviceTableHeader.insertRow(-1);
  var serviceHeader_cell = serviceHeader_row.insertCell(0);
  serviceHeader_cell.colSpan="100";
  var serviceTable = document.createElement("table");
  serviceTable.id = "service_group"+groupn;
  serviceTable.className = "service_group";
  serviceHeader_cell.appendChild(serviceTable);

  insertServiceHeader(serviceTable.id);
*/

  tablefooter = document.createElement("tfoot");
  table.appendChild(tablefooter);
  var footer_row=tablefooter.insertRow(-1);
  var footer_cell = footer_row.insertCell(0);
  footer_cell.scope="row";
  footer_cell.colSpan="20";
  //footer_cell.innerHTML="<input type='button' tabindex='116' class='button add_product_line' value='"+SUGAR.language.get(module_sugar_grp1, 'LBL_ADD_PRODUCT_LINE')+"' id='"+productTable.id+"addProductLine' onclick='insertProductLine(\""+productTable.id+"\",\""+groupn+"\")' />";
  //footer_cell.innerHTML+=" <input type='button' tabindex='116' class='button add_service_line' value='"+SUGAR.language.get(module_sugar_grp1, 'LBL_ADD_SERVICE_LINE')+"' id='"+serviceTable.id+"addServiceLine' onclick='insertServiceLine(\""+serviceTable.id+"\",\""+groupn+"\")' />";
  if(enable_groups){
   /* footer_cell.innerHTML+="<span class='totals'><label>"+SUGAR.language.get(module_sugar_grp1, 'LBL_TOTAL_AMT')+":</label><input name='group_total_amt[]' id='"+ table.id +"total_amt' class='group_total_amt' maxlength='26' value='' title='' tabindex='120' type='text' readonly></span>";

    
    var footer_row2=tablefooter.insertRow(-1);
    var footer_cell2 = footer_row2.insertCell(0);
    footer_cell2.scope="row";
    footer_cell2.colSpan="20";
    footer_cell2.innerHTML="<span class='totals'><label>"+SUGAR.language.get(module_sugar_grp1, 'LBL_DISCOUNT_AMOUNT')+":</label><input name='group_discount_amount[]' id='"+ table.id +"discount_amount' class='group_discount_amount' maxlength='26' value='' title='' tabindex='120' type='text' readonly></label>";

    var footer_row3=tablefooter.insertRow(-1);
    var footer_cell3 = footer_row3.insertCell(0);
    footer_cell3.scope="row";
    footer_cell3.colSpan="20";
    footer_cell3.innerHTML="<span class='totals'><label>"+SUGAR.language.get(module_sugar_grp1, 'LBL_SUBTOTAL_AMOUNT')+":</label><input name='group_subtotal_amount[]' id='"+ table.id +"subtotal_amount' class='group_subtotal_amount'  maxlength='26' value='' title='' tabindex='120' type='text' readonly></span>";

    var footer_row4=tablefooter.insertRow(-1);
    var footer_cell4 = footer_row4.insertCell(0);
    footer_cell4.scope="row";
    footer_cell4.colSpan="20";
    footer_cell4.innerHTML="<span class='totals'><label>"+SUGAR.language.get(module_sugar_grp1, 'LBL_TAX_AMOUNT')+":</label><input name='group_tax_amount[]' id='"+ table.id +"tax_amount' class='group_tax_amount' maxlength='26' value='' title='' tabindex='120' type='text' readonly></span>";

    if(document.getElementById('subtotal_tax_amount') !== null){
      var footer_row5=tablefooter.insertRow(-1);
      var footer_cell5 = footer_row5.insertCell(0);
      footer_cell5.scope="row";
      footer_cell5.colSpan="20";
      footer_cell5.innerHTML="<span class='totals'><label>"+SUGAR.language.get(module_sugar_grp1, 'LBL_SUBTOTAL_TAX_AMOUNT')+":</label><input name='group_subtotal_tax_amount[]' id='"+ table.id +"subtotal_tax_amount' class='group_subtotal_tax_amount' maxlength='26' value='' title='' tabindex='120' type='text' readonly></span>";

      if (typeof currencyFields !== 'undefined'){
        currencyFields.push("" + table.id+ 'subtotal_tax_amount');
      }
    }

    var footer_row2=tablefooter.insertRow(-1);
    var footer_cell2 = footer_row2.insertCell(0);
    footer_cell2.scope="row";
    footer_cell2.colSpan="20";
    footer_cell2.innerHTML="<span class='totals'><label>"+SUGAR.language.get(module_sugar_grp1, 'LBL_GROUP_TOTAL')+":</label><input name='group_total_amount[]' id='"+ table.id +"total_amount' class='group_total_amount'  maxlength='26' value='' title='' tabindex='120' type='text' readonly></span>";

    if (typeof currencyFields !== 'undefined'){
      currencyFields.push("" + table.id+ 'total_amt');
      currencyFields.push("" + table.id+ 'discount_amount');
      currencyFields.push("" + table.id+ 'subtotal_amount');
      currencyFields.push("" + table.id+ 'tax_amount');
      currencyFields.push("" + table.id+ 'total_amount');
    }
    */
  }
  groupn++;
  return groupn -1;
}

/**
 * Mark Group Deleted
 */

function markGroupDeleted(gn)
{
  document.getElementById('group_body' + gn).style.display = 'none';

  var rows = document.getElementById('group_body' + gn).getElementsByTagName('tbody');

  for (x=0; x < rows.length; x++) {
    var input = rows[x].getElementsByTagName('button');
    for (y=0; y < input.length; y++) {
      if (input[y].id.indexOf('delete_line') != -1) {
        input[y].click();
      }
    }
  }

}

/**
 * Mark line deleted
 */

function markLineDeleted(ln, key)
{
  // collapse line; update deleted value
  document.getElementById(key + 'body' + ln).style.display = 'none';
  document.getElementById(key + 'deleted' + ln).value = '1';
  document.getElementById(key + 'delete_line' + ln).onclick = '';
  var groupid = 'group' + document.getElementById(key + 'group_number' + ln).value;

  if(checkValidate('EditView',key+'product_id' +ln)){
    removeFromValidate('EditView',key+'product_id' +ln);
  }

  //calculateTotal(groupid);
 // calculateTotal();
}


/**
 * Calculate Line Values
 */

function calculateLine(ln, key){

  var required = 'product_list_price';
  if(document.getElementById(key + required + ln) === null){
    required = 'product_unit_price';
  }

  if (document.getElementById(key + 'name' + ln).value === '' || document.getElementById(key + required + ln).value === ''){
    return;
  }

  if(key === "product_" && document.getElementById(key + 'product_qty' + ln) !== null && document.getElementById(key + 'product_qty' + ln).value === ''){
    document.getElementById(key + 'product_qty' + ln).value =1;
  }

  var productUnitPrice = unformat2Number(document.getElementById(key + 'product_unit_price' + ln).value);
  var discount = 0;
  var dis = 'Percentage';
  var listPrice = get_value(key + 'product_list_price' + ln);
  if(document.getElementById(key + 'product_list_price' + ln) !== null && document.getElementById(key + 'product_discount' + ln) !== null && document.getElementById(key + 'discount' + ln) !== null){
    
    discount = get_value(key + 'product_discount' + ln);
    dis = document.getElementById(key + 'discount' + ln).value;
    
    if(dis == 'Amount')
    {
      if(discount > listPrice)
      {
        document.getElementById(key + 'product_discount' + ln).value = listPrice;
        discount = listPrice;
      }
      productUnitPrice = listPrice - discount;
      document.getElementById(key + 'product_unit_price' + ln).value = format2Number(listPrice - discount);
    } else if(dis == 'Percentage') {
      if(discount > 100) {
        document.getElementById(key + 'product_discount' + ln).value = 100;
        discount = 100;
      }
      discount = (discount/100) * listPrice;
      productUnitPrice = listPrice - discount;
      document.getElementById(key + 'product_unit_price' + ln).value = format2Number(listPrice - discount);
    }
    else
    {
      document.getElementById(key + 'product_unit_price' + ln).value = document.getElementById(key + 'product_list_price' + ln).value;
      document.getElementById(key + 'product_discount' + ln).value = '';
      discount = 0;
    }
   
    document.getElementById(key + 'product_list_price' + ln).value = format2Number(listPrice);
    //document.getElementById(key + 'product_discount' + ln).value = format2Number(unformat2Number(document.getElementById(key + 'product_discount' + ln).value));
    document.getElementById(key + 'product_discount_amount' + ln).value = format2Number(-discount, 6);
  }
 
  var productQty = 1;
  if(document.getElementById(key + 'product_qty' + ln) !== null){
    productQty = unformat2Number(document.getElementById(key + 'product_qty' + ln).value);
    Quantity_format2Number(ln);
  }

  var vat = 0;
  if(document.getElementById(key + 'vat' + ln) !== null){
      vat = unformatNumber(document.getElementById(key + 'vat' + ln).value,',','.');
  }
    
  var productTotalPrice = productQty * productUnitPrice;
  var totalvat=(productTotalPrice * vat) /100;

  if(total_tax){
    productTotalPrice=productTotalPrice + totalvat;
  }

  if(document.getElementById(key + 'vat_amt' + ln) !== null){
      document.getElementById(key + 'vat_amt' + ln).value = format2Number(totalvat);
  }
  
  document.getElementById(key + 'product_unit_price' + ln).value = format2Number(productUnitPrice);
  document.getElementById(key + 'product_total_price' + ln).value = format2Number(productTotalPrice);
  var groupid = 0;
  if(enable_groups){
    groupid = document.getElementById(key + 'group_number' + ln).value;
  }
  groupid = 'group' + groupid;

  calculateTotal(groupid);
  calculateTotal();

}

function calculateAllLines() {
  $('.product_group').each(function(productGroupkey, productGroupValue) {
      $(productGroupValue).find('tbody').each(function(productKey, productValue) {
        calculateLine(productKey, "product_");
      });
  });

  $('.service_group').each(function(serviceGroupkey, serviceGroupValue) {
    $(serviceGroupValue).find('tbody').each(function(serviceKey, serviceValue) {
      calculateLine(serviceKey, "service_");
    });
  });
}

/**
 * Calculate totals
 */
function calculateTotal(key)
{
  if (typeof key === 'undefined') {  key = 'lineItems'; }
  var row = document.getElementById(key).getElementsByTagName('tbody');
  if(key == 'lineItems') key = '';
  var length = row.length;
  var head = {};
  var tot_amt = 0;
  var subtotal = 0;
  var dis_tot = 0;
  var tax = 0;

  for (i=0; i < length; i++) {
    var qty = 1;
    var list = null;
    var unit = 0;
    var deleted = 0;
    var dis_amt = 0;
    var product_vat_amt = 0;

    var input = row[i].getElementsByTagName('input');
    for (j=0; j < input.length; j++) {
      if (input[j].id.indexOf('product_qty') != -1) {
        qty = unformat2Number(input[j].value);
      }
      if (input[j].id.indexOf('product_list_price') != -1)
      {
        list = unformat2Number(input[j].value);
      }
      if (input[j].id.indexOf('product_unit_price') != -1)
      {
        unit = unformat2Number(input[j].value);
      }
      if (input[j].id.indexOf('product_discount_amount') != -1)
      {
        dis_amt = unformat2Number(input[j].value);
      }
      if (input[j].id.indexOf('vat_amt') != -1)
      {
        product_vat_amt = unformat2Number(input[j].value);
      }
      if (input[j].id.indexOf('deleted') != -1) {
        deleted = input[j].value;
      }

    }

    if(deleted != 1 && key !== ''){
      head[row[i].parentNode.id] = 1;
    } else if(key !== '' && head[row[i].parentNode.id] != 1){
      head[row[i].parentNode.id] = 0;
    }

    if (qty !== 0 && list !== null && deleted != 1) {
      tot_amt += list * qty;
    } else if (qty !== 0 && unit !== 0 && deleted != 1) {
      tot_amt += unit * qty;
    }

    if (dis_amt !== 0 && deleted != 1) {
      dis_tot += dis_amt * qty;
    }
    if (product_vat_amt !== 0 && deleted != 1) {
      tax += product_vat_amt;
    }
  }

  for(var h in head){
    if (head[h] != 1 && document.getElementById(h + '_head') !== null) {
      document.getElementById(h + '_head').style.display = "none";
    }
  }

  subtotal = tot_amt + dis_tot;

  set_value(key+'total_amt',tot_amt);
  set_value(key+'subtotal_amount',subtotal);
  set_value(key+'discount_amount',dis_tot);

  var shipping = get_value(key+'shipping_amount');

  var shippingtax = get_value(key+'shipping_tax');

  var shippingtax_amt = shipping * (shippingtax/100);

  set_value(key+'shipping_tax_amt',shippingtax_amt);

  tax += shippingtax_amt;

  set_value(key+'tax_amount',tax);

  set_value(key+'subtotal_tax_amount',subtotal + tax);
  set_value(key+'total_amount',subtotal + tax + shipping);
}

function set_value(id, value){
  if(document.getElementById(id) !== null)
  {
    document.getElementById(id).value = format2Number(value);
  }
}

function get_value(id){
  if(document.getElementById(id) !== null)
  {
    return unformat2Number(document.getElementById(id).value);
  }
  return 0;
}


function unformat2Number(num)
{
  return unformatNumber(num, num_grp_sep, dec_sep);
}

function format2Number(str, sig)
{
  if (typeof sig === 'undefined') { sig = sig_digits; }
  num = Number(str);
  if(sig == 2){
    str = formatCurrency(num);
  }
  else{
    str = num.toFixed(sig);
  }

  str = str.split(/,/).join('{,}').split(/\./).join('{.}');
  str = str.split('{,}').join(num_grp_sep).split('{.}').join(dec_sep);

  return str;
}

function formatCurrency(strValue)
{
  strValue = strValue.toString().replace(/\$|\,/g,'');
  dblValue = parseFloat(strValue);

  blnSign = (dblValue == (dblValue = Math.abs(dblValue)));
  dblValue = Math.floor(dblValue*100+0.50000000001);
  intCents = dblValue%100;
  strCents = intCents.toString();
  dblValue = Math.floor(dblValue/100).toString();
  if(intCents<10)
    strCents = "0" + strCents;
  for (var i = 0; i < Math.floor((dblValue.length-(1+i))/3); i++)
    dblValue = dblValue.substring(0,dblValue.length-(4*i+3))+','+
      dblValue.substring(dblValue.length-(4*i+3));
  return (((blnSign)?'':'-') + dblValue + '.' + strCents);
}

function Quantity_format2Number(ln)
{
  var str = '';
  var qty=unformat2Number(document.getElementById('product_product_qty' + ln).value);
  if(qty === null){qty = 1;}

  if(qty === 0){
    str = '0';
  } else {
    str = format2Number(qty);
    if(sig_digits){
      str = str.replace(/0*$/,'');
      str = str.replace(dec_sep,'~');
      str = str.replace(/~$/,'');
      str = str.replace('~',dec_sep);
    }
  }

  document.getElementById('product_product_qty' + ln).value=str;
}

function formatNumber(n, num_grp_sep, dec_sep, round, precision) {
  if (typeof num_grp_sep == "undefined" || typeof dec_sep == "undefined") {
    return n;
  }
  if(n === 0) n = '0';

  n = n ? n.toString() : "";
  if (n.split) {
    n = n.split(".");
  } else {
    return n;
  }
  if (n.length > 2) {
    return n.join(".");
  }
  if (typeof round != "undefined") {
    if (round > 0 && n.length > 1) {
      n[1] = parseFloat("0." + n[1]);
      n[1] = Math.round(n[1] * Math.pow(10, round)) / Math.pow(10, round);
      n[1] = n[1].toString().split(".")[1];
    }
    if (round <= 0) {
      n[0] = Math.round(parseInt(n[0], 10) * Math.pow(10, round)) / Math.pow(10, round);
      n[1] = "";
    }
  }
  if (typeof precision != "undefined" && precision >= 0) {
    if (n.length > 1 && typeof n[1] != "undefined") {
      n[1] = n[1].substring(0, precision);
    } else {
      n[1] = "";
    }
    if (n[1].length < precision) {
      for (var wp = n[1].length; wp < precision; wp++) {
        n[1] += "0";
      }
    }
  }
  regex = /(\d+)(\d{3})/;
  while (num_grp_sep !== "" && regex.test(n[0])) {
    n[0] = n[0].toString().replace(regex, "$1" + num_grp_sep + "$2");
  }
  return n[0] + (n.length > 1 && n[1] !== "" ? dec_sep + n[1] : "");
}

function check_form(formname) {
  calculateAllLines();
  if (typeof(siw) != 'undefined' && siw && typeof(siw.selectingSomething) != 'undefined' && siw.selectingSomething)
    return false;
  return validate_form(formname, '');
}