<?php

/*Class by Mertol Kasanan
*
* The goal is to run parametrized SQL query with automatic query preparation and parameter binding.
*
* For an example:
* $db = new DB();
* $resul = $db->query('SELECT * FROM `users` WHERE id = ? AND user_type = ? LIMIT ?',$id,$user_type,$limit);
* This will return the result set as an array to $result. You don't have to worry about SQL Injection since the query is parametrized; INSERT,UPDATE or DELETE statements are also supported
* $result = $db->num_rows('INSERT INTO users(id,user_type) VALUES (NULL, ?)',$user_type);
* This will return the number of rows inserted; If was SELECT statement it would return number of selected rows;
* $result = $db->insert_id;
* This will return the auto increment value of last query;
* 
* DB() do the job.
* DB_Connect() is Singleton, so only 1 connection is made, no matter how many instances of class DB you have
*
* The basic use of the class:
* (for more details look at examples.txt and manual.txt)
* $db = new DB();
* $db->query('parametrized SQL QUERY',$parameter_1,$parameter_2,...,$parameter_n); if select statement,returns all rows in array , if other, and no error occurred returns true; if there is error returns false
* $db->num_rows; return number of rows selected,deleted,inserted or updated in last statement
* $db->num_rows('parametrized SQL QUERY',$parameter_1,$parameter_2,...,$parameter_n); returns number of rows selected,inserted,deleted or updated in that SQL QUERY
* $db->insert_id; returns last insert id of auto increment column for insert queries
*
*/

//connection settings:
static $db_host = "pack-status-db.kelim2go.com";

static $db_user = "alexansh";
static $db_pswd = "A1exazaz";
static $db_name = "pack_status";
// debugging settings
static $debug_db = false;
static $log_file = "log.xml";

class DB
    {
    public $db;
    public $stmt;
    public $error;
    public $log;

    public function __construct()
        {
        $db_connect=DB_Connect::singleton();
        $this->db  =$db_connect->db;
        }

    function __destruct()
        {
        /*destruct and log*/
        global $debug_db;
        $this->stmt->close();

        if ($debug_db)
            {
            $this->db_log($this->log);
            }
        }

    function __call($func, $arg_array)
        {
        switch ($func)
            {
            case 'num_rows':
                if ($arg_array != NULL)
                    {
                    $this->execute_query($arg_array);
                    $num_rows=$this->execute_result_info();

                    return $num_rows['num_rows'];
                    }

                $result=$this->execute_num_rows();
                return $result;
                break;
            case 'insert_id':
                if ($arg_array != NULL)
                    {
                    $this->execute_query($arg_array);
                    $num_rows=$this->execute_result_info();

                    return $num_rows['insert_id'];
                    }

                $result=$this->execute_num_rows();
                return $result;
                break;
            case 'query':
                $this->execute_query($arg_array);

                $result=$this->execute_result_array();
                return $result;
                break;
            }
        }

    function __get($v)
        {
        $num_rows=$this->execute_result_info();
        return $num_rows[$v];
        }

    private function execute_query($arg_array = NULL)
        {
        //determine the types of arguments

        $sql_query=array_shift($arg_array); // the first element is returned to sql_query and then removed

        foreach ($arg_array as $v)
            {
            switch ($v)
                {
                case is_string($v):
                    $types.='s';

                    break;

                case is_int($v):
                    $types.='i';

                    break;

                case is_double($v):
                    $types.='d';

                    break;
                }
            }

        // prepare the query
        print mysqli_connect_error();
        $this->stmt=$this->db->prepare($sql_query);

        // binding parameters if has any
        try
            {
            if (isset($arg_array[0]))
                {
                array_unshift($arg_array, $types);
                $bind=@call_user_func_array(array
                    (
                    $this->stmt,
                    'bind_param'
                    ),                      $arg_array);
                }

            if ($bind)
                {
                $time_start=microtime(true);
                $this->stmt->execute();
                $this->stmt->store_result();
                $time_end=microtime(true);
                $time    =$time_end - $time_start;

                $this->log[]=array
                    (
                    "query" => $sql_query,
                    "time"  => $time
                    );
                }
            else
                {
                throw new Exception('Binding error:' . $this->db->error);
                }
            }
        catch( Exception $e )
            {
            print $e->getMessage();
            }
        }

    private function execute_result_info()
        {
        if ($this->stmt->affected_rows > 0)
            {
            $num_rows=$this->stmt->affected_rows;
            }
        else
            {
            $num_rows=$this->stmt->num_rows();
            }

        $result['num_rows'] =$num_rows;
        $result['insert_id']=$this->stmt->insert_id;

        return $result;
        }

    private function execute_result_array()
        {
        try
            {
            if ($this->stmt->error)
                {
                throw new Exception('MySQLi STMT error:' . $this->stmt->error);
                }

            $result_metadata=@$this->stmt->result_metadata();
            }
        catch( Exception $e )
            {
            print $e->getMessage();
            }

        if (is_object($result_metadata))
            {
            $result_fields=array();

            while ($field=$result_metadata->fetch_field())
                {
                array_unshift($result_fields, $field->name);
                $params[]=&$row[$field->name];
                }

            call_user_func_array(array
                (
                $this->stmt,
                'bind_result'
                ),               $params);

            while ($this->stmt->fetch())
                {
                foreach ($row as $key => $val)
                    {
                    $c[$key]=$val;
                    }

                $result[]=$c;
                }

            return $result;
            }
        elseif ($this->stmt->errno == 0)
            {
            return true;
            }
        else
            {
            return $this->stmt->errno;
            }
        }

    /*here is the function for logging*/
    function db_log($query_arr)
        {
        global $log_file;
        $fh=fopen($log_file, 'a') or die("can't open the log file");
        $i =0;

        foreach ($query_arr as $k => $q)
            {
            $sql = $sql . "<query_" . $i . "><sql><![CDATA[ " . $q['query'] . "]]></sql><time>" . $q['time']
                . "</time></query_" . $i . ">\n";

            $i++;
            }

        $xml.="<pagewiev>\n";
        $xml.="<date><![CDATA[ " . date("F j, Y, g:i a") . "]]></date>\n";
        $xml.="<ip><![CDATA[ " . $_SERVER['REMOTE_ADDR'] . "]]></ip>\n";
        $xml.="<browser><![CDATA[ " . $_SERVER['HTTP_USER_AGENT'] . "]]></browser>\n";
        $xml.="<url><![CDATA[ " . $_SERVER['REQUEST_URI'] . "]]></url>\n";
        $xml.="<ref><![CDATA[ " . $_SERVER['HTTP_REFERER'] . "]]></ref>\n";
        $xml.="<memory><![CDATA[ " . memory_get_peak_usage() . "]]></memory>\n";
        $xml.="<sql>" . $sql . "</sql>";
        $xml.="</pagewiev>\n";

        fwrite($fh, $xml);
        fclose($fh);
        }
    }
?>