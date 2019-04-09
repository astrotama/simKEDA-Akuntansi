<?php
function rekeningdetil_prognosis_main($arg=NULL, $nama=NULL) {
   

	$output_form = drupal_get_form('rekeningdetil_prognosis_main_form');
	return drupal_render($output_form);
	
}

function rekeningdetil_prognosis_main_form ($form, &$form_state) {
	$kodek = arg(2);	
	$kodej = arg(3);
	$kodeo = arg(4);
	
	if (isUserSKPD()) {
		$kodeuk = apbd_getuseruk();
		$form['kodeuk'] = array(
			'#type' => 'hidden',
			'#default_value' => $kodeuk,
		);
		
	} else {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'));
		$query->orderBy('kodedinas', 'ASC');
		$results = $query->execute();
		$optskpd = array();
		//$optskpd['ZZ'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $optskpd[$data->kodeuk] = $data->namasingkat; 
			}
		}
		
		$form['kodeuk'] = array(
			'#type' => 'select',
			'#title' =>  t('SKPD'),
			// The entire enclosing div created here gets replaced when dropdown_first
			// is changed.
			'#options' => $optskpd,
			//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,5
			'#default_value' => $kodeuk,
			'#ajax' => array(
				'event'=>'change',
				'callback' =>'_ajax_obyek',
			),			
		);
	}	
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
	
	$options = array('- Pilih Jenis -');
	if (isset($form_state['values']['kodeo'])) {
		// Pre-populate options for rekdetil dropdown list if rekening id is set
		$optionsobyek = _load_obyek($form_state['values']['kodej'], $form_state['values']['kodeuk']);
	} else
		$optionsobyek = _load_obyek($kodej, $kodeuk);
	
	// Rekening dropdown list
	$form['wrapperobyek']['kodeo'] = array(
		'#title' => t('Obyek'),
		'#type' => 'select',
		'#options' => $optionsobyek,
		'#default_value' => $kodeo,
		'#validated' => TRUE,
		'#ajax' => array(
			'event'=>'change',
			'callback' =>'_ajax_obyek',
			'wrapper' => 'obyek-wrapper',
		),
	);
	$form['wrapperrincianobyek'] = array(
		'#prefix' => '<div id="obyek-wrapper">',
		'#suffix' => '</div>',
	);
	$form['wrapperrincianobyek']['tablerek']= array(
		'#prefix' => '<table class="table table-hover"><tr><th  width="10px">No</th><th width="90px">Kode</th><th>Uraian</th><th width="120px">Anggaran</th><th width="120px">Realisasi</th><th width="70px">%</th><th width="90px">Prognosis</th></tr>',
		 '#suffix' => '</table>',
	);
	if (isset($form_state['values']['kodeo'])) $kodeo = $form_state['values']['kodeo'];
	
	$i = 0;
	if (strlen($kodeo)<5) {
			$form['wrapperrincianobyek']['tablerek']['kodeo' . $i]= array(
					'#type' => 'value',
					'#value' => null,
			); 
			$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => null,
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['kodeo_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => null,
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => 'Pilih Kelompok, lalu pilih Jenis Rekening',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['anggaran' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '0',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['realisasi' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
					'#suffix' => '</td>',			); 
			$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['prognosis' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => 'Isikan prognosis disini',
					'#suffix' => '</td>',
					'#suffix' => '</td></tr>',
			); 		
	} else {

		//$results = db_query("SELECT o.kodero, o.kodeo, o.uraian, p.persen from {rincianobyek} as o left join {prognosisuk} as p on o.kodeo=p.kodeo where o.kodeo=:kodeo order by o.kodeo", array(':kodeo'=>$kodeo));
		
		if (isUserSKPD())
			$kodeuk  = apbd_getuseruk();
		else
			$kodeuk = $form_state['values']['kodeuk'];
		
		if (substr($kodeo, 0,1)=='5')
			$results = db_query('select distinct ro.kodero, ro.uraian from {rincianobyek} ro inner join {anggperkeg} a on ro.kodero=a.kodero inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where ro.kodeo=:kodeo and k.kodeuk=:kodeuk order by ro.kodero', array(':kodeo'=>$kodeo, ':kodeuk'=>$kodeuk));
		else
			$results = db_query('select distinct ro.kodero, ro.uraian from {rincianobyek} ro inner join {anggperuk} a on ro.kodero=a.kodero where ro.kodeo=:kodeo and a.kodeuk=:kodeuk order by ro.kodero', array(':kodeo'=>$kodeo, ':kodeuk'=>$kodeuk));
			
		foreach ($results as $data) {
			
			$persen = 100;
			$baru = '0';
			
			$resx = db_query('select persen from {prognosisuk} where kodero=:kodero and kodeuk=:kodeuk', array(':kodero'=>$data->kodero, ':kodeuk'=>$kodeuk));
			foreach ($resx as $datax) {
				$persen = $datax->persen;
				$baru = '1';
			}
			if ($persen == '') $persen = 100;
			if ($baru != '1') $baru = '0';
			
			
			
			$i++; 
			$form['wrapperrincianobyek']['tablerek']['kodero' . $i]= array(
					'#type' => 'value',
					'#value' => $data->kodero,
			); 
			$form['wrapperrincianobyek']['tablerek']['baru' . $kodeuk .  $data->kodero . $i]= array(
					'#type' => 'value',
					'#value' => $baru,  
			);  
			$form['wrapperrincianobyek']['tablerek']['nomor' . $i]= array(
					'#prefix' => '<tr><td>',
					'#markup' => $i,  
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['kodeo_v' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->kodero,
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['uraian' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => $data->uraian,
					'#suffix' => '</td>',
			); 
			
			$anggaran = read_anggaran($kodeuk, $data->kodero);
			$realisasi = read_realisasi($kodeuk, $data->kodero);
			$form['wrapperrincianobyek']['tablerek']['anggaran' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right">' . apbd_fn($anggaran) . '</p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['realisasi' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right">' . apbd_fn($realisasi) . '</p>',
					'#suffix' => '</td>',			); 
			$form['wrapperrincianobyek']['tablerek']['persenrea' . $i]= array(
					'#prefix' => '<td>',
					'#markup' => '<p style="text-align:right">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>',
					'#suffix' => '</td>',
			); 
			$form['wrapperrincianobyek']['tablerek']['persen' . $kodeuk .  $data->kodero . $i]= array(
					'#prefix' => '<td>',
					'#type' => 'textfield',
					'#attributes' => array('style' => 'text-align: right'),		//array('id' => 'righttf'),
					'#size' => 10,					
					'#default_value' => $persen,
					'#suffix' => '</td></tr>',
			); 
			
		}
		//dpm($results);
	}

	$form['jumlahrek']= array(
		'#type' => 'value',
		'#value' => $i,
	);
	
	$form['submitprint']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Simpan',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	
	/*
	$form['submitprognosis']= array(
		'#type' => 'submit',
		'#value' => '<span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span> Hitung',
		'#attributes' => array('class' => array('btn btn-primary btn-sm')),
	);	
	*/
	
	return $form;
}


function _ajax_jenis($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperobyek'];
}
function _ajax_obyek($form, $form_state) {
	// Return the dropdown list including the wrapper
	return $form['wrapperrincianobyek'];
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
function _load_obyek($kodej, $kodeuk) {
	$obyek = array('- Pilih Obyek -');
	
	//drupal_set_message($kodej);
	
	if (substr($kodej, 0,1)=='5')
		$result = db_query('select o.kodeo, o.uraian from {obyek} o inner join {anggperkeg} a on o.kodeo=left(a.kodero,5) inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where o.kodej=:kodej and k.kodeuk=:kodeuk', array(':kodej'=>$kodej, ':kodeuk'=>$kodeuk));
	else
		$result = db_query('select o.kodeo, o.uraian from {obyek} o inner join {anggperuk} a on o.kodeo=left(a.kodero,5) where o.kodej=:kodej and a.kodeuk=:kodeuk', array(':kodej'=>$kodej, ':kodeuk'=>$kodeuk));
	
	/*
	// Select table
	$query = db_select("obyek", "o");
	
	// Selected fields
	$query->fields("o", array('kodeo', 'uraian'));	
	$query->condition("o.kodej", $kodej, '='); 
	
	
	// Order by name
	$query->orderBy("o.kodeo");
	// Execute query
	$result = $query->execute();
	*/
	
	while($row = $result->fetchObject()){
		// Key-value pair for dropdown options 
		$obyek[$row->kodeo] = $row->kodeo . ' - ' . $row->uraian;
	}

	return $obyek;
}
function _load_ro($kodeo) {
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

function rekeningdetil_prognosis_main_form_validate($form, &$form_state) {
}

function rekeningdetil_prognosis_main_form_submit($form, &$form_state) {
$kodeuk = apbd_getuseruk();

if($form_state['clicked_button']['#value'] == $form_state['values']['submitprognosis']) {
	//rekap_prognosis();
	
} else { 


	$jumlahrek = $form_state['values']['jumlahrek'];
	for ($n=1; $n <= $jumlahrek; $n++) {

		$kodero = $form_state['values']['kodero' . $n];
		$persen = $form_state['values']['persen' . $kodeuk .  $kodero . $n];
		$baru = $form_state['values']['baru' . $kodeuk .  $kodero . $n];
		
		
		if (($baru=='0') or ($baru=='')) {
			
			db_insert('prognosisuk')
			->fields(array(
					'kodero' => $kodero,
					'kodeuk' => $kodeuk,
					'persen' => $persen,
					))
			->execute();
					
		} else {
			
			db_update('prognosisuk')
					->fields(
						array(
							'persen' => $persen,
						)
					)
					->condition('kodero', $kodero, '=')
					->condition('kodeuk', $kodeuk, '=')
					->execute();			
			}
	}

}

$kodek = substr($kodero,0,2); $kodej = substr($kodero,0,3);
drupal_goto('rekeningdetil/prognosis/' . $kodek . '/' . $kodej);
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

function read_anggaran($kodeuk, $kodero) {
	$x = 0;
	
	if (substr($kodero,0,1)=='4') {
		$res = db_query('SELECT jumlah anggaran FROM anggperuk WHERE kodero=:kodero AND kodeuk=:kodeuk', array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));
	} else {
		$res = db_query('SELECT SUM(anggperkeg.jumlah) anggaran FROM anggperkeg INNER JOIN kegiatanskpd ON anggperkeg.kodekeg=kegiatanskpd.kodekeg WHERE anggperkeg.kodero=:kodero AND kegiatanskpd.kodeuk=:kodeuk AND kegiatanskpd.inaktif=0', array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));
		
	}
	foreach ($res as $data) {
		$x = $data->anggaran;		
	}	
	return $x;
}

function read_realisasi($kodeuk, $kodero) {
	$x = 0;
	if (substr($kodero,0,1)=='4') {
		$res = db_query('SELECT SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
				FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero=:kodero AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));
	} else {
		$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
				FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero=:kodero AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodero'=>$kodero, ':kodeuk'=>$kodeuk));		
	}
	foreach ($res as $data) {
		$x = $data->realisasi;		
	}	
	return $x;
}

?>