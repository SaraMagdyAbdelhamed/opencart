<?php
class ControllerRestapiAccountSuccess extends Controller {
	public function index() {
		$this->load->language('account/success');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('restapi/common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('restapi/account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('restapi/account/success')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['logged_in'] = true;

		$this->load->model('account/customer_group');

		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->config->get('config_customer_group_id'));

		if ($customer_group_info && !$customer_group_info['approval']) {
			$data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('restapi/information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'), $this->url->link('restapi/information/contact'));
		}

		$data['button_continue'] = $this->language->get('button_continue');

		if ($this->cart->hasProducts()) {
			$data['continue'] = $this->url->link('restapi/checkout/cart');
		} else {
			$data['continue'] = $this->url->link('restapi/account/account', '', true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($data));
	}
}