<?php
interface IException
{
  /* Protected (final) methods from Exception class */
  public function getMessage();                   // Exception message
  public function getCode();                              // User-defined Exception code
  public function getFile();                              // Source filename
  public function getLine();                              // Source line
  public function getTrace();                             // An array of the backtrace()
  public function getTraceAsString();             // Formated string of tracei
  
  /* Overrideable (public) methods from Exception class */
  public function __toString();                   // formated string for display
  public function __construct($message = null, $code = 0);
  
  /* Custom methods */
  public function log($filename);
}

/** \brief
    implements IException-interface mostly by extending Exception.
*/
class fetException extends Exception implements IException
{
  /* constructor can be used to initialize custom objects(like logging)*/
  public function __construct($message=null, $code=0)
  {
    parent::__construct($message, $code);
  }
  
  /* __toString-method can be used for custom messages */
  public function __toString()
  {
    return parent::__toString()."\nTESTHEST";
  }
  
  public function log($filename)
  {}
}


/** \brief
Database interface. Common methods that a database-class MUST implement.
*/
interface IDatabase
{
  /* methods to be implemented in extending classes (database-specific methods)*/
  public function execute();
  public function get_row();
  public function commit(); 
  public function rollback();
  public function open();
  public function close();

  /*methods implemented in base class (general methods)*/
  public function insert($table,$record);
  public function update($table,$assign,$clause=null);
  public function delete($table,$clause=null);
  public function set_query($query);
  public function set_pagination($offset,$limit);
  public function set_transaction($bool);
  public function bind($name,$value,$maxlength=-1,$type=SQLT_CHR);
}

/** \brief
Abstract database-class. The basic methods for the databases we wish to use.
*/
abstract class fet_database implements IDatabase
{
  protected $username;
  protected $password;
  protected $database;
  protected $host;
  protected $port;
  protected $query;
  protected $connection;
  protected $offset;
  protected $limit;
  protected $bind_list;
  protected $transaction; //bool

  public function __construct($username,$password,$database,$host=null,$port=null)
  {
    $this->username=$username;
    $this->password=$password;
    $this->database=$database;
    $this->host=$host;
    $this->port=$port;
  }  


  /** \brief
      set the query.
   */
  public function set_query($query)
  {
    $this->query=$query;
  }

  /** \brief
      generate sql for inserting a row. call extending class's execute()-method
      
      $table; the table to insert into
      $record; an array or a comma-seperated list of values

      return nothing. throw fetException if execute fails.
   */
  public function insert($table,$record)
  {
    $sql="";
    $fields="";
    $values="";
    if( is_array($record) )
      {
	foreach( $record as $key=>$val )
	  {
	    if( strlen($fields) )
	      $fields.=",";
	    $fields.=$key;
	     
	    if( strlen($values) )
	      $values.=",";
	    $values.="'".$val."'";
	  }	    
	$sql="INSERT INTO ".$table."(".$fields.") VALUES(".$values.")";
      }
    else
      $sql="INSERT INTO ".$table." VALUES(".$record.")";
      

    $this->query=$sql;
    $this->execute();
  }
  
   /** \brief
      generate sql for updating a row. call extending class's execute()-method
      
      $table; the table to update
      $record; an array or a comma-seperated list of key=value
      $clause; an array of clauses (should always be set) 
      // TODO should method abort if clause is not set

      return nothing. throw fetException if execute fails.
   */
  public function update($table,$assign,$clause=null)
  {
    $sql = "";
    $where = "";
    
    if ( is_array($assign) ) 
      {
	foreach ( $assign as $key => $value ) 
	  {
	    if ( strlen($sql) ) 
	      $sql.=", ";
	    
	    $sql.=$key."='".$value."'";
	  }
      }  
    elseif( strlen($assign) )
	$sql=$assign;
    else
      throw new fetExcetption("no update sql");
    
    $sql = "UPDATE ".$table." SET ".$sql;
    
    if ( is_array($clause) ) 
      $where = $this->make_clause($clause);

    elseif ( $clause ) 
      $where = $clause;

    if ( strlen( $where ) ) 
      $sql .= " WHERE ".$where;
    
    $this->query=$sql;    
    $this->execute();
  }
  
  /** \brief
      generate sql for delete in a table. call extending class's execute()-method

      $table; the table to delete in.
      $clause; an array of clauses (should always be set) 
      // TODO should method abort if clause is not set

      return nothing. throw fetException if execute fails.
   */
  public function delete($table,$clause=null)
  {
    $sql="";
    $where="";
    if( is_array($clause) )
      $where=$this->make_clause($clause);
    elseif( $clause )
      $where=$clause;
    
    $sql="DELETE FROM ".$table." WHERE ".$where;
    $this->query=$sql;
    $this->execute();
  }

  /** \brief
      set pagination. - is only useable for selects
      // TODO check if query is select - abort method if not

      $offset; where should we start
      $limit; how many rows in result     
   */
  public function set_pagination($offset,$limit)
  {
    $this->offset=$start;
    $this->limit=$end;
  }

  /** \brief
     if transaction is set commit and rollback methods can be used.
     
     $bool; transaction=$bool
   */
  public function set_transaction($bool)
  {
    $this->transaction=$bool;
  }

  /* DEVELOPER NOTE */
  // mysql and postgres uses placeholders for preparing a sql.
  // oracle uses bind_by_name

  /**
     bind a variable.

     $name; name of variable to bind
     $value; value of variable.
   */
  public function bind($name,$value,$maxlength=-1,$type=SQLT_CHR)
  {
    $bind_array["name"]=($name[0] == ":"? $name : ":".$name);
    $bind_array["value"]=$value;
    $bind_array["maxlength"]=$maxlength;
    $bind_array["type"]=$type;
    $this->bind_list[]=$bind_array;    
  }

  /**
     \brief make sql AND clause from given array
   */
  private function make_clause($clause)
  {
    $sql = "";    
    foreach ( $clause as $key => $value ) 
      {
	if ( strlen($sql) ) 
	  $sql.=" AND ";
	
	$sql.= $key."='".$value."'";
      }
    return $sql;
  }
}


/** \brief 
    Class handles transactions for a postgres database;

    sample usage:

    $db=new pg_database("host=visoke port=5432 dbname=kvtestbase user=fvs password=fvs");
   
    INSERT
    1. with sql
      $db->set_query("INSERT INTO stats_opensearch VALUES('"2010-01-01','xxxx','12.2');
      $db->open();
      $db->execute();
      $db->close();
    2. with array
      $tablename="stats_opensearch";
      $record=array("time"=>"2010-01-01 00:00:00","timeid"=>"xxxx","seconds"=>"12.2");
      $db->open();
      $db->insert($tablename,$record);
      $db->close();

    UPDATE
    1. with sql
      $db->set_query("UPDATE stats_opensearch SET seconds=25,time=2009 WHERE timeid='xxxx');
      $db->open();
      $db->execute();
      $db->close();
    2. with array(s)
      $tablename="stats_opensearch";
      $assign=array("time"=>"2009",seconds"=>"25");
      $clause=array("timeid"=>"xxxx");
      $db->open();
      $db->update($tablename,$assign,$clause);
      $db->close();

    DELETE
    1. with sql
      $db->set_query("DELETE FROM stats_opensearch WHERE timeid='xxxx' AND seconds='12.2'");
      $db->open();
      $db->execute();
      $db->close();
    2. with array
      $clause=array("timeid"=>"xxxx","seconds"=>"12.2");
      $db->open();
      $db->delete("stats_opensearch",$clause);
      $db->close();
    
 */


/** DEVELOPER NOTES 
	postgres-database class
	TO REMEMBER
	// to escape characters
	string pg_escape_string([resource $connection], string $data)

	// for blobs, clobs etc (large objects).
	pg_query($database, "START TRANSACTION");
	$oid = pg_lo_create($database);
	$handle = pg_lo_open($database, $oid, "w");
	pg_lo_write($handle, "large object data");
	pg_lo_close($handle);
	pg_query($database, "commit");

	// for error recovering
	bool pg_connection_reset(resource $connection)
*/
class pg_database extends fet_database
{
  public function __construct($connectionstring)
  {
    $part=explode(" ",$connectionstring);
    foreach( $part as $key=>$val )
      {
	$pair=explode('=',$val);
	$cred[$pair[0]]=$pair[1];
      }
    
    parent::__construct($cred["user"],$cred["password"],$cred["dbname"],$cred["host"],$cred["port"]);
  } 

  private function set_large_object()
  {
    // TODO implement
  }

  private function connectionstring()
  {
    $ret="host=".$this->host." port=".$this->port." dbname=".$this->database." user=".$this->username." password=".$this->password;
    return $ret;
  }

  public function open()
  {
    if( ($this->connection=@pg_pconnect($this->connectionstring()))===false )
	throw new fetException("no connection");
  }
  
  /**
     wrapper for private function __execute
   */
  public function execute()
  {
    try{$this->__execute();}
    catch(Exception $e)
      {	die( $e->__toString() );}
  }
  
  private function __execute()
  {
    // set pagination
    if( $this->start && $this->end )
      $this->query.=' LIMIT '.$this->limit.' OFFSET '.$this->offset;	
    
    // use transaction if set
    if( $this->transaction )
      pg_query($this->connection,"START TRANSACTION");

    // check for bind-variables
    if( !empty($this->bind_list) )
      {
	if( @pg_prepare($this->connection,"my_query",$this->query)===false)
	  {
	    $message=pg_last_error();
	    if( $this->transaction )
	      @pg_query($this->connection,"ROLLBACK");
	    throw new fetException($message);
	  }
	$bind=array();
	
	foreach( $this->bind_list as $binds)
	  array_push($bind,$binds["value"]);
      
	if( ($this->result=@pg_execute($this->connection,"my_query",$bind))===false)
	  {
	    $message=pg_last_error();
	    if( $this->transaction )
	      @pg_query($this->connection,"ROLLBACK");
	    throw new fetException($message);
	  }
      }
    else  
      // if no bind-variables - just query
      if ( ($this->result= @pg_query($this->connection,$this->query)) === false )
	{
	  $message=pg_last_error();
	  if( $this->transaction )
	    pg_query($this->connection,"ROLLBACK");
	  throw new fetException($message);
	}
    if( $this->transaction )
      pg_query($this->connection,"COMMIT");
  }

  public function get_row()
  {
    return pg_fetch_assoc($this->result);
  }

  public function commit()
  {
    pg_query($this->connection,"COMMIT");   
    // postgres has autocommit enabled by default
    // use only if TRANSACTIONS are used
  }

  public function rollback()
  {    
     pg_query($this->connection,"ROLLBACK");
    // use only if TRANSACTIONS are used
  } 

  public function close()
  {  
    if( $this->connection )
      pg_close($this->connection);
  }

  public function __destruct()
  {    
  }
 
}

?>
