<?php
class ControllerRestapiCommonLocal extends Controller {
	public function index() {
	
		$this->load->language('common/header');

		$data['text_home'] = $this->language->get('text_home');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_all'] = $this->language->get('text_all');
		$data['text_search'] = $this->language->get('text_search');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_order'] = $this->language->get('text_order');

		$this->load->language('common/footer');

		$data['text_information'] = $this->language->get('text_information');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_extra'] = $this->language->get('text_extra');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_sitemap'] = $this->language->get('text_sitemap');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_voucher'] = $this->language->get('text_voucher');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_special'] = $this->language->get('text_special');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_newsletter'] = $this->language->get('text_newsletter');

        $this->load->language('account/account');

		$data['text_my_account'] = $this->language->get('text_my_account');
		$data['text_my_orders'] = $this->language->get('text_my_orders');
		$data['text_my_newsletter'] = $this->language->get('text_my_newsletter');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_password'] = $this->language->get('text_password');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_reward'] = $this->language->get('text_reward');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_recurring'] = $this->language->get('text_recurring');

		$this->load->language('product/category');

		$data['text_product'] = $this->language->get('text_product');

		$this->load->language('module/category');

		$data['text_categories'] = $this->language->get('heading_title');

		$this->load->language('module/account');

		$data['text_register']    = $this->language->get('text_register');
		$data['text_login']       = $this->language->get('text_login');
		$data['text_logout']      = $this->language->get('text_logout');
		$data['text_forgotten']   = $this->language->get('text_forgotten');
		$data['text_account']     = $this->language->get('text_account');
		$data['text_edit']        = $this->language->get('text_edit');
		$data['text_password']    = $this->language->get('text_password');
		$data['text_address']     = $this->language->get('text_address');
		$data['text_wishlist']    = $this->language->get('text_wishlist');
		$data['text_order']       = $this->language->get('text_order');
		$data['text_download']    = $this->language->get('text_download');
		$data['text_reward']      = $this->language->get('text_reward');
		$data['text_return']      = $this->language->get('text_return');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_newsletter']  = $this->language->get('text_newsletter');
		$data['text_recurring']   = $this->language->get('text_recurring');

		$this->load->language('checkout/checkout');

		$data['checkout_title'] = $this->language->get('heading_title');

		$data['text_register']                  = $this->language->get('Register Account');
$data['text_guest']                     = $this->language->get('Guest Checkout');
$data['text_checkout']                  = $this->language->get('Checkout Options');
$data['text_cart']                      = $this->language->get('Shopping Cart');

$data['error_shipping'] = $this->language->get('error_shipping');
$data['error_payment'] = $this->language->get('error_payment');
$data['error_country'] = $this->language->get('error_country');
$data['error_login'] = $this->language->get('error_login');
$data['error_agree'] = $this->language->get('error_agree');


$this->load->language('account/order');

$data['text_order']            = $this->language->get('Order Information');
$data['text_order_detail']     = $this->language->get('Order Details');
$data['text_shipping_address'] = $this->language->get('Shipping Address');
$data['text_shipping_method']  = $this->language->get('Shipping Method');
$data['text_payment_address']  = $this->language->get('Payment Address');
$data['text_payment_method']   = $this->language->get('Payment Method');

$this->load->language('total/coupon');

		$data['heading_coupon'] = $this->language->get('heading_title');
		$data['text_success'] = $this->language->get('text_success');
		$data['error_coupon'] = $this->language->get('error_coupon');
		$data['error_empty'] = $this->language->get('error_empty');




$data['text_edit_address']   = "Edit Address";
$data['text_new_address']   = "New Address";
$data['text_welcome']   = "Welcome";
$data['text_products']   = "Product";
$data['text_product_details']   = "Product Details";
$data['text_return_product']   = "Return Product";
$data['text_my_address']   = "My Address";
$data['text_register_account']   = "Register";
$data['text_buy_now']   = "Buy Now";
$data['text_menu']   = "Menu";
$data['text_add']   = "ADD";
$data['text_success']   = "SUCCESS";
$data['text_error']   = "ERROR";
$data['text_apply_coupon']   = "Apply Coupon";
$data['text_loading']   = "Loading...";
$data['text_button_continue']   = "Continue";
$data['text_same_for_shipping']   = "Same for Shipping";
$data['text_confirm_order']   = "Confirm Order";
$data['text_login_success']   = "Success: You have Logged In";
$data['text_error_return_reason']   = "Please select Reason for Return";
$data['text_error_product_opened']   = "Please select product is opened or not";
$data['text_success_register']   = "Success: Your Account Has been Created";
$data['text_edit']   = "Edit";

$data['text_trash']   = "Trash";

		return $data;
		
	}
	
}
