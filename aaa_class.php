<?php
/**
 *
 * This file is part of Open Library System.
 * Copyright © 2009, Dansk Bibliotekscenter a/s,
 * Tempovej 7-11, DK-2750 Ballerup, Denmark. CVR: 15149043
 *
 * Open Library System is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Library System is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Open Library System.  If not, see <http://www.gnu.org/licenses/>.
**/

/**
 * \brief AAA Authentication, Access control and Accounting
 *
 * only the first two A's are supported, since there is currently no need for accounting
 *
 * need oci_class and memcache_class to be defined
 * 
 * if aaa_fors_right is defined, then data is fetched from the webservice defined by the parameter
 *
**/

require_once('OLS_class_lib/oci_class.php');
require_once('OLS_class_lib/memcache_class.php');
require_once('OLS_class_lib/ip_class.php');

class aaa {

  private $aaa_cache;				// cache object
  private $cache_seconds;			// number of seconds to cache
  private $cache_key_prefix;
  private $error_cache_seconds;	// number of seconds to cache answer after an error
  private $ip_rights;			    // array with repeated elements: ip_list, ressource
  private $fors_oci;				// oci connection
  private $fors_credentials;		// oci login credentiales
  private $rights;				// the rights
  private $user;			    	// User if any
  private $group;			    	// Group if any
  private $password;				// Password if any
  private $ip;			    	// IP address
  private $fors_rights_url;     // url to forsRights server
  public $aaa_ip_groups = array();

  public function __construct($aaa_setup) {
    $this->fors_credentials = $aaa_setup['aaa_credentials'];
    if (isset($aaa_setup['aaa_cache_address']) and $aaa_setup['aaa_cache_address']) {
      $this->aaa_cache = new cache($aaa_setup['aaa_cache_address']);
      if (!$this->cache_seconds = $aaa_setup['aaa_cache_seconds'])
        $this->cache_seconds = 3600;
      $this->error_cache_seconds = 60;
    }
    $this->fors_rights_url = $aaa_setup['aaa_fors_rights'];
    $this->ip_rights = $aaa_setup['aaa_ip_rights'];
    if (!$this->cache_key_prefix = $aaa_setup['aaa_cache_key_prefix']) {
      $this->cache_key_prefix = 'AAA';
    }
  }

  /**
  * \brief sets a list of ressources and the right atributes of each
  *
  * @param $user       login name
  * @param $group      login group
  * @param $passw      login password
  * @param $ip         the users ip-address
  *
  * @returns TRUE if users has some rights
  **/
  public function init_rights($user, $group, $passw, $ip=0) {
    $this->user = $user;
    $this->group = $group;
    $this->password = $passw;
    $this->ip = $ip;
    if ($this->aaa_cache) {
      $cache_key = $this->cache_key_prefix . '_'.md5($this->user . '_' . $this->group . '_' . $this->password . '_' . $this->ip);
      if ($rights = $this->aaa_cache->get($cache_key)) {
        $this->rights = json_decode($rights);
        return !empty($this->rights);
      }
    }

    if ($this->rights = $this->fetch_rights_from_ip_rights($this->ip, $this->ip_rights)) {
      return TRUE;         // do no cache when found in ip-rights (ini-file)
    }

    if ($this->fors_rights_url) {
      $this->rights = $this->fetch_rights_from_fors_rights_ws($this->user, $this->group, $this->password, $this->ip, $this->fors_rights_url);
    }
    elseif (strpos($this->fors_credentials, '/') && strpos($this->fors_credentials, '@')) {
      $this->rights = $this->fetch_rights_from_ip_fors($this->ip, $this->fors_credentials);
      if (empty($this->rights)) {
        $this->rights = $this->fetch_rights_from_auth_fors($this->user, $this->group, $this->password, $this->fors_credentials);
      }
    }

    if ($this->aaa_cache)
      $this->aaa_cache->set($cache_key, json_encode($this->rights), (isset($error) ? $this->error_cache_seconds : $this->cache_seconds));
    return !empty($this->rights);
  }

  /**
  * \brief returns a list of ressources and the rights of each
  *
  * @param $ressource
  *
  * @returns array of ressources with rights for each or the rights for a given ressource
  **/
  public function get_rights($ressource='') {
    if ($ressource)
      return $this->rights->$ressource;
    else
      return $this->rights;
  }

  /**
  * \brief returns TRUE if user has $right to $ressource
  *
  * @param $ressource
  * @param $right
  *
  * @returns boolean
  **/
  public function has_right($ressource, $right) {
    return ($this->rights->$ressource->$right == TRUE);
  }


  /**
  * \brief Register $operation on $ressource
  *
  * @param $ressource
  * @param $operation
  *
  * @returns boolean
  **/
  public function accounting($ressource, $operation) {
    return TRUE;
  }


  /**
  * \brief set the rights array from the forsRight webservice
  *
  **/
  private function fetch_rights_from_fors_rights_ws($user, $group, $password, $ip, $fors_rights_url) {
    require_once('OLS_class_lib/curl_class.php');
    $curl = new curl();
    $url = sprintf($fors_rights_url, $user, $group, $password, $ip);
    $reply = unserialize($curl->get($url));
    if (isset($reply->forsRightsResponse->_value->ressource)) {
      foreach ($reply->forsRightsResponse->_value->ressource as $ressource) {
        $name = $ressource->_value->name->_value;
        foreach ($ressource->_value->right as $right) {
          $r = $right->_value;
          $rights->$name->$r = TRUE;
        }
      }
    }
    return $rights;
  }

  /**
  * \brief set the rights array from the ini-file
  *
  **/
  private function fetch_rights_from_ip_rights($ip, $ip_rights) {
    if ($ip && is_array($ip_rights)) {
      foreach ($ip_rights as $aaa_group => $aaa_par) {
        if (ip_func::ip_in_interval($ip, $aaa_par['ip_list'])) {
          $this->aaa_ip_groups[$aaa_group] = TRUE;
          if (isset($aaa_par['ressource'])) {
            foreach ($aaa_par['ressource'] as $ressource => $right_list) {
              $right_val = explode(',', $right_list);
              foreach ($right_val as $r) {
                $r = trim($r);
                $rights->$ressource->$r = TRUE;
              }
            }
          }
        }
      }
    }
    return $rights;
  }


  /**
  * \brief set the rights array from FORS using the ip of the caller
  *
  **/
  private function fetch_rights_from_ip_fors($ip, $fors_credentials) {
    if (!empty($fors_credentials) && $ip) {
      if (empty($this->fors_oci)) $this->fors_oci = new Oci($fors_credentials);
      try {
        $this->fors_oci->connect();
      }
      catch (ociException $e) {
        verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI connect error: ' . $this->fors_oci->get_error_string());
        return FALSE;
      }
      $long_ip = ip2long($ip);
      try {
        $this->fors_oci->bind('bind_ipval', $long_ip,-1,SQLT_LNG);
        $this->fors_oci->set_query('SELECT userid, ipend
                                   FROM domuserid
                                   WHERE ipstart <= :bind_ipval
                                   AND (:bind_ipval <= ipend OR ipend IS NULL)');
        $buf = $this->fors_oci->fetch_all_into_assoc();
        foreach ($buf as $key => $val) {
          if (empty($fors_userid) || $val['IPEND'])
            $fors_userid = $val['USERID'];
        }
      }
      catch (ociException $e) {
        verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI select error: ' . $this->fors_oci->get_error_string());
        $error = $this->fors_oci->get_error();
      }

      if (!empty($fors_userid)) {
        try {
          $this->fors_oci->bind('bind_userid', $fors_userid);
          $this->fors_oci->set_query('SELECT userids.userid, userids.state, logins_logingroup.groupname
                                     FROM logins_logingroup, userids
                                     WHERE userids.userid = logins_logingroup.userid
                                     AND userids.userid = :bind_userid');
          if ($buf = $this->fors_oci->fetch_into_assoc()) {
            $userid = $buf['USERID'];
            $state = $buf['STATE'];
            $this->group = $buf['GROUPNAME'];
          }
        }
        catch (ociException $e) {
          verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI select error: ' . $this->fors_oci->get_error_string());
          $error = $this->fors_oci->get_error();
        }
      }
      if ($state == 'OK')
        $rights = $this->fetch_rights_from_userid($userid, $this->group);
    }
    return $rights;
  }

  /**
  * \brief set the rights array from FORS using the authentication triple of the caller
  *
  **/
  private function fetch_rights_from_auth_fors($user, $group, $password, $fors_credentials) {
    if ($user &&  $fors_credentials) {
      if (empty($this->fors_oci)) $this->fors_oci = new Oci($fors_credentials);
      try {
        $this->fors_oci->connect();
      }
      catch (ociException $e) {
        verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI connect error: ' . $this->fors_oci->get_error_string());
        return FALSE;
      }
      try {
        $this->fors_oci->bind('bind_username', $user);
        $this->fors_oci->bind('bind_usergroup', $group);
        $this->fors_oci->set_query('SELECT userids.userid, userids.state, crypttype, password
                                   FROM logins_logingroup, userids
                                   WHERE userids.userid = logins_logingroup.userid
                                   AND (administratorflag = 0 OR administratorflag IS NULL)
                                   AND userids.login = :bind_username
                                   AND groupname = :bind_usergroup');
        $buf = $this->fors_oci->fetch_into_assoc();
        $userid = &$buf['USERID'];
        $crypttype = &$buf['CRYPTTYPE'];
        $pwd = &$buf['PASSWORD'];
        //$pwd = md5($this->password);			// test
        $state = &$buf['STATE'];
        if ($userid
            && $state == 'OK'
            && (($crypttype == 0 && $pwd == $password)
                || ($crypttype == 2 && $pwd == md5($password))))
          $rights = $this->fetch_rights_from_userid($userid, $group);
      }
      catch (ociException $e) {
        verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI select error: ' . $this->fors_oci->get_error_string());
        $error = $this->fors_oci->get_error();
      }
    }
    return $rights;
  }


  /**
  * \brief fecth rights from FORS given a FORS-userid and group
  *
  **/
  private function fetch_rights_from_userid($userid, $group) {
    $rights = new stdClass;
    if (empty($this->fors_oci)) $this->fors_oci = new Oci($this->fors_credentials);
    try {
      $this->fors_oci->connect();
    }
    catch (ociException $e) {
      verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI connect error: ' . $this->fors_oci->get_error_string());
      return $rights;
    }
    try {
      $this->fors_oci->bind('bind_userid', $userid);
      $this->fors_oci->set_query('SELECT t.functiontypeid, objecttypename2
                                 FROM table(fors_pkg.fors_get_rights (:bind_userid)) t, map1
                                 WHERE t.objectclassid = map1.objecttypeattr1
                                 AND t.attr1id = map1.objecttypeattr2');
      $buf = $this->fors_oci->fetch_all_into_assoc();
      foreach ($buf as $val) {
        $rights->$val['OBJECTTYPENAME2']->$val['FUNCTIONTYPEID'] = TRUE;
      }
      try {
        $this->fors_oci->bind('bind_bibnr', $group);
        $this->fors_oci->set_query('SELECT bib_nr FROM vip WHERE kmd_nr = :bind_bibnr');
        $buf = $this->fors_oci->fetch_all_into_assoc();
        $rights->vipInfo->agencyId->$group = TRUE;
        foreach ($buf as $val) {
          $rights->vipInfo->subAgencyId->$val['BIB_NR'] = TRUE;
        }
      }
      catch (ociException $e) {
        verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI select error: ' . $this->fors_oci->get_error_string());
      }
    }
    catch (ociException $e) {
      verbose::log(FATAL, 'AAA('.__LINE__.'):: OCI select error: ' . $this->fors_oci->get_error_string());
    }

    return $rights;
  }

}
?>
