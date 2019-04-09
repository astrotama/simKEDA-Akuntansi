<?php
function belanja_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';

	$kodeuk = 'ZZ';
	$keyword = '';
	$namasingkat = 'SELURUH SKPD';
	$sumberdana ='SEMUA';
	$jenis ='SEMUA';
	$bulan = date('m');
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;

			case 'filter':
				$bulan = arg(2);
				$kodeuk = arg(3);
				$jenis = arg(4);
				$sumberdana = arg(5);
				$keyword = arg(6);
				break;

			case 'simple':
				  $pdf=apbd_pdfd7('ce','L','cek','Pemerintah Kabupaten Jepara '.$bulan);
				  header('Content-Type: application/pdf');
				  header('Content-Length: ' . strlen($pdf));
				  header('Content-Disposition: attachment; filename="mydocument.pdf"');
				  print $pdf;
				  return NULL;
	  
				 /* $pdf = apbd_pdfd7('cek','L','head','foot');	
				  header('Content-Type: application/pdf');
				  header('Content-Length: ' . strlen($pdf));
				  header('Content-Disposition: attachment; filename="mydocument.pdf"');
				  print $pdf;
			  return NULL;*/
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["belanja_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["belanja_bulan"];
		if ($bulan=='') $bulan = '1';
		
		
		$dokumen = $_SESSION["belanja_dokumen"];
		if ($dokumen=='') $dokumen = 'ZZ';
		/*
		$jenisgaji = $_SESSION["sp2d_gaji_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';
		*/
	}
	
	
	//drupal_set_title('BELANJA');
	
	$output_form = drupal_get_form('belanja_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD','field'=> 'namaskpd', 'valign'=>'top'),
		array('data' => 'Kegiatan','field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => 'Sumberdana', 'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
		array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasix', 'valign'=>'top'),
		array('data' => 'Persen', 'width' => '10px', 'field'=> 'persen', 'valign'=>'top'),
		array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
	
	$query = db_select('apbdrekap', 'k')->extend('PagerDefault')->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('namaskpd', 'kodekeg', 'kegiatan', 'sumberdana'));
	$query->addExpression('SUM(k.anggaran1/1000)', 'anggaran1x');
	$query->addExpression('SUM(k.anggaran2/1000)', 'anggaran2x');
	$query->addExpression('SUM(k.realisasikum' . $bulan . '/1000)', 'realisasix');
	$query->addExpression('SUM(k.realisasikum' . $bulan . ')/SUM(k.anggaran2)', 'persen');
	$query->condition('k.kodeakun', '5', '=');
	if ($kodeuk !='ZZ') $query->condition('k.kodeskpd', $kodeuk, '=');
	
	if ($jenis=='GAJI') {
		$query->condition('k.kodekelompok', '51', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.kodekelompok', '51', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.kodekelompok', '52', '=');	
	} else if ($jenis=='TIDAK LANGSUNG') {
		$query->condition('k.kodekelompok', '51', '=');	
	}						 

	if ($sumberdana !='SEMUA') {
		$query->condition('k.Sumberdana', $sumberdana, '=');
	}
	
	if ($keyword !='') {
		if (substr($keyword,0,1) =='U')
			$query->condition('k.kodeurusan', substr($keyword,1,3), '=');
		else if (substr($keyword,0,1) =='F')
			$query->condition('k.kodefungsi', substr($keyword,1,2), '=');
		else
			$query->condition('k.namarincian', '%' . db_like($keyword) . '%', 'LIKE');
	}
	//$query->groupBy('namaskpd', 'koderincian','namarincian');
	$query->groupBy('namaskpd');
	$query->groupBy('kodekeg');
	$query->groupBy('kegiatan');
	$query->groupBy('sumberdana');
	
	//$query->groupBy('namarincian');
	$query->orderByHeader($header);
	$query->orderBy('k.namaskpd', 'ASC');
	$query->orderBy('k.kegiatan', 'ASC');
	$query->limit($limit);	

	//dpq($query);	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

	
		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$editlink = apbd_button_kegiatan('belanja/rekening/'.$bulan.'/'.$data->kodekeg);
		$editlink .= apbd_button_register('penata/register/'.$bulan.'/'.$data->kodekeg);
		//<font color="red">This is some text!</font>
		
		$anggaran = apbd_fn($data->anggaran2x);
		
		$kegiatan = l($data->kegiatan, 'belanja/rekening/'.$bulan.'/'.$data->kodekeg, array ('html' => true));
		
		if ($data->anggaran1x > $data->anggaran2x)
			$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1x) . '</font></p>';
		else if ($data->anggaran1x < $data->anggaran2x)
			$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1x) . '</font></p>';
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right','width'=>'40px', 'valign'=>'top'),
						array('data' => $data->namaskpd, 'align' => 'left', 'valign'=>'top'),
						array('data' => $kegiatan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
						array('data' => $anggaran,  'align' => 'right','width'=>'90px', 'valign'=>'top'),
						array('data' => apbd_fn($data->realisasix),'width'=>'90px', 'align' => 'right', 'valign'=>'top'),
						//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)),'width'=>'50px', 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1($data->persen*100),'width'=>'50px', 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran2x - $data->realisasix),'width'=>'90px', 'align' => 'right', 'valign'=>'top'),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						$editlink,
					);
	}

	//BUTTON
	//$btn = apbd_button_print('/belanja/filter/'.arg(2).'/'.arg(3).'/'.arg(4).'/'.arg(5).'/'.arg(6).'/pdf');
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	//$btn .= apbd_button_chart('belanja/chart/' . $bulan.'/'.$kodeuk . '/SEMUA/SEMUA/jenis_ab');
	
	$btn = '';
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
if(arg(7)=='pdf'){
		
		 
		 $output=getform($bulan,$kodeuk,$sumberdana,$jenis);
		 print_pdf_l ($output);
		 
		 /*
		 $pdf=apbd_pdfd7($output,'L','cek','Pemerintah Kabupaten Jepara '.$bulan);
		 $title = 'abc.pdf';
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="'.$title.'"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.filesize($pdf));
			header('Accept-Ranges: bytes');
			header('Expires: 0');
			header('Cache-Control: public, must-revalidate, max-age=0');  
		  
		  print $pdf;
		  return NULL;
		  */
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}
function getform($bulan,$kodeuk,$sumberdana,$jenis){
	$query = db_select('kegiatan' . $bulan, 'k');
		$query->innerJoin('unitkerja' . $bulan, 'u', 'k.kodeuk=u.kodeuk');

		# get the desired fields from the database
		$query->fields('k', array('kegiatan', 'kodeuk','kodekeg','sumberdana', 'keluaransasaran', 'keluarantarget', 'anggaran1', 'anggaran2','realisasi'));
		$query->fields('u', array('namasingkat'));
		if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
		if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');

		if ($jenis=='GAJI') {
			$query->condition('k.jenis', '1', '=');	
			$query->condition('k.isppkd', '0', '=');	
		} else if ($jenis=='PPKD') {
			$query->condition('k.jenis', '1', '=');	
			$query->condition('k.isppkd', '1', '=');	
		} else if ($jenis=='LANGSUNG') {
			$query->condition('k.jenis', '2', '=');	
		}
		
		
		$query->orderBy('k.kegiatan', 'ASC');
		//$query->limit($limit);
			
		# execute the query
		$results = $query->execute();
		$header = array (
			array('data' => 'No','height'=>'20px', 'align'=>'center','width' => '30px', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'SKPD','width' => '100px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Kegiatan','width' => '150px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Sumberdana','width' => '100px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Sasaran/Target','width' => '275px', 'align'=>'center',  'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Anggaran', 'width' => '100px', 'align'=>'center',  'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Realisasi', 'width' => '100px', 'align'=>'center',  'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Prsn', 'width' => '50px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			
		);
		$no=0;$rows = array();
		foreach ($results as $data) {
		$no++;
		$anggaran = apbd_fn($data->anggaran2);
		
		/*if ($data->anggaran1 > $data->anggaran2)
			$anggaran .= '<p><font>' . apbd_fn($data->anggaran1) . '</font></p>';
		else if ($data->anggaran1 < $data->anggaran2)
			$anggaran .= '<p><font>' . apbd_fn($data->anggaran1) . '</font></p>';*/
		
		$rows[] = array(
						array('data' => $no, 'align' => 'center','width'=>'30px', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->namasingkat, 'align' => 'left','width'=>'100px', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->kegiatan, 'align' => 'left','width'=>'150px', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->sumberdana, 'align' => 'left','width'=>'100px', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->keluaransasaran . '<p><i><font>' . $data->keluarantarget . '</font></i></p>', 'width' => '275px', 'align' => 'left', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $anggaran,  'align' => 'right','width'=>'100px', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_fn($data->realisasi),'width'=>'100px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2, $data->realisasi)),'width'=>'50px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
			}
		$rows[] = array(
						array('data' => '', 'align' => 'right','width'=>'905px', 'valign'=>'top','style'=>'border-top:1px solid black'),
						
						
					);	
		 
		 //$output=null;
		 $output=theme('table', array('header' => $header, 'rows' => $rows ));
		 return $output;
}

function belanja_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$jenis= $form_state['values']['jenis'];
	$sumberdana = $form_state['values']['sumberdana'];
	$keyword = $form_state['values']['keyword'];

	$_SESSION["belanja_kodeuk"] = $kodeuk;
	$_SESSION["belanja_bulan"] = $bulan;
	$_SESSION["belanja_dokumen"] = $sp2dok;
	
	$uri = 'belanja/filter/' . $bulan . '/'.$kodeuk . '/' . $jenis . '/' . $sumberdana . '/' . $keyword;// . '/' . $showchart;
	drupal_goto($uri);
	
}


function belanja_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$namasingkat = '|SELURUH SKPD';
	$sumberdana ='SEMUA';
	$jenis ='SEMUA';
	$bulan = date('m');
	$keyword = '';
	//$showchart = 'nochart';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$jenis = arg(4);
		$sumberdana = arg(5);
		$keyword = arg(6);
	}
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
	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["belanja_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["belanja_bulan"];
		if ($bulan=='') $bulan = '1';
		
		
		$dokumen = $_SESSION["belanja_dokumen"];
		if ($dokumen=='') $dokumen = 'ZZ';
		/*
		$jenisgaji = $_SESSION["sp2d_gaji_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';
		*/
	}
	
	if ($sumberdana=='SEMUA')
		$sumberdana_label ='|SEMUA SUMBERDANA';
	else
		$sumberdana_label ='|' . $sumberdana;
	
	if ($jenis=='SEMUA')
		$jenis_label ='|SEMUA JENIS BELANJA';
	else
		$jenis_label ='|' . $jenis;
	

	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . $namasingkat . $jenis_label . $sumberdana_label . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
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
	
	//SKPD
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
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $kodeuk,
	);

	$form['formdata']['jenis']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Jenis Belanja'), 
		'#default_value' => $jenis,
		
		'#options' => array(	
			 'SEMUA' => t('SEMUA'), 	
			 'GAJI' => t('GAJI'), 	
			 'LANGSUNG' => t('LANGSUNG'),
			 'PPKD' => t('PPKD'),	
		   ),
	);		

	$opt_sumberdana['SEMUA'] ='SEMUA';
	$query = db_select('sumberdanalt', 's');
	$query->fields('s', array('nomor','sumberdana'));
	$query->orderBy('nomor', 'ASC');;
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$opt_sumberdana[$data->sumberdana] = $data->sumberdana;
		}
	}	

	
	$form['formdata']['sumberdana'] = array(
		'#type' => 'select',
		'#title' =>  t('Sumber Dana'),
		'#options' => $opt_sumberdana,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $sumberdana,
	);

	$form['formdata']['keyword'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kata Kunci'),
		'#description' =>  t('Kata kunci untuk mencari kegiatan, diisi dengan menuliskan nama kegiatan'),
		'#default_value' => $keyword, 
	);	
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	
	return $form;
}

function belanja_jenis_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_sp2d_511 = array();
$arr_sp2d_513 = array();
$arr_sp2d_514 = array();
$arr_sp2d_515 = array();
$arr_sp2d_516 = array();
$arr_sp2d_517 = array();
$arr_sp2d_518 = array();
$arr_sp2d_521 = array();
$arr_sp2d_522 = array();
$arr_sp2d_523 = array();

if ($showchart=='jenis_ab') {
	$arr_agg_511 = array();
	$arr_agg_513 = array();
	$arr_agg_514 = array();
	$arr_agg_515 = array();
	$arr_agg_516 = array();
	$arr_agg_517 = array();
	$arr_agg_518 = array();
	$arr_agg_521 = array();
	$arr_agg_522 = array();
	$arr_agg_523 = array();

	$query = db_select('kegiatan' . $bulan, 'k');
	$query->innerJoin('kegiatanrekening' . $bulan, 'ki', 'k.kodekeg=ki.kodekeg');
	$query->addExpression('left(ki.kodero,3)', 'kodej');
	$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}

	$query->groupBy('left(ki.kodero,3)');
	$results = $query->execute();
	foreach ($results as $datas) {
		//print '<p>' . $datas->kodej . ' / ' . $datas->anggaran . '</p>';
		switch ($datas->kodej) {
			case "511":
				$arr_agg_511 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "513":
				$arr_agg_513 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "514":
				$arr_agg_514 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "515":
				$arr_agg_515 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "516":
				$arr_agg_516 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "517":
				$arr_agg_517 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "518":
				$arr_agg_518 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;

			case "521":
				$arr_agg_521 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "522":
				$arr_agg_522 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "523":
				$arr_agg_523 = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
		}	
	}	//foreach ($results as $datas)

}	//'jenis_ab'
	
$arr_sp2d_511 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_513 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_514 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_515 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_516 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_517 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_518 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

$arr_sp2d_521 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_522 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_523 = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
					 
//REALISASI SP2D
$i=0;
$ada511 = false; $ada513 = false; $ada514 = false;
$ada515 = false; $ada516 = false; $ada517 = false; $ada518 = false;
$ada521 = false; $ada522 = false; $ada523 = false;

for ($bulan=1; $bulan<=12; $bulan++){
	$query = db_select('dokumen' . $bulan, 'd');
	$query->innerJoin('dokumenrekening' . $bulan, 'di', 'd.dokid=di.dokid');
	$query->innerJoin('kegiatan' . $bulan, 'k', 'd.kodekeg=k.kodekeg');
	$query->addExpression('left(di.kodero,3)', 'kodej');
	$query->addExpression('SUM(di.jumlah/1000)', 'jmlsp2d');

	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}
	
	$query->condition('d.bulan', $bulan, '<=');
	$query->groupBy('left(di.kodero,3)');
	
	# execute the query
	$results = $query->execute();

	foreach ($results as $datas) {

		switch ($datas->kodej) {	
			case "511":
				$ada511 = true;
				$arr_sp2d_511[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "513":
				$ada513 = true;
				$arr_sp2d_513[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "514":
				$ada514 = true;
				$arr_sp2d_514[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "515":
				$ada515 = true;
				$arr_sp2d_515[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "516":
				$ada516 = true;
				$arr_sp2d_516[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "517":
				$ada517 = true;
				$arr_sp2d_517[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "518":
				$ada518 = true;
				$arr_sp2d_518[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;

			case "521":
				$ada521 = true;
				$arr_sp2d_521[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "522":
				$ada522 = true;
				$arr_sp2d_522[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "523":
				$ada523 = true;
				$arr_sp2d_523[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
		
		}
	}	

	$i++;
}	


$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Belanja'),
);
if ($ada511) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran511'] = array(
			'#type' => 'chart_data',
			'#title' => t('Gaji (A)'),
			'#data' => $arr_agg_511,
		);
	}
	$chart['sp2d511'] = array(
		'#type' => 'chart_data',
		'#title' => t('Gaji (B)'),
		'#data' => $arr_sp2d_511,
	);
}

if ($ada513) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran513'] = array(
			'#type' => 'chart_data',
			'#title' => t('Subsidi (A)'),
			'#data' => $arr_agg_513,
		);
	}
	$chart['sp2d513'] = array(
		'#type' => 'chart_data',
		'#title' => t('Subsidi (B)'),
		'#data' => $arr_sp2d_513,
	);
}	
if ($ada514) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran514'] = array(
			'#type' => 'chart_data',
			'#title' => t('Hibah (A)'),
			'#data' => $arr_agg_514,
		);
	}
	$chart['sp2d514'] = array(
		'#type' => 'chart_data',
		'#title' => t('Hibah (B)'),
		'#data' => $arr_sp2d_514,
	);
}	
if ($ada515) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran515'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bansos (A)'),
			'#data' => $arr_agg_515,
		);
	}
	$chart['sp2d515'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bansos (B)'),
		'#data' => $arr_sp2d_515,
	);
}	
if ($ada516) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran516'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bagsil (A)'),
			'#data' => $arr_agg_516,
		);
	}
	$chart['sp2d516'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bagsil (B)'),
		'#data' => $arr_sp2d_516,
	);
}	
if ($ada517) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran517'] = array(
			'#type' => 'chart_data',
			'#title' => t('Bankeu (A)'),
			'#data' => $arr_agg_517,
		);
	}
	$chart['sp2d517'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bankeu (B)'),
		'#data' => $arr_sp2d_517,
	);
}	
if ($ada518) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran518'] = array(
			'#type' => 'chart_data',
			'#title' => t('BTT (A)'),
			'#data' => $arr_agg_518,
		);
	}
	$chart['sp2d518'] = array(
		'#type' => 'chart_data',
		'#title' => t('BTT (B)'),
		'#data' => $arr_sp2d_518,
	);
}	

if ($ada521) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran521'] = array(
			'#type' => 'chart_data',
			'#title' => t('Pegawai (A)'),
			'#data' => $arr_agg_521,
		);
	}
	$chart['sp2d521'] = array(
		'#type' => 'chart_data',
		'#title' => t('Pegawai (B)'),
		'#data' => $arr_sp2d_521,
	);
}	
if ($ada522) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran522'] = array(
			'#type' => 'chart_data',
			'#title' => t('B&J (A)'),
			'#data' => $arr_agg_522,
		);
	}
	$chart['sp2d522'] = array(
		'#type' => 'chart_data',
		'#title' => t('B&J (B)'),
		'#data' => $arr_sp2d_522,
	);
}	
if ($ada523) {
	if ($showchart=='jenis_ab') {
		$chart['anggaran523'] = array(
			'#type' => 'chart_data',
			'#title' => t('Modal (A)'),
			'#data' => $arr_agg_523,
		);
	}
	$chart['sp2d523'] = array(
		'#type' => 'chart_data',
		'#title' => t('Modal (B)'),
		'#data' => $arr_sp2d_523,
	);
}	


$chart_register['realisasi_sp2d_skpd_jenis'] = $chart;

return drupal_render($chart_register);
	
}

function belanja_total_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_sp2d = array();

if ($showchart=='total_ab') {
	$arr_agg = array();


	$query = db_select('kegiatan' . $bulan, 'k');
	$query->innerJoin('kegiatanrekening' . $bulan, 'ki', 'k.kodekeg=ki.kodekeg');
	$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}

	$results = $query->execute();
	foreach ($results as $datas) {
		$arr_agg = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
					array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
					array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
					array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
					array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
					array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));

	}	//foreach ($results as $datas)

}	//'total_ab'
	
$arr_sp2d = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
			array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
					 
//REALISASI SP2D
$i=0;

for ($bulan=1; $bulan<=12; $bulan++){
	$query = db_select('dokumen' . $bulan, 'd');
	$query->innerJoin('dokumenrekening' . $bulan, 'di', 'd.dokid=di.dokid');
	$query->innerJoin('kegiatan' . $bulan, 'k', 'd.kodekeg=k.kodekeg');
	$query->addExpression('SUM(di.jumlah/1000)', 'jmlsp2d');

	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}
	
	$query->condition('d.bulan', $bulan, '<=');

	# execute the query
	$results = $query->execute();

	foreach ($results as $datas) {

		$arr_sp2d[$i] = array((int)$bulan, (real)$datas->jmlsp2d);

	}	

	$i++;
}	


$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realisasi Belanja'),
);
if ($showchart=='total_ab') {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		'#data' => $arr_agg,
	);
}
$chart['sp2d'] = array(
	'#type' => 'chart_data',
	'#title' => t('Belanja'),
	'#data' => $arr_sp2d,
);


$chart_register['realisasi_sp2d_skpd_total'] = $chart;

return drupal_render($chart_register);
	
}

function belanja_sumberdana_chart($bulan, $kodeuk, $sumberdana, $jenis, $showchart) {
//$bulan = 2015;		//arg(2);
//$kodeuk = '03';		//arg(3);

$arr_sp2d_DAU = array();
$arr_sp2d_DAK = array();
$arr_sp2d_BANPROV = array();
$arr_sp2d_DBH = array();
$arr_sp2d_DBHCHT = array();
$arr_sp2d_LLP = array();
$arr_sp2d_PD = array();
$arr_sp2d_BK = array();
$arr_sp2d_BLUD = array();

if ($showchart=='sumberdana_ab') {
	$arr_agg_DAU = array();
	$arr_agg_DAK = array();
	$arr_agg_BANPROV = array();
	$arr_agg_DBH = array();
	$arr_agg_DBHCHT = array();
	$arr_agg_LLP = array();
	$arr_agg_PD = array();
	$arr_agg_BK = array();
	$arr_agg_BLUD = array();
	$arr_agg_523 = array();

	$query = db_select('kegiatan' . $bulan, 'k');
	$query->innerJoin('kegiatanrekening' . $bulan, 'ki', 'k.kodekeg=ki.kodekeg');
	$query->fields('k', array('sumberdana'));
	$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}

	$query->groupBy('k.sumberdana');
	$results = $query->execute();
	foreach ($results as $datas) {
		//print '<p>' . $datas->kodej . ' / ' . $datas->anggaran . '</p>';
		switch ($datas->sumberdana) {
			case "DAU":
				$arr_agg_DAU = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "DAK":
				$arr_agg_DAK = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "BANPROV":
				$arr_agg_BANPROV = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "DBH":
				$arr_agg_DBH = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "DBH CHT":
				$arr_agg_DBHCHT = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "LAIN-LAIN PENDAPATAN":
				$arr_agg_LLP = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "PINJAMAN DAERAH":
				$arr_agg_PD = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;

			case "BANTUAN KHUSUS":
				$arr_agg_BK = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;
			case "BLUD":
				$arr_agg_BLUD = array(array(1, (real)$datas->anggaran), array(2, (real)$datas->anggaran),
									 array(3, (real)$datas->anggaran), array(4, (real)$datas->anggaran),			
									 array(5, (real)$datas->anggaran), array(6, (real)$datas->anggaran),
									 array(6, (real)$datas->anggaran), array(8, (real)$datas->anggaran),
									 array(9, (real)$datas->anggaran), array(10, (real)$datas->anggaran),
									 array(11, (real)$datas->anggaran), array(12, (real)$datas->anggaran));
				break;

		}	
	}	//foreach ($results as $datas)

}	//'sumberdana_ab'
	
$arr_sp2d_DAU = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_DAK = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_BANPROV = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_DBH = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_DBHCHT = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_LLP = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_PD = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));

$arr_sp2d_BK = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
$arr_sp2d_BLUD = array(array(1, 0), array(2, 0), array(3, 0), array(4, 0), array(5, 0), array(6, 0),
					 array(6, 0), array(8, 0), array(9, 0), array(10, 0), array(11, 0), array(12, 0));
					 
//REALISASI SP2D
$i=0;
$adaDAU = false; $adaDAK = false; $adaBANPROV = false;
$adaDBH = false; $adaDBHCHT = false; $adaLLP = false; $adaPD = false;
$adaBK = false; $adaBLUD = false;

for ($bulan=1; $bulan<=12; $bulan++){
	$query = db_select('dokumen' . $bulan, 'd');
	$query->innerJoin('dokumenrekening' . $bulan, 'di', 'd.dokid=di.dokid');
	$query->innerJoin('kegiatan' . $bulan, 'k', 'd.kodekeg=k.kodekeg');
	$query->fields('k', array('sumberdana'));
	$query->addExpression('SUM(di.jumlah/1000)', 'jmlsp2d');

	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}
	
	$query->condition('d.bulan', $bulan, '<=');
	$query->groupBy('k.sumberdana');
	
	# execute the query
	$results = $query->execute();

	foreach ($results as $datas) {

		switch ($datas->sumberdana) {	
			case "DAU":
				$adaDAU = true;
				$arr_sp2d_DAU[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "DAK":
				$adaDAK = true;
				$arr_sp2d_DAK[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "BANPROV":
				$adaBANPROV = true;
				$arr_sp2d_BANPROV[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "DBH":
				$adaDBH = true;
				$arr_sp2d_DBH[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "DBH CHT":
				$adaDBHCHT = true;
				$arr_sp2d_DBHCHT[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "LAIN-LAIN PENDAPATAN":
				$adaLLP = true;
				$arr_sp2d_LLP[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "PINJAMAN DAERAH":
				$adaPD = true;
				$arr_sp2d_PD[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;

			case "BANTUAN KHUSUS":
				$adaBK = true;
				$arr_sp2d_BK[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;
			case "BLUD":
				$adaBLUD = true;
				$arr_sp2d_BLUD[$i] = array((int)$bulan, (real)$datas->jmlsp2d);
				break;

		
		}
	}	

	$i++;
}	


$chart = array(
    '#type' => 'chart',
    '#chart_type' => 'line',
    '#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
    '#title' => t('Realiasi Belanja Berdasarkan Sumber Dana'),
);
if ($adaDAU) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDAU'] = array(
			'#type' => 'chart_data',
			'#title' => t('DAU (A)'),
			'#data' => $arr_agg_DAU,
		);
	}
	$chart['sp2dDAU'] = array(
		'#type' => 'chart_data',
		'#title' => t('DAU (B)'),
		'#data' => $arr_sp2d_DAU,
	);
}

if ($adaDAK) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDAK'] = array(
			'#type' => 'chart_data',
			'#title' => t('DAK (A)'),
			'#data' => $arr_agg_DAK,
		);
	}
	$chart['sp2dDAK'] = array(
		'#type' => 'chart_data',
		'#title' => t('DAK (B)'),
		'#data' => $arr_sp2d_DAK,
	);
}	
if ($adaBANPROV) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranBANPROV'] = array(
			'#type' => 'chart_data',
			'#title' => t('BANPROV (A)'),
			'#data' => $arr_agg_BANPROV,
		);
	}
	$chart['sp2dBANPROV'] = array(
		'#type' => 'chart_data',
		'#title' => t('BANPROV (B)'),
		'#data' => $arr_sp2d_BANPROV,
	);
}	
if ($adaDBH) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDBH'] = array(
			'#type' => 'chart_data',
			'#title' => t('DBH (A)'),
			'#data' => $arr_agg_DBH,
		);
	}
	$chart['sp2dDBH'] = array(
		'#type' => 'chart_data',
		'#title' => t('DBH (B)'),
		'#data' => $arr_sp2d_DBH,
	);
}	
if ($adaDBHCHT) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranDBHCHT'] = array(
			'#type' => 'chart_data',
			'#title' => t('DBHCHT (A)'),
			'#data' => $arr_agg_DBHCHT,
		);
	}
	$chart['sp2dDBHCHT'] = array(
		'#type' => 'chart_data',
		'#title' => t('DBHCHT (B)'),
		'#data' => $arr_sp2d_DBHCHT,
	);
}	
if ($adaLLP) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranLLP'] = array(
			'#type' => 'chart_data',
			'#title' => t('LLP (A)'),
			'#data' => $arr_agg_LLP,
		);
	}
	$chart['sp2dLLP'] = array(
		'#type' => 'chart_data',
		'#title' => t('LLP (B)'),
		'#data' => $arr_sp2d_LLP,
	);
}	
if ($adaPD) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranPD'] = array(
			'#type' => 'chart_data',
			'#title' => t('PD (A)'),
			'#data' => $arr_agg_PD,
		);
	}
	$chart['sp2dPD'] = array(
		'#type' => 'chart_data',
		'#title' => t('PD (B)'),
		'#data' => $arr_sp2d_PD,
	);
}	

if ($adaBK) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranBK'] = array(
			'#type' => 'chart_data',
			'#title' => t('BK (A)'),
			'#data' => $arr_agg_BK,
		);
	}
	$chart['sp2dBK'] = array(
		'#type' => 'chart_data',
		'#title' => t('BK (B)'),
		'#data' => $arr_sp2d_BK,
	);
}	
if ($adaBLUD) {
	if ($showchart=='sumberdana_ab') {
		$chart['anggaranBLUD'] = array(
			'#type' => 'chart_data',
			'#title' => t('BLUD (A)'),
			'#data' => $arr_agg_BLUD,
		);
	}
	$chart['sp2dBLUD'] = array(
		'#type' => 'chart_data',
		'#title' => t('BLUD (B)'),
		'#data' => $arr_sp2d_BLUD,
	);
}	

$chart_register['realisasi_sp2d_skpd_sumberdana'] = $chart;

return drupal_render($chart_register);
	
}

function belanja_sumberdana_chart_final($bulan, $kodeuk, $jenis, $inpersen) {
$arr_sumberdana_sumber = array('DAU', 'DAK', 'BANPROV', 'DBH', 'DBH CHT', 'LAIN-LAIN PENDAPATAN', 'PINJAMAN DAERAH', 'BANTUAN KHUSUS', 'BLUD');

$arr_sumberdana = array();
$arr_anggaran = array();
$arr_realisasi = array();
$sejuta = 1000000;

$x = -1;
for ($i=0; $i<=8; $i++){
	
	$ada = false;
	
	//ANGGARAN
	$query = db_select('kegiatan' . $bulan, 'k');
	$query->innerJoin('kegiatanrekening' . $bulan, 'ki', 'k.kodekeg=ki.kodekeg');
	$query->fields('k', array('sumberdana'));
	$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	$query->condition('k.sumberdana', $arr_sumberdana_sumber[$i], '=');
	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}
	$query->groupBy('k.sumberdana');
	$results = $query->execute();
	foreach ($results as $datas) {	
		if ($datas->anggaran>0) {
			$x++;
			
			$ada = true;
			
			$arr_sumberdana[$x] = $arr_sumberdana_sumber[$i];
			$arr_anggaran[$x]= round($datas->anggaran/$sejuta,2);
			$arr_realisasi[$x]= 0;
		}
	}	
	
	//REALISASI
	$query = db_select('dokumen' . $bulan, 'd');
	$query->innerJoin('dokumenrekening' . $bulan, 'di', 'd.dokid=di.dokid');
	$query->innerJoin('kegiatan' . $bulan, 'k', 'd.kodekeg=k.kodekeg');
	$query->fields('k', array('sumberdana'));
	$query->addExpression('SUM(di.jumlah/1000)', 'jmlsp2d');

	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	$query->condition('k.sumberdana', $arr_sumberdana_sumber[$i], '=');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}	
	$query->groupBy('k.sumberdana');
	
	# execute the query
	$results = $query->execute();
	foreach ($results as $datas) {	
		if ($ada) {
			$arr_realisasi[$x]= round($datas->jmlsp2d/$sejuta,2);		
		}
	}	
	
}


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Anggaran berdasarkan Sumberdana Tahun ' . $bulan),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	for ($i=0; $i<=$x; $i++){
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_realisasi[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_sumberdana,
);

$apbdbelanja['belanja_sumberdana_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}

function belanja_jenis_chart_final($bulan, $kodeuk, $sumberdana, $jenis, $inpersen) {
	
$arr_jenis_rek = array(array('511', 'Gaji'), array('513', 'Subsidi'), array('514', 'Hibah'), 
					   array('515', 'Bansos'), array('516', 'Bagsil'), array('517', 'Bankeu'), 
					   array('518', 'TTdg'), array('521', 'Pegawai'), array('522', 'B&J'), 
					   array('523', 'Modal'));	
	
$arr_jenis = array();
$arr_anggaran = array();
$arr_realisasi = array();
$sejuta = 1000000;

$x = -1;
for ($i=0; $i<=9; $i++){
	 
	$ada = false;
	
	//ANGGARAN
	$query = db_select('kegiatan' . $bulan, 'k');
	$query->innerJoin('kegiatanrekening' . $bulan, 'ki', 'k.kodekeg=ki.kodekeg');
	//$query->addExpression('left(ki.kodero,3)', 'kodej');
	$query->addExpression('SUM(ki.anggaran2/1000)', 'anggaran');
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');
	//$query->condition('ki.kodero', $arr_jenis[$i], 'LIKE');
	$query->condition('ki.kodero', db_like($arr_jenis_rek[$i][0]) . '%', 'LIKE');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}
	
	//drupal_set_message($query);
	
	$results = $query->execute();
	foreach ($results as $datas) {	
		if ((real)$datas->anggaran>0) {
			$x = $x+1;
			$ada = true;
			
			//drupal_set_message($arr_jenis_rek[$i][0]);	
			//drupal_set_message('a ' . $x);
			$arr_jenis[$x] = $arr_jenis_rek[$i][1];		//LABEL
			
			$arr_anggaran[$x]= round($datas->anggaran/$sejuta,2);	//ANGGARAN
			$arr_realisasi[$x]=0;									//REALISASI DEFAULT
		}
	}	
	
	//REALISASI
	$query = db_select('dokumen' . $bulan, 'd');
	$query->innerJoin('dokumenrekening' . $bulan, 'di', 'd.dokid=di.dokid');
	$query->innerJoin('kegiatan' . $bulan, 'k', 'd.kodekeg=k.kodekeg');
	$query->addExpression('SUM(di.jumlah/1000)', 'jmlsp2d');

	if ($kodeuk !='ZZ') $query->condition('d.kodeuk', $kodeuk, '=');
	if ($sumberdana!='SEMUA') $query->condition('k.sumberdana', $sumberdana, '=');
	//$query->condition('left(ki.kodero,3)', $arr_jenis[$i], '=');
	$query->condition('di.kodero', db_like($arr_jenis_rek[$i][0]) . '%', 'LIKE');

	if ($jenis=='GAJI') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '0', '=');	
	} else if ($jenis=='PPKD') {
		$query->condition('k.jenis', '1', '=');	
		$query->condition('k.isppkd', '1', '=');	
	} else if ($jenis=='LANGSUNG') {
		$query->condition('k.jenis', '2', '=');	
	}	
	
	//drupal_set_message($query);
	# execute the query
	$results = $query->execute();
	foreach ($results as $datas) {	
		if ($ada) {
			//drupal_set_message(db_like($arr_jenis_rek[$i][0]) . ' ok');	
			//drupal_set_message('r ' . $x);
			$arr_realisasi[$x]= round($datas->jmlsp2d/$sejuta,2);		
		}
	}	
	
}


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Anggaran berdasarkan Sumberdana Tahun ' . $bulan),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	for ($i=0; $i<=$x; $i++){
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_realisasi[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_jenis,
);

$apbdbelanja['belanja_jenis_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}

function belanja_fungsi_chart_final($bulan, $kelompok, $inpersen) {

$arr_fungsi = array();
$arr_anggaran = array();
$arr_realisasi = array();

$query = db_select('apbdrekap' . $bulan, 'r');
$query->fields('r', array('kodefungsi','namafungsi'));
$query->addExpression('SUM(r.anggaran2/1000000)', 'jmlanggaran');
$query->addExpression('SUM(r.realisasi/1000000)', 'jmlrealisasi');
$query->condition('r.kodeakun', '5', '=');
if ($kelompok !='ZZ') $query->condition('r.kodekelompok', $kelompok, '=');
$query->groupBy('kodefungsi','namafungsi');

drupal_set_message($query);

$results = $query->execute();
foreach ($results as $datas) {	
	
	$arr_fungsi[] = $datas->namafungsi;
	$arr_anggaran[]= (real)$datas->jmlanggaran;
	$arr_realisasi[]= (real)$datas->jmlrealisasi;
	
}	


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Anggaran berdasarkan Fungsi Tahun ' . $bulan),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	$x = count($arr_anggaran);
	for ($i=0; $i<=$x; $i++){
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_realisasi[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_fungsi,
);

$apbdbelanja['belanja_fungsi_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}

function belanja_urusan_chart_final($bulan, $kelompok, $inpersen) {

$arr_urusan = array();
$arr_anggaran = array();
$arr_realisasi = array();

$query = db_select('apbdrekap' . $bulan, 'r');
$query->fields('r', array('kodeurusan','urusansingkat'));
$query->addExpression('SUM(r.anggaran2/1000000)', 'jmlanggaran');
$query->addExpression('SUM(r.realisasi/1000000)', 'jmlrealisasi');
$query->condition('r.kodeakun', '5', '=');
if ($kelompok !='ZZ') $query->condition('r.kodekelompok', $kelompok, '=');
$query->groupBy('kodeurusan','urusansingkat');

//drupal_set_message('x' . $query);

$results = $query->execute();
foreach ($results as $datas) {	
	
	$arr_urusan[] = $datas->urusansingkat;
	$arr_anggaran[]= (real)$datas->jmlanggaran;
	$arr_realisasi[]= (real)$datas->jmlrealisasi;
	
}	


$chart = array(
	'#type' => 'chart',
	'#chart_type' => 'column',
	'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
	'#title' => t('Realisasi Anggaran berdasarkan Urusan Tahun ' . $bulan),
    '#legend_position' => 'right',
    '#data_labels' => TRUE,
    '#tooltips' => TRUE,
	
);

if ($inpersen) {
	$arr_persen = array();
	$x = count($arr_anggaran);
	for ($i=0; $i<=$x; $i++){
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_realisasi[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Anggaran'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_anggaran,
		'#suffix' => 'Juta',
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Realisasi'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => 'Juta',
	);
}
$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arr_urusan,
);

$apbdbelanja['belanja_urusan_chart_final'] = $chart;

return drupal_render($apbdbelanja);

}

function print_pdf($output) {
	 
	 $pdf=apbd_pdfd7($output,'L','cek','Pemerintah Kabupaten Jepara '.$bulan);
		$title = 'abc.pdf';
		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename="'.$title.'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($pdf));
		header('Accept-Ranges: bytes');
		header('Expires: 0');
		header('Cache-Control: public, must-revalidate, max-age=0');  
	  
	  print $pdf;
	  return NULL;	
}

?>
