<?php
function umum_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
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
				$keywordd = arg(4);

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
			$kodeuk = $_SESSION["jurnal_umum_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["jurnal_umum_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$keywordd = $_SESSION["keywordd"];
		//if ($keywordd=='') $keywordd = 'ZZ';

	}
	
	$output_form = drupal_get_form('umum_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => 'Keterangan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'total',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);

    $jurnalid = arg(2);
	if (isUserSKPD()) {
		$suffix = 'uk';
	} else {
		$suffix = '';
	} 	
	
	//drupal_set_message($kodeuk);
	//drupal_set_message($suffix);

	$query = db_select('jurnal' . $suffix, 'j')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');

	# get the desired fields from the database
	$query->fields('j', array('jurnalid', 'refid', 'kodeuk', 'nobukti', 'tanggal', 'keterangan', 'total'));
	$query->fields('u', array('namasingkat'));
	$query->fields('k', array('kegiatan'));
	
	//keywordd
	if ($keywordd!='') {
		$db_or = db_or();
		$db_or->condition('j.keterangan', '%' . db_like($keywordd) . '%', 'LIKE');	
		$db_or->condition('j.nobukti', '%' . db_like($keywordd) . '%', 'LIKE');	
		$db_or->condition('j.nobuktilain', '%' . db_like($keywordd) . '%', 'LIKE');	
		$query->condition($db_or);	
	} 
	
	if ($kodeuk =='ZZ') {
		global $user;
		$username = $user->name;		
		
		$query->innerJoin('userskpdakt', 'us', 'j.kodeuk=us.kodeuk');
		$query->condition('us.username', $username, '=');
	
	} else {
		$query->condition('j.kodeuk', $kodeuk, '=');
	}	
	
	if ($bulan !='0') $query->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
	
	//HANYA SELAIN`
	$query->condition('j.jenis', db_like('umum') . '%', 'LIKE');
	
	$query->orderByHeader($header);
	$query->orderBy('j.tanggal', 'ASC');
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
		$editlink = apbd_button_jurnal('umum/jurnal/' . $data->jurnalid);
		
		if ($data->kegiatan=='')
			$kegiatan = 'Non Kegiatan';
		else
			$kegiatan = $data->kegiatan;

		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $terjurnal,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),  'align' => 'center', 'valign'=>'top'),
						array('data' => $kegiatan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->total),'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	//BUTTON
	//$btn = apbd_button_baru('/umum/jurnal');
	//$btn = "&nbsp;" . apbd_button_excel('');	
	$btn = '';
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	if(arg(7)=='pdf'){
		$output=getData($kodeuk,$bulan,$jenisdokumen,$keywordd);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keywordd){
	
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
	
	//keywordd
	if ($keywordd!='ZZ') {
		$db_or = db_or();
		$db_or->condition('k.kegiatan', '%' . db_like($keywordd) . '%', 'LIKE');
		$db_or->condition('k.keperluan', '%' . db_like($keywordd) . '%', 'LIKE');		
		$db_or->condition('k.penerimanama', '%' . db_like($keywordd) . '%', 'LIKE');
		
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

function umum_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['skpd'];
	$bulan = $form_state['values']['bulan'];
	$keywordd = $form_state['values']['keywordd'];
	
	/*
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit2']) {
		drupal_set_message($form_state['values']['submit2']);
	}
	else{
		drupal_set_message($form_state['clicked_button']['#value']);
	}
	*/
	
	$_SESSION["jurnal_umum_kodeuk"] = $kodeuk;
	$_SESSION["jurnal_umum_bulan"] = $bulan;
		$_SESSION["keywordd"] = $keywordd;
	
	$uri = 'umum/filter/' . $kodeuk . '/' . $bulan . '/' . $keywordd;
	drupal_goto($uri);
	
}


function umum_main_form($form, &$form_state) {
	
	/*
	if (isUserSKPD())
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = 'ZZ';		//$bulan = date('m');	//$bulan = date('m');

	$bulan = '0';
	$keywordd = '';
	*/
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$keywordd = arg(4);
	
	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["jurnal_umum_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["jurnal_umum_bulan"];
		if ($bulan=='') $bulan = '1';
		
		$keywordd = $_SESSION["keywordd"];
		//if ($keywordd=='') $keywordd = 'ZZ';
/*
		$jenisgaji = $_SESSION["sp2d_gaji_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';
		*/
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
	if (isUserSKPD()) {
		$form['formdata']['skpd'] = array(
			'#type' => 'hidden',
			'#title' =>  t('SKPD'),
			'#default_value' => $kodeuk,
		);
		
	} else {

		global $user;
		$username = $user->name;		
	
		$option_skpd['ZZ'] = 'SELURUH SKPD';	
		
		$result = db_query('SELECT unitkerja.kodeuk, unitkerja.namasingkat FROM unitkerja INNER JOIN userskpd ON unitkerja.kodeuk=userskpd.kodeuk WHERE userskpd.username=:username ORDER BY unitkerja.kodedinas', array(':username' => $username));	
		while($row = $result->fetchObject()){
			$option_skpd[$row->kodeuk] = $row->namasingkat; 
		}
		 
		$form['formdata']['skpd'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#prefix' => '<div id="skpd-replace">',
			'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			'#default_value' => $kodeuk,
		);
	}	
	
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

	//JENIS DOKUMEN
	/*
	$opt_jenis['ZZ'] ='SEMUA';
	$opt_jenis['1'] = 'GANTI UANG (GU) PERSEDIAAN';
	$opt_jenis['3'] = 'LS GAJI';	
	$opt_jenis['4'] = 'LS BARANG DAN JASA';	
	$opt_jenis['5'] = 'GU NIHIL';	
	$opt_jenis['7'] = 'TU NIHIL';	
	$form['formdata']['jenis'] = array(
		'#type' => 'select',
		'#title' =>  t('Jenis Jurnal Umum'),
		'#options' => $opt_jenis,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenis,
	);	
	*/	

	$form['formdata']['keywordd'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kata Kunci'),
		'#description' =>  t('Kata kunci untuk mencari S2PD, bisa nama kegiatan, keperluan, atau nama penerima/pihak ketiga'),
		'#default_value' => $keywordd, 
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
