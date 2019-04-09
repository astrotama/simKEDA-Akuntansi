<?php
function laporan_prognosis_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	
	$margin = '10'; 
	$marginkiri = '20';
	$hal1 = '1'; 
	$ttdlaporan = '2';
	$tanggal = date('j F Y');
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
	} else {
		$kodeuk = 'ZZ';
	}	
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kodeuk = arg(2);
				$margin =arg(3);
				$tanggal =arg(4);
				$hal1 = arg(5);
				$marginkiri = arg(6);

				$ttdlaporan = arg(7);
				$cetakpdf = arg(8);
				
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} 
	
	
	//drupal_set_message($bulan);
	
	if ($cetakpdf == 'pdf') {
		$output = gen_report_realisasi_print_baru($kodeuk, $ttdlaporan, $tanggal, $cetakpdf);
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_L_Kiri($output, $margin, $marginkiri, 'LAP_PROGNOSIS' . apbd_tahun() . '.pdf');
		//return $output;
		
	} elseif ($cetakpdf == 'pdflengkap') {
		$output = gen_report_realisasi_print_lengkap($kodeuk);
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_L_Kiri($output, $margin, $marginkiri, 'LAP_PROGNOSIS' . apbd_tahun() . '.pdf');
		//return $output;
		
	} else if ($cetakpdf == 'pdfbaru') {
		$output = gen_report_realisasi_print_baru($kodeuk, $ttdlaporan, $tanggal, $cetakpdf);
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_L_Kiri($output, $margin, $marginkiri,'LAP_PROGNOSIS' . apbd_tahun() . '.pdf');
		//return $output;
		
	} else if ($cetakpdf=='excel') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Prognosis.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print($kodeuk, $ttdlaporan, $tanggal,$cetakpdf);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	}  else if ($cetakpdf=='excellengkap') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Prognosis.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = gen_report_realisasi_print_lengkap($kodeuk, $tanggal,$cetakpdf);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_realisasi($kodeuk);
		$output_form = drupal_get_form('laporan_prognosis_main_form');	
		
		if ($kodeuk=='ZZ')
			
			$kodeukkeg = '81';
		else
			$kodeukkeg = $kodeuk;
		
		if ($kodeuk=='ZZ'){
			$btn = '<div class="btn-group">' .
					'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
					' Cetak <span class="caret"></span>' .
					'</button>' .
						'<ul class="dropdown-menu">' .
							'<li><a href="/laporanprognosis/filter/ZZ/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/pdf">Standard</a></li>' .
							'<li><a href="/laporanprognosis/filter/ZZ/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/pdflengkap">Lengkap</a></li>' .
						'</ul>' .
					'</div>';
			$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanprognosis/filter/ZZ/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/excel">Standard</a></li>' .
						'<li><a href="/laporanprognosis/filter/ZZ/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/excellengkap">Lengkap</a></li>' .
					'</ul>' .
				'</div>';	
		} else {
			$btn = '<div class="btn-group">' .
					'<button type="button" class="btn btn-primary glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
					' Cetak <span class="caret"></span>' .
					'</button>' .
						'<ul class="dropdown-menu">' .
							'<li><a href="/laporanprognosis/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/pdf">Standard</a></li>' .
							'<li><a href="/laporanprognosis/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/pdflengkap">Lengkap</a></li>' .
						'</ul>' .
					'</div>';
			$btn .= '&nbsp;<div class="btn-group">' .
				'<button type="button" class="btn btn-primary glyphicon glyphicon-floppy-save dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Excel <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/laporanprognosis/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/excel">Standar</a></li>' .
						'<li><a href="/laporanprognosis/filter/' . $kodeuk . '/'. $margin . '/'. urlencode($tanggal)  . '/' . $hal1 . '/' . $marginkiri . '/'. $ttdlaporan . '/excellengkap">Lengkap</a></li>' .
					'</ul>' .
				'</div>';
		}
		$btn .= '&nbsp;' . l('Kegiatan', 'laporanprognosiskeg/filter/' . $kodeukkeg , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-th-list')));
		$btn .= '&nbsp;' . l('Input Prognosis', 'rekeningkeg/prognosis', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-pencil')));
		
		if (isSuperuser()) {
			$btn .= '&nbsp;' . l('Cek SKPD', 'laporan/skpd', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-saved')));
		}

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
	$ttdlaporan= $form_state['values']['ttdlaporan'];

	
	$uri = 'laporanprognosis/filter/' . $kodeuk . '/' . $margin . '/' . $tanggal . '/' . $hal1 . '/' . $marginkiri . '/' . $ttdlaporan;
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
	$ttdlaporan = '2';
	
	if(arg(2)!=null){		
		$kodeuk = arg(2);
		$margin =arg(3);
		$tanggal =arg(4);
		$hal1 = arg(5);
		$marginkiri = arg(6);
		$ttdlaporan = arg(7);
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
	array('data' => 'Tersedia', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%Anggaran', 'width' => '15px', 'valign'=>'top'),
	array('data' => '%Tersedia', 'width' => '15px', 'valign'=>'top'),
	array('data' => 'Sisa', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Prognosis', 'width' => '90px', 'valign'=>'top'),
);
$rows = array();

$bulan = 6;
$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
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
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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
		
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($realisasi+$data_jen->anggaran) >0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
				
				$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
				$rows[] = array(
					array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
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

					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					if (($realisasi+$data_oby->anggaran) >0) {
						
						$sisa = $data_oby->anggaran - $realisasi;
						if ($sisa<0) $sisa = 0;
					
						$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
						$rows[] = array(
							array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
							array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data_oby->anggaran - $realisasi) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
						);
					}
				}	//obyek			
				
			}
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)


// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)','tersedia');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->tersedia, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$tsd_belanja_total = $datas->tersedia;
	$rea_belanja_total = $realisasi;
	$pro_belanja_total= $prognosis;
	
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)','tersedia');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;

		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->tersedia) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->tersedia, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)','tersedia');
		$query->condition('keg.inaktif', '0', '='); 
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			$sisa = $data_jen->anggaran - $realisasi;
			
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->tersedia), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			$query = db_select('obyek', 'o');
			$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
			$query->fields('o', array('kodeo', 'uraian'));
			$query->addExpression('SUM(ag.jumlah)', 'anggaran'); $query->addExpression('SUM(ag.anggaran)','tersedia');
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('o.kodej', $data_jen->kodej, '='); 
			$query->groupBy('o.kodeo');
			$query->orderBy('o.kodeo');
			$results_oby = $query->execute();	
			foreach ($results_oby as $data_oby) {
				
				read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
				$sisa = $data_oby->anggaran - $realisasi;
				
				$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
				$rows[] = array(
					array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data_oby->tersedia) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
				);
				
			}	//obyek			
				
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)


//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$tersedia_netto = $agg_pendapata_total - $tsd_belanja_total;
$prognosis_netto = $pro_pendapata_total - $pro_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($tersedia_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($tersedia_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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

		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		
		$sisa = $data_kel->anggaran - $realisasi;
		//if ($sisa<0) $sisa = 0;
		
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);

			if (($realisasi+$data_jen->anggaran) >0) {

				$sisa = $data_jen->anggaran - $realisasi;
				//if ($sisa<0) $sisa = 0;
				
				$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
				$rows[] = array(
					array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
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
					
					read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
					
					if (($realisasi+$data_oby->anggaran) >0) {
						$sisa = $data_oby->anggaran - $realisasi;
						//if ($sisa<0) $sisa = 0;
						
						$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));					
						$rows[] = array(
							array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
							array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
						);
					}
					
				}	//obyek			
				
			}
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$tersedia_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($tersedia_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($tersedia_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
}


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print_baru($kodeuk, $penandatangan, $tanggal,$cetakpdf) {

$pimpinanatasnama = '';
if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';

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
	$results = db_query('select namauk,pimpinannama,pimpinanjabatan,pimpinannip from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
		$pimpinannama = $datas->pimpinannama;
		$pimpinanjabatan = $datas->pimpinanjabatan;
		$pimpinannip = $datas->pimpinannip;
	};
}

$rows[] = array(
	array('data' => '<strong>LAPORAN REALISASI SEMESTER PERTAMA PENDAPATAN DAN BELANJA SKPD</strong>', 'width' => '800px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => '<strong>SERTA PROGNOSIS ENAM (6) BULAN BERIKUTNYA</strong>', 'width' => '800px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '800px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

$rows[] = array(
	array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(), 'width' => '800px', 'align'=>'center','style'=>'font-size:80%;border:none'),
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
	array('data' => 'KODE','width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '300px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'JUMLAH ANGGARAN', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI SEMESTER PERTAMA', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold;'),
	array('data' => 'SISA ANGGARAN S/D SEMESTER PERTAMA', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PROGNOSIS', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'KETERANGAN', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black;'),
);
$header[] = array (
	array('data' => '1','width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold; background-color: #cccccc;'),
	array('data' => '2','width' => '300px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold; background-color: #cccccc;'),
	array('data' => '3', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold; background-color: #cccccc;'),
	array('data' => '4', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold; background-color: #cccccc;'),
	array('data' => '5 = 3 - 4', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black; background-color: #cccccc;'),
	array('data' => '6', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black; background-color: #cccccc;'),
	array('data' => '7', 'width' => '100px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black; background-color: #cccccc;'),
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
		array('data' => '', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran - $realisasi) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
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
		
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran - $realisasi) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
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
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($data_jen->anggaran + $realisasi)>0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
				
				$rows[] = array(
					array('data' => $data_jen->kodej, 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => $data_jen->uraian, 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($data_jen->anggaran - $realisasi) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($prognosis) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . $keterangan . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
				
					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					if (($data_oby->anggaran + $realisasi)>0) {
						
						$sisa = $data_oby->anggaran - $realisasi;
						if ($sisa<0) $sisa = 0;
						
						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($realisasi) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran - $realisasi) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($prognosis) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => $keterangan , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
						);
					}
				
				}	//obyek			
			}
			
		}	//jenis
		
		
	}
	


}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);
	
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
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),
		array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1.5px solid black;'),

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
				array('data' => '', 'width' => '300px', 'align'=>'left','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
				array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);			
		}
		
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
	
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
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

			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			if (($data_jen->anggaran + $realisasi)>0) {
			
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
				
				$rows[] = array(
					array('data' => $data_jen->kodej, 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => $data_jen->uraian, 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($data_jen->anggaran) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($realisasi) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($sisa) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . apbd_fn($prognosis) . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $bold_start . $keterangan . $bold_end, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					
					if (($data_oby->anggaran + $realisasi)>0) {
						$sisa = $data_oby->anggaran - $realisasi;
						if ($sisa<0) $sisa = 0;
					
						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($realisasi) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($sisa) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($prognosis) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => $keterangan , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
						);
						}
					
				}	//obyek			
			}	
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$prognosis_netto = $pro_pendapata_total - $pro_belanja_total;
$rows[] = array(
	array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
);


if (($kodeuk=='00') or ($kodeuk=='ZZ')) {
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:100%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
			
		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		//if ($sisa<0) $sisa = 0;
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
			array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;'),
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
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);
			if (($data_jen->anggaran + $realisasi)>0) {
				$sisa = $data_jen->anggaran - $realisasi;
				//if ($sisa<0) $sisa = 0;
			
				$rows[] = array(
					array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
					array('data' => $data_jen->uraian, 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($sisa), 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($prognosis), 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => $keterangan, 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
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
					
					read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
					if (($data_oby->anggaran + $realisasi)>0) {
						$sisa = $data_oby->anggaran - $realisasi;
						//if ($sisa<0) $sisa = 0;
					
						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($realisasi) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($sisa) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($prognosis) , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => $keterangan , 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
						);
					}
				}	//obyek			
			}	
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	$rows[] = array(
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN THN BERJALAN</strong>', 'width' => '300px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . $keterangan . '</strong>', 'width' => '100px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	);
	
	
}

	if (isUserSKPD())
		$cetakttd = true;
	else
		$cetakttd = ($kodeuk=='ZZ'? true: false );
		
	if($cetakttd) {

			$rows[] = array(
				array('data' => '', 'width' => '830px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;'),
				
			);
			$rows[] = array(
						array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => 'Jepara, '.$tanggal ,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
									
					);
			if ($pimpinanatasnama!='') {
				$rows[] = array(
							array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;'),
							array('data' => $pimpinanatasnama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
								
						);
			}					
			$rows[] = array(
						array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => $pimpinanjabatan,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:80%;'),
							
					);
			$rows[] = array(
						array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
					);
			$rows[] = array(
						array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
					);
			$rows[] = array(
						array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => '','width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;'),
							
					);
			$rows[] = array(
						array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;'),
						array('data' => $pimpinannama,'width' => '255px', 'align'=>'center','style'=>'border-right:1px solid black;text-decoration:underline;font-size:80%;'),					
					);
			$rows[] = array(
						array('data' => '','width' => '575px', 'align'=>'center','style'=>'border-left:1px solid black;border-bottom:1px solid black;'),
						array('data' => 'NIP. ' . $pimpinannip,'width' => '255px', 'align'=>'center','style'=>'border-bottom:1px solid black;border-right:1px solid black;font-size:80%;'),					
					);	
		
	}
	
//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}



function gen_report_realisasi_print($kodeuk, $ttdlaporan, $tanggal,$cetakpdf) {

$pimpinanatasnama = '';
if ($kodeuk == 'ZZ') {
	$skpd = 'KABUPATEN JEPARA';
	//$pimpinannama = apbd_bud_nama();
	//$pimpinanjabatan = apbd_bud_jabatan();
	//$pimpinannip = apbd_bud_nip();

	$pimpinanatasnama = variable_get('sekretarisjabatanatasnama', '');
	$pimpinannama = variable_get('sekretarisnama', '');
	$pimpinanjabatan = variable_get('sekretarisjabatan', '');
	$pimpinannip = variable_get('sekretarisnip', '');
	

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
	array('data' => '<strong>LAPORAN REALISASI SEMESTER I PENDAPATAN DAN BELANJA SKPD</strong>',  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => '<strong>SERTA PROGNOSIS ENAM (6) BULAN BERIKUTNYA</strong>', 'colspan'=>6, 'width' => '510px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'colspan'=>6, 'align'=>'center','style'=>'font-size:80%;border:none'),
);

$rows[] = array(
	array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(),  'colspan'=>6,'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
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

if ($cetakpdf == 'excel'){
//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '170px',  'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI Rupiah', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'REALISASI (%)', 'width' => '30px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'SISA ANGGARAN', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'PROGNOSIS SEMESTER II', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;font-weight: bold;border-bottom:1px solid black;border-right:1px solid black;'),
);
}else{
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
		array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;
	
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
		
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
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
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($data_jen->anggaran + $realisasi)>0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
				
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
				
					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					if (($data_oby->anggaran + $realisasi)>0) {
						
						$sisa = $data_oby->anggaran - $realisasi;
						if ($sisa<0) $sisa = 0;
						
						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran - $realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($prognosis) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
						);
					}
				
				}	//obyek			
			}
			
		}	//jenis
		
		
	}
	


}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => '', 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
);

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
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;

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
		
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
	
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

			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			if (($data_jen->anggaran + $realisasi)>0) {
			
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
				
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
					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					
					if (($data_oby->anggaran + $realisasi)>0) {
						$sisa = $data_oby->anggaran - $realisasi;
						if ($sisa<0) $sisa = 0;
					
						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' =>apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)) , 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($sisa) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($prognosis) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
						);
						}
					
				}	//obyek			
			}	
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
			
		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		//if ($sisa<0) $sisa = 0;
		
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
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);
			if (($data_jen->anggaran + $realisasi)>0) {
				//$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
			
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
					
					read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
					if (($data_oby->anggaran + $realisasi)>0) {
						//$sisa = $data_oby->anggaran - $realisasi;
						if ($sisa<0) $sisa = 0;
					
						$rows[] = array(
							array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '30px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-left:1px solid black'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '170px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($realisasi) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '30px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($sisa) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
							array('data' => apbd_fn($prognosis) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;font-style:italic;border-right:1px solid black;'),
						);
					}
				}	//obyek			
			}	
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
			if ($pimpinanatasnama!='') {
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

function gen_report_realisasi_print_lengkap($kodeuk) {

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
	array('data' => '<strong>LAPORAN REALISASI SEMESTER PERTAMA PENDAPATAN DAN BELANJA SKPD</strong>',  'colspan'=>9,'width' => '800px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => '<strong>SERTA PROGNOSIS ENAM (6) BULAN BERIKUTNYA</strong>', 'colspan'=>9, 'width' => '800px', 'align'=>'center','style'=>'font-size:100%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '800px', 'colspan'=>9, 'align'=>'center','style'=>'font-size:80%;border:none'),
);

$rows[] = array(
	array('data' => 'TAHUN ANGGARAN ' . apbd_tahun(),  'colspan'=>9,'width' => '800px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

$tabel_data = theme('table', array('header' => null, 'rows' => $rows ));

$agg_pendapata_total = 0; $agg_pendapata_total_bulanan = 0;
$agg_belanja_total = 0; $agg_belanja_total_bulanan = 0;
$agg_pembiayaan_netto = 0; $agg_pembiayaan_netto_bulanan = 0;

$rea_pendapata_total = 0; $pro_pendapata_total = 0;
$rea_belanja_total = 0; $pro_belanja_total = 0;
$rea_pembiayaan_netto = 0; $rea_pembiayaan_netto_bulanan = 0;

//TABEL
$header = array (
	array('data' => 'KODE','width' => '40px', 'valign'=>'top', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN', 'valign'=>'top', 'width' => '250px', 'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '90px', 'valign'=>'top',  'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'TERSEDIA', 'width' => '90px', 'valign'=>'top',  'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '90px', 'valign'=>'top',  'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '% AGG', 'width' => '40px', 'valign'=>'top',  'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => '% TSD', 'width' => '40px', 'valign'=>'top',  'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SISA', 'width' => '90px', 'valign'=>'top',  'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'PROGNOSIS', 'width' => '90px', 'valign'=>'top',  'align'=>'center','style'=>'border-top:1px solid black;border-bottom:2px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

$bulan = 6;
$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
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

	$rows[] = array(
		array('data' => '', 'width' => '40px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '250px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;;font-size:120%;'),
		array('data' => '', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'width' => '40px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '',  'valign'=>'top', 'width' => '90px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '',  'valign'=>'top', 'width' => '90px','align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
	);
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'width' => '40px', 'align' => 'left', 'valign'=>'top', 'style'=>' border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '250px', 'align' => 'left', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-weight: bold;font-size:120%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'valign'=>'top', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran - $realisasi) . '</strong>',  'valign'=>'top', 'width' => '90px', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>',  'valign'=>'top', 'width' => '90px','align'=>'right','style'=>'border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
	);
	
	//--
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
		
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$uraian = $data_kel->uraian;
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '40px', 'align' => 'left', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $uraian . '</strong>', 'width' => '250px', 'align' => 'left', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran - $realisasi) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
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
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($realisasi+$data_jen->anggaran) >0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
				
				$uraian = $data_jen->uraian;
				$rows[] = array(
					array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
					array('data' => $uraian, 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top','width' => '90px',  'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran - $realisasi), 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
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

					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					if (($realisasi+$data_oby->anggaran) >0) {
						
						$sisa = $data_oby->anggaran - $realisasi;
						if ($sisa<0) $sisa = 0;
					
						$rows[] = array(
							array('data' => $data_oby->kodeo, 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($realisasi) , 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($data_oby->anggaran - $realisasi) , 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;font-style:italic;'),
						);
					}
				}	//obyek			
				
			}
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

// * BELANJA * //
$rows[] = array(
	array('data' => '', 'width' => '40px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => '', 'width' => '250px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;;font-size:120%;'),
	array('data' => '', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => '', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => '', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => '', 'width' => '40px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => '',  'valign'=>'top', 'width' => '90px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => '',  'valign'=>'top', 'width' => '90px','align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
);
	
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->addExpression('SUM(ag.anggaran)','tersedia');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '250px', 'align' => 'left', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-size:120%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->tersedia) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->tersedia, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$tsd_belanja_total = $datas->tersedia;
	$rea_belanja_total = $realisasi;
	$pro_belanja_total= $prognosis;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->addExpression('SUM(ag.anggaran)','tersedia');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;

		$uraian = $data_kel->uraian;
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '40px', 'align' => 'left', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->tersedia) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->tersedia, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->addExpression('SUM(ag.anggaran)','tersedia');
		$query->condition('keg.inaktif', '0', '='); 
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			$sisa = $data_jen->anggaran - $realisasi;
			
			$uraian = $data_jen->uraian;
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top', 'width' => '250px',  'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->tersedia), 'align' => 'right',  'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($realisasi), 'align' => 'right','width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
			);
			
			
			//OBYEK
			$query = db_select('obyek', 'o');
			$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
			$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
			$query->fields('o', array('kodeo', 'uraian'));
			$query->addExpression('SUM(ag.jumlah)', 'anggaran');
			$query->addExpression('SUM(ag.anggaran)','tersedia');
			if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('o.kodej', $data_jen->kodej, '='); 
			$query->groupBy('o.kodeo');
			$query->orderBy('o.kodeo');
			$results_oby = $query->execute();	
			foreach ($results_oby as $data_oby) {
				
				read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
				$sisa = $data_oby->anggaran - $realisasi;
				
				$rows[] = array(
					array('data' => $data_oby->kodeo, 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
					array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top', 'width' => '250px', 'style'=>'border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($data_oby->tersedia) , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($realisasi) , 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'width' => '40px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_oby->tersedia, $realisasi)), 'align' => 'right', 'valign'=>'top', 'width' => '40px', 'style'=>'border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;font-style:italic;'),
					array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;font-style:italic;'),
				);
				
			}	//obyek			
				
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$tersedia_netto = $agg_pendapata_total - $tsd_belanja_total;
$prognosis_netto = $pro_pendapata_total - $pro_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($tersedia_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($tersedia_netto, $realisasi_netto)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
	array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'align' => 'right',  'width' => '90px','valign'=>'top', 'style'=>'border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
);

if (($kodeuk=='ZZ') or ($kodeuk=='00')) {

	$rows[] = array(
		array('data' => '', 'width' => '40px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '', 'width' => '250px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;;font-size:120%;'),
		array('data' => '', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '', 'width' => '40px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '',  'valign'=>'top', 'width' => '90px', 'align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
		array('data' => '',  'valign'=>'top', 'width' => '90px','align'=>'right','style'=>'font-size:40%;border-right:1px solid black;'),
	);
	
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;
	$prognosis_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>6</strong>', 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-size:120%;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '40px', 'style'=>'border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '40px', 'style'=>'border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
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

		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		
		$sisa = $data_kel->anggaran - $realisasi;
		//if ($sisa<0) $sisa = 0;
		
		$uraian = $data_kel->uraian;
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '40px', 'align' => 'left', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
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
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);

			if (($realisasi+$data_jen->anggaran) >0) {

				$sisa = $data_jen->anggaran - $realisasi;
				//if ($sisa<0) $sisa = 0;
				
				$uraian = $data_jen->uraian;
				$rows[] = array(
					array('data' => $data_jen->kodej, 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top', 'width' => '250px', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
					array('data' => apbd_fn($prognosis), 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;'),
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
					
					read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
					
					if (($realisasi+$data_oby->anggaran) >0) {
						$sisa = $data_oby->anggaran - $realisasi;
						//if ($sisa<0) $sisa = 0;
						
						$rows[] = array(
							array('data' => $data_oby->kodeo, 'align' => 'left', 'width' => '40px', 'valign'=>'top', 'style'=>'border-left:1px solid black;border-right:1px solid black;'),
							array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($sisa) , 'align' => 'right',  'width' => '90px','valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
							array('data' => apbd_fn($prognosis) , 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;font-style:italic;'),
						);
					}
					
				}	//obyek			
				
			}
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'width' => '250px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen(anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_netto_p) . '</strong>','width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto_p) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$tersedia_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$prognosis_netto += $prognosis_netto_p;
	
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top', 'width' => '40px', 'style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '250px', 'align' => 'left', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($tersedia_netto) . '</strong>', 'align' => 'right', 'valign'=>'top', 'width' => '90px', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'width' => '90px', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($tersedia_netto, $realisasi_netto)) . '</strong>', 'width' => '40px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_netto) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($prognosis_netto) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
	);
	
}
//--


//RENDER	
$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function read_realisasi($kodeuk, $kodeakun, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	
	
	//REALISASI
	if ((substr($kodeakun,0,1)=='4') or (substr($kodeakun,0,2)=='61')) {
		if ($kodeuk == 'ZZ')
			$res = db_query('SELECT SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
					FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero like :kodeakun AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%'));
			
		else 
			$res = db_query('SELECT SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
					FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	} else {
		if ($kodeuk == 'ZZ')
			$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
					FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid INNER JOIN kegiatanskpd ON jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%'));
		else
			$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
					FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid  INNER JOIN kegiatanskpd ON jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	}
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	if ($kodeuk == 'ZZ')
		$res = db_query('SELECT SUM(prognosis) as prognosisx 
				FROM prognosiskeg WHERE kodeo like :kodeakun', array(':kodeakun'=>$kodeakun . '%'));
		
	else 
		$res = db_query('SELECT SUM(prognosis) as prognosisx 
				FROM prognosiskeg WHERE kodeo like :kodeakun AND kodeuk=:kodeuk', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}

?>


