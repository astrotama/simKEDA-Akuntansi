<?php
function laporanuk_rekonsp2d_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	
	if (arg(1)!=null) {
		switch($arg) {
			case 'filter':
				$akun = arg(2);
				$kelompok = arg(3);			
				$bulan = arg(4);
				$cetakpdf = arg(5);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$akun = '5';	
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kelompok = 'SEMUA';
	}

	if ($akun=='') $akun = '5';	
	if ($bulan=='') $bulan = date('m');		//variable_get('apbdtahun', 0);
	if ($kelompok=='') $kelompok = 'SEMUA';
	
	if ($cetakpdf=='pdf') {
	
	} elseif ($cetakpdf=='excel') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Rekon Realisasi Belanja per SKPD dan SP2D.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_belanja($kelompok, $bulan);
		echo $output;

		
	} else {
		if ($akun=='4') {
			
		} else {
			drupal_set_title('Rekon Realisasi Belanja per SKPD/SP2D');
			$output = gen_report_realisasi_belanja($kelompok, $bulan);
		}
		
		$output_form = drupal_get_form('laporanuk_rekonsp2d_main_form');
		
		
		//$btn = '';
		$btn = l('Excel', 'laporanrekonskpdsp2d/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
	}	
	
}

function laporanuk_rekonsp2d_main_form_submit($form, &$form_state) {
	$akun= $form_state['values']['akun'];
	$bulan= $form_state['values']['bulan'];
	$kelompok= $form_state['values']['kelompok'];
	
	$uri = 'laporanrekonskpdsp2d/filter/' . $akun . '/' . $kelompok . '/' . $bulan;
	drupal_goto($uri);
	
}


function laporanuk_rekonsp2d_main_form($form, &$form_state) {
	
	$akun = '5';
	$bulan = date('m');
	$kelompok = 'SEMUA';
	
	
	if(arg(2)!=null){
		$akun = arg(2);
		$kelompok = arg(3);			
		$bulan = arg(4);
		
		//drupal_set_message($akun);
	}

	if ($akun=='') $akun = '5';	
	if ($bulan=='') $bulan = date('m');		//variable_get('apbdtahun', 0);
	if ($kelompok=='') $kelompok = 'SEMUA';
	
	if ($akun=='4')
		$akun_str ='|PENDAPATAN';
	else
		$akun_str ='|BELANJA';
		
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . '|' . $kelompok . $akun_str . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	$form['formdata']['akun']= array(
		'#type' => 'value',		//'radios', 
		'#value' => $akun,
	);
	
	$form['formdata']['kelompok']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kelompok,
		
		'#options' => array('ZZ'=>'SEMUA', 
							'0'=>'DINAS/BADAN/KANTOR',
							'1'=>'KECAMATAN',
							'2'=>'PUSKESMAS',
							'3'=>'SEKOLAH',
							'4'=>'UPT DIKPORA'),	
	);		
	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
			 '0' => t('SETAHUN'), 	
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
		   ),
	);
	
 
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}


function gen_report_realisasi_belanja($kelompok, $bulan) {

$anggaran_total = 0;
$realisasi_total = 0;
$dinas_total = 0;
$sp2d_total = 0;
$cp_total = 0;
$cpspj_total = 0;
$manual_total = 0;

//TABEL
$header = array (
	array('data' => 'SKPD', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => '[P]Pusat', 'width' => '90px', 'valign'=>'top'),
	array('data' => '[D]Dinas', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Sel(P-D)', 'width' => '60px', 'valign'=>'top'),
	array('data' => '[S]SP2D', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Sel(P-S)', 'width' => '60px', 'valign'=>'top'),
	array('data' => 'CP/Akt', 'width' => '60px', 'valign'=>'top'),
	array('data' => 'CP/SPJ', 'width' => '60px', 'valign'=>'top'),
	array('data' => 'Manual', 'width' => '60px', 'valign'=>'top'),
);
$rows = array();

// * BELANJA * //
//AKUN
$query = db_select('anggperkeg', 'a');
$query->innerJoin('kegiatanskpd', 'keg', 'a.kodekeg=keg.kodekeg');
$query->innerJoin('unitkerja', 'uk', 'keg.kodeuk=uk.kodeuk');
$query->fields('uk', array('kodeuk', 'namasingkat'));
$query->addExpression('SUM(a.anggaran)', 'anggaran');

if ($kelompok != 'ZZ') $query->condition('uk.kelompok', $kelompok, '='); 

$query->groupBy('uk.kodeuk');
$query->orderBy('uk.kodedinas');
$results = $query->execute();

$no = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	
	//REALISASI
	$realisasi = 0;
	if ($datas->kodeuk=='00') {
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
		
		$or = db_or();
		$or->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
		$or->condition('ji.kodero', db_like('62') . '%', 'LIKE'); 
		
		$sql->condition($or); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
 
	} else {
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
		$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	}
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	//DINAS
	$dinas = 0;
	if ($datas->kodeuk=='00') {
		$sql = db_select('jurnaluk', 'j');
		$sql->innerJoin('jurnalitemuk', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
		
		$or = db_or();
		$or->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
		$or->condition('ji.kodero', db_like('62') . '%', 'LIKE'); 
		
		$sql->condition($or); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
 
	} else {
		$sql = db_select('jurnaluk', 'j');
		$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
		$sql->innerJoin('jurnalitemuk', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
		$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	}
	$res = $sql->execute();
	foreach ($res as $data) {
		$dinas = $data->realisasi;
	}
	
	//CP
	$manual = 0;
	$cp = 0;
	if ($datas->kodeuk=='00') {
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit)', 'cp');
		$sql->addExpression('SUM(ji.debet)', 'manual');
		$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
		$sql->condition('j.jenis', 'umum-spj', '='); 
		
		$or = db_or();
		$or->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
		$or->condition('ji.kodero', db_like('62') . '%', 'LIKE'); 
		
		$sql->condition($or); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	} else {
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit)', 'cp');
		$sql->addExpression('SUM(ji.debet)', 'manual');
		$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
		$sql->condition('j.jenis', 'umum-spj', '='); 
		$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	}
	$res = $sql->execute();
	foreach ($res as $data) {
		$manual = $data->manual;
		$cp = $data->cp;
	}	
	
	//SP2D
	$sp2d = 0;
	db_set_active('penatausahaan');
	$sql = db_select('dokumen', 'j');
	$sql->addExpression('SUM(j.jumlah)', 'sp2d');
	$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('j.jenisdokumen', array(1, 3, 4, 5, 7), 'IN');
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
	$sql->condition('j.sp2dok', '1', '='); 
	$sql->condition('j.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$sp2d = $data->sp2d;
	}
	
	//CP SPJ
	$cpspj = 0;
	db_set_active('bendahara');
	$sql = db_select('bendahara', 'b');
	$sql->innerJoin('bendaharaitem', 'bi', 'b.bendid=bi.bendid');
	$sql->addExpression('SUM(bi.jumlah)', 'cpspj');
	$sql->condition('b.kodeuk', $datas->kodeuk, '='); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.tanggal) <= :month', array('month' => $bulan));
	$sql->condition('b.jenis', 'ret-spj', '='); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$cpspj = $data->cpspj;
	}	
	db_set_active();
	
	$no++;
	
	$selisihdinas = $realisasi - $dinas;
	$selisih = $realisasi - $sp2d;
	
	//'style'=>'border:none'
	$style = 'none';
	if ($selisih != 0) {
		if (($selisih + $cp)==0) 
			$style = 'color:blue';
		else
			$style = ($selisih==$manual? 'color:green' : 'color:red');
	}
	
	$stylecp = ($cp == $cpspj? $style : 'color:#ff6600');
	
	$styledinas = ($realisasi == $dinas? $style : 'color:#ff6600');
	
	$rows[] = array(
		array('data' => $datas->namasingkat, 'align' => 'left', 'valign'=>'top', 'style'=>$style),
		array('data' => apbd_fn($datas->anggaran), 'align' => 'right', 'valign'=>'top', 'style'=>$style),
		array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'style'=>$style),
		array('data' => apbd_fn($dinas), 'align' => 'right', 'valign'=>'top', 'style'=>$styledinas),
		array('data' => apbd_fn($selisihdinas), 'align' => 'right', 'valign'=>'top', 'style'=>$styledinas),
		array('data' => apbd_fn($sp2d), 'align' => 'right', 'valign'=>'top', 'style'=>$style),
		array('data' => apbd_fn($selisih), 'align' => 'right', 'valign'=>'top', 'style'=>$style),
		array('data' => apbd_fn($cp), 'align' => 'right', 'valign'=>'top', 'style'=>$stylecp),
		array('data' => apbd_fn($cpspj), 'align' => 'right', 'valign'=>'top', 'style'=>$stylecp),
		array('data' => apbd_fn($manual), 'align' => 'right', 'valign'=>'top', 'style'=>$style),
	);
	
	$anggaran_total += $datas->anggaran;
	$realisasi_total += $realisasi;
	$dinas_total += $dinas;
	$sp2d_total += $sp2d;
	$cp_total += $cp;
	$cpspj_total += $cpspj;
	$manual_total += $manual;
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($dinas_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_total - $dinas_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	
	array('data' => '<strong>' . apbd_fn($sp2d_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_total - $sp2d_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($cp_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($cpspj_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($manual_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_belanja_print($kelompok, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI BELANJA', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
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


//TABEL 
$rows = null;
$anggaran_total = 0;
$realisasi_total = 0;

//TABEL
$header = array (
	array('data' => 'No','width' => '20px', 'align'=>'center','style'=>'font-size:90%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'SKPD', 'width' => '300px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Anggaran', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realiasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$rows = array();

// * BELANJA * //
//AKUN
$query = db_select('anggperkeg', 'a');
$query->innerJoin('kegiatanskpd', 'keg', 'a.kodekeg=keg.kodekeg');
$query->innerJoin('unitkerja', 'uk', 'keg.kodeuk=uk.kodeuk');
$query->fields('uk', array('kodeuk', 'namauk'));
$query->addExpression('SUM(a.anggaran)', 'anggaran');
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.kodedinas');
$results = $query->execute();

$no = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	
	
	$realisasi = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'width' => '20px', 'align' => 'left', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->namauk, 'width' => '300px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->anggaran),'width' => '80px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)),'width' => '30px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
	);
	
	$anggaran_total += $datas->anggaran;
	$realisasi_total += $realisasi;
	
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '','width' => '20px',  'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_total, $realisasi_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
);


//RENDER	
$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

?>


