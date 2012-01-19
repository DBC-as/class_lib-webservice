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
* \brief ip-related functions
*
* Example usage:
*  if (ip_func::ip_in_interval("1.2.3.4", "1.2.2.2-1.2.2.9;1.2.3.1-1.2.3.8")) ...
*
*
**/

class ip_func {

  private static $_instance;

  private function __construct() {}

  /**
  * \brief returns true if ip is found in intervals
  *
  * @param $ip         the ip-address to check (string)
  * @param $intervals  ip-intervals (string)
  *        one or more intervals separated by ;
  *        each interval as n.n.n.n or n.n.n.n-m.m.m.m
  * @returns boolean
  **/
  public static function ip_in_interval($ip, $intervals) {
    $ip_int = self::ip2int($ip);
    foreach (explode(";", $intervals) as $interval) {
      list($from, $to) = explode("-", $interval);
      $from_int = $to_int = self::ip2int($from);
      if (!empty($to))
        $to_int = self::ip2int($to);
      if ($ip_int >= $from_int && $ip_int <= $to_int)
        return TRUE;
    }

    return FALSE;
  }

  private function ip2int($ip) {
    list($a, $b, $c, $d) = explode(".", $ip);
    return (($a * 256 + $b) * 256 + $c) * 256 + $d;
  }

}

?>
