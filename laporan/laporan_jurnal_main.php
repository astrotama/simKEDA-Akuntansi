<?php
function laporan_jurnal_main($arg=NULL, $nama=NULL) {
    $cetakpdf = '';
	
		$margin = '10'; 
		$marginkiri = '20';
		$hal1 = '9999'; 

		$jurnalid = arg(1);
		$cetakpdf = arg(2);
	
	
		$output = gen_jurnal_print($jurnalid);
	
	if ($cetakpdf == 'pdf') {

		
		$_SESSION["hal1"] = $hal1;
		apbd_ExportPDF_P_Kiri($output, $margin, $marginkiri,"LAP.pdf");
		//return $output;
		
		 
	} else {

		
		return $output;
		
	}	
	
}

function laporan_jurnal_main_form_submit($form, &$form_state) {

	
}


function laporan_jurnal_main_form($form, &$form_state) {
	

}

function gen_jurnal_print($jurnalid) {


$rows[] = array(
	array('data' => '<strong>JURNAL</strong>', 'width' => '510px', 'align'=>'center','style'=>'font-size:110%;border:none'),
);
$rows[] = array(
	array('data' => $skpd, 'width' => '510px', 'align'=>'center','style'=>'font-size:80%;border:none'),
);

//Header
$results = db_query('select j.tanggal, j.kodekeg, j.keterangan, j.nobukti, j.nobuktilain, u.namauk, j.jenisdokumen from {jurnal} j inner join {unitkerja} u on j.kodeuk=u.kodeuk where j.jurnalid=:jurnalid', array(':jurnalid'=>$jurnalid));

foreach ($results as $data) {
	$tanggal = $data->tanggal;
	$kodekeg = $data->kodekeg;
	$keperluan = $data->keterangan;
	$nobukti = $data->nobukti;
	$nobuktilain = $data->nobuktilain;
	$namauk = $data->namauk;
	$jenisdokumen = $data->jenisdokumen;
}
if ($jenisdokumen=='1') {
	$kegiatan = 'Ganti Uang';
} elseif ($jenisdokumen=='5') {
	$kegiatan = 'GU Nihil';
} elseif ($jenisdokumen=='7') {
	$kegiatan = 'TU Nihil';
} else {
	$kegiatan = 'Non Kegiatan';
	$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));

	foreach ($results as $data) {
		$kegiatan = $data->kegiatan;
	}
}

$rows[] = array(
	array('data' => 'OPD', 'width' => '50px', 'align'=>'left','style'=>'font-size:80%;border:none'),
		array('data' => ':', 'width' => '10px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	array('data' => $namauk, 'width' => '450px', 'align'=>'left','style'=>'font-size:80%;border:none'),
);
$rows[] = array(
	array('data' => 'Tanggal', 'width' => '50px', 'align'=>'left','style'=>'font-size:80%;border:none'),
		array('data' => ':', 'width' => '10px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	array('data' => apbd_fd_long($tanggal), 'width' => '200px', 'align'=>'left','style'=>'font-size:80%;border:none'),
		array('data' => 'ID Jurnal : ', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border:none'),
	array('data' => $jurnalid, 'width' => '180px', 'align'=>'left','style'=>'font-size:80%;border:none'),
);
$rows[] = array(
	array('data' => 'No. Bukti', 'width' => '50px', 'align'=>'left','style'=>'font-size:80%;border:none'),
		array('data' => ':', 'width' => '10px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	array('data' => $nobukti, 'width' => '200px', 'align'=>'left','style'=>'font-size:80%;border:none'),
	array('data' => 'No. Bukti Lain : ', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border:none'),
	array('data' => $nobuktilain, 'width' => '180px', 'align'=>'left','style'=>'font-size:80%;border:none'),
);

$rows[] = array(
	array('data' => 'Kegiatan', 'width' => '50px', 'align'=>'left','style'=>'font-size:80%;border:none'),
		array('data' => ':', 'width' => '10px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	array('data' => $kegiatan, 'width' => '450px', 'align'=>'left','style'=>'font-size:80%;border:none'),
);

$rows[] = array(
	array('data' => 'Keperluan', 'width' => '50px', 'align'=>'left','style'=>'font-size:80%;border:none'),
		array('data' => ':', 'width' => '10px', 'align'=>'center','style'=>'font-size:80%;border:none'),
	array('data' => $keperluan, 'width' => '450px', 'align'=>'left','style'=>'font-size:80%;border:none'),
);

$tabel_data =  theme('table', array('header' => null, 'rows' => $rows ));


//$header = array();
$header[] = array (
	array('data' => 'NO','width' => '15px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
		array('data' => 'KODE','width' => '40px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '315px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'DEBET', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
	array('data' => 'KREDIT', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
);

$nojurnal = 0;
// * APBD * //

$rows = null;
$rows = array();
$debet_t = 0; $kredit_t = 0; $n = 0;

$lastkeg='123'; 

$results = db_query('select ji.kodekeg, k.kegiatan, r.kodero, r.uraian, ji.debet, ji.kredit from {jurnalitem} ji inner join {rincianobyek} r on ji.kodero=r.kodero left join {kegiatanskpd} k on ji.kodekeg=k.kodekeg where ji.jurnalid=:jurnalid order by k.kegiatan, r.kodero', array(':jurnalid'=>$jurnalid));

//$results = db_query('select ji.kodekeg, r.kodero, r.uraian, ji.debet, ji.kredit from {jurnalitem} ji inner join {rincianobyek} r on ji.kodero=r.kodero where ji.jurnalid=:jurnalid order by r.kodero', array(':jurnalid'=>$jurnalid));

foreach ($results as $datas) {
	$n++;
	
	if ($datas->kodekeg != '') {
		if ($lastkeg != $datas->kodekeg) {

		$rows[] = array(
			array('data' => '', 'width' => '15px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
			array('data' => '', 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '<strong>' . $datas->kegiatan . '</strong>', 'width' => '315px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		);		
			$lastkeg = $datas->kodekeg;
			
		}	
	}	
	
	$debet_t += $datas->debet; 
	$kredit_t += $datas->kredit;

	$rows[] = array(
		array('data' => $n, 'width' => '15px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->kodero, 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'width' => '315px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->debet), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->kredit), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
}

$rows[] = array(
		array('data' => '', 'width' => '55px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TOTAL', 'width' => '315px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($debet_t), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($kredit_t), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
	);
$rows[] = array(array('data' => '', 'width' => '510px', 'align'=>'left','style'=>'font-size:80%;border-none;'),
);

if ($debet_t > 0) {
	$nojurnal++;
	$tabel_data .= '<p style="font-size:80%;">'. $nojurnal . '. JURNAL APBD</p>' . createT($header, $rows);

}

//$header = array();
$header = null;
$header[] = array (
	array('data' => 'NO','width' => '15px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KODE','width' => '40px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'URAIAN','width' => '150px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'KETERANGAN','width' => '165px', 'rowspan'=>2, 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-bottom:1px solid black;border-right:1px solid black;font-weight: bold'),
	array('data' => 'DEBET', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
	array('data' => 'KREDIT', 'width' => '70px', 'align'=>'center','style'=>'font-size:80%;border-top:1px solid black;border-right:1px solid black;border-bottom:1px solid black;font-weight: bold'),
);

// * LRA * //

$rows = null;
$rows = array();

$debet_t = 0; $kredit_t = 0;$n = 0;
$results = db_query('select ji.kodekeg, k.kegiatan, r.kodero, r.uraian, ji.keterangan, ji.debet, ji.kredit from {jurnalitemlra} as ji inner join {rincianobyeksap} as r on ji.kodero=r.kodero left join {kegiatanskpd} k on ji.kodekeg=k.kodekeg  where ji.jurnalid=:jurnalid order by k.kegiatan, r.kodero', array(':jurnalid'=>$jurnalid));
$lastkeg = '123';
foreach ($results as $datas) {

	if ($datas->kodekeg != '') {
		if ($lastkeg != $datas->kodekeg) {

			$rows[] = array(
				array('data' => '', 'width' => '15px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '', 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '<strong>' . $datas->kegiatan . '</strong>', 'width' => '150px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '165px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			$lastkeg = $datas->kodekeg;
			
		}	
	}
	
	$debet_t += $datas->debet; 
	$kredit_t += $datas->kredit;
	$n++;
	
	$keterangan = '';
	if (strlen($datas->keterangan)==8) {
		$res_ket = db_query('select kodero, uraian from {rincianobyek} where kodero=:kodero', array(':kodero'=>$datas->keterangan));
		foreach ($res_ket as $data_ket) {
			$keterangan = $data_ket->kodero . ', ' . $data_ket->uraian;
		}
			
	} else {
		$keterangan = str_replace(' | ', ', ', $datas->keterangan);
	}

	$rows[] = array(
		array('data' => $n, 'width' => '15px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->kodero, 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'width' => '150px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $keterangan, 'width' => '165px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->debet), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->kredit), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
}

$rows[] = array(
		array('data' => '', 'width' => '55px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TOTAL', 'width' => '315px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($debet_t), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($kredit_t), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
	);
$rows[] = array(array('data' => '', 'width' => '510px', 'align'=>'left','style'=>'font-size:80%;border-none;'),
);
	
if ($debet_t > 0) {
	$nojurnal++;
	$tabel_data .= '<p style="font-size:80%;">'. $nojurnal . '. JURNAL SAP-LRA</p>' . createT($header, $rows);

}
// * LO * //

$rows = null;
$rows = array();
$debet_t = 0; $kredit_t = 0;$n = 0;

$results = db_query('select ji.kodekeg, k.kegiatan, r.kodero, r.uraian, ji.keterangan, ji.debet, ji.kredit from {jurnalitemlo} as ji inner join {rincianobyeksap} as r on ji.kodero=r.kodero left join {kegiatanskpd} k on ji.kodekeg=k.kodekeg where ji.jurnalid=:jurnalid order by k.kegiatan, r.kodero', array(':jurnalid'=>$jurnalid));

foreach ($results as $datas) {

	if ($datas->kodekeg != '') {
		if ($lastkeg != $datas->kodekeg) {

			$rows[] = array(
				array('data' => '', 'width' => '15px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
				array('data' => '', 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '<strong>' . $datas->kegiatan . '</strong>', 'width' => '150px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '165px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
				array('data' => '', 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
			);
			$lastkeg = $datas->kodekeg;
			
		}	
	}
	
	$debet_t += $datas->debet; 
	$kredit_t += $datas->kredit;
	$n++;

	$keterangan = '';
	if (strlen($datas->keterangan)==8) {
		$res_ket = db_query('select kodero, uraian from {rincianobyek} where kodero=:kodero', array(':kodero'=>$datas->keterangan));
		foreach ($res_ket as $data_ket) {
			$keterangan = $data_ket->kodero . ', ' . $data_ket->uraian;
		}
			
	} else {
		$keterangan = str_replace(' | ', ', ', $datas->keterangan);
	}
	
	$rows[] = array(
		array('data' => $n, 'width' => '15px', 'align'=>'center','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;'),
		array('data' => $datas->kodero, 'width' => '40px', 'align'=>'center','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $datas->uraian, 'width' => '150px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => $keterangan, 'width' => '165px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->debet), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
		array('data' => apbd_fn($datas->kredit), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;'),
	);
	
}

$rows[] = array(
		array('data' => '', 'width' => '55px', 'align'=>'left','style'=>'font-size:80%;border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;'),
		array('data' => 'TOTAL', 'width' => '315px', 'align'=>'left','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($debet_t), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
		array('data' => apbd_fn($kredit_t), 'width' => '70px', 'align'=>'right','style'=>'font-size:80%;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;font-weight: bold'),
	);
$rows[] = array(array('data' => '', 'width' => '510px', 'align'=>'left','style'=>'font-size:80%;border-none;'),
);
	
if ($debet_t > 0) {
	$nojurnal++;
	$tabel_data .= '<p style="font-size:80%;">'. $nojurnal . '. JURNAL SAP-LO</p>' . createT($header, $rows);

}

return $tabel_data;

}


?>