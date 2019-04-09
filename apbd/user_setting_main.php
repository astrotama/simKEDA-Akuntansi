<?php

function user_setting_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('user_setting_main_form');
	return drupal_render($output_form);// . $output;
		
}
 
function user_setting_main_form($form, &$form_state) {
   

	$bulan = arg(1);
	$preview = arg(2);
	
	if ($bulan=='') {
		$bulan = date('n');
		if ($bulan==1)
			$bulan = 12;
		else
			$bulan--;
	}
	
	drupal_set_message('Haloovvv');
	
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
		'#type' => 'select', 
		'#title' =>  t('Bulan'),
		'#options' => $opt_bulan,
		'#default_value' => $bulan,
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
	
	if ($preview=='preview') {
		$form['preview_item'] = array (
			'#type' => 'item',
			'#markup' => preview_LRA(),
		);		
	}
	
	
	return $form;
}

function user_setting_main_form_submit($form, &$form_state) {
	$bulan = $form_state['values']['bulan'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['preview']) {
		drupal_goto('sikd/' . $bulan . '/preview');
	} else {
		//prepare_LRA($bulan);
		//drupal_goto('sikd/' . $bulan . '/preview');
		prepare_Anggaran();
	}
	
	//jurnalkan_balik();
}


function prepare_LRA_ByAgg($bulan) {

//RESET
db_delete('realisasi_sikd')
	->execute();
	
//PENDAPATAN 
$reskegmaster = db_query('select kodeuk,kodero from anggperuk');
//$reskegmaster = db_query('select distinct kodeuk from jurnal where month(tanggal)<=:bulan and jurnalid in (select jurnalid from jurnalitem where left(kodero,1)=:empat)', array(':bulan' => $bulan, ':empat' => '4'));
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		//$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.jenis=:jenis AND LEFT(ji.kodero,1)=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':jenis' => 'pad', ':kelompok' => '4', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
		$resrek = db_query('SELECT sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE ji.kodero=:kodero AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan', array( ':kodero' => $datakegmaster->kodero, ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
		foreach ($resrek as $datarek) {
			
				
				$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
				foreach ($resinforek as $datainforek) {

					db_insert('realisasi_sikd')
					->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
					->values(array(
							
						'periode' => $bulan, 
						'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
						'namaUrusanProgram' => $datakeg->urusan, 
						'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
						'namaUrusanPelaksana' => $datakeg->urusan, 
						'kodeSKPD' => $datakegmaster->kodeuk . '00', 
						'namaSKPD' => $datakeg->namauk, 
						'kodeProgram' => '000', 
						'namaProgram' => 'Non Program', 
						'kodeKegiatan' => '000000', 
						'namaKegiatan' => 'Non Kegiatan', 
						'kodeFungsi' => $datakeg->kodef, 
						'namaFungsi' => $datakeg->fungsi, 
						'kodeAkunUtama' => substr($datakegmaster->kodero, 0,1), 
						'namaAkunUtama' => $datainforek->namaakunutama, 
						'kodeAkunKelompok' => substr($datakegmaster->kodero, 1,1), 
						'namaAkunKelompok' => $datainforek->namaakunkelompok, 
						'kodeAkunJenis' => substr($datakegmaster->kodero, 2,1), 
						'namaAkunJenis' => $datainforek->namaakunjenis, 
						'kodeAkunObjek' => substr($datakegmaster->kodero, 3,2), 
						'namaAkunObjek' => $datainforek->namaakunobjek, 
						'kodeAkunRincian' => substr($datakegmaster->kodero, -3), 
						'namaAkunRincian' => $datainforek->namaakunrincian, 
						'kodeAkunSub' => '', 
						'namaAkunSub' => '', 
						'nilaiRealisasi' => $datarek->realisasi, 
						))
					->execute();							
				}	
				
			
		}

	}		
}

//BELANJA
$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd}');
foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		$resrek = db_query('SELECT ji.kodero, sum(ji.debet-ji.kredit) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.kodekeg=:kodekeg AND LEFT(ji.kodero,1)>=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':kodekeg' => $datakegmaster->kodekeg, ':kelompok' => '5', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
		
		foreach ($resrek as $datarek) {
				
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {
			
				db_insert('realisasi_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi'))
				->values(array(
						
					'periode' => $bulan, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusandinas, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => $datakeg->kodepro, 
					'namaProgram' => $datakeg->program, 
					'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
					'namaKegiatan' => $datakeg->kegiatan, 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiRealisasi' => $datarek->realisasi, 
				))
				->execute();	
			}	
				
		}

	}	
}	


}

function prepare_LRA($bulan) {

//drupal_set_message($bulan);

//RESET
db_delete('realisasi_sikd')
	->execute();
	
//PENDAPATAN 
//$reskegmaster = db_query('select distinct kodeuk from jurnal where jenis in (:pad, :nol) and month(tanggal)<=:bulan', array(':pad' => 'pad', ':nol' => '0', ':bulan' => $bulan));
$reskegmaster = db_query('select distinct kodeuk from anggperuk');
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		//$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE jenis in (:pad, :nol) AND LEFT(ji.kodero,1)>=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':pad' => 'pad', ':nol' => '0', ':kelompok' => '4', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
		$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE LEFT(ji.kodero,1)=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':kelompok' => '4', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
		foreach ($resrek as $datarek) {
			
				if ($datarek->realisasi>0) {
					$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
					foreach ($resinforek as $datainforek) {

						db_insert('realisasi_sikd')
						->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi', 'kodero'))
						->values(array(
								
							'periode' => $bulan, 
							'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
							'namaUrusanProgram' => $datakeg->urusan, 
							'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
							'namaUrusanPelaksana' => $datakeg->urusan, 
							'kodeSKPD' => $datakegmaster->kodeuk . '00', 
							'namaSKPD' => $datakeg->namauk, 
							'kodeProgram' => '000', 
							'namaProgram' => 'Non Program', 
							'kodeKegiatan' => '000000', 
							'namaKegiatan' => 'Non Kegiatan', 
							'kodeFungsi' => $datakeg->kodef, 
							'namaFungsi' => $datakeg->fungsi, 
							'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
							'namaAkunUtama' => $datainforek->namaakunutama, 
							'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
							'namaAkunKelompok' => $datainforek->namaakunkelompok, 
							'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
							'namaAkunJenis' => $datainforek->namaakunjenis, 
							'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
							'namaAkunObjek' => $datainforek->namaakunobjek, 
							'kodeAkunRincian' => substr($datarek->kodero, -3), 
							'namaAkunRincian' => $datainforek->namaakunrincian, 
							'kodeAkunSub' => '', 
							'namaAkunSub' => '', 
							'nilaiRealisasi' => $datarek->realisasi, 
							'kodero' => $datarek->kodero,
							))
						->execute();							
					}	
					
				}
		}

	}		
}

//BELANJA
//$reskegmaster = db_query('select distinct kodeuk, kodekeg from jurnal where jenis in (:spj,:umum) and month(tanggal)<=:bulan', array(':spj' => 'spj', ':umum' => 'umum-spj', ':bulan' => $bulan));
$reskegmaster = db_query('select distinct jurnal.kodeuk, jurnal.kodekeg from jurnal inner join jurnalitem on jurnal.jurnalid=jurnalitem.jurnalid where month(jurnal.tanggal)<=:bulan and left(jurnalitem.kodero,1)=:belanja', array(':belanja' => '5', ':bulan' => $bulan));

foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
		
		//REKENING
		$resrek = db_query('SELECT ji.kodero, sum(ji.debet-ji.kredit) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.kodekeg=:kodekeg AND LEFT(ji.kodero,1)>=:kelompok AND j.kodeuk=:kodeuk AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':kodekeg' => $datakegmaster->kodekeg, ':kelompok' => '5', ':kodeuk' => $datakegmaster->kodeuk, ':bulan' => $bulan));
		
		foreach ($resrek as $datarek) {
			if ($datarek->realisasi>0) {
	
				$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
				foreach ($resinforek as $datainforek) {
				
					db_insert('realisasi_sikd')
					->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi', 'kodero'))
					->values(array(
							
						'periode' => $bulan, 
						'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
						'namaUrusanProgram' => $datakeg->urusan, 
						'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
						'namaUrusanPelaksana' => $datakeg->urusandinas, 
						'kodeSKPD' => $datakegmaster->kodeuk . '00', 
						'namaSKPD' => $datakeg->namauk, 
						'kodeProgram' => $datakeg->kodepro, 
						'namaProgram' => $datakeg->program, 
						'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
						'namaKegiatan' => $datakeg->kegiatan, 
						'kodeFungsi' => $datakeg->kodef, 
						'namaFungsi' => $datakeg->fungsi, 
						'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
						'namaAkunUtama' => $datainforek->namaakunutama, 
						'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
						'namaAkunKelompok' => $datainforek->namaakunkelompok, 
						'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
						'namaAkunJenis' => $datainforek->namaakunjenis, 
						'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
						'namaAkunObjek' => $datainforek->namaakunobjek, 
						'kodeAkunRincian' => substr($datarek->kodero, -3), 
						'namaAkunRincian' => $datainforek->namaakunrincian, 
						'kodeAkunSub' => '', 
						'namaAkunSub' => '', 
						'nilaiRealisasi' => $datarek->realisasi, 
						'kodero' => $datarek->kodero,
					))
					->execute();	
				}
			}	
					
		}
	}	
}	

//PEMBIAYAAN
$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => '00'));
foreach ($reskeg as $datakeg) {
 
	//REKENING
	$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) masuk, sum(ji.debet-ji.kredit) keluar FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE LEFT(ji.kodero,1)>=:kelompok AND month(j.tanggal)<=:bulan GROUP BY ji.kodero', array(':kelompok' => '6', ':bulan' => $bulan));
	foreach ($resrek as $datarek) {
		
		$realisasi = (substr(($datarek->kodero), 0, 2)=='61' ? $datarek->masuk : $datarek->keluar);	
		if ($realisasi>0) {	
			
			
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {

				db_insert('realisasi_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiRealisasi', 'kodero'))
				->values(array(
						
					'periode' => $bulan, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusan, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => '000', 
					'namaProgram' => 'Non Program', 
					'kodeKegiatan' => '000000', 
					'namaKegiatan' => 'Non Kegiatan', 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiRealisasi' => $realisasi, 
					'kodero' => $datarek->kodero,
					))
				->execute();							
			}	
			
		}
	}

}		

}

function remap_LRA() {

//drupal_set_message($bulan);

//RESET
db_delete('realisasi_sikd_sap')->execute();

//Init
$res = db_query('insert into realisasi_sikd_sap select * from realisasi_sikd');

//Mapping
$res = db_query('update realisasi_sikd_sap set koderoapbd=kodero');
$res = db_query('update realisasi_sikd_sap set kodero=null');
$res = db_query('update realisasi_sikd_sap inner join rekeningmaplra_apbd on realisasi_sikd_sap.koderoapbd = rekeningmaplra_apbd.koderoapbd set realisasi_sikd_sap.kodero=rekeningmaplra_apbd.koderolra');

$res = db_query('update realisasi_sikd_sap inner join rincianobyeksap on realisasi_sikd_sap.kodero=rincianobyeksap.kodero set realisasi_sikd_sap.namaAkunRincian=rincianobyeksap.uraian');
$res = db_query('update realisasi_sikd_sap inner join obyeksap on left(realisasi_sikd_sap.kodero,5)=obyeksap.kodeo set realisasi_sikd_sap.namaAkunObjek=obyeksap.uraian');
$res = db_query('update realisasi_sikd_sap inner join jenissap on left(realisasi_sikd_sap.kodero,3)=jenissap.kodej set realisasi_sikd_sap.namaAkunJenis=jenissap.uraian');
$res = db_query('update realisasi_sikd_sap inner join kelompoksap on left(realisasi_sikd_sap.kodero,2)=kelompoksap.kodek set realisasi_sikd_sap.namaAkunKelompok=kelompoksap.uraian');
$res = db_query('update realisasi_sikd_sap inner join anggaransap on left(realisasi_sikd_sap.kodero,1)=anggaransap.kodea set realisasi_sikd_sap.namaAkunUtama=anggaransap.uraian');





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

$rpt_pendapatan_total = 0;
$rpt_belanja_total = 0;
$rpt_pembiayaan_netto = 0;

//TABEL
$header = array (
	array('data' => 'KODE','width' => '10px', 'valign'=>'top'),
	array('data' => 'URAIAN', 'valign'=>'top'),
	array('data' => 'SIKD', 'width' => '150px', 'valign'=>'top'),
	array('data' => 'REALISASI', 'width' => '150px', 'valign'=>'top'),
);

$rows = array();

//$res = db_query('select kodeAkunUtama, kodeAkunKelompok, namaAkunKelompok, sum(nilaiRealisasi) as realisasi from realisasi_sikd group by kodeAkunUtama, kodeAkunKelompok, namaAkunKelompok');
$res = db_query('select kodeAkunUtama, kodeAkunKelompok, namaAkunKelompok, sum(nilaiRealisasi) as realisasi from realisasi_sikd group by kodeAkunUtama, kodeAkunKelompok, namaAkunKelompok');

foreach ($res as $data) {
	
	
	$realisasi = 0;
	if ($data->kodeAkunUtama=='4') {
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok) . '%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$resx = $sql->execute();
		foreach ($resx as $datax) {
			$realisasi = $datax->realisasi;
		}			
		
	} elseif ($data->kodeAkunUtama=='5') { 
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('keg.inaktif', '0', '='); 
		$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok) . '%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$resx = $sql->execute();
		foreach ($resx as $datax) {
			$realisasi = $datax->realisasi;
		}
		
	} else {
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok) . '%', 'LIKE'); 
		$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		$resx = $sql->execute();
		foreach ($resx as $datax) {
			$realisasi = (($data->kodeAkunKelompok=='1') ? $datax->kreditdebet : $datax->debetkredit);
		}		
	}
	
	$realisasi_kelompok = $realisasi;
	
	$style = (($data->realisasi==$realisasi) ? '' : 'color: red;');
	
	$rows[] = array(
		array('data' => $data->kodeAkunUtama . $data->kodeAkunKelompok, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $data->namaAkunKelompok . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($data->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
	); 
	
	//Jenis
	$res_jenis = db_query('select kodeAkunJenis, namaAkunJenis, sum(nilaiRealisasi) as realisasi from realisasi_sikd where kodeAkunUtama=:kodeAkunUtama and kodeAkunKelompok=:kodeAkunKelompok group by kodeAkunJenis, namaAkunJenis', array(':kodeAkunUtama'=>$data->kodeAkunUtama, ':kodeAkunKelompok'=>$data->kodeAkunKelompok));
	foreach ($res_jenis as $data_jenis) {

		$realisasi = 0;
		if ($data->kodeAkunUtama=='4') {
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis) . '%', 'LIKE'); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$resx = $sql->execute();
			foreach ($resx as $datax) {
				$realisasi = $datax->realisasi;
			}			
			
		} elseif ($data->kodeAkunUtama=='5') { 
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.inaktif', '0', '='); 
			$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis) . '%', 'LIKE'); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$resx = $sql->execute();
			foreach ($resx as $datax) {
				$realisasi = $datax->realisasi;
			}
			
		} else {
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis) . '%', 'LIKE'); 
			$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
			$resx = $sql->execute();
			foreach ($resx as $datax) {
				$realisasi = (($data->kodeAkunKelompok=='1') ? $datax->kreditdebet : $datax->debetkredit);
			}		
		}	
		
		$style = (($data_jenis->realisasi==$realisasi) ? '' : 'color: red;');
		$rows[] = array(
			array('data' => $data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis, 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_jenis->namaAkunJenis . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_jenis->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
		);
		
		
		//Obyek
		$res_oby = db_query('select kodeAkunObjek, namaAkunObjek, sum(nilaiRealisasi) as realisasi from realisasi_sikd where kodeAkunUtama=:kodeAkunUtama and kodeAkunKelompok=:kodeAkunKelompok and kodeAkunJenis=:kodeAkunJenis group by kodeAkunObjek, namaAkunObjek', array(':kodeAkunUtama'=>$data->kodeAkunUtama, ':kodeAkunKelompok'=>$data->kodeAkunKelompok, ':kodeAkunJenis'=>$data_jenis->kodeAkunJenis));
		foreach ($res_oby as $data_oby) {

			$realisasi = 0;
			if ($data->kodeAkunUtama=='4') {
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
				$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek) . '%', 'LIKE'); 
				$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$resx = $sql->execute();
				foreach ($resx as $datax) {
					$realisasi = $datax->realisasi;
				}			
				
			} elseif ($data->kodeAkunUtama=='5') { 
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('keg.inaktif', '0', '='); 
				$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek) . '%', 'LIKE'); 
				$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$resx = $sql->execute();
				foreach ($resx as $datax) {
					$realisasi = $datax->realisasi;
				}
				
			} else {
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
				$sql->condition('ji.kodero', db_like($data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek) . '%', 'LIKE'); 
				$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
				$resx = $sql->execute();
				foreach ($resx as $datax) {
					$realisasi = (($data->kodeAkunKelompok=='1') ? $datax->kreditdebet : $datax->debetkredit);
				}		
			}	
			
			$style = (($data_oby->realisasi==$realisasi) ? '' : 'color: red;');	
			$rows[] = array(
				array('data' => $data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data_oby->namaAkunObjek, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_oby->realisasi), 'align' => 'right', 'valign'=>'top', 'style'=>$style),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top', 'style'=>$style),
			);

			//Rincian
			/*
			$res_det = db_query('select kodeAkunRincian, namaAkunRincian, sum(nilaiRealisasi) as realisasi from realisasi_sikd where kodeAkunUtama=:kodeAkunUtama and kodeAkunKelompok=:kodeAkunKelompok and kodeAkunJenis=:kodeAkunJenis and kodeAkunObjek=:kodeAkunObjek group by kodeAkunRincian, namaAkunRincian', array(':kodeAkunUtama'=>$data->kodeAkunUtama, ':kodeAkunKelompok'=>$data->kodeAkunKelompok, ':kodeAkunJenis'=>$data_jenis->kodeAkunJenis, ':kodeAkunObjek'=>$data_oby->kodeAkunObjek));
			foreach ($res_det as $data_det) {

				if ($data->kodeAkunUtama=='4') {
					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
					$sql->condition('ji.kodero', $data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek . $data_det->kodeAkunRincian, '='); 
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$resx = $sql->execute();
					foreach ($resx as $datax) {
						$realisasi = $datax->realisasi;
					}			
					
				} elseif ($data->kodeAkunUtama=='5') { 
					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
					$sql->condition('keg.inaktif', '0', '='); 
					$sql->condition('ji.kodero', $data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek . $data_det->kodeAkunRincian, '='); 
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$resx = $sql->execute();
					foreach ($resx as $datax) {
						$realisasi = $datax->realisasi;
					}
					
				} else {
					$sql = db_select('jurnal', 'j');
					$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
					$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
					$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
					$sql->condition('ji.kodero', $data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek . $data_det->kodeAkunRincian, '='); 
					$sql->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
					$resx = $sql->execute();
					foreach ($resx as $datax) {
						$realisasi = (($data->kodeAkunKelompok=='1') ? $datax->kreditdebet : $datax->debetkredit);
					}		
				}	
				
				$style = (($data_det->realisasi==$realisasi) ? '' : 'color: red;');				
				$rows[] = array(
					array('data' => $data->kodeAkunUtama . $data->kodeAkunKelompok .  $data_jenis->kodeAkunJenis .  $data_oby->kodeAkunObjek .  $data_det->kodeAkunRincian, 'align' => 'left', 'valign'=>'top'),
					array('data' => '<em>' . $data_det->namaAkunRincian . '</em>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<em>' . apbd_fn($data_det->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
					array('data' => '<em>' . apbd_fn($realisasi) . '</em>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
				);
				
			}
			*/
			
			
		}
		
		
		
	}
	
	if ($data->kodeAkunUtama=='4') {
		$rea_pendapatan_total += $data->realisasi;
		$rpt_pendapatan_total += $realisasi_kelompok;
		
		$style = (($rea_pendapatan_total==$rpt_pendapatan_total) ? '' : 'color: red;');
		if ($data->kodeAkunKelompok=='3') {
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>TOTAL PENDAPATAN</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($rea_pendapatan_total) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
				array('data' => '<strong>' . apbd_fn($rpt_pendapatan_total) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
			);			
		}

	} elseif ($data->kodeAkunUtama=='5') {
		$rea_belanja_total += $data->realisasi;
		$rpt_belanja_total += $realisasi_kelompok;
		
		$style = (($rea_belanja_total==$rpt_belanja_total) ? '' : 'color: red;');
		if ($data->kodeAkunKelompok=='2') {
			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>TOTAL BELANJA</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($rea_belanja_total) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
				array('data' => '<strong>' . apbd_fn($rpt_belanja_total) . '</strong>', 'align' => 'right', 'valign'=>'top', 'style'=>$style),
			);			

			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
			);			

			$rows[] = array(
				array('data' => '', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
				array('data' => '', 'align' => 'right', 'valign'=>'top'),
			);			
			 
		}		
		
	} else {
		
		if ($data->kodeAkunKelompok=='2') {
			$rea_pembiayaan_netto -= $data->realisasi;
			$rpt_pembiayaan_netto -= $realisasi_kelompok;
			
		} else {
			$rea_pembiayaan_netto += $data->realisasi;
			$rpt_pembiayaan_netto += $realisasi_kelompok;
		}
	}
		
}

$style = (($rea_pembiayaan_netto==$rpt_pembiayaan_netto) ? '' : 'color: red;');
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_pembiayaan_netto) . '</strong>','align' => 'right',  'valign'=>'top', 'style'=>$style),
	array('data' => '<strong>' . apbd_fn($rpt_pembiayaan_netto) . '</strong>','align' => 'right',  'valign'=>'top', 'style'=>$style),
);	
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '', 'align' => 'right', 'valign'=>'top'),
);	

$rea_silpa = $rea_pendapatan_total - $rea_belanja_total + $rea_pembiayaan_netto;
$rpt_silpa = $rpt_pendapatan_total - $rpt_belanja_total + $rpt_pembiayaan_netto;
$style = (($rea_silpa==$rpt_silpa) ? '' : 'color: red;');
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SILPA</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($rea_silpa) . '</strong>', 'align' => 'right',  'valign'=>'top', 'style'=>$style),
	array('data' => '<strong>' . apbd_fn($rpt_silpa) . '</strong>', 'align' => 'right',  'valign'=>'top', 'style'=>$style),
);	
	

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}


function jurnalkan() {

	$query = db_select('dokumen26', 'd');
	$query->join('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$query->fields('d', array('dokid','keperluan', 'kodekeg', 'sp2dno', 'sp2dtgl', 'sppno', 'spptgl', 'spmno', 'spmtgl', 'jumlah', 'jenisdokumen'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	$query->fields('k', array('kegiatan'));
	
		
	$results = $query->execute();
	foreach ($results as $data) {

		drupal_set_message($data->sp2dno);
		
		$dokid = $data->dokid;
		$keperluan = $data->keperluan;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		$kegiatan = $data->kegiatan;
		
		$sp2dno= $data->sp2dno;
		$tanggal= $data->sp2dtgl;		
		$spmno = $data->spmno;
		$sppspm = 'SPP: ' . $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl) . '; SPM: ' . $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
		$jumlah = $data->jumlah;
		
		$jenisdokumen = $data->jenisdokumen;
		

		$suffixjurnal = '';
		$jurnalid = apbd_getkodejurnal($kodeuk);
	
			
		$query = db_insert('jurnal' . $suffixjurnal)
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'jenisdokumen'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $dokid,
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'jenis' => 'spj',
						'nobukti' => $sp2dno,
						'nobuktilain' => '1226_123',
						'tanggal' =>$tanggal,
						'keterangan' => $keperluan, 
						'total' => $jumlah,
						'jenisdokumen' => $jenisdokumen,
					)
				);
		//echo $query;		
		$res = $query->execute();
		
		
		//ITEM KAS
		//APBD		
		if (($jenisdokumen == '3') or ($jenisdokumen == '4')) {			//GAJI & LS
			$rekkasapbd = apbd_getKodeROAPBD();
			$rekkassal = apbd_getKodeROSAL();
			$rekkaslo = apbd_getKodeRORKPPKD();

		} else {
			$rekkasapbd = apbd_getKodeROKasBendaharaPengeluaran();
			$rekkassal = $rekkasapbd;
			$rekkaslo = $rekkasapbd;
		}
		
		
		drupal_set_message($rekkasapbd);
		
		db_insert('jurnalitem' . $suffixjurnal)
			->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
			->values(array(
					'jurnalid'=> $jurnalid,
					'nomor'=> 0,
					'kodero' => $rekkasapbd,
					'kredit' => $jumlah,
					))
			->execute();
			
			
		$query = db_select('dokumenrekening26', 'n');
		$query->join('rincianobyek', 'r', 'n.kodero=r.kodero');
		$query->fields('n', array('kodero', 'jumlah'));
		$query->fields('r', array('uraian'));
		$query->condition('n.dokid', $dokid, '=');
		$query->condition('n.jumlah', 0, '>');
		$res_rek = $query->execute();
		$i = 0;
		foreach ($res_rek as $data_rek) {
			$i++;
			
			drupal_set_message($data_rek->kodero);
			//APBD
			
			db_insert('jurnalitem' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
				->values(array(
						'jurnalid'=> $jurnalid,
						'nomor'=> $i,
						'kodero' => $data_rek->kodero,
						'debet' => $data_rek->jumlah,
						))
				->execute();
			
		}
		
		
	}

		

}	

function jurnalkan_balik() {

	$query = db_select('dokumen26', 'd');
	$query->join('unitkerja', 'u', 'd.kodeuk=u.kodeuk');
	$query->join('kegiatanskpd', 'k', 'd.kodekeg=k.kodekeg');
	$query->fields('d', array('dokid','keperluan', 'kodekeg', 'sp2dno', 'sp2dtgl', 'sppno', 'spptgl', 'spmno', 'spmtgl', 'jumlah', 'jenisdokumen'));
	$query->fields('u', array('kodeuk', 'namasingkat'));
	$query->fields('k', array('kegiatan'));
	$query->condition('d.sp2dok', '2', '=');
		
	$results = $query->execute();
	foreach ($results as $data) {

		drupal_set_message($data->sp2dno);
		
		$dokid = $data->dokid;
		$keperluan = 'Koreksi ' . $data->keperluan;
		$skpd = $data->namasingkat;
		$kodeuk = $data->kodeuk;

		$kodekeg = $data->kodekeg;
		$kegiatan = $data->kegiatan;
		
		$sp2dno= $data->sp2dno;
		$tanggal= $data->sp2dtgl;		
		$spmno = $data->spmno;
		$sppspm = 'SPP: ' . $data->sppno . ', tanggal ' . apbd_format_tanggal_pendek($data->spptgl) . '; SPM: ' . $data->spmno . ', tanggal ' . apbd_format_tanggal_pendek($data->spmtgl);
		$jumlah = $data->jumlah;
		
		$jenisdokumen = $data->jenisdokumen;
		

		$suffixjurnal = '';
		$jurnalid = apbd_getkodejurnal($kodeuk);
	
			
		$query = db_insert('jurnal' . $suffixjurnal)
				->fields(array('jurnalid', 'refid', 'kodekeg', 'kodeuk', 'jenis', 'nobukti', 'nobuktilain', 'tanggal', 'keterangan', 'total', 'jenisdokumen'))
				->values(
					array(
						'jurnalid'=> $jurnalid,
						'refid' => $dokid,
						'kodekeg' => $kodekeg,
						'kodeuk' => $kodeuk,
						'jenis' => 'spj',
						'nobukti' => $sp2dno,
						'nobuktilain' => '1226_123_x',
						'tanggal' =>$tanggal,
						'keterangan' => $keperluan, 
						'total' => $jumlah,
						'jenisdokumen' => $jenisdokumen,
					)
				);
		
		//echo $query;		
		$res = $query->execute();
		
		
		//ITEM KAS
		//APBD		
		if (($jenisdokumen == '3') or ($jenisdokumen == '4')) {			//GAJI & LS
			$rekkasapbd = apbd_getKodeROAPBD();
			$rekkassal = apbd_getKodeROSAL();
			$rekkaslo = apbd_getKodeRORKPPKD();

		} else {
			$rekkasapbd = apbd_getKodeROKasBendaharaPengeluaran();
			$rekkassal = $rekkasapbd;
			$rekkaslo = $rekkasapbd;
		}
		
		
		drupal_set_message($rekkasapbd);
		
		db_insert('jurnalitem' . $suffixjurnal)
			->fields(array('jurnalid', 'nomor', 'kodero', 'debet'))
			->values(array(
					'jurnalid'=> $jurnalid,
					'nomor'=> 0,
					'kodero' => $rekkasapbd,
					'debet' => $jumlah,
					))
			->execute();
			
			
		$query = db_select('dokumenrekening26', 'n');
		$query->join('rincianobyek', 'r', 'n.kodero=r.kodero');
		$query->fields('n', array('kodero', 'jumlah'));
		$query->fields('r', array('uraian'));
		$query->condition('n.dokid', $dokid, '=');
		$query->condition('n.jumlah', 0, '>');
		$res_rek = $query->execute();
		$i = 0;
		foreach ($res_rek as $data_rek) {
			$i++;
			
			drupal_set_message($data_rek->kodero);
			//APBD
			
			db_insert('jurnalitem' . $suffixjurnal)
				->fields(array('jurnalid', 'nomor', 'kodero', 'kredit'))
				->values(array(
						'jurnalid'=> $jurnalid,
						'nomor'=> $i,
						'kodero' => $data_rek->kodero,
						'kredit' => $data_rek->jumlah,
						))
				->execute();
			
		}
		
		
	}

		

}	

function prepare_Anggaran() {
$periode = 1;	

//RESET
db_delete('anggaran_sikd')
	->execute();

//PENDAPATAN 
$reskegmaster = db_query('select kodeuk,kodero,jumlah from anggperuk');
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
	foreach ($reskeg as $datakeg) {
	 
				
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
		foreach ($resinforek as $datainforek) {

			db_insert('anggaran_sikd')
			->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiAnggaran'))
			->values(array(
					
				'periode' => $periode, 
				'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
				'namaUrusanProgram' => $datakeg->urusan, 
				'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
				'namaUrusanPelaksana' => $datakeg->urusan, 
				'kodeSKPD' => $datakegmaster->kodeuk . '00', 
				'namaSKPD' => $datakeg->namauk, 
				'kodeProgram' => '000', 
				'namaProgram' => 'Non Program', 
				'kodeKegiatan' => '000000', 
				'namaKegiatan' => 'Non Kegiatan', 
				'kodeFungsi' => $datakeg->kodef, 
				'namaFungsi' => $datakeg->fungsi, 
				'kodeAkunUtama' => substr($datakegmaster->kodero, 0,1), 
				'namaAkunUtama' => $datainforek->namaakunutama, 
				'kodeAkunKelompok' => substr($datakegmaster->kodero, 1,1), 
				'namaAkunKelompok' => $datainforek->namaakunkelompok, 
				'kodeAkunJenis' => substr($datakegmaster->kodero, 2,1), 
				'namaAkunJenis' => $datainforek->namaakunjenis, 
				'kodeAkunObjek' => substr($datakegmaster->kodero, 3,2), 
				'namaAkunObjek' => $datainforek->namaakunobjek, 
				'kodeAkunRincian' => substr($datakegmaster->kodero, -3), 
				'namaAkunRincian' => $datainforek->namaakunrincian, 
				'kodeAkunSub' => '', 
				'namaAkunSub' => '', 
				'nilaiAnggaran' => $datakegmaster->jumlah, 
				))
			->execute();							
		}	
		
	

	}		
}

//BELANJA
$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0');
foreach ($reskegmaster as $datakegmaster) {
	
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
	foreach ($reskeg as $datakeg) {
	 
		//REKENING
		$resrek = db_query('SELECT kodero, jumlah FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $datakegmaster->kodekeg));
		
		foreach ($resrek as $datarek) {
				
			$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
			foreach ($resinforek as $datainforek) {
			
				db_insert('anggaran_sikd')
				->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiAnggaran'))
				->values(array(
						
					'periode' => $periode, 
					'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
					'namaUrusanProgram' => $datakeg->urusan, 
					'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
					'namaUrusanPelaksana' => $datakeg->urusandinas, 
					'kodeSKPD' => $datakegmaster->kodeuk . '00', 
					'namaSKPD' => $datakeg->namauk, 
					'kodeProgram' => $datakeg->kodepro, 
					'namaProgram' => $datakeg->program, 
					'kodeKegiatan' => substr($datakegmaster->kodekeg,-6), 
					'namaKegiatan' => $datakeg->kegiatan, 
					'kodeFungsi' => $datakeg->kodef, 
					'namaFungsi' => $datakeg->fungsi, 
					'kodeAkunUtama' => substr($datarek->kodero, 0,1), 
					'namaAkunUtama' => $datainforek->namaakunutama, 
					'kodeAkunKelompok' => substr($datarek->kodero, 1,1), 
					'namaAkunKelompok' => $datainforek->namaakunkelompok, 
					'kodeAkunJenis' => substr($datarek->kodero, 2,1), 
					'namaAkunJenis' => $datainforek->namaakunjenis, 
					'kodeAkunObjek' => substr($datarek->kodero, 3,2), 
					'namaAkunObjek' => $datainforek->namaakunobjek, 
					'kodeAkunRincian' => substr($datarek->kodero, -3), 
					'namaAkunRincian' => $datainforek->namaakunrincian, 
					'kodeAkunSub' => '', 
					'namaAkunSub' => '', 
					'nilaiAnggaran' => $datarek->jumlah, 
				))
				->execute();	
			}	
				
		}

	}	
}	

//PENERIMAAN PEMBIAYAAN
$reskegmaster = db_query('select kodeuk,kodero,jumlah from anggperda');
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.namauk from unitkerja uk inner join urusan u on uk.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => '81'));
	foreach ($reskeg as $datakeg) {
	 
				
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobjek, namaakunrincian from q_rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
		foreach ($resinforek as $datainforek) {

			db_insert('anggaran_sikd')
			->fields(array('periode', 'kodeUrusanProgram', 'namaUrusanProgram', 'kodeUrusanPelaksana', 'namaUrusanPelaksana', 'kodeSKPD', 'namaSKPD', 'kodeProgram', 'namaProgram', 'kodeKegiatan', 'namaKegiatan', 'kodeFungsi', 'namaFungsi', 'kodeAkunUtama', 'namaAkunUtama', 'kodeAkunKelompok', 'namaAkunKelompok', 'kodeAkunJenis', 'namaAkunJenis', 'kodeAkunObjek', 'namaAkunObjek', 'kodeAkunRincian', 'namaAkunRincian', 'kodeAkunSub', 'namaAkunSub', 'nilaiAnggaran'))
			->values(array(
					
				'periode' => $periode, 
				'kodeUrusanProgram' => substr($datakeg->kodeu, 0,1) . '.' . substr($datakeg->kodeu, -2) . '.' , 
				'namaUrusanProgram' => $datakeg->urusan, 
				'kodeUrusanPelaksana' => substr($datakeg->kodedinas, 0,1) . '.' . substr($datakeg->kodedinas, 1, 2) . '.', 
				'namaUrusanPelaksana' => $datakeg->urusan, 
				'kodeSKPD' => $datakegmaster->kodeuk . '00', 
				'namaSKPD' => $datakeg->namauk, 
				'kodeProgram' => '000', 
				'namaProgram' => 'Non Program', 
				'kodeKegiatan' => '000000', 
				'namaKegiatan' => 'Non Kegiatan', 
				'kodeFungsi' => $datakeg->kodef, 
				'namaFungsi' => $datakeg->fungsi, 
				'kodeAkunUtama' => substr($datakegmaster->kodero, 0,1), 
				'namaAkunUtama' => $datainforek->namaakunutama, 
				'kodeAkunKelompok' => substr($datakegmaster->kodero, 1,1), 
				'namaAkunKelompok' => $datainforek->namaakunkelompok, 
				'kodeAkunJenis' => substr($datakegmaster->kodero, 2,1), 
				'namaAkunJenis' => $datainforek->namaakunjenis, 
				'kodeAkunObjek' => substr($datakegmaster->kodero, 3,2), 
				'namaAkunObjek' => $datainforek->namaakunobjek, 
				'kodeAkunRincian' => substr($datakegmaster->kodero, -3), 
				'namaAkunRincian' => $datainforek->namaakunrincian, 
				'kodeAkunSub' => '', 
				'namaAkunSub' => '', 
				'nilaiAnggaran' => $datakegmaster->jumlah, 
				))
			->execute();							
		}	
		
	

	}		
}

}



?>