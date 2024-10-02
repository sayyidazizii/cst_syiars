<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}


</style>
<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		$("#income_name").textbox('setValue','');
		$("#income_percentage").textbox('setValue','');
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var income_name 		= $("#income_name").val();
			var income_percentage 	= $("#income_percentage").val();
			var income_group 		= $("#income_group").val();
			var account_id 			= $("#account_id").val();
			
			if(income_name == ''){
				alert("Nama masih kosong");
				return false;
			} else if(income_percentage == ''){
				alert("Persen masih kosong");
				return false;
			}else if(income_group == ''){
				alert("Golongan Perkiraan masih kosong");
				return false;
			}else if(account_id == ''){
				alert("No. Perkiraan masih kosong");
				return false;
			} else {
				return true;
			}
		});
    });
</script>
<?php echo form_open('PreferencePPOB/processEditPreferencePPOB',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			Setting
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<?php
// print_r($data);
// print_r($member);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Setting
					</div>
				</div>
				<div class="portlet-body">
					<div class="form-body">
						<div class="row">	
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">By Admin mBayar</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="ppob_mbayar_admin" id="ppob_mbayar_admin" style="width:100%" value="<?php echo $preferenceppob['ppob_mbayar_admin'];?>"/>
											<input type="text" class="hidden" name="id_preference_ppob" id="id_preference_ppob" value="<?php echo $preferenceppob['id'];?>"/>
										</td>
									</tr>
									<tr>
										<td width="35%">COA By Admin mBayar</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php echo form_dropdown('ppob_account_income_mbayar', $acctaccount,set_value('ppob_account_income_mbayar',$preferenceppob['ppob_account_income_mbayar']),'id="ppob_account_income_mbayar" class="easyui-combobox"');?>
										</td>
									</tr>
									<tr>
										<td width="35%">COA By Dana PPOB</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php echo form_dropdown('ppob_account_down_payment', $acctaccount,set_value('ppob_account_down_payment',$preferenceppob['ppob_account_down_payment']),'id="ppob_account_down_payment" class="easyui-combobox"');?>
										</td>
									</tr>
									<tr>
										<td width="35%">COA By Pendapatan PPOB</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php echo form_dropdown('ppob_account_income', $acctaccount,set_value('ppob_account_income',$preferenceppob['ppob_account_income']),'id="ppob_account_income" class="easyui-combobox"');?>
										</td>
									</tr>
									<tr>
										<td width="35%">COA By Biaya Server PPOB</td>
										<td width="5%">:</td>
										<td width="60%">
											<?php echo form_dropdown('ppob_account_cost', $acctaccount,set_value('ppob_account_cost',$preferenceppob['ppob_account_cost']),'id="ppob_account_cost" class="easyui-combobox"');?>
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="ulang();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
										</td>
									</tr>
									
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
								</table>
							</div>					
						</div>						
					</div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
