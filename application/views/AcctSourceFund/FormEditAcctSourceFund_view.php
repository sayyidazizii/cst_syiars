<script>
	base_url = '<?php echo base_url();?>';
	function ulang(){
		document.getElementById("source_fund_name").value 				= "<?php echo $acctsourcefund['source_fund_name'] ?>";
		document.getElementById("source_fund_code").value 				= "<?php echo $acctsourcefund['source_fund_code'] ?>";
	}

	$(document).ready(function(){
        $("#Save").click(function(){
			var source_fund_code = $("#source_fund_code").val();
			
			if(source_fund_code == ''){
				alert('Kode Sumber Dana masih kosong');
				return false;
			} else if(source_fund_name == ''){
				alert('Nama Sumber Dana masih kosong');
				return false;
			} else {
				return true;
			}	
		});
    });
</script>
<?php echo form_open('AcctSourceFund/processEditAcctSourceFund',array('id' => 'myform', 'class' => 'horizontal-form')); ?>

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
			<a href="<?php echo base_url();?>AcctSourceFund">
				Daftar Sumber Dana
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>AcctSourceFund/editAcctSourceFund/"<?php $this->uri->segment(3); ?>>
				Edit Sumber Dana 
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<h3 class="page-title">
	Form Edit Sumber Dana 
</h3>
<?php
	echo $this->session->userdata('message');
	$this->session->unset_userdata('message');
?>
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Edit
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>AcctSourceFund" class="btn btn-default btn-sm">
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
									<input type="text" class="form-control" name="source_fund_code" id="source_fund_code" autocomplete="off" value="<?php echo set_value('source_fund_code',$acctsourcefund['source_fund_code']);?>"/>
									<label class="control-label">Kode Sumber Dana<span class="required">*</span></label>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group form-md-line-input">
									<input type="text" class="form-control" name="source_fund_name" id="source_fund_name" autocomplete="off" value="<?php echo set_value('source_fund_name',$acctsourcefund['source_fund_name']);?>"/>
									<label class="control-label">Nama Sumber Dana<span class="required">*</span></label>
								</div>
							</div>
						</div>

						

						<div class="row">
							<div class="col-md-12" style='text-align:right'>
								<button type="reset" name="Reset" value="Reset" class="btn btn-danger" onClick="ulang();"><i class="fa fa-times"> Batal</i></button>
								<button type="submit" name="Save" value="Save" class="btn green-jungle" title="Simpan Data"><i class="fa fa-check"> Simpan</i></button>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" class="form-control" name="source_fund_id" id="source_fund_id" placeholder="id" value="<?php echo set_value('source_fund_id',$acctsourcefund['source_fund_id']);?>"/>
<?php echo form_close(); ?>