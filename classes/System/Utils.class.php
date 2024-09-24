<?php
    /**
     * fruithost | OpenSource Hosting
     *
     * @author Adrian Preuß
     * @version 1.0.0
     * @license MIT
     */

    namespace fruithost\System;

    use fruithost\Localization\I18N;

    class Utils {
        public static function getMimeType($file) : string {
            $mime       = mime_content_type($file);
            $extension  = pathinfo($file, PATHINFO_EXTENSION);
            $additional = [
                'js'    => 'application/javascript',
                'json'  => 'application/json',
                'xml'   => 'application/xml',
                'css'   => 'text/css'
            ];

            /* Set MIME-Type when not given */
            if(!$mime) {
                if(array_key_exists($extension, $additional)) {
                    return $additional[$extension];
                }
            }

            /* Override misconfigured MIME-Types */
            if(array_key_exists($extension, $additional)) {
                if($mime !== $additional[$extension]) {
                    return $additional[$extension];
                }
            }

            return $mime;
        }

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
		
		public static function getFileSize($size) : string {
			if($size / 1024000000 > 1) {
				$retval = round($size / 1024000000, 1) . ' GB';
			} else if($size / 1024000 > 1) {
				$retval = round($size / 1024000, 1) . ' MB';
			} else if($size / 1024 > 1) {
				$retval = round($size / 1024, 1) . ' KB';
			} else {
				$retval = round($size, 1) . ' bytes';
			}

			return $retval;
		}
	}
?>