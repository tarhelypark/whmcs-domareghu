<?php
require_once('domareghu.php');

domareghu_sync_all_hu_domain();

function domareghu_sync_all_hu_domain() {
  global $mysql;
  connectToMySql();
  $result = select_query("tbldomains","","registrar = 'domareghu'","id");

  $api = new DomareghuApi();
 	$api->openHttpConnection();

  while ($domain = mysql_fetch_assoc($result)) {
    $params = array();
    $pos = strrpos($domain['domain'], '.');
    $params['sld'] = substr($domain['domain'], 0, $pos);
    $params['tld'] = substr($domain['domain'], $pos + 1);
    $params['domainid'] = $domain['id'];
    $params['api_key'] = DOMAREG_API_KEY;
    $params['use_custom_fields'] = DOMAREG_USE_CUSTOM_FIELDS;
 	  $params['custom_field_vatnr'] = DOMAREG_CUSTOM_FIELD_VATNR;
 	  $params['custom_field_idcard_nr'] = DOMAREG_CUSTOM_FIELD_IDCARD_NR;
 	  $params['custom_field_idcard_expire'] = DOMAREG_CUSTOM_FIELD_IDCARD_EXPIRE;
 	  $params['custom_field_birth_date'] = DOMAREG_CUSTOM_FIELD_BIRTH_DATE;
    if ($params['tld'] != 'hu') {continue;}

    echo "Domain feldolgozás: " . $domain['domain'] . "\n";

    $r = domareghu_getRegisterObj($params,true);
    $s = new Syncronize();
    $objValues = get_object_vars($r);
    foreach($objValues AS $key=>$value) {
      $s->$key = $value;
    }
    $s->deleted = 1;

    $response = $api->sendCommand('get_domain', $s);

    if ($response['error']) {
      if ($response['error_code'] != 500) {
        domareghu_add_domain($s, $domain, $api);
      } else {
        echo "Authentikációs hiba: " . $domain['domain'] . "\n";
      }
    } else {
      echo "A domain már a rendszerben van: " . $domain['domain'] . "\n";
    }
  }

  mysql_free_result($result);
  $api->closeHttpConnection();
  echo "Feldolgozás vége\n";
}

function domareghu_add_domain($s, $domain, $api) {
  $sendToDomareg = true;
  switch ($domain['status']) {
    case 'Expired':
      $s->status = 'E';
      $s->payed = '1';
      break;
    case 'Active':
      $s->status = 'A';
      $s->payed = '1';
      break;
    case 'Cancelled':
      $s->status = 'C';
      $s->payed = '1';
      break;
    case 'Pending':
      $s->status = 'M';
      $s->payed = '0';
      break;
    case 'Pending Transfer':
      $s->status = 'M';
      $s->payed = '0';
      break;
    case 'Fraud':
      $sendToDomareg = false;
      break;
    default:
      die('Ismeretlen státusz');
  }

  $s->expiry_date = $domain['nextduedate'];
  $s->reg_date = $domain['registrationdate'];
  $s->type = $domain['type'] == 'Register' ? 'R' : 'T';

  if ($sendToDomareg) {
    $response = $api->sendCommand('add_domain', $s);
    if (isset($response['error'])) {
      echo "Error code: " . $response['error_code'] . "\n";
      echo "Error message: " . $response['error_message'] . "\n";
    }
  }
}

function select_query($table,$fields,$where,$sort = null,$sortorder = null,$limits = null,$join = null) {
  global $mysql;
  $select = "SELECT ";
  $select .= $fields == "" ? "*" : $fields;
  $select .= " FROM $table";

  if ($join) {$select .= " JOIN $join";}
  if ($where) {$select .= " WHERE $where";}
  if ($sort) {$select .= " ORDER BY $sort";}
  if ($sortorder) {$select .= " $sortorder";}
  if ($limits) {$select .= " LIMIT $limits";}

  //echo $select . "\n";
  $result = mysql_query($select);
  if (!$result) {
      $message  = 'Invalid query: ' . mysql_error() . "\n";
      $message .= 'Whole query: ' . $select;
      die($message . "\n");
  }

  return $result;
}

function connectToMySql() {
  global $mysql;
  require_once(WHMCS_CONFIG);
  $mysql = mysql_connect($db_host, $db_username, $db_password);
  if (!$mysql) {
      die("Failed to connect to MySQL: " . mysql_error() . "\n");
  }

  if (!mysql_select_db($db_name)) {
      die("Unable to select mydbname: " . mysql_error() . "\n");
  }
  return $mysql;
}
