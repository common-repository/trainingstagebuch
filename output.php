<?php

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


/**
 * Gibt Trainingsuebersicht aus.
 */
function get_exercise_summary($xml, $show = array()) {
	$rVal = '<div class="tb_exercise_summary">'.
	        '<span class="heading">'.__('Summe aller Trainingseinheiten', 'trainingstagebuch').'</span>'.
	        '<table>';

	if (!empty($xml->count) and !empty($show['summary_count'])) {
		$rVal .= '<tr class="count">'.
		         '<td>'.__('Trainingseinheiten', 'trainingstagebuch').':</td>'.
		         '<td>'.$xml->count.'</td>'.
		         '</tr>';
	}

	if (!empty($show['summary_duration'])) {
		$rVal .= '<tr class="duration">'.
		         '<td>'.__('Zeit', 'trainingstagebuch').':</td>'.
		         '<td>'.$xml->duration.' '.__('Stunden', 'trainingstagebuch').'</td>'.
		         '</tr>';
	}

	if (!empty($show['summary_distance'])) {
		$rVal .= '<tr>'.
		         '<td>'.__('Kilometer', 'trainingstagebuch').':</td>'.
		         '<td>'.$xml->{'distance-km'}.' km</td>'.
		         '</tr>';
	}

	$rVal .= '</table>'.
	         '</div>';

	return $rVal;
}


/**
 * Gibt Trainingseinheiten aus.
 */
function get_exercises($xml, $show = array(), $start = NULL, $end = NULL) {
	if (count($xml->exercise) == 0) return '';

	$rVal = '<div class="tb_exercises"><table>';

	if (!empty($show['table_head'])) {
		$rVal .= '<tr>';
			if (!empty($show['number'])) $rVal .= '<td>'.__('Nummer', 'trainingstagebuch').'</td>';
			if (!empty($show['date'])) $rVal .= '<td>'.__('Datum', 'trainingstagebuch').'</td>';
			if (!empty($show['duration'])) $rVal .= '<td>'.__('Dauer', 'trainingstagebuch').'</td>';
			if (!empty($show['heartrate'])) $rVal .= '<td>'.__('Herzfrequenz', 'trainingstagebuch').'</td>';
			if (!empty($show['speed'])) $rVal .= '<td>'.__('Geschwindigkeit', 'trainingstagebuch').'</td>';
			if (!empty($show['borg'])) $rVal .= '<td>'.__('BORG-Bild', 'trainingstagebuch').'</td>';
		$rVal .= '</tr>';
	}

	$i = 0;
	foreach($xml->exercise as $exercise) {
		$i++;

		if ($start != NULL and $start > $i) continue;
		if ($end != NULL and $end < $i) continue;

		$rVal .= '<tr>';

			if (!empty($show['number'])) {
				$rVal .= '<th>'.$i.'</th>';
			}
	
	    	// Link zur Einheit
			if (!empty($show['date'])) {
	    		$rVal .= '<td><a href="http://trainingstagebuch.org/public/show/' . $exercise->id .'">';
				$rVal .= trainingstagebuch_convert_date($exercise->date);
				$rVal .= '</a></td>';
			}
	
			// Trainingsdauer
			if (!empty($show['duration'])) {
				$rVal .= "<td>$exercise->duration Std.</td>";
			}
	
			// Herzfrequenz (Dschn.) / Herzfrequenz (Max.)
			if (!empty($show['heartrate'])) {
				if ($exercise->{'heartrate-avg'} > 1) {
					$rVal .= '<td>' . $exercise->{'heartrate-avg'} . ' / ' . $exercise->{'heartrate-max'} . ' bpm</td>';
				} else {
					$rVal .= '<td></td>';
				}
			}
	
			// Geschwindigkeit (Dschn.) / Geschwindigkeit (Max.)
			if (!empty($show['speed'])) {
				if ($exercise->{'speed-avg'} > 1) {
					$rVal .= '<td>' . $exercise->{'speed-avg'} . '  / ' . $exercise->{'speed-max'} . ' km/h</td>';
				} else {
					$rVal .= '<td></td>';
				}
			}
	    
			// Trainingsbereich
			#$rVal .= '<td bgcolor="' . $exercise->{'zone-color'} . '">' . $exercise->{'zone-name'} . '</td>';
	    
			// Trainingsgefuehl (BORG)
			if (!empty($show['borg'])) {
				if ($exercise->{'borg-id'} >= 1) {
					$borgImg = $exercise->{'borg-image'};
					if (get_option(OPTION_TRAININGSTAGEBUCH_BORG_IMAGE) == 'small') $borgImg = str_replace('borg', 'borg/small', $borgImg);
					$rVal .= '<td><img src="http://trainingstagebuch.org/static' . $borgImg . '" style="max-width:100%" /></td>';
				} else {
					$rVal .= '<td></td>';
				}
			}

		$rVal .= '</tr>';
	}
	$rVal .= '</table></div>';

	return $rVal;
}


/**
 * Datum YYYY-MM-DD in TT.MM.YYYY umwandeln
 */
function trainingstagebuch_convert_date($date) {
	$datum = explode ('-', $date);
	return $datum[2].'.'.$datum[1].'.'.$datum[0];
}


// FILE ENDS HERE