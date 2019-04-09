<?php
function belanjarek_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
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
				$kodej = arg(4);
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
		$kodej = 'ZZ';
		$tingkat = '3';
		$keyword = '';
		$kodeparent = '';
	}
	
	//drupal_set_title('BELANJA');
	
	$output_form = drupal_get_form('belanjarek_main_form');
	
	$query = db_select('anggperkeg', 'a')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('kegiatanskpd', 'k', 'a.kodekeg=a.kodekeg')

	# get the desired fields from the database
	$query->addExpression('SUM(a.anggaran)', 'anggaran');
	$query->addExpression('COUNT (DISTINCT a.kodeuk)', 'jumlahskpd');
	$query->addExpression('COUNT (DISTINCT a.kodekeg)', 'jumlahkegiatan');
	

	if ($tingkat=='5') {
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => 'Kode','field'=> 'kodero', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran', 'valign'=>'top'),
			array('data' => 'Realisasi', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Persen', 'field'=> 'persen', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
			array('data' => '', 'width' => '20px', 'valign'=>'top'),
		);
		
		$query->innerJoin('rincianobyek', 'ro', 'a.kodero=ro.kodero')
		
		$query->fields('ro', array('kodero','uraian'));
		$query->groupBy('a.kodero');
		$query->orderByHeader($header);
		$query->orderBy('a.kodero', 'ASC');

		
	} else if ($tingkat=='4') {
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => 'Kode','field'=> 'kodeo', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran', 'valign'=>'top'),
			array('data' => 'Realisasi', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Persen', 'field'=> 'persen', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
			array('data' => '', 'width' => '20px', 'valign'=>'top'),
		);
		
		$query->innerJoin('obyek', 'o', 'LEFT(a.kodero, 5)=ro.kodeo')
		$query->fields('o', array('kodeo','uraian'));
		$query->groupBy('o.kodeo');
		$query->orderByHeader($header);
		$query->orderBy('o.kodeo', 'ASC');
		
	} else if ($tingkat=='3') {
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => 'Kode','field'=> 'kodej', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran', 'valign'=>'top'),
			array('data' => 'Realisasi', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Persen', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
			array('data' => '', 'width' => '20px', 'valign'=>'top'),
		);
		
		$query->innerJoin('jenis', 'j', 'LEFT(a.kodero, 3)=j.kodej')
		
		$query->fields('j', array('kodej','uraian'));
		$query->groupBy('j.kodej');
		$query->orderByHeader($header);
		$query->orderBy('j.kodej', 'ASC');
		
	} else {
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => 'Kode','field'=> 'kodek', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran', 'valign'=>'top'),
			array('data' => 'Realisasi', 'width' => '80px', 'valign'=>'top'),
			array('data' => 'Persen', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'A-R', 'width' => '50px', 'valign'=>'top'),
			array('data' => '', 'width' => '20px', 'valign'=>'top'),
		);
		
		$query->innerJoin('kelompok', 'l', 'LEFT(a.kodero, 2=l.kodek')
		
		$query->fields('l', array('kodek','uraian'));
		$query->groupBy('l.kodekelompok');
		$query->orderByHeader($header);
		$query->orderBy('k.kodekelompok', 'ASC');
		
	}
	
	if ($kodeuk !='ZZ') $query->condition('k.kodeuk', $kodeuk, '=');
	if ($kodej !='ZZ') $query->condition('a.kodero', db_like($kodej) . '%', 'LIKE');
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
			$koderek = $data->kodero;
			
			$tingkat_detil = '0';
			
		} else if ($tingkat=='4') {
			$koderek = $data->kodeo;
			$tingkat_detil = '5';
			
		} else if ($tingkat=='3') {
			$koderek = $data->kodej;
			$tingkat_detil = '4';
			
		} else {
			$koderek = $data->kodek;
			$tingkat_detil = '3';
			
		}
		$namarek = $data->uraian;

		//$uri = 'belanjarek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodej . '/' . $tingkat . '/' . $keyword;
		if ($tingkat<'5')
			$namarek = l($namarek, 'belanjarek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodej . '/' . $tingkat_detil . '/' . $keyword . '/' . $koderek , array ('html' => true));
		
		//<font color="red">This is some text!</font>
		$anggaran = apbd_fn($data->anggaran);
		
		$editlink = apbd_button_bukubesar('akuntansi/buku/'. $bulan .'/ZZ/'.$koderek . '/' . $kodeuk);
		
		//$bulan, $koderincian, $kodeskpd		
		//$editlink .= apbd_button_analisis('apbd/chart/rekeningberjalan/' . $bulan . '/' . $data->koderincian . '/' . $kodeuk);


		$keterangan = l($data->jumlahkegiatan . ' Kegiatan <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>', 'belanjarek/kegiatan/'.$bulan.'/'.$koderek . '/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'text-info pull-right')));

		if ($kodeuk=='ZZ') 
			$keterangan .= l($data->jumlahskpd . ' SKPD&nbsp;' , 'belanjarek/skpd/'.$bulan.'/'.$koderek , array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));

		
		//$keterangan .= '<span class="glyphicon glyphicon-star" aria-hidden="true"></span>';

		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $koderek, 'align' => 'left', 'valign'=>'top'),
						array('data' => $namarek . $keterangan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $anggaran, 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->realisasix), 'align' => 'right', 'valign'=>'top'),
						//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)), 'width' => '20px', 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1($data->persen*100), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->anggaran2x - $data->realisasix), 'align' => 'right', 'valign'=>'top'),
						$editlink,
					);
	} 
	//BUTTON	
	//$btn = apbd_button_print('/belanjarek/filter/'.$bulan.'/'.$kodeuk.'/'.$kodej.'/'.$keyword.'/pdf');
	//$btn .= "&nbsp;" . apbd_button_excel('');	
	//$btn .= apbd_button_chart('belanja/chart/' . $bulan.'/'.$kodeuk . '/SEMUA/SEMUA/total_kb');
	
	$btn = '';
	
	if(arg(6)=='pdf'){
		  
		  $output = GenFormContent($bulan,$kodeuk,$kodej,$keyword);
		  print_pdf_p($output);
	}
	else{
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');
		return drupal_render($output_form) . $btn . $output . $btn;
	}
	
	
}

function GenFormContent($bulan,$kodeuk,$kodej,$keyword){
	$header = array (
			array('data' => 'No','height'=>'20px', 'align'=>'center','width' => '40px', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Kode','width' => '100px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Uraian','width' => '220px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Anggaran','width' => '100px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Realisasi','width' => '100px', 'align'=>'center',  'valign'=>'top','style'=>'border:1px solid black'),
			array('data' => 'Prsn', 'width' => '50px', 'align'=>'center',  'valign'=>'top','style'=>'border:1px solid black'),
			//array('data' => 'Realisasi', 'width' => '100px', 'align'=>'center',  'valign'=>'top','style'=>'border:1px solid black'),
			//array('data' => 'Prsn', 'width' => '100px', 'align'=>'center', 'valign'=>'top','style'=>'border:1px solid black'),
			
		);
	/*$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode','field'=> 'kodero', 'valign'=>'top'),
		array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
		array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasix', 'valign'=>'top'),
		array('data' => 'Prsn', 'width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);*/
	
	$query = db_select('apbdrekap', 'k');//->extend('PagerDefault')->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('koderincian','namarincian'));
	$query->addExpression('SUM(k.anggaran1)', 'anggaran1x');
	$query->addExpression('SUM(k.anggaran2)', 'anggaran2x');
	$query->addExpression('SUM(k.realisasi)', 'realisasix');
	$query->addExpression('COUNT (DISTINCT k.kodeskpd)', 'jumlahskpd');
	$query->addExpression('COUNT (DISTINCT k.kodekeg)', 'jumlahkegiatan');
	$query->condition('k.kodeakun', '5', '=');
	if ($kodeuk !='ZZ') $query->condition('k.kodeskpd', $kodeuk, '=');
	if ($kodej !='ZZ') $query->condition('k.kodejenis', $kodej, 'LIKE');
	if ($keyword !='') $query->condition('k.namarincian', '%' . db_like($keyword) . '%', 'LIKE');
	//$query->groupBy('namaskpd', 'koderincian','namarincian');
	$query->groupBy('koderincian');
	$query->groupBy('namarincian');
	//$query->orderByHeader($header);
	$query->orderBy('k.koderincian', 'ASC');
	//$query->limit($limit);
	
	//drupal_set_message($query);
	
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
		
		//<font color="red">This is some text!</font>
		$anggaran = apbd_fn($data->anggaran2x);
		
		if ($data->anggaran1x > $data->anggaran2x)
			$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1x) . '</font></p>';
		else if ($data->anggaran1x < $data->anggaran2x)
			$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1x) . '</font></p>';

		$editlink = l('Buku Besar', 'akuntansi/buku/'. $bulan .'/ZZ/'.$data->koderincian . '/' . $kodeuk, array ('html' => true, 'attributes'=> array ('class'=>'btn btn-success btn-xs btn-block')));


		$keterangan = l($data->jumlahkegiatan . ' Kegiatan <span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>', 'belanjarek/kegiatan/'.$bulan.'/'.$data->koderincian . '/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'text-info pull-right')));

		if ($kodeuk=='ZZ') 
			$keterangan .= l($data->jumlahskpd . ' SKPD&nbsp;' , 'belanjarek/skpd/'.$bulan.'/'.$data->koderincian , array ('html' => true, 'attributes'=> array ('class'=>'text-success pull-right')));

		
		//$keterangan .= '<span class="glyphicon glyphicon-star" aria-hidden="true"></span>';
		$rows[] = array(
						array('data' => $no, 'align' => 'right','width'=>'40px', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->koderincian, 'align' => 'left','width'=>'100px', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $data->namarincian, 'align' => 'left','width'=>'220px', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => $anggaran, 'align' => 'right','width'=>'100px', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_fn($data->realisasix),'width' => '100px', 'align' => 'right', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2x, $data->realisasix)),  'align' => 'right','width'=>'50px', 'valign'=>'top','style'=>'border-right:1px solid black;border-bottom:0.1px solid grey;'),
						//array('data' => apbd_fn($data->realisasi),'width'=>'100px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black'),
						//array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2, $data->realisasi)),'width'=>'100px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;border-right:1px solid black'),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
		
	}
	$rows[] = array(
						array('data' => '', 'align' => 'right','width'=>'610px', 'valign'=>'top','style'=>'border-top:1px solid black;'),
						
					);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
	
}

function belanjarek_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodej = $form_state['values']['kodej'];
	$keyword = $form_state['values']['keyword'];
	$tingkat = $form_state['values']['tingkat'];

	$uri = 'belanjarek/filter/' . $bulan . '/'.$kodeuk . '/' . $kodej . '/' . $tingkat . '/' . $keyword;
	drupal_goto($uri);
	
}


function belanjarek_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$keyword = '';
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('m');
	$kodej = 'ZZ';
	$tingkat = '3';
	$jenis = '|SEMUA';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		$kodeuk = arg(3);
		$kodej = arg(4);
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
	
	if ($kodej!='ZZ') {
		if ($kodej=='511') 
			$jenis = '|GAJI';
		else if ($kodej=='513') 	
			$jenis = '|SUBSIDI';
		else if ($kodej=='514')
			$jenis = '|HIBAH';	
		else if ($kodej=='515')
 			$jenis = '|BANTUAN SOSIAL';
		else if ($kodej=='516')
 			$jenis = '|BAGI HASIL';
		else if ($kodej=='517')
			$jenis = '|BANTUAN KEUANGAN';
		else if ($kodej=='518')
			$jenis = '|TIDAK TERDUGA';
		else if ($kodej=='521')
			$jenis = '|PEGAWAI';
		else if ($kodej=='522') 	
			$jenis = '|BARANG JASA';
		else if ($kodej=='523')	
			$jenis = '|MODAL';
		
	}

	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . $namasingkat . $jenis . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
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

	$form['formdata']['kodej']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kodej,
		
		'#options' => array(	
			 'ZZ' => t('SEMUA'), 	
			 '511' => t('GAJI'), 	
			 '513' => t('SUBSIDI'), 
			 '514' => t('HIBAH'), 
			 '515' => t('BANTUAN SOSIAL'), 
			 '516' => t('BAGI HASIL'), 
			 '517' => t('BANTUAN KEUANGAN'), 
			 '518' => t('TIDAK TERDUGA'), 
			 '521' => t('PEGAWAI'),
			 '522' => t('BARANG JASA'),
			 '523' => t('MODAL'),	
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
		'#description' =>  t('Kata kunci untuk mencari rekening belanja, diisi dengan menuliskan sebagian nama rekening belanja'),
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


