<?php
class ControllerRestapiModuleInformation extends Controller {
	public function index() {
		$this->load->language('module/information');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_sitemap'] = $this->language->get('text_sitemap');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			$data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('restapi/information/information', 'information_id=' . $result['information_id'])
			);
		}

		$data['contact'] = $this->url->link('restapi/information/contact');
		$data['sitemap'] = $this->url->link('restapi/information/sitemap');

		return $data;
	}
}