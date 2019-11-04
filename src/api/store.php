<?php
include_once './php/database.class.php';
header('Content-Type: application/json; charset=UTF-8');
$parameters = array();
$proceed = false;
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (isset($_POST['site']) && isset($_POST['data'])) {
        $parameters['data'] = trim($_POST['data']);
        $parameters['site'] = trim($_POST['site']);
        // optional
        if (isset($_POST['info'])) {
            $parameters['info'] = trim($_POST['info']);
        }
        $proceed = true;
    }
} else if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
    if (isset($_GET['site']) && isset($_GET['data'])) {
        $parameters['data'] = trim($_GET['data']);
        $parameters['site'] = trim($_GET['site']);
        // optional
        if (isset($_GET['info'])) {
            $parameters['info'] = trim($_GET['info']);
        }
        $proceed = true;
    }
}
if ($proceed) {
    $response = array('status' => 'ok', 'message' => '');
    mb_internal_encoding('UTF-8');
    if (mb_strlen($parameters['site']) >= 1 && mb_strlen($parameters['site']) <= 300 && mb_strlen($parameters['data']) >= 1 && mb_strlen($parameters['data']) <= 1000) {
        $query = array('info' => array('column' => '', 'value' => ''));
        if (isset($parameters['info'])) {
            $query['info']['column'] = ', `info`';
            $query['info']['value'] = ', :info';
            if (mb_strlen($parameters['info']) > 100) {
                $parameters['info'] = substr($parameters['info'], 0, 100);
            }
        }
        $db = new Database();
        if ($db->isConnected()) {
            $db->query('INSERT INTO `data` (`data`, `ip`, `site`, `method`, `date`' . $query['info']['column'] . ') VALUES (:data, :ip, :site, :method, :date' . $query['info']['value'] . ')');
            $db->bind(':data', $parameters['data']);
            $db->bind(':ip', $_SERVER['REMOTE_ADDR']);
            $db->bind(':site', $parameters['site']);
            $db->bind(':method', $_SERVER['REQUEST_METHOD']);
            $db->bind(':date', date('Y-m-d H:i:s', time()));
            if (isset($parameters['info'])) {
                $db->bind(':info', $parameters['info']);
            }
            if (!$db->execute()) {
                $response['status'] = 'error';
                $response['message'] = 'Database error';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Database error';
        }
        $db->disconnect();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Incorrect data';
    }
    echo json_encode($response, JSON_PRETTY_PRINT);
}
?>
