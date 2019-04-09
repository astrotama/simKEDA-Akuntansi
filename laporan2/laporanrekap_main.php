<?php
function laporanrekap_main($arg=NULL, $nama=NULL) {
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
	
	
	$output = getlaporanrekap();
	print_pdf_l($output);
	
	
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
function getlaporanrekap(){
	$styleheader='border:1px solid black;';
	$style='border-right:1px solid black;';
	
	$header=array();
	$rows[]=array(
		array('data' => 'DAFTAR REKAPITULASI DAN TUNJANGAN PEGAWAI NEGERI PUSKESMAS JEPARA', 'width' => '900px','align'=>'center','style'=>'border:none;font-size:150%;'),
	);
	$rows[]=array(
		array('data' => 'UNTUK : BULAN DESEMBER 2016', 'width' => '900px','align'=>'center','style'=>'border:none;font-size:125%;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '675px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Lampiran III', 'width' => '65px','align'=>'left','style'=>'border:none;'),
		array('data' => '', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '', 'width' => '140px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '675px','align'=>'left','style'=>'border:none;'),
		array('data' => 'SEB PUOD dan Dirjen Anggaran', 'width' => '135px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '675px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Tanggal', 'width' => '65px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '24 Agustus 1999', 'width' => '140px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '675px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor', 'width' => '65px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => '900.876/2428/PUOD', 'width' => '140px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => '', 'width' => '675px','align'=>'left','style'=>'border:none;'),
		array('data' => 'Nomor', 'width' => '65px','align'=>'left','style'=>'border:none;'),
		array('data' => ':', 'width' => '20px','align'=>'center','style'=>'border:none;'),
		array('data' => 'SE-142/A/1999', 'width' => '140px','align'=>'left','style'=>'border:none;'),
	);
	$rows[]=array(
		array('data' => 'GOL', 'width' => '25px','align'=>'center','rowspan'=>'2','style'=>'border:1px solid black;'),
		array('data' => 'JML PWGIS/SU ANAK', 'width' => '25px','rowspan'=>'2','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'JIWA', 'width' => '25px','rowspan'=>'2','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Penghasilan', 'width' => '475px','align'=>'center','style'=>'border:1px solid black;'),
		array('data' => 'Potongan', 'width' => '350px','align'=>'center','style'=>'border:1px solid black;'),
		
	);
	$rows[]=array(
		array('data' => 'GAJI POKOK TUNJ. IS/SU TUNJ. ANAK JML KOTOR', 'width' => '67px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TPP TJDT', 'width' => '48px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TUNJ. ESELON TUNJ.UMUM TUNJ. FUNGS TUNJ. KHUSUS', 'width' => '78px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TUNJ. TERPENCIL TKD TJ.DIDIK TJ.BERAS', 'width' => '78px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TUNJ. PPH TUNJ. BPJS PEMBULATAN', 'width' => '78px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'PREMI JKK JKM', 'width' => '48px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'JUMLAH BRUTTO', 'width' => '78px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'IWP 10%', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'PPJ Ps.21 BPJS JKK JKM', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TAPERUM', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'CP', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'JUMLAH POTONGAN', 'width' => '70px','align'=>'center','style'=>'border-right:1px solid black;border-bottom:1px solid black;'),
	);
	
	$rows[] = array(
					array('data' => '','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '275px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => 'Jepara,10 November 2016','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$rows[] = array(
					array('data' => 'UPT KEPALA PUSKESMAS JEPARA','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '275px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => 'BENDAHARA PENGELUARAN','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
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
					array('data' => 'dr. TRIYONO TEGUH WIDODO, MM','width' => '325px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => '','width' => '275px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => 'RISWATI SETYORINI, S.Kep.','width' => '300px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					
	);
	$rows[] = array(
					array('data' => 'NIP. 19720713 400401 1 001','width' => '325px', 'align'=>'center','style'=>'border:none;'),
					array('data' => '','width' => '275px', 'align'=>'center','style'=>'border:none;font-weight:bold;text-decoration:underline;'),
					array('data' => 'NIP. 19700812 199303 2 009','width' => '300px', 'align'=>'center','style'=>'border:none;'),
					
	);
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
		return $output;
	}


function getlaporanrekap2(){
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
