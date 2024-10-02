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

				alert("Apakah Anda Yakin Akan Memproses Basil ??");
				return true;			
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
			<a href="<?php echo base_url();?>AcctSavingsProfitSharingNew">
				Perhitungan SRH
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Hasil Perhitungan SRH
</h3>
<?php 
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Hasil Perhitungan SRH
				</div>
			</div>
			<div class="portlet-body form">
				<div class="form-body">
					<div class="row">		
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Today Transaction Date</th>
								<th width="10%">Yesterday Transaction Date</th>
								<th width="10%">Range Date</th>
								<th width="15%">Opening Balance</th>
								<th width="15%">Last Balance</th>
								<th width="15%">SRH</th>
								<th width="15%">SRH Baru</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($hitungsrh)){
									echo "
										<tr>
											<td colspan='11' align='center'>Basil Belum Dihitung</td>
										</tr>
									";
								} else {
									foreach ($hitungsrh as $key=>$val){		
															
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td style='text-align:left'>".$val['today_transaction_date']."</td>
												<td style='text-align:left'>".$val['yesterday_transaction_date']."</td>
												<td>".$val['range_date']."</td>
												<td style='text-align:right'>".number_format($val['opening_balance'], 2)."</td>
												<td style='text-align:right'>".number_format($val['last_balance'], 2)."</td>
												<td style='text-align:right'>".number_format($val['daily_average_balance_old'], 2)."</td>
												<td style='text-align:right'>".number_format($val['daily_average_balance_new'], 2)."</td>
											</tr>
										";
										$no++;
										$totalsrhlama += $val['daily_average_balance_old'];
										$totalsrhbaru += $val['daily_average_balance_new'];
									} 
								}
							// print_r($period);	
							?>
							<tr>
								<td colspan="6"></td>
								<td><?php echo number_format($totalsrhlama, 2); ?></td>
								<td><?php echo number_format($totalsrhbaru, 2); ?></td>
							</tr>
							</tbody>
							</table>
					</div>						
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>