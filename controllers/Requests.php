<?php

require_once './RequestController.php';

class DefaultAction {
	protected string $url;
	protected string $method;

	protected function request() {
		$payload = null;
		
		/* If method is POST, get the data and prepare to send to API */ 
		if ($this->method === "POST") {	
			$payload = file_get_contents("php://input"); // Get the JSON object (already decoded, we don't need to transform)
		}

		return RequestController::defaultCurl(PAGARME_CORE_URL.$this->url, $this->method, $payload);
	}

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
		
		return $this->request();
	}
}

class Tokens extends DefaultAction {
	protected string $url = 'core/v5/tokens?';

	public function urlTreatment(string $received_method, array $array = null) {
		// This method only accept POST requests
		if ($received_method !== 'POST') {
			return '{"status": false, "message": "The requested resource does not support HTTP method other than POST!"}';
		} 

		$this->method = $received_method;
	
		if (empty($array) || count($array) > 1) {
			return '{"status": false, "message": "Incorrect action usage!"}';

		} else {
			// Concat Tokens URL with paramter to create card token Ex: https://api.pagar.me/core/v5/tokens?appId=public_key
			$this->url = $this->url . $array[0];
			return $this->request();
		}
	}
}

class Orders extends DefaultAction {
	protected string $url = 'core/v5/orders';

	public function urlTreatment(string $received_method, array $array = null) {
		$this->method = $received_method;

		if (!empty($array) && strpos($array[0], '?')) {
			// Concat Orders URL with customer parameter and/or others to create full URL
			// Ex: https://api.pagar.me/core/v5/orders?customer_id=abcdefg
			$this->url = $this->url . $array[0];

			// Check if there is a store filter (?store=storeId)
			if (strpos($array[0], 'store=')) {
				$allOrders = $this->request();
	
				// Filter orders with store code
				print_r($allOrders);
				exit();
			}

		} else {
			// DEFAULT ACTION
			// Get next action, if there is any
			if (!empty($array)) {
				// Get last key from array, in this case an index
				$last = array_key_last($array);

				foreach ($array as $key => $value) {
					// Check if action is not an empty string 
					// (when using like /orders/, the split method will create an empty string in last position)
					if ($value !== '') {	
						// Concat actions to URL
						// If we are in last iteration, don't apply '/' in the string's end
						$this->url = $key === $last ? $this->url . $value : $this->url . $value . '/';
					}
				}
			}			
		}
		
		return $this->request();
	}
}

class Customers extends DefaultAction {
	protected string $url = 'core/v5/customers/';
}

class Charges extends DefaultAction {
	protected string $url = 'core/v5/charges/';

	public function urlTreatment(string $received_method, array $array = null) {
		$this->method = $received_method;

		if (!empty($array)) {
			// Get last key from array, in this case an index
			$last = array_key_last($array);

			foreach ($array as $key => $value) {
				// Check if action is not an empty string 
				// (when using like /charges/, the split method will create an empty string in last position)
				if ($value !== '') {	
					// Concat actions to URL
					// If we are in last iteration, don't apply '/' in the string's end
					$this->url = $key === $last ? $this->url . $value : $this->url . $value . '/';
				}
			}
		}
		
		return $this->request();
	}
}

class Subscriptions extends DefaultAction {
	protected string $url = 'core/v5/subscriptions/';
}

class Plans extends DefaultAction {
	protected string $url = 'core/v5/plans/';
}

class Invoices extends DefaultAction {
	protected string $url = 'core/v5/invoices/';
}

class Recipients extends DefaultAction {
	protected string $url = 'core/v5/recipients/';
}

class Bin extends DefaultAction {
	protected string $url = 'bin/v1/bin/';
}

?>