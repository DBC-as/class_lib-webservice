<?php
/**
 *
 * This file is part of OpenLibrary.
 * Copyright © 2009, Dansk Bibliotekscenter a/s,
 * Tempovej 7-11, DK-2750 Ballerup, Denmark. CVR: 15149043
 *
 * OpenLibrary is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenLibrary is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OpenLibrary.  If not, see <http://www.gnu.org/licenses/>.
*/

/** \brief Webservice server
 *
 *
 */

require_once("OLS_class_lib/curl_class.php");
require_once("OLS_class_lib/verbose_class.php");
require_once("OLS_class_lib/inifile_class.php");
require_once("OLS_class_lib/timer_class.php");
require_once("OLS_class_lib/restconvert_class.php");
require_once("OLS_class_lib/xmlconvert_class.php");
require_once("OLS_class_lib/objconvert_class.php");

abstract class webServiceServer {

  protected $config; /// inifile object
  protected $verbose;  /// verbose object for logging
  protected $watch; /// timer object
	protected $xmldir="./"; /// xml directory

	/** \brief Webservice constructer
 	*
	* @param inifile <filename>
 	*
 	*/
	public function  __construct($inifile) {
	  // initialize config and verbose objects
    $this->config = new inifile($inifile); 
                                           
    if( $this->config->error ) {                                    
        die("Error: ".$this->config->error );
      }                                                                

    $this->verbose=new verbose($this->config->get_value("logfile", "setup"),
                               $this->config->get_value("verbose", "setup"));    
    $this->watch = new stopwatch("", " ", "", "%s:%01.3f");
    $this->watch->start($this->config->get_value("servicename","setup"));   

		if($this->config->get_value('xmldir')) 
			$this->xmldir=$this->config->get_value('xmldir');
	}

  /** \brief Handles request from webservice client
  *
  */
	public function handle_request() {
		  if( isset($_GET["HowRU"]) ) {                          
        	$this->HowRU();          
        	return;                  
      }  elseif(!isset($HTTP_RAW_POST_DATA) && isset($_POST['xml'])) {                                            
				$xml=trim(stripslashes($_POST['xml']));
        $this->soap_request($xml);                    
        return;                                    
      }  elseif(!empty($_SERVER['QUERY_STRING']) ) {                                            
        $this->rest_request();    
        return;
      } else {                                                
			 $this->create_sample_forms();
       exit;                                          
      }    
	}

  /** \brief Handles and validates soap request
	*
  * @param xml <string>
  */
	private function soap_request($xml) {

		if($this->config->get_value('xsd')) {
			if(!$this->validate_xml($xml,$this->config->get_value('xsd'))) {
				$error=1;
				// return error.
			}
		}

		if(!$error) {
      // parse til objekt
      $xmlconvert=new xmlconvert();
      $xmlobj=$xmlconvert->soap2obj($xml);
			$response_xmlobj=$this->call_xmlobj_function($xmlobj);

      // Branch to outputType
      $objconvert=new objconvert();
      list($service, $req) = each($xmlobj->Envelope->_value->Body->_value);
      switch ($req->_value->outputType->_value) {
        case "json":
          if ($callback=$req->_value->callback->_value)
			      echo $callback . ' && ' . $callback . '(' . $objconvert->obj2json($response_xmlobj) . ')';
          else
			      echo $objconvert->obj2json($response_xmlobj);
          break;
        case "phps":
			    echo $objconvert->obj2phps($response_xmlobj);
          break;
        case "xml":
			    echo $objconvert->obj2xmlNS($response_xmlobj);
          break;
        default: 
			    echo $response_xml=$objconvert->obj2soap($response_xmlobj);
      }

		} else {
			echo "Error in validation.";
		}
	}

	/** \brief Handles rest request, converts it to xml and calls soap_request()
  *
  * @param xml <string>
	*
  */
	private function rest_request() {
			// convert to soap
			$rest = new restconvert();
			$xml=$rest->rest2soap(&$this->config);
			$this->soap_request($xml);
	}

  /** \brief HowRU tests the webservice and answers back if things went OK. The test cases resides in the inifile.
  * 
  */
	private function HowRU() {
		$curl = new curl();
		$curl->set_option(CURLOPT_POST, 1);
		foreach ($this->config->get_value('test') as $k=>$v) {
			$reply=$curl->get($this->config->get_value('ws_url')."?action=".$v);
			$m=$this->config->get_value('preg_match');
			$preg_match=$m[$k];
			if(preg_match("/$preg_match/",$reply)) {
				$r=$this->config->get_value('ok_response');
				echo $r[$k];
			} else {
				$e=$this->config->get_value('error');
				echo $e[$k];
			}
		}
		$curl->close();
	}

  /** \brief Validates xml
  * 
  * @param xml <string>
  * @param schema_filename <string>
  * @param resolve_externals <bool>
	*
  */

  private function validate_xml($xml, $schema_filename, $resolve_externals='FALSE') {
		$validateXml = new DomDocument;
    $validateXml->resolveExternals = $resolve_externals;
    $validateXml->loadXml($xml);
    if ($validateXml->schemaValidate($schema_filename)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /** \brief Find operation in object created from xml and and calls this function defined by developer in extended class.
  *
  * @param xmlobj <object>
  *
  */
  private function call_xmlobj_function($xmlobj) {
    $function=key($xmlobj->Envelope->_value->Body->_value);
    $params=$xmlobj->Envelope->_value->Body->_value->$function->_value;
		$handle_function="handle_".$function;
    return $this->$handle_function($params);
  }

  /** \brief Create sample form for testing webservice. This is called of no request is send via browser.
  *
  *
  */

	private function create_sample_forms() {
    $i=0;

    echo "<html><body>";

    if(!isset($HTTP_RAW_POST_DATA)) {

    // Open a known directory, and proceed to read its contents
    if (is_dir($this->xmldir."/request")) {
      if ($dh = opendir($this->xmldir."/request")) {
        chdir($this->xmldir."/request");
        while (($file = readdir($dh)) !== false) {
          if(!is_dir($file)) {
            $fp=fopen($file,'r');
            if(preg_match('/xml$/',$file,$matches)) {
              $found_files=1;
              $contents = fread($fp, filesize($file));
              $ext=$matches[0];
              $reqs[]=str_replace("\n","\\n",addcslashes(html_entity_decode($contents),'"'));
              $names[]=$file;

              $i++;
            }
            echo '</form>';

            fclose($fp);
          }
        }
        closedir($dh);

        if($found_files) {

          echo '<script language="javascript">' . "\n" . 'var reqs = Array("' . implode('","', $reqs) . '");</script>';
          echo '<form name="f" method="POST" enctype="text/html; charset=utf-8"><textarea name="xml" rows=20 cols=140>';
          echo stripslashes($_REQUEST["xml"]);
          echo '</textarea><br><br>';
          echo '<select name="no" onChange="if (this.selectedIndex) document.f.xml.value = reqs[this.options[this.selectedIndex].value];">';
          echo '<option>Pick a test-request</option>';
          foreach ($reqs as $key => $req)
            echo '<option value="' . $key . '">'.$names[$key].'</option>';
          echo '</select> &nbsp; <input type="submit" name="subm" value="Try me">';
          echo '</form>';
        } else {
          echo "No example xml files found...";
        }
      }
    }
    echo "</body></html>";
    }
  }

}

?>
