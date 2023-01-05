<?php
require_once __DIR__."/../config.php";
require_once SITE_ROOT."/models/MySQL.php";

// TODO CREATE A DEFAULT ACTION CLASS TO EXTEND TO OTHERS

class Clients {
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
				return '{"status": true, "message": "Cliente adicionado/atualizado com sucesso!"}';

			case 'GET':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				}
				$list_of_clients = $client->select($uid)->fetch_all(MYSQLI_ASSOC);

				// Return the address parsed
				foreach ($list_of_clients as $key => $data) {
					$list_of_clients[$key]['endereco'] = json_decode($data['endereco']);
				}
				return json_encode($list_of_clients);
				
			case 'DELETE':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				} else {
					return '{"status": false, "message": "UID informado na requisição é inválido!"}';
				}
				$client->remove($uid);
				return '{"status": true, "message": "Cliente removido com sucesso!"}';
		}
	}
}

class Products {
	protected string $url;
	private array $allowed_methods = ["POST", "GET", "DELETE"];

	public function urlTreatment(string $received_method, array $array = null) {
		
		// Check for invalid methods 
		if (!in_array($received_method, $this->allowed_methods, true)) {
			return '{"status": false, "message": "Invalid HTTP method!"}';
		}

		$product = new Product();

		switch ($received_method) {
			case 'POST':
				// TODO VALIDATE INCOMING DATA
				// If method is POST, get the data and prepare to send to API
				$payload = json_decode(file_get_contents("php://input"), true); // Get the JSON object (already decoded, we don't need to transform)
				$product->uid = $payload['uid'];
				$product->nome = $payload['nome'];
				$product->valor = $payload['valor'];
				$product->image = $payload['image'];

				$product->upsert($product);
				return '{"status": true, "message": "Produto adicionado/atualizado com sucesso!"}';

			case 'GET':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				}
				return json_encode($product->select($uid)->fetch_all(MYSQLI_ASSOC));
				
			case 'DELETE':
				// Check if there is an UID in actions array
				$uid = null;
				if (!empty($array) && count($array) > 0 && $array[0] != "") {
					$uid = $array[0];
				} else {
					return '{"status": false, "message": "UID informado na requisição é inválido!"}';
				}
				$product->remove($uid);
				return '{"status": true, "message": "Produto removido com sucesso!"}';
		}
	}
}

class Sales {
	protected string $url;
	private array $allowed_methods = ["GET", "POST"];

	public function urlTreatment(string $received_method, array $array = null) {
		
		// Check for invalid methods 
		if (!in_array($received_method, $this->allowed_methods, true)) {
			return '{"status": false, "message": "Invalid HTTP method!"}';
		}

		$sale = new Sale();

		switch ($received_method) {
			case 'POST':
				// TODO VALIDATE INCOMING DATA
				// If method is POST, get the data and prepare to send to API
				$payload = json_decode(file_get_contents("php://input"), true); // Get the JSON object (already decoded, we don't need to transform)

				// Format products to array of products UIDs
				//$pIds = array_map(function($o) { return $o["uid"];}, $payload['products']); // NOT AN ERROR! Intelephense bug

				$sale->uid = $payload['uid'];
				$sale->data = $payload['data'];;
				$sale->user = json_encode($payload['user']);
				$sale->products = json_encode($payload['products']);
				$sale->total = $payload['total'];
				//$sale->user_uid = $payload['user']['uid'];
				//$sale->products_uids = json_encode($pIds)

				$sale->insert($sale);				
				return '{"status": true, "message": "Nova venda registrada com sucesso!"}';

			case 'GET':				
				//return json_encode($sale->select()->fetch_all(MYSQLI_ASSOC));
				
				$list_of_products_uids = $sale->select()->fetch_all(MYSQLI_ASSOC);
				// Return the uids parsed
				foreach ($list_of_products_uids as $key => $data) {
					$list_of_products_uids[$key]["products"] = json_decode($data["products"]);
					$list_of_products_uids[$key]["user"] = json_decode($data["user"]);
				}
				return json_encode($list_of_products_uids);
		}
	}
}
