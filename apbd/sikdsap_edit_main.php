<?php

function sikdsap_edit_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('sikdsap_edit_main_form');
	return drupal_render($output_form);// . $output;
		
}
 
function sikdsap_edit_main_form($form, &$form_state) {
   

	$preview = arg(1);

	$bulan = 1;
	$res = db_query('select periode from realisasi_sikd limit 1');
	foreach ($res as $data) {
		$bulan = $data->periode;
	}	

	$opt_bulan['1'] = 'Januari';
	$opt_bulan['2'] = 'Februari';
	$opt_bulan['3'] = 'Maret';
	$opt_bulan['4'] = 'April';
	$opt_bulan['5'] = 'Mei';
	$opt_bulan['6'] = 'Juni';
	$opt_bulan['7'] = 'Juli';
	$opt_bulan['8'] = 'Agustus';
	$opt_bulan['9'] = 'September';
	$opt_bulan['10'] = 'Oktober';
	$opt_bulan['11'] = 'Nopember';
	$opt_bulan['12'] = 'Desember';	
	$form['bulan'] = array (
		'#type' => 'item', 
		'#title' =>  t('Bulan'),
		'#markup' => '<p>' . $opt_bulan[$bulan] . '</p>' ,
	);
	$form['submit']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-play"> Generate</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		//'#suffix' => "&nbsp;<a href='' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	$form['preview']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-eye-open"> Preview</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	
	$form['preview_item'] = array (
		'#type' => 'item',
		'#markup' => preview_LRA($bulan),
	);		
	
	
	return $form;
}

function sikdsap_edit_main_form_submit($form, &$form_state) {
	//if($form_state['clicked_button']['#value'] == $form_state['values']['submit']) remap_LRA();
	//drupal_goto('sikdsap');
	
	prepare_LO('6');
}


function remap_LRA() {


//RESET
db_delete('realisasi_sikd_sap')->execute();

//Init
$res = db_query('insert into realisasi_sikd_sap select * from realisasi_sikd');


//Mapping
$res = db_query('update realisasi_sikd_sap set koderoapbd=kodero');
$res = db_query('update realisasi_sikd_sap set kodero=null');
$res = db_query('update realisasi_sikd_sap inner join rekeningmaplra_apbd on realisasi_sikd_sap.koderoapbd = rekeningmaplra_apbd.koderoapbd set realisasi_sikd_sap.kodero=rekeningmaplra_apbd.koderolra');


$res = db_query('update realisasi_sikd_sap inner join rincianobyeksap on realisasi_sikd_sap.kodero=rincianobyeksap.kodero set realisasi_sikd_sap.kodeAkunRincian=substring(rincianobyeksap.kodero, 6,3), realisasi_sikd_sap.namaAkunRincian=rincianobyeksap.uraian');

$res = db_query('update realisasi_sikd_sap inner join obyeksap on left(realisasi_sikd_sap.kodero,5)=obyeksap.kodeo set realisasi_sikd_sap.kodeAkunObjek=substring(obyeksap.kodeo, 4,2), realisasi_sikd_sap.namaAkunObjek=obyeksap.uraian');

$res = db_query('update realisasi_sikd_sap inner join jenissap on left(realisasi_sikd_sap.kodero,3)=jenissap.kodej set realisasi_sikd_sap.kodeAkunJenis=substring(jenissap.kodej, 3,1), realisasi_sikd_sap.namaAkunJenis=jenissap.uraian');

$res = db_query('update realisasi_sikd_sap inner join kelompoksap on left(realisasi_sikd_sap.kodero,2)=kelompoksap.kodek set realisasi_sikd_sap.kodeAkunKelompok=substring(kelompoksap.kodek, 2,1), realisasi_sikd_sap.namaAkunKelompok=kelompoksap.uraian');

$res = db_query('update realisasi_sikd_sap inner join anggaransap on left(realisasi_sikd_sap.kodero,1)=anggaransap.kodea set realisasi_sikd_sap.kodeAkunUtama=anggaransap.kodea, realisasi_sikd_sap.namaAkunUtama=anggaransap.uraian');





}
  
function preview_LRA() {

$bulan = 1;
$res = db_query('select periode from realisasi_sikd limit 1');
foreach ($res as $data) {
	$bulan = $data->periode;
}

$rea_pendapatan_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

//TABEL
$header = array (
	array('data' => 'KODE','width' => '10px', 'valign'=>'top'),
	array('data' => 'URAIAN', 'valign'=>'top'),
	array('data' => 'REALISASI BULAN ' . $bulan, 'width' => '150px', 'valign'=>'top'),
);

$rows = array();

$res = db_query('select kodeAkunUtama, kodeAkunKelompok, namaAkunKelompok, sum(nilaiRealisasi) as realisasi from realisasi_sikd_sap
group by kodeAkunUtama, kodeAkunKelompok, namaAkunKelompok order by kodeAkunUtama, kodeAkunKelompok');
foreach ($res as $data) {
	
	if ($data->kodeAkunUtama=='4') {
		$rea_pendapatan_total += $data->realisasi;


	} elseif ($data->kodeAkunUtama=='5') {
		
		if ($rea_belanja_total==0) {
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>TOTAL PENDAPATAN</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($rea_pendapatan_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);				
		}
		
		$rea_belanja_total += $data->realisasi;
		
		
	} else {
		if ($rea_pembiayaan_netto==0) {
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>TOTAL BELANJA</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($rea_belanja_total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);			

			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
			);			

			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
			);				}
		
		if ($data->kodeAkunKelompok=='2') 
			$rea_pembiayaan_netto -= $data->realisasi;
		else
			$rea_pembiayaan_netto += $data->realisasi;
	}

	$rows[] = array(
		array('data' => $data->kodeAkunUtama . $data->kodeAkunKelompok, 'align' => 'left', 'valign'=>'top'),
		array('data' => $data->namaAkunKelompok, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($data->realisasi), 'align' => 'right', 'valign'=>'top'),
	);
	
}
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_pembiayaan_netto) . '</strong>','align' => 'right',  'valign'=>'top'),
);	
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '', 'align' => 'right', 'valign'=>'top'),
);	
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SILPA</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_pendapatan_total - $rea_belanja_total + $rea_pembiayaan_netto) . '</strong>', 'align' => 'right',  'valign'=>'top'),
);	
	

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function prepare_LO($bulan) {

drupal_set_message($bulan);

//RESET
db_delete('realisasi_sikd_sap_lo')
	->execute();
	
//PENDAPATAN 
$resrek = db_query('SELECT LEFT(ji.kodero, 5) kodeo, sum(ji.kredit-ji.debet) nilaiLo FROM {jurnalitemlo} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE LEFT(ji.kodero,1)=:kelompok AND month(j.tanggal)<=:bulan GROUP BY LEFT(ji.kodero, 5)', array(':kelompok' => '8', ':bulan' => $bulan));
foreach ($resrek as $datarek) {
	
	if ($datarek->nilaiLo>0) {
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek from q_rekeninglengkap_sap_objek where kodeo=:kodeo', array(':kodeo' => $datarek->kodeo));
		foreach ($resinforek as $datainforek) {

			drupal_set_message($datarek->kodeo);

			db_insert('realisasi_sikd_sap_lo')
			->fields(array('kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'nilaiLo', 'kodeo'))
			->values(array(
					

				'kodeAkunUtama' => substr($datarek->kodeo, 0,1), 
				'namaAkunUtama' => $datainforek->namaakunutama, 
				'kodeAkunKelompok' => substr($datarek->kodeo, 1,1), 
				'namaAkunKelompok' => $datainforek->namaakunkelompok, 
				'kodeAkunJenis' => substr($datarek->kodeo, 2,1), 
				'namaAkunJenis' => $datainforek->namaakunjenis, 
				'kodeAkunObjek' => substr($datarek->kodeo, 3,2), 
				'namaAkunObjek' => $datainforek->namaakunobjek, 
				'nilaiLo' => $datarek->nilaiLo, 
				'kodeo' => $datarek->kodeo,
				))
			->execute();							
		}	
		
	}
}

//BEBAN
$resrek = db_query('SELECT LEFT(ji.kodero, 5) kodeo, sum(ji.debet-ji.kredit) nilaiLo FROM {jurnalitemlo} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE LEFT(ji.kodero,1)=:kelompok AND month(j.tanggal)<=:bulan GROUP BY LEFT(ji.kodero, 5)', array(':kelompok' => '9', ':bulan' => $bulan));
foreach ($resrek as $datarek) {
	drupal_set_message($datarek->kodeo);
	
	if ($datarek->nilaiLo>0) {
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek from q_rekeninglengkap_sap_objek where kodeo=:kodeo', array(':kodeo' => $datarek->kodeo));
		foreach ($resinforek as $datainforek) {

			db_insert('realisasi_sikd_sap_lo')
			->fields(array('kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'nilaiLo', 'kodeo'))
			->values(array(
					

				'kodeAkunUtama' => substr($datarek->kodeo, 0,1), 
				'namaAkunUtama' => $datainforek->namaakunutama, 
				'kodeAkunKelompok' => substr($datarek->kodeo, 1,1), 
				'namaAkunKelompok' => $datainforek->namaakunkelompok, 
				'kodeAkunJenis' => substr($datarek->kodeo, 2,1), 
				'namaAkunJenis' => $datainforek->namaakunjenis, 
				'kodeAkunObjek' => substr($datarek->kodeo, 3,2), 
				'namaAkunObjek' => $datainforek->namaakunobjek, 
				'nilaiLo' => $datarek->nilaiLo, 
				'kodeo' => $datarek->kodeo,
				))
			->execute();							
		}	
		
	}
}



}



?>