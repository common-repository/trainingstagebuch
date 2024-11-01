<?php

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

require_once('trainingstagebuch_api.php');
require_once('trainingstagebuch_helper.php');

/**
 * Leitet alle Methodenaufrufe an die Trainingstagebuch-Klasse weiter, sofern
 * kein Ergebnis dieses Aufrufs im Cache gefunden werden kann.
 *
 * Beispiel zur Nutzung:
 * <pre>
 *   $trainingstagebuch = new Trainingstagebuch($username, $password);
 *   $cache = new TrainingstagebuchCache($trainingstagebuch);
 *   if ($cache->isSetUp()) return $cache;
 *   else return $trainingstagebuch;
 * </pre>
 */
class TrainingstagebuchCache {

	/** Instanz der Trainingstagebuch-Klasse */
	private $trainingstagebuch;
	/** In diesem Verzeichnis werden die gecachten Ergebnisse gesichert */
	private $cacheDir;
	/** Zeit in Sekunden, bis ein Objekt ungueltig wird */
	private $cacheTTL;
	/** Enthaelt die Methode, die gecacht werden sollen */
	private $cachableMethods;

	public function __construct($trainingstagebuch, $cacheDir = 'cache', $cacheTTL = 86400) {
		if (is_a($trainingstagebuch, 'Trainingstagebuch') == false)
			throw new Exception('Erwarte Trainingstagebuch-Klasse');

		$this->trainingstagebuch = $trainingstagebuch;
		$this->cacheDir = $cacheDir;
		$this->cacheTTL = $cacheTTL;
		$this->cachableMethods = array('get_workouts', 'get_workout_year', 'get_workout_month', 'get_workout_week', 'get_workout', 'get_sports', 'get_material', 'get_routes', 'get_zones', 'get_borg', 'get_weather', 'get_wind', 'get_plan', 'get_plan_year', 'get_plan_month', 'get_plan_week', 'get_daily', 'get_daily_year', 'get_daily_month', 'get_daily_week');
	}

	/**
	 * Prueft ob die Verzeichnisse des Caches vorhanden, les und schreibbar
	 * sind. Dies sollte vor Verwendung des Caches geprueft werden.
	 */
	public function isSetUp() {
		if (file_exists($this->getCacheDir()) and is_writable($this->getCacheDir())) return true;
		return false;
	}

	/**
	 * Faengt alle Methodenaufrufe proxymaessig ab und holt das gewuenschte
	 * Ergebniss ggfls. aus dem Cache.
	 */
	public function __call($method, array $args) {
		if (is_callable(array($this->trainingstagebuch, $method)) == false)
			throw new Exception("Can't call method '$method'");

		$result = $this->get($method, $args);
		if ($result == NULL) {
			$m = new ReflectionMethod('Trainingstagebuch', $method);
			$result = $m->invokeArgs($this->trainingstagebuch, $args);
			$this->put($method, $args, $result);
		}
		return $result;
	}

	/**
	 * Holt Daten aus dem Cache.
	 */
	private function get($method, array $args) {
		if (in_array($method, $this->cachableMethods) == false) return NULL;
		if ($this->isSetUp() == false) return NULL; # sanity check

		$hash = $this->getHash($method, $args);
		$cachedFileData = $this->getCacheFileData($hash);
		$cachedFileMeta = $this->getCacheFileMeta($hash);
		if (TrainingstagebuchHelper::assertFileReadability($cachedFileData, false) === false) return NULL;
		if (TrainingstagebuchHelper::assertFileReadability($cachedFileMeta, false) === false) return NULL;

		# If the data expired, we'll delete the corresponding files from the cache
		if ((int) date('U') > (int) file_get_contents($cachedFileMeta) + $this->cacheTTL) {
			unlink($cachedFileData);
			unlink($cachedFileMeta);
			return NULL;
		}

		return simplexml_load_string(unserialize(file_get_contents($cachedFileData)), 'SimpleXMLElement', LIBXML_NOCDATA);
	}

	/**
	 * Speichert Daten in dem Cache.
	 */
	private function put($method, array $args, SimpleXMLElement $result) {
		if (in_array($method, $this->cachableMethods) == false) return;
		if ($this->isSetUp() == false) return; # sanity check

		$hash = $this->getHash($method, $args);
		$cachedFileData = $this->getCacheFileData($hash);
		$cachedFileMeta = $this->getCacheFileMeta($hash);

		file_put_contents($cachedFileData, serialize($result->asXML()));
		file_put_contents($cachedFileMeta, date('U'));
	}

	/**
	 * Erstellt anhand des Methodennamens und den Argumenten einen
	 * (hoffentlich) eindeutigen Hash.
	 */
	private function getHash($method, array $args) {
		$str = $method;
		foreach ($args as $arg) $str .= $arg;
		return md5($str);
	}

	/**
	 * Liefert das Cache-Verzeichnis.
	 */
	private function getCacheDir() {
		return dirname(__FILE__).'/'.$this->cacheDir;
	}

	/**
	 * Liefert den Dateinamen der Daten-Datei im Cache.
	 */
	private function getCacheFileData($hash) {
		return $this->getCacheDir().'/'.$hash.'.data';
	}

	/**
	 * Liefert den Dateinamen der Meta-Datei im Cache.
	 */
	private function getCacheFileMeta($hash) {
		return $this->getCacheDir().'/'.$hash.'.meta';
	}

}


// FILE ENDS HERE