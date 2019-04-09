<?php
function pendapatanman_uk_main($arg=NULL, $nama=NULL) {
	$limit = 10;
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'namauk',  'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'field'=> 'anggaran',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	); 

	//SKPD
	$query = db_select('anggperuk', 'a')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'uk', 'a.kodeuk=uk.kodeuk');
	$query->fields('uk', array('kodeuk', 'namauk'));
	$query->addExpression('SUM(a.anggaran)', 'anggaran');
	$query->groupBy('uk.kodeuk');
	$query->orderBy('uk.namauk');
	$query->limit($limit);
	$results = $query->execute();

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 
	$rows = array();
	
	foreach ($results as $data) {
		$no++;  
		
		//$editlink = createlink('SPJ','barupost/' . $data->kodekeg . '/' . $jenis);
		$editlink = apbd_button_baru_custom_small('pendapatanjurnalman/post/' . $data->kodeuk, 'Pilih');

		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $data->namauk,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->anggaran),'align' => 'right', 'valign'=>'top'),
			$editlink,
		);			
	}

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	
	//$output_form = drupal_get_form('pendapatanman_uk_main_form');
	//drupal_render($output_form) . $btn . $output . $btn;
	
	
	
	return $output;
	
}


function pendapatanman_uk_main_form_submit($form, &$form_state) {

	
}


function pendapatanman_uk_main_form($form, &$form_state) {

}


?>
