<?php
function jurnalspjjurnal_post_main($arg=NULL, $nama=NULL) {
	//drupal_add_css('files/css/textfield.css');
	
	$dokid = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = getTable($tahun,$dokid);
		print_pdf_p($output);
	
	} else {
	
		$btn = l('Cetak', 'jurnalspjantrian/jurnal/' . $dokid . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('jurnalspjjurnal_post_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function jurnalspjjurnal_post_main_form($form, &$form_state) {
	
	$dokid = arg(2);
	
	db_set_active('penatausahaan');
	
	$query = db_select('dokumen', 'd');
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$query->fields('d', array('dokid','keperluan', 'kodekeg', 'sp2dno', 'sp2dtgl', 'sppno', 'spptgl', 'spmno', 'spmtgl', 'jumlah', 'jenisdokumen'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	$query->fields('k', array('kegiatan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	//dpq($query);	
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SP2D No : ' . $data->sp2dno . ', ' . apbd_format_tanggal_pendek($data->sp2dtgl);
		
		$keperluan = $data->keperluan;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		$kegiatan = $data->kegiatan;
		
		$sp2dno= $data->sp2dno;
		$sp2dtgl= strtotime($data->sp2dtgl);		
		$spmno = $data->spmno;
		$sppspm = 'SPP: ' . $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl) . '; SPM: ' . $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
		$jumlah = $data->jumlah;
		
		$jenisdokumen = $data->jenisdokumen;
	}
	
	if ($kodekeg=='') $kodekeg = '000000';		
	if ($kegiatan=='') $kegiatan = 'Multi kegiatan GU';		
	
	drupal_set_title($title);
	

	$form['skpd'] = array(
		'#type' => 'item',
		//'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#markup' => '<p>' . $skpd . '</p>',
		'#markup' => '<div class="row"><div class="col-md-4"><h3>' . $skpd . '</h3></div><div class="col-md-8"><p class="text-right">' . apbd_link_esp2d($dokid) . '</p></div></div>',
	);	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	$form['jenisdokumen'] = array(
		'#type' => 'value',
		'#value' => $jenisdokumen,
	);	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);
	$form['nobuktilain'] = array(
		'#type' => 'value',
		'#value' => $spmno,
	);
	
	$form['sppspm'] = array(
		'#type' => 'item',
		'#title' =>  t('SPP dan SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $sppspm . '</p>',
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
		'#default_value' => $sp2dtgl,
		'#default_value'=> array(
			'year' => format_date($sp2dtgl, 'custom', 'Y'),
			'month' => format_date($sp2dtgl, 'custom', 'n'), 
			'day' => format_date($sp2dtgl, 'custom', 'j'), 
		  ), 
		
	);
	$form['nobukti'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $sp2dno,
	);

	$form['keperluan'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $keperluan,
	);


	$form['jumlah']= array(
		'#type' => 'textfield',
		'#title' => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#disabled' => true,
		'#default_value' => $jumlah,
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
		
		$results = db_query('select k.kodekeg,k.kegiatan,r.kodero, r.uraian, i.jumlah, i.kodekeg from dokumenrekening i inner join rincianobyek r on i.kodero=r.kodero inner join kegiatanskpd k on i.kodekeg=k.kodekeg where i.jumlah>0 and i.dokid=:dokid order by k.kegiatan, r.kodero', array(':dokid'=>$dokid));
		$i = 0;
		
		$disable_save = false; $lastkeg='123'; $nokeg = 0;
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

				//LRA-SAP
				$i++;
				$form['formlra']['table2']['koderolra' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 
				$form['formlra']['table2']['uraianlra' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 
				$form['formlra']['table2']['keteranganlra' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 

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

				//LO
				$i++;
				$form['formlo']['table']['koderolo' . $i]= array(
						'#type' => 'value',
						'#value' => '',
				); 
				$form['formlo']['table']['uraianlo' .  $i]= array(
						'#type' => 'value',
						'#value' => '',
				);
				$form['formlo']['table']['keteranganlo' .  $i]= array(
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
			$disable_save = $disable_save || ($data->kodekeg=='');
			
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
					//'#description' => $data->kodekeg,
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
				'#default_value'=> ''.$data->jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);
			
			//REK SAP-LRA			
			$koderosap = '51206001';
			$uraiansap = 'Belanja cetak (*)';
			
			//$sql = db_select('rekeningmaplra_apbd', 'rm');
			//$sql->join('rincianobyeksap', 'r', 'rm.koderolra=r.kodero');
			//$sql->fields('r',array('uraian', 'kodero'));
			//$sql->condition('rm.koderoapbd', $data->kodero, '=');
			
			$res = db_query('select r.kodero, r.uraian from rincianobyeksap r inner join rekeningmaplra_apbd rm on r.kodero=rm.koderolra where rm.koderoapbd=:koderoapbd', array(':koderoapbd'=>$data->kodero));
			foreach ($res as $datamap) {

				$koderosap = $datamap->kodero;
				$uraiansap = $datamap->uraian;
			}
			 

			
			$form['formlra']['table2']['koderolra' . $i]= array(
					'#type' => 'value',
					'#value' => $koderosap,
			); 
			$form['formlra']['table2']['uraianlra' . $i]= array(
					'#type' => 'value',
					'#value' => $uraiansap,
			); 
			$form['formlra']['table2']['keteranganlra' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero . ' | ' . $data->uraian,
			); 

			$form['formlra']['table2']['nomor_v' . $i]= array(
					'#prefix' => '<tr style="color:#006666"><td>',
					'#markup' => $norek,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['koderolra_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $koderosap,
					'#size' => 10,
					'#suffix' => '</td>',
				); 
			
			$form['formlra']['table2']['uraianlra_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraiansap, 
				'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['keteranganlra_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->kodero . ' | ' . $data->uraian, 
				'#suffix' => '</td>',
			); 
			$form['formlra']['table2']['jumlahlra' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '' . $data->jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);			
			
			//REK SAP-LO
			$koderosap = '51206001';
			$uraiansap = 'Belanja cetak';
			$sql = db_select('rekeningmapsap_apbd', 'rm');
			$sql->join('rincianobyeksap', 'r', 'rm.koderosap=r.kodero');
			$sql->fields('r',array('uraian', 'kodero'));
			$sql->condition('koderoapbd', $data->kodero, '=');
			$res = $sql->execute();
			foreach ($res as $datamap) {
				$koderosap = $datamap->kodero;
				$uraiansap = $datamap->uraian;
			}

			
			$form['formlo']['table']['koderolo' . $i]= array(
					'#type' => 'value',
					'#value' => $koderosap,
			); 
			$form['formlo']['table']['uraianlo' .  $i]= array(
					'#type' => 'value',
					'#value' => $uraiansap,
			);
			$form['formlo']['table']['keteranganlo' .  $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero . ' | ' . $data->uraian,
			);

			$form['formlo']['table3']['nomor_v' . $i]= array(
					'#prefix' => '<tr style="color:#73264d"><td>',
					'#markup' => $norek,
					//'#size' => 10,
					'#suffix' => '</td>',
			); 
			$form['formlo']['table3']['koderolo_v' . $i]= array(
					//'#type'         => 'textfield', 
					'#prefix' => '<td>',
					'#markup' => $koderosap,
					'#size' => 10,
					'#suffix' => '</td>',
				); 
			$form['formlo']['table3']['uraianlo_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $uraiansap, 
				'#suffix' => '</td>',
			); 
			$form['formlo']['table3']['keteranganlo_v' . $i]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<td>',
				'#markup'=> $data->kodero . ' | ' . $data->uraian, 
				'#suffix' => '</td>',
			); 			
			$form['formlo']['table3']['jumlahlo' . $i]= array(
				'#type'         => 'textfield', 
				'#default_value'=> '' . $data->jumlah, 
				'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
				'#size' => 25,
				'#prefix' => '<td>',
				'#suffix' => '</td></tr>',
			);
			
		}
	
	db_set_active();
	$form['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#disabled' => $disable_save,
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	
	return $form;
}

function jurnalspjjurnal_post_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$dokid = $form_state['values']['dokid'];
	
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$keperluan = $form_state['values']['keperluan'];
	$jumlahtotal = $form_state['values']['jumlah'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];

	$jumlahrek = $form_state['values']['jumlahrek'];

	$suffixjurnal = apbd_getsuffixjurnal();
	$jurnalid = apbd_getkodejurnal($kodeuk);
	
	//drupal_set_message($jurnalid);
	
	//BEGIN TRANSACTION
	
	
	//$transaction = db_transaction();
	
	//JURNAL
	//try {
		
		$query = db_insert('jurnal' . $suffixjurnal)
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'jenisdokumen'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $dokid,
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'jenis' => 'spj',
						'nobukti' => $nobukti,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						'keterangan' => $keperluan, 
						'total' => $jumlahtotal,
						'jenisdokumen' => $jenisdokumen,
					)
				);
		//echo $query;		
		$res = $query->execute();
		
		//ITEM KAS
		//APBD
		
		if (($jenisdokumen == '3') or ($jenisdokumen == '4')) {			//GAJI & LS
			$rekkasapbd = apbd_getKodeROAPBD();
			$rekkassal = apbd_getKodeROSAL();
			$rekkaslo = apbd_getKodeRORKPPKD();

		} else {
			$rekkasapbd = apbd_getKodeROKasBendaharaPengeluaran();
			$rekkassal = $rekkasapbd;
			$rekkaslo = $rekkasapbd;
		}
			
		db_insert('jurnalitem' . $suffixjurnal)
			->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
			->values(array(
					'jurnalid'=> $jurnalid,
					'nomor'=> 0,
					'kodero' => $rekkasapbd,
					'kredit' => $jumlahtotal,
					))
			->execute();
		//LRA
		db_insert('jurnalitemlra' . $suffixjurnal)
			->fields(array('jurnalid', 'nomor', 'kodero', 'keterangan', 'kredit'))
			->values(array(
					'jurnalid'=> $jurnalid,
					'nomor'=> 0,
					'kodero' => $rekkassal,
					'keterangan' => '',
					'kredit' => $jumlahtotal,
					))
			->execute();
		//LO
		db_insert('jurnalitemlo' . $suffixjurnal)
			->fields(array('jurnalid', 'nomor', 'kodero', 'keterangan', 'kredit'))
			->values(array(
					'jurnalid'=> $jurnalid,
					'nomor'=> 0,
					'kodero' => $rekkaslo,
					'keterangan' => '',
					'kredit' => $jumlahtotal,
					))
			->execute();
			
		
		//ITEM BELANJA
		$nomor = 0;
		for ($n=1; $n <= $jumlahrek; $n++){
			$iskeg = $form_state['values']['iskeg' . $n];
			if ($iskeg=='0') {
				$kodero = $form_state['values']['koderoapbd' . $n];
				$jumlah = $form_state['values']['jumlahapbd' . $n];
				$kodekegrek = $form_state['values']['kodekegrek' . $n];
				$nomor++;
				
				//APBD
				db_insert('jurnalitem' . $suffixjurnal)
					->fields(array('jurnalid', 'nomor', 'kodero', 'debet', 'kodekeg'))
					->values(array(
							'jurnalid'=> $jurnalid,
							'nomor'=> $nomor,
							'kodero' => $kodero,
							'debet' => $jumlah,
							'kodekeg' => $kodekegrek,
							))
					->execute();
				
				//LRA
				$koderolra = $form_state['values']['koderolra' . $n];
				$keteranganlra = $form_state['values']['keteranganlra' . $n];
				$jumlahlra = $form_state['values']['jumlahlra' . $n];
				db_insert('jurnalitemlra' . $suffixjurnal)
					->fields(array('jurnalid', 'nomor', 'kodero', 'keterangan', 'debet', 'kodekeg'))
					->values(array(
							'jurnalid'=> $jurnalid,
							'nomor'=> $nomor,
							'kodero' => $koderolra,
							'keterangan' => $keteranganlra,
							'debet' => $jumlahlra,
							'kodekeg' => $kodekegrek,
							))
					->execute();
				
				//LO
				$koderolo = $form_state['values']['koderolo' . $n];
				$keteranganlo = $form_state['values']['keteranganlo' . $n];
				$jumlahlo = $form_state['values']['jumlahlo' . $n];
				db_insert('jurnalitemlo' . $suffixjurnal)
					->fields(array('jurnalid', 'nomor', 'kodero', 'keterangan', 'debet', 'kodekeg'))
					->values(array(
							'jurnalid'=> $jurnalid,
							'nomor'=> $nomor,
							'kodero' => $koderolo,
							'keterangan' => $keteranganlo,
							'debet' => $jumlahlo,
							'kodekeg' => $kodekegrek,
							))
					->execute();
			}
		}	
		
	//}
	//	catch (Exception $e) {
	//	$transaction->rollback();
	//	watchdog_exception('jurnal-' . $dokid, $e);
	//	db_set_active();
	//}
	
	//DOKUMEN
	db_set_active('penatausahaan');
	$query = db_update('dokumen')
	->fields(
			array(
				'jurnalsudah' . $suffixjurnal => 1,
				'jurnalidspj' . $suffixjurnal => $jurnalid,
			)
		);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();
	db_set_active();
	
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('jurnalspjjurnal');
}

?>
