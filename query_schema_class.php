<?

/* testing:
echo 'new query_schema("opensearch.xsd")<br>';
$qs = new query_schema('opensearch.xsd');
echo '<hr/><pre>' . htmlentities( print_r($qs->get_schemas(),1) ) . '</pre>';
echo '<pre>' . htmlentities( $qs->get_element_xml('object') ) . '</pre>';
echo '$qs->get_element_sequence_prefix("object"),1 )<br>';
echo '<pre>' . print_r( $qs->get_element_sequence_prefix('object'),1 ) . '</pre>';
echo '<pre>' . print_r( $qs->get_element_sequence_namespace('object'),1 ) . '</pre>';
echo '$qs->ini_output()<br>';
echo '<pre>' . $qs->ini_output() . '</pre>';

echo '<hr>';
echo 'new query_schema("openfindorder.xsd")<br>';
$qs = new query_schema('openfindorder.xsd');
echo '<hr/><pre>' . htmlentities( print_r($qs->get_schemas(),1) ) . '</pre>';
echo '<pre>' . htmlentities( $qs->get_element_xml('findAllOrdersRequest') ) . '</pre>';
echo 'print_r( $qs->get_element_sequence_prefix("findAllOrdersRequest"),1 )<br>';
echo '<pre>' . print_r( $qs->get_element_sequence_prefix('findAllOrdersRequest'),1 ) . '</pre>';
echo '<pre>' . print_r( $qs->get_element_sequence_namespace('findAllOrdersRequest'),1 ) . '</pre>';
echo '<pre>' . $qs->ini_output() . '</pre>';
*/

/******************************************************
 * Jørgen Nielsen, 4/8 2010:                          *
 ******************************************************/

/*
 * Reads a XML schema into a DOMDocument.
 * If there are <xs:import> elements, the elements of that schema
 * will be added recursivly, discarding duplicates.
 *
 * The schema(s) can them be queried about schema elements.
 *
 * It's assumed that the schema files are local, with the same path as the script
 *
 * Timer (approx.) for simple schemas (1 schema):    Initialize: 0.005 sec, get_element_sequence_prefix(): 0.002 sec; ini_output():0.450 sec.
 * Timer (approx.) for complex schemas (5+ schemas): Initialize: 0.015 sec, get_element_sequence_prefix(): 0.060 sec; ini_output():0.750 sec.
 */

/* ex:
 * $qs = new query_schema('openfindorder.xsd');
 * echo htmlentities( $qs->get_element_xml('findAllOrdersRequest') );
 * print_r( $qs->get_element_sequence('findAllOrdersRequest') );
 * print_r( $qs->get_element_sequence_prefix('findAllOrdersRequest') );
 * print_r( $qs->ini_output() );
 */

// require_once("timer_func.phpi");

class query_schema {

  ///////////////////////
  // PRIVATE VARIABLES //
  ///////////////////////

  /**
   * The default schema, and recursivly imported schemas.
   * @access private
   * @var    array ('dom' => DOMdocument, 'namespace' => string, 'prefix' => string, 'url' => string).
   */
  private $schemas ;

  /**
   * The default schema.
   * @access private
   * @var    DOMdocument
   */
  private $dom ;


  ////////////////////
  // PUBLIC METHODS //
  ////////////////////


  /**
   * class constructor
   * Initializes the class
   * @param string $url. The URL to be accessed by this instance of the class.
   * @param array $schemas. The recursivly imported schemas.
   */

  public function __construct( $url, $schemas=NULL ) {

    require_once("timer_func.phpi");
    // $this->stopur = new stopwatch("query_schema: <br>\n", "<br>\n", "", "%s:%01.3f");
    // $this->stopur -> start("__construct");

    if ( $url == NULL ) {
      if (method_exists('verbose','log'))
        verbose::log(ERROR, "query_schema class called with no schema file.");
      elseif (function_exists('verbose'))
        verbose(ERROR, "query_schema class called with no schema file.");
      return false;
    }
    $this->schemas = $schemas;

    $this->dom = new DOMDocument();
    if ( is_file($url) )
      $this->dom -> load($url);
    else {
      if (method_exists('verbose','log'))
        verbose::log(ERROR, 'query_schema class : file '. $url . ' does not exist.');
      elseif (function_exists('verbose'))
        verbose(ERROR, 'query_schema class : file '. $url . ' does not exist.');
      return false;
    }

    $this->schemas[] = array (
      'dom' => $this->dom,
      'namespace' => $this->get_namespace(),
      'prefix' => $this->get_prefix(),
      'url' => $url
    );

    $this->import_schemas();

    // set prefix, in case it's not set in the imported schema
    foreach ($this->schemas as $i => $schema) {
      $prefix = $this->dom->documentElement->lookupPrefix( $schema['namespace'] );
      if ( $prefix )
        $this->schemas[$i]['prefix'] = $prefix;
    }

    // $this->stopur -> stop("__construct");

  }



  /**
   * get sortsequences for the schemas, formatted for a .ini file.
   * @returns string
   */
  public function ini_output() {

    // $this->stopur -> start("ini_output");
    $ini_out = '';
    foreach ($this->schemas as $i => $schema) {
      $xPath = new DOMXPath($schema['dom']);
      $nodelist = $xPath -> query('xs:element | xs:group');
      foreach ($nodelist as $node) {
        $element_name = $schema['prefix'].':'.$node->getAttribute('name');
        $list = $this->get_element_sequence_prefix( $element_name );
        $sort_order = '';
        if ( sizeof($list) > 1 ) {
          foreach ( $list as $elem ) {
            if ( $sort_order )
              $sort_order .= '/';
            $sort_order .= $elem;
          }
          $ini_out .= 'sort_sequence[' . $element_name . '] = ' . $sort_order . "\n";
        }
      }
    }
    // $this->stopur -> stop("ini_output");
    return $ini_out;
  }


  /**
   * get information on the named element, as specified in the schemas
   * @returns XML document
   */
  public function get_element_xml( $element_name ) {
    $node = $this->get_element_by_name( $element_name );
    if ( !$node )
      return false;
    $doc = new DOMDocument;
    $newnode = $doc->appendChild( $this->cloneNode($node,$doc) );
    return $doc->saveXML();
  }



  /**
   * get a list of the elements in the named element, as specified in the schemas
   * @returns array
   */
  public function get_element_sequence( $element_name ) {
    $node = $this->get_element_by_name( $element_name );
    if ( !$node )
      return false;
    return $this->node_2_list($node,$arr);
  }


  /**
   * get a list of the elements in the named element, with prefix, as specified in the schemas
   * @returns array
   */
  public function get_element_sequence_prefix( $element_name ) {
    // $this->stopur -> start("get_element_sequence_prefix");
    $node = $this->get_element_by_name( $element_name );
    if ( !$node )
      return false;
    return $this->node_2_list( $node, $arr, true );
    // $this->stopur -> stop("get_element_sequence_prefix");
  }



  /**
   * get a list of the elements in the named element, with namespace, as specified in the schemas
   * @returns array
   */
  public function get_element_sequence_namespace( $element_name ) {
    $node = $this->get_element_by_name( $element_name );
    if ( !$node )
      return false;
    return $this->node_2_list( $node, $arr, false, true );
  }



  /**
   * get the schemas used
   * @returns array
   */
  public function get_schemas() {
    return $this->schemas;
  }


  // public function get_stopur() {
  //   return $this->stopur->dump();
  // }


  /////////////////////
  // PRIVATE METHODS //
  /////////////////////


  /**
   * add element name to array, recursivly
   * @returns array
   */
  private function node_2_list( $node, $arr=NULL, $set_prefix=false, $set_ns=false ) {

    if ( $node->nodeName == 'xs:element' ) {
      $element_name   = $node->getAttribute('name');
      $element_prefix = $node->getAttribute('prefix');
      $element_ns = $node->getAttribute('namespace');
      if ( $set_prefix && $element_prefix )
        $element_name = $element_prefix . ':' . $element_name;
      if ( $set_ns && $element_ns )
        $element_name = $element_ns . ':' . $element_name;
      $arr[] = $element_name;
    }

    if ( $node->childNodes ) {
      foreach ( $node->childNodes as $child ) {
        if ( $child->nodeName != "#text" ) {
          $arr = $this->node_2_list($child,$arr,$set_prefix, $set_ns);
        }
      }
    }

    return $arr;

  }



  /**
   * get information on the named element, as specified in the schema
   * @returns DOMNode
   */
  private function get_element_by_name( $element_name ) {

    $pos = strpos($element_name,':');
    if ( $pos !== false ) {
      $elem = explode(':',$element_name);
      $prefix = $elem[0];
      $element_name = $elem[1];
    }

    $node = NULL;

    if ( $prefix ) {

      foreach ($this->schemas as $key => $schema) {
        if ( $prefix == $schema['prefix'] ) {
          $node = $this->dom_search($element_name, $schema['dom']);
          if ( $node && $schema['namespace'] != $this->get_namespace() ) {
            $node->setAttribute('prefix',$schema['prefix']);
            $node->setAttribute('namespace',$schema['namespace']);
          }
        }
      }

    } else {

      $node = $this->dom_search($element_name, $this->schemas[0]['dom']);
      if ( $node && $this->schemas[0]['namespace'] != $this->get_namespace() ) {
        $node->setAttribute('prefix',$this->schemas[0]['prefix']);
        $node->setAttribute('namespace',$this->schemas[0]['namespace']);
      }

    }

    return $node;
  }


  /**
   * Search the schema for xs:element or xs:group nodes with a name equal to $element_name
   * @returns DOMNode
   */
  private function dom_search( $element_name, $dom) {

    $xPath = new DOMXPath($dom);

    $nodelist = $xPath -> query('xs:element[@name = "'.$element_name.'"] | xs:group[@name = "'.$element_name.'"]');
    if (  $nodelist->length == 0 )
      return false;
    if (  $nodelist->length > 1 )
      return false;
    foreach ($nodelist as $node) {
      if ( $follow_node = $this->follow_node($node,$dom) )
        return $follow_node;
      return $node;
    }
  }



  /**
   * replace referenced xs:element and xs:group nodes
   * @returns DOMNode
   */
  private function follow_node( $node, $dom ) {

    if ( $ref = $node->getAttribute('ref') ) {
      if ( $follow_node = $this->get_element_by_name($ref) )
        $node = $follow_node;
    }

    if ( $node->childNodes ) {
      $i = $node->childNodes->length - 1;
      while ($i > -1) {
        $element = $node->childNodes->item($i);
        if ( $element->nodeName != "#text" ) {
          $newelement = $this->follow_node($element,$dom);
          $newelement = $this->cloneNode($newelement,$dom);
          $element->parentNode->replaceChild($newelement, $element);
        }
        if ( $element->nodeName == "xs:annotation" )
          $node->removeChild($node->childNodes->item($i));
        $i--;
      }
    }

    return $node;
  }



  /**
   * parse the schema for <import> elements, and save the imported schemas in $this->schemas
   */
  private function import_schemas() {

    /* now get the <import> nodes in a DOMNodeList: */
    $xPath = new DOMXPath($this->dom);
    $nodelist = $xPath -> query('xs:import');

    foreach ($nodelist as $node) {
      if ( $node -> hasAttribute('schemaLocation') ) {
        $namespace = $node->getAttribute('namespace');
        $schemaLocation = $node->getAttribute('schemaLocation');
        $parsed_url = parse_url($schemaLocation);
        $url = basename(parse_url($schemaLocation,PHP_URL_PATH));
        if ( !$this->is_loaded($namespace) ) {
          $new_schema = new query_schema( $url, $this->schemas );
          $this->schemas = $new_schema->get_schemas();
        }
      }
    }
  }


  /**
   * check if the schema is already loaded
   * @returns boolean
   */
  private function is_loaded($namespace) {
    foreach ( $this->schemas as $schema )
      if ( $schema['namespace'] == $namespace )
        return true;
    return false;
  }


  /**
   *
   * @returns string
   */
  private function get_namespace() {
    $namespace = NULL;
    foreach ( $this->dom->documentElement->attributes as $value )
      if ( $value->name == 'targetNamespace' )
        $namespace = $value->value;
    return $namespace;
  }


  /**
   *
   * @returns string
   */
  private function get_prefix() {
    $prefix = NULL;
    $prefix = $this->dom->documentElement->lookupPrefix( $this->get_namespace() );
    return $prefix;
  }


  /**
   * clone a node for appending to DOMDocument $doc
   * @returns mode
   */
  private function cloneNode($node,$doc) {
      $nd=$doc->createElement($node->nodeName);
      foreach ( $node->attributes as $value )
          $nd->setAttribute($value->nodeName,$value->value);
      if ( !$node->childNodes )
          return $nd;
      foreach ( $node->childNodes as $child ) {
          if ( $child->nodeName=="#text" )
              $nd->appendChild($doc->createTextNode($child->nodeValue));
          else
              $nd->appendChild($this->cloneNode($child,$doc));
      }
      return $nd;
  }


}

?>