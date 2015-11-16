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
	if (move_uploaded_file($_FILES['file']['tmp_name'], $newfilename)) {
		$data['status'] = 'success';
		$data['image'] = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://'  .$_SERVER['HTTP_HOST'].'/feriadepulgas/' . $newfilename;
	} else {
		$data['status'] = 'error';
		$data['error'] = 'problem uploading the image';
	}
}

echo json_encode($data);
?>