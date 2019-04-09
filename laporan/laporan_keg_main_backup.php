<?php
function laporan_keg_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 125px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
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
				$cetakpdf = arg(9);
				
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
			$kodeuk = '81';
		} 
		
	}
	
	if ($bulan=='0') $bulan='12';
	//drupal_set_title('BELANJA');
	
	 
	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat,$margin,$tanggal);
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		
		//return $output;
				
	}else if ($cetakpdf=='pdf2') {
		$output = gen_report_realisasi_print_flat($bulan, $kodeuk, $tingkat,$margin,$tanggal);
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		
		//return $output;
	} else if ($cetakpdf=='excel') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realiasi Kegiatan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat,$margin,$tanggal,$cetakpdf);
		
		echo $output;
				
	}else if ($cetakpdf=='excel2') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realiasi Kegiatan Ringkas.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		
		$output = gen_report_realisasi_print_flat($bulan, $kodeuk, $tingkat,$margin,$tanggal,$cetakpdf);
		
		echo $output;
				
	} else {
		$output = gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat);
		//$output = gen_report_realisasi_kegiatan_pendapatan($bulan, $kodeuk, $tingkat);
		//$output .= gen_report_realisasi_kegiatan_btl($bulan, $kodeuk, $tingkat);
		//$output .= gen_report_realisasi_kegiatan_bl($bulan, $kodeuk, $tingkat);
		$output_form = drupal_get_form('laporan_keg_main_form');	
		
		//$btn = l('Cetak', 'laporankeg/filter/' . $bulan . '/'. $kodeuk . '/'. $tingkat . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//'laporan/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin . '/' . $tanggal . '/' . $hal1; 
		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporankeg/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/pdf">Format Standard</a></li>' .
						'<li><a href="/laporankeg/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/pdf2">Format Ringkas</a></li>' .
					'</ul>' .
				'</div>';		
		$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporankeg/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/excel">Format Standard</a></li>' .
						'<li><a href="/laporankeg/filter/' . $bulan . '/' . urlencode($kodeuk) . '/' . $tingkat . '/' . $margin . '/' . urlencode($tanggal) . '/' . $hal1 .  '/' . $marginkiri . '/excel2">Format Ringkas</a></li>' .
					'</ul>' .
				'</div>';			
		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_keg_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];
	$margin = $form_state['values']['margin'];
	$marginkiri = $form_state['values']['marginkiri'];
	$tanggal = $form_state['values']['tanggal'];
	$hal1 = $form_state['values']['hal1'];
	
	$uri = 'laporankeg/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin. '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri;
	drupal_goto($uri);
	
}


function laporan_keg_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = '81';
	}
	$namasingkat = '|BPKAD';
	$bulan = date('n')-1;
	$tingkat = '3';
	$margin = '10';
	$marginkiri = '20';
	$tanggal = date('j F Y');
	$hal1 = '1';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$tingkat = arg(4);
		$margin =arg(5);
		$tanggal =arg(6);
		$hal1 =arg(7);
		$marginkiri =arg(8);
	} 
	
	if ($bulan=='0') $bulan='12';
	
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
			 '01' => t('JANUARI'), 	
			 '02' => t('FEBRUARI'),
			 '03' => t('MARET'),	
			 '04' => t('APRIL'),	
			 '05' => t('MEI'),	
			 '06' => t('JUNI'),	
			 '07' => t('JULI'),	
			 '08' => t('AGUSTUS'),	
			 '09' => t('SEPTEMBER'),	
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
	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => $arr_bulan,
	);
	

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
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => '3',
	);
	*/
	
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

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}


$results = db_query('select kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
};

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
if ($kodeuk=='ZZ') {
	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '15px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'width' => '80px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
	array('data' => '', 'width' => '10px', 'valign'=>'top'),
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
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:150%;'),
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
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/10/20/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
						
						$realisasi = 0;
						$query = db_select('jurnal' . $sufixjurnal, 'j');
						$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$query->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $query->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}
						
						$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
						$rows[] = array(
							array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
							array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
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
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
 
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	 
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:150%;'),
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
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
			
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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
			
			$realisasi = 0;
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;			
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/10/20/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil , 'align' => 'left', 'valign'=>'top'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);	
					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));		
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$query->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
							
							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
							$rows[] = array(
								array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'.$uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
			
		 
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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
		$query->condition('keg.inaktif', '0', '=');
		$query->condition('keg.total', '0', '>');
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			//$query->addExpression('SUM(j.total)', 'realisasi');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.jenis', '2', '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute(); 
			
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$kodepro = $kodedinas . '.' . $data_pro->kodepro;

			$detil = l('Detil', '/laporandetilpro/filter/' . $bulan . '/' . $kodeuk . '/' . $data_pro->kodepro . '/10/20/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
			);				
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				$realisasi = 0;
				
				$query = db_select('jurnal' . $sufixjurnal, 'j');
				//$query->addExpression('SUM(j.total)', 'realisasi');
				$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				//$query->addExpression('SUM(j.total)', 'realisasi');
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$query->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
				$query->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				
				if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $query->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				} 
				

				$uraian = l(strtoupper($data_keg->kegiatan), '/akuntansi/buku/' . $data_keg->kodekeg . '/5/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
				$detil = l('Detil', '/laporandetilkeg/filter/' . $bulan . '/' . $kodeuk . '/' . $data_keg->kodekeg . '/10/20/view', array('attributes' => array('class' => null)));
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);				
				$rows[] = array(
					array('data' => '<strong>' . $kodekeg  . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($data_keg->total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					
					//dpq($query);
					
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
					$uraian = l($data_jen->uraian, '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_jen->kodej . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
					
					$rows[] = array(
						array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
							
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_oby->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
								array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
									
									$realisasi = 0;
									$query = db_select('jurnal' . $sufixjurnal, 'j');
									$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
									$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
									$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$query->condition('ji.kodero', $data_rek->kodero, '='); 
									$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $query->execute();
									foreach ($res as $data) {
										$realisasi = $data->realisasi;
									}
									
									$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_rek->kodero . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
									$rows[] = array(
										array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
										array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
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


if ($kodeuk=='00') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' .  '6</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
		
			$rows[] = array(
				array('data' => $kodedinas . '.000.000.' . $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
				
					$rows[] = array(
						array('data' => $kodedinas . '.000.000.' . $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
							$query->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
						
							$rows[] = array(
								array('data' => $kodedinas . '.000.000.' . $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
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
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_kegiatan_pendapatan($bulan, $kodeuk, $tingkat) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}


$results = db_query('select kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
};

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
if ($kodeuk=='ZZ') {
	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '15px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'width' => '80px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
	array('data' => '', 'width' => '10px', 'valign'=>'top'),
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
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:150%;'),
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
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/10/20/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
						
						$realisasi = 0;
						$query = db_select('jurnal' . $sufixjurnal, 'j');
						$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$query->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $query->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}
						
						$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
						$rows[] = array(
							array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
							array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
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

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_kegiatan_btl($bulan, $kodeuk, $tingkat) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
 
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	 
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:150%;'),
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
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
			
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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
			
			$realisasi = 0;
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;			
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/10/20/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil , 'align' => 'left', 'valign'=>'top'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}

					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);	
					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));		
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$query->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
							
							$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
							$rows[] = array(
								array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'.$uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
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
	
}	//foreach ($results as $datas)

//RENDER	
$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_kegiatan_bl($bulan, $kodeuk, $tingkat) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}


//KELOMPOK BELANJA LANGSUNG
$query = db_select('kelompok', 'k');
$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('k', array('kodek', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('k.kodea', '5', '='); 
$query->condition('keg.jenis', '2', '='); 
$query->groupBy('k.kodek');
$query->orderBy('k.kodek');
$results_kel = $query->execute();	

foreach ($results_kel as $data_kel) {
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('keg.jenis', '2', '='); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
		
	 
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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
	$query->condition('keg.inaktif', '0', '=');
	$query->condition('keg.total', '0', '>');
	$query->groupBy('p.kodepro');
	$query->orderBy('p.kodepro');
	$results_pro = $query->execute(); 
	foreach ($results_pro as $data_pro) {

		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		//$query->addExpression('SUM(j.total)', 'realisasi');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
		$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute(); 
		
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		
		$kodepro = $kodedinas . '.' . $data_pro->kodepro;

		$detil = l('Detil', '/laporandetilpro/filter/' . $bulan . '/' . $kodeuk . '/' . $data_pro->kodepro . '/10/20/view', array('attributes' => array('class' => null)));
		
		$rows[] = array(
			array('data' => '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
		);				
		
		
		//KEGIATAN
		$query = db_select('kegiatanskpd', 'keg');
		$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.total', '0', '>'); 
		$query->orderBy('keg.kodepro', 'ASC');
		$query->orderBy('keg.kodekeg', 'ASC');
		$results_keg = $query->execute(); 
		foreach ($results_keg as $data_keg) {
			
			$realisasi = 0;
			
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			//$query->addExpression('SUM(j.total)', 'realisasi');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			//$query->addExpression('SUM(j.total)', 'realisasi');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
			$query->condition('j.kodekeg', $data_keg->kodekeg, '='); 
			
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			} 
			

			$uraian = l(strtoupper($data_keg->kegiatan), '/akuntansi/buku/' . $data_keg->kodekeg . '/5/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
			$detil = l('Detil', '/laporandetilkeg/filter/' . $bulan . '/' . $kodeuk . '/' . $data_keg->kodekeg . '/10/20/view', array('attributes' => array('class' => null)));
			
			$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);				
			$rows[] = array(
				array('data' => '<strong>' . $kodekeg  . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_keg->total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
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
				
				$realisasi = 0;
				$query = db_select('jurnal' . $sufixjurnal, 'j');
				$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				
				//dpq($query);
				
				$res = $query->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				}
			
				$kodej = $kodekeg  . '.' . $data_jen->kodej;
				$uraian = l($data_jen->uraian, '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_jen->kodej . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
				
				$rows[] = array(
					array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
						
						$realisasi = 0;
						$query = db_select('jurnal' . $sufixjurnal, 'j');
						$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
						$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
						$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $query->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}
						
						$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
						$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_oby->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
						$rows[] = array(
							array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
							array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
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
								
								$realisasi = 0;
								$query = db_select('jurnal' . $sufixjurnal, 'j');
								$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
								$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
								$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
								$query->condition('ji.kodero', $data_rek->kodero, '='); 
								$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
								if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
								if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
								$res = $query->execute();
								foreach ($res as $data) {
									$realisasi = $data->realisasi;
								}
								
								$uraian = l(ucfirst(strtolower($data_rek->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_rek->kodero . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
								$rows[] = array(
									array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
									array('data' => '<em>'. $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
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


//RENDER	
$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print($bulan, $kodeuk, $tingkat,$margin,$tanggal,$cetakpdf) {

if ($kodeuk=='33') {
	$tingkat = '3';
} elseif  (($kodeuk=='03') or ($kodeuk=='58')) {
	if ($tingkat == '5') $tingkat = '4';
}

$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip,kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
	$skpd = $datas->namauk;
	$pimpinannama = $datas->pimpinannama;
	$pimpinanjabatan = $datas->pimpinanjabatan;
	$pimpinannip = $datas->pimpinannip;
};

$rows[] = array(
	array('data' => 'LAPORAN REALISASI ANGGARAN',  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'border:none'),
);

if (($bulan=='0') or ($bulan=='12')) {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(),  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(),  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

//TABEL 
$rows = null;
if ($cetakpdf == 'excel'){
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '100px',  'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'REALISASI (%)', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'LEBIH/(KURANG)', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
}else{
$header[] = array (
	array('data' => 'KODE','width' => '100px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'LEBIH/(KURANG)', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Prsn', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
}
$rows = array();

//Batas
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);	

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

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}
foreach ($results as $datas) {
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:135%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi-$datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
				
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi-$data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}


			if ($tingkat==3) {
				$uraian =  ucwords(strtolower($data_jen->uraian));
				$bold_b = ''; $bold_e = ''; 
			} else {
				$uraian = $data_jen->uraian;
				$bold_b = '<strong>'; $bold_e = '</strong>'; 
			}
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			$rows[] = array(
				array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn($data_jen->anggaran) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn($realisasi) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_e, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn($realisasi-$data_jen->anggaran) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
					$rows[] = array(
						array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
						
						$realisasi = 0;
						$query = db_select('jurnal' . $sufixjurnal, 'j');
						$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$query->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $query->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}
					

						$rows[] = array(
							array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => '<em>'. apbd_fn($realisasi-$data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						);						
					
					}	//obyek					
					
					}
				}	//obyek			
			
			}	//if tingkat obyek
		}	//jenis
		
		
	}
	
	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$rea_belanja_total=0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rea_belanja_total = $realisasi;
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:135%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:3px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	
	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:50%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
	);	

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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			if ($tingkat==3) {
				$uraian =  ucwords(strtolower($data_jen->uraian));
				$bold_b = ''; $bold_e = ''; 
			} else {
				$uraian = $data_jen->uraian;
				$bold_b = '<strong>'; $bold_e = '</strong>'; 
			}
					
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			$rows[] = array(
				array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn($data_jen->anggaran) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn($realisasi) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_e, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_b . apbd_fn($data_jen->anggaran- $realisasi) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);	
					$rows[] = array(
						array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$query->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}				

							$rows[] = array(
								array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
	

	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);						
	
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
			
		
						
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
		);			
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.inaktif', '0', '=');
		$query->condition('keg.total', '0', '>');		
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			//Batas
			$rows[] = array(
				array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
			);			
			$kodepro = $kodedinas . '.' . $data_pro->kodepro;		
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				$realisasi = 0;
				
				$query = db_select('jurnal' . $sufixjurnal, 'j');
				$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$query->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
				if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $query->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				} 
				
				//Batas
				$rows[] = array(
					array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:25%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
				);			
				
				if ($tingkat==3) {
					$ss = 'font-weight: bold;';
				} else {
					$ss = 'border-bottom:1px solid black;';
				}
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);
				$rows[] = array(
					array('data' => $kodekeg, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => strtoupper($data_keg->kegiatan), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg->total), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
					array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
					array('data' => apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
					array('data' => apbd_fn($data_keg->total- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					
					//dpq($query);
					
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
				
					if ($tingkat==3) {
						$uraian =  ucwords(strtolower($data_jen->uraian));
						$bold_b = ''; $bold_e = ''; 
					} else {
						$uraian = $data_jen->uraian;
						$bold_b = '<strong>'; $bold_e = '</strong>'; 
					}
					
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
					$rows[] = array(
						array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => $uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => $bold_b . apbd_fn($data_jen->anggaran) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => $bold_b . apbd_fn($realisasi) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => $bold_b . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_e, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => $bold_b . apbd_fn($data_jen->anggaran- $realisasi) . $bold_e, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
							
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
									 
									$realisasi = 0;
									$query = db_select('jurnal' . $sufixjurnal, 'j');
									$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
									$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
									$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$query->condition('ji.kodero', $data_rek->kodero, '='); 
									$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $query->execute();
									foreach ($res as $data) {
										$realisasi = $data->realisasi;
									}
								

									$rows[] = array(
										array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
										array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)


//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if ($kodeuk=='00') {

	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);	

	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.6</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
		
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			$rows[] = array(
				array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
				
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
					$rows[] = array(
						array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
							$query->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
						
							$rows[] = array(
								array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)) . '</em>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran- $realisasi) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);						

							
						}	//obyek					
						
					}
				}	//obyek			
				
			}	//tingkat obyek
		}	//jenis
		
		
	}
	
	//PEMBIAYAAN NETTO	
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	);			
	

	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);	

	//SILPA
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>SISA LBH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	);			
	
}

//Batas
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);					
	
if(!isSuperuser()){
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

$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_printbag($bulan, $kodeuk, $tingkat,$margin,$tanggal,$bag) {


$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip,kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
	$skpd = $datas->namauk;
	$pimpinannama = $datas->pimpinannama;
	$pimpinanjabatan = $datas->pimpinanjabatan;
	$pimpinannip = $datas->pimpinannip;
};



$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

//TABEL 
$rows = null;
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '100px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Prsn', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
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

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}
$agg_pendapata_total=0;
foreach ($results as $datas) {
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}	
							
$agg_pendapata_total = $datas->anggaran;
$rea_pendapata_total = $realisasi;
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
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rea_belanja_total = $realisasi;
	
	/*$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);*/
	
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		/*$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);*/		
				
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			/*$rows[] = array(
				array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);*/
			
			
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);	
					$rows[] = array(
						array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$query->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}				

							$rows[] = array(
								array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
			
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);			
		$batasbawah=0+(($bag-1)*3);
		$range=3;
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->groupBy('p.kodepro');
		$query->orderBy('p.program');
		$query->range($batasbawah, $range);
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$kodepro = $kodedinas . '.' . $data_pro->kodepro;		
			$rows[] = array(
				array('data' => $kodepro, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => strtoupper($data_pro->program), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_pro->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_pro->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				$realisasi = 0;
				
				$query = db_select('jurnal' . $sufixjurnal, 'j');
				$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$query->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
				if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $query->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				} 
				

				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);
				$rows[] = array(
					array('data' => $kodekeg, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => strtoupper($data_keg->kegiatan), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg->total), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg->total- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					
					//dpq($query);
					
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
					$rows[] = array(
						array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_jen->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
							foreach ($res as $data) {
								$realisasi = $data->realisasi;
							}
							
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
								$query->orderBy('ro.kodero');
								$results_rek = $query->execute();	
								foreach ($results_rek as $data_rek) {
									 
									$realisasi = 0;
									$query = db_select('jurnal' . $sufixjurnal, 'j');
									$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
									$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
									$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$query->condition('ji.kodero', $data_rek->kodero, '='); 
									$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									$query->condition('j.kodekeg', $data_keg->kodekeg, '='); 
									$query->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $query->execute();
									foreach ($res as $data) {
										$realisasi = $data->realisasi;
									}
								

									$rows[] = array(
										array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
										array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)


//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


//Batas
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);						
if(!isSuperuser()){
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
	}
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_kegiatan_resume($bulan, $kodeuk, $tingkat) {

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}


$results = db_query('select kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
};

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '15px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'width' => '80px', 'valign'=>'top'),
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
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
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
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
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
						
						$realisasi = 0;
						$query = db_select('jurnal' . $sufixjurnal, 'j');
						$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$query->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $query->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}
					
						$rows[] = array(
							array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
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
$query->addExpression('SUM(ag.realisasi)', 'realisasi');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $datas->realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $datas->realisasi;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.realisasi)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $data_kel->realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.realisasi)', 'realisasi');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;			
			$rows[] = array(
				array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $data_jen->realisasi)), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->addExpression('SUM(ag.realisasi)', 'realisasi');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);					
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $data_oby->realisasi)), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->addExpression('SUM(ag.realisasi)', 'realisasi');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$rows[] = array(
								array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $data_rek->realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
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
	$query->addExpression('SUM(ag.realisasi)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi = $datas->realisasi;
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->innerJoin('anggperkeg', 'ag', 'keg.kodekeg=ag.kodekeg');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.realisasi)', 'realisasi');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->groupBy('p.kodepro');
		$query->orderBy('p.program');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasi = $data_pro->realisasi;

			$kodepro = $kodedinas . '.' . $data_pro->kodepro;
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);				
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->innerJoin('anggperkeg', 'ag', 'keg.kodekeg=ag.kodekeg');
			$query->fields('keg', array('kodekeg', 'kegiatan'));
			$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.realisasi)', 'realisasi');
			
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->groupBy('keg.kodekeg');
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				$realisasi = $data_keg->realisasi;
				
				//$kodekeg = $kodepro . '.' . substr($data_keg->kodekeg,-3);
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);
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
				$query->addExpression('SUM(ag.realisasi)', 'realisasi');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {
					
					$realisasi = $data_jen->realisasi;

				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
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
						$query->addExpression('SUM(ag.realisasi)', 'realisasi');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {
							
							$realisasi = $data_oby->realisasi;

							
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
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
								$query->addExpression('SUM(ag.realisasi)', 'realisasi');
								$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
								$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
								$query->groupBy('ro.kodero');
								$query->orderBy('ro.kodero');
								$results_rek = $query->execute();	
								foreach ($results_rek as $data_rek) {
									
									$realisasi = $data_rek->realisasi;
								
									$rows[] = array(
										array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'align' => 'left', 'valign'=>'top'),
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


if ($kodeuk=='ZZ') {
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
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
							
							$realisasi = 0;
							$query = db_select('jurnal' . $sufixjurnal, 'j');
							$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
							$query->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $query->execute();
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
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_resume($bulan, $kodeuk, $tingkat,$margin,$tanggal) {


$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip,kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
	$skpd = $datas->namauk;
	$pimpinannama = $datas->pimpinannama;
	$pimpinanjabatan = $datas->pimpinanjabatan;
	$pimpinannip = $datas->pimpinannip;
};

$rows[] = array(
	array('data' => 'LAPORAN REALISASI ANGGARAN', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

//TABEL 
$rows = null;
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '100px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Prsn', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
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

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}
foreach ($results as $datas) {
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
				
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			$rows[] = array(
				array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$query->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
					$rows[] = array(
						array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
						
						$realisasi = 0;
						$query = db_select('jurnal' . $sufixjurnal, 'j');
						$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
						$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
						$query->condition('ji.kodero', $data_rek->kodero, '='); 
						if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
						if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
						$res = $query->execute();
						foreach ($res as $data) {
							$realisasi = $data->realisasi;
						}
					

						$rows[] = array(
							array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
	
	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
$query->addExpression('SUM(ag.realisasi)', 'realisasi');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$rea_belanja_total=0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasi = $datas->realisasi;

	$rea_belanja_total = $realisasi;
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.realisasi)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		$realisasi = $data_kel->realisasi;

		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
				
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.realisasi)', 'realisasi');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			

			$realisasi = $data_jen->realisasi;
			
			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
			$rows[] = array(
				array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->addExpression('SUM(ag.realisasi)', 'realisasi');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					

					$realisasi = $data_oby->realisasi;
					
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);	
					$rows[] = array(
						array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);					
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->addExpression('SUM(ag.realisasi)', 'realisasi');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasi = $data_rek->realisasi;

							$rows[] = array(
								array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
	

	//KELOMPOK BELANJA LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.realisasi)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		$realisasi = $data_kel->realisasi;
			
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);			
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->innerJoin('anggperkeg', 'ag', 'keg.kodekeg=ag.kodekeg');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.realisasi)', 'realisasi');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->groupBy('p.kodepro');
		$query->orderBy('p.program');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasi = $data_pro->realisasi;
			
			$kodepro = $kodedinas . '.' . $data_pro->kodepro;		
			$rows[] = array(
				array('data' => $kodepro, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => strtoupper($data_pro->program), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_pro->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_pro->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->innerJoin('anggperkeg', 'ag', 'keg.kodekeg=ag.kodekeg');
			$query->fields('keg', array('kodekeg', 'kegiatan'));
			$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.realisasi)', 'realisasi');
			
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->groupBy('keg.kodekeg');
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				

				$realisasi = $data_keg->realisasi;
				 
				

				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);
				$rows[] = array(
					array('data' => $kodekeg, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => strtoupper($data_keg->kegiatan), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_keg->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg->anggaran - $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				);					
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->addExpression('SUM(ag.realisasi)', 'realisasi');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {
					

					$realisasi = $data_jen->realisasi;
					
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
					$rows[] = array(
						array('data' => $kodej, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_jen->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);					
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->addExpression('SUM(ag.realisasi)', 'realisasi');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {
							

							$realisasi = $data_oby->realisasi;
							
							
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$rows[] = array(
								array('data' => $kodeo, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => apbd_fn($data_oby->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);		
							
							//REKENING
							if ($tingkat=='5') {
								$query = db_select('rincianobyek', 'ro');
								$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
								$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
								$query->fields('ro', array('kodero', 'uraian'));
								$query->addExpression('SUM(ag.jumlah)', 'anggaran');
								$query->addExpression('SUM(ag.realisasi)', 'realisasi');
								$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
								$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
								$query->orderBy('ro.kodero');
								$results_rek = $query->execute();	
								foreach ($results_rek as $data_rek) {
									 

									$realisasi = $data_rek->realisasi;
									
								

									$rows[] = array(
										array('data' => $kodeo . '.' . substr($data_rek->kodero,-3), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
										array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)


//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


//Batas
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);						
if(!isSuperuser()){
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
	}
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_flat($bulan, $kodeuk, $tingkat,$margin,$tanggal,$cetakpdf) {

if ($kodeuk=='33') {
	$tingkat = '3';
} elseif  (($kodeuk=='03') or ($kodeuk=='58')) {
	if ($tingkat == '5') $tingkat = '4';
}

$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip,kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
	$skpd = $datas->namauk;
	$pimpinannama = $datas->pimpinannama;
	$pimpinanjabatan = $datas->pimpinanjabatan;
	$pimpinannip = $datas->pimpinannip;
};

$rows[] = array(
	array('data' => 'LAPORAN REALISASI ANGGARAN',  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(),  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(),  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

//TABEL 
$rows = null;
if ($cetakpdf == 'excel2'){
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '100px','align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'REALISASI (%)', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
}else{
$header[] = array (
	array('data' => 'KODE','width' => '100px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Prsn', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
}
$rows = array();

//Batas
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);	

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

if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}
foreach ($results as $datas) {
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:110%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
	);	
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	
	
	$query = db_select('rincianobyek', 'ro');
	$query->innerJoin('anggperuk', 'ag', 'ro.kodero=ag.kodero');
	$query->fields('ro', array('kodero', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('ro.kodero');
	$query->orderBy('ro.kodero');
	$results_rek = $query->execute();	
	foreach ($results_rek as $data_rek) {
		
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$query->condition('ji.kodero', $data_rek->kodero, '='); 
		if ($kodeuk!='ZZ') $query->condition('j.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
	

		$rows[] = array(
			array('data' => $kodedinas  . '.000.000.' . substr($data_rek->kodero,0,2) . '.' . substr($data_rek->kodero,-6), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_rek->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => apbd_fn($data_rek->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);						
	
	}			
					
	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
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
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$rea_belanja_total=0;
foreach ($results as $datas) {
	
	$realisasi = 0;
	$query = db_select('jurnal' . $sufixjurnal, 'j');
	$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $query->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	$rea_belanja_total = $realisasi;
	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:110%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:2px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	
	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:50%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:50%;border-right:1px solid black;'),
	);	

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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}

		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);	
		
		//REKENING
		$query = db_select('rincianobyek', 'ro');
		$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('ro', array('kodero', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('ag.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$query->groupBy('ro.kodero');
		$query->orderBy('ro.kodero');
		$results_rek = $query->execute();	
		foreach ($results_rek as $data_rek) {
			
			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', $data_rek->kodero, '='); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}				

			$rows[] = array(
				array('data' => $kodedinas . '.000.000.' . $data_kel->kodek . '.' . substr($data_rek->kodero,-5), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_rek->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);							
		
		}	//obyek					
							
	}		
	

	//Batas
	$rows[] = array(
		array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);						
	
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
		$realisasi = 0;
		$query = db_select('jurnal' . $sufixjurnal, 'j');
		$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $query->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
			
		
						
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
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

			$realisasi = 0;
			$query = db_select('jurnal' . $sufixjurnal, 'j');
			$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $query->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}

			//Batas
			$rows[] = array(
				array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
			);			
			$kodepro = $kodedinas . '.' . $data_pro->kodepro;		
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran- $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			);			
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				$realisasi = 0;
				
				$query = db_select('jurnal' . $sufixjurnal, 'j');
				$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$query->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
				if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $query->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				} 
				
				//Batas
				$rows[] = array(
					array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:25%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
					array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:25%;border-right:1px solid black;'),
				);			
				
				if ($tingkat==3) {
					$ss = 'font-weight: bold;';
				} else {
					$ss = 'border-bottom:1px solid black;';
				}
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);
				$rows[] = array(
					array('data' => $kodekeg, 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => strtoupper($data_keg->kegiatan), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_keg->total), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
					array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
					array('data' => apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
					array('data' => apbd_fn($data_keg->total- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;' . $ss),
				);					
				
				
	
				//REKENING
				$query = db_select('rincianobyek', 'ro');
				$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('ro', array('kodero', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->groupBy('ro.kodero');
				$query->orderBy('ro.kodero');
				$results_rek = $query->execute();
				foreach ($results_rek as $data_rek) {
					 
					$realisasi = 0;
					$query = db_select('jurnal' . $sufixjurnal, 'j');
					$query->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$query->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$query->condition('ji.kodero', $data_rek->kodero, '='); 
					$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $query->execute();
					foreach ($res as $data) {
						$realisasi = $data->realisasi;
					}
				

					$rows[] = array(
						array('data' => $kodekeg . '.52.' . substr($data_rek->kodero,-5), 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_rek->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_rek->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_rek->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_rek->anggaran- $realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);									
				
				}	//obyek					
					
						
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)


//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto- $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);

//Batas
$rows[] = array(
	array('data' => '', 'width' => '100px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;'),
);						

if(!isSuperuser()){
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


$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>


