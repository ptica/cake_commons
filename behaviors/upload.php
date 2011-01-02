<?php

uses('folder');
uses('file');

class UploadBehavior extends ModelBehavior {

	var $default_options = array('dir' => '',
								 'allowed_mime' => array(),
								 'allowed_ext' => array(),
								 'overwrite_existing' => false,
								 'create_directory' => true,
								 'resize' => false,
								 'canonize_filename' => false
								 );

	var $__fields = array();

	var $patterns = array(
							"/\s/", # Whitespace
							"/\&/", # Ampersand
							"/\+/"  # Plus
							);
	var $replacements = array(
								"-",   # Whitespace
								"_and_", # Ampersand
								"_plus_" # Plus
								);

	function setup(&$model, $config=array()) {
		//$this->File = &new File;
		$this->Folder = &new Folder;
		
		foreach($config as $field => $options) {

			// Check if given field exists
			if(!$model->hasField($field)) {
				unset($config[$field]);
				unset($model->data[$model->name][$field]);
			}

			// Merge given options with defaults
			$options = array_merge($this->default_options, (array) $options);

			// Generate temporary directory if none provided
			if(empty($options['dir'])) {
				$options['dir'] = 'userfiles' . DS . 'file' . DS . $model->name;
			}
			
			// Check if directory exists and create it if required
			if(!is_dir($options['dir'])) {
				if($options['create_directory'] && !$this->Folder->create($options['dir'])) {
					unset($config[$field]);
					unset($model->data[$model->name][$field]);
				}
			}

			// Check if directory is writable
			if(!is_writable($options['dir'])) {
				unset($config[$field]);
				unset($model->data[$model->name][$field]);
			}

			// Check that the given directory does not have a DS on the end
			if($options['dir'][strlen($options['dir'])-1] == DS) {
				$options['dir'] = substr($options['dir'],0,strlen($options['dir'])-2);
			}

		}

		$this->__fields = $config;
	}


	function beforeSave(&$model) {
		$filtered_filename = '';
		$random_filename = false;
		if(count($this->__fields) > 0) {
			foreach($this->__fields as $field=>$options) {
				// Check for existence
				if(isset($model->data) && empty($model->data[$model->name][$field])) {
					continue;
				}
				// Check for upload
				if(isset($model->data) && !is_array($model->data[$model->name][$field])) {
					unset($model->data[$model->name][$field]);
					continue;
				}
				// Check error
				if($model->data[$model->name][$field]['error'] > 0) {
					unset($model->data[$model->name][$field]);
					continue;
				}
				// Fix name
				if (@$options['canonize_filename']) {
					list($m, $f) = explode('.', $options['canonize_filename']);
					$s = array_pop(explode('.', $model->data[$model->name][$field]['name']));
    				$filtered_filename = $this->friendly_name($model->data[$m][$f]).'.'.$s;
				} else {
					/*
					$filename = preg_replace($this->patterns,$this->replacements,$model->data[$model->name][$field]['name']);
    				$filename = strtolower($filename);
    				for ($i=0;$i<strlen($filename);$i++) {
    					$current_char = substr($filename,$i,1);
    					if (ctype_alnum($current_char) == TRUE || $current_char == "-" || $current_char == ".") {
    						$filtered_filename .= $current_char;
    					}
    				}*/
					$parts = explode('.', $model->data[$model->name][$field]['name']);
					$s = array_pop($parts);
					$s = strtolower($s);
    				$filtered_filename = $this->friendly_name(implode('.', $parts)).'.'.$s;
				}

				$model->data[$model->name][$field]['name'] = $filtered_filename;

				// Check mime
				if(count($options['allowed_mime']) > 0 && !in_array($model->data[$model->name][$field]['type'], $options['allowed_mime'])) {
					unset($model->data[$model->name][$field]);
					continue;
				}

				// Check extensions
				if(count($options['allowed_ext']) > 0) {
					$matches = 0;
					foreach($options['allowed_ext'] as $extension) {
						if(substr(strtolower($model->data[$model->name][$field]['name']),-strlen($extension)) == $extension) {
							$matches++;
						}
					}

					if($matches == 0) {
						unset($model->data[$model->name][$field]);
						echo $model->data[$model->name][$field]['name'];
						continue;
					}

				}
				// Create final save path
				if(@$options['random_filename']) {
					$uniqueFileName = uniqid("");
					$saveAs = $options['dir'] . '/' . $uniqueFileName;
				} else {
					@list($n, $s) = split('\.', $model->data[$model->name][$field]['name'], 2);
					$saveAs = $options['dir'] . '/' . $model->data[$model->name][$field]['name'];
					$counter = 1;
					if (file_exists($saveAs) && $options['overwrite_existing']) {
						// try to unlink, have it failed will get modified in next step
						@unlink($saveAs);
    				}				
					while (file_exists($saveAs)) {
						$model->data[$model->name][$field]['name'] = $n.'-'.$counter;
						if (@$s) $model->data[$model->name][$field]['name'] .= '.'.$s; 
						$saveAs = $options['dir'] . '/' . $model->data[$model->name][$field]['name'];
						$counter++; 
					}
				}
				
				// Check if file exists
				if(file_exists($saveAs)) {
					if(!$options['overwrite_existing'] || !unlink($saveAs)) {
						unset($model->data[$model->name][$field]);
						continue;
					}
				}
				
				// Attempt to move uploaded file
				if(!move_uploaded_file($model->data[$model->name][$field]['tmp_name'], $saveAs)) {
					unset($model->data[$model->name][$field]);
					continue;
				}
				// resize if needed
				if (is_numeric($options['resize'])) {
					$this->resize($model, $saveAs, $options['resize']);
				} elseif ($options['resize']) {
					// call the upload_resize method of model
					$model->upload_resize($model, $saveAs);
				}

				// Update model data
				$model->data[$model->name]['dir'] = $options['dir'];
				$model->data[$model->name]['mimetype'] =  $model->data[$model->name][$field]['type'];
				$model->data[$model->name]['filesize'] = $model->data[$model->name][$field]['size'];
				//$model->data[$model->name][$field] = $model->data[$model->name][$field]['name'];
				$model->data[$model->name][$field] = @$options['random_filename'] ? $uniqueFileName : $model->data[$model->name][$field]['name'];
			}
		}
	}


	function beforeDelete(&$model) {
        if(count($this->__fields) > 0) {
            $model->read(null, $model->id);
            if (isset($model->data)) {
            	@$dir = $model->data[$model->name]['dir'];
            	@$filename = $model->data[$model->name]['filename'];
                @unlink($dir.DS.$filename);
            }
        }
        return true;
    }
    
    function resize(&$model, $filename, $new_w) {
    	$system = explode(".", $filename);
        
        if (preg_match("/jpg|jpeg/", $system[1])) {
            $src_img = imagecreatefromjpeg($filename);
        }
          
        if (preg_match("/png/", $system[1])) {
            $src_img = imagecreatefrompng($filename);
        }
        
        $old_x = imagesx($src_img);
        $old_y = imagesy($src_img);
        
        if ($old_x == $new_w) return;
        
        if (1 || $old_x >= $old_y) {
            // i always go for width 
        	$thumb_w = $new_w;
            $ratio = $old_y / $old_x;
            $thumb_h = $ratio * $new_w;
        } else if ($old_x < $old_y) {
            $thumb_h = $new_h;
            $ratio = $old_x / $old_y;
            $thumb_w = $ratio * $new_h;
        }

        $dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
        
        if (preg_match("/png/", $system[1])) {
            imagepng($dst_img, $filename);
        } else {
            imagejpeg($dst_img, $filename);
        }

        imagedestroy($dst_img);
        imagedestroy($src_img);
    }
	function xxx_friendly_name($nadpis) {
		$url = $nadpis;
		$url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
		$url = trim($url, "-");
		$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
		return $url;
	}
	// skynet hosting screws the iconv code :(
	
/**
     * Czech & Slovak diacritical chars => ASCII by dgx
     * -------------------------------------------------
     *
     * This source file is subject to the GNU GPL license.
     *
     * @author     David Grudl aka -dgx- <dave@dgx.cz>
     * @link       http://www.dgx.cz/
     * @copyright  Copyright (c) 2006 David Grudl
     * @license    GNU GENERAL PUBLIC LICENSE
     * @category   Text
     * @version    1.0
     */ 
	
	
	// UTF-8 to ASCII for diacritic chars
	function utf2ascii($s) {
    	static $tbl = array("\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z");
    	return strtr($s, $tbl);
	}
	
	
	function friendly_name($nadpis) {
		$url = $nadpis;
		$url = preg_replace('~\s+~', '-', $url);
		$url = preg_replace('~/~', '-', $url);
		$url = $this->utf2ascii($url);
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
		$url = trim($url, "-");
		return $url;
	}
}
?>
