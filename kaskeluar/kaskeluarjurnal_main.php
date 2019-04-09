<?php
function kaskeluarjurnal_main($arg=NULL, $nama=NULL) {
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
				$jenisdokumen = arg(4);
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
			$kodeuk = $_SESSION["kaskeluarjurnal_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["kaskeluarjurnal_bulan"];
		if ($bulan=='') $bulan = "0";
		
		$jenisdokumen = $_SESSION["kaskeluarjurnal_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = '0';
	
		$keyword = $_SESSION["kaskeluarjurnal_keyword"];
		if ($keyword=='') $keyword = 'ZZ';
	
		/*
		$sp2dok = $_SESSION["sp2d_gaji_sp2dok"];
		if ($sp2dok=='') $sp2dok = 'ZZ';

		$jenisgaji = $_SESSION["sp2d_gaji_jenisgaji"];
		if ($jenisgaji=='') $jenisgaji = 'ZZ';
		*/
	}

	if ($keyword == '') $keyword = 'ZZ';
	
	//drupal_set_message($keyword);
	//drupal_set_message($jenisdokumen);
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	$output_form = drupal_get_form('kaskeluarjurnal_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'No. SP2D','width' => '80px','field'=> 'refid', 'valign'=>'top'),
		array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => 'Keperluan', 'field'=> 'keterangan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'total',  'valign'=>'top'),
		array('data' => '', 'width' => '50px', 'valign'=>'top'),
		array('data' => '', 'width' => '50px', 'valign'=>'top'),
		
	);
	
	if (isUserSKPD())
		$query = db_select('jurnaluk', 'j')->extend('PagerDefault')->extend('TableSort');
	else
		$query = db_select('jurnal', 'j')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja', 'u', 'j.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatanskpd', 'k', 'j.kodekeg=k.kodekeg');
	if ($jenisdokumen !='ZZ') {
		$query->condition('j.jenisdokumen', $jenisdokumen, '=');
	}

	# get the desired fields from the database
	$query->fields('j', array('jurnalid', 'refid', 'kodeuk', 'nobukti', 'tanggal', 'keterangan', 'total'));
	$query->fields('u', array('namasingkat'));
	$query->fields('k', array('kegiatan'));
	
	//keyword
	if ($keyword!='ZZ') {
		$db_or = db_or();
		$db_or->condition('j.keterangan', '%' . db_like($keyword) . '%', 'LIKE');	
		$db_or->condition('j.nobukti', '%' . db_like($keyword) . '%', 'LIKE');	
		$db_or->condition('j.nobuktilain', '%' . db_like($keyword) . '%', 'LIKE');	
		$query->condition($db_or);	
	}
	
	if ($kodeuk =='ZZ') {
		global $user;
		$username = $user->name;		
		
		$query->innerJoin('userskpd', 'us', 'j.kodeuk=us.kodeuk');
		$query->condition('us.username', $username, '=');
	
	} else {
		$query->condition('j.kodeuk', $kodeuk, '=');
	}	
	
	if ($bulan !='0') $query->where('EXTRACT(MONTH FROM j.tanggal) = :month', array('month' => $bulan));
	
	//HANYA UP/TU
	$query->condition('j.jenis', 'kas', '=');
	
	$query->orderByHeader($header);
	$query->orderBy('j.tanggal', 'ASC');
	$query->limit($limit);
		
	dpq($query);
	
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
		
		if ($data->kegiatan=='')
			$namakegiatan = 'Uang Persediaan (UP/TU)';	
		else
			$namakegiatan = $data->kegiatan;;	
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_icon_jurnal_sudah(),'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => $data->nobukti, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal),  'align' => 'center', 'valign'=>'top'),
						array('data' => $namakegiatan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->total),'align' => 'right', 'valign'=>'top'),
						apbd_button_jurnal('kaskeluarjurnal/jurnaledit/' . $data->jurnalid),
						apbd_button_esp2d($data->refid),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	
	//BUTTON
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');

	return drupal_render($output_form) . $output;
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){
	

}

function kaskeluarjurnal_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$bulan = $form_state['values']['bulan'];
		$jenisdokumen = $form_state['values']['jenisdokumen'];
		$keyword = $form_state['values']['keyword'];
	} else {
		$bulan = '0';
		$jenisdokumen = 'ZZ';
		$keyword = '';
	}	
	$_SESSION["kaskeluarjurnal_kodeuk"] = $kodeuk;
	$_SESSION["kaskeluarjurnal_bulan"] = $bulan;
	$_SESSION["kaskeluarjurnal_jenisdokumen"] = $jenisdokumen;
	$_SESSION["kaskeluarjurnal_keyword"] = $keyword;
	
	$uri = 'kaskeluarjurnal/filter/' . $kodeuk . '/' . $bulan . '/' . $jenisdokumen . '/' . $keyword;
	drupal_goto($uri);
	
}


function kaskeluarjurnal_main_form($form, &$form_state) {
	
	/*
	if (isUserSKPD())
		$kodeuk = apbd_getuseruk();
	else
		$kodeuk = 'ZZ';		//$bulan = date('m');	//$bulan = date('m');
	$bulan = '0';
	$jenisdokumen = 'ZZ';
	$keyword = '';
	*/
	if(arg(2)!=null){
		
		$kodeuk = arg(2);
		$bulan=arg(3);
		$jenisdokumen = arg(4);
		$keyword = arg(5);

	} else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["kaskeluarjurnal_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["kaskeluarjurnal_bulan"];
		if ($bulan=='') $bulan = "0";
		
		$jenisdokumen = $_SESSION["kaskeluarjurnal_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = '0';
		
		
		$keyword = $_SESSION["kaskeluarjurnal_keyword"];

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
		$form['formdata']['kodeuk'] = array(
			'#type' => 'hidden',
			'#title' =>  t('SKPD'),
			'#default_value' => $kodeuk,
		);
		
	} else {
		global $user;
		$username = $user->name;		
	
		$option_skpd['ZZ'] = 'SELURUH SKPD';	
		
		if (isAdministrator())
			$result = db_query('SELECT kodeuk, namasingkat FROM unitkerja ORDER BY namasingkat');	
		else
			$result = db_query('SELECT unitkerja.kodeuk, unitkerja.namasingkat FROM unitkerja INNER JOIN userskpd ON unitkerja.kodeuk=userskpd.kodeuk WHERE userskpd.username=:username ORDER BY unitkerja.namasingkat', array(':username' => $username));	
		
		while($row = $result->fetchObject()){
			$option_skpd[$row->kodeuk] = $row->namasingkat; 
		}
				
		$form['formdata']['kodeuk'] = array(
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

	//JENIS DOKUMEN
	$opt_jenisdokumen['ZZ'] ='SEMUA';
	$opt_jenisdokumen['0'] = 'UP - UANG PERSEDIAAN';
	$opt_jenisdokumen['1'] = 'GU - GANTI UANG';	
	$opt_jenisdokumen['2'] = 'TU - TAMBAHAN UANG';	
	$form['formdata']['jenisdokumen'] = array(
		'#type' => 'select',
		'#title' =>  t('SP2D'),
		'#options' => $opt_jenisdokumen,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisdokumen,
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
	$form['formdata']['reset']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Reset',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);	
	return $form;
}



?>
