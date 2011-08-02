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


/** \brief Webservice server
 *
 *
 */

require_once('OLS_class_lib/curl_class.php');
require_once('OLS_class_lib/verbose_class.php');
require_once('OLS_class_lib/inifile_class.php');
require_once('OLS_class_lib/timer_class.php');
require_once('OLS_class_lib/aaa_class.php');
require_once('OLS_class_lib/restconvert_class.php');
require_once('OLS_class_lib/xmlconvert_class.php');
require_once('OLS_class_lib/objconvert_class.php');

abstract class webServiceServer {

    protected $config; // inifile object
    protected $watch; // timer object
    protected $aaa; // Authentication, Access control and Accounting object
    protected $xmldir='./'; // xml directory
    protected $validate= array(); // xml validate schemas
    protected $objconvert; // OLS object to xml convert
    protected $xmlconvert; // xml to OLS object convert
    protected $xmlns; // namespaces and prefixes
    protected $tag_sequence; // tag sequence according to XSD or noame of XSD
    protected $soap_action;
    protected $output_type='';


    /** \brief Webservice constructer
     *
    * @param inifile <filename>
     *
     */
    public function  __construct($inifile) {
        // initialize config and verbose objects
        $this->config = new inifile($inifile);

        if ($this->config->error) {
            die('Error: '.$this->config->error );
        }

        // service closed
        if ($http_error = $this->config->get_value('service_http_error', 'setup')) {
            header($http_error);
            die($http_error);
        }

        libxml_use_internal_errors(TRUE);

        verbose::open($this->config->get_value('logfile', 'setup'),
                      $this->config->get_value('verbose', 'setup'));
        $this->watch = new stopwatch('', ' ', '', '%s:%01.3f');

        if ($this->config->get_value('xmldir'))
            $this->xmldir=$this->config->get_value('xmldir');
        $this->xmlns = $this->config->get_value('xmlns', 'setup');
        $this->tag_sequence = $this->config->get_value('tag_sequence', 'setup');
        $this->version = $this->config->get_value('version', 'setup');
        $this->output_type = $this->config->get_value('default_output_type', 'setup');

        if ($aaa_oci = $this->config->get_value('aaa_credentials', 'aaa')) {
            $this->aaa = new aaa($aaa_oci,
                                 $this->config->get_value('aaa_cache_address', 'aaa'),
                                 $this->config->get_value('aaa_cache_seconds', 'aaa'),
                                 $this->config->get_value('aaa_ip_rights', 'aaa'));
        }
    }

    public function __destruct() { }

    /** \brief Handles request from webservice client
    *
    */
    public function handle_request() {
        if ($_SERVER['QUERY_STRING'] == 'ShowInfo')
            $this->ShowInfo();
        if ($_SERVER['QUERY_STRING'] == 'Version') {
            die($this->version);
        } elseif ($_SERVER['QUERY_STRING'] == 'HowRU') {
            $this->HowRU();
        } elseif ($_SERVER['QUERY_STRING'] == 'RegressionTest') {
            $this->RegressionTest();
        } elseif (isset($_POST['xml'])) {
            $xml=trim(stripslashes($_POST['xml']));
            $this->soap_request($xml);
        } elseif (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $this->soap_request($GLOBALS['HTTP_RAW_POST_DATA']);
        } elseif (!empty($_SERVER['QUERY_STRING']) ) {
            $this->rest_request();
        } elseif ($this->in_house()
                  || $this->config->get_value('show_samples', 'setup')
                  || ip_func::ip_in_interval($_SERVER['REMOTE_ADDR'], $this->config->get_value('show_samples_ip_list', 'setup'))) {
            $this->create_sample_forms();
        } else {
            header('HTTP/1.0 404 Not Found');
        }
    }

    /** \brief Handles and validates soap request
      *
    * @param xml <string>
    */
    private function soap_request($xml) {
        // Debug verbose::log(TRACE, 'Request ' . $xml);

        // validate request
        $this->validate = $this->config->get_value('validate');

        if ($this->validate['soap_request'] || $this->validate['request'])
            $error = ! $this->validate_soap($xml, $this->validate, 'request');

        if (empty($error)) {
            // parse to object
            $this->xmlconvert=new xmlconvert();
            $xmlobj=$this->xmlconvert->soap2obj($xml);
            // soap envelope?
            if ($xmlobj->Envelope) {
                $request_xmlobj = &$xmlobj->Envelope->_value->Body->_value;
                $soap_namespace = $xmlobj->Envelope->_namespace;
            } else {
                $request_xmlobj = &$xmlobj;
                $soap_namespace = 'http://www.w3.org/2003/05/soap-envelope';
                $this->output_type = 'xml';
            }

            // initialize objconvert and load namespaces
            $this->objconvert=new objconvert($this->xmlns, $this->tag_sequence);

            // handle request
            if ($response_xmlobj=$this->call_xmlobj_function($request_xmlobj)) {
                // validate response
                if ($this->validate['soap_response'] || $this->validate['response']) {
                    $response_xml=$this->objconvert->obj2soap($response_xmlobj, $soap_namespace);
                    $error = ! $this->validate_soap($response_xml, $this->validate, 'response');
                }

                if (empty($error)) {
                    // Branch to outputType
                    list($service, $req) = each($request_xmlobj);
                    if (empty($this->output_type) || $req->_value->outputType->_value)
                        $this->output_type = $req->_value->outputType->_value;
                    switch ($this->output_type) {
                    case 'json':
                        header('Content-Type: application/json');
                        $callback = &$req->_value->callback->_value;
                        if ($callback && preg_match("/^\w+$/", $callback))
                            echo $callback . ' && ' . $callback . '(' . $this->objconvert->obj2json($response_xmlobj) . ')';
                        else
                            echo $this->objconvert->obj2json($response_xmlobj);
                        break;
                    case 'php':
                        header('Content-Type: application/php');
                        echo $this->objconvert->obj2phps($response_xmlobj);
                        break;
                    case 'xml':
                        header('Content-Type: text/xml');
                        echo $this->objconvert->obj2xmlNS($response_xmlobj);
                        break;
                    default:
                        if (empty($response_xml))
                            $response_xml =  $this->objconvert->obj2soap($response_xmlobj, $soap_namespace);
                        if ($soap_namespace == 'http://www.w3.org/2003/05/soap-envelope' && empty($_POST['xml']))
                            header('Content-Type: application/soap+xml');   // soap 1.2
                        else
                            header('Content-Type: text/xml');
                        echo $response_xml;
                    }
                    // request done and response send, dump timer
                    if ($dump_timer = $this->config->get_value('dump_timer', 'setup'))
                        verbose::log(TIMER, sprintf($dump_timer, $this->soap_action) .
                                     ':: ' . $this->watch->dump());
                } else
                    $this->soap_error('Error in response validation.');
            } else
                $this->soap_error('Incorrect SOAP envelope or wrong/unsupported request');
        } else
            $this->soap_error('Error in request validation.');
    }

    /** \brief Handles rest request, converts it to xml and calls soap_request()
    *
    * @param xml <string>
    *
    */
    private function rest_request() {
        // convert to soap
        $xmlns = ($this->xmlns['NONE'] ? $this->xmlns['NONE'] : $this->xmlns['0']);
        $rest = new restconvert($xmlns);
        $xml=$rest->rest2soap(&$this->config);
        $this->soap_request($xml);
    }

    /** \brief
    *
    */
    private function ShowInfo() {
        $show_func = 'show_info';
        if (method_exists($this, $show_func) && $this->in_house())
            $this->$show_func();
    }

    /** \brief
    *  Return TRUE if the IP is in_house_domain
    */
    protected function in_house() {
        static $homie;
        if (!isset($homie)) {
            if (!$domain = $this->config->get_value('in_house_domain', 'setup'))
                $domain = '.dbc.dk';
            @ $remote = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $homie = (strpos($remote, $domain) + strlen($domain) == strlen($remote));
            if ($homie)
                $homie = (gethostbyname($remote) == $_SERVER['REMOTE_ADDR']); // paranoia check
        }
        return $homie;
    }

    /** \brief RegressionTest tests the webservice
    *
    */
    private function RegressionTest() {
        if (! is_dir($this->xmldir.'/regression'))
            die('No regression catalouge');

        if ($dh = opendir($this->xmldir.'/regression')) {
            chdir($this->xmldir.'/regression');
            $reqs = array();
            while (($file = readdir($dh)) !== false)
                if (!is_dir($file) && preg_match('/xml$/',$file,$matches)) 
                    $fnames[] = $file;
            if (count($fnames)) {
                asort($fnames);
                $curl = new curl();
                $curl->set_option(CURLOPT_POST, 1);
                foreach ($fnames as $fname) {
                    $contents = str_replace("\r\n", PHP_EOL, file_get_contents($fname));
                    $curl->set_post_xml($contents);
                    $reply = $curl->get($_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
                    echo $reply;
                }
            } else
                die('No files found for regression test');
        } else
            die('Cannot open regression catalouge: ' . $this->xmldir.'/regression');
    }

    /** \brief HowRU tests the webservice and answers "Gr8" if none of the tests fail. The test cases resides in the inifile.
    *
    */
    private function HowRU() {
        $curl = new curl();
        $curl->set_option(CURLOPT_POST, 1);
        $tests = $this->config->get_value('test', 'howru');
        if ($tests) {
            $reg_match = $this->config->get_value('preg_match', 'howru');
            $reg_error = $this->config->get_value('error', 'howru');
            $url = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            foreach ($tests as $k=>$v) {
                $reply=$curl->get($url.'?action='.$v);
                $preg_match=$reg_match[$k];
                if (!preg_match("/$preg_match/",$reply))
                    die($reg_error[$k]);
            }
            $curl->close();
        }
        die('Gr8');
    }

    /** \brief Validates soap and xml
    *
    * @param xml <string>
    * @param schema filenames <array>
    * @param validate name <string>
      *
    */

    protected function validate_soap($soap, $schemas, $validate_schema) {
        $validate_soap = new DomDocument;
        $validate_soap->preserveWhiteSpace = FALSE;
        @ $validate_soap->loadXml($soap);
        if (($sc = $schemas['soap_'.$validate_schema]) && ! @ $validate_soap->schemaValidate($sc))
            return FALSE;

        if ($sc = $schemas[$validate_schema]) {
            if ($validate_soap->firstChild->localName == 'Envelope'
                    && $validate_soap->firstChild->hasChildNodes()) {
                foreach ($validate_soap->firstChild->childNodes as $soap_node)
                if ($soap_node->localName == 'Body') {
                    $xml = &$soap_node->firstChild;
                    $validate_xml = new DOMdocument;
                    @ $validate_xml->appendChild($validate_xml->importNode($xml, TRUE));
                    break;
                }
            }
            if (empty($validate_xml))
                $validate_xml = &$validate_soap;

            if (! @ $validate_xml->schemaValidate($sc))
                return FALSE;
        }

        return TRUE;
    }

    /** \brief send an error header and soap fault
    *
    * @param err <string>
    *
    */
    protected function soap_error($err) {
        $elevel = array(LIBXML_ERR_WARNING => "\n Warning",
                        LIBXML_ERR_ERROR => "\n Error",
                        LIBXML_ERR_FATAL => "\n Fatal");
        if ($errors = libxml_get_errors())
            foreach ($errors as $error)
                $xml_err .= $elevel[$error->level] . ": " .  trim($error->message) . 
                            ($error->file ? " in file " . $error->file : " on line " . $error->line);
        header('HTTP/1.0 400 Bad Request');
        header('Content-Type: text/xml; charset="utf-8"');
        echo '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
        <SOAP-ENV:Body>
        <SOAP-ENV:Fault>
        <faultcode>SOAP-ENV:Server</faultcode>
        <faultstring>' . htmlspecialchars($err . $xml_err) . '</faultstring>
        </SOAP-ENV:Fault>
        </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>';
    }

    /** \brief Validates xml
    *
    * @param xml <string>
    * @param schema_filename <string>
    * @param resolve_externals <bool>
      *
    */

    protected function validate_xml($xml, $schema_filename, $resolve_externals=FALSE) {
        $validateXml = new DomDocument;
        $validateXml->resolveExternals = $resolve_externals;
        $validateXml->loadXml($xml);
        if (@ $validateXml->schemaValidate($schema_filename)) {
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
        if ($xmlobj) {
            $soapActions = $this->config->get_value('soapAction', 'setup');
            $request=key($xmlobj);
            if ($this->soap_action = array_search($request, $soapActions)) {
                $params=$xmlobj->$request->_value;
                if (method_exists($this, $this->soap_action)) {
                    if (is_object($this->aaa)) {
                        $auth = &$params->authentication->_value;
                        $this->aaa->init_rights($auth->userIdAut->_value,
                                                $auth->groupIdAut->_value,
                                                $auth->passwordAut->_value,
                                                $_SERVER['REMOTE_ADDR']);
                    }
                    return $this-> {$this->soap_action}($params);
                }
            }
        }

        return FALSE;
    }

    /** \brief Create sample form for testing webservice. This is called of no request is send via browser.
    *
    *
    */

    private function create_sample_forms() {
        echo '<html><head>';

        // Open a known directory, and proceed to read its contents
        if (is_dir($this->xmldir.'/request')) {
            if ($dh = opendir($this->xmldir.'/request')) {
                chdir($this->xmldir.'/request');
                $reqs = array();
                while (($file = readdir($dh)) !== false) {
                    if (!is_dir($file)) {
                        if (preg_match('/html$/',$file,$matches)) $info = file_get_contents($file);
                        if (preg_match('/xml$/',$file,$matches)) $fnames[] = $file;
                    }
                }
                closedir($dh);

                if ($info || count($fnames)) {
                    asort($fnames);
                    foreach ($fnames as $fname) {
                        $contents = str_replace("\r\n", PHP_EOL, file_get_contents($fname));
                        $contents=addcslashes(str_replace("\n",'\n',$contents), '"');
                        $reqs[]=$contents;
                        $names[]=$fname;
                    }

                    echo '<script language="javascript">' . "\n" . 'var reqs = Array("' . implode('","', $reqs) . '");</script>';
                    echo '</head><body><form target="_blank" name="f" method="POST" accept-charset="utf-8"><textarea name="xml" rows=20 cols=90>';
                    echo $_REQUEST['xml'];
                    echo '</textarea><br><br>';
                    echo '<select name="no" onChange="if (this.selectedIndex) document.f.xml.value = reqs[this.options[this.selectedIndex].value];">';
                    echo '<option>Pick a test-request</option>';
                    foreach ($reqs as $key => $req)
                    echo '<option value="' . $key . '">'.$names[$key].'</option>';
                    echo '</select> &nbsp; <input type="submit" name="subm" value="Try me">';
                    echo '</form>';
                    echo $info;
                } else {
                    echo '</head><body>No example xml files found...';
                }
                echo '<p style="font-size:0.6em">Version: ' . $this->version . '</p>';
            }
        }
        echo '</body></html>';
    }

}

?>
