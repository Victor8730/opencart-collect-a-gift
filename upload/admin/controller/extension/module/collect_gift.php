<?php

class ControllerExtensionModuleCollectGift extends Controller
{
    private $error = [];

    /**
     * Load the model of the module and setting/module model for save data
     * Saving module settings when the user clicked Save
     * Load the settings via the model method
     */
    public function index()
    {
        $this->load->language('extension/module/collect_gift');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/module');
        $this->load->model('catalog/category');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (!isset($this->request->get['module_id'])) {
                $this->model_setting_module->addModule('collect_gift', $this->request->post);
            } else {
                $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }


        /**
         * All data module
         * Contains variables for the template
         */
        $data = [];
        $data += $this->getBreadCrumbs();

        /**
         * Get user token from session and number of products to be translated
         */
        $data['user_token'] = $this->session->data['user_token'];
        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            $data['categories'][] = [
                'name' => $category['name'],
                'category_id' => $category['category_id'],
            ];
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['category'])) {
            $data['error_category'] = $this->error['category'];
        } else {
            $data['error_category'] = '';
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/collect_gift', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/collect_gift', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = $this->language->get('text_example');
        }

        if (isset($this->request->post['email'])) {
            $data['email'] = $this->request->post['email'];
        } elseif (!empty($module_info)) {
            $data['email'] = $module_info['email'];
        } else {
            $data['email'] = $this->config->get('config_email');
        }

        if (isset($this->request->post['category'])) {
            $data['category'] = $this->request->post['category'];
        } elseif (!empty($module_info)) {
            $data['category'] = $module_info['category'];
        } else {
            $data['category'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/collect_gift', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/collect_gift')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if (!$this->request->post['category']) {
            $this->error['category'] = $this->language->get('error_category');
        }

        return !$this->error;
    }

    private function getBreadCrumbs()
    {
        $breadcrumbs = [];
        $breadcrumbs['breadcrumbs'] = [];
        $breadcrumbs['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $breadcrumbs['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        ];

        if (!isset($this->request->get['module_id'])) {
            $breadcrumbs['breadcrumbs'][] = [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/collect_gift', 'user_token=' . $this->session->data['user_token'], true)
            ];
        } else {
            $breadcrumbs['breadcrumbs'][] = [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/collect_gift', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            ];
        }

        return $breadcrumbs;
    }
}
