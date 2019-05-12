<?php
	namespace Gauge;
	
	class Gauge {
		private $image				= NULL;
		private $image_needle		= NULL;
		private $image_background	= NULL;
		private $image_transparent	= NULL;
		private $display_range		= true;
		
		public function __construct() {
			$this->image_background		= dirname(__FILE__) . '/images/gauge_blank.png';
			$this->image_needle			= dirname(__FILE__) . '/images/gauge_needle.png';
			$this->image_transparent	= dirname(__FILE__) . '/images/blank.png';
		}

		public function render($name, $value, $range_bottom = 0, $range_top = 100) {
			$display_value = true;
			
			if($range_top == 0) {
				$range_bottom	= 0;
				$range_top		= 100;
			}
			
			if($range_bottom > $range_top) {
				$tmp			= $range_bottom;
				$range_top		= $range_bottom;
				$range_bottom	= $tmp;
				unset($tmp);
			}

			$this->image = imagecreatefrompng($this->image_background);
			
			imageAlphaBlending($this->image, true);
			imageSaveAlpha($this->image, true);
			
			if($display_value) {
				$this->addText($name, 9, 9);
				$this->addText($value, 8, 120);
				$this->addText('from', 8, 132);
				$this->addText($range_top, 8, 144);
				
			}
			
			if($this->display_range) {
				imagestring($this->image, 1, 44, 117, '0%', imagecolorallocate($this->image, 0, 0, 0));
				imagestring($this->image, 1, 105, 117, '100%', imagecolorallocate($this->image, 0, 0, 0));
			}

			if($value > $range_top) {
				$value = $range_top;
			}				

			if($value < $range_bottom) {
				$value = $range_bottom;
			}

			$angle	= (($value - $range_bottom) * 260) / ($range_top - $range_bottom);
			$needle	= imagecreatefrompng($this->image_needle);
			$new_x	= 0;
			$new_y	= 0;
			
			if($angle > 0) {
				#$angle = 0;
			}
			
			#imagestring($this->image, 1, 44, 117, $angle, imagecolorallocate($needle, 0, 0, 0));
			
			if($needle) {
				imageAlphaBlending($needle, true);
				imageSaveAlpha($needle, true);
				
				$needle_x	= imagesx($needle);
				$needle_y	= imagesy($needle);
				$needle		= imagerotate($needle, $angle, 0); // It's correct?
				
				if($needle) {
					$new_x = imagesx($needle);
					$new_y = imagesy($needle);
				}
			}
			
			$new_img = imagecreatefrompng($this->image_transparent);
			
			if($new_img) {
				imageAlphaBlending($new_img, true);
				imageSaveAlpha($new_img, true);
				
				if($needle) {
					imagecopy($new_img, $needle, 0, 0, round(($new_x - $needle_x) / 2) + 33, round(($new_y - $needle_y) / 2) + 33, $needle_x, $needle_y);
				}
				
				imagecopy($this->image, $new_img, 0, 0, 0, 0, 165, 165);
			}
			
			return $this;
		}

		public function addText($text, $font_size = 10, $top = 0) {
			$font			= dirname(__FILE__) . '/fonts/Roboto-Light.ttf';
			$angle			= 0;
			$width			= imagesx($this->image);
			$height			= imagesy($this->image);
			$centerX		= $width / 2;
			$centerY		= $height / 2;
			$bounds			= imageftbbox($font_size, $angle, $font, $text);
			$text_width		= abs($bounds[2]) - abs($bounds[0]);
			$text_height	= abs($bounds[5]) - abs($bounds[3]);
			$image_width	= imagesx($this->image);
			$image_height	= imagesy($this->image);
			$x				= ($image_width - $text_width) / 2;
			$y				= ($image_height + $text_height) / 2;
			
			imagettftext($this->image, $font_size, $angle, $x, $top, imagecolorallocate($this->image, 0, 0, 0), $font, $text);
		}
		
		public function display_png() {
			imagepng($this->image);
		}
		
		public function base64() {
			ob_start();
			$this->display_png();
			$content = ob_get_contents();
			ob_end_clean();
			return 'data:image/jpeg;base64,' . base64_encode($content);
		}
	}
?>