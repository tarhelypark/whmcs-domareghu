<?php
/**
 * Domareg API calls object
 *
 * @author Péter Képes
 * @version V1.0
 * @copyright Tárhelypark.hu, 06 February, 2013
 **/
require_once('domareghu_config.php');
require_once('domareghu_api.php');
require_once('domareghu_classes.php');

// TODO Ez nem tudom miért lett kiszedve, de vissza kéne rakni!
/*
function domareghu_getConfigArray() {
	$configarray = array(
	 "api_key" => array( "Type" => "text", "Size" => "50", "Description" => "Enter your API key here", ),
   "api_url" => array( "Type" => "text", "Size" => "100", "Description" => "API url for domareg.hu", "Default" => "http://ugyfel.domareg.hu/api/"),
	 "use_custom_fields" => array( "Type" => "yesno", "Description" => "Use custom customer's fileds"),
	 "custom_field_vatnr" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of vat nr" ),
	 "custom_field_idcard_nr" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of customer's idcard nr"),
	 "custom_field_idcard_expire" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of customer's idcard expiration date"),
	 "custom_field_birth_date" => array( "Type" => "text", "Size" => "2", "Description" => "Custom field number of customer's birth date")
	);
	return $configarray;
}*/

function domareghu_getConfigArray() {
  return array();
}
function domareghu_GetNameservers($params) {
  $q = new QueryDomain();
  $q->api_key = DOMAREG_API_KEY;
  $q->name = $params['original']['domainname'];

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
  $r = domareghu_getRegisterObj($params);
	$api = new DomareghuApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('register', $r);
	$api->closeHttpConnection();

	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	}

	return $values;
}

function domareghu_TransferDomain($params) {
  $r = domareghu_getRegisterObj($params);
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

	// echo "</pre>";
	return $values;
}

function domareghu_RenewDomain($params) {
  $r = new Renew();
  $r->api_key = DOMAREG_API_KEY;
  $r->name = $params['original']['domainname'];
  $r->period = $params["regperiod"];
	$api = new DomareghuApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('renew', $r);
	$api->closeHttpConnection();

	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	}

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
  $r = domareghu_getRegisterObj($params, true);
  $r->payed = 0;
  $api = new DomareghuApi();
	$api->openHttpConnection();
  if ($r->regtype == 'R') {
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
  $q->api_key = DOMAREG_API_KEY;
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
  $q->api_key = DOMAREG_API_KEY;
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
  $result = select_query("tbldomains","","id = " . $params["domainid"]);
  $domain = mysql_fetch_assoc($result);

  $result = select_query("tblclients","","id = " . $domain['userid']);
  $client = mysql_fetch_assoc($result);

  $params['original']['name'] = $params['domainname'];
  $params['original']['email'] = $client['email'];
  $params['original']['userid'] = $client['id'];
  $params['original']['notes'] = $client['notes'];
  $params['original']['password'] = $client['password'];

  if ($from_database) {
    $params['original']['name'] = $domain['domain'];
    $params['original']['regperiod'] = $domain['registrationperiod'];
    $params['original']['regtype'] = $domain['type'];
    $params['original']["domainid"] = $domain['id'];

    $result = select_query("tblorders","","id = " . $domain['orderid']);
    $order = mysql_fetch_assoc($result);

    if ($order['contactid'] == 0) {
      $params['original']['firstname'] = $client['firstname'];
      $params['original']['lastname'] = $client['lastname'];
      $params['original']['companyname'] = $client['companyname'];
      $params['original']['address1'] = $client['address1'];
      $params['original']['address2'] = $client['address2'];
      $params['original']['city'] = $client['city'];
      $params['original']['postcode'] = $client['postcode'];
      $params['original']['countrycode'] = $client['country'];
      $params['original']['phonenumber'] = $client['phonenumber'];

    } else {
      $result = select_query("tblcontacts","","id = " . $order['contactid']);
      $contact = mysql_fetch_assoc($result);

      $params['original']['firstname'] = $contact['firstname'];
      $params['original']['lastname'] = $contact['lastname'];
      $params['original']['companyname'] = $contact['companyname'];
      $params['original']['address1'] = $contact['address1'];
      $params['original']['address2'] = $contact['address2'];
      $params['original']['city'] = $contact['city'];
      $params['original']['postcode'] = $contact['postcode'];
      $params['original']['countrycode'] = $contact['country'];
      $params['original']['phonenumber'] = $contact['phonenumber'];
    }
  }

  // ISZT special country code differ from ISO
  if ($params['original']['countrycode'] == 'GB') {
    $params['original']['countrycode'] = 'UK';
  }

  $r = new Register();
  $r->api_key = DOMAREG_API_KEY;
  $r->name = $params['original']['name'];
  $r->payed = 1;
  $r->user_id = $params['original']['userid'];
  $r->first_name = $params['original']['firstname'];
  $r->last_name = $params['original']['lastname'];
  $r->company_name = $params['original']['companyname'];
  $r->email = $params['original']['email'];
  $r->address = $params['original']['address1'] . ' ' . $params['original']['address2'];
  $r->city = $params['original']['city'];
  $r->zip = $params['original']['postcode'];
  $r->country_code = $params['original']['countrycode'];
  $r->phone = $params['original']['phonenumber'];
  $r->note = $params['original']['notes'];
  $r->md5_password = $params['original']['password'];
  $r->period = $params['original']['regperiod'];
  $r->regtype = $params['original']['regtype'] == 'Register' ? 'R' : 'T';
  $r->domain_id = $params['original']["domainid"];

  if (DOMAREG_USE_CUSTOM_FIELDS == 'on') {
    $result = select_query("tblcustomfields","sortorder,value",
    "tblcustomfieldsvalues.relid = " . $params['original']['userid'] . " and type='client' ", '', '', 30,
      "tblcustomfieldsvalues ON tblcustomfieldsvalues.fieldid=tblcustomfields.id");
    $cfields = array();
 	  while ($row = mysql_fetch_assoc($result)) {
 	    $cfields[$row['sortorder']] = $row['value'];
 	  }

    $r->vatnr = $cfields[DOMAREG_CUSTOM_FIELD_VATNR];
    $r->idcard_nr = $cfields[DOMAREG_CUSTOM_FIELD_IDCARD_NR];
    $r->idcard_expire = $cfields[DOMAREG_CUSTOM_FIELD_IDCARD_EXPIRE];
    $r->birth_date = $cfields[DOMAREG_CUSTOM_FIELD_BIRTH_DATE];
  }

  return $r;
}

?>
