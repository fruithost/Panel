<?php
    namespace fruithost\System;

    use fruithost\Localization\I18N;

    class Utils {
		public static function randomString(int $length = 10) : string {
			$characters			= 'abcdefghijklmonpqrstuvwxyz-_ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			$charactersLength	= strlen($characters);
			$output				= '';
			
			for($position = 0; $position < $length; $position++) {
                // @ToDo Exception handling?
				$output .= $characters[random_int(0, $charactersLength - 1)];
			}
			
			return $output;
		}
		
		public static function zeroize(int $number, int $threshold = 2) : string {
			return sprintf('%0' . $threshold . 's', $number);
		}
		
		public static function getTimeDifference(int $from, int $to = null) : string {
			if(empty($to)) {
				$to = time();
			}
			
			$diff   = (int) abs($to - $from);
			$since  = '';

			if($diff < (60 * 60)) {
				$mins = round($diff / 60);
				
				if($mins <= 1) {
					$mins = 1;
				}
				
				$since = sprintf(I18N::get('%s mins'), $mins);
			} else if($diff < (24 * (60 * 60)) && $diff >= (60 * 60)) {
				$hours = round($diff / (60 * 60));
				
				if($hours <= 1) {
					$hours = 1;
				}
				
				$since = sprintf(I18N::get('%s hours'), $hours);
			} else if($diff < (7 * (24 * (60 * 60))) && $diff >= (24 * (60 * 60))) {
				$days = round($diff / (24 * (60 * 60)));
				
				if($days <= 1) {
					$days = 1;
				}
				
				$since = sprintf(I18N::get('%s days'), $days);
			} else if($diff < (30 * (24 * (60 * 60))) && $diff >= (7 * (24 * (60 * 60)))) {
				$weeks = round($diff / (7 * (24 * (60 * 60))));
				
				if($weeks <= 1) {
					$weeks = 1;
				}
				
				$since = sprintf(I18N::get('%s weeks'), $weeks);
			} else if($diff < (365 * (24 * (60 * 60))) && $diff >= (30 * (24 * (60 * 60)))) {
				$months = round($diff / (30 * (24 * (60 * 60))));
				
				if($months <= 1) {
					$months = 1;
				}
				
				$since = sprintf(I18N::get('%s months'), $months);
			} else if($diff >= (365 * (24 * (60 * 60)))) {
				$years = round($diff / (365 * (24 * (60 * 60))));
				
				if($years <= 1) {
					$years = 1;
				}
				
				$since = sprintf(I18N::get('%s years'), $years);
			}
			
			return $since;
		}
		
		public static function getFileSize($bytes, $dec = 2) : string {
			$size   = [ 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];
			$factor = floor((strlen($bytes) - 1) / 3);
			
			if($factor == 0) {
				$dec = 0;
			}
			
			return sprintf("%.{$dec}f %s", $bytes / (1000 ** $factor), $size[$factor]);
		}
	}
?>