<?php
if (!empty($_GET['type']) && !empty($_GET['item'])) {

	$rate = $_GET['type'] == 'up' ? 1 : 0;
	$item = $_GET['item'];
	
	try {
		
		require_once('../classes/Rate.php');
		$objRate = new Rate();
		
		if (!$objRate->isSubmitted($item)) {
			
			if ($objRate->addRating($item, $rate)) {
				echo json_encode(array('error' => false));
			} else {
				echo json_encode(array('error' => true, 'case' => 4));
			}
			
		} else {
			echo json_encode(array('error' => true, 'case' => 3));
		}
		
	} catch(Exception $e) {
		echo json_encode(array('error' => true, 'case' => 2));
	}

} else {
	echo json_encode(array('error' => true, 'case' => 1));
}