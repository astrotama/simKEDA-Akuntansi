<?php
function umumpusatkeg_newpick_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
    
	$kodeuk = arg(2);

	$transid = arg(3);
	$transid2 = arg(4);
	$transid3 = arg(5);
	
	if (isset($transid2)) $transid .= '/' . $transid2;
	if (isset($transid3)) $transid .= '/' . $transid3;
	drupal_set_message('id : ' . $transid);
	
	if (isset($transid2)) $transid .= '/' . $transid2;
	if (isset($transid3)) $transid .= '/' . $transid3;
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'field'=> 'kodekeg',  'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan',  'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '100px', 'field'=> 'jumlah',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
	); 

	
	$query = db_select('kegiatanskpd', 'd')->extend('PagerDefault')->extend('TableSort');;

	# get the desired fields from the database
	$query->fields('d', array('kodekeg',  'kodeuk', 'kegiatan', 'anggaran'));
	$query->condition('d.kodeuk', $kodeuk, '=');
	//if ($kodeuk!='00') $query->condition('d.jenis', 2, '=');
	$query->condition('d.inaktif', 0, '=');
	$query->condition('d.anggaran', 0, '>');

	$query->orderByHeader($header);
	$query->orderBy('d.jenis', 'ASC');
	$query->orderBy('d.kegiatan', 'ASC');
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
		
		$editlink = apbd_button_baru_custom_small('umumpusat/newpost/' . $data->kodekeg . '/' . $transid, 'Jurnal');

		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => $data->kodekeg,'align' => 'left', 'valign'=>'top'),
			array('data' => $data->kegiatan,'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data->anggaran),'align' => 'right', 'valign'=>'top'),
			$editlink,
		);			
	}

	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	
	//$output_form = drupal_get_form('umumpusatkeg_newpick_main_form');
	//drupal_render($output_form) . $btn . $output . $btn;
	
	
	
	return $output;
	
}




?>
