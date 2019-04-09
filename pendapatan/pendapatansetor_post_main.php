<?php
function pendapatansetor_post_main($arg=NULL, $nama=NULL) {
	
	$setorid = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = getTable($tahun,$setorid);
		print_pdf_p($output);
	
	} else {
	
		$btn = l('Cetak', 'pendapatan/edit/' . $setorid . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('pendapatansetor_post_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function getTable($tahun,$setorid){

}

function pendapatansetor_post_main_form($form, &$form_state) {
	
	$setorid = arg(2);
	
	$query = db_select('setor', 'a');
	$query->innerJoin('rincianobyek', 'r', 'a.kodero=r.kodero');
	$query->fields('a', array('setorid', 'keterangan', 'koderod', 'kodeuk', 'tanggal', 'jumlahkeluar'));
	$query->fields('r', array('kodero', 'uraian'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('a.setorid', $setorid, '=');
	
	//dpq($query);
	
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Nomor ' . $data->setorid . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$rekening = $data->kodero . ', ' . $data->uraian;
		
		$keterangan = $data->keterangan;
		$kodeuk = $data->kodeuk;

		$refno= '';
		$tanggal= strtotime($data->tanggal);		
		$nobukti = '';
		$jumlah = $data->jumlahkeluar;
	}

	drupal_set_title($title);
	

	$form['setorid'] = array(
		'#type' => 'value',
		'#value' => $setorid,
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


	$form['rekening'] = array(
		'#type' => 'item',
		'#title' =>  t('Rekening'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $rekening . '</p>',
	);	

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

	return $form;
}

function pendapatansetor_post_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$setorid = $form_state['values']['setorid'];
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keterangan = $form_state['values']['keterangan'];
	$jumlah = $form_state['values']['jumlah'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
	

	//BEGIN TRANSACTION
	$transaction = db_transaction();
	
	try {
		//JURNAL
		$jurnalid = apbd_getkodejurnal($kodeuk);
		drupal_set_message($jurnalid);
		$query = db_insert('jurnal')
				->fields(array('jurnalid', 'refid', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $setorid,
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
						'kodero' => apbd_getKodeROAPBD(), //'11103001',
						'debet' => $jumlah,
					)
				); 
		$res = $query->execute();
		//2. 
		$query = db_insert('jurnalitem')
				->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => 2,
						'kodero' => '11103001',
						'kredit' => $jumlah,
					)
				);
		$res = $query->execute();
		
		 
		
		//BANK
		$query = db_update('setor')
		->fields(
				array(
					'jurnalsudah' => 1,
					'jurnalid' => $jurnalid,
				)
			);
		$query->condition('setorid', $setorid, '=');
		$res = $query->execute();

	}
		catch (Exception $e) {
		$transaction->rollback();
		watchdog_exception('jurnal-pendapatan-' . $setorid, $e);
	}		
	//if ($res) drupal_goto('pendapatanantrian');
	drupal_goto('pendapatansetor');
}


?>
