<?php
class Database {
    private $handle = null;
    private $statement = null;
    private $connected = false;
    // make your own private function for logging exceptions
    public function __construct() {
        try {
            $config = @parse_ini_file('config.ini', true);
            // you can also have hardcoded settings (not recommended)
            if ($config) {
                $dsn = "{$config['database']['driver']}:host={$config['database']['host']};port={$config['database']['port']};dbname={$config['database']['dbname']};charset={$config['database']['charset']}";
                $options = array(
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => true,
                    PDO::ATTR_PERSISTENT => false
                );
                $this->handle = new PDO($dsn, $config['database']['user'], $config['database']['password'], $options);
                $this->connected = true;
            } else {
                throw new PDOException('Configuration file exception');
            }
        }
        catch(PDOException $e) {
            // log an exception
        }
    }
    public function query($query) {
        try {
            return $this->statement = $this->handle->prepare($query);
        }
        catch(PDOException $e) {
            // log an exception
            return false;
        }
    }
    public function bind($parameter, $value, $type = null) {
        try {
            if (is_null($type)) {
                if (is_null($value)) {
                    $type = PDO::PARAM_NULL;
                } else if (is_bool($value)) {
                    $type = PDO::PARAM_BOOL;
                } else if (is_int($value)) {
                    $type = PDO::PARAM_INT;
                } else {
                    $type = PDO::PARAM_STR;
                }
            }
            return $this->statement->bindValue($parameter, $value, $type);
        }
        catch(PDOException $e) {
            // log an exception
            return false;
        }
    }
    public function execute() {
        try {
            return $this->statement->execute();
        }
        catch(PDOException $e) {
            // log an exception
            return false;
        }
    }
    public function fetch() {
        try {
            return $this->statement->fetch();
        }
        catch(PDOException $e) {
            // log an exception
            return false;
        }
    }
    public function fetchAll() {
        try {
            return $this->statement->fetchAll();
        }
        catch(PDOException $e) {
            // log an exception
            return false;
        }
    }
    public function rowCount() {
        try {
            return $this->statement->rowCount();
        }
        catch(PDOException $e) {
            // log an exception
            return false;
        }
    }
    public function isConnected() {
        return $this->connected;
    }
    public function disconnect() {
        $this->connected = false;
        $this->statement = null;
        $this->handle = null;
    }
}
?>
