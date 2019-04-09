<?php
function kaskeluarbatch_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('kaskeluarbatch_main_form');
	return drupal_render($output_form);// . $output;

}

function kaskeluarbatch_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$isSKPD = true;
	} else {
	
		$kodeuk = arg(1);
		if ($kodeuk=='') $kodeuk = 'ZZ';

		global $user;
		$username = $user->name;		
		
		$isSKPD = false;
	}
	
	db_set_active('penatausahaan');
	
	$form['tbljurnal']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th>SPKD</th><th width="90px">NO. SP2D</th><th width="90px">TGL. SP2D</th><th>KEGIATAN</th><th>KEPERLUAN</th><th width="80px">JUMLAH</th><th width="5px"></th><th width="5px"></th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	
	if ($isSKPD) {
		$query = db_select('dokumen', 'd')->extend('TableSort');
		$query->innerJoin('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
		$query->fields('d', array('sp2dno', 'sp2dtgl', 'dokid', 'keperluan', 'jumlah'));
		$query->fields('k', array('kegiatan'));
		$query->fields('uk', array('namasingkat'));
		
		$query->condition('d.jenisdokumen', '2', '<=');		//up, gu, tu
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.jurnalkassudahuk', '0', '=');
		//$query->condition('d.sp2dsudah', '1', '=');
		
	} else {
		$query = db_select('dokumen', 'd')->extend('TableSort');
		$query->innerJoin('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
		$query->innerJoin('userskpdakt', 'u', 'd.kodeuk=u.kodeuk');
		$query->fields('d', array('sp2dno', 'sp2dtgl', 'dokid', 'keperluan', 'jumlah'));
		$query->fields('k', array('kegiatan'));
		$query->fields('uk', array('namasingkat'));
		
		$query->condition('d.jenisdokumen', '2', '<=');		//up, gu, tu
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.jurnalkassudah', '0', '=');
		//$query->condition('d.sp2dsudah', '1', '=');
		$query->condition('u.username', $username, '=');
	}
	
	if ($kodeuk != 'ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	
	$query->orderBy('d.sp2dtgl', 'ASC');
	$query->orderBy('d.sp2dno', 'ASC');
	$query->range(0, 10);
	
	dpq($query);
	
	$results = $query->execute();
	
	foreach ($results as $data) {

		$i++; 
		
		$kegiatan = $data->kegiatan;
		if ($kegiatan=='') $kegiatan = 'Non Kegiatan';
		$form['tbljurnal']['dokid' . $i]= array(
				'#type' => 'value',
				'#value' => $data->dokid,
		); 
		
		$form['tbljurnal']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['tbljurnal']['skpd' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $data->namasingkat, 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['sp2dno' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $data->sp2dno, 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['sp2dtgl' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> apbd_format_tanggal_pendek($data->sp2dtgl), 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['kegiatan' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $kegiatan, 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['keperluan' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $data->keperluan, 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['jumlah' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p align="right">' . apbd_fn($data->jumlah) . '</p>' , 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['jurnalkan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> true, 
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);	
		$form['tbljurnal']['esp2d' . $i]= array(
			'#type'         => 'item', 
			'#markup'=> apbd_link_esp2d($data->dokid), 
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);			
		

	}
	$form['jumlahkegiatan']= array(
		'#type' => 'value',
		'#value' => $i,
	);	

	db_set_active();
	
	//$ref = "javascript:history.go(-1)";
	//FORM SUBMIT DECLARATION
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Jurnalkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		//'#disabled' => $disable_simpan,
		//'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}

function kaskeluarbatch_main_form_validate($form, &$form_state) {

}

function kaskeluarbatch_main_form_submit($form, &$form_state) {

	$jumlahkegiatan = $form_state['values']['jumlahkegiatan'];

	for ($n=1; $n <= $jumlahkegiatan; $n++) {
		if ($form_state['values']['jurnalkan' . $n]) {
			
			$dokid = $form_state['values']['dokid' . $n];
			
			//drupal_set_message($dokid);
			
			jurnalkansp2d($dokid);
		}
		
	}


}

function jurnalkansp2d($dokid) {
	
	db_set_active('penatausahaan');
	$query = db_select('dokumen', 'd');
	$query->fields('d', array('dokid','keperluan', 'kodeuk', 'kodekeg', 'sp2dno', 'sp2dtgl', 'sppno', 'spptgl', 'spmno', 'spmtgl', 'jumlah', 'jenisdokumen'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$keperluan = $data->keperluan;
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		
		$nobukti= $data->sp2dno;
		$tanggalsql= $data->sp2dtgl;		
		$nobuktilain = $data->spmno;
		$jumlah = $data->jumlah;
		
		$jenisdokumen = $data->jenisdokumen;
		
	}	
	db_set_active();

	$suffixjurnal = apbd_getsuffixjurnal();
	$jurnalid = apbd_getkodejurnal($kodeuk);
	
	//BEGIN TRANSACTION
	$transaction = db_transaction();	
	
	try {
		//JURNAL
		$query = db_insert('jurnal' . $suffixjurnal)
				->fields(array('jurnalid', 'refid', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'kodekeg', 'jenisdokumen'))
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
						'kodekeg' => $kodekeg,
						'jenisdokumen' => $jenisdokumen,
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
		

	}
		catch (Exception $e) {
		$transaction->rollback();
		watchdog_exception('jurnal-' . $dokid, $e);
	}		

	//DOKUMEN
	db_set_active('penatausahaan');
	$query = db_update('dokumen')
	->fields(
			array(
				'jurnalkassudah'  . $suffixjurnal => 1,
				'jurnalidkas' . $suffixjurnal => $jurnalid,
			)
		);
	$query->condition('dokid', $dokid, '=');
	$res = $query->execute();
	db_set_active();	
	
	
	
}

?>
