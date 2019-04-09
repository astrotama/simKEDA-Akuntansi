<?php
function rekeningsap_jenis_main($arg=NULL, $nama=NULL) {

	$cetakpdf =  arg(2);
	if ($cetakpdf=='pdf') {
		$output = gen_output_rekening_print();
		apbd_ExportPDF_P($output, '10', "Daftar_Rekening_SAP.pdf");
	
	} else if ($cetakpdf=='xls') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Daftar_Rekening_SAP.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		$outputexcel = gen_output_rekening_print();
		echo $outputexcel;
		
	} else if ($cetakpdf=='printdetil') {
		$kodej =  arg(3);
		
		$output = gen_output_rekening_detil_print($kodej);
		apbd_ExportPDF_P($output, '10', "Daftar_Rekening_SAP.pdf");
		//return $output;

	} else {
		//drupal_set_message(arg(4));
		$output = gen_output_rekeningsap();
		$output_form = drupal_get_form('rekeningsap_jenis_main_form');	
		
		$btn = l('Cetak', 'rekeningsap/jenis/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-warning')));
		$btn .= '&nbsp;' . l('Excel', 'rekeningsap/jenis/xls' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-warning')));
		$btn .= '&nbsp;' . l('APBD', 'rekening/jenis' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-warning')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
	}	
	
}


function rekeningsap_jenis_main_form($form, &$form_state) {
	
	
}

function gen_output_rekeningsap() {
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '20px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => '', 'width' => '40px', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('anggaransap', 'a');
$query->fields('a', array('kodea', 'uraian'));
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('kelompoksap', 'k');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => null, 'align' => 'left', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenissap', 'j');
		$query->fields('j', array('kodej', 'uraian'));
			$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$jenis =  l($data_jen->uraian, 'rekeningsap/obyek/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			
			$print = l('<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak', 'rekeningsap/jenis/printdetil/'  . $data_jen->kodej , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-warning btn-sm')));

			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $jenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => $print, 'align' => 'left', 'valign'=>'top'),
			);
			
			

		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));
//$tabel_data = createT($header, $rows);


return $tabel_data;

}


function gen_output_rekening_print() {

$rows[] = array(
	array('data' => 'DAFTAR REKENING SAP', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);

$output = theme('table', array('header' => null, 'rows' => $rows ));
 
//TABEL
$header = array (
	array('data' => 'KODE', 'width' => '30px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN', 'width' => '480px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

//AKUN
$query = db_select('anggaransap', 'a');
$query->fields('a', array('kodea', 'uraian'));
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => $datas->kodea,  'width' => '30px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => $datas->uraian,  'width' => '480px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
	);
	
	//KELOMPOK
	$query = db_select('kelompoksap', 'k');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => $data_kel->kodek, 'width' => '30px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => $data_kel->uraian, 'width' => '480px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);		
		
		//JENIS
		$query = db_select('jenissap', 'j');
		$query->fields('j', array('kodej', 'uraian'));
			$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'width' => '30px','align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => ucwords(strtolower($data_jen->uraian)), 'width' => '480px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			);

		}	//jenis

		
		
	}
	
$rows[] = array(
	array('data' => null,  'width' => '30px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => null,  'width' => '480px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
);

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null,  'width' => '30px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:40%;border-top:1px solid black;'),
	array('data' => null,  'width' => '480px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-top:1px solid black;'),
);

//RENDER	
$output .= theme('table', array('header' => $header, 'rows' => $rows ));
//$tabel_data = createT($header, $rows);


return $output;

}


function gen_output_rekening_detil_print($kodej) {

$rows[] = array(
	array('data' => 'DAFTAR REKENING SAP', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);

$results = db_query('SELECT kodej, uraian FROM {jenissap} WHERE kodej=:kodej', array(':kodej'=>$kodej));
foreach ($results as $data) {
	$rows[] = array(
		array('data' => $data->kodej . ' - ' . $data->uraian, 'width' => '510px', 'align'=>'center','style'=>'border:none'),
	);	
}

$output = theme('table', array('header' => null, 'rows' => $rows ));
 
//TABEL
$header = array (
	array('data' => 'KODE', 'width' => '50px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN', 'width' => '460px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

//OBYEK
$results = db_query('SELECT kodeo, uraian FROM {obyeksap} WHERE kodej=:kodej ORDER BY kodeo', array(':kodej'=>$kodej));
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => apbd_format_rek_obyek($datas->kodeo),  'width' => '50px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => strtoupper($datas->uraian),  'width' => '460px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
	);
	
	//RINCIAN
	$results_rek = db_query('SELECT kodero, uraian FROM {rincianobyeksap} WHERE kodeo=:kodeo ORDER BY kodero', array(':kodeo'=>$datas->kodeo));
	foreach ($results_rek as $data_rek) {

		$rows[] = array(
			array('data' => apbd_format_rek_rincianobyek($data_rek->kodero), 'width' => '50px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => ucwords(strtolower($data_rek->uraian)), 'width' => '460px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		);		
		
		
		
	}
	
$rows[] = array(
	array('data' => null,  'width' => '50px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => null,  'width' => '460px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
);

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null,  'width' => '510px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-top:1px solid black;'),
);

//RENDER	
$output .= theme('table', array('header' => $header, 'rows' => $rows ));
//$tabel_data = createT($header, $rows);


return $output;

}



?>