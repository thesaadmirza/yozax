<?php

if ($yx['config']['usr_ads'] != 1) {
	$data['status']  = 400;
	$data['message'] = "Bad request";
}

else if ($yx['loggedin'] && $s == 'new' && !empty($yx['user']['wallet'])) {

	$request   = array();
	$error     = false;
	$request[] = (empty($_POST['name']) || empty($_POST['url']));
	$request[] = (empty($_POST['placement']) || !in_array($_POST['placement'],array(1,2,3)));
	$request[] = (empty($_POST['status']) || !in_array($_POST['status'],array(1,2)));
	$request[] = (empty($_FILES['image']) || !file_exists($_FILES['image']['tmp_name']));

	if (in_array(true, $request)) {
		$error = $yx['lang']['please_check_details'];
	}

	else{

		$image = getimagesize($_FILES['image']['tmp_name']);

		if (strlen($_POST['name']) < 5 || strlen($_POST['name']) > 50) {
			$error = $yx['lang']['enter_valid_name'];
		}

		else if (!in_array($image[2], array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_BMP))) {
			$error = $yx['lang']['error_found_while_uploading'];
		}

		else if(!YX_IsUrl($_POST['url'])){
			$error = $yx['lang']['invalid_url'];
		}
	}

	if (empty($error)) {

		$image_file_info   = array(
            'file' => $_FILES['image']['tmp_name'],
            'size' => $_FILES['image']['size'],
            'name' => $_FILES['image']['name'],
            'type' => $_FILES['image']['type']
        );

        $image_file_upload = YX_ShareFile($image_file_info);

        if (!empty($image_file_upload['filename'])) {
        	$re_data = array(
				'user_id' => $yx['user']['id'],
				'title' => YX_Secure($_POST['name']),
				'url' => YX_Secure($_POST['url']),
				'placement' => YX_Secure($_POST['placement']),
				'status' => YX_Secure($_POST['status']),
				'media_file' => $image_file_upload['filename'],
				'time' => time(),
				'type' => 'image',
			);

			$insert = $db->insert(T_USER_ADS,$re_data);

			if (!empty($insert)) {
				$data['id']      = $insert;
				$data['status']  = 200;
				$data['message'] = $yx['lang']['ad_created'];
			}
        }
        else{
        	$error           = $yx['lang']['error_found_please_try_again_later'];
        	$data['status']  = 400;
			$data['message'] = "$error_icon $error";
        }
	}

	else{
		$data['status']  = 400;
		$data['message'] = "$error_icon $error";
	}
}

else if ($yx['loggedin'] && $s == 'edit' && !empty($yx['user']['wallet'])) {
	$request   = array();
	$error     = false;
	$ad_image  = false;
	$request[] = (empty($_POST['name']) || empty($_POST['url']));
	$request[] = (empty($_POST['placement']) || !in_array($_POST['placement'],array(1,2,3)));
	$request[] = (empty($_POST['status']) || !in_array($_POST['status'],array(1,2)));
	$request[] = (empty($_POST['id']) || !is_numeric($_POST['id']));

	

	if (in_array(true, $request)) {
		$error = $yx['lang']['please_check_details'];
	}

	else{

		$db->where('user_id',$yx['user']['id']);
		$db->where('id',$_POST['id']);
		$ad        = $db->getOne(T_USER_ADS);
		$ad_image  = (!empty($_FILES['image']) && file_exists($_FILES['image']['tmp_name']));


		if (empty($ad)) {
			$error = $yx['lang']['please_check_details'];
		}

		else if (strlen($_POST['name']) < 5 || strlen($_POST['name']) > 50) {
			$error = $yx['lang']['enter_valid_name'];
		}

		else if(!YX_IsUrl($_POST['url'])){
			$error = $yx['lang']['invalid_url'];
		}

		else if ($ad_image === true) {

			$image = getimagesize($_FILES['image']['tmp_name']);

			if (!in_array($image[2], array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_BMP))) {
				$error = $yx['lang']['error_found_while_uploading'];
			}
		}
	}

	if (empty($error)) {

		$up_data = array(
			'title' => YX_Secure($_POST['name']),
			'url' => YX_Secure($_POST['url']),
			'placement' => YX_Secure($_POST['placement']),
			'status' => YX_Secure($_POST['status']),
		);

		if ($ad_image === true) {
			$image_file_info   = array(
	            'file' => $_FILES['image']['tmp_name'],
	            'size' => $_FILES['image']['size'],
	            'name' => $_FILES['image']['name'],
	            'type' => $_FILES['image']['type']
	        );

	        $image_file_upload = YX_ShareFile($image_file_info);

	        if (!empty($image_file_upload['filename'])) {
	        	$up_data['media_file'] = $image_file_upload['filename'];
        	}

		}

		$update = $db->where('id',$ad->id)->update(T_USER_ADS,$up_data);

		if (!empty($update)) {
			$data['id']      = $ad->id;
			$data['status']  = 200;
			$data['message'] = $yx['lang']['ad_saved'];
		}

		else{
        	$error           = $yx['lang']['error_found_please_try_again_later'];
        	$data['status']  = 400;
			$data['message'] = "$error_icon $error";
        }   
	}

	else{
		$data['status']  = 400;
		$data['message'] = "$error_icon $error";
	}
}

else if ($yx['loggedin'] && $s == 'delete') {
	
	if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
		YX_DeleteUserAD($_POST['id']);
	}
}

else if ($s == 'rc' && !empty($_POST['id']) && is_numeric($_POST['id'])) {
	var_dump(yx_adcon_charge($_POST['id']));
}

