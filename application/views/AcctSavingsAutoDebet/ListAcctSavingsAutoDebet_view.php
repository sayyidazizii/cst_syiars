<style>
	th{
		font-size:14px  !important;
		font-weight: bold !important;
		text-align:center !important;
		margin : 0 auto;
		vertical-align:middle !important;
	}
	td{
		font-size:12px  !important;
		font-weight: normal !important;
	}
	
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$("#Save").click(function(){
			var month 	= $("#month_period").val();
			var year 	= $("#year_period").val();

			
			if(month == ''){
				alert("Bulan masih kosong");
				return false;
			}else if(year == ''){
				alert("Tahun masih kosong");
				return false;
			}else {
				alert("Apakah Periode Bulan dan Tahun yang Anda Pilih Sudah Benar ??");
				return true;
			} 
			// else if(savings_account_id == ''){
			// 	alert("Rek. Simpanan masih kosong");
			// 	return false;
			// } 	
		});
	});
</script>
<div class="row-fluid">
	

			<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-bar">	
	<ul class="page-breadcrumb">
		<li>
			<i class="fa fa-home"></i>
			<a href="<?php echo base_url();?>">
				Beranda
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctSavingsAutoDebet">
				Perhitungan Basil Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perhitungan Basil Simpanan
</h3>
<?php 
	echo form_open('savings-auto-debet/get-savings-account',array('id' => 'myform', 'class' => 'horizontal-form')); 

	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
	$year_now 		=	date('Y');
	
	for($i = ($year_now-2); $i<($year_now+2); $i++){
		$year[$i] 	= $i;
	}

	$period 				= $this->AcctSavingsAutoDebet_model->getPeriodLog();

	$last_month 			= substr($period['period'],0,2);

	if($last_month == 12){
		$next_month 		= 1;
	} else {
		$next_month 		= $last_month + 1;
	}
	
	

	if($next_month < 10){
		$next_period = '0'.$next_month;
	} else {
		$next_period = $next_month;
	}

	$data['year_period'] 	= $year_now;
	$data['month_period'] 	= $next_period;

	// print_r($next_month);
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Auto Debet Simpanan - Simpanan Pokok
				</div>
			</div>
			<div class="portlet-body form">
				<div class="form-body">
					<div class="row">	
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php 
									echo form_dropdown('month_period', $month, set_value('month_period', $data['month_period']),'id="month_period" class="form-control select2me" ');
								?>

								<label class="control-label">Bulan</label>
							</div>
						</div>					
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php 
									echo form_dropdown('year_period', $year, set_value('year_period', $data['year_period']),'id="year_period" class="form-control select2me" ');
								?>
								<label class="control-label">Tahun</label>
								
							</div>
						</div>
						
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Proses</button>
						</div>	
					</div>
						
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Auto Debet
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>AcctSavingsAutoDebet/processAcctSavingsAutoDebet" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Proses Auto Debet
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
					<thead>
						<tr>
							<th width="1%"></th>
							<th width="4%">No</th>
							<th width="10%">Nama Anggota</th>
							<th width="15%">Cabang</th>
							<th width="15%">Jenis Simpanan</th>
							<th width="15%">Nomor Rekening</th>
							<th width="15%">Alamat</th>
							<th width="15%">Jumlah Saldo</th>
							<!-- <th width="10%">Action</th> -->
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($corememberautodebet)){
								echo "
									<tr>
										<td colspan='8' align='center'>Empty Data</td>
									</tr>
								";
							} else {
								foreach ($corememberautodebet as $key=>$val){									
									echo"
										<tr>			
											<td style='text-align:center'></td>
											<td style='text-align:center'>$no.</td>
											<td>".$val['member_name']."</td>
											<td>".$val['branch_name']."</td>
											<td>".$val['savings_name']."</td>
											<td>".$val['savings_account_no']."</td>
											<td>".$val['member_address']."</td>
											<td style='text-align:right'>".number_format($val['savings_account_last_balance'])."</td>
										</tr>
									";
									$no++;
								} 
								// <td>
								// 	<a href='".$this->config->item('base_url').'AcctSavingsBankMutation/voidAcctSavingsBankMutation/'.$val['savings_bank_mutation_id']."'class='btn default btn-xs red'>
								// 		<i class='fa fa-trash-o'></i> Batal
								// 	</a>
								// </td>
							}
							
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>


<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Lainnya
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_4">
					<thead>
						<tr>
							<th width="1%"></th>
							<th width="4%">No</th>
							<th width="10%">Nama Anggota</th>
							<th width="15%">Cabang</th>
							<th width="15%">Jenis Simpanan</th>
							<th width="15%">Nomor Rekening</th>
							<th width="15%">Alamat</th>
							<th width="15%">Jumlah Saldo</th>
							<!-- <th width="10%">Action</th> -->
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							if(empty($coremembernonautodebet)){
								echo "
									<tr>
										<td colspan='8' align='center'>Empty Data</td>
									</tr>
								";
							} else {
								foreach ($coremembernonautodebet as $key=>$val){									
									echo"
										<tr>			
											<td style='text-align:center'></td>
											<td style='text-align:center'>$no.</td>
											<td>".$val['member_name']."</td>
											<td>".$val['branch_name']."</td>
											<td>".$val['savings_name']."</td>
											<td>".$val['savings_account_no']."</td>
											<td>".$val['member_address']."</td>
											<td style='text-align:right'>".number_format($val['savings_account_last_balance'])."</td>
										</tr>
									";
									$no++;
								} 
							}
							
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>