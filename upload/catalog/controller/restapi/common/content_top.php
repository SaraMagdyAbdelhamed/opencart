<?php
class ControllerRestapiCommonContentTop extends Controller {
	public function index() {
		$this->load->model('design/layout');

		$route = 'common/home';

		$layout_id = 0;

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		$this->load->model('extension/module');

		$data['modules'] = array();

		$modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_top');

		foreach ($modules as $module) {
			$part = explode('.', $module['code']);

			if (isset($part[0]) && $this->config->get($part[0] . '_status')) {
				$data['modules'][] = $this->load->controller('restapi/module/' . $part[0]);
			}

			if (isset($part[1])) {
				$setting_info = $this->model_extension_module->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$data['modules'][] = $this->load->controller('restapi/module/' . $part[0], $setting_info);
				}
			}
		}

		return $data;

	}
}