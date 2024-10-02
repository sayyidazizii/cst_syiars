<style>
	th, td {
	  padding: 3px;
	}
	input:focus { 
	  background-color: 42f483;
	}


</style>

<script>
	// $(document).ready(function(){
	// 	$('#deposito_account_extra_period').textbox({
	// 	   collapsible:false,
	// 	   minimizable:false,
	// 	   maximizable:false,
	// 	   closable:false
	// 	});

	// 	$('#deposito_account_extra_period').textbox('clear').textbox('textbox').focus();
	// });

	$(document).ready(function(){
       $('#deposito_account_extra_period').textbox({
			onChange: function(value){
			var period 	= +document.getElementById("deposito_account_extra_period").value;

			
        	var no = 1 + parseFloat(period);
        	var d = new Date(),
		        month = '' + (d.getMonth() + no),
		        day = '' + d.getDate(),
		        year = d.getFullYear();

		    if (month > 12 ){
		    	month = month - 12;
		    	year = year + 1;
		    } else {
		    	month = month;
		    	year = year;
		    }

		    if (month.toString().length < 2){

		    	month = '0' + month;
		    } 
		    if (day.toString().length < 2) {
		    	day = '0' + day;
		    }

		    var date = [day, month,  year].join('-');

		    console.log(date);
		    $('#deposito_account_extra_due_date').textbox('setValue', date);
		    // document.getElementById('deposito_account_extra_due_date').value		= [day, month,  year].join('-');
		}
        })
    });
	
</script>
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
			<a href="<?php echo base_url();?>AcctDepositoAccount">
				Daftar Rekening Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctDepositoAccount/AcctDepositoAccountDueDate">
				Perpanjangan Simpanan Berjangka
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->

<h3 class="page-title">
	Perpanjangan Simpanan Berjangka
</h3>
<?php echo form_open('AcctDepositoAccount/processAddAcctDepositoAccountExtra',array('id' => 'myform', 'class' => 'horizontal-form')); ?>
<?php
// print_r($acctdepositoaccount);
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			 <div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Detail
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctDepositoAccount" class="btn btn-default btn-sm">
							<i class="fa fa-angle-left"></i>
							<span class="hidden-480">
								Kembali
							</span>
						</a>
					</div>
				</div>
				<div class="portlet-body form">
					<div class="form-body">
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Simpka</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_no" id="deposito_account_no" value="<?php echo $acctdepositoaccount['deposito_account_no'];?>" readonly/>
											<input type="hidden" class="form-control" name="deposito_account_id" id="deposito_account_id" value="<?php echo $acctdepositoaccount['deposito_account_id'];?>" readonly/>
										</td>
									</tr>
									<tr>
										<td width="35%">No. Seri</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_serial_no" id="deposito_account_serial_no" value="<?php echo $acctdepositoaccount['deposito_account_serial_no'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Simpanan Berjangka</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_name" id="deposito_name" value="<?php echo $acctdepositoaccount['deposito_name'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_name" id="member_name" value="<?php echo $acctdepositoaccount['member_name'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Anggota</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_no" id="member_no" value="<?php echo $acctdepositoaccount['member_no'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jenis Kelamin</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_gender" id="member_gender" value="<?php echo $membergender[$acctdepositoaccount['member_gender']];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Alamat</td>
										<td width="5%">:</td>
										<td width="60%"><?php echo form_textarea(array('rows'=>'2','name'=>'member_address','class'=>'easyui-textarea','id'=>'member_address','disabled'=>'disabled', 'value'=> $acctdepositoaccount['member_address']))?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="city_name" id="city_name" value="<?php echo $this->AcctDepositoAccount_model->getCityName($acctdepositoaccount['city_id']);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Kecamatan</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="kecamatan_name" id="kecamatan_name" value="<?php echo $this->AcctDepositoAccount_model->getKecamatanName($acctdepositoaccount['kecamatan_id']);?>" readonly/></td>
									</tr>
									
									
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="identity_name" id="identity_name" value="<?php echo $memberidentity[$acctdepositoaccount['identity_id']];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">No. Identitas</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="member_identity_no" id="member_identity_no" value="<?php echo $acctdepositoaccount['member_identity_no'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu (Bln)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_period" id="deposito_account_period" value="<?php echo $acctdepositoaccount['deposito_account_period'];?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Saldo</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_amount" id="deposito_account_amount" value="<?php echo number_format($acctdepositoaccount['deposito_account_amount'], 2);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Mulai</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 70%" name="deposito_account_date" id="deposito_account_date" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_date']);?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 70%" name="deposito_account_due_date" id="deposito_account_due_date" value="<?php echo tgltoview($acctdepositoaccount['deposito_account_due_date']);?>" readonly/></td>
									</tr>
									<tr>
										<td colspan="3" align="left"><b>Perpanjangan Simpanan</b></td>
									</tr>
									<tr>
										<td width="35%">Jangka Waktu (Bln)</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 100%" name="deposito_account_extra_period" id="deposito_account_extra_period" autocomplete="off"/></td>
									</tr>
									<tr>
										<td width="35%">Tanggal Mulai</td>
										<td width="5%">:</td>
										<td width="60%"><input type="text" class="easyui-textbox" style="width: 70%" name="deposito_account_extra_date" id="deposito_account_extra_date" autocomplete="off" value="<?php echo date('d-m-Y'); ?>" readonly/></td>
									</tr>
									<tr>
										<td width="35%">Jatuh Tempo</td>
										<td width="5%">:</td>
										<td width="60%">
											<input type="text" class="easyui-textbox" style="width: 70%" name="deposito_account_extra_due_date" id="deposito_account_extra_due_date" autocomplete="off" />
										</td>
									</tr>

									<input type="hidden" class="form-control" name="member_id" id="member_id" value="<?php echo $acctdepositoaccount['member_id'];?>" readonly/>
									<input type="hidden" class="form-control" name="deposito_id" id="deposito_id" value="<?php echo $acctdepositoaccount['deposito_id'];?>" readonly/>
									<input type="hidden" class="form-control" name="deposito_account_nisbah" id="deposito_account_nisbah" value="<?php echo $acctdepositoaccount['deposito_account_nisbah'];?>" readonly/>
									<input type="hidden" class="form-control" name="deposito_account_amount" id="deposito_account_amount" value="<?php echo $acctdepositoaccount['deposito_account_amount'];?>" readonly/>
									<input type="hidden" class="form-control" name="savings_account_id" id="savings_account_id" value="<?php echo $acctdepositoaccount['savings_account_id'];?>" readonly/>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_data();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
										</td>
									</tr>
								</table>
							</div>
						</div>					
					</div>
				</div>
			 </div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>