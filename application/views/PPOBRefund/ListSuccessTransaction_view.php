
<script type="text/javascript">
 

 </script>

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
			<a href="<?php echo base_url();?>PPOBRefund/addRefundTransaction">
				Daftar Transaksi
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Transaksi <small>Kelola Daftar Transaksi</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');

	$sesi=$this->session->userdata('filter-PPOBRefund');

	if(!is_array($sesi)){
		$sesi['start_date']			= date('Y-m-d');
		$sesi['end_date']			= date('Y-m-d');
		$sesi['branch_id']			= '';
	}
?>	
<?php	echo form_open('PPOBRefund/filter',array('id' => 'myform', 'class' => '')); 

	// $start_date			= $sesi['start_date'];
	// $end_date			= $sesi['end_date'];
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Pencarian
				</div>
				<div class="tools">
					<a href="javascript:;" class='expand'></a>
				</div>
			</div>
			<div class="portlet-body display-hide">
				<div class="form-body form">
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-big date-picker" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="<?php echo tgltoview($sesi['start_date']);?>"/>
								<label class="control-label">Tanggal Mulai
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input class="form-control form-control-inline input-big date-picker" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="<?php echo tgltoview($sesi['end_date']);?>"/>
								<label class="control-label">Tanggal Akhir
									<span class="required">
										*
									</span>
								</label>
							</div>
						</div>
						<!-- <?php if($auth['branch_status'] == 1) { ?>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<?php
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id',$sesi['branch_id']),'id="branch_id" class="form-control select2me" ');
								?>
								<label>Cabang</label>
							</div>
						</div>
						<?php } ?> -->
					</div>

					<div class="row">
						<div class="form-actions right">
							<button type="button" class="btn red" onClick="reset_search();"><i class="fa fa-times"></i> Batal</button>
							<button type="submit" class="btn green-jungle"><i class="fa fa-search"></i> Cari</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Daftar
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<table class="table table-striped table-bordeblue table-hover table-checkable order-column" id="sample_1">
						<thead>
							<tr>
								<th width="0%"></th>
								<th width="5%">No</th>
								<th width="10%">Nomor Anggota</th>
								<th width="15%">Nama Anggota</th>
								<th width="12%">Cabang</th>
								<th width="5%">Kode Unik PPOB</th>
								<th width="5%">Nomor Transaksi</th>
								<th width="8%">Nominal Transaksi</th>
								<th width="7%">Tanggal Transaksi</th>
								<th width="33%">Remark</th>
								<th width="5%">Action</th>
							</tr>
						</thead>
						<tbody>
								<?php
									$no = 1;
									if(empty($successtransaction)){
										echo "
											<tr>
												<td colspan='10' align='center'>Data Kosong</td>
											</tr>
										";
									} else {
										foreach ($successtransaction as $key => $val){			
											$coremember = $this->PPOBRefund_model->getCoreMember_Detail($val['member_id']);
											echo"
												<tr>
													<td style='text-align:center'></td>
													<td style='text-align:center'>".$no."</td>
													<td>".$coremember['member_no']."</td>
													<td>".$coremember['member_name']."</td>
													<td>".$coremember['branch_name']."</td>
													<td>".$val['ppob_unique_code']."</td>
													<td>".$val['ppob_transaction_no']."</td>
													<td style='text-align:right'>".nominal($val['ppob_transaction_amount'])."</td>
													<td>".$val['created_on']."</td>
													<td>".$val['ppob_transaction_remark']."</td>
													<td>
														<a href='".$this->config->item('base_url').'PPOBRefund/processAddRefundTransaction/'.$val['ppob_transaction_id']."' onClick='javascript:return confirm(\"Yakin Refund Transaksi Ini ?\")' class='btn default btn-xs red'>
															<i class='fa fa-edit'></i> Refund
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