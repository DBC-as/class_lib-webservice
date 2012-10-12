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


class xmlconvert {

  public function __construct() {
  }

  /** \brief Create an ols--object out of SOAP xml
   *
  *
   */
  public function soap2obj(&$request) {
    if (empty($request)) return FALSE;

    $dom = new DomDocument();
    $dom->preserveWhiteSpace = false;
    if (@ $dom->loadXML($request))
      return $this->xml2obj($dom);
  }

  /** \brief Converts domdocument object to object.
  *
  *
  */

  public function xml2obj($domobj, $force_NS='') {
    foreach ($domobj->childNodes as $node) {
      if ($node->nodeName == '#comment') {
        continue;
      }
      if ($force_NS) {
        $subnode->_namespace = $force_NS;
      }
      elseif ($node->namespaceURI) {
        $subnode->_namespace = $node->namespaceURI;
      }
      if ($node->nodeName == '#text' || $node->nodeName == '#cdata-section') {
        if (!trim($node->nodeValue)) {
          continue;
        }
        $subnode->_value = $node->nodeValue;
        $localName = '#text';
      }
      else {
        $localName = $node->localName;
        $subnode->_value = $this->xml2obj($node, $force_NS);
        if ($node->hasAttributes()) {
          foreach ($node->attributes as $attr) {
            $i = strpos($attr->nodeName, ':');
            $a_nodename = $attr->localName;
            if ($attr->namespaceURI)
              $subnode->_attributes->{$a_nodename}->_namespace = $attr->namespaceURI;
            $subnode->_attributes->{$a_nodename}->_value = $attr->nodeValue;
          }
        }
      }
      $ret->{$localName}[] = $subnode;
      unset($subnode);
    }

    // remove unnecessary level(s) for text-nodes and non-repeated tags
    if ($ret) {
      if (count((array)$ret) == 1 && isset($ret->{'#text'})) {
        $ret = $ret->{'#text'}[0]->_value;
      }
      else {
        foreach ($ret as $tag => $obj) {
          if (count($obj) == 1) {
            $ret->$tag = $obj[0];
          }
        }
        reset($ret);
      }
    }

    return $ret;

  }

}


