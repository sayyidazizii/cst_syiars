<script>
	base_url = '<?php echo base_url();?>';
	mappia = "	<?php 
					$site_url = 'PpobTopup/addPpobTopup';
					echo site_url($site_url); 
				?>";

	function reset_add(){
		document.location= base_url+"PpobTopup/reset_add";
	}

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('PpobTopup/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}
	
	function function_state_add(value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('PpobTopup/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}

    function convertToRupiah(angka){
		var rupiah = '';
		var angkarev = angka.toString().split('').reverse().join('');
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+',';
		return rupiah.split('',rupiah.length-1).reverse().join('');
	}

	$(document).ready(function(value, no){
        $("#ppob_topup_amount_view").change(function(){

			ppob_topup_amount 		= document.getElementById('ppob_topup_amount_view').value;

			name = "ppob_topup_amount";

			function_elements_add(name,ppob_topup_amount);

			document.getElementById("ppob_topup_amount").value 		= ppob_topup_amount;
			document.getElementById("ppob_topup_amount_view").value 	= toRp(ppob_topup_amount);

		});
    });

	
	$(document).ready(function(){
        $("#branch_id").change(function(){
            var branch_id = $("#branch_id").val();
				console.log("branch_id", branch_id);
				$.post(base_url + 'PpobTopup/getTopupAmountBranch',
				{branch_id: branch_id},
				function(data){
					console.log("data", data);
					$("#topup_branch_balance").val(data.topup_branch_balance);
					$("#topup_branch_balance_view").val(toRp(data.topup_branch_balance));
				},
						'json'
				);

            });
        });
</script>

<!-- <style>

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

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}<div class="portlet"> 
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	
</style> -->

<?php 
		echo form_open('PpobTopup/processAddPpobTopup',array('id' => 'myform', 'class' => 'horizontal-form')); 


		$auth 	= $this->session->userdata('auth');
		$sesi 	= $this->session->userdata('unique');
		$data 	= $this->session->userdata('addPpobTopup-'.$sesi['unique']);
		$token 	= $this->session->userdata('ppobtopuptoken-'.$sesi['unique']);

		if(empty($data['branch_id'])){
			$data['branch_id'] = 0;
		}

		if(empty($data['account_id'])){
			$data['account_id'] = 0;
		}

		if(empty($data['ppob_topup_amount'])){
			$data['ppob_topup_amount'] = 0;
		}

		if(empty($data['ppob_topup_remark'])){
			$data['ppob_topup_remark'] = 0;
		}



	?>
	
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
	<div class = "page-bar">
		<ul class="page-breadcrumb">
			<li>
				<a href="<?php echo base_url();?>">
					Beranda
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="<?php echo base_url();?>PpobTopup">
					Daftar Top Up PPOB
				</a>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<a href="<?php echo base_url();?>PpobTopup/addPpobTopup">
					Tambah Top Up PPOB
				</a>
			</li>
		</ul>
	</div>
	<h3 class="page-title">
	Form Tambah Top Up PPOB
	</h3>
	<!-- END PAGE TITLE & BREADCRUMB-->	

<div class="row">
	<div class="col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Top Up PPOB
				</div>
				<div class="actions">
					<a href="<?php echo base_url();?>PpobTopup" class="btn btn-default btn-sm">
						<i class="fa fa-angle-left"></i>
						<span class="hidden-480">
							Kembali
						</span>
					</a>
				</div>
			</div>

			<div class="portlet-body form">
				<div class="form-body">
				<?php
					echo $this->session->userdata('message');
					$this->session->unset_userdata('message');
				?>
					<div class = "row">
						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								
								<input type="text" class="form-control" name="ppob_topup_date" id="ppob_topup_date" value="<?php echo date('d-m-Y');?>" readonly>

								<label class="control-label">Tanggal Top Up 
									<span class="required">
									*
									</span>
								</label>
								
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="ppob_topup_balance_view" id="ppob_topup_balance_view" value="<?php echo nominal($ppob_company_balance); ?>" readonly>

								<input type="text" class="hidden" name="ppob_topup_balance" id="ppob_topup_balance" value="<?php echo $ppob_company_balance; ?>" >

								<label class="control-label">Sisa Saldo</label>
							</div>
						</div>
					</div>

					<div class ="row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php 
									echo form_dropdown('branch_id', $corebranch,set_value('branch_id', $data['branch_id']),'id="branch_id" class="form-control select2me"');
								?>

								<label class="control-label">Cabang
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="topup_branch_balance_view" id="topup_branch_balance_view" value="0" readonly>

								<input type="text" class="hidden" name="topup_branch_balance" id="topup_branch_balance" value="0" >

								<label class="control-label">Sisa Saldo</label>
							</div>
						</div>
					</div>

					<div class = "row">
						<div class="col-md-6">
							<div class="form-group form-md-line-input">
								<?php 
									echo form_dropdown('account_id', $acctaccount,set_value('account_id', $data['account_id']),'id="account_id" class="form-control select2me"');
								?>

								<label class="control-label">Kas / Bank
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-6">
							<div class="form-group form-md-line-input">
								<input type="text" class="form-control" name="ppob_topup_amount_view" id="ppob_topup_amount_view" value="<?php echo nominal($data['ppob_topup_amount']); ?>" >

								<input type="text" class="hidden" name="ppob_topup_amount" id="ppob_topup_amount" value="<?php echo $data['ppob_topup_amount']; ?>" >

								<label class="control-label">Jumlah Top Up
									<span class="required">
									*
									</span>
								</label>
							</div>
						</div>

						<div class = "col-md-12">
							<div class="form-group form-md-line-input">
								<textarea class="form-control" name="ppob_topup_remark" id="ppob_topup_remark"></textarea>

								<label class="control-label">Keterangan</label>
							</div>
						</div>
					</div>

					<input type="text" class="hidden" name="ppob_topup_token" id="ppob_topup_token" value="<?php echo $token; ?>" readonly>


					<div class="row">
						<div class="form-actions right">
							<button type="submit" name="Save" value="Save" id="Save" class="btn btn-md green-jungle" title="Simpan Data" ><i class="fa fa-check"> Simpan</i></button>
						</div>
					</div>	

				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>


