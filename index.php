<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Content-Type: application/json');

require_once __DIR__.'/controllers/Requests.php';
require_once __DIR__.'/config.php';

// FOR DEBUG USAGE ONLY
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Friendly URL configuration
if ($_GET['url']) {
	// Break the URL using the forward slash as a reference point, 
	// separating the strings into an array
	$url = explode('/', $_GET['url']);
	//array_shift($url); // Remove empty string from first element of array
	// Check if the first element is related to our API
	// Ex: http://example.com/api/action
	//if ($url[0] === 'api') {
	if (in_array($url[0], ACTIONS_UNIVERSE, true)) {
		// Check if action demanded is valid
		if (isset($url[0]) && !empty($url[0])) {	
			$action = strtolower($url[0]); // Get the action to be performed

			$numOfParams = count($url);
			if ($numOfParams > 3) { // Bigger action is /client/client_id
				echo '{"status": false, "message": "Number of parameters exceeds supported limit."}';
				exit;
			}

			// Checks if the action belongs to the universe of valid actions.
			if (!in_array($action, ACTIONS_UNIVERSE, true)) {
				echo '{"status": false, "message": "Ação inválida foi chamada!"}';
				exit;
			}

			// Get REQUEST_METHOD type (GET, POST, DELETE)
			$method = $_SERVER['REQUEST_METHOD'];

			// Get methods
			$methodsArray = array_slice($url, 2);
			
			// Get auth header and check authentication (TODO)
			// $headers = getallheaders();			
			// if (array_key_exists('Authorization', $headers)) {
			// 	$authHeader = $headers['Authorization'];
				
			// 	// Validate user with Authorization header

			// } else {
			// 	echo '{"status": false, "message": "Requisição não contém header de autenticação!"}';
			// 	exit();
			// }			

			try {				
				echo call_user_func_array(array(new $action, 'urlTreatment'), [$method, $methodsArray]);
				http_response_code(200);
				exit;
				
			} catch (Exception $e) {
				echo '{"status": false, "message": "Falha de comunicação com a API!", "error": "'.$e.'"}';
				exit;
			}

		} else {
			// Invalid usage of API
			echo '{"status": false, "message": "Utilização inválida da API, nenhuma método foi chamado!"}';
			exit;
		}	
			
	} else {
		// Invalid usage of API
		echo '{"status": false, "message": "Utilização inválida da API, caminho não reconhecido!"}';
		exit;
	}

} else {
	// Invalid usage of API
	echo '{"status": false, "message": "Utilização inválida da API, caminho não reconhecido!", "code": 2}';
	exit;
}
?>