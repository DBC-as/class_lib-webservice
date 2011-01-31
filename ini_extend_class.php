<?php
/**
extends inifile_class. if [import] section is set an array of inifiles is initialized.
inifiles are fetched via openfile webservice.

sample import section in openscan.ini:
[import]
adhl["1.3"][]=adhl

sample usage:
require_once("ini_extend_class.php");

$hest = new ini_extend("openscan.ini");

$hest->dump("adhl"); // print_r imported ini_file 
$hest->get_section("setup","adhl"); // get section from imported ini_file
$hest->get_value("setup","version","adhl"); // get value from imported ini_file

$hest->dump(); //print_r self
$hest->get_section("setup"); // get section from self
$hest->get_value("setup","version"); // get value from self

**/


require_once("inifile_class.php");
require_once("curl_class.php");
require_once("memcache_class.php");

class ini_extend extends inifile
{
  private $use_cache;
  private $cache;
  private $ini_files = array();
  
  private $cache_name;
  private $mother;
  public $error;

  const ws_file="http://metode.dbc.dk/~pjo/OpenLibrary/OpenFile/trunk/server.php/?";

  public function __construct($inifile, $use_cache=false)
  {
    parent::__construct($inifile);
    
    // hold the original ini-file
    $this->ini_files[trim($inifile)] = parent::_clone();
 
    if( $use_cache )
      {
	$this->cache=new cache($this->get_value("cache_host", "setup"),
			       $this->get_value("cache_port", "setup"),
			       $this->get_value("cache_expire", "setup"));

      }
    else // an empty object is needed.
      $this->cache=new cache(" ");
    
    // print_r($this->ini_files);
    //exit;
    libxml_use_internal_errors(true);
    $this->import(); 
  }

  //mini wrappers for memcache_class. 
  private function cache_get()
  {
    return $this->cache->get($this->cache_key());
  }

  private function cache_set($value)
  {
    return $this->cache->set($this->cache_key(),$value);
  }

   // cache key for this class
  private function cache_key()
  {
    $key = "ini_";
    $key .= "_";
    $key .= $this->cache_name;

    return $key;
  }

  /***** overwritten methods; get_section & get_value from parent_class (inifile_class) *********/

  // TODO error-check

  public function get_section( $section,$inifile=NULL ) 
  {
    // TODO fixme. This is not a good solution.    
    if( !$inifile )
      {
	//look in first ini-file 
	reset($this->ini_files);
	$inifile = key($this->ini_files);
      }
    
    // look in mother ini-file
    if( !$ret =$this->ini_files[$inifile]->get_section($section) )
      if( $this->mother )
	$ret = $this->ini_files[$this->mother]->get_section($section); 

    return $ret;
  }

  public function get_value( $value, $section, $inifile=NULL )
  {
    // TODO fixme. This is not a good solution.
    if( !$inifile )
      {
	//look in first ini-file 
	reset($this->ini_files);
	$inifile = key($this->ini_files);
      }

    // look in mother ini-file
    if( !$ret = $this->ini_files[$inifile]->get_value($value,$section) )
      if( $this->mother )
	$ret = $this->ini_files[$this->mother]->get_value($value,$section);

    return $ret;
  }


  /*********** end overwritten methods **********/


  public function dump($inifile=NULL)
  {
    if( !$inifile )
      {	
	//look in first ini-file 
	reset($this->ini_files);
	$inifile = key($this->ini_files);
      }

    // look in mother ini-file
    if( !$ret = $this->ini_files[$inifile]->get() )
      if( $this->mother )
	$ret = $this->ini_files[$this->mother]->get();

    print_r($ret);
  }
  
  
  private function import()
  {
    $import =  $this->get_section("import");

    if( isset($import) )
      foreach( $import as $key=>$imp )
	{	
	  // if mother ini-file is not yet set; set it as the first imported ini-file
	  if( !$this->mother )
	    $this->mother = $key;

	  // set cache_name for get and set
	  $this->cache_name = $key.key($imp);
	  if( !$file = $this->cache_get() )
	    {
	      $file = $this->get_xml($key,key($imp));

	      // error check
	      if( $this->error )
		return;

	      $this->cache_set($file); 
	    }
	  
	  $ini = new inifile($file);

	  // Error check
	  if( $ini->error )
	    $this->error = $ini->error;

	  $this->ini_files[$imp[key($imp)][0]]=$ini;
	}
  }

  /**
   * get file via cache or openfile webservice
   **/
  private function get_xml($filename,$version,$filepath=null)
  {
    $url = self::ws_file."action=getFile&fileName=$filename&version=$version&fileType=ini&filePath=files/";
  
    $curl = new curl();
    $curl->set_url($url);
    $xml = $curl->get();
    
    $this->check_curl($curl);
    //error check
    if( $this->error )
      return;

    $ret = $this->file_contents($xml);
    return $ret;       
  }

  private function file_contents($xml)
  {
    $dom = new DOMDocument();
    $dom->loadXML($xml);

    $this->check_lib_xml();

    // error check
    if( $this->error )
      return;
    
    $xpath = new DOMXPath($dom);
    $this->check_file_response($xpath); 

    // error check
    if( $this->error )
      return;
      
    $query = "//types:content";
    $nodelist = $xpath->query($query);
    return $nodelist->item(0)->nodeValue;
  }

  private function check_lib_xml()
  {
    // error check
    if ($errors = libxml_get_errors())
      {
	foreach ($errors as $error) 
	  $this->error .= "lib_xml: ".$error->message;
	
	libxml_clear_errors();
      }    
  }

  private function check_curl($curl)
  {
    $status = $curl->get_status();
    if( $status['http_code'] != 200 )
      $this->error = "http_code: ".$status['http_code']."  from url :".$status['url']."\n";
  }

  private function check_file_response($xpath)
  {
     $query = "//error";
     $nodes = $xpath->query($query);
     if( $nodes->length > 0 )
       $this->error = $nodes->item(0)->nodeValue;
  }
}
?>
