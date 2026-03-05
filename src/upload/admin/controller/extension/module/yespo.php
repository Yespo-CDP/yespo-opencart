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
		
		if (version_compare(VERSION,'3.0.0.0', '>=')) {
			$token = 'user_token=' . $this->session->data['user_token'];
			$extension = 'marketplace/extension';
			$data['token'] = $token;
		} else {
			$token = 'token=' . $this->session->data['token'];
			$extension = 'extension/extension';
			$data['token'] = $token;
		}
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['action']) && $this->request->post['action'] == 'disconnect') {
				$this->uninstall();
				$this->install();
				$this->response->redirect($this->url->link('extension/module/yespo', $token, true));
			}
		}
		
		$language_data = $this->language->all();
		foreach ($language_data as $key => $value) {
			$data[$key] = $value;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token, true)
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($extension, $token . '&type=module', true)
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/yespo', $token, true)
		];

		$data['action'] = $this->url->link('extension/module/yespo', $token, true);
		$data['cancel'] = $this->url->link($extension, $token . '&type=module', true);

		$data['yespo_status'] = $this->config->get('yespo_status');
		$data['yespo_api_key'] = $this->config->get('yespo_api_key');
		$data['yespo_orgname'] = $this->config->get('yespo_orgname');
		
		$data['yespo_site_script'] = $this->config->get('yespo_site_script');
		$data['yespo_web_push'] = $this->config->get('yespo_web_push');

		$data['yespo_customers_page'] = $this->config->get('yespo_customers_page') ? $this->config->get('yespo_customers_page') : 1;
		$data['yespo_orders_page'] = $this->config->get('yespo_orders_page') ? $this->config->get('yespo_orders_page') : 1;

		$data['yespo_customers_loaded'] = $this->config->get('yespo_customers_loaded');
		$data['yespo_orders_loaded'] = $this->config->get('yespo_orders_loaded');
		
		$data['yespo_bad_customers'] = $this->config->get('yespo_bad_customers') ? (int)$this->config->get('yespo_bad_customers') : 0;
		$data['yespo_bad_orders'] = $this->config->get('yespo_bad_orders') ? (int)$this->config->get('yespo_bad_orders') : 0;

		$data['customers_limit'] = $this->config->get('yespo_customers_limit') ? $this->config->get('yespo_customers_limit') : ($this->config->get('customers_limit') ? $this->config->get('customers_limit') : 2000);
		$data['orders_limit'] = $this->config->get('yespo_orders_limit') ? $this->config->get('yespo_orders_limit') : ($this->config->get('orders_limit') ? $this->config->get('orders_limit') : 300);

		$this->load->model('extension/module/yespo');
		$data['total_customers_db'] = $this->model_extension_module_yespo->getTotalCustomers();
		$data['total_orders_db'] = $this->model_extension_module_yespo->getTotalOrders();		
		
		$data['check_api_key'] = html_entity_decode($this->url->link('extension/module/yespo/checkApiKey', $token, true));
		$data['load_customers'] = html_entity_decode($this->url->link('extension/module/yespo/loadCustomers', $token, true));
		$data['load_orders'] = html_entity_decode($this->url->link('extension/module/yespo/loadOrders', $token, true));
		$data['get_site_script'] = html_entity_decode($this->url->link('extension/module/yespo/getSiteScript', $token, true));
		$data['add_web_push'] = html_entity_decode($this->url->link('extension/module/yespo/addWebPush', $token, true));
		$data['disconnect'] = html_entity_decode($this->url->link('extension/module/yespo/disconnect', $token, true));
		$data['set_active'] = html_entity_decode($this->url->link('extension/module/yespo/setActive', $token, true));
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/yespo', $data));
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
				$log_data = [
					'orgId'        => $request['orgId'],
					'typeCMS'      => 'OpenCart',
					'errorMessage' => '',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME']]),
					'message'      => 'ADD_API_KEY_SUCCESS',
					'log_level'    => 'INFO'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
				
				$json['success'] = true;
				$json['org_name'] = $request['organisationName'];
				$json['orgid'] = $request['orgId'];
				
				$this->load->model('setting/setting');
				
				if ($current_api_key !== $api_key) {
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_api_key', $api_key);
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_orgid', $request['orgId']);
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_orgname', $request['organisationName']);
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_loaded', '0');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_loaded', '0');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_customers', '0');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_orders', '0');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_page', '1');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_page', '1');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_site_script', '');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_web_push', '');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_status', '0');
					if (version_compare(VERSION,'3.0.0.0', '>=')) {
						$this->model_setting_setting->editSetting('module_yespo', ['module_yespo_status' => 0]);
					}
					$json['reset'] = true;
				} else {
					$json['reset'] = false;
				}
			} else {
				$json['success'] = false;
				$log_data = [
					'orgId'        => '',
					'typeCMS'      => 'OpenCart',
					'errorMessage' => 'ADD_API_KEY_FAILED',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'responseBody' => $request]),
					'message'      => 'ADD_API_KEY_FAILED',
					'log_level'    => 'INFO'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
			}
		} else {
			$json['success'] = false;
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function setActive() {
		$json = ['success' => false];
		if ($this->validate()) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_status', '1');
			if (version_compare(VERSION,'3.0.0.0', '>=')) {
				$this->model_setting_setting->editSetting('module_yespo', ['module_yespo_status' => 1]);
			}
			$json['success'] = true;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addCustomer($route, $args, $output = null) {
		if (!empty($output)) {
			$customer_id = (int)$output;
			$this->load->model('customer/customer');
			$customer_info = $this->model_customer_customer->getCustomer($customer_id);
			if ($customer_info) {
				$phone = preg_replace('/[^0-9]/', '', (string)$customer_info['telephone']);
				$request_body = [
					'externalCustomerId' => $customer_info['customer_id'],
					'firstName'  => $customer_info['firstname'],
					'lastName'   => $customer_info['lastname'],
					'channels'   => [['type' => 'email', 'value' => $customer_info['email']], ['type' => 'sms', 'value' => $phone]]
				];
				$this->load->model('extension/module/yespo');
				$this->model_extension_module_yespo->makeRequest($request_body, $this->contact_url);
			}
		}
	}
	
	public function editCustomer($route, $args, $output = null) {
		$customer_id = isset($args[0]) ? (int)$args[0] : 0;
		
		if ($customer_id) {
			$this->load->model('customer/customer');
			$customer_info = $this->model_customer_customer->getCustomer($customer_id);
			if ($customer_info) {
				$phone = preg_replace('/[^0-9]/', '', (string)$customer_info['telephone']);
				$request_body = [
					'externalCustomerId' => $customer_info['customer_id'],
					'firstName'  => $customer_info['firstname'],
					'lastName'   => $customer_info['lastname'],
					'channels'   => [['type' => 'email', 'value' => $customer_info['email']], ['type' => 'sms', 'value' => $phone]]
				];
				$this->load->model('extension/module/yespo');
				$this->model_extension_module_yespo->makeRequest($request_body, $this->contact_url);
			}
		}
	}
	
	public function deleteCustomer($route, $args, $output = null) {
		$customer_id = isset($args[0]) ? (int)$args[0] : 0;
		
		if ($customer_id) {
			$request_body = [
				'externalCustomerId' => $customer_id,
				'erase'              => true
			];
			$this->load->model('extension/module/yespo');
			$this->model_extension_module_yespo->makeRequest($request_body, $this->contact_url, 'DELETE');
		}
	}
	
	public function loadCustomers() {
		$this->load->language('extension/module/yespo');
		$this->load->model('extension/module/yespo');
		$this->load->model('setting/setting');

		$json = [];
		
		$page = isset($this->request->post['page']) ? (int)$this->request->post['page'] : 1;
		$limit = 2000;
		$start = ($page - 1) * $limit;
		
		if (empty($this->session->data['yespo_total_customers'])) {
			$json['total_customers'] = $this->model_extension_module_yespo->getTotalCustomers();
			$this->session->data['yespo_total_customers'] = $json['total_customers'];
		} else {
			$json['total_customers'] = $this->session->data['yespo_total_customers'];
		}
		
		$customers = $this->model_extension_module_yespo->getCustomers($start, $limit);
		$total_customers_in_batch = is_array($customers) ? count($customers) : 0;

		if ($total_customers_in_batch > 0) {
			$contacts_payload = [];

			foreach ($customers as $customer) {
				$phone = preg_replace('/[^0-9]/', '', (string)$customer['telephone']);
				$contacts_payload[] = [
					'externalCustomerId' => $customer['customer_id'],
					'email'      => $customer['email'],
					'firstName'  => $customer['firstname'],
					'lastName'   => $customer['lastname'],
					'phone'      => $phone,
					'channels'   => [['type' => 'email', 'value' => $customer['email']], ['type' => 'sms', 'value' => $phone]]
				];
			}

			$request_body = [
				'contacts'      => $contacts_payload,
				'dedupeOn'      => 'externalCustomerId'
			];
			
			$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->contacts_url);
			
			$failed_count = 0;
			if (isset($response['failedContacts']) && is_array($response['failedContacts'])) {
				$failed_count = count($response['failedContacts']);
			}

			if (isset($response['id'])) {
				$json['success'] = true;
				$json['processed_count'] = count($contacts_payload);
				$json['failed_count'] = $failed_count;
				
				$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_page', $page);
				
				if ($page == 1) {
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_customers', $failed_count);
				} else {
					$current_bad = (int)$this->config->get('yespo_bad_customers');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_customers', $current_bad + $failed_count);
				}

				$batch_end = (($start + $limit) > $json['total_customers']) ? $json['total_customers'] : ($start + $limit);
				$batch_range = $start . '-' . $batch_end;
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => '',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'batchRange' => $batch_range, 'responseBody' => $response]),
					'message'      => 'SEND_CONTACTS_BULK_SUCCESS',
					'log_level'    => 'INFO' 
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);

				if ($total_customers_in_batch == $limit) {
					$json['next_page'] = $page + 1;
				} else {
					$json['next_page'] = false;
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_loaded', '1');
				}

			} elseif (!empty($response)) {
				$json['success'] = false;
				if (isset($response['http_code']) && $response['http_code'] == 401) {
					$json['error'] = $this->language->get('error_api_key_2');
				} else {
					$json['error'] = $this->language->get('error_connection');
				}
				
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => 'Upload Error',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'responseBody' => $response]),
					'message'      => 'SEND_CONTACTS_BULK_FAILED',
					'log_level'    => 'ERROR'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
			}

		} else {
			$json['success'] = true;
			$json['processed_count'] = 0;
			$json['failed_count'] = 0;
			$json['next_page'] = false;
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_customers_loaded', '1');
			
			$log_data = [
				'orgId'        => (int)$this->config->get('yespo_orgid'),
				'typeCMS'      => 'OpenCart',
				'errorMessage' => '',
				'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME']]),
				'message'      => 'SEND_CONTACTS_BULK_SUCCESS',
				'log_level'    => 'INFO'
			];
			$this->model_extension_module_yespo->makeLogRequest($log_data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function loadOrders() {
		$this->load->language('extension/module/yespo');
		$this->load->model('extension/module/yespo');
		$this->load->model('sale/order'); 
		$this->load->model('setting/setting');
		
		$json = [];
		
		$page = isset($this->request->post['page']) ? (int)$this->request->post['page'] : 1;
		$limit = 300;
		$start = ($page - 1) * $limit;
		
		if (empty($this->session->data['yespo_total_orders'])) {
			$json['total_orders'] = $this->model_extension_module_yespo->getTotalOrders();
			$this->session->data['yespo_total_orders'] = $json['total_orders'];
		} else {
			$json['total_orders'] = $this->session->data['yespo_total_orders'];
		}
		
		$orders = $this->model_extension_module_yespo->getOrders($start, $limit);
		$total_in_batch = is_array($orders) ? count($orders) : 0;

		$in_progress_status = $this->config->get('config_processing_status');
		$delivered_status = $this->config->get('config_complete_status');

		if ($total_in_batch > 0) {
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
					'externalOrderId'    => $order['order_id'],
					'totalCost'          => $this->currency->format($order['total'], $this->config->get('config_currency'), '', false),
					'status'             => $status,
					'date'               => gmdate('Y-m-d\TH:i:s\Z', strtotime($order['date_added'])),
					'currency'           => $this->config->get('config_currency'),
					'email'              => $order['email'],
					'phone'              => preg_replace('/[^0-9]/', '', (string)$order['telephone']),
					'firstName'          => $order['firstname'],
					'lastName'           => $order['lastname'],
					'deliveryMethod'     => $order['shipping_method'],
					'paymentMethod'      => $order['payment_method'],
					'items'              => $items
				];
				if ($order['customer_id'] > 0) {
					$order_data['externalCustomerId'] = $order['customer_id'];
				}
				$orders_payload[] = $order_data;
			}

			$request_body = [
				'orders' => $orders_payload
			];

			$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->orders_url);
			$failed_count = 0;
			if (isset($response['failedOrders']) && is_array($response['failedOrders'])) {
				$failed_count = count($response['failedOrders']);
			}
			
			if (!empty($response) && isset($response['http_code']) && $response['http_code'] == 200) {
				$json['success'] = true;
				$json['processed_count'] = count($orders_payload);
				$json['failed_count'] = $failed_count;
				
				$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_page', $page);
				
				if ($page == 1) {
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_orders', $failed_count);
				} else {
					$current_bad = (int)$this->config->get('yespo_bad_orders');
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_bad_orders', $current_bad + $failed_count);
				}

				$batch_end = ($start + $limit) > $json['total_orders'] ? $json['total_orders'] : ($start + $limit);
				$batch_range = $start . '-' . $batch_end;
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => '',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'batchRange' => $batch_range, 'responseBody' => $response]),
					'message'      => 'SEND_ORDERS_INFO',
					'log_level'    => 'INFO'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);

				if ($total_in_batch == $limit) {
					$json['next_page'] = $page + 1;
				} else {
					$json['next_page'] = false;
					$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_loaded', '1');
				}

			} elseif (!empty($response)) {
				$json['success'] = false;
				if (isset($response['http_code']) && $response['http_code'] == 401) {
					$json['error'] = $this->language->get('error_api_key_2');
				} else {
					$json['error'] = $this->language->get('error_connection');
				}
				
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => 'Order Upload Error',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'responseBody' => $response]),
					'message'      => 'SEND_ORDERS_BULK_FAILED',
					'log_level'    => 'ERROR'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
			}

		} else {
			$json['success'] = true;
			$json['processed_count'] = 0;
			$json['failed_count'] = 0;
			$json['next_page'] = false;
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_orders_loaded', '1');

			$log_data = [
				'orgId'        => (int)$this->config->get('yespo_orgid'),
				'typeCMS'      => 'OpenCart',
				'errorMessage' => '',
				'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME']]),
				'message'      => 'SEND_ORDERS_BULK_SUCCESS',
				'log_level'    => 'INFO'
			];
			$this->model_extension_module_yespo->makeLogRequest($log_data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getSiteScript() {
		$this->load->model('extension/module/yespo');
		$this->load->model('setting/setting');
		
		$json = [];
		$domain = $this->request->server['SERVER_NAME'];
		
		$request_body = ['domain' => $domain];
		
		$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->domains_url);
		
		if (!empty($response['http_code']) && in_array($response['http_code'], [200, 201])) {
			$log_data = [
				'orgId'        => (int)$this->config->get('yespo_orgid'),
				'typeCMS'      => 'OpenCart',
				'errorMessage' => '',
				'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]),
				'message'      => 'ADD_DOMAIN_SUCCESS',
				'log_level'    => 'INFO'
			];
			$this->model_extension_module_yespo->makeLogRequest($log_data);
			$this->model_setting_setting->editSettingValue('yespo', 'yespo_siteid', $response['siteId']);	
			$json['siteid'] = $response['siteId'];	
			
			$response = $this->model_extension_module_yespo->makePlainRequest($request_body, $this->site_script_url);
			if (!empty($response['text']) && !empty($response['http_code']) && in_array($response['http_code'], [200])) {
				$this->model_setting_setting->editSettingValue('yespo', 'yespo_site_script', $response['text']);
				$json['site_script'] = $response['text'];
				$json['success'] = true; 
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => '',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]),
					'message'      => 'GET_SCRIPT_SUCCESS',
					'log_level'    => 'INFO'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
			} else {
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => '',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]),
					'message'      => 'GET_SCRIPT_FAILED',
					'log_level'    => 'ERROR'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
			}
		} else {
			$json['error'] = true;
			$log_data = [
				'orgId'        => (int)$this->config->get('yespo_orgid'),
				'typeCMS'      => 'OpenCart',
				'errorMessage' => '',
				'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]),
				'message'      => 'ADD_DOMAIN_FAILED',
				'log_level'    => 'ERROR'
			];
			$this->model_extension_module_yespo->makeLogRequest($log_data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addWebPush() {
		$this->load->model('extension/module/yespo');

		$json = [];
		$domain = $this->request->server['SERVER_NAME'];
		$request_body = [
			'domain' => $domain,
			'serviceWorkerName' => 'sw-yespo.js',
			'serviceWorkerPath' => '/',
			'serviceWorkerScope' => '/'
		];
		$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->webpush_domain_url);
		
		if (!empty($response['http_code']) && in_array($response['http_code'], [200])) {
			$log_data = [
				'orgId'        => (int)$this->config->get('yespo_orgid'),
				'typeCMS'      => 'OpenCart',
				'errorMessage' => '',
				'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]),
				'message'      => 'ADD_WEB_PUSH_DOMAIN_SUCCESS',
				'log_level'    => 'INFO'
			];
			$this->model_extension_module_yespo->makeLogRequest($log_data); 
			$request_body = [
				'domain' => $domain
			];
			$response = $this->model_extension_module_yespo->makeRequest($request_body, $this->webpush_script_url, 'GET');

			if (!empty($response['script']) && !empty($response['http_code']) && in_array($response['http_code'], [200])) {
				$this->load->model('setting/setting'); 
				$this->model_setting_setting->editSettingValue('yespo', 'yespo_web_push', '1');
				$this->model_setting_setting->editSettingValue('yespo', 'yespo_web_push_script', $response['script']);
				$json['web_push_script'] = $response['script'];
				if (!empty($response['serviceWorker'])) {
					$root_dir = realpath(DIR_APPLICATION . '..');
					file_put_contents($root_dir . '/sw-yespo.js', $response['serviceWorker']);
				}
				$json['success'] = true; 
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => '',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]),
					'message'      => 'GET_WEB_PUSH_SCRIPT_SUCCESS',
					'log_level'    => 'INFO'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
			} else {
				$json['error'] = true;
				$log_data = [
					'orgId'        => (int)$this->config->get('yespo_orgid'),
					'typeCMS'      => 'OpenCart',
					'errorMessage' => '',
					'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'requestBody' => $request_body, 'responseBody' => $response]),
					'message'      => 'GET_WEB_PUSH_SCRIPT_FAILED',
					'log_level'    => 'ERROR'
				];
				$this->model_extension_module_yespo->makeLogRequest($log_data);
			}
		} else {
			$json['error'] = true;
			$log_data = [
				'orgId'        => (int)$this->config->get('yespo_orgid'),
				'typeCMS'      => 'OpenCart',
				'errorMessage' => '',
				'data'         => json_encode(['domain' => $this->request->server['SERVER_NAME'], 'responseBody' => $response]),
				'message'      => 'ADD_DOMAIN_FAILED',
				'log_level'    => 'ERROR'
			];
			$this->model_extension_module_yespo->makeLogRequest($log_data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/yespo')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
	
	public function install() {
		$this->load->model('setting/setting');
		$setting = [
			'yespo_status' => '',
			'yespo_api_key' => '',
			'yespo_orgid' => '',
			'yespo_siteid' => '',
			'yespo_orgname' => '',
			'yespo_customers_loaded' => '0',
			'yespo_orders_loaded' => '0',
			'yespo_customers_page' => '1',
			'yespo_orders_page' => '1',
			'yespo_bad_customers' => '0',
			'yespo_bad_orders' => '0',
			'yespo_site_script' => '',
			'yespo_web_push' => '',
			'yespo_web_push_script' => ''
		];
		
		$this->model_setting_setting->editSetting('yespo', $setting);
		
		if (version_compare(VERSION,'3.0.0.0', '>=')) {
			$this->model_setting_setting->editSetting('module_yespo', ['module_yespo_status' => 0]);
			$this->load->model('setting/event');
		} else {
			$this->load->model('extension/event');
		}

		$events = $this->getYespoEvents();
		foreach ($events as $code => $value) {
			if (version_compare(VERSION,'3.0.0.0', '>=')) {
				$this->model_setting_event->deleteEventByCode($code);
				$this->model_setting_event->addEvent($code, $value['trigger'], $value['action'], 1, 0);
			} else {
				$this->model_extension_event->deleteEvent($code);
				$this->model_extension_event->addEvent($code, $value['trigger'], $value['action'], 1);
			}
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
				if (version_compare(VERSION,'3.0.0.0', '>=')) {
					$this->model_setting_event->deleteEventByCode($code);
				} else {
					$this->model_extension_event->deleteEvent($code);
				}
			}
		}
	}
	
	public function disconnect() { 
		if ($this->user->hasPermission('modify', 'extension/module/yespo')) {
			if (version_compare(VERSION,'3.0.0.0', '>=')) {
				$token = 'user_token=' . $this->session->data['user_token'];
			} else {
				$token = 'token=' . $this->session->data['token'];
			}
			$this->uninstall();
			$this->install();
			$this->response->redirect($this->url->link('extension/module/yespo', $token, true));
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