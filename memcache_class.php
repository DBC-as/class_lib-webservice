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
   
   thin wrapper for memcache class.

*/

class cache
{
  private static $_memcache=null;
  private static $expire=600;

 /**
  * \brief constructor
  * @param host (string)
  * @param port (integer)
  * @param expire (integer)
  **/

  function __construct($host, $port, $expire=0) {
		$_memcache=new Memcache();
		if(!@$_memcache->connect($host,$port) )
	  	$_memcache=null;
    if ($expire) $this->expire = $expire;
  }

  function __destruct() { }


 /**
  * \brief Gets data store with key in the memcached server
  * @param key (string)
  **/

  public static function get($key) {
    if(is_object($_memcache))
      return $_memcache->get($key);
    return FALSE;
  }

 /**
  * \brief store data with key in the memcache-server
  * @param key (string)
  * @param data (string)
  **/

  public static function set($key,$data) {   
    if(is_object($_memcache))
       return $_memcache->set($key, $data, FALSE, $this->expire);
     return FALSE;
  }

 /**
  * \brief mark all items in cache as expired
  **/

  public static function flush() {
    if(is_object($_memcache))
      return $_memcache->flush();
    return FALSE;
  } 
}


?>
