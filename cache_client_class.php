<?php

/**
   
   wrapper for memcache class.

*/

define( HOST,"localhost" );
define( PORT,"11211" );

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
    return self::client()->get("mykey");
  }

  public static function set($key,$data)
  {   
    self::client()->set($key, $data, false, 600);
  }
}


?>