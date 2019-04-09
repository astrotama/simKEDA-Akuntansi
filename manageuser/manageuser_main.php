<?php
function manageuser_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
	
	$pdf = '';
	if ($arg) {
		switch($arg) {
			case 'filter':
				$bulan = arg(2);
				$kodek = arg(3);
				break;

			case 'pdf':
				$pdf = 'pdf';
				$bulan = arg(2);
				$kodek = arg(3);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kodek = 'ZZ';
		$keyword = '';
	}
	
	//drupal_set_message($bulan);
	//drupal_set_message($kodeuk);
	
	//drupal_set_title('manageuser #' . $bulan);
	
	if ($pdf=='pdf') {
		$output = getData($bulan, $kodek);
		return $output;
		
	} else {
		$output_form = drupal_get_form('manageuser_main_form');
		$header = array (
			array('data' => 'No','width' => '10px', 'valign'=>'top'),
			array('data' => 'Username', 'width' => '180px',  'field'=> 'namaskpd', 'valign'=>'top'), 
			array('data' => 'email', 'width' => '80px', 'field'=> 'anggaran2x', 'valign'=>'top'),
			array('data' => 'Edit', 'width' => '30px', 'field'=> 'anggaran2x', 'valign'=>'top'),
			
		);
		
		$query = db_select('users', 'k')->extend('PagerDefault')->extend('TableSort');

		# get the desired fields from the database
		$query->fields('k', array('uid','name','mail'));
		$query->orderByHeader($header);
		$query->orderBy('k.name', 'ASC');
		$query->limit($limit);	
			
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
			$editlink = apbd_button_user('');
			//<font color="red">This is some text!</font>
			
			$rows[] = array(
							array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
							array('data' => $data->name, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->mail, 'align' => 'right', 'valign'=>'top'),
							$editlink,
						);
		}

		//BUTTON
		//$btn = apbd_button_print('manageuser/pdf/' . $bulan.'/' . $kodek);
		//$btn .= "&nbsp;" . apbd_button_excel('');	
		//$btn .= apbd_button_chart('manageuser/chart/' . $bulan.'/ZZ/jenis_rb');
		
		$btn ='';
		
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		$output .= theme('pager');

		return drupal_render($output_form) . $btn . $output . $btn;
		//return drupal_render($output_form) . $btn . $output . $btn;
	}
}

function getData($bulan, $kodero){
	
	$header = array (
		array('data' => 'No','height'=>'20px', 'width' => '30px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Kode','height'=>'20px', 'width' => '70px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'SKPD','height'=>'20px', 'width' => '265px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Anggaran','height'=>'20px', 'width' => '100px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Realisasi','height'=>'20px', 'width' => '100px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		array('data' => '%','height'=>'20px', 'width' => '40px','valign'=>'middle', 'align'=>'center','style'=>'border:1px solid black;'),
		
	);

	
	//drupal_set_message($bulan);
	$query = db_select('apbdrekap', 'r');

	# get the desired fields from the database
	$query->fields('r', array('kodeskpd', 'namaskpd'));
	
	$query->addExpression('SUM(anggaran2)', 'anggaran');
	$query->addExpression('SUM(realisasikum' . $bulan . ')', 'realisasi');
	
	//condition
	$query->condition('koderincian',db_like($kodero) . '%','LIKE');

	//grouping
	$query->groupBy('kodeskpd');
	$query->groupBy('namaskpd');
	
	//$query->orderByHeader($header);
	$query->orderByHeader($header);
	$query->orderBy('kodeskpd', 'ASC');
	
	//dpq($query);
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	$rows = array();
	foreach ($results as $data) {
		$no++;  



		$rows[] = array(
						array('data' => $no, 'width' => '30px','valign'=>'middle', 'align'=>'center','style'=>'border-right:1px solid black;border-left:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => $data->kodeskpd, 'width' => '70px','valign'=>'middle', 'align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => $data->namaskpd , 'width' => '265px','valign'=>'middle', 'align'=>'left','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn($data->anggaran), 'width' => '100px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn($data->realisasi), 'width' => '100px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran, $data->realisasi)), 'width' => '40px','valign'=>'middle', 'align'=>'right','style'=>'border-right:1px solid black;border-bottom:0.1px dashed #cdcdcd;'),
						
					);
	
	}
		$rows[] = array(
						array('data' => '', 'width' => '605px','valign'=>'middle', 'align'=>'center','style'=>'border-top:1px solid black;'),
					);
		
		
	
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
	
}


function manageuser_main_form_submit($form, &$form_state) {
	$bulan= $form_state['values']['bulan'];
	$kodek = $form_state['values']['kodek'];
	
	$uri = 'manageuser/filter/' . $bulan.'/' . $kodek;
	drupal_goto($uri);	
}

function manageuser_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$keyword = '';
	$namasingkat = '|SELURUH SKPD';
	$bulan = date('m');
	$kodek = 'ZZ';
	
	if(arg(2)!=null){
		
		$bulan = arg(2);
		
		$kodek = arg(3);
		$keyword = arg(4);
	}
	
	if ($kodek=='41') 
		$kelompok = '|manageuser ASLI DAERAH';
	else if ($kodek=='42') 
		$kelompok = '|DANA PERIMBANGAN';
	else if ($kodek=='43') 
		$kelompok = '|LAIN-LAIN manageuser YANG SAH';
	else
		$kelompok = '|SEMUA manageuser';

	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		//'#title'=> '<p>' . $bulan . $namasingkat . $kelompok . '</p>' . '<p><em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em></p>',
		'#title'=> $bulan . $kelompok . '<em><small class="text-info pull-right">klik disini utk menampilkan/menyembunyikan pilihan data</small></em>',
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

	$form['formdata']['kodek']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kodek,
		
		'#options' => array(	
			 'ZZ' => t('SEMUA'), 	
			 '41' => t('manageuser ASLI DAERAH'), 	
			 '42' => t('DANA PERIMBANGAN'),
			 '43' => t('LAIN-LAIN manageuser YANG SAH'),	
		   ),
	);		

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	
	$form['formdata']['submitx'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

?>
