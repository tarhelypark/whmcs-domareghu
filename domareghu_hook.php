<?php
/**
 * domareg.hu hooks
 * Copy this file into /includes/hooks
 *
 * @author Peter Kepes
 * @version V1.0
 * @copyright CodePlay Solutions Kft. - Tárhelypark, 20 September, 2013
 **/
require_once($_SERVER['DOCUMENT_ROOT'] . '/modules/registrars/domareghu/domareghu.php');
 /**
  * sendNewDomainToDomaregHu function
  *
  * @return void
  * @author Péter Képes
  **/
function sendNewDomainToDomaregHu($vars) {
  // Change this username to your admin username!
  $apiUsername = 'api-client';
  $domains = $vars["Domains"];

  if(count($domains) !== 0) {
    foreach($domains as $domain) {
      $result = select_query("tbldomains", "*", array("id" => $domain));
      $domainData = mysql_fetch_array($result);
      $domainName = $domainData["domain"];
      $userId = $domainData["userid"];

      if(endsWith(strtolower($domainName), ".hu")) {

        /*$table = "tbldomains";
        $update = array("registrar"=>"domareghu");
        $where = array("id"=>$domainData["id"]);
        update_query($table,$update,$where);

        $command = "domainrequestepp";
        $adminuser = $apiUsername;
        $values["domainid"] = $domain;
        $results = localAPI($command,$values,$adminuser);*/

        // echo "<pre>";
        // var_dump($params);
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
    if ($item['type'] == 'DomainTransfer' && strpos($item['description'],'.hu ') !== false) {
      $desc = str_replace('Év','db',$item['description']);

      $table = "tblinvoiceitems";
      $update = array("description"=> $desc);
      $where = array("id"=>$item["id"]);
      update_query($table,$update,$where);
    }
    if ($item['type'] == 'DomainRegister' && strpos($item['description'],'.hu ') !== false && strpos($item['description'],'1 Év') !== false) {
      $desc = substr($item['description'], 0, strpos($item['description'],'1 Év')) . '2 Év';

      $table = "tblinvoiceitems";
      $update = array("description"=> $desc);
      $where = array("id"=>$item["id"]);
      update_query($table,$update,$where);
    }
  }
}

function disableDomainRegistrationConfirmation($vars) {
  if ($vars["messagename"] == "Domain Registration Confirmation") {
    $result = select_query("tbldomains", "*", array("id" => $vars["relid"]));
    $domainData = mysql_fetch_array($result);
    if ($domainData['registrar'] == 'domareghu') {
      $ret = array();
      $ret['abortsend'] = true;
      return $merge_fields;
    }
  }
}

add_hook("AfterShoppingCartCheckout",10,"checkHuDomainDates");
add_hook("AfterShoppingCartCheckout",20,"sendNewDomainToDomaregHu");
add_hook("InvoiceCreated",10,"changeHuInvoiceItems");
add_hook("EmailPreSend",10,"disableDomainRegistrationConfirmation");