<?php
function umum_newkeg_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$output_form = drupal_get_form('umum_newkeg_main_form');
	return drupal_render($output_form);// . $output;
	
}

function umum_newkeg_main_form($form, &$form_state) {

	$bendid = arg(2);
	
	//db_set_active('bendahara');
	
	$query = db_select('bendahara', 'b');
	$query->innerJoin('unitkerja', 'uk', 'b.kodeuk=uk.kodeuk');
	$query->innerJoin('kegiatanskpd', 'k', 'b.kodekeg=k.kodekeg');
	$query->fields('b', array('spjno', 'tanggal', 'bendid', 'jenis', 'jenispanjar', 'keperluan', 'total', 'kodeuk'));
	$query->fields('uk', array('namasingkat'));
	$query->fields('k', array('kodekeg', 'kegiatan'));
	$query->condition('b.bendid', $bendid, '=');
	
	dpq($query);	
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Jurnal Kas ' . $data->spjno . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$kodekeg = $data->kodekeg;
		$kegiatan = '<p>' . $data->kegiatan . '</p>';

		$keperluan = $data->keperluan;
		$skpd = '<p>' . $data->namasingkat . '</p>';
		$kodeuk = $data->kodeuk;

		$nobukti = $data->spjno;
		$tanggal = strtotime($data->tanggal);		
		$total = $data->total;
		
		$jenis = $data->jenis;
		
	}

	
	drupal_set_title($title);
	
	$form['refid'] = array(
		'#type' => 'value',
		'#value' => $bendid,
	);
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);

	$form['skpd'] = array(
		'#type' => 'item',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => $skpd,
	);
	$form['kegiatan'] = array(
		'#type' => 'item',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => $kegiatan,
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
		
		//rek	
		$query = db_select('bendaharaitem', 'bi');
		$query->join('rincianobyek', 'r', 'bi.kodero=r.kodero');
		$query->fields('r', array('kodero', 'uraian'));
		$query->fields('bi', array('jumlah'));
		$query->condition('bi.bendid', $bendid, '=');
		$results = $query->execute();
		foreach ($results as $data) {

			$i++;			
			
			if ($jenis=='pindahbuku') {
				if (substr($data->kodero, 0, 1)=='5') {
					if ($data->jumlah>0) {
						$debet = $data->jumlah;
						$kredit = 0;
					} else {
						$debet = 0;
						$kredit = abs($data->jumlah);
					}
									
				} else {
					
					if ($data->jumlah>0) {
						$kredit = $data->jumlah;
						$debet = 0;
					} else {
						$kredit = 0;
						$debet = $data->jumlah;
					}
				}
				
			} else {
				if (substr($data->kodero, 0, 1)=='5') {
					$debet = 0;
					$kredit = $data->jumlah;
									
				} else {
					$debet = $data->jumlah;
					$kredit = 0;	
				}
			}
			
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
				'#default_value'=> $debet, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);
			$form['formapbd']['table']['kredit' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> $kredit, 
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
	//db_set_active();
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);

	return $form;
}

function umum_newkeg_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$refid = $form_state['values']['refid'];
	$kodekeg = $form_state['values']['kodekeg'];
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keperluan = $form_state['values']['keperluan'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];

	$jumlahrek = $form_state['values']['jumlahrek'];
	//drupal_set_message($jumlahrek);
	
	//BEGIN TRANSACTION
	if (isUserSKPD()) {
		$jurnalid = apbd_getkodejurnal_uk($kodeuk);
		$suffix = 'uk';
	} else {
		$jurnalid = apbd_getkodejurnal($kodeuk);
		$suffix = '';
	}
	
	//$transaction = db_transaction();
	$totaldebet = 0;
	$totalkredit = 0;
	
	//JURNAL
	//try {

		//Delete Jurnal Item APBD
		db_delete('jurnalitem' . $suffix)
			->condition('jurnalid', $jurnalid)
			->execute();
	
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
			db_insert('jurnalitem' . $suffix)
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
		
		
		$query = db_insert('jurnal' . $suffix)
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $refid,
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
				'jurnalsudah'  . $suffix => 1,
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
