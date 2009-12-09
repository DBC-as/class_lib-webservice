<?php

/**
   
   thin wrapper for memcache class.

*/

define( HOST,"localhost" );
define( PORT,"11211" );
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
	self::$_memcache->connect(HOST,PORT);
      }
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
  private $file;
  private $content;

  public function __construct()
  {
    $size=filesize(CACHEFILE);
    echo $size;
    exit;
    $this->file = fopen(CACHEFILE,"w+");
    $data = fread($this->file,$size) )
    $this->content=unserialize($data);
  }
  
  public function hit()
  {
    if(! $data=$this->content )
      $data=array("hits"=>0,"miss"=>0);

    $data['hits']++;
 
    fwrite($this->file,serialize($data));
  }

  public function miss()
  {
    if(! $data=$this->content )
      $data=array("hits"=>0,"miss"=>0);

    $data['miss']++;
 
    fwrite($this->file,serialize($data));
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