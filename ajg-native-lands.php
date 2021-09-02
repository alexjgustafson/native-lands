<?php

/*
Plugin Name: Native Lands Search
Plugin URI: https://github.com/alexjgustafson/native-lands
Description: A plugin supplying experiences related to native lands for Pipewrench Magazine
Version: 1.1.3
Author: Alex J. Gustafson Tech, LLC
Author URI: https://www.alexjgustafson.tech
License: GPL v2 or later
*/

namespace Ajg;

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Http\Adapter\Guzzle7\Client;
use Geocoder\Model\AddressCollection;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Collection;

include 'blocks/ajg-native-lands-search/ajg-native-lands-search.php';

class NativeLands{

	const NATIVE_LAND_URL = 'https://native-land.ca/api/index.php';

	const OPTIONS_NAME = 'ajgnl_options';

	public function __construct(){
		add_action('admin_init', [__CLASS__,'settingsInit']);
		add_action( 'admin_menu', [__CLASS__,'addOptionsPage'] );
		add_action('wp_ajax_native_land_search', [__CLASS__,'handleTerritorySearch'] );
		add_action('wp_ajax_nopriv_native_land_search', [__CLASS__,'handleTerritorySearch'] );
		add_action('wp_enqueue_scripts', [__CLASS__,'enqueueScripts']);
		$this->registerBlockPatterns();
	}

	public static function init(){
		$instance = new self;
	}

	public static function enqueueScripts(){
		if(is_admin()) return;
		if(!has_block('ajgnl/ajg-native-lands-search')) return;
   		wp_enqueue_script( 'ajg-native-lands', plugin_dir_url( __FILE__ ) . 'index.js', ['jquery'], null, true );

   		wp_localize_script(
   			'ajg-native-lands',
			'ajgnl_ajax',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'spinner_url' => site_url('/wp-includes/images/spinner-2x.gif'),
			]
		);
	}

	public static function getGeocodeResult(string $address): Collection {
		$adapter  = new Client(new GuzzleClient());
		$geocoder = new GoogleMaps($adapter, null, self::getGeocodeApiKey());
		try{
			$result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
		}
		catch(\Geocoder\Exception\Exception $e){
			return new AddressCollection([]);
		}
		return  $result;
	}

	public static function getAddressCoordinates(string $address) {
		$geocode = self::getGeocodeResult($address);
		if(!$geocode->count())
			return null;
		return $geocode->get(0)->getCoordinates();
	}

	public static function getNativeLandTerritoriesByPosition(string $lat, string $lng){
		$client = new GuzzleClient();
		try{
			$res = $client->request('GET',self::NATIVE_LAND_URL, [
				'query'	=>	[
					'maps' => 'territories',
					'position'	=>	$lat . ',' . $lng,
				],
			]);
		}
		catch(RequestException $e){
			return new \WP_Error($e->getCode(),$e->getMessage(),$e);
		}

		return $res->getBody()->getContents();
	}

	public static function handleTerritorySearch(){
		if(isset($_POST['address'])){
			$address = sanitize_text_field( $_POST['address'] );
			$coordinates = self::getAddressCoordinates($address);
			if(!$coordinates){
				wp_send_json_error('no coordinates');
			}

			$nativeLandInfo = self::getNativeLandTerritoriesByPosition(strval($coordinates->getLatitude()), strval($coordinates->getLongitude()));
			if(is_wp_error($nativeLandInfo)){
				wp_send_json_error($nativeLandInfo);
			}
			wp_send_json_success($nativeLandInfo);
		} else {
			wp_send_json_error('no address');
		}
	}

	public function registerBlockPatterns(){
		register_block_pattern(
			'ajg-native-lands/search-aside',
			array(
				'title'       => __( 'Native Lands Aside', 'ajg-native-lands' ),
				'description' => _x( 'Two-column layout with the Native Lands Search within a Cover block.', 'Block pattern description', 'ajg-native-lands' ),
				'content'     => '<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"42%"} -->
<div class="wp-block-column" style="flex-basis:42%"><!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In scelerisque sapien at massa dictum feugiat. Nunc vitae sem maximus, semper metus ut, condimentum lorem. Maecenas sit amet vehicula nunc.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"58%"} -->
<div class="wp-block-column" style="flex-basis:58%"><!-- wp:cover {"customOverlayColor":"#4d5913","style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"}}}} -->
<div class="wp-block-cover has-background-dim" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;background-color:#4d5913"><div class="wp-block-cover__inner-container"><!-- wp:ajgnl/ajg-native-lands-search {"blockId":"b0684c23-0e40-4412-96ff-5f71ff2ab4c7"} -->
<div class="wp-block-ajgnl-ajg-native-lands-search"><div class="wp-block-ajgnl-ajg-native-lands-search__intro"><h4 class="wp-block-ajgnl-ajg-native-lands-search__headline">Are you on unceded indigenous land?</h4><p class="wp-block-ajgnl-ajg-native-lands-search__description">Enter a street address, town, or zip code to see whose.</p></div><form action="javascript:void(0);" class="wp-block-ajgnl-ajg-native-lands-search__search" data-ajgnls-search="b0684c23-0e40-4412-96ff-5f71ff2ab4c7"><input type="text" data-ajgnls-query="b0684c23-0e40-4412-96ff-5f71ff2ab4c7"/><input type="submit" value="Tell me." data-ajgnls-submit="b0684c23-0e40-4412-96ff-5f71ff2ab4c7"/></form><p class="wp-block-ajgnl-ajg-native-lands-search__error">There was a problem searching for that location.<br/><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p><p class="wp-block-ajgnl-ajg-native-lands-search__no-results">There are no indigenous nations that correspond to that location; results are most likely for folks in the Americans and Oceania.<br/><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p><div class="wp-block-ajgnl-ajg-native-lands-search__success"><p>That location has historically been the home of the:</p><ul class="wp-block-ajgnl-ajg-native-lands-search__list"></ul><p><a href="https://native-land.ca" target="_blank" rel="noopener">Learn more -- explore the full map.</a></p></div></div>
<!-- /wp:ajgnl/ajg-native-lands-search --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
			)
		);
	}

	public static function settingsInit(){
		register_setting( 'ajg_native_lands', self::OPTIONS_NAME );
		add_settings_section(
			'ajgnl_api_keys',
			__( 'API Keys', 'ajg_native_lands' ), [__CLASS__, 'apiKeysSettingsSection'],
			'ajg_native_lands'
		);
		add_settings_field(
			'ajgnl_geocoding_api_key', // As of WP 4.6 this value is used only internally.
			// Use $args' label_for to populate the id inside the callback.
			__( 'Google Maps Geocoding API Key', 'ajg_native_lands' ),
			[__CLASS__,'geocodingApiKeyField'],
			'ajg_native_lands',
			'ajgnl_api_keys',
			[
				'label_for'         => 'ajgnl-geocoding-api-key',
				'class'             => 'ajgnl',
			]
		);
	}

	public static function addOptionsPage(){
		add_submenu_page(
			'options-general.php',
			'Native Lands Search',
			'Native Lands',
			'manage_options',
			'ajg-native-lands',
			[__CLASS__, 'optionsPageHtml']
		);
	}

	public static function optionsPageHtml() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'ajg_native_lands' );
				do_settings_sections( 'ajg_native_lands' );
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	public static function apiKeysSettingsSection($args){
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Third-party services are required to use this tool as expected.', 'ajg-native-lands' ); ?></p>
		<?php
	}

	public static function geocodingApiKeyField($args){
		$options = get_option( self::OPTIONS_NAME );
		?>
		<input type="text"
			   id="<?php echo esc_attr( $args['label_for'] ); ?>"
			   name="<?php echo self::OPTIONS_NAME; ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
			   value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr($options[ $args['label_for'] ]) : ''; ?>"
		/>
		<?php
	}

	public static function getGeocodeApiKey(){
		$fieldName = 'ajgnl-geocoding-api-key';
		$options = get_option( self::OPTIONS_NAME );
		return isset( $options[ $fieldName ] ) ? esc_attr($options[ $fieldName ]) : '';
	}
}

add_action('init', [NativeLands::class, 'init']);
