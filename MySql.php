<?php
 
/**
 * Should be called like this
 * 
 * $mysql = new Mysql('localhost', 'root', null, 'royale_fb_photos');
 */
 
class Mysql {
    
    private $host;
    private $user;
    private $pswd;
    private $database;
 
        function __construct($host = 'localhost', $user = 'root', $pswd = '', $database = 'royale_fb_photos')
        {
                $this->host = $host;
                $this->user = $user;
                $this->pswd = $pswd;
                $this->database = $database;
        }
 
        function connect()
        {
                if(!mysql_connect($this->host, $this->user, $this->pswd, $this->database))
                    throw new Exception('Error connecting to mysql');
                if(!mysql_select_db($this->database))
                    throw new Exception('Error selecting database: ' . $this->database);
        }
 
        // This function is pretty useless, php automatically closes connection at end of life.
        function close()
        {
                //return mysql_close($this->conn);
        }
 
        function query($sql)
        {
                $rs = mysql_query($sql);
                
                if(!$rs)
                    throw new Exception(mysql_error());
                
                return $rs;
        }
 
        function message($type = '', $msg = '')
        {
                echo($type.'<br />'.$msg);
        }
 
        function affectedRows()
        {
                return mysql_affected_rows();
        }
 
        function getResult($rs, $rowno, $field = 0)
        {
                return mysql_result($rs, $rowno, $field);
        }
 
        function getRowsNum($rs)
        {
                $rownum = 0;
                $rownum = mysql_num_rows($rs);
                return $rownum;
        }
 
        // Not sure why you would use this, mysql_fetch_array should work just as good.
        function getRows($rs)
        {
                //$rownum = 0; this is useless, get's overrided by below
                $rownum = mysql_num_rows($rs); // will return false if there is a problem
                if(!$rownum)
                    $rownum = 0;
                
                /**
                 * If there are no rows, $rows will not be defined, and may crash depending on how the server is setup
                 */
                $rows = array();
                
                if($rownum > 0)
                {
                        for($i = 0; $i < $rownum; $i++)
                        {
                                $rows[$i] = mysql_fetch_row($rs);
                        }
                }
                return $rows;
        }
        
        function getFieldsNum($rs)
        {
                return mysql_num_fields($rs);
        }
 
        function getField($rs)
        {
                return mysql_fetch_field($rs);
        }
 
        // This is only useful if the mysql result is a few megas worth of data.
        // These types of garbage collection can actually slow down PHP. It's best to rely on the built in GC for mysql.
        function freeRs($rs)
        {
                return mysql_free_result($rs);
        }
 
        function insertId()
        {
                return mysql_insert_id();
        }
 
        function fetchRow($rs)
        {
                return mysql_fetch_row($rs);
        }
 
        function fetchArray($rs, $type=MYSQL_BOTH)
                                                //MYSQL_ASSOC 1; MYSQL_NUM 2; MYSQL_BOTH 3
        {
                return mysql_fetch_array($rs, $type);
        }
}
 
?>