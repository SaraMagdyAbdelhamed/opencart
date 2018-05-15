<?php

class ControllerAppcreatorPushcron extends Controller {

    const TIME_BINARY_SIZE = 4;
    const TOKEN_LENGTH_BINARY_SIZE = 2;
    const DEVICE_BINARY_SIZE = 32;
    const ERROR_RESPONSE_SIZE = 6;
    const ERROR_RESPONSE_COMMAND = 8;
    const STATUS_CODE_INTERNAL_ERROR = 999;

    protected $_aErrorResponseMessages = array(
        0 => 'No errors encountered',
        1 => 'Processing error',
        2 => 'Missing device token',
        3 => 'Missing topic',
        4 => 'Missing payload',
        5 => 'Invalid token size',
        6 => 'Invalid topic size',
        7 => 'Invalid payload size',
        8 => 'Invalid token',
        self::STATUS_CODE_INTERNAL_ERROR => 'Internal error'
    );

    public function onConstruct() {
        
    }

    public function sendPushAction($deviceType) {
        if ($deviceType == 'ios') {
            $iphone_devices['token'] = array($_GET['Token']);
        } else {
            $android_devices = array($_GET['Token']);
        }
        $get = new STDClass();
        $get->Message = $_GET['Message'];
        $get->Action_ID = $_GET['Action_ID'];
        $get->Module_ID = $_GET['Module_ID'];
        $get->Orginal_ID = $_GET['Orginal_ID'];
        $get->Id_Num = $_GET['Id_Num'];
        $get->Picture = $_GET['Picture'];
        $get->Number = $_GET['Number'];
        $get->Sound = $_GET['Sound'];
        if (count($iphone_devices) > 0)
            $this->Notfiy_Send($iphone_devices, 1, $get, true);
        if (count($android_devices) > 0)
            $this->Notfiy_Send($android_devices, 2, $get, true);
        //}
    }

    public function processPushAction() {
        $messageinfo = $this->db->query("SELECT * FROM " . DB_PREFIX . "users_push_archive WHERE Finished IS NOT NULL AND Send_Time<" . time() . " LIMIT 0,1");

        if (!$messageinfo) {
            exit;
        }
        $get = new STDClass();
        $messageinfo = $messageinfo->row;
        $get->ID = $messageinfo['ID'];
        $get->Message = $messageinfo['Message'];
        $get->Action_ID = $messageinfo['Action_ID'];
        $get->Module_ID = $messageinfo['Module_ID'];
        $get->Orginal_ID = $messageinfo['Orginal_ID'];
        $get->Id_Num = $messageinfo['Id_Num'];
        $get->Picture = $messageinfo['Picture'];
        $get->Number = $messageinfo['Number'];
        $get->Sound = $messageinfo['Sound'];

        $messages = $this->db->query("SELECT * FROM " . DB_PREFIX . "users_push_queue WHERE Msg_ID='{$messageinfo['ID']}' ORDER BY Type ASC LIMIT 1000");

        if ($messages->rows) {
            $iphone_devices = $android_devices = array();
            foreach ($messages->rows as $get1) {
                if ($get1['Type'] == 1) {
                    $iphone_devices['token'][] = $get1['Token'];
                    $iphone_devices['id'][] = $get1['ID'];
                } else {
                    $android_devices[] = $get1['Token'];
                    $this->db->query("DELETE FROM " . DB_PREFIX . "users_push_queue WHERE ID='{$get1['ID']}'");
                }
            }

            if (count($iphone_devices) > 0)
                $this->Notfiy_Send($iphone_devices, 1, $get, false, $get->ID);
            if (count($android_devices) > 0)
                $this->Notfiy_Send($android_devices, 2, $get, false, $get->ID);
        }
        else {
            $this->db->query("UPDATE " . DB_PREFIX . "users_push_archive SET Finished='" . time() . "' WHERE ID='$messageinfo[ID]'");
        }
    }

    private function Notfiy_Send($device_token, $device_type, $push, $debug = false, $feedbackid = 0) {
        $this->load->model('setting/setting');
        $apple_cert_file = $this->getSettingValue("robo_apple_cert_file");
        $apple_pass_phrase = $this->getSettingValue("robo_apple_pass_phrase");
        $apple_server = $this->getSettingValue("robo_apple_server");
        $google_push_key = $this->getSettingValue("robo_google_push_api_key");
        if ($device_type == 1) {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $apple_cert_file);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $apple_pass_phrase);
            @ $fp = stream_socket_client($apple_server, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);
            $this->db->query("INSERT INTO " . DB_PREFIX . "users_push_feedback (Device_Type,Feedback) VALUES ('$device_type','$feedbackid')");
            $payload = $this->getPayload($push->Message, $push);
            foreach ($device_token['token'] AS $key => $token) {
                @ $msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
                $resj = fwrite($fp, $msg, strlen($msg));
                unset($device_token['token'][$key]);
                if (!$resj) {
                    @fclose($fp);
                    return true;
                }
                if ($debug) {
                    echo $resj . '<br />' . date('d/m/y h:i:s');
                    exit;
                }
                $this->db->query("DELETE FROM " . DB_PREFIX . "users_push_queue WHERE ID='" . $device_token['id'][$key] . "'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "users_push_feedback (Device_Type) VALUES ('$device_type')");
            }
            fclose($fp);
        } else {

            $url = 'https://android.googleapis.com/gcm/send';
            $payload = new STDClass();
            $payload->message = $push->Message;
            $payload->action_id = $push->Action_ID;
            $payload->module_id = $push->Module_ID;
            $payload->orginal_id = $push->Orginal_ID;
            $payload->id = $push->Id_Num;
            $payload->picture = $push->Picture;
            $payload->number = $push->Number;
            $payload->sound = $push->Sound;
            $fields = array('registration_ids' => $device_token, 'data' => $payload);
            $headers = array('Authorization: key=' . $google_push_key, 'Content-Type: application/json');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
            if ($debug) {
                echo json_encode($fields);
                //echo $result."\n".date('d/m/y h:i:s')."\n".json_encode($payload);
                exit;
            }
            $this->db->query("INSERT INTO " . DB_PREFIX . "users_push_feedback (Tokens,Device_Type,Feedback) VALUES ('" . serialize($device_token) . "','$device_type','$result')");
            curl_close($ch);
        }
    }

    public function processFeedbackAction() {
        $this->load->model('setting/setting');
        $apple_cert_file = $this->getSettingValue("robo_apple_cert_file");
        $apple_pass_phrase = $this->getSettingValue("robo_apple_pass_phrase");
        $apple_feedback_server = $this->getSettingValue("robo_apple_feedback_server");
        $feedbacks = $this->db->query("SELECT * FROM " . DB_PREFIX . "users_push_feedback ORDER BY ID ASC");
        foreach ($feedbacks->rows AS $feedback) {
            if ($feedback['Device_Type'] == 1) {
                $ctx = stream_context_create();
                stream_context_set_option($ctx, 'ssl', 'local_cert', $apple_cert_file);
                stream_context_set_option($ctx, 'ssl', 'passphrase', $apple_pass_phrase);
                @$fpssl = stream_socket_client($apple_feedback_server, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
                if (!$fpssl)
                    break;
                $nFeedbackTupleLen = self::TIME_BINARY_SIZE + self::TOKEN_LENGTH_BINARY_SIZE + self::DEVICE_BINARY_SIZE;
                $sBuffer = '';
                while (!feof($fpssl)) {
                    $sBuffer .= $sCurrBuffer = fread($fpssl, 8192);
                    $nCurrBufferLen = strlen($sCurrBuffer);
                    unset($sCurrBuffer, $nCurrBufferLen);
                    $nBufferLen = strlen($sBuffer);
                    if ($nBufferLen >= $nFeedbackTupleLen) {
                        $nFeedbackTuples = floor($nBufferLen / $nFeedbackTupleLen);
                        for ($i = 0; $i < $nFeedbackTuples; $i++) {
                            $sFeedbackTuple = substr($sBuffer, 0, $nFeedbackTupleLen);
                            $sBuffer = substr($sBuffer, $nFeedbackTupleLen);
                            $aFeedback = $this->_parseBinaryTuple($sFeedbackTuple);
                            $this->db->query("UPDATE " . DB_PREFIX . "users_token SET Active='0' WHERE Token='$aFeedback[deviceToken]'");
                            unset($aFeedback);
                        }
                    }
                    $read = array($fpssl);
                    $null = NULL;
                    $nChangedStreams = stream_select($read, $null, $null, 0, 1000000);
                    if ($nChangedStreams === false) {
                        break;
                    }
                }
            } else {
                $tokens = unserialize($feedback->Tokens);
                $result = json_decode($feedback->Feedback);
                foreach ($result->results AS $key => $status) {
                    if (isset($status->error)) {
                        if ($status->error == 'InvalidRegistration' || $status->error == 'NotRegistered' || $status->error == 'MismatchSenderId') {
                            $this->db->query("UPDATE " . DB_PREFIX . "users_token SET Active='0' WHERE Token='$tokens[$key]'");
                        }
                    }
                }
            }
            $this->db->query("DELETE FROM " . DB_PREFIX . "users_push_feedback WHERE ID='" . $feedback->ID . "'");
        }
    }

    protected function _parseBinaryTuple($sBinaryTuple) {
        return unpack('Ntimestamp/ntokenLength/H*deviceToken', $sBinaryTuple);
    }

    protected function _getPayload($message, $push) {
        $body['aps'] = array(
            'alert' => $message,
            'action_id' => $push->Action_ID,
            'module_id' => $push->Module_ID,
            'orginal_id' => $push->Orginal_ID,
            'id' => $push->Id_Num,
            'picture' => $push->Picture,
            'number' => $push->Number,
            'sound' => $push->Sound,
        );
        return $body;
    }

    protected function getPayload($message, $push) {
        @$sJSON = json_encode($this->_getPayload($message, $push), defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
        if (!defined('JSON_UNESCAPED_UNICODE') && function_exists('mb_convert_encoding')) {
            $sJSON = preg_replace_callback('~\\\\u([0-9a-f]{4})~i', create_function('$aMatches', 'return mb_convert_encoding(pack("H*", $aMatches[1]), "UTF-8", "UTF-16");'), $sJSON);
        }
        $sJSONPayload = str_replace('"aps":[]', '"aps":{}', $sJSON);
        $nJSONPayloadLen = strlen($sJSONPayload);
        if ($nJSONPayloadLen > 256) {
            $nMaxTextLen = $nTextLen = strlen($message) - ($nJSONPayloadLen - 256);
            if ($nMaxTextLen > 0) {
                while (strlen($message = mb_substr($message, 0, --$nTextLen, 'UTF-8')) > $nMaxTextLen);
                return $this->getPayload($message, $push);
            }
        }
        return $sJSONPayload;
    }

    public function getSettingValue($key, $store_id = 0) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;	
		}
	}
}
