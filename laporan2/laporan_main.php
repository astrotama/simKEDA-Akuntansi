<?php
function laporan_main($arg=NULL, $nama=NULL) {
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
	
	
	$output = getLaporan();
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
function getLaporan(){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'A2-1', 'width' => '150px','align'=>'right','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold'),
	);
	$rows[]=array(
		array('data' => 'Nama Kegiatan', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Rekening', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Tahun anggaran', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'TANDA BUKTI PENGELUARAN', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;text-decoration:underline;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Sudah terima dari', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Uang sejumlah', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => 'Untuk', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '100px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '50px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '475px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '<div style="vertical-align:middle;">TERBILANG Rp</div>', 'width' => '150px','rowspan'=>'3','align'=>'left','style'=>'border-top:2px solid black;border-bottom:2px solid black;font-size:200%;vertical-align: middle;'),
		array('data' => '', 'width' => '150px','rowspan'=>'3','align'=>'left','style'=>'border-top:2px solid black;border-bottom:2px solid black;font-size:200%;vertical-align: middle;'),
		array('data' => '', 'width' => '25px','rowspan'=>'3','align'=>'left','style'=>'border:none'),
		array('data' => 'Jepara, 10 Nopember 2016', 'width' => '300px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Yang berhak menerima', 'width' => '300px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Tanda tangan', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '30px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '150px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '325px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nama', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '30px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '150px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '325px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Alamat', 'width' => '120px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '30px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '150px','align'=>'left','style'=>'border-bottom:1px solid black;'),
	);
	$rows[] = array(
					array('data' => 'Setuju dibayarkan,','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'Pengguna Anggaran','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'Bendahara Pengeluaran','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'ADI SASONGKO, S.Pi., M.Si','width' => '325px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => 'RISTANTIYO','width' => '300px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => 'NIP. 19691107 199803 1 004','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => 'NIP. 19620708 198607 1 001','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	}


function getLaporan2(){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	$header=array();
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Lembar Asli', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Pengguna Anggaran/PPK-SKPD', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Salinan 1', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Kuasa BUD', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Salinan 2', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Bendahara Pengeluaran/PPTK', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '375px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Salinan 3', 'width' => '80px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Untuk Arsip Bendahara Pengeluaran/PPTK', 'width' => '150px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '250px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PENELITIAN KELENGKAPAN DOKUMEN SPP', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:150%;font-weight:bold;'),
	);
	$rows[]=array(
		array('data' => 'NOMOR : 001/TL/0401/2016', 'width' => '625px','align'=>'center','style'=>'border:none;font-size:130%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '625px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'PEMBAYARAN LANGSUNG GAJI [SPP-LS GAJI]', 'width' => '625px','align'=>'left','style'=>'border:none;'),
	);
	$checkbox=array(
		'Surat Pengantar SPP-LS',
		'Ringkasan SPP-LS',
		'Rincian SPP-LS',
		'Gaji Susulan',
		'Pembayaran Gaji Induk',
		'Kekurangan Gaji',
		'Gaji Terusan',
		'Uang duka wafat / tewas yang dilengkapi dengan daftar gaji induk /gaji susulan/kekurangan gaji/uang duka wafat/tewas',
		'SK CPNS',
		'SK PNS',
		'SK Kenaikan Pangkat',
		'SK Jabatan',
		'Kenaikan Gaji Berkala',
		'Surat Pernyataan Pelantikan',
		'Surat Pernyataan Masih Menduduki Jabatan',
		'Surat Pernyataan melaksanakan tugas',
		'Daftar Keluarga(KP4)',
		'Fotokopi surat nikah',
		'Fotokopi akte kelahiran',
		'SKPP',
		'Daftar potongan sewa rumah dinas',
		'Surat keterangan masih sekolah/kuliah',
		'Surat pindah',
		'Surat kematian',
		'SSP PPh Pasal 21',
		'Peraturan perundang-undangan mengenai penghasilan pimpinan dan anggota DPRD serta gaji dan tunjangan kepala daerah/wakil kepala daerah',
	);
	for($n=0;$n<sizeof($checkbox);$n++){
		$rows[]=array(
			array('data' => '<div style="width:5px;height:5px;background:red"></div>', 'width' => '25px','align'=>'left','style'=>'border:0.1px solid black;'),
			array('data' => '', 'width' => '5px','align'=>'left','style'=>'border:none'),
			array('data' => $checkbox[$n], 'width' => '595px','align'=>'left','style'=>'border:none;'),
		);
	}
	$rows[]=array(
		array('data' => 'PENELITI KELENGKAPAN DOKUMEN SPP', 'width' => '625px','align'=>'left','style'=>'border:none;text-decoration: underline;'),
	);
	$rows[]=array(
		array('data' => 'Tanggal', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Nama', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'NIP', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'Tanda Tangan', 'width' => '150px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'left','style'=>'border:none;'),
		array('data' => '.....................................', 'width' => '200px','align'=>'left','style'=>'border:none;'),
	);
	$attributes=array('style'=>'cellspacing="10";');
	$output = theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => $attributes));
	
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
