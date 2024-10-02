<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Configuration {
	public function __construct(){
		define('SQL_HOST','localhost');
		define('SQL_USER','root');
		define('SQL_PASSWORD','');
		define('SQL_DB','ims_mitra_utama');
		define('PAGGING','10');
		define('LIMIT','E5BWpg==');
	}

	public function Month(){
		$month 		= array(
						'01'		=> "Januari",
						'02'		=> "Februari",
						'03'		=> "Maret",
						'04'		=> "April",
						'05'		=> "Mei",
						'06'		=> "Juni",
						'07'		=> "Juli",
						'08'		=> "Agustus",
						'09'		=> "September",
						'10'		=> "Oktober",
						'11'		=> "November",
						'12'		=> "Desember",
					); 

		return $month;
	}

	public function MonthName(){
		$month_name 		= array(
						'January'		=> "01",
						'February'		=> "02",
						'March'			=> "03",
						'April'			=> "04",
						'May'			=> "05",
						'June'			=> "06",
						'July'			=> "07",
						'August'		=> "08",
						'September'		=> "09",
						'October'		=> "10",
						'November'		=> "11",
						'December'		=> "12",
					); 

		return $month_name;
	}

	public function DayName(){
		$day_name 		= array(
						'Sun' 			=> "Minggu",
						'Mon' 			=> "Senin",
						'Tue' 			=> "Selasa",
						'Wed' 			=> "Rabu",
						'Thu' 			=> "Kamis",
						'Fri' 			=> "Jumat",
						'Sat' 			=> "Sabtu",
					); 

		return $day_name;
	}

	public function PrintStatus(){
		$print_status = array (0 => 'Belum Cetak', 1 => 'Sudah Cetak');

		return $print_status;
	}

	public function MemberSavings(){
		$member_savings = array (0 => '', 1 => 'Simpanan Pokok', 2 => 'Simpanan Khusus', 3 => 'Simpanan Wajib');

		return $member_savings;
	}

	public function ReportType(){
		$report_type = array (1 => 'Laba Rugi', 2 => 'Neraca');

		return $report_type;
	}

	public function MemberGender(){
		$member_gender = array (9 => ' ', 0 => 'Perempuan', 1 => 'Laki - Laki');

		return $member_gender;
	}

	public function MemberStatus(){
		$member_status = array (0 => 'Calon Anggota', 1 => 'Anggota');

		return $member_status;
	}

	public function SavingsProfitSharing(){
		$savings_provit_sharing = array (0 => 'Tidak Bagi Hasil', 1 => 'Dapat Bagi Hasil');

		return $savings_provit_sharing;
	}

	public function AccountStatus(){
		$account_status = array (0 => 'Debit', 1 => 'Kredit');

		return $account_status;
	}

	public function KelompokPerkiraan(){
		$kelompok_perkiraan = array (0 => 'NA - Neraca Aktiva', 1 => 'NP - Neraca Pasiva', 2 => 'RA - Rugi Laba (A)', 3 => 'RP - Rugi Laba (B)');

		return $kelompok_perkiraan;
	}

	public function MemberCharacter(){
		$member_character = array (9 => ' ', 2 => 'Pendiri', 0 => 'Biasa', 1 => 'Luar Biasa');

		return $member_character;
	}

	public function MemberIdentity(){
		$member_identity = array (9 => ' ', 0 => 'KTP', 1 => 'KK', 2 => 'SIM', 3 => 'AKTA');

		return $member_identity;
	}

	public function KelompokLaporanSimpanan(){
		$kelompok_laporan_simpanan = array (0 => 'Global', 1 => 'Jenis Simpanan');

		return $kelompok_laporan_simpanan;
	}
	
	public function KelompokLaporanSimpananBerjangka(){
		$kelompok_laporan_simpanan_berjangka = array (0 => 'Global', 1 => 'Jenis Waktu');

		return $kelompok_laporan_simpanan_berjangka;
	}

	public function KelompokLaporanPembiayaan(){
		$kelompok_laporan_pembiayaan = array (0 => 'Global', 1 => 'Jenis Akad', 2 => 'Sumber Dana');

		return $kelompok_laporan_pembiayaan;
	}

	public function FamilyRelationship(){
		$family_relationship = array (9 => ' ', 0 => 'Anak', 1 => 'Istri/Suami', 2 => 'Saudara', 3 => 'Lainnya');

		return $family_relationship;
	}

	public function BlockirType(){
		$blockir_type = array (9 => '', 0 => 'Rekening', 1 => 'Saldo');

		return $blockir_type;
	}

	public function BlockirStatus(){
		$blockir_status = array (9 => '', 0 => 'UnBlockir', 1 => 'Blockir');

		return $blockir_status;
	}

	public function AcctReportType(){
		$acct_report_type = array (1 => 'Title', 2 => 'Subtitle', 3 => 'Parent', 4 => 'Loop', 5 => 'Sum', 6 => 'GrandTotal');

		return $acct_report_type;
	}

	public function ProfitLossReportType(){
		$profit_loss_report_type = array (1 => 'Rugi Laba Bulanan', 2 => 'Rugi Laba Tahunan');

		return $profit_loss_report_type;
	}

	public function AgunanType(){
		$agunan_type = array (9 => '', 1 => 'BPKB', 2 => 'Sertifikat');
	}

	public function AccountComparationReportType(){
		$account_comparation_report_type = array (1 => 'Komparasi Bulanan', 2 => 'Komparasi Tahunan');

		return $account_comparation_report_type;
	}

	public function FinancialAnalysisType(){
		$financial_analysis_type = array (
				0 => '',
				1 => 'LIKUIDITAS CASH RATIO',
				2 => 'CAR (Capital Aset Ratio)',
				3 => 'FDR (Financing to Debt Ratio)',
				4 => 'BOPO (Beban Operasional vs Pendapatan Operasional)',
		);

		return $financial_analysis_type;
	}

	public function ManagementZakatType(){
		$management_zakat_type = array (9 => '', 0 => 'Penerimaan Zakat', 1 => 'Penyaluran Zakat');

		return $management_zakat_type;
	}

	public function SourceFundZakat(){
		$source_fund_zakat = array (9 => '', 0 => 'KSPPS (Internal)', 1 => 'Pihak Luar (Eksternal)');

		return $source_fund_zakat;
	}

	public function DistributionZakat(){
		$distribution_zakat = array (9 => '', 0 => 'Badan / Yayasan', 1 => 'Lainnya');

		return $distribution_zakat;
	}

	public function BranchStatus(){
		$branch_status = array (0 => 'Kantor Cabang', 1 => 'Kantor Pusat');

		return $branch_status;
	}

	public function ConsolidationReport(){
		$consolidation_report = array (9 => '', 0 => 'Cabang', 1 => 'Konsolidasi');

		return $consolidation_report;
	}

	public function SavingsCashMutationStatus(){
		$savings_cash_mutation_status = array (0 => 'Admin', 1 => 'Android');

		return $savings_cash_mutation_status;
	}

	public function CreditsPaymentStatus(){
		$credits_payment_status = array (0 => 'Admin', 1 => 'Android');

		return $credits_payment_status;
	}

	public function MembershipStatus(){
		$membership_status 	= array (0 => 'Aktif', 1 => 'Sudah Dihapus', 2 => 'Keluar');

		return $membership_status;
	}

	/* public function PpobTopupStatus(){
		$ppob_topup_status = array ( 9 => 'Belum Proses', 0 => 'Proses', 1 => 'Sukses');

		return $ppob_topup_status;
	}

	public function TransferStatus(){
		$transfer_status = array ( 0 => 'Admin Koperasi', 1 => 'Aplikasi Mbayar', 2 => 'Ziswaf', 3 => 'Topup PPOB');

		return $transfer_status;
	} */

	public function PpobTopupStatus(){
		$ppob_topup_status = array ( 9 => 'Belum Proses', 0 => 'Proses', 1 => 'Sukses', 2 => 'Gagal');

		return $ppob_topup_status;
	}

	public function PpobStatus(){
		$ppob_status = array ( 9 => 'Belum Proses', 0 => 'Proses', 1 => 'Sukses', 2 => 'Gagal');

		return $ppob_status;
	}

	public function TransferStatus(){
		$transfer_status = array ( 0 => 'Admin Koperasi', 1 => 'Aplikasi Mbayar', 2 => 'Ziswaf', 3 => 'Topup PPOB');

		return $transfer_status;
	}

	public function PpobResponeCode(){
		$respone_code = array (
			00		=> 'Transaksi Sukses',
			99		=> 'Transaksi Gagal',
			98		=> 'Kode produk tidak tersedia',
			97		=> 'Kode produk tidak aktif',
			96		=> 'Host atau biller sedang offline',
			95		=> 'Inquiry gagal',
			94		=> 'ID Pelanggan tidak valid',
			93		=> 'Nominal pembayaran tidak valid',
			92		=> 'Pembayaran gagal',
			91		=> 'Pembayaran sudah dilakukan untuk hari ini',
			90		=> 'Pembayaran sedang dalam proses',
			89		=> 'Pembayaran sedang dalam proses',
			88		=> 'Data pelanggan tidak valid',
			87		=> 'Ref ID tidak ditemukan',
			86		=> 'Tagihan sudah dibayar',
			85		=> 'Transaksi tidak dapat dilakukan',
			84		=> 'Transaksi tidak dapat dilakukan, cut off time',
			83		=> 'Inquiry produk tidak tersedia',
			82		=> 'Kode produk tidak diperbolehkan',
			68		=> 'Transaksi Suspect',
			9983	=> 'Pembayaran terjadi Gangguan, Lakukan Advice Manual, transaksi tetap dilakukan pendebetan',
			22		=> 'quota/deposit tidak mencukupi',
		);

		return $respone_code;
	}

	public function JumlahBulan(){
		$month 		= array (
						'01'		=> "1 Bulan",
						'02'		=> "2 Bulan",
						'03'		=> "3 Bulan",
						'04'		=> "4 Bulan",
						'05'		=> "5 Bulan",
						'06'		=> "6 Bulan",
						'07'		=> "7 Bulan",
						'08'		=> "8 Bulan",
						'09'		=> "9 Bulan",
						'10'		=> "10 Bulan",
						'11'		=> "11 Bulan",
						'12'		=> "12 Bulan",
					);

		return $month;
	}

	public function SettingPriceStatus(){
		$settingpricestatus = array ( 0 => 'Belum Di Pilih', 1 => 'Up Selling', 2 => 'Fix Admin');

		return $settingpricestatus;
	}

	function Unpush($pesan,$key){//$key >= 0 or <=25
		$msg = str_split($pesan);
		$dresult = '';
		for($j=1;$j<=strlen($pesan);$j++){
			if ((ord($msg[$j-1])<65) or (ord($msg[$j-1])>90)){
				$dresult = $dresult.$msg[$j-1];
			} else {
				$ord_msg[$j-1] = 65+fmod(ord($msg[$j-1])+65-$key,26);
				$dresult = $dresult.chr($ord_msg[$j-1]);
			}
		}
		return $dresult;
	}
	
	function convert($msg){
		$division	= bindec("010");
		$lenght		= strlen($msg);
		$div		= $lenght/$division;
		$ret		='';
		$block		='';
		for($i=0;$i<$div;$i++){
			$val	= substr($msg,$i*$division,$division);
			if(substr($val,1,1)=="0"){
				$val = substr($val,0,1);
			}
			$dec 	= hexdec($val);
			if(strlen($dec)==1){
				$bin='00'.$dec;
			}else if(strlen($dec)==2){
				$bin='0'.$dec;
			} else {
				$bin=$dec;
			}
			$block .= $bin;
			if (strlen($block)==6){
				$text = chr(bindec($block));
				$ret	.= $text;
				$block='';
			}
		}
		return $ret;
	}
	
	function Text($plain){
		$division	= bindec("010");
		$lenght		= strlen($plain);
		$div		= $lenght/$division;
		$ret		='';
		$block		='';
		for($i=0;$i<$div;$i++){
			$val	= substr($plain,$i*$division,$division);
			if($val=='00'){
				continue;
			} else {
				$dec 	= hexdec($val);
				if(strlen($dec)==1){
					$bin='00'.$dec;
				}else if(strlen($dec)==2){
					$bin='0'.$dec;
				} else {
					$bin=$dec;
				}
				$ret .= $bin;
			}
		}
		return chr(bindec($ret));
	}
	
	function reassembly($byte){
		$text = '';
		for($i=0;$i<(strlen($byte)/6);$i++){
			$text .= $this->Text(substr($byte,$i*6,6));
		}
		return $text;
	}
	
	function rearrange($text){
		for($i=0;$i<(strlen($text)/2);$i++){
			$arr[$i] = substr($text,$i*2,2);
		}
		$result = implode(":",$arr);
		return $result;
	}
}