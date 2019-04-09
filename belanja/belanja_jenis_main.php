<?php
function belanja_jenis_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	if (arg(2)) {
		$field='kodeskpd';
		$kodeuk=arg(3);
		$tahun=arg(2);
		
		if(!isset($kodeuk) or $kodeuk=='ZZ'){
			$field='1';
			$kodeuk='1';
		}
		
		
	} else {
		$field='1';
		$kodeuk='1';
		$tahun=2015;
	}
	
	//drupal_set_message($tahun);
	if($kodeuk!='1' && isset($kodeuk)){
		$query = db_select('unitkerja'.$tahun, 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
					->condition('kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
				foreach($results as $data) {
						$ntitle=$data->namasingkat;
				}
			}
	}
	else{
		$ntitle='SELURUH SKPD';
	}
	
	if ($tahun<2010) $tahun = 2010;
	
	$tahun1 = $tahun-1;
	$tahun2 = $tahun-2;
	$title='REKAP BELANJA '.$ntitle . ' ' . $tahun2 . '-' . $tahun;
	drupal_set_title($title);

	
	$output_form = drupal_get_form('belanja_jenis_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'rowspan'=>'2', 'valign'=>'top'),
		array('data' => 'Nama', 'width' => '180px', 'rowspan'=>'2', 'valign'=>'top'),
		array('data' => 'A' . $tahun2, 'width' => '80px', 'valign'=>'top'),
		array('data' => 'R' . $tahun2, 'width' => '80px', 'valign'=>'top'),
		array('data' => '%' . $tahun2, 'width' => '25px', 'valign'=>'top'),
		array('data' => 'A' . $tahun1, 'width' => '80px', 'valign'=>'top'),
		array('data' => 'R' . $tahun1, 'width' => '80px', 'valign'=>'top'),
		array('data' => '%' . $tahun1, 'width' => '25px', 'valign'=>'top'),
		array('data' => 'A' . $tahun, 'width' => '80px',  'valign'=>'top'),
		array('data' => 'R' . $tahun, 'width' => '80px', 'width' => '80px', 'valign'=>'top'),
		array('data' => '%' . $tahun, 'width' => '25px', 'valign'=>'top'),
		
		
	);
	
	
	
	/*
	# get the desired fields from the database
	  
	$query->fields('p', array('kegiatan', 'kodeuk','sumberdana', 'sasaran', 'target', 'anggaran1', 'anggaran2','realisasi'))
		//
		->condition($field, $kodeuk, '=')
		->orderByHeader($header)
		->orderBy('kegiatan', 'ASC')
		->limit($limit);
	*/
	//drupal_set_message($tahun);
	
	
	$query = db_select('apbdrekap' . $tahun, 'k')->extend('PagerDefault')->extend('TableSort');
	

	# get the desired fields from the database
	  $ne=arg(3);
	$query->distinct();
	$query->fields('k', array('namakelompok','kodekelompok'));
	$query->addExpression('SUM(anggaran2)', 'anggaran');
	$query->addExpression('SUM(realisasi)', 'realisasi');
	$query->condition($field, $kodeuk, '=');
	$query->groupBy('namakelompok');
	$query->orderBy('kodekelompok', 'ASC');
	
	//$query->limit(100);
	
	# execute the query
	$results = $query->execute();
	//drupal_set_message($query);
	# build the table fields
	$no=0;

	

	
	$rows = array();
	$nilai = array();
	foreach ($results as $data) {
		$nilai['nama']['kelompok']=$data->namakelompok;
		
		//for($l=2013;$l<2016;$l++){
		for($l=$tahun2; $l<=$tahun; $l++){
			$query = db_select('apbdrekap' . $l, 'k')->extend('PagerDefault')->extend('TableSort');
			# get the desired fields from the database
			$query->distinct();
			$query->fields('k', array('namakelompok','kodekelompok'));
			$query->addExpression('SUM(anggaran2)', 'anggaran');
			$query->addExpression('SUM(realisasi)', 'realisasi');
			$query->condition('namakelompok', $data->namakelompok, '=');
			$query->condition($field, $kodeuk, '=');
			$query->groupBy('namakelompok');
			//$query->orderBy('k.kodero', 'ASC');
			
			$query->limit(100);//drupal_set_message($query);
			# execute the query
			$results = $query->execute();
			foreach ($results as $datas) {
				$no++;
				$nilai[$l]['anggaran']= $datas->anggaran;
				$nilai[$l]['realisasi']= $datas->realisasi;
				
				
			}
			
		}
		$rows[] = array(
						array('data' => $data->kodekelompok, 'align' => 'left', 'valign'=>'top'),
						array('data' => '<strong>' . $data->namakelompok . '</strong>', 'align' => 'left', 'valign'=>'top'),

						array('data' => '<p class="text-success"><strong>' . apbd_fn($nilai[$tahun2]['anggaran']) . '</strong></p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-success"><strong>' . apbd_fn($nilai[$tahun2]['realisasi']) . '</strong></p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-success"><strong>' . apbd_fn1(apbd_hitungpersen($nilai[$tahun2]['anggaran'], $nilai[$tahun2]['realisasi'])) . '</strong></p>', 'align' => 'right'),
						
						
						array('data' => '<p class="text-primary"><strong>' . apbd_fn($nilai[$tahun1]['anggaran']) . '</strong></p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-primary"><strong>' . apbd_fn($nilai[$tahun1]['realisasi']) . '</strong></p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-primary"><strong>' . apbd_fn1(apbd_hitungpersen($nilai[$tahun1]['anggaran'],$nilai[$tahun1]['realisasi'])) . '</strong></p>', 'align' => 'right'),
						
						
						array('data' => '<p class="text-danger"><strong>' . apbd_fn($nilai[$tahun]['anggaran']) . '</strong></p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-danger"><strong>' . apbd_fn( $nilai[$tahun]['realisasi']) . '</strong></p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-danger"><strong>' . apbd_fn1(apbd_hitungpersen($nilai[$tahun]['anggaran'],$nilai[$tahun]['realisasi'])) . '</strong></p>', 'align' => 'right'),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		
		
		//<font color="red">This is some text!</font>
		/*$anggaran = apbd_fn($data->anggaran2);
		
		if ($data->anggaran1 > $data->anggaran2)
			$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1) . '</font></p>';
		else if ($data->anggaran1 < $data->anggaran2)
			$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1) . '</font></p>';
		*/
		
		$query = db_select('apbdrekap' . $tahun, 'k')->extend('PagerDefault')->extend('TableSort');
		$query->distinct();
		$query->fields('k', array('namajenis','kodejenis'));
		$query->addExpression('SUM(anggaran2)', 'anggaran');
		$query->addExpression('SUM(realisasi)', 'realisasi');
		$query->condition('kodekelompok', $data->kodekelompok, '=');
		$query->condition($field, $kodeuk, '=');
		$query->groupBy('namajenis');
		$query->orderBy('kodejenis', 'ASC');
		
		//$query->limit(100);
		# execute the query
		$results = $query->execute();
		foreach ($results as $data) {
			for($l=2013;$l<2016;$l++){
				$query = db_select('apbdrekap' . $l, 'k')->extend('PagerDefault')->extend('TableSort');
				# get the desired fields from the database
				$query->distinct();
				$query->fields('k', array('namajenis','kodekelompok'));
				$query->addExpression('SUM(anggaran2)', 'anggaran');
				$query->addExpression('SUM(realisasi)', 'realisasi');
				$query->condition('kodejenis', $data->kodejenis, '=');
				$query->condition($field, $kodeuk, '=');
				$query->groupBy('namajenis');
				//$query->orderBy('k.kodero', 'ASC');
				
				$query->limit(100);
				# execute the query
				$results = $query->execute();
				foreach ($results as $datas) {
					$no++;
					$nilai[$l]['anggaran']= $datas->anggaran;
					$nilai[$l]['realisasi']= $datas->realisasi;
					
					
				}
				
			}
			
			$rows[] = array(
						array('data' => $data->kodejenis, 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data->namajenis)), 'align' => 'left', 'valign'=>'top'),
						
						array('data' => '<p class="text-success">' . apbd_fn($nilai[$tahun2]['anggaran']) . '</p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-success">' . apbd_fn($nilai[$tahun2]['realisasi']) . '</p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-success">' . apbd_fn1(apbd_hitungpersen($nilai[$tahun2]['anggaran'],$nilai[$tahun2]['realisasi'])) . '</p>', 'align' => 'right'),
						
						array('data' => '<p class="text-primary">' . apbd_fn($nilai[$tahun1]['anggaran']) . '</p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-primary">' . apbd_fn($nilai[$tahun1]['realisasi']) . '</p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-primary">' . apbd_fn1(apbd_hitungpersen($nilai[$tahun1]['anggaran'],$nilai[$tahun1]['realisasi'])) . '</p>', 'align' => 'right'),
						
						array('data' => '<p class="text-danger">' . apbd_fn($nilai[$tahun]['anggaran']) . '</p>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-danger">' . apbd_fn($nilai[$tahun]['realisasi']) . '</p>',  'align' => 'right', 'valign'=>'top'),
						array('data' => '<p class="text-danger">' . apbd_fn1(apbd_hitungpersen($nilai[$tahun]['anggaran'],$nilai[$tahun]['realisasi'])) . '</p>', 'align' => 'right'),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
			
		}
	}

	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	
	$output_form = drupal_get_form('belanja_jenis_main_form');
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	//$output .= theme('pager');
	return  drupal_render($output_form).$btn . $output . $btn;
}


function belanja_jenis_main_form_submit($form, &$form_state) {
	$tahun= $form_state['values']['tahun'];
	$skpd = $form_state['values']['skpd'];
	
	//drupal_set_message($row[2014][1]); 
	$kodeuk = 'ZZ';
	$query = db_select('unitkerja'.$tahun, 'p');
	$query->fields('p', array('namasingkat','kodeuk'))
		  ->condition('namasingkat',$skpd,'=');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$kodeuk = $data->kodeuk;
		}
	}
	$uri = 'belanja/jenis/' . $tahun.'/'.$kodeuk;
	drupal_goto($uri);
	
}


function belanja_jenis_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$namasingkat = 'SELURUH SKPD';
	if(arg(2)!=null){
		
		$tahun = arg(2);
		
		$kodeuk = arg(3);
		$query = db_select('unitkerja'.arg(2), 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',arg(3),'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat=$data->namasingkat;
			}
		}
			 
	}
	else{
		$tahun = 2015;
		$namasingkat='';
		
	}
	
	
	// Get the list of options to populate the first dropdown.
	$option_tahun = _ajax_get_tahun_dropdown();
	// If we have a value for the first dropdown from $form_state['values'] we use
	// this both as the default value for the first dropdown and also as a
	// parameter to pass to the function that retrieves the options for the
	// second dropdown.
  
	$selected_tahun = isset($form_state['values']['tahun']) ? $form_state['values']['tahun'] : $tahun;
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$form['formdata']['tahun'] = array(
		'#type' => 'select',
		'#title' => 'Tahun',
		'#options' => $option_tahun,
		'#default_value' => $tahun,		//$selected,
		// Bind an ajax callback to the change event (which is the default for the
		// select form type) of the first dropdown. It will replace the second
		// dropdown when rebuilt.
		'#ajax' => array(
		  // When 'event' occurs, Drupal will perform an ajax request in the
		  // background. Usually the default value is sufficient (eg. change for
		  // select elements), but valid values include any jQuery event,
		  // most notably 'mousedown', 'blur', and 'submit'.
		  // 'event' => 'change',
			'callback' => 'belanja_jenis_main_form_callback',
			'wrapper' => 'skpd-replace',
		),
	);

	$form['formdata']['skpd'] = array(
		'#type' => 'select',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#prefix' => '<div id="skpd-replace">',
		'#suffix' => '</div>',
		// When the form is rebuilt during ajax processing, the $selected variable
		// will now have the new value and so the options will change.
		'#options' => _ajax_get_skpd_dropdown($selected_tahun),
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $namasingkat,
	);
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Tampilkan'),
	);
	return $form;
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
function belanja_jenis_main_form_callback($form, $form_state) {
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
