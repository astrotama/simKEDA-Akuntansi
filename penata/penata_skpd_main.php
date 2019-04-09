<?php
function Penata_skpd_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
				$ntitle = 'Penata Usahaan';
				$nntitle ='';
				$tahun = arg(2);
				
				$kodekeg = arg(3);
				
				drupal_set_title($ntitle);
				
				
		
		
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$kodekeg = 'ZZ';
		
	}
	
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'valign'=>'top'),
		array('data' => 'No.SP2D', 'valign'=>'top'),
		array('data' => 'Tgl.SP2D', 'valign'=>'top'),
		array('data' => 'Uraian',  'valign'=>'top'),
		array('data' => 'Penerima', 'width' => '80px',  'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px',  'valign'=>'top'),
		
	);
	
	/*
	$query = db_select('kegiatan' . $tahun, 'p')->extend('PagerDefault')->extend('TableSort');
	if ($kodeuk !='ZZ'){
		$field='kodeuk';
		$value= $kodeuk;
	}
	else {
		$field='1';
		$value='1';
	};

	# get the desired fields from the database
	  
	$query->fields('p', array('kegiatan', 'kodeuk','sumberdana', 'sasaran', 'target', 'anggaran1', 'anggaran2','realisasi'))
		//
		->condition($field, $value, '=')
		->orderByHeader($header)
		->orderBy('kegiatan', 'ASC')
		->limit($limit);
	*/
	//$tahun='2015';
	$query = db_select('dokumen' . $tahun, 'k')->extend('TableSort');
	//$query->innerJoin('unitkerja' . $tahun, 'u', 'k.kodeuk=u.kodeuk');
	if ($kodekeg !='ZZ'){
		$field='k.kodekeg';
		$value= $kodekeg;
	}
	else {
		$field='1';
		$value='1';
	};
	//drupal_set_message($kodekeg);

	# get the desired fields from the database
	 $ne=arg(3);
	$query->fields('k', array('kodeuk', 'sp2dno','sp2dtgl','keperluan', 'penerimanama', 'jumlah'));
	//$query->fields('u', array('namasingkat'));
	$query->condition($field, $ne, '=');
	//$query->condition($field2, $value2, '=');
	$query->orderByHeader($header);
	$query->orderBy('k.sp2dtgl', 'ASC');
	//$query->limit($limit);
	//drupal_set_message($ne);	
	//drupal_set_message($query);	
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

	
	$total=0;	
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		
		$query = db_select('unitkerja' . $tahun, 'k')->extend('PagerDefault')->extend('TableSort');
		$query->fields('k', array('namasingkat'));
		$query->condition('kodeuk', $data->kodeuk, '=');
		
		$results = $query->execute();
		foreach ($results as $datas){
			$namasingkat=$datas->namasingkat;
		}
		$arrtgl=explode('-',$data->sp2dtgl);
		$bulanoption=array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$tgl=$arrtgl[2].'-'.$arrtgl[1].'-'.$arrtgl[0];
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => $namasingkat, 'width' => '100px', 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sp2dno, 'width' => '100px','align' => 'left', 'valign'=>'top'),
						array('data' => $tgl, 'width' => '120px', 'align' => 'center', 'valign'=>'top'),
						array('data' => $data->keperluan, 'width' => '230px', 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimanama, 'width' => '120px', 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah), 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					$total+=$data->jumlah;
	}
	
	$rows[] = array(
						array('data' => 'JUMLAH', 'width' => '10px','colspan'=>'6', 'align' => 'center', 'valign'=>'top'),
						array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	//$output .= theme('pager');
	return $btn . $output . $btn;
}



/**
 * Selects just the second dropdown to be returned for re-rendering.
 *
 * Since the controlling logic for populating the form is in the form builder
 * function, all we do here is select the element and return it to be updated.
 *
 * @return array
 *   Renderable array (the second dropdown)
 */
function Penata_skpd_main_form_callback($form, $form_state) {
  return $form['formdata']['skpd'];
}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */
function _ajax_get_tahun_dropdown() {
  // drupal_map_assoc() just makes an array('String' => 'String'...).
  return drupal_map_assoc(
    array(
	  t('2015'),
	  t('2014'),
	  t('2013'),
	  t('2012'),
      t('2011'),
      t('2010'),
      t('2009'),
      t('2008'),
    )
  );
}

/**
 * Helper function to populate the second dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @param string $key
 *   This will determine which set of options is returned.
 *
 * @return array
 *   Dropdown options
 */
function _ajax_get_skpd_dropdown($key = '') {
	$row = array();
	for($n=2015;$n>=2008;$n--){
		$query = db_select('unitkerja'.$n, 'p');

		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');

		# execute the query
		$results = $query->execute();
		
			
		# build the table fields
		$row[$n]['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $row[$n][$data->kodeuk] = $data->namasingkat; 
			}
		}
	}
	
	$options = array(
		t('2008') => drupal_map_assoc(
			$row[2008]
		),
		t('2009') => drupal_map_assoc(
			$row[2009]
		),
		t('2010') => drupal_map_assoc(
			$row[2010]
		),
		t('2011') => drupal_map_assoc(
			$row[2011]
		),
		t('2012') => drupal_map_assoc(
			$row[2012]
		),
		t('2013') => drupal_map_assoc(
			$row[2013]
		),
		t('2014') => drupal_map_assoc(
			$row[2014]
		),
		t('2015') => drupal_map_assoc(
			$row[2015]
		),
	);
	
	if (isset($options[$key])) {
		return $options[$key];
	} else {
		return array();
	}
}


?>
