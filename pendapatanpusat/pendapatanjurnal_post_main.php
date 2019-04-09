<?php
function pendapatanjurnal_post_main($arg=NULL, $nama=NULL) {
	
	$transid = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = getTable($tahun,$transid);
		print_pdf_p($output);
	
	} else {
	
		$btn = l('Cetak', 'pendapatan/edit/' . $transid . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('pendapatanjurnal_post_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function getTable($tahun,$transid){

}

function pendapatanjurnal_post_main_form($form, &$form_state) {
	$referer = $_SERVER['HTTP_REFERER'];
	
	$transid = arg(2);
	
	$transid2 = arg(3);
	$transid3 = arg(4);
	
	if (isset($transid2)) $transid .= '/' . $transid2;
	if (isset($transid3)) $transid .= '/' . $transid3;
	
	$query = db_select('apbdtrans', 'a');
	$query->innerJoin('apbdtransitem', 'ai', 'a.transid=ai.transid');
	$query->innerJoin('rincianobyek', 'r', 'ai.kodero=r.kodero');
	$query->fields('a', array('transid', 'keterangan', 'refno', 'kodeuk', 'tanggal', 'nobukti', 'total'));
	$query->fields('r', array('kodero', 'uraian'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('a.transid', $transid, '=');
	
	dpq($query);
	
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Nomor ' . $data->nobukti . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$kodero = $data->kodero;
		$rekening = $data->kodero . ', ' . $data->uraian;
		
		$keterangan = $data->keterangan;
		$kodeuk = $data->kodeuk;

		$refno= $data->refno;
		$tanggal= strtotime($data->tanggal);		
		$nobukti = $data->nobukti;
		$jumlah = $data->total;
	}
	
	drupal_set_title($title);
	

	$form['transid'] = array(
		'#type' => 'value',
		'#value' => $transid,
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

	//SKPD
	$query = db_select('unitkerja', 'p');
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
	
	$form['nobukti'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $refno,
	);
	$form['nobuktilain'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti Lain'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $nobukti,
	);
	$form['keterangan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keterangan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keterangan,
	);


	//AJAX
	// Rekening dropdown list
	$form['rekening'] = array(
		'#title' => t('Rekening'),
		'#type' => 'select',
		'#options' => _load_rekening($kodero),
		'#default_value' => $kodero,
		'#validated' => TRUE,
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
	} else
		$options = _load_rekdetil($kodero);

	// Detil dropdown list
	$form['wrapperdetil']['rekdetil'] = array(
		'#title' => t('Detil'),
		'#type' => 'select',
		'#options' => $options,
		'#default_value' => $koderod,
		'#validated' => TRUE,
	);
	$form['detiladd'] = array(
		'#type' => 'submit',
		'#value' =>'Detil',
		'#attributes' => array('class' => array('btn btn-info btn-sm pull-right')),
		//'#suffix' =>,
	);

	//END AJAX

	$form['jumlah']= array(
		'#type' => 'textfield',
		'#title' => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#disabled' => true,
		'#default_value' => $jumlah,
	);

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		//'#value' => 'Simpan',
		//'#attributes' => array('class' => array('btn btn-success')),
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		
	); 

	$form['formdata']['submitbelanja']= array(
		'#type' => 'submit',
		'#disabled' => isAuditor(),
		//'#attributes' => array('class' => array('btn btn-info')),
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan Belanja',
		'#attributes' => array('class' => array('btn btn-warning btn-sm')),
		'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
		
	);
	
	return $form;
}

function pendapatanjurnal_post_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$transid = $form_state['values']['transid'];
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keterangan = $form_state['values']['keterangan'];
	$jumlah = $form_state['values']['jumlah'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
	
	$kodero = $form_state['values']['rekening'];
	$koderod = $form_state['values']['rekdetil'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitbelanja']) {
		//BANK
		$query = db_update('apbdtrans')
		->fields(
				array(
					'jurnalsudah' => 0,
					'isbelanja' => 1,
				)
			);
		$query->condition('transid', $transid, '=');
		$res = $query->execute();
	
	} else {
		//BEGIN TRANSACTION
		$transaction = db_transaction();
		
		try {
			//JURNAL
			$jurnalid = apbd_getkodejurnal($kodeuk);
			//drupal_set_message($jurnalid);
			$query = db_insert('jurnal')
					->fields(array('jurnalid', 'refid', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
					->values(
						array(
							'jurnalid'=> $jurnalid,
							'refid' => $transid,
							'kodeuk' => $kodeuk,
							'jenis' => 'pad',
							'nobukti' => $nobukti,
							'nobuktilain' => $nobuktilain,
							'tanggal' =>$tanggalsql,
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
							'kodero' => apbd_getKodeROSAL(),
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
			
			
			
			//BANK
			$query = db_update('apbdtrans')
			->fields(
					array(
						'jurnalsudah' => 1,
						'jurnalid' => $jurnalid,
					)
				);
			$query->condition('transid', $transid, '=');
			$res = $query->execute();

		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('jurnal-pendapatan-' . $transid, $e);
		}		
		
	}
	//if ($res) drupal_goto('pendapatanantrian');
	drupal_goto('pendapatanantrian');
}


function _ajax_rekdetil($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperdetil'];
}

/**
 * Function for populating rekening
 */
function _load_rekening($kodero) {
	$rekening = array('- Pilih Rekening -');

	//$kode = '41';
	//$kode = substr($kodero, 0, 2);


	
	// Select table
	$query = db_select("rincianobyek", "r");
	// Selected fields
	$query->fields("r", array('kodero', 'uraian'));
	// Filter the active ones only
	//$query->condition("r.kodeo", db_like($kode) . '%', 'LIKE');
	 
	$or = db_or();
	$or->condition('r.kodero', db_like('4') . '%', 'LIKE');
	$or->condition('r.kodero', db_like('61') . '%', 'LIKE');
	$or->condition('r.kodero', db_like('9') . '%', 'LIKE');
	
	$query->condition($or);	
	
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
