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
			<a href="<?php echo base_url();?>PreferencePPOB">
				Setting PPOB
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Setting PPOB <small>Kelola Setting PPOB</small>
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
						<a href="<?php echo base_url();?>PreferencePPOB/addPreferencePPOB" class="btn btn-default btn-sm">
							<i class="fa fa-plus"></i>
							<span class="hidden-480">
								Tambah Setting PPOB Baru
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
								<th width="10%">Cabang</th>
								<th width="10%">Admin mBayar</th>
								<th width="15%">mBayar </th></th>
								<th width="15%">Dana PPOB </th></th>
								<th width="15%">Pendapatan PPOB </th></th>
								<th width="15%">Server PPOB </th></th>
								<th width="15%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								if(empty($preferenceppob)){
									echo "
										<tr>
											<td colspan='9' align='center'>Data Kosong</td>
										</tr>
									";
								} else {
									foreach ($preferenceppob as $key=>$val){									
										echo"
											<tr>
												<td style='text-align:center'></td>			
												<td style='text-align:center'>".$no."</td>
												<td>".$val['branch_code']." - ".$val['branch_name']."</td>
												<td>".$val['ppob_mbayar_admin']."</td>
												<td>".$val['ppob_account_income_mbayar']."</td>
												<td>".$val['ppob_account_down_payment']."</td>
												<td>".$val['ppob_account_income']."</td>
												<td>".$val['ppob_account_cost']."</td>
												<td>
													<a href='".$this->config->item('base_url').'PreferencePPOB/editPreferencePPOB/'.$val['job_id']."' class='btn default btn-xs purple'>
														<i class='fa fa-edit'></i> Edit
													</a>
													<a href='".$this->config->item('base_url').'PreferencePPOB/deletePreferencePPOB/'.$val['job_id']."'class='btn default btn-xs red', onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'>
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