<?php
class ModelExtensionRestApi extends Model {

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mstoreapp` (
			`mstoreapp_order_id` int(11) NOT NULL AUTO_INCREMENT,
			`mstoreapp_license_key` CHAR(16) NOT NULL DEFAULT '0',
			`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY (`mstoreapp_order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
	}

	public function addLicense($license = "qwerasdfzxcv1qaz2wsx4rfv") {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "mstoreapp` SET `mstoreapp_license_key` = '" . $this->db->escape($license) . "', `date_added` = now()");

	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mstoreapp`");
	}


}