<?php

class ControllerExtensionModuleCollectGift extends Controller
{
    public function index($setting)
    {
        $this->load->language('extension/module/collect_gift');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $data = [];
        $filter_data = ['filter_category_id' => $setting['category'], 'order' => 'DESC',];
        $results = $this->model_catalog_product->getProducts($filter_data);

        if ($results) {
            foreach ($results as $result) {
                if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $price = false;
                }

                if ((float)$result['special']) {
                    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                } else {
                    $special = false;
                }

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = $result['rating'];
                } else {
                    $rating = false;
                }

                $data['products'][] = [
                    'product_id' => $result['product_id'],
                    'thumb' => ($result['image']) ? 'image/'.$result['image'] : 'image/placeholder.png',
                    'name' => $result['name'],
                    'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                    'price' => round($result['price']),
                    'price_format' => $price,
                    'special' => round($result['special']),
                    'special_format' => $special,
                    'tax' => $tax,
                    'rating' => $rating,
                    'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                ];
            }

            return $this->load->view('extension/module/collect_gift', $data);
        }
    }
}
