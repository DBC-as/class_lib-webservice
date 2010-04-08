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
 * need oci_class and memcache_class or memcachedb_class to be defined
 *
**/

require_once("OLS_class_lib/oci_class.php");
//require_once("OLS_class_lib/memcachedb_class.php");
require_once("OLS_class_lib/memcache_class.php");
require_once("OLS_class_lib/ip_class.php");

class aaa {

  private $aaa_cache;						// cache object
  private $cache_seconds;				// number of seconds to cache
  private $ip_rights;				    // array with repeated elements: ip_list, ressource
  private $fors_oci;						// oci connection
  private $fors_credentials;		// oci login credentiales
  private $rights;							// the rights

  public function __construct($fors_credentials, $cache_addr = "", $cache_seconds = 0, $ip_rights="") {
    $this->fors_credentials = $fors_credentials;
    if ($cache_addr) {
      $this->aaa_cache = new cache($cache_addr);
      if (!$this->cache_seconds = $cache_seconds)
        $this->cache_seconds = 3600;
    }
    $this->ip_rights = $ip_rights;
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
    if ($this->aaa_cache) {
      $cache_key = "AAA_".md5($user . "_" . $group . "_" . $passw . "_" . $ip);
      if ($this->rights = $this->aaa_cache->get($cache_key))
        return !empty($this->rights);
    }

    if ($ip && is_array($this->ip_rights)) {
      foreach ($this->ip_rights as $rights)
        if (ip_func::ip_in_interval($ip, $rights["ip_list"]))
          foreach ($rights["ressource"] as $ressource => $right)
            $this->rights->$ressource->$right = TRUE;
    }

    if (!$this->rights && $ip) {
      if (empty($this->fors_oci)) $this->fors_oci = new Oci($this->fors_credentials);
      if (!$this->fors_oci->connect()) return FALSE;
      $int_ip = $this->ip2int($ip);
      $this->fors_oci->bind("bind_ipval", &$int_ip,-1,SQLT_LNG);
      $this->fors_oci->set_query("SELECT userid, ipend
            FROM domuserid
            WHERE ipstart <= :bind_ipval
              AND (:bind_ipval <= ipend OR ipend IS NULL)");
      $buf = $this->fors_oci->fetch_all_into_assoc();
      $error = $this->fors_oci->get_error();
      foreach ($buf as $key => $val)
        if (empty($fors_userid) || $val["IPEND"])
          $fors_userid = $val["USERID"];

      if (!empty($fors_userid)) {
        $this->fors_oci->bind("bind_userid", &$fors_userid);
        $this->fors_oci->set_query("SELECT userids.userid, userids.state
                FROM logins_logingroup, userids
                WHERE userids.userid = logins_logingroup.userid
                  AND userids.userid = :bind_userid");
        if ($buf = $this->fors_oci->fetch_into_assoc()) {
          $userid = $buf["USERID"];
          $state = $buf["STATE"];
        }
      }
      if ($state == "OK") 
        $this->rights = $this->fetch_rights_from_userid($userid);
    } 
  
    if (!$this->rights && $user) {
      if (empty($this->fors_oci)) $this->fors_oci = new Oci($this->fors_credentials);
      if (!$this->fors_oci->connect()) return FALSE;
      $this->fors_oci->bind("bind_username", &$user);
      $this->fors_oci->bind("bind_usergroup", &$group);
      $this->fors_oci->set_query("SELECT userids.userid, userids.state, crypttype, password
            FROM logins_logingroup, userids
            WHERE userids.userid = logins_logingroup.userid
              AND (administratorflag = 0 OR administratorflag IS NULL)
              AND userids.login = :bind_username
              AND groupname = :bind_usergroup");
      $buf = $this->fors_oci->fetch_into_assoc();
      $userid = &$buf["USERID"];
      $crypttype = &$buf["CRYPTTYPE"];
      $pwd = &$buf["PASSWORD"];
      $state = &$buf["STATE"];
      if ($userid 
       && $state == "OK" 
       && (($crypttype == 0 && $pwd == $passw) 
        || ($crypttype == 2 && $pwd == md5($passw))))
        $this->rights = $this->fetch_rights_from_userid($userid);
    }
    if ($this->aaa_cache)
      $this->aaa_cache->set($cache_key, $this->rights, $this->cache_seconds);
    return !empty($this->rights);
  }

  /**
  * \brief returns a list of ressources and the rights of each
  *
  * @param $ressource 
  * 
  * @returns array of ressources with rights for each or the rights for a given ressource
  **/
  public function get_rights($ressource="") {
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
    return $this->rights->$ressource->$right == TRUE;
  }

  private function fetch_rights_from_userid($userid) {
    $rights = new stdClass;
    if (empty($this->fors_oci)) $this->fors_oci = new Oci($this->fors_credentials);
    if ($this->fors_oci->connect()) {
      $this->fors_oci->bind("bind_userid", &$userid);
      $this->fors_oci->set_query("SELECT t.functiontypeid, objecttypename2
                  FROM table(fors_pkg.fors_get_rights (:bind_userid)) t, map1
                  WHERE t.objectclassid = map1.objecttypeattr1
                    AND t.attr1id = map1.objecttypeattr2");
      $buf = $this->fors_oci->fetch_all_into_assoc();
      foreach ($buf as $val)
        $rights->$val["OBJECTTYPENAME2"]->$val["FUNCTIONTYPEID"] = TRUE;
    }
    return $rights;
  }

  private function ip2int($ip) {
    list($a, $b, $c, $d) = explode(".", $ip);
    return (($a * 256 + $b) * 256 + $c) * 256 + $d;
  }

}
?>
