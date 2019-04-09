<?php
function pendapatanmasuk_batch_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('pendapatanmasuk_batch_main_form');
	return drupal_render($output_form);// . $output;

}

function pendapatanmasuk_batch_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		
	} else {
		$kodeuk = '00';
	}	

 
	db_set_active('pendapatan');
	$results = db_query('select s.kodero, s.koderod, s.setorid, s.kodeuk, s.tanggal, s.jurnalsudah, s.keterangan, s.jumlahmasuk, s.jurnalid, r.uraian from setor s inner join rincianobyek r on s.kodero=r.kodero where s.jurnalsudah=0 and s.jumlahmasuk>0 and s.kodeuk=:kodeuk order by s.tanggal asc limit 5', array(':kodeuk'=>$kodeuk));
	$arr_result = $results->fetchAllAssoc('setorid');
	db_set_active();
	
	$form['tbljurnal']= array(
		'#prefix' => '<table class="table table-hover"><tr><th width="10px">NO</th><th width="90px">TANGGAL</th><th>REKENING</th><th>URAIAN</th><th>DETIL</th><th width="80px">JUMLAH</th><th width="5px"></th></tr>',
		 '#suffix' => '</table>',
	);	
	$i = 0;
	
	
	
	foreach ($arr_result as $data) {

		$i++; 
		
		$option_rod = array();
		$option_rod[''] = '-Pilih-'; 
		$query = db_select('rincianobyekdetil', 'r');
		# get the desired fields from the database
		$query->fields('r', array('koderod','uraian'))
				->condition('r.kodero', $data->kodero, '=')
				->orderBy('koderod', 'ASC');
		# execute the query
		$res = $query->execute();
		# build the table fields
		if($results){
			foreach($res as $dat) {
			  $option_rod[$dat->koderod] = $dat->uraian; 
			}
		}	
		
	
		$form['tbljurnal']['setorid' . $i]= array(
				'#type' => 'value',
				'#value' => $data->setorid,
		); 
		$form['tbljurnal']['tanggal' . $i]= array(
				'#type' => 'value',
				'#value' => $data->tanggal,
		);		
		$form['tbljurnal']['kodero' . $i]= array(
				'#type' => 'value',
				'#value' => $data->kodero,
		);		
		$form['tbljurnal']['jumlah' . $i]= array(
				'#type' => 'value',
				'#value' => $data->jumlahmasuk,
		);		
		$form['tbljurnal']['e_koderod' . $i]= array(
				'#type' => 'value',
				'#value' => $data->koderod,
		);		

		$form['tbljurnal']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $i,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['tbljurnal']['tanggalview' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> apbd_format_tanggal_pendek($data->tanggal), 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['rekening' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $data->kodero . ' - ' . $data->uraian, 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['keterangan' . $i]= array(
			'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#default_value'=> $data->keterangan, 
			'#suffix' => '</td>',
		); 
		//$required = strlen($data->kodero)>3;
		$form['tbljurnal']['koderod' . $i]= array(
			'#type'         => 'select', 
			'#prefix' => '<td>',
			'#options' => $option_rod,
			//'#required' => $required,
			'#default_value'=> '', 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['jumlahview' . $i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p align="right">' . apbd_fn($data->jumlahmasuk) . '</p>' , 
			'#suffix' => '</td>',
		); 
		$form['tbljurnal']['jurnalkan' . $i]= array(
			'#type'         => 'checkbox', 
			'#default_value'=> true, 
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);	

	}
	
	$form['kodeuk']= array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);	
	$form['jumlahkegiatan']= array(
		'#type' => 'value',
		'#value' => $i,
	);	

	
	
	//$ref = "javascript:history.go(-1)";
	//FORM SUBMIT DECLARATION
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Jurnalkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm pull-right')),
		//'#disabled' => $disable_simpan,
		//'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
	);
	
	return $form;
}

function pendapatanmasuk_batch_main_form_validate($form, &$form_state) {
$jumlahkegiatan = $form_state['values']['jumlahkegiatan'];	
for ($n=1; $n <= $jumlahkegiatan; $n++) {
	if ($form_state['values']['jurnalkan' . $n]) {
		$e_koderod = $form_state['values']['e_koderod' . $n];
		if (strlen($e_koderod)>3) {
			$koderod = $form_state['values']['koderod' . $n];
			if ($koderod=='') {
				form_set_error('koderod'. $n, 'Detil rekening nomor ' . $n . ' agar diisi sesuai dengan isian d PBP.');
			}			
		}
	}
}		
}


function pendapatanmasuk_batch_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$jumlahkegiatan = $form_state['values']['jumlahkegiatan'];

	for ($n=1; $n <= $jumlahkegiatan; $n++) { 
		if ($form_state['values']['jurnalkan' . $n]) {
			
			$setorid = $form_state['values']['setorid' . $n];
			$tanggal = $form_state['values']['tanggal' . $n];
			$keterangan = $form_state['values']['keterangan' . $n];
			$kodero = $form_state['values']['kodero' . $n];
			$koderod = $form_state['values']['koderod' . $n];
			$jumlah = $form_state['values']['jumlah' . $n];
			
			//drupal_set_message($kodeuk);			
			//drupal_set_message($setorid);
			//drupal_set_message($tanggal);
			//drupal_set_message($keterangan);
			//drupal_set_message($kodero);
			//drupal_set_message($koderod);
			//drupal_set_message($jumlah);
			//drupal_set_message($setorid);
			
			$res = jurnalkansetoran($kodeuk, $setorid, $setorid, $tanggal, $keterangan, $kodero, $koderod, $jumlah);
			if ($res) drupal_set_message('Penjurnalan penerimaan ke-'. $n . ' berhasil.');
		}
		
	}


}

function jurnalkansetoran($kodeuk, $setorid, $nobukti, $tanggal, $keterangan, $kodero, $koderod, $jumlah) {
	
	$jurnalid = apbd_getkodejurnal_uk($kodeuk);
	$query = db_insert('jurnaluk')
			->fields(array('jurnalid', 'refid', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'refid' => $setorid,
					'kodeuk' => $kodeuk,
					'jenis' => 'pad-in',
					'nobukti' => $nobukti,
					'nobuktilain' => $nobukti,
					'tanggal' =>$tanggal,
					'keterangan' => $keterangan, 
					'total' => $jumlah,
				)
			);
	//drupal_set_message($query);		
	$res = $query->execute();
	
	//JURNAL ITEM APBD
	//1
	$query = db_insert('jurnalitemuk')
			->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
			->values(
				array(
					'jurnalid' => $jurnalid,
					'nomor' => 1,
					'kodero' => '11103001',
					'debet' => $jumlah,
				)
			); 
	$res = $query->execute();
	//2. 
	$query = db_insert('jurnalitemuk')
			->fields(array('jurnalid', 'nomor', 'kodero', 'koderod', 'kredit'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => 2,
					'kodero' => $kodero,
					'koderod' => $koderod,
					'kredit' => $jumlah,
				)
			);
	$res = $query->execute();	
	
	//Rek LO
	$koderosap = '81101001';
	$sql = db_select('rekeningmapsap_apbd', 'rm');
	$sql->fields('rm',array('koderosap'));
	$sql->condition('koderoapbd', $kodero, '=');
	$res = $sql->execute();
	foreach ($res as $datamap) {
		$koderosap = $datamap->koderosap;
	}
	$query = db_insert('jurnalitemlouk')
			->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => 2,
					'kodero' => $koderosap,
					'kredit' => $jumlah,
				)
			);
	$res = $query->execute();
	
	
	//JURNAL ITEM LRA
	$query = db_insert('jurnalitemlrauk')
			->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => '1',
					'kodero' => apbd_getKodeRORKPPKD(),
					'debet' => $jumlah,
				)
			);
	$res = $query->execute();
	//Rek LRA
	$koderosap = '41101001';
	$sql = db_select('rekeningmaplra_apbd', 'rm');
	$sql->fields('rm',array('koderolra'));
	$sql->condition('koderoapbd', $kodero, '=');
	$res = $sql->execute();
	foreach ($res as $datamap) {
		$koderosap = $datamap->koderolra;
	}
	$query = db_insert('jurnalitemlrauk')
			->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
			->values(
				array(
					'jurnalid'=> $jurnalid,
					'nomor' => '2',
					'kodero' => $koderosap,
					'kredit' => $jumlah,
				)
			);
	$res = $query->execute();	
	
	//PBP
	db_set_active('pendapatan');
	$query = db_update('setor')
	->fields(
			array(
				'jurnalsudah' => 1,
				'jurnalid' => $jurnalid,
			)
		);
	$query->condition('setorid', $setorid, '=');
	$res = $query->execute();
	
	db_set_active();	
	
	return true;
}

?>
