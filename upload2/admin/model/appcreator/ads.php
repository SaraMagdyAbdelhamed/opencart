<?php

class ModelAppcreatorAds extends Model {

    public function addAds($data, $other_data) {
        
        $this->db->query("INSERT INTO `" . DB_PREFIX . "users_application_ads`(Title, Action_ID, Img, Active, Link, StartDate, EndDate, Module_ID)VALUES('{$data['Title']}', '{$data['Action_ID']}','{$data['Img']}','{$data['Active']}','{$data['Link']}','{$data['StartDate']}','{$data['EndDate']}', '{$data['Module_ID']}');");

        $last_insert_id = $this->db->getLastId();
        
        $this->load->model('setting/setting');
        $arr_json = array();
        foreach($other_data as $k => $v)
        {
            $arr_json[$last_insert_id][$k] = $v;
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting(`key`,`value`,`code`,`serialized`)VALUES('" . "robo_app_ads_opts_".$last_insert_id . "','" . json_encode($arr_json) . "','" . "robo_app_ads_opts_".$last_insert_id . "',1)");

        return $last_insert_id;
    }

    public function editAds($row_id, $data, $other_data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "users_application_ads` "
                . "SET Title = '" . $data['Title'] . "',"
                . "Action_ID='" . $data['Action_ID'] . "',"
                . "Img='" . $data['Img'] . "',"
                . "Active='" . $data['Active'] . "',"
                . "Link='" . $data['Link'] . "',"
                . "StartDate='" . $data['StartDate'] . "',"
                . "EndDate='" . $data['EndDate'] . "',"
                . "Module_ID='" . $data['Module_ID'] . "' WHERE ID = '" . (int) $row_id . "'");
        $this->load->model('setting/setting');
        $arr_json = array();
        foreach($other_data as $k => $v)
        {
            $arr_json[$row_id][$k] = $v;
        }
        if($this->model_setting_setting->getSettingValue("robo_app_ads_opts_".$row_id) != null)
        {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value`='".json_encode($arr_json)."' WHERE `key`='"."robo_app_ads_opts_".$row_id."'");
        }else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting(`key`,`value`,`code`,`serialized`)VALUES('" . "robo_app_ads_opts_".$row_id . "','" . json_encode($arr_json) . "','" . "robo_app_ads_opts_".$row_id . "',1)");
        }
    }

    public function deleteAds($row_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "users_application_ads` WHERE ID = '" . (int) $row_id . "'");
    }

    public function getAds($row_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "users_application_ads` WHERE ID = '" . (int) $row_id . "'");

        return $query->row;
    }

    public function getAdsList($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "users_application_ads`";

        $sort_data = array(
            'Title',
            'Module_ID',
            'Active',
            'Count',
            'Link',
            'Action_ID',
            'StartDate',
            'EndDate'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY ID";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalAds() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "users_application_ads`");

        return $query->row['total'];
    }

    function getModuleName($value) {
        $out = "";
        switch ($value) {
            case 1:
                $out .= "الاخبار";
                break;
            case 3:
                $out .= "الفيديو";
                break;
            case 4:
                $out .= "الصور";
                break;
            case 6:
                $out .= "الصفحات";
                break;
            case 5:
                $out .= "المنتجات";
                break;
            case 47:
                $out .= "الاستقتاء";
                break;
        }
        return $out;
    }
}