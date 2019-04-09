<?php
function laporan_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';

 
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$kodeuk = arg(3);
				$tingkat = arg(4);
				$margin =arg(5);
				$tanggal =arg(6);
				$hal1 = arg(7);
				$marginkiri = arg(8);
				$ttdlaporan = arg(9);
				$cetakpdf = arg(10);
				break;

			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}

	} else {
		$bulan = date('n')-1;		//variable_get('apbdtahun', 0);
		$tingkat = '3';
		$margin = '10';
		$marginkiri = '20';
		$hal1 = '1';
		$tanggal = date('j F Y');
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = 'YY';
		}
		$ttdlaporan = 2;

	}

	if ($bulan=='0') $bulan='12';

	//drupal_set_message($ttdlaporan);

	if ($cetakpdf == 'pdf') {
		$index = arg(11);
		$output = gen_report_realisasi_print_resume($bulan, $kodeuk, $tingkat, $tanggal, $ttdlaporan, $cetakpdf, $index);

		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;

	} else if ($cetakpdf =='pdfp') {
		$output = gen_report_realisasi_print_periodik($bulan, $kodeuk, $tingkat, $tanggal, $ttdlaporan);
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;

	} else if ($cetakpdf =='pad') {
		$output = gen_report_realisasi_print_pendapatan($bulan, $kodeuk, $tingkat, $tanggal);
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;

	} else if ($cetakpdf =='bel') {
		$output = gen_report_realisasi_print_belanja($bulan, $kodeuk, $tingkat, $tanggal);
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;

	} else if ($cetakpdf=='sikd') {
		if ($bulan=='1') {
			$output = gen_report_realisasi_print_sikd1('1', 'ZZ', '3', $margin,$tanggal);
		} else if ($bulan=='2') {
			$output = gen_report_realisasi_print_sikd2('2', 'ZZ', '3', $margin,$tanggal);
		} else if ($bulan=='4') {
			$output = gen_report_realisasi_print_sikd4('4', 'ZZ', '3', $margin,$tanggal);
		} else {
			$output = gen_report_realisasi_print_sikd('3', 'ZZ', '3', $margin,$tanggal);
		}
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;

	} else if ($cetakpdf=='excel') {

		//drupal_set_message('select ' . $cetakpdf);

		//gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, $penandatangan, $cetakpdf) {
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, 0, $cetakpdf);

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Komulatif Excel.xls" );
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";

	}else if ($cetakpdf=='excel2') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Periodik.xls" );
		header("Pragma: no-cache");
		header("Expires: 0");
		$output = gen_report_realisasi_print_periodik($bulan, $kodeuk, $tingkat, $tanggal, $cetakpdf);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";

	} else {
		//drupal_set_message(arg(4));
		if ($kodeuk=='YY')
			$output = '<p>Pilih OPD dan periode laporan.</p>';
		else
			$output = gen_report_realisasi_resume($bulan, $kodeuk, $tingkat, false);
		$output_form = drupal_get_form('laporan_main_form');

		//asli
		/*
		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/' . $ttdlaporan . '/pdf">Realisasi Kumulatif</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri .  '/' . $ttdlaporan . '/pdfp">Realisasi Periodik</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri .  '/' . $ttdlaporan . '/pad">Realisasi Khusus Pendapatan</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri .  '/' . $ttdlaporan . '/bel">Realisasi Khusus Belanja</a></li>' .
					'</ul>' .
				'</div>';
		*/
		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/' . $ttdlaporan . '/pdf">Realisasi</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/' . $ttdlaporan . '/pdf/4">Realisasi - Pendapatan</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/' . $ttdlaporan . '/pdf/5">Realisasi - Belanja</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/' . $ttdlaporan . '/pdf/6">Realisasi - Pembiayaan</a></li>' .
					'</ul>' .
				'</div>';
		$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri .  '/' . $ttdlaporan . '/excel">Realisasi Kumulatif</a></li>' .
						'<li><a href="/laporan/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri .  '/' . $ttdlaporan . '/excel2">Realisasi Periodik</a></li>' .
					'</ul>' .
				'</div>';



		//$btn = '';

		return drupal_render($output_form) . $btn . $output . $btn;

	}

}

function laporan_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$margin = $form_state['values']['margin'];
	$marginkiri = $form_state['values']['marginkiri'];
	$tanggal = $form_state['values']['tanggal'];
	$hal1 = $form_state['values']['hal1'];

	$ttdlaporan= $form_state['values']['ttdlaporan'];

	$uri = 'laporanres/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin . '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri . '/' . $ttdlaporan;
	drupal_goto($uri);

}


function laporan_main_form($form, &$form_state) {

	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = '81';
	}
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('n')-1;
	$tingkat = '3';
	$margin = '10';
	$marginkiri = '20';
	$tanggal = date('j F Y');
	$hal1 = '1';
	$ttdlaporan = '2';

	if(arg(2)!=null){

		$bulan = arg(2);
		$kodeuk = arg(3);
		$tingkat = arg(4);
		$margin =arg(5);
		$tanggal =arg(6);
		$hal1 =arg(7);
		$marginkiri =arg(8);
		$ttdlaporan = arg(9);

	}

	if ($bulan=='0') $bulan='12';

	//drupal_set_message($bulan);

	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat= '|' . $data->namasingkat;
			}
		}
	}

	$arr_bulan = array(
			 '1' => t('JANUARI'),
			 '2' => t('FEBRUARI'),
			 '3' => t('MARET'),
			 '4' => t('APRIL'),
			 '5' => t('MEI'),
			 '6' => t('JUNI'),
			 '7' => t('JULI'),
			 '8' => t('AGUSTUS'),
			 '9' => t('SEPTEMBER'),
			 '10' => t('OKTOBER'),
			 '11' => t('NOVEMBER'),
			 '12' => t('DESEMBER'),
		   );
	$opttingkat = array();
	$opttingkat['3'] = 'JENIS';
	$opttingkat['4'] = 'OBYEK';
	$opttingkat['5'] = 'RINCIAN';

	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $arr_bulan[$bulan] . $namasingkat . '|' . $opttingkat[$tingkat] . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,
	);

	//SKPD
	if (isUserSKPD()) {
		$form['formdata']['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
		);

	} else {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'));
		$query->orderBy('kodedinas', 'ASC');
		$results = $query->execute();
		$optskpd = array();
		$optskpd['ZZ'] = 'SELURUH SKPD';
		if($results){
			foreach($results as $data) {
			  $optskpd[$data->kodeuk] = $data->namasingkat;
			}
		}

		$form['formdata']['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $optskpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,5
			'#default_value' => $kodeuk,
		);
	}


	$form['formdata']['tingkat'] = array(
		'#type' => 'select',
		'#title' =>  t('Tingkat'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $opttingkat,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $tingkat,
	);

	/*
	$form['formdata']['tingkat'] = array(
		'#type' => 'hidden',
		'#title' =>  t('Tingkat'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#options' => $opttingkat,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => '3',
	);
	*/

	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,
		'#options' => $arr_bulan,
	);
	$form['formdata']['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin Atas',
		'#default_value' => $margin,
	);
	$form['formdata']['marginkiri']= array(
		'#type' => 'textfield',
		'#title' => 'Margin Kiri',
		'#default_value' => $marginkiri,
	);
	$form['formdata']['hal1']= array(
		'#type' => 'textfield',
		'#title' => 'Halaman #1',
		'#default_value' => $hal1,
	);
	$form['formdata']['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#default_value' => $tanggal ,
	);

	if (isUserSKPD()) {
		$form['formdata']['ttdlaporan']= array(
			'#type'         => 'value',
			'#value' => '2',
		);
	} else {
		$form['formdata']['setting_table']= array(
			'#prefix' => '<table>',
			 '#suffix' => '</table>',
		);
		$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD', 'SEKRETARIS DAERAH', 'SEKRETARIS DINAS', 'KABID AKUNTANSI');
		$form['formdata']['setting_table']['ttdlaporan']= array(
			'#type'         => 'select',
			'#title' =>  t('PENANDA TANGAN LAPORAN'),
			'#options' => $penandatangan,
			'#default_value'=> $ttdlaporan,
			'#prefix' => '<tr><td style="width:250%; color:black">',
			'#suffix' => '</td>',
			//'#suffix' => "&nbsp;<a href='ttdlaporan' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Setting</a>",

		);
		$form['formdata']['setting_table']['setting']= array(
			'#type'         => 'item',
			'#markup' => "&nbsp;<a href='/ttdlaporan' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-user' aria-hidden='true'></span>KEPALA</a>",
			'#prefix' => '<td style="width:46%">',
			'#suffix' => '</td></tr>',
		);
	}

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($bulan, $kodeuk, $tingkat) {

    //drupal_set_message($bulan);

    if ($bulan=='0') $bulan='12';

    drupal_set_time_limit(0);
    ini_set('memory_limit', '1024M');

    $margin = 10;
    $marginkiri = 20;

    if (isUserSKPD()) {
    	$sufixjurnal = 'uk';
    } else {
    	$sufixjurnal = '';
    }

    $agg_pendapata_total = 0; $agg_pendapata_total_bulanan = 0;
    $agg_belanja_total = 0; $agg_belanja_total_bulanan = 0;
    $agg_pembiayaan_netto = 0; $agg_pembiayaan_netto_bulanan = 0;

    $rea_pendapata_total = 0; $rea_pendapata_total_bulanan = 0;
    $rea_belanja_total = 0; $rea_belanja_total_bulanan = 0;
    $rea_pembiayaan_netto = 0; $rea_pembiayaan_netto_bulanan = 0;

    //TABEL
    $header = array (
    	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
    	array('data' => 'Uraian', 'valign'=>'top'),
    	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
    	array('data' => 'Bulan Ini', 'width' => '90px', 'valign'=>'top'),
    	array('data' => 'Kumulatif', 'width' => '90px', 'valign'=>'top'),
    	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
    	array('data' => '', 'valign'=>'top'),
    	array('data' => '', 'width' => '10px', 'valign'=>'top'),
    );

    $rows = array();

    $tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));
    /*
    if ($kodeuk=='ZZ') {
    	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';
    } else {
    	$tanggal_awal = apbd_tahun() . '-01-01';
    }
    */
    $tanggal_awal = apbd_tahun() . '-01-01';

    // * PENDAPATAN * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
    if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    $query->condition('a.kodea', '4', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = 0; $bulanan = 0;
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$realisasi = $data->realisasi;
    	}
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$bulanan = $data->realisasi;
    	}

    	$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $datas->kodea  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	$agg_pendapata_total = $datas->anggaran;
    	$rea_pendapata_total = $realisasi;
    	$rea_pendapata_total_bulanan = $bulanan;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$sql->condition('k.kodea', $datas->kodea, '=');
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$realisasi = $data->realisasi;
    		}
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$bulanan = $data->realisasi;
    		}

    		$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_kel->kodek  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),

    		);


    		//JENIS
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi = $data->realisasi;
    			}
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$bulanan = $data->realisasi;
    			}

    			//$uri = '/akuntansi/buku/ZZ/'  . $kodero  . '/'  . $kodeuk . '/' . $tglawalx  . '/' . $tglakhirx . '/'  . $koderod;

    			$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_jen->kodej  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

    			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
    				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    				array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    			);

    			if ($tingkat>'3') {
    				//OBYEK
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$realisasi = $data->realisasi;
    					}
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$bulanan = $data->realisasi;
    					}

    					$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    					$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    					$rows[] = array(
    						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
    						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($bulanan) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    						array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    						$realisasi = 0; $bulanan = 0;
    						$sql = db_select('jurnal' . $sufixjurnal, 'j');
    						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    						$sql->condition('ji.kodero', $data_rek->kodero, '=');
    						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    						$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    						$res = $sql->execute();
    						foreach ($res as $data) {
    							$realisasi = $data->realisasi;
    						}
    						$sql = db_select('jurnal' . $sufixjurnal, 'j');
    						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    						$sql->condition('ji.kodero', $data_rek->kodero, '=');
    						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    						$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    						$res = $sql->execute();
    						foreach ($res as $data) {
    							$bulanan = $data->realisasi;
    						}

    						if (($data_rek->anggaran+$realisasi)>0) {
    							$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_rek->kodero  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    							$rows[] = array(
    								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($bulanan) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    								array('data' => '', 'align' => 'right', 'valign'=>'top'),
    							);
    						}
    					}	//obyek

    					}
    				}	//obyek

    			}	//if tingkat obyek
    		}	//jenis


    	}


    }	//foreach ($results as $datas)

    // * BELANJA * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
    if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    $query->condition('keg.inaktif', '0', '=');
    $query->condition('a.kodea', '5', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = 0; $bulanan = 0;
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
    	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    	$sql->condition('keg.inaktif', '0', '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$realisasi = $data->realisasi;
    	}
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    	$sql->condition('keg.inaktif', '0', '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$bulanan = $data->realisasi;
    	}

    	$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $datas->kodea  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	$agg_belanja_total = $datas->anggaran;
    	$rea_belanja_total = $realisasi;
    	$rea_belanja_total_bulanan= $bulanan;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    	$query->condition('keg.inaktif', '0', '=');
    	$query->condition('k.kodea', $datas->kodea, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    		$sql->condition('keg.inaktif', '0', '=');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$realisasi = $data->realisasi;
    		}

    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    		$sql->condition('keg.inaktif', '0', '=');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$bulanan = $data->realisasi;
    		}

    		$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_kel->kodek  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		);

    		//JENIS
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		$query->condition('keg.inaktif', '0', '=');
    		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    			$sql->condition('keg.inaktif', '0', '=');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi = $data->realisasi;
    			}
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    			$sql->condition('keg.inaktif', '0', '=');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$bulanan = $data->realisasi;
    			}

    			$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_jen->kodej  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
    				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    				array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    			);


    			//OBYEK
    			if (($tingkat>'3') || ($data_jen->kodej=='523')) {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->condition('keg.inaktif', '0', '=');
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');

    				//dpq($query);

    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    					$sql->condition('keg.inaktif', '0', '=');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$realisasi = $data->realisasi;
    					}
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    					$sql->condition('keg.inaktif', '0', '=');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$bulanan = $data->realisasi;
    					}

    					$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    					$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    					$rows[] = array(
    						array('data' => $data_oby->kodeo , 'align' => 'left', 'valign'=>'top'),
    						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($bulanan) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    						array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
    						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    						$query->condition('keg.inaktif', '0', '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = 0; $bulanan = 0;
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->condition('keg.inaktif', '0', '=');
    							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								$realisasi = $data->realisasi;
    							}

    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->condition('keg.inaktif', '0', '=');
    							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								$bulanan = $data->realisasi;
    							}

    							$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_rek->kodero  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    							$rows[] = array(
    								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($bulanan) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    								array('data' => '', 'align' => 'right', 'valign'=>'top'),
    							);

    						}	//obyek

    					}	//rekening
    				}	//obyek

    			}	//if obyek
    		}	//jenis


    	}


    }	//foreach ($results as $datas)
    //SURPLUS DEFIIT
    $anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
    $realisasi_netto_bulanan = $rea_pendapata_total_bulanan - $rea_belanja_total_bulanan;
    $realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
    $rows[] = array(
    	array('data' => '', 'align' => 'left', 'valign'=>'top'),
    	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
    	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '<strong>' . apbd_fn($realisasi_netto_bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '', 'align' => 'right', 'valign'=>'top'),
    );

    if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
    	//PEMBIAYAAN
    	$anggaran_netto_p = 0;
    	$realisasi_netto_p = 0;
    	$realisasi_netto_p_bulanan = 0;
    	$realisasi_netto_p_bulanan = 0;

    	$rows[] = array(
    		array('data' => '<strong>6</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    		}
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    		}

    		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		);


    		if ($data_kel->kodek=='61') {
    			$anggaran_netto_p += $data_kel->anggaran;
    			$realisasi_netto_p += $realisasi;
    			$realisasi_netto_p_bulanan += $bulanan;

    		} else	{
    			$anggaran_netto_p -= $data_kel->anggaran;
    			$realisasi_netto_p -= $realisasi;
    			$realisasi_netto_p_bulanan -= $bulanan;
    		}

    		//JENIS
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    			}
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    			}

    			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
    				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    				array('data' => '', 'align' => 'right', 'valign'=>'top'),
    				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    			);

    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    					}
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    					}

    					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    					$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    					$rows[] = array(
    						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
    						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($bulanan) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    						array('data' => '', 'align' => 'right', 'valign'=>'top'),
    						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = 0; $bulanan = 0;
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    							}
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    							}

    							if (($data_rek->anggaran+$realisasi)>0) {
    								$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    								$rows[] = array(
    									array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
    									array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($bulanan) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '', 'align' => 'right', 'valign'=>'top'),
    								);
    							}
    						}	//obyek

    					}
    				}	//obyek

    			}	//tingkat obyek
    		}	//jenis


    	}

    	//SURPLUS DEFIIT
    	$rows[] = array(
    		array('data' => '', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p_bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	//SILPA

    	$anggaran_netto += $anggaran_netto_p;
    	$realisasi_netto += $realisasi_netto_p;
    	$realisasi_netto_bulanan += $realisasi_netto_p_bulanan;

    	$rows[] = array(
    		array('data' => '', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    } elseif (($kodeuk=='00') or ($kodeuk=='36')) {
    	//PEMBIAYAAN
    	$anggaran_netto_p = 0;
    	$realisasi_netto_p = 0;
    	$realisasi_netto_p_bulanan = 0;
    	$realisasi_netto_p_bulanan = 0;

    	$rows[] = array(
    		array('data' => '<strong>6</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	$query->condition('ag.kodeuk', $kodeuk, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		$sql->condition('j.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    		}
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		$sql->condition('j.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    		}

    		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		);


    		if ($data_kel->kodek=='61') {
    			$anggaran_netto_p += $data_kel->anggaran;
    			$realisasi_netto_p += $realisasi;
    			$realisasi_netto_p_bulanan += $bulanan;

    		} else	{
    			$anggaran_netto_p -= $data_kel->anggaran;
    			$realisasi_netto_p -= $realisasi;
    			$realisasi_netto_p_bulanan -= $bulanan;
    		}

    		//JENIS
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->condition('ag.kodeuk', $kodeuk, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			$sql->condition('j.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    			}
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			$sql->condition('j.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    			}

    			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
    				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($bulanan), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    				array('data' => '', 'align' => 'right', 'valign'=>'top'),
    				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    			);

    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				$query->condition('ag.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					$sql->condition('j.kodeuk', $kodeuk, '=');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    					}
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    					$sql->condition('j.kodeuk', $kodeuk, '=');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    					}

    					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    					$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    					$rows[] = array(
    						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
    						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($bulanan) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    						array('data' => '', 'align' => 'right', 'valign'=>'top'),
    						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->condition('ag.kodeuk', $kodeuk, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = 0; $bulanan = 0;
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    							$sql->condition('j.kodeuk', $kodeuk, '=');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    							}
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    							$sql->condition('j.kodeuk', $kodeuk, '=');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    							}

    							if (($data_rek->anggaran+$realisasi)>0) {
    								$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    								$rows[] = array(
    									array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
    									array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($bulanan) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '', 'align' => 'right', 'valign'=>'top'),
    								);
    							}
    						}	//obyek

    					}
    				}	//obyek

    			}	//tingkat obyek
    		}	//jenis


    	}

    	//SURPLUS DEFIIT
    	$rows[] = array(
    		array('data' => '', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p_bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	//SILPA

    	$anggaran_netto += $anggaran_netto_p;
    	$realisasi_netto += $realisasi_netto_p;
    	$realisasi_netto_bulanan += $realisasi_netto_p_bulanan;

    	$rows[] = array(
    		array('data' => '', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_bulanan) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    }




    //RENDER
    $tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

    //return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
    return $tabel_data;

}

function gen_report_realisasi_resume($bulan, $kodeuk, $tingkat) {

    //drupal_set_message($bulan);

    if ($bulan=='0') $bulan='12';

    drupal_set_time_limit(0);
    ini_set('memory_limit', '1024M');

    $margin = 10;
    $marginkiri = 20;

    if (isUserSKPD()) {
    	$sufixjurnal = 'uk';
    } else {
    	$sufixjurnal = ($kodeuk=='ZZ'? '' : $kodeuk);
    }

    $agg_pendapata_total = 0; 
    $agg_belanja_total = 0; 
    $agg_pembiayaan_netto = 0; 

    $rea_pendapata_total = 0; 
    $rea_belanja_total = 0; 
    $rea_pembiayaan_netto = 0; 

    //TABEL
    $header = array (
    	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
    	array('data' => 'Uraian', 'valign'=>'top'),
    	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
    	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
    	array('data' => 'Validasi', 'width' => '90px', 'valign'=>'top'),
    	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
    	array('data' => '', 'valign'=>'top'),
    	array('data' => '', 'width' => '10px', 'valign'=>'top'),
    );

    $rows = array();

    $tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));
    /*
    if ($kodeuk=='ZZ') {
    	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';
    } else {
    	$tanggal_awal = apbd_tahun() . '-01-01';
    }
    */
    $tanggal_awal = apbd_tahun() . '-01-01';

    // * PENDAPATAN * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    $query->condition('a.kodea', '4', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi_v = 0; 
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$realisasi_v = $data->realisasi;
    	}
		
		$realisasi = $datas->realisasi;
    	$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $datas->kodea  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_v) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	$agg_pendapata_total = $datas->anggaran;
    	$rea_pendapata_total = $realisasi;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$sql->condition('k.kodea', $datas->kodea, '=');
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
			
			$realisasi = $data_kel->realisasi;
			
    		$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_kel->kodek  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),

    		);


    		//JENIS
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

				$realisasi = $data_jen->realisasi;

    			//$uri = '/akuntansi/buku/ZZ/'  . $kodero  . '/'  . $kodeuk . '/' . $tglawalx  . '/' . $tglakhirx . '/'  . $koderod;

    			$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_jen->kodej  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

    			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
    				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
    				array('data' => '', 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    				array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    			);

    			if ($tingkat>'3') {
    				//OBYEK
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

						$realisasi = $data_oby->realisasi;

    					$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    					$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    					$rows[] = array(
    						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
    						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => '' , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    						array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

							$realisasi = $data_rek->realisasi;

    						if (($data_rek->anggaran+$realisasi)>0) {
    							$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_rek->kodero  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    							$rows[] = array(
    								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    								array('data' => '', 'align' => 'right', 'valign'=>'top'),
    							);
    						}
    					}	//obyek

    					}
    				}	//obyek

    			}	//if tingkat obyek
    		}	//jenis


    	}


    }	//foreach ($results as $datas)

    // * BELANJA * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    $query->condition('keg.inaktif', '0', '=');
    $query->condition('a.kodea', '5', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi_v = 0; 
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
    	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    	$sql->condition('keg.inaktif', '0', '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$realisasi_v = $data->realisasi;
    	}

		$realisasi = $datas->realisasi;
    	$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $datas->kodea  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn($realisasi_v) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    		array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	$agg_belanja_total = $datas->anggaran;
    	$rea_belanja_total = $realisasi;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    	$query->condition('keg.inaktif', '0', '=');
    	$query->condition('k.kodea', $datas->kodea, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {

    		$realisasi_v = '';
			/*
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    		$sql->condition('keg.inaktif', '0', '=');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$realisasi_v = $data->realisasi;
    		}
			*/
			$realisasi = $data_kel->realisasi;

    		$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_kel->kodek  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi_v) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		);

    		//JENIS
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		$query->condition('keg.inaktif', '0', '=');
    		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {
				
    			$realisasi_v = ''; 
    			/*
				$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    			$sql->condition('keg.inaktif', '0', '=');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi_v = $data->realisasi;
    			}
				*/
				
    			$realisasi = $data_jen->realisasi;

    			$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_jen->kodej  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
    				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi_v), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    				array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    			);


    			//OBYEK
    			if (($tingkat>'3') || ($data_jen->kodej=='523')) {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->condition('keg.inaktif', '0', '=');
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');

    				//dpq($query);

    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = $data_oby->realisasi;

    					$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    					$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    					$rows[] = array(
    						array('data' => $data_oby->kodeo , 'align' => 'left', 'valign'=>'top'),
    						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => '' , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    						array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
    						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    						$query->condition('keg.inaktif', '0', '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

								$realisasi = $data_rek->realisasi;

    							$skpd = ($kodeuk=='ZZ'? l('SKPD', '/laporandetiluk/filter/'  . $data_rek->kodero  . '/' . $bulan . '/' . $margin, array('attributes' => array('class' => null))) : '');
    							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    							$rows[] = array(
    								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '', 'align' => 'right', 'valign'=>'top'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
    								array('data' => $skpd, 'align' => 'right', 'valign'=>'top'),
    								array('data' => '', 'align' => 'right', 'valign'=>'top'),
    							);

    						}	//obyek

    					}	//rekening
    				}	//obyek

    			}	//if obyek
    		}	//jenis


    	}


    }	//foreach ($results as $datas)
    //SURPLUS DEFIIT
    $anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
    $realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
	
    $rows[] = array(
    	array('data' => '', 'align' => 'left', 'valign'=>'top'),
    	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
    	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	array('data' => '', 'align' => 'right', 'valign'=>'top'),
    );

    if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
    	//PEMBIAYAAN
    	$anggaran_netto_p = 0;
    	$realisasi_netto_p = 0;

    	$rows[] = array(
    		array('data' => '<strong>6</strong>', 'align' => 'left', 'valign'=>'top'),
    		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		array('data' => '', 'align' => 'right', 'valign'=>'top'),
    	);

    	//KELOMPOK
		
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
		
		//dpq($query);
		
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
			$realisasi_v = 0; 
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
				$realisasi_v = ($data_kel->kodek=='61'? $data->kreditdebet : $data->debetkredit); 
    		}
			
			
			$realisasi = $data_kel->realisasi;
    		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn($realisasi_v) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    			array('data' => '', 'align' => 'right', 'valign'=>'top'),
    		);


    		if ($data_kel->kodek=='61') {
    			$anggaran_netto_p += $data_kel->anggaran;
    			$realisasi_netto_p += $realisasi;

    		} else	{
    			$anggaran_netto_p -= $data_kel->anggaran;
    			$realisasi_netto_p -= $realisasi;
    		}

    		//JENIS
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi_v = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
    			}
				
    			$realisasi = $data_jen->realisasi;

    			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
    				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn($realisasi_v), 'align' => 'right', 'valign'=>'top'),
    				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    				array('data' => '', 'align' => 'right', 'valign'=>'top'),
    				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    			);

    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {


						$sql = db_select('jurnal' . $sufixjurnal, 'j');
						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
						$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
						$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
						$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasi_v = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
						}
						
						$realisasi = $data_oby->realisasi;
						
    					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    					$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_oby->kodeo . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));

    					$rows[] = array(
    						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
    						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn($realisasi_v), 'align' => 'right', 'valign'=>'top'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
    						array('data' => '', 'align' => 'right', 'valign'=>'top'),
    						array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
					
							$query = db_select('rincianobyek', 'ro');
							$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
							$query->fields('ro', array('kodero', 'uraian'));
							$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
							$query->condition('ro.kodeo', $data_oby->kodeo, '=');
							$query->groupBy('ro.kodero');
							$query->orderBy('ro.kodero');
							$results_rek = $query->execute();
							foreach ($results_rek as $data_rek) {

								$sql = db_select('jurnal' . $sufixjurnal, 'j');
								$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
								$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
								$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
								$sql->condition('ji.kodero', $data_rek->kodero, '=');
								$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
								$res = $sql->execute();
								foreach ($res as $data) {
									$realisasi_v = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
								}

							
								$realisasi = $data_rek->realisasi;
    							if (($data_rek->anggaran+$realisasi)>0) {
    								$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
    								$rows[] = array(
    									array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
    									array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn($realisasi_v) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '', 'align' => 'right', 'valign'=>'top'),
    									array('data' => '', 'align' => 'right', 'valign'=>'top'),
    								);
    							}
							}
    					}	//obyek

    				}	//obyek

    			}	//tingkat obyek
    		}	//jenis
		}

    }
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);

	//SILPA

	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;

	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);


    //RENDER
    $tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

    //return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
    return $tabel_data;

}


function gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, $penandatangan, $cetakpdf) {

    //drupal_set_message('x . ' . $cetakpdf);

    drupal_set_time_limit(0);
    ini_set('memory_limit', '1024M');



    if (isUserSKPD()) {
    	$sufixjurnal = 'uk';
    } else {
    	$sufixjurnal = '';
    }

    if ($kodeuk == 'ZZ') {
    	$skpd = 'KABUPATEN JEPARA';

    	//	$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD','SEKRETARIS DINAS');

    	//drupal_set_message('p . ' . $penandatangan);

    	if ($penandatangan=='0') {

    		$pimpinanatasnama = '';
    		$pimpinannama = variable_get('bupatinama', '');
    		$pimpinanjabatan = variable_get('bupatijabatan', '');
    		$pimpinannip = '';

    	} elseif ($penandatangan=='1') {
    		$pimpinanatasnama = variable_get('wabupjabatanatasnama', '');
    		$pimpinannama = variable_get('wabupnama', '');
    		$pimpinanjabatan = variable_get('wabupjabatan', '');
    		$pimpinannip = '';

    	} elseif ($penandatangan=='2') {
    		$pimpinanatasnama = variable_get('kepalajabatanatasnama', '');
    		$pimpinannama = variable_get('kepalanama', '');
    		$pimpinanjabatan = variable_get('kepalajabatan', '');
    		$pimpinannip = variable_get('kepalanip', '');

    	} elseif ($penandatangan=='3') {
    		$pimpinannama = variable_get('setdanama', '');
    		$pimpinanjabatan = variable_get('setdajabatan', '');
    		$pimpinannip = variable_get('setdanip', '');
    		$pimpinanatasnama = variable_get('setdajabatanatasnama', '');

    	} elseif ($penandatangan=='4') {
    		$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
    		$pimpinannama = variable_get('sekretarisnama', '');
    		$pimpinanjabatan = variable_get('sekretarisjabatan', '');
    		$pimpinannip = variable_get('sekretarisnip', '');

    	} elseif ($penandatangan=='5') {
    		$pimpinannama = variable_get('kabidnama', '');
    		$pimpinanjabatan = variable_get('kabidjabatan', '');
    		$pimpinannip = variable_get('kabidnip', '');
    		$pimpinanatasnama = variable_get('kabidjabatanatasnama', '');

    	} else {
    		$pimpinanatasnama = '';
    		$pimpinannama = apbd_bud_nama();
    		$pimpinanjabatan = apbd_bud_jabatan();
    		$pimpinannip = apbd_bud_nip();
    	}


    } else {
    	$pimpinanatasnama = '';

    	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
    	foreach ($results as $datas) {
    		$skpd = $datas->namauk;
    		$pimpinannama = $datas->pimpinannama;
    		$pimpinanjabatan = $datas->pimpinanjabatan;
    		$pimpinannip = $datas->pimpinannip;
    	};
    }

    $rows[] = array(
    	array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
    );
    $rows[] = array(
    	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
    );

    if (($bulan=='0') or ($bulan=='12')) {
    	$rows[] = array(
    		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
    	);

    } else {
    	$rows[] = array(
    		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
    	);
    }

    $tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

    $agg_pendapata_total = 0;
    $agg_belanja_total = 0;
    $agg_pembiayaan_netto = 0;

    $rea_pendapata_total = 0;
    $rea_belanja_total = 0;
    $rea_pembiayaan_netto = 0;

    $rows = null;
    //TABEL


    if ($cetakpdf=='excel') {
    	$header[] = array (
    		array('data' => 'KODE','width' => '45px',  'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'URAIAN','width' => '225px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'ANGGARAN (RP)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI (RP)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI (%)', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'LEBIH/(KURANG)', 'width' => '70px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    	);

    } else {

    	//$header = array();
    	$header[] = array (
    		array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI', 'width' => '100px', 'colspan'=>2,  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'LEBIH/(KURANG)', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    	);
    	$header[] = array (
    		array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
    		array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
    	);

    }

    $rows = array();

    // * PENDAPATAN * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
    if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    $query->condition('a.kodea', '4', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');

    //dpq ($query);
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = 0; $bulanan = 0;
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$realisasi = $data->realisasi;
    	}
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi-$datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    	);

    	$agg_pendapata_total = $datas->anggaran;
    	$rea_pendapata_total = $realisasi;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$sql->condition('k.kodea', $datas->kodea, '=');
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$realisasi = $data->realisasi;
    		}

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    		);

    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi-$data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);


    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi = $data->realisasi;
    			}

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi-$data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);

    			if ($tingkat>'3') {
    				//OBYEK
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$realisasi = $data->realisasi;
    					}

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi-$data_oby->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    						$realisasi = 0; $bulanan = 0;
    						$sql = db_select('jurnal' . $sufixjurnal, 'j');
    						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
    						$sql->condition('ji.kodero', $data_rek->kodero, '=');
    						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
    						$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    						$res = $sql->execute();
    						foreach ($res as $data) {
    							$realisasi = $data->realisasi;
    						}

    						if (($data_rek->anggaran+$realisasi)>0) {
    							$rows[] = array(
    								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi-$data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    							);
    						}

    					}	//obyek

    					}
    				}	//obyek

    			}	//if tingkat obyek
    		}	//jenis


    	}

    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    }	//foreach ($results as $datas)

    // * BELANJA * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
    if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    $query->condition('keg.inaktif', '0', '=');
    $query->condition('a.kodea', '5', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = 0; $bulanan = 0;
    	$sql = db_select('jurnal' . $sufixjurnal, 'j');
    	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
    	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
    	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    	$res = $sql->execute();
    	foreach ($res as $data) {
    		$realisasi = $data->realisasi;
    	}
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    	);

    	$agg_belanja_total = $datas->anggaran;
    	$rea_belanja_total = $realisasi;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    	$query->condition('keg.inaktif', '0', '=');
    	$query->condition('k.kodea', $datas->kodea, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			$realisasi = $data->realisasi;
    		}

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    		);
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);

    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}

    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->condition('keg.inaktif', '0', '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				$realisasi = $data->realisasi;
    			}

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);


    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						$realisasi = $data->realisasi;
    					}

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' =>apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
    						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = 0; $bulanan = 0;
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								$realisasi = $data->realisasi;
    							}

    							$rows[] = array(
    								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran- $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    							);

    						}	//obyek

    					}	//rekening
    				}	//obyek

    			}	//if obyek
    		}	//jenis


    	}


    }	//foreach ($results as $datas)
    //SURPLUS DEFIIT
    $anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
    $realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
    $rows[] = array(
    	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    );


    if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//PEMBIAYAAN
    	$anggaran_netto_p = 0;
    	$realisasi_netto_p = 0;

    	$rows[] = array(
    		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			if ($data_kel->kodek=='61')
    				$realisasi = $data->kreditdebet;
    			else
    				$realisasi = $data->debetkredit;
    		}

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    		);
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);


    		if ($data_kel->kodek=='61') {
    			$anggaran_netto_p += $data_kel->anggaran;
    			$realisasi_netto_p += $realisasi;

    		} else	{
    			$anggaran_netto_p -= $data_kel->anggaran;
    			$realisasi_netto_p -= $realisasi;
    		}

    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				//$realisasi = $data->realisasi;

    				if ($data_kel->kodek=='61')
    					$realisasi = $data->kreditdebet;
    				else
    					$realisasi = $data->debetkredit;
    			}

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);

    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						if ($data_kel->kodek=='61')
    							$realisasi = $data->kreditdebet;
    						else
    							$realisasi = $data->debetkredit;
    					}

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran - $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = 0; $bulanan = 0;
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								if ($data_kel->kodek=='61')
    									$realisasi = $data->kreditdebet;
    								else
    									$realisasi = $data->debetkredit;
    							}

    							if (($data_rek->anggaran+$realisasi)>0) {
    								$rows[] = array(
    									array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    									array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1($data_rek->anggaran - $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								);
    							}

    						}	//obyek

    					}
    				}	//obyek

    			}	//tingkat obyek
    		}	//jenis


    	}

    	//SURPLUS DEFIIT
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    	);

    	//SILPA

    	$anggaran_netto += $anggaran_netto_p;
    	$realisasi_netto += $realisasi_netto_p;
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
    		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
    	);

    } elseif (($kodeuk=='36') or ($kodeuk=='00')) {
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//PEMBIAYAAN
    	$anggaran_netto_p = 0;
    	$realisasi_netto_p = 0;

    	$rows[] = array(
    		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->condition('ag.kodeuk', $kodeuk, '=');
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = 0; $bulanan = 0;
    		$sql = db_select('jurnal' . $sufixjurnal, 'j');
    		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    		$sql->condition('j.kodeuk', $kodeuk, '=');
    		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
    		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    		$res = $sql->execute();
    		foreach ($res as $data) {
    			if ($data_kel->kodek=='61')
    				$realisasi = $data->kreditdebet;
    			else
    				$realisasi = $data->debetkredit;
    		}

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    		);
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);


    		if ($data_kel->kodek=='61') {
    			$anggaran_netto_p += $data_kel->anggaran;
    			$realisasi_netto_p += $realisasi;

    		} else	{
    			$anggaran_netto_p -= $data_kel->anggaran;
    			$realisasi_netto_p -= $realisasi;
    		}

    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->condition('ag.kodeuk', $kodeuk, '=');
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = 0; $bulanan = 0;
    			$sql = db_select('jurnal' . $sufixjurnal, 'j');
    			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    			$sql->condition('j.kodeuk', $kodeuk, '=');
    			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
    			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    			$res = $sql->execute();
    			foreach ($res as $data) {
    				//$realisasi = $data->realisasi;

    				if ($data_kel->kodek=='61')
    					$realisasi = $data->kreditdebet;
    				else
    					$realisasi = $data->debetkredit;
    			}

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);

    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    				$query->condition('ag.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = 0; $bulanan = 0;
    					$sql = db_select('jurnal' . $sufixjurnal, 'j');
    					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    					$sql->condition('j.kodeuk', $kodeuk, '=');
    					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
    					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    					$res = $sql->execute();
    					foreach ($res as $data) {
    						if ($data_kel->kodek=='61')
    							$realisasi = $data->kreditdebet;
    						else
    							$realisasi = $data->debetkredit;
    					}

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran - $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->condition('ag.kodeuk', $kodeuk, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = 0; $bulanan = 0;
    							$sql = db_select('jurnal' . $sufixjurnal, 'j');
    							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
    							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
    							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
    							$sql->condition('j.kodeuk', $kodeuk, '=');
    							$sql->condition('ji.kodero', $data_rek->kodero, '=');
    							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
    							$res = $sql->execute();
    							foreach ($res as $data) {
    								if ($data_kel->kodek=='61')
    									$realisasi = $data->kreditdebet;
    								else
    									$realisasi = $data->debetkredit;
    							}

    							if (($data_rek->anggaran+$realisasi)>0) {
    								$rows[] = array(
    									array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    									array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1($data_rek->anggaran - $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								);
    							}

    						}	//obyek

    					}
    				}	//obyek

    			}	//tingkat obyek
    		}	//jenis


    	}

    	//SURPLUS DEFIIT
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    	);

    	//SILPA

    	$anggaran_netto += $anggaran_netto_p;
    	$realisasi_netto += $realisasi_netto_p;
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
    		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
    	);

    }

    	if (isUserSKPD())
    		$cetakttd = true;
    	else
    		$cetakttd = ($kodeuk=='ZZ'? true: false );

    	if($cetakttd) {
    			$rows[] = array(
    				array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

    			);
    			$rows[] = array(
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
    						array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

    					);

    			if ($pimpinanatasnama != '') {
    				$rows[] = array(
    							array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
    							array('data' => $pimpinanatasnama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
    						);
    			}
    			$rows[] = array(
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
    						array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

    					);
    			$rows[] = array(
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

    					);
    			$rows[] = array(
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

    					);
    			$rows[] = array(
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

    					);
    			$rows[] = array(
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
    						array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
    					);

    			if ($pimpinannip != '') {
    				$rows[] = array(
    							array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
    							array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
    						);
    			}

    			$rows[] = array(
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
    						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
    					);
    	}


    //RENDER
    //$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
    $tabel_data .= createT($header, $rows);

    //return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
    return $tabel_data;

}


function gen_report_realisasi_print_resume($bulan, $kodeuk, $tingkat, $tanggal, $penandatangan, $cetakpdf, $index) {

    //drupal_set_message('x . ' . $cetakpdf);

    drupal_set_time_limit(0);
    ini_set('memory_limit', '1024M');

	if ($index=='') $index = '0';

    if (isUserSKPD()) {
    	$sufixjurnal = 'uk';
    } else {
    	$sufixjurnal = ($kodeuk=='ZZ'? '' : $kodeuk);
    }

    if ($kodeuk == 'ZZ') {
    	$skpd = 'KABUPATEN JEPARA';

    	//	$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD','SEKRETARIS DINAS');

    	//drupal_set_message('p . ' . $penandatangan);

    	if ($penandatangan=='0') {

    		$pimpinanatasnama = '';
    		$pimpinannama = variable_get('bupatinama', '');
    		$pimpinanjabatan = variable_get('bupatijabatan', '');
    		$pimpinannip = '';

    	} elseif ($penandatangan=='1') {
    		$pimpinanatasnama = variable_get('wabupjabatanatasnama', '');
    		$pimpinannama = variable_get('wabupnama', '');
    		$pimpinanjabatan = variable_get('wabupjabatan', '');
    		$pimpinannip = '';

    	} elseif ($penandatangan=='2') {
    		$pimpinanatasnama = variable_get('kepalajabatanatasnama', '');
    		$pimpinannama = variable_get('kepalanama', '');
    		$pimpinanjabatan = variable_get('kepalajabatan', '');
    		$pimpinannip = variable_get('kepalanip', '');

    	} elseif ($penandatangan=='3') {
    		$pimpinannama = variable_get('setdanama', '');
    		$pimpinanjabatan = variable_get('setdajabatan', '');
    		$pimpinannip = variable_get('setdanip', '');
    		$pimpinanatasnama = variable_get('setdajabatanatasnama', '');

    	} elseif ($penandatangan=='4') {
    		$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
    		$pimpinannama = variable_get('sekretarisnama', '');
    		$pimpinanjabatan = variable_get('sekretarisjabatan', '');
    		$pimpinannip = variable_get('sekretarisnip', '');

    	} elseif ($penandatangan=='5') {
    		$pimpinannama = variable_get('kabidnama', '');
    		$pimpinanjabatan = variable_get('kabidjabatan', '');
    		$pimpinannip = variable_get('kabidnip', '');
    		$pimpinanatasnama = variable_get('kabidjabatanatasnama', '');

    	} else {
    		$pimpinanatasnama = '';
    		$pimpinannama = apbd_bud_nama();
    		$pimpinanjabatan = apbd_bud_jabatan();
    		$pimpinannip = apbd_bud_nip();
    	}


    } else {
    	$pimpinanatasnama = '';

    	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
    	foreach ($results as $datas) {
    		$skpd = $datas->namauk;
    		$pimpinannama = $datas->pimpinannama;
    		$pimpinanjabatan = $datas->pimpinanjabatan;
    		$pimpinannip = $datas->pimpinannip;
    	};
    }
	
	if (($index=='0') or ($index=='4')) {
		$rows[] = array(
			array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
		);
		$rows[] = array(
			array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
		);

		if (($bulan=='0') or ($bulan=='12')) {
			$rows[] = array(
				array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
			);

		} else {
			$rows[] = array(
				array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
			);
		}

		$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
	} else {
		$tabel_data = '';
	}
    $agg_pendapata_total = 0;
    $agg_belanja_total = 0;
    $agg_pembiayaan_netto = 0;

    $rea_pendapata_total = 0;
    $rea_belanja_total = 0;
    $rea_pembiayaan_netto = 0;

    $rows = null;
    //TABEL


    if ($cetakpdf=='excel') {
    	$header[] = array (
    		array('data' => 'KODE','width' => '45px',  'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'URAIAN','width' => '225px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'ANGGARAN (RP)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI (RP)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI (%)', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'LEBIH/(KURANG)', 'width' => '70px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    	);

    } else {

    	//$header = array();
    	$header[] = array (
    		array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI', 'width' => '100px', 'colspan'=>2,  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'LEBIH/', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
    	);
    	$header[] = array (
    		array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
    		array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
    		array('data' => '(KURANG)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
			
    	);

    }

    $rows = array();


    //AKUN
    // * PENDAPATAN * //
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    $query->condition('a.kodea', '4', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');

    //dpq ($query);
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = $datas->realisasi;
    	$agg_pendapata_total = $datas->anggaran;
    	$rea_pendapata_total = $realisasi;
		
		if (($index=='0') or ($index=='4')) {

    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi-$datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    	);


    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->condition('k.kodea', $datas->kodea, '=');
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = $data_kel->realisasi;

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:60%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    		);

    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi-$data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);


    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

				if ($tingkat>3) {
					$rows[] = array(
						array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:45%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
					);
				}		
    			$realisasi = $data_jen->realisasi;
			
    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi-$data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);

    			if ($tingkat>'3') {
    				//OBYEK
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

						if ($tingkat>4) {
							$rows[] = array(
								array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
							);
						}						
    					$realisasi = $data_oby->realisasi;

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    						array('data' => $data_oby->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi-$data_oby->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    						$realisasi = $data_rek->realisasi;

    						if (($data_rek->anggaran+$realisasi)>0) {
    							$rows[] = array(
    								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi-$data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    							);
    						}

    					}	//obyek

    					}
    				}	//obyek

    			}	//if tingkat obyek
    		}	//jenis


    	}
		
		}	//INDEX
		
		if ($index=='4')  {
			$rows[] = array(
				array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			);			

		} else  {
			$rows[] = array(
				array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
		}

    }	//foreach ($results as $datas)
	
	
	
    // * BELANJA * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    $query->condition('keg.inaktif', '0', '=');
    $query->condition('a.kodea', '5', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = $datas->realisasi;
    	$agg_belanja_total = $datas->anggaran;
    	$rea_belanja_total = $realisasi;
		//SURPLUS DEFIIT
		$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
		$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
		
		if (($index=='0') or ($index=='5')) {
    	$rows[] = array(
    		array('data' => '<s trong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    	);


    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    	$query->condition('keg.inaktif', '0', '=');
    	$query->condition('k.kodea', $datas->kodea, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = $data_kel->realisasi;

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:60%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    		);
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);

    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
				
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}

    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->condition('keg.inaktif', '0', '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

				if ($tingkat>3) {
					$rows[] = array(
						array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:45%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
					);
				}			
    			$realisasi = $data_jen->realisasi;

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);


    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

						if ($tingkat>4) {
							$rows[] = array(
								array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
							);
						}					
    					$realisasi = $data_oby->realisasi;

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    						array('data' => $data_oby->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' =>apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
    						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = $data_rek->realisasi;

    							$rows[] = array(
    								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    								array('data' => '<em>'. $data_rek->uraian . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran- $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    							);

    						}	//obyek

    					}	//rekening
    				}	//obyek

    			}	//if obyek
    		}	//jenis


    	}

		$rows[] = array(
			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:45%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
		);		
		$rows[] = array(
			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		); 
		$rows[] = array(
			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		}	//INDEX
    }	//foreach ($results as $datas)

	
	if (($index=='0') or ($index=='6')) {
    if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//PEMBIAYAAN
    	$anggaran_netto_p = 0;
    	$realisasi_netto_p = 0;

    	$rows[] = array(
    		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = $data_kel->realisasi;

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:60%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:60%;border-right:1px solid black;'),
    		);
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);


    		if ($data_kel->kodek=='61') {
    			$anggaran_netto_p += $data_kel->anggaran;
    			$realisasi_netto_p += $realisasi;

    		} else	{
    			$anggaran_netto_p -= $data_kel->anggaran;
    			$realisasi_netto_p -= $realisasi;
    		}

    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
				
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

				if ($tingkat>3) {
					$rows[] = array(
						array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:45%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
						array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
					);
				}
				
    			$realisasi = $data_jen->realisasi;

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);

    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

						if ($tingkat>4) {
							$rows[] = array(
								array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
								array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
							);
						}					
    					$realisasi = $data_oby->realisasi;

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    						array('data' => $data_oby->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran - $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = $data_rek->realisasi;

    							if (($data_rek->anggaran+$realisasi)>0) {
    								$rows[] = array(
    									array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    									array('data' => '<em>'. $data_rek->uraian . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1($data_rek->anggaran - $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								);
    							}

    						}	//obyek

    					}
    				}	//obyek

    			}	//tingkat obyek
    		}	//jenis


    	}

		$rows[] = array(
			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:45%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:45%;border-right:1px solid black;'),
		);
    	//SURPLUS DEFIIT
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    	);
		$rows[] = array(
			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
		);		

    	//SILPA

    	$anggaran_netto += $anggaran_netto_p;
    	$realisasi_netto += $realisasi_netto_p;
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;'),
    	);
		$rows[] = array(
			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;border-bottom:1px solid black;'),
		);			

		} 

	}	//INDEX

	if (isUserSKPD())
		$cetakttd = true;
	else {
		if (($index=='0') or ($index=='6')) 
			$cetakttd = ($kodeuk=='ZZ'? true: false );
		else
			$cetakttd = false;
	}
	
	if($cetakttd) {
			$rows[] = array(
				array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

			);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

					);

			if ($pimpinanatasnama != '') {
				$rows[] = array(
							array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
							array('data' => $pimpinanatasnama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
						);
			}
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
					);

			if ($pimpinannip != '') {
				$rows[] = array(
							array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
							array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
						);
			}

			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
					);
	}


    //RENDER
    //$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
    $tabel_data .= createT($header, $rows);

    //return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
    return $tabel_data;

}



function gen_report_realisasi_print_resumeX($bulan, $kodeuk, $tingkat, $tanggal, $penandatangan, $cetakpdf) {

    //drupal_set_message('x . ' . $cetakpdf);

    drupal_set_time_limit(0);
    ini_set('memory_limit', '1024M');



    if (isUserSKPD()) {
    	$sufixjurnal = 'uk';
    } else {
    	$sufixjurnal = ($kodeuk=='ZZ'? '' : $kodeuk);
    }

    if ($kodeuk == 'ZZ') {
    	$skpd = 'KABUPATEN JEPARA';

    	//	$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD','SEKRETARIS DINAS');

    	//drupal_set_message('p . ' . $penandatangan);

    	if ($penandatangan=='0') {

    		$pimpinanatasnama = '';
    		$pimpinannama = variable_get('bupatinama', '');
    		$pimpinanjabatan = variable_get('bupatijabatan', '');
    		$pimpinannip = '';

    	} elseif ($penandatangan=='1') {
    		$pimpinanatasnama = variable_get('wabupjabatanatasnama', '');
    		$pimpinannama = variable_get('wabupnama', '');
    		$pimpinanjabatan = variable_get('wabupjabatan', '');
    		$pimpinannip = '';

    	} elseif ($penandatangan=='2') {
    		$pimpinanatasnama = variable_get('kepalajabatanatasnama', '');
    		$pimpinannama = variable_get('kepalanama', '');
    		$pimpinanjabatan = variable_get('kepalajabatan', '');
    		$pimpinannip = variable_get('kepalanip', '');

    	} elseif ($penandatangan=='3') {
    		$pimpinannama = variable_get('setdanama', '');
    		$pimpinanjabatan = variable_get('setdajabatan', '');
    		$pimpinannip = variable_get('setdanip', '');
    		$pimpinanatasnama = variable_get('setdajabatanatasnama', '');

    	} elseif ($penandatangan=='4') {
    		$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
    		$pimpinannama = variable_get('sekretarisnama', '');
    		$pimpinanjabatan = variable_get('sekretarisjabatan', '');
    		$pimpinannip = variable_get('sekretarisnip', '');

    	} elseif ($penandatangan=='5') {
    		$pimpinannama = variable_get('kabidnama', '');
    		$pimpinanjabatan = variable_get('kabidjabatan', '');
    		$pimpinannip = variable_get('kabidnip', '');
    		$pimpinanatasnama = variable_get('kabidjabatanatasnama', '');

    	} else {
    		$pimpinanatasnama = '';
    		$pimpinannama = apbd_bud_nama();
    		$pimpinanjabatan = apbd_bud_jabatan();
    		$pimpinannip = apbd_bud_nip();
    	}


    } else {
    	$pimpinanatasnama = '';

    	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
    	foreach ($results as $datas) {
    		$skpd = $datas->namauk;
    		$pimpinannama = $datas->pimpinannama;
    		$pimpinanjabatan = $datas->pimpinanjabatan;
    		$pimpinannip = $datas->pimpinannip;
    	};
    }
	
    $rows[] = array(
    	array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
    );
    $rows[] = array(
    	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
    );

    if (($bulan=='0') or ($bulan=='12')) {
    	$rows[] = array(
    		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
    	);

    } else {
    	$rows[] = array(
    		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
    	);
    }

    $tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

    $agg_pendapata_total = 0;
    $agg_belanja_total = 0;
    $agg_pembiayaan_netto = 0;

    $rea_pendapata_total = 0;
    $rea_belanja_total = 0;
    $rea_pembiayaan_netto = 0;

    $rows = null;
    //TABEL


    if ($cetakpdf=='excel') {
    	$header[] = array (
    		array('data' => 'KODE','width' => '45px',  'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'URAIAN','width' => '225px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'ANGGARAN (RP)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI (RP)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI (%)', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'LEBIH/(KURANG)', 'width' => '70px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    	);

    } else {

    	//$header = array();
    	$header[] = array (
    		array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'REALISASI', 'width' => '100px', 'colspan'=>2,  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    		array('data' => 'LEBIH/(KURANG)', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
    	);
    	$header[] = array (
    		array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
    		array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
    	);

    }

    $rows = array();

    // * PENDAPATAN * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    $query->condition('a.kodea', '4', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');

    //dpq ($query);
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = $datas->realisasi;

    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi-$datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    	);

    	$agg_pendapata_total = $datas->anggaran;
    	$rea_pendapata_total = $realisasi;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->condition('k.kodea', $datas->kodea, '=');
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = $data_kel->realisasi;

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    		);

    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi-$data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);


    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = $data_jen->realisasi;

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi-$data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);

    			if ($tingkat>'3') {
    				//OBYEK
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = $data_oby->realisasi;

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi-$data_oby->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    						$realisasi = $data_rek->realisasi;

    						if (($data_rek->anggaran+$realisasi)>0) {
    							$rows[] = array(
    								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi-$data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    							);
    						}

    					}	//obyek

    					}
    				}	//obyek

    			}	//if tingkat obyek
    		}	//jenis


    	}

    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    }	//foreach ($results as $datas)
	
	
    // * BELANJA * //
    //AKUN
    $query = db_select('anggaran', 'a');
    $query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
    $query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    $query->fields('a', array('kodea', 'uraian'));
    $query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    $query->condition('keg.inaktif', '0', '=');
    $query->condition('a.kodea', '5', '=');
    $query->groupBy('a.kodea');
    $query->orderBy('a.kodea');
    $results = $query->execute();

    foreach ($results as $datas) {
    	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

    	$realisasi = $datas->realisasi;
    	$rows[] = array(
    		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
    	);

    	$agg_belanja_total = $datas->anggaran;
    	$rea_belanja_total = $realisasi;

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    	$query->condition('keg.inaktif', '0', '=');
    	$query->condition('k.kodea', $datas->kodea, '=');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = $data_kel->realisasi;

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    		);
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);

    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}

    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->condition('keg.inaktif', '0', '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = $data_jen->realisasi;

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);


    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = $data_oby->realisasi;

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' =>apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
    						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = $data_rek->realisasi;

    							$rows[] = array(
    								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								array('data' => '<em>'. apbd_fn($data_rek->anggaran- $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    							);

    						}	//obyek

    					}	//rekening
    				}	//obyek

    			}	//if obyek
    		}	//jenis


    	}


    }	//foreach ($results as $datas)
    //SURPLUS DEFIIT
    $anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
    $realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
    $rows[] = array(
    	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
    );

	
    if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
    		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//PEMBIAYAAN
    	$anggaran_netto_p = 0;
    	$realisasi_netto_p = 0;

    	$rows[] = array(
    		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    	);

    	//KELOMPOK
    	$query = db_select('kelompok', 'k');
    	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
    	$query->fields('k', array('kodek', 'uraian'));
    	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    	$query->groupBy('k.kodek');
    	$query->orderBy('k.kodek');
    	$results_kel = $query->execute();

    	foreach ($results_kel as $data_kel) {
    		$realisasi = $data_kel->realisasi;

    		$rows[] = array(
    			array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:30%;border-left:1px solid black;border-right:1px solid black;'),
    			array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:30%;border-right:1px solid black;'),
    		);
    		$rows[] = array(
    			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
    			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
    		);


    		if ($data_kel->kodek=='61') {
    			$anggaran_netto_p += $data_kel->anggaran;
    			$realisasi_netto_p += $realisasi;

    		} else	{
    			$anggaran_netto_p -= $data_kel->anggaran;
    			$realisasi_netto_p -= $realisasi;
    		}

    		//JENIS
    		if ($tingkat>3) {
    			$bold_start = '<strong>';
    			$bold_end = '</strong>';
    		} else {
    			$bold_start = '';
    			$bold_end = '';
    		}
    		$query = db_select('jenis', 'j');
    		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
    		$query->fields('j', array('kodej', 'uraian'));
    		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    		$query->condition('j.kodek', $data_kel->kodek, '=');
    		$query->groupBy('j.kodej');
    		$query->orderBy('j.kodej');
    		$results_jen = $query->execute();
    		foreach ($results_jen as $data_jen) {

    			$realisasi = $data_jen->realisasi;

    			$rows[] = array(
    				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    			);

    			//OBYEK
    			if ($tingkat>'3') {
    				$query = db_select('obyek', 'o');
    				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
    				$query->fields('o', array('kodeo', 'uraian'));
    				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    				$query->condition('o.kodej', $data_jen->kodej, '=');
    				$query->groupBy('o.kodeo');
    				$query->orderBy('o.kodeo');
    				$results_oby = $query->execute();
    				foreach ($results_oby as $data_oby) {

    					$realisasi = $data_oby->realisasi;

    					$rows[] = array(
    						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    						array('data' => apbd_fn($data_oby->anggaran - $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    					);

    					//REKENING
    					if ($tingkat=='5') {
    						$query = db_select('rincianobyek', 'ro');
    						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
    						$query->fields('ro', array('kodero', 'uraian'));
    						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
							$query->addExpression('SUM(ag.jumlahsesudah)', 'realisasi');
    						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
    						$query->groupBy('ro.kodero');
    						$query->orderBy('ro.kodero');
    						$results_rek = $query->execute();
    						foreach ($results_rek as $data_rek) {

    							$realisasi = $data_rek->realisasi;

    							if (($data_rek->anggaran+$realisasi)>0) {
    								$rows[] = array(
    									array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
    									array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    									array('data' => '<em>'. apbd_fn1($data_rek->anggaran - $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
    								);
    							}

    						}	//obyek

    					}
    				}	//obyek

    			}	//tingkat obyek
    		}	//jenis


    	}

    	//SURPLUS DEFIIT
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    	);

    	//SILPA

    	$anggaran_netto += $anggaran_netto_p;
    	$realisasi_netto += $realisasi_netto_p;
    	$rows[] = array(
    		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
    		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
    		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
    	);

		} 

	

	if (isUserSKPD())
		$cetakttd = true;
	else
		$cetakttd = ($kodeuk=='ZZ'? true: false );

	if($cetakttd) {
			$rows[] = array(
				array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

			);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

					);

			if ($pimpinanatasnama != '') {
				$rows[] = array(
							array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
							array('data' => $pimpinanatasnama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
						);
			}
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
					);

			if ($pimpinannip != '') {
				$rows[] = array(
							array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
							array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
						);
			}

			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
					);
	}


    //RENDER
    //$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
    $tabel_data .= createT($header, $rows);

    //return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
    return $tabel_data;

}





function gen_report_realisasi_print_pendapatan($bulan, $kodeuk, $tingkat, $tanggal) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PENDAPATAN</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
	);

} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '4', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');

//dpq ($query);
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 0; $bulanan = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$sql->condition('k.kodea', $datas->kodea, '=');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

						$realisasi = 0; $bulanan = 0;
						$sql = db_select('jurnal' . $sufixjurnal, 'j');
						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '=');
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
						$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}

						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($data_rek->anggaran- $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						);

					}	//obyek

					}
				}	//obyek

			}	//if tingkat obyek
		}	//jenis


	}

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
	);

}	//foreach ($results as $datas)


//RENDER
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_belanja($bulan, $kodeuk, $tingkat, $tanggal) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN BELANJA</strong>', 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
	);

} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_belanja_total = 0;

$rea_belanja_total = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
$query->condition('keg.inaktif', '0', '=');
$query->condition('a.kodea', '5', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 0; $bulanan = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('keg.inaktif', '0', '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}

		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->condition('keg.inaktif', '0', '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' =>apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

							$realisasi = 0; $bulanan = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '=');
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}

							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran- $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);

						}	//obyek

					}	//rekening
				}	//obyek

			}	//if obyek
		}	//jenis


	}


}	//foreach ($results as $datas)
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:2px solid black;border-bottom:2px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:2px solid black;border-bottom:2px solid black;'),
	array('data' => '<strong>' . apbd_fn($agg_belanja_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:2px solid black;border-bottom:2px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_belanja_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:2px solid black;border-bottom:2px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_belanja_total, $rea_belanja_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:2px solid black;border-bottom:2px solid black;'),
	array('data' => '<strong>' . apbd_fn($agg_belanja_total- $rea_belanja_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:2px solid black;border-bottom:2px solid black;'),
);


//RENDER
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_periodik($bulan, $kodeuk, $tingkat, $tanggal, $cetakpdf) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);

} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$rows = null;
//TABEL

if ($cetakpdf=='excel2') {
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px',  'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px','align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bulan Ini', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Kumulatif', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'REALISASI (%)', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

} else {
	$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '170px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Bulan Ini', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Kumulatif', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

}

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '4', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');

//dpq ($query);
$results = $query->execute();

foreach ($results as $datas) {

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	$realisasi = 0; $bulanan = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$bulanan = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	$rea_pendapata_total_bulanan = $bulanan;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$sql->condition('k.kodea', $datas->kodea, '=');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = $data->realisasi;
			}

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($bulanan) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$bulanan = $data->realisasi;
					}
					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($bulanan) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

						$realisasi = 0; $bulanan = 0;
						$sql = db_select('jurnal' . $sufixjurnal, 'j');
						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '=');
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
						$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}
						$sql = db_select('jurnal' . $sufixjurnal, 'j');
						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '=');
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
						$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$bulanan = $data->realisasi;
						}
						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($bulanan) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						);

					}	//obyek

					}
				}	//obyek

			}	//if tingkat obyek
		}	//jenis


	}

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
$query->condition('keg.inaktif', '0', '=');
$query->condition('a.kodea', '5', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 0; $bulanan = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('keg.inaktif', '0', '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$bulanan = $data->realisasi;
	}

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	$rea_belanja_total_bulanan = $bulanan;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('keg.inaktif', '0', '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('keg.inaktif', '0', '=');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = $data->realisasi;
		}
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}

		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->condition('keg.inaktif', '0', '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = $data->realisasi;
			}
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($bulanan) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$bulanan = $data->realisasi;
					}

					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($bulanan) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' =>apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

							$realisasi = 0; $bulanan = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '=');
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '=');
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$bulanan = $data->realisasi;
							}

							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($bulanan) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);

						}	//obyek

					}	//rekening
				}	//obyek

			}	//if obyek
		}	//jenis


	}


}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$realisasi_netto_bulanan = $rea_pendapata_total_bulanan - $rea_belanja_total_bulanan;
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto_bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if (($kodeuk=='ZZ') or ($kodeuk=='ZZ')) {
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$realisasi_netto_p_bulanan = 0;

	$rows[] = array(
		array('data' => '6', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
		}
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
		}
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);


		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;
			$realisasi_netto_p_bulanan += $bulanan;

		} else	{
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
			$realisasi_netto_p_bulanan -= $bulanan;
		}

		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
			}
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
			}

			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($bulanan), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
					}
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
					}

					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($bulanan) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

							$realisasi = 0; $bulanan = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
							$sql->condition('ji.kodero', $data_rek->kodero, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
							}
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
							$sql->condition('ji.kodero', $data_rek->kodero, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$bulanan = (($data_kel->kodek=='61') ? $data->kreditdebet : $realisasi = $data->debetkredit);
							}

							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($bulanan) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);

						}	//rincian obyek

					}
				}	//obyek

			}	//tingkat obyek
		}	//jenis


	}

	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p_bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);

	//SILPA

	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$realisasi_netto_bulanan += $realisasi_netto_p_bulanan;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_bulanan) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	);


}

	if (isUserSKPD())
		$cetakttd = true;
	else
		$cetakttd = ($kodeuk=='ZZ'? true: false );

	if($cetakttd) {

			$rows[] = array(
				array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

			);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
					);
			$rows[] = array(
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
						array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
					);

	}

//RENDER
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat) {

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

//TABEL
$header = array (
	array('data' => 'KODE','width' => '15px', 'valign'=>'top'),
	array('data' => 'URAIAN', 'valign'=>'top'),
	array('data' => 'ANGGARAN', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'REALISASI', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '4', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 0; $bulanan = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => $datas->kodea, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);

	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$sql->condition('k.kodea', $datas->kodea, '=');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);


		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			);

			if ($tingkat>'3') {
				//OBYEK
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

						$realisasi = 0; $bulanan = 0;
						$sql = db_select('jurnal' . $sufixjurnal, 'j');
						$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$sql->condition('ji.kodero', $data_rek->kodero, '=');
						if ($kodeuk!='ZZ') $sql->condition('j.kodeuk', $kodeuk, '=');
						$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $sql->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}

						$rows[] = array(
							array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
							array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
						);

					}	//obyek

					}
				}	//obyek

			}	//if tingkat obyek
		}	//jenis


	}


}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '5', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 0; $bulanan = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.kodeuk', $kodeuk, '=');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE');
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => $datas->kodea, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);

	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;

	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->condition('keg.jenis', '1', '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);

		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$rows[] = array(
				array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			);


			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

							$realisasi = 0; $bulanan = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '=');
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}

							$rows[] = array(
								array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
							);

						}	//obyek

					}	//rekening
				}	//obyek

			}	//if obyek
		}	//jenis


	}


	//KELOMPOK BELANJA LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->condition('keg.jenis', '2', '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		$sql->condition('keg.kodeuk', $kodeuk, '=');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);

		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('keg.jenis', '2', '=');
		$query->groupBy('p.kodepro');
		$query->orderBy('p.program');
		$results_pro = $query->execute();
		foreach ($results_pro as $data_pro) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.kodepro', $data_pro->kodepro, '=');
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$kodepro = $data_kel->kodek . '.' . $data_pro->kodepro;
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);


			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'anggaran'));
			$query->condition('keg.kodeuk', $kodeuk, '=');
			$query->condition('keg.kodepro', $data_pro->kodepro, '=');
			$query->condition('keg.jenis', '2', '=');
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute();
			foreach ($results_keg as $data_keg) {

				$realisasi = 0; $bulanan = 0;

				$sql = db_select('jurnal' . $sufixjurnal, 'j');
				$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('j.kodekeg', $data_keg->kodekeg, '=');
				$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				}


				$kodekeg = $kodepro . '.' . substr($data_keg->kodekeg,-3);
				$rows[] = array(
					array('data' => '<strong>' . $kodekeg  . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . strtoupper($data_keg->kegiatan) . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($data_keg->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				);


				//JENIS

				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '=');
				$query->condition('j.kodek', $data_kel->kodek, '=');
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();
				foreach ($results_jen as $data_jen) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
					$sql->condition('keg.kodekeg', $data_keg->kodekeg, '=');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

					//dpq($sql);

					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$kodej = $kodekeg . '.' . substr($data_jen->kodej,-1);
					$rows[] = array(
						array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
						array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					);


					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '=');
						$query->condition('o.kodej', $data_jen->kodej, '=');
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();
						foreach ($results_oby as $data_oby) {

							$realisasi = 0; $bulanan = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
							$sql->condition('keg.kodekeg', $data_keg->kodekeg, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}

							$rows[] = array(
								array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
							);

							//REKENING
							if ($tingkat=='5') {
								$query = db_select('rincianobyek', 'ro');
								$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
								$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
								$query->fields('ro', array('kodero', 'uraian'));
								$query->addExpression('SUM(ag.jumlah)', 'anggaran');
								$query->condition('keg.kodekeg', $data_keg->kodekeg, '=');
								$query->condition('ro.kodeo', $data_oby->kodeo, '=');
								$query->groupBy('ro.kodero');
								$query->orderBy('ro.kodero');
								$results_rek = $query->execute();
								foreach ($results_rek as $data_rek) {

									$realisasi = 0; $bulanan = 0;
									$sql = db_select('jurnal' . $sufixjurnal, 'j');
									$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
									$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
									$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$sql->condition('ji.kodero', $data_rek->kodero, '=');
									$query->condition('keg.kodekeg', $data_keg->kodekeg, '=');
									if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '=');
									$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $sql->execute();
									foreach ($res as $data) {
										$realisasi = $data->realisasi;
									}

									$rows[] = array(
										array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
										array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
									);

								}	//obyek

							}	//rekening
						}	//obyek

					}	//if obyek
				}	//jenis



			}



		}

	}

}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


if (($kodeuk=='ZZ')  or   ($kodeuk=='00') ){
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '6', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {
		$realisasi = 0; $bulanan = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE');
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);


		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;

		} else	{
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
		}

		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			$realisasi = 0; $bulanan = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE');
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			);

			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->condition('o.kodej', $data_jen->kodej, '=');
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();
				foreach ($results_oby as $data_oby) {

					$realisasi = 0; $bulanan = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE');
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					);

					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->condition('ro.kodeo', $data_oby->kodeo, '=');
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();
						foreach ($results_rek as $data_rek) {

							$realisasi = 0; $bulanan = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '=');
							$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}

							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
							);

						}	//obyek

					}
				}	//obyek

			}	//tingkat obyek
		}	//jenis


	}

	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);

	//SILPA

	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);

}


//RENDER
$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}
function hoook_export_excel() {
    module_load_include('inc', 'phpexcel');
    $data = array();
    $headers = array();
    // First worksheet
    // Get the nodes
    $data = array
    (
        array("Volvo",22,18),
        array("BMW",15,13),
        array("Saab",5,2),
        array("Land Rover",17,15)
    );

    $headers = array("CAR","COUNT1","COUNT2");

    // Store the file in sites/default/files
    $dir = file_stream_wrapper_get_instance_by_uri('public://')->realpath();
    $filename = 'export1.xls';
    $path = "$dir/$filename";
    // Use the .xls format
    $options = array('format' => 'xls');
    $result = phpexcel_export($headers, $data, $path, $options);
    if ($result == PHPEXCEL_SUCCESS) {
        drupal_set_message(t("Ok"));
    }
    else {
        drupal_set_message(t("Error"), 'error');
    }
}

function gen_report_realisasi_print_sikd($bulan, $kodeuk, $tingkat,$margin,$tanggal) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);

} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '4', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');

//dpq ($query);
$results = $query->execute();

foreach ($results as $datas) {
	$realisasi = 483985025267;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='41')
			$realisasi = 57877839525;
		else if ($data_kel->kodek=='42')
			$realisasi = 399167568312;
		else if ($data_kel->kodek=='43')
			$realisasi = 26939617430;
		else
			$realisasi = 0;

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='411')
				$realisasi = 9101287086;
			else if ($data_jen->kodej=='412')
				$realisasi = 3577802911;
			else if ($data_jen->kodej=='413')
				$realisasi = 635981;
			else if ($data_jen->kodej=='414')
				$realisasi = 45198113547;

			else if ($data_jen->kodej=='421')
				$realisasi = 5599746892;
			else if ($data_jen->kodej=='422')
				$realisasi = 333457772000;
			else if ($data_jen->kodej=='423')
				$realisasi = 60110049420;

			else if ($data_jen->kodej=='431')
				$realisasi = 22240430;
			else if ($data_jen->kodej=='433')
				$realisasi = 0;
			elseif ($data_jen->kodej=='434')
				$realisasi = 26917377000;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

		}	//jenis


	}

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
$query->condition('keg.inaktif', '0', '=');
$query->condition('a.kodea', '5', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 189073979389;
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('keg.inaktif', '0', '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='51')
			$realisasi = 143352961842;
		else if ($data_kel->kodek=='52')
			$realisasi = 45721017547;
		else
			$realisasi = 0;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}

		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->condition('keg.inaktif', '0', '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='511')
				$realisasi = 140664627931;
			else if ($data_jen->kodej=='512')
				$realisasi = 0;
			else if ($data_jen->kodej=='513')
				$realisasi = 0;
			else if ($data_jen->kodej=='514')
				$realisasi = 1476464000;
			else if ($data_jen->kodej=='515')
				$realisasi = 845000000;
			else if ($data_jen->kodej=='516')
				$realisasi = 91304980;
			else if ($data_jen->kodej=='517')
				$realisasi = 0;
			else if ($data_jen->kodej=='518')
				$realisasi = 275564931;

			else if ($data_jen->kodej=='521')
				$realisasi = 945451500;
			else if ($data_jen->kodej=='522')
				$realisasi = 29960222869;
			else if ($data_jen->kodej=='523')
				$realisasi = 14815343178;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}


}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if (($kodeuk=='ZZ') or   ($kodeuk=='00') ) {
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='61')
			$realisasi = 0;
		else
			$realisasi = 4400000000;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;

		} else	{
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
		}

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_kel->kodek=='61')
				$realisasi = 0;
			else
				$realisasi = 4400000000;


			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}

	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);

	//SILPA

	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	);


}

	if(arg(6)!='pdf'){
		$rows[] = array(
			array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

		);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				);
	}

//RENDER
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_sikd1($bulan, $kodeuk, $tingkat,$margin,$tanggal) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);

} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '4', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');

//dpq ($query);
$results = $query->execute();

foreach ($results as $datas) {
	$realisasi = 86414278223;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='41')
			$realisasi = 3038980663;
		else if ($data_kel->kodek=='42')
			$realisasi = 83364443000;
		else if ($data_kel->kodek=='43')
			$realisasi = 10854560;
		else
			$realisasi = 0;

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='411')
				$realisasi = 1321740499;
			else if ($data_jen->kodej=='412')
				$realisasi = 1381670650;
			else if ($data_jen->kodej=='413')
				$realisasi = 0;
			else if ($data_jen->kodej=='414')
				$realisasi = 335569514;

			else if ($data_jen->kodej=='421')
				$realisasi = 0;
			else if ($data_jen->kodej=='422')
				$realisasi = 83364443000;
			else if ($data_jen->kodej=='423')
				$realisasi = 0;

			else if ($data_jen->kodej=='431')
				$realisasi = 10854560;
			else if ($data_jen->kodej=='433')
				$realisasi = 0;
			elseif ($data_jen->kodej=='434')
				$realisasi = 0;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

		}	//jenis


	}

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
$query->condition('keg.inaktif', '0', '=');
$query->condition('a.kodea', '5', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 49000223889;
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('keg.inaktif', '0', '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='51')
			$realisasi = 43265741451;
		else if ($data_kel->kodek=='52')
			$realisasi = 5734482438;
		else
			$realisasi = 0;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;bo%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}

		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->condition('keg.inaktif', '0', '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='511')
				$realisasi = 43188265411;
			else if ($data_jen->kodej=='512')
				$realisasi = 0;
			else if ($data_jen->kodej=='513')
				$realisasi = 0;
			else if ($data_jen->kodej=='514')
				$realisasi = 0;
			else if ($data_jen->kodej=='515')
				$realisasi = 0;
			else if ($data_jen->kodej=='516')
				$realisasi = 0;
			else if ($data_jen->kodej=='517')
				$realisasi = 0;
			else if ($data_jen->kodej=='518')
				$realisasi = 77476040;

			else if ($data_jen->kodej=='521')
				$realisasi = 0;
			else if ($data_jen->kodej=='522')
				$realisasi = 1541331038;
			else if ($data_jen->kodej=='523')
				$realisasi = 4193151400;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}


}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($dkel->kodek=='61')
			$realisasi = 0;
		else
			$realisasi = 0;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;

		} else	{
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
		}

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_kel->kodek=='61')
				$realisasi = 0;
			else
				$realisasi = 0;


			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}

	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);

	//SILPA

	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	);


}

	if(arg(6)!='pdf'){
		$rows[] = array(
			array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

		);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				);
	}

//RENDER
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_sikd2($bulan, $kodeuk, $tingkat,$margin,$tanggal) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);

} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '4', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');

//dpq ($query);
$results = $query->execute();

foreach ($results as $datas) {
	$realisasi = 178502435349;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='41')
			$realisasi = 7439226212;
		else if ($data_kel->kodek=='42')
			$realisasi = 171042067057;
		else if ($data_kel->kodek=='43')
			$realisasi = 21142080;
		else
			$realisasi = 0;

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='411')
				$realisasi = 3866337091;
			else if ($data_jen->kodej=='412')
				$realisasi = 2681829210;
			else if ($data_jen->kodej=='413')
				$realisasi = 635981;
			else if ($data_jen->kodej=='414')
				$realisasi = 890423930;

			else if ($data_jen->kodej=='421')
				$realisasi = 4313181057;
			else if ($data_jen->kodej=='422')
				$realisasi = 166728886000;
			else if ($data_jen->kodej=='423')
				$realisasi = 0;

			else if ($data_jen->kodej=='431')
				$realisasi = 21142080;
			else if ($data_jen->kodej=='433')
				$realisasi = 0;
			elseif ($data_jen->kodej=='434')
				$realisasi = 0;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

		}	//jenis


	}

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
$query->condition('keg.inaktif', '0', '=');
$query->condition('a.kodea', '5', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 106869072117;
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('keg.inaktif', '0', '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='51')
			$realisasi = 90365548033;
		else if ($data_kel->kodek=='52')
			$realisasi = 16503524084;
		else
			$realisasi = 0;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}

		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->condition('keg.inaktif', '0', '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='511')
				$realisasi = 90196607993;
			else if ($data_jen->kodej=='512')
				$realisasi = 0;
			else if ($data_jen->kodej=='513')
				$realisasi = 0;
			else if ($data_jen->kodej=='514')
				$realisasi = 91464000;
			else if ($data_jen->kodej=='515')
				$realisasi = 0;
			else if ($data_jen->kodej=='516')
				$realisasi = 0;
			else if ($data_jen->kodej=='517')
				$realisasi = 0;
			else if ($data_jen->kodej=='518')
				$realisasi = 77476040;

			else if ($data_jen->kodej=='521')
				$realisasi = 256365500;
			else if ($data_jen->kodej=='522')
				$realisasi = 11745832684;
			else if ($data_jen->kodej=='523')
				$realisasi = 4501325900;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}


}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='61')
			$realisasi = 0;
		else
			$realisasi = 0;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;

		} else	{
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
		}

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_kel->kodek=='61')
				$realisasi = 0;
			else
				$realisasi = 0;


			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}

	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);

	//SILPA

	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	);


}

	if(arg(6)!='pdf'){
		$rows[] = array(
			array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

		);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				);
	}

//RENDER
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_sikd4($bulan, $kodeuk, $tingkat,$margin,$tanggal) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();

} else {
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);

} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '225px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
$query->condition('a.kodea', '4', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');

//dpq ($query);
$results = $query->execute();

foreach ($results as $datas) {
	$realisasi = 691268573036;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='41')
			$realisasi = 209539752920;
		else if ($data_kel->kodek=='42')
			$realisasi = 431780220496;
		else if ($data_kel->kodek=='43')
			$realisasi = 49948599620;
		else
			$realisasi = 0;

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='411')
				$realisasi = 24444317959;
			else if ($data_jen->kodej=='412')
				$realisasi = 5387012201;
			else if ($data_jen->kodej=='413')
				$realisasi = 6745759974;
			else if ($data_jen->kodej=='414')
				$realisasi = 172962662786;

			else if ($data_jen->kodej=='421')
				$realisasi = 14042130076;
			else if ($data_jen->kodej=='422')
				$realisasi = 333457772000;
			else if ($data_jen->kodej=='423')
				$realisasi = 84280318420;

			else if ($data_jen->kodej=='431')
				$realisasi = 442619767;
			else if ($data_jen->kodej=='433')
				$realisasi = 22588602853;
			elseif ($data_jen->kodej=='434')
				$realisasi = 26917377000;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);

		}	//jenis


	}

	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
$query->condition('keg.inaktif', '0', '=');
$query->condition('a.kodea', '5', '=');
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	$realisasi = 313135203979;
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);

	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
	$query->condition('keg.inaktif', '0', '=');
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='51')
			$realisasi = 206375162191;
		else if ($data_kel->kodek=='52')
			$realisasi = 106760041788;
		else
			$realisasi = 0;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}

		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '=');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->condition('keg.inaktif', '0', '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_jen->kodej=='511')
				$realisasi = 195573962880;
			else if ($data_jen->kodej=='512')
				$realisasi = 0;
			else if ($data_jen->kodej=='513')
				$realisasi = 0;
			else if ($data_jen->kodej=='514')
				$realisasi = 5803664000;
			else if ($data_jen->kodej=='515')
				$realisasi = 2003000000;
			else if ($data_jen->kodej=='516')
				$realisasi = 91304980;
			else if ($data_jen->kodej=='517')
				$realisasi = 2627665400;
			else if ($data_jen->kodej=='518')
				$realisasi = 275564931;

			else if ($data_jen->kodej=='521')
				$realisasi = 3936330500;
			else if ($data_jen->kodej=='522')
				$realisasi = 76524014088;
			else if ($data_jen->kodej=='523')
				$realisasi = 26299697200;

			else
				$realisasi = 0;

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}


}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if (($kodeuk=='ZZ') or  ($kodeuk=='00') ){
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);

	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();

	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodek=='61')
			$realisasi = 224074600760;
		else
			$realisasi = 11900000000;


		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran, $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);


		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;

		} else	{
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
		}

		//JENIS
		if ($tingkat>3) {
			$bold_start = '<strong>';
			$bold_end = '</strong>';
		} else {
			$bold_start = '';
			$bold_end = '';
		}
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();
		foreach ($results_jen as $data_jen) {

			if ($data_kel->kodek=='611')
				$realisasi = 223839875760;
			else if ($data_kel->kodek=='615')
				$realisasi = 234725000;
			else
				$realisasi = 11900000000;


			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => $data_jen->uraian, 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran- $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);


		}	//jenis


	}

	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p, $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);

	//SILPA

	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'width' => '45px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '225px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto, $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	);


}

	if(arg(6)!='pdf'){
		$rows[] = array(
			array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),

		);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),

				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;'),
					array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),
				);
		$rows[] = array(
					array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
					array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),
				);
	}

//RENDER
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>
