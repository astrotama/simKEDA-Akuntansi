<?php
function pendapatanman_rek_main($arg=NULL, $nama=NULL) {
	$limit = 20;
	$kodeuk = arg(2);
	
	drupal_set_message($kodeuk);
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'width' => '90px', 'field'=> 'kodero',  'valign'=>'top'),
		array('data' => 'Rekening', 'field'=> 'uraian',  'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'field'=> 'anggaran',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	); 

	//SKPD
	$query = db_select('anggperuk', 'a')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->fields('a', array('anggaran'));
	$query->condition('a.kodeuk', $kodeuk, '=');
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
		$editlink = apbd_button_baru_custom_small('pendapatanjurnalman/post/' . $kodeuk . '/' . $data->kodero, 'Jurnalkan');

		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $data->kodero,'align' => 'left', 'valign'=>'top'),
			array('data' => $data->uraian,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->anggaran),'align' => 'right', 'valign'=>'top'),
			$editlink,
		);			
	}

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	
	//$output_form = drupal_get_form('pendapatanman_rek_main_form');
	//drupal_render($output_form) . $btn . $output . $btn;
	
	
	
	return $output;
	
}


function pendapatanman_rek_main_form_submit($form, &$form_state) {

	
}


function pendapatanman_rek_main_form($form, &$form_state) {

}


?>
