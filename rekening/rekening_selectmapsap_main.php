<?php
function rekening_selectmapsap_main($arg=NULL, $nama=NULL) {
   
	$koderoapbd = arg(2);
	$_SESSION["kodero_apbd"] = $koderoapbd;
	
	$command = arg(3);
	$koderosap = arg(4);
	
	//drupal_set_message($command);
	//drupal_set_message($koderosap);
	if ($command=='update') {
		update_rekening_map($koderoapbd, $koderosap);
		drupal_goto('rekening/rincian/' . substr($koderoapbd, 0, 5) . '/view');
		
	} else {
	
		$output_form = drupal_get_form('rekening_selectmapsap_main_form');
		return drupal_render($output_form);
	}	
}

function rekening_selectmapsap_main_form ($form, &$form_state) {
	$koderoapbd = arg(2);
	
	$res = db_query('select kodero, uraian from {rincianobyek} where kodero=:kodero', array(':kodero'=>$koderoapbd));
	foreach ($res as $data) {
		drupal_set_title('Mapping SAP-LRA | ' . $data->kodero . ' - ' . $data->uraian);
	}
	
	$kodeo_sap = '';
	$kodej_sap = '';
	$_SESSION["kodero_sap"] = $kodeo_sap;
	$res = db_query('select koderosap from {rekeningmapsap_apbd} where koderoapbd=:koderoapbd', array(':koderoapbd'=>$koderoapbd));
	foreach ($res as $datamap) {
		$kodeo_sap = substr($datamap->koderosap, 0, 5);
		$kodej_sap = substr($datamap->koderosap, 0, 3);
		$_SESSION["kodero_sap"] = $datamap->koderosap;
	}
	
	
	//AJAX
	// Rekening dropdown list
	$form['kodej'] = array(
		'#title' => t('Jenis'),
		'#type' => 'select',
		'#options' => _load_jenis($koderoapbd),
		'#default_value' => $kodej_sap,
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
		$options = _load_obyek($kodej_sap);

	// Detil dropdown list
	$form['wrapperobyek']['kodeo'] = array(
		'#title' => t('Obyek'),
		'#type' => 'select',
		'#options' => $options,
		'#default_value' => $kodeo_sap,
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
	
	if (isset($form_state['values']['kodeo'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$rekenings = _load_rekening($form_state['values']['kodeo']);
	} else
		$rekenings = _load_rekening($kodeo_sap);

	// Detil dropdown list
	$form['wrapperrekening']['rekselector'] = array(
		'#markup' => $rekenings,
	);	
	//END AJAX
	
	/*
	$form['formlain'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Rekening SAP yang Dipilih Juga Berlaku untuk Rekening Dibawah ini',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
	$form['formlain']['tablerek']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="5px">No</th><th width="30px">Kode</th><th>Uraian</th><th width="5px"></th></tr>',
		 '#suffix' => '</table>',
	);
	
	$results = db_query("SELECT kodero, uraian from {rincianobyek} where kodeo=:kodeo and kodero<>:koderoapbd and kodero not in (select koderoapbd from rekeningmapsap_apbd) order by kodero", array(':koderoapbd'=>$koderoapbd, ':kodeo'=>substr($koderoapbd, 0, 5)));

	$i = 0;
	foreach ($results as $data) {

		$i++; 
		$form['formlain']['tablerek']['kodero' . $i]= array(
				'#type' => 'value',
				'#value' => $data->kodero,
		); 
		$form['formlain']['tablerek']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 3,
				'#suffix' => '</td>',
		); 
		$form['formlain']['tablerek']['kodero_v' . $i]= array(
				'#prefix' => '<td>',
				'#type' => 'item',
				'#markup' => $data->kodero,
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formlain']['tablerek']['uraian_v' . $i]= array(
				'#prefix' => '<td>',
				'#type' => 'item',
				'#markup' => $data->uraian,
				'#size' => 70,
				'#suffix' => '</td>',
		); 
		$form['formlain']['tablerek']['pilih' . $i]= array(
				'#prefix' => '<td>',
				'#type' => 'checkbox',
				'#default_value' => '0',
				'#suffix' => '</td></tr>',
		); 	
	}
	$form['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);	
	*/
	
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
function _load_jenis($koderoapbd) {
	$jenises = array('- Pilih Jenis -');

	if (substr($koderoapbd,0,1)=='4')
		$result = db_query("SELECT kodej, uraian FROM {jenissap} WHERE left(kodej,1)='8'");
	else if (substr($koderoapbd,0,1)=='5')
		$result = db_query("SELECT kodej, uraian FROM {jenissap} WHERE left(kodej,1) IN ('1', '6', '9')");
	else if (substr($koderoapbd,0,1)=='6')
		$result = db_query("SELECT kodej, uraian FROM {jenissap} WHERE left(kodej,1) IN ('1', '2', '6', '8', '9')");
	else
		$result = db_query("SELECT kodej, uraian FROM {jenissap} WHERE left(kodej,1) IN ('4', '5', '7')");
	
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

function _load_rekening($kodeo) {

$kodero_sap = $_SESSION["kodero_sap"];
$kodero_apbd = $_SESSION["kodero_apbd"];


if ($kodeo=='') {
	//$tabel_data = '<p>Pilih Jenis lalu pilih Obyek. Kemudian dari daftar rekening yang muncul, pilih Rekening yang sesuai.</p>';
	$tabel_data = '';
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
		
		if ($data->kodero==$kodero_sap) {
			$linkedit = l('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Pilih', 'rekening/rincian/'  . $kodeo , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-success btn-sm')));
		} else {
			$linkedit = l('<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Pilih', 'rekening/selectmapsap/' . $kodero_apbd . '/update/' . $data->kodero, array ('html' => true, 'attributes'=> array ('class'=>'btn btn-warning btn-sm')));
		}
		
		
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


function update_rekening_map($koderoapbd, $koderosap) {
	
	//drupal_set_message($koderoapbd);
	//drupal_set_message($koderosap);
	
	$ada = false;
	$res = db_query('select koderosap from {rekeningmapsap_apbd} where koderoapbd=:koderoapbd', array(':koderoapbd'=>$koderoapbd));
	foreach ($res as $datamap) {
		$ada = true;
	}
	
	if ($ada) {
		//drupal_set_message('Ada');
		
		db_update('rekeningmapsap_apbd')
		->fields(array(
				'koderosap' => $koderosap,
				))
		->condition('koderoapbd', $koderoapbd, '=')
		->execute();
		
	} else {
		//drupal_set_message('Tidak Ada');
		db_insert('rekeningmapsap_apbd')
		->fields(array(
				'koderoapbd' => $koderoapbd,
				'koderosap' => $koderosap,
				))
		->execute();		
	}	
	return null;
}	

?>
