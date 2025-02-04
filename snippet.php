<?php

function custom_ninja_forms_register_mailerlite_action($actions) {
    $actions['mailerlite'] = new NF_MailerLite_Action();
    return $actions;
}
add_filter('ninja_forms_register_actions', 'custom_ninja_forms_register_mailerlite_action');

if (!class_exists('NF_MailerLite_Action')) {
    class NF_MailerLite_Action extends NF_Abstracts_Action
    {
        protected $_name = 'mailerlite';
        protected $_nicename = 'MailerLite Integration';
        protected $_icon = 'fa-envelope';
        protected $_timing = 'normal';
        protected $_priority = '10';

        public function __construct()
        {
            parent::__construct();

            $this->_settings = array(
                'mailerlite_api_key' => array(
                    'name' => 'mailerlite_api_key',
                    'type' => 'textbox',
                    'label' => __('MailerLite API Key', 'text-domain'),
                    'width' => 'full',
                    'group' => 'primary',
                    'value' => '',
                    'help' => __('Enter your MailerLite API key here.', 'text-domain'),
                ),
                'mailerlite_group_id' => array(
                    'name' => 'mailerlite_group_id',
                    'type' => 'textbox',
                    'label' => __('MailerLite Group ID (Optional)', 'text-domain'),
                    'width' => 'full',
                    'group' => 'primary',
                    'value' => '',
                    'help' => __('Enter the MailerLite Group ID if you want to add the subscriber to a specific group.', 'text-domain'),
                ),
            );

            // Add a filter to ensure the settings are rendered
            add_filter('ninja_forms_action_settings', array($this, 'filter_settings'), 10, 1);
        }

        public function filter_settings($settings)
        {
            $settings[$this->_name] = $this->_settings;
            return $settings;
        }

        public function process($action_settings, $form_id, $form_data)
        {
            $api_key = sanitize_text_field($action_settings['mailerlite_api_key'] ?? '');
            $group_id = sanitize_text_field($action_settings['mailerlite_group_id'] ?? '');

            // Extract email and name from form submission
            $fields = $form_data['fields'];
            $email = '';
            $name = '';

            foreach ($fields as $field) {
                if ($field['key'] == 'email') {
                    $email = sanitize_email($field['value']);
                }
                if ($field['key'] == 'name') {
                    $name = sanitize_text_field($field['value']);
                }
            }

            if (empty($email)) {
                return; // Stop if no email is provided
            }

            // MailerLite API URL
            $url = "https://api.mailerlite.com/api/v2/subscribers";

            // Prepare subscriber data
            $subscriber_data = [
                'email' => $email,
                'name' => $name,
                'resubscribe' => true, // Allows re-adding if unsubscribed
            ];

            if (!empty($group_id)) {
                $subscriber_data['groups'] = [$group_id];
            }

            // Send data using WP HTTP API
            $response = wp_remote_post($url, [
                'method'    => 'POST',
                'headers'   => [
                    'Content-Type'  => 'application/json',
                    'X-MailerLite-ApiKey' => $api_key,
                ],
                'body'      => json_encode($subscriber_data),
                'timeout'   => 10,
            ]);

            if (is_wp_error($response)) {
                error_log('MailerLite API Error: ' . $response->get_error_message());
            }
        }
    }
}
