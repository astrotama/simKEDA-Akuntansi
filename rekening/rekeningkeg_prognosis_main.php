<?php
function rekeningkeg_prognosis_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('rekeningkeg_prognosis_main_form');
	return drupal_render($output_form);
	
}

function rekeningkeg_prognosis_main_form ($form, &$form_state) {
	
	////drupal_set_message(arg(1));
	////drupal_set_message('2. ' . arg(2));
	$batch = 0;
	$uk = ''; 
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
		);
		//$uk = 'uk';
		$batch = arg(3);

		
	} else {
		//$uk = ''; 
		
		$param1 = arg(1);
		if (($param1 != 'ajax') and ($param1!='')) {
		//if (isset($param))	{
			$kodeuk = arg(2);
			$batch = arg(3);
			
			
			////drupal_set_message('3. ' . $batch);
		} else {
			$kodeuk = '58';
			if (!isset($_SESSION['prognosisuk'])) $_SESSION['prognosisuk'] = $kodeuk;		
			$kodeuk = $_SESSION['prognosisuk'];
			
			$batch = '1';
		}
		
		$optskpd[''] = '- PILIH SKPD -';
		 
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'));
		$query->orderBy('kodedinas', 'ASC');
		$results = $query->execute();
		$optskpd = array();
		$optskpd['XX'] = '- PILIH SKPD -';
		//$optskpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $optskpd[$data->kodeuk] = $data->namasingkat; 
			}
		}
		
		$form['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $optskpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,5
			'#default_value' => $kodeuk,
			'#ajax' => array(
				'event'=>'change',
				'callback' =>'_ajax_obyek',
				'wrapper' => 'obyek-wrapper',
			),
		
		);
	}	
	
	//drupal_set_message('b. ' . $batch);
	$auto = arg(4);
	
	$form['batch']= array(
		'#type' => 'value',
		'#value' => $batch,
	);
		
	
	if (isUserSKPD()) {
		$kodeuk  = apbd_getuseruk();
	} else {
		if (($param1 != 'ajax') and ($param1!='')) {
			
		} else {
			$kodeuk = $form_state['values']['kodeuk'];			
		}	
	}
	
	$form['wrapperrincianobyek'] = array(
		'#prefix' => '<div id="obyek-wrapper">',
		'#suffix' => '</div>',
	);
	$form['wrapperrincianobyek']['mnulink' . $kodeuk]= array(
		'#type' => 'markup',
		'#markup' => '<p style="text-align:center">' . apbd_get_paging($kodeuk) . '</p>',
	); 

	
	$form['wrapperrincianobyek']['tablerek']= array(
		'#prefix' => '<table class="table table-hover"><tr><th  width="10px">NO.</th><th>URAIAN</th><th width="120px">ANGGARAN</th><th width="120px">REALISASI</th><th width="70px">% REA</th><th width="120px">SISA</th><th width="100px">% PROG</th><th width="120px">RP. PROG</th><th  width="5px"></th></tr>',
		 '#suffix' => '</table>',
	);
	
	$i = 0; $ikeg = 0;
		
	 
	$tanggal_awal = apbd_tahun() . '-01-01';
	$tanggal_akhir = apbd_tahun() . '-06-30';
 
	//PENDAPATAN
	if (($batch==1) or ($batch=='')) {
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodeuk=:kodeuk', array(':kodeuk'=>$kodeuk));
		foreach ($results as $data) {
			$anggaran = $data->anggaran;
		}
		////drupal_set_message($anggaran);
		
		if ($anggaran>0) {

			$kodekeg = '4';
			$i++;
			$realisasi = read_realisasi_keg($kodeuk, $kodekeg, $uk);
			$prognosis = apbd_read_prognosis_pendapatan($kodeuk);
		
			$form['wrapperrincianobyek']['tablerek']['kodekeg' . $i]= array(
					'#type' => 'value',
					'#value' => $kodekeg,
			); 
			$form['wrapperrincianobyek']['tablerek']['kodeo' . $i]= array(
					'#type' => 'value',
					'#value' => '0',
			); 
			
			$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => '',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<strong>PENDAPATAN</strong>',
					'#suffix' => '</td>',
			); 
			
			$form['wrapperrincianobyek']['tablerek']['anggaran' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran) . '</strong></p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['realisasi' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($realisasi) . '</strong></p>',
					'#suffix' => '</td>',			
			); 
			$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</strong></p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['sisa' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran - $realisasi) . '</strong></p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['persen' . $kodeuk . $kodekeg . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
					'#suffix' => '</td>',	
			); 
			$form['wrapperrincianobyek']['tablerek']['prognosis' . $kodeuk . $kodekeg . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($prognosis) . '</strong></p>',
					'#suffix' => '</td>',	
			); 
			$form['wrapperrincianobyek']['tablerek']['status' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
					'#suffix' => '</td></tr>',	
			); 
			
			//REKENING	
			$results = db_query('select o.kodeo, o.uraian, sum(a.jumlah) as anggaran from {obyek} o inner join {anggperuk} a on o.kodeo=left(a.kodero,5) where a.kodeuk=:kodeuk group by o.kodeo, o.uraian order by o.kodeo', array(':kodeuk'=>$kodeuk));
				
			foreach ($results as $data) {
				
				$persen = 0;
				$baru = '0';

				$anggaran = $data->anggaran;
				$realisasi = read_realisasi_rek($kodeuk, $kodekeg, $data->kodeo, $uk);				
				$prognosis = 0;
				
				$status =  apbd_icon_prognosis_belum();
				$resx = db_query('select persen,prognosis from {prognosiskeg} where kodeuk=:kodeuk and kodeo=:kodeo and kodekeg=:kodekeg', array(':kodeo'=>$data->kodeo, ':kodekeg'=>$kodekeg, ':kodeuk'=>$kodeuk));
				foreach ($resx as $datax) { 
					$persen = $datax->persen;
					$prognosis = $datax->prognosis;
					$baru = '1';
					$status = apbd_icon_prognosis_sudah();
				}
				if ($persen == '') $persen = 0;
				if ($prognosis == '') $prognosis = 0;
				if ($baru != '1') $baru = '0';
				
				$persenrea = apbd_hitungpersen($anggaran, $realisasi);
				if ($auto=='auto') {
					
					 //$persen = round(100 - $persenrea,1);
					 $persen = 100;
					 if ($persen<0) {
						 $persen = 100;
						 $prognosis = $realisasi;
					 } else {	
						$prognosis = (($persen/100) * $anggaran) - $realisasi;
					 }

					 if ($prognosis<0) $prognosis = $realisasi;	
					 
					 //$prognosis = 100;
				}
				
				
				$i++; 
				$form['wrapperrincianobyek']['tablerek']['kodekeg' . $i]= array(
						'#type' => 'value',
						'#value' => $kodekeg,
				); 
				$form['wrapperrincianobyek']['tablerek']['kodeo' . $i]= array(
						'#type' => 'value',
						'#value' => $data->kodeo,
				); 

				$form['wrapperrincianobyek']['tablerek']['anggaran' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
						'#type' => 'value',
						'#value' => $anggaran,  
				);  
				$form['wrapperrincianobyek']['tablerek']['realisasi' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
						'#type' => 'value',
						'#value' => $realisasi,  
				);  

				$form['wrapperrincianobyek']['tablerek']['sisa' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
						'#type' => 'value',
						'#value' => $anggaran - $realisasi,  
				);  
				
				$form['wrapperrincianobyek']['tablerek']['baru' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
				//$form['wrapperrincianobyek']['tablerek']['baru' . $i]= array(
						'#type' => 'value',
						'#value' => $baru,  
				);  
				$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
						'#prefix' => '<tr><td>',
						'#markup' => '',
						'#suffix' => '</td>',
				); 
				$uraian = l($data->kodeo . ' - ' . $data->uraian, '/akuntansi/buku/ZZ/' . $data->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
				
				$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => $uraian,
						'#suffix' => '</td>',
				); 
				
				//$realisasi = 0;
				$form['wrapperrincianobyek']['tablerek']['anggaran_view' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn($anggaran) . '</p>',
						'#suffix' => '</td>',
				); 
				$form['wrapperrincianobyek']['tablerek']['realisasi_str' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn($realisasi) . '</p>',
						'#suffix' => '</td>',			); 
				$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn1($persenrea) . '</p>',
						'#suffix' => '</td>',
				); 
				$form['wrapperrincianobyek']['tablerek']['sisa' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran - $realisasi) . '</strong></p>',
						'#suffix' => '</td>',
				); 

				
				$form['wrapperrincianobyek']['tablerek']['persen' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
				//$form['wrapperrincianobyek']['tablerek']['persen' . $i]= array(
						'#prefix' => '<td>',
						'#type' => 'textfield',
						'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
						'#size' => 10,					
						'#default_value' => $persen,
						'#suffix' => '</td>',
				); 	
				$form['wrapperrincianobyek']['tablerek']['preview' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn($prognosis) . '</p>',
						'#suffix' => '</td>',
				); 
				$form['wrapperrincianobyek']['tablerek']['status' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => $status,
						'#suffix' => '</td></tr>',	
				); 
				
			}		
		}
	}
	
	//KEGIATAN
	if (($batch=='0') or ($batch==''))
		$reskeg = db_query('select kodekeg,kegiatan,total from {kegiatanskpd} where inaktif=0 and kodeuk=:kodeuk order by jenis,kegiatan limit 15', array(':kodeuk'=>$kodeuk));
	else
		$reskeg = db_query('select k.kodekeg,k.kegiatan,k.total from {kegiatanskpd} k inner join {kegiatanbk8} b on k.kodekeg=b.kodekeg where k.inaktif=0 and b.batch=:batch and k.kodeuk=:kodeuk order by k.jenis,k.kegiatan limit 15', array(':kodeuk'=>$kodeuk, ':batch'=>$batch));
	
	////drupal_set_message($batch);	
	foreach ($reskeg as $datakeg) {
		
		//KEGIATAN
		$i++;
		$ikeg++;
		$form['wrapperrincianobyek']['tablerek']['kodekeg' . $i]= array(
				'#type' => 'value',
				'#value' => $datakeg->kodekeg,
		); 
		$form['wrapperrincianobyek']['tablerek']['kodeo' . $i]= array(
				'#type' => 'value',
				'#value' => '0',
		); 
		$form['wrapperrincianobyek']['tablerek']['baru' . $kodeuk . $datakeg->kodekeg . $i]= array(
				'#type' => 'value',
				'#value' => '',  
		);  
		////drupal_set_message((($batch * 15) + $ikeg));
		$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => '<strong>' . ((($batch-1) * 15) + $ikeg) . '.</strong>',
				'#suffix' => '</td>',
		); 
		$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<strong>' . $datakeg->kegiatan . '</strong>',
				'#suffix' => '</td>',
		); 
		
		$anggaran = $datakeg->total;
		$realisasi = read_realisasi_keg($kodeuk, $datakeg->kodekeg, $uk);
		$prognosis = apbd_read_prognosis_belanja($datakeg->kodekeg);
		
		//$realisasi = 0;
		$form['wrapperrincianobyek']['tablerek']['anggaran' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran) . '</strong></p>',
				'#suffix' => '</td>',
		); 
		$form['wrapperrincianobyek']['tablerek']['realisasi' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($realisasi) . '</strong></p>',
				'#suffix' => '</td>',			
		); 
		$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p style="text-align:right"><strong>' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</strong></p>',
				'#suffix' => '</td>',
		); 
		$form['wrapperrincianobyek']['tablerek']['sisa' . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran - $realisasi) . '</strong></p>',
				'#suffix' => '</td>',
		); 
		$form['wrapperrincianobyek']['tablerek']['persen' . $kodeuk . $datakeg->kodekeg . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '',
				'#suffix' => '</td>',	
		); 		
		$form['wrapperrincianobyek']['tablerek']['prognosis' . $kodeuk . $datakeg->kodekeg . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($prognosis) . '</strong></p>',
				'#suffix' => '</td>',	
		); 
		$form['wrapperrincianobyek']['tablerek']['status'  . $i]= array(
				'#prefix' => '<td>',
				'#markup' => '',
				'#suffix' => '</td></tr>',	
		); 
		
		//rekening
		
		$results = db_query('select o.kodeo, o.uraian, sum(a.jumlah) as anggaran from {obyek} o inner join {anggperkeg} a on o.kodeo=left(a.kodero,5) where a.kodekeg=:kodekeg group by o.kodeo, o.uraian order by o.kodeo', array(':kodekeg'=>$datakeg->kodekeg));
			
		foreach ($results as $data) {
			
			$persen = 0;
			$baru = '0';
			$prognosis = 0;
			
			$status = apbd_icon_prognosis_belum();
			$resx = db_query('select persen,prognosis from {prognosiskeg} where kodeuk=:kodeuk and kodeo=:kodeo and kodekeg=:kodekeg', array(':kodeo'=>$data->kodeo, ':kodekeg'=>$datakeg->kodekeg, ':kodeuk'=>$kodeuk));
			foreach ($resx as $datax) {
				$persen = $datax->persen;
				$prognosis = $datax->prognosis;
				$baru = '1';
				$status = apbd_icon_prognosis_sudah();
			}
			if ($persen == '') $persen = 0;
			if ($prognosis == '') $prognosis = 0;
			if ($baru != '1') $baru = '0';
			
			$anggaran = $data->anggaran;
			//$realisasi = 0;
			$realisasi = read_realisasi_rek($kodeuk, $datakeg->kodekeg, $data->kodeo, $uk);
			if (($anggaran+$realisasi)>0) {
				
				$persenrea = apbd_hitungpersen($anggaran, $realisasi);
				if ($auto=='auto') {
					 //$persen = round(100 - $persenrea,1);
					 $persen = 100;
					 if ($persen<0) {
						 $persen = 100;
						 $prognosis = $realisasi;
					 } else {	
						$prognosis = (($persen/100) * $anggaran) - $realisasi;
					 }

					 if ($prognosis<0) $prognosis = $realisasi;	
				}
				
				$i++; 
				$form['wrapperrincianobyek']['tablerek']['kodekeg' . $i]= array(
						'#type' => 'value',
						'#value' => $datakeg->kodekeg,
				); 
				$form['wrapperrincianobyek']['tablerek']['kodeo' . $i]= array(
						'#type' => 'value',
						'#value' => $data->kodeo,
				); 
				$form['wrapperrincianobyek']['tablerek']['anggaran' . $kodeuk . $datakeg->kodekeg .  $data->kodeo . $i]= array(
						'#type' => 'value',
						'#value' => $anggaran,  
				);  
				$form['wrapperrincianobyek']['tablerek']['realisasi' . $kodeuk . $datakeg->kodekeg .  $data->kodeo . $i]= array(
						'#type' => 'value',
						'#value' => $realisasi,  
				); 
				$form['wrapperrincianobyek']['tablerek']['sisa' . $kodeuk . $datakeg->kodekeg .  $data->kodeo . $i]= array(
						'#type' => 'value',
						'#value' => $anggaran - $realisasi,  
				);  
				
				$form['wrapperrincianobyek']['tablerek']['baru' . $kodeuk . $datakeg->kodekeg .  $data->kodeo . $i]= array(
				//$form['wrapperrincianobyek']['tablerek']['baru' . $i]= array(
						'#type' => 'value',
						'#value' => $baru,  
				);  
				$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
						'#prefix' => '<tr><td>',
						'#markup' => '',
						'#suffix' => '</td>',
				); 
				$uraian = l($data->kodeo . ' - ' . $data->uraian, '/akuntansi/buku/' . $datakeg->kodekeg . '/' . $data->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
				
				$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => $uraian,
						'#suffix' => '</td>',
				); 
				
				$form['wrapperrincianobyek']['tablerek']['anggaran' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn($anggaran) . '</p>',
						'#suffix' => '</td>',
				); 
				$form['wrapperrincianobyek']['tablerek']['realisasi' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn($realisasi) . '</p>',
						'#suffix' => '</td>',			); 
				$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn1($persenrea) . '</p>', 
						'#suffix' => '</td>',
				); 
				$form['wrapperrincianobyek']['tablerek']['sisa' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran - $realisasi) . '</strong></p>',
						'#suffix' => '</td>',
				); 
				$form['wrapperrincianobyek']['tablerek']['persen' . $kodeuk . $datakeg->kodekeg .  $data->kodeo . $i]= array(
				//$form['wrapperrincianobyek']['tablerek']['persen' . $i]= array(
						'#prefix' => '<td>',
						'#type' => 'textfield',
						'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
						'#size' => 10,					
						'#default_value' => $persen,
						'#suffix' => '</td>',
				); 	
				$form['wrapperrincianobyek']['tablerek']['prognosis' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => '<p style="text-align:right">' . apbd_fn($prognosis) . '</p>', 
						'#suffix' => '</td>',
				); 
				$form['wrapperrincianobyek']['tablerek']['status' . $i]= array(
						'#prefix' => '<td>',
						'#markup' => $status, 
						'#suffix' => '</td></tr>',
				); 
			}
			
		}		
		
		
	}

	//PEMBIAYAAN
	if ($batch=='999') {
		$results = db_query('select left(kodero,2) as kodek, sum(jumlah) as anggaran from {anggperda} group by left(kodero,2)');
		foreach ($results as $data) {

			$anggaran = $data->anggaran;
			$realisasi = read_realisasi_keg($kodeuk, $kodekeg, $uk);

			$kodekeg = $data->kodek;
			$uraian = ($data->kodek=='61' ? 'PENERIMAAN PEMBIAYAAN':'PENGELUUARAN PEMBIAYAAN');
			
			$i++;
			$prognosis = apbd_read_prognosis_pembiayaan($kodekeg);
		
			$form['wrapperrincianobyek']['tablerek']['kodekeg' . $i]= array(
					'#type' => 'value',
					'#value' => $kodekeg,
			); 
			$form['wrapperrincianobyek']['tablerek']['kodeo' . $i]= array(
					'#type' => 'value',
					'#value' => '0',
			); 
			
			$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => '',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<strong>' . $uraian . '</strong>',
					'#suffix' => '</td>',
			); 
			
			$form['wrapperrincianobyek']['tablerek']['anggaran' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran) . '</strong></p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['realisasi' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($realisasi) . '</strong></p>',
					'#suffix' => '</td>',			
			); 
			$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</strong></p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['sisa' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran - $realisasi) . '</strong></p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['persen' . $kodeuk . $kodekeg . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
					'#suffix' => '</td>',	
			); 
			$form['wrapperrincianobyek']['tablerek']['prognosis' . $kodeuk . $kodekeg . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($prognosis) . '</strong></p>',
					'#suffix' => '</td>',	
			); 
			$form['wrapperrincianobyek']['tablerek']['status' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
					'#suffix' => '</td></tr>',	
			); 
			
			//REKENING	
			$results = db_query('select o.kodeo, o.uraian, sum(a.jumlah) as anggaran from {obyek} o inner join {anggperda} a on o.kodeo=left(a.kodero,5) where o.kodeo like :pembiayaan group by o.kodeo, o.uraian order by o.kodeo', array(':pembiayaan'=>$kodekeg . '%'));
				
			foreach ($results as $data) {
				
				$persen = 0;
				$baru = '0';

				$anggaran = $data->anggaran;
				$realisasi = read_realisasi_rek($kodeuk, $kodekeg, $data->kodeo, $uk);				
				
				if (($anggaran+$realisasi)>0) {

					$prognosis = 0;
					
					$status =  apbd_icon_prognosis_belum();
					$resx = db_query('select persen,prognosis from {prognosiskeg} where kodeuk=:kodeuk and kodeo=:kodeo and kodekeg=:kodekeg', array(':kodeo'=>$data->kodeo, ':kodekeg'=>$kodekeg, ':kodeuk'=>$kodeuk));
					foreach ($resx as $datax) {
						$persen = $datax->persen;
						$prognosis = $datax->prognosis;
						$baru = '1';
						$status = apbd_icon_prognosis_sudah();
					}
					if ($persen == '') $persen = 0;
					if ($prognosis == '') $prognosis = 0;
					if ($baru != '1') $baru = '0';
					
					$persenrea = apbd_hitungpersen($anggaran, $realisasi);
					if ($auto=='auto') {
						 $persen = round(100 - $persenrea,1);
						 if ($persen<0) {
							 $persen = 100;
							 $prognosis = $realisasi;
						 } else {	
							$prognosis = ($persen/100) * ($anggaran - $realisasi);
						 }
						 if ($prognosis<0) $prognosis = $realisasi;	
					}
					
					$i++; 
					$form['wrapperrincianobyek']['tablerek']['kodekeg' . $i]= array(
							'#type' => 'value',
							'#value' => $kodekeg,
					); 
					$form['wrapperrincianobyek']['tablerek']['kodeo' . $i]= array(
							'#type' => 'value',
							'#value' => $data->kodeo,
					); 

					$form['wrapperrincianobyek']['tablerek']['anggaran' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
							'#type' => 'value',
							'#value' => $anggaran,  
					);  
					$form['wrapperrincianobyek']['tablerek']['realisasi' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
							'#type' => 'value',
							'#value' => $realisasi,  
					);  

					$form['wrapperrincianobyek']['tablerek']['sisa' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
							'#type' => 'value',
							'#value' => $anggaran - $realisasi,  
					);  
					
					$form['wrapperrincianobyek']['tablerek']['baru' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
					//$form['wrapperrincianobyek']['tablerek']['baru' . $i]= array(
							'#type' => 'value',
							'#value' => $baru,  
					);  
					$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
							'#prefix' => '<tr><td>',
							'#markup' => '',
							'#suffix' => '</td>',
					); 
					$uraian = l($data->kodeo . ' - ' . $data->uraian, '/akuntansi/buku/ZZ/' . $data->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
					
					$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
							'#prefix' => '<td>',
							'#markup' => $uraian,
							'#suffix' => '</td>',
					); 
					
					//$realisasi = 0;
					$form['wrapperrincianobyek']['tablerek']['anggaran' . $i]= array(
							'#prefix' => '<td>',
							'#markup' => '<p style="text-align:right">' . apbd_fn($anggaran) . '</p>',
							'#suffix' => '</td>',
					); 
					$form['wrapperrincianobyek']['tablerek']['realisasi' . $i]= array(
							'#prefix' => '<td>',
							'#markup' => '<p style="text-align:right">' . apbd_fn($realisasi) . '</p>',
							'#suffix' => '</td>',			); 
					$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
							'#prefix' => '<td>',
							'#markup' => '<p style="text-align:right">' . apbd_fn1($persenrea) . '</p>',
							'#suffix' => '</td>',
					); 

					$form['wrapperrincianobyek']['tablerek']['sisa' . $i]= array(
							'#prefix' => '<td>',
							'#markup' => '<p style="text-align:right"><strong>' . apbd_fn($anggaran - $realisasi) . '</strong></p>',
							'#suffix' => '</td>',
					); 
					
					$form['wrapperrincianobyek']['tablerek']['persen' . $kodeuk . $kodekeg .  $data->kodeo . $i]= array(
					//$form['wrapperrincianobyek']['tablerek']['persen' . $i]= array(
							'#prefix' => '<td>',
							'#type' => 'textfield',
							'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
							'#size' => 10,					
							'#default_value' => $persen,
							'#suffix' => '</td>',
					); 	
					$form['wrapperrincianobyek']['tablerek']['preview' . $i]= array(
							'#prefix' => '<td>',
							'#markup' => '<p style="text-align:right">' . apbd_fn($prognosis) . '</p>',
							'#suffix' => '</td>',
					); 
					$form['wrapperrincianobyek']['tablerek']['status' . $i]= array(
							'#prefix' => '<td>',
							'#markup' => $status,
							'#suffix' => '</td></tr>',	
					); 
				}
			}		
			
		}
		

	}		
	
	$form['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	
	$form['submitsave']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	/*

	*/
	
	/*
		$form['submitprognosis']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span> Hitung',
			'#attributes' => array('class' => array('btn btn-primary btn-sm')),
		);	
	*/

	
	if (isAdministrator()) {
		$form['submitauto']= array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-play" aria-hidden="true"></span> Otomatis',
			'#attributes' => array('class' => array('btn btn-primary btn-sm')),
		);	
		$form['submitcron']= array(
			'#type' => 'submit',
			//'#prefix' => '<div class="col-md-1">',
			//'#suffix' => '</div>',
			'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Paging',
			'#attributes' => array('class' => array('btn btn-primary btn-sm pull-right')),
		);		
	}	
	
	return $form;
}


function _ajax_jenis($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperobyek'];
}
function _ajax_obyek($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperrincianobyek'];
}

function _ajax_kelompok($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperjenis'];
}

function _load_kelompok() {
	$kelompoks = array('- Pilih Kelompok -');


	// Select table
	$query = db_select("kelompok", "k");
	// Selected fields
	$query->fields("k", array('kodek', 'uraian'));	
	$query->condition("k.kodea", '4', '>=');
	
	
	// Order by name
	$query->orderBy("k.kodek");
	// Execute query
	$result = $query->execute(); 

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$kelompoks[$row->kodek] = $row->kodek . ' - ' . $row->uraian;
	}

	return $kelompoks;
}


/**
 * Function for populating rekening
 */
function _load_jenis($kodek) {
	$jenises = array('- Pilih Jenis -');


	// Select table
	$query = db_select("jenis", "j");
	// Selected fields
	$query->fields("j", array('kodej', 'uraian'));	
	$query->condition("j.kodek", $kodek, '=');
	
	
	// Order by name
	$query->orderBy("j.kodej");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$jenises[$row->kodej] = $row->kodej . ' - ' . $row->uraian;
	}

	return $jenises;
}
function _load_obyek($kodej, $kodeuk) {
	$obyek = array('- Pilih Obyek -');
	
	////drupal_set_message($kodej);
	
	if (substr($kodej, 0,1)=='5')
		$result = db_query('select o.kodeo, o.uraian from {obyek} o inner join {anggperkeg} a on o.kodeo=left(a.kodero,5) inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where o.kodej=:kodej and k.kodeuk=:kodeuk', array(':kodej'=>$kodej, ':kodeuk'=>$kodeuk));
	else
		$result = db_query('select o.kodeo, o.uraian from {obyek} o inner join {anggperuk} a on o.kodeo=left(a.kodero,5) where o.kodej=:kodej and a.kodeuk=:kodeuk', array(':kodej'=>$kodej, ':kodeuk'=>$kodeuk));
	
	/*
	// Select table
	$query = db_select("obyek", "o");
	
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));	
	$query->condition("o.kodej", $kodej, '='); 
	
	
	// Order by name
	$query->orderBy("o.kodeo");
	// Execute query
	$result = $query->execute();
	*/
	
	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options 
		$obyek[$row->kodeo] = $row->kodeo . ' - ' . $row->uraian;
	}

	return $obyek;
}
function _load_ro($kodeo) {
	$obyeks = array('- Pilih Obyek -');

	
	// Select table
	$query = db_select("obyek", "o");
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));
	// Filter the active ones only
	$query->condition("o.kodej", $kodek, '=');
	
	// Order by name
	$query->orderBy("o.kodeo");
	// Execute query
	$result = $query->execute();
	

	
	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$obyeks[$row->kodeo] = $row->uraian;
	}

	return $obyeks;
}

function rekeningkeg_prognosis_main_form_validate($form, &$form_state) {
}

function rekeningkeg_prognosis_main_form_submit($form, &$form_state) {
$kodeuk = $form_state['values']['kodeuk'];
$batch = $form_state['values']['batch'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitauto']) {
	drupal_goto('rekeningkeg/prognosis/' . $kodeuk . '/' . $batch . '/auto');
	
} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitcron']) {
	apbd_keg_paging();	
	
} else { 


	$jumlahrek = $form_state['values']['jumlahrek'];
	for ($n=1; $n <= $jumlahrek; $n++) {

		//$kodeuk . $datakeg->kodekeg .  $data->kodeo	
		$kodekeg = $form_state['values']['kodekeg' . $n];
		$kodeo = $form_state['values']['kodeo' . $n];
		
		if ($kodeo!='0') {
			$persen = $form_state['values']['persen' . $kodeuk . $kodekeg .  $kodeo . $n];
			$baru = $form_state['values']['baru' . $kodeuk . $kodekeg .  $kodeo . $n];
			$persen = $form_state['values']['persen' . $kodeuk . $kodekeg .  $kodeo . $n];
			$sisa = $form_state['values']['sisa' . $kodeuk . $kodekeg .  $kodeo . $n];
			$anggaran = $form_state['values']['anggaran' . $kodeuk . $kodekeg .  $kodeo . $n];
			$realisasi = $form_state['values']['realisasi' . $kodeuk . $kodekeg .  $kodeo . $n];
			
			//$baru = $form_state['values']['baru' . $n];
			 
			//drupal_set_message('agg . ' . $anggaran); 
			//drupal_set_message('keg . ' . $kodekeg);
			//drupal_set_message('rek . ' . $kodeo);
			//drupal_set_message('% . ' . $persen);
			
			//$prognosis = ($persen/100) * $sisa;
			if ($persen==-1)
				$prognosis = $realisasi;
			elseif ($persen==-9)
				$prognosis = 0;			
			else
				$prognosis = (($persen/100) * $anggaran) - $realisasi;
			if ($prognosis<0) $prognosis = abs($prognosis);
			
			//drupal_set_message('p . ' . $prognosis);
			
			if (($baru=='0') or ($baru=='')) {
				////drupal_set_message('new');
				
				db_insert('prognosiskeg')
				->fields(array(
						'kodeo' => $kodeo,
						'kodeuk' => $kodeuk,
						'kodekeg' => $kodekeg,
						'persen' => $persen,
						'prognosis' => $prognosis,
						))
				->execute();
				
						
			} else {
				//drupal_set_message('edit');
				//drupal_set_message($persen);
				
				if (substr($kodeo, 0, 1)=='6') {
					db_update('prognosiskeg')
							->fields(
								array(
									'persen' => $persen,
									'prognosis' => $prognosis,
								)
							)
							->condition('kodeo', $kodeo, '=')
							->condition('kodeuk', $kodeuk, '=')
							->execute();	
				} else {
					db_update('prognosiskeg')
							->fields(
								array(
									'persen' => $persen,
									'prognosis' => $prognosis,
								)
							)
							->condition('kodeo', $kodeo, '=')
							->condition('kodeuk', $kodeuk, '=')
							->condition('kodekeg', $kodekeg, '=')
							->execute();	
				}				
			}
			
			
			
		}
	}
	
	drupal_goto('rekeningkeg/prognosis/' . $kodeuk . '/' . $batch);

}


}

function rekap_prognosis() {

/*
update anggperuk set kodeuk='00' where kodeuk='81' and left(kodero,2) in ('42', '43')

update anggperuk set kodeuk='00' where kodeuk='81' and left(kodero,3) in ('413', '414')

insert into prognosisskpd (kodeuk, kodeo, anggaran)
SELECT kodeuk, left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperuk group by kodeuk, left(kodero,5)

insert into prognosisskpd (kodeuk, kodeo, anggaran)
SELECT kegiatanskpd.kodeuk, left(anggperkeg.kodero,5) as kodeo, sum(anggperkeg.jumlah) as anggaran 
from anggperkeg inner join kegiatanskpd on anggperkeg.kodekeg=kegiatanskpd.kodekeg 
where kegiatanskpd.inaktif=0
group by kegiatanskpd.kodeuk, left(anggperkeg.kodero,5)

insert into prognosisskpd (kodeuk, kodeo, anggaran)
SELECT '00', left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperda group by left(kodero,5)

update jurnal inner join jurnalitem on jurnal.jurnalid=jurnalitem.jurnalid
set jurnal.kodeuk='00' where jurnal.kodeuk='81' and left(jurnalitem.kodero,3) in ('413', '414')

*KAB*

insert into prognosiskab (kodeo, anggaran)
SELECT left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperuk group by left(kodero,5)

insert into prognosiskab (kodeo, anggaran)
SELECT  left(anggperkeg.kodero,5) as kodeo, sum(anggperkeg.jumlah) as anggaran 
from anggperkeg inner join kegiatanskpd on anggperkeg.kodekeg=kegiatanskpd.kodekeg 
where kegiatanskpd.inaktif=0
group by left(anggperkeg.kodero,5)

insert into prognosiskab (kodeo, anggaran)
SELECT left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperda group by left(kodero,5)
	
*/

$res_x = db_query('UPDATE prognosisskpd set realisasi=0, prognosis=0');

$res_uk = db_query("select kodeuk from {unitkerja} order by kodeuk");
foreach ($res_uk as $data_uk) {

	$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and 
			MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'5', ':kodeuk'=>$data_uk->kodeuk));
	foreach ($res_data as $data) {
		
		db_update('prognosisskpd')
		->fields(array( 
				'realisasi' => $data->realisasi,
				))
		->condition("kodeo", $data->kodeo, '=')
		->condition("kodeuk", $data_uk->kodeuk, '=')		
		->execute();

		
	}
	$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
			FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and 
			MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'4', ':kodeuk'=>$data_uk->kodeuk));
	foreach ($res_data as $data) {
		
		db_update('prognosisskpd')
		->fields(array( 
				'realisasi' => $data->realisasi,
				))
		->condition("kodeo", $data->kodeo, '=')
		->condition("kodeuk", $data_uk->kodeuk, '=')		
		->execute();

		
	}
	
	if ($data_uk->kodeuk=='00') {
		$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
				FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
				MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'62', ':kodeuk'=>$data_uk->kodeuk));
		foreach ($res_data as $data) {
			
			db_update('prognosisskpd')
			->fields(array( 
					'realisasi' => $data->realisasi,
					))
			->condition("kodeo", $data->kodeo, '=')
			->condition("kodeuk", $data_uk->kodeuk, '=')		
			->execute();


			
		}
		$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
				FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
				MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'61', ':kodeuk'=>$data_uk->kodeuk));
		foreach ($res_data as $data) {
			
			db_update('prognosisskpd')
			->fields(array( 
					'realisasi' => $data->realisasi,
					))
			->condition("kodeo", $data->kodeo, '=')
			->condition("kodeuk", $data_uk->kodeuk, '=')		
			->execute();


			
		}		
	}
}

$res_x = db_query('UPDATE prognosisskpd SET sisa=anggaran-realisasi');

$res_x = db_query('UPDATE prognosisskpd inner join prognosis ON prognosisskpd.kodeo=prognosis.kodeo SET prognosisskpd.persen=prognosis.persen');
$res_x = db_query('UPDATE prognosisskpd SET prognosis=(persen/100)*sisa');

$res_x = db_query('UPDATE prognosisskpd SET sisa=0 WHERE sisa<0');
$res_x = db_query('UPDATE prognosisskpd SET prognosis=realisasi WHERE prognosis<=0');

//KAB*
$res_x = db_query('UPDATE prognosiskab set realisasi=0, prognosis=0');

$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and MONTH(jurnal.tanggal)<=6 GROUP BY LEFT(jurnalitem.kodero, 5)', array(':akun'=>'5'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();

	
}
$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
		FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and 
		MONTH(jurnal.tanggal)<=6 GROUP BY LEFT(jurnalitem.kodero, 5)', array(':akun'=>'4'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();

	
}

$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
		FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
		MONTH(jurnal.tanggal)<=6 AND LEFT(jurnalitem.kodero, 5)', array(':akun'=>'62'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();


	
}
$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
		FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
		MONTH(jurnal.tanggal)<=6 GROUP BY LEFT(jurnalitem.kodero, 5)', array(':akun'=>'61'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();


	
}	

$res_x = db_query('UPDATE prognosiskab SET sisa=anggaran-realisasi');

$res_x = db_query('UPDATE prognosiskab inner join prognosis ON prognosiskab.kodeo=prognosis.kodeo SET prognosiskab.persen=prognosis.persen');
$res_x = db_query('UPDATE prognosiskab SET prognosis=(persen/100)*sisa');

$res_x = db_query('UPDATE prognosiskab SET sisa=0 WHERE sisa<0');
$res_x = db_query('UPDATE prognosiskab SET prognosis=realisasi WHERE prognosis<=0');	
	
}	

function read_anggaran($kodeuk, $kodero) {
	$x = 0;
	
	if (substr($kodero,0,1)=='4') {
		$res = db_query('SELECT jumlah anggaran FROM anggperuk WHERE kodero=:kodero AND kodeuk=:kodeuk', array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));
	} else {
		$res = db_query('SELECT SUM(anggperkeg.jumlah) anggaran FROM anggperkeg INNER JOIN kegiatanskpd ON anggperkeg.kodekeg=kegiatanskpd.kodekeg WHERE anggperkeg.kodero=:kodero AND kegiatanskpd.kodeuk=:kodeuk AND kegiatanskpd.inaktif=0', array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));
		
	}
	foreach ($res as $data) {
		$x = $data->anggaran;		
	}	
	return $x;
}

function read_realisasi_keg($kodeuk, $kodekeg, $uk) {

	$x = 0;
	if ($kodekeg=='4') 
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .kredit-jurnalitem' . $uk . ' .debet) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE jurnal' . $uk . ' .kodeuk=:kodeuk AND MONTH(jurnal' . $uk . ' .tanggal)<=6 AND jurnalitem' . $uk . ' .kodero like :pendapatan', array(':kodeuk'=>$kodeuk, ':pendapatan'=>'4%'));		
	elseif ($kodekeg=='61') 
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .kredit-jurnalitem' . $uk . ' .debet) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE MONTH(jurnal' . $uk . ' .tanggal)<=6 AND jurnalitem' . $uk . ' .kodero like :penerimaan', array(':penerimaan'=>'61%'));		
	elseif ($kodekeg=='62') 
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .debet-jurnalitem' . $uk . ' .kredit) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE MONTH(jurnal' . $uk . ' .tanggal)<=6 AND jurnalitem' . $uk . ' .kodero like :pengeluaran', array(':pengeluaran'=>'62%'));	
	else		
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .debet-jurnalitem' . $uk . ' .kredit) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE jurnal' . $uk . ' .kodekeg=:kodekeg AND MONTH(jurnal' . $uk . ' .tanggal)<=6 AND jurnalitem' . $uk . ' .kodero like :belanja', array(':kodekeg'=>$kodekeg, ':belanja'=>'5%'));		
	foreach ($res as $data) {
		$x = $data->realisasi;		
	}	
	return $x;
}

function read_realisasi_rek($kodeuk, $kodekeg, $kodeo, $uk) {
	$x = 0;
	if ($kodekeg=='4') 
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .kredit-jurnalitem' . $uk . ' .debet) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE jurnal' . $uk . ' .kodeuk=:kodeuk AND jurnalitem' . $uk . ' .kodero LIKE :kodeo AND jurnal' . $uk . ' .tanggal<=:batas', array(':kodeuk'=>$kodeuk, ':kodeo'=>$kodeo . '%', ':batas'=>'2018-06-30'));		
	elseif ($kodekeg=='61') 
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .kredit-jurnalitem' . $uk . ' .debet) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE jurnalitem' . $uk . ' .kodero LIKE :kodeo AND jurnal' . $uk . ' .tanggal<=:batas', array(':kodeo'=>$kodeo . '%', ':batas'=>'2018-06-30'));		
	elseif ($kodekeg=='62') 
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .debet-jurnalitem' . $uk . ' .kredit) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE jurnalitem' . $uk . ' .kodero LIKE :kodeo AND jurnal' . $uk . ' .tanggal<=:batas', array(':kodeo'=>$kodeo . '%', ':batas'=>'2018-06-30'));		
	else
		$res = db_query('SELECT SUM(jurnalitem' . $uk . ' .debet-jurnalitem' . $uk . ' .kredit) as realisasi 
			FROM jurnalitem' . $uk . '  INNER JOIN jurnal' . $uk . '  on jurnalitem' . $uk . ' .jurnalid=jurnal' . $uk . ' .jurnalid WHERE jurnal' . $uk . ' .kodekeg=:kodekeg AND jurnalitem' . $uk . ' .kodero LIKE :kodeo AND jurnal' . $uk . ' .tanggal<=:batas', array(':kodekeg'=>$kodekeg, ':kodeo'=>$kodeo . '%', ':batas'=>'2018-06-30'));		
	foreach ($res as $data) {
		$x = $data->realisasi;		
	}	
	return $x;
}

function apbd_keg_paging() {
	$batas = 15;

	$res_uk = db_query('select kodeuk from {unitkerja} order by kodeuk');
	foreach ($res_uk as $datauk) {
		//drupal_set_message($datauk->kodeuk);

		$num = db_delete('kegiatanbk8')
		  ->condition('kodeuk', $datauk->kodeuk)
		  ->execute();
		
		$batch = 1;
		$i  = 0;
		
		$res_keg = db_query('select kodekeg from {kegiatanskpd} where inaktif=0 and kodeuk=:kodeuk order by kodepro,kodekeg', array(':kodeuk' => $datauk->kodeuk));
		foreach ($res_keg as $datakeg) {
			//drupal_set_message($datakeg->kodekeg);
			
			$lastkeg = $datakeg->kodekeg;
			
			$i++;

			$query = db_insert('kegiatanbk8') // Table name no longer needs {}
				->fields(array(
					  'kodekeg' => $datakeg->kodekeg,
					  'batch' => $batch,
					  'kodeuk' => $datauk->kodeuk,					  
				))
				->execute();	
			
			if ($i == $batas) {
				$batch++ ;
				$i = 0;
			}	
				
		}	//kodekeg	
			
		
	}	//kodeuk
}

function apbd_get_paging($kodeuk) {
	
$i = 0;
$strlink = '';

$batch = arg(3);
if ($batch=='') $batch = '1';

$res_page = db_query('select distinct batch from {kegiatanbk8} where kodeuk=:kodeuk order by batch', array(':kodeuk' => $kodeuk));
foreach ($res_page as $data) {
	$i++;
	
	if ($batch==$data->batch) {
		if ($strlink=='')
			$strlink = '<strong>' . $data->batch . '</strong>';
		else
			$strlink .= ' | <strong>' . $data->batch . '</strong>';
		
	} else {
		if ($strlink=='')
			$strlink = l($data->batch, '/rekeningkeg/prognosis/' . $kodeuk . '/'  . $data->batch, array('attributes' => array('class' => null)));
		else
			$strlink .= ' | ' . l($data->batch, '/rekeningkeg/prognosis/' . $kodeuk . '/'  . $data->batch, array('attributes' => array('class' => null)));
	}
}

if ($kodeuk=='00') $strlink .= ' | ' . l('Pembiayaan', '/rekeningkeg/prognosis/' . $kodeuk . '/999', array('attributes' => array('class' => null)));

if ($i<=1) $strlink = '';
return $strlink; 	
}

function apbd_read_prognosis_pendapatan($kodeuk) {
$jumlah = 0;	
$resx = db_query('select sum(prognosis) as jumlah from {prognosiskeg} where kodeuk=:kodeuk and kodeo like :pendapatan', array(':pendapatan'=>'4%', ':kodeuk'=>$kodeuk));
foreach ($resx as $datax) {
	$jumlah = $datax->jumlah;
}
return $jumlah;	
}

function apbd_read_prognosis_pembiayaan($kodek) {
$jumlah = 0;	
$resx = db_query('select sum(prognosis) as jumlah from {prognosiskeg} where kodeo like :pembiayaan', array(':pembiayaan'=> $kodek . '%'));
foreach ($resx as $datax) {
	$jumlah = $datax->jumlah;
}
return $jumlah;	
}

function apbd_read_prognosis_belanja($kodekeg) {
$jumlah = 0;	
$resx = db_query('select sum(prognosis) as jumlah from {prognosiskeg} where kodekeg=:kodekeg and kodeo like :belanja', array(':belanja'=>'5%', ':kodekeg'=>$kodekeg));
foreach ($resx as $datax) {
	$jumlah = $datax->jumlah;
}
return $jumlah;	
}

?>