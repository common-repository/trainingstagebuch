<?
/*
 * Client-Implementation der Trainingstagebuch.org API.
 *
 * (Funktioniert nur mit PHP5 oder neuer)
 */

#
# Trainingstagebuch API implementation
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

require_once('trainingstagebuch_data.php');
require_once('trainingstagebuch_cache.php');

/**
 * Erstellt Instanzen der Trainingstagebuch-Klasse, entweder mit einem
 * zwischengeschalteten Cache oder ohne.
 */
class TrainingstagebuchFactory {

	public static function produce($username, $password) {
		$trainingstagebuch = new Trainingstagebuch($username, $password);

		$cache = new TrainingstagebuchCache($trainingstagebuch);
		if ($cache->isSetUp()) return $cache;

		return $trainingstagebuch;
	}
}


/**
 * Diese Klasse dient als Schnittstelle zur API von Trainingstagebuch.org. Hier
 * sind alle Methoden implementiert, die die API bereitstellt.
 *
 * Beispiel zur Nutzung:
 * <pre>
 *   $t = new Trainingstagebuch('benutzername', 'geheimespasswort');
 *   $t->get_workouts();
 *   $t->get_workout_month(2009, 1);
 * </pre>
 *
 * Die Klasse wirft im Fehlerfall Exceptions und sollte daher in einem
 * try-catch-Block untergebracht werden, um die Fehlermeldungen sauber an den
 * Benutzer weitergeben zu koennen.
 */
class Trainingstagebuch {

	/** Enthaelt die Basis-URL zu trainingstagebuch.org */
	private $baseurl;
	/** Listet alle Funktionen der API mit Parametern */
	private $functions;
	/** Benutzername */
	private $username;
	/** Passwort */
	private $password;
	/** Datasource */
	private $data;

	/**
	 * Erstellt eine Instanz dieser Klasse und erwartet einen gueltigen
	 * Benutzernamen mit Passwort.
	 */
	public function __construct($username, $password) {
		$this->assert_version();

		if (empty($username) or empty($password))
			throw new Exception('Benutzername oder Passwort nicht gesetzt.');
		$this->username = $username;
		$this->password = $password;

		# Standard-Werte
		$this->baseurl = 'http://trainingstagebuch.org/';
		$this->functions = array('workouts/list',
		                         'workouts/year/[YEAR]',
		                         'workouts/month/[YEAR]/[MONTH]',
		                         'workouts/week/[YEAR]/[WEEK]',
		                         'workouts/show/[ID]',
		                         'workouts/edit/new',
		                         'sports/list',
		                         'material/list',
		                         'routes/list',
		                         'zones/list',
		                         'borg/list ',
		                         'weather/list ',
		                         'wind/list ',
		                         'plan/list',
		                         'plan/year/[YEAR]',
		                         'plan/month/[YEAR]/[MONTH]',
		                         'plan/week/[YEAR]/[WEEK]',
		                         'plan/edit/new',
		                         'daily/list',
		                         'daily/year/[YEAR]',
		                         'daily/month/[YEAR]/[MONTH]',
		                         'daily/week/[YEAR]/[WEEK]',
		                         'file/upload');

		$this->data = new TrainingstagebuchData();

		# Zur Sicherheit, es koennte SSO-ID vorkommen
		error_reporting(0);
	}


	/**
	 * Stellt sicher, dass zumindest PHP5 installiert ist und wir essentielle
	 * Funktionen nutzen koennen.
	 */
	private function assert_version() {
		if (!function_exists('simplexml_load_file') or !function_exists('simplexml_load_string'))
			throw new Exception('Bitte PHP5 oder neuer installieren.');
	}


	/**
	 * Gibt die SSO-Id fuer den Benutzernamen mit Passwort zurueck.
	 */
	private function get_sso_id() {
		$xml_file = $this->data->loadXML('http://trainingstagebuch.org/login/sso?user='.$this->username.'&pass='.$this->password);
		$sso = trim($xml_file->session);
		if (empty($sso)) throw new Exception('Konnte SSO-ID nicht ermitteln.');
		return $sso;
	}


	/**
	 * Ruft eine Funktion der API auf.
	 */
	private function call($function, array $parameters = array()) {
		# Funktion heraussuchen
		$function_with_parameters = NULL;
		foreach ($this->functions as $func) {
			if (strpos($func, $function) !== false) {
				$function_with_parameters = $func;
				break;
			}
		}
		if (empty($function_with_parameters))
			throw new Exception('Unbekannte Funktion');

		# Anzal an Parametern herausfinden
		preg_match_all('/\[[A-Z]*\]/', $function_with_parameters, $matches);
		$required_parameters = (empty($matches[0]) ? 0 : count($matches[0]));
		if ($required_parameters != count($parameters))
			throw new Exception('Zu wenige Parameter: '.$function_with_parameters.' erwartet '.$required_parameters.' Parameter');
		
		# Parameter einsetzen
		if ($required_parameters > 0) {
			foreach ($parameters as $key => $value) {
				$function_with_parameters = str_replace('['.strtoupper($key).']', $value, $function_with_parameters);
			}
		}

		# URL zusammenstellen
		$sso = $this->get_sso_id();
		$url = $this->baseurl.$function_with_parameters.'?view=xml&sso='.$sso;

		$xml = $this->data->loadXML($url);
		if (empty($xml)) throw new Exception('Keine Antwort der API erhalten.');

		return $xml;
	}


	/**
	 * Prueft Verbindung mit API-Server.
	 */
	public function test() {
		try {
			$this->get_sso_id();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	public function get_workouts() { return $this->call('workouts/list'); }
	public function get_workout_year($year) { return $this->call('workouts/year', array('year' => $year)); }
	public function get_workout_month($year, $month) { return $this->call('workouts/month', array('year' => $year, 'month' => $month)); }
	public function get_workout_week($year, $week) { return $this->call('workouts/week', array('year' => $year, 'week' => $week)); }
	public function get_workout($id) { return $this->call('workouts/show', array('id' => $id)); }
	public function new_workout() { throw new Exception('Nicht implementiert'); }

	public function get_sports() { return $this->call('sports/list'); }
	public function get_material() { return $this->call('material/list'); }
	public function get_routes() { return $this->call('routes/list'); }
	public function get_zones() { return $this->call('zones/list'); }
	public function get_borg() { return $this->call('borg/list'); }
	public function get_weather() { return $this->call('weather/list'); }
	public function get_wind() { return $this->call('wind/list'); }

	public function get_plan() { return $this->call('plan/list'); }
	public function get_plan_year($year) { return $this->call('plan/year', array('year' => $year)); }
	public function get_plan_month($year, $month) { return $this->call('plan/month', array('year' => $year, 'month' => $month)); }
	public function get_plan_week($year, $week) { return $this->call('plan/week', array('year' => $year, 'week' => $week)); }
	public function new_plan() { throw new Exception('Nicht implementiert'); }

	public function get_daily() { return $this->call('daily/list'); }
	public function get_daily_year($year) { return $this->call('daily/year', array('year' => $year)); }
	public function get_daily_month($year, $month) { return $this->call('daily/month', array('year' => $year, 'month' => $month)); }
	public function get_daily_week($year, $week) { return $this->call('daily/week', array('year' => $year, 'week' => $week)); }

	public function file_upload() { throw new Exception('Nicht implementiert'); }
}


// FILE ENDS HERE