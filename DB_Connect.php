<?php
/*
*DB_Connect() is SINGLETON. Look at DB()::__constructor() to see the usage.   
*/

class DB_Connect
    {
    private static $instance;
    public $db;

    public static function singleton()
        {
        if (!isset(self::$instance))
            {
            $c             =__CLASS__;
            self::$instance=new $c;
            }

        return self::$instance;
        }

    public function __clone() { trigger_error('Cloning not allowed.', E_USER_ERROR); }

    private function __construct()
        {
        global $db_host, $db_user, $db_pswd, $db_name;

        try
            {
            @$this->db=new mysqli($db_host, $db_user, $db_pswd, $db_name);

            if (mysqli_connect_error())
                {
                throw new Exception('Database error:' . mysqli_connect_error());
                }
            }
        catch( Exception $e )
            {
            print $e->getMessage();
            }

        $this->db->set_charset('utf8');
        }

    function __destruct() { $this->db->close(); }
    }
?>