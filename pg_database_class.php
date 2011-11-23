<?php
    require_once("IDatabase_class.php");
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

        private $query_name;

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
            if($this->host)
                $ret.="host=".$this->host;
            if( $this->port )
                $ret.=" port=".$this->port;
            if( $this->database )
                $ret.=" dbname=".$this->database;
            if( $this->username )
                $ret.=" user=".$this->username;
            if( $this->password )
                $ret.=" password=".$this->password;

            // set connection timeout
            $ret.=" connect_timeout=5";
            return $ret;
        }

        public function open()
        {
            if( ($this->connection=@pg_pconnect($this->connectionstring()))===false )
                throw new fetException("no connection");
        }

        /**
        wrapper for private function _execute
        */
        public function execute()
        {
            // set pagination
            if( $this->offset>-1 && $this->limit )
                $this->query.=' LIMIT '.$this->limit.' OFFSET '.$this->offset;	

            try{$this->_execute();}
            catch(Exception $e)
            {	
                throw new fetException($e->__toString());
            }
        }

        private function _queryname()
        {
            return "testhest";
            //echo $this->query;
            $name = str_replace(' ','_',$this->query);
            $name = str_replace(',','_',$name);
            $name = str_replace('(','_',$name);
            $name = str_replace(')','_',$name);
            return $name;	
        }

        private function _execute($statement_key=null)
        {


            // use transaction if set
            if( $this->transaction )
                @pg_query($this->connection,"START TRANSACTION");

            // check for bind-variables
            if( !empty($this->bind_list) )
            {

                $this->query_name = $this->_queryname();
                //if( @pg_prepare($this->connection,"my_query",$this->query)===false)
                // use sql as statement-name
                if( @pg_prepare($this->connection,$this->query_name,$this->query)===false)
                {
                    $message=pg_last_error();
                    if( $this->transaction )
                        @pg_query($this->connection,"ROLLBACK");
                    @pg_query($this->connection,"DEALLOCATE ".$this->query_name);

                    throw new fetException($message);
                }
                $bind=array();

                foreach( $this->bind_list as $binds)
                    array_push($bind,$binds["value"]);

                if( ($this->result=@pg_execute($this->connection,$this->query_name,$bind))===false)
                {
                    $message=pg_last_error();
                    if( $this->transaction )
                        @pg_query($this->connection,"ROLLBACK");
                    @pg_query($this->connection,"DEALLOCATE ".$this->query_name);
                    throw new fetException($message);
                }
            }
            else  
                // if no bind-variables - just query
                if ( ($this->result= @pg_query($this->connection,$this->query)) === false )
                {
                    $message=pg_last_error();
                    if( $this->transaction )
                        @pg_query($this->connection,"ROLLBACK");
                    throw new fetException($message);
            }
            if( $this->transaction )
                @pg_query($this->connection,"COMMIT");
        }

        public function query_params($query="",$params=array())
        {
            if( ($this->result=@pg_query_params($this->connection,$query,$params))===false)
            {
                $message=pg_last_error();
                if( $this->transaction )
                    @pg_query($this->connection,"ROLLBACK");
                throw new fetException($message);
            }
        }


        public function get_row()
        {
            return pg_fetch_assoc($this->result);
        }

        public function commit()
        {
            if( $this->transaction )
                pg_query($this->connection,"COMMIT");   
            // postgres has autocommit enabled by default
            // use only if TRANSACTIONS are used
        }

        public function rollback()
        {    
            if( $this->transaction )
                pg_query($this->connection,"ROLLBACK");
            // use only if TRANSACTIONS are used
        } 

        public function close()
        {  
            @pg_query($this->connection,"DEALLOCATE ".$this->query_name);
            if( $this->connection )
                pg_close($this->connection);

        }

        public function fetch($sql,$arr="")
        {
            if ( $arr ) 
                $this->query_params($sql,$arr);
            else
                $this->exe($sql);

            $data_array = pg_fetch_all($this->result);
            return $data_array;
        }

        public function exe($sql) {
            if  ( ! $this->result = @pg_query($this->connection,$sql)) {
                $message = pg_last_error();
                throw new fetException("sql failed:$message \n $sql\n");
            }
        }

        public function __destruct()
        {    
        }

    }

?>
