<?php
function jurnalspjbatch_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('jurnalspjbatch_main_form');
	return drupal_render($output_form);// . $output;

}

function jurnalspjbatch_main_form($form, &$form_state) {
	
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
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.jurnalsudahuk', '0', '=');
		$query->condition('d.sp2dno', '', '<>');

	} else {
		$query = db_select('dokumen', 'd')->extend('TableSort');
		$query->innerJoin('unitkerja', 'uk', 'd.kodeuk=uk.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
		$query->innerJoin('userskpd', 'u', 'd.kodeuk=u.kodeuk');
		$query->fields('d', array('sp2dno', 'sp2dtgl', 'dokid', 'keperluan', 'jumlah', 'jenisdokumen'));
		$query->fields('k', array('kegiatan'));
		$query->fields('uk', array('namasingkat'));
		$query->condition('d.sp2dok', '1', '=');
		$query->condition('d.jurnalsudah', '0', '=');
		$query->condition('d.sp2dno', '', '<>');
		$query->condition('u.username', $username, '=');
	}	
	if ($kodeuk != 'ZZ') $query->condition('d.kodeuk', $kodeuk, '=');

	$query->condition('d.jenisdokumen', array(1, 3, 4, 5, 7), 'IN');
	
	$query->orderBy('d.sp2dtgl', 'ASC');
	$query->orderBy('d.sp2dno', 'ASC');
	$query->range(0, 10);
	$results = $query->execute();
	//dpq($query);
	foreach ($results as $data) {

		$i++; 

		if ($data->jenisdokumen=='1')
			$kegiatan = 'Ganti Uang';
		elseif ($data->jenisdokumen=='5')
			$kegiatan = 'GU Nihil';
		elseif ($data->jenisdokumen=='7')
			$kegiatan = 'TU Nihil';
		else
			$kegiatan = $data->kegiatan;
		
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
		//apbd_link_esp2d($dokid)
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

function jurnalspjbatch_main_form_validate($form, &$form_state) {

}

function jurnalspjbatch_main_form_submit($form, &$form_state) {

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
	$query->innerJoin('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$query->fields('d', array('dokid','keperluan', 'kodekeg', 'sp2dno', 'sp2dtgl', 'sppno', 'spptgl', 'spmno', 'spmtgl', 'jumlah', 'jenisdokumen'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	$query->fields('k', array('kegiatan'));
	
	//$query->fields('u', array('namasingkat'));
	$query->condition('d.dokid', $dokid, '=');
	
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$keperluan = $data->keperluan;
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		$kegiatan = $data->kegiatan;
		
		$sp2dno= $data->sp2dno;
		$sp2dtgl= $data->sp2dtgl;		
		$spmno = $data->spmno;
		$jumlahtotal = $data->jumlah;
		
		$jenisdokumen = $data->jenisdokumen;
	}	

	//rekening
	$arr_kode = array();
	$arr_jumlah = array();
	$arr_kodekeg = array();
	$query = db_select('dokumenrekening', 'n');
	$query->join('rincianobyek', 'r', 'n.kodero=r.kodero');
	$query->fields('n', array('kodero', 'jumlah', 'kodekeg'));
	$query->fields('r', array('uraian'));
	$query->condition('n.dokid', $dokid, '=');
	$query->condition('n.jumlah', 0, '>');
	$results = $query->execute();
	$jumlahrek = 0;
	foreach ($results as $data) {
		$jumlahrek++;
		
		$arr_kode[$jumlahrek]  = $data->kodero;
		$arr_jumlah[$jumlahrek]  = $data->jumlah;
		$arr_kodekeg[$jumlahrek]  = $data->kodekeg;
		
		//drupal_set_message($jumlahrek . ': ' . $arr_kode[$jumlahrek] . ' - ' . $arr_jumlah[$jumlahrek]);
		
	}	
	//$jumlahrek = $i;	
	db_set_active();
	
	
	//BEGIN TRANSACTION
	$suffixjurnal = apbd_getsuffixjurnal();
	$jurnalid = apbd_getkodejurnal($kodeuk);
	
	$transaction = db_transaction();
	
	//JURNAL
	try {
		
		
		$query = db_insert('jurnal' . $suffixjurnal)
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'jenisdokumen'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $dokid,
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'jenis' => 'spj',
						'nobukti' => $sp2dno,
						'nobuktilain' => $spmno,
						'tanggal' =>$sp2dtgl,
						'keterangan' => $keperluan, 
						'total' => $jumlahtotal,
						'jenisdokumen' => $jenisdokumen,
					)
				);
		//echo $query;		
		$res = $query->execute();
		
		if (($jenisdokumen == '5') or ($jenisdokumen == '7')) {
			$rekkasapbd = apbd_getKodeROKasBendaharaPengeluaran();
			$rekkassal = $rekkasapbd;
			$rekkaslo = $rekkasapbd;

		} else {
			$rekkasapbd = apbd_getKodeROAPBD();
			$rekkassal = apbd_getKodeROSAL();
			$rekkaslo = apbd_getKodeRORKPPKD();
		}		
		
		//ITEM KAS
		//APBD
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
		//drupal_set_message($jumlahrek);
		for ($n=1; $n <= $jumlahrek; $n++){
			$kodero = $arr_kode[$n];
			$jumlah = $arr_jumlah[$n];
			$kodekeg_item = $arr_kodekeg[$n];
			if ($kodekeg_item=='') $kodekeg_item = $kodekeg;
			
			//drupal_set_message($n);
			//drupal_set_message('ABPD : ' . $kodero);
			//drupal_set_message('SAP : ' . apbd_get_rekening_sap($kodero));
			//drupal_set_message('LO : ' . apbd_get_rekening_lo($kodero));
			
			
			//APBD
			db_insert('jurnalitem' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet', 'kodekeg'))
				->values(array(
						'jurnalid'=> $jurnalid,
						'nomor'=> $n,
						'kodero' => $kodero,
						'debet' => $jumlah,
						'kodekeg' => $kodekeg_item,
						))
				->execute();
			
			//LRA
			$koderolra = apbd_get_rekening_sap($kodero);
			db_insert('jurnalitemlra' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet', 'keterangan', 'kodekeg'))
				->values(array(
						'jurnalid'=> $jurnalid,
						'nomor'=> $n,
						'kodero' => $koderolra,
						'debet' => $jumlah,
						'keterangan' => $kodero,
						'kodekeg' => $kodekeg_item,
						))
				->execute();			

			//LO
			$koderolo = apbd_get_rekening_lo($kodero);
			db_insert('jurnalitemlo' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet', 'keterangan', 'kodekeg'))
				->values(array(
						'jurnalid'=> $jurnalid,
						'nomor'=> $n,
						'kodero' => $koderolo,
						'debet' => $jumlah,
						'keterangan' => $kodero,
						'kodekeg' => $kodekeg_item,
						))
				->execute();			
				
		}	
	
	
	}
		catch (Exception $e) {
		$transaction->rollback();
		watchdog_exception('jurnal-' . $dokid, $e);
		db_set_active();
	}
	
	
	
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
	
	
	
}

?>
