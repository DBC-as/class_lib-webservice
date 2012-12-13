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
 * \brief Class for handling data transfer to the ES database
 *
 * Needs configuration data, for example (but not necessarily) from an ini file:
 * 
 * [es_database]
 * ; ES database
 * credentials = foo/bar@foobar.dbc.dk
 * 
 * ; list of valid es_format, used as format in xml_control
 * format[put-agency-here][theme] = theme
 * format[put-agency-here][dkabm] = pg
 * 
 * ; list of valid agencies and their corresponding databaseName
 * databaseName[put-agency-here] = and-corresponding-databaseName-here
 * 
 * userid = 3
 * creator = openSearchAdmin
 * action = 1
 * schema = addi
 * elementSetName = esn
 * 
 * xml_control = <?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?><es:referencedata><es:info submitter=&quot;%s&quot; language=&quot;%s&quot; format=&quot;%s&quot;></es:info></es:referencedata>
 * 
 * Example usage:
 *
 *
 */

class esException extends Exception {}

class es_database {
  private $config;
  private $oci;
  private $local_oci = true;

  
/**
 * Constructor for the es_database class
 * @param array $config The config array (see above for an example)
 * @param Oci $oci If an Oci object already exists, it can be used in this class
 */
  public function __construct($config, $oci=null) {
    $this->config = $config;
    if (isset($oci)) {
      $this->oci = $oci;
      $this->local_oci = false;
    }
  }

  
/**
 * Desctructor for the es_database class
 */
  public function __destruct() {
    if ($this->local_oci) {
      if (is_object($this->oci)) {
        $this->oci->disconnect();
      }
      unset($this->oci);
    }
  }

  
/**
 * Fetches the OCI object, if it is present. If not, it is created
 * @return Oci The OCI object
 * @throws esException
 */
  private function oci() {
    if (!is_object($this->oci)) {
      $this->oci = new Oci($this->config['credentials']);
      $this->oci->set_charset('UTF8');
      try { 
        $this->oci->connect(); 
      } catch (ociException $e) {
        verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI connect error: ' . $this->oci->get_error_string());
        throw(new esException('error_reaching_es_database'));
      }
    }
    return $this->oci;
  }
  
  
/**
 * Ships an iso 2709 marc record to the ES database
 * @param string $rec XML record
 * @param type $agency
 * @param type $rec_format
 * @throws esException
 */
  public function ship_to_ES(&$rec, $agency, $rec_format) {
    $databaseName = $this->config['databaseName'];
    $formats = $this->config['format'];
    if (($database_name = $databaseName[$agency]) && ($format = $formats[$agency][$rec_format])) {
      $rec_control = html_entity_decode(sprintf($this->config['xml_control'], $agency, 'dan', $format));
      $oci = $this->oci();
      try { 
        $oci->set_query('SELECT taskpackageRefSeq.nextval FROM dual');
        $val = $oci->fetch_into_assoc();
      } catch (ociException $e) {
        verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI fetch nextval error: ' . $oci->get_error_string());
        throw(new esException('error_reaching_es_database'));
      }
      if ($tgt_ref = $val['NEXTVAL']) {
        $pck_type = 5;
        $pck_name = $tgt_ref;
        $userid = $this->config['userid'];
        $creator = $this->config['creator'];
        try {
          $oci->bind('bind_pck_type', $pck_type);
          $oci->bind('bind_pck_name', $pck_name);
          $oci->bind('bind_tgt_ref', $tgt_ref);
          $oci->bind('bind_userid', $userid);
          $oci->bind('bind_creator', $creator);
          $oci->set_query('INSERT INTO taskpackage 
                             (packagetype, packageName, userid, targetReference, creator)
                           VALUES (:bind_pck_type, :bind_pck_name, :bind_userid, :bind_tgt_ref, :bind_creator)');
        } catch (ociException $e) {
          verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI insert into taskpackage error: ' . $oci->get_error_string());
          $oci->rollback();
          throw(new esException('error_writing_es_database'));
        }
        $schema = $this->config['schema'];
        $elementSetName = $this->config['elementSetName'];
        $action = $this->config['action'];
        try {
          $oci->bind('bind_tgt_ref', $tgt_ref);
          $oci->bind('bind_action', $action);
          $oci->bind('bind_databaseName', $database_name);
          $oci->bind('bind_schema', $schema);
          $oci->bind('bind_elementSetName', $elementSetName);
          $oci->set_query('INSERT INTO taskspecificUpdate
                             (targetReference, action, databaseName, schema, elementSetName)
                           VALUES (:bind_tgt_ref, :bind_action, :bind_databaseName, :bind_schema, :bind_elementSetName)');
        } catch (ociException $e) {
          verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI insert into taskspecificUpdate error: ' . $oci->get_error_string());
          $oci->rollback();
          throw(new esException('error_writing_es_database'));
        }
        $lbnr = 0;
        try { 
          $oci->bind('bind_tgt_ref', $tgt_ref);
          $oci->bind('bind_lbnr', $lbnr);
          $oci->bind('bind_supplementalid3', $rec_control);
          $rec_lob = $oci->create_lob(); 
        } catch (ociException $e) {
          verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI cannot create LOB error: ' . $oci->get_error_string());
          $oci->rollback();
          throw(new esException('error_writing_es_database'));
        }
        try {
          $oci->bind('bind_rec_lob', $rec_lob, -1, OCI_B_BLOB);
          $oci->set_query('INSERT INTO suppliedrecords
                           (targetreference, lbnr, supplementalid3, record)
                           VALUES (:bind_tgt_ref, :bind_lbnr, :bind_supplementalid3, EMPTY_BLOB())
                           RETURNING record into :bind_rec_lob');
        } catch (ociException $e) {
          verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI insert into suppliedrecords error: ' . $oci->get_error_string());
          $oci->rollback();
          throw(new esException('error_writing_es_database'));
        }
        if ($rec_lob->save($rec)) {
          try { 
            $oci->commit(); 
          } catch (ociException $e) {
            verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI commit error: ' . $oci->get_error_string());
          }
        } else {
          verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI save blob into suppliedrecords error: ' . $err);
          try { 
            $oci->rollback();
          } catch (ociException $e) {
            verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI rollback error: ' . $oci->get_error_string());
          }
          throw(new esException('error_writing_es_database'));
        }
        if ($err = $oci->get_error_string()) {
          verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI commit error: ' . $err);
          throw(new esException('error_writing_es_database'));
        }
      } else {
        verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI nextval error: ' . $err);
        throw(new esException('error_fetching_taskpackage_number'));
      }
    } else
      throw(new esException('unknown_agency'));
  }

/**
 * Get the task status for ES database
 * @param type $task_id Task package identifier
 * @throws esException
 * @todo Is not yet fully implemented
 */
  public function get_task_status($task_id) {
    $oci = $this->oci();
    try { 
      $oci->bind('bind_task_id', $task_id);
      $oci->set_query('SELECT taskstatus, packagediagnostics 
                         FROM taskpackage 
                        WHERE targetreference = :bind_task_id');
      $val = $oci->fetch_into_assoc();
// taskstatus: 0=pending 1=active 2=complete 3=aborted 
//echo 'et: '; print_r($val);

  // if complete then ... 
      $oci->bind('bind_task_id', $task_id);
      $oci->set_query('SELECT updatestatus
                         FROM taskspecificupdate
                        WHERE targetreference = :bind_task_id');
      $val = $oci->fetch_into_assoc();
// updatestatus: 1=success 2=partial 3=failure
//echo 'to: '; print_r($val);

// 
      $oci->bind('bind_task_id', $task_id);
      $oci->set_query('SELECT targetreference, lbnr, recordorsurdiag2 
                         FROM TaskPackageRecordStructure 
                        WHERE targetreference = :bind_task_id AND recordorsurdiag2 IS NOT NULL');
      $val = $oci->fetch_all_into_assoc();
//echo 'tre: '; print_r($val);
    } catch (ociException $e) {
      verbose::log(FATAL, 'es_database_class('.__LINE__.'):: OCI select taskstatus error: ' . $oci->get_error_string());
      throw(new esException('error_reaching_es_database'));
    }
  return 'ok';
  }
  
}

