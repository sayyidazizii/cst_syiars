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

	.flexigrid div.pDiv input {
		vertical-align:middle !important;
	}
	
	.flexigrid div.pDiv div.pDiv2 {
		margin-bottom: 10px !important;
	}
	

</style>
	
<script>
	base_url = '<?php echo base_url();?>';
	function reset_search(){
		document.location = base_url+"mainpage/reset_search";
	}

	function function_elements_add(name, value){
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('mainpage/function_elements_add');?>",
				data : {'name' : name, 'value' : value},
				success: function(msg){
						// alert(name);
			}
		});
	}
	
	function function_state_add(value){
		// alert(value);
		$.ajax({
				type: "POST",
				url : "<?php echo site_url('mainpage/function_state_add');?>",
				data : {'value' : value},
				success: function(msg){
			}
		});
	}

	$(document).ready(function(){
        $("#client_category_id").change(function(){
            var client_category_id = $("#client_category_id").val();
            $.ajax({
               type : "POST",
               url  : "<?php echo base_url(); ?>mainpage/getCoreClient",
               data : {client_category_id: client_category_id},
               success: function(data){
                   $("#client_id").html(data);
               }
            });
        });
    });

</script>
<?php
	$data=$this->session->userdata('filter-mainpage');
	if(!is_array($data)){
		$data['start_date']				= date('d-m-Y');
		$data['end_date']				= date('d-m-Y');
		$data['client_category_id']		= '';
		$data['client_id']				= '';
	}
?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Menu Utama
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body form">
					<div class = "row">
						<div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle done uppercase"> ANGGOTA
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."CoreMember"; ?>">Data Anggota</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."CoreMember/updateCoreMemberStatus"; ?>">Update Anggota Luar Biasa</a>
                                                    </h3>
                                                </div>
                                            </li>                                                    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle uppercase"> Simpanan
                                            
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctSavingsAccount"?>">Simpanan Biasa</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctDepositoAccount"?>">Simpanan Berjangka</a>
                                                    </h3>
                                                </div>
                                            </li>                                                    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle done uppercase"> Lending

                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctCreditAccount/addform"?>">Rekening Baru</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctCreditAccount/detailAcctCreditsAccount"?>">Histori Angsuran Pinjaman</a>
                                                    </h3>
                                                </div>
                                            </li>                                                    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                   	</div>
                   	<BR>
                   	<BR>
                   	<div class = "row">
                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle uppercase"> Mutasi Simpanan Biasa
                                            
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctSavingsCashMutation"?>">Mutasi Tunai</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctSavingsTransferMutation"?>">Mutasi Antar Rekening</a>
                                                    </h3>
                                                </div>
                                            </li>                                                    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle done uppercase"> Mutasi Simpanan Berjangka

                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctDepositoAccount/AcctDepositoAccountDueDate"?>">Perpanjangan</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctDepositoAccount/addNewAcctDepositoAccount"?>">Deposito Baru</a>
                                                    </h3>
                                                </div>
                                            </li>                                                    
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1 group">
                                    <a class="list-toggle-container">
                                        <div class="list-toggle uppercase"> Angsuran
                                            
                                        </div>
                                    </a>
                                    <div class="panel-collapse collapse in" id="completed-simple">
                                        <ul>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctCashPayments/addAcctCashPayment"?>">Angsuran Tunai</a>
                                                    </h3>
                                                </div>
                                            </li>
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="list-item-content">
                                                    <h3 class="uppercase">
                                                        <a href="<?php echo base_url()."AcctCashPayments/addCashlessPayment"?>">Angsuran Non Tunai</a>
                                                    </h3>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
							<?php
								/*$auth 				= $this->session->userdata('auth');
								$menu = $this->MainPage_model->getParentMenu($auth['user_group_level']);
									print_r($menu); 

									foreach($menu as $key=>$val){
										$datamenu = $this->MainPage_model->getDataParentmenu($val['detect']);
										$class 		= $this->uri->segment(1);
										if ($class==''){
											$class='MainPage';
										}
										$active		= $this->MainPage_model->getActive($class);
										$compare 	= $datamenu['id_menu'];

										if($active == $compare){
											$stat = 'active';
										} else{
											$stat='';
										}

										if($datamenu['id_menu'] == '1'){
											echo'
											<li>
												<a href="'.base_url().$datamenu['id'].'">
													'.$datamenu['text'].'
												</a>
											</li>
										';
										}else{
											$class 		= $this->uri->segment(1);

											if ($class == ''){
												$class = 'MainPage';
											}
											$active		= $this->MainPage_model->getActive($class);
											$compare 	= $datamenu['id_menu'];

											if($active == $compare){
												$stat = 'active';
											} else {
												$stat='';
											}

											echo'
												<li>
													<a href="">
														<i class="fa '.$datamenu['image'].'"></i>
															'.$datamenu['text'].'
													</a>
													<ul >
											';
											$datasubmenu = $this->MainPage_model->getParentSubMenu2($auth['user_group_level'],$val['detect']);

											foreach($datasubmenu as $key2=>$val2){
												$idmenucari 	= substr($val2['id_menu'],0,2);
												$countsubmenu 	= count($this->MainPage_model->getSubMenu2($idmenucari));

												if($countsubmenu > 1){
													$submenuopen 	= $this->MainPage_model->getDataParentmenu($idmenucari);
													$class2 		= $this->uri->segment(1);

													if ($class == ''){
														$class = 'MainPage';
													}

													$active2		= $this->MainPage_model->getActive2($class);
													$compare2 		= $submenuopen['id_menu'];

													if($active2==$compare2){
														$stat2 = 'active';
													} else {
														$stat2='';
													}
													// echo'
													// <li class="dropdown-submenu '.$stat2.'">
															// <a href="'.base_url().$submenuopen['id'].'">
															// '.$submenuopen['text'].'
															// </a>
															// <ul class="dropdown-menu">
														// ';
													echo'
														<li>
															<a data-toggle="dropdown-submenu" data-close-others="true" href="#">
																<!-- <i class="fa '.$submenuopen['image'].'"></i> -->
																	'.$submenuopen['text'].'
															</a>
															<ul>
														';
															
														$datasubmenu2 = $this->MainPage_model->getParentSubMenu3($auth['user_group_level'],$submenuopen['id_menu']);	
														// print_r($datasubmenu2); exit;
														foreach($datasubmenu2 as $key3=>$val3){
															$idmenucari2 	= substr($val3['id_menu'],0,3);
															$countsubmenu2	= count($this->MainPage_model->getSubMenu2($idmenucari2));

															if($countsubmenu2 > 1){
																$submenuopen2 	= $this->MainPage_model->getDataParentmenu($idmenucari2);
																$class3 		= $this->uri->segment(1);

																if($class3 == ''){
																	$class2 = 'MainPage';
																}
																$active3		= $this->MainPage_model->getActive3($class);
																$compare3 		= $submenuopen2['id_menu'];
																if($active3==$compare3){$stat3 = 'active';}else{$stat3='';}
																// echo'
																// <li class="dropdown-submenu '.$stat3.'">
																	// <a href="'.base_url().$submenuopen2['id'].'">
																	// '.$submenuopen2['text'].'
																	// </a>
																	// <ul class="dropdown-menu">	
																	// ';
																	echo'
																<li>
																		<a data-toggle="dropdown-submenu" data-close-others="true" href="#">
																			<!--<i class="fa '.$submenuopen2['image'].'"></i> -->
																				'.$submenuopen2['text'].'
																			
																	
																		</a>
																		<ul>
																	';
																$datasubmenu3= $this->MainPage_model->getParentSubMenu($auth['user_group_level'],$submenuopen2['id_menu']);	
																	foreach($datasubmenu3 as $key4=>$val4){
																			echo'
																			<li >
																				<a href="'.base_url().$val4['id'].'">
																				'.$val4['text'].'
																				</a>
																				</li>
																			';
																			}
																			echo'	
																	</ul>	
																</li>
																';
															}
															else{
															$submenuopen3=$this->MainPage_model->getDataParentmenu($val3['id_menu']);
																echo'
																<li>
																<a href="'.base_url().$submenuopen3['id'].'">
																'.$submenuopen3['text'].'
																</a>
																</li>
																';
															}
														}
														echo'	
														</ul>
													</li>
													';
												}else{
													$submenuopen2=$this->MainPage_model->getDataParentmenu($val2['id_menu']);
														$judul=$submenuopen2['text'];
														echo'
															<li >
																<a href="'.base_url().$submenuopen2['id'].'">
																<!-- <i class="fa '.$submenuopen2['image'].'"></i> -->
																	'.$judul.'
																</a>
															</li>
														';
												}
											}
											echo'	
												</ul>
												</li>
											';
										}
									}*/
								?>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
