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
			<a href="<?php echo base_url();?>PpobTopupMember">
				Daftar Top Up PPOB Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$sesi=$this->session->userdata('filter-ppobtopup');

	if(!is_array($sesi)){
		$sesi['start_date']				= date('Y-m-d');
		$sesi['end_date']				= date('Y-m-d');
	}
?>	
<?php	echo form_open('PpobTopupMember/filter',array('id' => 'myform', 'class' => '')); 

	$start_date			= $sesi['start_date'];
	$end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar Top Up PPOB Anggota
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>PpobTopupMember/addPpobTopupMember" class="btn btn-default btn-sm">
						<i class="fa fa-plus"></i>
						<span class="hidden-480">
							Tambah Top Up Anggota Baru 
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					 <div class = "row">
						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($start_date);?>"/>
								<label class="control-label">Tanggal Awal
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-4">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-medium date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($end_date);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>

			<?php echo form_close(); ?>

			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordered table-hover table-full-width" id="sample_3">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Tanggal Top up</th>
								<th width="15%">No. Anggota</th>
								<th width="20%">Anggota</th>
								<th width="20%">Simpanan</th>
								<th width="15%">Jumlah Top up</th>
							</tr>
						</thead>
						<tbody>
								<?php
									$no = 1;
									//print_r($PpobTopupMember);
									if(!is_array($PpobTopupMember)){
										echo "<tr><th colspan='6' style='text-align  : center !important;'>Data is Empty</th></tr>";
									} else {
										foreach($PpobTopupMember as $key=>$val){
											echo"
												<tr>
													<td style='text-align: center'>".$no."</td>				
													<td style='text-align: center'>".tgltoview($val['ppob_topup_member_date'])."</td>
													<td style='text-align: center !important;'>".$val['member_no']."</td>
													<td style='text-align: left !important;'>".$val['member_name']."</td>
													<td style='text-align: left !important;'>".$val['savings_account_no']." - ".$val['savings_name']."</td>
													<td style='text-align: right !important;'>".nominal($val['ppob_topup_member_amount'],2)."</td>
													
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
	</div>
</div>