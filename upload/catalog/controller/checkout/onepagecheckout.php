<?php

class ControllerCheckoutOnepagecheckout extends Controller
{
    public $errors = array();

    /**
     * @param string $get Specific flag
     * @return string Output checkout view
     */
    public function index($get = '')
    {

        // Load language files
        $this->load->language('checkout/onepagecheckout');

        // Validate cart
        if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
            if ($get === 'get_cart') {
                return false;
            }
            $this->response->redirect($this->url->link('common/home'));
        }
        if (
            !$this->cart->hasStock() &&
            (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))
        ) {
            $data['error_warning'] = $this->language->get('error_stock');
        } elseif (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error_warning'] = '';
        }

        // Check if shipping method is set
        if (!isset($this->session->data['shipping_method']) || !isset($this->session->data['shipping_method']['cost'])) {
            $data['default_sm'] = true;
        } else {
            $data['default_sm'] = false;
        }

        $this->document->addStyle('catalog/view/theme/default/stylesheet/onepagecheckout.css');
        $this->document->addScript('catalog/view/javascript/jquery.maskedinput.min.js');

        // Load Models
        $this->load->model('tool/image');
        $this->load->model('extension/extension');
        $this->load->model('checkout/onepagecheckout');
        $this->load->model('checkout/order');
        $this->load->model('account/address');

        $data['text_login'] = $this->language->get('text_login');
        $data['text_notlogged'] = $this->language->get('text_notlogged');
        $data['text_customer'] = $this->language->get('text_customer');
        $data['text_cart'] = $this->language->get('text_cart');
        $data['text_full_name'] = $this->language->get('text_full_name');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_town'] = $this->language->get('text_town');
        $data['text_delivery_method'] = $this->language->get('text_delivery_method');
        $data['text_delivery_type_1'] = $this->language->get('text_delivery_type_1');
        $data['text_delivery_type_2'] = $this->language->get('text_delivery_type_2');
        $data['text_delivery_placeholder'] = $this->language->get('text_delivery_placeholder');
        $data['text_payment_method'] = $this->language->get('text_payment_method');
        $data['text_comment'] = $this->language->get('text_comment');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_price'] = $this->language->get('text_price');
        $data['text_quantity'] = $this->language->get('text_quantity');
        $data['text_total'] = $this->language->get('text_total');
        $data['text_phone_format'] = $this->language->get('text_phone_format');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_new_customer'] = $this->language->get('text_new_customer');
        $data['text_register'] = $this->language->get('text_register');
        $data['text_register_account'] = $this->language->get('text_register_account');
        $data['text_returning_customer'] = $this->language->get('text_returning_customer');
        $data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
        $data['text_forgotten'] = $this->language->get('text_forgotten');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_login'] = $this->language->get('button_login');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');

        $data['action'] = $this->url->link('account/login', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['forgotten'] = $this->url->link('account/forgotten', '', true);

        // Breadcrumbs
        $data['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            ),
            array(
                'text' => $this->language->get('text_cart'),
                'href' => $this->url->link('checkout/cart')
            ),
            array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('checkout/checkout', '', true)
            )
        );

        // Products
        $products = $this->cart->getProducts();

        foreach ($products as $i => $product) {

            if($product['image']){
                $products[$i]['thumb'] = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
            } else {
                $products[$i]['thumb'] = '';
            }

            $products[$i]['href'] = $this->url->link('product/product', 'product_id=' . $products[$i]['product_id']);

            if($this->request->server['REQUEST_METHOD'] != 'POST') {
                $products[$i]['price'] = $this->currency->format($product['price'], $this->session->data['currency']);
            } else {
                $products[$i]['price'] = $product['price'];
            }
            $product_total = 0;
            $option_data = array();

            foreach ($product['option'] as $option) {
                $option_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'value' => $option['value'],
                    'type' => $option['type']
                );
            }
            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
            }
        }

        $data['products'] = $products;

        // Totals
        $totals_data = $this->getTotals();
        $totals = $totals_data['totals'];
        $total = $totals_data['total'];

        $data['totals'] = array();
        foreach ($totals as $total) {
            $data['totals'][] = array(
                'title' => $total['title'],
                'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
            );
        }

        // Gift Voucher
        $data['vouchers'] = array();

        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'code' => token(10),
                    'to_name' => $voucher['to_name'],
                    'to_email' => $voucher['to_email'],
                    'from_name' => $voucher['from_name'],
                    'from_email' => $voucher['from_email'],
                    'voucher_theme_id' => $voucher['voucher_theme_id'],
                    'message' => $voucher['message'],
                    'amount' => $voucher['amount']
                );
            }
        }

        if ( $this->customer->isLogged()) {
            $addr = $this->model_account_address->getAddress($this->customer->getAddressId());
            $data['c_logged'] = true;
            $data['c_name'] = $this->customer->getFirstName().' '.$this->customer->getLastName();
            $data['city'] =$addr['city'];
            $data['address_1'] = $addr['address_1'];
            $data['email'] = $this->customer->getEmail();
            $data['telephone'] = $this->customer->getTelephone();
        } else {
            $data['c_logged'] = false;
            $data['c_name'] = '';
            $data['city'] = '';
            $data['address_1'] = '';
            $data['email'] = '';
            $data['telephone'] = '';
        }

        if (isset($this->session->data['account'])) {
            $data['account'] = $this->session->data['account'];
        } else {
            $data['account'] = '';
        }

        if (isset($this->session->data['payment_address']['firstname'])) {
            $data['firstname'] = $this->session->data['payment_address']['firstname'];
        } else {
            $data['firstname'] = '';
        }

        if (isset($this->session->data['payment_address']['address_1'])) {
            $data['address_1'] = $this->session->data['payment_address']['address_1'];
        }

        if (isset($this->session->data['payment_address']['city'])) {
            $data['city'] = $this->session->data['payment_address']['city'];
        }

        if (isset($this->session->data['payment_address']['telephone'])) {
            $data['telephone'] = $this->session->data['payment_address']['telephone'];
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }

        if (isset($this->session->data['email'])) {
            $data['email'] = $this->session->data['email'];
        }

        if (isset($this->session->data['address_1'])) {
            $data['address_1'] = $this->session->data['address_1'];
        }

        $this->errors = [];
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            if ($this->validateForm()) {
                $order_data = array();

                if ($this->affiliate->isLogged()) {
                    $order_data['affiliate_id'] = $this->affiliate->getId();
                } else {
                    $order_data['affiliate_id'] = '';
                }

                $order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
                $order_data['store_id'] = $this->config->get('config_store_id');
                $order_data['store_name'] = $this->config->get('config_name');

                if ($order_data['store_id']) {
                    $order_data['store_url'] = $this->config->get('config_url');
                } else {
                    if ($this->request->server['HTTPS']) {
                        $order_data['store_url'] = HTTPS_SERVER;
                    } else {
                        $order_data['store_url'] = HTTP_SERVER;
                    }
                }

                $order_data['products'] = $data['products'];
                $order_data['vouchers'] = $data['vouchers'];
                $order_data['totals']   = $totals;
                $order_data['total']    = $total['value'];

                if (isset($this->request->post['firstname'])) {
                    $post_name = htmlspecialchars($this->request->post['firstname']);
                    $this->session->data['payment_address']['firstname'] = $post_name;
                    $order_data['firstname'] = $post_name;
                }

                if (isset($this->request->post['telephone'])) {
                    $post_telephone = $this->request->post['telephone'];
                    $this->session->data['payment_address']['telephone'] = $post_telephone;
                    $order_data['telephone'] = $post_telephone;
                }

                if (isset($this->request->post['email'])) {
                    $post_email = filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL);
                    $this->session->data['payment_address']['email'] = $post_email;
                    $order_data['email'] = $post_email;
                    if($post_email) {
                        $order_data['order_status_id'] = 0;
                    } else {
                        $order_data['order_status_id'] = $this->config->get('config_order_status_id');
                    }
                }

                if (isset($this->request->post['city'])) {
                    $post_city = htmlspecialchars($this->request->post['city']);
                    $this->session->data['payment_address']['city'] = $post_city;
                    $order_data['city'] = $post_city;
                }

                if (isset($this->request->post['shipping_method'])) {
                    $post_shipping_method = json_decode(htmlspecialchars_decode($this->request->post['shipping_method']), true);
                    $this->session->data['shipping_method'] = $post_shipping_method;
                    $order_data['shipping_method'] = $post_shipping_method;
                }

                if (isset($this->request->post['address_1'])) {
                    $post_payment_address = htmlspecialchars($this->request->post['address_1']);
                    $this->session->data['payment_address']['address_1'] = $post_payment_address;
                    $order_data['address_1'] = $post_payment_address;
                }

                if (isset($this->request->post['payment_method'])) {
                    $post_payment_method = json_decode(htmlspecialchars_decode($this->request->post['payment_method']), true);
                    $this->session->data['payment_method'] = $post_payment_method;
                    $order_data['payment_method'] = $post_payment_method;
                }

                if (isset($this->request->post['firstname'])) {
                    $this->session->data['firstname'] = htmlspecialchars($this->request->post['firstname']);
                }

                if (isset($this->request->post['comment'])) {
                    $post_comment = htmlspecialchars($this->request->post['comment']);
                    $this->session->data['comment'] = $post_comment;
                    $order_data['comment'] = $post_comment;
                }

                if (isset($this->request->post['delivery-type'])) {
                    $post_delivery_type = htmlspecialchars($this->request->post['delivery-type']);
                    $this->session->data['delivery-type'] = $post_delivery_type;
                    $order_data['address_1'] = $post_delivery_type . ' - ' . $order_data['address_1'];
                }

                $order_data['language_id'] = $this->config->get('config_language_id');
                $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
                $order_data['currency_code'] = $this->session->data['currency'];
                $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
                $order_data['ip'] = $this->request->server['REMOTE_ADDR'];

                if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                    $order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
                } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                    $order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
                } else {
                    $order_data['forwarded_ip'] = '';
                }

                if (isset($this->request->server['HTTP_USER_AGENT'])) {
                    $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
                } else {
                    $order_data['user_agent'] = '';
                }

                if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                    $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
                } else {
                    $order_data['accept_language'] = '';
                }

                $order_data['customer_id'] = 0;
                if (isset($this->session->data['guest']['customer_group_id'])) {
                    $order_data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
                } else {
                    $order_data['customer_group_id'] = $this->config->get('config_customer_group_id');
                }

                $json['order_id'] = $this->model_checkout_onepagecheckout->addOrder($order_data);
                $this->model_checkout_order->addOrderHistory($json['order_id'], $this->config->get('config_order_status_id'), '', 0, 0);

                $this->session->data['order_id'] = $json['order_id'];

                $json['payment'] = $this->load->controller('payment/' . $this->session->data['payment_method']['code']);

                if($this->session->data['payment_method']['code']=='cod') {
                    $json['cod'] = 1;
                }

            } else {
                $json['error'] = $this->errors;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));

        } else {

            // Cart modules
            $data['modules'] = array();
            $files = glob(DIR_APPLICATION . '/controller/total/*.php');
            if ($files) {
                foreach ($files as $file) {
                    if (basename($file, '.php') === 'shipping') continue;

                    $result = $this->load->controller('total/' . basename($file, '.php'));
                    if ($result) {
                        $data['modules'][] = $result;
                    }
                }
            }

            // Shipping Methods
            $data['shipping_methods'] = $this->getShippingView();
            // Payment Methods
            $data['payment_methods'] = $this->getPaymentView();

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            if ($get === 'get_cart') {
                return $data;

            } elseif ($get === 'get_error') {
                return $data['error_warning'];
            }

            $data['cart'] = $this->getCartView($data);

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/onepagecheckout.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/checkout/onepagecheckout.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/checkout/onepagecheckout.tpl', $data));
            }
        }
    }

    /**
     * @param array $total Total order value
     * @return array Payment methods
     */
    public function getPaymentMethods($total)
    {
        $this->load->model('extension/extension');

        $this->session->data['payment_address']['country_id'] = 0;
        $this->session->data['payment_address']['zone_id'] = 0;

        $method_data = array();
        $results = $this->model_extension_extension->getExtensions('payment');
        $recurring = $this->cart->hasRecurringProducts();

        if (isset($this->session->data['payment_method']['code'])) {
            $active_payment_method = $this->session->data['payment_method']['code'];
        } else {
            $active_payment_method = '';
        }

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {

                $this->load->model('payment/' . $result['code']);
                $method = $this->{'model_payment_' . $result['code']}->getMethod($this->session->data['payment_address'], $total);

                if ($method) {
                    if ($recurring) {
                        if (
                            property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') &&
                            $this->{'model_extension_payment_' . $result['code']}->recurringPayments()
                        ) {
                            $method_data[$result['code']] = $method;
                        }
                    } else {
                        $method_data[$result['code']] = $method;
                    }
                }
            }
        }

        $sort_order = array();

        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);

        foreach ($method_data as $key => $pm) {
            if ($active_payment_method == $pm['code']) {
                $selected_pm = true;
            } else {
                $selected_pm = false;
            }
            $method_data[$key]['selected'] = $selected_pm;
        }

        $this->session->data['payment_methods'] = $method_data;

        return $method_data;
    }

    /**
     * @return array Shipping methods
     */
    public function getShippingMethods()
    {
        $this->load->model('extension/extension');

        $this->session->data['shipping_address']['country_id'] = 0;
        $this->session->data['shipping_address']['zone_id'] = 0;
        $method_data = array();
        $results = $this->model_extension_extension->getExtensions('shipping');

        if (isset($this->session->data['shipping_method']['code'])) {
            $active_shipping_method = $this->session->data['shipping_method']['code'];
        } else {
            $active_shipping_method = '';
        }

        foreach ($results as $result) {
            if ($this->config->get($result['code'] . '_status')) {

                $this->load->model('shipping/' . $result['code']);
                $quote = $this->{'model_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);

                if ($quote) {
                    $method_data[$result['code']] = array(
                        'title' => $quote['title'],
                        'quote' => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error' => $quote['error']
                    );
                }
            }
        }

        $sort_order = array();

        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);

        $this->session->data['shipping_methods'] = $method_data;

        $data = array();

        foreach ($method_data as $i => $shipping_method) {
            foreach ($shipping_method['quote'] as $shipping_method2) {
                if ($active_shipping_method == $shipping_method2['code']) {
                    $selected_sm = true;
                } else {
                    $selected_sm = false;
                }
                $data[$i]['value'] = $shipping_method2['code'];
                $data[$i]['title'] = $shipping_method2['title'];
                $data[$i]['selected'] = $selected_sm;
                if (isset($shipping_method2['cost'])) {
                    $data[$i]['cost'] = $shipping_method2['cost'];
                } else {
                    $data[$i]['cost']='';
                }
            }
        }

        return $data;
    }

    /**
     * @return array Order totals
     */
    public function getTotals()
    {
        $this->load->model('extension/extension');

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Display prices
        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

            $sort_order = array();
            $results = $this->model_extension_extension->getExtensions('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {

                    $this->load->model('total/' . $result['code']);
                    $this->{'model_total_' . $result['code']}->getTotal($totals, $total, $taxes);
                }
            }

            $sort_order = array();
            foreach ($totals as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $totals);
        }

        $data = array(
            'totals' => $totals,
            'total'  => $total,
            'taxes'  => $taxes
        );

        return $data;

    }

    /**
     * @param false|array $cart_data
     * @return false|string Cart view
     */
    public function getCartView($cart_data = false)
    {
        if (is_array($cart_data) && !empty($cart_data)) {
            $data = $cart_data;
        } else {
            $data = $this->index('get_cart');
            if (!$data) return false;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/onepagecheckout_cart.tpl')) {
            $cart = $this->load->view($this->config->get('config_template') . '/template/checkout/onepagecheckout_cart.tpl', $data);
        } else {
            $cart = $this->load->view('default/template/checkout/onepagecheckout_cart.tpl', $data);
        }

        return $cart;
    }

    /**
     * @return false|string Payment view
     */
    public function getPaymentView()
    {
        $totals_data = $this->getTotals();
        if (is_array($totals_data) && !empty($totals_data['total'])) {
            $total = $totals_data['total'];
        } else {
            $total = 0;
        }

        $data['payment_methods'] = $this->getPaymentMethods($total);

        if (!$data['payment_methods']) {
            return false;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/onepagecheckout_payment_methods.tpl')) {
            $view = $this->load->view($this->config->get('config_template') . '/template/checkout/onepagecheckout_payment_methods.tpl', $data);
        } else {
            $view = $this->load->view('default/template/checkout/onepagecheckout_payment_methods.tpl', $data);
        }

        return $view;

    }

    /**
     * @return false|string Shipping view
     */
    public function getShippingView()
    {
        $data['shipping_methods'] = $this->getShippingMethods();

        if (!$data['shipping_methods']) {
            return false;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/onepagecheckout_shipping_methods.tpl')) {
            $view = $this->load->view($this->config->get('config_template') . '/template/checkout/onepagecheckout_shipping_methods.tpl', $data);
        } else {
            $view = $this->load->view('default/template/checkout/onepagecheckout_shipping_methods.tpl', $data);
        }

        return $view;
    }

    /**
     * Login customer and return json answer
     */
    public function ajaxLogin()
    {
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateLogin()) {

            $this->load->model('account/address');
            $addr = $this->model_account_address->getAddress($this->customer->getAddressId());
            $loginData['c_name'] = $this->customer->getFirstName().' '.$this->customer->getLastName();
            $loginData['city'] =$addr['city'];
            $loginData['address_1'] = $addr['address_1'];
            $loginData['email'] = $this->customer->getEmail();
            $loginData['telephone'] = $this->customer->getTelephone();
            $this->load->language('account/login');

            // Unset guest
            unset($this->session->data['guest']);

            // Default Shipping Address
            $this->load->model('account/address');

            if ($this->config->get('config_tax_customer') == 'payment') {
                $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            if ($this->config->get('config_tax_customer') == 'shipping') {
                $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
            }

            // Add to activity log
            if ($this->config->get('config_customer_activity')) {
                $this->load->model('account/activity');

                $activity_data = array(
                    'customer_id' => $this->customer->getId(),
                    'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
                );

                $this->model_account_activity->addActivity('login', $activity_data);
            }
        }

        if( $this->errors) {
            $loginData['errors'] = $this->errors;
        } else {
            $loginData['errors'] = 0;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($loginData));
    }

    /**
     * @return bool
     */
    protected function validateLogin() {
        // Check how many login attempts have been made.
        $this->load->model('account/customer');
        $this->load->language('account/login');
        $login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

        if (
            $login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) &&
            strtotime('-5 minutes') < strtotime($login_info['date_modified'])
        ) {
            $this->errors['warning'] = $this->language->get('error_attempts');
        }

        // Check if customer has been approved.
        $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

        if ($customer_info && !$customer_info['approved']) {
            $this->errors['warning'] = $this->language->get('error_approved');
        }

        if (!$this->errors) {
            if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {

                $this->errors['warning'] = $this->language->get('error_login');
                $this->model_account_customer->addLoginAttempt($this->request->post['email']);

            } else {
                $this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
            }
        }

        return  !$this->errors;
    }

    /**
     * @return bool
     */
    public function validateForm()
    {
        $this->error = [];
        if (
            (utf8_strlen(trim($this->request->post['firstname'])) < 1) ||
            (utf8_strlen(trim($this->request->post['firstname'])) > 42)
        ) {
            $data['error']['firstname'] = $this->language->get('error_firstname');
        }

        if (!preg_match('/^380[0-9]{9}$/', $this->request->post['telephone'])) {
            $data['error']['telephone'] = $this->language->get('error_telephone');
        }

        if (!empty($data['error'])) {
            $this->errors = $data['error'];
            return false;
        } else {
            return true;
        }
    }

    /**
     * Update product quantity and return json answer
     */
    public function updateQuantity()
    {
        $this->load->language('checkout/cart');

        $json = array();

        // Update
        if (!empty($this->request->post['quantity'])) {
            foreach ($this->request->post['quantity'] as $key => $value) {
                $this->cart->update($key, $value);
            }
            $json['status'] = 'Ok';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Ajax request - return string Output shipping view
     */
    public function shipping()
    {
        $this->response->setOutput($this->getShippingView());
    }

    /**
     * Ajax request - return string Output payment view
     */
    public function payment()
    {
        $this->response->setOutput($this->getPaymentView());
    }

    /**
     * Ajax request - return string Output cart view
     */
    public function cart()
    {
        $cart = $this->getCartView();

        if (!$cart) {
            $json = array('status' => false);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
        } else {
            $this->response->setOutput($cart);
        }
    }

    /**
     * Ajax request - return json answer
     */
    public function error()
    {
        $error = $this->index('get_error');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(array('error' => $error)));
    }

}
