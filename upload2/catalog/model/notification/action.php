<?php
class ModelNotificationAction extends Model {
	public function addAction($product_id,$action_id,$user_id) {
		$query = $this->db->query( "INSERT INTO `" . DB_PREFIX . "users_actions` SET `product_id` = '" . (int)$product_id . "',`action_id` = '" . $action_id . "',`user_id` = '" . $user_id . "'");

		return $this->db->getLastId();
	}
}