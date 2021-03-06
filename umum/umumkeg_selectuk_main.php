<?php
function umumkeg_selectuk_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
    
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'namauk',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	); 

	global $user;
	$username = $user->name;	
	
	$query = db_select('unitkerja', 'u')->extend('PagerDefault')->extend('TableSort');;
	$query->innerJoin('userskpd', 'us', 'u.kodeuk=us.kodeuk');

	# get the desired fields from the database
	$query->fields('u', array('kodeuk',  'namauk'));
	$query->condition('us.username', $username, '=');

	$query->orderByHeader($header);
	$query->orderBy('u.namauk', 'ASC');
	$query->limit($limit);

	# execute the query
	$results = $query->execute();
		
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 
	$rows = array();
	
	foreach ($results as $data) {
		$no++;  
		
		$editlink = apbd_button_baru_custom_small('umum/newpick/' . $data->kodeuk, 'Lanjut');

		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $data->namauk,'align' => 'left', 'valign'=>'top'),
			$editlink,
		);			
	}

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	
	
	return $output;
	
}


?>
