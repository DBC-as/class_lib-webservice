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
    $this->file = fopen(CACHEFILE,"w+");
    
    if( $this->file )
      if( $data = fread($this->file,filesize($this->file) ) )
	$this->content=unserialize($data);
         
  }
  
  public function hit()
  {
    if(! $data=$this->content )
      $data=array("hits"=>0,"miss"=>0);

    print_r($data);

    fwrite($this->file,serialize($data));
  }

  public function miss()
  {
  }
  
  public function hitratio()
  {
  }

  public function read()
  {
  }
}


?>