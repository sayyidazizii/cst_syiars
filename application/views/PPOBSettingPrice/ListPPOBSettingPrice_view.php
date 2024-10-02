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
	<?php
		echo $this->session->userdata('message');
		$this->session->unset_userdata('message');
	?>

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
			<a href="<?php echo base_url();?>PPOBSettingPrice">
				Daftar Setting Harga PPOB
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Setting Harga PPOB <small>Kelola Setting Harga PPOB</small>
</h3>
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Daftar
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>PPOBSettingPrice/addPPOBSettingPrice" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Setting Harga PPOB Baru
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="0%"></th>
								<th width="5%">No</th>
								<th width="25%">Kode Setting Harga PPOB</th>
								<th width="15%">Fee Setting Harga PPOB</th>
								<th width="15%">Komisi Setting Harga PPOB</th>
								<th width="15%">Maksimal Setting Harga PPOB</th>
								<th width="10%">Status</th>
								<th width="15%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($ppobsettingprice)){
									echo "
										<tr>
											<td colspan='7' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($ppobsettingprice as $key=>$val){									
										echo"
											<tr>
												<td style='text-align:center'></td>			
												<td style='text-align:center'>".$no."</td>
												<td style='text-align:left'>".$val['setting_price_code']."</td>
												<td style='text-align:right'>".nominal($val['setting_price_fee'])."</td>
												<td style='text-align:right'>".nominal($val['setting_price_commission'])."</td>
												<td style='text-align:right'>".nominal($val['setting_price_max'])."</td>
												<td style='text-align:right'>".$settingpricestatus[$val['setting_price_status']]."</td>
												<td>
													<a href='".$this->config->item('base_url').'PPOBSettingPrice/editPPOBSettingPrice/'.$val['setting_price_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
												</td>
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