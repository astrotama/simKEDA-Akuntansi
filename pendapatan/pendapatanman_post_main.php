<?php
function pendapatanman_post_main($arg=NULL, $nama=NULL) {
	
	$output_form = drupal_get_form('pendapatanman_post_main_form');
	return drupal_render($output_form);// . $output;
	
}

function getTable($tahun,$transid){

}

function pendapatanman_post_main_form($form, &$form_state) {

	//$kodeuk = arg(2);
	//$kodero = arg(3);
	
	$title = 'Jurnal Pendapatan Baru';
	
	$transid = '';
	
	$keterangan = '';

	$tanggal= mktime(0,0,0,date('m'),date('d'),apbd_tahun());		
	$nobukti = '';
	$nobuktilain = '';
	$jumlah = '0';
	
	drupal_set_title($title);
	


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
		//'#default_value' => $kodeuk,
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
		'#default_value' => $nobuktilain,
	);


	//AJAX
	// Rekening dropdown list

	if (isset($form_state['values']['kodeuk'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$opt_rekening = _load_rekening($form_state['values']['kodeuk']);
	} else 
		$opt_rekening = _load_rekening('');
	
	
	$form['rekening'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => $opt_rekening, 		//_load_rekening($kodeuk),
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_rekdetil',
			'wrapper' => 'rekdetil-wrapper',
		),
	);

	// Wrapper for rekdetil dropdown list
	$form['wrapperdetil'] = array(
		'#prefix' => '<div id="rekdetil-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Detil -');
	if (isset($form_state['values']['rekening'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$options = _load_rekdetil($form_state['values']['rekening']);
	} 

	// Detil dropdown list
	$form['wrapperdetil']['rekdetil'] = array(
		'#title' => t('Detil'),
		'#type' => 'select',
		'#options' => $options,
	);

	//END AJAX

	$form['jumlah']= array(
		'#type' => 'textfield',
		'#title' => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#disabled' => true,
		'#default_value' => $jumlah,
	);

	$form['keterangan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keterangan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keterangan,
	);
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
}

function pendapatanman_post_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$jurnalid = apbd_getkodejurnal($kodeuk);

	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keterangan = $form_state['values']['keterangan'];
	$jumlah = $form_state['values']['jumlah'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
	
	$kodero = $form_state['values']['rekening'];
	$koderod = $form_state['values']['rekdetil'];
	if ($koderod=='0') $koderod = '';
	
	//BEGIN TRANSACTION

	$query = db_insert('jurnal')
			->fields(array('jurnalid', 'nourut', 'refid', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nourut' => '0',
					'refid' => '000',
					'kodeuk' => $kodeuk,
					'jenis' => 'pad',
					'nobukti' => $nobukti,
					'nobuktilain' => $nobuktilain,
					'tanggal' => $tanggalsql,
					'keterangan' => $keterangan, 
					'total' => $jumlah,
				)
			);
	//drupal_set_message($query);		
	$res = $query->execute();

	//JURNAL ITEM APBD
	//1
	$query = db_insert('jurnalitem')
			->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
			->values(
				array(
					'jurnalid' => $jurnalid,
					'nomor' => 1,
					'kodero' => apbd_getKodeROAPBD(),
					'debet' => $jumlah,
				)
			); 
	$res = $query->execute();
	//2. 
	$query = db_insert('jurnalitem')
			->fields(array('jurnalid', 'nomor', 'kodero', 'koderod', 'kredit'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => 2,
					'kodero' => $kodero,
					'koderod' => $koderod,
					'kredit' => $jumlah,
				)
			);
	$res = $query->execute();
	
	 
	//JURNAL ITEM LO
	$query = db_insert('jurnalitemlo')
			->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => 1,
					'kodero' => apbd_getKodeRORKPPKD(),
					'debet' => $jumlah,
				)
			);
	$res = $query->execute();
	
	//Rek LO
	$koderosap = '81101001';
	$sql = db_select('rekeningmapsap_apbd', 'rm');
	$sql->fields('rm',array('koderosap'));
	$sql->condition('koderoapbd', $kodero, '=');
	$res = $sql->execute();
	foreach ($res as $datamap) {
		$koderosap = $datamap->koderosap;
	}
	$query = db_insert('jurnalitemlo')
			->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => 2,
					'kodero' => $koderosap,
					'kredit' => $jumlah,
				)
			);
	$res = $query->execute();
	
	
	//JURNAL ITEM LRA
	$query = db_insert('jurnalitemlra')
			->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => '1',
					'kodero' => apbd_getKodeRORKPPKD(),
					'debet' => $jumlah,
				)
			);
	$res = $query->execute();
	//Rek LRA
	$koderosap = '41101001';
	$sql = db_select('rekeningmaplra_apbd', 'rm');
	$sql->fields('rm',array('koderolra'));
	$sql->condition('koderoapbd', $kodero, '=');
	$res = $sql->execute();
	foreach ($res as $datamap) {
		$koderosap = $datamap->koderolra;
	}
	$query = db_insert('jurnalitemlra')
			->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => '2',
					'kodero' => $koderosap,
					'kredit' => $jumlah,
				)
			);
	$res = $query->execute();	
	
	
	//if ($res) drupal_goto('pendapatanantrian');
	
	drupal_goto('pendapatanjurnal');
}


function _ajax_rekdetil($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperdetil'];
}

/**
 * Function for populating rekening
 */
function _load_rekening() {
	$rekening = array('- Pilih Rekening -');
 

	// Select table
	$query = db_select("rincianobyek", "r");
	$query->innerJoin("anggperuk", "a", "r.kodero=a.kodero");
	$query->innerJoin("unitkerja", "u", "a.kodeuk=u.kodeuk");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	//$query->condition("a.kodeuk", $kodeuk, '=');
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

/**
 * Function for populating rekdetil
 */
function _load_rekdetil($kodero) {
	$rekdetil = array('- Pilih Detil -');

	// Select table
	$query = db_select("rincianobyekdetil", "r");
	// Selected fields
	$query->fields("r", array('koderod', 'uraian'));
	// Filter the active ones only
	$query->condition("r.kodero", $kodero, "=");
	// Order by name
	$query->orderBy("r.koderod");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$rekdetil[$row->koderod] = $row->koderod . ' - ' . $row->uraian;
	}

	return $rekdetil;
}
 
?>
