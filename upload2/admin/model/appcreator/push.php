<?php
class ModelAppcreatorPush extends Model {
    public function addPush($POST)
    {
        $data = array();
        $data['Message'] = $POST['Message'];
        $data['Type'] = $POST['Device_Type'];
        $data['Module_ID'] = $POST['Module_ID'];
        $data['Id_Num'] = $POST['Selectvarid'];
        $data['Number'] = 1;
        $data['Picture'] = $POST['Picture'];
        $data['Sound'] = $POST['Sound'];
        $data['Action_ID'] = $POST['Action_ID'];
        $data['Send_Time'] = strtotime($POST['Send_Time']);
        
        $this->db->query("INSERT INTO `" . DB_PREFIX . "users_push_archive`(Message, Type, Module_ID, Id_Num, Number, Picture, Sound, Action_ID, Send_Time)VALUES('{$data['Message']}','{$data['Type']}','{$data['Module_ID']}','{$data['Id_Num']}','{$data['Number']}','{$data['Picture']}','{$data['Sound']}','{$data['Action_ID']}','{$data['Send_Time']}')");
        $insert_id = $this->db->getLastId();
        
        if($insert_id)
        {
           $data['Msg_ID'] = $insert_id;
           $this->queuePushMessages($data);
           return $insert_id;
        }else{
            return false;
        }
    }
    
    public function queuePushMessages($data) {
        $counter = 0;
        if ($data['Type'] == 'ios') {
            $type = "AND Type='1'";
        } elseif ($data['Type'] == 'android') {
            $type = "AND Type='2'";
        } else {
            $type = '';
        }
        $devices = $this->db->query("SELECT Type,Token FROM ".DB_PREFIX."users_token WHERE Active='1' $type");
        if ($devices) {
            foreach ($devices->rows as $device) {
                
                $push = array();
                $push['Token'] = $device['Token'];
                //$push['Message'] = $data['Message'];
                $push['Type'] = $device['Type'];
                $push['Msg_ID'] = $data['Msg_ID'];
                $this->db->query("INSERT INTO `" . DB_PREFIX."users_push_queue`(Token, Type, Msg_ID)VALUES('{$push['Token']}','{$push['Type']}','{$push['Msg_ID']}')");
                $counter++;
                
            }
            $this->db->query("UPDATE ".DB_PREFIX."users_push_archive SET Num_Msgs='$counter' WHERE ID='{$data['Msg_ID']}'");
        }
    }
    
    function update($POST, $id)
    {
        $table_name = DB_PREFIX . 'users_push_archive';
        $data = array();
        $data['Message'] = $POST['Message'];
        $data['Number'] = 1;
        $data['Picture'] = $POST['Picture'];
        $data['Sound'] = $POST['Sound'];
        $data['Send_Time'] = strtotime($POST['Send_Time']);
        $update = $this->db->query("UPDATE ".$table_name." SET Message='{$data['Message']}',Number='{$data['Number']}',Picture='{$data['Picture']}',Sound='{$data['Sound']}',Send_Time='{$data['Send_Time']}' WHERE ID='{$id}'");
        if($update)
        {
            return true;
        }
        return false;
    }
    
    public function getPushList($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "users_push_archive`";

        $sort_data = array(
            'Message',
            'Type',
            'Num_Msgs',
            'Module_ID',
            'Send_Time',
            'Finished'
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
    
    public function getTotalPush() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "users_push_archive`");

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
    
    function get_device_name($val) {
        switch ($val) {
            case "all":
                return "كل الاجهزة";
            case "ios":
                return "الايفون";
            case "android":
                return "الاندرويد";
        }
    }
    
    public function deletePush($row_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "users_push_archive` WHERE ID = '" . (int) $row_id . "'");
    }

    public function getPush($row_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "users_push_archive` WHERE ID = '" . (int) $row_id . "'");

        return $query->row;
    }
    
    public function saveSettings($data)
    {
        $this->load->model('setting/setting');
        if($this->model_setting_setting->getSettingValue("robo_google_push_api_key") != null)
        {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value`='".$data["google_push_api_key"]."' WHERE `key`='robo_google_push_api_key'");
        }else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting(`key`,`value`,`code`,`serialized`)VALUES('robo_google_push_api_key','" . $data["google_push_api_key"] . "','robo_google_push_api_key',0)");
        }
        if($this->model_setting_setting->getSettingValue("robo_apple_cert_file") != null)
        {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value`='".$data["apple_cert_file"]."' WHERE `key`='robo_apple_cert_file'");
        }else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting(`key`,`value`,`code`,`serialized`)VALUES('robo_apple_cert_file','" . $data["apple_cert_file"] . "','robo_apple_cert_file',0)");
        }
        if($this->model_setting_setting->getSettingValue("robo_apple_pass_phrase") != null)
        {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value`='".$data["apple_pass_phrase"]."' WHERE `key`='robo_apple_pass_phrase'");
        }else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting(`key`,`value`,`code`,`serialized`)VALUES('robo_apple_pass_phrase','" . $data["apple_pass_phrase"] . "','robo_apple_pass_phrase',0)");
        }
        if($this->model_setting_setting->getSettingValue("robo_apple_feedback_server") != null)
        {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value`='".$data["apple_feedback_server"]."' WHERE `key`='robo_apple_feedback_server'");
        }else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting(`key`,`value`,`code`,`serialized`)VALUES('robo_apple_feedback_server','" . $data["apple_feedback_server"] . "','robo_apple_feedback_server',0)");
        }
        if($this->model_setting_setting->getSettingValue("robo_apple_server") != null)
        {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value`='".$data["apple_server"]."' WHERE `key`='robo_apple_server'");
        }else{
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting(`key`,`value`,`code`,`serialized`)VALUES('robo_apple_server','" . $data["apple_server"] . "','robo_apple_server',0)");
        }
    }
}