<?php
class ControllerAppcreatorApi extends Controller {
       
    public function index() {
        // exit();
        $this->document->setTitle("Appcreator Api");
        $this->load->model('setting/setting');
        $this->document->setTitle("Appcreator Api");
        $data = array();
        $data['heading_title'] = "Appcreator Api";
        $data['text_list'] = "Api Url";
        $data['entry_title'] = "Api Url";
        
        $url = '';
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['user_token'], true)
        );


        $data['breadcrumbs'][] = array(
            'text' => "Appcreator Api",
            'href' => $this->url->link('appcreator/api', 'token=' . $this->session->data['user_token'] . $url, true)
        );
        
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base_url'] = HTTPS_CATALOG;
        } else {
            $data['base_url'] = HTTP_CATALOG;
        }
        $data['api_url'] = $data['base_url'] . "index.php?route=appcreator/appcreator&api_auth=".$this->model_setting_setting->getSettingValue('robo_api_auth');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('appcreator/api', $data));
    }
}