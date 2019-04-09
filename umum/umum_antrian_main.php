<?php
function umum_antrian_main($arg=NULL, $nama=NULL) {
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
				
				$kodeuk = arg(3);
				$bulan = arg(4);

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
		else
			$kodeuk = 'ZZ';
		//$bulan = date('m');
		$bulan = '0';
	}
	
	if (isUserSKPD())
		$isskpd = true;
	else
		$isskpd = false; 
	
	//drupal_set_message(apbd_getkodejurnal('90'));
	
	//db_set_active('bendahara');
	
	$output_form = drupal_get_form('umum_antrian_main_form');
	if($isskpd==true){
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'No. SPJ','width' => '80px','field'=> 'spjno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'total',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
		
		

		$query = db_select('bendahara', 'b')->extend('PagerDefault')->extend('TableSort');
		//$query = db_select('bendahara', 'b');
		$query->innerJoin('unitkerja', 'uk', 'b.kodeuk=uk.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'b.kodekeg=k.kodekeg');
		$query->fields('b', array('spjno', 'tanggal', 'bendid', 'jenis', 'keperluan', 'total', 'jurnalsudah', 'jurnalsudahuk'));
		$query->fields('uk', array('namasingkat'));
		$query->fields('k', array('kegiatan'));
		
		$or = db_or();
		$or->condition('b.jenis', db_like('ret') . '%', 'LIKE');
		$or->condition('b.jenis', 'pindahbuku', '=');
		
		//$query->condition('b.jenis', db_like('ret') . '%', 'LIKE');
		$query->condition($or);
		
		if ($isskpd)
			$query->condition('b.jurnalsudahuk', '0', '=');
		else
			$query->condition('b.jurnalsudah', '0', '=');
		
		$query->condition('b.kodeuk', $kodeuk, '=');
		
		$query->orderByHeader($header);
		$query->orderBy('b.tanggal', 'ASC');
		$query->orderBy('b.bendid', 'ASC');
		//$query->range(0, $limit);
		
		$query->limit($limit);
			
		//dpq($query);
		
		# execute the query
		$results = $query->execute();
		
		//$results = $res->fetchAll();
			 
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
			
			
			if ($isskpd) {
				if($data->jurnalsudahuk=='1'){
					$jurnalsudah = apbd_icon_jurnal_sudah();
					$editlink = apbd_button_jurnal('umum_antrian/jurnaledit/' . $data->jurnalid);
				
				} else {
					$jurnalsudah = apbd_icon_jurnal_belum();
					if ($data->jenis=='ret-kas')
						$editlink = apbd_button_jurnalkan('umum/jurnalkas/' . $data->bendid);
					else
						$editlink = apbd_button_jurnalkan('umum/jurnalkeg/' . $data->bendid);
				}
				
			} else {
				if($data->jurnalsudah=='1'){
					$jurnalsudah = apbd_icon_jurnal_sudah();
					$editlink = apbd_button_jurnal('umum_antrian/jurnaledit/' . $data->jurnalid);
				
				} else {
					$jurnalsudah = apbd_icon_jurnal_belum();
					if ($data->jenis=='ret-kas')
						$editlink = apbd_button_jurnalkan('umum/jurnalkas/' . $data->bendid);
					else
						$editlink = apbd_button_jurnalkan('umum/jurnalkeg/' . $data->bendid);
				}
			}
			
			if ($data->kegiatan=='')
				$kegiatan = 'Non Kegiatan (Kas)';
			else
				$kegiatan = $data->kegiatan;
			
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => $jurnalsudah,'align' => 'right', 'valign'=>'top'),
							//array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
							array('data' => $data->spjno, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal),  'align' => 'center', 'valign'=>'top'),
							array('data' => $kegiatan, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->keperluan, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->total),'align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		}
	}else{
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'No. SPJ','width' => '80px','field'=> 'spjno', 'valign'=>'top'),
			array('data' => 'Tanggal', 'width' => '90px','field'=> 'tanggal', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Keperluan', 'field'=> 'keperluan', 'valign'=>'top'),
			array('data' => 'Jumlah', 'width' => '80px', 'field'=> 'total',  'valign'=>'top'),
			array('data' => '', 'width' => '60px', 'valign'=>'top'),
			
		);
		
		

		$query = db_select('bendahara', 'b')->extend('PagerDefault')->extend('TableSort');
		//$query = db_select('bendahara', 'b');
		$query->innerJoin('unitkerja', 'uk', 'b.kodeuk=uk.kodeuk');
		$query->leftJoin('kegiatanskpd', 'k', 'b.kodekeg=k.kodekeg');
		$query->fields('b', array('spjno', 'tanggal', 'bendid', 'jenis', 'keperluan', 'total', 'jurnalsudah', 'jurnalsudahuk'));
		$query->fields('uk', array('namasingkat'));
		$query->fields('k', array('kegiatan'));
		$query->condition('b.jenis', db_like('ret') . '%', 'LIKE');
		if ($isskpd)
			$query->condition('b.jurnalsudahuk', '0', '=');
		else
			$query->condition('b.jurnalsudah', '0', '=');
		
		if ($kodeuk != 'ZZ') $query->condition('b.kodeuk', $kodeuk, '=');
		
		$query->orderByHeader($header);
		$query->orderBy('b.tanggal', 'ASC');
		$query->orderBy('b.bendid', 'ASC');
		//$query->range(0, $limit);
		
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
			
			
			if ($isskpd) {
				if($data->jurnalsudahuk=='1'){
					$jurnalsudah = apbd_icon_jurnal_sudah();
					$editlink = apbd_button_jurnal('umum_antrian/jurnaledit/' . $data->jurnalid);
				
				} else {
					$jurnalsudah = apbd_icon_jurnal_belum();
					if ($data->jenis=='ret-kas')
						$editlink = apbd_button_jurnalkan('umum/jurnalkas/' . $data->bendid);
					else
						$editlink = apbd_button_jurnalkan('umum/jurnalkeg/' . $data->bendid);
				}
				
			} else {
				if($data->jurnalsudah=='1'){
					$jurnalsudah = apbd_icon_jurnal_sudah();
					$editlink = apbd_button_jurnal('umum_antrian/jurnaledit/' . $data->jurnalid);
				
				} else {
					$jurnalsudah = apbd_icon_jurnal_belum();
					if ($data->jenis=='ret-kas')
						$editlink = apbd_button_jurnalkan('umum/jurnalkas/' . $data->bendid);
					else
						$editlink = apbd_button_jurnalkan('umum/jurnalkeg/' . $data->bendid);
				}
			}
			
			if ($data->kegiatan=='')
				$kegiatan = 'Non Kegiatan (Kas)';
			else
				$kegiatan = $data->kegiatan;
			
			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => $jurnalsudah,'align' => 'right', 'valign'=>'top'),
							array('data' => $data->namasingkat,  'align' => 'left', 'valign'=>'top'),
							array('data' => $data->spjno, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_format_tanggal_pendek($data->tanggal),  'align' => 'center', 'valign'=>'top'),
							array('data' => $kegiatan, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->keperluan, 'align' => 'left', 'valign'=>'top'),
							array('data' => apbd_fn($data->total),'align' => 'right', 'valign'=>'top'),
							$editlink,
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							
						);
		}
	}
	
	
	//db_set_active();
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form) . $output;
	
	
}


function getData($kodeuk,$bulan,$jenisdokumen,$keyword){

}

function umum_antrian_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['skpd'];
	$bulan = $form_state['values']['bulan'];
	
	$uri = 'umum/antrian/filter/' . $kodeuk . '/' . $bulan ;
	drupal_goto($uri);
	
}

 
function umum_antrian_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	//$bulan = date('m');
	$bulan = '0';
	
	if(arg(3)!=null){
		
		$kodeuk = arg(3);
		$bulan=arg(4);

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
	if(!isUserSKPD()){
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

	$form['formdata']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-success btn-sm')),
	);
	return $form;
}



?>
