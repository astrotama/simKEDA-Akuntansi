<?php
function rekening_jenis_main($arg=NULL, $nama=NULL) {
	 
	//drupal_set_message(arg(2));
	$cetakpdf =  arg(2);
	if ($cetakpdf=='pdf') {
		$output = gen_output_rekening_print();
		apbd_ExportPDF_P($output, '10', "Daftar_Rekening_APBD.pdf");
	
	} else if ($cetakpdf=='xls') {
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Daftar_Rekening_APBD.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		$outputexcel = gen_output_rekening_print();
		echo $outputexcel;
		
	} else if ($cetakpdf=='printdetil') {
		$kodej =  arg(3);
		
		$output = gen_output_rekening_detil_print($kodej);
		apbd_ExportPDF_P($output, '10', "Daftar_Rekening_APBD.pdf");
		//return $output;

	} else if ($cetakpdf=='printmap') {
		$kodej =  arg(3);
		
		$output = gen_output_rekening_detil_map_print($kodej);
		apbd_ExportPDF_L($output, '10', "Daftar_Rekening_Map_APBD.pdf");
		
	} else {
		//drupal_set_message(arg(4));
		$output = gen_output_rekening();
		$output_form = drupal_get_form('rekening_jenis_main_form');	
		
		$btn = l('Cetak', 'rekening/jenis/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp;' . l('Excel', 'rekening/jenis/xls' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		$btn .= '&nbsp;' . l('SAP', 'rekeningsap/jenis' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
	}	
	
}


function rekening_jenis_main_form($form, &$form_state) {
	
	
}

function gen_output_rekening() {
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '20px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => '', 'valign'=>'top'),
	array('data' => '', 'valign'=>'top'),
	array('data' => '', 'width' => '40px', 'valign'=>'top'),
	array('data' => '', 'width' => '40px', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('anggaran', 'a');
$query->fields('a', array('kodea', 'uraian'));
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => '<strong>' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => null, 'align' => 'center', 'valign'=>'top'),
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
		array('data' => null, 'align' => 'left', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '=');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	foreach ($results_kel as $data_kel) {

		$rows[] = array(
			array('data' => '<strong>' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => null, 'align' => 'left', 'valign'=>'top'),
			array('data' => null, 'align' => 'left', 'valign'=>'top'),
			
			array('data' => null, 'align' => 'center', 'valign'=>'top'),
			array('data' => null, 'align' => 'left', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->fields('j', array('kodej', 'uraian'));
			$query->condition('j.kodek', $data_kel->kodek, '=');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			$jenis =  l($data_jen->uraian, 'rekening/obyek/' . $data_jen->kodej, array('attributes' => array('class' => null)));
	
			$print1 = l('<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Cetak', 'rekening/jenis/printdetil/'  . $data_jen->kodej , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary btn-sm')));
			$print2 = l('<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Mapping', 'rekening/jenis/printmap/'  . $data_jen->kodej , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary btn-sm')));
			
			$rows[] = array(
				array('data' => $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
				array('data' => $jenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => cek_mapping_lra($data_jen->kodej), 'align' => 'center', 'valign'=>'top'),
				array('data' => cek_mapping_sap($data_jen->kodej), 'align' => 'center', 'valign'=>'top'),
				array('data' => $print1, 'align' => 'left', 'valign'=>'top'),
				array('data' => $print2, 'align' => 'left', 'valign'=>'top'),
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
	array('data' => 'DAFTAR REKENING APBD', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);

$output = theme('table', array('header' => null, 'rows' => $rows ));
 
//TABEL
$header = array (
	array('data' => 'KODE', 'width' => '30px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN', 'width' => '480px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$rows = array();

//AKUN
$query = db_select('anggaran', 'a');
$query->fields('a', array('kodea', 'uraian'));
$query->orderBy('a.kodea');
$results = $query->execute();
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => $datas->kodea,  'width' => '30px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => $datas->uraian,  'width' => '480px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
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
		$query = db_select('jenis', 'j');
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
	array('data' => 'DAFTAR REKENING APBD', 'width' => '510px', 'align'=>'center','style'=>'border:none'),
);

$results = db_query('SELECT kodej, uraian FROM {jenis} WHERE kodej=:kodej', array(':kodej'=>$kodej));
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
$results = db_query('SELECT kodeo, uraian FROM {obyek} WHERE kodej=:kodej ORDER BY kodeo', array(':kodej'=>$kodej));
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => apbd_format_rek_obyek($datas->kodeo),  'width' => '50px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => strtoupper($datas->uraian),  'width' => '460px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
	);
	
	//RINCIAN
	$results_rek = db_query('SELECT kodero, uraian FROM {rincianobyek} WHERE kodeo=:kodeo ORDER BY kodero', array(':kodeo'=>$datas->kodeo));
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


function gen_output_rekening_detil_map_print($kodej) {

$rows[] = array(
	array('data' => 'DAFTAR REKENING APBD DAN MAPPING SAP', 'width' => '840px', 'align'=>'center','style'=>'border:none'),
);

$results = db_query('SELECT kodej, uraian FROM {jenis} WHERE kodej=:kodej', array(':kodej'=>$kodej));
foreach ($results as $data) {
	$rows[] = array(
		array('data' => $data->kodej . ' - ' . $data->uraian, 'width' => '840px', 'align'=>'center','style'=>'border:none'),
	);	
}

$output = theme('table', array('header' => null, 'rows' => $rows ));
 
$header = array();
//TABEL
$header[] = array (
	array('data' => 'REKENING PERMENDAGRI', 'width' => '280px', 'colspan' => '2', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REKENING SAP=LRA', 'width' => '280px', 'colspan' => '2', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'REKENING SAP-LO/NERACA', 'width' => '280px', 'colspan' => '2', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);
$header[] = array (
	array('data' => 'KODE', 'width' => '35px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN', 'width' => '245px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KODE', 'width' => '35px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN', 'width' => '245px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KODE', 'width' => '35px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN', 'width' => '245px', 'valign'=>'top', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
);

$rows = array();

//OBYEK
$results = db_query('SELECT kodeo, uraian FROM {obyek} WHERE kodej=:kodej ORDER BY kodeo', array(':kodej'=>$kodej));
foreach ($results as $datas) {

	$rows[] = array(
		array('data' => apbd_format_rek_obyek($datas->kodeo),  'width' => '35px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => strtoupper($datas->uraian),  'width' => '245px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:80%;border-right:1px solid black;font-weight: bold'),
		array('data' => '',  'width' => '35px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-right:1px solid black;font-weight: bold'),
		array('data' => '',  'width' => '245px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:80%;border-right:1px solid black;font-weight: bold'),
		array('data' => '',  'width' => '35px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:80%;border-right:1px solid black;font-weight: bold'),
		array('data' => '',  'width' => '245px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:80%;border-right:1px solid black;font-weight: bold'),
	);
	
	//RINCIAN
	$results_rek = db_query('SELECT kodero, uraian FROM {rincianobyek} WHERE kodeo=:kodeo ORDER BY kodero', array(':kodeo'=>$datas->kodeo));
	foreach ($results_rek as $data_rek) {
		
		//SAP-LRA
		$kodero_sap = '';
		$uraian_sap =  '<p style="color:red">Tidak Ada</p>';
		$sql = db_select('rekeningmaplra_apbd', 'rm');
		$sql->join('rincianobyeksap', 'r', 'rm.koderolra=r.kodero');
		$sql->fields('r',array('uraian', 'kodero'));
		$sql->condition('koderoapbd', $data_rek->kodero, '=');
		$res = $sql->execute();
		foreach ($res as $datamap) {
			$kodero_sap = apbd_format_rek_rincianobyek($datamap->kodero);
			$uraian_sap =  $datamap->uraian;
		}		
		
		//LO/NERACA
		$kodero_lo = '';
		$uraian_lo = '<p style="color:red">Tidak Ada</p>';
		$sql = db_select('rekeningmapsap_apbd', 'rm');
		$sql->join('rincianobyeksap', 'r', 'rm.koderosap=r.kodero');
		$sql->fields('r',array('uraian', 'kodero'));
		$sql->condition('koderoapbd', $data_rek->kodero, '=');
		$res = $sql->execute();
		foreach ($res as $datamap) {
			$kodero_lo = apbd_format_rek_rincianobyek($datamap->kodero);
			$uraian_lo =  $datamap->uraian;
		}	
		
		$rows[] = array(
			array('data' => apbd_format_rek_rincianobyek($data_rek->kodero), 'width' => '35px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => ucwords(strtolower($data_rek->uraian)), 'width' => '245px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-right:1px solid black;'),
			
			array('data' => $kodero_sap, 'width' => '35px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => $uraian_sap, 'width' => '245px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-right:1px solid black;'),
			
			array('data' => $kodero_lo, 'width' => '35px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => $uraian_lo, 'width' => '245px', 'align' => 'left', 'valign'=>'top', 'valign'=>'top','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
		
		
		
	}
	
$rows[] = array(
	array('data' => null,  'width' => '35px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:40%;border-left:1px solid black;border-right:1px solid black;'),
	array('data' => null,  'width' => '245px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),

	array('data' => null,  'width' => '35px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => null,  'width' => '245px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),

	array('data' => null,  'width' => '35px', 'align' => 'left', 'valign'=>'top','style'=>'font-size:40%;border-right:1px solid black;'),
	array('data' => null,  'width' => '245px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-right:1px solid black;'),
	
);

}	//foreach ($results as $datas)

$rows[] = array(
	array('data' => null,  'width' => '840px', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:40%;border-top:1px solid black;'),
);

//RENDER	
//$output .= theme('table', array('header' => $header, 'rows' => $rows ));
$output .= createT($header, $rows);


return $output;

}

function cek_mapping_sap($kodej) {
$x = 0;

$res = db_query('select count(kodero) jumlah from rincianobyek where kodero like :kodej and kodero not in (select koderoapbd from rekeningmapsap_apbd)', array(':kodej'=>$kodej . '%'));
foreach ($res as $dat) {
	$x = $dat->jumlah;
} 	

if ($x==0) 
	return '<p style="color:green;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></p>';
else
	return '<p style="color:red;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></p>';
}
 
function cek_mapping_lra($kodej) {
$x = 0;

$res = db_query('select count(kodero) jumlah from rincianobyek where kodero like :kodej and kodero not in (select koderoapbd from rekeningmaplra_apbd)', array(':kodej'=>$kodej . '%'));
foreach ($res as $dat) {
	$x = $dat->jumlah;
} 	

if ($x==0) 
	return '<p style="color:green;"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></p>';
else
	return '<p style="color:red;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></p>';
}

?>