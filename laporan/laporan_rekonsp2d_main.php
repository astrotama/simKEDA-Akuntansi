<?php
function laporan_rekonsp2d_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	if ($arg) {
		$filter = arg(1);
		$bulan = arg(2);
		$kodeuk = arg(3);
		$tingkat = arg(4);
		$cetakpdf = arg(5);
		
	} else {
		$bulan = date('n')-1;		//variable_get('apbdtahun', 0);
		if (isUserSKPD()) {
			$filter = 'filter';
			$kodeuk = apbd_getuseruk();
			$tingkat = '3';
		} else {
			$filter = 'filteruk';
			$kodeuk = '69';
			$tingkat = '3';	
		}
		
	}
	
	if ($bulan=='0') $bulan='12';
	 
	
	if ($filter=='filter') {
		$output = gen_report_realisasi($bulan, $kodeuk, $tingkat);
		$output_form = drupal_get_form('laporan_rekonsp2d_main_form');	

		if ($cetakpdf == 'xls') {
			header( "Content-Type: application/vnd.ms-excel" );
			header( "Content-disposition: attachment; filename=Rekon_Akuntansi_Kas_per_Rekening.xls" );
			header("Pragma: no-cache"); 
			header("Expires: 0");
			echo $output;
			
		} else {
			$btn = l('Excel', 'laporanrekonkas/filter/' . $bulan . '/'. $kodeuk .'/'.$tingkat. '/xls' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));
			return drupal_render($output_form) . $btn . $output . $btn;
		}
		
	} else if ($filter=='filterkeg') {
		
		$output = gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat);
		if ($cetakpdf == 'xls') {
			header( "Content-Type: application/vnd.ms-excel" );
			header( "Content-disposition: attachment; filename=Rekon_Akuntansi_Kas_per_Kegiatan.xls" );
			header("Pragma: no-cache"); 
			header("Expires: 0");
			echo $output;
			
		} else {
			$output_form = drupal_get_form('laporan_rekonsp2d_main_form');	
			
			$btn = l('Excel', 'laporanrekonkas/filterkeg/' . $bulan . '/'. $kodeuk .'/'.$tingkat. '/xls' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));

			return drupal_render($output_form) . $btn . $output . $btn;
		}
		
	} else {
		//drupal_set_message(arg(4));
		
		$output = gen_report_realisasi_uk($bulan, $tingkat);
		if ($cetakpdf == 'xls') {
			header( "Content-Type: application/vnd.ms-excel" );
			header( "Content-disposition: attachment; filename=Rekon_Akuntansi_Kas_per_SKPD.xls" );
			header("Pragma: no-cache"); 
			header("Expires: 0");
			echo $output;
			
		} else {
			$output_form = drupal_get_form('laporan_rekonsp2d_main_form');	
			
			$btn = l('Excel', 'laporanrekonkas/filteruk/' . $bulan . '/'. $kodeuk .'/'.$tingkat. '/xls' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));

			return drupal_render($output_form) . $btn . $output . $btn;
		}	
	}	
	
}

function laporan_rekonsp2d_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$tingkat = $form_state['values']['tingkat'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$uri = 'laporanrekonsp2d/filter/' . $bulan . '/'. $kodeuk . '/' . $tingkat;
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitkeg']) {
		$uri = 'laporanrekonsp2d/filterkeg/' . $bulan . '/'. $kodeuk . '/' . $tingkat;
	} else {
		$uri = 'laporanrekonsp2d/filteruk/' . $bulan . '/'. $kodeuk . '/' . $tingkat;
	}	
	drupal_goto($uri);
	
}


function laporan_rekonsp2d_main_form($form, &$form_state) {
	
	if(arg(1)==null){
		if (isUserSKPD()) {
			$filter = 'filter';
		} else {
			$filter = 'filteruk';
		}		
	} else {
		$filter = arg(1);
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
	
	if ($filter=='filteruk') {
		$kodeuk = 'ZZ';
		$bulan = date('n')-1;
		$tingkat = '5';
		
		if(arg(2)!=null){
			
			$bulan = arg(2);
			$kodeuk = arg(3);
			$tingkat = arg(4);
			
		} 

		if ($bulan=='0') $bulan='12';

		$opttingkat = array();
		$opttingkat['5'] = 'SEMUA BELANJA';
		$opttingkat['51'] = 'BELANJA TIDAK LANGSUNG';
		$opttingkat['52'] = 'BELANJA LANGSUNG';
		
		$form['formdata'] = array (
			'#type' => 'fieldset',
			'#title'=> $arr_bulan[$bulan] . '|' . $opttingkat[$tingkat] . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
			//'#attributes' => array('class' => array('container-inline')),
			'#collapsible' => TRUE, 
			'#collapsed' => TRUE,        
		);	

		
		$form['formdata']['bulan'] = array(
			'#type' => 'select',
			'#title' => 'Bulan',
			'#default_value' => $bulan,	
			'#options' => $arr_bulan,
		);
		
		//SKPD
		$form['formdata']['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
		);
			
		$form['formdata']['tingkat'] = array(
			'#type' => 'select',
			'#title' =>  t('Kelompok Belanja'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $opttingkat,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $tingkat,
		);
		 
		$form['formdata']['submituk'] = array(
			'#type' => 'submit',
			'#value' => apbd_button_tampilkan(),
			'#attributes' => array('class' => array('btn btn-success')),
		);
	
	} else {

		$opttingkat = array();
		$opttingkat['3'] = 'JENIS';
		$opttingkat['4'] = 'OBYEK';
		$opttingkat['5'] = 'RINCIAN';
	
		if (isUserSKPD()) {
			$kodeuk = apbd_getuseruk();
		} else {
			$kodeuk = '81';
		}
		$namasingkat = '|SELURUH SKPD';
		$bulan = date('n')-1;
		$tingkat = '3';
		
		if(arg(2)!=null){
			
			$bulan = arg(2);
			$kodeuk = arg(3);
			$tingkat = arg(4);
			
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

		
		$form['formdata'] = array (
			'#type' => 'fieldset',
			'#title'=> $arr_bulan[$bulan] . $namasingkat . '|' . $opttingkat[$tingkat] . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
			//'#attributes' => array('class' => array('container-inline')),
			'#collapsible' => TRUE, 
			'#collapsed' => TRUE,        
		);	

		
		$form['formdata']['bulan'] = array(
			'#type' => 'select',
			'#title' => 'Bulan',
			'#default_value' => $bulan,	
			'#options' => $arr_bulan,
		);
		
		//SKPD
		if (isUserSKPD()) {
			$form['formdata']['kodeuk'] = array(
				'#type' => 'hidden',
				'#default_value' => $kodeuk,
			);
			
		} else {
			global $user;
			$username = $user->name;		
			
			$result = db_query('SELECT unitkerja.kodeuk, unitkerja.namasingkat FROM unitkerja INNER JOIN userskpd ON unitkerja.kodeuk=userskpd.kodeuk WHERE userskpd.username=:username ORDER BY unitkerja.namasingkat', array(':username' => $username));	
			while($row = $result->fetchObject()){
				$option_skpd[$row->kodeuk] = $row->namasingkat; 
			}
			
			$form['formdata']['kodeuk'] = array(
				'#type' => 'select',
				'#title' =>  t('SKPD'),
				// The entire enclosing div created here gets replaced when dropdown_first
				// is changed.
				'#options' => $option_skpd,
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
		 
		$form['formdata']['submit'] = array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>Tampilkan per Rekening',
			'#attributes' => array('class' => array('btn btn-success')),
		);
		$form['formdata']['submitkeg'] = array(
			'#type' => 'submit',
			'#value' => '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>Tampilkan per Kegiatan',
			'#attributes' => array('class' => array('btn btn-info')),
		);
	}
	return $form;
}

function gen_report_realisasi($bulan, $kodeuk, $tingkat) {

if (isUserSKPD())
	$sufixjurnal = 'uk';
else
	$sufixjurnal = '';

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
$tanggal_awal = apbd_tahun() . '-01-01';
	
$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapatan_total_dinas = 0;
$rea_belanja_total_dinas = 0;
$rea_pembiayaan_netto_dinas = 0;

$rea_pendapatan_total_pusat = 0;
$rea_belanja_total_pusat = 0;
$rea_pembiayaan_netto_pusat = 0;

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Jurnal', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'SP2D', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Selisih', 'width' => '90px', 'valign'=>'top'),
);
$rows = array();

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasijurnal = 0;$realisasikas = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasijurnal = $data->realisasi;
	}
	
	
	db_set_active('penatausahaan');
	$sql = db_select('dokumen', 'j');
	$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
	$sql->condition('j.sp2dok', '1', '='); 
	$sql->condition('j.sp2dno', '', '<>'); 	 
	
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasikas = $data->realisasi;
	}
	db_set_active();
	
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea. '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasijurnal) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasikas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasikas- $realisasijurnal) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total_dinas = $realisasijurnal;
	$rea_belanja_total_pusat = $realisasikas;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasijurnal = 0;$realisasikas = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasijurnal = $data->realisasi;
		}
		
		
		db_set_active('penatausahaan');
		$sql = db_select('dokumen', 'j');
		$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
		$sql->condition('j.sp2dok', '1', '='); 
		$sql->condition('j.sp2dno', '', '<>'); 
		 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasikas = $data->realisasi;
		}
		db_set_active();	
		
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasijurnal) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasikas) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasikas- $realisasijurnal) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasijurnal = 0;$realisasikas = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasijurnal = $data->realisasi;
			}
			
			
			db_set_active('penatausahaan');
			$sql = db_select('dokumen', 'j');
			$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
			$sql->condition('j.sp2dok', '1', '='); 
			$sql->condition('j.sp2dno', '', '<>'); 
			 
			
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasikas = $data->realisasi;
			}
			db_set_active();
			
			$realisasijurnal_s = l(apbd_fn($realisasijurnal), '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => $realisasijurnal_s, 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasikas), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasikas- $realisasijurnal), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasijurnal = 0;$realisasikas = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasijurnal = $data->realisasi;
					}
				
					db_set_active('penatausahaan');
					$sql = db_select('dokumen', 'j');
					$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
					$sql->condition('j.sp2dok', '1', '='); 
					$sql->condition('j.sp2dno', '', '<>'); 
					 
					
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasikas = (is_null($data->realisasi)?0: $data->realisasi);
					}
					db_set_active();

					$realisasijurnal_s = l(apbd_fn($realisasijurnal), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					

					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => $realisasijurnal_s , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasikas) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasikas- $realisasijurnal), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasijurnal = 0;$realisasikas = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasijurnal = $data->realisasi;
							}
						
							db_set_active('penatausahaan');
							$sql = db_select('dokumen', 'j');
							$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
							$sql->condition('j.sp2dok', '1', '='); 
							$sql->condition('j.sp2dno', '', '<>'); 
							 
							
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasikas = $data->realisasi;
							}
							db_set_active();

							$realisasijurnal_s = l(apbd_fn($realisasijurnal), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
							
							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. $realisasijurnal_s . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasikas) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasikas- $realisasijurnal) . '</em>', 'align' => 'right', 'valign'=>'top'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)



//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_print($bulan, $kodeuk, $tingkat) {

if (isUserSKPD())
	$sufixjurnal = 'uk';
else
	$sufixjurnal = '';

if ($kodeuk == 'ZZ')
	$skpd = 'KABUPATEN JEPARA';
else {
	$results = db_query('select namauk from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	foreach ($results as $datas) {
		$skpd = $datas->namauk;
	};
}

$rows[] = array(
	array('data' => 'LAPORAN REALISASI ANGGARAN', 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
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

$rea_pendapata_total_dinas = 0;
$rea_belanja_total_dinas = 0;
$rea_pembiayaan_netto_dinas = 0;

$rea_pendapata_total_pusat = 0;
$rea_belanja_total_pusat = 0;
$rea_pembiayaan_netto_pusat = 0;

$rows = null;
//TABEL


//$header = array();
$header[] = array (
	array('data' => 'KODE','width' => '45px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '185px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'ANGGARAN', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REALISASI', 'width' => '140px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'SELISIH', 'width' => '70px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'Jurnal', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	array('data' => 'SPJ', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
);

$rows = array();

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasijurnal = 0; $realisasikas = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasijurnal = $data->realisasi;
	}

	db_set_active('penatausahaan');
	$sql = db_select('dokumen', 'j');
	$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
	if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$sql->condition('j.sp2dok', '1', '='); 
	$sql->condition('j.sp2dno', '', '<>'); 
	 
	
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasikas = $data->realisasi;
	}
	db_set_active();

	$rows[] = array(
		array('data' => $datas->kodea, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'width' => '185px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasijurnal) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasikas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => '<strong>' . apbd_fn($realisasikas - $realisasijurnal) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total_dinas = $realisasijurnal;
	$rea_belanja_total_pusat = $realisasijurnal;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasijurnal = 0; $realisasikas = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasijurnal = $data->realisasi;
		}
			
		db_set_active('penatausahaan');
		$sql = db_select('dokumen', 'j');
		$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$sql->condition('j.sp2dok', '1', '='); 
		$sql->condition('j.sp2dno', '', '<>'); 
		 
		
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasikas = $data->realisasi;
		}			
		db_set_active();
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'width' => '185px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasijurnal) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasikas) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . apbd_fn($realisasikas - $realisasijurnal) . '</strong>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasijurnal = 0; $realisasikas = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasijurnal = $data->realisasi;
			}
		
			db_set_active('penatausahaan');
			$sql = db_select('dokumen', 'j');
			$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$sql->condition('j.sp2dok', '1', '='); 
			$sql->condition('j.sp2dno', '', '<>'); 
			 
			
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasikas = $data->realisasi;
			}
			db_set_active();

			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => $data_jen->uraian, 'width' => '185px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($data_jen->anggaran), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasijurnal), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasikas), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => apbd_fn($realisasikas - $realisasijurnal), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasijurnal = 0; $realisasikas = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasijurnal = $data->realisasi;
					}
				
					db_set_active('penatausahaan');
					$sql = db_select('dokumen', 'j');
					$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$sql->condition('j.sp2dok', '1', '='); 
					$sql->condition('j.sp2dno', '', '<>'); 
					 
					
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasikas = $data->realisasi;
					}
					db_set_active();

					$rows[] = array(
						array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'width' => '185px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data_oby->anggaran) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasijurnal) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' =>apbd_fn($realisasikas) , 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($realisasikas - $realisasijurnal), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasijurnal = 0; $realisasikas = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasijurnal = $data->realisasi;
							}

							db_set_active('penatausahaan');
							$sql = db_select('dokumen', 'j');
							$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$sql->condition('j.sp2dok', '1', '='); 
							$sql->condition('j.sp2dno', '', '<>'); 
							 
							
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasikas = $data->realisasi;
							}
							db_set_active();
							
							$rows[] = array(
								array('data' => $data_jen->kodej . '.' . substr($data_oby->kodeo,-2) . '.'. substr($data_rek->kodero, -3), 'width' => '45px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'width' => '185px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasijurnal) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasikas) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
								array('data' => '<em>'. apbd_fn($realisasikas - $realisasijurnal) . '</em>', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
							);
						
						}	//obyek					
						
					}	//rekening
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)



$rows[] = array(
		array('data' => '', 'width' => '510px', 'align'=>'right','style'=>'font-size:80%;border-top:1px solid black;'),
		
	);

//RENDER	
//$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat) {

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
$tanggal_awal = apbd_tahun() . '-01-01';


if (isUserSKPD()) {
	$sufixjurnal = 'uk';
} else {
	$sufixjurnal = '';
}



//TABEL
$header = array (
	array('data' => 'Kode','width' => '15px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Jurnal', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'SP2D', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Selisih', 'width' => '80px', 'valign'=>'top'),
);
$rows = array();

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasi_lra = 0; $realisasi_spj = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi_lra = $data->realisasi;
	}
	
	db_set_active('penatausahaan');
	$sql = db_select('dokumen', 'b');
	$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
	$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
	$sql->condition('keg.kodeuk', $kodeuk, '='); 
	$sql->condition('bi.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
	$sql->condition('b.sp2dok', '1', '='); 
	$sql->condition('b.sp2dno', '', '<>'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi_spj = $data->realisasi;
	}
	db_set_active();
	
	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea. '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_spj) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_spj -  $realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total_lra = $realisasi_lra;
	$rea_belanja_total_spj = $realisasi_spj;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi_lra = 0; $realisasi_spj = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi_lra = $data->realisasi;
		}
		
		db_set_active('penatausahaan');		
		$sql = db_select('dokumen', 'b');
		$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
		$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->condition('bi.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
		$sql->condition('b.sp2dok', '1', '='); 
		$sql->condition('b.sp2dno', '', '<>'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi_spj = $data->realisasi;
		}
		db_set_active();
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi_spj) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi_spj -  $realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasi_lra = 0; $realisasi_spj = 0;
			$kodej = $data_kel->kodek . '.000.000.' . substr($data_jen->kodej,-1);
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi_lra = $data->realisasi;
			}
			
			db_set_active('penatausahaan');
			$sql = db_select('dokumen', 'b');
			$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
			$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
			$sql->condition('keg.kodeuk', $kodeuk, '='); 
			$sql->condition('bi.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
			$sql->condition('b.sp2dok', '1', '='); 
			$sql->condition('b.sp2dno', '', '<>'); 
			
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi_spj = $data->realisasi;
			}
			db_set_active();
			
			$realisasi_lra_s = l(apbd_fn($realisasi_lra), '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => $realisasi_lra_s, 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi_spj), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi_spj -  $realisasi_lra), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasi_lra = 0; $realisasi_spj = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi_lra = $data->realisasi;
					}
					
					db_set_active('penatausahaan');
					$sql = db_select('dokumen', 'b');
					$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
					$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
					$sql->condition('keg.kodeuk', $kodeuk, '='); 
					$sql->condition('bi.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
					$sql->condition('b.sp2dok', '1', '='); 
					$sql->condition('b.sp2dno', '', '<>'); 
					
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi_spj = $data->realisasi;
					}	
					db_set_active();		

					$realisasi_lra_s = l(apbd_fn($realisasi_lra), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					
					$rows[] = array(
						array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => $realisasi_lra_s , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi_spj) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi_spj -  $realisasi_lra), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasi_lra = 0; $realisasi_spj = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi_lra = $data->realisasi;
							}
							
							db_set_active('penatausahaan');
							$sql = db_select('dokumen', 'b');
							$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
							$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
							$sql->condition('keg.kodeuk', $kodeuk, '='); 
							$sql->condition('bi.kodero', db_like($data_rek->kodero) . '%', 'LIKE'); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
							$sql->condition('b.sp2dok', '1', '='); 
							$sql->condition('b.sp2dno', '', '<>'); 

							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi_spj = $data->realisasi;
							}	
							db_set_active();

							$realisasi_lra_s = l(apbd_fn($realisasi_lra), '/akuntansi/buku/ZZ/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
							
							$rows[] = array(
								array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. $realisasi_lra_s . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasi_spj) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasi_spj -  $realisasi_lra) . '</em>', 'align' => 'right', 'valign'=>'top'),
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
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi_lra = 0; $realisasi_spj = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi_lra = $data->realisasi;
		}
		
		db_set_active('penatausahaan');	
		$sql = db_select('dokumen', 'b');
		$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
		$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
		$sql->condition('keg.kodeuk', $kodeuk, '='); 
		$sql->condition('bi.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
		$sql->condition('b.sp2dok', '1', '='); 
		$sql->condition('b.sp2dno', '', '<>'); 

		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi_spj = $data->realisasi;
		}
		db_set_active();	
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi_spj) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi_spj -  $realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.anggaran)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.anggaran', '0', '>'); 
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			$realisasi_lra = 0; $realisasi_spj = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$sql->condition('keg.jenis', '2', '='); 
			$sql->condition('ji.kodero', db_like('52') . '%', 'LIKE'); 
			if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi_lra = $data->realisasi;
			}
			
			db_set_active('penatausahaan');
			$sql = db_select('dokumen', 'b');
			$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
			$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
			$sql->condition('keg.kodeuk', $kodeuk, '='); 
			$sql->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$sql->condition('keg.jenis', '2', '='); 
			$sql->condition('bi.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
			$sql->condition('b.sp2dok', '1', '='); 
			$sql->condition('b.sp2dno', '', '<>'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi_spj = $data->realisasi;
			}
			db_set_active();
			
			$kodepro = $data_kel->kodek . '.' . $data_pro->kodepro;
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi_spj) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi_spj -  $realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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
				
				$realisasi_lra = 0; $realisasi_spj = 0;
				
				$sql = db_select('jurnal' . $sufixjurnal, 'j');
				$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('j.kodekeg', $data_keg->kodekeg, '='); 
				$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
				$sql->condition('ji.kodero', db_like('52') . '%', 'LIKE');
				if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasi_lra = $data->realisasi;
				} 
				
				db_set_active('penatausahaan');
				$sql = db_select('dokumen', 'b');
				$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
				$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
				$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
				$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$sql->condition('bi.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
				if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
				$sql->condition('b.sp2dok', '1', '='); 
				$sql->condition('b.sp2dno', '', '<>'); 

				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasi_spj = $data->realisasi;
				} 
				db_set_active();

				$realisasi_lra_s = l(apbd_fn($realisasi_lra), '/akuntansi/buku/' . $data_keg->kodekeg . '/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
				
				$kodekeg = $kodepro . '.' . substr($data_keg->kodekeg,-3);
				$rows[] = array(
					array('data' => '<strong>' . $kodekeg  . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . strtoupper($data_keg->kegiatan) . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($data_keg->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . $realisasi_lra_s . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasi_spj) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasi_spj -  $realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				);				
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {
					
					$realisasi_lra = 0; $realisasi_spj = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi_lra = $data->realisasi;
					}
					
					db_set_active('penatausahaan');
					$sql = db_select('dokumen', 'b');
					$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
					$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
					$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
					$sql->condition('bi.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
					$sql->condition('b.sp2dok', '1', '='); 
					$sql->condition('b.sp2dno', '', '<>'); 

					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi_spj = $data->realisasi;
					}
					db_set_active();

					$realisasi_lra_s = l(apbd_fn($realisasi_lra), '/akuntansi/buku/' . $data_keg->kodekeg . '/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
					
					$kodej = $kodekeg . '.' . substr($data_jen->kodej,-1);
					$rows[] = array(
						array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
						array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
						array('data' => $realisasi_lra_s, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi_spj), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi_spj -  $realisasi_lra), 'align' => 'right', 'valign'=>'top'),
					);
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {
							
							$realisasi_lra = 0; $realisasi_spj = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
							$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
							$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi_lra = $data->realisasi;
							}
							
							db_set_active('penatausahaan');
							$sql = db_select('dokumen', 'b');
							$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
							$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
							$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
							$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
							$sql->condition('bi.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
							$sql->condition('b.sp2dok', '1', '='); 
							$sql->condition('b.sp2dno', '', '<>'); 

							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi_spj = $data->realisasi;
							}						
							db_set_active();

							$realisasi_lra_s = l(apbd_fn($realisasi_lra), '/akuntansi/buku/' . $data_keg->kodekeg . '/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
							
							$rows[] = array(
								array('data' => $kodej . substr($data_oby->kodeo,-2), 'align' => 'left', 'valign'=>'top'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => $realisasi_lra_s , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi_spj) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi_spj -  $realisasi_lra), 'align' => 'right', 'valign'=>'top'),
							);
							
							//REKENING
							if ($tingkat=='5') {
								$query = db_select('rincianobyek', 'ro');
								$query->innerJoin('anggperkeg', 'ag', 'ro.kodero=ag.kodero');
								$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
								$query->fields('ro', array('kodero', 'uraian'));
								$query->addExpression('SUM(ag.anggaran)', 'anggaran');
								$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
								$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
								$query->groupBy('ro.kodero');
								$query->orderBy('ro.kodero');
								$results_rek = $query->execute();	
								foreach ($results_rek as $data_rek) {
									
									$realisasi_lra = 0; $realisasi_spj = 0;
									$sql = db_select('jurnal' . $sufixjurnal, 'j');
									$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
									$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
									$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
									$sql->condition('ji.kodero', $data_rek->kodero, '='); 
									$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									if ($kodeuk!='ZZ') $sql->condition('keg.kodeuk', $kodeuk, '='); 
									if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
									$res = $sql->execute();
									foreach ($res as $data) {
										$realisasi_lra = $data->realisasi;
									}
									
									db_set_active('penatausahaan');
									$sql = db_select('dokumen', 'b');
									$sql->innerJoin('dokumenrekening', 'bi', 'b.dokid=bi.dokid');
									$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=b.kodekeg');
									$sql->addExpression('SUM(bi.jumlah)', 'realisasi');
									$sql->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
									$sql->condition('bi.kodero', $data_rek->kodero, '='); 
									if ($bulan>0) $sql->where('EXTRACT(MONTH FROM b.sp2dtgl) <= :month', array('month' => $bulan));
									$sql->condition('b.sp2dok', '1', '='); 
									$sql->condition('b.sp2dno', '', '<>'); 

									$res = $sql->execute();
									foreach ($res as $data) {
										$realisasi_spj = $data->realisasi;
									}									
									db_set_active();

									$realisasi_lra_s = l(apbd_fn($realisasi_lra), '/akuntansi/buku/' . $data_keg->kodekeg . '/'  . $data_rek->kodero  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
									
									$rows[] = array(
										array('data' => $kodej . substr($data_rek->kodero,-5), 'align' => 'left', 'valign'=>'top'),
										array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. $realisasi_lra_s . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($realisasi_spj) . '</em>', 'align' => 'right', 'valign'=>'top'),
										array('data' => '<em>'. apbd_fn($realisasi_spj -  $realisasi_lra) . '</em>', 'align' => 'right', 'valign'=>'top'),
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


/*
if ($kodeuk=='ZZ') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_lra_netto_p = 0;

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
	$query->addExpression('SUM(ag.anggaran)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$realisasi_lra = 0; $realisasi_spj = 0;
		$sql = db_select('jurnal' . $sufixjurnal, 'j');
		$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi_lra = $data->realisasi;
		}
		
		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran -  $realisasi_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_lra_netto_p += $realisasi_lra;

		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_lra_netto_p -= $realisasi_lra;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$realisasi_lra = 0; $realisasi_spj = 0;
			$sql = db_select('jurnal' . $sufixjurnal, 'j');
			$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi_lra = $data->realisasi;
			}
		
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi_lra), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran -  $realisasi_lra), 'align' => 'right', 'valign'=>'top'),
			);
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.anggaran)', 'anggaran');
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {
					
					$realisasi_lra = 0; $realisasi_spj = 0;
					$sql = db_select('jurnal' . $sufixjurnal, 'j');
					$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', db_like($data_oby->kodeo) . '%', 'LIKE'); 
					if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$res = $sql->execute();
					foreach ($res as $data) {
						$realisasi_lra = $data->realisasi;
					}
				
					$rows[] = array(
						array('data' => $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi_lra) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran -  $realisasi_lra), 'align' => 'right', 'valign'=>'top'),
					);
					
					//REKENING
					if ($tingkat=='5') {
						$query = db_select('rincianobyek', 'ro');
						$query->innerJoin('anggperda', 'ag', 'ro.kodero=ag.kodero');
						$query->fields('ro', array('kodero', 'uraian'));
						$query->addExpression('SUM(ag.anggaran)', 'anggaran');
						$query->condition('ro.kodeo', $data_oby->kodeo, '='); 
						$query->groupBy('ro.kodero');
						$query->orderBy('ro.kodero');
						$results_rek = $query->execute();	
						foreach ($results_rek as $data_rek) {
							
							$realisasi_lra = 0; $realisasi_spj = 0;
							$sql = db_select('jurnal' . $sufixjurnal, 'j');
							$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
							$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
							$sql->condition('ji.kodero', $data_rek->kodero, '='); 
							if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
							$res = $sql->execute();
							foreach ($res as $data) {
								$realisasi_lra = $data->realisasi;
							}
						
							$rows[] = array(
								array('data' => $data_rek->kodero, 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. ucfirst(strtolower($data_rek->uraian)) . '</em>', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($realisasi_lra) . '</em>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<em>'. apbd_fn($data_rek->anggaran -  $realisasi_lra) . '</em>', 'align' => 'right', 'valign'=>'top'),
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
		array('data' => '<strong>' . apbd_fn($realisasi_lra_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p - $realisasi_lra_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_lra_netto += $realisasi_lra_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_lra_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto - $realisasi_lra_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
}
*/

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function gen_report_realisasi_uk($bulan, $tingkat) {

if (isUserSKPD())
	$sufixjurnal = 'uk';
else
	$sufixjurnal = '';

$tanggal_akhir = date("Y-m-d", mktime(0, 0, 0, $bulan+1,0,apbd_tahun()));;	
$tanggal_awal = apbd_tahun() . '-01-01';
	
$agg_belanja_total = 0;
$rea_belanja_total_lra = 0;
$rea_belanja_total_spj = 0;

//TABEL
$header = array (
	array('data' => 'No.','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Jurnal', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'SP2D', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Selisih', 'width' => '90px', 'valign'=>'top'),
);
$rows = array();

// * BELANJA * //
//AKUN
$query = db_select('anggperkeg', 'ag');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->innerJoin('unitkerja', 'uk', 'uk.kodeuk=keg.kodeuk');
$query->fields('uk', array('kodeuk', 'namauk'));
$query->addExpression('SUM(ag.anggaran)', 'anggaran');
$query->condition('keg.inaktif', '0', '='); 
//$query->condition('keg.kodeuk', '05', '<='); 
$query->groupBy('uk.kodeuk');
$query->orderBy('uk.kodedinas');


$results = $query->execute();

$i = 0;
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$realisasijurnal = 0;$realisasikas = 0;
	$sql = db_select('jurnal' . $sufixjurnal, 'j');
	$sql->innerJoin('jurnalitem' . $sufixjurnal, 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ji.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasijurnal = $data->realisasi;
	}
	
	
	db_set_active('penatausahaan');
	$sql = db_select('dokumen', 'j');
	$sql->innerJoin('dokumenrekening', 'ji', 'j.dokid=ji.dokid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$sql->addExpression('SUM(ji.jumlah)', 'realisasi');
	$sql->condition('keg.kodeuk', $datas->kodeuk, '='); 
	$sql->condition('ji.kodero', db_like('5') . '%', 'LIKE'); 
	if ($bulan>0) $sql->where('EXTRACT(MONTH FROM j.sp2dtgl) <= :month', array('month' => $bulan));
	$sql->condition('j.sp2dok', '1', '='); 
	$sql->condition('j.sp2dno', '', '<>'); 
	
	//dpq($sql);
	
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasikas = $data->realisasi;
	}
	db_set_active();
	
	
	//laporanrekonsp2d/filter/12/57/3

	$skpd = l($datas->namauk, '/laporanrekonsp2d/filter/'  . $bulan  . '/'  . $datas->kodeuk . '/3', array('attributes' => array('class' => null)));
	
	$i++;
	$rows[] = array(
		array('data' => $i, 'align' => 'left', 'valign'=>'top'),
		array('data' => $skpd , 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->anggaran) , 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasijurnal) , 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasikas) , 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($realisasikas- $realisasijurnal) , 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total += $datas->anggaran;
	$rea_belanja_total_lra += $realisasijurnal;
	$rea_belanja_total_spj += $realisasikas;
	
}	//foreach ($results as $datas)
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($agg_belanja_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_belanja_total_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_belanja_total_spj) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_belanja_total_spj - $rea_belanja_total_lra) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


?>


