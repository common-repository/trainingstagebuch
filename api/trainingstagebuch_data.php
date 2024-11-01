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

class TrainingstagebuchData {

	public function loadXML($url) {
		if (ini_get('allow_url_fopen') == true) {
			return $this->load_fopen($url);
		} else if (function_exists('curl_init')) {
			return $this->load_curl($url);
		} else {
			throw new Exception('Kann Daten weder direkt noch mit cURL laden. Entweder "allow_url_fopen" aktivieren oder cURL installieren.');
		}
	}

	private function load_fopen($url) {
		return simplexml_load_file($url);
	}

	private function load_curl($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		curl_close($curl);
		return simplexml_load_string($result);
	}
}


// FILE ENDS HERE