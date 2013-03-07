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


/** \brief Convert ols-object to json, xml, php
 *
 * An OLS object contains the data in _value
 * It may have a namespace in _namespace
 * and it may have attributes in _attributes
 * and it may have a cdata-flag set in _cdata (only used in xml-output)
 *
 * Example:
 *   $obj->tagname->_value = "A&A";
 *   will convert to a xml doc like: <tagname>A&amp;A</tagname>
 *                   json like: {"tagname":{"$":"A&A"},"@namespaces":null}
 *
 * Example:
 *   $obj->tagname->_value = "A&A";
 *   $obj->tagname->_namespace = "http://some.namespace.com/";
 *   will convert to a xml doc like: <ns1:tagname xmlns:ns1="http://some.namespace.com/">A&amp;A</ns1:tagname>
 *                   json like: {"tagname":{"$":"A&A","@":"ns1"},"@namespaces":{"ns1":"http:\/\/some.namespace.com\/"}}
 *
 * Example:
 *   $obj->tagname->_value = "A&A";
 *   $obj->tagname->_attributes->attr->_value = "ATTR";
 *   will convert to a xml doc like: <tagname attr="ATTR">A&amp;A</tagname>
 *                   json like: {"tagname":{"$":"A&A","@attr":{"$":"ATTR"}},"@namespaces":null}
 *
 * Example:
 *   $obj->tagname->_value = "A&A";
 *   $obj->tagname->_attributes->attr->_value = "ATTR";
 *   $obj->tagname->_attributes->attr->_namespace = "http://some.namespace.com/";
 *   will convert to a xml doc like: <tagname ns1:attr="ATTR" xmlns:ns1="http://some.namespace.com/">A&amp;A</tagname>
 *                   json like: {"tagname":{"$":"A&A","@attr":{"$":"ATTR","@":"ns1"}},"@namespaces":{"ns1":"http:\/\/some.namespace.com\/"}}
 *
 * Example:
 *   $obj->tagname->_value = "A&A";
 *   $obj->tagname->_cdata = TRUE;
 *   will convert to a xml doc like: <tagname><![CDATA[A&A]]></tagname>
 *                   json like: {"tagname":{"$":"A&A"},"@namespaces":null}
	*/

class objconvert {

  private $tag_sequence=array();
  private $namespaces=array();
  private $used_namespaces=array();
  private $default_namespace;

  public function __construct($xmlns='', $tag_seq='') {
    if ($xmlns) {
      foreach ($xmlns as $prefix => $ns) {
        if ($prefix == 'NONE' || $prefix == '0')
          $prefix = '';
        $this->add_namespace($ns, $prefix);
      }
    }
    $this->tag_sequence = $tag_seq;
  }

  public function set_default_namespace($ns) {
    $this->default_namespace = $ns;
    if ($this->namespaces[$ns] == '') {
      unset($this->namespaces[$ns]);         // remove deprecated setup
    }
  }

  /** \brief Convert ols-object to json
  	*/
  public function obj2json($obj) {
    foreach ($this->namespaces as $ns => $prefix) {
      if ($prefix)
        $o_ns->$prefix = $ns;
      else
        $o_ns->{'$'} = $ns;
    }
    $json_obj = $this->obj2badgerfish_obj($obj);
    $json_obj->{'@namespaces'} = $o_ns;
    return json_encode($json_obj);
  }

  /** \brief compress ols object to badgerfish-inspired object
  	*/
  private function obj2badgerfish_obj($obj) {
    if ($obj) {
      foreach ($obj as $key => $o) {
        if (is_array($o)) {
          foreach ($o as $o_i) {
            $ret->{$key}[] = $this->build_json_obj($o_i);
          }
        }
        else
          $ret->$key = $this->build_json_obj($o);
      }
    }
    return $ret;
  }

  /** \brief convert one object
  	*/
  private function build_json_obj($obj) {
    if (is_scalar($obj->_value))
      $ret->{'$'} = html_entity_decode($obj->_value);
    else
      $ret = $this->obj2badgerfish_obj($obj->_value);
    if ($obj->_attributes) {
      foreach ($obj->_attributes as $aname => $aval) {
        $ret->{'@'.$aname} = $this->build_json_obj($aval);
      }
    }
    if ($obj->_namespace)
      $ret->{'@'} = $this->get_namespace_prefix($obj->_namespace);
    return $ret;
  }

  /** \brief experimental php serialized
  	*/
  public function obj2phps($obj) {
    return serialize($obj);
  }

  /** \brief Convert ols-object to xml with namespaces
  	*/
  public function obj2xmlNs($obj) {
    $this->used_namespaces = array();
    $xml = $this->obj2xml($obj);
    $used_ns = $this->get_used_namespaces_as_header();
    if ($used_ns && $i = strpos($xml, '>'))
      $xml = substr($xml, 0, $i) . $used_ns . substr($xml, $i);
    return $this->xml_header() . $xml;
  }

  /** \brief Convert ols-object to soap
  	*/
  public function obj2soap($obj, $soap_ns = 'http://schemas.xmlsoap.org/soap/envelope/') {
    $this->used_namespaces = array();
    $xml = $this->obj2xml($obj);
    return $this->xml_header() . 
           '<SOAP-ENV:Envelope xmlns:SOAP-ENV="' . $soap_ns . '"' . 
           $this->get_used_namespaces_as_header() . '><SOAP-ENV:Body>' . 
           $xml . '</SOAP-ENV:Body></SOAP-ENV:Envelope>';
  }

  /** \brief
   */
  private function get_used_namespaces_as_header() {
    foreach ($this->namespaces as $ns => $prefix) {
      if ($this->used_namespaces[$ns] || empty($prefix))
        $used_ns .= ' xmlns' . ($prefix ? ':'.$prefix : '') . '="' . $ns . '"';
    }
    if ($this->default_namespace) {
      $used_ns .= ' xmlns="' . $this->default_namespace . '"';
    }
    return $used_ns;
  }

  /** \brief UTF-8 header
  	*/
  private function xml_header() {
    return '<?xml version="1.0" encoding="UTF-8"?>';
  }

  /** \brief Convert ols-object to xml
   *
  	* used namespaces are returned in this->namespaces
  	* namespaces can be preset with add_namespace()
  	*
  	*/
  public function obj2xml($obj) {
    $this->check_tag_sequence();
    $ret = '';
    if ($obj) {
      foreach ($obj as $tag => $o) {
        if (is_array($o)) {
          foreach ($o as $o_i) {
            $ret .= $this->build_xml($tag, $o_i);
          }
        }
        else
          $ret .= $this->build_xml($tag, $o);
      }
    }
    return $ret;
  }

  /** \brief handles one node
  	*/
  private function build_xml($tag, $obj) {
    $ret = '';
    if ($obj->_attributes) {
      foreach ($obj->_attributes as $a_name => $a_val) {
        if ($a_val->_namespace)
          $a_prefix = $this->set_prefix_separator($this->get_namespace_prefix($a_val->_namespace));
        else
          $a_prefix = '';
        $attr .= ' ' . $a_prefix . $a_name . '="' . htmlspecialchars($a_val->_value) . '"';
// prefix in value hack
        $this->set_used_prefix($a_val->_value);
      }
    }
    if ($obj->_namespace)
      $prefix = $this->set_prefix_separator($this->get_namespace_prefix($obj->_namespace));
    if (is_scalar($obj->_value))
      if ($obj->_cdata)
        return $this->tag_me($prefix.$tag, $attr, '<![CDATA[' . $obj->_value . ']]>');
      else
        return $this->tag_me($prefix.$tag, $attr, htmlspecialchars($obj->_value));
    else
      return $this->tag_me($prefix.$tag, $attr, $this->obj2xml($obj->_value));
  }

  /** \brief Updates used_namespaces from prefix in $val
  	*/
  private function set_used_prefix($val) {
    if ($p = strpos($val, ':')) {
      foreach ($this->namespaces as $ns => $prefix) {
        if ($prefix == substr($val, 0, $p)) {
          $this->used_namespaces[$ns] = TRUE;
          break;
        }
      }
    }
  }

  /** \brief returns prefixes and store namespaces
  	*/
  private function get_namespace_prefix($ns) {
    if (empty($this->namespaces[$ns])) {
      $i = 1;
      while (in_array('ns'.$i, $this->namespaces)) $i++;
      $this->namespaces[$ns] = 'ns'.$i;
    }
    $this->used_namespaces[$ns] = TRUE;
    return $this->namespaces[$ns];
  }

  /** \brief Separator between prefix and tag-name in xml
  	*/
  private function set_prefix_separator($prefix) {
    if ($prefix) return $prefix . ':';
    else return $prefix;
  }

  /** \brief get or use tag_sequence
  	*/
  private function check_tag_sequence() {
    if ($this->tag_sequence && is_scalar($this->tag_sequence)) {
      require_once('OLS_class_lib/schema_class.php');
      $schema_parser = new schema_something();
      $this->tag_sequence = $schema_parser->parse($this->tag_sequence);
    }
  }

  /** \brief Adds known namespaces
  	*/
  public function add_namespace($namespace,$prefix) {
    $this->namespaces[$namespace]=$prefix;
    asort($this->namespaces);
  }

  /** \brief Returns used namespaces
  	*/
  public function get_namespaces() {
    return $this->namespaces;
  }

  /** \brief Set namespace on all object nodes
  	*/
  public function set_obj_namespace($obj, $ns) {
    if (empty($obj) || is_scalar($obj))
      return $obj;
    if (is_array($obj)) {
      foreach ($obj as $key => $val) {
        $ret[$key] = $this->set_obj_namespace($val, $ns);
      }
    }
    else {
      foreach ($obj as $key => $val) {
        $ret->$key = $this->set_obj_namespace($val, $ns);
        if ($key === '_value')
          $ret->_namespace = $ns;
      }
    }
    return $ret;
  }

  /** \brief produce balanced xml
  	*/
  public function tag_me($tag, $attr, $val) {
    if ($tag == '#text') {
      return $val;
    }
    else {
      if ($attr && $attr[0] <> ' ') $space = ' ';
      return '<' . $tag . $space . $attr . '>' . $val . '</' . $tag . '>';
    }
  }

}




