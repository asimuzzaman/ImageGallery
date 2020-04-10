<?php
namespace App\Libraries;
/*
	This is a third party library which is modified by this developer (Md. Asimuzzaman)
*/
class ThumbGen {

	/*
		@params
		- Source image directory, relative, absolute or web
		- Thumbnail width
		- Thumbnail height
		- Thumbnail output directory
		NB. the original library has different parameters
		
		@return
		- null
	*/
	public static function create_cropped_thumbnail($input_path, $thumb_width, $thumb_height, $output_path) {

	    if (!(is_integer($thumb_width) && $thumb_width > 0) && !($thumb_width === "*")) {
	        echo "The width is invalid";
	        exit(1);
	    }

	    if (!(is_integer($thumb_height) && $thumb_height > 0) && !($thumb_height === "*")) {
	        echo "The height is invalid";
	        exit(1);
	    }

	    $extension = pathinfo($input_path, PATHINFO_EXTENSION);
	    switch ($extension) {
	        case "jpg":
	        case "jpeg":
	            $source_image = imagecreatefromjpeg($input_path);
	            break;
	        case "gif":
	            $source_image = imagecreatefromgif($input_path);
	            break;
	        case "png":
	            $source_image = imagecreatefrompng($input_path);
	            break;
	        default:
	            exit(1);
	            break;
	    }

	    $source_width = imageSX($source_image);
	    $source_height = imageSY($source_image);

	    if (($source_width / $source_height) == ($thumb_width / $thumb_height)) {
	        $source_x = 0;
	        $source_y = 0;
	    }

	    if (($source_width / $source_height) > ($thumb_width / $thumb_height)) {
	        $source_y = 0;
	        $temp_width = $source_height * $thumb_width / $thumb_height;
	        $source_x = ($source_width - $temp_width) / 2;
	        $source_width = $temp_width;
	    }

	    if (($source_width / $source_height) < ($thumb_width / $thumb_height)) {
	        $source_x = 0;
	        $temp_height = $source_width * $thumb_height / $thumb_width;
	        $source_y = ($source_height - $temp_height) / 2;
	        $source_height = $temp_height;
	    }

	    $target_image = ImageCreateTrueColor($thumb_width, $thumb_height);

	    imagecopyresampled($target_image, $source_image, 0, 0, $source_x, $source_y, $thumb_width, $thumb_height, $source_width, $source_height);

	    $image_name = basename($input_path); //ADDED

	    switch ($extension) {
	        case "jpg":
	        case "jpeg":
	            imagejpeg($target_image, $output_path ."/". $image_name);
	            break;
	        case "gif":
	            imagegif($target_image, $output_path ."/". $image_name);
	            break;
	        case "png":
	            imagepng($target_image, $output_path ."/". $image_name); //CHANGED
	            break;
	        default:
	            exit(1);
	            break;
	    }

	    imagedestroy($target_image);
	    imagedestroy($source_image);
	}
}

?>