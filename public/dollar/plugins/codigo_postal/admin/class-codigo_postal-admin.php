<?php

require_once CP_PLUGIN_PATH . 'includes/class_state_page.php';
require_once CP_PLUGIN_PATH . 'admin/classes/class_cp_dashboard.php';
require_once CP_PLUGIN_PATH . 'includes/class_vk_template.php';

use vk_templates\Template;

class Codigo_postal_Admin {

	private $plugin_name;
	private $version;
	private $meta_keys;
	private $options;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->meta_keys = include CP_PLUGIN_PATH . 'data/meta_keys.php';
		$this->options = include CP_PLUGIN_PATH . 'data/options.php';
		define('SHORTCODE', '[geolocation]');

	}

	public function create_page_for_category( $term_id, $tt_id )
	{

		$term 		= get_term( $term_id );
		$is_state 	= get_term_meta( $term_id, $this->meta_keys['category_is_state'], true );
		$is_city	= $this->is_city( $term );

		if( (bool)$is_state === true || $is_city === true ) {

			$shortcodes = include CP_PLUGIN_PATH . 'data/shortcodes.php';

			$shortcode = $shortcodes['state_archive_page'];
			$post_meta_key = $this->meta_keys['term_id_in_page'];
			$term_meta_key = $this->meta_keys['page_id_in_term'];

			$page = new State_Page( $term );
			$result = $page->create(
				$shortcode,
				$post_meta_key,
				$term_meta_key
			);

		}

		if( $is_city === true ) {

			$state_page_id = get_term_meta( $term->parent, $this->meta_keys['page_id_in_term'], true );

			$postarr = array(

				'ID' => $result['post_id'],
				'post_parent' => (int)$state_page_id

			);

			wp_update_post( $postarr );

		}
	}

	public function update_page_for_category( $term_id, $tt_id )
	{

		$term = get_term( $term_id );
		$is_state = get_term_meta( $term_id, $this->meta_keys['category_is_state'], true );
		$is_city	= $this->is_city( $term );

		if( (bool)$is_state === true || $is_city === true ) {

			$page_id = get_term_meta( $term_id, $this->meta_keys['page_id_in_term'], true );
			$page = get_post( $page_id );

			if( !empty( $page_id ) && !empty( $page ) ) {

				$page = new State_Page( $term );
				$page->update( $page_id );

			}

		}
	}

	public function delete_page_for_category( $term_id, $taxonomy )
	{

		$term = get_term( $term_id );
		$is_state = get_term_meta( $term_id, $this->meta_keys['category_is_state'], true );
		$is_city	= $this->is_city( $term );

		if( (bool)$is_state === true || $is_city === true ) {

			$page = new State_Page( $term );
			$result = $page->delete( $this->meta_keys['page_id_in_term'] );

		}

	}

	public function include_acf_groups()
	{

		acf_add_local_field_group(array(
			'key' => 'group_60833ec0ea92a',
			'title' => 'Estado',
			'fields' => array(
				array(
					'key' => 'field_60833ec751215',
					'label' => 'Estado',
					'name' => $this->meta_keys['category_is_state'],
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => 'Esta categoría es para un estado',
					'default_value' => 0,
					'ui' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'taxonomy',
						'operator' => '==',
						'value' => 'category',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
		));

	}

	public function register_settings()
	{

		register_setting(
			'cp_settings_group',
			$this->options['common_category'],
		);

		register_setting(
			'cp_settings_group',
			$this->options['copo_page'],
		);

		register_setting(
			'cp_settings_group',
			$this->options['default_page'],
		);

		register_setting(
			'cp_settings_group',
			$this->options['zc_endpoint'],
		);

		add_settings_section(
			'cp_settings_section',
			'Opciones estados y municipios',
			[$this, 'echo_section_header'],
			'cp_options'
		);

		add_settings_field(
			'cp_common_category_field',
			'Categoría común a estados y municipios:',
			[$this, 'common_category_callback'],
			'cp_options',
			'cp_settings_section'
		);

		add_settings_field(
			'cp_copo_page',
			'Página COPO:',
			[$this, 'copo_page_callback'],
			'cp_options',
			'cp_settings_section'
		);

		add_settings_field(
			'cp_zc_endpoint',
			'Endpoint códigos postales:',
			[$this, 'zc_endpoint_callback'],
			'cp_options',
			'cp_settings_section'
		);

		add_settings_field(
			'cp_default_page',
			'Página por defecto:',
			[$this, 'default_page_callback'],
			'cp_options',
			'cp_settings_section'
		);

	}

	public function echo_section_header()
	{

		echo '';

	}

	public function common_category_callback()
	{

		$common_category = get_option( $this->options['common_category'] );

		$categories = get_categories(
			array(
				'hide_empty' => false,
				'parent' => 0
			)
		);

		$template = new Template();
        $view = $template->load(
			CP_PLUGIN_PATH . 'admin/templates/categories.php',
			array(
				'option_name' => $this->options['common_category'],
				'common_category' => $common_category,
				'categories' => $categories
			)
		);

		echo $view;

	}

	public function copo_page_callback()
	{

		$copo_page = get_option( $this->options['copo_page'] );

		if( !isset( $copo_page ) || empty( $copo_page ) )
			echo '<input type="text" name="' .
					$this->options['copo_page'] .
					'" placeholder="Página COPO.">';
		else
			echo '<input type="text" name="' .
					$this->options['copo_page'] .
					'" placeholder="Página COPO." value="' .
					$copo_page . '">';

	}

	public function zc_endpoint_callback()
	{

		$endpoint = get_option( $this->options['zc_endpoint'] );

		if( !isset( $endpoint ) || empty( $endpoint ) )
			echo '<input type="text" name="' .
					$this->options['zc_endpoint'] .
					'" placeholder="Endpoint url">';
		else
			echo '<input type="text" name="' .
					$this->options['zc_endpoint'] .
					'" placeholder="Endpoint url" value="' .
					$endpoint . '">';

	}

	public function default_page_callback()
	{

		$default = get_option( $this->options['default_page'] );

		if( !isset( $default ) || empty( $default ) )
			echo '<input type="text" name="' .
					$this->options['default_page'] .
					'" placeholder="Página por defecto">';
		else
			echo '<input type="text" name="' .
					$this->options['default_page'] .
					'" placeholder="Página por defecto" value="' .
					$default . '">';

	}

	public function register_menu_page()
	{
		$options_dashboard = new Cp_Dashboard( $this->plugin_name, $this->version );

		wp_enqueue_script( $this->plugin_name . '_jquery_ajax',
			plugin_dir_url( __FILE__ ) . 'assets/js/codigo_postal-admin.js',
			array( 'jquery' ),
			$this->version,
			false );

		add_menu_page(
			'Codigo Postal',
			'Codigo Postal',
			'manage_options',
			'cp_options',
			[$options_dashboard, 'load_template'],
			'',
			6
		);
	}

	private function is_city( $term )
	{

		if( $term->parent === 0  )
			return false;

		$is_parent_state = get_term_meta( $term->parent, $this->meta_keys['category_is_state'], true );

		if( (bool)$is_parent_state !== true )
			return false;

		return true;

	}
	public function display_location($content) {
			if (is_page()) {
				return $content;
			} else {
					return $this->display_location_post($content);
			}
	}
	public function display_location_post($content){
		global $post;
		$html = '';
		settype($html, "string");


		$latitude = $this->clean_coordinate(get_post_meta($post->ID, 'geo_latitude', true));
		$longitude = $this->clean_coordinate(get_post_meta($post->ID, 'geo_longitude', true));
		$on = (bool) get_post_meta($post->ID, 'geo_enabled', true);
		$public = (bool) get_post_meta($post->ID, 'geo_public', true);

		if (((empty($latitude)) || (empty($longitude))) ||
				($on === '' || $on === false) ||
				($public === '' || $public === false)) {
				$content = str_replace(SHORTCODE, '', $content);
				return $content;
		}

		$address = (string) get_post_meta($post->ID, 'geo_address', true);
		if (empty($address)) {
				$address = $this->reverse_geocode($latitude, $longitude);
		}


		$html = '<amp-iframe width="600" height="400" layout="responsive" sandbox="allow-scripts allow-same-origin allow-popups" frameborder="0" src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDX0A027JvK91Zn79dcjW4R-2Hrn3w2Mv4&center='.$latitude.','.$longitude.'&q='.$address.'"></amp-iframe>';
		$content = str_replace(SHORTCODE, '', $content);
		$content = $content.'<br/><br/>'.$html;

		return $content;
	}

	public function clean_coordinate($coordinate) {
			$pattern = '/^(\-)?(\d{1,3})\.(\d{1,15})/';
			preg_match($pattern, $coordinate, $matches);
			return $matches[0];
	}
	public function reverse_geocode($latitude, $longitude) {
			$json = $this->pullGoogleJSON($latitude, $longitude);
			$city = '';
			$state = '';
			$country = '';
			$address = "";
			foreach ($json->results as $result)
			{
					if(isset($result->formatted_address)){
						$address = $result->formatted_address;
						break;
					}
			}
			return $address;
	}
	public function buildAddress($city, $state, $country) {
	    $address = '';
	    if (($city != '') && ($state != '') && ($country != '')) {
	            $address = $city.', '.$state.', '.$country;
	    } else if (($city != '') && ($state != '')) {
	            $address = $city.', '.$state;
	    } else if (($state != '') && ($country != '')) {
	            $address = $state.', '.$country;
	    } else if ($country != '') {
	            $address = $country;
	    }
	    return esc_html($address);
	}
	public function pullGoogleJSON($latitude, $longitude) {
			$lang = $this->getSiteLang();
			$url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDX0A027JvK91Zn79dcjW4R-2Hrn3w2Mv4&language=".$lang."&latlng=".$latitude.",".$longitude;
			$decoded = json_decode(wp_remote_get($url)['body']);
			return $decoded;
	}
	public function getSiteLang() {
			$language = substr(get_locale(), 0, 2);
			return $language;
	}
	public function admin_head() {
	    global $post;
	    $post_id = $post->ID;
	    $zoom = 16;
	    echo '		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDX0A027JvK91Zn79dcjW4R-2Hrn3w2Mv4"></script>'; ?>
			<script type="text/javascript">
			 	var $j = jQuery.noConflict();
				$j(function() {
					$j(document).ready(function() {
					    var hasLocation = false;
						var center = new google.maps.LatLng(0.0,0.0);
						var postLatitude =  '<?php echo esc_js((string) get_post_meta($post_id, 'geo_latitude', true)); ?>';
						var postLongitude =  '<?php echo esc_js((string) get_post_meta($post_id, 'geo_longitude', true)); ?>';
	                    var isPublic = '<?php echo esc_js((string)get_post_meta($post_id, 'geo_public', true)); ?>';
	                    var isGeoEnabled = '<?php echo esc_js((string)get_post_meta($post_id, 'geo_enabled', true)); ?>';

	                    if (isPublic === '0')
							$j("#geolocation-public").attr('checked', false);
						else
							$j("#geolocation-public").attr('checked', true);

	                    if (isGeoEnabled === '0')
							disableGeo();
						else
							enableGeo();

	                    if ((postLatitude !== '') && (postLongitude !== '')) {
							center = new google.maps.LatLng(postLatitude, postLongitude);
							hasLocation = true;
							$j("#geolocation-latitude").val(center.lat());
							$j("#geolocation-longitude").val(center.lng());
							reverseGeocode(center);
						}

					 	var myOptions = {
					      'zoom': <?php echo $zoom; ?>,
					      'center': center,
					      'mapTypeId': google.maps.MapTypeId.ROADMAP
					    };
					    var image = '<?php echo esc_js(esc_url(plugins_url('img/wp_pin.png', __FILE__))); ?>';
					    var shadow = new google.maps.MarkerImage('<?php echo esc_js(esc_url(plugins_url('img/wp_pin_shadow.png', __FILE__))); ?>',
							new google.maps.Size(39, 23),
							new google.maps.Point(0, 0),
							new google.maps.Point(12, 25));

					    var map = new google.maps.Map(document.getElementById('geolocation-map'), myOptions);
						var marker = new google.maps.Marker({
							position: center,
							map: map,
							title:'Post Location'<?php if ((bool) get_option('geolocation_wp_pin')) { ?>,
							icon: image,
							shadow: shadow
						<?php } ?>
						});

						if((!hasLocation) && (google.loader.ClientLocation)) {
					      center = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
					      reverseGeocode(center);
					    }
					    else if(!hasLocation) {
					    	map.setZoom(1);
					    }

						google.maps.event.addListener(map, 'click', function(event) {
							placeMarker(event.latLng);
						});

						var currentAddress;
						var customAddress = false;
						$j("#geolocation-address").click(function(){
							currentAddress = $j(this).val();
	                        if (currentAddress !== '')
								$j("#geolocation-address").val('');
						});

						$j("#geolocation-load").click(function(){
	                        if ($j("#geolocation-address").val() !== '') {
								customAddress = true;
								currentAddress = $j("#geolocation-address").val();
								geocode(currentAddress);
							}
						});

						$j("#geolocation-address").keyup(function(e) {
	                        if (e.keyCode === 13)
								$j("#geolocation-load").click();
						});

						$j("#geolocation-enabled").click(function(){
							enableGeo();
						});

						$j("#geolocation-disabled").click(function(){
							disableGeo();
						});

						function placeMarker(location) {
							marker.setPosition(location);
							map.setCenter(location);
	                        if ((location.lat() !== '') && (location.lng() !== '')) {
								$j("#geolocation-latitude").val(location.lat());
								$j("#geolocation-longitude").val(location.lng());
							}

							if(!customAddress)
								reverseGeocode(location);
						}

						function geocode(address) {
							var geocoder = new google.maps.Geocoder();
							var latitude = 0;
							var longitude = 0;
						  if (geocoder) {
								geocoder.geocode({"address": address}, function(results, status) {
	                                if (status === google.maps.GeocoderStatus.OK) {
										placeMarker(results[0].geometry.location);
										latitude = results[0].geometry.location.lat();
										longitude = results[0].geometry.location.lng();
										if(!hasLocation) {
									    	map.setZoom(16);
									    	hasLocation = true;
										}
									}
								});
							}
							$j("#geodata").html(latitude + ', ' + longitude);
						}

						function reverseGeocode(location) {
							var geocoder = new google.maps.Geocoder();
						    if (geocoder) {
								geocoder.geocode({"latLng": location}, function(results, status) {
	                                if (status === google.maps.GeocoderStatus.OK) {
								  if(results[1]) {
								  	var address = results[1].formatted_address;
	                                  if (address === "") {
								  		address = results[7].formatted_address;
	                                  } else {
										$j("#geolocation-address").val(address);
										placeMarker(location);
								  	}
								  }
								}
								});
							}
						}

						function enableGeo() {
							$j("#geolocation-address").removeAttr('disabled');
							$j("#geolocation-load").removeAttr('disabled');
							$j("#geolocation-map").css('filter', '');
							$j("#geolocation-map").css('opacity', '');
							$j("#geolocation-map").css('-moz-opacity', '');
							$j("#geolocation-public").removeAttr('disabled');
							$j("#geolocation-map").removeAttr('readonly');
							$j("#geolocation-disabled").removeAttr('checked');
							$j("#geolocation-enabled").attr('checked', 'checked');

	                        if (isPublic === '1')
								$j("#geolocation-public").attr('checked', 'checked');
						}

						function disableGeo() {
							$j("#geolocation-address").attr('disabled', 'disabled');
							$j("#geolocation-load").attr('disabled', 'disabled');
							$j("#geolocation-map").css('filter', 'alpha(opacity=50)');
							$j("#geolocation-map").css('opacity', '0.5');
							$j("#geolocation-map").css('-moz-opacity', '0.5');
							$j("#geolocation-map").attr('readonly', 'readonly');
							$j("#geolocation-public").attr('disabled', 'disabled');

							$j("#geolocation-enabled").removeAttr('checked');
							$j("#geolocation-disabled").attr('checked', 'checked');

	                        if (isPublic === '1')
								$j("#geolocation-public").attr('checked', 'checked');
						}
					});
				});
			</script>
			<?php
	}
	public function geolocation_save_postdata($post_id) {
	    // Check authorization, permissions, autosave, etc
	    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
	        (('page' == $_POST['post_type']) && (!current_user_can('edit_page', $post_id))) ||
	        (!current_user_can('edit_post', $post_id))) {
	        return $post_id;
	    }

	    $latitude = $this->clean_coordinate($_POST['geolocation-latitude']);
	    $longitude = $this->clean_coordinate($_POST['geolocation-longitude']);
	    $address = $this->reverse_geocode($latitude, $longitude);
	    $public = $_POST['geolocation-public'];
	    $on = $_POST['geolocation-on'];

	    if ((!empty($latitude)) && (!empty($longitude))) {
	        update_post_meta($post_id, 'geo_latitude', $latitude);
	        update_post_meta($post_id, 'geo_longitude', $longitude);

	        if ($address != '') {
	            update_post_meta($post_id, 'geo_address', $address);
	        }
	        if ($on) {
	            update_post_meta($post_id, 'geo_enabled', 1);
	        } else {
	            update_post_meta($post_id, 'geo_enabled', 0);
	        }
	        if ($public) {
	                update_post_meta($post_id, 'geo_public', 1);
	        } else {
	                update_post_meta($post_id, 'geo_public', 0);
	        }
	    }

	    return $post_id;
	}
}
