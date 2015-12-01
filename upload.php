<?php
set_time_limit(120);

ini_set('upload_max_filesize', '10M');
$data = array();
header('Content-Type: application/json');

$target_dir = "upload/";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
$newname = uniqid() . '.' . $imageFileType;
$newfilename = $target_dir . $newname;
// Check if image file is a actual image or fake image
$check = getimagesize($_FILES["file"]["tmp_name"]);
if($check !== false) {
	$data['detail'] = "File is an image - " . $check["mime"] . ".";
} else {
    $data['detail'] = "File is not an image.";
    $uploadOk = 0;
}

if($uploadOk) {

    $image = imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name']));

    $exif = exif_read_data($_FILES['file']['tmp_name']);
    if(!empty($exif['Orientation'])) {
        switch($exif['Orientation']) {
            case 8:
                $image = imagerotate($image,90,0);
                break;
            case 3:
                $image = imagerotate($image,180,0);
                break;
            case 6:
                $image = imagerotate($image,-90,0);
                break;
        }
    }

    $width = 1000;
    list($width_orig, $height_orig) = getimagesize($jpgFile);
    if($width_orig > $width) {
        $height = (int) (($width / $width_orig) * $height_orig);
        // Resample
        $image_p = imagecreatetruecolor($width, $height);    
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
    }
    else {
        $image_p = $image;
    }

	//if (move_uploaded_file($image_p, $newfilename)) {
    if( imagejpeg($image_p, $newfilename) ) {
		$data['status'] = 'success';
		$data['image'] = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://'  .$_SERVER['HTTP_HOST']. '/'. $newfilename;
	} else {
		$data['status'] = 'error';
		$data['error'] = 'problem uploading the image';
	}
}

echo json_encode($data);
?>