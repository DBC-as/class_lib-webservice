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
 * \brief Class for handling OCI
 *
 * Example usage:
 *
 * <?
 * require('oci_class.php');
 *
 * define(VIP_US,'user');
 * define(VIP_PW,'passwd');
 * define(VIP_DB,'dbname');
 *
 * $oci = new Oci(VIP_US, VIP_PW, VIP_DB);
 *
 * $oci->connect();
 *
 * $oci->set_query("SELECT * FROM sdi_user where EMAIL like '%dbc.dk'");
 *
 * echo '<PRE>';
 * while($data=$oci->fetch_into_assoc()) {
 *   print_r($data);
 * }

 * $oci->disconnect();
 *
 * ?>
 *
 * Example of how to use insert_BLOB
 *
 * Data is retrieved from xml with "minixml"
 *
 * Please notice that you should replace the name of the BLOB column with "EMPTY_BLBO()"
 * and instead give the name as the second parameter.
 *
 *for($cnt = 0; $cnt < $Pro->numChildren(); $cnt++ ) {
 *  $id = $AllProducts[$cnt]->getElement('Id')->getValue();
 *  echo "Id:$id\n";
 *
 *  // check whether the record is in the database;
 *  $idno = $oci->fetch_into_assoc("select id from xmldata where id = $id");
 *  if ( $oci->get_error() ) {
 *    print_web($oci->get_error(),"get_error: \n");
 *    exit;
 *  }
 *
 *  if ( $idno['ID'] == $id ) continue;
 *  $strng = $AllProducts[$cnt]->toString();
 *
 *  $oci->insert_BLOB("insert into xmldata ( id,createdate,data ) values( $id,sysdate,EMPTY_BLOB() )","data",$strng);
 *  if ( $oci->get_error() ) {
 *    print_web($oci->get_error(),"get_error: \n");
 *    exit;
 *  }
 *  $oci->commit();
 *  if ( $oci->get_error() ) {
 *    print_web($oci->get_error(),"get_error: \n");
 *    exit;
 *  }
 *}
 *
 */


class ociException extends Exception {
  
  public function __construct($ociError) {
    parent::__construct($ociError['message'] . ' --- ' . $ociError['sqltext']);
  }

  public function __toString() {
    return 'ociException -->'.$this->getMessage().' --- '.$this->getFile().':'.$this->getLine()."\nStack trace:\n".$this->getTraceAsString();
  }

}

class oci {

  ///////////////////////////////////////
  // PRIVATE VARIABLES DO NOT CHANGE!!!//
  ///////////////////////////////////////

  /// Value for successful connection <bool>
  var $connect;

  /// Oci statement <string>
  var $statement;

  // Bind list
  var $bind_list;

  /// SQL query <string>
  var $query;

  /// Iterator for number of rows fetched. <int>
  var $num_fetched_rows;

  /// Username for database connection <string>
  var $username;

  /// Password for database connection <string>
  var $password;

  /// Tnsname for database connection <string>
  var $database;

  /// Determines wether connection is persistent. <bool>
  var $persistent_connect = false;

  /// Contains error string. Empty if no error. <string>
  var $error;

  /// Pagination enable flag <bool>
  var $enable_pagination=false;

  /// Pagination begin <int>
  var $begin;

  /// Pagination end <int>
  var $end;

  /// Default value for end <int>
  var $end_default_val=25;

  /// Commit enabled <bool>
  var $commit_enabled=false;

  /// Set max connect retries
  var $num_connect_attempts=1;

  /// Stores updated rows number <int>
  var $num_rows;

  ////////////////////
  // PUBLIC METHODS //
  ////////////////////

 /**
  * \brief  constructor
  * @param username username for db connection OR credentials in format: user/pwd@dbname
  * @param password password for db connection
  * @param database database name (from tnsnames.ora)
  */

  function oci($username,$password='',$database='') {
    
    if($password=='' && $database=='') {
      
      
      $expl=explode('/', $username,2);
      $this->username=$expl[0];
      $expl=explode('@', $expl[1]);
      $this->password=$expl[0];
      $this->database=$expl[1];
      
    } else {
      $this->username=$username;
      $this->password=$password;
      $this->database=$database;
    }
    if (defined('OCI_NUM_CONNECT_ATTEMTS')
        && is_numeric(OCI_NUM_CONNECT_ATTEMTS)
        && OCI_NUM_CONNECT_ATTEMTS < 20)
      $this->num_connect_attempts = OCI_NUM_CONNECT_ATTEMTS;
    
    $this->charset = NULL;
  }
  
  function destructor() {
    $this->disconnect();
  }



  function cursor_open() {
    return oci_new_cursor($this->connect);
  }

 /**
  * \brief Set's pagination start and end values and enables pagination flag
  * @param begin pagination (int)
  * @param end pagination (int)
  */

  function set_pagination($begin,$end) {

    if(empty($begin))
      $begin=0;

    if(empty($end))
      $end=$this->end_default_val;

    if(!is_numeric($begin) || !is_numeric($end)) {
      Die('Validation error: Only integers allowed for pagination');
    }

    $this->begin=$begin;
    $this->end=$end;

    $this->enable_pagination=true;
  }


 /**
  * \brief Sets number of attempts for connect
  * @param num_connect_attempts
  */

  function set_num_connect_attempts($num_connect_attempts) {
    return $this->num_connect_attempts=$num_connect_attempts;
  }

 /**
  * \brief sets charset
  */

  function set_charset($charset) {
    return $this->charset = $charset;
  }

 /**
  * \brief Returns number of updated rows.
  * @return int
  */

  function get_num_rows() {
    return $this->num_rows;
  }


 /**
  * \brief Check if connection is persistent
  * @return bool
  */

  function is_persistent_connect() {
    return $this->persistent_connect;
  }

 /**
  * \brief Get OCI error
  * @return string.
  */

  function get_error() {
    return $this->error;
  }

 /**
  * \brief Return OCI error-string
  */

  function get_error_string() {
    if ($this->error && is_array($this->error))
      return $this->error['code'] . ': ' . $this->error['message'];
    else
      return FALSE;
  }


 /**
  * \brief Get OCI connector
  *
  * Returns OCI connecter in case the user would like to work with it (i.e. for OCI functions not supported by this wrapper class).
  *
  * @return object.
  */

  function get_connector() {
    return $this->connect;
  }


 /**
  * \brief Open new OCI connection
  * @return bool.
  */

  function connect($connect_count=-1) {

    $this->clear_OCI_error();

    if (is_resource($this->connect)) {
      // $this->oci_log(OCI, 'oci_pconnect:: ' . $this->username . '@' . $this->database . ' reuse connection');
      return true;
    }

    if($connect_count==-1) $connect_count=$this->num_connect_attempts;
    
    $this->connect=@oci_pconnect($this->username, $this->password, $this->database, $this->charset );
    
    if (!is_resource($this->connect)) {
      if($connect_count>1) {
        $this->oci_log(WARNING, 'oci_pconnect:: ' . $this->username . '@' . $this->database . ' reconnect (' . $connect_count . ') with error: ' . $this->get_error_string());
        return $this->connect($connect_count-1);
      }

      $this->set_OCI_error(oci_error());
      $this->oci_log(ERROR, 'oci_pconnect:: ' . $this->username . '@' . $this->database . ' failed with error: ' . $this->get_error_string());
      throw new ociException(oci_error());
    } 
    else {
      return true;
    }
  }

 /**
  * \brief Enable or disable commit
  * @param bool
  */

  function commit_enable($state) {
    $this->commit_enabled=$state;
  }


 /**
  * \brief Set and parse query
  * @param query SQL query (string)
  * @return (bool)
  */

  function set_query($query) {
    
    $this->clear_OCI_error();
    // reset num_fetched_rows iterator and result set
    $this->num_fetched_rows=0;
    $this->result=array();
    
    // set query
    
    if($this->enable_pagination) {
      
      $this->query = "select *
      from (select /*+ FIRST_ROWS(10)) */
      a.*, ROWNUM rnum
      from (".$query.") a
      where ROWNUM<=".$this->end." )
      where rnum>=".$this->begin;

    } else {
      $this->query=$query;
    }
    
   $this->statement = @ociparse($this->connect, $this->query);
   if ( ! $this->statement ) {
     $this->set_OCI_error(oci_error($this->connect));
     $this->oci_log(ERROR, 'ociparse:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
     throw new ociException($this->error);
    }
    
    if(!empty($this->bind_list)) {
      foreach($this->bind_list as $k=>$v) {
        if ( ! @oci_bind_by_name($this->statement, $v['name'], $v['value'], $v['maxlength'], $v['type']) ) {
          $this->set_OCI_error(ocierror($this->statement));
          $this->oci_log(ERROR, 'oci_bind_by_name:: failed on ' . $this->query . ' binding ' . $v['name'] . ' to ' . $v['value'] . ' type: '. $v['type'] . ' with error: ' . $this->get_error_string());
          throw new ociException($this->error);
        }
      }
      $this->bind_list = array();
    }
    
    if($this->commit_enabled) 
      $success  = @ociexecute($this->statement, OCI_COMMIT_ON_SUCCESS);
    else
      $success  = @ociexecute($this->statement, OCI_DEFAULT);
    if ( ! $success ) {
      $this->set_OCI_error(oci_error($this->statement));
      $this->oci_log(ERROR, 'ociexecute:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
      throw new ociException($this->error);
    }
    $this->num_rows=oci_num_rows($this->statement);
    $this->oci_log(OCI, 'ociexecute:: ' . $this->query . ' success with no error: ' . $this->get_error_string());
    return TRUE;
  }


 /**
  * \brief Insert data including a BLOB into a row in a database
  * @param insert string in sql 
  * @param is the name of the column name of the BLOB 
  * @param a ref. to the data
  * @return int
  *
  * Ex. "insert into xmldata (id, createdate, xmldata) values ($id, sysdate, EMPTY_BLOB() )" 
  */

    function insert_BLOB($sql,$name,&$data) {
   
      $this->query = $sql . " returning $name into :data_loc \n";
      $this->statement = @ociparse($this->connect, $this->query);
      $this->set_OCI_error(ocierror($this->connect));
      if (!is_resource($this->statement)) {
        $this->oci_log(ERROR, 'ociparse:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
        throw new ociException($this->error);
      }

      if(!empty($this->bind_list)) {
        foreach($this->bind_list as $k=>$v) {
          $success = @oci_bind_by_name($this->statement, $v['name'], $v['value'], $v['maxlength'], $v['type']);
          $this->set_OCI_error(ocierror($this->statement));
          if (!$success) {
            $this->oci_log(ERROR, 'oci_bind_by_name:: failed on ' . $this->query . ' binding ' . $v['name'] . ' to ' . $v['value'] . 'type: '. $v['type'] . ' with error: ' . $this->get_error_string());
            throw new ociException($this->error);
          }
        }
        $this->bind_list = array();
      }
      
      // Creates an "empty" OCI-Lob object to bind to the locator
      if ( ! $dataLOB = @oci_new_descriptor($this->connect, OCI_D_LOB) ) {
        $this->set_OCI_error(ocierror($this->statement));
        $this->oci_log(ERROR, 'oci_new_descriptor:: failed on  ' . $this->query . ' with error: ' . $this->get_error_string());
        throw new ociException($this->error);
      }

      // Bind the returned Oracle LOB locator to the PHP LOB object
      if ( ! @oci_bind_by_name($this->statement, ":data_loc", $dataLOB, strlen($data), OCI_B_BLOB) ) {
        $this->set_OCI_error(ocierror($this->statement));
        $this->oci_log(ERROR, 'oci_bind_by_name:: failed on  ' . $this->query . ' with error: ' . $this->get_error_string());
        throw new ociException($this->error);
      }

      if ( ! @ociexecute($this->statement, OCI_DEFAULT) ) {
        $this->set_OCI_error(ocierror($this->statement));
        $this->oci_log(ERROR, 'ociexecute:: failed on  ' . $this->query . ' with error: ' . $this->get_error_string());
        throw new ociException($this->error);
      }
      
      // Now import a file to the LOB
      if ( !$dataLOB->save($data) ) {
        $this->set_OCI_error(ocierror($this->statement));
        $this->oci_log(ERROR, 'save:: failed on  ' . $this->query . ' with error: ' . $this->get_error_string());
        throw new ociException($this->error);
      }


      // Free resources
      ociFreestatement($this->statement);
      $dataLOB->free();

      return true;
    }
      

 /**
  * \brief Commits outstanding statements
  * @return bool
  */

  function commit() {
    if ( ! oci_commit($this->connect) ) {
      $this->set_OCI_error(ocierror($this->statement));
      $this->oci_log(ERROR, 'commit:: failed on  ' . $this->query . ' with error: ' . $this->get_error_string());
      throw new ociException($this->error);
    }
      

  }


 /**
  * \brief Rollback outstanding statements
  * @return bool
  */

  function rollback() {
    if ( ! oci_rollback($this->connect)) {
      $this->set_OCI_error(ocierror($this->statement));
      $this->oci_log(ERROR, 'rollback:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
      throw new ociException($this->error);
    }
    return true;
  }


 /**
  * \brief Creates an empty OCI lob
  * @return OCI lob
  */
  function create_lob() {
    if ($lob = oci_new_descriptor($this->connect, OCI_D_LOB))
      return $lob;
    else {
      $this->set_OCI_error(ocierror($this->statement));
      $this->oci_log(ERROR, 'create_lob:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
      throw new ociException($this->error);
    }

    return false; 
  }


  function bind($name, $value, $maxlength=-1, $type=SQLT_CHR) {
    $bind_array['name']=($name[0] == ':'? $name : ':'.$name);
    $bind_array['value']=&$value;
    $bind_array['maxlength']=$maxlength;
    $bind_array['type']=$type;
    $this->bind_list[]=$bind_array;
  }

 /**
  * \brief Get query
  * @return string
  */

  function get_query()
  {
    return $this->query;
  }

 /**
  * \brief Fetches current blob and return it. There must be only one element (the BLOB) to be fetched.
  * @param query SQL string
  * @return string | bool
  */

  function fetch_BLOB($sql = '') {


    if ( $sql ) {
      $this->set_query($sql);
      if ( $this->error ) return (false);
    }

    if ( ! $res =oci_fetch_row($this->statement)) {
      $this->set_OCI_error(ocierror($this->statement));
      $this->oci_log(ERROR, 'fetch_BLOB:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
      throw new ociException($this->error);
    }
    
    $this->num_fetched_rows++;
    $this->result = $res[0]->load();

    return $this->result;
  }

 /**
  * \brief Fetches current data into an associative array (use while loop around this function  to get all)
  * @return array | bool
  */

  function fetch_into_assoc($sql = '') {

    if ( $sql ) {
      $this->set_query($sql);
      if ( $this->error ) return (false);
    }
    if ( ! $this->result=oci_fetch_array($this->statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
      $this->set_OCI_error(ocierror($this->statement));
      if ( $this->error ) {
        $this->oci_log(ERROR, 'oci_fetch_array:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
        throw new ociException($this->error);
      }
    }
    $this->num_fetched_rows++;
    return $this->result;
  }


 /**
  * \brief Fetches all data into an associative array
  * @return array
  */

  function fetch_all_into_assoc($sql = '') {
    
    if ( $sql ) {
      $this->set_query($sql);
      if ( $this->error ) return (false);
    }

    while($tmp_result=oci_fetch_array($this->statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
      $this->num_fetched_rows++;
      $this->result[]=$tmp_result;
    }
    $this->set_OCI_error(ocierror($this->statement));
    if ( $this->error ) {
      $this->oci_log(ERROR, 'oci_fetch_all_into_assoc:: failed on ' . $this->query . ' with error: ' . $this->get_error_string());
      throw new ociException(oci_error());
    }
    return $this->result;
  }


 /**
  * \brief Returns last number of rows fetched
  * @return int
  */

  function get_num_fetched_rows() {
    return $this->num_fetched_rows;
  }


 /**
  * \brief Closes OCI connection
  */

  function disconnect() {
    ocilogoff($this->connect);
  }

  /////////////////////
  // PRIVATE METHODS //
  /////////////////////

 /**
  * \brief Set's OCI error
  * @param oci_error Expects output from ocierror() function (array)
  */

  function set_OCI_error($OCIerror) {
    if ($OCIerror && empty($this->error))
      $this->error = $OCIerror;
  }

 /**
  * \brief Clear OCI error
  */

  function clear_OCI_error() {
    $this->error = array();
  }

 /**
  * \brief Set's connection to persistent
  */

  function set_persistent() {
    $this->persistent_connect=true;
  }

  private function oci_log($log_level, $msg) {
    if (method_exists('verbose','log'))
      verbose::log($log_level, $msg);
  }
}
?>
