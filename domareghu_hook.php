<?php
/**
 * domareg.hu hooks
 * Copy this file into /includes/hooks
 *
 * @author Peter Kepes
 * @version V1.0
 * @copyright CodePlay Solutions Kft. - Tárhelypark, 20 September, 2013
 **/

if (strpos(dirname(__FILE__), 'modules/registrars/domareghu') === false) {
  require_once(dirname(__FILE__) . '/../../modules/registrars/domareghu/domareghu.php');
} else {
  require_once('domareghu.php');
}

 /**
  * sendNewDomainToDomaregHu function
  *
  * @return void
  * @author Péter Képes
  **/
function sendNewDomainToDomaregHu($vars) {
  $apiUsername = 'api-client';
  $domains = $vars["Domains"];

  if(count($domains) !== 0) {
    foreach($domains as $domain) {
      $result = select_query("tbldomains", "*", array("id" => $domain));
      $domainData = mysql_fetch_array($result);
      $domainName = $domainData["domain"];
      $userId = $domainData["userid"];

      if(endsWith(strtolower($domainName), ".hu")) {
        $d = array('domainid' => $domain);
        $r = domareghu_getRegisterObj($d, true);
        $r->payed = 0;
        $api = new DomareghuApi();
      	$api->openHttpConnection();
        if ($r->regtype == 'R') {
      	  $response = $api->sendCommand('register', $r);
        } else {
          $response = $api->sendCommand('transfer', $r);
        }
      	$api->closeHttpConnection();
      }
    }
  }
}

/**
 * checkHuDomainDates function
 *
 * Ellenőrzi, hogy a .hu végződésű regisztrációnál jó-e a dátum. Csak 2 év lehet, de ezt a felületen
 * nem lehet megadni '-1'-el, mert akkor az 1 évnél megadott transzfer és egy éves hosszabbítás nem jelenik meg
 *
 * @return void
 * @author Péter Képes
 **/
function checkHuDomainDates($vars) {
  global $CONFIG;
  $domains = $vars["Domains"];
  $systemDomain = trim($CONFIG['Domain']);

  if(count($domains) !== 0) {
    $domainsData = array();
    foreach($domains as $domain) {
      $result = select_query("tbldomains", "*", array("id" => $domain));
      $domainData = mysql_fetch_array($result);
      $domainName = $domainData["domain"];
      $userId = $domainData["userid"];

      if(endsWith(strtolower($domainName), ".hu")) {

        if ($domainData["type"] == 'Register' && $domainData["registrationperiod"] == 1) {
          $table = "tbldomains";

          $nextinvoicedate = date("Y-m-d",strtotime(date("Y-m-d", strtotime($domainData["nextduedate"])) . " + 2 year"));

          #$update = array("registrationperiod"=>"2");
          $update = array("expirydate"=>$nextinvoicedate);
          $where = array("id"=>$domainData["id"]);
          update_query($table,$update,$where);
        }
      }
    }
  }
}

/**
 * changeHuTransfePreriod function
 *
 * A .hu domain transzfer egyszeri költség, és nem hosszabbítja meg a domain lejáratát, ezért a számlán
 * a mennyiségnek nem évben, hanem darabban kell szerepelnie. Módosítani kell a számla tételeket
 *
 * @return void
 * @author Péter Képes
 **/
function changeHuInvoiceItems($vars) {
  $invoiceId = $vars['invoiceid'];
  $items = select_query("tblinvoiceitems", "*", array("invoiceid" => $invoiceId));

  while ($item = mysql_fetch_assoc($items)) {
    $desc = $item['description'];
    $changed = false;

    if ($item['type'] == 'DomainTransfer' && strpos($desc, '.hu ') !== false) {
      $desc = str_replace('év', 'db', $desc);
      $desc = str_replace('Év', 'db', $desc);
      $changed = true;
    }

    if ($item['type'] == 'DomainRegister' && strpos($desc, '.hu ') !== false &&
      (strpos($desc, '1 Év') !== false || strpos($desc, '1 év') !== false) ) {
      $desc = str_replace('1 év', '2 év', $desc);
      $desc = str_replace('1 Év', '2 Év', $desc);
      $pattern = '/\((\d{4})\/\d{2}\/\d{2} - (\d{4})\/\d{2}\/\d{2}\)/';

      preg_match($pattern, $desc, $matches );

      $date_range = $matches[0];
      $year_from = intval($matches[1]);
      $year_to = intval($matches[2]);

      $new_date_range = str_replace(' - ' . $year_to, ' - ' . ($year_from + 2), $date_range);
      $desc = str_replace($date_range, $new_date_range, $desc);

      $changed = true;
    }

    if ($changed) {
      $table = "tblinvoiceitems";
      $update = array("description"=> $desc);
      $where = array("id"=>$item["id"]);
      update_query($table,$update,$where);
    }
  }
}

function disableDomainRegistrationConfirmation($vars) {
  if ($vars["messagename"] == "Domain Registration Confirmation" || $vars["messagename"] == "Domain Transfer Initiated") {
    $result = select_query("tbldomains", "*", array("id" => $vars["relid"]));
    $domainData = mysql_fetch_array($result);
    if ($domainData['registrar'] == 'domareghu') {
      $ret = array();
      $ret['abortsend'] = true;
      return $ret;
    }
  }
}

/**
 * changeDomaregHuPassword function
 *
 * Hook for password change
 *
 * @return void
 * @author Péter Képes
 **/
function changeDomaregHuPassword($vars) {
  $vars['userid'];
  $vars['password'];

  $params['user_id'] = $vars['userid'];
  $params['password'] = $vars['password'];
  $params['api_key'] = DOMAREG_API_KEY;

  $api = new DomareghuApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('change_password', $params);
	$api->closeHttpConnection();
}

add_hook("AfterShoppingCartCheckout",10,"checkHuDomainDates");
add_hook("AfterShoppingCartCheckout",20,"sendNewDomainToDomaregHu");
add_hook("InvoiceCreated",10,"changeHuInvoiceItems");
add_hook("UpdateInvoiceTotal",10,"changeHuInvoiceItems");
add_hook("EmailPreSend",10,"disableDomainRegistrationConfirmation");
add_hook("ClientChangePassword",10,"changeDomaregHuPassword");
