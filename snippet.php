<?php
// Register the custom action class in Ninja Forms
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
                    'label' => __('MailerLite Group IDs (Comma Separated, Optional)', 'text-domain'),
                    'width' => 'full',
                    'group' => 'primary',
                    'value' => '',
                    'help' => __('Enter the MailerLite Group IDs if you want to add the subscriber to a specific group. You can specify multiple groups separated by a comma.', 'text-domain'),
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
            $group_id_field = sanitize_text_field( $action_settings['mailerlite_group_id'] ?? '' );

            // Extract email and name from form submission
            $fields = $form_data['fields'];
            $id = '';
			$email = '';
            $name = '';
			$last_name = '';
			$church = '';
			$phone = '';
			$birth_date = '';
			$reg_days = [];
			$payment_type = '';
			$pay_amount = 0;
			$ministry_areas = [];
			$ministry_days = [];
			$children = '';
			$zip = '';
			$city = '';
			$address = '';

            foreach ($fields as $field) {
				 switch ( $field['key'] ) {
                    case 'identifier':
                        $id = sanitize_text_field( $field['value'] );
                        break;
                    case 'email':
                        $email = sanitize_email( $field['value'] );
                        break;
                    case 'name':
                        $name = sanitize_text_field( $field['value'] );
                        break;
                    case 'keresztnev':
                        $last_name = sanitize_text_field( $field['value'] );
                        break;
                    case 'church':
                        $church = sanitize_text_field( $field['value'] );
                        break;
                    case 'phone':
                        $phone = sanitize_text_field( $field['value'] );
                        break;
						 
                    case 'iranyitoszam':
                        $zip = sanitize_text_field( $field['value'] );
                        break;
                    case 'varos':
                        $city = sanitize_text_field( $field['value'] );
                        break;
                    case 'cim':
                        $address = sanitize_text_field( $field['value'] );
                        break;
						 
                    case 'payment_type':
                        $payment_type = sanitize_text_field( $field['value'] );
                        break;
                    case 'gyerekfelugyelet':
                        $children = (sanitize_text_field( $field['value'] ) == '1' ? 'igen' : 'nem');
                        break;
                    case 'pay_amount':
                        $pay_amount = intval( $field['value'] );
                        break;
                    case 'birth_date':
                        $date_raw = sanitize_text_field( $field['value'] );
						 
                        // Attempt to parse using a couple of common formats.
                        // Adjust these format strings to match how your form outputs dates.
                        $date_obj = DateTime::createFromFormat('d/m/Y', $date_raw);
                        if ( ! $date_obj ) {
                            $date_obj = DateTime::createFromFormat('Y-m-d', $date_raw);
                        }
						if ( ! $date_obj ) {
                            $date_obj = DateTime::createFromFormat('Y.m.d', $date_raw);
                        }
						 
                        if ( $date_obj ) {
                            $birth_date = $date_obj->format('Y-m-d');
                        } else {
                            error_log("Failed to parse date: " . $date_raw);
                        }
						 
                        break;
                    case 'reg_days':
                        // If multiple checkboxes are used, expect an array of values.
                        if ( is_array( $field['value'] ) ) {
                            $reg_days = array_map( 'sanitize_text_field', $field['value'] );
                        } else {
                            // If not an array, split a comma-separated string.
                            $reg_days = array_map( 'sanitize_text_field', explode( ',', $field['value'] ) );
                        }
                        break;
                    case 'ministry_days':
                        // If multiple checkboxes are used, expect an array of values.
                        if ( is_array( $field['value'] ) ) {
                            $ministry_days = array_map( 'sanitize_text_field', $field['value'] );
                        } else {
                            // If not an array, split a comma-separated string.
                            $ministry_days = array_map( 'sanitize_text_field', explode( ',', $field['value'] ) );
                        }
                        break;
                    case 'ministry_areas':
                        // If multiple checkboxes are used, expect an array of values.
                        if ( is_array( $field['value'] ) ) {
                            $ministry_areas = array_map( 'sanitize_text_field', $field['value'] );
                        } else {
                            // If not an array, split a comma-separated string.
                            $ministry_areas = array_map( 'sanitize_text_field', explode( ',', $field['value'] ) );
                        }
                        break;
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
                'resubscribe' => false, // Disables re-adding if unsubscribed
				'fields'      => [
					'id'            => $id,
					'last_name'	    => $last_name,
                    'church'        => $church, 
					'phone'         => $phone,
					'zip'           => $zip,
					'city'          => $city,
					'address'       => $address,
					'birthday'      => $birth_date,
					'payment_type'  => $payment_type,
					'pay_amount'    => $pay_amount,
					'children'      => $children,
					'paid'          => 'false',
                    'regdays'       => implode( ',', $reg_days ),       // Multiple checkboxes as comma-separated values
					'ministry_days' => implode( ',', $ministry_days ),  // Multiple checkboxes as comma-separated values
					'ministries'    => implode( ',', $ministry_areas ), // Multiple checkboxes as comma-separated values
                ],
            ];

			 // Process group IDs from a comma-separated list.
            if ( ! empty( $group_id_field ) ) {
                $group_ids = array_map( 'trim', explode( ',', $group_id_field ) );
                $group_ids = array_filter( $group_ids ); // Remove any empty values.
                if ( ! empty( $group_ids ) ) {
                    $subscriber_data['groups'] = $group_ids;
                }
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
