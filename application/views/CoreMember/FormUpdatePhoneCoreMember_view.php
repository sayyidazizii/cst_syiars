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

	var loop = 1;

	$(document).ready(function(){
		$('#member_name').textbox({
		   collapsible:false,
		   minimizable:false,
		   maximizable:false,
		   closable:false
		});
		
		$('#member_name').textbox('textbox').focus();
	});

	function toRp(number) {
		var number = number.toString(), 
		rupiah = number.split('.')[0], 
		cents = (number.split('.')[1] || '') +'00';
		rupiah = rupiah.split('').reverse().join('')
			.replace(/(\d{3}(?!$))/g, '$1,')
			.split('').reverse().join('');
		return rupiah + '.' + cents.slice(0, 2);
	}


	function function_elements_edit(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('CoreMember/function_elements_edit');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
			}
		});
	}

	function reset_edit(){
		document.location = base_url+"CoreMember/reset_edit/<?php echo $coremember['member_id']?>";
	}


	$(document).ready(function(){
		 $('#province_id').combobox({
			  onChange: function(value){
			  	var province_id   = document.getElementById("province_id").value;
			  	/*alert(province_id);*/
			    $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>CoreMember/getCoreCity/"+province_id,
	               data : {province_id: province_id},
	               success: function(data){
	               	/*alert(data);*/
	               		$('#city_id').combobox({
							url:"<?php echo base_url(); ?>CoreMember/getCoreCity/"+province_id,
							valueField:'city_id',
							textField:'city_name',
							loadData:data
						});
	               }
	            });
			  }
			});
	});

	$(document).ready(function(){
		$('#city_id').combobox({
			onChange: function(value){
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
	            $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>CoreMember/getCoreKecamatan/"+value,
	               data : {value: value},
	               success: function(data){
	               /*	alert(data);*/
					 	$('#kecamatan_id').combobox({
							url:"<?php echo base_url(); ?>CoreMember/getCoreKecamatan/"+value,
							valueField:'kecamatan_id',
							textField:'kecamatan_name'
						});
						
	              	}
	            });
  			}
  		});		
    });

    $(document).ready(function(){
		$('#city_id').combobox({
			onChange: function(value){
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
	            $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>CoreMember/getCoreKecamatan/"+value,
	               data : {value: value},
	               success: function(data){
	               /*	alert(data);*/
					 	$('#kecamatan_id').combobox({
							url:"<?php echo base_url(); ?>CoreMember/getCoreKecamatan/"+value,
							valueField:'kecamatan_id',
							textField:'kecamatan_name'
						});
						
	              	}
	            });
  			}
  		});		
    });

    $(document).ready(function(){
		$('#kecamatan_id').combobox({
			onChange: function(value){
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
	            $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>CoreMember/getCoreKelurahan/"+value,
	               data : {value: value},
	               success: function(data){
	               /*	alert(data);*/
					 	$('#kelurahan_id').combobox({
							url:"<?php echo base_url(); ?>CoreMember/getCoreKelurahan/"+value,
							valueField:'kelurahan_id',
							textField:'kelurahan_name'
						});
						
	              	}
	            });
  			}
  		});		
    });

    $(document).ready(function(){
		$('#kelurahan_id').combobox({
			onChange: function(value){
				/*alert(value);
      		var city_id   = document.getElementById("city_id").value;
      		alert(city_id);*/
            
	            $.ajax({
	               type : "POST",
	               url  : "<?php echo base_url(); ?>CoreMember/getCoreDusun/"+value,
	               data : {value: value},
	               success: function(data){
	               /*	alert(data);*/
					 	$('#dusun_id').combobox({
							url:"<?php echo base_url(); ?>CoreMember/getCoreDusun/"+value,
							valueField:'dusun_id',
							textField:'dusun_name'
						});
						
	              	}
	            });
  			}
  		});		
    });

    $(document).ready(function(){
		$('#member_special_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#member_special_savings').textbox('setValue', value);
				$('#member_special_savings_view').textbox('setValue', tampil);
				
				}else{
					loop=1;
					return;
				}
			
			}
		});

		$('#member_mandatory_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#member_mandatory_savings').textbox('setValue', value);
				$('#member_mandatory_savings_view').textbox('setValue', tampil);

				
				}else{
					loop=1;
					return;
				}
			
			}
		});

		$('#member_principal_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#member_principal_savings').textbox('setValue', value);
				$('#member_principal_savings_view').textbox('setValue', tampil);

				
				}else{
					loop=1;
					return;
				}
			
			}
		});

		$('#opening_member_special_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#opening_member_special_savings').textbox('setValue', value);
				$('#opening_member_special_savings_view').textbox('setValue', tampil);
				
				}else{
					loop=1;
					return;
				}
			
			}
		});

		$('#opening_member_mandatory_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#opening_member_mandatory_savings').textbox('setValue', value);
				$('#opening_member_mandatory_savings_view').textbox('setValue', tampil);

				
				}else{
					loop=1;
					return;
				}
			
			}
		});

		$('#opening_member_principal_savings_view').textbox({
			onChange: function(value){
				console.log(value);
				console.log(loop);
				if(loop == 0){
					loop= 1;
					return;
				}
				if(loop ==1){
					loop =0;
					var tampil = toRp(value);
				$('#opening_member_principal_savings').textbox('setValue', value);
				$('#opening_member_principal_savings_view').textbox('setValue', tampil);

				
				}else{
					loop=1;
					return;
				}
			
			}
		});
	});
</script>
<?php echo form_open('CoreMember/processCreatePasswordCoreMember',array('id' => 'myform', 'class' => 'horizontal-form')); 
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
			<a href="<?php echo base_url();?>CoreMember">
				Daftar Anggota
			</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<a href="<?php echo base_url();?>CoreMember/createPasswordCoreMember/"<?php echo $coremember['member_id'] ?>>
				Buat Password
			</a>
		</li>
	</ul>
</div>
		<!-- END PAGE TITLE & BREADCRUMB-->
<div class="row">
	<div class="col-md-12">
		<div class="portlet"> 
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						Form Buat Password
					</div>
					<div class="actions">
						<a href="<?php echo base_url();?>CoreMember" class="btn btn-default btn-sm">
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
							print_r($coremember);
						?>

						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. HP <span class="required" style="color:red;">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_phone" id="member_phone" autocomplete="off" value="" style="width: 100%"/>
											<input type="hidden" name="member_no" id="member_no" autocomplete="off" value="<?php echo $coremember['member_no']?>" style="width: 100%"/>
											<input type="hidden" name="member_name" id="member_name" autocomplete="off" value="<?php echo $coremember['member_name']?>" style="width: 100%"/>
											<input type="hidden" name="member_id" id="member_id" autocomplete="off" value="<?php echo $coremember['member_id']?>" style="width: 100%"/>
											<input type="hidden" name="branch_id" id="branch_id" autocomplete="off" value="<?php echo $coremember['branch_id']?>" style="width: 100%"/>
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%" align="right">
											<button type="button" class="btn red" onClick="reset_edit();"><i class="fa fa-times"></i> Batal</button>
											<button type="submit" class="btn green-jungle"><i class="fa fa-check"></i> Simpan</button>
										</td>
									</tr>
								</table>
							</div>
							
								<input type="hidden" class="form-control" name="member_id" id="member_id" placeholder="id" value="<?php echo set_value('member_id',$coremember['member_id']);?>"/>

						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo form_close(); ?>