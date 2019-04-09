<?php
function akuntansi_edit_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
				$tahun = arg(2);
				$jurnalid = arg(3);
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$jurnalid = '';
		
	}
	
	drupal_set_title('Jurnal');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	
	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'width' => '80px','valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => 'Debet', 'width' => '120px', 'valign'=>'top'),
		array('data' => 'Kredit', 'width' => '120px', 'valign'=>'top'),
		
		
	);
	
	# get the desired fields from the database

	//$tahun='2015';
	$query = db_select('jurnalitem' . $tahun, 'k')->extend('TableSort');
	$query->fields('k', array('uraian', 'kodero','debet','kredit','keterangan'));
	$query->condition('k.jurnalid', $jurnalid, '=');

	# get the desired fields from the database
	$query->orderByHeader($header);
	$query->orderBy('k.kodero', 'ASC');
	//$query->limit($limit);
	//drupal_set_message($ne);	
	//drupal_set_message($query);	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;


	
	$total=0;$total2=0;		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		
		
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					$total+=$data->debet;
					$total2+=$data->kredit;
	}
	
	$rows[] = array(
						array('data' => '<strong>TOTAL</strong>', 'width' => '10px','colspan'=>'3', 'align' => 'center', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn($total) . '</strong>', 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn($total2) . '</strong>', 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$btn = l('Cetak', '/akuntansi/edit/'.$tahun.'/'.$jurnalid.'/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	
	if(arg(4)=='pdf'){
			  
			  $output = getTable($tahun,$jurnalid);
			  print_pdf_p($output);
		}
	else{
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('akuntansi_edit_main_form');
		return drupal_render($output_form).$btn . $output . $btn;
	}
	
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
function akuntansi_edit_main_form_callback($form, $form_state) {
  return $form['formdata']['skpd'];
}

function akuntansi_edit_main_form($form, &$form_state) {
	
	$tahun = arg(2);
	$jurnalid = arg(3);
	
	$query = db_select('jurnal' . $tahun, 'j');
	$query->leftJoin('kegiatan' . $tahun, 'k', 'j.kodekeg=k.kodekeg');
	$query->fields('j', array('jurnalid','kodeuk', 'kodekeg', 'nobukti','noref','tanggal', 'keterangan', 'total'));
	$query->fields('k', array('kegiatan'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('jurnalid', $jurnalid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$tanggal = apbd_format_tanggal_pendek($data->tanggal);
		$nobukti= $data ->nobukti;
		$noref= $data ->noref;
		$keterangan= $data ->keterangan;

		$kodekeg = $data->kodekeg;
		$kegiatan = $data->kegiatan;
		
	}
	if ($kegiatan=='') $kegiatan = 'Non Kegiatan';
	
	$form['formdata']['tanggal'] = array(
		'#type' => 'item',
		'#title' =>  t('Tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#markup' => '<p>' . $tanggal . '</p>',
	);
	$form['formdata']['nobukti'] = array(
		'#type' => 'item',
		'#title' =>  t('No Bukti'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#markup' =>'<p>' . $nobukti . '</p>',
	);
	$form['formdata']['noref'] = array(
		'#type' => 'item',
		'#title' =>  t('No Ref'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#markup' =>'<p>' . $noref . '</p>',
	);
	$form['formdata']['kegiatan'] = array(
		'#type' => 'item',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#markup' =>'<p>' . $kegiatan . '</p>',
	);
	
	$form['formdata']['ket'] = array(
		'#type' => 'item',
		'#title' =>  t('Keterangan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#markup' =>'<p>' . $keterangan . '</p>',
	);
	
	
	return $form;
}

function getTable($tahun,$jurnalid){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'Uraian', 'width' => '240px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Debet', 'width' => '120px','align'=>'center','style'=>$styleheader),
		array('data' => 'Kredit', 'width' => '120px','align'=>'center','style'=>$styleheader),
		
		
	);
		
			
			# get the desired fields from the database

			//$tahun='2015';
			$query = db_select('jurnalitem' . $tahun, 'k')->extend('TableSort');
			$query->fields('k', array('uraian', 'kodero','debet','kredit','keterangan'));
			$query->condition('k.jurnalid', $jurnalid, '=');

			# get the desired fields from the database
			$query->orderByHeader($header);
			$query->orderBy('k.kodero', 'ASC');
			//$query->limit($limit);
			//drupal_set_message($ne);	
			//drupal_set_message($query);	
			# execute the query
			$results = $query->execute();
				
			# build the table fields
			$no=0;


			
			$total=0;$total2=0;		
			$rows = array();
			foreach ($results as $data) {
				$no++;  
				
				
				
				$rows[] = array(
							array('data' => $no,'width' => '40px', 'align'=>'right','style'=>'border-left:1px solid black;'.$style),
							array('data' => $data->kodero, 'width' => '80px','align'=>'left','style'=>$style),
							array('data' => $data->uraian, 'width' => '240px', 'align'=>'left','style'=>$style),
							array('data' => apbd_fn($data->debet), 'width' => '120px','align'=>'right','style'=>$style),
							array('data' => apbd_fn($data->kredit), 'width' => '120px','align'=>'right','style'=>$style),
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
								
							);
							$total+=$data->debet;
							$total2+=$data->kredit;
			}
			
			$rows[] = array(
								array('data' => 'JUMLAH', 'width' => '360px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
								array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
								array('data' => apbd_fn($total2), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
								
								//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
								
							);
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
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
