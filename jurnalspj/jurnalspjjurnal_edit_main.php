<?php
function jurnalspjjurnal_edit_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$jurnalid = arg(2);	
	
		$output_form = drupal_get_form('jurnalspjjurnal_edit_main_form');
		return drupal_render($output_form);// . $output;
	
}

function jurnalspjjurnal_edit_main_form($form, &$form_state) {
	
	$jurnalid = arg(2);
	
	$suffixjurnal = apbd_getsuffixjurnal();	
		
	$query = db_select('jurnal' . $suffixjurnal, 'j');
	$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');
	$query->fields('u', array('kodeuk', 'namasingkat'));
	$query->fields('j', array('jurnalid', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'refid', 'jenisdokumen'));	
	$query->fields('k', array('kodekeg', 'kegiatan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('j.jurnalid', $jurnalid, '=');
	
	//dpq($query);	
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'Jurnal Belanja No : ' . $data->nobukti . ', ' . apbd_format_tanggal_pendek($data->tanggal);
		
		$refid = $data->refid;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;


		$keperluan = $data->keterangan;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		//$tanggal= strtotime($data->tanggal);

		$tanggal = dateapi_convert_timestamp_to_datetime($data->tanggal);
		
		$nobukti= $data->nobukti;
		$nobuktilain = $data->nobuktilain;
		
		$total = $data->total;

		$kodekeg = $data->kodekeg;
		if ($data->jenisdokumen=='1')
			$kegiatan = 'Ganti Uang';
		elseif ($data->jenisdokumen=='5')
			$kegiatan = 'GU Nihil';
		elseif ($data->jenisdokumen=='7')
			$kegiatan = 'TU Nihil';
		else
			$kegiatan = $data->kegiatan;		
	}

	if ($kodekeg=='') $kodekeg = '000000';		
	if ($kegiatan=='') $kegiatan = 'Multi kegiatan GU';		
	
	drupal_set_title($title);
	

	$form['jurnalprint'] = array(
		'#type' => 'item',

		'#markup' => '<div class="row"><div class="col-md-8"><h3>' . $skpd . '</h3></div><div class="col-md-4"><p class="text-right">' . apbd_link_esp2d($refid) . "&nbsp;" . apbd_link_cetakjurnal($jurnalid) .  '</p></div></div>',
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
	//ITEM LRA-SAP
	$form['formlra'] = array (
		'#type' => 'fieldset',
		'#title'=> 'JURNAL SAP-LRA',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	//ITEM LRA-LO
	$form['formlo'] = array (
		'#type' => 'fieldset',
		'#title'=> 'JURNAL SAP-LO',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	
		$form['formapbd']['table']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="130px">JUMLAH</th></tr>',
			 '#suffix' => '</table>',
		);	
		$form['formlra']['table2']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="40%">KETERANGAN</th><th width="130px">JUMLAH</th></tr>',
			 '#suffix' => '</table>',
		);	
		$form['formlo']['table3']= array(
			'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">KODE</th><th>URAIAN</th><th width="40%">KETERANGAN</th><th width="130px">JUMLAH</th></tr>',
			 '#suffix' => '</table>',
		);	
		
		//ITEM APBD
		$query = db_select('jurnalitem' . $suffixjurnal, 'ji');
		$query->innerJoin('rincianobyek', 'r', 'ji.kodero=r.kodero');
		$query->innerJoin('kegiatanskpd', 'k', 'ji.kodekeg=k.kodekeg');
		$query->fields('ji', array('kodero', 'debet', 'nomor', 'kodekeg'));
		$query->fields('r', array('uraian'));
		$query->fields('k', array('kegiatan'));
		$query->condition('ji.jurnalid', $jurnalid, '=');
		$query->condition('ji.nomor', '0', '>');
		$query->orderBy('k.kegiatan', 'ASC');
		$query->orderBy('r.kodero', 'ASC');
		$results = $query->execute();
		
		$lastkeg='123'; $i = 0;
		foreach ($results as $data) {

			if ($lastkeg == $data->kodekeg) {
				$norek++;
				
			} else {
				$norek = 1;	
				$lastkeg = $data->kodekeg;

				//APBD
				$i++;
				$form['formapbd']['table']['kodekegrek' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 
				$form['formapbd']['table']['iskeg' . $i]= array(
						'#type' => 'value',
						'#value' => '1',
				); 
				$form['formapbd']['table']['koderoapbd' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 
				$form['formapbd']['table']['uraianapbd' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 
				$form['formapbd']['table']['nomor' . $i]= array(
						'#prefix' => '<tr><td>',
						'#markup' => '',
						'#suffix' => '</td>',
				); 
				$form['formapbd']['table']['kodero' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<strong>' . substr($data->kodekeg, -6). '</strong>',
						'#suffix' => '</td>',
				); 
				$form['formapbd']['table']['uraian' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<strong>' . $data->kegiatan . '</strong>', 
					'#suffix' => '</td>',
				); 
				$form['formapbd']['table']['jumlahapbd' . $i]= array(
					'#prefix' => '<td>',
					'#markup'=> '', 
					'#suffix' => '</td></tr>',
				);		
			
			}	
			
			$i++;
			
			//APBD
			$form['formapbd']['table']['kodekegrek' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodekeg,
			); 
			$form['formapbd']['table']['iskeg' . $i]= array(
					'#type' => 'value',
					'#value' => '0',
			);			
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
					'#markup' => $norek,
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
			$form['formapbd']['table']['jumlahapbd' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> ''.$data->debet, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);
			
		}
		
		//ITEM LRA
		$query = db_select('jurnalitemlra' . $suffixjurnal, 'ji');
		$query->innerJoin('rincianobyeksap', 'r', 'ji.kodero=r.kodero');
		$query->innerJoin('kegiatanskpd', 'k', 'ji.kodekeg=k.kodekeg');
		$query->fields('ji', array('kodero', 'debet', 'nomor', 'kodekeg', 'keterangan'));
		$query->fields('r', array('uraian'));
		$query->fields('k', array('kegiatan'));
		$query->condition('ji.jurnalid', $jurnalid, '=');
		$query->condition('ji.nomor', '0', '>');
		$query->orderBy('k.kegiatan', 'ASC');
		$query->orderBy('r.kodero', 'ASC');
		$results = $query->execute();
		
		$lastkeg='123'; 
		foreach ($results as $data) {

			if ($lastkeg == $data->kodekeg) {
				$norek++;
				
			} else {
				$norek = 1;	
				$lastkeg = $data->kodekeg;

				//LRA-SAP
				$i++;

				$form['formlra']['table2']['nomor_v' . $i]= array(
						'#prefix' => '<tr style="color:#006666"><td>',
						'#markup' => '',
						'#suffix' => '</td>',
				); 
				$form['formlra']['table2']['koderolra_v' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<strong>' . substr($data->kodekeg, -6). '</strong>',
						'#suffix' => '</td>',
					); 
				
				$form['formlra']['table2']['uraianlra_v' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '<strong>' . $data->kegiatan . '</strong>', 
					'#suffix' => '</td>',
				); 
				$form['formlra']['table2']['keteranganlra_v' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup'=> '', 
					'#suffix' => '</td>',
				); 
				$form['formlra']['table2']['jumlahlra' . $i]= array(
					'#prefix' => '<td>',
					'#markup'=> '', 
					'#suffix' => '</td></tr>',
				);	

		
			}	
			
			$i++;
			$form['formlra']['table2']['koderolra' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 

			$form['formlra']['table2']['nomor_v' . $i]= array(
					'#prefix' => '<tr style="color:#006666"><td>',
					'#markup' => $norek,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['koderolra_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
				); 
			
			$form['formlra']['table2']['uraianlra_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['keteranganlra_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->keterangan,  
				'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['jumlahlra' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '' . $data->debet, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);		
		}
		

		//ITEM LO
		$query = db_select('jurnalitemlo' . $suffixjurnal, 'ji');
		$query->innerJoin('rincianobyeksap', 'r', 'ji.kodero=r.kodero');
		$query->innerJoin('kegiatanskpd', 'k', 'ji.kodekeg=k.kodekeg');
		$query->fields('ji', array('kodero', 'debet', 'nomor', 'kodekeg', 'keterangan'));
		$query->fields('r', array('uraian'));
		$query->fields('k', array('kegiatan'));
		$query->condition('ji.jurnalid', $jurnalid, '=');
		$query->condition('ji.nomor', '0', '>');
		$query->orderBy('k.kegiatan', 'ASC');
		$query->orderBy('r.kodero', 'ASC');
		$results = $query->execute();
		$lastkeg='123';
		foreach ($results as $data) {

			if ($lastkeg == $data->kodekeg) {
				$norek++;
				
			} else {
				$norek = 1;	
				$lastkeg = $data->kodekeg;


				//LO
				$i++;
				$form['formlo']['table']['koderolo' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 

				$form['formlo']['table3']['nomor_v' . $i]= array(
						'#prefix' => '<tr style="color:#73264d"><td>',
						'#markup' => '',
						//'#size' => 10,
						'#suffix' => '</td>',
				); 
				$form['formlo']['table3']['koderolo_v' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<strong>' . substr($data->kodekeg, -6). '</strong>',
						'#suffix' => '</td>',
					); 
				$form['formlo']['table3']['uraianlo_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup'=> '<strong>' . $data->kegiatan . '</strong>', 
					'#suffix' => '</td>',
				); 
				$form['formlo']['table3']['keteranganlo_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup'=> '', 
					'#suffix' => '</td>',
				); 			
				$form['formlo']['table3']['jumlahlo' . $i]= array(
					'#prefix' => '<td>',
					'#markup'=> '', 
					'#suffix' => '</td></tr>',
				);				
			}	
			
			$i++;
			$form['formlo']['table']['koderolo' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 


			$form['formlo']['table3']['nomor_v' . $i]= array(
					'#prefix' => '<tr style="color:#73264d"><td>',
					'#markup' => $data->nomor,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formlo']['table3']['koderolo_v' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#size' => 10,
					'#suffix' => '</td>',
				); 
			$form['formlo']['table3']['uraianlo_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['formlo']['table3']['keteranganlo_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->keterangan, 
				'#suffix' => '</td>',
			); 			
			$form['formlo']['table3']['jumlahlo' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '' . $data->debet, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);
			
		}
		
		
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		//'#disabled' => $disable_save,
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['formdata']['submithapus']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Hapus',
		'#attributes' => array('class' => array('btn btn-danger btn-sm')),
	);
	if (isAdministrator()) {
		$form['formdata']['submithapusonly']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Hapus Tanpa Reset Antrian',
			'#attributes' => array('class' => array('btn btn-danger btn-sm')),
		);
	}

	return $form;
}

function jurnalspjjurnal_edit_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$refid = $form_state['values']['refid'];
	$jurnalid = $form_state['values']['jurnalid'];
	$kodekeg = $form_state['values']['kodekeg'];

	$suffixjurnal = apbd_getsuffixjurnal();;
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submithapus']) {
		drupal_goto('jurnalspjjurnal/delete/'. $jurnalid);

		
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submithapusonly']) {
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
				
	} else {
	
		$nobukti = $form_state['values']['nobukti'];
		$nobuktilain = $form_state['values']['nobuktilain'];
		$keperluan = $form_state['values']['keperluan'];
		
		//$tanggal = $form_state['values']['tanggal'];
		//$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
		
		$tanggalsql = dateapi_convert_timestamp_to_datetime($form_state['values']['tanggal']);

		
		//BEGIN TRANSACTION
		$transaction = db_transaction();
		
		//JURNAL
		try {
				
			//jurnal
			$query = db_update('jurnal' . $suffixjurnal)
			->fields( 
					array(
						'keterangan' => $keperluan,
						'nobukti' => $nobukti,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						//'total' => $jumlahtotal,
						'kodekeg' => $kodekeg,
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
	drupal_goto('jurnalspjjurnal');
}


?>