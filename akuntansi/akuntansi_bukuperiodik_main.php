<?php
function akuntansi_buku_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
    
	if ($arg) {
		
		$ntitle = 'Buku Besar';
	
		$kodekeg = arg(2);
		$kodero = arg(3);
		$kodeuk = arg(4);
		$tglawal = arg(5);
		$tglakhir = arg(6);
		$koderod = arg(7);
				

	} else {
		$kodekeg = 'ZZ';
		$kodeuk = 'ZZ';
		
	}
	if ($kodeuk=='') $kodeuk='ZZ';
	
	drupal_set_title($ntitle);
	
	
	if ($koderod=='') $koderod = 'none';
	
	$btn = apbd_button_print('/akuntansi/buku/'. $kodekeg .'/'. $kodero . '/'. $kodeuk . '/'. $tglawal . '/' . $tglakhir  . '/' . $koderod . '/pdf');
	$btn .= "&nbsp;" . apbd_button_excel('/akuntansi/buku/'. $kodekeg .'/'. $kodero . '/'. $kodeuk . '/'. $tglawal . '/' . $tglakhir  . '/' . $koderod . '/excel');	
	
	if(arg(8)=='pdf'){
			  
			  $output = getDataPrint($kodekeg, $kodero, $koderod, $kodeuk, $tglawal, $tglakhir);
			  apbd_ExportPDF_P($output, 10, "LAP");
			  print_pdf_p($output);
				
		} else if (arg(8)=='excel') {

		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan_buku.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$output = getDataPrint($kodekeg, $kodero, $koderod, $kodeuk, $tglawal, $tglakhir);
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	}
	else{
		$output = getDataView($kodekeg, $kodero, $koderod, $kodeuk, $tglawal, $tglakhir);
		//$output .= theme('pager');
		$output_form = drupal_get_form('akuntansi_buku_main_form');
		return drupal_render($output_form).$btn . $output . $btn;	
	}
	
}

function getDataView($kodekeg, $kodero, $koderod, $kodeuk, $tglawal, $tglakhir) {

	if (isSuperuser()) {
		$suffix = '';
	} else {
		$suffix = 'uk';
	}
	
	if ($koderod=='0') $koderod = 'none';
	if ($kodeuk=='ZZ') {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Tanggal', 'field'=> 'tanggal', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'SKPD','field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'No. Bukti', 'valign'=>'top'),
			array('data' => 'No. Ref', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Debet', 'field'=> 'debet','width' => '90px', 'valign'=>'top'),
			array('data' => 'Kredit', 'field'=> 'kredit','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		);	
	} else {	
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Tanggal', 'field'=> 'tanggal', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'No. Bukti', 'valign'=>'top'),
			array('data' => 'No. Ref', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Debet', 'field'=> 'debet','width' => '90px', 'valign'=>'top'),
			array('data' => 'Kredit', 'field'=> 'kredit','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
		);
	}		
		
	
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');
		
		$query->fields('ji', array('kodero','debet','kredit'));
		$query->fields('j', array('jurnalid', 'kodekeg', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
		$query->fields('k', array('kegiatan'));
		$query->fields('u', array('namasingkat'));
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');
		$query->condition('j.tanggal', $tglawal, '>=');
		$query->condition('j.tanggal', $tglakhir, '<=');
		
		//$query->orderByHeader($header);
		$query->orderBy('j.tanggal', 'ASC');
		//$query->limit($limit);
		//drupal_set_message($ne);	
		//drupal_set_message($query);	
		# execute the query
	
	} else {
		$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		
		$query->fields('ji', array('kodero','debet','kredit'));
		$query->fields('j', array('jurnalid', 'kodekeg', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
		$query->fields('u', array('namasingkat'));
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');
		
		if ($koderod !='none') $query->condition('ji.koderod', $koderod, '=');

		$query->condition('j.tanggal', $tglawal, '>=');
		$query->condition('j.tanggal', $tglakhir, '<=');
		
		
		//$query->orderByHeader($header);
		$query->orderBy('j.tanggal', 'ASC');
		//$query->limit($limit);
		//drupal_set_message($ne);	
		//drupal_set_message($query);	
		# execute the query
	}
	dpq($query);
	
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	
	$totalkdebet=0;$totalkredit=0;		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$nobukti = $data->nobukti;
		if ($nobukti=='') 
			$nobukti = 	$data->nobuktilain;
		else {
			if ($data->nobuktilain!='') $nobukti .= '/' . $data->nobuktilain;
		}
		
		//$editlink = apbd_button_jurnal('akuntansi/edit/'.$tahun.'/'.$data->jurnalid);
		$editlink = apbd_button_jurnal('pendapatanjurnal/jurnaledit/' . $data->jurnalid);
		
		/*
		if ($data->refid!='') {
			$editlink .= apbd_button_sp2d('penata/edit/tahun/'.$data->refid);
		}		
		*/
		
		$kegiatan = '';
		/*
		if (substr($kodero,0,1)=='5')
			if ($data->kegiatan!='') {
				$kegiatan = l($data->kegiatan , 'belanja/rekening/' . '/' . $data->kodekeg , array ('html' => true, 'attributes'=> array ('class'=>'text-info pull-right')));
			}
		else 
			$kegiatan = 'Pendaptan';
		*/
		
		if ($kodeuk=='ZZ') {
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->keterangan . $kegiatan, 'align' => 'left', 'valign'=>'top'),
							array('data' => $nobukti, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->noref, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		} else {
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->keterangan . $kegiatan, 'align' => 'left', 'valign'=>'top'),
							array('data' => $nobukti, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->noref, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		}
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
	//SEBELUMNYA
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');
		
		$query->addExpression('SUM(debet)', 'sadebet');
		$query->addExpression('SUM(kredit)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');
		$query->condition('j.tanggal', $tglawal, '<');
	
	} else {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		
		$query->addExpression('SUM(debet)', 'sadebet');
		$query->addExpression('SUM(kredit)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');

		$query->condition('j.tanggal', $tglawal, '<');
		
	}	
	$results = $query->execute();
	
	$saldoawal = 0;	
	$sakredit = 0;	
	foreach ($results as $data) {
		$sadebet = $data->sadebet;	
		$sakredit = $data->sakredit;	
	}
	
	if ($kodeuk=='ZZ') {
		$rows[] = array(
							array('data' => '<strong>SUB TOTAL</strong>', 'colspan'=>'6', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
		$rows[] = array(
							array('data' => '<strong>JUMLAH SEBELUMNYA</strong>', 'colspan'=>'6', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($sadebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($sakredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
		$totaldebet = $totaldebet + $sadebet;
		$totalkredit = $totalkredit + $sakredit;
		$rows[] = array(
							array('data' => '<strong>TOTAL</strong>', 'colspan'=>'6', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);						
		if ($totalkdebet>=$totalkredit)
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'6', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkdebet-$totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);
		else
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'6', 'align' => 'left', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkredit-$totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);
			
			
	} else {
		$rows[] = array(
							array('data' => '<strong>SUB TOTAL</strong>', 'colspan'=>'5', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
		$rows[] = array(
							array('data' => '<strong>JUMLAH SEBELUMNYA</strong>', 'colspan'=>'5', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($sadebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($sakredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
		$totaldebet = $totaldebet + $sadebet;
		$totalkredit = $totalkredit + $sakredit;
		$rows[] = array(
							array('data' => '<strong>TOTAL</strong>', 'colspan'=>'5', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);
						
		if ($totalkdebet>=$totalkredit)
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'5', 'align' => 'left', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkdebet-$totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);
		else
			$rows[] = array(
								array('data' => '<strong>NETTO</strong>', 'colspan'=>'5', 'align' => 'left', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
								array('data' => '<strong>' . apbd_fn($totalkredit-$totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
								array('data' => '', 'align' => 'right', 'valign'=>'top'),
							);						
	}
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function getDataPrint($kodekeg, $kodero, $koderod, $kodeuk, $tglawal, $tglakhir) {

	if (isSuperuser()) {
		$suffix = '';
	} else {
		$suffix = 'uk';
	}
	
	if ($koderod=='0') $koderod = 'none';
	
	if (substr($kodero,0,1)=='5') {
		
		if ($kodekeg=='ZZ') {		//SELURUH KEGIATAN
			$kegiatan = 'SELURUH KEGIATAN';
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
		} else {

			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('k', array('kegiatan', 'kodeuk'));
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			
			$query->condition('k.kodekeg', $kodekeg, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$kegiatan = $data->kegiatan;
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				$kodeuk= $data->kodeuk;
			}
		}
		
	} else if ((substr($kodero,0,1)=='4') or (substr($kodero,0,1)=='6')) {		//PENDAPATAN & PEMBIAYAAN
			if (substr($kodero,0,1)=='4')
				$kegiatan = 'PENDAPATAN';
			else 
				$kegiatan = 'PEMBIAYAAN';
			
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('anggperuk', 'a');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			
			if ($kodeuk !='ZZ') $query->condition('a.kodeuk', $kodeuk, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
			//Detil
			$rekeningdetil = '';
			if ($koderos != 'none') {
				$query = db_select('rincianobyekdetil', 'ro');
				$query->fields('ro', array('koderod','uraian'));
				$query->condition('ro.kodero', $kodero, '=');
				$query->condition('ro.koderod', $koderod, '=');
				
				$results = $query->execute();
				foreach ($results as $data) {
					$rekeningdetil = $data->koderod . ' - ' . $data->uraian;
					
				}
			}
			
	} else if (substr($kodero,0,1)=='1')  {		//KAS
			$kegiatan = 'KAS';
			
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('rincianobyek', 'ro');
			$query->fields('ro', array('kodero','uraian'));
			$query->condition('ro.kodero', $kodero, '=');
			
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				
			}
			$anggaran= '0';
			
	} else {		//NON APBD
		$kegiatan = 'NON ANGGARAN';
		$rekening= $data->kodero;
		$anggaran = 0;
	}
	
	if ($kodeuk=='ZZ') 
		$namauk= 'SELURUH SKPD';
	else {
		$query = db_select('unitkerja', 'u');
		$query->fields('u', array('namauk'));
		$query->condition('u.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$namauk= $data->namauk;
		}		
	}	
	
	$top=array();
	//apbd_format_tanggal_pendek($data->tanggal)
	if ($koderod !='none') {
		$top[] = array(
				array('data' => 'BUKU BESAR PEMBANTU','width' => '510px', 'align'=>'center','style'=>'border:none;'),
				);	
	} else {
		$top[] = array(
				array('data' => 'BUKU BESAR','width' => '510px', 'align'=>'center','style'=>'border:none;'),
				);
	}
	
	
	$top[] = array(
						array('data' => 'Tanggal ' . apbd_format_tanggal_pendek($tglawal) . ' s/d. ' . apbd_format_tanggal_pendek($tglakhir) ,'width' => '510px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => '','width' => '510px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => 'SKPD','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => $namauk,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => 'Kegiatan','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => $kegiatan,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => 'Rekening','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => $rekening,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	if ($koderos != 'none') {
		$top[] = array(
							array('data' => 'Detil Rekening','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
							array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
							array('data' => $rekeningdetil,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
							
		);
	}
	$top[] = array(
						array('data' => 'Anggaran','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => apbd_fn($anggaran),'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$output = theme('table', array('header' => null, 'rows' => $top ));
	
	$header = array ();
	$header = array (
		array('data' => 'No', 'width' => '30px', 'align'=>'center','style'=>'font-size:90%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Tanggal', 'width' => '55px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian','width' => '140px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'No. Bukti', 'width' => '80px','align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'No. Ref', 'width' => '50px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Debet', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Kredit', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);	
		
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');
		
		$query->fields('ji', array('kodero','debet','kredit'));
		$query->fields('j', array('jurnalid', 'kodekeg', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
		$query->fields('k', array('kegiatan'));
		$query->fields('u', array('namasingkat'));
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');
		$query->condition('j.tanggal', $tglawal, '>=');
		$query->condition('j.tanggal', $tglakhir, '<=');
		
		//$query->orderByHeader($header);
		$query->orderBy('j.tanggal', 'ASC');
		//$query->limit($limit);
		//drupal_set_message($ne);	
		//drupal_set_message($query);	
		# execute the query
	
	} else {
		$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		
		$query->fields('ji', array('kodero','debet','kredit'));
		$query->fields('j', array('jurnalid', 'kodekeg', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
		$query->fields('u', array('namasingkat'));
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');

		$query->condition('j.tanggal', $tglawal, '>=');
		$query->condition('j.tanggal', $tglakhir, '<=');
		if ($koderod !='none') $query->condition('ji.koderod', $koderod, '=');
		
		//$query->orderByHeader($header);
		$query->orderBy('j.tanggal', 'ASC');
		//$query->limit($limit);
		//drupal_set_message($ne);	
		//drupal_set_message($query);	
		# execute the query
	}
	//dpq($query);
	
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	
	$totalkdebet=0;$totalkredit=0;		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$nobukti = $data->nobukti;
		if ($nobukti=='') 
			$nobukti = 	$data->nobuktilain;
		else {
			if ($data->nobuktilain!='') $nobukti .= '/' . $data->nobuktilain;
		}
		
		//$editlink = apbd_button_jurnal('akuntansi/edit/'.$tahun.'/'.$data->jurnalid);
		$editlink = apbd_button_jurnal('pendapatanjurnal/jurnaledit/' . $data->jurnalid);
		
		/*
		if ($data->refid!='') {
			$editlink .= apbd_button_sp2d('penata/edit/tahun/'.$data->refid);
		}		
		*/
		
		$kegiatan = '';
		/*
		if (substr($kodero,0,1)=='5')
			if ($data->kegiatan!='') {
				$kegiatan = l($data->kegiatan , 'belanja/rekening/' . '/' . $data->kodekeg , array ('html' => true, 'attributes'=> array ('class'=>'text-info pull-right')));
			}
		else 
			$kegiatan = 'Pendaptan';
		*/
		
		$rows[] = array(
						array('data' => $no, 'width' => '30px', 'align'=>'right','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal), 'width' => '55px', 'align' => 'center', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => $data->keterangan . $kegiatan, 'width' => '140px', 'align' => 'left', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => $nobukti, 'width' => '80px', 'align' => 'center', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => $data->noref, 'width' => '50px', 'align' => 'center', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => apbd_fn($data->debet), 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => apbd_fn($data->kredit), 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
						
					);
		
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
	//SEBELUMNYA
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');
		
		$query->addExpression('SUM(debet)', 'sadebet');
		$query->addExpression('SUM(kredit)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');
		$query->condition('j.tanggal', $tglawal, '<');
	
	} else {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		
		$query->addExpression('SUM(debet)', 'sadebet');
		$query->addExpression('SUM(kredit)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');

		$query->condition('j.tanggal', $tglawal, '<');
		
	}	
	$results = $query->execute();
	
	$saldoawal = 0;	
	$sakredit = 0;	
	foreach ($results as $data) {
		$sadebet = $data->sadebet;	
		$sakredit = $data->sakredit;	
	}
	

	$rows[] = array(
						array('data' => '<strong>SUB TOTAL</strong>', 'colspan'=>'5', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-top:1px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-top:1px solid black;'),
					);
	$rows[] = array(
						array('data' => '<strong>JUMLAH SEBELUMNYA</strong>', 'colspan'=>'5', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => '<strong>' . apbd_fn($sadebet) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => '<strong>' . apbd_fn($sakredit) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
					);
	$totaldebet = $totaldebet + $sadebet;
	$totalkredit = $totalkredit + $sakredit;
	$rows[] = array(
						array('data' => '<strong>TOTAL</strong>', 'colspan'=>'5', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;'),
					);
					
	if ($totalkdebet>=$totalkredit)
		$rows[] = array(
							array('data' => '<strong>NETTO</strong>', 'colspan'=>'5', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
							array('data' => '<strong>' . apbd_fn($totalkdebet-$totalkredit) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
							array('data' => '', 'align' => 'right', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
						);
	else
		$rows[] = array(
							array('data' => '<strong>NETTO</strong>', 'colspan'=>'5', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
							array('data' => '', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
							array('data' => '<strong>' . apbd_fn($totalkredit-$totalkdebet) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-bottom:1px solid black;border-top:1px solid black;'),
						);						
	
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}


function getTable($tahun,$kodeuk,$kodekeg,$kodero){
	
	if (substr($kodero,0,1)=='5') {
		
		if ($kodekeg=='ZZ') {		//SELURUH KEGIATAN
			$kegiatan = 'SELURUH KEGIATAN';
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
		} else {

			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('k', array('kegiatan', 'kodeuk'));
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			$query->condition('k.kodekeg', $kodekeg, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$kegiatan = $data->kegiatan;
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				$kodeuk= $data->kodeuk;
			}
		}
		
	} else if ((substr($kodero,0,1)=='4') or (substr($kodero,0,1)=='6')) {		//PENDAPATAN & PEMBIAYAAN
			if (substr($kodero,0,1)=='4')
				$kegiatan = 'PENDAPATAN';
			else 
				$kegiatan = 'PEMBIAYAAN';
			
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('anggperuk', 'a');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			if ($kodeuk !='ZZ') $query->condition('a.kodeuk', $kodeuk, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
		
	} else {		//NON APBD
		$kegiatan = 'NON ANGGARAN';
		$rekening= $data->kodero;
		$anggaran = 0;
	}
	
	if ($kodeuk=='ZZ') 
		$namauk= 'SELURUH SKPD';
	else {
		$query = db_select('unitkerja', 'u');
		$query->fields('u', array('namauk'));
		$query->condition('u.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$namauk= $data->namauk;
		}		
	}	
	
	$top=array();
	$top[] = array(
						array('data' => 'SKPD','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => $namauk,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => 'Kegiatan','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => $kegiatan,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => 'Rekening','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => $rekening,'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => 'Anggaran','width' => '70px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						array('data' => ':','width' => '20px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						array('data' => apbd_fn($anggaran),'width' => '280px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$header = array ();
	$output = theme('table', array('header' => $header, 'rows' => $top ));
	
	
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Tanggal', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'Uraian', 'width' => '140px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'No. Bukti', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'No. Ref', 'width' => '80px','align'=>'center','style'=>$styleheader),
		array('data' => 'Debet', 'width' => '90', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kredit', 'width' => '90','align'=>'center','style'=>$styleheader),
		
		
		
	);
	$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
	$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
	//$query->leftJoin('kegiatan', 'k', 'j.kodekeg=k.kodekeg');
	$query->fields('ji', array('kodero','debet','kredit'));
	$query->fields('j', array('jurnalid', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
	//$query->fields('k', array('kegiatan'));
	
	if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
	if ($kodekeg !='ZZ') $query->condition('j.kodekeg', $kodekeg, '=');
	if ($kodero !='ZZ') $query->condition('ji.kodero', $kodero, '=');
	
	//$query->orderByHeader($header);
	$query->orderBy('j.tanggal', 'ASC');
	//$query->limit($limit);
	//drupal_set_message($ne);	
	
	//drupal_set_message($query);	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;


	
	$totalkdebet=0;$totalkredit=0;		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$nobukti = $data->nobukti;
		if ($nobukti=='') 
			$nobukti = 	$data->nobuktilain;
		else {
			if ($data->nobuktilain!='') $nobukti .= '/' . $data->nobuktilain;
		} 
		
		$kegiatan = '';
		if ($data->kegiatan!='') {
			$kegiatan = '<p>' . $data->kegiatan . '</p>';
		}
		
		$rows[] = array(
						array('data' => $no,'width' => '40px', 'align'=>'right','style'=>'border-left:1px solid black;border-right:1px solid black;font-size:90%'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),'width' => '80px', 'align'=>'center','style'=>'border-right:1px solid black;font-size:90%'),
						array('data' => $data->keterangan . $kegiatan,'width' => '140px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:90%'),
						array('data' => $nobukti,'width' => '80px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:90%'),
						array('data' => $data->noref,'width' => '80px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:90%'),
						array('data' => apbd_fn($data->debet),'width' => '90px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:90%'),
						array('data' => apbd_fn($data->kredit),'width' => '90px', 'align'=>'right','style'=>'border-right:1px solid black;font-size:90%'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
		);
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
	$rows[] = array(
						array('data' => 'JUMLAH', 'width' => '420px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
						array('data' => apbd_fn($totalkdebet), 'width' => '90px','colspan'=>'3', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
						array('data' => apbd_fn($totalkredit), 'width' => '90px','colspan'=>'3', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
	);
	
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	//$output = 'cek';
	return $output;
}

function akuntansi_buku_main_form($form, &$form_state) {

	$kodekeg = arg(2);
	$kodero = arg(3);
	$kodeuk = arg(4);
	
	//drupal_set_message(arg(3));
	
	if (!isset($kodeuk)) $kodeuk='ZZ';
	
	
	if (substr($kodero,0,1)=='5') {
		
		if ($kodekeg=='ZZ') {		//SELURUH KEGIATAN
			$kegiatan = 'SELURUH KEGIATAN';
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
		} else {

			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('k', array('kegiatan', 'kodeuk'));
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			$query->condition('k.kodekeg', $kodekeg, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$kegiatan = $data->kegiatan;
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				$kodeuk= $data->kodeuk;
			}
		}
		
	} else if ((substr($kodero,0,1)=='4') or (substr($kodero,0,1)=='6')) {		//PENDAPATAN & PEMBIAYAAN
			if (substr($kodero,0,1)=='4')
				$kegiatan = 'PENDAPATAN';
			else 
				$kegiatan = 'PEMBIAYAAN';
			
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('anggperuk', 'a');
			$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
			$query->fields('ro', array('kodero','uraian'));
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			$query->condition('a.kodero', $kodero, '=');
			if ($kodeuk !='ZZ') $query->condition('a.kodeuk', $kodeuk, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $data->kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
		
	} else {		//NON APBD
		$kegiatan = 'NON ANGGARAN';
		$rekening= $data->kodero;
		$anggaran = 0;
	}
	
	if ($kodeuk=='ZZ') 
		$namauk= 'SELURUH SKPD';
	else {
		$query = db_select('unitkerja', 'u');
		$query->fields('u', array('namauk'));
		$query->condition('u.kodeuk', $kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $data) {
			$namauk= $data->namauk;
		}		
	}	
	$form['skpd'] = array(
		'#type' => 'item',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $namauk . '</p>',
	);
	$form['keg'] = array(
		'#type' => 'item',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $kegiatan . '</p>',
	);
	$form['rekening'] = array(
		'#type' => 'item',
		'#title' =>  t('Rekening'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $rekening . '</p>',
	);
	$form['anggaran'] = array(
		'#type' => 'item',
		'#title' =>  t('Anggaran'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p class="text-right">' . apbd_fn($anggaran) . '</p>',
		//'#markup' => '<p>' . apbd_fn($anggaran) . '</p>',
	);
	
	$kodeakun = substr($kodero,0,1);
	/*
	if (($kodeakun =='4') or ($kodeakun =='5') or ($kodeakun =='6')) {
		$form['chart'] = array(
			'#type' => 'item',
			'#title' =>  t('Anggaran'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			//'#disabled' => true,
			'#markup' =>  draw_chart_buku_besar('', $kodeuk, $kodekeg, $kodero),
		);
	}
	*/
	
	return $form;
}


?>
