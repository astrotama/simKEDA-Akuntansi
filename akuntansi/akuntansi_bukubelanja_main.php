<?php
function akuntansi_bukubelanja_main($arg=NULL, $nama=NULL) {
   

	$output_form = drupal_get_form('akuntansi_bukubelanja_main_form');
	return drupal_render($output_form);
	
}

function akuntansi_bukubelanja_main_form ($form, &$form_state) {
	$kodeuk = arg(1);	
	$kodekeg = arg(2);
	$kodero = arg(2);

	$tglawal_form =  apbd_date_create_dateone_form();		//mktime(0, 0, 0, date('m'), 1, apbd_tahun());
	$tglakhir_form =  apbd_date_create_currdate_form();		//mktime(0, 0, 0, date('m'), date('d'), apbd_tahun());

	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
			'#validated' => TRUE,
			'#ajax' => array(
				'event'=>'change',
				'callback' =>'_ajax_kegiatan',
				'wrapper' => 'kegiatan-wrapper',
			),
		);	
		
	} else {
	
		//AJAX
		// Rekening dropdown list
		$form['kodeuk'] = array(
			'#title' => t('Unit Kerja'),
			'#type' => 'select',
			'#options' => _load_skpd(),
			'#default_value' => $kodeuk,
			'#validated' => TRUE,
			'#ajax' => array(
				'event'=>'change',
				'callback' =>'_ajax_kegiatan',
				'wrapper' => 'kegiatan-wrapper',
			),
		);

	}

	// Wrapper for rekdetil dropdown list
	$form['wrapperkegiatan'] = array(
		'#prefix' => '<div id="kegiatan-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Kegiatan -');
	if (isset($form_state['values']['kodeuk'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options = _load_kegiatan($form_state['values']['kodeuk']);
	} else
		$options = _load_kegiatan($kodeuk);

	// Detil dropdown list
	$form['wrapperkegiatan']['kodekeg'] = array(
		'#title' => t('Kegiatan'),
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
	if (isset($form_state['values']['kodekeg'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$rekenings = _load_rekening($form_state['values']['kodekeg']);
	} else
		$rekenings = _load_rekening($kodekeg);

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


function _ajax_kegiatan($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperkegiatan'];
}


function _ajax_rekening($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperrekening'];
}

/**
 * Function for populating rekening
 */
function _load_skpd() {
	$skpd = array('- Pilih SKPD -');


	// Select table
	$query = db_select("unitkerja", "u");
	// Selected fields
	$query->fields("u", array('kodeuk', 'namasingkat'));
	
	// Order by name
	$query->orderBy("u.namasingkat");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$skpd[$row->kodeuk] = $row->namasingkat;
	}

	return $skpd;
}

function _load_kegiatan($kodeuk) {
	$kegiatan = array('- Pilih Kegiatan -');


	// Select table
	$query = db_select("kegiatanskpd", "k");
	// Selected fields
	$query->fields("k", array('kodekeg', 'kegiatan'));
	// Filter the active ones only
	$query->condition("k.kodeuk", $kodeuk, '=');
	
	// Order by name
	$query->orderBy("k.kegiatan");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$kegiatan[$row->kodekeg] = $row->kegiatan;
	}

	return $kegiatan;
}

function _load_rekening($kodekeg) {
	$rekening = array('- Pilih Rekening -');
	//$rekening = array($kodekeg);
	
	// Select table
	$query = db_select("anggperkeg", "a");
	$query->innerJoin("rincianobyek", "r", "a.kodero=r.kodero");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	$query->condition("a.kodekeg", $kodekeg, "=");
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

function akuntansi_bukubelanja_main_form_validate($form, &$form_state) {
	$tglawal = $form_state['values']['tglawal'];
	$tglawalx = apbd_date_convert_form2db($tglawal);
	
	$tglakhir = $form_state['values']['tglakhir'];
	$tglakhirx = apbd_date_convert_form2db($tglakhir);		
	if ($tglakhirx < $tglawalx) form_set_error('tglakhir', 'Tanggal laporan harus diisi dengan benar, dimana tanggal akhir tidak boleh lebih kecil daripada tanggal awal');
}

function akuntansi_bukubelanja_main_form_submit($form, &$form_state) {
	//akuntansi/buku/kodekeg/41201006/kodeuk
	$kodeuk = $form_state['values']['kodeuk'];
	$kodekeg = $form_state['values']['kodekeg'];
	$kodero = $form_state['values']['kodero'];
	
	$tglawal = $form_state['values']['tglawal'];
	$tglawalx = apbd_date_convert_form2db($tglawal);
	
	$tglakhir = $form_state['values']['tglakhir'];
	$tglakhirx = apbd_date_convert_form2db($tglakhir);		
	$uri = '/akuntansi/buku/' . $kodekeg . '/'  . $kodero . '/'  . $kodeuk . '/' . $tglawalx  . '/' . $tglakhirx;
	
	drupal_goto($uri);

}

	

?>
