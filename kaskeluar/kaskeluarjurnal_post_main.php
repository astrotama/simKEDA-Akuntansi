<?php
function kaskeluarjurnal_post_main($arg=NULL, $nama=NULL) {
	
	$dokid = arg(2);	
	if(arg(3)=='pdf'){			  
		$output = getTable($tahun,$dokid);
		print_pdf_p($output);
	
	} else {
	
		$btn = l('Cetak', 'kaskeluar/edit/' . $dokid . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('kaskeluarjurnal_post_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function getTable($tahun,$dokid){
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('dokid','keperluan', 'kegiatan', 'sppno', 'spptgl', 'spmno', 'spmtgl', 
					'penerimanama', 'penerimaalamat', 'penerimabanknama', 'penerimabankrekening', 'penerimanpwp'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$keperluan = $data->keperluan;
		$kegiatan = $data->kegiatan;
		
		$spp = $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl);
		$spm = $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
		$penerimanama = $data->penerimanama;
		$penerimaalamat = $data->penerimaalamat;
		if ($penerimaalamat=='') $penerimaalamat = 'Kosong, tidak diisi';
		$penerimarekening = $data->penerimabankrekening . ' ' . $data->penerimabanknama;
		$penerimanpwp = $data->penerimanpwp;
		if ($penerimanpwp=='') $penerimanpwp = 'Kosong, tidak diisi';
	}
	$top=array();
	$top[] = array (
		array('data' => 'SPP','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $spp, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'SPM','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $spm, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Keperluan','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $keperluan, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimanama, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima Alamat','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimaalamat, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima REkening','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimarekening, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima NPWP','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimanpwp, 'width' => '300px', 'align'=>'left'),
	);
	$header = array ();
	$output = theme('table', array('header' => $header, 'rows' => $top ));
	//INFO SP2D
	//INFO SP2D
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('sp2dno','sp2dtgl', 'jumlah', 'jenisdokumen'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dno= $data->sp2dno;
		$sp2dtgl= $data->sp2dtgl;
		$jumlah = $data->jumlah;
		$jenisdokumen = $data->jenisdokumen;
	}
	
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('sp2dno','sp2dtgl'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dno= $data->sp2dno;
		$sp2dtgl= $data->sp2dtgl;
	}
	
	$arrtgl=explode('-',$sp2dtgl);
	$tanggal=$arrtgl[2].'-'.$arrtgl[1].'-'.$arrtgl[0];	
	
	drupal_set_title('SP2D No : ' . $sp2dno . ', Tanggal : ' . $tanggal);
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	//REKENING
	
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '130px','align'=>'center','style'=>$styleheader),
		array('data' => 'Rekening', 'width' => '300px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Jumlah', 'width' => '130px','align'=>'center','style'=>$styleheader),
		
		
	);
	$query = db_select('dokumenrekening' , 'k');//->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('uraian', 'kodero','jumlah'));
	$query->condition('k.dokid', $dokid, '=');
	//$query->orderByHeader($header);
	$query->orderBy('k.kodero', 'ASC');
	# execute the query
	$results = $query->execute();
	
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	
	foreach ($results as $data) {
		$no++;  
		
		$rows[] = array(
						array('data' => $no.' ', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
						array('data' => $data->kodero, 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $data->uraian, 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($data->jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					$total+=$data->jumlah;
	}
	if ($no==0) {
		$no++;
		$str_jenis ='';
		if ($jenisdokumen==2) $str_jenis = 'Tambahan ';
		$rows[] = array(
			array('data' => $no, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
			array('data' => '00000000', 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => $str_jenis . 'Uang Persediaan (UP)', 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => apbd_fn($jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
		);
		
	}
	/*$rows[] = array(
						array('data' => $query, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						//array('data' => $data->kodero, 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $query, 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						//array('data' => apbd_fn($data->jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);*/
	$rows[] = array(
				array('data' => 'TOTAL', 'width' => '470px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
				array('data' => apbd_fn($total), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
				);
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));

	//POTONGAN
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '130px','align'=>'center','style'=>$styleheader),
		array('data' => 'Potongan', 'width' => '300px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Jumlah', 'width' => '130px','align'=>'center','style'=>$styleheader),
		
		
	);
	
	$query = db_select('dokumenpotongan' , 'k');//->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('uraian', 'nourut','jumlah'));
	$query->condition('k.dokid', $dokid, '=');
	//$query->orderByHeader($header);
	$query->orderBy('k.nourut', 'ASC');
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
			$rows[] = array(
							array('data' => $no.' ', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
							array('data' => $data->nourut, 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => $data->uraian, 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => apbd_fn($data->jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
							
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
						$total+=$data->jumlah;
		}
		
		if ($total==0) {
		$rows[] = array(
							array('data' => '', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
							array('data' => '', 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => 'Tidak ada potongan', 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => '', 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
							
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);		
		}
		$rows[] = array(
					array('data' => 'TOTAL', 'width' => '470px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
					array('data' => apbd_fn($total), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
					);					
		$output .= theme('table', array('header' => $header, 'rows' => $rows ));

		//PAJAK
		$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '130px','align'=>'center','style'=>$styleheader),
		array('data' => 'Pajak', 'width' => '300px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Jumlah', 'width' => '130px','align'=>'center','style'=>$styleheader),
		
		
	);
		
		$query = db_select('dokumenpajak' , 'k');//->extend('TableSort');

		# get the desired fields from the database
		$query->fields('k', array('uraian', 'kode','jumlah'));
		$query->condition('k.dokid', $dokid, '=');
		//$query->orderByHeader($header);
		$query->orderBy('k.kode', 'ASC');
		# execute the query
		$results = $query->execute();
			
		# build the table fields
		$no=0;
		$total=0;	
		
		$rows = array();
		foreach ($results as $data) {
			$no++;  
			$rows[] = array(
							array('data' => $no.' ', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
							array('data' => $data->kode, 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => $data->uraian, 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => apbd_fn($data->jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
							
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
			
						$total+=$data->jumlah;
		}
		
		if ($total==0) {
			$rows[] = array(
							array('data' => '', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
							array('data' => '', 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => 'Tidak ada pajak', 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
							array('data' => '', 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
							
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);		
		}
		$rows[] = array(
					array('data' => 'TOTAL', 'width' => '470px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
					array('data' => apbd_fn($total), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
					);
		$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return 	$output;
	
}

function kaskeluarjurnal_post_main_form($form, &$form_state) {
	
	$dokid = arg(2);
	
	db_set_active('penatausahaan');
	
	$query = db_select('dokumen', 'k');
	$query->join('unitkerja', 'u', 'k.kodeuk=u.kodeuk');
	$query->fields('k', array('dokid','keperluan', 'kegiatan', 'sp2dno', 'sp2dtgl', 'sppno', 'spptgl', 'spmno', 'spmtgl', 'jumlah', 'jenisdokumen', 'kodekeg'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('k.dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$title = 'SP2D No : ' . $data->sp2dno . ', ' . apbd_format_tanggal_pendek($data->sp2dtgl);
		
		$keperluan = $data->keperluan;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		$sp2dno= $data->sp2dno;
		$sp2dtgl= strtotime($data->sp2dtgl);		
		$spmno = $data->spmno;
		$sppspm = 'SPP: ' . $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl) . '; SPM: ' . $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
		$jumlah = $data->jumlah;
		
		$jenisdokumen = $data->jenisdokumen;
		$kodekeg = $data->kodekeg;
		
	}
	
	db_set_active();
	
	drupal_set_title($title);
	

	$form['skpd'] = array(
		'#type' => 'item',
		//'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		//'#markup' => '<p>' . $skpd . '</p>',
		//'#markup' => '<table><tr><td style="width:90%"><h3>' . $skpd . '</h3></td><td></td><td style="width:10%"><p align="text-align:right">' . apbd_link_esp2d($dokid) . '</p></td></tr></table>',
		
		'#markup' => '<div class="row"><div class="col-md-4"><h3>' . $skpd . '</h3></div><div class="col-md-8"><p class="text-right">' . apbd_link_esp2d($dokid) . '</p></div></div>',
	);	
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);	
	$form['jenisdokumen'] = array(
		'#type' => 'value',
		'#value' => $jenisdokumen,
	);		
	$form['dokid'] = array(
		'#type' => 'value',
		'#value' => $dokid,
	);	
	$form['sppspm'] = array(
		'#type' => 'item',
		'#title' =>  t('SPP dan SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $sppspm . '</p>',
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
	$form['nobuktilain'] = array(
		'#type' => 'textfield',
		'#title' =>  t('No Bukti Lain'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#default_value' => $spmno,
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

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		//'#value' => 'Simpan',
		//'#attributes' => array('class' => array('btn btn-success')),
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
		
	);

	return $form;
}

function kaskeluarjurnal_post_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$dokid = $form_state['values']['dokid'];
	
	$nobukti = $form_state['values']['nobukti'];
	$nobuktilain = $form_state['values']['nobuktilain'];
	$jenisdokumen = $form_state['values']['jenisdokumen'];
	$kodekeg = $form_state['values']['kodekeg'];
	$keperluan = $form_state['values']['keperluan'];
	$jumlah = $form_state['values']['jumlah'];
	
	$tanggal = $form_state['values']['tanggal'];
	$tanggalsql = $tanggal['year'] . '-' . $tanggal['month'] . '-' . $tanggal['day'];
	
	$suffixjurnal = apbd_getsuffixjurnal();
	$jurnalid = apbd_getkodejurnal($kodeuk);
	
	//BEGIN TRANSACTION
	//$transaction = db_transaction();
	
	//try {
		//JURNAL
		$query = db_insert('jurnal' . $suffixjurnal)
				->fields(array('jurnalid', 'refid', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'jenisdokumen', 'kodekeg'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $dokid,
						'kodeuk' => $kodeuk,
						'jenis' => 'kas',
						'nobukti' => $nobukti,
						'nobuktilain' => $nobuktilain,
						'tanggal' =>$tanggalsql,
						'keterangan' => $keperluan, 
						'total' => $jumlah,
						'jenisdokumen' => $jenisdokumen,
						'kodekeg' => $kodekeg,
					)
				);
		//echo $query;		
		$res = $query->execute();

		//JURNAL ITEM APBD
		//1
		$query = db_insert('jurnalitem' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
				->values(
					array(
						'jurnalid' => $jurnalid,
						'nomor' => '1',
						'kodero' => apbd_getKodeROAPBD(),
						'kredit' => $jumlah,
					)
				);
		$res = $query->execute();
		//2. 
		$query = db_insert('jurnalitem' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => '2',
						'kodero' => apbd_getKodeROBendaharaPengeluaran(),
						'debet' => $jumlah,
					)
				);
		$res = $query->execute();
		
		//JURNAL ITEM LO
		$query = db_insert('jurnalitemlo' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => '1',
						'kodero' => apbd_getKodeRORKPPKD(),
						'kredit' => $jumlah,
					)
				);
		$res = $query->execute();
		$query = db_insert('jurnalitemlo' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'nomor' => '2',
						'kodero' => apbd_getKodeROBendaharaPengeluaran(),
						'debet' => $jumlah,
					)
				);
		$res = $query->execute();
		
	//}
	//	catch (Exception $e) {
	//	$transaction->rollback();
	//	watchdog_exception('jurnal-' . $dokid, $e);
	//}		

	//DOKUMEN
	db_set_active('penatausahaan');
	$query = db_update('dokumen')
	->fields(
			array(
				'jurnalkassudah' . $suffixjurnal => 1,
				'jurnalidkas' . $suffixjurnal => $jurnalid,
			)
		);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();
	db_set_active();
	
	//if ($res) drupal_goto('kaskeluarantrian');
	drupal_goto('kaskeluarantrian');
}


?>
