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
			<a href="<?php echo base_url();?>AcctSavings">
				Daftar Kode Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Kode Simpanan <small>Kelola Kode Simpanan</small>
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
						Daftar
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctSavings/addAcctSavings" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Kode Simpanan Baru
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Kode Simpanan</th>
								<th width="15%">Nama Simpanan</th>
								<th width="15%">Nomor Perikraan</th>
								<th width="15%">Bagi Hasil</th>
								<th width="15%">Nomor Perikraan Basil</th>
								<th width="10%">Nisbah</th>
								<th width="10%">Bagi Hasil</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($acctsavings)){
									echo "
										<tr>
											<td colspan='7' align='center'>Emty Data</td>
										</tr>
									";
								} else {
									foreach ($acctsavings as $key=>$val){									
										echo"
											<tr>			
												<td style='text-align:center'>$no.</td>
												<td>".$val['savings_code']."</td>
												<td>".$val['savings_name']."</td>
												<td>".$val['account_code']." - ".$val['account_name']."</td>
												<td>".$savingsprofitsharing[$val['savings_profit_sharing']]."</td>
												<td>".$this->AcctSavings_model->getAccountName($val['account_basil_id'])."</td>
												<td>".$val['savings_nisbah']."</td>
												<td>".$val['savings_basil']."</td>
												<td>
													<a href='".$this->config->item('base_url').'AcctSavings/editAcctSavings/'.$val['savings_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='".$this->config->item('base_url').'AcctSavings/deleteAcctSavings/'.$val['savings_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
														<i class='fa fa-trash-o'></i> Hapus
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