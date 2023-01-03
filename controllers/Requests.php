<?php
require_once __DIR__."/../config.php";
require_once SITE_ROOT."/models/MySQL.php";

class DefaultAction {
	protected string $url;
	protected string $method;

	public function urlTreatment(string $received_method, array $array = null) {
		$this->method = $received_method;

		// Get next action, if there is any
		$i = 0;
		if (!empty($array)) {
			foreach ($array as $value) {
				// Check if action is not an empty string 
				// (when using like /orders/, the split method will create a empty string in last position)
				if ($value !== '') {	
					// Concat actions to URL
					$this->url = $this->url . $value . '/';
					$i++;
				}
			}
		}
		
		// return $this->request();
		var_dump($this->url);
	}
}

class Clients extends DefaultAction {
	protected string $url;
	private array $allowed_methods = ["POST", "GET", "DELETE"];

	public function urlTreatment(string $received_method, array $array = null) {
		
		// Check for invalid methods 
		if (!in_array($received_method, $this->allowed_methods, true)) {
			return '{"status": false, "message": "Invalid HTTP method!"}';
		}

		$client = new Client();

		switch ($received_method) {
			case 'POST':
				// TODO VALIDATE INCOMING DATA
				// If method is POST, get the data and prepare to send to API
				$payload = json_decode(file_get_contents("php://input"), true); // Decode string data and transform in array format
				$client->uid = $payload['uid'];
				$client->nome = $payload['nome'];
				$client->cpf = $payload['cpf'];
				$client->endereco = json_encode($payload['endereco']);
				$client->email = $payload['email'];
				$client->nascimento = $payload['nascimento'];
				$client->image = $payload['image'];

				$client->upsert($client);
				return '{"status": true, "message": "Novo cliente adicionado com sucesso!"}';

			case 'GET':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				}
				return json_encode($client->select($uid)->fetch_all(MYSQLI_ASSOC));
				
			case 'DELETE':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				}
				return $client->remove($uid);
		}
	}
}

class Products extends DefaultAction {
	protected string $url;
	private array $allowed_methods = ["POST", "GET", "DELETE"];

	public function urlTreatment(string $received_method, array $array = null) {
		
		// Check for invalid methods 
		if (!in_array($received_method, $this->allowed_methods, true)) {
			return '{"status": false, "message": "Invalid HTTP method!"}';
		} 

		$this->method = $received_method;

		$product = new Product();

		switch ($this->method) {
			case 'POST':
				// TODO VALIDATE INCOMING DATA
				// If method is POST, get the data and prepare to send to API
				$payload = file_get_contents("php://input"); // Get the JSON object (already decoded, we don't need to transform)
				$product->uid = $payload['uid'];
				$product->nome = $payload['nome'];
				$product->valor = $payload['valor'];
				$product->image = $payload['image'];

				return $product->upsert($product);

			case 'GET':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				}
				return $product->select($uid);
				
			case 'DELETE':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				}
				return $product->remove($uid);
		}
	}
}

class Sales extends DefaultAction {
	protected string $url;
	private array $allowed_methods = ["GET", "POST"];

	public function urlTreatment(string $received_method, array $array = null) {
		
		// Check for invalid methods 
		if (!in_array($received_method, $this->allowed_methods, true)) {
			return '{"status": false, "message": "Invalid HTTP method!"}';
		} 

		$this->method = $received_method;

		$sale = new Sale();

		switch ($this->method) {
			case 'POST':
				// TODO VALIDATE INCOMING DATA
				// If method is POST, get the data and prepare to send to API
				$payload = file_get_contents("php://input"); // Get the JSON object (already decoded, we don't need to transform)

				// Format products to array of products UIDs
				$pIds = array_map(function($o) { return $o->uid;}, $payload['products']); // NOT AN ERROR! Intelephense bug

				$sale->uid = $payload['uid'];
				$sale->data = $payload['data'];
				$sale->user_uid = $payload['user']['uid'];
				$sale->products_uids = json_encode($pIds);
				$sale->total = $payload['total'];

				return $sale->insert($sale);

			case 'GET':				
				return $sale->select();
		}
	}
}
