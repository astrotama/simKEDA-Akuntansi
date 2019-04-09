<?php
function laporanfu_keg_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 125px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	$margin =0;
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$margin =arg(3);
				$tanggal =arg(4);
				$cetakpdf = arg(5);
				$ttdlaporan = arg(6);
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
		$ttdlaporan = 2;
	}
	
	//drupal_set_title('BELANJA');
	  
	if ($cetakpdf=='pdf_fu') {

		$output = gen_report_realisasi_print($bulan, $tanggal, $ttdlaporan);
		//$output = gen_report_realisasi_print_2016($tanggal);
		apbd_ExportPDF_L($output, $margin, "LAP");
		
	} else if ($cetakpdf=='pdf_fu_16') {

		$output = gen_report_realisasi_print_2016($tanggal, $ttdlaporan);
		apbd_ExportPDF_L($output, $margin, "LAP");

	} else if ($cetakpdf=='pdf_f') {

		$output = gen_report_realisasi_print_fungsi($bulan, $tanggal, $ttdlaporan);
		apbd_ExportPDF_L($output, $margin, "LAP");
				
	} else if ($cetakpdf=='pdf_u') {

		$output = gen_report_realisasi_print_urusan($bulan, $tanggal, $ttdlaporan);
		apbd_ExportPDF_L($output, $margin, "LAP");
		
	} else if ($cetakpdf=='pdf_uspk') {
		//$output = 'abc';
		$index = arg(7);
		if ($index=='') $index = '1';
		$output = gen_report_realisasi_print_urusan_program_kegiatan($tanggal,  $ttdlaporan, $index);
		apbd_ExportPDF_L($output, $margin, "LAP");
		//return $output;
		
	} elseif ($cetakpdf=='xls_fu') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realisasi Per Fungsi Urusan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print($bulan, $kodeuk, $tingkat,$margin,$tanggal, $ttdlaporan);
		echo $output;
				
	} elseif ($cetakpdf=='xls_f') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realisasi Per Fungsi.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print_fungsi($bulan, $kodeuk, $tingkat,$margin,$tanggal, $ttdlaporan,$cetakpdf);
		echo $output;

	} elseif ($cetakpdf=='xls_u') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Realisasi Per Urusan.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print_urusan($bulan, $kodeuk, $tingkat,$margin,$tanggal, $ttdlaporan,$cetakpdf);
		echo $output;
		
	} else {
		//$output = gen_report_realisasi_urusan($bulan);
		$output = '';
		$output_form = drupal_get_form('laporanfu_keg_main_form');	
		
		$btn = l('Cetak', 'laporanfu/filter/' . $bulan .'/'.$margin.'/'.$tanggal. '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= l('Excel', 'laporanfu/filter/' . $bulan .'/'.$margin.'/'.$tanggal. '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-success')));

		$btn = '<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Cetak <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/pdf_fu_16/'. $ttdlaporan . '">Fungsi/Urusan 2016</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/pdf_fu/'. $ttdlaporan .'">Fungsi/Urusan</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/pdf_f/'. $ttdlaporan .'">Fungsi</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/pdf_u/'. $ttdlaporan .'">Urusan</a></li>' .
					'</ul>' .
				'</div>';		
		$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/xls_fu/'. $ttdlaporan .'">Fungsi/Urusan</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/xls_f/'. $ttdlaporan .'">Fungsi</a></li>' .
						'<li><a href="/laporanfu/filter/' . $bulan . '/'. $margin .'/' . urlencode($tanggal) . '/xls_u/'. $ttdlaporan .'">Urusan</a></li>' .
					'</ul>' .
				'</div>';
				
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporanfu_keg_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$margin = $form_state['values']['margin'];
	$tanggal = $form_state['values']['tanggal'];
	$ttdlaporan= $form_state['values']['ttdlaporan'];
	$cetakpdf = '0';
	$uri = 'laporanfu/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat. '/' . $margin. '/' . $tanggal . '/' . $cetakpdf . '/' . $ttdlaporan;;
	drupal_goto($uri);
	
}


function laporanfu_keg_main_form($form, &$form_state) {
	
	$bulan = date('n');
	$margin = 15;
	$tanggal = date('j F Y');
	$ttdlaporan = '2';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$margin =arg(3);
		$tanggal =arg(4);
		$ttdlaporan = arg(9);
		
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
	
	$penandatangan = array ('BUPATI','WAKIL BUPATI','KEPALA BPKAD','SEKRETARIS DINAS');
	$form['formdata']['ttdlaporan']= array(
		'#type'         => 'select', 
		'#title' =>  t('PENANDA TANGAN LAPORAN'),
		'#options' => $penandatangan,
		'#default_value'=> $ttdlaporan,
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

function gen_report_realisasi_print($bulan, $tanggal, $penandatangan) {
	
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
	$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
	$pimpinannama = variable_get('sekretarisnama', '');
	$pimpinanjabatan = variable_get('sekretarisjabatan', '');
	$pimpinannip = variable_get('sekretarisnip', '');
	
	//drupal_set_message('s . ' . $pimpinannip);
	
} else {
	$pimpinanatasnama = '';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();
}	


$skpd = 'KABUPATEN JEPARA';
//$pimpinannama = apbd_bud_nama();
//$pimpinanjabatan = apbd_bud_jabatan();
//$pimpinannip = apbd_bud_nip();

// $pimpinannama = 'AHMAD MARZUQI';
// $pimpinanjabatan = 'BUPATI JEPARA';

if (arg(5) == 'xls_fu'){
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '35px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','width' => '215px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TL ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TL REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TL (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
}else{
	
$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER FUNGSI/URUSAN</strong>',  'colspan'=>7,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px',  'colspan'=>7,'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(),  'colspan'=>7,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>7, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
}
$rows[] = array(
	array('data' => '', 'width' => '820px',  'colspan'=>7,'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => '', 'width' => '820px', 'colspan'=>7, 'align'=>'center','style'=>'border:none'),
);


//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
$tabel_data = createT(null, $rows);


//TABEL 
$rows = null;

$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '35px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','rowspan'=>2,'width' => '215px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
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
}
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
		array('data' => $datas->kodef , 'align' => 'left', 'width' => '35px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => $datas->fungsi  , 'align' => 'left', 'width' => '215px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
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
			array('data' => $datas->kodef . '.' . $data_u->kodeu , 'align' => 'left', 'width' => '35px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusan  , 'align' => 'left', 'width' => '215px', 'style'=>'border-right:1px solid black;'),
			
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
/*
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'An. BUPATI JEPARA','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
*/
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
			array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_fungsi($bulan, $tanggal, $penandatangan) {

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
	$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
	$pimpinannama = variable_get('sekretarisnama', '');
	$pimpinanjabatan = variable_get('sekretarisjabatan', '');
	$pimpinannip = variable_get('sekretarisnip', '');
	
	//drupal_set_message('s . ' . $pimpinannip);
	
} else {
	$pimpinanatasnama = '';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();
}

$skpd = 'KABUPATEN JEPARA';
//$pimpinannama = apbd_bud_nama();
//$pimpinanjabatan = apbd_bud_jabatan();
//$pimpinannip = apbd_bud_nip();

//$pimpinannama = 'DIAN KRISTIANDI';
//$pimpinanjabatan = 'WAKIL BUPATI JEPARA';
//$pimpinannama = 'AHMAD MARZUQI';
//$pimpinanjabatan = 'BUPATI JEPARA';


if (arg(5) == 'xls_f'){
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '35px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','width' => '215px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TL ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TL REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TL (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
}else{

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER FUNGSI</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px', 'colspan'=>11, 'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>11, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
}
$rows[] = array(
	array('data' => '', 'width' => '820px', 'colspan'=>11, 'align'=>'center','style'=>'border:none'),
);
$rows[] = array(
	array('data' => '', 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
);


//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
$tabel_data = createT(null, $rows);


//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '35px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','rowspan'=>2,'width' => '215px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
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
}
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
/*
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'An. BUPATI JEPARA','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
*/
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
			array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_print_urusan($bulan, $tanggal,  $penandatangan) {

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
	$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
	$pimpinannama = variable_get('sekretarisnama', '');
	$pimpinanjabatan = variable_get('sekretarisjabatan', '');
	$pimpinannip = variable_get('sekretarisnip', '');
	
	//drupal_set_message('s . ' . $pimpinannip);
	
} else {
	$pimpinanatasnama = '';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();
}

$skpd = 'KABUPATEN JEPARA';
//$pimpinannama = apbd_bud_nama();
//$pimpinanjabatan = apbd_bud_jabatan();
//$pimpinannip = apbd_bud_nip();

//$pimpinannama = 'AHMAD MARZUQI';
//$pimpinanjabatan = 'BUPATI JEPARA';

//$pimpinannama = 'DIAN KRISTIANDI';
//$pimpinanjabatan = 'WAKIL BUPATI JEPARA';

if (arg(5) == 'xls_u'){
//$header = array();
$header[] = array ( 
	array('data' => 'KODE','width' => '35px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','width' => '215px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TL ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TL REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TL (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA L (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL ANGGARAN', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL REALISASI', 'width' => '80px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA TOTAL (%)', 'width' => '30px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
}else{

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER URUSAN</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
);

if ($bulan=='0') {
	$rows[] = array(
		array('data' => 'TAHUN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	
} else {
	$rows[] = array(
		array('data' => 'BULAN : ' . $bulan . ' TAHUN : ' . apbd_tahun(), 'colspan'=>11, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
}
$rows[] = array(
	array('data' => '',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
);

//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
$tabel_data = createT(null, $rows);


//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '35px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','rowspan'=>2,'width' => '215px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
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
}
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
/*
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
			array('data' => 'An. BUPATI JEPARA','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
				
		);
*/
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
			array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
		);
$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_2016($tanggal,  $penandatangan) {

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
	$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
	$pimpinannama = variable_get('sekretarisnama', '');
	$pimpinanjabatan = variable_get('sekretarisjabatan', '');
	$pimpinannip = variable_get('sekretarisnip', '');
	
	//drupal_set_message('s . ' . $pimpinannip);
	
} else {
	$pimpinanatasnama = '';
	$pimpinannama = apbd_bud_nama();
	$pimpinanjabatan = apbd_bud_jabatan();
	$pimpinannip = apbd_bud_nip();
}

$skpd = 'KABUPATEN JEPARA';
// $pimpinannama = 'Ir. SHOLIH, MM';
// $pimpinanjabatan = 'SEKRETARIS DAERAH';
//$pimpinannip = '');

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER FUNGSI/URUSAN</strong>', 'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);

$rows[] = array(
	array('data' => 'TAHUN : 2016', 'width' => '820px', 'align'=>'center','style'=>'border:none'),
);
	

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
	array('data' => 'KODE','rowspan'=>2,'width' => '40px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'FUNGSI/URUSAN','width' => '100px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '490px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BELANJA LANGSUNG', 'width' => '190px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'SKPD','width' => '100px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'Pegawai', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bunga', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Subsidi', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Hibah', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bantuan Sosial', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bagi Hasil', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Bantuan Keuangan', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Tidak Terduga', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'Pegawai', 'width' => '60px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Barang Jasa', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'Modal', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

$t_gaji = 0; $t_bunga = 0; $t_subsidi = 0; $t_hibah = 0; $t_bansos = 0; $t_bagihasil = 0; $t_bankeu = 0; $t_takterduga = 0;
$t_pegawai = 0; $t_barangjasa = 0; $t_modal = 0; 

//FUNGSI
$query = db_select('fungsi16', 'f');
$query->fields('f', array('kodef', 'fungsi'));
$query->orderBy('f.kodef');
$results = $query->execute();

foreach ($results as $dataf) {

	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'left', 'width' => '100px', 'style'=>'border-right:1px solid black;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),

		
	);			
	//REA
	$gaji = 0; $bunga = 0; $subsidi = 0; $hibah = 0; $bansos = 0; $bagihasil = 0; $bankeu = 0; $takterduga = 0;
	$pegawai = 0; $barangjasa = 0; $modal = 0; 
	$res = db_query('select concat(kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis) as kodejenis, sum(nilaiRealisasi) as realisasi from realisasi_sikd_2016 where kodeFungsi=:kodeFungsi group by concat(kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis)', array(':kodeFungsi'=>$dataf->kodef));
	foreach ($res as $data) {
		switch ($data->kodejenis) {
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

	$t_gaji += $gaji; $t_bunga += $bunga; $t_subsidi += $subsidi; 
	$t_hibah += $hibah; $t_bansos += $bansos; $t_bagihasil += $bagihasil; 
	$t_bankeu += $bankeu; $t_takterduga += $takterduga;
	
	$t_pegawai += $pegawai; $t_barangjasa += $barangjasa; $t_modal += $modal; 
	
	$rows[] = array(
		array('data' => $dataf->kodef , 'align' => 'left', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => $dataf->fungsi  , 'align' => 'left', 'width' => '100px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($gaji) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bunga) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($subsidi) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($hibah) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bansos) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bagihasil) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($bankeu) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($takterduga), 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		

		array('data' =>  apbd_fn($pegawai) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($barangjasa), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($modal), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		
	);
	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'left', 'width' => '100px', 'style'=>'border-right:1px solid black;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),

		
	);	
	
	
	//URUSAN
	$query = db_select('urusan16', 'u');
	$query->fields('u', array('kodeu', 'urusan'));
	$query->condition('u.kodef', $dataf->kodef, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		//REA
		$gaji = 0; $bunga = 0; $subsidi = 0; $hibah = 0; $bansos = 0; $bagihasil = 0; $bankeu = 0; $takterduga = 0;
		$pegawai = 0; $barangjasa = 0; $modal = 0; 
		$res = db_query('select concat(kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis) as kodejenis, sum(nilaiRealisasi) as realisasi from realisasi_sikd_2016 where kodeUrusanPelaksana=:kodeUrusanPelaksana group by concat(kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis)', array(':kodeUrusanPelaksana'=>$data_u->kodeu));
		foreach ($res as $data) {
			switch ($data->kodejenis) {
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
			array('data' => $dataf->kodef . '.' . $data_u->kodeu , 'align' => 'left', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;;font-weight: bold'),
			array('data' => $data_u->urusan  , 'align' => 'left', 'width' => '100px', 'style'=>'border-right:1px solid black;;font-weight: bold'),
			
			array('data' => apbd_fn($gaji) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($bunga) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' => apbd_fn($subsidi) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($hibah) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' => apbd_fn($bansos) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($bagihasil) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' => apbd_fn($bankeu) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($takterduga) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			
			array('data' =>  apbd_fn($pegawai) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($barangjasa), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($modal), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;'),

			
		);
		
		
		//SKPD
		$query = db_select('unitkerja16', 'u');
		$query->fields('u', array('kodeuk', 'kodedinas', 'namauk'));
		$query->condition('u.kodeu', $data_u->kodeu, '='); 
		$query->orderBy('u.kodedinas');
		$results_uk = $query->execute();
		foreach ($results_uk as $data_uk) {
			//REA
			$gaji = 0; $bunga = 0; $subsidi = 0; $hibah = 0; $bansos = 0; $bagihasil = 0; $bankeu = 0; $takterduga = 0;
			$pegawai = 0; $barangjasa = 0; $modal = 0; 
			$res = db_query('select concat(kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis) as kodejenis, sum(nilaiRealisasi) as realisasi from realisasi_sikd_2016 where kodeSKPD=:kodeSKPD group by concat(kodeAkunUtama, kodeAkunKelompok, kodeAkunJenis)', array(':kodeSKPD'=>$data_uk->kodeuk));
			foreach ($res as $data) {
				switch ($data->kodejenis) {
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
				array('data' => $dataf->kodef . '.' . substr($data_uk->kodedinas,0,3) . '.' . substr($data_uk->kodedinas, 3,2)  , 'align' => 'left', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_uk->namauk, 'align' => 'left', 'width' => '100px', 'style'=>'border-right:1px solid black;'),
				
				array('data' => apbd_fn($gaji) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($bunga) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($subsidi) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($hibah) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($bansos) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($bagihasil) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($bankeu) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($takterduga) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
				
				array('data' =>  apbd_fn($pegawai) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($barangjasa), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
				array('data' =>  apbd_fn($modal), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),

				
			);			
		}

	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'align' => 'left', 'width' => '100px', 'style'=>'border-right:1px solid black;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' => '' , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		
		array('data' =>  '' , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
		array('data' =>  '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),

		
	);				
	}
	
	
}	//foreach ($results as $datas)


//TOTAL
$rows[] = array(
	array('data' => '' , 'align' => 'left', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL' , 'align' => 'left', 'width' => '100px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	
	array('data' => apbd_fn($t_gaji) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' =>  apbd_fn($t_bunga) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' =>  apbd_fn($t_subsidi) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' => apbd_fn($t_hibah) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' =>  apbd_fn($t_bansos) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' =>  apbd_fn($t_bagihasil) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' =>  apbd_fn($t_bankeu) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	array('data' =>  apbd_fn($t_takterduga), 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-top:1px solid black;font-weight: bold'),
	

	array('data' =>  apbd_fn($t_pegawai) , 'align' => 'right', 'width' => '60px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;border-top:1px solid black;'),
	array('data' =>  apbd_fn($t_barangjasa), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;border-top:1px solid black;'),
	array('data' =>  apbd_fn($t_modal), 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold;border-top:1px solid black;'),
	
);

//ttd
$rows[] = array(
	array('data' => '', 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
	
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
/*
		$rows[] = array(
			array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
			array('data' => 'NIP. ' . $pimpinannip,'width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
		);	
*/	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_urusan_program_kegiatan($tanggal, $penandatangan, $index) {

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

if ($index=='') $index = '1';

$tabel_data = '';
if ($index=='1') {
	$skpd = 'PEMERINTAH KABUPATEN JEPARA';


	$rows[] = array(
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER URUSAN - OPD - PROGRAM - KEGIATAN</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
	);
	$rows[] = array(
		array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
	);

	$rows[] = array(
		array('data' => 'TAHUN ANGGARAN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	$rows[] = array(
		array('data' => '',  'colspan'=>12,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);

	//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
	$tabel_data = createT(null, $rows);

}

//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '70px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URUSAN','width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN','colspan'=>4, 'width' => '265px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'colspan'=>5, 'width' => '285px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'width' => '70px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'PROGRAM/KEGIATAN', 'width' => '150px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '20px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	
	array('data' => 'ANGGARAN', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();
$agg_pegawai_t = 0; $agg_barangjasa_t = 0; $agg_modal_t = 0; 
$rea_pegawai_t = 0; $rea_barangjasa_t = 0; $rea_modal_t = 0; 

$agg_t = 0; $rea_t = 0;

	
//SIFAT URUSAN
$query = db_select('urusansifat', 'f');
$query->fields('f', array('sifat', 'uraian'));
$query->condition('f.sifat', $index, '='); 
$query->orderBy('f.sifat');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agg_pegawai = 0; $agg_barangjasa = 0; $agg_modal = 0; 
	$rea_pegawai = 0; $rea_barangjasa = 0; $rea_modal = 0; 
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');
	
	$sql->addExpression('SUM(k.agg_pegawai)', 'agg_pegawai');
	$sql->addExpression('SUM(k.agg_barangjasa)', 'agg_barangjasa');
	$sql->addExpression('SUM(k.agg_modal)', 'agg_modal');

	$sql->addExpression('SUM(k.rea_pegawai)', 'rea_pegawai');
	$sql->addExpression('SUM(k.rea_barangjasa)', 'rea_barangjasa');
	$sql->addExpression('SUM(k.rea_modal)', 'rea_modal');
	
	$sql->condition('u.sifat', $datas->sifat, '='); 
		
	$res = $sql->execute();
	foreach ($res as $data) {
		$agg_pegawai = $data->agg_pegawai;
		$agg_barangjasa = $data->agg_barangjasa;
		$agg_modal = $data->agg_modal;

		$rea_pegawai = $data->rea_pegawai;
		$rea_barangjasa = $data->rea_barangjasa;
		$rea_modal = $data->rea_modal;
		
	}
	
	$agg = $agg_pegawai + $agg_barangjasa + $agg_modal;
	$rea = $rea_pegawai + $rea_barangjasa + $rea_modal;
	
	//space
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:70%;'),

		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
	);			
	
	$rows[] = array(
		array('data' => $datas->sifat , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg - $rea), 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
	);
	
	$agg_pegawai_t += $agg_pegawai; $agg_barangjasa_t += $agg_barangjasa; $agg_modal_t += $agg_modal; 
	$rea_pegawai_t += $rea_pegawai; $rea_barangjasa_t += $rea_barangjasa; $rea_modal_t += $rea_modal; 
	
	$agg_t += $agg; $rea_t += $rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusansingkat1'));
	$query->condition('u.sifat', $datas->sifat, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$agg_pegawai = 0; $agg_barangjasa = 0; $agg_modal = 0; 
		$rea_pegawai = 0; $rea_barangjasa = 0; $rea_modal = 0; 
		
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');

		$sql->addExpression('SUM(k.agg_pegawai)', 'agg_pegawai');
		$sql->addExpression('SUM(k.agg_barangjasa)', 'agg_barangjasa');
		$sql->addExpression('SUM(k.agg_modal)', 'agg_modal');

		$sql->addExpression('SUM(k.rea_pegawai)', 'rea_pegawai');
		$sql->addExpression('SUM(k.rea_barangjasa)', 'rea_barangjasa');
		$sql->addExpression('SUM(k.rea_modal)', 'rea_modal');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$agg_pegawai = $data->agg_pegawai;
			$agg_barangjasa = $data->agg_barangjasa;
			$agg_modal = $data->agg_modal;

			$rea_pegawai = $data->rea_pegawai;
			$rea_barangjasa = $data->rea_barangjasa;
			$rea_modal = $data->rea_modal;
		}
		$agg = $agg_pegawai + $agg_barangjasa + $agg_modal;
		$rea = $rea_pegawai + $rea_barangjasa + $rea_modal;		

		//space
		$rows[] = array(
			array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:50%;'),

			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),		
		);			
		$rows[] = array(
			array('data' => $data_u->kodeu , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusansingkat1  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),

			array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			
		);
		
		//SKPD
		$res_skpd = db_query('SELECT unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namauk, sum(kegiatan_bl.agg_pegawai) agg_pegawai, sum(kegiatan_bl.agg_barangjasa) agg_barangjasa, sum(kegiatan_bl.agg_modal) agg_modal, sum(kegiatan_bl.rea_pegawai) rea_pegawai, sum(kegiatan_bl.rea_barangjasa) rea_barangjasa, sum(kegiatan_bl.rea_modal) rea_modal from unitkerja inner join kegiatan_bl on unitkerja.kodeuk=kegiatan_bl.kodeuk inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu group by unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namasingkat order by unitkerja.kodedinas', array(':kodeu'=>$data_u->kodeu));	
		foreach ($res_skpd as $data_skpd) {
			
			$agg = $data_skpd->agg_pegawai + $data_skpd->agg_barangjasa + $data_skpd->agg_modal;
			$rea = $data_skpd->rea_pegawai + $data_skpd->rea_barangjasa + $data_skpd->rea_modal;
			
			//space
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:30%;'),

				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),		
			);	
			$rows[] = array(
				array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_skpd->namauk, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				
				array('data' => apbd_fn($data_skpd->agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				array('data' => apbd_fn($data_skpd->rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),

				array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				
			);			

				//PROGRAM
				$res_program = db_query('SELECT program.kodepro, program.program, sum(kegiatan_bl.agg_pegawai) agg_pegawai, sum(kegiatan_bl.agg_barangjasa) agg_barangjasa, sum(kegiatan_bl.agg_modal) agg_modal, sum(kegiatan_bl.rea_pegawai) rea_pegawai, sum(kegiatan_bl.rea_barangjasa) rea_barangjasa, sum(kegiatan_bl.rea_modal) rea_modal from kegiatan_bl inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu and kegiatan_bl.kodeuk=:kodeuk group by program.kodepro, program.program order by program.kodepro', array(':kodeu'=>$data_u->kodeu, ':kodeuk'=>$data_skpd->kodeuk));	
				foreach ($res_program as $data_program) {
					
					$agg = $data_program->agg_pegawai + $data_program->agg_barangjasa + $data_program->agg_modal;
					$rea = $data_program->rea_pegawai + $data_program->rea_barangjasa + $data_program->rea_modal;

					//space
					$rows[] = array(
						array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:15%;'),

						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),		
					);						
					$rows[] = array(
						array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
						array('data' => strtoupper($data_program->program ) , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
						
						array('data' => apbd_fn($data_program->agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						array('data' => apbd_fn($data_program->rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),

						array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						
					);			
					
					//KEGIATAN
					$res_keg = db_query('SELECT kodepro, kodekeg, kegiatan, agg_pegawai, agg_barangjasa, agg_modal, rea_pegawai, rea_barangjasa, rea_modal from kegiatan_bl where (agg_pegawai+agg_barangjasa+agg_modal)>0 and kodepro=:kodepro and kodeuk=:kodeuk order by kodekeg', array(':kodepro'=>$data_program->kodepro, ':kodeuk'=>$data_skpd->kodeuk));	
					foreach ($res_keg as $data_keg) {

						$agg = $data_keg->agg_pegawai + $data_keg->agg_barangjasa + $data_keg->agg_modal;
						$rea = $data_keg->rea_pegawai + $data_keg->rea_barangjasa + $data_keg->rea_modal;
						
						//font-style: italic
						$rows[] = array(
							array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro . '.' . substr($data_keg->kodekeg, -3), 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
							array('data' => $data_keg->kegiatan  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;'),

							array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							
						);			
						
						
						
					}	//KEGAITAN					
					
				}	//PROGRAM
				
				
		} //SKPD
	}		//URUSAN
	

}	//foreach ($results as $datas)

if ($index=='4') {

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
		$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
		$pimpinannama = variable_get('sekretarisnama', '');
		$pimpinanjabatan = variable_get('sekretarisjabatan', '');
		$pimpinannip = variable_get('sekretarisnip', '');
		
		//drupal_set_message('s . ' . $pimpinannip);
		
	} else {
		$pimpinanatasnama = '';
		$pimpinannama = apbd_bud_nama();
		$pimpinanjabatan = apbd_bud_jabatan();
		$pimpinannip = apbd_bud_nip();
	}	

	$res_tot = db_query('SELECT sum(kegiatan_bl.agg_pegawai) agg_pegawai, sum(kegiatan_bl.agg_barangjasa) agg_barangjasa, sum(kegiatan_bl.agg_modal) agg_modal, sum(kegiatan_bl.rea_pegawai) rea_pegawai, sum(kegiatan_bl.rea_barangjasa) rea_barangjasa, sum(kegiatan_bl.rea_modal) rea_modal from kegiatan_bl');	
	foreach ($res_tot as $data) {
		$agg_pegawai_t = $data->agg_pegawai;
		$agg_barangjasa_t = $data->agg_barangjasa;
		$agg_modal_t = $data->agg_modal;

		$rea_pegawai_t = $data->rea_pegawai;
		$rea_barangjasa_t = $data->rea_barangjasa;
		$rea_modal_t = $data->rea_modal;
		
	}	
	$agg_t = $agg_pegawai_t + $agg_barangjasa_t + $agg_modal_t;
	$rea_t = $rea_pegawai_t + $rea_barangjasa_t + $rea_modal_t;

	//TOTAL
	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '70px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => apbd_fn($agg_pegawai_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_barangjasa_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_modal_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		
		array('data' => apbd_fn($rea_pegawai_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_barangjasa_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_modal_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg_t, $rea_t)), 'align' => 'right', 'width' => '20px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

		array('data' =>  apbd_fn($agg_t + $rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
		
	);


	//ttd
	$rows[] = array(
		array('data' => '', 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, '.$tanggal ,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	/*
	$rows[] = array(
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'An. BUPATI JEPARA','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	*/
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);	
} else {
	//ttd
	$rows[] = array(
		array('data' => '', 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);
	
}
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print_urusan_program_kegiatan($tanggal, $penandatangan, $index) {

drupal_set_time_limit(0);
ini_set('memory_limit', '1024M');

if ($index=='') $index = '1';

$tabel_data = '';
if ($index=='1') {
	$skpd = 'PEMERINTAH KABUPATEN JEPARA';


	$rows[] = array(
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN PER URUSAN - OPD - PROGRAM - KEGIATAN</strong>',  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'font-size:125%;border:none'),
	);
	$rows[] = array(
		array('data' => $skpd, 'width' => '820px',  'colspan'=>11,'align'=>'center','style'=>'border:none'),
	);

	$rows[] = array(
		array('data' => 'TAHUN ANGGARAN : ' . apbd_tahun(),  'colspan'=>11,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);
	$rows[] = array(
		array('data' => '',  'colspan'=>12,'width' => '820px', 'align'=>'center','style'=>'border:none'),
	);

	//$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));
	$tabel_data = createT(null, $rows);

}

//TABEL 
$rows = null;
$header[] = array (
	array('data' => 'KODE','rowspan'=>2,'width' => '70px', 'style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URUSAN','width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'ANGGARAN','colspan'=>4, 'width' => '265px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'colspan'=>5, 'width' => '285px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'width' => '70px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight: bold'),
);
	
$header[] = array (
	array('data' => 'PROGRAM/KEGIATAN', 'width' => '150px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	
	array('data' => 'PEGAWAI', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'BARANG JASA', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'MODAL', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TOTAL', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '%', 'width' => '20px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),

	
	array('data' => 'ANGGARAN', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();
$agg_pegawai_t = 0; $agg_barangjasa_t = 0; $agg_modal_t = 0; 
$rea_pegawai_t = 0; $rea_barangjasa_t = 0; $rea_modal_t = 0; 

$agg_t = 0; $rea_t = 0;

	
//SIFAT URUSAN
$query = db_select('urusansifat', 'f');
$query->fields('f', array('sifat', 'uraian'));
$query->condition('f.sifat', $index, '='); 
$query->orderBy('f.sifat');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agg_pegawai = 0; $agg_barangjasa = 0; $agg_modal = 0; 
	$rea_pegawai = 0; $rea_barangjasa = 0; $rea_modal = 0; 
	
	//AGG
	$sql = db_select('urusan', 'u');
	$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
	$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');
	
	$sql->addExpression('SUM(k.agg_pegawai)', 'agg_pegawai');
	$sql->addExpression('SUM(k.agg_barangjasa)', 'agg_barangjasa');
	$sql->addExpression('SUM(k.agg_modal)', 'agg_modal');

	$sql->addExpression('SUM(k.rea_pegawai)', 'rea_pegawai');
	$sql->addExpression('SUM(k.rea_barangjasa)', 'rea_barangjasa');
	$sql->addExpression('SUM(k.rea_modal)', 'rea_modal');
	
	$sql->condition('u.sifat', $datas->sifat, '='); 
		
	$res = $sql->execute();
	foreach ($res as $data) {
		$agg_pegawai = $data->agg_pegawai;
		$agg_barangjasa = $data->agg_barangjasa;
		$agg_modal = $data->agg_modal;

		$rea_pegawai = $data->rea_pegawai;
		$rea_barangjasa = $data->rea_barangjasa;
		$rea_modal = $data->rea_modal;
		
	}
	
	$agg = $agg_pegawai + $agg_barangjasa + $agg_modal;
	$rea = $rea_pegawai + $rea_barangjasa + $rea_modal;
	
	//space
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),
		array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:70%;'),

		array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:70%;'),		
	);			
	
	$rows[] = array(
		array('data' => $datas->sifat , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
		array('data' => apbd_fn($rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),

		array('data' =>  apbd_fn($agg - $rea), 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:3px solid black;font-weight: bold'),
		
	);
	
	$agg_pegawai_t += $agg_pegawai; $agg_barangjasa_t += $agg_barangjasa; $agg_modal_t += $agg_modal; 
	$rea_pegawai_t += $rea_pegawai; $rea_barangjasa_t += $rea_barangjasa; $rea_modal_t += $rea_modal; 
	
	$agg_t += $agg; $rea_t += $rea;
	
	//URUSAN
	$query = db_select('urusan', 'u');
	$query->fields('u', array('kodeu', 'urusansingkat1'));
	$query->condition('u.sifat', $datas->sifat, '='); 
	$query->orderBy('u.kodeu');
	$results_u = $query->execute();
	foreach ($results_u as $data_u) {
		
		$agg_pegawai = 0; $agg_barangjasa = 0; $agg_modal = 0; 
		$rea_pegawai = 0; $rea_barangjasa = 0; $rea_modal = 0; 
		
		$sql = db_select('urusan', 'u');
		$sql->innerJoin('program', 'p', 'u.kodeu=p.kodeu');
		$sql->innerJoin('kegiatan_bl', 'k', 'k.kodepro=p.kodepro');

		$sql->addExpression('SUM(k.agg_pegawai)', 'agg_pegawai');
		$sql->addExpression('SUM(k.agg_barangjasa)', 'agg_barangjasa');
		$sql->addExpression('SUM(k.agg_modal)', 'agg_modal');

		$sql->addExpression('SUM(k.rea_pegawai)', 'rea_pegawai');
		$sql->addExpression('SUM(k.rea_barangjasa)', 'rea_barangjasa');
		$sql->addExpression('SUM(k.rea_modal)', 'rea_modal');
		
		$sql->condition('u.kodeu', $data_u->kodeu, '='); 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$agg_pegawai = $data->agg_pegawai;
			$agg_barangjasa = $data->agg_barangjasa;
			$agg_modal = $data->agg_modal;

			$rea_pegawai = $data->rea_pegawai;
			$rea_barangjasa = $data->rea_barangjasa;
			$rea_modal = $data->rea_modal;
		}
		$agg = $agg_pegawai + $agg_barangjasa + $agg_modal;
		$rea = $rea_pegawai + $rea_barangjasa + $rea_modal;		

		//space
		$rows[] = array(
			array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),
			array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:50%;'),

			array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:50%;'),		
		);			
		$rows[] = array(
			array('data' => $data_u->kodeu , 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_u->urusansingkat1  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			array('data' => apbd_fn($rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),

			array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:2px solid black;border-bottom:2px solid black;font-weight: bold;'),
			
			
		);
		
		//SKPD
		$res_skpd = db_query('SELECT unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namauk, sum(kegiatan_bl.agg_pegawai) agg_pegawai, sum(kegiatan_bl.agg_barangjasa) agg_barangjasa, sum(kegiatan_bl.agg_modal) agg_modal, sum(kegiatan_bl.rea_pegawai) rea_pegawai, sum(kegiatan_bl.rea_barangjasa) rea_barangjasa, sum(kegiatan_bl.rea_modal) rea_modal from unitkerja inner join kegiatan_bl on unitkerja.kodeuk=kegiatan_bl.kodeuk inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu group by unitkerja.kodeuk, unitkerja.kodedinas, unitkerja.namasingkat order by unitkerja.kodedinas', array(':kodeu'=>$data_u->kodeu));	
		foreach ($res_skpd as $data_skpd) {
			
			$agg = $data_skpd->agg_pegawai + $data_skpd->agg_barangjasa + $data_skpd->agg_modal;
			$rea = $data_skpd->rea_pegawai + $data_skpd->rea_barangjasa + $data_skpd->rea_modal;
			
			//space
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),
				array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:30%;'),

				array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:30%;'),		
			);	
			$rows[] = array(
				array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_skpd->namauk, 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-weight: bold;'),
				
				array('data' => apbd_fn($data_skpd->agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				array('data' => apbd_fn($data_skpd->rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($data_skpd->rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),

				array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;'),
				
				
			);			

				//PROGRAM
				$res_program = db_query('SELECT program.kodepro, program.program, sum(kegiatan_bl.agg_pegawai) agg_pegawai, sum(kegiatan_bl.agg_barangjasa) agg_barangjasa, sum(kegiatan_bl.agg_modal) agg_modal, sum(kegiatan_bl.rea_pegawai) rea_pegawai, sum(kegiatan_bl.rea_barangjasa) rea_barangjasa, sum(kegiatan_bl.rea_modal) rea_modal from kegiatan_bl inner join program on kegiatan_bl.kodepro=program.kodepro where program.kodeu=:kodeu and kegiatan_bl.kodeuk=:kodeuk group by program.kodepro, program.program order by program.kodepro', array(':kodeu'=>$data_u->kodeu, ':kodeuk'=>$data_skpd->kodeuk));	
				foreach ($res_program as $data_program) {
					
					$agg = $data_program->agg_pegawai + $data_program->agg_barangjasa + $data_program->agg_modal;
					$rea = $data_program->rea_pegawai + $data_program->rea_barangjasa + $data_program->rea_modal;

					//space
					$rows[] = array(
						array('data' => '', 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),
						array('data' => '', 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;font-size:15%;'),

						array('data' => '', 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;font-size:15%;'),		
					);						
					$rows[] = array(
						array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro, 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
						array('data' => strtoupper($data_program->program ) , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
						
						array('data' => apbd_fn($data_program->agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						array('data' => apbd_fn($data_program->rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($data_program->rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),

						array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
						
						
					);			
					
					//KEGIATAN
					$res_keg = db_query('SELECT kodepro, kodekeg, kegiatan, agg_pegawai, agg_barangjasa, agg_modal, rea_pegawai, rea_barangjasa, rea_modal from kegiatan_bl where (agg_pegawai+agg_barangjasa+agg_modal)>0 and kodepro=:kodepro and kodeuk=:kodeuk order by kodekeg', array(':kodepro'=>$data_program->kodepro, ':kodeuk'=>$data_skpd->kodeuk));	
					foreach ($res_keg as $data_keg) {

						$agg = $data_keg->agg_pegawai + $data_keg->agg_barangjasa + $data_keg->agg_modal;
						$rea = $data_keg->rea_pegawai + $data_keg->rea_barangjasa + $data_keg->rea_modal;
						
						//font-style: italic
						$rows[] = array(
							array('data' => $data_u->kodeu . '.' . $data_skpd->kodedinas . '.' . $data_program->kodepro . '.' . substr($data_keg->kodekeg, -3), 'align' => 'left', 'width' => '70px', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
							array('data' => $data_keg->kegiatan  , 'align' => 'left', 'width' => '150px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->agg_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->agg_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($agg) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							array('data' => apbd_fn($data_keg->rea_pegawai) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_barangjasa) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($data_keg->rea_modal) , 'align' => 'right', 'width' => '65px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn($rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							array('data' =>  apbd_fn1(apbd_hitungpersen($agg, $rea)), 'align' => 'right', 'width' => '20px', 'style'=>'border-right:1px solid black;'),

							array('data' =>  apbd_fn($agg - $rea) , 'align' => 'right', 'width' => '70px', 'style'=>'border-right:1px solid black;'),
							
							
						);			
						
						
						
					}	//KEGAITAN					
					
				}	//PROGRAM
				
				
		} //SKPD
	}		//URUSAN
	

}	//foreach ($results as $datas)

if ($index=='4') {

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
		$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
		$pimpinannama = variable_get('sekretarisnama', '');
		$pimpinanjabatan = variable_get('sekretarisjabatan', '');
		$pimpinannip = variable_get('sekretarisnip', '');
		
		//drupal_set_message('s . ' . $pimpinannip);
		
	} else {
		$pimpinanatasnama = '';
		$pimpinannama = apbd_bud_nama();
		$pimpinanjabatan = apbd_bud_jabatan();
		$pimpinannip = apbd_bud_nip();
	}	

	$res_tot = db_query('SELECT sum(kegiatan_bl.agg_pegawai) agg_pegawai, sum(kegiatan_bl.agg_barangjasa) agg_barangjasa, sum(kegiatan_bl.agg_modal) agg_modal, sum(kegiatan_bl.rea_pegawai) rea_pegawai, sum(kegiatan_bl.rea_barangjasa) rea_barangjasa, sum(kegiatan_bl.rea_modal) rea_modal from kegiatan_bl');	
	foreach ($res_tot as $data) {
		$agg_pegawai_t = $data->agg_pegawai;
		$agg_barangjasa_t = $data->agg_barangjasa;
		$agg_modal_t = $data->agg_modal;

		$rea_pegawai_t = $data->rea_pegawai;
		$rea_barangjasa_t = $data->rea_barangjasa;
		$rea_modal_t = $data->rea_modal;
		
	}	
	$agg_t = $agg_pegawai_t + $agg_barangjasa_t + $agg_modal_t;
	$rea_t = $rea_pegawai_t + $rea_barangjasa_t + $rea_modal_t;

	//TOTAL
	$rows[] = array(
		array('data' => '' , 'align' => 'left', 'width' => '70px', 'style'=>'border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		array('data' => 'TOTAL'  , 'align' => 'left', 'width' => '150px', 'style'=>'border-top:1px solid black;border-right:1px solid black;font-weight:bold;border-bottom:1px solid black;'),
		
		array('data' => apbd_fn($agg_pegawai_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_barangjasa_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_modal_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($agg_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		
		array('data' => apbd_fn($rea_pegawai_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_barangjasa_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_modal_t) , 'align' => 'right', 'width' => '65px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn($rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight:bold;'),
		array('data' =>  apbd_fn1(apbd_hitungpersen($agg_t, $rea_t)), 'align' => 'right', 'width' => '20px', 'style'=>'border-bottom:1px solid black;border-top:1px solid black;border-right:1px solid black;font-weight:bold;'),

		array('data' =>  apbd_fn($agg_t + $rea_t) , 'align' => 'right', 'width' => '70px', 'style'=>'border-bottom:1px solid black;border-right:1px solid black;border-top:1px solid black;font-weight:bold;'),
		
	);


	//ttd
	$rows[] = array(
		array('data' => '', 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
		
	);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'Jepara, '.$tanggal ,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
			);
	/*
	$rows[] = array(
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => 'An. BUPATI JEPARA','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	*/
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => $pimpinanjabatan,'width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;'),
					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;'),
				array('data' => '<strong>' . $pimpinannama . '</strong>','width' => '410px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;'),					
			);
	$rows[] = array(
				array('data' => '','width' => '430px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
				array('data' => '','width' => '410px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;'),					
			);	
} else {
	//ttd
	$rows[] = array(
		array('data' => '', 'width' => '840px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);
	
}
	
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>


