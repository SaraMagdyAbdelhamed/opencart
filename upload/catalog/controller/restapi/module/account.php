<?php
class ControllerRestapiModuleAccount extends Controller {
	public function index() {
		$this->load->language('module/account');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_forgotten'] = $this->language->get('text_forgotten');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_password'] = $this->language->get('text_password');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_reward'] = $this->language->get('text_reward');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_recurring'] = $this->language->get('text_recurring');

		$data['logged'] = $this->customer->isLogged();
		$data['register'] = $this->url->link('restapi/account/register', '', true);
		$data['login'] = $this->url->link('restapi/account/login', '', true);
		$data['logout'] = $this->url->link('restapi/account/logout', '', true);
		$data['forgotten'] = $this->url->link('restapi/account/forgotten', '', true);
		$data['account'] = $this->url->link('restapi/account/account', '', true);
		$data['edit'] = $this->url->link('restapi/account/edit', '', true);
		$data['password'] = $this->url->link('restapi/account/password', '', true);
		$data['address'] = $this->url->link('restapi/account/address', '', true);
		$data['wishlist'] = $this->url->link('restapi/account/wishlist');
		$data['order'] = $this->url->link('restapi/account/order', '', true);
		$data['download'] = $this->url->link('restapi/account/download', '', true);
		$data['reward'] = $this->url->link('restapi/account/reward', '', true);
		$data['return'] = $this->url->link('restapi/account/return', '', true);
		$data['transaction'] = $this->url->link('restapi/account/transaction', '', true);
		$data['newsletter'] = $this->url->link('restapi/account/newsletter', '', true);
		$data['recurring'] = $this->url->link('restapi/account/recurring', '', true);

		return $data;
	}
}