<?php
function pendapatansetor_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				
				$kodeuk = arg(2);
				$bulan = arg(3);
				$hari = arg(4);
				$statusjurnal = arg(5);
				$keyword = arg(6);

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	}  else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["pendapatansetor_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["pendapatansetor_bulan"];
		if ($bulan=='') $bulan = date('m');

		$hari = $_SESSION["pendapatansetor_hari"];
		if ($hari=='') $hari = '0';
		
		$statusjurnal = $_SESSION["pendapatansetor_statusjurnal"];
		if ($statusjurnal=='') $statusjurnal = 'ZZ';
		
		$keyword = $_SESSION["pendapatansetor_keyword"];
	}

	$kodeuk = apbd_getuseruk();
	
	if ($keyword == '') $keyword = 'ZZ';

	
	$output_form = drupal_get_form('pendapatansetor_main_form');
	
		
	db_set_active('pendapatan');
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => 'Keterangan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'jumlahkeluar',  'valign'=>'top'),
		array('data' => '', 'width' => '60px', 'valign'=>'top'),
		
	);
	

	$query = db_select('q_antriansetorkeluar', 'q')->extend('PagerDefault')->extend('TableSort');
	$query->fields('q', array('id', 'kodeuk', 'tgl_keluar', 'keterangan', 'jumlah'));
	//$query->condition('a.jumlahkeluar', 0, '>');
	
	//keyword
	/*
	if ($keyword!='ZZ') {
		$db_or = db_or();
		$db_or->condition('a.keterangan', '%' . db_like($keyword) . '%', 'LIKE');	
		$query->condition($db_or);	
	}
	*/
	
	if ($keyword !='ZZ') $query->condition('q.keterangan', '%' . db_like($keyword) . '%', 'LIKE');
	if ($kodeuk !='ZZ') $query->condition('q.kodeuk', $kodeuk, '=');
	if ($bulan !='0') $query->where('EXTRACT(MONTH FROM q.tgl_keluar) = :month', array('month' => $bulan));
	if ($hari !='0') $query->where('EXTRACT(DAY FROM q.tgl_keluar) = :day', array('day' => $hari));
	
	//if ($statusjurnal !='ZZ') $query->condition('a.jurnalsudah', $statusjurnal, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('q.tgl_keluar', 'ASC');
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
		
		/*
		if($data->jurnalsudah=='1'){
			$jurnalsudah = apbd_icon_jurnal_sudah();
			//$editlink = apbd_button_jurnal('pendapatanmasuk/edit/' . $data->jurnalid);
			$editlink = 'Sudah';
		} else {
			
		*/	
			$jurnalsudah = apbd_icon_jurnal_belum();
			//$editlink = apbd_button_jurnalkan('pendapatanmasuk/jurnal/' . $data->setorid);
			//$editlink = 'Belum';
			$editlink = createlink('<span class="glyphicon glyphicon-chevron-right" aria-hidden="true">Jurnal</span>','pendapatansetor/jurnal/' . $data->id);
			
		
		$uraian= '';
		$res = db_query('select s.kodero, r.uraian namarekening, s.uraian, s.jumlahmasuk from setor s inner join rincianobyek r on s.kodero=r.kodero where s.idkeluar=:idkeluar', array(':idkeluar'=>$data->id));	
		foreach ($res as $dat) {
			$uraian .= $dat->kodero . ' - ' . $dat->namarekening . ', ' . $dat->uraian . ' ' . apbd_fn($dat->jumlahmasuk) . '; ';
		}	
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $jurnalsudah,'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->tgl_keluar),  'align' => 'center', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						$editlink,
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	db_set_active();
	
	
	//BUTTON
	//$btn = apbd_button_print('/pendapatansetor/filter/' . $kodeuk . '/' . $bulan . '/' . $keyword . '/pdf');
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	/*	
	if(arg(7)=='pdf'){
		//$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		//print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	*/
	return drupal_render($output_form) . $output;
	
}


function pendapatansetor_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['skpd'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$bulan = $form_state['values']['bulan'];
		$hari = $form_state['values']['hari'];
		$statusjurnal = $form_state['values']['statusjurnal'];
		$keyword = $form_state['values']['keyword'];
		
	} else {
		$bulan = '0';
		$hari = '0';
		$statusjurnal = '0';
		$keyword = '';

	}
	$_SESSION["pendapatansetor_kodeuk"] = $kodeuk;
	$_SESSION["pendapatansetor_bulan"] = $bulan;
	$_SESSION["pendapatansetor_hari"] = $hari;
	$_SESSION["pendapatansetor_statusjurnal"] = $statusjurnal;
	$_SESSION["pendapatansetor_keyword"] = $keyword;
	
	$uri = 'pendapatansetor/filter/' . $kodeuk . '/' . $bulan . '/' . $hari . '/' . $statusjurnal . '/' . $keyword;
	drupal_goto($uri);
	
}


function pendapatansetor_main_form($form, &$form_state) {
	/*
	$kodeuk = 'ZZ';
	//$bulan = date('m');
	$bulan = '9';
	$statusjurnal = 'ZZ';
	$keyword = '';
	*/
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$hari = arg(4);
		$statusjurnal = arg(5);
		$keyword = arg(6);

	}  else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["pendapatansetor_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["pendapatansetor_bulan"];
		if ($bulan=='') $bulan = "date('n')";

		$hari = $_SESSION["pendapatansetor_hari"];
		if ($hari=='') $hari = '0';
		
		$statusjurnal = $_SESSION["pendapatansetor_statusjurnal"];
		if ($statusjurnal=='') $statusjurnal = 'ZZ';
		
		$keyword = $_SESSION["pendapatansetor_keyword"];

	}
 
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=>  'PILIHAN DATA',
		//'#title'=>  '<p>PILIHAN DATA</p>' . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);		
	
	if (isUserSKPD()) {
		$form['formdata']['skpd'] = array(
			'#type' => 'value',
			'#value' =>  $kodeuk,

		);			
	} else {
		//SKPD
		$query = db_select('unitkerja', 'p');
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
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#prefix' => '<div id="skpd-replace">',
			'#suffix' => '</div>',
			// When the form is rebuilt during ajax processing, the $selected variable
			// will now have the new value and so the options will change.
			'#options' => $option_skpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
			//'#default_value' => $namasingkat,
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
	
	$opt_jurnal['ZZ'] ='SEMUA';
	$opt_jurnal['0'] = 'BELUM JURNAL';
	$opt_jurnal['1'] = 'SUDAH JURNAL';	
	$form['formdata']['statusjurnal'] = array(
		'#type' => 'select',
		'#title' =>  t('Penjurnalan'),
		'#options' => $opt_jurnal,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $statusjurnal,
	);	 

		
	$form['formdata']['keyword'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kata Kunci'),
		'#description' =>  t('Kata kunci untuk mencari, bisa nama kegiatan, keterangan, atau nama penerima/pihak ketiga'),
		'#default_value' => $keyword, 
	);	

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	$form['formdata']['reset']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Reset',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);	
	
	return $form;
}



?>
