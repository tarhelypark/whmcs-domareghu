<?php
/**
 * domareg.hu API objects
 *
 * @author Péter Képes
 * @version V1.0
 * @copyright domareg.hu, 4 January, 2014
 **/

/**
 * Register object for domain registration
 *
 * @author Péter Képes
 **/
class Register {
  public $api_key;            // domareg api key
  public $name;               // Domain name
  public $payed;              // 0/1 Is domain payed by customer. If not registration will not start.
  public $user_id;            // User's ID (free text)
  public $first_name;         // 'Peter'
  public $last_name;          // 'Kepes'
  public $company_name;       // 'Tarhelypark Ltd.'
  public $email;              // user's mail adress
  public $address;            // user's address with street number
  public $city;               // 'Budapest'
  public $zip;                // 1234
  public $country_code;       // ex.: HU
  public $phone;              // Free text
  public $note;               // Free text
  public $md5_password;       // MD5 coded password
  public $vatnr;              // null or 12345678-1-12 format
  public $idcard_nr;          // free text
  public $idcard_expire;      // 2022.11.28
  public $birth_date;         // 2022.11.28
  public $period;             // Recurring time in years (ex.: 3)
}

/**
 * Query object for domain query
 *
 * @package default
 * @author Péter Képes
 **/
class QueryDomain {
  public $api_key;            // domareg.hu api key
  public $name;               // Domain name
}

/**
 * Syncronize object
 *
 * @author Péter Képes
 **/
class Syncronize extends Register {
  public $status;         // Status (I, M, R, A, C, D, E)
  public $expiry_date;    // Domain expiration date
  public $payed;          // 0/1
  public $reg_date;
  public $type;           // R: Register T: Transfer
}

/**
 * Domain renew object
 *
 * @author Péter Képes
 **/
class Renew {
  public $api_key;            // domareg api key
  public $name;               // Domain name
  public $period;             // Recurring time in years (ex.: 3)
}

