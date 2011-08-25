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
 * need curl_class and memcache_class or memcachedb_class to be defined
 *
**/

require_once('OLS_class_lib/memcache_class.php');
require_once('OLS_class_lib/curl_class.php');

class search_profiles {

    private $profile_cache;		    // cache object
    private $profiles;		        // profiles for $agency
    private $agency_uri;	        // uri of openagency service
    private $cache_seconds;		    // number of seconds to cache
    private $error_cache_seconds;	// number of seconds to cache answer after an error

    public function __construct($open_agency, $cache_host, $cache_port='', $cache_seconds = 0) {
        if ($cache_host) {
            $this->profile_cache = new cache($cache_host, $cache_port);
            if (!$this->cache_seconds = $cache_seconds)
                $this->cache_seconds = 600;
        }
        $this->agency_uri = $open_agency;
    }

    /**
    * \brief Get a given profile for the agency
    *
    * @param $profile_name       name of profile
    *
    * @returns profile if found, FALSE otherwise
    **/
    public function get_profile($agency, $profile_name) {
        if ($this->profile_cache) {
            $cache_key = 'PROFILE_' . $this->agency;
            $this->profiles = $this->profile_cache->get($cache_key);
        }

        if (!$this->profiles) {
            $curl = new curl();
            $curl->set_option(CURLOPT_TIMEOUT, 10);
            $res_xml = $curl->get(sprintf($this->agency_uri, $agency));
            $curl_err = $curl->get_status();
            if ($curl_err['http_code'] < 200 || $curl_err['http_code'] > 299) {
                $this->profiles[strtolower($p_name)] = FALSE;
                if (method_exists('verbose','log'))
                  verbose::log(FATAL, __FUNCTION__ . '():: Cannot fetch profile ' . $profile_name . 
                                        ' from ' . sprintf($this->agency_uri, $agency));
            } else {
                $dom = new DomDocument();
                $dom->preserveWhiteSpace = false;
                if (@ $dom->loadXML($res_xml)) {
                    foreach ($dom->getElementsByTagName('profile') as $profile) {
                        $p_name = '';
                        $p_val = array();
                        foreach ($profile->childNodes as $p)
                        if ($p->nodeName == 'profileName')
                            $p_name = $p->nodeValue;
                        elseif ($p->nodeName == 'source') {
                            foreach ($p->childNodes as $s)
                            $r[$s->nodeName] = $s->nodeValue;
                            $p_val[] = $r;
                        }
                        $this->profiles[strtolower($p_name)] = $p_val;
                    }
                } else {
                    $this->profiles = array();
                }

            }
            if ($this->profile_cache)
                $this->profile_cache->set($cache_key, $this->profiles, $this->cache_seconds);
        }

        if ($p = &$this->profiles[strtolower($profile_name)])
            return $p;
        else
            return FALSE;
    }

}
?>
