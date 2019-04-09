<?php
function jurnalspjantrian_main($arg=NULL, $nama=NULL) {
	$qlike='';
	$limit = 10;
	if (isUserSKPD()) {
		$jurnalsuffix = 'uk';
		$isSKPD = true;
	} else {
		$jurnalsuffix = '';		//$bulan = date('m');
		$isSKPD = false;
	}
	
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
			
				$kodeuk = arg(2);
				
				$bulan = arg(3);
				$jenisdokumen = arg(4);
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
			$kodeuk = $_SESSION["jurnalantrian_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["jurnalantrian_bulan"];
		if ($bulan=='') $bulan = '0';
		
		$jenisdokumen = $_SESSION["jurnalantrian_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = 'ZZ';
		
		$statusjurnal = $_SESSION["jurnalantrian_statusjurnal"];
		if ($statusjurnal=='') $statusjurnal = '0';
		
		$keyword = $_SESSION["jurnalantrian_keyword"];

	}
	
	if ($keyword == '') $keyword = 'ZZ';
	

	if (isUserSKPD()) {
		$jurnalsuffix = 'uk';
		$lblstatus = 'Pusat';
	} else {
		$jurnalsuffix = '';		//$bulan = date('m');	
		$lblstatus = 'Dinas';
	}
	
	$output_form = drupal_get_form('jurnalspjantrian_main_form');

	db_set_active('penatausahaan');
	
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD', 'field'=> 'kodeuk', 'valign'=>'top'),
		array('data' => 'No. SP2D','width' => '80px','field'=> 'sp2dno', 'valign'=>'top'),
		array('data' => 'Tgl. SP2D', 'width' => '90px','field'=> 'sp2dtgl', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'jumlah',  'valign'=>'top'),
		array('data' => $lblstatus, 'width' => '40px',  'valign'=>'top'),
		array('data' => '', 'width' => '50px', 'valign'=>'top'),
		array('data' => '', 'width' => '50px', 'valign'=>'top'),
		
	);
	

	$query = db_select('dokumen', 'k')->extend('PagerDefault')->extend('TableSort');
	//$query->innerJoin('dokumenrekening', 'dr', 'k.dokid=dr.dokid');
	$query->innerJoin('unitkerja', 'u', 'k.kodeuk=u.kodeuk');
	$query->leftJoin('kegiatanskpd', 'keg', 'keg.kodekeg=k.kodekeg');

	# get the desired fields from the database
	$query->fields('k', array('dokid', 'jurnalidspj', 'jurnalidspjuk', 'kodeuk', 'sp2dno', 'sp2dtgl', 'jurnalsudah', 'jurnalsudahuk','keperluan', 'jumlah', 'jenisdokumen'));
	$query->fields('u', array('namasingkat'));
	$query->fields('keg', array('kegiatan'));
	
	//keyword
	if ($keyword!='ZZ') {
		$db_or = db_or();
		$db_or->condition('keg.kegiatan', '%' . db_like($keyword) . '%', 'LIKE');
		$db_or->condition('k.keperluan', '%' . db_like($keyword) . '%', 'LIKE');	
		$query->condition($db_or);	
	}
	

	if ($kodeuk =='ZZ') {
		global $user;
		$username = $user->name;		
		
		$query->innerJoin('userskpd', 'us', 'k.kodeuk=us.kodeuk');
		$query->condition('us.username', $username, '=');
	} else {
		$query->condition('k.kodeuk', $kodeuk, '=');
	}	
	
	if ($bulan !='0') $query->condition('k.bulan', $bulan, '=');
	if ($jenisdokumen =='ZZ') 
		$query->condition('k.jenisdokumen', array(1, 3, 4, 5, 7), 'IN');
	else 
		$query->condition('k.jenisdokumen', $jenisdokumen, '=');
	$query->condition('k.sp2dok', 0, '>');
	$query->condition('k.sp2dno', '', '<>');

		
	if ($statusjurnal !='ZZ') $query->condition('k.jurnalsudah' . $jurnalsuffix, $statusjurnal, '=');
	
	$query->orderByHeader($header);
	$query->orderBy('k.sp2dtgl', 'ASC');
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
		
		
		if ($isSKPD) {
			if($data->jurnalsudahuk=='1'){
				$jurnalsudah = apbd_icon_jurnal_sudah();
				$editlink = apbd_button_jurnal('jurnalspjjurnal/jurnaledit/' . $data->jurnalidspjuk);
			
			} else {
				$jurnalsudah = apbd_icon_jurnal_belum();
				$editlink = apbd_button_jurnalkan('jurnalspjantrian/jurnal/' . $data->dokid);
			}
			
			if ($data->jurnalsudah == '1') {
				$status = 'Sudah';
				$style = 'green';
				
			} else {
				$status = 'Belum';
				$style = 'red';
			}
			
		} else {
			if($data->jurnalsudah=='1'){
				$jurnalsudah = apbd_icon_jurnal_sudah();
				$editlink = apbd_button_jurnal('jurnalspjjurnal/jurnaledit/' . $data->jurnalidspj);
			
			} else {
				$jurnalsudah = apbd_icon_jurnal_belum();
				$editlink = apbd_button_jurnalkan('jurnalspjantrian/jurnal/' . $data->dokid);
			}
			
			if ($data->jurnalsudahuk == '1') {
				$status = 'Sudah';
				$style = 'green';
				
			} else {
				$status = 'Belum';
				$style = 'red';
			}
		}

		if ($data->jenisdokumen=='1')
			$kegiatan = 'Ganti Uang';
		elseif ($data->jenisdokumen=='5')
			$kegiatan = 'GU Nihil';
		elseif ($data->jenisdokumen=='7')
			$kegiatan = 'TU Nihil';
		else
			$kegiatan = $data->kegiatan;
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $jurnalsudah,'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sp2dno, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->sp2dtgl),  'align' => 'center', 'valign'=>'top'),
						array('data' => $kegiatan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->keperluan, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah),'align' => 'right', 'valign'=>'top'),
						array('data' => $status,'align' => 'center', 'valign'=>'top', 'style'=>'color: ' . $style),
						$editlink,
						apbd_button_esp2d($data->dokid),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	}
	
	db_set_active();
	
	//BUTTON
	//$btn = apbd_button_print('/jurnalspjantrian/filter/' . $kodeuk . '/' . $bulan . '/' . $jenisdokumen . '/' . $keyword . '/pdf');
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	
	/*	
	if(arg(7)=='pdf'){
		$output=getData($kodeuk,$bulan,$jenisdokumen,$keyword);
		print_pdf_l($output);
		
	}
	else{
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	*/
	return drupal_render($output_form) . $output;
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){
	

}

function jurnalspjantrian_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		$bulan = $form_state['values']['bulan'];
		$jenisdokumen = $form_state['values']['jenisdokumen'];
		$statusjurnal = $form_state['values']['statusjurnal'];
		$keyword = $form_state['values']['keyword'];
	} else {
		$bulan = '0';
		$jenisdokumen = 'ZZ';
		$statusjurnal = '0';
		$keyword = '';
	}
	
	$_SESSION["jurnalantrian_kodeuk"] = $kodeuk;
	$_SESSION["jurnalantrian_bulan"] = $bulan;
	$_SESSION["jurnalantrian_jenisdokumen"] = $jenisdokumen;
	$_SESSION["jurnalantrian_statusjurnal"] = $statusjurnal;
	$_SESSION["jurnalantrian_keyword"] = $keyword;
	
	$uri = 'jurnalspjantrian/filter/' . $kodeuk . '/' . $bulan . '/' . $jenisdokumen . '/' . $statusjurnal . '/' . $keyword;
	drupal_goto($uri);
	
}


function jurnalspjantrian_main_form($form, &$form_state) {
	
	global $user;
	$username = $user->name;		
	
	if(arg(2)!=null){
		
		$kodeuk = arg(2);					
		$bulan=arg(3);
		$jenisdokumen = arg(4);
		$statusjurnal = arg(5);
		$keyword = arg(6);

	}  else {
		if (isUserSKPD()) 
			$kodeuk = apbd_getuseruk();
		else {
			$kodeuk = $_SESSION["jurnalantrian_kodeuk"];
			if ($kodeuk=='') $kodeuk = 'ZZ';
		}
		//$bulan = date('m');
		$bulan = $_SESSION["jurnalantrian_bulan"];
		if ($bulan=='') $bulan = "date('n')";
		
		$jenisdokumen = $_SESSION["jurnalantrian_jenisdokumen"];
		if ($jenisdokumen=='') $jenisdokumen = '0';
		
		$statusjurnal = $_SESSION["jurnalantrian_statusjurnal"];
		if ($statusjurnal=='') $statusjurnal = '0';
		
		$keyword = $_SESSION["jurnalantrian_keyword"];

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
		$form['formdata']['kodeuk'] = array(
			'#type' => 'value',
			'#value' => $kodeuk,
		);
	} else {
		$option_skpd['ZZ'] = 'SELURUH SKPD';	
		
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
	$opt_jenisdokumen['ZZ'] ='SEMUA';
	$opt_jenisdokumen['1'] = 'GANTI UANG (GU) PERSEDIAAN';
	$opt_jenisdokumen['3'] = 'LS GAJI';	
	$opt_jenisdokumen['4'] = 'LS BARANG DAN JASA';	
	$opt_jenisdokumen['5'] = 'GU NIHIL';	
	$opt_jenisdokumen['7'] = 'TU NIHIL';	
	//$opt_jenisdokumen['6'] = 'PENDAPATAN';	
	//$opt_jenisdokumen['8'] = 'P F K';	
	$form['formdata']['jenisdokumen'] = array(
		'#type' => 'select',
		'#title' =>  t('Dokumen'),
		'#options' => $opt_jenisdokumen,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $jenisdokumen,
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
		'#description' =>  t('Kata kunci untuk mencari S2PD, bisa nama kegiatan, keperluan, atau nama penerima/pihak ketiga'),
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
