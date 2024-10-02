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
			<a href="<?php echo base_url();?>AcctDepositoProfitSharing">
				Perhitungan Basil Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perhitungan Basil Simpanan Berjangka
</h3>
<?php 

	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi = $this->session->userdata('unique');
?>	
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Perhitungan Basil Simpanan
				</div>
			</div>
			<div class="portlet-body form">
				<div class="form-body">
					<div class="row">	
						<?php
							echo form_open('AcctSavingsTransferPrincipalSavings/processAdd');  ?>	
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="20%">Nama</th>
								<th width="20%">Jenis Simpanan</th>
								<th width="10%">No. SimpKa</th>
								<th width="20%">Alamat</th>
								<th width="10%">Opening Balance</th>
								<th width="10%">Last Balance</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($datacoremember)){
									echo "
										<tr>
											<td colspan='6' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($datacoremember as $key=>$val){						
										echo"
											<tr>			
												<td style='text-align:center'>".$val['no']."</td>
												<td style='text-align:left'>".$val['member_id']." - ".$val['member_name']."</td>
												<td style='text-align:left'>".$val['savings_id']." - ".$val['savings_name']."</td>
												<td style='text-align:left'>".$val['savings_account_no']."</td>
												<td>".$val['member_address']."</td>
												<td style='text-align:right'>".number_format($val['savings_account_opening_balance'], 2)."</td>
												<td style='text-align:right'>".number_format($val['savings_account_last_balance'], 2)."</td>
												
												
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
						<div class="form-actions right">
							<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Proses</i></button>
						</div>	
					</div>
						
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php echo form_close(); ?>