<?php
function baru_main($arg=NULL, $nama=NULL) {

	
	$output_form = drupal_get_form('baru_main_form');
	return drupal_render($output_form);//.$output;
	
	
}
function baru_main_form($form, &$form_state) {
	
	//SKPD
	$query = db_select('unitkerja', 'p');
	$query->innerJoin('anggperuk', 'a', 'p.kodeuk=a.kodeuk');
	# get the desired fields from the database
	$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
			->orderBy('kodedinas', 'ASC');
	# execute the query
	$results = $query->execute();
	# build the table fields
	if($results){
		foreach($results as $data) {
		  $option_skpd[$data->kodeuk] = $data->namasingkat; 
		}
	}		
	
	
	$form['kodeuk'] = array(
		'#type' => 'select',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#prefix' => '<div id="skpd-replace">',
		'#suffix' => '</div>',
		// When the form is rebuilt during ajax processing, the $selected variable
		// will now have the new value and so the options will change.
		'#options' => $option_skpd,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $kodeuk,
	);
	
	$form['tanggaltitle'] = array(
	'#markup' => '<b>Tanggal</b>',
	);
	$form['tanggal']= array(
		 '#type' => 'date_select', // types 'date_select, date_text' and 'date_timezone' are also supported. See .inc file.
		 '#default_value' => $tanggal, 
				
		 //'#default_value'=> array(
		//	'year' => format_date($TANGGAL, 'custom', 'Y'),
		//	'month' => format_date($TANGGAL, 'custom', 'n'), 
		//	'day' => format_date($TANGGAL, 'custom', 'j'), 
		 // ), 
		 
		 '#date_format' => 'd-m-Y',
		 '#date_label_position' => 'within', // See other available attributes and what they do in date_api_elements.inc
		 '#date_timezone' => 'America/Chicago', // Optional, if your date has a timezone other than the site timezone.
		 //'#date_increment' => 15, // Optional, used by the date_select and date_popup elements to increment minutes and seconds.
		 '#date_year_range' => '-30:+1', // Optional, used to set the year range (back 3 years and forward 3 years is the default).
		 //'#description' => 'Tanggal',
	);
	
	$form['nobukti'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);
	$form['nobuktilain'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti Lain'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);
	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keterangan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);
	
	//KENDARAAN
	$form['formdata1'] = array (
		'#type' => 'fieldset',
		'#title'=> ' Rekening 1',
		'#prefix' => '<div class="col-md-6">',
		 '#suffix' => '</div>',
		//'#field_prefix' => _bootstrap_icon('envelope'),
		//'#collapsible' => FALSE,
		'#collapsed' => FALSE,  	
	);	
		//AJAX
	// Rekening dropdown list
	$form['formdata1']['kodej1'] = array(
		'#title' => t('Jenis'),
		'#type' => 'select',
		'#options' => _load_jenis1(),
		'#default_value' => '',
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_jenis1',
			'wrapper' => 'jenis-wrapper1',
		),
	);

	// Wrapper for rekdetil dropdown list
	$form['formdata1']['wrapperobyek1'] = array(
		'#prefix' => '<div id="jenis-wrapper1">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Jenis -');
	if (isset($form_state['values']['kodej1'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options1 = _load_obyek1($form_state['values']['kodej1']);
	} else
		$options1 = _load_obyek1('');

	// Detil dropdown list
	$form['formdata1']['wrapperobyek1']['kodeo1'] = array(
		'#title' => t('Obyek'),
		'#type' => 'select',
		'#options' => $options1,
		'#default_value' => '',
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_rekening1',
			'wrapper' => 'rekening-wrapper1',
		),
		
	);

	// Wrapper for rekdetil dropdown list
	$form['formdata1']['wrapperrekening1'] = array(
		'#prefix' => '<div id="rekening-wrapper1">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$rekenings = array('- Pilih Rekening -');
	if (isset($form_state['values']['kodeo1'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$rekenings1 = _load_rekening1($form_state['values']['kodeo1']);
	} else
		$rekenings1 = _load_rekening1('');

	// Detil dropdown list
	$form['formdata1']['wrapperrekening1']['kodero1'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => $rekenings1,
		'#default_value' => '',
		'#validated' => TRUE,
	);	
	//END AJAX	
		
		$form['formdata1']['debit1'] = array(
			'#type' => 'textfield',
			'#prefix' => '<div class="col-md-6">',
			'#suffix' => '</div>',
			'#title' =>  t('Debit'),
			'#required' => TRUE,
			'#default_value' => '',
		);
		
		$form['formdata1']['kredit1'] = array(
			'#type' => 'textfield',
			'#prefix' => '<div class="col-md-6">',
		 '#suffix' => '</div>',
			'#title' =>  t('Kredit'),
			'#required' => TRUE,
			'#default_value' => '',
		);
	
	//KENDARAAN
	$form['formdata2'] = array (
		'#type' => 'fieldset',
		'#prefix' => '<div class="col-md-6">',
		 '#suffix' => '</div>',
		'#title'=> ' Rekening 2',
		//'#field_prefix' => _bootstrap_icon('envelope'),
		//'#collapsible' => FALSE,
		'#collapsed' => FALSE,  	
	);	
		//AJAX
	// Rekening dropdown list
	$form['formdata2']['kodej2'] = array(
		'#title' => t('Jenis'),
		'#type' => 'select',
		'#options' => _load_jenis2(),
		'#default_value' => '',
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_jenis2',
			'wrapper' => 'jenis-wrapper2',
		),
	);

	// Wrapper for rekdetil dropdown list
	$form['formdata2']['wrapperobyek2'] = array(
		'#prefix' => '<div id="jenis-wrapper2">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Jenis -');
	if (isset($form_state['values']['kodej2'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options2 = _load_obyek2($form_state['values']['kodej2']);
	} else
		$options2 = _load_obyek2('');

	// Detil dropdown list
	$form['formdata2']['wrapperobyek2']['kodeo2'] = array(
		'#title' => t('Obyek'),
		'#type' => 'select',
		'#options' => $options2,
		'#default_value' => '',
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_rekening2',
			'wrapper' => 'rekening-wrapper2',
		),
		
	);

	// Wrapper for rekdetil dropdown list
	$form['formdata2']['wrapperrekening2'] = array(
		'#prefix' => '<div id="rekening-wrapper2">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$rekenings = array('- Pilih Rekening -');
	if (isset($form_state['values']['kodeo2'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$rekenings2 = _load_rekening2($form_state['values']['kodeo2']);
	} else
		$rekenings2 = _load_rekening2('');

	// Detil dropdown list
	$form['formdata2']['wrapperrekening2']['kodero2'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => $rekenings2,
		'#default_value' => '',
		'#validated' => TRUE,
	);	
	//END AJAX	
		
		$form['formdata2']['debit2'] = array(
			'#type' => 'textfield',
			'#prefix' => '<div class="col-md-6">',
		 '#suffix' => '</div>',
			'#title' =>  t('Debit'),
			'#required' => TRUE,
			'#default_value' => '',
		);
		
		$form['formdata2']['kredit2'] = array(
			'#type' => 'textfield',
			'#prefix' => '<div class="col-md-6">',
		 '#suffix' => '</div>',
			'#title' =>  t('Kredit'),
			'#required' => TRUE,
			'#default_value' => '',
		);
	
	return $form;

}

function _ajax_jenis1($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['formdata1']['wrapperobyek1'];
}


function _ajax_rekening1($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['formdata1']['wrapperrekening1'];
}

/**
 * Function for populating rekening
 */
function _load_jenis1() {
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

function _load_obyek1($kodej1) {
	$obyeks = array('- Pilih Obyek -');


	// Select table
	$query = db_select("obyek", "o");
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));
	// Filter the active ones only
	$query->condition("o.kodej", $kodej1, '=');
	
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

function _load_rekening1($kodeo1) {
	$rekening = array('- Pilih Rekening -');
	//$rekening = array($kodeo);
	
	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodeo", $kodeo1, "=");
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

function _get_uraian1($kodero1){
	
	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero' , 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodero", $kodero1, "=");
	// Order by name
	// Execute query
	$result = $query->execute();
	
	while($data = $result->fetchObject()){
		// Key-value pair for dropdown options
		$uraian[$data->kodero] = $data->uraian;
	}
	
	return $uraian;
	
}

function _ajax_jenis2($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['formdata2']['wrapperobyek2'];
}


function _ajax_rekening2($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['formdata2']['wrapperrekening2'];
}

/**
 * Function for populating rekening
 */
function _load_jenis2() {
	$jenises = array('- Pilih Jenis -');


	// Select table
	$query = db_select("jenis", "j");
	// Selected fields
	$query->fields("j", array('kodej', 'uraian'));
	
	$or = db_or();
	$or->condition("j.kodek", db_like('5') . '%', 'LIKE');
	$or->condition("j.kodek", db_like('2') . '%', 'LIKE');
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

function _load_obyek2($kodej2) {
	$obyeks = array('- Pilih Obyek -');


	// Select table
	$query = db_select("obyek", "o");
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));
	// Filter the active ones only
	$query->condition("o.kodej", $kodej2, '=');
	
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

function _load_rekening2($kodeo2) {
	$rekening = array('- Pilih Rekening -');
	//$rekening = array($kodeo);
	
	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodeo", $kodeo2, "=");
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

function _get_uraian2($kodero2){
	
	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero' , 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodero", $kodero2, "=");
	// Order by name
	// Execute query
	$result = $query->execute();
	
	while($data = $result->fetchObject()){
		// Key-value pair for dropdown options
		$uraian[$data->kodero] = $data->uraian;
	}
	
	return $uraian;
	
}
	
?>