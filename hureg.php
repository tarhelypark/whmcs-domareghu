<?php

require_once('api.php');

function hureg_getConfigArray() {
	$configarray = array(
	 "Username" => array( "Type" => "text", "Size" => "20", "Description" => "Enter your username here", ),
	 "Password" => array( "Type" => "password", "Size" => "20", "Description" => "Enter your password here", ),
	 "api_key" => array( "Type" => "text", "Size" => "50", "Description" => "Enter your API key here", ),
	 "TestMode" => array( "Type" => "yesno", ),
	);
	return $configarray;
}

function hureg_GetNameservers($params) {
  echo "<pre>";
  var_dump($params);
  echo "</pre>";
  
	$api = new HuregApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('getNameServers', $params);
	$api->closeHttpConnection();

	# Put your code to get the nameservers here and return the values below
	$values["ns1"] = $response["ns1"];
	  
	# If error, return the error message in the value below
	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	}
	return $values;
}

function hureg_SaveNameservers($params) {
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

function hureg_GetRegistrarLock($params) {
	return "unlocked";
}

function hureg_SaveRegistrarLock($params) {
	return null;
}

function hureg_GetEmailForwarding($params) {
	return null;
}

function hureg_SaveEmailForwarding($params) {
  	return null;
}

function hureg_GetDNS($params) {
}

function hureg_SaveDNS($params) {
}

function hureg_RegisterDomain($params) {
  echo "<pre>";
  var_dump($params);
  echo "</pre>";
  
	$api = new HuregApi();
	$api->openHttpConnection();
	$response = $api->sendCommand('register', $params);
	$api->closeHttpConnection();
	
	if ($response['error'] == true) {
	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
	}
	
	return $values;
}

function hureg_TransferDomain($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	$regperiod = $params["regperiod"];
	$transfersecret = $params["transfersecret"];
	$nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];
	# Registrant Details
	$RegistrantFirstName = $params["firstname"];
	$RegistrantLastName = $params["lastname"];
	$RegistrantAddress1 = $params["address1"];
	$RegistrantAddress2 = $params["address2"];
	$RegistrantCity = $params["city"];
	$RegistrantStateProvince = $params["state"];
	$RegistrantPostalCode = $params["postcode"];
	$RegistrantCountry = $params["country"];
	$RegistrantEmailAddress = $params["email"];
	$RegistrantPhone = $params["phonenumber"];
	# Admin Details
	$AdminFirstName = $params["adminfirstname"];
	$AdminLastName = $params["adminlastname"];
	$AdminAddress1 = $params["adminaddress1"];
	$AdminAddress2 = $params["adminaddress2"];
	$AdminCity = $params["admincity"];
	$AdminStateProvince = $params["adminstate"];
	$AdminPostalCode = $params["adminpostcode"];
	$AdminCountry = $params["admincountry"];
	$AdminEmailAddress = $params["adminemail"];
	$AdminPhone = $params["adminphonenumber"];
	
	
}

function hureg_RenewDomain($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	$regperiod = $params["regperiod"];
	# Put your code to renew domain here
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}

function hureg_GetContactDetails($params) {
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

function hureg_SaveContactDetails($params) {
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

function hureg_GetEPPCode($params) {
}

function hureg_RegisterNameserver($params) {
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

function hureg_ModifyNameserver($params) {
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

function hureg_DeleteNameserver($params) {
}

function modulename_TransferSync($params) {

  # Your available parameters are:
  $params['domainid'];
  $params['domain'];
  $params['sld'];
  $params['tld'];
  $params['registrar'];
  $params['regperiod'];
  $params['status'];
  $params['dnsmanagement'];
  $params['emailforwarding'];
  $params['idprotection'];
 
  # Other parameters used in your _getConfigArray() function would also be available for use in this function

  # Put your code to check on the domain transfer status here
  $values = array();
 
  # - if the transfer has completed successfully
  $values['completed'] = true; #  when transfer completes successfully
  $values['expirydate'] = '2013-10-28'; # the expiry date of the domain (if available)

  # - or if failed
  $values['failed'] = true; # when transfer fails permenantly
  $values['reason'] = "Reason here..."; # reason for the transfer failing (if available) - a generic failure reason is given if no reason is returned

  # - or if errored
  $values['error'] = "Message here..."; # error if the check fails - for example domain not found

  return $values; # return the details of the sync
}

function modulename_Sync($params) {
  # Your available parameters are:
  $params['domainid'];
  $params['domain'];
  $params['sld'];
  $params['tld'];
  $params['registrar'];
  $params['regperiod'];
  $params['status'];
  $params['dnsmanagement'];
  $params['emailforwarding'];
  $params['idprotection'];
 
  # Other parameters used in your _getConfigArray() function would also be available for use in this function
  
  # Put your code to check on the domain status here
  $values = array();
  
  echo "<pre>";
  var_dump($params);
  echo "</pre>";

 	$api = new HuregApi();
 	$api->openHttpConnection();
 	$response = $api->sendCommand('register', $params);
 	$api->closeHttpConnection();

 	if ($response['error'] == true) {
 	  $values["error"] = $response['error_code'] . ' - ' . $response['error_message'];
 	}
 	
  $values['active'] = true; # set to true if the domain is active
  $values['expired'] = true; # or set to true if the domain has expired
  $values['expirydate'] = '2013-10-28'; # populate with the domains expiry date if available
  
  return $values; # return the details of the sync
}

?>