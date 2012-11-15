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
 * \brief Class for handling FTP
 *
 * Example usage:
 *
 * <?
 * require('ftp_class.php');
 *
 * $ftp = new ftp('some_ftp_host.dk', 'my_name/my_password');
 * $ftp->put_ascii('local_file_name', 'remote_file_name');
 *
 */


class ftp {

  private $connection; 
  private $user_name = 'anonymous'; 
  private $user_pass = 'anonymous'; 
  private $error_message = FALSE;

  public function __construct($host, $credentials, $timeout = 90, $port = 21) {
    if ($this->connection = ftp_connect($host, $port, $timeout)) {
      if ($credentials && strpos($credentials, '/')) {
        list($this->user_name, $this->user_pass) = explode('/', $credentials);
      }
      if (@ ! ftp_login($this->connection, $this->user_name, $this->user_pass)) {
        $this->error_message = 'Error logging in to ' . $host;
      }
    }
    else {
      $this->error_message = 'Error making a connection for ' . $host;
    }
  }

  public function __destruct() {
    if (is_resource($this->connection)) {
      ftp_close($this->connection);
    }
  }

  public function put_ascii($local, $remote) {
    return $this->put($local, $remote, FTP_ASCII);
  }

  public function put_binary($local, $remote) {
    return $this->put($local, $remote, FTP_BINARY);
  }

  public function get_error_message() {
    return $this->error_message;
  }

  private function put($local, $remote, $mode) {
    if (is_resource($this->connection)) {
      if (ftp_put($this->connection, $remote, $local, $mode)) {
        return TRUE;
      }
      else {
        $this->error_message = 'Error putting file ' . $local;
        return FALSE;
      }
    }
  }
}

