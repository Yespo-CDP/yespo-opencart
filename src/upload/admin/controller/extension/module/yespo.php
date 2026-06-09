<?php
class ControllerExtensionModuleYespo extends Controller {
	private $error = [];
	private $contacts_url = 'https://yespo.io/api/v1/contacts';
	private $contact_url = 'https://yespo.io/api/v1/contact';
	private $account_info_url = 'https://yespo.io/api/v1/account/info';
	private $orders_url = 'https://yespo.io/api/v1/orders';
	private $domains_url = 'https://yespo.io/api/v1/site/domains';
	private $site_script_url = 'https://yespo.io/api/v1/site/script';
	private $webpush_domain_url = 'https://yespo.io/api/v1/site/webpush/domain';
	private $webpush_script_url = 'https://yespo.io/api/v1/site/webpush/script';

	public function index() {
		$this->load->language('extension/module/yespo');
		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		$token_data = $this->getTokenData();
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['action']) && $this->request->post['action'] == 'disconnect') {
				$this->uninstall();
				$this->install();
				$this->response->redirect($this->url->link('extension/module/yespo', $token_data['token'], true));
			}
		}
		
		$data = $this->language->all();
		$data['token'] = $token_data['token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['breadcrumbs'] = $this->getBreadcrumbs($token_data['token'], $token_data['extension']);
		$data = array_merge($data, $this->getModuleLinks($token_data['token'], $token_data['extension']));
		$data = array_merge($data, $this->getSettingsData());
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/yespo', $data));
	}
	
	private function getTokenData() {
		if (version_compare(VERSION, '3.0.0.0', '>=')) {
			$token_data = [
				'token'     => 'user_token=' . $this->session->data['user_token'],
				'extension' => 'marketplace/extension',
			];
		} else {
			$token_data = [
				'token'     => 'token=' . $this->session->data['token'],
				'extension' => 'extension/extension',
			];
		}
		return $token_data;
	}
	
	private function getBreadcrumbs($token, $extension) {
		return [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', $token, true),
			],
			[
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link($extension, $token . '&type=module', true),
			],
			[
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/yespo', $token, true),
			],
		];
	}
	
	private function getModuleLinks($token, $extension) {
		return [
			'action'           => $this->url->link('extension/module/yespo', $token, true),
			'cancel'           => $this->url->link($extension, $token . '&type=module', true),
			'check_api_key'    => html_entity_decode($this->url->link('extension/module/yespo/checkApiKey', $token, true)),
			'load_customers'   => html_entity_decode($this->url->link('extension/module/yespo/loadCustomers', $token, true)),
			'load_orders'      => html_entity_decode($this->url->link('extension/module/yespo/loadOrders', $token, true)),
			'get_site_script'  => html_entity_decode($this->url->link('extension/module/yespo/getSiteScript', $token, true)),
			'add_web_push'     => html_entity_decode($this->url->link('extension/module/yespo/addWebPush', $token, true)),
			'disconnect'       => html_entity_decode($this->url->link('extension/module/yespo/disconnect', $token, true)),
			'set_active'       => html_entity_decode($this->url->link('extension/module/yespo/setActive', $token, true)),
			'toggle_app_inbox' => html_entity_decode($this->url->link('extension/module/yespo/toggleAppInbox', $token, true)),
		];
	}

	private function getSettingsData() {
		$this->load->model('extension/module/yespo');
		return [
			'yespo_status'           => $this->config->get('yespo_status'),
			'yespo_api_key'          => $this->config->get('yespo_api_key'),
			'yespo_orgname'          => $this->config->get('yespo_orgname'),
			'yespo_site_script'      => $this->config->get('yespo_site_script'),
			'yespo_web_push'         => $this->config->get('yespo_web_push'),
			'yespo_web_push_script'  => $this->config->get('yespo_web_push_script'),
			'yespo_app_inbox'        => $this->config->get('yespo_app_inbox') ? $this->config->get('yespo_app_inbox') : 0,
			'yespo_customers_page'   => $this->config->get('yespo_customers_page') ? $this->config->get('yespo_customers_page') : 1,
			'yespo_orders_page'      => $this->config->get('yespo_orders_page') ? $this->config->get('yespo_orders_page') : 1,
			'yespo_customers_loaded' => $this->config->get('yespo_customers_loaded'),
			'yespo_orders_loaded'    => $this->config->get('yespo_orders_loaded'),
			'yespo_bad_customers'    => $this->config->get('yespo_bad_customers') ? (int)$this->config->get('yespo_bad_customers') : 0,
			'yespo_bad_orders'       => $this->config->get('yespo_bad_orders') ? (int)$this->config->get('yespo_bad_orders') : 0,
			'customers_limit'        => $this->config->get('yespo_customers_limit') ? $this->config->get('yespo_customers_limit') : 2000,
			'orders_limit'           => $this->config->get('yespo_orders_limit') ? $this->config->get('yespo_orders_limit') : 300,
			'total_customers_db'     => $this->model_extension_module_yespo->getTotalCustomers(),
			'total_orders_db'        => $this->model_extension_module_yespo->getTotalOrders(),
		];
	}
	
	public function checkApiKey() {
		$json = [];
		$api_key = !empty($this->request->post['api_key']) ? $this->request->post['api_key'] : false;
		$this->load->language('extension/module/yespo');
		$this->load->model('extension/module/yespo');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $api_key && $this->validate()) {
			$current_api_key = $this->config->get('yespo_api_key');
			$request = $this->model_extension_module_yespo->makeRequest([], $this->account_info_url, 'GET', $api_key, true);
			
			if (!empty($request['orgId'])) {
				$this->logApiEvent('ADD_API_KEY_SUCCESS', 'INFO', ['domain' => $this->request->server['SERVER_NAME']], $request['orgId']);
				$json['success'] = true;
				$json['org_name'] = $request['organisationName'];
				$json['orgid'] = $request['orgId'];
				
				if ($current_api_key !== $api_key) {
					$this->resetSettings($api_key, $request['orgId'], $request['organisationName']);
					$json['reset'] = true;
				} else {
					$json['reset'] = false;
				}
			} else {
				$json['success'] = false;
				$this->logApiEvent('ADD_API_KEY_FAILED', 'INFO', ['domain' => $this->request->server['SERVER_NAME'], 'responseBody' => $request]);
			}
		} else {
			$json['success'] = false;
		}
		
		$this->respondJson($json);
	}

	public function setActive() {
		$json = ['success' => false];
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_status', '1');
			if (version_compare(VERSION,'3.0.0.0', '>=')) {
				$this->model_setting_setting->editSetting('module_yespo', ['module_yespo_status' => 1]);
			}
			$json['success'] = true;
		}
		
		$this->respondJson($json);
	}

	public function sendCustomer($customer_id) {
		$this->load->model('customer/customer');
		
		$customer_info = $this->model_customer_customer->getCustomer($customer_id);
		
		if ($customer_info) {
			$phone = preg_replace('/[^0-9]/', '', (string)$customer_info['telephone']);
			$request_body = [
				'externalCustomerId' => $customer_info['customer_id'],
				'firstName' => $customer_info['firstname'],
				'lastName'  => $customer_info['lastname'],
				'channels'  => [['type' => 'email', 'value' => $customer_info['email']], ['type' => 'sms', 'value' => $phone]],
			];
			$this->load->model('extension/module/yespo');
			$this->model_extension_module_yespo->makeRequest($request_body, $this->contact_url);
		}
	}

	public function addCustomer($route, $args, $output = null) {
		if (!empty($output)) {
			$customer_id = (int)$output;
			if ($customer_id) {
				$this->sendCustomer($customer_id);
			}
		}
	}

	public function editCustomer($route, $args, $output = null) {
		$customer_id = isset($args[0]) ? (int)$args[0] : 0;
		if ($customer_id) {
			$this->sendCustomer($customer_id);
		}
	}

	public function deleteCustomer($route, $args, $output = null) {
		$customer_id = isset($args[0]) ? (int)$args[0] : 0;
		if ($customer_id) {
			$request_body = [
				'externalCustomerId' => $customer_id,
				'erase'              => true,
			];
			$this->load->model('extension/module/yespo');
			$this->model_extension_module_yespo->makeRequest($request_body, $this->contact_url, 'DELETE');
		}
	}
	
	public function loadCustomers() {
		$json = ['success' => false];
		
		if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !$this->validate()) {
			$json['error'] = 'permission_or_method';
			$this->respondJson($json);
			return;
		}
		
		$this->load->language('extension/module/yespo');
		$this->load->model('extension/module/yespo');
		$this->load->model('setting/setting');
		
		$page = isset($this->request->post['page']) ? (int)$this->request->post['page'] : 1;
		$limit = $this->config->get('yespo_customers_limit') ? (int)$this->config->get('yespo_customers_limit') : 2000;
		$start = ($page - 1) * $limit;
		
		if (empty($this->session->data['yespo_total_customers'])) {
			$json['total_customers'] = $this->model_extension_module_yespo->getTotalCustomers();
			$this->session->data['yespo_total_customers'] = $json['total_customers'];
		} else {
			$json['total_customers'] = $this->session->data['yespo_total_customers'];
		}
		
		$customers = $this->model_extension_module_yespo->getCustomers($start, $limit);
		
		$total_customers_in_batch = is_array($customers) ? count($customers) : 0;
		
		if ($total_customers_in_batch === 0) {
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_loaded', '1');
			$this->logApiEvent('SEND_CONTACTS_BULK_SUCCESS', 'INFO', ['domain' => $this->request->server['SERVER_NAME']]);
			$json['success'] = true;
			$json['processed_count'] = 0;
			$json['failed_count'] = 0;
			$json['next_page'] = false;
			
			$this->respondJson($json);
			return;
		}
		
		$contacts_payload = [];
		
		foreach ($customers as $customer) {
			$phone = preg_replace('/[^0-9]/', '', (string)$customer['telephone']);
			$contacts_payload[] = [
				'externalCustomerId' => $customer['customer_id'],
				'email'              => $customer['email'],
				'firstName'          => $customer['firstname'],
				'lastName'           => $customer['lastname'],
				'phone'              => $phone,
				'channels'           => [['type' => 'email', 'value' => $customer['email']], ['type' => 'sms', 'value' => $phone]],
			];
		}
		
		$request_body = [
			'contacts' => $contacts_payload,
			'dedupeOn' => 'externalCustomerId',
		];
		
		$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->contacts_url);
		
		$failed_count = 0;
		
		if (isset($response['failedContacts']) && is_array($response['failedContacts'])) {
			$failed_count = count($response['failedContacts']);
		}
		
		if (!isset($response['id'])) {
			if (isset($response['http_code']) && $response['http_code'] == 401) {
				$json['error'] = $this->language->get('error_api_key_2');
			} else {
				$json['error'] = $this->language->get('error_connection');
			}
			$this->logApiEvent('SEND_CONTACTS_BULK_FAILED', 'ERROR', ['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]);
			$this->respondJson($json);
			return;
		}
		
		$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_page', $page);
		
		if ($page == 1) {
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_customers', $failed_count);
		} else {
			$current_bad = (int)$this->config->get('yespo_bad_customers');
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_customers', $current_bad + $failed_count);
		}
		
		$batch_end = (($start + $limit) > $json['total_customers']) ? $json['total_customers'] : ($start + $limit);
		$batch_range = $start . '-' . $batch_end;
		
		$this->logApiEvent('SEND_CONTACTS_BULK_SUCCESS', 'INFO', ['domain' => $this->request->server['SERVER_NAME'], 'batchRange' => $batch_range]);
		
		if ($total_customers_in_batch == $limit) {
			$json['next_page'] = $page + 1;
		} else {
			$json['next_page'] = false;
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_loaded', '1');
		}
		
		$json['success'] = true;
		$json['processed_count'] = count($contacts_payload);
		$json['failed_count'] = $failed_count;
		
		$this->respondJson($json);
	}
	
	public function loadOrders() {
		$json = ['success' => false];
		
		if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !$this->validate()) {
			$json['error'] = 'permission_or_method';
			$this->respondJson($json);
			return;
		}
		
		$this->load->language('extension/module/yespo');
		$this->load->model('extension/module/yespo');
		$this->load->model('sale/order'); 
		$this->load->model('setting/setting');
		
		$page = isset($this->request->post['page']) ? (int)$this->request->post['page'] : 1;
		$limit = $this->config->get('yespo_orders_limit') ? (int)$this->config->get('yespo_orders_limit') : 300;
		$start = ($page - 1) * $limit;
		
		if (empty($this->session->data['yespo_total_orders'])) {
			$json['total_orders'] = $this->model_extension_module_yespo->getTotalOrders();
			$this->session->data['yespo_total_orders'] = $json['total_orders'];
		} else {
			$json['total_orders'] = $this->session->data['yespo_total_orders'];
		}
		
		$orders = $this->model_extension_module_yespo->getOrders($start, $limit);
		
		$total_in_batch = is_array($orders) ? count($orders) : 0;
		
		if ($total_in_batch === 0) {
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_loaded', '1');
			$this->logApiEvent('SEND_ORDERS_BULK_SUCCESS', 'INFO', ['domain' => $this->request->server['SERVER_NAME']]);
			$json['success'] = true;
			$json['processed_count'] = 0;
			$json['failed_count'] = 0;
			$json['next_page'] = false;
			$this->respondJson($json);
			return;
		}
		
		$in_progress_status = $this->config->get('config_processing_status');
		$delivered_status = $this->config->get('config_complete_status');
		$orders_payload = [];
		
		foreach ($orders as $order) {
			$items = [];
			
			$products = $this->model_sale_order->getOrderProducts($order['order_id']);
			
			if (is_array($products)) {
				foreach ($products as $product) {
					$items[] = [
						'externalItemId' => $product['product_id'],
						'name'           => $product['name'],
						'quantity'       => (int)$product['quantity'],
						'cost'           => $this->currency->format($product['price'], $this->config->get('config_currency'), '', false),
					];
				}
			}
			
			$status = 'INITIALIZED';
			
			if (is_array($in_progress_status) && in_array($order['order_status_id'], $in_progress_status)) {
				$status = 'IN_PROGRESS';
			}
			
			if (is_array($delivered_status) && in_array($order['order_status_id'], $delivered_status)) {
				$status = 'DELIVERED';
			}
			
			$order_data = [
				'externalOrderId' => $order['order_id'],
				'totalCost'       => $this->currency->format($order['total'], $this->config->get('config_currency'), '', false),
				'status'          => $status,
				'date'            => gmdate('Y-m-d\TH:i:s\Z', strtotime($order['date_added'])),
				'currency'        => $this->config->get('config_currency'),
				'email'           => $order['email'],
				'phone'           => preg_replace('/[^0-9]/', '', (string)$order['telephone']),
				'firstName'       => $order['firstname'],
				'lastName'        => $order['lastname'],
				'deliveryMethod'  => $order['shipping_method'],
				'paymentMethod'   => $order['payment_method'],
				'items'           => $items,
			];
			
			if ($order['customer_id'] > 0) {
				$order_data['externalCustomerId'] = $order['customer_id'];
			}
			
			$orders_payload[] = $order_data;
		}
		
		$request_body['orders'] = $orders_payload;
		
		$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->orders_url);
		
		$failed_count = 0;
		
		if (isset($response['failedOrders']) && is_array($response['failedOrders'])) {
			$failed_count = count($response['failedOrders']);
		}
		
		if (empty($response) || !isset($response['http_code']) || $response['http_code'] != 200) {
			if (isset($response['http_code']) && $response['http_code'] == 401) {
				$json['error'] = $this->language->get('error_api_key_2');
			} else {
				$json['error'] = $this->language->get('error_connection');
			}
			
			$this->logApiEvent('SEND_ORDERS_BULK_FAILED', 'ERROR', ['domain' => $this->request->server['SERVER_NAME'], 'responseBody' => $response]);
			$this->respondJson($json);
			return;
		}
		
		$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_page', $page);
		
		if ($page == 1) {
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_orders', $failed_count);
		} else {
			$current_bad = (int)$this->config->get('yespo_bad_orders');
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_orders', $current_bad + $failed_count);
		}
		
		$batch_end = ($start + $limit) > $json['total_orders'] ? $json['total_orders'] : ($start + $limit);
		$batch_range = $start . '-' . $batch_end;
		
		$this->logApiEvent('SEND_ORDERS_BULK_SUCCESS', 'INFO', ['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'batchRange' => $batch_range, 'responseBody' => $response]);
		
		if ($total_in_batch == $limit) {
			$json['next_page'] = $page + 1;
		} else {
			$json['next_page'] = false;
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_loaded', '1');
		}
		
		$json['success'] = true;
		$json['processed_count'] = count($orders_payload);
		$json['failed_count'] = $failed_count;
		
		$this->respondJson($json);
	}
	
	public function getSiteScript() {
		$json = ['success' => false];
		
		if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !$this->validate()) {
			$json['error'] = 'permission_or_method';
			$this->respondJson($json);
			return;
		}
		
		$this->load->model('extension/module/yespo');
		$this->load->model('setting/setting');
		
		$domain = $this->request->server['SERVER_NAME'];
		$request_body = ['domain' => $domain];
		
		$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->domains_url);
		
		if (empty($response['http_code']) || !in_array($response['http_code'], [200, 201])) {
			$json['error'] = true;
			$this->logApiEvent('ADD_DOMAIN_FAILED', 'ERROR', ['domain' => $domain, 'requestBody' => $request_body, 'responseBody' => $response]);
			$this->respondJson($json);
			return;
		}
		
		$this->logApiEvent('ADD_DOMAIN_SUCCESS', 'INFO', ['domain' => $domain]);
		$this->model_setting_setting->editSettingValue('yespo', 'yespo_siteid', $response['siteId']);	
		$json['siteid'] = $response['siteId'];	
		
		$script_response = $this->model_extension_module_yespo->makePlainRequest($request_body, $this->site_script_url);
		
		if (empty($script_response['text']) || empty($script_response['http_code']) || !in_array($script_response['http_code'], [200])) {
			$this->logApiEvent('GET_SCRIPT_FAILED', 'ERROR', ['domain' => $domain, 'requestBody' => $request_body, 'responseBody' => $script_response]);
			$this->respondJson($json);
			return;
		}
		
		$this->model_setting_setting->editSettingValue('yespo', 'yespo_site_script', $script_response['text']);
		$json['success'] = true;
		
		$this->logApiEvent('GET_SCRIPT_SUCCESS', 'INFO', ['domain' => $domain]);
		
		$this->respondJson($json);
	}
	
	public function addWebPush() {
		$json = ['success' => false];
		
		if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !$this->validate()) {
			$json['error'] = 'permission_or_method';
			$this->respondJson($json);
			return;
		}
		
		$this->load->model('extension/module/yespo');
		
		$domain = $this->request->server['SERVER_NAME'];
		$request_body = [
			'domain'             => $domain,
			'serviceWorkerName'  => 'sw-yespo.js',
			'serviceWorkerPath'  => '/',
			'serviceWorkerScope' => '/'
		];
		
		$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->webpush_domain_url);
		
		if (empty($response['http_code']) || $response['http_code'] != 200) {
			$json['error'] = true;
			$this->logApiEvent('ADD_WEB_PUSH_DOMAIN_FAILED', 'ERROR', ['domain' => $domain, 'requestBody' => $request_body, 'responseBody' => $response]);
			$this->respondJson($json);
			return;
		}
		
		$this->logApiEvent('ADD_WEB_PUSH_DOMAIN_SUCCESS', 'INFO', ['domain' => $domain]);
		
		$script_request_body = ['domain' => $domain];
		$script_response = $this->model_extension_module_yespo->makeRequest($script_request_body, $this->webpush_script_url, 'GET');
		
		if (empty($script_response['script']) || empty($script_response['http_code']) || $script_response['http_code'] != 200) {
			$json['error'] = true;
			$this->logApiEvent('GET_WEB_PUSH_SCRIPT_FAILED', 'ERROR', ['domain' => $domain, 'requestBody' => $script_request_body, 'responseBody' => $script_response]);
			$this->respondJson($json);
			return;
		}
		
		if (!empty($script_response['serviceWorker'])) {
			$root_dir = realpath(DIR_APPLICATION . '..');
			$put_result = file_put_contents($root_dir . '/sw-yespo.js', $script_response['serviceWorker']);
			if ($put_result === false) {
				$json['error'] = true;
				$this->logApiEvent('WRITE_SW_FAILED', 'ERROR', ['domain' => $domain, 'requestBody' => $script_request_body, 'responseBody' => $script_response, 'path' => $root_dir . '/sw-yespo.js']);
				$this->respondJson($json);
				return;
			}
		}
		
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSettingValue('yespo', 'yespo_web_push', '1');
		$this->model_setting_setting->editSettingValue('yespo', 'yespo_web_push_script', $script_response['script']);
		
		$json['success'] = true;
		
		$this->logApiEvent('GET_WEB_PUSH_SCRIPT_SUCCESS', 'INFO', ['domain' => $domain]);
		
		$this->respondJson($json);
	}
	
	private function respondJson($data) {
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}

	private function logApiEvent($message, $level, $data = [], $org_id = null) {
		$this->load->model('extension/module/yespo');
		
		$log_data = [
			'data'      => json_encode($data),
			'message'   => $message,
			'log_level' => $level,
		];
		
		if ($org_id !== null) {
			$this->model_extension_module_yespo->makeLogRequest($log_data, $org_id);
		} else {
			$this->model_extension_module_yespo->makeLogRequest($log_data);
		}
	}
	
	public function toggleAppInbox() {
		$json = ['success' => false];
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->load->model('setting/setting');
			
			$current_status = $this->config->get('yespo_app_inbox');
			
			if (is_null($current_status)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$this->config->get('config_store_id') . "', `code` = 'yespo', `key` = 'yespo_app_inbox', `value` = '0'");
			}
			
			$new_status = $current_status == '1' ? '0' : '1';
			
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_app_inbox', $new_status);
			
			$domain = $this->request->server['SERVER_NAME'];
			
			if ($new_status == '1') {
				$this->logApiEvent('APP_INBOX_ENABLED', 'INFO', ['domain' => $domain]);
			} else {
				$this->logApiEvent('APP_INBOX_DISABLED', 'INFO', ['domain' => $domain]);
			}
			
			$json['success'] = true;
			$json['new_status'] = $new_status;
		}
		
		$this->respondJson($json);
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/yespo')) {
			$this->load->language('extension/module/yespo');
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
	
	public function install() {
		$this->load->model('setting/setting');
		$setting = [
			'yespo_status'           => '',
			'yespo_api_key'          => '',
			'yespo_orgid'            => '',
			'yespo_siteid'           => '',
			'yespo_orgname'          => '',
			'yespo_customers_loaded' => '0',
			'yespo_orders_loaded'    => '0',
			'yespo_customers_page'   => '1',
			'yespo_orders_page'      => '1',
			'yespo_bad_customers'    => '0',
			'yespo_bad_orders'       => '0',
			'yespo_site_script'      => '',
			'yespo_web_push'         => '',
			'yespo_web_push_script'  => '',
			'yespo_app_inbox'        => '0',
			'yespo_customers_limit'  => 2000,
			'yespo_orders_limit'     => 300,
		];
		
		$this->model_setting_setting->editSetting('yespo', $setting);
		
		if (version_compare(VERSION,'3.0.0.0', '>=')) {
			$this->model_setting_setting->editSetting('module_yespo', ['module_yespo_status' => 0]);
		}
		
		$events = $this->getYespoEvents();
		
		foreach ($events as $code => $value) {
			$this->addEvent($code, $value['trigger'], $value['action']);
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "yespo_failed_customers` (`id` INT NOT NULL AUTO_INCREMENT , `customer_id` INT NOT NULL , `attempt_count` TINYINT NOT NULL , `last_attempt` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "yespo_failed_orders` (`id` INT NOT NULL AUTO_INCREMENT , `order_id` INT NOT NULL , `attempt_count` TINYINT NOT NULL , `last_attempt` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
	}
	
	public function uninstall() {
		if ($this->user->hasPermission('modify', 'extension/module/yespo')) {
			$this->load->model('setting/setting');
			
			if (version_compare(VERSION,'3.0.0.0', '>=')) {
				$this->load->model('setting/event');
			} else {
				$this->load->model('extension/event');
			}
			
			$this->model_setting_setting->deleteSetting('yespo');
			
			if (version_compare(VERSION,'3.0.0.0', '>=')) {
				$this->model_setting_setting->deleteSetting('module_yespo');
			}
			
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "yespo_failed_customers`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "yespo_failed_orders`");
			
			$events = $this->getYespoEvents();
			
			foreach ($events as $code => $value) { 
				$this->deleteEvent($code);
			}
		}
	}
	
	private function resetSettings($api_key, $org_id, $org_name) {
		if ($this->user->hasPermission('modify', 'extension/module/yespo')) {
			$this->load->model('setting/setting');
			
			$setting = [
				'yespo_status'           => '0',
				'yespo_api_key'          => $api_key,
				'yespo_orgid'            => $org_id,
				'yespo_siteid'           => '',
				'yespo_orgname'          => $org_name,
				'yespo_customers_loaded' => '0',
				'yespo_orders_loaded'    => '0',
				'yespo_customers_page'   => '1',
				'yespo_orders_page'      => '1',
				'yespo_bad_customers'    => '0',
				'yespo_bad_orders'       => '0',
				'yespo_site_script'      => '',
				'yespo_web_push'         => '',
				'yespo_web_push_script'  => '',
				'yespo_app_inbox'        => '0',
				'yespo_customers_limit'  => 2000,
				'yespo_orders_limit'     => 300,
			];
			
			$this->model_setting_setting->editSetting('yespo', $setting);
			
			if (version_compare(VERSION, '3.0.0.0', '>=')) {
				$this->model_setting_setting->editSetting('module_yespo', ['module_yespo_status' => 0]);
			}
		}
	}
	
	private function addEvent($code, $trigger, $action) {
		if (version_compare(VERSION, '3.0.0.0', '>=')) {
			$this->load->model('setting/event');
			$this->model_setting_event->deleteEventByCode($code);
			$this->model_setting_event->addEvent($code, $trigger, $action, 1, 0);
		} else {
			$this->load->model('extension/event');
			$this->model_extension_event->deleteEvent($code);
			$this->model_extension_event->addEvent($code, $trigger, $action, 1);
		}
	}
	
	private function deleteEvent($code) {
		if (version_compare(VERSION, '3.0.0.0', '>=')) {
			$this->load->model('setting/event');
			$this->model_setting_event->deleteEventByCode($code);
		} else {
			$this->load->model('extension/event');
			$this->model_extension_event->deleteEvent($code);
		}
	}

	public function disconnect() { 
		if ($this->user->hasPermission('modify', 'extension/module/yespo')) {
			$token_data = $this->getTokenData();
			$this->uninstall();
			$this->install();
			$this->response->redirect($this->url->link('extension/module/yespo', $token_data['token'], true));
		}
	}
	
	private function getYespoEvents() {
		$events = [
			'yespo_add_customer_admin' => [
				'trigger' => 'admin/model/customer/customer/addCustomer/after',
				'action'  => 'extension/module/yespo/addCustomer',
			],
			'yespo_edit_customer_admin' => [
				'trigger' => 'admin/model/customer/customer/editCustomer/after',
				'action'  => 'extension/module/yespo/editCustomer',
			],
			'yespo_delete_customer_admin' => [
				'trigger' => 'admin/model/customer/customer/deleteCustomer/after',
				'action'  => 'extension/module/yespo/deleteCustomer',
			],
			'yespo_add_customer_catalog' => [
				'trigger' => 'catalog/model/account/customer/addCustomer/after',
				'action'  => 'extension/module/yespo/addCustomer',
			],
			'yespo_edit_customer_catalog' => [
				'trigger' => 'catalog/model/account/customer/editCustomer/after',
				'action'  => 'extension/module/yespo/editCustomer',
			],
			'yespo_order_catalog' => [
				'trigger' => 'catalog/model/checkout/order/addOrderHistory/after',
				'action'  => 'extension/module/yespo/processOrder',
			],
			'yespo_check_bad_orders_catalog' => [
				'trigger' => 'catalog/model/checkout/order/addOrderHistory/after',
				'action'  => 'extension/module/yespo/checkBadOrdersAndCustomers',
			],
		];
		return $events;
	}
}