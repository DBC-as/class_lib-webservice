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

  const ws_file="http://vision.dbc.dk/~pjo/OpenLibrary/OpenFile/trunk/server.php/?";

  public function __construct($inifile, $use_cache=true)
  {
    //$this->ini_files[$inifile] = new inifile($inifile);
    parent::__construct($inifile);
    
    $this->ini_files[$inifile] = parent::_clone();
   
    if( $use_cache )
      {
	$this->cache=new cache($this->get_value("cache_host", "setup"),
			       $this->get_value("cache_port", "setup"),
			       $this->get_value("cache_expire", "setup"));

      }
    else
      $this->cache=null;
    
    $this->import();
       //  foreach( $this->ini_files as $key=>$val )
	     //      echo $key."\n";
       // print_r($this->ini_files);

  }

  private function cache_get()
  {
    if( $ret = $this->cache->get( $this->cache_key()) )
      return $ret;

    return false;
  }

  private function cache_set($value)
  {
    if( $this->cache->set($this->cache_key(),$value) )
      return true;

    return false;	
  }

  /***** overwritten methods from parent_class (inifile_class) *********/

  // TODO error-check

  public function get_section( $section,$inifile=NULL ) 
  {
 
    if( !$inifile )
      {
	reset($this->ini_files);
	$inifile = key($this->ini_files);
      }

    return $this->ini_files[$inifile]->get_section($section,$inifile);
  }

  public function get_value( $section, $value, $inifile=NULL )
  {
    if( !$inifile )
      {
	reset($this->ini_files);
	$inifile = key($this->ini_files);
      }
  
    return $this->ini_files[$inifile]->get_value($section,$value);
  }


  /*********** end overwritten methods **********/


  public function dump($inifile=NULL)
  {
    if( !$inifile )
      {
	 reset($this->ini_files);
	 $inifile =  key($this->ini_files);
      }

     print_r($this->ini_files[$inifile]->get());
  }
  /**
     get or set the use_cache variable
   */
  public function use_cache($set = null)
  {
    if( !$set )
      return $this->use_cache;
    else
      $this->use_cache = $set;    
  }
  
  public function import()
  {
    // TODO error-check
    $import =  $this->get_section("import");
    /*foreach( $import as $key=>$imp )
	{
     $file = $this->get_xml($key,key($imp));
     $ini = new inifile($file);
     $this->ini_files[$imp[key($imp)][0]]=$ini;
     }*/
     if( isset($import) )
      foreach( $import as $key=>$imp )
	{	
	  $this->cache_name = $key.key($imp);
	  if( !$file = $this->cache_get() )
	    {
	      $file = $this->get_xml($key,key($imp));
	      $this->cache_set($file); 
	    }
	  else
	    die("YES");
	  
	  $ini = new inifile($file);
	  $this->ini_files[$imp[key($imp)][0]]=$ini;
	  }
  }

  private function r_implode( $glue, $pieces )
  {
    foreach( $pieces as $r_pieces )
      {
	if( is_array( $r_pieces ) )
	  {
	    $retVal[] = $this->r_implode( $glue, $r_pieces );
	  }
	else
	  {
	    $retVal[] = $r_pieces;
	  }
      }
    return implode( $glue, $retVal );
  } 

  // cache key for this class
  private function cache_key($inifile=NULL)
  {
    $key = "ini_";

    $key .= $this->get_value("wsdl","setup",$inifile);
    $key .= "_";
    $key .= $this->get_value("version","setup",$inifile); 

    return $key;
  }

  private function cache()
  {
    // TODO implement
  }

  /**
   * get file via cache or openfile webservice
   **/
  private function get_xml($filename,$version,$filepath=null)
  {
    // TODO error-check

    $url = self::ws_file."action=getFile&fileName=$filename&version=$version&fileType=ini&filePath=files/";
  
    $curl = new curl();
    $curl->set_url($url);

    $xml = $curl->get();
    
    $ret = $this->file_contents($xml);
    
    return $ret;       
  }

  private function file_contents($xml)
  {
    // TODO error-check
    //   echo $xml;
    //exit;
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $query = "//types:content";
    $nodelist = $xpath->query($query);
    
    return $nodelist->item(0)->nodeValue;
  }
}
?>