<?php
function umum_newpad_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('umum_newpad_main_form');
	return drupal_render($output_form);// . $output;
	
}

function umum_newpad_main_form($form, &$form_state) {
	
	$title = 'Jurnal Umum Restitusi';		
	$tanggal =  apbd_date_create_currdate_form();	
	
	//SKPD
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => apbd_getuseruk(),
	);

	$form['tanggal'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $tanggal,
		'#default_value'=> array(
			'year' => format_date($tanggal, 'custom', 'Y'),
			'month' => format_date($tanggal, 'custom', 'n'), 
			'day' => format_date($tanggal, 'custom', 'j'), 
		  ), 
		
	);
	$form['nobukti'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $nobukti,
	);
	$form['nobuktilain'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti Lain'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $refno,
	);
	
	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keterangan,
	);


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
	
	if (isset($form_state['values']['kodeo'])) $kodeo = $form_state['values']['kodeo'];
	// Wrapper for rekdetil dropdown list
	$form['wrapperrekening'] = array(
		'#prefix' => '<div id="rekening-wrapper">',
		'#suffix' => '</div>',
	);
	
	/*
	// Options for rekdetil dropdown list
	$rekenings = array('- Pilih Rekening -');
	if (isset($form_state['values']['kodeo'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$rekenings = _load_rekening($form_state['values']['kodeo']);
	} else
		$rekenings = _load_rekening($kodeo);

	// Detil dropdown list
	$form['wrapperrekening']['formapbd']['kodero'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => $rekenings,
		'#default_value' => '',
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_detil',
			'wrapper' => 'detil-wrapper',
		),		
	);			
	*/

	$form['wrapperrekening']['formapbd'] = array (
		'#type' => 'fieldset',
		'#title'=> 'JURNAL',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);		
	$i = 0;
	$form['wrapperrekening']['formapbd']['tablerek']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">DEBET</th><th width="130px">KREDIT</th></tr>',
			 '#suffix' => '</table>',
	);

		$i++;
		$form['wrapperrekening']['formapbd']['tablerek']['kodero' . $i]= array(
				'#type' => 'value',
				'#value' => apbd_getKodeROAPBD(),
		); 
		$form['wrapperrekening']['formapbd']['tablerek']['uraian' . $i]= array(
				'#type' => 'value',
				'#value' => 'Kas di Kas Daerah',
		); 
		
		$form['wrapperrekening']['formapbd']['tablerek']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['wrapperrekening']['formapbd']['tablerek']['koderoview' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => apbd_getKodeROAPBD(),
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['wrapperrekening']['formapbd']['tablerek']['uraianview' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> 'Kas di Kas Daerah', 
			'#suffix' => '</td>',
		); 
		$form['wrapperrekening']['formapbd']['tablerek']['debet' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> '0', 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['wrapperrekening']['formapbd']['tablerek']['kredit' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> '0', 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);		
	
		$results = db_query("SELECT kodero, uraian from {rincianobyek} where kodeo=:kodeo order by kodero", array(':kodeo'=>$kodeo));
		foreach ($results as $data) {

			$i++; 
			$form['wrapperrekening']['formapbd']['tablerek']['kodero' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 
			$form['wrapperrekening']['formapbd']['tablerek']['uraian' . $i]= array(
					'#type' => 'value',
					'#value' => $data->uraian,
			); 
			
			
			$form['wrapperrekening']['formapbd']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['wrapperrekening']['formapbd']['tablerek']['koderoview' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['wrapperrekening']['formapbd']['tablerek']['uraianview' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['wrapperrekening']['formapbd']['tablerek']['debet' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '0', 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);
			$form['wrapperrekening']['formapbd']['tablerek']['kredit' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '0', 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);
			
		}	

	$form['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);
		
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);

	return $form;
}

function _ajax_jenis($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperobyek'];
}


function _ajax_rekening($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperrekening']['formapbd'];
}

/**
 * Function for populating rekening
 */
function _load_jenis() {
	$jenises = array('- Pilih Jenis -');


	// Select 	// Select tablerek
	$query = db_select("jenis", "j");
	// Selected fields
	$query->fields("j", array('kodej', 'uraian'));
	
	$query->condition("j.kodek", db_like('4')  . '%', 'LIKE');	
	// Order by name
	$query->orderBy("j.kodej");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$jenises[$row->kodej] = $row->kodej . ' - ' . $row->uraian;
	}
	

	
	return $jenises;
}

function _load_obyek($kodej) {
	$obyeks = array('- Pilih Obyek -');


	// Select tablerek
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
		$obyeks[$row->kodeo] = $row->kodeo . ' - ' . $row->uraian;
	}

	return $obyeks;
}

function _load_rekening($kodeo) {
	$rekening = array('- Pilih Rekening -');
	//$rekening = array($kodeo);
	
	// Select tablerek
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

function umum_newpad_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$kodekeg = '000000';
	$transid = '000000';
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keperluan = $form_state['values']['keperluan'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];

	$jumlahrek = $form_state['values']['jumlahrek'];
	
	//BEGIN TRANSACTION
	$jurnalid = apbd_getkodejurnal_uk($kodeuk);
	
	$transaction = db_transaction();
	$totaldebet = 0;
	$totalkredit = 0;
	
	//drupal_set_message($jurnalid);
	//drupal_set_message('s  : ');
	//drupal_set_message('rek  : ' . $jumlahrek);
	
	//JURNAL
	try {
		
		//ITEM BELANJA
		for ($n=1; $n <= $jumlahrek; $n++){
			$kodero = $form_state['values']['kodero' . $n];
			$debet = $form_state['values']['debet' . $n];
			$kredit = $form_state['values']['kredit' . $n];
			
			$totaldebet += $debet;
			$totalkredit += $kredit;
			
			//drupal_set_message($kodero);
			
			//APBD
			if (($debet+$kredit)>0) {
				db_insert('jurnalitemuk')
					->fields(array('jurnalid', 'nomor', 'kodero', 'debet', 'kredit'))
					->values(array(
							'jurnalid'=> $jurnalid,
							'nomor'=> $n,
							'kodero' => $kodero,
							'debet' => $debet,
							'kredit'=> $kredit,
							))
					->execute();
			}	
		}

		$query = db_insert('jurnaluk' )
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $transid,
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'jenis' => 'umum-rt',
						'nobukti' => $nobukti,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						'keterangan' => $keperluan, 
						'total' => $totaldebet,
					)
				);
		//echo $query;		
		$res = $query->execute();
		
		
	}
		catch (Exception $e) {
		$transaction->rollback();
		watchdog_exception('jurnal-' . $kodekeg, $e);
	}
	
	//if ($res) drupal_goto('kaskeluarantrian');
	//drupal_goto('umum');
}


?>
