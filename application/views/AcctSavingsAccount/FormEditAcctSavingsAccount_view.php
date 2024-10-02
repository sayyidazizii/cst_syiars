<style>
	th, td {
	  padding: 3px;
	  font-size: 13px;
	}
	input:focus { 
	  background-color: 42f483;
	}
	.custom{

		margin: 0px; padding-top: 0px; padding-bottom: 0px; height: 50px; line-height: 50px; width: 50px;

	}
	.textbox .textbox-text{
		font-size: 13px;


	}


</style>
<script>
	base_url = '<?php echo base_url();?>';
	
</script>
<?php echo form_open('AcctSavingsAccount/processEditAcctSavingsAccount',array('id' => 'myform', 'class' => 'horizontal-form')); ?>


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
			<a href="<?php echo base_url();?>AcctSavingsAccount">
				Daftar Rekening Simpanan
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctSavingsAccount/editAcctSavingsAccount/<?php echo $this->uri->segment(3); ?>">
				Edit Rekening Simpanan 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<?php
// print_r($auth);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit Rekening Simpanan
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctSavingsAccount" class="btn btn-default btn-sm">
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
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" value="<?php echo $acctsavingsaccount['member_no'];?>" style="width: 60%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_no" id="member_no" value="<?php echo $acctsavingsaccount['member_name'];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Lahir</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-datebox date-picker" name="member_date_of_birth" id="member_date_of_birth" value="<?php echo $acctsavingsaccount['member_date_of_birth'];?>" style="width: 70%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_gender" id="member_gender" value="<?php echo $membergender[$acctsavingsaccount['member_gender']];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled','value'=>$acctsavingsaccount['member_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="city_name" id="city_name" value="<?php echo $acctsavingsaccount['city_name'];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="kecamatan_name" id="kecamatan_name" value="<?php echo $acctsavingsaccount['kecamatan_name'];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Pekerjaan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_job" id="member_job" value="<?php echo $acctsavingsaccount['member_job'];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">No. Telp</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_phone" id="member_phone" value="<?php echo $acctsavingsaccount['member_phone'];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity" id="member_identity" value="<?php echo $memberidentity[$acctsavingsaccount['member_identity']];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" value="<?php echo $acctsavingsaccount['member_identity_no'];?>" style="width: 100%" disabled="disabled"/></td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Jenis Simpanan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox"  id="savings_account_date" value="<?php echo $acctsavingsaccount['savings_name']; ?>" disabled="disabled" style="width: 70%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Buka<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox"  id="savings_account_date" value="<?php echo tgltoview($acctsavingsaccount['savings_account_date']); ?>" disabled="disabled" style="width: 70%" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">No. Rekening<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_no" id="savings_account_no" value="<?php echo $acctsavingsaccount['savings_account_no']?>" autocomplete="off" style="width: 100%" autofocus/>
											<input type="hidden" class="easyui-textbox" name="savings_account_id" id="savings_account_id" value="<?php echo $acctsavingsaccount['savings_account_id']?>" autocomplete="off" /></td>
									</tr>
									<tr>
										<td width="35%">Setoran (Rp)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_first_deposit_amount_view" id="savings_account_first_deposit_amount_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($acctsavingsaccount['savings_account_first_deposit_amount'], 2);?>" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%">Biaya Adm (Rp)<span class="required">*</span></td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" name="savings_account_adm_amount_view" id="savings_account_adm_amount_view" autocomplete="off" style="width: 100%" value="<?php echo number_format($acctsavingsaccount['savings_account_adm_amount'], 2);?>" disabled="disabled"/></td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_data();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" name="Save" id="Save" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
										</td>
									</tr>
								</table>
							</div>
						</div>




						<!-- <h3> Ahli Waris </h3> -->


					</div>
				</div>
			 </div>
		</div>
	</div>
</div>


<?php echo form_close(); ?>
