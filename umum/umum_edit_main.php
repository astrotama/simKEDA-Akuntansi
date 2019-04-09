<?php
function umum_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$jurnalid = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = getTable($tahun,$jurnalid);
		print_pdf_p($output);
	
	} else {
	
		$btn = l('Cetak', 'jurnalspjjurnal/jurnaledit/' . $jurnalid . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('umum_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function umum_edit_main_form($form, &$form_state) {

	if (isUserSKPD()) {
		$suffix = 'uk';
	} else {
		$suffix = '';
	} 
	
	$jurnalid = arg(2);
	//drupal_set_message($jurnalid);
	
	$query = db_select('jurnal' . $suffix , 'j');
	$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');
	$query->fields('u', array('kodeuk', 'namasingkat'));
	$query->fields('j', array('jurnalid', 'kodekeg', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'refid', 'jenisju'));	
	$query->fields('k', array('kegiatan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('j.jurnalid', $jurnalid, '=');
	
	//dpq($query);	
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Jurnal Umum No : ' . $data->nobukti . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$refid = $data->refid;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		$kegiatan = $data->kegiatan;
		if ($kegiatan=='') $kegiatan = 'Non Kegiatan';
		
		$keperluan = $data->keterangan;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		//$tanggal= strtotime($data->tanggal);

		$tanggal = dateapi_convert_timestamp_to_datetime($data->tanggal);
		
		$nobukti= $data->nobukti;
		$nobuktilain = $data->nobuktilain;
		
		$total = $data->total;
		
		$jenisju = $data->jenisju;
	}
	
	drupal_set_title($title);
	

	$form['skpd'] = array(
		'#type' => 'item',
		//'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $skpd . '</p>',
	);	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	$form['jurnalid'] = array(
		'#type' => 'value',
		'#value' => $jurnalid,
	);	
	$form['refid'] = array(
		'#type' => 'value',
		'#value' => $refid,
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
	
	/*$form['tanggal'] = array(
		'#type' => 'date',
		'#title' =>  t('Tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value'=> array(
			'year' => format_date($tanggal, 'custom', 'Y'),
			'month' => format_date($tanggal, 'custom', 'n'), 
			'day' => format_date($tanggal, 'custom', 'j'), 
		  ), 
		
	);*/
	
	$form['tanggaltitle'] = array(
	'#markup' => 'tanggal',
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
	
	$arr_ju['CP'] = 'CONTRA POST';
	$arr_ju['CM'] = 'CP MELEKAT';
	$arr_ju['PB'] = 'PEMINDAHBUKUAN';
	$arr_ju['BL'] = 'B L U D';
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
		'#default_value' => $keperluan,
	);


	$form['total']= array(
		'#type' => 'textfield',
		'#title' => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#disabled' => true,
		'#default_value' => $total,
	);

	//ITEM APBD
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

		
		//ITEM APBD
		$i = 0;
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->join('rincianobyek', 'r', 'ji.kodero=r.kodero');
		$query->fields('ji', array('kodero', 'debet', 'kredit', 'nomor'));
		$query->fields('r', array('uraian'));
		$query->condition('ji.jurnalid', $jurnalid, '=');
		//dpq($query);
		$results = $query->execute();
		foreach ($results as $data) {
			
			//APBD
			$form['formapbd']['table']['koderoapbd' . $data->nomor]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 
			$form['formapbd']['table']['uraianapbd' . $data->nomor]= array(
					'#type' => 'value',
					'#value' => $data->uraian,
			); 
			
			
			$form['formapbd']['table']['nomor' . $data->nomor]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $data->nomor,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['kodero' . $data->nomor]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['uraian' . $data->nomor]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['formapbd']['table']['debet' . $data->nomor]= array(
				'#type'         => 'textfield', 
				'#default_value'=> ''.$data->debet, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td>',
			);
			$form['formapbd']['table']['kredit' . $data->nomor]= array(
				'#type'         => 'textfield', 
				'#default_value'=> ''.$data->kredit, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);
			
			$i = $data->nomor;
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
	$form['formdata']['submithapus']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Hapus',
		'#attributes' => array('class' => array('btn btn-danger btn-sm')),
	);


	return $form;
}

function umum_edit_main_form_submit($form, &$form_state) {
	$refid = $form_state['values']['refid'];
	$jurnalid = $form_state['values']['jurnalid'];
	$kodekeg = $form_state['values']['kodekeg'];

	if (isUserSKPD()) {
		$suffix = 'uk';
	} else {
		$suffix = '';
	}
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submithapus']) {

		
		drupal_goto('umum/delete/'. $jurnalid);
		
		} else if($form_state['clicked_button']['#value'] == $form_state['values']['submithapusonly']) {
			//Delete Jurnal
			db_delete('jurnal' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();

			//Delete Jurnal Item APBD
			db_delete('jurnalitem' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LRA
			db_delete('jurnalitemlra' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LO
			db_delete('jurnalitemlo' . $suffix)
				->condition('jurnalid', $jurnalid)
				->execute();
	
	} else {
	
		$nobukti = $form_state['values']['nobukti'];
		$nobuktilain = $form_state['values']['nobuktilain'];
		$keperluan = $form_state['values']['keperluan'];
		
		//$tanggal = $form_state['values']['tanggal'];
		//$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
		
		$tanggalsql = dateapi_convert_timestamp_to_datetime($form_state['values']['tanggal']);

		$jumlahrek = $form_state['values']['jumlahrek'];
		$jenisju = $form_state['values']['jenisju'];
		
		//BEGIN TRANSACTION
		$transaction = db_transaction();
		$jumlahtotal = 0;
		
		//JURNAL
		try {
				
			//ITEM BELANJA
			for ($n=1; $n <= $jumlahrek; $n++){
				$kodero = $form_state['values']['koderoapbd' . $n];
				$debet = $form_state['values']['debet' . $n];
				$kredit = $form_state['values']['kredit' . $n];
				
				$jumlahtotal += $debet;
				
				//APBD
				$query = db_update('jurnalitem' . $suffix)
						->fields(
							array(
								'debet' => $debet,
								'kredit' => $kredit,
								'kodekeg' => $kodekeg,
							)
						);
				$query->condition('jurnalid', $jurnalid, '=');
				$query->condition('kodero', $kodero, '=');
				$query->condition('nomor', $n, '=');
				$res = $query->execute();
				
				
			}	//end item
			
			//jurnal
			$query = db_update('jurnal' . $suffix)
			->fields( 
					array(
						'keterangan' => $keperluan,
						'nobukti' => $nobukti,
						'jenisju' => $jenisju,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						'total' => $jumlahtotal,
					)
				);
			$query->condition('jurnalid', $jurnalid, '=');
			$res = $query->execute();
			
		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('jurnal-edit-' . $jurnalid, $e);
		}
	
	}
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('umum');
}


?>
