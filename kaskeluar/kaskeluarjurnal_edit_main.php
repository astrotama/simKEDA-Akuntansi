<?php
function kaskeluarjurnal_edit_main($arg=NULL, $nama=NULL) {
	
	$jurnalid = arg(2);	

	$output_form = drupal_get_form('kaskeluarjurnal_edit_main_form');
		return drupal_render($output_form);
	
}

function getTable($tahun,$dokid){
	
	
}

function kaskeluarjurnal_edit_main_form($form, &$form_state) {
	
	$jurnalid = arg(2);
	
	$suffixjurnal = apbd_getsuffixjurnal();
	
	$query = db_select('jurnal' . $suffixjurnal, 'j');
	$query->join('unitkerja', 'u', 'j.kodeuk=u.kodeuk');

	$query->fields('u', array('kodeuk', 'namasingkat'));
	$query->fields('j', array('jurnalid', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'refid'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('j.jurnalid', $jurnalid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SP2D No : ' . $data->nobukti . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$dokid = $data->refid;
		
		$keperluan = $data->keterangan;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		//$tanggal= strtotime($data->tanggal);	

		$tanggal = dateapi_convert_timestamp_to_datetime($data->tanggal);
		
		$nobukti= $data->nobukti;
		$nobuktilain = $data->nobuktilain;

		//$sppspm = 'SPP: ' . $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl) . '; SPM: ' . $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
		$total = $data->total;
	}
	
	drupal_set_title($title);
	

	
	$form['jurnalprint'] = array(
		'#type' => 'item',

		'#markup' => '<div class="row"><div class="col-md-4"><h3>' . $skpd . '</h3></div><div class="col-md-8"><p class="text-right">' . apbd_link_cetakjurnal($jurnalid) .  '</p></div></div>',
	);	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	$form['jurnalid'] = array(
		'#type' => 'value',
		'#value' => $jurnalid,
	);	
	/*
	$form['sppspm'] = array(
		'#type' => 'item',
		'#title' =>  t('SPP dan SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $sppspm . '</p>',
	);	
	*/
	
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

function kaskeluarjurnal_edit_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$dokid = $form_state['values']['dokid'];
	$jurnalid = $form_state['values']['jurnalid']; 
	
	$suffixjurnal = apbd_getsuffixjurnal();

	if($form_state['clicked_button']['#value'] == $form_state['values']['submithapus']) {

		//BEGIN TRANSACTION
		//$transaction = db_transaction();
		
		//try {
			
			//Delete Jurnal
			db_delete('jurnal' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();

			//Delete Jurnal Item APBD
			db_delete('jurnalitem' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LRA
			db_delete('jurnalitemlra' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();
			//Delete Jurnal Item LO
			db_delete('jurnalitemlo' . $suffixjurnal)
				->condition('jurnalid', $jurnalid)
				->execute();
				
			//Reset Dokumen
			db_set_active('penatausahaan');
			$query = db_update('dokumen')
			->fields(
					array(
						'jurnalkassudah' . $suffixjurnal => 0,
						'jurnalidkas' . $suffixjurnal => '',
					)
				);
			$query->condition('dokid', $dokid, '=');
			$res = $query->execute();
			db_set_active();
			
				
		//}
		//	catch (Exception $e) {
		//	$transaction->rollback();
		//	watchdog_exception('jurnal-delete-' . $jurnalid, $e);
		//}	 	
		
	
	} else {		//SIMPAN

		//BEGIN TRANSACTION
		$transaction = db_transaction();
		
		try {

			$nobukti = $form_state['values']['nobukti'];
			$nobuktilain = $form_state['values']['nobuktilain'];
			$keperluan = $form_state['values']['keperluan'];
			$total = $form_state['values']['total'];
			
			//$tanggal = $form_state['values']['tanggal'];
			//$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
			
			$tanggalsql = dateapi_convert_timestamp_to_datetime($form_state['values']['tanggal']);

			//JURNAL
			$query = db_update('jurnal' . $suffixjurnal)
			->fields( 
					array(
						'keterangan' => $keperluan,
						'nobukti' => $nobukti,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						'total' => $total,
					)
				);
			$query->condition('jurnalid', $jurnalid, '=');
			$res = $query->execute();
			

			//JURNAL ITEM APBD
			//1
			$query = db_update('jurnalitem' . $suffixjurnal)
					->fields(
						array(
							'kredit' => $total,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('kodero', apbd_getKodeROAPBD(), '=');
			$res = $query->execute();
			//2. 
			$query = db_update('jurnalitem' . $suffixjurnal)
					->fields(
						array(
							'debet' => $total,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('kodero', apbd_getKodeROBendaharaPengeluaran(), '=');
			$res = $query->execute();
			
			//JURNAL ITEM LO
			$query = db_update('jurnalitemlo' . $suffixjurnal)
					->fields(
						array(
							'kredit' => $total,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('kodero', apbd_getKodeRORKPPKD(), '=');
			$res = $query->execute();

			$query = db_update('jurnalitemlo' . $suffixjurnal)
					->fields(
						array(
							'debet' => $total,
						)
					);
			$query->condition('jurnalid', $jurnalid, '=');
			$query->condition('kodero', apbd_getKodeROBendaharaPengeluaran(), '=');
			$res = $query->execute();
			
		}
			catch (Exception $e) {
			$transaction->rollback();
			watchdog_exception('jurnal-edit-' . $jurnalid, $e);
		}		
			
	}	


	
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('kaskeluarjurnal');
}


?>