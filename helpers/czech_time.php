<?php

App::import('Helper', 'Time'); 

class CzechTimeHelper extends TimeHelper {

/**
 * Returns either a relative date or a formatted date depending
 * on the difference between the current time and given datetime.
 * $datetime should be in a <i>strtotime</i>-parsable format, like MySQL's datetime datatype.
 *
 * Relative dates look something like this:
 *	3 weeks, 4 days ago
 *	15 seconds ago
 * Formatted dates look like this:
 *	on 02/18/2004
 *
 * The returned string includes 'ago' or 'on' and assumes you'll properly add a word
 * like 'Posted ' before the function output.
 *
 * @param string $date_string Datetime string or Unix timestamp
 * @param string $format Default format if timestamp is used in $date_string
 * @param string $backwards False if $date_string is in the past, true if in the future
 * @return string Relative time string.
 */
	function timeAgoInWords($datetime_string, $format = 'j.n. Y', $backwards = false) {
		$datetime = $this->fromString($datetime_string);

		$in_seconds = $datetime;

		if ($backwards) {
			$diff = $in_seconds - time();
		} else {
			$diff = time() - $in_seconds;
		}

		$months = floor($diff / 2419200);
		$diff -= $months * 2419200;
		$weeks = floor($diff / 604800);
		$diff -= $weeks * 604800;
		$days = floor($diff / 86400);
		$diff -= $days * 86400;
		$hours = floor($diff / 3600);
		$diff -= $hours * 3600;
		$minutes = floor($diff / 60);
		$diff -= $minutes * 60;
		$seconds = $diff;

		if ($months > 0) {
			// over a month old, just show date (mm/dd/yyyy format)
			$relative_date = date($format, $in_seconds);
			$add_before = false;
		} else {
			$relative_date = '';
			$add_before = true;

			if ($weeks > 0 && $weeks < 2) {
				// weeks and days
				$add_before = false;
				$relative_date .= 'minulý týden';
				//$relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
			} elseif ($weeks > 1) {
				$relative_date .= ($relative_date ? ', ' : '') . $weeks . ' ' . ($weeks > 1 ? 'týdny' : 'týdnem');
				//$relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';				
			} elseif ($days > 0 && $days < 2) {
				// yesterday 
				$add_before = false;
				$relative_date .= 'včera';
				//$relative_date .= $hours > 0 ? ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '') : '';
			} elseif ($days > 1) {
				// days
				$relative_date .= ($relative_date ? ', ' : '') . $days . ' ' . ($days > 1 ? 'dny' : 'dnem');
				//$relative_date .= $hours > 0 ? ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '') : '';
			} elseif ($hours > 0) {
				// hours and minutes
				$relative_date .= ($relative_date ? ', ' : '') . $hours . ' ' . ($hours > 1 ? 'hodinami' : 'hodinou');
				//$relative_date .= $minutes > 0 ? ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '';
			} elseif ($minutes > 0) {
				// minutes only
				$relative_date .= ($relative_date ? ', ' : '') . $minutes . ' ' . ($minutes > 1 ? 'minutami' : 'minutou');
			} else {
				// seconds only
				$relative_date .= ($relative_date ? ', ' : '') . $seconds . ' ' . ($seconds != 1 ? 'sekundami' : 'sekundou');
			}
		}

		$ret = $relative_date;
		// show relative date and add proper verbiage
		if (!$backwards && $add_before) {
			$ret = 'před ' . $ret;
		}
		return $this->output($ret);
	}
	
	function nice($date_string=null, $format1="j.n. Y", $format2=", H:i") {
		if (preg_match('~^\d+\.\d+.\s*\d+~', $date_string)) $date_string = $this->_convertDate($date_string);
	
		if ($date_string != null) {
			$date = $this->fromString($date_string);
		} else {
			$date = time();
		}
		if (date("Y") == date("Y", $date)) $format1=preg_replace('/ Y/', '', $format1);
		if (date("H:i", $date) == '00:00') $format2=''; 

		$ret = date($format1.$format2, $date);
		return $this->output($ret);
	}
	
	function _convertDate($date) {
		@list($day, $month, $year) = split('[ .]', $date);
		if (empty($year)) $year = date('Y');
		return "$year-$month-$day";	
	}
	
	
	var $czech_days = array('Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne');
	function abbr_day_name($date_string) {
		if ($date_string != null) {
			$date = $this->fromString($date_string);
		} else {
			$date = time();
		}
		$ret = $this->czech_days[date("w", $date)];
		return $this->output($ret);
	}
	
	function interval($dates) {
		$res = '';
		if (empty($dates['ends'])) {
			$res .= $this->abbr_day_name($dates['begins']).' '.$this->nice($dates['begins']);
		} else {
			$res .= $this->nice($dates['begins']);
			$res .= ' &ndash; ';
			$res .= $this->nice($dates['ends']);
		}
		return $res;
	}
}
?>