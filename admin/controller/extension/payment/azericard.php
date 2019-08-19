<?php
class ControllerExtensionPaymentAzericard extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/azericard');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('azericard', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');

		$data['entry_testing'] = $this->language->get('entry_testing');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['desc'])) {
			$data['error_desc'] = $this->error['desc'];
		} else {
			$data['error_desc'] = '';
		}

		if (isset($this->error['merch_name'])) {
			$data['error_merch_name'] = $this->error['merch_name'];
		} else {
			$data['error_merch_name'] = '';
		}

		if (isset($this->error['merch_url'])) {
			$data['error_merch_url'] = $this->error['merch_url'];
		} else {
			$data['error_merch_url'] = '';
		}

		if (isset($this->error['terminal'])) {
			$data['error_terminal'] = $this->error['terminal'];
		} else {
			$data['error_terminal'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/azericard', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/azericard', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		if (isset($this->request->post['azericard_testing'])) {
			$data['azericard_testing'] = $this->request->post['azericard_testing'];
		} else {
			$data['azericard_testing'] = $this->config->get('azericard_testing');
		}

		if (isset($this->request->post['azericard_desc'])) {
			$data['azericard_desc'] = $this->request->post['azericard_desc'];
		} else {
			$data['azericard_desc'] = $this->config->get('azericard_desc');
		}

		if (isset($this->request->post['azericard_merch_name'])) {
			$data['azericard_merch_name'] = $this->request->post['azericard_merch_name'];
		} else {
			$data['azericard_merch_name'] = $this->config->get('azericard_merch_name');
		}

		if (isset($this->request->post['azericard_merch_url'])) {
			$data['azericard_merch_url'] = $this->request->post['azericard_merch_url'];
		} else {
			$data['azericard_merch_url'] = $this->config->get('azericard_merch_url');
		}

		if (isset($this->request->post['azericard_terminal'])) {
			$data['azericard_terminal'] = $this->request->post['azericard_terminal'];
		} else {
			$data['azericard_terminal'] = $this->config->get('azericard_terminal');
		}

		if (isset($this->request->post['azericard_email'])) {
			$data['azericard_email'] = $this->request->post['azericard_email'];
		} else {
			$data['azericard_email'] = $this->config->get('azericard_email');
		}

		if (isset($this->request->post['azericard_key'])) {
			$data['azericard_key'] = $this->request->post['azericard_key'];
		} else {
			$data['azericard_key'] = $this->config->get('azericard_key');
		}

		if (isset($this->request->post['azericard_total'])) {
			$data['azericard_total'] = $this->request->post['azericard_total'];
		} else {
			$data['azericard_total'] = $this->config->get('azericard_total');
		}

		if (isset($this->request->post['azericard_order_status_id'])) {
			$data['azericard_order_status_id'] = $this->request->post['azericard_order_status_id'];
		} else {
			$data['azericard_order_status_id'] = $this->config->get('azericard_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['azericard_geo_zone_id'])) {
			$data['azericard_geo_zone_id'] = $this->request->post['azericard_geo_zone_id'];
		} else {
			$data['azericard_geo_zone_id'] = $this->config->get('azericard_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['azericard_status'])) {
			$data['azericard_status'] = $this->request->post['azericard_status'];
		} else {
			$data['azericard_status'] = $this->config->get('azericard_status');
		}

		if (isset($this->request->post['azericard_sort_order'])) {
			$data['azericard_sort_order'] = $this->request->post['azericard_sort_order'];
		} else {
			$data['azericard_sort_order'] = $this->config->get('azericard_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/azericard', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/azericard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['azericard_desc']) {
			$this->error['desc'] = $this->language->get('error_desc');
		}

		if (!$this->request->post['azericard_merch_name']) {
			$this->error['merch_name'] = $this->language->get('error_merch_name');
		}

		if (!$this->request->post['azericard_merch_url']) {
			$this->error['merch_url'] = $this->language->get('error_merch_url');
		}

		if (!$this->request->post['azericard_terminal']) {
			$this->error['terminal'] = $this->language->get('error_terminal');
		}

		if (!$this->request->post['azericard_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->request->post['azericard_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}

		return !$this->error;
	}
}