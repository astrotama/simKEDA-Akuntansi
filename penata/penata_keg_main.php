<?php
function penata_keg_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
		$tahun = arg(2);
		$kodekeg = arg(3);

		$query = db_select('kegiatan'.$tahun, 'p');
		$query->fields('p', array('kegiatan'))
			  ->condition('kodekeg',$kodekeg,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$title= $data->kegiatan;
			}
		}		
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$kodekeg = 'ZZ';
		
	}
	
	drupal_set_title('Register SP2D || '. $title);
	
	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'No.SP2D', 'width' => '60px', 'valign'=>'top'),
		array('data' => 'Tgl. SP2D', 'width' => '90px', 'valign'=>'top'),
		array('data' => 'Uraian',  'valign'=>'top'),
		array('data' => 'Penerima', 'valign'=>'top'),
		array('data' => 'Rekening', 'valign'=>'top'),
		array('data' => 'NPWP', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '100px',  'valign'=>'top'),
		
	);

	$query = db_select('dokumen' . $tahun, 'k')->extend('TableSort');
	# get the desired fields from the database
	$query->fields('k', array('kodeuk', 'dokid', 'sp2dno','sp2dtgl','keperluan', 'penerimanama', 'penerimabanknama', 'penerimabankrekening', 'penerimanpwp', 'jumlah'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('k.kodekeg', $kodekeg, '=');
	$query->orderByHeader($header);
	$query->orderBy('k.sp2dtgl', 'ASC');
	//$query->limit($limit);
	//drupal_set_message($ne);	
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

	
	$total=0;	
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$sp2dno = l($data->sp2dno, 'penata/edit/'. $tahun . '/'. $data->dokid, array ('html' => true));

		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $sp2dno, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_format_tanggal_pendek($data->sp2dtgl), 'align' => 'center', 'valign'=>'top'),
						array('data' => $data->keperluan,  'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimanama, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimabankrekening . '<p>' . $data->penerimabanknama . '</p>', 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->penerimanpwp, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					$total+=$data->jumlah;
	}
	
	$rows[] = array(
						array('data' => 'JUMLAH', 'colspan'=>'7', 'align' => 'center', 'valign'=>'top'),
						array('data' => apbd_fn($total), 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	//$output .= theme('pager');
	return $btn . $output . $btn;
}




?>
