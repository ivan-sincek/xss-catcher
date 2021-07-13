<?php
include_once './php/database.class.php';
include_once './php/query.class.php';
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
$parameters = array(
    'data' => null,
    'site' => null,
    'info' => null,
    'redirect' => null
);
$response = array(
    'status' => 'error',
    'message' => array()
);
$error = false;
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (($data = @json_decode(file_get_contents('php://input')))) {
        $parameters['site'] = isset($data->site) ? trim(urldecode($data->site)) : $parameters['site'];
        $parameters['data'] = isset($data->data) ? urldecode($data->data) : $parameters['data'];
        $parameters['info'] = isset($data->info) ? trim(urldecode($data->info)) : $parameters['info'];
        $parameters['redirect'] = isset($data->redirect) ? trim(urldecode($data->redirect)) : $parameters['info'];
    } else {
        $parameters['site'] = isset($_POST['site']) ? trim($_POST['site']) : $parameters['site'];
        $parameters['data'] = isset($_POST['data']) ? $_POST['data'] : $parameters['data'];
        $parameters['info'] = isset($_POST['info']) ? trim($_POST['info']) : $parameters['info'];
        $parameters['redirect'] = isset($_POST['redirect']) ? trim($_POST['redirect']) : $parameters['redirect'];
    }
} else if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
    $parameters['site'] = isset($_GET['site']) ? trim($_GET['site']) : $parameters['site'];
    $parameters['data'] = isset($_GET['data']) ? $_GET['data'] : $parameters['data'];
    $parameters['info'] = isset($_GET['info']) ? trim($_GET['info']) : $parameters['info'];
    $parameters['redirect'] = isset($_GET['redirect']) ? trim($_GET['redirect']) : $parameters['redirect'];
} else {
    $response['message'] = 'HTTP method not supported.';
    $error = true;
}
if (!$error) {
    if (strlen($parameters['site']) > 300) {
        $response['message']['site'] = 'Site is exceeding 300 characters';
        $error = true;
    }
    if (strlen($parameters['data']) > 1000) {
        $response['message']['data'] = 'Data is exceeding 1000 characters';
        $error = true;
    }
    if (strlen($parameters['info']) > 300) {
        $parameters['info'] = substr($parameters['info'], 0, 300);
    }
    if (strlen($parameters['redirect']) > 300) {
        $response['message']['redirect'] = 'Redirect is exceeding 300 characters';
        $error = true;
    }
    if (!$error) {
        $params = array(
            'site' => $parameters['site'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'data' => $parameters['data'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'date' => date('Y-m-d H:i:s', time()) ,
            'info' => $parameters['info'],
            'redirect' => $parameters['redirect']
        );
        if (Query::insert('INSERT INTO `data` (`site`, `method`, `data`, `ip`, `date`, `info`, `redirect`) VALUES (:site, :method, :data, :ip, :date, :info, :redirect)', $params)) {
            $response['status'] = 'ok';
            if ($parameters['redirect']) {
                header("Location: ./${parameters['redirect']}");
            }
        } else {
            $response['message']['global'] = 'Database error';
        }
    }
}
echo json_encode($response, JSON_PRETTY_PRINT);
?>
