<?php

namespace fileUploader;

class fileUploader {
	public function upload($destnation, $fileControl, $name) {
		$doc_root = $_SERVER ["DOCUMENT_ROOT"];
		
		$perPhoto = "";
		if ((! empty ( $fileControl )) && ($fileControl ['error'] == 0)) {
			
			// var_dump($fileControl);
			
			$filename = basename ( $fileControl ['name'] );
			$ext = substr ( $filename, strrpos ( $filename, '.' ) + 1 );
			
			// check file size less than 5MB in Byte
			$maxsize = 5 * 1024 * 1024;
			if ($fileControl ["size"] < $maxsize) {
				// Determine the path to which we want to save this file
				$newname = $doc_root . $destnation . $name . '.' . $ext;
				$GLOBALS ['nameOfFile'] = $name . '.' . $ext;
				// var_dump($newname);
				// Check if the file with the same name is already exists on the server
				if (! file_exists ( $newname )) {
					
					// Attempt to move the uploaded file to it's new place
					if ((move_uploaded_file ( $fileControl ['tmp_name'], $newname ))) {
						$perPhoto = $name . '.' . $ext;
					} else {
						$perPhoto = "Error: A problem occurred during file upload!";
					}
				} else {
					
					$perPhoto = "Error: File  with the same name is already exists on the server !";
				}
			} else {
				$perPhoto = "Error: Only files under 5MB are accepted for upload";
			}
		} else {
			$perPhoto = "Error: No file selected!";
		}
		return $perPhoto;
	}
}