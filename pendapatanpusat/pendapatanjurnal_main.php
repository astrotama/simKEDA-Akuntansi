<?php
function pendapatanjurnal_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 20;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				//drupal_set_message('filter');
				//drupal_set_message(arg(5));
				
				$kodeuk = arg(2);
				$bulan = arg(3);
				$hari = arg(4);
				$keyword = arg(5);

				break;
				
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
			$kodeuk = $_SESSION["pendapatanjurnaluk_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["pendapatanjurnaluk_bulan"];
		if ($bulan=='') $bulan = date('n');
		
		$hari = $_SESSION["pendapatanjurnaluk_hari"];
		if ($hari=='') $hari = '0';
		
		$keyword = $_SESSION["pendapatanjurnaluk_keyword"];
		//if ($keyword == '') $keyword = '';
		
		/*
		$sp2dok = $_SESSION["sp2d_gaji_sp2dok"];
		if ($sp2dok=='') $sp2dok = 'ZZ';

		$jenisgaji = $_SESSION["sp2d_gaji_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';
		*/
	}
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('pendapatanjurnal_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Rekening', 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => 'Detil', 'valign'=>'top'),
		array('data' => 'Keterangan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'total',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('jurnal', 'j')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
	$query->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$query->innerJoin('rincianobyek', 'r', 'ji.kodero=r.kodero');

	# get the desired fields from the database
	$query->fields('j', array('jurnalid', 'refid', 'kodeuk', 'nobukti', 'tanggal', 'keterangan', 'total'));
	$query->fields('u', array('namasingkat'));
	$query->fields('r', array('kodero', 'uraian'));
	$query->fields('ji', array('koderod'));
	
	//keyword 
	if ($keyword!='') {
		$db_or = db_or();
		$db_or->condition('j.keterangan', '%' . db_like($keyword) . '%', 'LIKE');	
		$db_or->condition('j.nobukti', '%' . db_like($keyword) . '%', 'LIKE');	
		$db_or->condition('j.nobuktilain', '%' . db_like($keyword) . '%', 'LIKE');	
		$query->condition($db_or);
		$db_or->condition('r.uraian', '%' . db_like($keyword) . '%', 'LIKE');	
		$query->condition($db_or);
	}
	
	if ($kodeuk !='ZZ') $query->condition('j.kodeuk', $kodeuk, '=');
	
	$or = db_or();
	$or->condition('ji.kodero', db_like('4') . '%', 'LIKE');
	$or->condition('ji.kodero', db_like('61') . '%', 'LIKE');
	
	$query->condition($or);
	//$query->condition('ji.kodero', db_like('4') . '%', 'LIKE');
	if ($bulan !='0') $query->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
	if ($hari !='0') $query->where('EXTRACT(DAY FROM j.tanggal) = :day', array('day' => $hari));
	
	//HANYA SELAIN`
	$query->condition('j.jenis', 'pad', '=');
	
	$query->orderByHeader($header);
	$query->orderBy('j.tanggal', 'DESC');
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
		
		$terjurnal = apbd_icon_jurnal_sudah();
		$editlink = apbd_button_jurnal('pendapatanjurnal/jurnaledit/' . $data->jurnalid);
		
		//jurnal
		//akuntansi/buku/00/41201006/11
		//$editlink .= apbd_button_bukubesar('akuntansi/buku/ZZ/' . $data->kodero . '/' . $kodeuk);
		
		$detil = '';
		
		if ($data->koderod<>'') {
			$query = db_select('rincianobyekdetil', 'rod');
			$query->fields('rod', array('uraian'));	
			$query->condition('rod.koderod', $data->koderod, '=');
				
			# execute the query
			$resx = $query->execute();
			foreach ($resx as $datadetil) {
				$detil = $datadetil->uraian;
			}
							
		}	
		
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $terjurnal,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),  'align' => 'center', 'valign'=>'top'),
						array('data' => $data->kodero . ' - ' . $data->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => $detil, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->total),'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	//BUTTON
	$btn = apbd_button_baru('/pendapatanjurnalman');
	$btn .= "&nbsp;" . apbd_button_print('/pendapatanjurnal/filter/' . $kodeuk . '/' . $bulan . '/' . $keyword . '/pdf');
	$btn .= "&nbsp;" . apbd_button_excel('');	
	if ($hari!='0') $btn .= "&nbsp;" . apbd_button_hapus('/pendapatanjurnal/deleteday/' . apbd_tahun() . '-' . sprintf("%02d", $bulan) . '-' . sprintf("%02d", $hari) );
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(7)=='pdf'){
		$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){
	
	$header = array (
		array('data' => 'No','height'=>'20px','width' => '40px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'SKPD','height'=>'20px','width' => '160px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Type Dok.','height'=>'20px','width' => '40px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'No SP2D','height'=>'20px','width' => '60px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Tgl SP2D','height'=>'20px','width' => '90px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Kegiatan','height'=>'20px', 'width' => '150px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Keperluan','height'=>'20px', 'width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Penerima','height'=>'20px', 'width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'Jumlah','height'=>'20px', 'width' => '120px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		
	);
	$query = db_select('dokumen', 'k');//->extend('PagerDefault')->extend('TableSort');
	# get the desired fields from the database
	$query->fields('k', array('dokid','kodeuk', 'sp2dno','sp2dtgl','keperluan', 'kegiatan', 'penerimanama', 'jumlah','jenisdokumen'));
	
	//keyword
	if ($keyword!='ZZ') {
		$db_or = db_or();
		$db_or->condition('k.kegiatan', '%' . db_like($keyword) . '%', 'LIKE');
		$db_or->condition('k.keperluan', '%' . db_like($keyword) . '%', 'LIKE');		
		$db_or->condition('k.penerimanama', '%' . db_like($keyword) . '%', 'LIKE');
		
		$query->condition($db_or);
	}
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->condition('k.bulan', $bulan, '=');
	if ($jenisdokumen !='ZZ') $query->condition('k.jenisdokumen', $jenisdokumen, '=');
	
	//$query->orderByHeader($header);
	$query->orderBy('k.sp2dtgl', 'ASC');
	//$query->limit($limit);
		
	//drupal_set_message($query);
	
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	

	
		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		$query = db_select('unitkerja', 'k')->extend('PagerDefault')->extend('TableSort');
		$query->fields('k', array('namasingkat'));
		$query->condition('kodeuk', $data->kodeuk, '=');
		$results = $query->execute();
		foreach ($results as $datas){
			$namasingkat=$datas->namasingkat;
		}
		
		$query = db_select('dokumentipe', 'd')->extend('PagerDefault')->extend('TableSort');
		$query->fields('d', array('uraian'));
		$query->condition('kode', $data->jenisdokumen, '=');
		$results = $query->execute();
		foreach ($results as $datas){
			$doktipe=$datas->uraian;
		}
		$rows[] = array(
						array('data' => $no,'width' => '40px', 'align' => 'center', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $namasingkat,'width' => '160px',  'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $doktipe,'width' => '40px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->sp2dno,'width' => '60px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_format_tanggal_pendek($data->sp2dtgl),'width' => '90px',  'align' => 'center', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->kegiatan,'width' => '150px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->keperluan,'width' => '120px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->penerimanama,'width' => '120px', 'align' => 'left', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_fn($data->jumlah),'width' => '120px','align' => 'right', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					
	}
	$rows[] = array(
						array('data' => '','width' => '900px', 'align' => 'center', 'valign'=>'top','style'=>'border-top:1px solid black;'),
						
					);

	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function pendapatanjurnal_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['skpd'];
	$bulan = $form_state['values']['bulan'];
	$hari = $form_state['values']['hari'];
	$keyword = $form_state['values']['keyword'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/
	
	$_SESSION["pendapatanjurnaluk_kodeuk"] = $kodeuk;
	$_SESSION["pendapatanjurnaluk_bulan"] = $bulan;
	$_SESSION["pendapatanjurnaluk_hari"] = $hari;
	$_SESSION["pendapatanjurnaluk_keyword"] = $keyword;
	
	$uri = 'pendapatanjurnal/filter/' . $kodeuk . '/' . $bulan . '/' . $hari . '/' . $keyword;
	drupal_goto($uri);
	
}


function pendapatanjurnal_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$bulan = date('n');
	$hari = '0';
	$keyword = '';
	
	//drupal_set_message($bulan);
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$hari=arg(4);
		$keyword = arg(5);

	}  else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["pendapatanjurnaluk_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["pendapatanjurnaluk_bulan"];
		if ($bulan=='') $bulan = "date('n')";
		
		$hari = $_SESSION["pendapatanjurnaluk_hari"];
		if ($hari=='') $hari = '0';
		
		$keyword = $_SESSION["pendapatanjurnaluk_keyword"];

	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		
	
	//SKPD
	$query = db_select('unitkerja', 'p');
	$query->innerJoin('anggperuk', 'a', 'a.kodeuk=p.kodeuk');
	# get the desired fields from the database
	$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
			->orderBy('kodedinas', 'ASC');
	# execute the query
	$results = $query->execute();
	# build the table fields
	$option_skpd['ZZ'] = 'SELURUH SKPD'; 
	if($results){
		foreach($results as $data) {
		  $option_skpd[$data->kodeuk] = $data->namasingkat; 
		}
	}		
	$form['formdata']['skpd'] = array(
		'#type' => 'select',
		'#title' =>  t('SKPD'),
		'#options' => $option_skpd,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $kodeuk,
	);
	
	//BULAN
	$option_bulan =array('Setahun', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_bulan,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$bulan,
	);

	//HARI
	$option_hari =array('Sebulan', '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30', '31');
	$form['formdata']['hari'] = array(
		'#type' => 'select',
		'#title' =>  t('Tanggal'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $option_hari,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' =>$hari,
	);
	

	$form['formdata']['keyword'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kata Kunci'),
		'#description' =>  t('Kata kunci untuk mencari S2PD, bisa nama kegiatan, keperluan, atau nama penerima/pihak ketiga'),
		'#default_value' => $keyword, 
	);	
	
	//align-justify
	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}


?>