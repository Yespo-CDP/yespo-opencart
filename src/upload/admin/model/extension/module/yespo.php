<?php
class ModelExtensionModuleYespo extends Model {

	public function makeRequest($request_data = [], $event_url = '', $method = 'POST', $api_key = '', $override = false) {
		$password = $this->config->get('yespo_api_key');
		if ((empty($password) || $override) && !empty($api_key)) {
			$password = $api_key;
		}
		if (empty($password)) {
			return;
		}
		$ch = curl_init();
		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
		} else {
			$query = http_build_query($request_data);
			$event_url .= ((strpos($event_url, '?') !== false) ? '&' : '?') . $query;
		}
		if ($method == 'DELETE') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json; charset=UTF-8', 'Content-Type: application/json; charset=UTF-8']);
		curl_setopt($ch, CURLOPT_URL, $event_url);
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			$this->log->write('Yespo API makeRequest cURL Error (' . curl_errno($ch) . '): ' . curl_error($ch));
		}
		$response_json = json_decode($response, true);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($response == '') {
			$response_json = [];
		}
		if ($response_json === null) {
			return [
				'error' => 'bad_json_format',
				'http_code' => $http_code,
				'raw_response' => $response 
			];
		}
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

	public function makePlainRequest($request_data = [], $event_url = '') {
		$password = $this->config->get('yespo_api_key');
		if (empty($password)) {
			return;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: text/plain; charset=UTF-8']);
		curl_setopt($ch, CURLOPT_URL, $event_url);
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		$response_json['text'] = $response;
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
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
		if (curl_errno($ch)) {
			$this->log->write('Yespo API makePlainRequest cURL Error (' . curl_errno($ch) . '): ' . curl_error($ch));
		}
		curl_close($ch);
		return $response_json;
	}

	public function makeLogRequest($request_data = [], $orgid = '', $event_url = 'https://events.yespo.io/logs/v1/plugin') {
		$request_data['typeCMS'] = 'OpenCart';
		$request_data['errorMessage'] = '';
		$request_data['orgId'] = (int)$this->config->get('yespo_orgid');
		if (!empty($orgid)) {
			$request_data['orgId'] = (int)$orgid;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json; charset=UTF-8', 'Content-Type: application/json; charset=UTF-8']);
		curl_setopt($ch, CURLOPT_URL, $event_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_exec($ch);
		if (curl_errno($ch)) {
			$this->log->write('Yespo API makePlainRequest cURL Error (' . curl_errno($ch) . '): ' . curl_error($ch));
		}
		curl_close($ch);
	}
	
	public function getCustomers($start, $limit) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE `status` = '1' ORDER BY `customer_id` ASC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}

	public function getTotalCustomers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE status = 1");
		return $query->row['total'];
	}

	public function getOrders($start, $limit) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_status_id` > 0 ORDER BY `order_id` ASC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}

	public function getTotalOrders() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order WHERE `order_status_id` > 0");
		return $query->row['total'];
	}
}