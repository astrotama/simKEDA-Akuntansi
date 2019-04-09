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
		/*	
		$btn .= "&nbsp;" . '<div class="btn-group pull-right">' .
				'<button type="button" class="btn btn-primary btn-sm glyphicon glyphicon-print dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
				' Periode <span class="caret"></span>' .
				'</button>' .
					'<ul class="dropdown-menu">' .
						'<li><a href="/akuntansi/buku/' . urlencode($kodekeg) .'/'. $kodero . '/'. urlencode($kodeuk) . '/'. apbd_tahun() . '-01-01/' . apbd_tahun()  . '-03-31/' . $koderod . '">Triwulan #1</a></li>' .
						'<li><a href="/akuntansi/buku/' . urlencode($kodekeg) .'/'. $kodero . '/'. urlencode($kodeuk) . '/'. apbd_tahun() . '-04-01/' . apbd_tahun() . '-06-30/' . $koderod . '/pdfp">Triwulan #2</a></li>' .
						'<li><a href="/akuntansi/buku/' . $kodekeg .'/'. $kodero . '/'. $kodeuk . '/'. apbd_tahun() . '-07-01/' . apbd_tahun() . '-09-30/' . urlencode($kodekeg) . '/pdf">Triwulan #3</a></li>' .
						'<li><a href="/akuntansi/buku/' . urlencode($kodekeg) .'/'. $kodero . '/'. urlencode($kodeuk) . '/'. apbd_tahun() . '-10-01/' . apbd_tahun() . '-12-31/' . $koderod . '/pdf">Triwulan #4</a></li>' .
						'<li><a href="/akuntansi/buku/' . urlencode($kodekeg) .'/'. $kodero . '/'. urlencode($kodeuk) . '/'. apbd_tahun() . '-01-01/' . apbd_tahun() . '-06-30/' . $koderod . '/pdf">Semester I</a></li>' .
						'<li><a href="/akuntansi/buku/' . urlencode($kodekeg) .'/'. $kodero . '/'. urlencode($kodeuk) . '/'. apbd_tahun() . '-07-01/' . apbd_tahun() . '-12-31/' . $koderod . '/pdf">Semester II</a></li>' .
						'<li><a href="/akuntansi/buku/' . urlencode($kodekeg) .'/'. $kodero . '/'. urlencode($kodeuk) . '/'. apbd_tahun() . '-01-01/' . apbd_tahun() . '-12-31/' . $koderod . '/pdf">Setahun</a></li>' .
					'</ul>' .
				'</div>';
		*/			
				
	if(arg(8)=='pdf'){
			  
			  $output = getDataPrint($kodekeg, $kodero, $koderod, $kodeuk, $tglawal, $tglakhir);
			  $_SESSION["hal1"] = 1;
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
		 
	} else{
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
			array('data' => 'Tanggal',  'width' => '90px', 'valign'=>'top'),
			array('data' => 'SKPD',  'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'No. Bukti', 'valign'=>'top'),
			array('data' => 'Debet',  'width' => '90px', 'valign'=>'top'),
			array('data' => 'Kredit',  'width' => '90px', 'valign'=>'top'),
			array('data' => 'Saldo',  'width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
		);	
	} else {	
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'Uraian', 'valign'=>'top'),
			array('data' => 'No. Bukti', 'valign'=>'top'),
			array('data' => 'Debet', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'Kredit', 'width' => '90px', 'valign'=>'top'),
			array('data' => 'Saldo', 'width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
		);
	}		
		
	//SEBELUMNYA
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'ji.kodekeg=k.kodekeg');
		
		$query->addExpression('SUM(debet-kredit)', 'sadebet');
		$query->addExpression('SUM(kredit-debet)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');
		$query->condition('j.tanggal', $tglawal, '<');
	
	} else {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		
		$query->addExpression('SUM(debet-kredit)', 'sadebet');
		$query->addExpression('SUM(kredit-debet)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');
		
		$query->condition('j.tanggal', $tglawal, '<');
		
	}	
	$results = $query->execute();
	
	$sakredit = 0;	
	$sadebet = 0;
	foreach ($results as $data) {
		$sadebet = $data->sadebet;	
		$sakredit = $data->sakredit;	
	}
	
	if ((substr($kodero,0,1)=='1') or (substr($kodero,0,1)=='5') or (substr($kodero,0,2)=='62')) 
		$saldo = $sadebet;	
	else
		$saldo = $sakredit;	
	
	$rows = array();
	if ($kodeuk=='ZZ') {
		$rows[] = array(
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => 'SALDO AWAL', 'align' => 'left', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => NULL, 'align' => 'right', 'valign'=>'top'),
						array('data' => NULL, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($saldo), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:80%'),
						NULL,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					); 
	} else {
		$rows[] = array(
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => 'SALDO AWAL', 'align' => 'left', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => NULL, 'align' => 'right', 'valign'=>'top'),
						array('data' => NULL, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($saldo), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:80%'),
						NULL,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
				);
	}
	
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'ji.kodekeg=k.kodekeg');
		
		$query->fields('ji', array('kodero','debet','kredit'));
		$query->fields('j', array('jurnalid', 'kodekeg', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan', 'jenis'));
		$query->fields('k', array('kegiatan'));
		$query->fields('u', array('namasingkat'));
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');
		$query->condition('j.tanggal', $tglawal, '>=');
		$query->condition('j.tanggal', $tglakhir, '<=');
		
		//$query->orderByHeader($header);
		$query->orderBy('j.tanggal', 'ASC');
		//$query->limit($limit);
		//drupal_set_message($suffix);	
		//drupal_set_message($query);	
		# execute the query
	
	} else {
		$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		
		$query->fields('ji', array('kodero','debet','kredit'));
		$query->fields('j', array('jurnalid', 'kodekeg', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan', 'jenis'));
		$query->fields('u', array('namasingkat'));
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');
		
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
	//dpq($query);
	
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	
	$totalkdebet=0;$totalkredit=0;		

	foreach ($results as $data) {
		$no++;  
		
		$nobukti = $data->nobukti;
		if ($nobukti=='') 
			$nobukti = 	$data->nobuktilain;
		else {
			if ($data->nobuktilain!='') $nobukti .= ' (' . $data->nobuktilain . ')';
		}
		
		$editlink = apbd_button_jurnal('');
		$edoc = apbd_button_edok('');

		if (isSuperuser()) {
			if ($data->jenis=='spj') {
				$editlink = apbd_button_jurnal('jurnalspjjurnal/jurnaledit/' . $data->jurnalid);
				$edoc = apbd_button_esp2d($data->refid);
			} else if ($data->jenis=='pad') {
				$editlink = apbd_button_jurnal('pendapatanjurnal/jurnaledit/' . $data->jurnalid);
			} else if (($data->jenis=='umum-spj') or ($data->jenis=='umum-kas')) {
				$editlink = apbd_button_jurnal('umum/jurnal/' . $data->jurnalid);
			} else {
				$editlink = apbd_button_jurnal('umum/jurnal/' . $data->jurnalid);
			}
				
		} 	else {
			if ($data->jenis=='spj') {
				$editlink = apbd_button_jurnal('jurnalspjjurnal/jurnaledit/' . $data->jurnalid);
				$edoc = apbd_button_esp2d($data->refid);
			} else if ($data->jenis=='pad-in') {
				$editlink = apbd_button_jurnal('pendapatanmasuk/edit/' . $data->jurnalid);
			} else if ($data->jenis=='pad-setor') {
				$editlink = apbd_button_jurnal('pendapatansetor/edit/' . $data->jurnalid);
			} else if (($data->jenis=='umum-spj') or ($data->jenis=='umum-kas')) {
				$editlink = apbd_button_jurnal('umum/jurnal/' . $data->jurnalid);
			} else {
				$editlink = apbd_button_jurnal('umum/jurnal/' . $data->jurnalid);
			}
		}
		
		
		if ((substr($kodero,0,1)=='1') or (substr($kodero,0,1)=='5') or (substr($kodero,0,2)=='62'))
			$saldo = $saldo + $data->debet - $data->kredit;	
		else 
			$saldo = $saldo + $data->kredit - $data->debet;	
				
		if ($kodeuk=='ZZ') {
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => $nobukti, 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_fn($saldo), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							$editlink,
							$edoc,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		} else {
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => $nobukti, 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							array('data' => apbd_fn($saldo), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
							$editlink,
							$edoc,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		}
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
	
	if ($kodeuk=='ZZ') {
		$rows[] = array(
							array('data' => '<strong>TOTAL</strong>', 'colspan'=>'5', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($saldo) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '', 'align' => 'right', 'valign'=>'top'),
						);					
 
			
			 
	} else {
		$rows[] = array(
							array('data' => '<strong>TOTAL</strong>', 'colspan'=>'4', 'align' => 'left', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
							array('data' => '<strong>' . apbd_fn($saldo) . '</strong>', 'align' => 'right', 'valign'=>'top'),
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
			$query->innerJoin('kegiatanskpd', 'k', 'k.kodekeg=a.kodekeg');
			$query->condition('k.inaktif', '0', '=');
			if ($kodeuk!='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
			
			if (strlen($kodero)==8) {
				$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
				$query->fields('ro', array('uraian'));
				$query->condition('a.kodero', $kodero, '=');
				
			} else {
				$query->condition('a.kodero', db_like($kodero) . '%', 'LIKE');
				
				if (strlen($kodero)==5) {
					$query->innerJoin('obyek', 'ro', 'left(a.kodero,5)=ro.kodeo');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==3) {
					$query->innerJoin('jenis', 'ro', 'left(a.kodero,3)=ro.kodej');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==2) {	
					$query->innerJoin('kelompok', 'ro', 'left(a.kodero,2)=ro.kodek');
					$query->fields('ro', array('uraian'));
				
				} else {
					$query->innerJoin('anggaran', 'ro', 'left(a.kodero,1)=ro.kodea');
					$query->fields('ro', array('uraian'));
					
				}
					
			}			
			$query->addExpression('SUM(a.jumlah)', 'anggaran');
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
		} else {

			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$query->fields('k', array('kegiatan', 'kodeuk'));
			$query->condition('k.inaktif', '0', '=');
			if ($kodeuk!='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
			
			if (strlen($kodero)==8) {
				$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
				$query->fields('ro', array('uraian'));
				$query->condition('a.kodero', $kodero, '=');
				
			} else {
				$query->condition('a.kodero', db_like($kodero) . '%', 'LIKE');
				
				if (strlen($kodero)==5) {
					$query->innerJoin('obyek', 'ro', 'left(a.kodero,5)=ro.kodeo');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==3) {
					$query->innerJoin('jenis', 'ro', 'left(a.kodero,3)=ro.kodej');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==2) {	
					$query->innerJoin('kelompok', 'ro', 'left(a.kodero,2)=ro.kodek');
					$query->fields('ro', array('uraian'));
				
				} else {
					$query->innerJoin('anggaran', 'ro', 'left(a.kodero,1)=ro.kodea');
					$query->fields('ro', array('uraian'));
					
				}
					
			}
			
			$query->addExpression('SUM(a.jumlah)', 'anggaran');
			$query->condition('k.kodekeg', $kodekeg, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$kegiatan = $data->kegiatan;
				$rekening= $kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				$kodeuk= $data->kodeuk;
			}
		}
		
	} else if ((substr($kodero,0,1)=='4') or (substr($kodero,0,1)=='6')) {		//PENDAPATAN & PEMBIAYAAN
			if (substr($kodero,0,1)=='4') {
				$kegiatan = 'PENDAPATAN';
				$query = db_select('anggperuk', 'a');
			} else {
				$kegiatan = 'PEMBIAYAAN';
				$query = db_select('anggperda', 'a');
			}
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			
			if (strlen($kodero)==8) {
				$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
				$query->fields('ro', array('uraian'));
				$query->condition('a.kodero', $kodero, '=');
				
			} else {
				$query->condition('a.kodero', db_like($kodero) . '%', 'LIKE');
				
				if (strlen($kodero)==5) {
					$query->innerJoin('obyek', 'ro', 'left(a.kodero,5)=ro.kodeo');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==3) {
					$query->innerJoin('jenis', 'ro', 'left(a.kodero,3)=ro.kodej');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==2) {	
					$query->innerJoin('kelompok', 'ro', 'left(a.kodero,2)=ro.kodek');
					$query->fields('ro', array('uraian'));
				
				} else {
					$query->innerJoin('anggaran', 'ro', 'left(a.kodero,1)=ro.kodea');
					$query->fields('ro', array('uraian'));
					
				}
					
			}			
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			if ($kodeuk !='ZZ') $query->condition('a.kodeuk', $kodeuk, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $kodero . ' - ' . $data->uraian;
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
		array('data' => 'Uraian','width' => '120px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'No. Bukti', 'width' => '70px','align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Debet', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Kredit', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Saldo', 'width' => '80px', 'align'=>'center','style'=>'font-size:90%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);	

	//SEBELUMNYA
	$rows = array(); 
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'ji.kodekeg=k.kodekeg');
		
		$query->addExpression('SUM(debet-kredit)', 'sadebet');
		$query->addExpression('SUM(kredit-debet)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');
		$query->condition('j.tanggal', $tglawal, '<');
	
	} else {
		$query = db_select('jurnalitem' . $suffix, 'ji');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		
		$query->addExpression('SUM(debet-kredit)', 'sadebet');
		$query->addExpression('SUM(kredit-debet)', 'sakredit');
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');

		$query->condition('j.tanggal', $tglawal, '<');
		
	}	
	$results = $query->execute(); 
	 
	$sakredit = 0; $sadebet = 0;	
	foreach ($results as $data) {
		$sadebet = $data->sadebet;	
		$sakredit = $data->sakredit;	
	}
	if ((substr($kodero,0,1)=='1') or (substr($kodero,0,1)=='5') or (substr($kodero,0,2)=='62')) 
		$saldo = $sadebet;	
	else
		$saldo = $sakredit;	

	$rows[] = array(
					array('data' => '', 'width' => '30px', 'align'=>'right','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => '', 'width' => '55px', 'align' => 'center', 'style'=>'font-size:90%;border-right:1px solid black;'),
					array('data' => 'SALDO AWAL', 'width' => '120px', 'align' => 'left', 'style'=>'font-size:90%;border-right:1px solid black;'),
					array('data' => '', 'width' => '70px', 'align' => 'center', 'style'=>'font-size:90%;border-right:1px solid black;'),
					array('data' => '', 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
					array('data' => '', 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
					array('data' => apbd_fn($saldo), 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
					
				);	
	//SEKARANG
	if (substr($kodero,0,1)=='5') {
		$query = db_select('jurnalitem' . $suffix, 'ji')->extend('TableSort');
		$query->innerJoin('jurnal' . $suffix, 'j', 'ji.jurnalid=j.jurnalid');
		$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'ji.kodekeg=k.kodekeg');
		
		$query->fields('ji', array('kodero','debet','kredit'));
		$query->fields('j', array('jurnalid', 'kodekeg', 'refid', 'tanggal', 'nobukti', 'nobuktilain', 'noref','keterangan'));
		$query->fields('k', array('kegiatan'));
		$query->fields('u', array('namasingkat'));
		
		if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');
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
		if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
		if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');

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
	
	foreach ($results as $data) {
		$no++;  
		
		$nobukti = $data->nobukti;
		if ($nobukti=='') 
			$nobukti = 	$data->nobuktilain;
		else 
			if ($data->nobuktilain!='') $nobukti .= '/' . $data->nobuktilain;
		
		
		/*
		$kegiatan = '';
		if (substr($kodero,0,1)=='5')
			if ($data->kegiatan!='') {
				$kegiatan = l($data->kegiatan , 'belanja/rekening/' . '/' . $data->kodekeg , array ('html' => true, 'attributes'=> array ('class'=>'text-info pull-right')));
			}
		else 
			$kegiatan = 'Pendaptan';
		*/

		//if ((substr($kodero,0,1)=='5')==(substr($kodero,0,2)=='62'))
		//	$saldo += $data->debet;
		//else
		//if (substr($kodero,0,1)=='5') 
		//	$saldo = $saldo + $data->debet - $data->kredit;	
		//else 
		//	$saldo = $saldo + $data->kredit - $data->debet;	
				
		if ((substr($kodero,0,1)=='1') or (substr($kodero,0,1)=='5') or (substr($kodero,0,2)=='62'))
			$saldo = $saldo + $data->debet - $data->kredit;	
		else 
			$saldo = $saldo + $data->kredit - $data->debet;		
		
		$rows[] = array(
						array('data' => $no, 'width' => '30px', 'align'=>'right','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal), 'width' => '55px', 'align' => 'center', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => $data->keterangan . $kegiatan, 'width' => '120px', 'align' => 'left', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => $nobukti, 'width' => '70px', 'align' => 'center', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => apbd_fn($data->debet), 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => apbd_fn($data->kredit), 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
						array('data' => apbd_fn($saldo), 'width' => '80px', 'align' => 'right', 'style'=>'font-size:90%;border-right:1px solid black;'),
						
					);
		
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
 
	

	$rows[] = array(
						array('data' => '<strong>TOTAL</strong>', 'colspan'=>'4', 'align'=>'left','style'=>'font-size:90%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
						array('data' => '<strong>' . apbd_fn($saldo) . '</strong>', 'align'=>'right','style'=>'font-size:90%;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
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
	if ($kodekeg !='ZZ') $query->condition('ji.kodekeg', $kodekeg, '=');
	if ($kodero !='ZZ') $query->condition('ji.kodero', db_like($kodero) . '%', 'LIKE');
	
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
	$tglawal = arg(5);
	$tglakhir = arg(6);
	$koderod = arg(7);

	$form['kodekeg'] = array(
		'#type' => 'value',
		'#value' => $kodekeg,
	);
	$form['kodero'] = array(
		'#type' => 'value',
		'#value' => $kodero,
	);
	$form['kodeuk'] = array(
		'#type' => 'value',
		'#value' => $kodeuk,
	);
	$form['koderod'] = array(
		'#type' => 'value',
		'#value' => $koderod,
	);
	
	
	//drupal_set_message(arg(3));
	
	if (!isset($kodeuk)) $kodeuk='ZZ';
	
	
	if (substr($kodero,0,1)=='5') {
		
		if ($kodekeg=='ZZ') {		//SELURUH KEGIATAN
			$kegiatan = 'SELURUH KEGIATAN';
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('kegiatanskpd', 'k', 'k.kodekeg=a.kodekeg');
			$query->condition('k.inaktif', '0', '=');
			if ($kodeuk!='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
			
			if (strlen($kodero)==8) {
				$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
				$query->fields('ro', array('uraian'));
				$query->condition('a.kodero', $kodero, '=');
				
			} else {
				$query->condition('a.kodero', db_like($kodero) . '%', 'LIKE');
				
				if (strlen($kodero)==5) {
					$query->innerJoin('obyek', 'ro', 'left(a.kodero,5)=ro.kodeo');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==3) {
					$query->innerJoin('jenis', 'ro', 'left(a.kodero,3)=ro.kodej');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==2) {	
					$query->innerJoin('kelompok', 'ro', 'left(a.kodero,2)=ro.kodek');
					$query->fields('ro', array('uraian'));
				
				} else {
					$query->innerJoin('anggaran', 'ro', 'left(a.kodero,1)=ro.kodea');
					$query->fields('ro', array('uraian'));
					
				}
					
			}			
			$query->addExpression('SUM(a.jumlah)', 'anggaran');
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				
			}
			
		} else {

			$query = db_select('anggperkeg', 'a');
			$query->innerJoin('kegiatanskpd', 'k', 'a.kodekeg=k.kodekeg');
			$query->fields('k', array('kegiatan', 'kodeuk'));
			$query->condition('k.inaktif', '0', '=');
			if ($kodeuk!='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
			
			if (strlen($kodero)==8) {
				$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
				$query->fields('ro', array('uraian'));
				$query->condition('a.kodero', $kodero, '=');
				
			} else {
				$query->condition('a.kodero', db_like($kodero) . '%', 'LIKE');
				
				if (strlen($kodero)==5) {
					$query->innerJoin('obyek', 'ro', 'left(a.kodero,5)=ro.kodeo');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==3) {
					$query->innerJoin('jenis', 'ro', 'left(a.kodero,3)=ro.kodej');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==2) {	
					$query->innerJoin('kelompok', 'ro', 'left(a.kodero,2)=ro.kodek');
					$query->fields('ro', array('uraian'));
				
				} else {
					$query->innerJoin('anggaran', 'ro', 'left(a.kodero,1)=ro.kodea');
					$query->fields('ro', array('uraian'));
					
				}
					
			}
			
			$query->addExpression('SUM(a.jumlah)', 'anggaran');
			$query->condition('k.kodekeg', $kodekeg, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$kegiatan = $data->kegiatan;
				$rekening= $kodero . ' - ' . $data->uraian;
				$anggaran= $data->anggaran;
				$kodeuk= $data->kodeuk;
			}
		}
		
	} else if ((substr($kodero,0,1)=='4') or (substr($kodero,0,1)=='6')) {		//PENDAPATAN & PEMBIAYAAN
			if (substr($kodero,0,1)=='4') {
				$kegiatan = 'PENDAPATAN';
				$query = db_select('anggperuk', 'a');
			} else {
				$kegiatan = 'PEMBIAYAAN';
				$query = db_select('anggperda', 'a');
			}
			//$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
			
			if (strlen($kodero)==8) {
				$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero');
				$query->fields('ro', array('uraian'));
				$query->condition('a.kodero', $kodero, '=');
				
			} else {
				$query->condition('a.kodero', db_like($kodero) . '%', 'LIKE');
				
				if (strlen($kodero)==5) {
					$query->innerJoin('obyek', 'ro', 'left(a.kodero,5)=ro.kodeo');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==3) {
					$query->innerJoin('jenis', 'ro', 'left(a.kodero,3)=ro.kodej');
					$query->fields('ro', array('uraian'));
					
				} else if (strlen($kodero)==2) {	
					$query->innerJoin('kelompok', 'ro', 'left(a.kodero,2)=ro.kodek');
					$query->fields('ro', array('uraian'));
				
				} else {
					$query->innerJoin('anggaran', 'ro', 'left(a.kodero,1)=ro.kodea');
					$query->fields('ro', array('uraian'));
					
				}
					
			}			
			$query->addExpression('SUM(a.anggaran)', 'anggaran');
			if ($kodeuk !='ZZ') $query->condition('a.kodeuk', $kodeuk, '=');
			//drupal_set_message($query);		
			# execute the query
			$results = $query->execute();
			foreach ($results as $data) {
				$rekening= $kodero . ' - ' . $data->uraian;
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
	
	$form['formtanggal'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tanggal Buku Besar<em><small class="text-info pull-right">Klik disini untuk mengubah tanggal</small></em>',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$tglawal_form = strtotime($tglawal);
	$form['formtanggal']['tglawal'] = array(
		'#type' => 'date',
		'#title' =>  t('Periode laporan, mulai tanggal'),
		'#default_value'=> array(
			'year' => format_date($tglawal_form, 'custom', 'Y'),
			'month' => format_date($tglawal_form, 'custom', 'n'), 
			'day' => format_date($tglawal_form, 'custom', 'j'), 
		  ), 		
	);	
	$tglakhir_form = strtotime($tglakhir);
	$form['formtanggal']['tglakhir'] = array(
		'#type' => 'date',
		'#title' =>  t('Sampai tanggal'),
		'#default_value'=> array(
			'year' => format_date($tglakhir_form, 'custom', 'Y'),
			'month' => format_date($tglakhir_form, 'custom', 'n'), 
			'day' => format_date($tglakhir_form, 'custom', 'j'), 
		  ), 		
	);		
	$form['formtanggal']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-play" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	
	return $form;
}

function akuntansi_buku_main_form_submit($form, &$form_state) {
	//akuntansi/buku/kodeo/41201006/kodej
	$kodekeg = $form_state['values']['kodekeg'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodero = $form_state['values']['kodero'];
	$koderod = $form_state['values']['koderod'];

	$tglawal = $form_state['values']['tglawal'];
	$tglawalx = apbd_date_convert_form2db($tglawal);
	
	$tglakhir = $form_state['values']['tglakhir'];
	$tglakhirx = apbd_date_convert_form2db($tglakhir);		

	$uri = '/akuntansi/buku/' . $kodekeg . '/'  . $kodero . '/'  . $kodeuk . '/' . $tglawalx  . '/' . $tglakhirx . '/' . $koderod;
	
	drupal_goto($uri);

}
?>
