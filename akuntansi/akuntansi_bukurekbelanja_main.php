<?php
function akuntansi_bukurekbelanja_main($arg=NULL, $nama=NULL) {
   

	$output_form = drupal_get_form('akuntansi_bukurekbelanja_main_form');
	return drupal_render($output_form);
	
}

function akuntansi_bukurekbelanja_main_form ($form, &$form_state) {
	$kodej = arg(1);	
	$kodeo = arg(2);
	$kodero = arg(2);

	drupal_set_message($kodeo);
	
	$tglawal_form =  apbd_date_create_dateone_form();		//mktime(0, 0, 0, date('m'), 1, apbd_tahun());
	$tglakhir_form =  apbd_date_create_currdate_form();		//mktime(0, 0, 0, date('m'), date('d'), apbd_tahun());

	//SKPD
	if (isUserSKPD()) {
		$form['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => apbd_getuseruk(),
			'#validated' => TRUE,
		);	
		
	} else {
		$skpds = array();
		$skpds['ZZ'] = 'SELURUH SKPD';
		$query = db_select("unitkerja", "u");
		// Selected fields
		$query->fields("u", array('kodeuk', 'namasingkat'));
		
		// Order by name
		$query->orderBy("u.namasingkat");
		// Execute query
		$result = $query->execute();

		while($row = $result->fetchObject()){
			// Key-value pair for dropdown options
			$skpds[$row->kodeuk] = $row->namasingkat;
		}	
		$form['kodeuk'] = array(
			'#title' => t('SKPD'),
			'#type' => 'select',
			'#options' => $skpds,
			'#default_value' => 'ZZ',
			'#validated' => TRUE,
		);	
	}	
	
	//AJAX
	// Rekening dropdown list
	$form['kodej'] = array(
		'#title' => t('Jenis'),
		'#type' => 'select',
		'#options' => _load_jenis(),
		'#default_value' => $kodej,
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_jenis',
			'wrapper' => 'jenis-wrapper',
		),
	);

	// Wrapper for rekdetil dropdown list
	$form['wrapperobyek'] = array(
		'#prefix' => '<div id="jenis-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Jenis -');
	if (isset($form_state['values']['kodej'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options = _load_obyek($form_state['values']['kodej']);
	} else
		$options = _load_obyek($kodej);

	// Detil dropdown list
	$form['wrapperobyek']['kodeo'] = array(
		'#title' => t('Obyek'),
		'#type' => 'select',
		'#options' => $options,
		'#default_value' => '',
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_rekening',
			'wrapper' => 'rekening-wrapper',
		),
		
	);

	// Wrapper for rekdetil dropdown list
	$form['wrapperrekening'] = array(
		'#prefix' => '<div id="rekening-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$rekenings = array('- Pilih Rekening -');
	if (isset($form_state['values']['kodeo'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$rekenings = _load_rekening($form_state['values']['kodeo']);
	} else
		$rekenings = _load_rekening($kodeo);

	// Detil dropdown list
	$form['wrapperrekening']['kodero'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => $rekenings,
		'#default_value' => '',
		'#validated' => TRUE,
	);	
	//END AJAX

	$form['tglawal'] = array(
		'#type' => 'date',
		'#title' =>  t('Periode laporan, mulai tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#default_value' => $tglawal,
		'#default_value'=> array(
			'year' => format_date($tglawal_form, 'custom', 'Y'),
			'month' => format_date($tglawal_form, 'custom', 'n'), 
			'day' => format_date($tglawal_form, 'custom', 'j'), 
		  ), 		
	);
	$form['tglakhir'] = array(
		'#type' => 'date',
		'#title' =>  t('Sampai dengan tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#default_value' => $tglakhir,
		'#default_value'=> array(
			'year' => format_date($tglakhir_form, 'custom', 'Y'),
			'month' => format_date($tglakhir_form, 'custom', 'n'), 
			'day' => format_date($tglakhir_form, 'custom', 'j'), 
		  ), 		
	);
	$form['submitprint']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	

	return $form;
}


function _ajax_jenis($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperobyek'];
}


function _ajax_rekening($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperrekening'];
}

/**
 * Function for populating rekening
 */
function _load_jenis() {
	$jenises = array('- Pilih Jenis -');


	// Select table
	$query = db_select("jenis", "j");
	// Selected fields
	$query->fields("j", array('kodej', 'uraian'));
	
	$or = db_or();
	$or->condition("j.kodek", db_like('5') . '%', 'LIKE');
	$or->condition("j.kodek", db_like('1') . '%', 'LIKE');
	if (isSuperuser()) $or->condition("j.kodek", db_like('62') . '%', 'LIKE');
	
	//$query->condition("j.kodek", db_like('5') . '%', 'LIKE');
	$query->condition($or);
	
	// Order by name
	$query->orderBy("j.kodej");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$jenises[$row->kodej] = $row->uraian;
	}

	$query = db_select("jenis", "j");
	// Selected fields
	$query->fields("j", array('kodej', 'uraian'));
	
	$query->condition("j.kodek", db_like('62') . '%', 'LIKE');
	
	// Order by name
	$query->orderBy("j.kodej");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$jenises[$row->kodej] = $row->uraian;
	}
	
	return $jenises;
}

function _load_obyek($kodej) {
	$obyeks = array('- Pilih Obyek -');


	// Select table
	$query = db_select("obyek", "o");
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));
	// Filter the active ones only
	$query->condition("o.kodej", $kodej, '=');
	
	// Order by name
	$query->orderBy("o.kodeo");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$obyeks[$row->kodeo] = $row->uraian;
	}

	return $obyeks;
}

function _load_rekening($kodeo) {
	$rekening = array('- Pilih Rekening -');
	//$rekening = array($kodeo);
	
	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodeo", $kodeo, "=");
	// Order by name
	$query->orderBy("r.kodero");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$rekening[$row->kodero] = $row->kodero . ' - ' . $row->uraian;
	}

	return $rekening;
}

function akuntansi_bukurekbelanja_main_form_validate($form, &$form_state) {
	$tglawal = $form_state['values']['tglawal'];
	$tglawalx = apbd_date_convert_form2db($tglawal);
	
	$tglakhir = $form_state['values']['tglakhir'];
	$tglakhirx = apbd_date_convert_form2db($tglakhir);		
	if ($tglakhirx < $tglawalx) form_set_error('tglakhir', 'Tanggal laporan harus diisi dengan benar, dimana tanggal akhir tidak boleh lebih kecil daripada tanggal awal');

}

function akuntansi_bukurekbelanja_main_form_submit($form, &$form_state) {
	//akuntansi/buku/kodeo/41201006/kodej
	$kodeuk = $form_state['values']['kodeuk'];
	$kodej = $form_state['values']['kodej'];
	$kodeo = $form_state['values']['kodeo'];
	$kodero = $form_state['values']['kodero'];

	$tglawal = $form_state['values']['tglawal'];
	$tglawalx = apbd_date_convert_form2db($tglawal);
	
	$tglakhir = $form_state['values']['tglakhir'];
	$tglakhirx = apbd_date_convert_form2db($tglakhir);		

	if (strlen($kodeo)<5) $kodeo = $kodej;
	if (strlen($kodero)<8) $kodero = $kodeo;
	
	$uri = '/akuntansi/buku/ZZ/'  . $kodero . '/'  . $kodeuk . '/' . $tglawalx  . '/' . $tglakhirx;
	
	drupal_goto($uri);

}

	

?>
