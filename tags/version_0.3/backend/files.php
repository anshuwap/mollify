<?php
	/**
	 * Copyright (c) 2008- Samuli Järvelä
	 *
	 * All rights reserved. This program and the accompanying materials
	 * are made available under the terms of the Eclipse Public License v1.0
	 * which accompanies this distribution, and is available at
	 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	 * this entire header must remain intact.
	 */

	function get_file_id($file) {
		return base64_encode($file);
	}

	function get_filename($id) {
		return base64_decode($id);
	}

	function get_filename_from_url() {
		if (!isset($_GET["id"])) {
			return FALSE;
		}
		return get_filename($_GET["id"]);
	}
	
	function get_dir_from_url() {
		if (!isset($_GET["dir"])) return FALSE;
		return get_filename($_GET["dir"]);
	}
	
	function assert_file($filename) {
		global $error, $error_details;
		
		if (!file_exists($filename)) {
			$error = "FILE_DOES_NOT_EXIST";
			$error_details = basename($filename);
			return FALSE;
		}
		if(!is_file($filename)) {
			$error = "NOT_A_FILE";
			$error_details = basename($filename);
			return FALSE;
		}
		return TRUE;
	}
	
	function get_directories($account) {
		global $error, $error_details;
		
		$path = get_dir_from_url();
		if (!$path) {
			$error = "INVALID_PATH";
			return FALSE;
		}
		
		$files = scandir($path);
		$result = array();
		
		foreach($files as $i => $name) {
			if (substr($name, 0, 1) == '.') {
				continue;
			}
			$fullPath = $path.DIRECTORY_SEPARATOR.$name;
			if (!is_dir($fullPath)) {
				continue;
			}
			$result[] = array("id" => get_file_id($fullPath), "name" => $name, "path" => $fullPath);
		}
		
		return $result;
	}
	
	function get_files($account) {
		global $error, $error_details;
		$ignored = array('descript.ion');
		
		$path = get_dir_from_url();
		if (!$path) {
			$error = "INVALID_PATH";
			return FALSE;
		}

		$files = scandir($path);
		$result = array();
		
		foreach($files as $i => $name) {
			if (substr($name, 0, 1) == '.' || in_array(strtolower($name), $ignored)) {
				continue;
			}
			$fullPath = $path.DIRECTORY_SEPARATOR.$name;
			if (is_dir($fullPath)) {
				continue;
			}
			
			$ext_pos = strrpos($name, '.');
			if ($ext_pos > 0) {
				$extension = substr($name, strrpos($name, '.') + 1);
			} else {
				$extension = "";
			}
			
			$result[] = array("id" => get_file_id($fullPath), "name" => $name, "extension" => $extension, "size" => filesize($fullPath));
		}
		
		return $result;
	}
	
	function get_file_details($filename) {
		if (!assert_file($filename)) {
			return FALSE;
		}
		$datetime_format = "YmdHis";
		
		$result = array(
			"id" => get_file_id($filename),
			"last_changed" => date($datetime_format, filectime($filename)),
			"last_modified" => date($datetime_format, filemtime($filename)),
			"last_accessed" => date($datetime_format, fileatime($filename)),
			"description" => get_description($filename));
		return $result;
	}
	
	function get_description($filename) {
		$path = dirname($filename);
		$file = basename($filename);
		$descriptions = get_descriptions_from_file($path.DIRECTORY_SEPARATOR."descript.ion");

		if (!isset($descriptions[$file])) return "";
		return $descriptions[$file];
	}
		
	function get_descriptions_from_file($descript_ion) {
		$result = array();
		if (!file_exists($descript_ion)) return $result;
	
		$handle = @fopen($descript_ion, "r");
		if (!$handle) return $result;
		
	    while (!feof($handle)) {
	        $line = fgets($handle, 4096);

			// check for quote marks (")
			if (ord(substr($line, 0, 1)) === 34) {
				$line = substr($line, 1);
				$split = strpos($line, chr(34));
			} else {
	        	$split = strpos($line, ' ');
			}
			if ($split <= 0) continue;

			$name = trim(substr($line, 0, $split));
			$desc = trim(substr($line, $split + 1));
			$result[$name] = $desc;
	    }
	    fclose($handle);
		
		return $result;
	}
	
	function rename_file($filename, $new_name) {
		if (!assert_file($filename)) {
			return FALSE;
		}
		
		$new = dirname($filename).DIRECTORY_SEPARATOR.$new_name;
		if (file_exists($new)) {
			$error = "FILE_ALREADY_EXISTS";
			$error_details = basename($new);
			return FALSE;
		}
		
		return rename($filename, $new);
	}

	function delete_file($filename) {
		global $error, $error_details;
		
		if (!assert_file($filename)) {
			return FALSE;
		}
		if (!unlink($filename)) {
			$error = "CANNOT_DELETE";
			$error_details = basename($filename);
			return FALSE;
		}
		return TRUE;
	}

	function upload_file($dir) {
		global $error, $error_details;
		
		if (!isset($_FILES['upload'])) {
			$error = "NO_UPLOAD_DATA";
			return FALSE;
		}
		
		$name = $_FILES['upload']['name'];
		$origin = $_FILES['upload']['tmp_name'];
		$target = $dir.DIRECTORY_SEPARATOR.$name;
		
		if ($_FILES["file"]["error"] != UPLOAD_ERR_OK) {
			$error = "UPLOAD_FAILED";
			$error_details = $_FILES["file"]["error"];
			return FALSE;
		}
		
		if (file_exists($target)) {
			$error = "FILE_ALREADY_EXISTS";
			$error_details = basename($target);
			return FALSE;
		}

		if (move_uploaded_file($origin, $target)) {
			return TRUE;
		}
		
		$error = "SAVING_FAILED";
		$error_details = $name;
		return FALSE;
	}
	
	function download($filename) {
		global $error, $error_details;
		
		if (!assert_file($filename)) {
			return FALSE;
		}
		
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=".basename($filename).";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename));
		
		readfile($filename);
		return TRUE;
	}
?>