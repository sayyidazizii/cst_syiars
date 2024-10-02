
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
			<a href="<?php echo base_url();?>PPOBRefund">
				Daftar Transaksi Refund
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Daftar Transaksi Refund <small>Kelola Daftar Transaksi Refund</small>
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
									if(empty($refundtransaction)){
										echo "
											<tr>
												<td colspan='10' align='center'>Data Kosong</td>
											</tr>
										";
									} else {
										foreach ($refundtransaction as $key => $val){
											$coremember = $this->PPOBRefund_model->getCoreMember_Detail($val['member_id']);
											echo"
												<tr>
													<td style='text-align:center'>".$no."</td>			
													<td>".$coremember['member_no']."</td>
													<td>".$coremember['member_name']."</td>
													<td>".$coremember['branch_name']."</td>
													<td>".$val['ppob_unique_code']."</td>
													<td>".$val['ppob_transaction_no']."</td>
													<td>".$val['ppob_transaction_amount']."</td>
													<td>".$val['created_on']."</td>
													<td>".$val['ppob_transaction_remark']."</td>
													<td>
														<a href='".$this->config->item('base_url').'PPOBRefund/detailRefundTransaction/'.$val['ppob_transaction_id']."' class='btn default btn-xs purple'>
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