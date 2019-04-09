<?php
function tambah_jurnal_item_main($arg=NULL, $nama=NULL) {
	$kodero = arg(2);
	if (!empty($kodero)) {
		update_jurnal_item($kodero);
		drupal_goto('jurnal_item');
		
	} else {
	
		$output_form = drupal_get_form('tambah_jurnal_item_main_form');
		return drupal_render($output_form);
	}	
}

function tambah_jurnal_item_main_form ($form, &$form_state) {
	
	//AJAX
	// Rekening dropdown list
	$form['kodej'] = array(
		'#title' => t('Jenis'),
		'#type' => 'select',
		'#options' => _load_jenis(),
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
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options = _load_obyek($form_state['values']['kodej']);


	// Detil dropdown list
	$form['wrapperobyek']['kodeo'] = array(
		'#title' => t('Obyek'),
		'#type' => 'select',
		'#options' => $options,
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

	$form['wrapperrekening']['descripton'] = array(
		'#markup' => '<p>Pilih rekening dari Jenis dan Obyek yang sesuai dengan mengklik tombil Pilih</p>',
	);	
	
		// Pre-populate options for rekdetil dropdown list if rekening id is set
	$rekenings = _load_rekening($form_state['values']['kodeo'], $form_state['values']['kodej']);

	// Detil dropdown list
	$form['wrapperrekening']['rekselector'] = array(
		'#markup' => $rekenings,
	);	
	//END AJAX

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
	$result = db_query("SELECT kodej, uraian FROM {jenissap}");
	
	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$jenises[$row->kodej] = $row->kodej . ' - ' . $row->uraian;
	}

	return $jenises;
}

function _load_obyek($kodej) {
	$obyeks = array('- Pilih Obyek -');


	// Select table
	$query = db_select("obyeksap", "o");
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
		$obyeks[$row->kodeo] = $row->kodeo . ' - ' . $row->uraian;
	}

	return $obyeks;
}

function _load_rekening($kodeo, $kodej) {
if ($kodeo=='') {
	$tabel_data = '<p>Piilih Jenis lalu pilih Obyek. Kemudian dari daftar rekening yang muncul, pilih Rekening yang sesuai.</p>';
} else {
	//TABEL
	$header = array (
		array('data' => 'Kode','width' => '20px', 'valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => '', 'width' => '40px', 'valign'=>'top'),
	);

	$rows = array();
	$query = db_select('rincianobyeksap', 'r');
	$query->fields('r', array('kodero', 'uraian'));
	$query->condition('r.kodeo', $kodeo, '=');
	$query->orderBy('r.kodero');
	$results = $query->execute();	
	foreach ($results as $data) {
		
		$linkedit = l('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Pilih', 'jurnal_item/tambah/'  . $data->kodero , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-success btn-sm')));
		
		
		$rows[] = array(
			array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
			array('data' => $linkedit, 'align' => 'left', 'valign'=>'top'),
		);
		
		

	}	//jenis

	//RENDER	
	$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));
	//$tabel_data = createT($header, $rows);

}
return $tabel_data;
}


function update_jurnal_item($kodero) {
	
	$ada = false;
	$res = db_query('select kodero from {jurnalitem} where kodero=:kodero', array(':kodero'=>$kodero));
	foreach ($res as $datamap) {
		$ada = true;
	}
	
	if (!$ada) {
	$res = db_query('select uraian from {rincianobyeksap} where kodero=:kodero', array(':kodero'=>$kodero));
	foreach ($res as $data) {
		$uraian = $data->uraian;
	}
		
		db_insert('jurnalitem')
		->fields(array('jurnalid', 'nomor', 'kodero', 'uraian', 'debet', 'kredit', 'keterangan', 'koderod'))
		->values(array(
				'jurnalid' => 1,
				'nomor' => 1,
				'kodero' => $kodero,
				'uraian' => $uraian,
				'debet' => 0,
				'kredit' => 0,
				'keterangan' => ' ',
				'koderod' => 1,
				))
		->execute();
		
	} 
	return null;
}	

?>
