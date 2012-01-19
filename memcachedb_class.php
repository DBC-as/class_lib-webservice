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
*/

/**
 *
 * Wrapper for memcachedb
 *
 * Information about memcachedb and the corresponding server, can be
 * found at http://memcachedb.org
 *
 * ex.
 *   $cache_db = new cache_db("localhost", "11211", strtotime("+1 week"));
 *    or
 *   $cache_db = new cache_db("localhost:11211");
 *   $my_settings = $cache_db->get("my_settings");
 *   .
 *   .
 *   $cache_db->set("my_settings", $my_settings, strtotime("+1 day"));
 *
*/

class cache_db {
  private $memcache=null;
  private $expire;

  /**
   * \brief constructor
   * @param host (string)
   * @param port (integer)
   * @param expire (timestamp) default expire datestamp
   **/

  function __construct($host, $port, $expire="") {
    $this->memcache=new Memcache();
    if (empty($port) && strpos($host, ":"))
      list($host, $port) = explode(":", $host, 2);
    if (!@$this->memcache->connect($host,$port) )
      $this->memcache=null;
    $this->expire = ($expire?$expire:strtotime("+1 day"));
  }

  function __destruct() { }


  /**
   * \brief Gets data stored with key $key
   * @param key (string)
   **/

  public function get($key) {
    if (is_object($this->memcache))
      return $this->memcache->get($key);
    return FALSE;
  }

  /**
   * \brief stores data with key $key in the memcache-server
   * @param key (string)
   * @param data (string)
   * @param expire (timestamp)
   **/

  public function set($key,$data, $expire=0) {
    if (is_object($this->memcache))
      return $this->memcache->set($key, $data, FALSE, ($expire?$expire:$this->expire));
    return FALSE;
  }

  /**
   * \brief Delete data store with key in the memcached server
   * @param key (string)
   **/

  public function delete($key) {
    if (is_object($this->memcache))
      return $this->memcache->delete($key);
    return FALSE;
  }

  /**
   * \brief mark all items in cache as expired
   **/

  public function flush() {
    if (is_object($this->memcache))
      return $this->memcache->flush();
    return FALSE;
  }
}


?>
