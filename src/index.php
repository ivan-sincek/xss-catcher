<?php
include_once './php/database.class.php';
include_once './php/query.class.php';
header('Content-Type: application/json; charset=UTF-8');
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Credentials: true');
$parameters = array(
    'headers' => null,
    'data' => null,
    'site' => null,
    'redirect' => null,
    'info' => null
);
$response = array(
    'status' => 'error',
    'message' => array()
);
$error = false;
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
    if (($data = @json_decode(file_get_contents('php://input')))) {
        $parameters['data'] = isset($data->data) ? urldecode($data->data) : $parameters['data'];
        $parameters['site'] = isset($data->site) ? trim(urldecode($data->site)) : $parameters['site'];
        $parameters['redirect'] = isset($data->redirect) ? trim(urldecode($data->redirect)) : $parameters['redirect'];
        $parameters['info'] = isset($data->info) ? trim(urldecode($data->info)) : $parameters['info'];
    } else {
        $parameters['data'] = isset($_POST['data']) ? $_POST['data'] : $parameters['data'];
        $parameters['site'] = isset($_POST['site']) ? trim($_POST['site']) : $parameters['site'];
        $parameters['redirect'] = isset($_POST['redirect']) ? trim($_POST['redirect']) : $parameters['redirect'];
        $parameters['info'] = isset($_POST['info']) ? trim($_POST['info']) : $parameters['info'];
    }
} else if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
    $parameters['data'] = isset($_GET['data']) ? $_GET['data'] : $parameters['data'];
    $parameters['site'] = isset($_GET['site']) ? trim($_GET['site']) : $parameters['site'];
    $parameters['redirect'] = isset($_GET['redirect']) ? trim($_GET['redirect']) : $parameters['redirect'];
    $parameters['info'] = isset($_GET['info']) ? trim($_GET['info']) : $parameters['info'];
} else {
    $error = true;
    $response['message'] = 'HTTP method not supported.';
}
function to_text($array) {
    $tmp = '';
    foreach ($array as $key => $value) {
        $tmp .= ($tmp ? "\n" : "") . "${key}: ${value}";
    }
    return $tmp;
}
if (!$error) {
	$params = array(
		'method' => $_SERVER['REQUEST_METHOD'],
		'headers' => to_text(getallheaders()),
		'data' => $parameters['data'],
		'date' => date('Y-m-d H:i:s', time()),
		'ip' => $_SERVER['REMOTE_ADDR'],
		'site' => $parameters['site'],
		'redirect' => $parameters['redirect'],
		'info' => $parameters['info']
	);
	if (Query::insert('INSERT INTO `data` (`method`, `headers`, `data`, `date`, `ip`, `site`, `redirect`, `info`) VALUES (:method, :headers, :data, :date, :ip, :site, :redirect, :info)', $params)) {
		$response['status'] = 'ok';
		if ($parameters['redirect']) {
			header("Location: ./{$parameters['redirect']}");
		}
	} else {
		$response['message']['global'] = 'Database error.';
	}
}
echo json_encode($response, JSON_PRETTY_PRINT);
?>
