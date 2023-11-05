<?php
	namespace fruithost;
	
	class Utils {
		public static function randomString(int $length = 10) : string {
			$characters			= 'abcdefghijklmonpqrstuvwxyz-_ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			$charactersLength	= strlen($characters);
			$output				= '';
			
			for($position = 0; $position < $length; $position++) {
				$output .= $characters[random_int(0, $charactersLength - 1)];
			}
			
			return $output;
		}
		
		public static function zeroize(int $number, int $threshold = 2) : string {
			return sprintf('%0' . $threshold . 's', $number);
		}
		
		public static function getTimeDifference(int $from, int $to = NULL) : string {
			if(empty($to)) {
				$to = time();
			}
			
			$diff = (int) abs($to - $from);
			
			if($diff < (60 * 60)) {
				$mins = round($diff / 60);
				
				if($mins <= 1) {
					$mins = 1;
				}
				
				$since = sprintf('%s mins', $mins);
			} else if($diff < (24 * (60 * 60)) && $diff >= (60 * 60)) {
				$hours = round($diff / (60 * 60));
				
				if($hours <= 1) {
					$hours = 1;
				}
				
				$since = sprintf('%s hours', $hours);
			} else if($diff < (7 * (24 * (60 * 60))) && $diff >= (24 * (60 * 60))) {
				$days = round($diff / (24 * (60 * 60)));
				
				if($days <= 1) {
					$days = 1;
				}
				
				$since = sprintf('%s days', $days);
			} else if($diff < (30 * (24 * (60 * 60))) && $diff >= (7 * (24 * (60 * 60)))) {
				$weeks = round($diff / (7 * (24 * (60 * 60))));
				
				if($weeks <= 1) {
					$weeks = 1;
				}
				
				$since = sprintf('%s weeks', $weeks);
			} else if($diff < (365 * (24 * (60 * 60))) && $diff >= (30 * (24 * (60 * 60)))) {
				$months = round($diff / (30 * (24 * (60 * 60))));
				
				if($months <= 1) {
					$months = 1;
				}
				
				$since = sprintf('%s months', $months);
			} else if($diff >= (365 * (24 * (60 * 60)))) {
				$years = round($diff / (365 * (24 * (60 * 60))));
				
				if($years <= 1) {
					$years = 1;
				}
				
				$since = sprintf('%s years', $years);
			}
			
			return $since;
		}
	}
?>