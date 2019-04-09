<?php
function opd_main($arg=NULL, $nama=NULL) {
  
	$qlike='';
	$limit = 20;
	
	
		//$output_form = drupal_get_form('opd_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode',  'field'=> 'kodedinas', 'valign'=>'top'), 
		array('data' => 'Nama', 'field'=> 'namauk', 'valign'=>'top'),
		array('data' => 'Singkatan', 'field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'Pimpinan', 'field'=> 'pimpinannama', 'valign'=>'top'),
		array('data' => 'Bendahara', 'field'=> 'bendaharanama', 'valign'=>'top'),
		//array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
		
		$query = db_select('unitkerja', 'uk')->extend('PagerDefault')->extend('TableSort');
		
		# get the desired fields from the database
		$query->fields('uk', array('kodeuk', 'namasingkat', 'namauk', 'kodedinas', 'pimpinannama', 'bendaharanama'));

		if (!isSuperuser()) {
			$kodeuk = apbd_getuseruk();
			$query->condition('uk.kodeuk', $kodeuk, '=');	
			
		}
		
		$query->orderByHeader($header);
		$query->orderBy('uk.kodedinas', 'ASC');
		$query->limit($limit);	
		
			
		# execute the query
		$results = $query->execute();
			
		# build the table fields
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
			
			
			$skpd = l($data->namauk, 'opd/edit/' . $data->kodeuk , array ('html' => true));
			
			//$editlink =  apbd_button_hapus('operator/delete/' . $data->username);
			
			
			$rows[] = array(
							array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
							array('data' => $data->kodedinas, 'align' => 'left', 'valign'=>'top'),
							array('data' => $skpd, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->pimpinannama, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->bendaharanama, 'align' => 'left', 'valign'=>'top'),
							//array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
						);
		}

		//BUTTON
		//$btn = apbd_button_baru('operator/edit');
		//$btn .= "&nbsp;" . apbd_button_excel('');	
		
		
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');

		return $output;
}

function opd_main_form_submit($form, &$form_state) {
	
}

function opd_main_form($form, &$form_state) {
	
}

?>