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
 * \brief
 *
 *
 *
 * need curl_class and memcache_class to be defined
 *
**/

require_once('OLS_class_lib/memcache_class.php');
require_once('OLS_class_lib/curl_class.php');

class agency_type {

  private $agency_type_tab;
  private $agency_cache;		    // cache object
  private $agency_uri;	        // uri of openagency service

  public function __construct($open_agency, $cache_host, $cache_port='', $cache_seconds = 0) {
    if ($cache_host) {
      $this->agency_cache = new cache($cache_host, $cache_port, $cache_seconds);
    }
    $this->agency_uri = $open_agency;
  }

  /**
  * \brief Get a given bracnh_type for the agency
  *
  * @param $agency       name of agency
  *
  * @returns bracnh_type if found, NULL otherwise
  **/
  public function get_branch_type($agency) {
    if (empty($agency_type_tab)) {
      $this->fetch_agency_type_tab();
    }
    return $this->agency_type_tab[$agency]['branchType'];
  }

  /**
  * \brief Get a given agency_type for the agency
  *
  * @param $agency       name of agency
  *
  * @returns agency_type if found, NULL otherwise
  **/
  public function get_agency_type($agency) {
    if (empty($agency_type_tab)) {
      $this->fetch_agency_type_tab();
    }
    return $this->agency_type_tab[$agency]['agencyType'];
  }

  /**
  * \brief Fetch agencyType and branchType using openAgency::findLibrary
  *
  **/
  private function fetch_agency_type_tab() {
    if ($this->agency_cache) {
      $cache_key = 'branch_types';
      $this->agency_type_tab = $this->agency_cache->get($cache_key);
    }

    if (!$this->agency_type_tab) {
      $curl = new curl();
      $curl->set_option(CURLOPT_TIMEOUT, 10);
      $res_xml = $curl->get(sprintf($this->agency_uri));
      $curl_err = $curl->get_status();
      if ($curl_err['http_code'] < 200 || $curl_err['http_code'] > 299) {
        if (method_exists('verbose','log')) {
          verbose::log(FATAL, __FUNCTION__ . '():: Cannot fetch agencies from ' . sprintf($this->agency_uri));
        }
      }
      else {
        $dom = new DomDocument();
        $dom->preserveWhiteSpace = false;
        if (@ $dom->loadXML($res_xml)) {
          foreach ($dom->getElementsByTagName('pickupAgency') as $agency) {
            $this->agency_type_tab[$this->node_value($agency, 'branchId')] =
              array('agencyType' => $this->node_value($agency, 'agencyType'),
                    'branchType' => $this->node_value($agency, 'branchType'));
          }
        }
        $curl->close();
      }
      if ($this->agency_cache && $this->agency_type_tab) {
        $this->agency_cache->set($cache_key, $this->agency_type_tab);
      }
    }
  }

  private function node_value($node, $tag) {
    return $node->getElementsByTagName($tag)->item(0)->nodeValue;
  }

}
?>
