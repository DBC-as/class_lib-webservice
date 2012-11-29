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
 * try {
 *   $ftp = new ftp('some_ftp_host.dk', 'my_name/my_password');
 *   $ftp->put_ascii('local_file_name', 'remote_file_name');
 * } catch (ftpException $e) {
 *   echo "FTP Error: " . $e->getMessage();
 * }
 * 
 */ 
 
/**
 * ftpException class
 */
class ftpException extends Exception {}

/**
 * ftp class
 */
class ftp {
  private $connection; 
  private $user_name = 'anonymous'; 
  private $user_pass = 'anonymous'; 

  /**
   * Constructor
   * @param string $host Hostname
   * @param string $credentials Credential information in the form username/password
   * @param integer $timeout Timeout value in seconds (default value=90)
   * @param integer $port Port number (default value=21)
   * @throws ftpException
   */  
  public function __construct($host, $credentials, $timeout = 90, $port = 21) {
    $this->connection = ftp_connect($host, $port, $timeout);
    if ($this->connection === FALSE) {
      throw new ftpException("Error making a connection for $host");
    }
    if ($credentials && strpos($credentials, '/')) {
      list($this->user_name, $this->user_pass) = explode('/', $credentials);
    }
    if (! ftp_login($this->connection, $this->user_name, $this->user_pass)) {
      throw new ftpException("Error logging in to $host");
    }
  }

  /**
   * Destructor
   */
  public function __destruct() {
    if (is_resource($this->connection)) {
      ftp_close($this->connection);
    }
  }

  /**
   * Deletes a file on the remote FTP server
   * @param string $remote Remote file name
   */
  public function delete($remote) {
    if (! ftp_delete($this->connection, $remote)) {
      throw new ftpException("Error deleting remote file $remote");
    }
  }

  /**
   * Puts an ASCII file to the remote FTP server
   * @param string $local Local file name
   * @param string $remote Remote file name
   */
  public function put_ascii($local, $remote) {
    $this->_put($local, $remote, FTP_ASCII);
  }

  /**
   * Puts a binary file to the remote FTP server
   * @param string $local Local file name
   * @param string $remote Remote file name
   */
  public function put_binary($local, $remote) {
    $this->_put($local, $remote, FTP_BINARY);
  }

  /**
   * Puts a file to the remote FTP server
   * @param string $local Local file name
   * @param string $remote Remote file name
   * @param int $mode FTP_ASCII or FTP_BINARY
   */
  private function _put($local, $remote, $mode) {
    if (!is_resource($this->connection)) {
      throw new ftpException('Attempt to use an illegal ftp resource in put');
    }
    if (! ftp_put($this->connection, $remote, $local, $mode)) {
      throw new ftpException("Error putting file $local");
    }
  }

  /**
   * Gets an ASCII file from the remote FTP server
   * @param string $local Local file name
   * @param string $remote Remote file name
   */
  public function get_ascii($local, $remote) {
    $this->_get($local, $remote, FTP_ASCII);
  }

  /**
   * Gets a binary file from the remote FTP server
   * @param string $local Local file name
   * @param string $remote Remote file name
   */
  public function get_binary($local, $remote) {
    $this->_get($local, $remote, FTP_BINARY);
  }

  /**
   * Gets a file from the remote FTP server
   * @param string $local Local file name
   * @param string $remote Remote file name
   * @param int $mode FTP_ASCII or FTP_BINARY
   */
  private function _get($local, $remote, $mode) {
    if (!is_resource($this->connection)) {
      throw new ftpException('Attempt to use an illegal ftp resource in get');
    }
    if (! ftp_get($this->connection, $local, $remote, $mode)) {
      throw new ftpException("Error getting file $local");
    }
  }

}

