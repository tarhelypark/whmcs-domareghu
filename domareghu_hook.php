<?php
/**
 * domareg.hu hooks
 * Copy this file into /includes/hooks
 *
 * @author Peter Kepes
 * @version V1.0
 * @copyright CodePlay Solutions Kft. - Tárhelypark, 20 September, 2013
 **/

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
    $domainsData = array();
    foreach($domains as $domain) {
      $result = select_query("tbldomains", "*", array("id" => $domain));
      $domainData = mysql_fetch_array($result);
      $domainName = $domainData["domain"];
      $userId = $domainData["userid"];

      if(endsWith(strtolower($domainName), ".hu")) {
        if ($domainData["type"] == 'Register') {
          $table = "tbldomains";
          $update = array("registrar"=>"domareghu");
          $where = array("id"=>$domainData["id"]);
          update_query($table,$update,$where);

          $command = "domainrequestepp";
          $adminuser = $apiUsername;
          $values["domainid"] = $domain;
          $results = localAPI($command,$values,$adminuser);
        }
      }
    }
  }
}

add_hook("AfterShoppingCartCheckout",1,"sendNewDomainToDomaregHu");