<?php

function gen_report_realisasi_kegiatan($bulan, $kodeuk, $tingkat) {

$sufixjurnal = '';


$results = db_query('select kodedinas from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
foreach ($results as $datas) {
	$kodedinas = $datas->kodedinas;
};

$agg_pendapata_total = 0;
$agg_belanja_total = 0;
$agg_pembiayaan_netto = 0;

$rea_pendapata_total = 0;
$rea_belanja_total = 0;
$rea_pembiayaan_netto = 0;

$tanggal_akhir = apbd_tahun() . '-06-30';
$tanggal_awal = apbd_tahun() . '-01-01';
 
//TABEL
$header = array (
	array('data' => 'Kode','width' => '15px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Persen', 'width' => '15px', 'valign'=>'top'),
	array('data' => 'Sisa', 'width' => '80px', 'valign'=>'top'),
	array('data' => 'Prognosis', 'width' => '80px', 'valign'=>'top'),
);
$rows = array();

// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();

$realisasi = 0; $prognosis = 0 ; $sisa = 0;
foreach ($results as $datas) {
	
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;
	if ($sisa<0) $sisa = 0;
	
	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:150%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>',
			'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			
			if (($realisasi+$data_jen->anggaran) >0) {
				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;
				$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

				$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/10/20/view', array('attributes' => array('class' => null)));
				
				$rows[] = array(
					array('data' => $kodej, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
				);
				
				if ($tingkat>'3') {
					//OBYEK
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperuk', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					if ($kodeuk!='ZZ') $query->condition('ag.kodeuk', $kodeuk, '='); 
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
								array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
							);
						}
					}	//obyek			
				
				}	//if tingkat obyek
			}
		}	//jenis
		
		
	}
	

}	//foreach ($results as $datas)

// * BELANJA * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('keg.kodeuk', $kodeuk, '='); 
$query->condition('a.kodea', '5', '='); 
$query->condition('keg.inaktif', '0', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
 
foreach ($results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	 
	read_realisasi($kodeuk, $datas->kodea, $realisasi, $prognosis);
	$sisa = $datas->anggaran - $realisasi;

	$rows[] = array(
		array('data' =>  '<strong>' . $kodedinas . '.' . $datas->kodea . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:150%;'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	
	//KELOMPOK BELANJA TIDAK LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '1', '='); 
	$query->condition('keg.inaktif', '0', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;		
	
		$uraian = l($data_kel->uraian, '/akuntansi/buku/ZZ/'  . $data_kel->kodek  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi($kodeuk, $data_jen->kodej, $realisasi, $prognosis);
			$sisa = $data_jen->anggaran - $realisasi;

			$kodej = $kodedinas . '.000.000.' . $data_jen->kodej;			
			$uraian = l($data_jen->uraian, '/akuntansi/buku/ZZ/'  . $data_jen->kodej  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));

			$detil = l('Detil', '/laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/10/20/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil , 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
			);
			
			
			//OBYEK
			if ($tingkat>'3') {
				$query = db_select('obyek', 'o');
				$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('o', array('kodeo', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				if ($kodeuk!='ZZ') $query->condition('keg.kodeuk', $kodeuk, '='); 
				$query->condition('o.kodej', $data_jen->kodej, '='); 
				$query->groupBy('o.kodeo');
				$query->orderBy('o.kodeo');
				$results_oby = $query->execute();	
				foreach ($results_oby as $data_oby) {

					read_realisasi($kodeuk, $data_oby->kodeo, $realisasi, $prognosis);
					$sisa = $data_oby->anggaran - $realisasi;
				
					$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);	
					$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/ZZ/'  . $data_oby->kodeo  . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));		
					$rows[] = array(
						array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
					);
					
				}	//obyek			
				
			}	//if obyek
		}	//jenis
		
		
	}
	

	//KELOMPOK BELANJA LANGSUNG
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.kodeuk', $kodeuk, '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->condition('keg.jenis', '2', '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
			
		read_realisasi($kodeuk, $data_kel->kodek, $realisasi, $prognosis);
		$sisa = $data_kel->anggaran - $realisasi;
		 
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
		
		//PROGRAM
		$query = db_select('program', 'p');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodepro=p.kodepro');
		$query->fields('p', array('kodepro', 'program'));
		$query->addExpression('SUM(keg.total)', 'anggaran');
		$query->condition('keg.kodeuk', $kodeuk, '='); 
		$query->condition('keg.jenis', '2', '='); 
		$query->condition('keg.inaktif', '0', '=');
		$query->condition('keg.total', '0', '>');
		$query->groupBy('p.kodepro');
		$query->orderBy('p.kodepro');
		$results_pro = $query->execute(); 
		foreach ($results_pro as $data_pro) {

			read_realisasi_program($kodeuk, $data_pro->kodepro, $realisasi, $prognosis);
			$sisa = $data_pro->anggaran - $realisasi;

			$kodepro = $kodedinas . '.' . $data_pro->kodepro;

			$detil = l('Detil', '/laporandetilpro/filter/' . $bulan . '/' . $kodeuk . '/' . $data_pro->kodepro . '/10/20/view', array('attributes' => array('class' => null)));
			
			$rows[] = array(
				array('data' => '<strong>' . $kodepro . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . strtoupper($data_pro->program) . '</strong>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($data_pro->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_pro->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => $detil, 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			);				
			
			
			//KEGIATAN
			$query = db_select('kegiatanskpd', 'keg');
			$query->fields('keg', array('kodekeg', 'kegiatan', 'total'));
			$query->condition('keg.kodeuk', $kodeuk, '='); 
			$query->condition('keg.kodepro', $data_pro->kodepro, '='); 
			$query->condition('keg.jenis', '2', '='); 
			$query->condition('keg.total', '0', '>'); 
			$query->orderBy('keg.kodepro', 'ASC');
			$query->orderBy('keg.kodekeg', 'ASC');
			$results_keg = $query->execute(); 
			foreach ($results_keg as $data_keg) {
				
				read_realisasi_kegiatan($kodeuk, $data_keg->kodekeg, $realisasi, $prognosis);
				$sisa = $data_keg->anggaran - $realisasi;
			
				$uraian = l(strtoupper($data_keg->kegiatan), '/akuntansi/buku/' . $data_keg->kodekeg . '/5/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
				$detil = l('Detil', '/laporandetilkeg/filter/' . $bulan . '/' . $kodeuk . '/' . $data_keg->kodekeg . '/10/20/view', array('attributes' => array('class' => null)));
				
				$kodekeg = $kodedinas . '.' . $data_pro->kodepro . '.' . substr($data_keg->kodekeg,-3);				
				$rows[] = array(
					array('data' => '<strong>' . $kodekeg  . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . $uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($data_keg->total) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_keg->total, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
					array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
				);				
				
				
				//JENIS
				
				$query = db_select('jenis', 'j');
				$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
				$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
				$query->fields('j', array('kodej', 'uraian'));
				$query->addExpression('SUM(ag.jumlah)', 'anggaran');
				$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
				$query->condition('j.kodek', $data_kel->kodek, '='); 
				$query->groupBy('j.kodej');
				$query->orderBy('j.kodej');
				$results_jen = $query->execute();	
				foreach ($results_jen as $data_jen) {

					read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_jen->kodej, $realisasi, $prognosis);
					$sisa = $data_jen->anggaran - $realisasi;
				
					$kodej = $kodekeg  . '.' . $data_jen->kodej;
					$uraian = l($data_jen->uraian, '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_jen->kodej . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
					
					$rows[] = array(
						array('data' => $kodej , 'align' => 'left', 'valign'=>'top'),
						array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
					);
					
					
					//OBYEK
					if ($tingkat>'3') {
						$query = db_select('obyek', 'o');
						$query->innerJoin('anggperkeg', 'ag', 'o.kodeo=left(ag.kodero,5)');
						$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
						$query->fields('o', array('kodeo', 'uraian'));
						$query->addExpression('SUM(ag.jumlah)', 'anggaran');
						$query->condition('keg.kodekeg', $data_keg->kodekeg, '='); 
						$query->condition('o.kodej', $data_jen->kodej, '='); 
						$query->groupBy('o.kodeo');
						$query->orderBy('o.kodeo');
						$results_oby = $query->execute();	
						foreach ($results_oby as $data_oby) {

							read_realisasi_kegiatan_rekening($kodeuk, $data_keg->kodekeg, $data_oby->kodeo, $realisasi, $prognosis);
							$sisa = $data_oby->anggaran - $realisasi;
						
							$kodeo = $kodej . '.' . substr($data_oby->kodeo,-2);
							$uraian = l(ucfirst(strtolower($data_oby->uraian)), '/akuntansi/buku/' . $data_keg->kodekeg . '/' . $data_oby->kodeo . '/'  . $kodeuk . '/' . $tanggal_awal . '/' . $tanggal_akhir . '/', array('attributes' => array('class' => null)));								
							$rows[] = array(
								array('data' => $kodeo, 'align' => 'left', 'valign'=>'top'),
								array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
							);
							
						}	//obyek			
						
					}	//if obyek
				}	//jenis
				
				
			
			}
					
			
				
		}	
		
	}
		
}	//foreach ($results as $datas)
//SURPLUS DEFIIT
$anggaran_netto = $agg_pendapata_total - $agg_belanja_total;
$realisasi_netto = $rea_pendapata_total - $rea_belanja_total;
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);


if ($kodeuk=='00') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '<strong>' . $kodedinas . '.000.000.' .  '6</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top', 'style'=>'font-size:135%;'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		read_realisasi('ZZ', $data_kel->kodek, $realisasi, $prognosis);
		
		$sisa = $data_kel->anggaran - $realisasi;
		if ($sisa<0) $sisa = 0;
		
		$rows[] = array(
			array('data' => '<strong>' . $kodedinas . '.000.000.' . $data_kel->kodek . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . $data_kel->uraian . '</strong>', 'align' => 'left', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($data_kel->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
			array('data' => '<strong>' . apbd_fn($prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		);		
	
		
		if ($data_kel->kodek=='61') {
			$anggaran_netto_p += $data_kel->anggaran;
			$realisasi_netto_p += $realisasi;

		} else	{	
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $realisasi;
		}
		
		//JENIS
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {
			
			read_realisasi('ZZ', $data_jen->kodej, $realisasi, $prognosis);

			if (($realisasi+$data_jen->anggaran) >0) {

				$sisa = $data_jen->anggaran - $realisasi;
				if ($sisa<0) $sisa = 0;
					
				$rows[] = array(
					array('data' => $kodedinas . '.000.000.' . $data_jen->kodej, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data_jen->uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data_jen->anggaran), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($realisasi), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($sisa), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($prognosis), 'align' => 'right', 'valign'=>'top'),
				);
			
				//OBYEK
				if ($tingkat>'3') {
					$query = db_select('obyek', 'o');
					$query->innerJoin('anggperda', 'ag', 'o.kodeo=left(ag.kodero,5)');
					$query->fields('o', array('kodeo', 'uraian'));
					$query->addExpression('SUM(ag.jumlah)', 'anggaran');
					$query->condition('o.kodej', $data_jen->kodej, '='); 
					$query->groupBy('o.kodeo');
					$query->orderBy('o.kodeo');
					$results_oby = $query->execute();	
					foreach ($results_oby as $data_oby) {
						
						read_realisasi('ZZ', $data_oby->kodeo, $realisasi, $prognosis);
						
						if (($realisasi+$data_oby->anggaran) >0) {
							$sisa = $data_oby->anggaran - $realisasi;
							if ($sisa<0) $sisa = 0;
						
							$rows[] = array(
								array('data' => $kodedinas . '.000.000.' . $data_oby->kodeo, 'align' => 'left', 'valign'=>'top'),
								array('data' => ucfirst(strtolower($data_oby->uraian)), 'align' => 'left', 'valign'=>'top'),
								array('data' => apbd_fn($data_oby->anggaran) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($realisasi) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn1(apbd_hitungpersen($data_oby->anggaran, $realisasi)), 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($sisa) , 'align' => 'right', 'valign'=>'top'),
								array('data' => apbd_fn($prognosis) , 'align' => 'right', 'valign'=>'top'),
							);
						}	
					}	//obyek			
					
				}	//tingkat obyek
			}
		}	//jenis
		
		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_sisa_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_prognosis_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_sisa) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_prognosis) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
}


//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function read_realisasi($kodeuk, $kodeakun, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	
	
	//REALISASI
	if ((substr($kodeakun,0,1)=='4') or (substr($kodeakun,0,2)=='61')) {

		$res = db_query('SELECT SUM(jurnalitem.kredit-jurnalitem.debet) as realisasi 
				FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	} else {

		$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
				FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	}
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS

	$res = db_query('SELECT SUM(prognosis) as prognosisx 
			FROM prognosiskeg WHERE kodeo like :kodeakun AND kodeuk=:kodeuk', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}

function read_realisasi_program($kodeuk, $kodepro, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	

	$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
			FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid inner join kegiatanskpd on jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND kegiatanskpd.kodepro=:kodepro AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodepro'=>$kodepro));
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	$res = db_query('SELECT SUM(prognosis) as prognosisx FROM prognosiskeg INNER JOIN kegiatanskpd ON prognosiskeg.kodekeg=kegiatanskpd.kodekeg WHERE kodeo like :kodeakun AND kodeuk=:kodeuk AND kegiatanskpd.kodepro=:kodepro', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodepro'=>$kodepro));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}

function read_realisasi_kegiatan($kodeuk, $kodekeg, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	

	$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
			FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid inner join kegiatanskpd on jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	$res = db_query('SELECT SUM(prognosis) as prognosisx FROM prognosiskeg INNER JOIN kegiatanskpd ON prognosiskeg.kodekeg=kegiatanskpd.kodekeg WHERE kodeo like :kodeakun AND kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg', array(':kodeakun'=>'5%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}

function read_realisasi_kegiatan_rekening($kodeuk, $kodekeg, $kodeakun, &$realisasi, &$prognosis) {
	$realisasi = 0; $prognosis = 0;
	

	$res = db_query('SELECT SUM(jurnalitem.debet-jurnalitem.kredit) as realisasi 
			FROM jurnalitem INNER JOIN jurnal on jurnalitem.jurnalid=jurnal.jurnalid inner join kegiatanskpd on jurnal.kodekeg=kegiatanskpd.kodekeg WHERE jurnalitem.kodero like :kodeakun AND jurnal.kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg AND MONTH(jurnal.tanggal)<=6', array(':kodeakun'=> $kodeakun . '%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$realisasi = $data->realisasi;			
	}	
	
	
	//PROGNOSIS
	$res = db_query('SELECT SUM(prognosis) as prognosisx FROM prognosiskeg INNER JOIN kegiatanskpd ON prognosiskeg.kodekeg=kegiatanskpd.kodekeg WHERE kodeo like :kodeakun AND kodeuk=:kodeuk AND kegiatanskpd.kodekeg=:kodekeg', array(':kodeakun'=> $kodeakun . '%', ':kodeuk'=>$kodeuk, ':kodekeg'=>$kodekeg));
	foreach ($res as $data) {
		$prognosis = $data->prognosisx;			
	}
	
	
	return $true;
	
}



	
