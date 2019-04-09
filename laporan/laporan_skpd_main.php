<?php
function laporan_skpd_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	
	
	if ($arg) {
		switch($arg) {
			case 'filter':
				$cetakpdf = arg(3);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		} 
		
	} 
	
	if ($cetakpdf == 'pdf') {
		$output = gen_report_skpd_print(); 
		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P($output, 20,  "LAP.pdf");
		//return $output;
		
	} else if ($cetakpdf=='excel') { 
		
		//drupal_set_message('select ' . $cetakpdf);
		
		//gen_report_realisasi_print($bulan, $kodeuk, $tingkat, $tanggal, $penandatangan, $cetakpdf) {
		$output = gen_report_skpd_print();
			
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Laporan Komulatif Excel.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;
		//echo 'John' . "\t" . 'Doe' . "\t" . '555-5555' . "\n";
		 
	} else {
		//drupal_set_message(arg(4));
		$output = gen_report_skpd();
		//$output_form = drupal_get_form('laporan_skpd_main_form');	  
		
		
		$btn = l('Cetak', 'laporan/skpd/filter/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-print')));
		$btn .= '&nbsp;' . l('Excel', 'laporan/skpd/filter/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary glyphicon glyphicon-floppy-save')));	
		
		

		//$btn = '';
		
		return /*drupal_render($output_form) .*/ $btn . $output . $btn;
		
	}	
	
}


function gen_report_skpd() {

	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'Kode',  'width' => '20px', 'valign'=>'top'), 
		array('data' => 'Nama',  'valign'=>'top'),
	);

			
		# execute the query
		//$results = $query->execute();
		
		$results = db_query('SELECT kodeuk, kodedinas, namauk FROM {unitkerja} WHERE kodeuk NOT IN (SELECT kodeuk FROM {prognosiskeg}) ORDER BY kodedinas');
			
		# build the table fields
		$no=0;


			
		$rows = array();
		foreach ($results as $data) {
			$no++;  
			
			
			//$skpd = l($data->namauk, 'opd/edit/' . $data->kodeuk , array ('html' => true));
			
			$rows[] = array(
							array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
							array('data' => $data->kodedinas, 'align' => 'left', 'valign'=>'top'),
							array('data' => $data->namauk, 'align' => 'left', 'valign'=>'top'),
						);
		}

		//BUTTON
		//$btn = apbd_button_baru('operator/edit');
		//$btn .= "&nbsp;" . apbd_button_excel('');	
		
		
		$output = theme('table', array('header' => $header, 'rows' => $rows ));
		//$output .= theme('pager');

		return $output;

}

function gen_report_skpd_print() {

	
	$qlike='';
	$limit = 20;
	
	$header = array (
		array('data' => '','width' => '25px', 'valign'=>'top','align'=>'center','style'=>'font-size:80px;'),
		array('data' => '', 'valign'=>'top','style'=>'font-size:35px;'), 
		array('data' => '',  'valign'=>'top','style'=>'font-size:35px;'),
	);
	
		//$output_form = drupal_get_form('opd_main_form');
	$header = array (
		array('data' => 'No','width' => '25px', 'valign'=>'top','align'=>'center','style'=>'font-size:35px;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => 'Kode', 'valign'=>'top','style'=>'font-size:35px;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'), 
		array('data' => 'Nama',  'valign'=>'top','style'=>'font-size:35px;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	);
		
		$results = db_query('SELECT kodeuk, kodedinas, namauk FROM {unitkerja} WHERE kodeuk NOT IN (SELECT kodeuk FROM {prognosiskeg}) ORDER BY kodedinas');
			
		# build the table fields
		$no=0;


			
		$rows = array();
		foreach ($results as $data) {
			$no++;  
			
			
			$skpd = l($data->namauk, 'opd/edit/' . $data->kodeuk , array ('html' => true));
			
			//$editlink =  apbd_button_hapus('operator/delete/' . $data->username);
			
			
			$rows[] = array(
							array('data' => $no, 'width' => '25px', 'align' => 'center', 'valign'=>'top','style'=>'font-size:35px;border-left:1px solid black;'),
							array('data' => $data->kodedinas, 'align' => 'left', 'valign'=>'top','style'=>'font-size:35px;border-left:1px solid black;border-right:1px solid black;'),
							array('data' => $data->namauk, 'align' => 'left', 'valign'=>'top','style'=>'font-size:35px;border-right:1px solid black;'),
						);
		}
		$rows[] = array(
							array('data' => '', 'width' => '25px', 'align' => 'center', 'valign'=>'top','style'=>'font-size:35px;border-left:1px solid black;border-bottom:1px solid black;'),
							array('data' => '', 'align' => 'left', 'valign'=>'top','style'=>'font-size:35px;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;'),
							array('data' => '', 'align' => 'left', 'valign'=>'top','style'=>'font-size:35px;border-right:1px solid black;border-bottom:1px solid black;'),
						);

$tabel_data .= theme('table', array('header' => $header, 'rows' => $rows ));
//$tabel_data .= createT($header, $rows);

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}




?>

