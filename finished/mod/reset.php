<?php
try {

	require_once('../classes/Rate.php');
	$objRate = new Rate();
	
	if ($objRate->reset()) {
		echo json_encode(array('error' => false));
	} else {
		echo json_encode(array('error' => true, 'case' => 2));
	}

} catch(Exception $e) {
	echo json_encode(array('error' => true, 'case' => 1));
}