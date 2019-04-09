<?php
function umum_newkas_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('umum_newkas_main_form');
	return drupal_render($output_form);// . $output;
	
}

function umum_newkas_main_form($form, &$form_state) {

	$bendid = arg(2);
	
	db_set_active('bendahara');
	
	$query = db_select('bendahara', 'b');
	$query->innerJoin('unitkerja', 'uk', 'b.kodeuk=uk.kodeuk');
	$query->fields('b', array('spjno', 'tanggal', 'bendid', 'jenis', 'keperluan', 'total', 'kodeuk'));
	$query->fields('uk', array('namasingkat'));
	$query->condition('b.bendid', $bendid, '=');
	
	//dpq($query);	
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Jurnal Kas ' . $data->spjno . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$keperluan = $data->keperluan;
		$skpd = '<p>' . $data->namasingkat . '</p>';
		$kodeuk = $data->kodeuk;

		$nobukti = $data->spjno;
		$tanggal = strtotime($data->tanggal);		
		$total = $data->total;
		
	}
	
	
	db_set_active();
	
	drupal_set_title($title);
	
	$form['refid'] = array(
		'#type' => 'value',
		'#value' => $bendid,
	);
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);

	$form['skpd'] = array(
		'#type' => 'item',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => $skpd,
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
		'#title' =>  t('No. Bukti'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $nobukti,
	);
	$form['nobuktilain'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No. Bukti Lain'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
	);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);


	//ITEM APBD
	$i = 0;
	$form['formapbd'] = array (
		'#type' => 'fieldset',
		'#title'=> 'JURNAL APBD',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);	
	
		$form['formapbd']['table']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">DEBET</th><th width="130px">KREDIT</th></tr>',
			 '#suffix' => '</table>',
		);	
		
		//KAS DA
		$i++;
		$form['formapbd']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => '11101001',
		); 
		$form['formapbd']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => 'Kas di Kas Daerah',
		); 
		
		$form['formapbd']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '11101001',
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> 'Kas di Kas Daerah', 
			'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['debet' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $total, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['formapbd']['table']['kredit' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> '0', 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);		

		//KAS SKPD
		$i++;
		$form['formapbd']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => '11102001',
		); 
		$form['formapbd']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => 'Kas di Bendahara Pengeluaran',
		); 
		
		$form['formapbd']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i+1,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '11102001',
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> 'Kas di Bendahara Pengeluaran', 
			'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['debet' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> '0', 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['formapbd']['table']['kredit' . $i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> $total, 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);			
		
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

function umum_newkas_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$refid = $form_state['values']['refid'];
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keperluan = $form_state['values']['keperluan'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];

	$jumlahrek = $form_state['values']['jumlahrek'];
	drupal_set_message($jumlahrek);
	
	//BEGIN TRANSACTION
	$jurnalid = apbd_getkodejurnal($kodeuk);
	//drupal_set_message('UK : ' . $kodeuk);
	//drupal_set_message('JurnalID : ' . $jurnalid);
	
	//$transaction = db_transaction();
	$totaldebet = 0;
	$totalkredit = 0;
	
	//JURNAL
	//try {
		
		//ITEM BELANJA
		for ($n=1; $n <= $jumlahrek; $n++){
			$kodero = $form_state['values']['koderoapbd' . $n];
			$debet = $form_state['values']['debet' . $n];
			$kredit = $form_state['values']['kredit' . $n];
			
			//drupal_set_message($kodero);
			//drupal_set_message($debet);
			//drupal_set_message($kredit);
			
			$totaldebet += $debet;
			$totalkredit += $kredit;
			
			
			//APBD
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
		
		
		$query = db_insert('jurnaluk')
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $refid,
						'kodekeg' => '000000',
						'kodeuk' => $kodeuk,
						'jenis' => 'umum-kas',
						'nobukti' => $nobukti,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						'keterangan' => $keperluan, 
						'total' => $totaldebet,
					)
				);
		//echo $query;		
		$res = $query->execute();
		
	/*
	}
		catch (Exception $e) {
		$transaction->rollback();
		watchdog_exception('jurnal-' . $refid, $e);
		db_set_active();
	}
	*/

	//Bendahara
	
	db_set_active('bendahara');
	$query = db_update('bendahara')
	->fields(
			array(
				'jurnalsudah' => 1,
				'jurnalid' => $jurnalid,
			)
		);
	$query->condition('bendid', $refid, '=');
	$res = $query->execute();
	db_set_active();
	
	
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('umum/antrian');
	
}


?>
