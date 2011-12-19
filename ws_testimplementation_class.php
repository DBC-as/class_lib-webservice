<?php
require_once("xmlconvert_class.php");
require_once("objconvert_class.php");
require_once("curl_class.php");

class ws_testimplementation {

	private $xml_request_path;
	private $request_objects;
	private $xmlconvert;
	private $objconvert;

	function __construct () {
		$this->xml_request_path='xml/request/';
		$this->xmlconvert = new xmlconvert();
		$this->objconvert = new objconvert();
	}

	function load_request($request_name) {
		$request=file_get_contents($this->xml_request_path.$request_name.'.xml');
		$obj=$this->xmlconvert->soap2obj($request);
		$this->request_objects[$request_name]=$obj;
	}

	function print_request($request_name) {
		echo $xml=$this->objconvert->obj2xml($this->request_objects[$request_name]);
	}

	function change_tag_value(&$request_object, $target_tag_name, $target_tag_value) {
		foreach ($request_object as $k=>$v) {
			if($k==$target_tag_name) {
				$v->_value=$target_tag_value;
				break;
			} 
			if(is_object($v)) {
				$this->change_tag_value($v, $target_tag_name, $target_tag_value);
			} 
		}
	}

	function get_request_object($request_name) {
		return $this->request_objects[$request_name];
	}

	function  show_request_objects() {
		print_r($this->request_objects);
	}
}

?>
