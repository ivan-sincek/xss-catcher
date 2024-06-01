<?php
class Query extends Database {
    // returns false on failure
    // returns empty array, one-dimensional associative array, or two-dimensional associative array (default) on success
    public static function select($query, $parameters = array(), $type = 'all') {
        $results = false;
        $db = new Database();
        if ($db->isConnected()) {
            $db->query($query);
            foreach ($parameters as $key => $value) {
                $db->bind(":{$key}", $value);
            }
            if ($db->execute()) {
                $count = $db->rowCount();
                if ($count === 0) {
                    $results = array();
                } else if ($count > 0 && $type === 'single') {
                    $results = $db->fetch();
                } else if ($count > 0 && $type === 'all') {
                    $results = $db->fetchAll();
                }
            }
        }
        $db->disconnect();
        return $results;
    }
    // returns false on failure
    // returns zero or grater on success
    public static function count($query, $parameters = array()) {
        $results = false;
        $db = new Database();
        if ($db->isConnected()) {
            $db->query($query);
            foreach ($parameters as $key => $value) {
                $db->bind(":{$key}", $value);
            }
            if ($db->execute()) {
                $results = $db->rowCount();
            }
        }
        $db->disconnect();
        return $results;
    }
    public static function insert($query, $parameters = array()) {
        return self::count($query, $parameters);
    }
    public static function update($query, $parameters = array()) {
        return self::count($query, $parameters);
    }
    public static function delete($query, $parameters = array()) {
        return self::count($query, $parameters);
    }
}
?>
