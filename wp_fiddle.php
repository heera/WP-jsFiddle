<?php
/**
 * @package WP_Fiddle
 * @version 1.0
 */
/*
Plugin Name: WP Fiddle
Description: JS Fiddle in your WordPress Blog.
Author: Heera
Version: 1.0
Author URI: http://heera.it/
*/
function add_fiddle_menu()
{
	add_menu_page( 'WP Fiddle', 'WP Fiddle', 1, 'wp_fiddle_settings', 'wp_fiddle_function', plugins_url( '/js/images/jsfiddle-icon.png', __FILE__ ));
	wp_enqueue_script('jquery-ui-sortable');
}
add_action( 'admin_menu', 'add_fiddle_menu' );

function set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
	if ($file == $plugin) {
		return array_merge(
			$links,
			array( sprintf( '<a href="admin.php?page=wp_fiddle_settings">%s</a>', __('Settings') ) )
		);
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'set_plugin_meta', 10, 2 );

function wp_fiddle_function()
{
	wp_enqueue_script( 'wp_jsFiddle_settings', plugins_url( '/js/wp_jsFiddle_settings.js', __FILE__ ));
	$base_elements=array('result'=>'result', 'javascript'=>'javascript', 'html'=>'html', 'css'=>'css');
	$option_name='fiddle_options';
	if(isset($_POST['btn_submit']))
	{
		update_option($option_name, $_POST[$option_name]);
		update_option('fiddle_theme', $_POST['fiddle_theme']);
		update_option('jsFiddleOff', $_POST['jsFiddleOff']);	
	}
	$current_opt=get_option($option_name, true);
	if(is_array($current_opt) && count($current_opt) > 0)
	{
		foreach($current_opt as $op) unset($base_elements[$op]);
		$base_elements_new=$base_elements;
		$base_elements=$current_opt+$base_elements_new;
		$tabs=implode(',', $current_opt);
		$tabs=str_ireplace('javascript', 'js', $tabs);
	}
	else
	{
		$current_opt=array();
		$tabs='result,js,html,css';
	}
	$fiddle_theme=get_option('fiddle_theme');
	$jsFiddleOff=get_option('jsFiddleOff', true);
	if($jsFiddleOff && !is_array($jsFiddleOff)) $jsFiddleOff=array('frontPage', 'categoryPage', 'archivePage');
	
	?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>WP JS Fiddle Settings</h2><br />
			<form method="post" action="">
				<table class="widefat" id="tabSettings">
					<thead><th>Select tabs to display in the fiddle and set the display order by drag and drop</th></thead>
					<tbody>
						<?php foreach($base_elements as $el):?>
						<tr style="cursor:move">
							<td>
								<label>
									<input type="checkbox" name="fiddle_options[]" <?php if(in_array($el, $current_opt) || count($current_opt)==0) echo 'checked' ?> value="<?php echo $el?>" /> <?php echo ucfirst($el) ?>
								</label>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>			
				</table>
				<h4>To see the changes you have made in the tabs you must save your settings.</h4>
				<table class="widefat">
					<thead><th>Select a theme</th><th>Don't display fiddle in</th><th></th></thead>
					<tbody>
						<tr>
							<td style="padding:10px 0 10px 8px">
								<label>
									<input type="radio" name="fiddle_theme" value="light" <?php if(!$fiddle_theme || $fiddle_theme=='light') echo 'checked' ?> /> Light <small>(Small Code)</small>
								</label>
								<label style="margin-left:20px">
									<input type="radio" name="fiddle_theme" value="presentation" <?php if($fiddle_theme=='presentation') echo 'checked' ?> /> Presentation <small>(Bigger Code)</small>
								</label>
							</td>
							<td style="padding:10px 0 10px 8px;">
								<label title="Check if you don't want to display the fiddle in the front page">
									<input type="checkbox" name="jsFiddleOff[]" value="frontPage" <?php if(is_array($jsFiddleOff) && in_array('frontPage', $jsFiddleOff)) echo 'checked';?> /> Front Page
								</label>
								<label title="Check if you don't want to display the fiddle in the category page" style="margin-left:5px">
									<input type="checkbox" name="jsFiddleOff[]" value="categoryPage" <?php if(is_array($jsFiddleOff) && in_array('categoryPage', $jsFiddleOff)) echo 'checked';?> /> Category Page
								</label>
								<label title="Check if you don't want to display the fiddle in the archive page" style="margin-left:5px">
									<input type="checkbox" name="jsFiddleOff[]" value="archivePage" <?php if(is_array($jsFiddleOff) && in_array('archivePage', $jsFiddleOff)) echo 'checked';?> /> Archive Page
								</label>		
							</td>
							<td style="text-align:right;padding:10px 10px 10px 0">
								<input class="button-primary" type="submit" name="btn_submit" value="Save Settings" />
							</td>
						</tr>
					</tbody>
				</table>
				<br />
				<h2>How to use</h2>
				<p style="line-height:2em;text-align:justify">
					To insert a fiddle in your page/post just click the fiddle icon displayed in
					the toolbar of your edotor when it's in the visual mode and write/paste the
					link of your fiddle and then press "OK" button. It will insert
					<span style="background:#DFDFDF">[wp-js-fiddle url="your_link" style="width:100%; height:400px; border:solid #4173A0 1px;"]</span>
					in your editor, which will render an iframe in your page. Alternatively you can write the shortcode code manually with your link and also you can change the style, by default it's
					<span style="background:#DFDFDF">style="width:100%; height:400px; border:solid #4173A0 1px;"</span>. Click the Save Settings button to save your changes.
				</p>
				<h2>DEMO</h2>
				<div id="jsf" style="height:400px;border:solid #4173A0 1px">
					<iframe style='width: 100%; height: 100%;' src='http://jsfiddle.net/heera/VStJ5/9/embedded/<?php echo $tabs.'/'.$fiddle_theme ?>' frameborder='0'></iframe>
				</div>	
			</form>
		</div>
	<?php
}

add_action( 'init', 'register_js_fiddle_shortcode');
function register_js_fiddle_shortcode(){
	add_shortcode('wp-js-fiddle', 'wp_js_fiddle_function');
	my_js_fiddle_init();
}

function register_button($buttons) {
	array_push( $buttons, "|", "wpjsfiddle" );
	return $buttons;
}

function add_plugin($plugin_array) {
	$plugin_array['wpjsfiddle'] = plugins_url( 'js/wp_js_fiddle.js', __FILE__ );
	return $plugin_array;
}

function my_js_fiddle_init() {
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
		return;
	}
	if ( get_user_option('rich_editing') == 'true' ) {
		add_filter( 'mce_external_plugins', 'add_plugin' );
		add_filter( 'mce_buttons', 'register_button' );
	}
}

function wp_js_fiddle_function($atts)
{
	$jsFiddleOff=get_option('jsFiddleOff', true);
	if(is_array($jsFiddleOff))
	{
		if((in_array('frontPage', $jsFiddleOff) && is_front_page()) || (in_array('categoryPage', $jsFiddleOff) && is_category()) || (in_array('archivePage', $jsFiddleOff) && is_archive()))
			return;
	}	

	extract(shortcode_atts(array(
		'url' =>'',
		'style' =>''
	), $atts));
	$opt=get_option('fiddle_options', true);
	if(is_array($opt) && count($opt)>0) $tabs=implode(',', $opt);
	else $tabs='result,js,html,css';
	$fiddle_theme=get_option('fiddle_theme');
	$fiddle_theme=$fiddle_theme=='' ? 'light' : $fiddle_theme;
	if (substr($url, -1) == '/') $url = substr($url, 0, -1);
	$url=$url."/embedded/".str_ireplace('javascript', 'js', $tabs)."/".$fiddle_theme."/";
	$return_string.="<iframe style='".$style."' src='".$url."' frameborder='0'></iframe>";
	return $return_string;
}