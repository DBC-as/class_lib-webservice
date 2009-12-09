<?php

/**
   
   thin wrapper for memcache class.

*/

define( HOST,"localhostyyyy" );
define( PORT,"11211-11" );
define( LOGPATH,"/tmp/nypunkt_pjo/");
define( CACHEFILE,"cache_log.log" );

class cache
{
  
  private static $_memcache=null;
  // private constructor to avoid abuse( new() ) of class
  private function __construct(){}
  
  private static function client()
  {
    if( self::$_memcache==null )
      {
	self::$_memcache=new Memcache();
	@self::$_memcache->connect(HOST,PORT);
      }

    // var_dump(self::$_memcache);
    //exit;
    
return self::$_memcache;

    
  }

  public static function get($key)
  {
    return self::client()->get($key);
  }

  public static function set($key,$data)
  {   
    self::client()->set($key, $data, false, 600);
  }

  /** mark all items in cache as expired*/
  public static function flush()
  {
    return self::client()->flush();
  } 
}

class cache_log
{
  private $content;
  private $filepath;

  public function __construct($servicename)
  {
    $this->filepath=LOGPATH.$servicename."_".CACHEFILE;

    if( file_exists($this->filepath) )
      {
	$size=filesize($this->filepath);
	$file = fopen($this->filepath,"r");
	$data = fread($file,$size); 
	$this->content=unserialize($data);
	fclose($file);
      }
  }
  
  public function hit()
  {
    if(! $data=$this->content )
      $data=array("hits"=>0,"miss"=>0);

    $data['hits']++; 
    $this->write($data);
  }

  public function miss()
  {
    if(! $data=$this->content )
      $data=array("hits"=>0,"miss"=>0);

    $data['miss']++;    
    $this->write($data);
  }

  private function write($data)
  {
    $file = fopen($this->filepath,"w");
    fwrite($file,serialize($data));
    fclose($file);
  }
  
  public function hitratio()
  {
    $data=$this->content;
    return $data['hits']."/".count($data);
  }

  public function read()
  {
    print_r($this->content);
  }
}


?>