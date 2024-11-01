<?php
/*
Plugin Name: Trainingstagebuch
Plugin URI: http://www.christianschenk.org/projects/wordpress-trainingstagebuch-plugin/
Description: Trainigsdaten in deinem Blog.
Version: 0.8
Author: Christian Schenk
Author URI: http://www.christianschenk.org/
*/

#
# WordPress Trainingstagebuch plugin
# Copyright (C) 2009-2012 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#


# Identifier fuer den Benutzername und Passwort
define('OPTION_TRAININGSTAGEBUCH_USERNAME', 'trainingstagebuch_username');
define('OPTION_TRAININGSTAGEBUCH_PASSWORD', 'trainingstagebuch_password');
# Identifier fuer grosses/kleines BORG-Bild
define('OPTION_TRAININGSTAGEBUCH_BORG_IMAGE', 'trainingstagebuch_borg_image');
# Identifier fuer die Anzeige
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_ALL', 'trainingstagebuch_display_summary_all');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_COUNT', 'trainingstagebuch_display_summary_count');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DURATION', 'trainingstagebuch_display_summary_duration');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DISTANCE', 'trainingstagebuch_display_summary_distance');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_TABLE_HEAD', 'trainingstagebuch_display_table_head');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_NUMBER', 'trainingstagebuch_display_number');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_DATE', 'trainingstagebuch_display_date');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_DURATION', 'trainingstagebuch_display_duration');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_HEARTRATE', 'trainingstagebuch_display_heartrate');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_SPEED', 'trainingstagebuch_display_speed');
define('OPTION_TRAININGSTAGEBUCH_DISPLAY_BORG', 'trainingstagebuch_display_borg');


/**
 * Trainingstagebuch init.
 */
function trainingstagebuch_init() {
	if (function_exists('load_plugin_textdomain')) {
		load_plugin_textdomain('trainingstagebuch', 'wp-content/plugins/trainingstagebuch/messages');
	}
}
if (function_exists('add_action')) add_action('init', 'trainingstagebuch_init');


/**
 * Fuegt eine Seite 'Trainingstagebuch' in den Admin-Bereich ein.
 */
function trainingstagebuch_add_options_page() {
	if(function_exists('add_options_page'))
		add_options_page('Trainingstagebuch', 'Trainingstagebuch', 5, basename(__FILE__), 'trainingstagebuch_show_options_page');
}
if (function_exists('add_action')) add_action('admin_menu', 'trainingstagebuch_add_options_page');


/**
 * Verwaltet die Optionsseite
 */
function trainingstagebuch_show_options_page() {
	$username = get_option(OPTION_TRAININGSTAGEBUCH_USERNAME);
	$password = get_option(OPTION_TRAININGSTAGEBUCH_PASSWORD);
	$borg_img = get_option(OPTION_TRAININGSTAGEBUCH_BORG_IMAGE);

	$display_summary_all      = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_ALL);
	$display_summary_count    = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_COUNT);
	$display_summary_duration = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DURATION);
	$display_summary_distance = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DISTANCE);

	$display_table_head = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_TABLE_HEAD);
	$display_number     = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_NUMBER);
	$display_date       = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DATE);
	$display_duration   = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DURATION);
	$display_heartrate  = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_HEARTRATE);
	$display_speed      = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SPEED);
	$display_borg       = get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_BORG);

	if(isset($_POST['updateoptions'])) {
		function getIsset($var) {
			if (!isset($var))
				return '0';
			else
				return '1';
		}

		$username = $_POST['username'];
		$password = $_POST['password'];
		$borg_img = $_POST['borg_img'];

		$display_summary_all      = getIsset($_POST['display_summary_all']);
		$display_summary_count    = getIsset($_POST['display_summary_count']);
		$display_summary_duration = getIsset($_POST['display_summary_duration']);
		$display_summary_distance = getIsset($_POST['display_summary_distance']);

		$display_table_head = getIsset($_POST['display_table_head']);
		$display_number     = getIsset($_POST['display_number']);
		$display_date       = getIsset($_POST['display_date']);
		$display_duration   = getIsset($_POST['display_duration']);
		$display_heartrate  = getIsset($_POST['display_heartrate']);
		$display_speed      = getIsset($_POST['display_speed']);
		$display_borg       = getIsset($_POST['display_borg']);

		update_option(OPTION_TRAININGSTAGEBUCH_USERNAME, $username);
		update_option(OPTION_TRAININGSTAGEBUCH_PASSWORD, $password);
		update_option(OPTION_TRAININGSTAGEBUCH_BORG_IMAGE, $borg_img);

		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_ALL, $display_summary_all);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_COUNT, $display_summary_count);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DURATION, $display_summary_duration);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DISTANCE, $display_summary_distance);

		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_TABLE_HEAD, $display_table_head);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_NUMBER, $display_number);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DATE, $display_date);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DURATION, $display_duration);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_HEARTRATE, $display_heartrate);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SPEED, $display_speed);
		update_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_BORG, $display_borg);

		echo '<div class="updated"><p><strong>'.__('Optionen gesichert', 'trainingstagebuch').'.</strong></p></div>';
	}
?>
<div class="wrap">
<h2>Trainingstagebuch</h2>
<form name="options" method="post" action="">
<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
	<tr valign="top">
		<th scope="row"><?php _e('Benutzername', 'trainingstagebuch'); ?>:</th>
		<td><input name="username" value="<?php echo $username; ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Passwort', 'trainingstagebuch'); ?>:</th>
		<td><input name="password" type="password" value="<?php echo $password; ?>" /></td>
	</tr>
</table>
<h3><?php _e('Optionen', 'trainingstagebuch'); ?></h3>
<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
	<tr valign="top">
		<th scope="row"><?php _e('Gr&ouml;&szlig;e des BORG-Bildes', 'trainingstagebuch'); ?>:</th>
		<td>
			<select name="borg_img">
  				<option <?php if ($borg_img == 'big') echo 'selected="selected" '; ?>value="big"><?php _e('gro&szlig;', 'trainingstagebuch'); ?></option>
  				<option <?php if ($borg_img == 'small') echo 'selected="selected" '; ?>value="small"><?php _e('klein', 'trainingstagebuch'); ?></option>
			</select>
		</td>
	</tr>
</table>

<script language="JavaScript">
function set_cbs(cb_parent) {
	var cbs = new Array(document.options.display_summary_count,
	                    document.options.display_summary_duration,
	                    document.options.display_summary_distance);
	for (var i = 0, n = cbs.length; i < n; i++) {
		cbs[i].disabled = (cb_parent.checked == true) ? 'disabled' : false;
	}
}
</script>

<h3><?php _e('Ausgabe', 'trainingstagebuch'); ?></h3>
<p><?php _e('Hier l&auml;sst sich die Ausgabe des Shortcodes anpassen.', 'trainingstagebuch'); ?></p>
<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
	<tr valign="top">
		<th scope="row"><?php _e('Summe aller Trainingseinheiten', 'trainingstagebuch'); ?>:</th>
		<td>
			<input type="checkbox" name="display_summary_all" value="1" onClick="set_cbs(this)" <?php if ($display_summary_all == true) echo 'checked="checked"'; ?>/> <?php _e('Alles', 'trainingstagebuch'); ?><br/>
			<div style="margin-left:2em;">
				<input type="checkbox" name="display_summary_count" value="1" <?php if ($display_summary_all == true) echo 'disabled="disabled"'; if ($display_summary_count == true) echo 'checked="checked"'; ?>/> <?php _e('Anzahl Einheiten', 'trainingstagebuch'); ?><br/>
				<input type="checkbox" name="display_summary_duration" value="1" <?php if ($display_summary_all == true) echo 'disabled="disabled"'; if ($display_summary_duration == true) echo 'checked="checked"'; ?>/> <?php _e('Zeit', 'trainingstagebuch'); ?><br/>
				<input type="checkbox" name="display_summary_distance" value="1" <?php if ($display_summary_all == true) echo 'disabled="disabled"'; if ($display_summary_distance == true) echo 'checked="checked"'; ?>/> <?php _e('Distanz', 'trainingstagebuch'); ?><br/>
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Tabellenkopf', 'trainingstagebuch'); ?>:</th>
		<td><input type="checkbox" name="display_table_head" value="1" <?php if ($display_table_head == true) echo 'checked="checked"'; ?>/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Nummer', 'trainingstagebuch'); ?>:</th>
		<td><input type="checkbox" name="display_number" value="1" <?php if ($display_number == true) echo 'checked="checked"'; ?>/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Datum', 'trainingstagebuch'); ?>:</th>
		<td><input type="checkbox" name="display_date" value="1" <?php if ($display_date == true) echo 'checked="checked"'; ?>/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Dauer', 'trainingstagebuch'); ?>:</th>
		<td><input type="checkbox" name="display_duration" value="1" <?php if ($display_duration == true) echo 'checked="checked"'; ?>/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Herzfrequenz', 'trainingstagebuch'); ?>:</th>
		<td><input type="checkbox" name="display_heartrate" value="1" <?php if ($display_heartrate == true) echo 'checked="checked"'; ?>/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('Geschwindigkeit', 'trainingstagebuch'); ?>:</th>
		<td><input type="checkbox" name="display_speed" value="1" <?php if ($display_speed == true) echo 'checked="checked"'; ?>/></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e('BORG-Bild', 'trainingstagebuch'); ?>:</th>
		<td><input type="checkbox" name="display_borg" value="1" <?php if ($display_borg == true) echo 'checked="checked"'; ?>/></td>
	</tr>
</table>
<p class="submit">
	<input type="submit" name="updateoptions" value="<?php _e('Speichern', 'trainingstagebuch'); ?>" />
</p>
</form>

<?php
if (!empty($username) and !empty($password)) {
	require_once('api/trainingstagebuch_api.php');
	$trainingstagebuch = new Trainingstagebuch($username, $password);
	$trainingstagebuchStatus = $trainingstagebuch->test();
	$cache = new TrainingstagebuchCache($trainingstagebuch);
	$cacheStatus = $cache->isSetUp();
?>
<h3><?php _e('Status', 'trainingstagebuch'); ?></h3>
	<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Verbindung API-Server', 'trainingstagebuch'); ?>:</th>
			<td><?php echo ($trainingstagebuchStatus === true) ? __('OK', 'trainingstagebuch') : __('Fehler', 'trainingstagebuch'); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Cache', 'trainingstagebuch'); ?>:</th>
			<td><?php echo ($cacheStatus === true) ? __('OK', 'trainingstagebuch') : __('Fehler', 'trainingstagebuch'); ?></td>
		</tr>
	</table>
<?php } ?>
</div>
<?php
}


/**
 * Speichert Standardwerte fuer die Optionen in der Datenbank.
 */
function trainingstagebuch_activate() {
	#add_option(OPTION_TRAININGSTAGEBUCH_USERNAME);  
	#add_option(OPTION_TRAININGSTAGEBUCH_PASSWORD);  
	#add_option(OPTION_TRAININGSTAGEBUCH_BORG_IMAGE);  
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_ALL, '1');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_COUNT, '0');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DURATION, '0');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DISTANCE, '0');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_TABLE_HEAD, '1');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_NUMBER, '1');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DATE, '1');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DURATION, '1');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_HEARTRATE, '1');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SPEED, '1');
	add_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_BORG, '1');
}
if (function_exists('register_activation_hook'))
	register_activation_hook(__FILE__, 'trainingstagebuch_activate');


/**
 * Loescht alle Optionen aus der Datenbank.
 */
function trainingstagebuch_deactivate() {
	delete_option(OPTION_TRAININGSTAGEBUCH_USERNAME);  
	delete_option(OPTION_TRAININGSTAGEBUCH_PASSWORD);  
	delete_option(OPTION_TRAININGSTAGEBUCH_BORG_IMAGE);  
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_ALL);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_COUNT);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DURATION);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DISTANCE);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_TABLE_HEAD);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_NUMBER);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DATE);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DURATION);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_HEARTRATE);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SPEED);
	delete_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_BORG);
	#delete_option('trainingstagebuch_widget');  
}
if (function_exists('register_deactivation_hook'))
	register_deactivation_hook(__FILE__, 'trainingstagebuch_deactivate');


/**
 * Gibt Trainingsdaten aus.
 */
function trainingstagebuch_shortcode($atts, $content = NULL) {
	extract(shortcode_atts(array('type' => NULL, 'exclude' => NULL,
	                             'year' => NULL, 'month' => NULL, 'week' => NULL, 'id' => NULL,
	                             'start' => NULL, 'end' => NULL), $atts));
	# sanity checks
	if (empty($type)) return '';

	$username = get_option(OPTION_TRAININGSTAGEBUCH_USERNAME);
	$password = get_option(OPTION_TRAININGSTAGEBUCH_PASSWORD);

	$rVal = '';
	try {
		require_once('api/trainingstagebuch_api.php');
		$trainingstagebuch = TrainingstagebuchFactory::produce($username, $password);

		$method = 'get_'.strtolower($type);
		if (is_callable(array('Trainingstagebuch', $method)) == false)
			throw new Exception(sprintf(__("Typ '%s' unbekannt.", 'trainingstagebuch'), $type));

		$result = NULL;
		if ($method == 'get_workout') {
			if (empty($id)) throw new Exception(__("Attribut 'id' fehlt.", 'trainingstagebuch'));
			$result = $trainingstagebuch->$method($id);
			# Wird nur ein einzelnes Workout angefragt, verpacken wir das noch
			# in einem 'exercise' Element
			$tmp->duration = $result->duration;
			$tmp->{'distance-km'} = $result->{'distance-km'};
			$tmp->exercise = array($result);
			$result = $tmp;
		} else if (strpos($method, 'year')) {
			if (empty($year)) throw new Exception(__("Attribut 'year' fehlt.", 'trainingstagebuch'));
			$result = $trainingstagebuch->$method($year);
		} else if (strpos($method, 'month')) {
			if (empty($year)) throw new Exception(__("Attribut 'year' fehlt.", 'trainingstagebuch'));
			if (empty($month)) throw new Exception(__("Attribut 'month' fehlt.", 'trainingstagebuch'));
			$result = $trainingstagebuch->$method($year, $month);
		} else if (strpos($method, 'week')) {
			if (empty($year)) throw new Exception(__("Attribut 'year' fehlt.", 'trainingstagebuch'));
			if (empty($week)) throw new Exception(__("Attribut 'week' fehlt.", 'trainingstagebuch'));
			$result = $trainingstagebuch->$method($year, $week);
		} else {
			$result = $trainingstagebuch->$method();
		}
		if (empty($result)) throw new Exception(__('Kein Ergebnis erhalten.', 'trainingstagebuch'));

		#var_dump($result);

		if (strpos($method, 'workout') or strpos($method, 'plan')) {
			require_once('output.php');

			$show = get_show($exclude);

			if (!empty($show['summary_all'])) {
				$rVal .= get_exercise_summary($result, $show);
			}

			$rVal .= get_exercises($result, $show, $start, $end);
		} else {
			throw new Exception(__('Keine Ausgabe implementiert.', 'trainingstagebuch'));
		}

	} catch(Exception $e) {
		return '<br>Fehler: '.$e->getMessage().'<br>';
	}

	return $rVal;
}
if (function_exists('add_shortcode'))
	foreach (array('tb', 'trainingstagebuch') as $shortcode)
		add_shortcode($shortcode, 'trainingstagebuch_shortcode');


/**
 * Liefert Array mit den Werten, die angezeigt werden sollen.
 * - Die Einstellungen im Shortcode ueberschreiben die Einstellungen im Admin-Interface.
 */
function get_show($exclude) {
	$exclude_db = '';
	addExclude('summary_all', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_ALL), $exclude_db);
	addExclude('summary_count', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_COUNT), $exclude_db);
	addExclude('summary_duration', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DURATION), $exclude_db);
	addExclude('summary_distance', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DISTANCE), $exclude_db);
	addExclude('table_head', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_TABLE_HEAD), $exclude_db);
	addExclude('number', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_NUMBER), $exclude_db);
	addExclude('date', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DATE), $exclude_db);
	addExclude('duration', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_DURATION), $exclude_db);
	addExclude('heartrate', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_HEARTRATE), $exclude_db);
	addExclude('speed', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SPEED), $exclude_db);
	addExclude('borg', get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_BORG), $exclude_db);
	if (get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_ALL) == true) {
		$exclude_db = preg_replace('/summary_(count|duration|distance)/', '', $exclude_db);
		$exclude_db = preg_replace('/([^:]*):{2,}([^:]*)/', '$1:$2', $exclude_db);
		$exclude_db = preg_replace('/:{2,}|^:|:$/', '', $exclude_db);
	} else {
		if (get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_COUNT) == true or
		    get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DURATION) == true or
		    get_option(OPTION_TRAININGSTAGEBUCH_DISPLAY_SUMMARY_DISTANCE) == true) {
			$exclude_db = preg_replace('/(:?summary_all)|(summary_all:?)/', '', $exclude_db);
		} else {
			addExclude('summary_all', 0, $exclude_db);
		}
	}

	# Default values
	$result = array('summary_all' => true, 'summary_count' => true, 'summary_duration' => true, 'summary_distance' => true,
	                'table_head' => true, 'number' => true, 'date' => true, 'duration' => true, 'heartrate' => true, 'speed' => true, 'borg' => true);

	$exclude = (strlen($exclude) > 0) ? explode(':', $exclude) : array();
	$exclude_db = (strlen($exclude_db) > 0) ? explode(':', $exclude_db) : array();

	foreach (array_merge($exclude, $exclude_db) as $key)
		if (array_key_exists($key, $result))
			$result[$key] = false;

	return $result;
}

/**
 * Fuegt $name an $exclude an, falls $attribute false ist.
 */
function addExclude($name, $attribute, &$exclude) {
	if ($attribute == true) return;
	#if (strpos($exclude, $name) === false) return;
	$exclude = $exclude . ((strlen($exclude) == 0) ? '' : ':') . $name;
}


/**
 * Trainingstagebuch Widget
 */
class Trainingstagebuch_Widget extends WP_Widget {
	function Trainingstagebuch_Widget() {
		$widget_ops = array('classname' => 'widget_trainingstagebuch', 'description' => __('Infos aus dem Trainingstagebuch', 'trainingstagebuch') );
		$this->WP_Widget('trainingstagebuch', __('Trainingstagebuch', 'trainingstagebuch'), $widget_ops);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);

		echo $before_widget;

		switch ($instance['typ']) {
		case 'banner_l':
			echo get_promo_banner('468_60', 468, 60);
			break;
		case 'banner_m':
			echo get_promo_banner('280_35', 280, 35);
			break;
		case 'banner_s':
			echo get_promo_banner('234_60', 234, 60);
			break;
		case 'badge_1':
			echo get_promo_banner('80_15', 82, 17);
			break;
		case 'badge_2':
			echo get_promo_banner('80_15_2', 80, 15);
			break;
		case 'text':
			$username = get_option(OPTION_TRAININGSTAGEBUCH_USERNAME);
			echo '<script language="JavaScript" src="http://trainingstagebuch.org/user/show/'.$username.'/javascript"></script>';
			break;
		case 'month':
			echo get_train_o_meter('month', 190, 70);
			break;
		case 'year':
			echo get_train_o_meter('year', 190, 70);
			break;
		default:
			_e('Fehler', 'trainingstagebuch');
		}

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['typ'] = strip_tags($new_instance['typ']);
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'typ' => '' ) );
		$typ = strip_tags($instance['typ']);
?>
		<p>
			<?php _e('Typ', 'trainingstagebuch'); ?>
			<label for="<?php echo $this->get_field_id('typ'); ?>">
				<select name="<?php echo $this->get_field_name('typ'); ?>">
					<option <?php if ($typ == 'banner_l') echo 'selected="selected" '; ?>value="banner_l"><?php _e('Banner (gro&szlig;)', 'trainingstagebuch'); ?></option>
					<option <?php if ($typ == 'banner_m') echo 'selected="selected" '; ?>value="banner_m"><?php _e('Banner (mittel)', 'trainingstagebuch'); ?></option>
					<option <?php if ($typ == 'banner_s') echo 'selected="selected" '; ?>value="banner_s"><?php _e('Banner (klein)', 'trainingstagebuch'); ?></option>
					<option <?php if ($typ == 'badge_1') echo 'selected="selected" '; ?>value="badge_1"><?php _e('Badge 1', 'trainingstagebuch'); ?></option>
					<option <?php if ($typ == 'badge_2') echo 'selected="selected" '; ?>value="badge_2"><?php _e('Badge 2', 'trainingstagebuch'); ?></option>
					<option <?php if ($typ == 'text') echo 'selected="selected" '; ?>value="text"><?php _e('Trainingsdaten als Text', 'trainingstagebuch'); ?></option>
					<option <?php if ($typ == 'month') echo 'selected="selected" '; ?>value="month"><?php _e('Bild der Trainingsdaten (Monat)', 'trainingstagebuch'); ?></option>
					<option <?php if ($typ == 'year') echo 'selected="selected" '; ?>value="year"><?php _e('Bild der Trainingsdaten (Jahr)', 'trainingstagebuch'); ?></option>
				</select>
			</label>
		</p>
<?php
	}
}
if (function_exists('add_action'))
	add_action('widgets_init', create_function('', 'return register_widget("Trainingstagebuch_Widget");'));


/**
 * Gibt ein Promotion-Banner aus.
 */
function get_promo_banner($type, $width, $height) {
	if (!in_array($type, array('468_60', '280_35', '234_60', '80_15', '80_15_2'))) return '';
	if (!is_numeric($width) or !is_numeric($height)) return '';
	return '<a href="http://trainingstagebuch.org/" title="Kostenloses Trainingstagebuch und Trainingspläne">'.
	       '<img src="http://trainingstagebuch.org/static/images/promote/'.$type.'.jpg" alt="Trainingstagebuch.org" border="0" width="'.$width.'" height="'.$height.'" style="max-width:'.$width.'px" />'.
	       '</a>';
}


/**
 * Gibt ein Train-o-Meter aus.
 */
function get_train_o_meter($type, $width, $height) {
	if (!in_array($type, array('month', 'year'))) return '';
	if (!is_numeric($width) or !is_numeric($height)) return '';
	$username = get_option(OPTION_TRAININGSTAGEBUCH_USERNAME);
	return '<a href="http://trainingstagebuch.org/user/show/'.$username.'" title="Mein Trainingstagebuch">'.
	       '<img src="http://trainingstagebuch.org/user/trainometer/'.$username.'/'.$type.'.png" alt="Train-O-Meter" border="0" width="'.$width.'" height="'.$height.'" style="max-width:'.$width.'px" /></a>';
}


// FILE ENDS HERE