<?php
class ControllerAppcreatorPush extends Controller {
    private $error = array();

    public function index() {

        $this->document->setTitle("البوش نوتيفيكيشن");

        $this->load->model('appcreator/push');

        $this->getList();
    }
    
    public function settings() {
        $this->document->setTitle("اعدادات البوش نوتيفيكيشن");
        $this->load->model('appcreator/push');
        if (($this->request->server['REQUEST_METHOD'] == 'POST'))
        {
            $request = $this->request->post;
            $this->model_appcreator_push->saveSettings($request);
            $this->session->data['success'] = "تمت العملية بنجاح";
            $this->response->redirect($this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'], true));
        }
        $this->load->model('setting/setting');
        $data['heading_title'] = "اعدادات البوش نوتيفيكيشن";
        $data['text_form'] = "اعدادات البوش نوتيفيكيشن";
        $data['entry_google_push_api_key'] = "GOOGLE PUSH API KEY";
        $data['entry_apple_cert_file'] = "APPLE CERT FILE";
        $data['entry_apple_pass_phrase'] = "APPLE PASS PHRASE";
        $data['entry_apple_feedback_server'] = "APPLE FEEDBACK SERVER";
        $data['entry_apple_server'] = "APPLE SERVER";
        $data['button_save'] = "حفظ ";
        $data['button_cancel'] = "الغاء";
        $data['cancel'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'], true);
        $data['robo_google_push_api_key'] = $this->model_setting_setting->getSettingValue("robo_google_push_api_key") != null?$this->model_setting_setting->getSettingValue("robo_google_push_api_key"):"";
        $data['robo_apple_cert_file'] = $this->model_setting_setting->getSettingValue("robo_apple_cert_file") != null?$this->model_setting_setting->getSettingValue("robo_apple_cert_file"):"";
        $data['robo_apple_pass_phrase'] = $this->model_setting_setting->getSettingValue("robo_apple_pass_phrase") != null?$this->model_setting_setting->getSettingValue("robo_apple_pass_phrase"):"";
        $data['robo_apple_feedback_server'] = $this->model_setting_setting->getSettingValue("robo_apple_feedback_server") != null?$this->model_setting_setting->getSettingValue("robo_apple_feedback_server"):"";
        $data['robo_apple_server'] = $this->model_setting_setting->getSettingValue("robo_apple_server") != null?$this->model_setting_setting->getSettingValue("robo_apple_server"):"";
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base_url'] = HTTPS_CATALOG;
        } else {
            $data['base_url'] = HTTP_CATALOG;
        }
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => "البوش نوتيفيكيشن",
            'href' => $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'], true)
        );
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('appcreator/push_settings', $data));
    }
    
    public function add() {

        $this->document->setTitle("البوش نوتيفيكيشن");

        $this->load->model('appcreator/push');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $request = $this->request->post;

            $insert_id = $this->model_appcreator_push->addPush($request);

            $this->session->data['success'] = "تمت العملية بنجاح";

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }
    public function edit() {
        $this->document->setTitle("البوش نوتيفيكيشن");

        $this->load->model('appcreator/push');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $request = $this->request->post;
            
            $this->model_appcreator_push->update($request, $this->request->get['id']);

            $this->session->data['success'] = "تمت العملية بنجاح";

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }
    
    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'ID';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['user_token'], true)
        );


        $data['breadcrumbs'][] = array(
            'text' => "البوش نوتيفيكيشن",
            'href' => $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('appcreator/push/add', 'token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('appcreator/push/delete', 'token=' . $this->session->data['user_token'] . $url, true);

        $data['filters'] = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $push_total = $this->model_appcreator_push->getTotalPush();
        $results = $this->model_appcreator_push->getPushList($filter_data);

        if ($results) {
            foreach ($results as $result) {
                $data['push'][] = array(
                    'ID' => $result['ID'],
                    'Message' => $result['Message'],
                    'Module_ID' => $this->model_appcreator_push->getModuleName($result['Module_ID']),
                    'Action_ID' => $result['Action_ID'],
                    'Type' => $this->model_appcreator_push->get_device_name($result['Type']),
                    'Num_Msgs' => $result['Num_Msgs'],
                    'Send_Time' => $result['Send_Time'],
                    'Finished' => $result['Finished'],
                    'edit' => $this->url->link('appcreator/push/edit', 'token=' . $this->session->data['user_token'] . '&id=' . $result['ID'] . $url, true)
                );
            }
        } else {
            $data['push'] = array();
        }

        $data['heading_title'] = "البوش نوتيفيكيشن";

        $data['text_list'] = "عرض البوش نوتيفيكيشن";
        $data['text_no_results'] = "لا توجد نتائج";
        $data['text_confirm'] = "هل انت متأكد؟";

        $data['column_message'] = "الرسالة";
        $data['column_type'] = "الأجهزة";
        $data['column_num_msgs'] = "عدد الأجهزة";
        $data['column_send_time'] = "تاريخ الارسال";
        $data['column_finished'] = "تاريخ الانتهاء";
        $data['column_module'] = "الموديول";
        $data['column_action'] = "خيارات";

        $data['button_add'] = "اضافة";
        $data['button_edit'] = "تعديل";
        $data['button_delete'] = "حذف";

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_message'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . '&sort=Message' . $url, true);
        $data['sort_type'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . '&sort=Type' . $url, true);
        $data['sort_num_msgs'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . '&sort=Num_Msgs' . $url, true);
        $data['sort_module'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . '&sort=Module_ID' . $url, true);
        $data['sort_send_time'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . '&sort=Send_Time' . $url, true);
        $data['sort_finished'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . '&sort=Finished' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $push_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($push_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($push_total - $this->config->get('config_limit_admin'))) ? $push_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $push_total, ceil($push_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;
        
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base_url'] = HTTPS_CATALOG;
        } else {
            $data['base_url'] = HTTP_CATALOG;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('appcreator/push_list', $data));
    }
    
    protected function getForm() {
        $this->load->model('setting/setting');
        $data['heading_title'] = "البوش نوتيفيكيشن";
                
        $data['text_form'] = !isset($this->request->get['id']) ? "اضافة" : "تعديل";

        $data['entry_basic'] = "خصائص الاشعار";
        $data['entry_send_to'] = "الارسال الى";
        $data['entry_send_time'] = "وقت الارسال";
        $data['entry_module'] = "الموديول";
        $data['entry_action'] = "الصفحة";
        $data['entry_content'] = "المحتوى";
        $data['entry_selectvar'] = "اختر احد المواد";
        $data['entry_options'] = "مكونات الاشعار";
        $data['entry_sound'] = "نغمة الرسالة";
        $data['entry_picture'] = "شكل الأيقونة";
        $data['entry_message'] = "نص الرسالة";

        $data['button_save'] = "حفظ وارسال";
        $data['button_cancel'] = "الغاء";
        $data['button_ad_add'] = "اضافة";
        $data['button_remove'] = "حذف";

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['attribute_message'])) {
            $data['error_message'] = $this->error['attribute_message'];
        } else {
            $data['error_message'] = "";
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => "البوش نوتيفيكيشن",
            'href' => $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['id'])) {
            $data['action'] = $this->url->link('appcreator/push/add', 'token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('appcreator/push/edit', 'token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . $url, true);
        $push_info = array();
        if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $push_info = $this->model_appcreator_push->getPush($this->request->get['id']);
        }
        
        
        $data['push_info'] = $push_info;
        $data['id'] = isset($this->request->get['id']) && $this->request->get['id']!=""?$this->request->get['id']:"";
        
        $data['token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['sort_order'] = '';

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base_url'] = HTTPS_ADMIN;
        } else {
            $data['base_url'] = 'HTTP_ADMIN';
        }
        $data['token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('appcreator/push_form', $data));
    }
    
    public function delete() {

        $this->document->setTitle("البوش نوتيفيكيشن");

        $this->load->model('appcreator/push');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $id) {
                $this->model_appcreator_push->deletePush($id);
            }

            $this->session->data['success'] = "تمت العملية بنجاح";

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('appcreator/push', 'token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    
    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'appcreator/push')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['Message'] || $this->request->post['Message']=="") {
            $this->error['attribute_message'] = "الرسالة مطلوبة";
        }

        return !$this->error;
    }
    
    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'appcreator/push')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
