<?php
function laporan_fulengkap_main($arg=NULL, $nama=NULL) {
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
				$margin =arg(3);
				$tanggal =arg(4);
				$cetakpdf = arg(5);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('n');		//variable_get('apbdtahun', 0);
		$margin = '15';
		$tanggal = date('j F Y');
		
	}
	
	//drupal_set_title('BELANJA');
	
	 
	if ($cetakpdf=='pdf_fu') {

		$output = gen_report_realisasi_print($bulan, $tanggal);
		apbd_ExportPDF_L($output, $margin, "LAP");
		
	} else if ($cetakpdf=='pdf_f') {

		$output = gen_report_realisasi_print_fungsi($bulan, $tanggal);
		apbd_ExportPDF_L($output, $margin, "LAP");
				
	} else if ($cetakpdf=='pdf_u') {

		$output = gen_report_realisasi_print_urusan($bulan, $tanggal);
		apbd_ExportPDF_L($output, $margin, "LAP");
				
	} elseif ($cetakpdf=='xls_fu') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realisasi Per Fungsi Urusan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat,$margin,$tanggal);
		echo $output;
				
	} elseif ($cetakpdf=='xls_f') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realisasi Per Fungsi.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print_fungsi($bulan, $kodeuk, $tingkat,$margin,$tanggal);
		echo $output;

	} elseif ($cetakpdf=='xls_u') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realisasi Per Urusan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print_urusan($bulan, $kodeuk, $tingkat,$margin,$tanggal);
		echo $output;
		
	} else {
		$output = gen_report_realisasi_urusan($bulan);
		$output_form = drupal_get_form('laporan_fulengkap_main_form');	
		
		//$btn = l('Cetak', 'laporanfu/filter/' . $bulan .'/'.$margin.'/'.$tanggal. '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		//$btn .= l('Excel', 'laporanfu/filter/' . $bulan .'/'.$margin.'/'.$tanggal. '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-success')));

		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/pdf_fu">Fungsi/Urusan</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/pdf_f">Fungsi</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/pdf_u">Urusan</a></li>' .
					'</ul>' .
				'</div>';		
		$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/xls_fu">Fungsi/Urusan</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/xls_f">Fungsi</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/xls_u">Urusan</a></li>' .
					'</ul>' .
				'</div>';
				
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporan_fulengkap_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$margin = $form_state['values']['margin'];
	$tanggal = $form_state['values']['tanggal'];
	$uri = 'laporanfu/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin. '/' . $tanggal;
	drupal_goto($uri);
	
}


function laporan_fulengkap_main_form($form, &$form_state) {
	
	$bulan = date('n');
	$margin = 15;
	$tanggal = date('j F Y');
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$margin =arg(3);
		$tanggal =arg(4);
		
	} 
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'PILIHAN DATA<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
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
	$form['formdata']['margin']= array(
		'#type' => 'textfield',
		'#title' => 'Margin',
		'#default_value' => $margin,
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

function gen_report_realisasi_urusan($bulan) {

$btl_agg_t = 0; 
$btl_rea_t = 0; 
$bl_agg_t = 0; 
$bl_rea_t = 0;

//TABEL
$header = array (
	array('data' => 'Kode','width' => '15px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'BTL(A)', 'width' => '80px', 'valign'=>'top', 'style'=>'color: #b37700'),
	array('data' => 'BTL(R)', 'width' => '80px', 'valign'=>'top', 'style'=>'color: #b37700'),
	array('data' => 'BTL(%)', 'width' => '15px', 'valign'=>'top', 'style'=>'color: #b37700'),
	
	array('data' => 'BL(A)', 'width' => '80px', 'valign'=>'top', 'style'=>'color: #009900'),
	array('data' => 'BL(R)', 'width' => '80px', 'valign'=>'top', 'style'=>'color: #009900'),
	array('data' => 'BL(%)', 'width' => '15px', 'valign'=>'top', 'style'=>'color: #009900'),
	
	array('data' => 'TOT(A)', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'TOT(R)', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'TOT(%)', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();


//FUNGSI
$query = db_select('fungsi', 'f');
$query->fields('f', array('kodef', 'fungsi'));
$query->orderBy('f.kodef');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$btl_agg = 0; $btl_rea = 0; $bl_agg = 0; $bl_rea = 0;
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
	$sql->innerJoin('anggperkeg', 'a', 'k.kodekeg=a.kodekeg');
	$sql->fields('k', array('jenis'));
	$sql->addExpression('SUM(jumlah)', 'anggaran');
	
	$sql->condition('k.inaktif', '0', '='); 
	$sql->condition('u.kodef', $datas->kodef, '='); 
	
	$sql->groupBy('k.jenis');
	$sql->orderBy('k.jenis');
	
	$res = $sql->execute();
	foreach ($res as $data) {
		if ($data->jenis=='1')
			$btl_agg = $data->anggaran;
		else	
			$bl_agg = $data->anggaran;
	}
	
	//REA
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
	$sql->innerJoin('jurnal', 'j', 'k.kodekeg=j.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	
	$sql->fields('k', array('jenis'));
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	
	$sql->condition('u.kodef', $datas->kodef, '='); 
	$sql->condition('ji.kodero', '5%', 'LIKE'); 
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$sql->groupBy('k.jenis');
	$sql->orderBy('k.jenis');
	
	//dpq ($sql);
	
	$res = $sql->execute();
	foreach ($res as $data) {
		if ($data->jenis=='1')
			$btl_rea = $data->realisasi;
		else	
			$bl_rea = $data->realisasi;
	}	
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodef . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->fungsi . '</strong>', 'align' => 'left', 'valign'=>'top'),
		
		array('data' => '<strong>' . apbd_fn($btl_agg) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
		array('data' => '<strong>' . apbd_fn($btl_rea) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($btl_agg, $btl_rea)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
		
		array('data' => '<strong>' . apbd_fn($bl_agg) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),
		array('data' => '<strong>' . apbd_fn($bl_rea) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($bl_agg, $bl_rea)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),

		array('data' => '<strong>' . apbd_fn($btl_agg + $bl_agg) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($btl_rea + $bl_rea) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($btl_agg + $bl_agg, $btl_rea + $bl_rea)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		
	);
	
	$btl_agg_t += $btl_agg; 
	$btl_rea_t += $btl_rea; 
	$bl_agg_t += $bl_agg; 
	$bl_rea_t += $bl_rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusan'));
	$query->condition('u.kodef', $datas->kodef, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$btl_agg = 0; $btl_rea = 0; $bl_agg = 0; $bl_rea = 0;
		
		//AGG
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
		$sql->innerJoin('anggperkeg', 'a', 'k.kodekeg=a.kodekeg');
		$sql->fields('k', array('jenis'));
		$sql->addExpression('SUM(jumlah)', 'anggaran');
		
		$sql->condition('k.inaktif', '0', '='); 
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$sql->groupBy('k.jenis');
		$sql->orderBy('k.jenis');
		
		$res = $sql->execute();
		foreach ($res as $data) {
			if ($data->jenis=='1')
				$btl_agg = $data->anggaran;
			else	
				$bl_agg = $data->anggaran;
		}
		
		//REA
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
		$sql->innerJoin('jurnal', 'j', 'k.kodekeg=j.kodekeg');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		
		$sql->fields('k', array('jenis'));
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '=');  
		$sql->condition('ji.kodero', '5%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		
		$sql->groupBy('k.jenis');
		$sql->orderBy('k.jenis');
		
		$res = $sql->execute();
		foreach ($res as $data) {
			if ($data->jenis=='1')
				$btl_rea = $data->realisasi;
			else	
				$bl_rea = $data->realisasi;
		}	
		
		$rows[] = array(
			array('data' => $datas->kodef . '.' . $data_u->kodeu, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data_u->urusan, 'align' => 'left', 'valign'=>'top'),
			
			array('data' => apbd_fn($btl_agg), 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
			array('data' => apbd_fn($btl_rea), 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
			array('data' => apbd_fn1(apbd_hitungpersen($btl_agg, $btl_rea)), 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
			
			array('data' => apbd_fn($bl_agg), 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),
			array('data' => apbd_fn($bl_rea), 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),
			array('data' => apbd_fn1(apbd_hitungpersen($bl_agg, $bl_rea)), 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),

			array('data' => apbd_fn($btl_agg + $bl_agg), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($btl_rea + $bl_rea), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($btl_agg + $bl_agg, $btl_rea + $bl_rea)), 'align' => 'right', 'valign'=>'top'),
			
		);
		
	}
	

}	//foreach ($results as $datas)

//TOTAL
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	
	array('data' => '<strong>' . apbd_fn($btl_agg_t) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
	array('data' => '<strong>' . apbd_fn($btl_rea_t) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($btl_agg_t, $btl_rea_t)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #b37700'),
	
	array('data' => '<strong>' . apbd_fn($bl_agg_t) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),
	array('data' => '<strong>' . apbd_fn($bl_rea_t) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($bl_agg_t, $bl_rea_t)) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>'color: #009900'),

	array('data' => '<strong>' . apbd_fn($btl_agg_t + $bl_agg_t) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($btl_rea_t + $bl_rea_t) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($btl_agg_t + $bl_agg_t, $btl_rea_t + $bl_rea_t)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	
);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_2016($bulan, $tanggal) {


$skpd = 'KABUPATEN JEPARA';
$pimpinannama = apbd_bud_nama();
$pimpinanjabatan = apbd_bud_jabatan();
$pimpinannip = apbd_bud_nip();

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER FUNGSI/URUSAN</strong>', 'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
}
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);


//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
$tabel_data = createT(null, $rows);


//TABEL 
$rows = null;
//$header = array();
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '20px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','rowspan'=>2,'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SKPD','rowspan'=>2,'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '480px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA LANGSUNG', 'width' => '180px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'Pegawai', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bunga', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Subsidi', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Hibah', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bantuan Sosial', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bagi Hasil', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bantuan Keuangan', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Tidak Terduga', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'Pegawai', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Barang Jasa', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Modal', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

//FUNGSI
$query = db_select('fungsi16', 'f');
$query->fields('f', array('kodef', 'fungsi'));
$query->orderBy('f.kodef');
$results = $query->execute();

foreach ($results as $dataf) {
	
	//REA
	$gaji = 0; $bunga = 0; $subsidi = 0; $hibah = 0; $bansos = 0; $bagihasil = 0; $bankeu = 0; $takterduga = 0;
	$pegawai = 0; $barangjasa = 0; $modal = 0; 
	$res = db_query('select kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis, sum(nilaiRealisasi) as realisasi from realisasi_sikd_2016 where kodeFungsi=:kodeFungsi group by kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis', array(':kodeFungsi'=>$datas->kodef);
	foreach ($res as $data) {
		switch ($data->kodeAkunUtama . $data->kodeAkunKelompok . $data->kodeAkunJenis) {
			case '511':
				$gaji = $data->realisasi;
				break;
			case '512':
				$bunga = $data->realisasi;
				break;
			case '513':
				$subsidi = $data->realisasi;
				break;
			case '514':
				$hibah = $data->realisasi;
				break;
			case '515':
				$bansos = $data->realisasi;
				break;
			case '516':
				$bagihasil = $data->realisasi;
				break;
			case '517':
				$bankeu = $data->realisasi;
				break;
			case '518':
				$takterduga = $data->realisasi;
				break;

			case '521':
				$pegawai = $data->realisasi;
				break;
			case '522':
				$barangjasa = $data->realisasi;
				break;
			case '523':
				$modal = $data->realisasi;
				break;
				
		}	
	}	
	
	$rows[] = array(
		array('data' => $dataf->kodef , 'align' => 'left', 'width' => '20px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => $dataf->fungsi  , 'align' => 'left', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' => ''  , 'align' => 'left', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($gaji) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bunga) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($subsidi) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($hibah) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bansos) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bagihasil) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bankeu) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($takterduga), 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
]
		array('data' =>  apbd_fn($pegawai) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($barangjasa), 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($modal), 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
	);
	
	//URUSAN
	$query = db_select('urusan16', 'u');
	$query->fields('u', array('kodeu', 'urusan'));
	$query->condition('u.kodef', $datas->kodef, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		//REA
		$gaji = 0; $bunga = 0; $subsidi = 0; $hibah = 0; $bansos = 0; $bagihasil = 0; $bankeu = 0; $takterduga = 0;
		$pegawai = 0; $barangjasa = 0; $modal = 0; 
		$res = db_query('select kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis, sum(nilaiRealisasi) as realisasi from realisasi_sikd_2016 where kodeUrusanPelaksana=:kodeUrusanPelaksana group by kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis', array(':kodeUrusanPelaksana	'=>$data_u->kodeu);
		foreach ($res as $data) {
			switch ($data->kodeAkunUtama . $data->kodeAkunKelompok . $data->kodeAkunJenis) {
				case '511':
					$gaji = $data->realisasi;
					break;
				case '512':
					$bunga = $data->realisasi;
					break;
				case '513':
					$subsidi = $data->realisasi;
					break;
				case '514':
					$hibah = $data->realisasi;
					break;
				case '515':
					$bansos = $data->realisasi;
					break;
				case '516':
					$bagihasil = $data->realisasi;
					break;
				case '517':
					$bankeu = $data->realisasi;
					break;
				case '518':
					$takterduga = $data->realisasi;
					break;

				case '521':
					$pegawai = $data->realisasi;
					break;
				case '522':
					$barangjasa = $data->realisasi;
					break;
				case '523':
					$modal = $data->realisasi;
					break;
					
			}	
		}	
		
		
		
		$rows[] = array(
			array('data' => $dataf->kodef . '.' . $data_u->kodeu , 'align' => 'left', 'width' => '35px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusan  , 'align' => 'left', 'width' => '215px', 'style'=>'border-right:1px solid black;'),
			
			array('data' => apbd_fn($gaj) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($bunga) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($subsidi) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($hibah) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($bansos) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($bagihasil) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' => apbd_fn($bankeu) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($takterduga) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			
			array('data' =>  apbd_fn($pegawai) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($barangjasa), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($modal), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),

\			
		);
	
		//SKPD
		$query = db_select('unitkerja16', 'u');
		$query->fields('u', array('kodeuk', 'kodedinas', 'namauk'));
		$query->condition('u.kodeu', $data_u->kodeu, '='); 
		$query->orderBy('u.kodedinas');
		$results_uk = $query->execute();
		foreach ($results_u kas $data_uk) {
			//REA
			$gaji = 0; $bunga = 0; $subsidi = 0; $hibah = 0; $bansos = 0; $bagihasil = 0; $bankeu = 0; $takterduga = 0;
			$pegawai = 0; $barangjasa = 0; $modal = 0; 
			$res = db_query('select kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis, sum(nilaiRealisasi) as realisasi from realisasi_sikd_2016 where kodeSKPD=:kodeSKPDkodeSKPD group by kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis', array(':kodeSKPD	'=>$data_uk->kodeuK);
			foreach ($res as $data) {
				switch ($data->kodeAkunUtama . $data->kodeAkunKelompok . $data->kodeAkunJenis) {
					case '511':
						$gaji = $data->realisasi;
						break;
					case '512':
						$bunga = $data->realisasi;
						break;
					case '513':
						$subsidi = $data->realisasi;
						break;
					case '514':
						$hibah = $data->realisasi;
						break;
					case '515':
						$bansos = $data->realisasi;
						break;
					case '516':
						$bagihasil = $data->realisasi;
						break;
					case '517':
						$bankeu = $data->realisasi;
						break;
					case '518':
						$takterduga = $data->realisasi;
						break;

					case '521':
						$pegawai = $data->realisasi;
						break;
					case '522':
						$barangjasa = $data->realisasi;
						break;
					case '523':
						$modal = $data->realisasi;
						break;
						
				}	
			}		

			$rows[] = array(
				array('data' => $dataf->kodef . '.' . $data_uk->kodedinas , 'align' => 'left', 'width' => '35px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_uk->namauk  , 'align' => 'left', 'width' => '215px', 'style'=>'border-right:1px solid black;'),
				
				array('data' => apbd_fn($gaj) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($bunga) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($subsidi) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($hibah) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($bansos) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($bagihasil) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($bankeu) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($takterduga) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				
				array('data' =>  apbd_fn($pegawai) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($barangjasa), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($modal), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),

	\			
			);			
		}
		
	}
	

}	//foreach ($results as $datas)

/*
//TOTAL
$rows[] = array(
	array('data' => '' , 'align' => 'left', 'width' => '35px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '215px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	
	array('data' => apbd_fn($btl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($btl_rea_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg_t, $btl_rea_t)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
	array('data' =>  apbd_fn($bl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($bl_rea_t), 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($bl_agg_t, $bl_rea_t)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

	array('data' =>  apbd_fn($btl_agg_t + $bl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($btl_rea_t + $bl_rea_t), 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg_t + $bl_agg_t, $btl_rea_t + $bl_rea_t)), 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
);
*/

//ttd
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
	
);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'Jepara, '.$tanggal ,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => $pimpinanjabatan,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => $pimpinannama,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'NIP. ' . $pimpinannip,'width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_fungsi($bulan, $tanggal) {


$skpd = 'KABUPATEN JEPARA';
$pimpinannama = apbd_bud_nama();
$pimpinanjabatan = apbd_bud_jabatan();
$pimpinannip = apbd_bud_nip();

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER FUNGSI</strong>', 'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
}
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);


//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
$tabel_data = createT(null, $rows);


//TABEL 
$rows = null;
//$header = array();
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '35px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI','rowspan'=>2,'width' => '215px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '190px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA LANGSUNG', 'width' => '190px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL BELANJA', 'width' => '190px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

//FUNGSI
$query = db_select('fungsi', 'f');
$query->fields('f', array('kodef', 'fungsi'));
$query->orderBy('f.kodef');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$btl_agg = 0; $btl_rea = 0; $bl_agg = 0; $bl_rea = 0;
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
	$sql->innerJoin('anggperkeg', 'a', 'k.kodekeg=a.kodekeg');
	$sql->fields('k', array('jenis'));
	$sql->addExpression('SUM(a.jumlah)', 'anggaran');
	
	$sql->condition('k.inaktif', '0', '='); 
	$sql->condition('u.kodef', $datas->kodef, '='); 
	
	$sql->groupBy('k.jenis');
	$sql->orderBy('k.jenis');
	
	$res = $sql->execute();
	foreach ($res as $data) {
		if ($data->jenis=='1')
			$btl_agg = $data->anggaran;
		else	
			$bl_agg = $data->anggaran;
	}
	
	//REA
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
	$sql->innerJoin('jurnal', 'j', 'k.kodekeg=j.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	
	$sql->fields('k', array('jenis'));
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	
	$sql->condition('u.kodef', $datas->kodef, '='); 
	$sql->condition('ji.kodero', '5%', 'LIKE'); 
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$sql->groupBy('k.jenis');
	$sql->orderBy('k.jenis');
	
	//dpq ($sql);
	
	$res = $sql->execute();
	foreach ($res as $data) {
		if ($data->jenis=='1')
			$btl_rea = $data->realisasi;
		else	
			$bl_rea = $data->realisasi;
	}	
	
	$rows[] = array(
		array('data' => $datas->kodef , 'align' => 'center', 'width' => '35px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->fungsi  , 'align' => 'left', 'width' => '215px', 'style'=>'border-right:1px solid black;'),
		
		array('data' => apbd_fn($btl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  apbd_fn($btl_rea) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg, $btl_rea)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;'),
		
		array('data' =>  apbd_fn($bl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  apbd_fn($bl_rea), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($bl_agg, $bl_rea)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;'),

		array('data' =>  apbd_fn($btl_agg + $bl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  apbd_fn($btl_rea + $bl_rea), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg + $bl_agg, $btl_rea + $bl_rea)), 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;'),
		
	);
	
	$btl_agg_t += $btl_agg; 
	$btl_rea_t += $btl_rea; 
	$bl_agg_t += $bl_agg; 
	$bl_rea_t += $bl_rea;
	

}	//foreach ($results as $datas)

//TOTAL
$rows[] = array(
	array('data' => '' , 'align' => 'left', 'width' => '35px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '215px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	
	array('data' => apbd_fn($btl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($btl_rea_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg_t, $btl_rea_t)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
	array('data' =>  apbd_fn($bl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($bl_rea_t), 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($bl_agg_t, $bl_rea_t)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

	array('data' =>  apbd_fn($btl_agg_t + $bl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($btl_rea_t + $bl_rea_t), 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg_t + $bl_agg_t, $btl_rea_t + $bl_rea_t)), 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
);


//ttd
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
	
);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'Jepara, '.$tanggal ,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => $pimpinanjabatan,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => $pimpinannama,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'NIP. ' . $pimpinannip,'width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_print_urusan($bulan, $tanggal) {


$skpd = 'KABUPATEN JEPARA';
$pimpinannama = apbd_bud_nama();
$pimpinanjabatan = apbd_bud_jabatan();
$pimpinannip = apbd_bud_nip();

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER URUSAN</strong>', 'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(), 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
}
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);

//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
$tabel_data = createT(null, $rows);


//TABEL 
$rows = null;
//$header = array();
$header[] = array (
	array('data' => 'NO.','rowspan'=>2,'width' => '20px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URUSAN','rowspan'=>2,'width' => '230px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '190px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA LANGSUNG', 'width' => '190px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL BELANJA', 'width' => '190px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

//SIFAT URUSAN
$query = db_select('urusansifat', 'f');
$query->fields('f', array('sifat', 'uraian'));
$query->orderBy('f.sifat');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$btl_agg = 0; $btl_rea = 0; $bl_agg = 0; $bl_rea = 0;
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
	$sql->innerJoin('anggperkeg', 'a', 'k.kodekeg=a.kodekeg');
	$sql->fields('k', array('jenis'));
	$sql->addExpression('SUM(a.jumlah)', 'anggaran');
	
	$sql->condition('k.inaktif', '0', '='); 
	$sql->condition('u.sifat', $datas->sifat, '='); 
	
	$sql->groupBy('k.jenis');
	$sql->orderBy('k.jenis');
	
	$res = $sql->execute();
	foreach ($res as $data) {
		if ($data->jenis=='1')
			$btl_agg = $data->anggaran;
		else	
			$bl_agg = $data->anggaran;
	}
	
	//REA
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
	$sql->innerJoin('jurnal', 'j', 'k.kodekeg=j.kodekeg');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	
	$sql->fields('k', array('jenis'));
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	
	$sql->condition('u.sifat', $datas->sifat, '='); 
	$sql->condition('ji.kodero', '5%', 'LIKE'); 
	$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$sql->groupBy('k.jenis');
	$sql->orderBy('k.jenis');
	
	//dpq ($sql);
	
	$res = $sql->execute();
	foreach ($res as $data) {
		if ($data->jenis=='1')
			$btl_rea = $data->realisasi;
		else	
			$bl_rea = $data->realisasi;
	}	
	
	$rows[] = array(
		array('data' => $datas->sifat , 'align' => 'left', 'width' => '20px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => $datas->uraian, 'align' => 'left', 'width' => '230px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($btl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($btl_rea) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg, $btl_rea)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' =>  apbd_fn($bl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bl_rea), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($bl_agg, $bl_rea)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($btl_agg + $bl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($btl_rea + $bl_rea), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg + $bl_agg, $btl_rea + $bl_rea)), 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
	);
	
	$btl_agg_t += $btl_agg; 
	$btl_rea_t += $btl_rea; 
	$bl_agg_t += $bl_agg; 
	$bl_rea_t += $bl_rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusansingkat1'));
	$query->condition('u.sifat', $datas->sifat, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$btl_agg = 0; $btl_rea = 0; $bl_agg = 0; $bl_rea = 0;
		
		//AGG
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
		$sql->innerJoin('anggperkeg', 'a', 'k.kodekeg=a.kodekeg');
		$sql->fields('k', array('jenis'));
		$sql->addExpression('SUM(a.jumlah)', 'anggaran');
		
		$sql->condition('k.inaktif', '0', '='); 
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$sql->groupBy('k.jenis');
		$sql->orderBy('k.jenis');
		
		$res = $sql->execute();
		foreach ($res as $data) {
			if ($data->jenis=='1')
				$btl_agg = $data->anggaran;
			else	
				$bl_agg = $data->anggaran;
		}
		
		//REA
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatanskpd', 'k', 'k.kodepro=p.kodepro');
		$sql->innerJoin('jurnal', 'j', 'k.kodekeg=j.kodekeg');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		
		$sql->fields('k', array('jenis'));
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '=');  
		$sql->condition('ji.kodero', '5%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		
		$sql->groupBy('k.jenis');
		$sql->orderBy('k.jenis');
		
		$res = $sql->execute();
		foreach ($res as $data) {
			if ($data->jenis=='1')
				$btl_rea = $data->realisasi;
			else	
				$bl_rea = $data->realisasi;
		}	
		
		
		$rows[] = array(
			array('data' => $data_u->kodeu , 'align' => 'left', 'width' => '20px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusansingkat1  , 'align' => 'left', 'width' => '230px', 'style'=>'border-right:1px solid black;'),
			
			array('data' => apbd_fn($btl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($btl_rea) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg, $btl_rea)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;'),
			
			array('data' =>  apbd_fn($bl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($bl_rea), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($bl_agg, $bl_rea)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;'),

			array('data' =>  apbd_fn($btl_agg + $bl_agg) , 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn($btl_rea + $bl_rea), 'align' => 'right', 'width' => '80px', 'style'=>'border-right:1px solid black;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg + $bl_agg, $btl_rea + $bl_rea)), 'align' => 'right', 'width' => '30px', 'style'=>'border-right:1px solid black;'),
			
		);
		
	}
	

}	//foreach ($results as $datas)

//TOTAL
$rows[] = array(
	array('data' => '' , 'align' => 'left', 'width' => '20px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '230px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
	
	array('data' => apbd_fn($btl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($btl_rea_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg_t, $btl_rea_t)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
	array('data' =>  apbd_fn($bl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($bl_rea_t), 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($bl_agg_t, $bl_rea_t)) , 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

	array('data' =>  apbd_fn($btl_agg_t + $bl_agg_t) , 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn($btl_rea_t + $bl_rea_t), 'align' => 'right', 'width' => '80px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
	array('data' =>  apbd_fn1(apbd_hitungpersen($btl_agg_t + $bl_agg_t, $btl_rea_t + $bl_rea_t)), 'align' => 'right', 'width' => '30px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),
	
);


//ttd
$rows[] = array(
	array('data' => '', 'width' => '820px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
	
);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'Jepara, '.$tanggal ,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
						
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => $pimpinanjabatan,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => $pimpinannama,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'NIP. ' . $pimpinannip,'width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



?>


