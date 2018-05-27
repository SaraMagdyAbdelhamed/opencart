<?php
class ModelNotificationPush extends Model {
	public function addNotification($product_id,$action_id,$user_id) {
		$query = $this->db->query( "INSERT INTO `" . DB_PREFIX . "push_notifications_from_api` SET `product_id` = '" . (int)$product_id . "',`action_id` = '" . $action_id . "',`user_id` = '" . $user_id . "'");

		return $this->db->getLastId();
	}
}