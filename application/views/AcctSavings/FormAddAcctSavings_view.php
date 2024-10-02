<script>
	base_url = '<?php echo base_url();?>';
	mappia = "	<?php 
					$site_url = 'AcctSavings/addAcctSavings/';
					echo site_url($site_url); 
				?>";

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('AcctSavings/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}
	
	function function_state_add(value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('AcctSavings/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_data(){
		document.location = "<?php echo base_url();?>AcctSavings/reset_data";
	}

	function processAddAcctAccount() {
		var account_code 			= $("#account_code").val();
		var account_name 			= $("#account_name").val();
		var account_status 			= $("#account_status").val();
		var account_group 			= $("#account_group").val();
		var account_type_id 		= $("#account_type_id").val();

		if(account_code == ''){
			alert('Nomor Perkiraan masih kosong');
			$('#account_code').val('');
		} else if(account_name == ''){
			alert('Nama Perkiraan masih kosong');
			$('#account_name').val('');
		} else if(account_group == ''){
			alert('Golongan Perkiraan masih kosong');
			$('#account_group').val('');
		} else  {
			$.ajax({
				type: "POST",
				url : "<?php echo site_url('AcctSavings/processAddAcctAccount');?>",
				data: {
						'account_code' 		: account_code,
						'account_name'		: account_name,
						'account_status'	: account_status,
						'account_group'		: account_group,
						'account_type_id'	: account_type_id,
					},

				success: function(msg){
					// alert(msg);
					$('#account_code').val('');
					$('#account_name').val('');
					$('#account_group').val('');
					window.location.replace(mappia);
			}
			});
		}
	}
</script>
<?php echo form_open('AcctSavings/processAddAcctSavings',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
	$sesi 	= $this->session->userdata('unique');
	$data 	= $this->session->userdata('addacctsavings-'.$sesi['unique']);

	if (empty($data['savings_code'])){
		$data['savings_code'] 					= '';
	}

	if (empty($data['savings_name'])){
		$data['savings_name'] 					= '';
	}

	if (empty($data['savings_nisbah'])){
		$data['savings_nisbah'] 				= '';
	}
		
	if (empty($data['account_id'])){
		$data['account_id'] 					= 0;
	}

	if (empty($data['account_basil_id'])){
		$data['account_basil_id'] 				= 0;
	}

	if (empty($data['savings_basil'])){
		$data['savings_basil'] 					= '';
	}

	if (empty($data['savings_profit_sharing'])){
		$data['savings_profit_sharing'] 		= 0;
	}
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
			<a href="<?php echo base_url();?>AcctSavings">
				Daftar Kode Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctSavings/addAcctSavings">
				Tambah Kode Simpanan 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Form Tambah Kode Simpanan
</h3>
<?php
// print_r($data);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Tambah
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctSavings" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">						
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
										<input type="text" class="form-control" name="savings_code" id="savings_code" autocomplete="off" value="<?php echo set_value('savings_code',$data['savings_code']);?>" onChange="function_elements_add(this.name, this.value);"/>
									
									<label class="control-label">Kode Simpanan<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
										<input type="text" class="form-control" name="savings_name" id="savings_name" value="<?php echo set_value('savings_name',$data['savings_name']);?>" onChange="function_elements_add(this.name, this.value);">
									
									<label class="control-label">Nama Simpanan
										<span class="required">
											*
										</span>
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php 
										echo form_dropdown('account_id', $acctaccount, set_value('account_id', $data['account_id']), 'id="account_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');
									?>

									<label class="control-label">Nomor Perkiraan</label>
								</div>
							</div>
							<div class="col-md-6" id='search2' style='display:block;'>
								<div class="form-group form-md-line-input">
									<label class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
									<a class="btn blue" data-toggle="modal" href="#modalcoabaru"><i class="fa fa-plus"></i> Tambah Nomor Perkiraan Baru</a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php echo form_dropdown('savings_profit_sharing', $savingsprofitsharing, set_value('savings_profit_sharing', $data['savings_profit_sharing']),'id="savings_profit_sharing" class="form-control select2me" onChange="function_elements_add(this.name, this.value);" ');?>
									<label class="control-label">Bagi Hasil</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<?php 
										echo form_dropdown('account_basil_id', $acctaccount, set_value('account_id', $data['account_basil_id']), 'id="account_basil_id" class="form-control select2me" onChange="function_elements_add(this.name, this.value);"');
									?>

									<label class="control-label">Nomor Perkiraan Bagi Hasil</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_nisbah" id="savings_nisbah" autocomplete="off" value="<?php echo set_value('savings_nisbah',$data['savings_nisbah']);?>" onChange="function_elements_add(this.name, this.value);"/>
									<label class="control-label">Nisbah<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="savings_basil" id="savings_basil" autocomplete="off" value="<?php echo set_value('savings_basil',$data['savings_basil']);?>" onChange="function_elements_add(this.name, this.value);"/>
									<label class="control-label">Bagi Hasil<span class="required">*</span></label>
								</div>
							</div>
						</div>

						

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="reset_data();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" id="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>

<div class="modal fade bs-modal-lg" id="modalcoabaru" tabindex="-1" role="basic" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">Nomor Perkiraan Baru</h4>
			</div>
			
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<input type="text" class="form-control" id="account_code" name="account_code" autocomplete="off">
							<label class="control-label">Nomor Perkiraan</label>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<input type="text" class="form-control" id="account_name" name="account_name" autocomplete="off">
							<label class="control-label">Nama Perkiraan</label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<input type="text" class="form-control" id="account_group" name="account_group" autocomplete="off">
							<label class="control-label">Golongan Perkiraan</label>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<?php echo form_dropdown('account_status', $accountstatus, set_value('account_status'),'id="account_status" class="form-control select2me"');?>
							<label class="control-label"></label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group form-md-line-input">
							<?php echo form_dropdown('account_type_id', $kelompokperkiraan, set_value('account_type_id'),'id="account_type_id" class="form-control select2me"');?>
							<label class="control-label">Kelompok Perkiraan</label>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn red" data-dismiss="modal"><i class="fa fa-times"> Tutup</i></button>
				<button type="button" class="btn green-jungle" onClick='processAddAcctAccount();'><i class="fa fa-check"> Simpan</i></button>
			</div>
		</div>
	</div>
</div>