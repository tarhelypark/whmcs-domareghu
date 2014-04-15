<?php
/**
 * Domareg API calls object
 *
 * @author Péter Képes
 * @version V1.0
 * @copyright Tárhelypark.hu, 06 February, 2013
 **/
require_once('api.php');
require_once('classes.php');

function domareghu_getConfigArray() {
	$configarray = array(
	 "api_key" => array( "Type" => "text", "Size" => "50", "Description" => "Enter your API key here", ),
	 "use_custom_fields" => array( "Type" => "yesno", "Description" => "Use custom customer's fileds"),
	 "custom_field_vatnr" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of vat nr" ),
	 "custom_field_idcard_nr" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of customer's idcard nr"),
	 "custom_field_idcard_expire" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of customer's idcard expiration date"),
	 "custom_field_birth_date" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of customer's birth date")
	);
	return $configarray;
}

function domareghu_GetNameservers($params) {
  echo "<pre>";
  var_dump($params);

  $q = new QueryDomain();
  $q->api_key = $params['api_key'];
  $q->name = $params['sld'] . '.' . $params['tld'];

  var_dump($q);

	$api = new DomareghuApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('get_nameservers', $q);
	$api->closeHttpConnection();

	# Put your code to get the nameservers here and return the values below
	$values["ns1"] = $response["ns1"];

	# If error, return the error message in the value below
	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	}

	echo "</pre>";
	return $values;
}

function domareghu_SaveNameservers($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
  $nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];
  $nameserver3 = $params["ns3"];
	$nameserver4 = $params["ns4"];
	# Put your code to save the nameservers here
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}

function domareghu_GetRegistrarLock($params) {
	return "unlocked";
}

function domareghu_SaveRegistrarLock($params) {
	return null;
}

function domareghu_GetEmailForwarding($params) {
	return null;
}

function domareghu_SaveEmailForwarding($params) {
  	return null;
}

function domareghu_GetDNS($params) {
}

function domareghu_SaveDNS($params) {
}

function domareghu_RegisterDomain($params) {
  echo "<pre>";
  echo "domareghu_RegisterDomain 1\n";
  var_dump($params);

  $r = domareghu_getRegisterObj($params);

  echo "domareghu_RegisterDomain 2\n";
  var_dump($r);

	$api = new DomareghuApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('register', $r);
	$api->closeHttpConnection();

	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	}

	echo "</pre>";
	return $values;
}

function domareghu_TransferDomain($params) {
  echo "<pre>";
  echo "domareghu_TransferDomain 1\n";
  var_dump($params);

  $r = domareghu_getRegisterObj($params);

  echo "domareghu_TransferDomain 2\n";
  var_dump($r);

	$api = new DomareghuApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('transfer', $r);
	$api->closeHttpConnection();

	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	} else {
    $table = "tbldomains";
    $update = array("status"=>"Active");
    $where = array("id"=>$params['domainid']);
    #update_query($table,$update,$where);
	}

	echo "</pre>";
	return $values;
}

function domareghu_RenewDomain($params) {
  $r = new Renew();
  $r->api_key = $params['api_key'];
  $r->name = $params['sld'] . '.' . $params['tld'];
  $r->period = $params["regperiod"];
  echo "<pre>";
	$api = new DomareghuApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('renew', $r);
	$api->closeHttpConnection();

	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	}

  echo "</pre>";
	return $values;
}

function domareghu_GetContactDetails($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Put your code to get WHOIS data here
	# Data should be returned in an array as follows
	$values["Registrant"]["First Name"] = $firstname;
	$values["Registrant"]["Last Name"] = $lastname;
	$values["Admin"]["First Name"] = $adminfirstname;
	$values["Admin"]["Last Name"] = $adminlastname;
	$values["Tech"]["First Name"] = $techfirstname;
	$values["Tech"]["Last Name"] = $techlastname;
	return $values;
}

function domareghu_SaveContactDetails($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Data is returned as specified in the GetContactDetails() function
	$firstname = $params["contactdetails"]["Registrant"]["First Name"];
	$lastname = $params["contactdetails"]["Registrant"]["Last Name"];
	$adminfirstname = $params["contactdetails"]["Admin"]["First Name"];
	$adminlastname = $params["contactdetails"]["Admin"]["Last Name"];
	$techfirstname = $params["contactdetails"]["Tech"]["First Name"];
	$techlastname = $params["contactdetails"]["Tech"]["Last Name"];
	# Put your code to save new WHOIS data here
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}

function domareghu_GetEPPCode($params) {
  echo "<pre>";
  var_dump($params);
  $r = domareghu_getRegisterObj($params, true);
  $r->payed = 0;
  $api = new DomareghuApi();
	$api->openHttpConnection();
  if ($params["regtype"] == 'Register') {
	  $response = $api->sendCommand('register', $r);
  } else {
    $response = $api->sendCommand('transfer', $r);
  }
	$api->closeHttpConnection();

  if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	} else {
	  $values["eppcode"] ='Sikeres nyilvántartásba vétel: ' . $r->name;
	}

  echo "</pre>";
  return $values;
}

function domareghu_RegisterNameserver($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    $ipaddress = $params["ipaddress"];
    # Put your code to register the nameserver here
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}

function domareghu_ModifyNameserver($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    $currentipaddress = $params["currentipaddress"];
    $newipaddress = $params["newipaddress"];
    # Put your code to update the nameserver here
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}

function domareghu_DeleteNameserver($params) {
}

function domareghu_TransferSync($params) {
  $params['name'] = $params['sld'] . '.' . $params['tld'];
  $q = new QueryDomain();
  $q->api_key = $params['api_key'];
  $q->name = $params['name'];

  $values = array();

 	$api = new DomareghuApi();
 	$api->openHttpConnection();
 	$response = $api->sendCommand('get_expiry', $q);
  $api->closeHttpConnection();

  $values = array();

  if ($response['error'] == true) {
    # TODO Nem tudunk olyan hibakódot visszaadni, amivel megkülönböztethető
    # a kommunikciós hiba és az, hogy a domain törölve van már
    # Szerver oldalon kell először olyan választ adni amiből négy eset deríthető ki:
    # 1. Minden ok, a domain aktív
    # 2. A domain a rendszerben van, de még nincsen regisztrálva, halad felé
    # 3. A domain törölve van a rendszerből de benne volt
    # 4. A domain nincs a rendszerben

    # Error
    #if ($response['error_code'] == 100) {
      # Error in communication
      $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
    #} else {
      #$values['failed'] = true; # when transfer fails permenantly
      #$values['reason'] = $response['error_message']; # reason for the transfer failing (if available) - a generic failure reason is given if no reason is returned
    #}
  } else {
    # - if the transfer has completed successfully
    $values['completed'] = true; #  when transfer completes successfully
 	  $values['expirydate'] = $response['expiry_date'];
  }

  return $values; # return the details of the sync
}

/**
 * domareghu_Sync
 * Syncronize active .hu domain names to domareg.hu
 *
 *
 * @return void
 * @author Péter Képes
 **/
function domareghu_Sync($params) {
  # Query domain expiry information
  $params['name'] = $params['sld'] . '.' . $params['tld'];
  $q = new QueryDomain();
  $q->api_key = $params['api_key'];
  $q->name = $params['name'];

  $values = array();

 	$api = new DomareghuApi();
 	$api->openHttpConnection();
 	$response = $api->sendCommand('get_expiry', $q);

  # If domain not found we should add it to domareg.hu
 	if ($response['error'] == true) {
 	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
 	  #$response = $api->sendCommand('add_domain', $q);

    /*gdomareghu_getRegisterObj($params, true);
    # Check if addation is successed
    if ($response['error'] == true) {
 	    $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
 	  } else {
 	    # Check expiry information again
 	    $response = $api->sendCommand('getExpiry', $q);
 	    if ($response['error'] == true) {
   	    $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
   	  } else {
   	    $values['active'] = $response['expired'] == 0;
     	  $values['expired'] = $response['expired'] == 1;
     	  $values['expirydate'] = $response['expiry_date'];
   	  }
 	  }*/
 	} else {
 	  $values['active'] = $response['expired'] == 0;
 	  $values['expired'] = $response['expired'] == 1;
 	  $values['expirydate'] = $response['expiry_date'];
 	}
  $api->closeHttpConnection();
  return $values;
}

/**
 * domareghu_getRegisterObj
 * Create a Register API object based on domainid
 *
 * domainid tbldomains.id
 * @return void
 * @author Péter Képes
 **/
function domareghu_getRegisterObj($params, $from_database = false) {

  $params['name'] = $params['sld'] . '.' . $params['tld'];
  if ($from_database) {
    $result = select_query("tbldomains","","id = " . $params["domainid"]);
    $domain = mysql_fetch_assoc($result);

    $result = select_query("tblclients","","id = " . $domain['userid']);
    $client = mysql_fetch_assoc($result);

    $params['name'] = $domain['domain'];
    $params['userid'] = $client['id'];
    $params['firstname'] = $client['firstname'];
    $params['lastname'] = $client['lastname'];
    $params['companyname'] = $client['companyname'];
    $params['email'] = $client['email'];
    $params['address1'] = $client['address1'];
    $params['address2'] = $client['address2'];
    $params['city'] = $client['city'];
    $params['postcode'] = $client['postcode'];
    $params['countrycode'] = $client['country'];
    $params['phonenumber'] = $client['phonenumber'];
    $params['notes'] = $client['notes'];
    $params['password'] = $client['password'];
    $params['regperiod'] = $domain['registrationperiod'];
  }

  $r = new Register();
  $r->api_key = $params['api_key'];
  $r->name = $params['name'];
  $r->payed = 1;
  $r->user_id = $params['userid'];
  $r->first_name = $params['firstname'];
  $r->last_name = $params['lastname'];
  $r->company_name = $params['companyname'];
  $r->email = $params['email'];
  $r->address = $params['address1'] . ' ' . $params['address2'];
  $r->city = $params['city'];
  $r->zip = $params['postcode'];
  $r->country_code = $params['countrycode'];
  $r->phone = $params['phonenumber'];
  $r->note = $params['notes'];
  $r->md5_password = $params['password'];
  $r->period = $params['regperiod'];

  if ($params['use_custom_fields'] == 'yes') {
    $r->vatnr = $params['customfields' . $params['custom_field_vatnr']];
    $r->idcard_nr = $params['customfields' . $params['custom_field_idcard_nr']];
    $r->idcard_expire = $params['customfields' . $params['custom_field_idcard_expire']];
    $r->birth_date = $params['customfields' . $params['custom_field_birth_date']];
  }

  /*
  if ($params['use_custom_fields'] == 'on') {
    $result = select_query("tblcustomfields","sortorder,value",
    "tblcustomfieldsvalues.relid = " . $domain['userid'] . " and type='client' ", '', '', 30,
      "tblcustomfieldsvalues ON tblcustomfieldsvalues.fieldid=tblcustomfields.id");
    $cfields = array();
 	  while ($row = mysql_fetch_assoc($result)) {
 	    $cfields[$row['sortorder']] = $row['value'];
 	  }

    $r->vatnr = $cfields[$params['custom_field_vatnr']];
    $r->idcard_nr = $cfields[$params['custom_field_idcard_nr']];
    $r->idcard_expire = $cfields[$params['custom_field_idcard_expire']];
    $r->birth_date = $cfields[$params['custom_field_birth_date']];
  }*/

  return $r;
}

?>