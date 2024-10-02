<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("account_code").value 			= "<?php echo $ppobsettingprice['account_code']; ?>";
		document.getElementById("account_name").value 			= "<?php echo $ppobsettingprice['account_name']; ?>";
		document.getElementById("account_group").value 			= "<?php echo $ppobsettingprice['account_group']; ?>";
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var setting_price_fee 			= $("#setting_price_fee").val();
			var setting_price_commission 	= $("#setting_price_commission").val();
			var setting_price_max 			= $("#setting_price_max").val();
			
			var total_fee_commission 			= parseFloat(setting_price_fee) + parseFloat(setting_price_commission);

			if (parseFloat(total_fee_commission) > parseFloat(setting_price_max)){
				alert("Total Fee Dan Komisi Lebih Besar Daripada Maksimal Setting Harga");
				return false
			} else {
				return true;
			}
		});
    });
</script>
<?php echo form_open('PPOBSettingPrice/processEditPPOBSettingPrice',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
		<li>
			<a href="<?php echo base_url();?>PPOBSettingPrice/editPPOBSettingPrice/"<?php $ppobsettingprice['setting_price_id'] ?>>
				Edit Setting Harga PPOB 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Setting Harga PPOB 
</h3>

<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>PPOBSettingPrice" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<?php
							echo $this->session->userdata('message');
							$this->session->unset_userdata('message');
						?>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="setting_price_code" name="setting_price_code" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_code']; ?>" readonly>

									<input type="hidden" class="form-control" id="setting_price_id" name="setting_price_id" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_id']; ?>" readonly>
									<label class="control-label">Kode Setting Harga PPOB</label>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="setting_price_status" name="setting_price_status" autocomplete="off" value="<?php echo $settingpricestatus[$ppobsettingprice['setting_price_status']]; ?>" readonly>
									<label class="control-label">Status Setting Harga PPOB</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="setting_price_fee" name="setting_price_fee" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_fee']; ?>">

									<input type="hidden" class="form-control" id="setting_price_fee_old" name="setting_price_fee_old" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_fee']; ?>">

									<label class="control-label">Fee Setting Harga PPOB</label>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="setting_price_commission" name="setting_price_commission" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_commission']; ?>">

									<input type="hidden" class="form-control" id="setting_price_commission_old" name="setting_price_commission_old" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_commission']; ?>">	

									<label class="control-label">Komisi Setting Harga PPOB</label>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" id="setting_price_max" name="setting_price_max" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_max']; ?>" readonly>

									<input type="hidden" class="form-control" id="setting_price_max_old" name="setting_price_max_old" autocomplete="off" value="<?php echo $ppobsettingprice['setting_price_max']; ?>">	

									<label class="control-label">Maksimal Setting Harga PPOB</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" id="Save" name="Save" value="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" class="form-control" name="setting_price_id" id="setting_price_id" placeholder="id" value="<?php echo set_value('setting_price_id',$ppobsettingprice['setting_price_id']);?>"/>
<?php echo form_close(); ?>