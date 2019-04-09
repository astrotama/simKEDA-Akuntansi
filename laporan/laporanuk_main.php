<?php
function laporanuk_main($arg=NULL, $nama=NULL) {
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
		if ($akun=='4') {
			$output = gen_report_realisasi_pendapatan_print($kelompok, $bulan);
			apbd_ExportPDF_P($output, 10, "LAP.pdf");
		} else {
			$output = gen_report_realisasi_belanja_print($kelompok, $bulan);
			apbd_ExportPDF_P($output, 10, "LAP.pdf");
		}
		
	} elseif ($cetakpdf=='pdfdetil') {
			$output = gen_report_realisasi_pendapatan_print_detil($kelompok, $bulan);
			apbd_ExportPDF_P($output, 10, "LAP.pdf");

	} elseif ($cetakpdf=='pdfk') {
			$output = gen_report_realisasi_belanja_kelompok_print($kelompok, $bulan);
			apbd_ExportPDF_L($output, 10, "LAP.pdf");
		
	} elseif ($cetakpdf=='pdfj') {
			$output = gen_report_realisasi_belanja_jenis_print($kelompok, $bulan);
			apbd_ExportPDF_L($output, 10, "LAP.pdf");

	} elseif ($cetakpdf=='pdfr') {
			$output = gen_report_realisasi_belanja_jenis_modal_print($kelompok, $bulan);
			apbd_ExportPDF_L_Wide($output, 10, "LAP.pdf");
			
	} else if ($cetakpdf=='xls') {
 
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Belanja SKPD.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_belanja_print($kelompok, $bulan);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";

	}elseif ($cetakpdf=='xlsdetil') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan SKPD detil.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
			$output = gen_report_realisasi_pendapatan_print_detil($kelompok, $bulan);
			echo $output;

	} else if ($cetakpdf=='xlsk') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Belanja SKPD per Kelompok.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_belanja_kelompok_print($kelompok, $bulan);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		
	} else if ($cetakpdf=='xlsj') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Belanja SKPD per Jenis.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_belanja_jenis_print($kelompok, $bulan);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		
	} else {
		if ($akun=='4') {
			drupal_set_title('Realisasi Pendapatan per SKPD');
			$output = gen_report_realisasi_pendapatan($kelompok, $bulan);
		} else {
			drupal_set_title('Realisasi Belanja per SKPD');
			$output = gen_report_realisasi_belanja($kelompok, $bulan);
		}
		
		$output_form = drupal_get_form('laporanuk_main_form');
		
		
		//$btn = '';
		if ($akun=='4') {
			$btn = l('Cetak', 'laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/pdfdetil' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
			$btn .= '&nbsp;' . l('Excel', 'laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/xlsdetil' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));

			//$btn .= '&nbsp;' . l('Cetak Detil', 'laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/pdfdetil' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		} else {
			$btn = '<div class="btn-group">' .
					'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
					' Cetak <span class="caret"></span>' .
					'</button>' .
						'<ul class="dropdown-menu">' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/pdf">Belanja</a></li>' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/pdfk">Belanja per Kelompok</a></li>' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/pdfj">Belanja Langsung per Jenis</a></li>' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/pdfr">Belanja Langsung Rincian Modal</a></li>' .
						'</ul>' .
					'</div>';		 
			$btn .= '&nbsp;<div class="btn-group">' .
					'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
					' Excel <span class="caret"></span>' .
					'</button>' .
						'<ul class="dropdown-menu">' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/xls">Belanja</a></li>' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/xlsk">Belanja per Kelompok</a></li>' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/xlsj">Belanja Langsung per Jenis</a></li>' .
							'<li><a href="/laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/xlsr">Belanja Langsung Rincian Modal</a></li>' .
						'</ul>' .
					'</div>';			
		}
		return drupal_render($output_form) . $btn . $output . $btn;
	}	
	
}

function laporanuk_main_form_submit($form, &$form_state) {
	$akun= $form_state['values']['akun'];
	$bulan= $form_state['values']['bulan'];
	$kelompok= $form_state['values']['kelompok'];
	
	$uri = 'laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan;
	drupal_goto($uri);
	
}


function laporanuk_main_form($form, &$form_state) {
	
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
	
	if ($akun=='4') {
		$akun_str ='|PENDAPATAN';
		if (($kelompok=='') or ($kelompok=='SEMUA')) $kelompok = '2';
		
	} else {
		$akun_str ='|BELANJA';
	}	
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
	
	if ($akun=='5') {
		$form['formdata']['kelompok']= array(
			'#type' => 'select',		//'radios', 
			'#title' => t('Kelompok'), 
			'#default_value' => $kelompok,
			
			'#options' => array('SEMUA'=>'SEMUA', 
								'0'=>'DINAS/BADAN/KANTOR',
								'1'=>'KECAMATAN',
								'2'=>'PUSKESMAS',
								'3'=>'SEKOLAH',
								'4'=>'UPT DIKPORA'),	
		);		
		
	} else {
		$form['formdata']['kelompok']= array(
			'#type' => 'select',		//'radios', 
			'#title' => t('Tingkat'), 
			'#default_value' => $kelompok,
			
			'#options' => array('1'=>'SKPD', 
								'2'=>'REKENING',
								'3'=>'DETIL REKENING'),	
		);		
		
	}	
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

function gen_report_realisasi_pendapatan($kelompok, $bulan) {

$anggaran_total = 0;
$realisasi_total = 0;

if ($bulan=='0')
	$tanggal_akhir = apbd_tahun() . '-12-31';
else
	$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
$tanggal_awal = apbd_tahun() . '-01-01';

	
//TABEL
$header = array (
	array('data' => 'No','width' => '5px', 'valign'=>'top'),
	array('data' => 'SKPD', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggperuk', 'a');
$query->innerJoin('unitkerja', 'uk', 'a.kodeuk=uk.kodeuk');
$query->fields('uk', array('kodeuk', 'namauk'));
$query->addExpression('SUM(a.jumlah)', 'anggaran');
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.namasingkat');
$results = $query->execute();

$no = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	
	
	$realisasi = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('4') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => '<strong>' . $no . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->namauk . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$anggaran_total += $datas->anggaran;
	$realisasi_total += $realisasi;
	
	//REKENING
	if ($kelompok>='2') {
		$sql1 = db_select('anggperuk', 'a');
		$sql1->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
		$sql1->fields('a', array('kodero', 'jumlah'));
		$sql1->fields('ro', array('uraian'));
		$sql1->condition('a.kodeuk', $datas->kodeuk, '='); 
		$sql1->orderBy('ro.kodero');
		$res1 = $sql1->execute();
		$no1 = 0;
		foreach ($res1 as $data1) {
			
			$realisasi = 0;
			$sql2 = db_select('jurnal', 'j');
			$sql2->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql2->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql2->condition('j.kodeuk', $datas->kodeuk, '='); 
			$sql2->condition('ji.kodero', $data1->kodero, '='); 
			if ($bulan>0) $sql2->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res2 = $sql2->execute();
			foreach ($res2 as $data2) {
				$realisasi = $data2->realisasi;
			}
			
			$uraian = l($data1->uraian, '/akuntansi/buku/ZZ/'  . $data1->kodero  . '/'  . $datas->kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));	
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data1->jumlah), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data1->jumlah, $realisasi)), 'align' => 'right', 'valign'=>'top'),
			);
			
			//SUB REKENING
			if ($kelompok=='3') {
				$sql1 = db_select('rincianobyekdetil', 'rod');
				$sql1->fields('rod', array('koderod', 'uraian'));
				$sql1->condition('rod.kodero', $data1->kodero, '='); 
				$sql1->orderBy('rod.uraian');
				$res_detil = $sql1->execute();
				$no1 = 0;
				foreach ($res_detil as $data_detil) {
					
					$realisasi = 0;
					$sql2 = db_select('jurnal', 'j');
					$sql2->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql2->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql2->condition('j.kodeuk', $datas->kodeuk, '='); 
					$sql2->condition('ji.kodero', $data1->kodero, '='); 
					$sql2->condition('ji.koderod', $data_detil->koderod, '='); 
					if ($bulan>0) $sql2->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res2 = $sql2->execute();
					foreach ($res2 as $data2) {
						$realisasi = $data2->realisasi;
					}
					
					if ($realisasi>0) {
						$uraian = l('- ' . $data_detil->uraian, '/akuntansi/buku/ZZ/'  . $data1->kodero  . '/'  . $datas->kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/' . $data_detil->koderod, array('attributes' => array('class' => null)));	
						$rows[] = array(
							array('data' => '', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<em>' . $uraian . '</em>', 'align' => 'left', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<em>' . apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
					}	
				}			
				
			}
		}	

	}
}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_total, $realisasi_total)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_pendapatan_print($kelompok, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI PENDAPATAN', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
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
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realiasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggperuk', 'a');
$query->innerJoin('unitkerja', 'uk', 'a.kodeuk=uk.kodeuk');
$query->fields('uk', array('kodeuk', 'namauk'));
$query->addExpression('SUM(a.jumlah)', 'jumlah');
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.kodedinas');
$results = $query->execute();

$no = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	
	
	$realisasi = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('4') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'width' => '20px', 'align' => 'left', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->namauk, 'width' => '300px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->jumlah),'width' => '80px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->jumlah, $realisasi)),'width' => '30px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
	);
	
	$anggaran_total += $datas->jumlah;
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

function gen_report_realisasi_pendapatan_print_detil($kelompok, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI PENDAPATAN', 'width' => '510px', 'colspan'=>5, 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px', 'colspan'=>5, 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>5, 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>5, 'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));


//TABEL 
$rows = null;
$anggaran_total = 0;
$realisasi_total = 0;

//TABEL
$header = array (
	array('data' => 'No','width' => '40px', 'align'=>'center','style'=>'font-size:90%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'SKPD', 'width' => '280px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realiasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggperuk', 'a');
$query->innerJoin('unitkerja', 'uk', 'a.kodeuk=uk.kodeuk');
$query->fields('uk', array('kodeuk', 'namauk'));
$query->addExpression('SUM(a.jumlah)', 'jumlah');
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.namauk');
$results = $query->execute();

$no = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasi = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	$sql->condition('j.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('4') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => sprintf('%02d', $no), 'width' => '40px', 'align' => 'left', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->namauk . '</strong>', 'width' => '280px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->jumlah) . '</strong>','width' => '80px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->jumlah, $realisasi)) . '</strong>','width' => '30px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
	);
	
	$anggaran_total += $datas->jumlah;
	$realisasi_total += $realisasi;
			
			
	//rekening
	$sql1 = db_select('anggperuk', 'a');
	$sql1->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
	$sql1->fields('a', array('kodero', 'jumlah'));
	$sql1->fields('ro', array('uraian'));
	$sql1->condition('a.kodeuk', $datas->kodeuk, '='); 
	$res1 = $sql1->execute();
	$no1 = 0;
	foreach ($res1 as $data1) {
		$realisasi1 = 0;
		$sql2 = db_select('jurnal', 'j');
		$sql2->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql2->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql2->condition('j.kodeuk', $datas->kodeuk, '='); 
		$sql2->condition('ji.kodero', $data1->kodero, '='); 
		if ($bulan>0) $sql2->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res2 = $sql2->execute();
		foreach ($res2 as $data2) {
			$realisasi1 = $data2->realisasi;
		}
		
		$no1++;
		$rows[] = array(
			array('data' => sprintf('%02d', $no) . '.' . sprintf('%02d', $no1), 'width' => '40px', 'align' => 'left', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data1->uraian, 'width' => '280px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;'),
			array('data' => apbd_fn($data1->jumlah),'width' => '80px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
			array('data' => apbd_fn($realisasi1), 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
			array('data' => apbd_fn1(apbd_hitungpersen($data1->jumlah, $realisasi1)),'width' => '30px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		);
		
		//sub detil
		$sqlx = db_select('rincianobyekdetil', 'rod');
		$sqlx->fields('rod', array('koderod', 'uraian'));
		$sqlx->condition('rod.kodero', $data1->kodero, '='); 
		$sqlx->orderBy('rod.uraian');
		
		$res_detil = $sqlx->execute();
		$no2 = 0;
		foreach ($res_detil as $data_detil) {
			
			$realisasi = 0;
			$sql2 = db_select('jurnal', 'j');
			$sql2->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql2->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql2->condition('j.kodeuk', $datas->kodeuk, '='); 
			$sql2->condition('ji.kodero', $data1->kodero, '='); 
			$sql2->condition('ji.koderod', $data_detil->koderod, '='); 
			if ($bulan>0) $sql2->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res2 = $sql2->execute();
			foreach ($res2 as $data2) {
				$realisasi = $data2->realisasi;
			}
			
			if ($realisasi>0) {
				$no2++;
				$rows[] = array(
					array('data' => sprintf('%02d', $no) . '.' . sprintf('%02d', $no1) . '.' . sprintf('%02d', $no2), 'width' => '40px', 'align' => 'left', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => $data_detil->uraian, 'width' => '280px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;font-style:italic;'),
					array('data' => '','width' => '80px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($realisasi), 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;font-style:italic;'),
					array('data' => '','width' => '30px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;font-style:italic;'),
				);
			}
		}
		
		
	}

	//end prototype

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '','width' => '40px',  'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '280px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_total + $anggaran1_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_total + $realisasi1_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_total, $realisasi_total, $anggaran1_total, $realisasi1_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
);


//RENDER	
$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_belanja($kelompok, $bulan) {

$anggaran_total = 0;
$realisasi_total = 0;

//TABEL 
$rows = null;
$anggaran_total = 0;
$realisasi_total = 0;

//TABEL
$rows[] = array (
	array('data' => 'NO','width' => '20px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'OPD', 'width' => '235px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '190px', 'colspan'=>3, 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA LANGSUNG', 'width' => '190px', 'colspan'=>3, 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL', 'width' => '190px', 'colspan'=>3, 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$rows[] = array (
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

//$rows = array();

if ($kelompok=='SEMUA')
	$res_uk = db_query('select kodeuk, kodedinas, namasingkat from {unitkerja} order by kodedinas');
else
	$res_uk = db_query('select kodeuk, kodedinas, namasingkat from {unitkerja} where kelompok=:kelompok order by kodedinas', array(':kelompok'=>$kelompok));

$agg_btl_total = 0;
$agg_bl_total = 0;
$rea_btl_total = 0;
$rea_bl_total = 0;

$no = 0;
foreach ($res_uk as $data_uk) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	//ANGGARAN
	$agg_btl = 0; $agg_bl = 0;
	$res = db_query('select left(a.kodero,2) as rekening, sum(a.jumlah) as jumlah from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg  where k.kodeuk=:kodeuk group by left(a.kodero,2)', array(':kodeuk'=>$data_uk->kodeuk));
	foreach ($res as $data) {
		if ($data->rekening=='51')
			$agg_btl  = $data->jumlah;
		else
			$agg_bl  = $data->jumlah;
	}
	
	//REALISASI
	$rea_btl = 0; $rea_bl = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('51') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_btl = $data->realisasi;
	}
	
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('52') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_bl = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $data_uk->namasingkat, 'width' => '235px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($agg_btl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_btl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_btl, $rea_btl)),'width' => '30px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($agg_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_bl, $rea_bl)),'width' => '30px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
 
		array('data' => apbd_fn($agg_btl + $agg_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_btl + $rea_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_btl + $agg_bl, $rea_btl + $rea_bl)),'width' => '30px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		
	);
	
	$agg_btl_total += $agg_btl;
	$agg_bl_total += $agg_bl;

	$rea_btl_total += $rea_btl;
	$rea_bl_total += $rea_bl;
	
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '','width' => '20px',  'align'=>'left','style'=>'font-size:100%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '235px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' . apbd_fn($agg_btl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_btl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_btl_total, $rea_btl_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),

	array('data' => '<strong>' . apbd_fn($agg_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_bl_total, $rea_bl_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),

	array('data' => '<strong>' . apbd_fn($agg_btl_total+$agg_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_btl_total+$rea_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_btl_total+$agg_bl_total, $rea_btl_total+$rea_bl_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	
);

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_belanja_print($kelompok, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI BELANJA', 'width' => '510px',  'colspan'=>5,'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '510px', 'colspan'=>5, 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '510px', 'colspan'=>5, 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>5, 'width' => '510px', 'align'=>'center','style'=>'border:none'),
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
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
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
$query->addExpression('SUM(a.jumlah)', 'jumlah');
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.namauk');
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
		array('data' => $no, 'width' => '20px', 'align' => 'center', 'style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->namauk, 'width' => '300px', 'align'=>'left','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->jumlah),'width' => '80px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn($realisasi), 'width' => '80px', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($datas->jumlah, $realisasi)),'width' => '30px',  'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
	);
	
	$anggaran_total += $datas->jumlah;
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

function gen_report_realisasi_belanja_jenis_print($kelompok, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI BELANJA PER JENIS', 'width' => '825px', 'colspan'=>14, 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '825px', 'colspan'=>14, 'align'=>'center','style'=>'font-size:110%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '825px', 'colspan'=>14, 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>14, 'width' => '825px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));


//TABEL 
$rows = null;
$anggaran_total = 0;
$realisasi_total = 0;
if (arg(5) == 'xlsj'){
//TABEL
$header[] = array (
	array('data' => 'NO','width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'OPD', 'width' => '195px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PEGAWAI Anggaran', 'width' => '60px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PEGAWAI Realisasi', 'width' => '60px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PEGAWAI (%)', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BARANG JASA Anggaran', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BARANG JASA Realisasi', 'width' => '65px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BARANG JASA %', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Anggaran', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Realisasi', 'width' => '65px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL %', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL Anggaran', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL Realisasi', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL %', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
}else{
$header[] = array (
	array('data' => 'NO','width' => '20px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'OPD', 'width' => '195px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PEGAWAI', 'width' => '140px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BARANG JASA', 'width' => '155px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL', 'width' => '155px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL', 'width' => '160px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$header[] = array (
	array('data' => 'jumlah', 'width' => '60px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '60px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
	array('data' => 'jumlah', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '65px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
	array('data' => 'jumlah', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '65px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),

	array('data' => 'jumlah', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '70px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
}
$rows = array();

if ($kelompok=='SEMUA')
	$res_uk = db_query('select kodeuk, kodedinas, namauk from {unitkerja} order by namauk');
else
	$res_uk = db_query('select kodeuk, kodedinas, namauk from {unitkerja} where kelompok=:kelompok order by namauk', array(':kelompok'=>$kelompok));

$agg_peg_total = 0;
$agg_barang_total = 0;
$agg_modal_total = 0;

$rea_peg_total = 0;
$rea_barang_total = 0;
$rea_moda_total = 0;

$no = 0;
foreach ($res_uk as $data_uk) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	//ANGGARAN
	$agg_peg = 0; $agg_barang = 0; ; $agg_modal = 0;
	$res = db_query('select left(a.kodero,3) as rekening, sum(a.jumlah) as jumlah from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg  where k.kodeuk=:kodeuk and k.jenis=2 group by left(a.kodero,3)', array(':kodeuk'=>$data_uk->kodeuk));
	foreach ($res as $data) {
		if ($data->rekening=='521')
			$agg_peg  = $data->jumlah;
		else if ($data->rekening=='522')
			$agg_barang  = $data->jumlah;
		else 
			$agg_modal  = $data->jumlah;
	}
	
	//REALISASI
	$rea_peg = 0; $rea_barang = 0; $rea_modal = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('521') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_peg = $data->realisasi;
	}
	
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('522') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_barang = $data->realisasi;
	}

	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('523') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_modal = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $data_uk->namauk, 'width' => '195px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($agg_peg),'width' => '60px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_peg),'width' => '60px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_peg, $rea_peg)),'width' => '20px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($agg_barang),'width' => '70px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_barang),'width' => '65px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_barang, $rea_barang)),'width' => '20px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
 
		array('data' => apbd_fn($agg_modal),'width' => '70px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_modal),'width' => '65px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_modal, $rea_modal)),'width' => '20px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),

		array('data' => apbd_fn($agg_peg + $agg_barang + $agg_modal),'width' => '70px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_peg + $rea_barang + $rea_modal),'width' => '70px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_peg + $agg_barang + $agg_modal, $rea_peg + $rea_barang + $rea_modal)),'width' => '20px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		
	);
	
	$agg_peg_total += $agg_peg;
	$agg_barang_total += $agg_barang;
	$agg_modal_total += $agg_modal;

	$rea_peg_total += $rea_peg;
	$rea_barang_total += $rea_barang;
	$rea_modal_total += $rea_modal;
	
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '','width' => '20px',  'align'=>'left','style'=>'font-size:100%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '195px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' . apbd_fn($agg_peg_total) . '</strong>', 'width' => '60px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_peg_total) . '</strong>', 'width' => '60px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_peg_total, $rea_peg_total)) . '</strong>', 'width' => '20px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),

	array('data' => '<strong>' . apbd_fn($agg_barang_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_barang_total) . '</strong>', 'width' => '65px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_barang_total, $rea_barang_total)) . '</strong>', 'width' => '20px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),

	array('data' => '<strong>' . apbd_fn($agg_modal_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_modal_total) . '</strong>', 'width' => '65px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_modal_total, $rea_modal_total)) . '</strong>', 'width' => '20px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),

	array('data' => '<strong>' . apbd_fn($agg_peg_total+$agg_barang_total+$agg_modal_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_peg_total+$rea_barang_total+$rea_modal_total) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_peg_total+$agg_barang_total+$agg_modal_total, $rea_peg_total+$rea_barang_total+$rea_modal_total)) . '</strong>', 'width' => '20px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	
);


//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_belanja_kelompok_print($kelompok, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI BELANJA PER KELOMPOK', 'width' => '810px', 'colspan'=>11, 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '810px', 'colspan'=>11, 'align'=>'center','style'=>'font-size:110%;border:none'),
); 

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '810px', 'colspan'=>11, 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>11, 'width' => '810px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));


//TABEL 
$rows = null;
$anggaran_total = 0;
$realisasi_total = 0;
if (arg(5) == 'xlsk'){
//TABEL
$header[] = array (
	array('data' => 'NO','width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'OPD', 'width' => '235px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA TL Anggaran', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA TL Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA TL %', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA L Anggaran', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA L Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA L %', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL Anggaran', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL %', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
}else{
$header[] = array (
	array('data' => 'NO','width' => '20px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'OPD', 'width' => '235px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '190px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BELANJA LANGSUNG', 'width' => '190px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL', 'width' => '190px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$header[] = array (
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'jumlah', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '80px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => '%', 'width' => '30px', 'align'=>'center','style'=>'font-size:100%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
}
$rows = array();

if ($kelompok=='SEMUA')
	$res_uk = db_query('select kodeuk, kodedinas, namauk from {unitkerja} order by namauk');
else
	$res_uk = db_query('select kodeuk, kodedinas, namauk from {unitkerja} where kelompok=:kelompok order by namauk', array(':kelompok'=>$kelompok));

$agg_btl_total = 0;
$agg_bl_total = 0;
$rea_btl_total = 0;
$rea_bl_total = 0;

$no = 0;
foreach ($res_uk as $data_uk) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	//ANGGARAN
	$agg_btl = 0; $agg_bl = 0;
	$res = db_query('select left(a.kodero,2) as rekening, sum(a.jumlah) as jumlah from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg  where k.kodeuk=:kodeuk group by left(a.kodero,2)', array(':kodeuk'=>$data_uk->kodeuk));
	foreach ($res as $data) {
		if ($data->rekening=='51')
			$agg_btl  = $data->jumlah;
		else
			$agg_bl  = $data->jumlah;
	}
	
	//REALISASI
	$rea_btl = 0; $rea_bl = 0;
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('51') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_btl = $data->realisasi;
	}
	
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('52') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_bl = $data->realisasi;
	}
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'width' => '20px', 'align'=>'center','style'=>'font-size:100%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $data_uk->namauk, 'width' => '235px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($agg_btl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_btl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_btl, $rea_btl)),'width' => '30px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		
		array('data' => apbd_fn($agg_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_bl, $rea_bl)),'width' => '30px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
 
		array('data' => apbd_fn($agg_btl + $agg_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn($rea_btl + $rea_bl),'width' => '80px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => apbd_fn1(apbd_hitungpersen($agg_btl + $agg_bl, $rea_btl + $rea_bl)),'width' => '30px',  'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;'),
		
	);
	
	$agg_btl_total += $agg_btl;
	$agg_bl_total += $agg_bl;

	$rea_btl_total += $rea_btl;
	$rea_bl_total += $rea_bl;
	
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '','width' => '20px',  'align'=>'left','style'=>'font-size:100%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>TOTAL</strong>', 'width' => '235px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	
	array('data' => '<strong>' . apbd_fn($agg_btl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_btl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_btl_total, $rea_btl_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),

	array('data' => '<strong>' . apbd_fn($agg_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_bl_total, $rea_bl_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),

	array('data' => '<strong>' . apbd_fn($agg_btl_total+$agg_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($rea_btl_total+$rea_bl_total) . '</strong>', 'width' => '80px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($agg_btl_total+$agg_bl_total, $rea_btl_total+$rea_bl_total)) . '</strong>', 'width' => '30px', 'align'=>'right','style'=>'font-size:100%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	
);


//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_belanja_jenis_modal_print($kelompok, $bulan) {

$rows[] = array(
	array('data' => 'LAPORAN REALISASI BELANJA LANGSUNG PER JENIS/RINCI', 'width' => '825px', 'colspan'=>19, 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '825px', 'colspan'=>19, 'align'=>'center','style'=>'font-size:110%;border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '825px', 'colspan'=>19, 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(),  'colspan'=>19,'width' => '825px', 'align'=>'center','style'=>'border:none'),
	);
}

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));


//TABEL 
$rows = null;
$anggaran_total = 0;
$realisasi_total = 0;
if (arg(5) == 'xlsr'){
//TABEL
$header[] = array (
	array('data' => '','width' => '800px', 'align'=>'right','style'=>'font-size:70%;border-none;'),
	array('data' => 'dalam ribuan rupiah','width' => '75px', 'align'=>'right','style'=>'font-size:70%;border-none;'),
);
$header[] = array (
	array('data' => 'NO','width' => '15px', 'align'=>'center','style'=>'font-size:85%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'OPD', 'width' => '80px',  'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PEGAWAI Anggaran', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PEGAWAI Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BARANG JASA Anggaran', 'width' => '50px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BARANG JASA Realisasi', 'width' => '50px',  'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
	array('data' => 'MODAL Tanah Anggaran', 'width' => '35px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Tanah Realisasi', 'width' => '35px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Mesin Anggaran', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Mesin Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Gedung Anggaran', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Gedung Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Jaringan Anggaran', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL Jaringan Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL ATL BOS Anggaran', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL ATL BOS Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL JUMLAH Anggaran', 'width' => '50px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL JUMLAH Realisasi', 'width' => '50px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
	array('data' => 'TOTAL Anggaran', 'width' => '55px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL Realisasi', 'width' => '55px',  'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

}else{
$header[] = array (
	array('data' => '','width' => '800px', 'align'=>'right','style'=>'font-size:70%;border-none;'),
	array('data' => 'dalam ribuan rupiah','width' => '75px', 'align'=>'right','style'=>'font-size:70%;border-none;'),
);
$header[] = array (
	array('data' => '','width' => '15px', 'align'=>'center','style'=>'font-size:85%;border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '80px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-right:1px solid black;'),
	array('data' => 'PEGAWAI', 'width' => '80px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'BARANG JASA', 'width' => '100px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'MODAL', 'width' => '490px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'TOTAL', 'width' => '110px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);
$header[] = array (
	array('data' => 'NO','width' => '15px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-left:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'OPD', 'width' => '80px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-bottom:1px solid black;border-right:1px solid black;'),

	array('data' => 'jumlah', 'width' => '40px','rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '40px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
	array('data' => 'jumlah', 'width' => '50px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '50px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
	array('data' => 'Tanah', 'width' => '70px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Mesin', 'width' => '80px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Gedung', 'width' => '80px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Jaringan', 'width' => '80px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'ATL + BOS', 'width' => '80px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'JUMLAH', 'width' => '100px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),

	array('data' => 'jumlah', 'width' => '55px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '55px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
);

$header[] = array (
	array('data' => 'jumlah', 'width' => '35px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '35px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
	array('data' => 'jumlah', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),

	array('data' => 'jumlah', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),

	array('data' => 'jumlah', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),

	array('data' => 'jumlah', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '40px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),

	array('data' => 'jumlah', 'width' => '50px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'Realisasi', 'width' => '50px', 'align'=>'center','style'=>'font-size:85%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	
);
}
$rows = array();

if ($kelompok=='SEMUA')
	$res_uk = db_query('select kodeuk, kodedinas, namasingkat from {unitkerja} order by namauk');
else
	$res_uk = db_query('select kodeuk, kodedinas, namasingkat from {unitkerja} where kelompok=:kelompok order by namauk', array(':kelompok'=>$kelompok));

$agg_peg_total = 0;
$agg_barang_total = 0;
$agg_modal_total = 0;

$agg_modal_1_total = 0;
$agg_modal_2_total = 0;
$agg_modal_3_total = 0;
$agg_modal_4_total = 0;
$agg_modal_5_total = 0;

$rea_peg_total = 0;
$rea_barang_total = 0;
$rea_modal_total = 0;

$rea_modal_1_total = 0;
$rea_modal_2_total = 0;
$rea_modal_3_total = 0;
$rea_modal_4_total = 0;
$rea_modal_5_total = 0;

$no = 0;
$seribu = 1000;
foreach ($res_uk as $data_uk) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));

	//ANGGARAN
	$agg_peg = 0; $agg_barang = 0; $agg_modal = 0;
	$agg_modal_1 = 0; $agg_modal_2 = 0; $agg_modal_3 = 0; $agg_modal_4 = 0; $agg_modal_5 = 0; 
	
	
	$res = db_query('select left(a.kodero,3) as rekening, sum(a.jumlah) as jumlah from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg  where k.kodeuk=:kodeuk and k.jenis=2 group by left(a.kodero,3)', array(':kodeuk'=>$data_uk->kodeuk));
	foreach ($res as $data) {
		if ($data->rekening=='521')
			$agg_peg  = $data->jumlah;
		else if ($data->rekening=='522')
			$agg_barang  = $data->jumlah;
		else 
			$agg_modal  = $data->jumlah;
	}

	$res = db_query('select left(a.kodero,5) as rekening, sum(a.jumlah) as jumlah from {kegiatanskpd} as k inner join {anggperkeg} as a on k.kodekeg=a.kodekeg  where k.kodeuk=:kodeuk and k.jenis=2 and left(a.kodero,3)=:modal group by left(a.kodero,5)', array(':kodeuk'=>$data_uk->kodeuk, ':modal'=>'523'));
	foreach ($res as $data) {
		if ($data->rekening=='52301')
			$agg_modal_1  = $data->jumlah;
		else if ($data->rekening=='52302')
			$agg_modal_2  = $data->jumlah;
		else if ($data->rekening=='52303')
			$agg_modal_3  = $data->jumlah;
		else if ($data->rekening=='52304')
			$agg_modal_4  = $data->jumlah;
		else 
			$agg_modal_5  += $data->jumlah;
	}
	
	//REALISASI
	$rea_peg = 0; $rea_barang = 0; $rea_modal = 0;
	$rea_modal_1 = 0; $rea_modal_2 = 0; $rea_modal_3 = 0; $rea_modal_4 = 0; $rea_modal_5 = 0; 
	
	
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('521') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_peg = $data->realisasi;
	}
	
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('522') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$rea_barang = $data->realisasi;
	}
	
	//MODAL
	/*
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('kegiatanskpd', 'keg', 'j.kodekeg=keg.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->fields('ji', array('namasingkat','kodeuk'));
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('j.kodeuk', $data_uk->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('523') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	*/
	
	
	if ($bulan==0)
		$res = db_query('select left(ji.kodero,5) as rekening, sum(ji.debet-ji.kredit) as realisasi from {jurnal} as j inner join {jurnalitem} as ji on j.jurnalid=ji.jurnalid inner join {kegiatanskpd} as k on j.kodekeg=k.kodekeg where j.kodeuk=:kodeuk and left(ji.kodero,3)=:modal group by left(ji.kodero,5)', array(':kodeuk'=>$data_uk->kodeuk, ':modal'=>'523'));
	else
		$res = db_query('select left(ji.kodero,5) as rekening, sum(ji.debet-ji.kredit) as realisasi from {jurnal} as j inner join {jurnalitem} as ji on j.jurnalid=ji.jurnalid inner join {kegiatanskpd} as k on j.kodekeg=k.kodekeg where j.kodeuk=:kodeuk and left(ji.kodero,3)=:modal and month(j.tanggal)<=:bulan group by left(ji.kodero,5)', array(':kodeuk'=>$data_uk->kodeuk, ':modal'=>'523', ':bulan'=>$bulan));
	

	
	foreach ($res as $data) {
		if ($data->rekening=='52301')
			$rea_modal_1  = $data->realisasi;
		else if ($data->rekening=='52302')
			$rea_modal_2  = $data->realisasi;
		else if ($data->rekening=='52303')
			$rea_modal_3  = $data->realisasi;
		else if ($data->rekening=='52304')
			$rea_modal_4  = $data->realisasi;
		else 
			$rea_modal_5  += $data->realisasi;
		
		$rea_modal += $data->realisasi;
	}
	
	
	
	$no++;
	$rows[] = array(
		array('data' => $no, 'width' => '15px', 'align'=>'center','style'=>'font-size:85%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => $data_uk->namasingkat, 'width' => '80px', 'align'=>'left','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		
		array('data' => apbd_fn($agg_peg/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_peg/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		
		array('data' => apbd_fn($agg_barang/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_barang/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
 
 
		array('data' => apbd_fn($agg_modal_1/$seribu),'width' => '35px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_modal_1/$seribu),'width' => '35px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),

		array('data' => apbd_fn($agg_modal_2/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_modal_2/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),

		array('data' => apbd_fn($agg_modal_3/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_modal_3/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),

		array('data' => apbd_fn($agg_modal_4/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_modal_4/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),

		array('data' => apbd_fn($agg_modal_5/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_modal_5/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),

		array('data' => apbd_fn($agg_modal/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn($rea_modal/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		
		
		array('data' => apbd_fn(($agg_peg + $agg_barang + $agg_modal)/$seribu),'width' => '55px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		array('data' => apbd_fn(($rea_peg + $rea_barang + $rea_modal)/$seribu),'width' => '55px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-bottom:1px solid gray;'),
		
	);
	
	$agg_peg_total += $agg_peg;
	$agg_barang_total += $agg_barang;
	$agg_modal_total += $agg_modal;

	$rea_peg_total += $rea_peg;
	$rea_barang_total += $rea_barang;
	$rea_modal_total += $rea_modal;
	
	$agg_modal_1_total += $agg_modal_1;
	$agg_modal_2_total += $agg_modal_2;
	$agg_modal_3_total += $agg_modal_3;
	$agg_modal_4_total += $agg_modal_4;
	$agg_modal_5_total += $agg_modal_5;
	
	$rea_modal_1_total += $rea_modal_1;
	$rea_modal_2_total += $rea_modal_2;
	$rea_modal_3_total += $rea_modal_3;
	$rea_modal_4_total += $rea_modal_4;
	$rea_modal_5_total += $rea_modal_5;
	 

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '', 'width' => '15px', 'align'=>'center','style'=>'font-size:85%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => 'TOTAL', 'width' => '80px', 'align'=>'left','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	
	array('data' => apbd_fn($agg_peg_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_peg_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	
	array('data' => apbd_fn($agg_barang_total/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_barang_total/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),


	array('data' => apbd_fn($agg_modal_1_total/$seribu),'width' => '35px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_modal_1_total/$seribu),'width' => '35px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),

	array('data' => apbd_fn($agg_modal_2_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_modal_2_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),

	array('data' => apbd_fn($agg_modal_3_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_modal_3_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),

	array('data' => apbd_fn($agg_modal_4_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_modal_4_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),

	array('data' => apbd_fn($agg_modal_5_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_modal_5_total/$seribu),'width' => '40px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),

	array('data' => apbd_fn($agg_modal_total/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn($rea_modal_total/$seribu),'width' => '50px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	
	
	array('data' => apbd_fn(($agg_peg_total + $agg_barang_total + $agg_modal_total)/$seribu),'width' => '55px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => apbd_fn(($rea_peg_total + $rea_barang_total + $rea_modal_total)/$seribu),'width' => '55px',  'align'=>'right','style'=>'font-size:85%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
);	

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>


