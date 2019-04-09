<?php
function rekening_prognosis_main($arg=NULL, $nama=NULL) {
   

	$output_form = drupal_get_form('rekening_prognosis_main_form');
	return drupal_render($output_form);
	
}

function rekening_prognosis_main_form ($form, &$form_state) {
	$kodek = arg(2);	
	$kodej = arg(3);
	$kodeo = arg(4);
	
	
	//AJAX
	// Rekening dropdown list
	$form['kodek'] = array(
		'#title' => t('Kelompok'),
		'#type' => 'select',
		'#options' => _load_kelompok(),
		'#default_value' => $kodek,
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_kelompok',
			'wrapper' => 'kelompok-wrapper',
		),
	);
	// Wrapper for rekdetil dropdown list
	$form['wrapperjenis'] = array(
		'#prefix' => '<div id="kelompok-wrapper">',
		'#suffix' => '</div>',
	);

	// Options for rekdetil dropdown list
	$options = array('- Pilih Jenis -');
	if (isset($form_state['values']['kodej'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$optionsjenis = _load_jenis($form_state['values']['kodek']);
	} else
		$optionsjenis = _load_jenis($kodek);
	
	// Rekening dropdown list
	$form['wrapperjenis']['kodej'] = array(
		'#title' => t('Jenis'),
		'#type' => 'select',
		'#options' => $optionsjenis,
		'#default_value' => $kodej,
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_jenis',
			'wrapper' => 'jenis-wrapper',
		),
	);
	// Wrapper for rekdetil dropdown list
	$form['wrapperobyek'] = array(
		'#prefix' => '<div id="jenis-wrapper">',
		'#suffix' => '</div>',
	);
	
	if (isset($form_state['values']['kodej'])) $kodej = $form_state['values']['kodej'];
	
	$form['wrapperobyek']['tablerek']= array(
		'#prefix' => '<table class="table table-hover"><tr><th  width="10px">No</th><th width="90px">Kode</th><th>Uraian</th><th width="100px">Prognosis</th></tr>',
		 '#suffix' => '</table>',
	);
	
	$i = 0;
	if (strlen($kodej)<3) {
			$form['wrapperobyek']['tablerek']['kodeo' . $i]= array(
					'#type' => 'value',
					'#value' => null,
			); 
			$form['wrapperobyek']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => null,
					'#suffix' => '</td>',
			); 
			$form['wrapperobyek']['tablerek']['kodeo_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => null,
					'#suffix' => '</td>',
			); 
			$form['wrapperobyek']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => 'Pilih Kelompok, lalu pilih Jenis Rekening',
					'#suffix' => '</td>',
					'#suffix' => '</td>',
			); 
			$form['wrapperobyek']['tablerek']['prognosis' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => 'Isikan prognosis disini',
					'#suffix' => '</td>',
					'#suffix' => '</td></tr>',
			); 		
	} else {
		$results = db_query("SELECT o.kodeo, o.uraian, p.persen from {obyek} as o inner join {prognosis} as p on o.kodeo=p.kodeo where o.kodej=:kodej order by o.kodeo", array(':kodej'=>$kodej));
		foreach ($results as $data) {

			$i++; 
			$form['wrapperobyek']['tablerek']['kodeo' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodeo,
			); 
			$form['wrapperobyek']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,
					'#suffix' => '</td>',
			); 
			$form['wrapperobyek']['tablerek']['kodeo_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodeo,
					'#suffix' => '</td>',
			); 
			$form['wrapperobyek']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->uraian,
					'#suffix' => '</td>',
			); 
			$form['wrapperobyek']['tablerek']['persen' . $data->kodeo . $i]= array(
					'#prefix' => '<td>',
					'#type' => 'textfield',
					'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
					'#size' => 10,					
					'#default_value' => $data->persen,
					'#suffix' => '</td></tr>',
			); 
			
		}
	}

	$form['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	
	$form['submitprint']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	
	$form['submitprognosis']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Hitung',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	

	return $form;
}


function _ajax_jenis($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperobyek'];
}


function _ajax_kelompok($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperjenis'];
}

function _load_kelompok() {
	$kelompoks = array('- Pilih Kelompok -');


	// Select table
	$query = db_select("kelompok", "k");
	// Selected fields
	$query->fields("k", array('kodek', 'uraian'));	
	$query->condition("k.kodea", '4', '>=');
	
	
	// Order by name
	$query->orderBy("k.kodek");
	// Execute query
	$result = $query->execute(); 

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$kelompoks[$row->kodek] = $row->kodek . ' - ' . $row->uraian;
	}

	return $kelompoks;
}


/**
 * Function for populating rekening
 */
function _load_jenis($kodek) {
	$jenises = array('- Pilih Jenis -');


	// Select table
	$query = db_select("jenis", "j");
	// Selected fields
	$query->fields("j", array('kodej', 'uraian'));	
	$query->condition("j.kodek", $kodek, '=');
	
	
	// Order by name
	$query->orderBy("j.kodej");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$jenises[$row->kodej] = $row->kodej . ' - ' . $row->uraian;
	}

	return $jenises;
}

function _load_obyek($kodek) {
	$obyeks = array('- Pilih Obyek -');


	// Select table
	$query = db_select("obyek", "o");
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));
	// Filter the active ones only
	$query->condition("o.kodej", $kodek, '=');
	
	// Order by name
	$query->orderBy("o.kodeo");
	// Execute query
	$result = $query->execute();

	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options
		$obyeks[$row->kodeo] = $row->uraian;
	}

	return $obyeks;
}

function rekening_prognosis_main_form_validate($form, &$form_state) {
}

function rekening_prognosis_main_form_submit($form, &$form_state) {
$kodek = $form_state['values']['kodek'];
$kodej = $form_state['values']['kodej'];

if($form_state['clicked_button']['#value'] == $form_state['values']['submitprognosis']) {
	rekap_prognosis();
	
} else {


	$jumlahrek = $form_state['values']['jumlahrek'];
	for ($n=1; $n <= $jumlahrek; $n++) {

		$kodeo = $form_state['values']['kodeo' . $n];
		$persen = $form_state['values']['persen' . $kodeo . $n];
		
		db_update('prognosis')
				->fields(
					array(
						'persen' => $persen,
					)
				)->condition('kodeo', $kodeo, '=')->execute();			
		
	}

}

drupal_goto('rekening/prognosis/' . $kodek . '/' . $kodej);
}

function rekap_prognosis() {

/*
update anggperuk set kodeuk='00' where kodeuk='81' and left(kodero,2) in ('42', '43')

update anggperuk set kodeuk='00' where kodeuk='81' and left(kodero,3) in ('413', '414')

insert into prognosisskpd (kodeuk, kodeo, anggaran)
SELECT kodeuk, left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperuk group by kodeuk, left(kodero,5)

insert into prognosisskpd (kodeuk, kodeo, anggaran)
SELECT kegiatanskpd.kodeuk, left(anggperkeg.kodero,5) as kodeo, sum(anggperkeg.jumlah) as anggaran 
from anggperkeg inner join kegiatanskpd on anggperkeg.kodekeg=kegiatanskpd.kodekeg 
where kegiatanskpd.inaktif=0
group by kegiatanskpd.kodeuk, left(anggperkeg.kodero,5)

insert into prognosisskpd (kodeuk, kodeo, anggaran)
SELECT '00', left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperda group by left(kodero,5)

update jurnal inner join jurnalitem on jurnal.jurnalid=jurnalitem.jurnalid
set jurnal.kodeuk='00' where jurnal.kodeuk='81' and left(jurnalitem.kodero,3) in ('413', '414')

*KAB*

insert into prognosiskab (kodeo, anggaran)
SELECT left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperuk group by left(kodero,5)

insert into prognosiskab (kodeo, anggaran)
SELECT  left(anggperkeg.kodero,5) as kodeo, sum(anggperkeg.jumlah) as anggaran 
from anggperkeg inner join kegiatanskpd on anggperkeg.kodekeg=kegiatanskpd.kodekeg 
where kegiatanskpd.inaktif=0
group by left(anggperkeg.kodero,5)

insert into prognosiskab (kodeo, anggaran)
SELECT left(kodero,5) as kodeo, sum(jumlah) as anggaran 
from anggperda group by left(kodero,5)
	
*/

$res_x = db_query('UPDATE prognosisskpd set realisasi=0, prognosis=0');

$res_uk = db_query("select kodeuk from {unitkerja} order by kodeuk");
foreach ($res_uk as $data_uk) {

	$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and 
			MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'5', ':kodeuk'=>$data_uk->kodeuk));
	foreach ($res_data as $data) {
		
		db_update('prognosisskpd')
		->fields(array( 
				'realisasi' => $data->realisasi,
				))
		->condition("kodeo", $data->kodeo, '=')
		->condition("kodeuk", $data_uk->kodeuk, '=')		
		->execute();

		
	}
	$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
			FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and 
			MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'4', ':kodeuk'=>$data_uk->kodeuk));
	foreach ($res_data as $data) {
		
		db_update('prognosisskpd')
		->fields(array( 
				'realisasi' => $data->realisasi,
				))
		->condition("kodeo", $data->kodeo, '=')
		->condition("kodeuk", $data_uk->kodeuk, '=')		
		->execute();

		
	}
	
	if ($data_uk->kodeuk=='00') {
		$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
				FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
				MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'62', ':kodeuk'=>$data_uk->kodeuk));
		foreach ($res_data as $data) {
			
			db_update('prognosisskpd')
			->fields(array( 
					'realisasi' => $data->realisasi,
					))
			->condition("kodeo", $data->kodeo, '=')
			->condition("kodeuk", $data_uk->kodeuk, '=')		
			->execute();


			
		}
		$res_data = db_query('SELECT jurnal.kodeuk, LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
				FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
				MONTH(jurnal.tanggal)<=6 AND jurnal.kodeuk=:kodeuk GROUP BY jurnal.kodeuk, LEFT(jurnalitem.kodero, 5)', array(':akun'=>'61', ':kodeuk'=>$data_uk->kodeuk));
		foreach ($res_data as $data) {
			
			db_update('prognosisskpd')
			->fields(array( 
					'realisasi' => $data->realisasi,
					))
			->condition("kodeo", $data->kodeo, '=')
			->condition("kodeuk", $data_uk->kodeuk, '=')		
			->execute();


			
		}		
	}
}

$res_x = db_query('UPDATE prognosisskpd SET sisa=anggaran-realisasi');

$res_x = db_query('UPDATE prognosisskpd inner join prognosis ON prognosisskpd.kodeo=prognosis.kodeo SET prognosisskpd.persen=prognosis.persen');
$res_x = db_query('UPDATE prognosisskpd SET prognosis=(persen/100)*sisa');

$res_x = db_query('UPDATE prognosisskpd SET sisa=0 WHERE sisa<0');
$res_x = db_query('UPDATE prognosisskpd SET prognosis=realisasi WHERE prognosis<=0');

//KAB*
$res_x = db_query('UPDATE prognosiskab set realisasi=0, prognosis=0');

$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and MONTH(jurnal.tanggal)<=6 GROUP BY LEFT(jurnalitem.kodero, 5)', array(':akun'=>'5'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();

	
}
$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
		FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,1)=:akun and 
		MONTH(jurnal.tanggal)<=6 GROUP BY LEFT(jurnalitem.kodero, 5)', array(':akun'=>'4'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();

	
}

$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
		FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
		MONTH(jurnal.tanggal)<=6 AND LEFT(jurnalitem.kodero, 5)', array(':akun'=>'62'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();


	
}
$res_data = db_query('SELECT LEFT(jurnalitem.kodero, 5) as kodeo, SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
		FROM jurnalitem inner join jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE LEFT(jurnalitem.kodero,2)=:akun and 
		MONTH(jurnal.tanggal)<=6 GROUP BY LEFT(jurnalitem.kodero, 5)', array(':akun'=>'61'));
foreach ($res_data as $data) {
	
	db_update('prognosiskab')
	->fields(array( 
			'realisasi' => $data->realisasi,
			))
	->condition("kodeo", $data->kodeo, '=')
	->execute();


	
}	

$res_x = db_query('UPDATE prognosiskab SET sisa=anggaran-realisasi');

$res_x = db_query('UPDATE prognosiskab inner join prognosis ON prognosiskab.kodeo=prognosis.kodeo SET prognosiskab.persen=prognosis.persen');
$res_x = db_query('UPDATE prognosiskab SET prognosis=(persen/100)*sisa');

$res_x = db_query('UPDATE prognosiskab SET sisa=0 WHERE sisa<0');
$res_x = db_query('UPDATE prognosiskab SET prognosis=realisasi WHERE prognosis<=0');	
	
}	

?>