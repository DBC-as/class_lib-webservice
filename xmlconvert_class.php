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

class xmlconvert {

	public function __construct() {
	}

	/** \brief Create an ols--object out of SOAP xml
 	*
	*
 	*/
	public function soap2obj(&$request) {
 	 $dom = new DomDocument();
 	 $dom->preserveWhiteSpace = false;
 	 if ($dom->loadXML($request))
 	   return $this->xml2obj($dom);
	}

  /** \brief Converts domdocument object to object.
  *
  *
  */

	public function xml2obj($domobj) {
 	 foreach ($domobj->childNodes as $node) {
 	   if ($node->nodeName == "#text")
 	     $ret = $node->nodeValue;
 	   else {
 	     $i = strpos($node->nodeName, ":");
 	     $nodename = ($i ? substr($node->nodeName, $i+1) : $node->nodeName);
 	     if ($node->namespaceURI)
 	       $help->_namespace = $node->namespaceURI;
 	     $help->_value = $this->xml2obj($node);
 	     if ($node->hasAttributes())
 	       foreach ($node->attributes as $attr) {
 	         $i = strpos($attr->nodeName, ":");
 	         $a_nodename = ($i ? substr($attr->nodeName, $i+1) : $attr->nodeName);
 	         if ($attr->namespaceURI)
 	           $help->_attributes->{$a_nodename}->_namespace = $attr->namespaceURI;
 	         $help->_attributes->{$a_nodename}->_value = $attr->nodeValue;
 	       }
 	     if (is_array($ret->{$nodename}))
 	       $ret->{$nodename}[] = $help;
 	     elseif (isset($ret->$nodename)) {
 	       $tmp = $ret->$nodename;
 	       unset($ret->$nodename);
 	       $ret->{$nodename}[] = $tmp;
 	       $ret->{$nodename}[] = $help;
 	     } else
 	       $ret->$nodename = $help;
 	     unset($help);
 	   }
 	 }
 	 return $ret;
	}

}



?>
