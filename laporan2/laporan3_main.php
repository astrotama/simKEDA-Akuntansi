<?php
function laporan3_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		
				$tahun = arg(2);
				$jurnalid = arg(3);
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$jurnalid = '';
		
	}
	
	drupal_set_title('Jurnal');
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	
	$output = getlaporan3();
	print_pdf_p($output);
	
	
}



/**
 * Selects just the second dropdown to be returned for re-rendering.
 *
 * Since the controlling logic for populating the form is in the form builder
 * function, all we do here is select the element and return it to be updated.
 *
 * @return array
 *   Renderable array (the second dropdown)
 */
function getLaporan3(){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'IKHTISAR PEMBAYARAN KEKURANGAN TUNJ. FUNG. DSB PARA PEGAWAI DAERAH', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'LAMPIRAN SE MENDAGLI', 'width' => '250px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TAHUN', 'width' => '75px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'APRIL S/D OKTOBER 2016', 'width' => '280px','align'=>'left','style'=>'border:none;'),
		array('data' => 'TANGGAL', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '23-05-2016', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'INSTANSI', 'width' => '75px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'UPT PUSKESMAS BANGSRI I', 'width' => '280px','align'=>'left','style'=>'border:none;'),
		array('data' => 'NOMOR', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '972/3048 POLD', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'LAPORAN', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'MODEL PG II', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$rows=null;
	$header = array (
		array('data' => 'NO','width' => '55px', 'align'=>'center','style'=>$styleheader),
		array('data' => 'URAIAN', 'width' => '200px','align'=>'center','style'=>$styleheader),
		array('data' => 'MENURUT DAFTAR GAJI', 'width' => '170px', 'align'=>'center','style'=>$styleheader),
		//array('data' => 'Jumlah', 'width' => '120px','align'=>'center','style'=>$styleheader),
		array('data' => 'HSL PENELITIAN BAG. KEUANGAN', 'width' => '200px','align'=>'center','style'=>$styleheader),
		
		
	);
		
			
			# get the desired fields from the database

			//$tahun='2015';
			$total=0;
			for($n=0;$n<10;$n++) {
				$jumlah=100000000;
				$rows[] = array(
						
							array('data' => $n,'width' => '55px', 'align'=>'right','style'=>'border-left:1px solid black;'.$style),
							array('data' => 'nama', 'width' => '200px','align'=>'left','style'=>$style),
							array('data' => 'uraian', 'width' => '170px', 'align'=>'left','style'=>$style),
							//array('data' => '', 'width' => '120px','align'=>'right','style'=>$style),
							array('data' => $jumlah, 'width' => '200px','align'=>'right','style'=>$style),
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
				);
				$total+=$jumlah;		
				
							
			}
			$rows[] = array(
						
							array('data' => '','width' => '55px', 'align'=>'right','style'=>'border-left:1px solid black;border-bottom:2px solid black;border-top:2px solid black;'.$style),
							array('data' => 'JUMLAH', 'width' => '200px','align'=>'left','style'=>'border-bottom:2px solid black;border-top:2px solid black;'.$style),
							array('data' => $total, 'width' => '170px', 'align'=>'left','style'=>'border-bottom:2px solid black;border-top:2px solid black;'.$style),
							//array('data' => '', 'width' => '120px','align'=>'right','style'=>$style),
							array('data' => $jumlah, 'width' => '200px','align'=>'right','style'=>'border-bottom:2px solid black;border-top:2px solid black;'.$style),
							//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
				);
			$rows[] = array(
							array('data' => '','width' => '625px', 'align'=>'left','style'=>'border:none;'),
							
			);
			
		$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		$header= array();
		$rows= array();
		$rows[]=array(
			array('data' => 'SPMU', 'width' => '75px','align'=>'left','style'=>'border:none;'),
			array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
			array('data' => 'Jepara, OKTOBER 2016 Sesuai dengan penelitian diatas', 'width' => '350px','align'=>'center','style'=>'border:none;'),
		);
		$rows[]=array(
			array('data' => 'TANGGAL', 'width' => '75px','align'=>'left','style'=>'border:none;'),
			array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;'),
			array('data' => 'maka gaji bruto adalah sebesar', 'width' => '250px','align'=>'left','style'=>'border:none;'),
		);
		$rows[]=array(
			array('data' => 'NOMOR', 'width' => '75px','align'=>'left','style'=>'border:none;'),
			array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;'),
			array('data' => 'Rp.', 'width' => '100px','align'=>'left','style'=>'border:none;'),
			array('data' => '2.290.000', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		);
		$rows[]=array(
			array('data' => '', 'width' => '75px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '20px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;'),
			array('data' => 'atas bertambah/berkurang', 'width' => '350px','align'=>'left','style'=>'border:none;'),
			
		);
		$rows[]=array(
			array('data' => '', 'width' => '75px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '20px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '180px','align'=>'left','style'=>'border:none;'),
			array('data' => '', 'width' => '100px','align'=>'center','style'=>'border:none;'),
			array('data' => 'Rp.', 'width' => '100px','align'=>'left','style'=>'border:none;'),
			array('data' => '0', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		);
		$rows[] = array(
							array('data' => '','width' => '425px', 'align'=>'center','style'=>''),
							array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
							
			);
		$rows[] = array(
						array('data' => '','width' => '425px', 'align'=>'center','style'=>''),
						array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
						
		);
		$rows[] = array(
						array('data' => 'Ub. KABID PERBENDAHARAAN','width' => '225px', 'align'=>'center','style'=>''),
						array('data' => 'BENDAHARA PENGELUARAN','width' => '200px', 'align'=>'center','style'=>''),
						array('data' => 'PEMBUAT DAFTAR GAJI','width' => '200px', 'align'=>'center','style'=>''),
						
		);
		$rows[] = array(
						array('data' => 'KAS DAERAH','width' => '225px', 'align'=>'center','style'=>''),
						array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
						array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
						
		);
		$rows[] = array(
						array('data' => 'KASIE PERBENDAHARAAN','width' => '225px', 'align'=>'center','style'=>''),
						array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
						array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
						
		);
		$rows[] = array(
						array('data' => '','width' => '425px', 'align'=>'center','style'=>''),
						array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
						
		);
		$rows[] = array(
						array('data' => '','width' => '425px', 'align'=>'center','style'=>''),
						array('data' => '','width' => '200px', 'align'=>'center','style'=>''),
						
		);
		$rows[] = array(
						array('data' => 'SITI NURJANAH. SE','width' => '225px', 'align'=>'center','style'=>'text-decoration:underline;'),
						array('data' => 'YUNI APTRIANA','width' => '200px', 'align'=>'center','style'=>'text-decoration:underline;'),
						array('data' => 'LESTARINI KHAMUDAH','width' => '200px', 'align'=>'center','style'=>'text-decoration:underline;'),
						
		);
		$rows[] = array(
						array('data' => 'NIP. 19650903 198603 2 018','width' => '225px', 'align'=>'center','style'=>''),
						array('data' => 'NIP. 19750411 200604 2 007','width' => '200px', 'align'=>'center','style'=>''),
						array('data' => 'NIP. 19670720 198912 2 001','width' => '200px', 'align'=>'center','style'=>''),
						
		);
		$output .= theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
		
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

