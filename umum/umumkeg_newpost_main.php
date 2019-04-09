<?php
function umumkeg_newpost_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$kodekeg = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = getTable($tahun,$kodekeg);
		print_pdf_p($output);
	
	} else {
	
		$btn = l('Cetak', 'jurnalspjantrian/jurnal/' . $kodekeg . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('umumkeg_newpost_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function umumkeg_newpost_main_form($form, &$form_state) {
	
	$kodekeg = arg(2);
	
	$query = db_select('kegiatanskpd', 'k');
	$query->join('unitkerja', 'u', 'k.kodeuk=u.kodeuk');
	$query->fields('k', array('kodekeg', 'kodekeg', 'kegiatan'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('k.kodekeg', $kodekeg, '=');
	
	dpq($query);	
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Jurnal Umum Kegiatan';
		
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		$kegiatan = $data->kegiatan;
		
	}
	$tanggal =  apbd_date_create_currdate_form();	
	
	drupal_set_title($title);
	

	$form['skpd'] = array(
		'#type' => 'item',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $skpd . '</p>',
	);	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);
	
	$form['kegiatan'] = array(
		'#type' => 'item',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $kegiatan . '</p>',
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

	$arr_ju['CP'] = 'CONTRA POST';
	$arr_ju['CM'] = 'CP MELEKAT';
	$arr_ju['PB'] = 'PEMINDAHBUKUAN';
	$arr_ju['BL'] = 'B L U D';
	$arr_ju['BS'] = 'B O S';
	$form['jenisju'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis'),
		'#options' =>  $arr_ju,
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => 'CP',
	);
	
	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => '',
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
				'#value' => apbd_getKodeROAPBD(),
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
				'#markup' => apbd_getKodeROAPBD(),
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
			'#default_value'=> '0', 
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
				'#value' => apbd_getKodeROBendaharaPengeluaran(),
		); 
		$form['formapbd']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => 'Kas di Bendahara Pengeluaran',
		); 
		
		$form['formapbd']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => apbd_getKodeROBendaharaPengeluaran(),
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
			'#default_value'=> '0', 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);			
		
		//KAS BLUD
		$i++;
		$form['formapbd']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => apbd_getKodeROBendaharaPengeluaranBLUD(),
		); 
		$form['formapbd']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => 'Kas di Bendahara Pengeluaran BLUD',
		); 
		
		$form['formapbd']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => apbd_getKodeROBendaharaPengeluaranBLUD(),
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> 'Kas di Bendahara Pengeluaran BLUD', 
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
			'#default_value'=> '0', 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);		

		//KAS BOS
		$i++;
		$form['formapbd']['table']['koderoapbd' . $i]= array(
				'#type' => 'value',
				'#value' => apbd_getKodeROBendaharaPengeluaranBOS(),
		); 
		$form['formapbd']['table']['uraianapbd' . $i]= array(
				'#type' => 'value',
				'#value' => 'Kas di Bendahara Pengeluaran BOS',
		); 
		
		$form['formapbd']['table']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['kodero' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => apbd_getKodeROBendaharaPengeluaranBOS(),
				'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['formapbd']['table']['uraian' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> 'Kas di Bendahara Pengeluaran BOS', 
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
			'#default_value'=> '0', 
			'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
			'#size' => 25,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);			
		$query = db_select('anggperkeg', 'a');
		$query->join('rincianobyek', 'r', 'a.kodero=r.kodero');
		$query->fields('r', array('kodero', 'uraian'));
		$query->condition('a.kodekeg', $kodekeg, '=');
		//$query->condition('a.anggaran', 0, '>');
		$results = $query->execute();
		foreach ($results as $data) {

			$i++;
			
			$form['formapbd']['table']['koderoapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 
			$form['formapbd']['table']['uraianapbd' . $i]= array(
					'#type' => 'value',
					'#value' => $data->uraian,
			); 
			
			
			$form['formapbd']['table']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['kodero' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['uraian' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
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

function umumkeg_newpost_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$kodekeg = $form_state['values']['kodekeg'];
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keperluan = $form_state['values']['keperluan'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];

	$jumlahrek = $form_state['values']['jumlahrek'];
	
	//BEGIN TRANSACTION
	if (isUserSKPD()) {
		$jurnalid = apbd_getkodejurnal_uk($kodeuk);
		$suffix = 'uk';
	} else {
		$jurnalid = apbd_getkodejurnal($kodeuk);
		$suffix = '';
	}
	
	$transaction = db_transaction();
	$totaldebet = 0;
	$totalkredit = 0;
	
	//JURNAL
	try {

		//Delete Jurnal Item APBD
		db_delete('jurnalitem' . $suffix)
			->condition('jurnalid', $jurnalid)
			->execute();
	
		//ITEM BELANJA
		for ($n=1; $n <= $jumlahrek; $n++){
			$kodero = $form_state['values']['koderoapbd' . $n];
			$debet = $form_state['values']['debet' . $n];
			$kredit = $form_state['values']['kredit' . $n];
			
			$totaldebet += $debet;
			$totalkredit += $kredit;
			
			//APBD
			db_insert('jurnalitem' . $suffix )
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet', 'kredit', 'kodekeg'))
				->values(array(
						'jurnalid'=> $jurnalid,
						'nomor'=> $n,
						'kodero' => $kodero,
						'debet' => $debet,
						'kredit'=> $kredit,
						'kodekeg' => $kodekeg,
						))
				->execute();
			
		}

		$query = db_insert('jurnal' . $suffix )
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => '000000',
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'jenis' => 'umum-spj',
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
		db_set_active();
	}
	
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('umum');
}


?>
