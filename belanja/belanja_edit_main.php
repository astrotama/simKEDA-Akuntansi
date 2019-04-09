<?php
function belanja_edit_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	//$limit = 10;
    
	if ($arg) {
		$bulan = arg(2);
		$kodekeg = arg(3);
		
		
	} else {
		$bulan = date('m');		//variable_get('apbdtahun', 0);
		$kodekeg = 'ZZ';
	}
	if($kodekeg!='ZZ'){
		$query = db_select('kegiatan', 'p');
		$query->fields('p', array('kegiatan'))
					->condition('kodekeg',$kodekeg,'=');
		$results = $query->execute();
		if($results){
				foreach($results as $data) {
					$title=$data->kegiatan;
				}
			}
	}
	
	
	
	drupal_set_title($title);

	
	$query = db_select('apbdrekap', 'k')->extend('PagerDefault')->extend('TableSort');
	
	# get the desired fields from the database
	$query->fields('k', array('koderincian', 'namarincian','anggaran1', 'anggaran2'));
	$query->addExpression('realisasikum' . $bulan, 'realisasi');
	
	//$query->condition($field, $value, '=');
	$query->condition('kodekeg',$kodekeg,'=');
	//$query->orderByHeader($header);
	$query->orderBy('k.koderincian', 'ASC');
	
	//$query->limit(100);
	# execute the query
	$results = $query->execute();
	//drupal_set_message($ne);
	//drupal_set_message($query);
	# build the table fields
	$no=0;

	

	
	//$editlink .= l('Register', '', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-warning btn-xs btn-block')));
	//$editlink .= l('Gambar', '', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-danger btn-xs btn-block')));
	$width = array('40px','60px','270px','100px','100px','55px');	
	$widthnull = array('','','','','','');	
	$widthfix=array();
	$rows = array();
	//$width['']
	
	$anggarantot = 0;
	$realisasitot =0;
	foreach ($results as $data) {
				$no++;  
				
				$anggarantot += $data->anggaran2;
				$realisasitot += $data->realisasi;
				
				if(arg(4)=='pdf')
					{
						$widthfix= arrayCopy($width);
						$editlink = '';
						$style ='border-left:1px solid black;border-right:1px solid black';
						$stylehead='border:1px solid black ';
					}
				else {
					$widthfix= arrayCopy($widthnull);
					
					$editlink = apbd_button_bukubesar('akuntansi/buku/'. $bulan .'/'. $kodekeg .'/'.$data->koderincian);
					$style='';
					$stylehead='';
				}
					
				//<font color="red">This is some text!</font>
				$anggaran = apbd_fn($data->anggaran2);
				
				if ($data->anggaran1 > $data->anggaran2)
					$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1) . '</font></p>';
				else if ($data->anggaran1 < $data->anggaran2)
					$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1) . '</font></p>';
				
				
				$rows[] = array(
								array('data' => $no, 'width' => $widthfix[0], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
								array('data' => $data->koderincian, 'width' => $widthfix[1], 'align' => 'left', 'valign'=>'top', 'style'=>$style),
								array('data' => $data->namarincian, 'width' => $widthfix[2],'align' => 'left', 'valign'=>'top', 'style'=>$style),
								array('data' => $anggaran,'width' => $widthfix[3], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
								array('data' => apbd_fn($data->realisasi),'width' => $widthfix[4], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
								array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2, $data->realisasi)),'width' => $widthfix[5], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
								//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
								$editlink,
							);
			}
			$rows[] = array(
							array('data' => '', 'width' => $widthfix[0], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
							array('data' => '', 'width' => $widthfix[1], 'align' => 'left', 'valign'=>'top', 'style'=>$style),
							array('data' => 'TOTAL', 'width' => $widthfix[2],'align' => 'left', 'valign'=>'top', 'style'=>$style),
							array('data' => apbd_fn($anggarantot),'width' => $widthfix[3], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
							array('data' => apbd_fn($realisasitot),'width' => $widthfix[4], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
							array('data' => apbd_fn1(apbd_hitungpersen($anggarantot, $realisasitot)),'width' => $widthfix[5], 'align' => 'right', 'valign'=>'top', 'style'=>$style),
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
							'',
						);			
	$header = array (
		array('data' => 'No','width' => $widthfix[0], 'align'=>'center','valign'=>'top', 'style'=>$stylehead),
		array('data' => 'Kode','width'=> $widthfix[1], 'align'=>'center','valign'=>'top', 'style'=>$stylehead),
		array('data' => 'Uraian','width'=> $widthfix[2], 'align'=>'center','valign'=>'top', 'style'=>$stylehead),
		array('data' => 'Anggaran','width' => $widthfix[3], 'align'=>'center','valign'=>'top', 'style'=>$stylehead),
		array('data' => 'Realisasi', 'width'=> $widthfix[4], 'align'=>'center','valign'=>'top', 'style'=>$stylehead),
		array('data' => 'Persen', 'width' => $widthfix[5], 'align'=>'center','valign'=>'top', 'style'=>$stylehead),
		
	);	

//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$btn = l('Cetak', '/belanja/rekening/'.arg(2).'/'.arg(3).'/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	if(arg(4)=='pdf'){
			$rows[] = array(
									array('data' => '', 'width' => '625px', 'align' => 'right', 'valign'=>'top', 'style'=>'border-top:1px solid black'),
									
								);
			  $output=theme('table', array('header' => $header, 'rows' => $rows ));
			  print_pdf_p($output);
		}
		else{
			
			$output_form = drupal_get_form('belanja_edit_main_form');
			
			$output = theme('table', array('header' => $header, 'rows' => $rows ));
			//$output .= theme('pager');
			return  drupal_render($output_form) . $btn . $output . $btn;
		}
	
}


function belanja_edit_main_form($form, &$form_state) {
	
	$bulan = arg(2);
	$kodekeg = arg(3);
	
	$query = db_select('kegiatan', 'k');
	$query->fields('k', array('kodekeg', 'kegiatan', 'program', 'urusan', 'lokasi', 'sumberdana', 'programsasaran', 'programtarget', 'masukansasaran', 'masukantarget', 'keluaransasaran', 'keluarantarget', 'hasilsasaran', 'hasiltarget', 'kelompoksasaran'));
	//$query->fields('u', array('namasingkat'));
	$query->condition('kodekeg', $kodekeg, '=');
		
	# execute the query
	$results = $query->execute();
	foreach ($results as $data) {
		
		$program = $data->program;
		$urusan = $data->urusan;
		
		$sumberdana = $data->sumberdana;
		$lokasi = $data->lokasi;

		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		$masukantarget = $data->masukantarget;
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		
		$kelompoksasaran = $data->kelompoksasaran;
		
	}
	//$arrtgl=explode('-',$sp2dtgl);
	//$tanggal=$arrtgl[2].'-'.$arrtgl[1].'-'.$arrtgl[0];
	
	$form['program'] = array(
		'#type' => 'item',
		'#title' =>  t('Program'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $program . '</p>',
	);	
	$form['urusan'] = array(
		'#type' => 'item',
		'#title' =>  t('Urusan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $urusan . '</p>',
	);	
	$form['sumberdana'] = array(
		'#type' => 'item',
		'#title' =>  t('Sumberdana'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $sumberdana . '</p>',
	);	
	$form['lokasi'] = array(
		'#type' => 'item',
		'#title' =>  t('Lokasi'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $lokasi . '</p>',
	);	
	$form['kelompoksasaran'] = array(
		'#type' => 'item',
		'#title' =>  t('Kelompok Sasaran Kegiatan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		//'#disabled' => true,
		'#markup' => '<p>' . $kelompoksasaran . '</p>',
	);

	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> '<p>TOLOK UKUR KINERJA</p>' . '<p><em><small class="text-warning pull-right">klik disini utk menampilkan/menyembunyikan tolok ukur kinerja</small></em></p>',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	
	$form['formdata']['programtuk'] = array(
		'#type' => 'item',
		'#title' =>  t('Program'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#disabled' => true,
		'#markup' =>'<p>' . $programsasaran. '<em class="text-info pull-right">' . $programtarget . '</em></p>',
	);
	$form['formdata']['masukantuk'] = array(
		'#type' => 'item',
		'#title' =>  t('Masukan'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#disabled' => true,
		'#markup' =>'<p>' . $masukansasaran. '<em class="text-info pull-right">' . $masukantarget . '</em></p>',
	);
	$form['formdata']['keluarantuk'] = array(
		'#type' => 'item',
		'#title' =>  t('Keluaran'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#disabled' => true,
		'#markup' =>'<p>' . $keluaransasaran. '<em class="text-info pull-right">' . $keluarantarget . '</em></p>',
	);
	$form['formdata']['hasiltuk'] = array(
		'#type' => 'item',
		'#title' =>  t('Hasil'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#disabled' => true,
		'#markup' =>'<p>' . $hasilsasaran. '<em class="text-info pull-right">' . $hasiltarget . '</em></p>',
	);	

		
	return $form;
	
}

