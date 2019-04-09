<?php
function akuntansi_kud_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
    
	$ntitle = 'Kas Umum Daerah';
	if ($arg) {
		
		
	
		$tglawal = arg(2);
		$tglakhir = arg(3);
				

	} else {
		$tglawal = '2017-01-01';
		$tglakhir = '2017-01-10';
		
	}
	
	drupal_set_title($ntitle);
	
	
	if ($koderod=='') $koderod = 'none';
	
	$btn = apbd_button_print('/akuntansi/kud/'.  $tglawal . '/' . $tglakhir . '/pdf');
	$btn .= "&nbsp;" . apbd_button_excel('');	

	if(arg(4)=='pdf'){
			  
			  $output = getDataPrint($tglawal, $tglakhir);
			  $_SESSION["hal1"] = 1;
			  apbd_ExportPDF_P($output, 10, "LAP");
			  print_pdf_p($output);
				
		}
	else{
		$output = getDataView($tglawal, $tglakhir);
		//$output .= theme('pager');
		$output_form = drupal_get_form('akuntansi_kud_main_form');
		return drupal_render($output_form).$btn . $output . $btn;	
	}
	
}

function getDataView($tglawal, $tglakhir) {

	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'Tanggal',  'width' => '90px', 'valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => 'No. Ref', 'valign'=>'top'),
		array('data' => 'Masuk',  'width' => '90px', 'valign'=>'top'),
		array('data' => 'Keluar',  'width' => '90px', 'valign'=>'top'),
		array('data' => 'Saldo',  'width' => '90px', 'valign'=>'top'),
	);	
	
	$results = db_query('select sum(debet-kredit) as saldo from v_kasumumdaerah where tanggal<:tglawal', array(':tglawal'=>$tglawal));
	
	foreach ($results as $data) {
		$saldo = $data->saldo;	
	}
	
	$rows = array();
	$rows[] = array(
					array('data' => '', 'align' => 'right', 'valign'=>'top'),
					array('data' => '', 'align' => 'left', 'valign'=>'top'),
					array('data' => 'SALDO AWAL', 'align' => 'left', 'valign'=>'top'),
					array('data' => '', 'align' => 'left', 'valign'=>'top'),
					array('data' => NULL, 'align' => 'right', 'valign'=>'top'),
					array('data' => NULL, 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($saldo), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
					NULL,
					
				); 

	
	$results = db_query('select jurnalid, tanggal, keterangan, noref, nobukti, debet, kredit from v_kasumumdaerah where tanggal>=:tglawal and tanggal<=:tglakhir order by tanggal,jenis,jurnalid', array(':tglawal'=>$tglawal, ':tglakhir'=>$tglakhir));	
	# build the table fields
	$no=0;
	$totalkdebet=0;$totalkredit=0;		
	
	
	foreach ($results as $data) {
		$no++;  
		
		$saldo += ($data->debet - $data->kredit);
		$keterangan = ($data->keterangan==''? 'Setoran':$data->keterangan);
		$rows[] = array(
						array('data' => $no, 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal), 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
						array('data' => $keterangan, 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
						array('data' => $data->nobukti, 'align' => 'left', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
						array('data' => apbd_fn($data->debet), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
						array('data' => apbd_fn($data->kredit), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
						array('data' => apbd_fn($saldo), 'align' => 'right', 'valign'=>'top', 'style'=>'border:none;font-size:90%'),
						
					);

		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
	
	$rows[] = array(
						array('data' => '<strong>TOTAL</strong>', 'colspan'=>'4', 'align' => 'left', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '<strong>' . apbd_fn($saldo) . '</strong>', 'align' => 'right', 'valign'=>'top'),
						array('data' => '', 'align' => 'right', 'valign'=>'top'),
					);					
 
			
			 
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}


function getDataPrint($tglawal, $tglakhir) {
	
	$top=array();
	$top[] = array(
			array('data' => 'PEMERINTAH KABUPATEN JEPARA','width' => '510px', 'align'=>'center','style'=>'border:none;'),
			);
	$top[] = array(
			array('data' => 'BUKU KAS UMUM DAERAH','width' => '510px', 'align'=>'center','style'=>'border:none;'),
			);
	
	
	$top[] = array(
						array('data' => 'Tanggal ' . apbd_format_tanggal_pendek($tglawal) . ' s/d. ' . apbd_format_tanggal_pendek($tglakhir) ,'width' => '510px', 'align'=>'center','style'=>'border:none;font-size:90%'),
						
	);
	$top[] = array(
						array('data' => '','width' => '510px', 'align'=>'left','style'=>'border:none;font-size:90%'),
						
	);
	$output = theme('table', array('header' => null, 'rows' => $top ));
	
	$header = array ();
	$header = array (
		array('data' => 'No', 'width' => '25px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Tanggal', 'width' => '45px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Uraian','width' => '170px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'No. Ref', 'width' => '50px','align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Masuk', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Keluar', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
		array('data' => 'Saldo', 'width' => '75px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;'),
	);	

	//SEBELUMNYA
	$rows = array(); 
	$results = db_query('select sum(debet-kredit) as saldo from v_kasumumdaerah where tanggal<:tglawal', array(':tglawal'=>$tglawal));
	
	foreach ($results as $data) {
		$saldo = $data->saldo;	
	}
				
	$rows[] = array(
					array('data' => '', 'width' => '25px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
					array('data' => '', 'width' => '45px', 'align' => 'center', 'style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => 'SALDO AWAL', 'width' => '170px', 'align' => 'left', 'style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => '', 'width' => '50px', 'align' => 'center', 'style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => '', 'width' => '75px', 'align' => 'right', 'style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => '', 'width' => '75px', 'align' => 'right', 'style'=>'font-size:80%;border-right:1px solid black;'),
					array('data' => apbd_fn($saldo), 'width' => '75px', 'align' => 'right', 'style'=>'font-size:80%;border-right:1px solid black;'),
					
				);	

		
	# build the table fields
	$results = db_query('select jurnalid, tanggal, keterangan, noref, nobukti, debet, kredit from v_kasumumdaerah where tanggal>=:tglawal and tanggal<=:tglakhir order by tanggal,jenis,jurnalid', array(':tglawal'=>$tglawal, ':tglakhir'=>$tglakhir));

	$no=0;
	$totalkdebet=0;$totalkredit=0;		
	
	foreach ($results as $data) {
		$no++;  
		
		$saldo += ($data->debet - $data->kredit);
		$keterangan = ($data->keterangan==''? 'Setoran':$data->keterangan);

		$rows[] = array(
						array('data' => $no, 'width' => '25px', 'align'=>'right','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
						array('data' => apbd_format_tanggal_pendek($data->tanggal), 'width' => '45px', 'align' => 'center', 'style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => $keterangan, 'width' => '170px', 'align' => 'left', 'style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => $data->nobukti, 'width' => '50px', 'align' => 'center', 'style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data->debet), 'width' => '75px', 'align' => 'right', 'style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($data->kredit), 'width' => '75px', 'align' => 'right', 'style'=>'font-size:80%;border-right:1px solid black;'),
						array('data' => apbd_fn($saldo), 'width' => '75px', 'align' => 'right', 'style'=>'font-size:80%;border-right:1px solid black;'),
						
					);
		
		$totalkdebet+=$data->debet;
		$totalkredit+=$data->kredit;
	}
	
 
	

	$rows[] = array(
						array('data' => '<strong>TOTAL</strong>', 'colspan'=>'4', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkdebet) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
						array('data' => '<strong>' . apbd_fn($totalkredit) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
						array('data' => '<strong>' . apbd_fn($saldo) . '</strong>', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:2px solid black;'),
					);
					
	
	
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
}

function akuntansi_kud_main_form($form, &$form_state) {
	
	$tglawal = arg(2);
	$tglakhir = arg(3);
	if ($tglawal=='') {
		$tglawal = '2017-01-01';
		$tglakhir = '2017-01-10';
	}
	
	$form['formtanggal'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tanggal Buku Kas<em><small class="text-info pull-right">Klik disini untuk mengubah tanggal</small></em>',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$tglawal_form = strtotime($tglawal);
	$form['formtanggal']['tglawal'] = array(
		'#type' => 'date',
		'#title' =>  t('Periode laporan, mulai tanggal'),
		'#default_value'=> array(
			'year' => format_date($tglawal_form, 'custom', 'Y'),
			'month' => format_date($tglawal_form, 'custom', 'n'), 
			'day' => format_date($tglawal_form, 'custom', 'j'), 
		  ), 		
	);	
	$tglakhir_form = strtotime($tglakhir);
	$form['formtanggal']['tglakhir'] = array(
		'#type' => 'date',
		'#title' =>  t('Sampai tanggal'),
		'#default_value'=> array(
			'year' => format_date($tglakhir_form, 'custom', 'Y'),
			'month' => format_date($tglakhir_form, 'custom', 'n'), 
			'day' => format_date($tglakhir_form, 'custom', 'j'), 
		  ), 		
	);		
	$form['formtanggal']['submit']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-play" aria-hidden="true"></span> Tampilkan',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	
	return $form;
}

function akuntansi_kud_main_form_submit($form, &$form_state) {
	//akuntansi/kud/kodeo/41201006/kodej

	$tglawal = $form_state['values']['tglawal'];
	$tglawalx = apbd_date_convert_form2db($tglawal);
	
	$tglakhir = $form_state['values']['tglakhir'];
	$tglakhirx = apbd_date_convert_form2db($tglakhir);		

	$uri = '/akuntansi/kud/' . $tglawalx  . '/' . $tglakhirx ;
	
	drupal_goto($uri);

}
?>
