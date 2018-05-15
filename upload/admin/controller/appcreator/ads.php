<?php

class ControllerAppcreatorAds extends Controller {

    private $error = array();

    public function index() {
        $this->document->setTitle("اعلانات روبو آب");

        $this->load->model('appcreator/ads');

        $this->getList();
    }

    public function add() {

        $this->document->setTitle("اعلانات روبو آب");

        $this->load->model('appcreator/ads');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $request = $this->request->post;
            $data['Title'] = $request['Title'];
            $data['Module_ID'] = $request['Module_ID'];
            $data['Action_ID'] = $request['Action_ID'];
            $data['Img'] = $request['Img'];
            $data['Link'] = $request['Link'];
            $data['StartDate'] = $request['StartDate'];
            $data['EndDate'] = $request['EndDate'];
            $data['Active'] = $request['Active'];

            $other_item_data = array();
            $other_item_data['Type'] = $request['Ads_Type'];
            $other_item_data['Ads_Provider'] = $request['Ads_Provider'];
            $other_item_data['IOS_Publisher_ID'] = $request['IOS_Publisher_ID'];
            $other_item_data['Android_Publisher_ID'] = $request['Android_Publisher_ID'];
            $other_item_data['Position'] = $request['Ads_Position'];

            $insert_id = $this->model_appcreator_ads->addAds($data, $other_item_data);

            $this->session->data['success'] = $this->language->get('text_success');

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

            $this->response->redirect($this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->document->setTitle("اعلانات روبو آب");

        $this->load->model('appcreator/ads');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $request = $this->request->post;
            $data['Title'] = $request['Title'];
            $data['Module_ID'] = $request['Module_ID'];
            $data['Action_ID'] = $request['Action_ID'];
            $data['Img'] = $request['Img'];
            $data['Link'] = $request['Link'];
            $data['StartDate'] = $request['StartDate'];
            $data['EndDate'] = $request['EndDate'];
            $data['Active'] = $request['Active'];

            $other_item_data = array();
            $other_item_data['Type'] = $request['Ads_Type'];
            $other_item_data['Ads_Provider'] = $request['Ads_Provider'];
            $other_item_data['IOS_Publisher_ID'] = $request['IOS_Publisher_ID'];
            $other_item_data['Android_Publisher_ID'] = $request['Android_Publisher_ID'];
            $other_item_data['Position'] = $request['Ads_Position'];
            
            $this->model_appcreator_ads->editAds($this->request->get['id'], $data, $other_item_data);

            $this->session->data['success'] = $this->language->get('text_success');

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

            $this->response->redirect($this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->document->setTitle("اعلانات روبو آب");

        $this->load->model('appcreator/ads');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $id) {
                $this->model_appcreator_ads->deleteAds($id);
            }

            $this->session->data['success'] = 'تمت العملية بنجاح';

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

            $this->response->redirect($this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'Title';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
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
            'text' => 'اعلانات روبو آب',
            'href' => $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('appcreator/ads/add', 'token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('appcreator/ads/delete', 'token=' . $this->session->data['user_token'] . $url, true);

        $data['filters'] = array();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $ads_total = $this->model_appcreator_ads->getTotalAds();
        $results = $this->model_appcreator_ads->getAdsList($filter_data);

        if ($results) {
            foreach ($results as $result) {
                $data['ads'][] = array(
                    'ID' => $result['ID'],
                    'Title' => $result['Title'],
                    'Module_ID' => $this->model_appcreator_ads->getModuleName($result['Module_ID']),
                    'Action_ID' => $result['Action_ID'],
                    'Active' => $result['Active'],
                    'Count' => $result['Count'],
                    'Img' => $result['Img'],
                    'Link' => $result['Link'],
                    'StartDate' => $result['StartDate'],
                    'EndDate' => $result['EndDate'],
                    'edit' => $this->url->link('appcreator/ads/edit', 'token=' . $this->session->data['user_token'] . '&id=' . $result['ID'] . $url, true)
                );
            }
        } else {
            $data['ads'] = array();
        }

        $data['heading_title'] = 'اعلانات روبو آب';

        $data['text_list'] = 'عرض الاعلانات';
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_title'] = 'العنوان';
        $data['column_active'] = 'التفعيل';
        $data['column_count'] = 'الضغطات';
        $data['column_link'] = 'الرابط';
        $data['column_img'] = 'الصورة';
        $data['column_module'] = 'الموديويل';
        $data['column_startdate'] = 'تاريخ البداية';
        $data['column_enddate'] = 'تاريخ النهاية';
        $data['column_action'] = "خيارات";

        $data['button_add'] = 'اضافة';
        $data['button_edit'] = 'تعديل';
        $data['button_delete'] = 'حذف';

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

        $data['sort_name'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . '&sort=Title' . $url, true);
        $data['sort_active'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . '&sort=Active' . $url, true);
        $data['sort_count'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . '&sort=Count' . $url, true);
        $data['sort_link'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . '&sort=Link' . $url, true);
        $data['sort_module'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . '&sort=Module_ID' . $url, true);
        $data['sort_startdate'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . '&sort=StartDate' . $url, true);
        $data['sort_enddate'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . '&sort=EndDate' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $ads_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($ads_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ads_total - $this->config->get('config_limit_admin'))) ? $ads_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ads_total, ceil($ads_total / $this->config->get('config_limit_admin')));

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

        $this->response->setOutput($this->load->view('appcreator/ads_list', $data));
    }

    protected function getForm() {
        $this->load->model('setting/setting');
        $data['heading_title'] = 'اعلانات روبو آب';
                
        $data['text_form'] = !isset($this->request->get['id']) ? 'اضافة' : 'تعديل';

        $data['entry_basic'] = 'البيانات الاساسية';
        $data['entry_title'] = 'العنوان';
        $data['entry_module'] = 'الموديول';
        $data['entry_action'] = 'الصفحة';
        $data['entry_img'] = 'الصورة';
        $data['entry_link'] = 'الرابط';
        $data['entry_startdate'] = 'تاريخ البداية';
        $data['entry_enddate'] = 'تاريخ النهاية';
        $data['entry_active'] = 'التفعيل';
        $data['entry_options'] = 'خيارات الاعلان';
        $data['entry_ad_type'] = 'نوع الاعلان';
        $data['entry_ad_pos'] = 'مكان الاعلان';
        $data['entry_ad_provider'] = 'مزود الاعلان';
        $data['entry_ios_publisher'] = 'IOS Publisher ID';
        $data['entry_android_publisher'] = 'Android Publisher ID';

        $data['button_save'] = 'حفظ';
        $data['button_cancel'] = 'الغاء';
        $data['button_ad_add'] = 'اضافة';
        $data['button_remove'] = 'حذف';

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['attribute_title'])) {
            $data['error_title'] = $this->error['attribute_title'];
        } else {
            $data['error_title'] = "";
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
            'text' => 'اعلانات روبو آب',
            'href' => $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['id'])) {
            $data['action'] = $this->url->link('appcreator/ads/add', 'token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('appcreator/ads/edit', 'token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('appcreator/ads', 'token=' . $this->session->data['user_token'] . $url, true);
        $ads_info = array();
        $other_data = array();
        if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $ads_info = $this->model_appcreator_ads->getAds($this->request->get['id']);
            $query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE `key` = '" . "robo_app_ads_opts_".$this->request->get['id'] . "'");
            if ($query->num_rows) {
                $other_data = json_decode($query->row['value']);
                $other_data = $other_data->{$this->request->get['id']};
            }
        }
        
        
        $data['ads_info'] = $ads_info;
        $data['other_data'] = $other_data;
        
        $data['token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['sort_order'] = '';

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base_url'] = HTTPS_ADMIN;
        } else {
            $data['base_url'] = HTTPS_ADMIN;
        }
        $data['token'] = $this->session->data['user_token'];

        $this->load->model('tool/image');
        if (isset($this->request->post['Img']) && is_file(DIR_IMAGE . $this->request->post['Img'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['Img'], 100, 100);
        } elseif (!empty($ads_info) && is_file(DIR_IMAGE . $ads_info['Img'])) {
            $data['thumb'] = $this->model_tool_image->resize($ads_info['Img'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('appcreator/ads_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'appcreator/ads')) {
            $this->error['warning'] = 'ليس لديك الصلاحية الكافية لتعديل لاعلان';
        }

        if (!$this->request->post['Title'] || $this->request->post['Title']=="") {
            $this->error['attribute_title'] = 'العنوان';
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'appcreator/ads')) {
            $this->error['warning'] = 'ليس لديك الصلاحية الكافية لحذف لاعلان';
        }

        return !$this->error;
    }
    
    public function getActions() {
        if (isset($_GET['data']) && !empty($_GET['data'])) {
            $module_id = $_GET['data'];
            $actions = array();
            switch ($module_id) {
                case 1:
                    $actions = array(2 => 'كل الاخبار', 6 => 'عرض الخبر', 3 => 'اقسام الاخبار', 5 => 'اخبار الرئيسية');
                    break;
                case 3:
                    $actions = array(2 => 'كل الفيديوهات', 6 => 'عرض الفيديو', 3 => 'أقسام الفيديو');
                    break;
                case 4:
                    $actions = array(2 => 'كل الصور', 6 => 'عرض الصورة', 3 => 'اقسام الصور');
                    break;
                case 6:
                    $actions = array(2 => 'كل الصفحات', 26 => 'عرض الصفحة');
                    break;
                case 5:
                    $actions = array(2 => 'كل المنتجات', 6 => 'عرض المنتج', 3 => 'اقسام المنتجات');
                    break;
                case 47:
                    $actions = array(2 => 'نتائج الاستفتاء', 9 => 'عرض الاستفتاء');
                    break;
                default :
                    $actions = array();
            }
            foreach ($actions as $k => $v) {
                echo '<option value="' . $k . '">' . $v . '</option>';
            }
        }
    }
}