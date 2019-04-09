<?php
function jurnal_edit_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	drupal_add_css('files/css/textfield.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
		$nntitle ='';
		$tahun = arg(2);
		
		$dokid = arg(3);
				
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$dokid = '';
		
	}

	//INFO SP2D
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('sp2dno','sp2dtgl', 'jumlah', 'jenisdokumen'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dno= $data->sp2dno;
		$sp2dtgl= $data->sp2dtgl;
		$jumlah = $data->jumlah;
		$jenisdokumen = $data->jenisdokumen;
	}
	
	$arrtgl=explode('-',$sp2dtgl);
	$tanggal=$arrtgl[2].'-'.$arrtgl[1].'-'.$arrtgl[0];	
	
	drupal_set_title('SP2D No : ' . $sp2dno . ', Tanggal : ' . $tanggal);
	
	//REKENING
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'width' => '80px','valign'=>'top'),
		array('data' => 'Rekening', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '90px','valign'=>'top'),
		
		
	);
	
	$query = db_select('dokumenrekening' , 'k')->extend('TableSort');

	
	# get the desired fields from the database
	$query->fields('k', array('uraian', 'kodero','jumlah'));
	$query->condition('k.dokid', $dokid, '=');
	$query->orderByHeader($header);
	$query->orderBy('k.kodero', 'ASC');
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		//drupal_set_message($dokid);
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					$total+=$data->jumlah;
	}
	
	if ($no==0) {
		$no++;
		$str_jenis ='';
		if ($jenisdokumen==2) $str_jenis = 'Tambahan ';
		$rows[] = array(
			array('data' => $no, 'align' => 'right', 'valign'=>'top'),
			array('data' => '00000000', 'align' => 'left', 'valign'=>'top'),
			array('data' => $str_jenis . 'Uang Persediaan (UP)', 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($jumlah), 'align' => 'right', 'valign'=>'top'),
		);
		
	}
	
	$rows[] = array(
				array('data' => 'TOTAL', 'width' => '10px','colspan'=>'3', 'align' => 'center', 'valign'=>'top'),
				array('data' => apbd_fn($total), 'align' => 'right', 'valign'=>'top'),
				);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));

	//POTONGAN
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'width' => '80px','valign'=>'top'),
		array('data' => 'Potongan', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '100px','valign'=>'top'),
		
		
	);
	
	$query = db_select('dokumenpotongan' , 'k')->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('uraian', 'nourut','jumlah'));
	$query->condition('k.dokid', $dokid, '=');
	$query->orderByHeader($header);
	$query->orderBy('k.nourut', 'ASC');
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->nourut, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					$total+=$data->jumlah;
	}
	
	if ($total==0) {
		$rows[] = array(
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => 'Tidak ada potongan', 'align' => 'left', 'valign'=>'top'),
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);		
	}
	$rows[] = array(
				array('data' => 'TOTAL', 'width' => '10px','colspan'=>'3', 'align' => 'center', 'valign'=>'top'),
				array('data' => apbd_fn($total), 'align' => 'right', 'valign'=>'top'),
				);					
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));

	//PAJAK
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode', 'width' => '80px','valign'=>'top'),
		array('data' => 'Pajak', 'valign'=>'top'),
		array('data' => 'Jumlah', 'width' => '100px','valign'=>'top'),
		
		
	);
	
	$query = db_select('dokumenpajak' , 'k')->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('uraian', 'kode','jumlah'));
	$query->condition('k.dokid', $dokid, '=');
	$query->orderByHeader($header);
	$query->orderBy('k.kode', 'ASC');
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top'),
						array('data' => '000' . $data->kode, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					$total+=$data->jumlah;
	}
	
	if ($total==0) {
		$rows[] = array(
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						array('data' => '', 'align' => 'left', 'valign'=>'top'),
						array('data' => 'Tidak ada pajak', 'align' => 'left', 'valign'=>'top'),
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);		
	}
	$rows[] = array(
				array('data' => 'TOTAL', 'width' => '10px','colspan'=>'3', 'align' => 'center', 'valign'=>'top'),
				array('data' => apbd_fn($total), 'align' => 'right', 'valign'=>'top'),
				);					
	if(arg(4)=='pdf'){
			  
			  $output = getTable($tahun,$dokid);
			  print_pdf_p($output);
		}
	else{
		//drupal_set_message(arg(4));
		$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	
		$btn = l('Cetak', 'jurnal/edit/'.$tahun.'/'.$dokid.'/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		//$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');
		$output_form = drupal_get_form('jurnal_edit_main_form');
		return drupal_render($output_form);// . $output;
	}		
	
}

function getTable($tahun,$dokid){
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('dokid','keperluan', 'kegiatan', 'sppno', 'spptgl', 'spmno', 'spmtgl','penerimanama', 'penerimaalamat', 'penerimabanknama', 'penerimabankrekening', 'penerimanpwp'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$keperluan = $data->keperluan;
		$kegiatan = $data->kegiatan;
		
		$spp = $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl);
		$spm = $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
		$penerimanama = $data->penerimanama;
		$penerimaalamat = $data->penerimaalamat;
		if ($penerimaalamat=='') $penerimaalamat = 'Kosong, tidak diisi';
		$penerimarekening = $data->penerimabankrekening . ' ' . $data->penerimabanknama;
		$penerimanpwp = $data->penerimanpwp;
		if ($penerimanpwp=='') $penerimanpwp = 'Kosong, tidak diisi';
	}
	$top=array();
	$top[] = array (
		array('data' => 'SPP','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $spp, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'SPM','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $spm, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Keperluan','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $keperluan, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimanama, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima Alamat','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimaalamat, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima REkening','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimarekening, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array (
		array('data' => 'Penerima NPWP','width' => '100px', 'align'=>'left'),
		array('data' => ':', 'width' => '30px','align'=>'center'),
		array('data' => $penerimanpwp, 'width' => '300px', 'align'=>'left'),
	);
	$top[] = array(
				array('data' => '', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);			
	$top[] = array(
				array('data' => 'APBD', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);
	$top[] = array(
				array('data' => '', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);	
	$header = array ();
	$output = theme('table', array('header' => $header, 'rows' => $top ));
	//INFO SP2D
	//INFO SP2D
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('sp2dno','sp2dtgl', 'jumlah', 'jenisdokumen'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dno= $data->sp2dno;
		$sp2dtgl= $data->sp2dtgl;
		$jumlah = $data->jumlah;
		$jenisdokumen = $data->jenisdokumen;
	}
	
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('sp2dno','sp2dtgl'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$sp2dno= $data->sp2dno;
		$sp2dtgl= $data->sp2dtgl;
	}
	
	$arrtgl=explode('-',$sp2dtgl);
	$tanggal=$arrtgl[2].'-'.$arrtgl[1].'-'.$arrtgl[0];	
	
	drupal_set_title('SP2D No : ' . $sp2dno . ', Tanggal : ' . $tanggal);
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
//Jurnalitem APBD
	
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '70px','align'=>'center','style'=>$styleheader),
		array('data' => 'Rekening', 'width' => '170px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Debet', 'width' => '120px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kredit', 'width' => '120px','align'=>'center','style'=>$styleheader),
		array('data' => 'Jumlah', 'width' => '120px','align'=>'center','style'=>$styleheader),
		
		
	);
	$query = db_select('jurnalitem' , 'k');//->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('uraian', 'kodero','debet','kredit'));
	$query->condition('k.jurnalid', $dokid, '=');
	//$query->orderByHeader($header);
	$query->orderBy('k.kodero', 'ASC');
	# execute the query
	$results = $query->execute();
	
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	
	foreach ($results as $data) {
		$no++;  
		$total+=$data->debet;
		$total-=$data->kredit;
		$rows[] = array(
						array('data' => $no.' ', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
						array('data' => $data->kodero, 'width' => '70px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $data->uraian, 'width' => '170px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						//array('data' => apbd_fn($data->debet), 'width' => '1300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($data->debet), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($data->kredit), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					//$total+=$data->debet;
	}
	if ($no==0) {
		$no++;
		$str_jenis ='';
		if ($jenisdokumen==2) $str_jenis = 'Tambahan ';
		$rows[] = array(
			array('data' => $no, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
			array('data' => '00000000', 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => $str_jenis . 'Uang Persediaan (UP)', 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => apbd_fn($jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
		);
		
	}
	/*$rows[] = array(
						array('data' => $query, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						//array('data' => $data->kodero, 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $query, 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						//array('data' => apbd_fn($data->jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);*/
	$rows[] = array(
				array('data' => 'TOTAL', 'width' => '520px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
				array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
				);
	$rows[] = array(
				array('data' => '', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);			
	$rows[] = array(
				array('data' => 'SAP', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);
	$rows[] = array(
				array('data' => '', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);			
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));

////Jurnalitem SAP
	$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '70px','align'=>'center','style'=>$styleheader),
		array('data' => 'Rekening', 'width' => '170px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Debet', 'width' => '120px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kredit', 'width' => '120px','align'=>'center','style'=>$styleheader),
		array('data' => 'Jumlah', 'width' => '120px','align'=>'center','style'=>$styleheader),
		
		
	);
	$query = db_select('jurnalitemSAP' , 'k');//->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('uraian', 'kodero','debet','kredit'));
	$query->condition('k.jurnalid', $dokid, '=');
	//$query->orderByHeader($header);
	$query->orderBy('k.kodero', 'ASC');
	# execute the query
	$results = $query->execute();
	
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	
	foreach ($results as $data) {
		$no++;  
		$total+=$data->debet;
		$total-=$data->kredit;
		$rows[] = array(
						array('data' => $no.' ', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
						array('data' => $data->kodero, 'width' => '70px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $data->uraian, 'width' => '170px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						//array('data' => apbd_fn($data->debet), 'width' => '1300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($data->debet), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($data->kredit), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					//$total+=$data->debet;
	}
	if ($no==0) {
		$no++;
		$str_jenis ='';
		if ($jenisdokumen==2) $str_jenis = 'Tambahan ';
		$rows[] = array(
			array('data' => $no, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
			array('data' => '00000000', 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => $str_jenis . 'Uang Persediaan (UP)', 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => apbd_fn($jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
		);
		
	}
	/*$rows[] = array(
						array('data' => $query, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						//array('data' => $data->kodero, 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $query, 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						//array('data' => apbd_fn($data->jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);*/
	$rows[] = array(
				array('data' => 'TOTAL', 'width' => '520px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
				array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
				);
	$rows[] = array(
				array('data' => '', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);			
	$rows[] = array(
				array('data' => 'LO', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);
	$rows[] = array(
				array('data' => '', 'width' => '520px', 'align' => 'left', 'valign'=>'top','style'=>'border:none'),
				
				);			
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));

////Jurnalitem LO
		$header = array (
		array('data' => 'No','width' => '40px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kode', 'width' => '70px','align'=>'center','style'=>$styleheader),
		array('data' => 'Rekening', 'width' => '170px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Debet', 'width' => '120px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'Kredit', 'width' => '120px','align'=>'center','style'=>$styleheader),
		array('data' => 'Jumlah', 'width' => '120px','align'=>'center','style'=>$styleheader),
		
		
	);
	$query = db_select('jurnalitemLO' , 'k');//->extend('TableSort');

	# get the desired fields from the database
	$query->fields('k', array('uraian', 'kodero','debet','kredit'));
	$query->condition('k.jurnalid', $dokid, '=');
	//$query->orderByHeader($header);
	$query->orderBy('k.kodero', 'ASC');
	# execute the query
	$results = $query->execute();
	
		
	# build the table fields
	$no=0;
	$total=0;	
	
	$rows = array();
	
	foreach ($results as $data) {
		$no++;  
		$total+=$data->debet;
		$total-=$data->kredit;
		$rows[] = array(
						array('data' => $no.' ', 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
						array('data' => $data->kodero, 'width' => '70px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $data->uraian, 'width' => '170px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						//array('data' => apbd_fn($data->debet), 'width' => '1300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($data->debet), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($data->kredit), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);
					//$total+=$data->debet;
	}
	if ($no==0) {
		$no++;
		$str_jenis ='';
		if ($jenisdokumen==2) $str_jenis = 'Tambahan ';
		$rows[] = array(
			array('data' => $no, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>'border-left:1px solid black;'.$style),
			array('data' => '00000000', 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => $str_jenis . 'Uang Persediaan (UP)', 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
			array('data' => apbd_fn($jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
		);
		
	}
	/*$rows[] = array(
						array('data' => $query, 'width' => '40px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						//array('data' => $data->kodero, 'width' => '130px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						array('data' => $query, 'width' => '300px', 'align' => 'left', 'valign'=>'top','style'=>$style),
						//array('data' => apbd_fn($data->jumlah), 'width' => '130px', 'align' => 'right', 'valign'=>'top','style'=>$style),
						
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						
					);*/
	$rows[] = array(
				array('data' => 'TOTAL', 'width' => '520px','colspan'=>'3', 'align' => 'center', 'valign'=>'top','style'=>$styleheader),
				array('data' => apbd_fn($total), 'width' => '120px', 'align' => 'right', 'valign'=>'top','style'=>$styleheader),
				);
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return 	$output;
	
}

function jurnal_edit_main_form($form, &$form_state) {
	
	$tahun = arg(2);
	$dokid = arg(3);
	
	$query = db_select('dokumen' , 'k');
	$query->fields('k', array('dokid','keperluan', 'kegiatan', 'sppno', 'spptgl', 'spmno', 'spmtgl', 
					'penerimanama', 'penerimaalamat', 'penerimabanknama', 'penerimabankrekening', 'penerimanpwp'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('dokid', $dokid, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		$keperluan = $data->keperluan;
		$kegiatan = $data->kegiatan;
		
		$spp = $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl);
		$spm = $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
	}
	//$arrtgl=explode('-',$sp2dtgl);
	//$tanggal=$arrtgl[2].'-'.$arrtgl[1].'-'.$arrtgl[0];
	
	$form['spp'] = array(
		'#type' => 'item',
		'#title' =>  t('SPP'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $spp . '</p>',
	);	
	$form['spm'] = array(
		'#type' => 'item',
		'#title' =>  t('SPM'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $spm . '</p>',
	);	
	$form['keperluan'] = array(
		'#type' => 'item',
		'#title' =>  t('Keperluan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $keperluan . '</p>',
	);
	$form['kegiatan'] = array(
		'#type' => 'item',
		'#title' =>  t('Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' =>'<p>' . $kegiatan . '</p>',
	);

	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> '<p>Penerima : ' . $penerimanama . '</p>'.'',
		//'#collapsible' => TRUE,
		//'#collapsed' => TRUE,        
	);	
	

	
	//QUERY .....
	$query = db_select('dokumen', 'k');//->extend('PagerDefault')->extend('TableSort');
	# get the desired fields from the database
	$query->fields('k', array('dokid','kodeuk','kodekeg', 'sp2dno','sp2dtgl','keperluan', 'kegiatan', 'penerimanama', 'jumlah','jenisdokumen'));
	$query->condition('k.dokid', arg(3), '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$kodeuk=$data->kodeuk;
		$nobukti=$data->sp2dno;
		$tanggal=$data->sp2dtgl;
		$jumlah=$data->jumlah;
		$keterangan=$data->keperluan;
	}
	$query = db_select('unitkerja', 'u');
	$query->fields('u', array('namasingkat','kodeuk'));
	$query->condition('u.kodeuk', $kodeuk, '=');
	$results = $query->execute();
	foreach ($results as $data) {
		$skpd=$data->namasingkat;
	}
	
	//drupal_set_message(arg(3));
	$form['formdata']['kodeuk']= array(
		'#type' => 'hidden',
		'#value' => $kodeuk,
	);
	$form['formdata']['keterangan']= array(
		'#type' => 'hidden',
		'#value' => $keterangan,
	);
	$form['formdata']['total']= array(
		'#type' => 'hidden',
		'#value' => $jumlah,
	);
	

	$form['formdata']['skpd']= array(
		'#type' => 'textfield',
		'#title' => 'SKPD',
		'#disabled' => true,
		'#default_value' => $skpd,
	);
	$form['formdata']['tanggal']= array(
		'#type' => 'textfield',
		'#title' => 'Tanggal',
		'#disabled' => true,
		'#default_value' => $tanggal,
	);
	$form['formdata']['nobukti']= array(
		'#type' => 'textfield',
		'#title' => 'No Bukti',
		'#disabled' => true,
		'#default_value' => $nobukti,
	);
	$form['formdata']['nobukti2']= array(
		'#type' => 'textfield',
		'#title' => 'No Bukti Lain',
	);
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		//'#title' =>  t('Simpan'),
		'#value' => 'Simpan',
		
	);
	$form['formdata']['cetak'] = array(
		'#type' => 'submit',
		//'#title' =>  t('Simpan'),
		'#value' => 'Cetak',
		
	);
//Jurnalitem APBD ......
	$form['tittle1'] = array(
		'#markup' => '<h1>APBD</h1>',
		
	);
	$form['table']= array(
		'#prefix' => '<table><tr><th >Kodero</th><th >Uraian</th><th>Debet</th><th>Kredit</th><th>Jumlah</th></tr>',
		 '#suffix' => '</table>',
	);	
	$query = db_select('jurnalitem', 'n')
    ->fields('n',array('uraian', 'kodero','debet','kredit'))
	->condition('jurnalid', $dokid, '=');
	$results = $query->execute();$i=0;$totalapbd=0;
    foreach ($results as $data) {
		$totalapbd+=$data->debet;
		$totalapbd-=$data->kredit;
		$form['table']['koderoapbd'.$i]= array(
				'#type' => 'value',
				'#value' => $data->kodero,
		); 
		$form['table']['uraianapbd'.$i]= array(
				'#type' => 'value',
				'#value' => $data->uraian,
		); 
		
		
		$form['table']['kodero'.$i]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $data->kodero,
				'#size' => 10,
				'#suffix' => '</td>',
			); 
		$form['table']['uraian'.$i]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $data->uraian, 
			'#suffix' => '</td>',
		); 
		$form['table']['debetapbd'.$i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> ''.$data->debet, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['table']['kreditapbd'.$i]= array(
			'#type'         => 'textfield', 
			'#default_value'=> ''.$data->kredit, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['table']['jumlahapbd'.$i]= array(
			//'#type'         => 'textfield', 
			'#markup'=> ''.$totalapbd, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
		$i++;
	}
//Jurnalitem SAP ......
	$form['tittle2'] = array(
		'#markup' => '<h1>SAP</h1>',
		
	);
	$form['table2']= array(
		'#prefix' => '<table><tr><th >Kodero</th><th >Uraian</th><th>Debet</th><th>Kredit</th><th>Jumlah</th></tr>',
		 '#suffix' => '</table>',
	);	
	$query = db_select('jurnalitemSAP', 'n')
    ->fields('n',array('uraian', 'kodero','debet','kredit'))
	->condition('jurnalid', $dokid, '=');
	$results = $query->execute();$i2=0;$totalsap=0;
    foreach ($results as $data) {
		$totalsap+=$data->debet;
		$totalsap-=$data->kredit;
		$form['table']['koderosap'.$i2]= array(
				'#type' => 'value',
				'#value' => $data->kodero,
		); 
		$form['table']['uraiansap'.$i2]= array(
				'#type' => 'value',
				'#value' => $data->uraian,
		); 
		$form['table2']['kodero'.$i2]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<tr><td>',
				'#markup' => $data->kodero,
				'#size' => 10,
				'#suffix' => '</td>',
			); 
		$form['table2']['uraian'.$i2]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $data->uraian, 
			'#suffix' => '</td>',
		); 
		$form['table2']['debetsap'.$i2]= array(
			'#type'         => 'textfield', 
			'#default_value'=> ''.$data->debet, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['table2']['kreditsap'.$i2]= array(
			'#type'         => 'textfield', 
			'#default_value'=> ''.$data->kredit, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['table2']['jumlahsap'.$i2]= array(
			//'#type'         => 'textfield', 
			'#markup'=> ''.$totalsap, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
		$i2++;
	}
//Jurnalitem LO ......
	$form['tittle3'] = array(
		'#markup' => '<h1>LO</h1>',
		
	);
	$form['table3']= array(
		'#prefix' => '<table><tr><th >Kodero</th><th >Uraian</th><th>Debet</th><th>Kredit</th><th>Jumlah</th></tr>',
		 '#suffix' => '</table>',
	);	
	$query = db_select('jurnalitemLO', 'n')
    ->fields('n',array('uraian', 'kodero','debet','kredit'))
	->condition('jurnalid', $dokid, '=');
	$results = $query->execute();$i3=0;$totallo=0;
    foreach ($results as $data) {
		$totallo+=$data->debet;
		$totallo-=$data->kredit;
		$form['table']['koderolo'.$i3]= array(
				'#type' => 'value',
				'#value' => $data->kodero,
		); 
		$form['table']['uraianlo'.$i3]= array(
				'#type' => 'value',
				'#value' => $data->uraian,
		);
		$form['table3']['kodero'.$i3]= array(
				//'#type'         => 'textfield', 
				'#prefix' => '<tr><td>',
				'#markup' => $data->kodero,
				'#size' => 10,
				'#suffix' => '</td>',
			); 
		$form['table3']['uraian'.$i3]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> $data->uraian, 
			'#suffix' => '</td>',
		); 
		$form['table3']['debetlo'.$i3]= array(
			'#type'         => 'textfield', 
			'#default_value'=> ''.$data->debet, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['table3']['kreditlo'.$i3]= array(
			'#type'         => 'textfield', 
			'#default_value'=> ''.$data->kredit, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td>',
		);
		$form['table3']['jumlahlo'.$i3]= array(
			//'#type'         => 'textfield', 
			'#markup'=> ''.$totallo, 
			'#attributes' => array('id' => 'righttf'),
			'#size' => 20,
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
		$i3++;
	}
	$form['table3']['apbd']= array(
		'#type'         => 'hidden', 
		'#value'=> $i, 
	);
	$form['table3']['sao']= array(
		'#type'         => 'hidden', 
		'#value'=> $i2, 
	);
	$form['table3']['lo']= array(
		'#type'         => 'hidden', 
		'#value'=> $i3, 
	);
	return $form;
}
function jurnal_edit_main_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$nobukti = $form_state['values']['nobukti'];
	$nobukti2 = $form_state['values']['nobukti2'];
	$keterangan = $form_state['values']['keterangan'];
	$total = $form_state['values']['total'];
	$tanggal = $form_state['values']['tanggal'];
	$jumlah = $form_state['values']['apbd'];
	//drupal_set_message($jumlah);
	$batas=$jumlah;
	for($n=0;$n<$batas;$n++){
		$kodero=$form_state['values']['koderoapbd'.$n];
		$uraian=$form_state['values']['uraianapbd'.$n];
		$jumlah=$form_state['values']['jumlahapbd'.$n];
		db_insert('jurnalitem')
		->fields(array('jurnalid','kodero','uraian','debet'))
		->values(array(
				'jurnalid'=> arg(3),
				'kodero' => $kodero,
				'uraian' => $uraian,
				'debet' => $jumlah,
				))
		->execute();
		$koderosap=$form_state['values']['koderosap'.$n];
		$uraiansap=$form_state['values']['uraiansap'.$n];
		$jumlahsap=$form_state['values']['jumlahsap'.$n];
		db_insert('jurnalitemSAP')
		->fields(array('jurnalid','kodero','uraian','debet'))
		->values(array(
				'jurnalid'=> arg(3),
				'kodero' => $koderosap,
				'uraian' => $uraiansap,
				'debet' => $jumlahsap,
				))
		->execute();
		$koderolo=$form_state['values']['koderolo'.$n];
		$uraianlo=$form_state['values']['uraianlo'.$n];
		$jumlahlo=$form_state['values']['jumlahlo'.$n];
		db_insert('jurnalitemLO')
		->fields(array('jurnalid','kodero','uraian','debet'))
		->values(array(
				'jurnalid'=> arg(3),
				'kodero' => $koderolo,
				'uraian' => $uraianlo,
				'debet' => $jumlahlo,
				))
		->execute();
		
	}
	db_insert('jurnal')
	->fields(array('jurnalid','dokid', 'kodeuk','nobukti','nobuktilain','tanggal', 'keterangan','total'))
	->values(array(
			'jurnalid'=> arg(3),
			'dokid' => arg(3),
			'kodeuk' => $kodeuk,
			'nobukti' => $nobukti,
			'nobuktilain' => $nobukti2,
			'tanggal' =>$tanggal,
			'keterangan' => $keterangan,
			'total' => $total,
			))
	->execute();
	//QUERY .....
	$query = db_update('dokumen')//->extend('PagerDefault')->extend('TableSort');
	# get the desired fields from the database
	->fields(
			array(
				'terjurnal' => 1,
			)
		);
	$query->condition('dokid', arg(3), '=');
	$results = $query->execute();
}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */


?>
