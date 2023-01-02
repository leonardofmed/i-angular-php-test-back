<?php
/**
 * API to mirror payments for Pagar.me
 * Usage ex: site.com/YOUR_API_FOLDER/payment/orders -> Will GET user orders
*/

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
require_once './PaymentController.php';
require_once './FirebaseAuthController.php';
require_once __DIR__.'/vendor/autoload.php';

// TODO REMOVE
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if ($_GET['url']) {
	// Break the URL using the forward slash as a reference point, 
	// separating the strings into an array 
	$url = explode('/', $_GET['url']);

	// Check if the first element is related to our API
	if ($url[0] === 'payment') {
		// Check if action demanded is valid
		if (isset($url[1]) && !empty($url[1])) {	
			$action = strtolower($url[1]); // Get the action to be performed

			$numOfParams = count($url);
			if ($numOfParams > 7) { // Bigger action is /subscriptions/subscription_id/items/item_id/usages/usage_id
				echo '{"status": false, "message": "Number of parameters exceeds supported limit."}';
				exit;
			}

			// Checks if the action belongs to the universe of valid actions.
			if (!in_array($action, ACTIONS_UNIVERSE, true)) {
				echo '{"status": false, "message": "Invalid action called!"}';
				exit;
			}

			// Get REQUEST_METHOD type (GET, POST)
			$method = $_SERVER['REQUEST_METHOD'];

			// Add specific condition to tokens action
			if ($action === 'tokens') {
				// Check if appId parameter exist in requested URI
				if (strpos($_SERVER['REQUEST_URI'], 'appId=')) {					
					// Get parameter with value in url (appId=abcdefg)
					$urlParam = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "?") + 1);

				} else {
					echo '{"status": false, "message": "Incorrect action usage!"}';
					exit;
				}
			}

			// TODO ADD METHOD TO GET AND FILTER ORDERS RELATED TO A SINGLE STORE (REQUEST MADE FROM STORE DASHBOARD PANEL)
			if ($action === 'orders') {
				// Check if customer_id parameter exist in requested URI (requesting orders from a single customer)
				if (strpos($_SERVER['REQUEST_URI'], 'customer_id=')) {					
					// Get parameter with value in url (?customer_id=abcdefg)
					// In this case, there can be more parameters as well
					$urlParam = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "?"));
				} 
			}

			// Get methods
			if ($action === 'tokens') {
				$methodsArray = [$urlParam];
			} elseif ($action === 'orders') {
				$methodsArray = $urlParam ? [$urlParam] : array_slice($url, 2);
			} else {
				$methodsArray = array_slice($url, 2);
			}
			
			// Get auth header and check Firebase authentication
			$headers = getallheaders();			
			if (array_key_exists('Authorization', $headers)) {
				$authHeader = $headers['Authorization'];
				
				// Validate user with Authorization header
				(new FirebaseAuth)->verifyIdToken($authHeader);

			} else {
				echo '{"status": false, "message": "Requisição não contém header de autenticação!"}';
				exit();
			}
			

			try {				
				echo call_user_func_array(array(new $action, 'urlTreatment'), [$method, $methodsArray]);
				http_response_code(200);
				exit;
				
			} catch (Exception $e) {
				echo '{"status": false, "message": "Communication failure with payment API!", "error": "'.$e.'"}';
				exit;
			}

		} else {
			// Invalid usage of API
			echo '{"status": false, "message": "Invalid API usage, no action has been called."}';
			exit;
		}		
	}
}
?>