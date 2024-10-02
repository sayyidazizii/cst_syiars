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
<?php echo form_open('CoreMember/processEditCoreMember',array('id' => 'myform', 'class' => 'horizontal-form')); 
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
			<a href="<?php echo base_url();?>CoreMember/editCoreMember/"<?php echo $coremember['member_id'] ?>>
				Edit Anggota 
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
						Form Edit
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
						?>

						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table width="100%">
									<tr>
										<td width="35%">No. Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_no" id="member_no" autocomplete="off" value="<?php echo set_value('member_no', $coremember['member_no']);?>" style="width: 60%"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><input type="text" class="easyui-textbox" name="member_name" id="member_name" autocomplete="off" value="<?php echo set_value('member_name', $coremember['member_name']);?>" style="width: 100%"/></td>
									</tr>
									<tr>
										<td width="35%">Sifat Anggota<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('member_character', $membercharacter, set_value('member_character',$coremember['member_character']),'id="member_character" class="easyui-combobox" style="width: 70%"');?></td>
									</tr>
									<!-- <tr>
										<td width="35%">Jenis Kelamin<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('member_gender', $membergender, set_value('member_gender',$coremember['member_gender']),'id="member_gender" class="easyui-combobox" style="width: 70%"');?></td>
									</tr> -->
									<tr>
										<td width="35%">Provinsi<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><?php echo form_dropdown('province_id', $coreprovince, set_value('province_id',$coremember['province_id']),'id="province_id" class="easyui-combobox" style="width: 70%"');?></td>
									</tr>
									<tr>
										<td width="35%">Kabupaten<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['province_id'])){
													$corecity = create_double($this->CoreMember_model->getCoreCity($coremember['province_id']), 'city_id', 'city_name');

													echo form_dropdown('city_id', $corecity, set_value('city_id', $coremember['city_id']), 'id="city_id" class="easyui-combobox" style="width: 70%" ');
												} else {
											?>
												<select name="city_id" id="city_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kecamatan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['city_id'])){
													$corekecamatan = create_double($this->CoreMember_model->getCoreKecamatan($coremember['city_id']), 'kecamatan_id', 'kecamatan_name');

													echo form_dropdown('kecamatan_id', $corekecamatan, set_value('kecamatan_id', $coremember['kecamatan_id']), 'id="kecamatan_id" class="easyui-combobox" style="width: 70%" ');
												} else {
											?>
												<select name="kecamatan_id" id="kecamatan_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Kelurahan<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['kecamatan_id'])){
													$corekelurahan = create_double($this->CoreMember_model->getCoreKelurahan($coremember['kecamatan_id']), 'kelurahan_id', 'kelurahan_name');

													echo form_dropdown('kelurahan_id', $corekelurahan, set_value('kelurahan_id', $coremember['kelurahan_id']), 'id="kelurahan_id" class="easyui-combobox" style="width: 70%" ');
												} else {
											?>
												<select name="kelurahan_id" id="kelurahan_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>
										<td width="35%">Dusun<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php
												if (!empty($coremember['kelurahan_id'])){
													$coredusun = create_double($this->CoreMember_model->getCoreDusun($coremember['kelurahan_id']), 'dusun_id', 'dusun_name');

													echo form_dropdown('dusun_id', $coredusun, set_value('dusun_id', $coremember['dusun_id']), 'id="dusun_id" class="easyui-combobox" style="width: 70%" ');
												} else {
											?>
												<select name="dusun_id" id="dusun_id" class="easyui-combobox" style="width: 70%">
													<option value="">--Pilih Salah Satu--</option>
												</select>
											<?php
												}
											?>
										</td>
									</tr>
									<tr>


									<tr>
										<td width="35%">Alamat<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%"><textarea rows="3" name="member_address" id="member_address" class="easyui-textarea" style="width: 100%" ><?php echo $coremember['member_address'];?></textarea></td>
									</tr>
									<tr>
										<td width="35%">Nama Ibu Kandung<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_mother" id="member_mother" autocomplete="off" style="width: 100%" value="<?php echo $coremember['member_mother'];?>" />
										</td>
									</tr>
									<tr>
										<td width="35%">Nama Ahli Waris<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_heir" id="member_heir" autocomplete="off" style="width: 100%" value="<?php echo $coremember['member_heir'];?>"/>
										</td>
									</tr>	
									<tr>
										<td width="35%">Hubungan Ahli Waris<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<?php 
												echo form_dropdown('member_family_relationship', $familyrelationship, set_value('member_family_relationship',$coremember['member_family_relationship']),'id="member_family_relationship" class="easyui-combobox" style="width:100%;"');
											?>
										</td>
									</tr>	
									<tr>
										<td width="35%">No. Identitas<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_identity_no" id="member_identity_no" autocomplete="off" style="width: 100%" value="<?php echo $coremember['member_identity_no'];?>"/>
										</td>
									</tr>
								</table>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<table>	
									<tr>
										<td width="35%">Saldo Awal S.Pokok<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="opening_member_principal_savings_view" id="opening_member_principal_savings_view" autocomplete="off" style="width: 100%" />
											<input type="hidden" class="easyui-textbox" name="opening_member_principal_savings" id="opening_member_principal_savings" autocomplete="off" />
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo Awal S.Khusus<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="opening_member_special_savings_view" id="opening_member_special_savings_view" autocomplete="off" style="width: 100%" />
											<input type="hidden" class="easyui-textbox" name="opening_member_special_savings" id="opening_member_special_savings" autocomplete="off"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Saldo Awal S.Wajib<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="opening_member_mandatory_savings_view" id="opening_member_mandatory_savings_view" autocomplete="off" style="width: 100%" />
											<input type="hidden" class="easyui-textbox" name="opening_member_mandatory_savings" id="opening_member_mandatory_savings" autocomplete="off"/>
										</td>
									</tr>
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%"></td>
									</tr>	
									<tr>
										<td width="35%"></td>
										<td width="5%"></td>
										<td width="60%"></td>
									</tr>							
									<tr>
										<td width="35%">Simpanan Pokok<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_principal_savings_view" id="member_principal_savings_view" autocomplete="off" style="width: 100%" />
											<input type="hidden" class="easyui-textbox" name="member_principal_savings" id="member_principal_savings" autocomplete="off" />
										</td>
									</tr>
									<tr>
										<td width="35%">Simpanan Khusus<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_special_savings_view" id="member_special_savings_view" autocomplete="off" style="width: 100%" />
											<input type="hidden" class="easyui-textbox" name="member_special_savings" id="member_special_savings" autocomplete="off"/>
										</td>
									</tr>
									<tr>
										<td width="35%">Simpanan Wajib<span class="required">*</span></td>
										<td width="5%"></td>
										<td width="60%">
											<input type="text" class="easyui-textbox" name="member_mandatory_savings_view" id="member_mandatory_savings_view" autocomplete="off" style="width: 100%" />
											<input type="hidden" class="easyui-textbox" name="member_mandatory_savings" id="member_mandatory_savings" autocomplete="off"/>
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