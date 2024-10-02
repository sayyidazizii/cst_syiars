
<script type="text/javascript">

	
function processRefundTransaction(ppob_transaction_id) {
		var remark 								= prompt("Refund Remark:", "");
		var member_id 							= document.getElementById('member_id').value;
		var ppob_product_id 					= document.getElementById('ppob_product_id').value;
		var branch_id 							= document.getElementById('branch_id').value;
		var ppob_company_id 					= document.getElementById('ppob_company_id').value;
		var ppob_transaction_amount				= document.getElementById('ppob_transaction_amount').value;
		var ppob_transaction_default_amount 	= document.getElementById('ppob_transaction_default_amount').value;
		var ppob_transaction_fee_amount 		= document.getElementById('ppob_transaction_fee_amount').value;
		var ppob_transaction_commission_amount 	= document.getElementById('ppob_transaction_commission_amount').value;
		var savings_account_id 					= document.getElementById('savings_account_id').value;
		var savings_id 							= document.getElementById('savings_id').value;
		if(remark!=null&&remark!=''){
			$.ajax({
					type: "POST",
					url : "<?php echo site_url('PPOBRefund/processRefundTransaction');?>",
					data : {
							'remark' 							: remark,
							'ppob_transaction_id' 				: ppob_transaction_id,
							'member_id' 						: member_id,
							'ppob_product_id' 					: ppob_product_id,
							'branch_id' 						: branch_id,
							'ppob_company_id' 					: ppob_company_id,
							'ppob_transaction_amount' 			: ppob_transaction_amount,
							'ppob_transaction_default_amount' 	: ppob_transaction_default_amount,
							'ppob_transaction_fee_amount' 		: ppob_transaction_fee_amount,
							'ppob_transaction_commission_amount': ppob_transaction_commission_amount,
							'savings_account_id' 				: savings_account_id,
							'savings_id' 						: savings_id
						},
					success: function(msg){
						location.href = "<?php echo site_url('PPOBRefund');?>"
				}
			});
		}else{
			alert("Input Remark Please!");
		}
	}

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
		<li>
			<a href="<?php echo base_url();?>PPOBRefund/DetailRefundTransaction/<?php echo $transactiondetail['ppob_transaction_id'] ;?>">
				Detail Transaksi Refund
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
			<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Transaksi Refund<small> Detail Transaksi Refund</small>
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');

	$auth = $this->session->userdata('auth');

	$coremember = $this->PPOBRefund_model->getCoreMember_Detail($transactiondetail['member_id']);
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Detail
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>PPOBRefund" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="ppob_transaction_no" id="ppob_transaction_no" value="<?php echo $transactiondetail['ppob_transaction_no'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="ppob_transaction_id" id="ppob_transaction_id" value="<?php echo $transactiondetail['ppob_transaction_id'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="branch_id" id="branch_id" value="<?php echo $coremember['branch_id'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="ppob_company_id" id="ppob_company_id" value="<?php echo $transactiondetail['ppob_company_id'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="member_id" id="member_id" value="<?php echo $coremember['member_id'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="ppob_product_id" id="ppob_product_id" value="<?php echo $transactiondetail['ppob_product_id'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="ppob_transaction_default_amount" id="ppob_transaction_default_amount" value="<?php echo $transactiondetail['ppob_transaction_default_amount'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="ppob_transaction_fee_amount" id="ppob_transaction_fee_amount" value="<?php echo $transactiondetail['ppob_transaction_fee_amount'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="ppob_transaction_commission_amount" id="ppob_transaction_commission_amount" value="<?php echo $transactiondetail['ppob_transaction_commission_amount'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="savings_account_id" id="savings_account_id" value="<?php echo $transactiondetail['savings_account_id'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<input type="hidden" name="savings_id" id="savings_id" value="<?php echo $transactiondetail['savings_id'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Nomor Transaksi PPOB</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="ppob_unique_code" id="ppob_unique_code" value="<?php echo $transactiondetail['ppob_unique_code'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Kode Unik PPOB</label>
							</div>
						</div>
					</div>

					<div class = "row">
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<input type="text" name="member_no" id="member_no" value="<?php echo $coremember['member_no'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Nomor Anggota</label>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<input type="text" name="member_name" id="member_name" value="<?php echo $coremember['member_name'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Nama Anggota</label>
							</div>
						</div>
				
						<div class="col-md-4">
							<div class="form-group form-md-line-input">
								<input type="text" name="branch" id="branch" value="<?php echo $coremember['branch_name'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Cabang</label>
							</div>
						</div>
					</div>

					<div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="ppob_transaction_amount" id="ppob_transaction_amount" value="<?php echo $transactiondetail['ppob_transaction_amount'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Nominal Transaksi</label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" name="transaction_date" id="transaction_date" value="<?php echo $transactiondetail['created_on'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Tanggal Transaksi</label>
							</div>
						</div>
					</div>

					<div class = "row">
						<div class="col-md-12">
							<div class="form-group form-md-line-input">
								<input type="text" name="ppob_transaction_remark" id="ppob_transaction_remark" value="<?php echo $transactiondetail['ppob_transaction_remark'] ;?>"  class="form-control" onChange="function_elements_add(this.name, this.value);" readonly>
								<label class="control-label">Remark</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-actions right">
							<button class="btn green-jungle" onclick="processRefundTransaction(<?php echo $transactiondetail['ppob_transaction_id'] ?>)"></i>Refund</button>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>