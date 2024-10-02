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
			<a href="<?php echo base_url();?>AcctDepositoProfitSharingCalculate">
				Perhitungan Basil Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Perhitungan Basil Simpanan
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="row">
					<?php
							echo form_open('AcctDepositoProfitSharingCalculate/processPrinting');  ?>	
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Member (Deposito)</th>
								<th width="20%">Savings (Deposito)</th>
								<th width="10%">Due Date</th>
								<th width="15%">Member (Savings)</th>
								<th width="15%">Savings (Savings)</th>
								<th width="15%">Simpanan Berjangka</th>
								<th width="15%">Index</th>
								<th width="15%">Basil 3 bulan</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctdepositoprofitsharing)){
									echo "
										<tr>
											<td colspan='11' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($acctdepositoprofitsharing as $key=>$val){		
									$acctsavingsaccount = $this->AcctDepositoProfitSharingCalculate_model->getAcctSavingsAccount_Detail($val['savings_account_id']);							
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td style='text-align:left'>".$val['member_name']."</td>
												<td style='text-align:left'>".$val['deposito_account_no']."</td>
												<td style='text-align:left'>".tgltoview($val['deposito_account_due_date'])."</td>
												<td>".$acctsavingsaccount['member_name']."</td>
												<td>".$acctsavingsaccount['savings_account_no']."</td>

												<td style='text-align:right'>".number_format($val['deposito_daily_average_balance'], 2)."</td>
												<td style='text-align:left'>".$val['deposito_index_amount']."</td>
												<td style='text-align:right'>".number_format($val['deposito_profit_sharing_amount'], 2)."</td>
												
											</tr>
										";
										$no++;
									} 
								}
								
							?>
							</tbody>
							</table>
					</div>
					<div class="row">
						<div class="col-md-12 " style="text-align  : right !important;">
							<input type="submit" name="Preview" id="Preview" value="Preview" class="btn blue" title="Preview">
							
												
						</div>
					</div>

						
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>
