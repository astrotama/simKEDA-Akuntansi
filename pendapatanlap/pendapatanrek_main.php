<?php
function pendapatanrek_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				
				$kodeuk = arg(3);
				$kodek = arg(4);
				$tingkat = arg(5);
				$keyword = arg(6);
				if ($keyword=='kosong') $keyword = '';
				$kodeparent = arg(7);
				
				$pdf = arg(8);
				break;
				
			case 'pdf':
				$pdf = 'pdf';
				$bulan = arg(2);
				
				$kodeuk = arg(3);
				$kodek = arg(4);
				$tingkat = arg(5);
				$keyword = arg(6);
				if ($keyword=='kosong') $keyword = '';
				$kodeparent = arg(7);
				break;

			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kodeuk = 'ZZ';
		$kodek = 'ZZ';
		$tingkat = '3';
		$keyword = '';
		$kodeparent = '';
		$pdf = '';
	}
	
	//drupal_set_message($pdf);
	if ($pdf=='pdf') {
		//drupal_set_title('PENDAPATAN BULAN #' . $bulan);
		$output=getData($bulan,$kodeuk,$kodek,$tingkat, $keyword);
		print_pdf_p($output);
		
		//return $output;
	
	} else {
		$output_form = drupal_get_form('pendapatanrek_main_form') ;

		
		$query = db_select('apbdrekap', 'k')->extend('PagerDefault')->extend('TableSort');

		# get the desired fields from the database
		$query->addExpression('SUM(k.anggaran1/1000)', 'anggaran1x');
		$query->addExpression('SUM(k.anggaran2/1000)', 'anggaran2x');
		$query->addExpression('SUM(k.realisasikum' . $bulan . '/1000)', 'realisasix');
		$query->addExpression('(SUM(k.realisasikum' . $bulan . ')/SUM(k.anggaran2))', 'persen');
		$query->addExpression('COUNT (DISTINCT k.kodeskpd)', 'jumlahskpd');
		$query->condition('k.kodeakun', '4', '=');
		if ($kodeuk !='ZZ') $query->condition('k.kodeskpd', $kodeuk, '=');
		if ($kodek !='ZZ') $query->condition('k.kodekelompok', $kodek, '=');
		if ($keyword !='') $query->condition('k.namarincian', '%' . db_like($keyword) . '%', 'LIKE');
		//$query->groupBy('namaskpd', 'koderincian','namarincian');
		
		if ($tingkat=='5') {

			$header = array (
				array('data' => 'No','width' => '10px', 'valign'=>'top'),
				array('data' => 'Kode', 'field'=> 'koderincian','valign'=>'top'),
				array('data' => 'Uraian', 'field'=> 'namarincian', 'valign'=>'top'),
				array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
				array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasix', 'valign'=>'top'),
				array('data' => 'Persen', 'width' => '10px', 'field'=> 'persen', 'valign'=>'top'),
				array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
				array('data' => '', 'width' => '20px', 'valign'=>'top'),
			);		

			$query->fields('k', array('koderincian','namarincian'));
			$query->groupBy('koderincian');
			$query->orderByHeader($header);
			$query->orderBy('k.koderincian', 'ASC');
			$tingkat_detil = '0';
			
		} else if ($tingkat=='4') {
			$header = array (
				array('data' => 'No','width' => '10px','valign'=>'top'),
				array('data' => 'Kode', 'field'=> 'kodeobyek', 'valign'=>'top'),
				array('data' => 'Uraian', 'field'=> 'namaobyek','valign'=>'top'),
				array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
				array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasix', 'valign'=>'top'),
				array('data' => 'Persen', 'width' => '10px', 'field'=> 'persen', 'valign'=>'top'),
				array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
				array('data' => '', 'width' => '20px', 'valign'=>'top'),
			);

			$query->fields('k', array('kodeobyek','namaobyek'));
			$query->groupBy('kodeobyek');
			$query->orderByHeader($header);
			$query->orderBy('k.kodeobyek', 'ASC');
			$tingkat_detil = '5';
			
		} else if ($tingkat=='3') {
			$header = array (
				array('data' => 'No','width' => '10px', 'valign'=>'top'),
				array('data' => 'Kode', 'field'=> 'kodejenis','valign'=>'top'),
				array('data' => 'Uraian', 'field'=> 'namajenis','valign'=>'top'),
				array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
				array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasix', 'valign'=>'top'),
				array('data' => 'Persen', 'width' => '10px', 'field'=> 'persen', 'valign'=>'top'),
				array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
				array('data' => '', 'width' => '20px', 'valign'=>'top'),
			);

			$query->fields('k', array('kodejenis','namajenis'));
			$query->groupBy('kodejenis');
			$query->orderByHeader($header);
			$query->orderBy('k.kodejenis', 'ASC');
			$tingkat_detil = '4';
			
		} else {
			$header = array (
				array('data' => 'No','width' => '10px', 'valign'=>'top'),
				array('data' => 'Kode', 'field'=> 'kodekelompok', 'valign'=>'top'),
				array('data' => 'Uraian', 'field'=> 'namakelompok','valign'=>'top'),
				array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
				array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasix', 'valign'=>'top'),
				array('data' => 'Persen', 'width' => '10px', 'field'=> 'persen', 'valign'=>'top'),
				array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
				array('data' => '', 'width' => '20px', 'valign'=>'top'),
			);

			$query->fields('k', array('kodekelompok','namakelompok'));
			$query->groupBy('kodekelompok');
			$query->orderByHeader($header);
			$query->orderBy('k.kodekelompok', 'ASC');
			$tingkat_detil = '3';
			
		}
		
		if ($kodeparent!='') $query->condition('k.koderincian', db_like($kodeparent) . '%', 'LIKE');	
		
		//$query->groupBy('namarincian');
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
		
		if ($keyword=='') $keyword = 'kosong';
		$rows = array();
		foreach ($results as $data) {
			$no++;   

			if ($tingkat=='5') {
				$koderek = $data->koderincian;
				$namarek = $data->namarincian;
				
			} else if ($tingkat=='4') {
				$koderek = $data->kodeobyek;
				$namarek = $data->namaobyek;
				
			} else if ($tingkat=='3') {
				$koderek = $data->kodejenis;
				$namarek = $data->namajenis;
			} else {
				$koderek = $data->kodekelompok;
				$namarek = $data->namakelompok;			
			}
			
			//$uri = 'pendapatanrek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodek . '/' . $tingkat . '/' .  $keyword;
			if ($tingkat<'5') {
				//$namarek = l($namarek, 'pendapatanrek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodek . '/' . $tingkat_detil . '/' .  $keyword . '/' . $koderek . $kodeparent , array ('html' => true));
				$namarek = l($namarek, 'pendapatanrek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodek . '/' . $tingkat_detil . '/' .  $keyword . '/' . $koderek, array ('html' => true));
			}
			
			//<font color="red">This is some text!</font>
			$anggaran = apbd_fn($data->anggaran2x);
			
			if ($data->anggaran1x > $data->anggaran2x)
				$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1x) . '</font></p>';
			else if ($data->anggaran1x < $data->anggaran2x)
				$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1x) . '</font></p>';

			$editlink = apbd_button_bukubesar('akuntansi/buku/'. $bulan .'/ZZ/'.$koderek . '/' . $kodeuk);
			//$editlink .= apbd_button_analisis('apbd/chart/rekeningberjalan/' . $bulan . '/' . $koderek . '/' . $kodeuk);
			
			
			$keterangan = l($data->jumlahskpd . ' SKPD <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>' , 'pendapatanrek/skpd/'.$bulan.'/'.$koderek , array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));

			
			//$keterangan .= '<span class="glyphicon glyphicon-star" aria-hidden="true"></span>';

			$rows[] = array(
							array('data' => $no, 'align' => 'right', 'valign'=>'top'),
							array('data' => $koderek,'align' => 'left', 'valign'=>'top'),
							array('data' => $namarek . $keterangan, 'align' => 'left', 'valign'=>'top'),
							array('data' => $anggaran, 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->realisasix), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn1($data->persen*100), 'width' => '20px', 'align' => 'right', 'valign'=>'top'),
							//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)), 'align' => 'right', 'valign'=>'top'),
							array('data' => apbd_fn($data->anggaran2x-$data->realisasix), 'align' => 'right', 'valign'=>'top'),
							$editlink,
						);
		
		}
			

		
		//BUTTON
		//$uri = 'pendapatanrek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodek . '/' . $tingkat . '/' .  $keyword;
		//$btn = apbd_button_print('pendapatanrek/pdf/' . $bulan . '/'.$kodeuk . '/' . $kodek . '/' . $tingkat . '/' .  $keyword);
		//$btn .= "&nbsp;" . apbd_button_excel('');	
		//$btn .= apbd_button_chart('pendapatan/chart/' . $bulan.'/'.$kodeuk . '/jenis_rb');
		
		//$btn = 	l('Analisis<span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>' , 'pendapatan/chart/'.$bulan.'/'.$kodeuk . '/jenis_rb' , array ('html' => true));
		
		$btn = '';
		
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');
		return drupal_render($output_form) . $btn . $output . $btn;
	
	}
	//return drupal_render($output_form) . $btn . $output . $btn;
}

function getData($bulan,$kodeuk,$kodek,$tingkat, $keyword){
	
	$header = array (
		array('data' => 'No','height'=>'20px', 'width' => '30px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Kode','height'=>'20px', 'width' => '70px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Uraian','height'=>'20px', 'width' => '265px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Anggaran','height'=>'20px', 'width' => '100px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Realisasi','height'=>'20px', 'width' => '100px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => '%','height'=>'20px', 'width' => '40px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		
	);

	
	$query = db_select('apbdrekap', 'k');//->extend('PagerDefault')->extend('TableSort');

	# get the desired fields from the database
	$query->addExpression('SUM(k.anggaran1)', 'anggaran1x');
	$query->addExpression('SUM(k.anggaran2)', 'anggaran2x');
	$query->addExpression('SUM(k.realisasikum' . $bulan . ')', 'realisasix');
	$query->condition('k.kodeakun', '4', '=');
	if ($kodeuk !='ZZ') $query->condition('k.kodeskpd', $kodeuk, '=');
	if ($kodek !='ZZ') $query->condition('k.kodekelompok', $kodek, '=');
	if ($keyword !='') $query->condition('k.namarincian', '%' . db_like($keyword) . '%', 'LIKE');
	//$query->groupBy('namaskpd', 'koderincian','namarincian');
	
	if ($tingkat=='5') {
		$query->fields('k', array('koderincian','namarincian'));
		$query->groupBy('koderincian');
		$query->orderBy('k.koderincian', 'ASC');
		$tingkat_detil = '0';
		
	} else if ($tingkat=='4') {
		$query->fields('k', array('kodeobyek','namaobyek'));
		$query->groupBy('kodeobyek');
		$query->orderBy('k.kodeobyek', 'ASC');
		$tingkat_detil = '5';
		
	} else if ($tingkat=='3') {
		$query->fields('k', array('kodejenis','namajenis'));
		$query->groupBy('kodejenis');
		$query->orderBy('k.kodejenis', 'ASC');
		$tingkat_detil = '4';
		
	} else {
		$query->fields('k', array('kodekelompok','namakelompok'));
		$query->groupBy('kodekelompok');
		$query->orderBy('k.kodekelompok', 'ASC');
		$tingkat_detil = '3';
		
	}	
	
	//dpq($query);
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	$rows = array();
	foreach ($results as $data) {
		$no++;  

		if ($tingkat=='5') {
			$koderek = $data->koderincian;
			$namarek = $data->namarincian;
			
		} else if ($tingkat=='4') {
			$koderek = $data->kodeobyek;
			$namarek = $data->namaobyek;
			
		} else if ($tingkat=='3') {
			$koderek = $data->kodejenis;
			$namarek = $data->namajenis;
		} else {
			$koderek = $data->kodekelompok;
			$namarek = $data->namakelompok;			
		}		
		//<font color="red">This is some text!</font>
		$anggaran = apbd_fn($data->anggaran2x);
		
		/*if ($data->anggaran1x > $data->anggaran2x)
			$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1x) . '</font></p>';
		else if ($data->anggaran1x < $data->anggaran2x)
			$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1x) . '</font></p>';
		*/
		
		
		//$keterangan .= '<span class="glyphicon glyphicon-star" aria-hidden="true"></span>';

		$rows[] = array(
						array('data' => $no, 'width' => '30px','valign'=>'middle', 'align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => $koderek, 'width' => '70px','valign'=>'middle', 'align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => $namarek , 'width' => '265px','valign'=>'middle', 'align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => $anggaran, 'width' => '100px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn($data->realisasix), 'width' => '100px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)), 'width' => '40px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						
					);
	
	}
		$rows[] = array(
						array('data' => '', 'width' => '605px','valign'=>'middle', 'align'=>'center','style'=>'border-top:1px solid black;'),
					);
		
		
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
	
}

function pendapatanrek_main_form_submit($form, &$form_state) {
	$bulan=  $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodek = $form_state['values']['kodek'];
	$tingkat = $form_state['values']['tingkat'];
	$keyword = $form_state['values']['keyword'];
	
	//$namarek ='pendapatanrek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodek . '/' . $tingkat_detil . '/' .  $keyword . '/' . $koderek . $kodeparent , array ('html' => true));
	
	$uri = 'pendapatanrek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodek . '/' . $tingkat . '/' .  $keyword;
	drupal_goto($uri);
	
}


function pendapatanrek_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$keyword = '';
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('m');
	$tingkat = '3';
	$kodek = 'ZZ';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$kodek = arg(4);
		$tingkat = arg(5);
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
	}
	
	
	if ($kodek=='41') 
		$kelompok = '|PENDAPATAN ASLI DAERAH';
	else if ($kodek=='42') 
		$kelompok = '|DANA PERIMBANGAN';
	else if ($kodek=='43') 
		$kelompok = '|LAIN-LAIN PENDAPATAN YANG SAH';
	else
		$kelompok = '|SEMUA PENDAPATAN';

	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=> '<p>' . $bulan . $namasingkat . $kelompok . '</p>' . '<p><em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em></p>',
		'#title'=> $bulan . $namasingkat . $kelompok . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	

	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
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
	$subquery = db_select('pendapatan', 'ukp');
	$subquery->fields('ukp', array('kodeuk'));
	
	$query = db_select('unitkerja', 'p');
	$query->fields('p', array('namasingkat','kodeuk'));
	$query->condition('p.kodeuk', $subquery, 'IN');
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

	$form['formdata']['kodek']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kodek,
		
		'#options' => array(	
			 'ZZ' => t('00-SEMUA'), 	
			 '41' => t('41-PENDAPATAN ASLI DAERAH'), 	
			 '42' => t('42-DANA PERIMBANGAN'),
			 '43' => t('43-LAIN-LAIN PENDAPATAN YANG SAH'),	
		   ),
	);		

	$form['formdata']['tingkat']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Rekening'), 
		'#default_value' => $tingkat,
		
		'#options' => array(	
			 '2' => t('2-KELOMPOK'),
			 '3' => t('3-JENIS'), 	
			 '4' => t('4-OBYEK'),
			 '5' => t('5-RINCIAN OBYEK'),	
		   ),
	);		

	$form['formdata']['keyword'] = array(
		'#type' => 'textfield',
		'#title' =>  t('Kata Kunci'),
		'#description' =>  t('Kata kunci untuk mencari rekening pendapatanrek, diisi dengan menuliskan sebagian nama rekening pendapatanrek'),
		'#default_value' => $keyword, 
	);	
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}


?>
