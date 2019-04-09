<?php
function laporan_prognosis_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	
	
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kodeuk = arg(2);
				$margin =arg(3);
				$tanggal =arg(4);
				$hal1 = arg(5);
				$marginkiri = arg(6);
				$cetakpdf = arg(7);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$margin = '10'; 
		$marginkiri = '20';
		$hal1 = '1'; 
		$tanggal = date('j F Y');
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = 'ZZ';
		}
		
	}
	
	
	//drupal_set_message($bulan);
	
	if ($cetakpdf == 'pdf') {
		$output = gen_report_realisasi_print($kodeuk, $tanggal);
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
		
	} else if ($cetakpdf=='excel') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Prognosis.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print($kodeuk, $tanggal);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($kodeuk);
		$output_form = drupal_get_form('laporan_prognosis_main_form');	
		
		
		$btn = l('Cetak', 'laporanprognosis/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		$btn .= '&nbsp;' . l('Excel', 'laporanprognosis/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
		$btn .= '&nbsp;' . l('Input Prognosis', 'rekeningkeg/prognosis', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
		

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_prognosis_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$margin = $form_state['values']['margin'];
	$marginkiri = $form_state['values']['marginkiri'];
	$tanggal = $form_state['values']['tanggal'];
	$hal1 = $form_state['values']['hal1'];

	
	$uri = 'laporanprognosis/filter/' . $kodeuk . '/' . $margin . '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri;
	drupal_goto($uri);
	
}


function laporan_prognosis_main_form($form, &$form_state) {
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = 'ZZ';
	}
	$namasingkat = 'SELURUH SKPD';
	$margin = '10';
	$marginkiri = '20';
	$tanggal = date('j F Y');
	$hal1 = '1';
	
	if(arg(2)!=null){		
		$kodeuk = arg(2);
		$margin =arg(3);
		$tanggal =arg(4);
		$hal1 = arg(5);
		$marginkiri = arg(6);
	} 
	
	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat= $data->namasingkat;
			}
		}	
	}
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $namasingkat . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
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

function gen_report_realisasi($kodeuk) {

$agg_pendapata_total = 0; $agg_pendapata_total_bulanan = 0;
$agg_belanja_total = 0; $agg_belanja_total_bulanan = 0;
$agg_pembiayaan_netto = 0; $agg_pembiayaan_netto_bulanan = 0;

$rea_pendapata_total = 0; $pro_pendapata_total = 0;
$rea_belanja_total = 0; $pro_belanja_total = 0;
$rea_pembiayaan_netto = 0; $rea_pembiayaan_netto_bulanan = 0;

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
	array('data' => 'Sisa', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Prognosis', 'width' => '90px', 'valign'=>'top'),
);
$rows = array();

$bulan = 6;
$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,'2017'));;	
if ($kodeuk=='ZZ') {
	$tanggal_awal = apbd_tahun() . '-' . sprintf('%02d', $bulan) . '-01';		
} else {
	$tanggal_awal = apbd_tahun() . '-01-01';
}

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

$realisasi = 0; $prognosis = 0 ; $sisa = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	read_realisasi_akun($kodeuk, $datas->kodea, $realisasi, $sisa, $prognosis);
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran - $realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	$pro_pendapata_total = $prognosis;
	
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
		
		read_realisasi_kelompok($kodeuk, $data_kel->kodek, $realisasi, $sisa, $prognosis);
		
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran - $realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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
			
			read_realisasi_jenis($kodeuk, $data_jen->kodej, $realisasi, $sisa, $prognosis);
			
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran - $realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
			);
			
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
				
				read_realisasi_obyek($kodeuk, $data_oby->kodeo, $realisasi, $sisa, $prognosis);
				
				$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
				$rows[] = array(
					array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data_oby->anggaran - $realisasi) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
				);
				
			}	//obyek			
			
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
	
	read_realisasi_akun($kodeuk, $datas->kodea, $realisasi, $sisa, $prognosis);
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	$pro_belanja_total= $prognosis;
	
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

		read_realisasi_kelompok($kodeuk, $data_kel->kodek, $realisasi, $sisa, $prognosis);

		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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
			
			read_realisasi_jenis($kodeuk, $data_jen->kodej, $realisasi, $sisa, $prognosis);
			
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
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
				
				read_realisasi_obyek($kodeuk, $data_oby->kodeo, $realisasi, $sisa, $prognosis);
				
				$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
				$rows[] = array(
					array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
				);
				
			}	//obyek			
				
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$prognosis_netto = $pro_pendapata_total - $pro_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);

if (($kodeuk=='ZZ') or ($kodeuk=='00')) {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
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

		read_realisasi_kelompok('ZZ', $data_kel->kodek, $realisasi, $sisa, $prognosis);
		
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;
			$prognosis_netto_p += $prognosis;

		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
			$prognosis_netto_p -= $prognosis;
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
			
			read_realisasi_jenis('ZZ', $data_jen->kodej, $realisasi, $sisa, $prognosis);	
			
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
			);
			
			//OBYEK
			$query = db_select('obyek', 'o');
			$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
			$query->fields('o', array('kodeo', 'uraian'));
			$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->condition('o.kodej', $data_jen->kodej, '='); 
			$query->groupBy('o.kodeo');
			$query->orderBy('o.kodeo');
			$results_oby = $query->execute();	
			foreach ($results_oby as $data_oby) {
				
				read_realisasi_obyek('ZZ', $data_oby->kodeo, $realisasi, $sisa, $prognosis);
				
				$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));					
				$rows[] = array(
					array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
				);
				
			}	//obyek			
			
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
}


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($kodeuk, $tanggal) {

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

$rows[] = array(
	array('data' => 'PROGNOSIS SEMESTER II - TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$pro_pendapata_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$pro_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '30px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI SEMESTER I', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'PROGNOSIS', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SEMESTER II', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
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

	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	read_realisasi_akun($kodeuk, $datas->kodea, $realisasi, $sisa, $prognosis);	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran - $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
	);
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	$pro_pendapata_total = $prognosis;
	
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
		
		read_realisasi_kelompok($kodeuk, $data_kel->kodek, $realisasi, $sisa, $prognosis);	
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran - $realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		
		//JENIS
		$bold_start = '';
		$bold_end = '';

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
			
			read_realisasi_jenis($kodeuk, $data_jen->kodej, $realisasi, $sisa, $prognosis);	
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran - $realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($prognosis) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
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
				
				read_realisasi_obyek($kodeuk, $data_oby->kodeo, $realisasi, $sisa, $prognosis);	
				
				$rows[] = array(
					array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($data_oby->anggaran - $realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($prognosis) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
				);
				
			}	//obyek			
			
		}	//jenis
		
		
	}
	
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
	
	read_realisasi_akun($kodeuk, $datas->kodea, $realisasi, $sisa, $prognosis);	

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),

	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	$pro_belanja_total = $prognosis;
	
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
		
		if ($data_kel->kodek=='52') {
			$rows[] = array(
				array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);			
		}
		
		read_realisasi_kelompok($kodeuk, $data_kel->kodek, $realisasi, $sisa, $prognosis);	
	
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
		
		//JENIS
		$bold_start = '';
		$bold_end = '';
		
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

			read_realisasi_jenis($kodeuk, $data_jen->kodej, $realisasi, $sisa, $prognosis);		
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)) . $bold_end, 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($sisa) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => $bold_start . apbd_fn($prognosis) . $bold_end, 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			
			//OBYEK
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
				
				$realisasi = 0; $prognosis = 0;
				read_realisasi_obyek($kodeuk, $data_oby->kodeo, $realisasi, $sisa, $prognosis);	
			
				$rows[] = array(
					array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' =>apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($sisa) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($prognosis) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
				);
				
			}	//obyek			
				
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$prognosis_netto = $pro_pendapata_total - $pro_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;'),
);


if (($kodeuk=='00') or ($kodeuk=='ZZ')) {
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
			
		read_realisasi_kelompok('ZZ', $data_kel->kodek, $realisasi, $sisa, $prognosis);	
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
		);		
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;
			$prognosis_netto_p += $prognosis;

		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
			$prognosis_netto_p -= $prognosis;
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
			
			read_realisasi_jenis('ZZ', $data_jen->kodej, $realisasi, $sisa, $prognosis);	
		
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
				array('data' => $data_jen->uraian, 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($sisa), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($prognosis), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			//OBYEK
			$query = db_select('obyek', 'o');
			$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
			$query->fields('o', array('kodeo', 'uraian'));
			$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->condition('o.kodej', $data_jen->kodej, '='); 
			$query->groupBy('o.kodeo');
			$query->orderBy('o.kodeo');
			$results_oby = $query->execute();	
			foreach ($results_oby as $data_oby) {
				
				read_realisasi_obyek('ZZ', $data_oby->kodeo, $realisasi, $sisa, $prognosis);	
			
				$rows[] = array(
					array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
					array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($sisa) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
					array('data' => apbd_fn($prognosis) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
				);
				
			}	//obyek			
				
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN THN BERJALAN</strong>', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($realisasi_netto, $prognosis_netto)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);
	
	
} else {
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-top:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-top:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
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



function read_realisasi_akun($kodeuk, $kodea, &$realisasi, &$sisa, &$prognosis) {
$realisasi = 0; $prognosis = 0; $sisa = 0; 
if ($kodeuk=='ZZ')
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosiskab} where left(kodeo,1)=:kodea', array(':kodea'=>$kodea));
else
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosisskpd} where kodeuk=:kodeuk and left(kodeo,1)=:kodea', array(':kodeuk'=>$kodeuk, ':kodea'=>$kodea));

foreach ($res as $data) {
	$realisasi = $data->jmlrealisasi;
	$prognosis = $data->jmlprognosis; $sisa = $data->jmlsisa;
}
return true;
}

function read_realisasi_kelompok($kodeuk, $kodek, &$realisasi, &$sisa, &$prognosis) {
$realisasi = 0; $prognosis = 0; $sisa = 0; 
if ($kodeuk=='ZZ')
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosiskab} where left(kodeo,2)=:kodek', array(':kodek'=>$kodek));
else
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosisskpd} where kodeuk=:kodeuk and left(kodeo,2)=:kodek', array(':kodeuk'=>$kodeuk, ':kodek'=>$kodek));
foreach ($res as $data) {
	$realisasi = $data->jmlrealisasi;
	$prognosis = $data->jmlprognosis; $sisa = $data->jmlsisa;
}	
return true;
}

function read_realisasi_jenis($kodeuk, $kodej, &$realisasi, &$sisa, &$prognosis) {
$realisasi = 0; $prognosis = 0; $sisa = 0; 
if ($kodeuk=='ZZ')
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosiskab} where left(kodeo,3)=:kodej', array(':kodej'=>$kodej));
else
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosisskpd} where kodeuk=:kodeuk and left(kodeo,3)=:kodej', array(':kodeuk'=>$kodeuk, ':kodej'=>$kodej));
foreach ($res as $data) {
	$realisasi = $data->jmlrealisasi;
	$prognosis = $data->jmlprognosis; $sisa = $data->jmlsisa;
}
return true;	
}

function read_realisasi_obyek($kodeuk, $kodeo, &$realisasi, &$sisa, &$prognosis) {
$realisasi = 0; $prognosis = 0 ; $sisa = 0; 
if ($kodeuk=='ZZ')
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosiskab} where kodeo=:kodeo', array(':kodeo'=>$kodeo));
else
	$res = db_query('select sum(realisasi) as jmlrealisasi, sum(sisa) as jmlsisa, sum(prognosis) as jmlprognosis from {prognosisskpd} where kodeuk=:kodeuk and kodeo=:kodeo', array(':kodeuk'=>$kodeuk, ':kodeo'=>$kodeo));
foreach ($res as $data) {
	$realisasi = $data->jmlrealisasi;
	$prognosis = $data->jmlprognosis; $sisa = $data->jmlsisa;
}
return true;	
}

?>


