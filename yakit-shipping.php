<?php
/**
 * Plugin Name: Yakit
 * Plugin URI: https://www.yakit.com/woocommerce/yakit-for-woocommerce/
 * Description: * Transparent pricing with no minimums or monthly charges. * Guaranteed duties and taxes along with shipping cost in your shopping cart. * Ship internationally to more than 45 countries now!
 * Version: 1.2.3
 * Author: Yakit
 * Author URI: https://www.yakit.com/
 * License: GPLv2 or later
 * License URI: https://www.yakit.com
 * Domain Path: yakit.com
 * Text Domain: yakit
 */
 //echo __FILE__;exit;

if ( ! defined( 'WPINC' ) ) {
 
    die;
 
}
/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
 
    function yakit_shipping_method() {
        if ( ! class_exists( 'yakit_Shipping_Method' ) ) {
            class yakit_Shipping_Method extends WC_Shipping_Method {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct($instance_id = 0) {
                    $this->id                 = 'yakit'; 
					$this->instance_id = absint( $instance_id );
                    $this->method_title       = __( 'Yakit Shipping', 'yakit' );  
                    $this->method_description = __( 'Custom Shipping Method for Yakit', 'yakit' ); 
					$this->supports = array('shipping-zones','instance-settings');
                    $this->init();
 
                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Yakit Shipping', 'yakit' );
				}
				
 
                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    // Load the settings API
                    $this->init_form_fields(); 
                    $this->init_settings(); 
					
                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}
		        /**
                 * Define settings field for this shipping
                 * @return void 
                 */
                function init_form_fields() { 
                    $this->instance_form_fields = array( 
                     'enabled' => array(
                          'title' => __( 'Enable', 'yakit' ),
                          'type' => 'checkbox',
                          'description' => __( 'Enable this shipping.', 'yakit' ),
                          'default' => 'yes'
                          ),
					/*'accountname' => array(
                        'title' => __( 'Account name/Username', 'yakit' ),
                          'type' => 'text',
                          'description' => __( 'Yakit account username', 'yakit' ),
                          'default' =>'' 
                          ),
					'accountkey' => array(
                        'title' => __( 'Account Key', 'yakit' ),
                          'type' => 'text',
                          'description' => __( 'Yakit account key', 'yakit' ),
                          'default' => ''
                          ),*/
                    'title' => array(
                        'title' => __( 'Title', 'yakit' ),
                          'type' => 'text',
                          'description' => __( 'Title to be display on site', 'yakit' ),
                          'default' => __( 'Yakit Shipping', 'yakit' )
                          ),
                    );
                 }
				 
				
				
				
                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 * Service type based rate listed
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping( $package=array() ) {
					global $woocommerce;
					$this->instance_settings = get_option( $this->get_instance_option_key(), null );
                    //$user_name = $this->instance_settings['accountname'];
                    //$password = $this->instance_settings['accountkey'];
					$user_name = get_option('yakit_account_username');
					$password = get_option('yakit_account_key');
					$url = trim("https://shipping.yakit.com/shipperInterface/woocommerceRateProvider");
					$data['currencyUnit']=get_option('woocommerce_currency');
					$data['shipmentWeightUnit']=get_option('woocommerce_weight_unit');
					$data['ttd']='ALL';
					$data['region']=$woocommerce->customer->get_shipping_state();
					$dt=$woocommerce->customer->get_shipping_country();

					if(is_array($dt)) $data['countryList']=$dt;
					else $data['countryList']=array($dt);
					//print_r($data['countryList']);
					$data['items']=array();
					//echo "IN";exit;

					$default = wc_get_base_location();
					$coo=$default['country'];
					foreach($package['contents'] as $item_id => $values ) {
						$_product = $values['data'];
						$weight = $weight + $_product->get_weight() * $values['quantity'];
						$wt_unit=get_option('woocommerce_weight_unit');
						$curnt_item=array('productId'=>$_product->get_sku(),'displayName'=>$_product->get_title(),'description'=>$_product->get_title(),'countryOfOrigin'=>$coo,'itemWeight'=>$_product->get_weight(),'itemWeightUnit'=>$wt_unit,'value'=>$_product->get_price(),'quantity'=>$values['quantity']);
						$dimensions=$_product->get_dimensions();
						if(!empty($_product->get_length()) && !empty($_product->get_width()) && !empty($_product->get_height())){
							$length=$_product->get_length();
							$width=$_product->get_width();
							$height=$_product->get_height();
						}
						$dimunit=get_option('woocommerce_dimension_unit');
						$shipment_weight = $shipment_weight + ($_product->get_weight()*$values['quantity']);
						array_push($data['items'],$curnt_item);
					}
					$data['shipmentWeight']=$shipment_weight;
					$data['length']= $length;
					$data['width']= $width;
					$data['height']= $height;
					$data['dimUnit']= $dimunit;
					$data['storeURL']= get_option( 'siteurl' );
					$data['storeRootPath']= get_permalink( woocommerce_get_page_id('shop'));

					$check = json_encode($data);
				
					
	
					$args = array(
                                'method'      => 'POST',
                                'headers'     => array(
                                'Content-Type' => 'application/json',
                                'version' => 2,
                                'Authorization' => 'Basic ' . base64_encode($user_name . ":" . $password)),
                                'body'        => $check,
                                'sslverify'   => 'false',
								'timeout'	  => 10000
                             );
                    $res = wp_remote_get( $url, $args );
                    //$body = wp_remote_retrieve_body($res);
                    $http_code = wp_remote_retrieve_response_code( $res);
                    $return=$res['body'];
					

					$response = json_decode($return,true);
					if (!empty( $response['data'] )) {
						switch ($http_code) {
						case 200:  # OK
						$err_msg='';
						if(count($response['error'])>0){
							for($i=0;$i<count($response['error']);$i++){
								$err_msg .=$response['error'][$i]['message']."<br>";
							}
						}
						break;
						default:
						//$response = json_decode($return,true);
						$err_msg='';
						for($i=0;$i<count($response['error']);$i++){
							$err_msg .=$response['error'][$i]['message']."<br>";
						}
						break;
						}
					}
 
					if(isset($response['error'])){
					}else if(count($response)>0){
						$ar=0;
						$standard = array();
						$standard['currency'] = strtoupper($response['currencyEcho']);
						$express = array();
						$express['currency'] = strtoupper($response['currencyEcho']);
						if(is_array($response['ttdList'])){
							foreach($response['ttdList'] as $result){
									foreach ($result['countryList'] as $standardrate) {
                                        $standard['deliveryCharge']=number_format((float)$standardrate['deliveryCharge'], 2, '.', '');
                                        $standard['dutiesTaxes']=number_format((float)$standardrate['dutiesTaxes'], 2, '.', '');
                                        $standard['total_price'] = ($standardrate['deliveryCharge']+$standardrate['dutiesTaxes']);
                                        if($standard['total_price']==0) $standard_label = "Yakit Standard 6-14 days: FREE";
                                        $standard['total_price'] =number_format((float)$standard['total_price'], 2, '.', '');
                                    }
									$rate = array(
                                                'id' => $this->id.":".$result['ttd'],
                                                'label' => $result['ttd'],
                                                'cost' => $standard['total_price']
                                                //'taxes' => $standard['dutiesTaxes']
                                            );
                                    $this->add_rate( $rate );
							}
						}
					}//else
                }
            }
        }
    }
	
    add_action( 'woocommerce_shipping_init', 'yakit_shipping_method' );
 
    function add_yakit_shipping_method( $methods ) {
        $methods['yakit'] = 'yakit_Shipping_Method';
//$methods = array('Standard','Express','Domestic');
        return $methods;
    }
 
    add_filter( 'woocommerce_shipping_methods', 'add_yakit_shipping_method' );

	function activation_redirect() {
		if(strpos($_REQUEST['plugin'],'yakit')!==false){
		$email=get_option('admin_email');
		$user=get_user_by('email',$email);
		$user_id=$user->ID?$user->ID:get_current_user_id();
        $store_url = get_option('siteurl');
        $endpoint = '/wc-auth/v1/authorize';
        $params = [
                    'app_name' => 'Yakit',
                    'scope' => 'read_write',
                    'user_id' => $user_id,
                    'return_url' => 'https://www.yakit.com/user/register?s_url='.get_option( 'siteurl' ).'&s_email='.get_option('admin_email').'&platform=woo',
                    'callback_url' => 'https://shipping.yakit.com/shipperInterface/woocommerceOAuth?s_url='.get_option( 'siteurl' ).'&s_email='.get_option('admin_email')
                    ];
        $query=json_encode($params);
        $query_string = http_build_query( $params );
        $url= $store_url . $endpoint . '?' . $query_string;
		exit(wp_redirect($url));
	}
    }
	add_action( 'activated_plugin', 'activation_redirect' );



add_action( 'woocommerce_email_before_order_table', 'add_link_back_to_order', 10, 2 );
function add_link_back_to_order( $order, $is_admin ) {
	//Only for admin emails
	if ( $is_admin ) {
		return;
	}
	if($order->status=='completed'){
	$tracking_url=get_post_meta( $order->id, 'woocommerce_yakit_tracking', true );
	//echo "<pre>";print_r($order);echo "</pre>";exit;	
	// Open the section with a paragraph so it is separated from the other content
	$link = '<h3>Tracking URL: </h3><p>';
	// Add the anchor link with the admin path to the order page
	$link .= '<a href="'.$tracking_url.'">';
	// Clickable text
	$link .= __( 'Click here to track your order', 'your_domain' );
	// Close the link
	$link .= '</a>';
	// Close the paragraph
	$link .= '</p>';
	// Return the link into the email
	echo $link;
	}
}


			// creating a new sub tab in API settings
	add_filter( 'woocommerce_get_sections_api', 'add_subtab' );
	//add_filter('woocommerce_get_sections_shipping','add_subtab');
	function add_subtab( $settings_tabs ) {
		$settings_tabs['yakit_settings'] = __( 'Yakit Settings', 'woocommerce-yakit-settings-tab' );
		return $settings_tabs;
	}


	add_action('admin_menu', 'register_yakit_submenu_page');

	function register_yakit_submenu_page() {
		add_submenu_page( 'woocommerce', 'Yakit Shipping Tool', 'Yakit Shipping Tool', 'manage_options', 'yakit-shipping-tool', 'yakit_submenu_page_callback' ); 
	}

	function Yakit_submenu_page_callback() {
		if(get_option('yakit_account_username')!=='' && get_option('yakit_account_key')!==''){
		echo '<h4>Start Shipping!</h4><form action="https://shipping.yakit.com/j_spring_security_check" method="POST" target="new">
		<input name="j_username" value="'.get_option('yakit_account_username').'" type="hidden"> 
		<input name="j_password" value="'.get_option('yakit_account_key').'" type="hidden">
		<div class="submit_text"></div> 
		<input value="Yakit Shipping Tool" title="Calculate rates and dispatch shipments" type="submit"></form>';
		}else{
			echo "Please setup the Yakit account by referreing Yakit plugin readme.txt";
		}
	}
	
	// adding settings (HTML Form)
	add_filter( 'woocommerce_get_settings_api', 'add_subtab_settings',10, 2 );
	function add_subtab_settings( $settings ) {
		$current_section = (isset($_GET['section']) && !empty($_GET['section']))? $_GET['section']:'';
		if ( $current_section == 'yakit_settings' ) {
			if(isset($_GET['au'])) update_option('yakit_account_username',$_GET['au']);
			if(isset($_GET['ak'])) update_option('yakit_account_key',$_GET['ak']);
			if(isset($_GET['au']) && isset($_GET['ak'])) echo '<h3 style="color:green;">Thank you for setting up Yakit account! Start shipping!</h3>';
			$custom_settings = array();
			$custom_settings[] = array( 'name' => __( 'Yakit Settings', 'text-domain' ), 
                                   'type' => 'title', 
                                   'desc' => __( 'The following account settings used to communicate with Yakit rate api.', 'text-domain' ), 
                                   'id' => 'yakit_settings'
                                  );

			$custom_settings[] = array(
                                    'name'     => __( 'Account Username', 'text-domain' ),
                                    'id'       => 'yakit_account_username',
                                    'type'     => 'text',
                                    'default'  => get_option('yakit_account_username'),

                                );
			$custom_settings[] = array(
                                    'name'     => __( 'Account Key', 'text-domain' ),
                                    'id'       => 'yakit_account_key',
                                    'type'     => 'text',
                                    'default'  => get_option('yakit_account_key'),

                                );
			$custom_settings[] = array( 'type' => 'sectionend', 'id' => 'test-options' );             
			return $custom_settings;
		} else {
			// If not, return the standard settings
			return $settings;
		}
	}
	

    function yakit_validate_order( $posted )   {
 
        $packages = WC()->shipping->get_packages();
 
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
         
        if( is_array( $chosen_methods ) && in_array( 'yakit', $chosen_methods ) ) {
             
            foreach ( $packages as $i => $package ) {
 
                if ( $chosen_methods[ $i ] != "yakit" ) {
                             
                    continue;
                             
                }
 
                $yakit_Shipping_Method = new yakit_Shipping_Method();
                $weightLimit = (int) $yakit_Shipping_Method->settings['weight'];
                $weight = 0;
 
                foreach ( $package['contents'] as $item_id => $values ) 
                { 
                    $_product = $values['data']; 
                    $weight = $weight + $_product->get_weight() * $values['quantity']; 
                }
 
                $weight = wc_get_weight( $weight, 'kg' );
                
                if( $weight > $weightLimit ) {
 
                        $message = sprintf( __( 'Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'yakit' ), $weight, $weightLimit, $yakit_Shipping_Method->title );
                             
                        $messageType = "error";
 
                        if( ! wc_has_notice( $message, $messageType ) ) {
                         
                            wc_add_notice( $message, $messageType );
                      
                        }
                }
            }       
        } 
    }
	
    add_action( 'woocommerce_review_order_before_cart_contents', 'yakit_validate_order' , 10 );
    add_action( 'woocommerce_after_checkout_validation', 'yakit_validate_order' , 10 );
}