<?php
class ControllerRestapiPaymentCod extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['text_loading'] = $this->language->get('text_loading');

		$data['continue'] = $this->url->link('restapi/checkout/success');

		return $data;
	}

	public function confirm() {
		if ($this->session->data['payment_method']['code'] == 'cod') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cod_order_status_id'));

        $data = $this->load->controller('restapi/checkout/success');



		} else $data = $this->load->controller('restapi/checkout/failure');

$this->response->addHeader('Content-Type: application/json');
$this->response->setOutput(json_encode($data));

	}
}
