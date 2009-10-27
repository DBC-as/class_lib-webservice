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

class objconvert {

	private $namespaces=array();

	public function __construct() {
	}

	/** \brief Convert ols-object to soap-xml
	 *
 	*
 	*/
	public function obj2json($obj) {  
    return json_encode($this->obj2badgerfish_obj($obj));
  }

  public function obj2badgerfish_obj($obj) {
    foreach ($obj as $key => $o) {
      if (is_scalar($o->_value))
        $ret->$key->{'$'} = $o->_value;
      else
        $ret->$key = $this->obj2badgerfish_obj($o->_value);
      if ($o->_attributes)
        foreach ($o->_attributes as $aname => $aval)
          $ret->$key->{'@'.$aname} = $aval->_value;
      if ($o->_namespace)
        $ret->$key->{'@xmlns'}->{'$'} = $o->_namespace;
    }
    return $ret;
  }
	public function obj2phps($obj) {
    return serialize($obj);
  }

	public function obj2xmlNs($obj) {
    $xml = $this->obj2xml($obj);
    foreach ($this->namespaces as $ns => $prefix)
      $used_ns .= ' xmlns' . ($prefix ? ':'.$prefix : '') . '="' . $ns . '"';
    if ($used_ns && $i = strpos($xml, ">"))
      $xml = substr($xml, 0, $i) . $used_ns . substr($xml, $i);
    return $this->xml_header() . $xml;
  }
	public function obj2soap($obj) {
    $xml = $this->obj2xml($obj);
    foreach ($this->namespaces as $ns => $prefix)
      $used_ns .= ' xmlns' . ($prefix ? ':'.$prefix : '') . '"' . $ns . '"';
    return $this->xml_header() . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"' . $used_ns . '><SOAP-ENV:Body>' . $xml . '</SOAP-ENV:Body></SOAP-ENV:Envelope>';
  }
  private function xml_header() {
    return '<?xml version="1.0" encoding="UTF-8"?>';
  }

	/** \brief Convert ols-object to xml
	 *
 	* used namespaces are returned in namespaces
 	* namespaces can be preset in namespaces
 	*
 	*/
	public function obj2xml($obj) {
 	 $ret = "";
   if ($obj)
 	   foreach ($obj as $tag => $o) {
 	     if (is_array($o))
 	       foreach ($o as $o_i)
 	         $ret .= $this->build_xml($tag, $o_i, $this->namespaces);
 	     else
 	         $ret .= $this->build_xml($tag, $o, $this->namespaces);
 	   }
 	 return $ret;
	}

	private function build_xml($tag, $obj) {
 	 $ret = "";
 	 if ($obj->_attributes)
 	   foreach ($obj->_attributes as $a_name => $a_val) {
 	     if ($a_val->_namespace)
 	       $a_prefix = $this->get_namespace_prefix($a_val->_namespace, $this->namespaces);
 	     $attr .= ' ' . $a_prefix . $a_name . '="' . $a_val->_value . '"';
 	   }
 	 if ($obj->_namespace)
 	   $prefix = $this->get_namespace_prefix($obj->_namespace, $this->namespaces);
 	 if (is_scalar($obj->_value))  
 	 	return $this->tag_me($prefix.$tag, $attr, $obj->_value);
 	 else
 	   return $this->tag_me($prefix.$tag, $attr, $this->obj2xml($obj->_value, $this->namespaces));
	}

	private function get_namespace_prefix($ns, &$namespaces) {
 	 if (empty($this->namespaces[$ns])) {
 	   $i = 1;
 	   while (in_array("ns".$i, $this->namespaces)) $i++;
 	   $this->namespaces[$ns] = "ns".$i;
 	 }
 	 return $this->namespaces[$ns] . ":";
	}

	public function add_namespace($namespace,$prefix) {
		 $this->namespaces[$namespace]=$prefix;
	}

	public function get_namespaces() {
		 return $this->namespaces;
	}

	function tag_me($tag, $attr, $val) {
 	 return '<' . $tag . $attr . '>' . $val . '</' . $tag . '>';
	}

}



?>
