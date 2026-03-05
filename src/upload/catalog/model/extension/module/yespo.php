<?php
class ModelExtensionModuleYespo extends Model {
	
	public function makeLogRequest($request_data = [], $event_url = 'https://events.yespo.io/logs/v1/plugin') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json; charset=UTF-8', 'Content-Type: application/json; charset=UTF-8']);
		curl_setopt($ch, CURLOPT_URL, $event_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		curl_close($ch);
	}
	
	public function makeRequest($request_data = [], $event_url = '', $method = 'POST') {
		
		$user = 'anyvalue';
		$password = $this->config->get('yespo_api_key');

		if (empty($password)) return; 
		$ch = curl_init();
		if ($method != 'GET') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data, JSON_UNESCAPED_UNICODE));
		} else {
			$query = http_build_query($request_data);
			$event_url .= ((strpos($event_url, '?') !== false) ? '&' : '?') . $query;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json; charset=UTF-8', 'Content-Type: application/json; charset=UTF-8']);
		curl_setopt($ch, CURLOPT_URL, $event_url);
		curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		$response_json = json_decode($response, true);
		if ($response == '') {
			$response_json = [];
		}
		if ($response_json === null) {
			return [
				'error' => 'bad_json_format',
				'raw_response' => $response,
			];
		}
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($http_code >= 400) {
			if (!isset($response_json['error'])) {
				if ($http_code == 401) {
					$response_json['error'] = 'unauthorized';
				} elseif ($http_code == 400) {
					$response_json['error'] = 'bad_request';
				} else {
					$response_json['error'] = 'http_error_' . $http_code;
				}
			}
		}
		$response_json['http_code'] = $http_code;
		return $response_json;
	}
	
	public function trackEvent($request_data = []) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data, JSON_UNESCAPED_UNICODE));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json; charset=UTF-8', 'Content-Type: application/json; charset=UTF-8']);
		curl_setopt($ch, CURLOPT_URL, 'https://tracker.yespo.io/api/v2');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			curl_setopt($ch, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
		} else {
			return;
		}
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		$response_json['text'] = $response;
		$response_json['http_code'] = $http_code;
		
		$log_str = json_encode($request_data);
		
		$log_data = [
			'orgId'        => (int)$this->config->get('yespo_orgid'),
			'typeCMS'      => 'OpenCart',
			'errorMessage' => '',
			'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_data, 'responseBody' => $response_json]),
			'message'      => 'TRACK_EVENT',
			'log_level'    => 'INFO',
		];
		$this->makeLogRequest($log_data);
		
		return $response_json; 
	}
	
	public function setBadOrder($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "yespo_failed_orders` WHERE attempt_count > 5");
		$query = $this->db->query("SELECT id FROM `" . DB_PREFIX . "yespo_failed_orders` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");
		if ($query->num_rows) {
			$this->db->query("UPDATE `" . DB_PREFIX . "yespo_failed_orders` SET attempt_count = (attempt_count + 1), last_attempt = NOW() WHERE order_id = '" . (int)$order_id . "'");
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "yespo_failed_orders` SET order_id = '" . (int)$order_id . "', attempt_count = 1, last_attempt = NOW()");
		}
	}
	
	public function setBadCustomer($customer_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "yespo_failed_customers` WHERE attempt_count > 5");
		$query = $this->db->query("SELECT id FROM `" . DB_PREFIX . "yespo_failed_customers` WHERE customer_id = '" . (int)$customer_id . "' LIMIT 1");
		if ($query->num_rows) {
			$this->db->query("UPDATE `" . DB_PREFIX . "yespo_failed_customers` SET attempt_count = (attempt_count + 1), last_attempt = NOW() WHERE customer_id = '" . (int)$customer_id . "'");
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "yespo_failed_customers` SET customer_id = '" . (int)$customer_id . "', attempt_count = 1, last_attempt = NOW()");
		}
	}
	
	public function getGeneralInfo() {
		$general_info = [
			'siteId' => $this->config->get('yespo_siteid'),
			'datetime' => (int)(microtime(true) * 1000),
		];

		if (!empty($this->request->cookie['sc'])) {
			$general_info['cookies']['sc'] = $this->request->cookie['sc'];
		}
		return $general_info;
	}
	
	public function getCustomerDataGeneralInfo($order_info = []) {
		
		if ($this->customer->isLogged()) {
			$general_info = [
			'externalCustomerId' => $this->customer->isLogged(),
			'user_email'         => $this->customer->getEmail(),
			'user_name'          => $this->customer->getFirstname() . ($this->customer->getLastName() ? ' ' . $this->customer->getLastName() : ''),
			'user_phone'         => preg_replace('/[^0-9]/', '', (string)$this->customer->getTelephone()),
			];
		}
		
		if ($order_info) {
			$general_info = [
			'user_email'         => $order_info['email'],
			'user_name'          => $order_info['firstname'] . ($order_info['lastname'] ? ' ' . $order_info['lastname'] : ''),
			'user_phone'         => preg_replace('/[^0-9]/', '', (string)$order_info['telephone']),
			];
			
			if ($order_info['customer_id']) {
				$general_info['externalCustomerId'] = $order_info['customer_id'];
			}
		}
		
		$general_info['siteId'] = $this->config->get('yespo_siteid');
		$general_info['datetime'] = (int)(microtime(true) * 1000);
		
		if (!empty($this->request->cookie['sc'])) {
			$general_info['cookies']['sc'] = $this->request->cookie['sc'];
		}
		return $general_info;
	}
	
	public function addWishlist($product_info) {
		
		$tracking_data = [];
		$tracking_data['GeneralInfo'] = [
			'eventName' => 'AddToWishlist',
		];
		
		$general_info = $this->getGeneralInfo();
		if (!empty($general_info)) {
			$tracking_data['GeneralInfo'] = array_merge($tracking_data['GeneralInfo'], $general_info);
		}
		
		$tracking_data['AddToWishlist'] = [];

		$tracking_data['AddToWishlist']['Product'] = [
			'productKey' => $product_info['product_id'],
			'price' => (string)$this->currency->format($product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency'], '', false),
			'isInStock' => (string)($product_info['quantity'] > 0),
		];
		
 		$this->trackEvent($tracking_data);
	
	}
	
	public function sendCustomerData($order_info = []) {
		
		$tracking_data = [];
		$tracking_data['GeneralInfo'] = [
			'eventName' => 'CustomerData',
		];
		
		$general_info = $this->getCustomerDataGeneralInfo($order_info);
		
		if (!empty($general_info)) {
			$tracking_data['GeneralInfo'] = array_merge($tracking_data['GeneralInfo'], $general_info);
		}
	 		
		$this->trackEvent($tracking_data);
		usleep(200000);
	
	}
	
	public function sendCart() {
		
		$this->session->data['yespo_guid'] = uniqid();
		
		$tracking_data = [];
		$tracking_data['GeneralInfo'] = [
			'eventName' => 'StatusCart',
		];
		
		$general_info = $this->getGeneralInfo();
		if (!empty($general_info)) {
			$tracking_data['GeneralInfo'] = array_merge($tracking_data['GeneralInfo'], $general_info);
		}
		
		$tracking_data['StatusCart'] = [];
		
		if (!empty($this->session->data['yespo_guid'])) {
			$tracking_data['StatusCart']['GUID'] = $this->session->data['yespo_guid'];
		}
		
		$tracking_data['StatusCart']['Products'] = [];
		
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			$cart_product = [
				'productKey' => $product['product_id'],
				'price' => (string)$this->currency->format($product['price'], $this->session->data['currency'], '', false),
				'quantity' => (int)$product['quantity'],
				'price_currency_code' => $this->session->data['currency'],
			];
			$tracking_data['StatusCart']['Products'][] = $cart_product;
		}
	 	
		$this->trackEvent($tracking_data);
	}
	
	public function sendOrder($order_id, $order_info) {
		
		$this->sendCustomerData($order_info);
		
		$tracking_data = [];
		$tracking_data['GeneralInfo'] = [
			'eventName' => 'PurchasedItems',
			'siteId' => $this->config->get('yespo_siteid'),
			'datetime' => (int)(microtime(true) * 1000),
		];
		
		$general_info = $this->getGeneralInfo();
		
		if (!empty($this->request->cookie['sc'])) {
			$general_info['cookies']['sc'] = $this->request->cookie['sc'];
		}
		
		if (!empty($general_info)) {
			$tracking_data['GeneralInfo'] = array_merge($tracking_data['GeneralInfo'], $general_info);
		}

		$tracking_data['PurchasedItems'] = [];
		$tracking_data['PurchasedItems']['OrderNumber'] = (string)$order_id;
		if (!empty($this->session->data['yespo_guid'])) {
			$tracking_data['PurchasedItems']['GUID'] = $this->session->data['yespo_guid'];
		} 
		$tracking_data['PurchasedItems']['TrackedOrderId'] = uniqid();

		$tracking_data['PurchasedItems']['Products'] = [];
		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		foreach ($order_product_query->rows as $product) {
			$tracking_data['PurchasedItems']['Products'][] = [
				'product_id'     => (string)$product['product_id'],
				'unit_price'     => (string)$this->currency->format($product['price'], $this->config->get('config_currency'), '', false),
				'quantity'       => (int)$product['quantity'],
			];
		}  
		$this->trackEvent($tracking_data);
	}
}