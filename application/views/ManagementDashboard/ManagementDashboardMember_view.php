
<script type="text/javascript">
	base_url = '<?php echo base_url();?>';
</script>
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

	#chartdivmember {
		width: 100%;
		height: 400px;
	}

	#chartdivtotalmember {
		width: 100%;
		height: 500px;
	}

	#chartdivtotalsavings {
		width: 100%;
		height: 500px;
	}

	#chartdivtotalsavingsaccount {
		width: 100%;
		height: 750px;
	}
</style>
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
		

<!-- <h3 class="page-title">
	Dashboard
</h3> -->

<?php 
	$auth 	= $this->session->userdata('auth');

	
	

	/* print_r("week_to_date_difference ");
	print_r($week_to_date_difference);
	print_r("<BR> ");
	print_r("<BR> ");
	print_r("week_to_date_percentage ");
	print_r($week_to_date_percentage);
	print_r("<BR> ");
	print_r("<BR> ");
	exit; */
?>

<script>

	/* chartdivmember */
	am5.ready(function() {

		var root = am5.Root.new("chartdivmember");
		root.setThemes([
			am5themes_Animated.new(root)
		]);

		var chart = root.container.children.push(am5xy.XYChart.new(root, {
			panX: false,
			panY: false,
			wheelX: "panX",
			wheelY: "zoomX",
			layout: root.verticalLayout
		}));

		var legend = chart.children.push(
			am5.Legend.new(root, {
				centerX: am5.p50,
				x: am5.p50
			})
		);

		var data = [
			<?php 
				if (!empty($data_countmember)){						
					foreach ($data_countmember as $keyCount => $valCount){ 
						$array_key = array_keys($valCount);
			?>
						{
							<?php
								foreach ($array_key as $key){
									if ($key == 'period'){
										$period = substr($valCount[$key], 7, 6);
							?>

										"<?php echo $key ?>":"<?php echo $period ?>",
							<?php
									} else {
							?>
										"<?php echo $key ?>":<?php echo $valCount[$key] ?>,
							<?php
									}
								}
							?>
						},
			<?php 
					}
				}
			?>
		]
		

		var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
			categoryField: "period",
			renderer: am5xy.AxisRendererX.new(root, {
				cellStartLocation: 0.1,
				cellEndLocation: 0.9
			}),
			tooltip: am5.Tooltip.new(root, {})
		}));

		xAxis.data.setAll(data);

		var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
			renderer: am5xy.AxisRendererY.new(root, {})
		}));

		function makeSeries(name, fieldName) {
		var series = chart.series.push(am5xy.ColumnSeries.new(root, {
			name: name,
			xAxis: xAxis,
			yAxis: yAxis,
			valueYField: fieldName,
			categoryXField: "period"
		}));

		series.columns.template.setAll({
			tooltipText: "{name}, {categoryX}:{valueY}",
			width: am5.percent(90),
			tooltipY: 0
		});

		series.data.setAll(data);

		series.appear();

		series.bullets.push(function () {
			return am5.Bullet.new(root, {
			locationY: 0,
			sprite: am5.Label.new(root, {
				text: "{valueY}",
				fill: root.interfaceColors.get("alternativeText"),
				centerY: 0,
				centerX: am5.p50,
				populateText: true
			})
			});
		});

		legend.data.push(series);
		}

		<?php 
			if (!empty($corebranch)){						
				foreach ($corebranch as $keyBranch => $valBranch){ 
					echo "makeSeries(\"".$valBranch['branch_codename']."\", \"".$valBranch['branch_codename']."\");";
				}
			}
		?>

		chart.appear(1000, 100);

	}); 

	/* chartdivtotalmember */
	am5.ready(function() {

		var root = am5.Root.new("chartdivtotalmember");

		root.setThemes([
			am5themes_Animated.new(root)
		]);

		var container = root.container.children.push(am5.Container.new(root, {
			width: am5.percent(100),
			height: am5.percent(100),
			layout: root.verticalLayout
		}));

		var series = container.children.push(am5hierarchy.Sunburst.new(root, {
			singleBranchOnly: true,
			downDepth: 10,
			initialDepth: 10,
			topDepth: 1,
			innerRadius:am5.percent(30),
			valueField: "value",
			categoryField: "name",
			childDataField: "children"
		}));

		series.data.setAll([{
			name: "root",
			children: [
				
					<?php 
						foreach ($totalmember_branch as $keyTotal => $valTotal){
							if ($valTotal['total_member'] > 0){
								$percentage = $valTotal['total_active_member'] / $valTotal['total_member'] * 100;
							} else {
								$percentage = 0;
							}
							
					?>
						{
							name: "<?php echo $valTotal['branch_codename']?>",
							children: [
								{ 
									name: "Total", 
									value: <?php echo $valTotal['total_member']?>
								},
								{ 
									name: "Aktif", 
									value: <?php echo $valTotal['total_active_member']?>
								},
								{ 
									name: "Persentase", 
									value: <?php echo number_format($percentage, 2) ?> 
								}
							]
						},
					<?php
						}
					?>
			]
		}]);

		series.selectDataItem(series.dataItems[0]);

		series.appear(1000, 100);

	});

	/* chartdivtotalsavings */
	am5.ready(function() {

		var root = am5.Root.new("chartdivtotalsavings");

		root.setThemes([am5themes_Animated.new(root)]);

		var chart = root.container.children.push(
			am5xy.XYChart.new(root, {
				panX: false,
				panY: false,
				wheelX: "panX",
				wheelY: "zoomX",
				layout: root.verticalLayout
			})
		);

		chart.set(
			"scrollbarX",
			am5.Scrollbar.new(root, {
				orientation: "horizontal"
			})
		);

		var data = [
			<?php 
				if (!empty($data_membersavings)){
					foreach ($data_membersavings as $key => $val) { ?>
			{
				savings_name				: "<?php echo $val['savings_name']; ?>",
				total_member_savings		: <?php echo $val['total_member_savings']; ?>,
				total_amount_savings		: <?php echo $val['total_amount_savings']; ?>
			},
			<?php }} ?>
		];

		
		var xAxis = chart.xAxes.push(
			am5xy.CategoryAxis.new(root, {
				categoryField: "savings_name",
				renderer: am5xy.AxisRendererX.new(root, {}),
				tooltip: am5.Tooltip.new(root, {})
			})
		);

		xAxis.data.setAll(data);

		var yAxis = chart.yAxes.push(
			am5xy.ValueAxis.new(root, {
				min: 0,
				extraMax: 0.1,
				renderer: am5xy.AxisRendererY.new(root, {})
			})
		);

		var series1 = chart.series.push(
			am5xy.ColumnSeries.new(root, {
				name: "Total Simpanan",
				xAxis: xAxis,
				yAxis: yAxis,
				valueYField: "total_amount_savings",
				categoryXField: "savings_name",
				tooltip:am5.Tooltip.new(root, {
				pointerOrientation:"horizontal",
				labelText:"{name} in {categoryX}: {valueY} {info}"
				})
			})
		);

		series1.columns.template.setAll({
			tooltipY: am5.percent(10),
			templateField: "columnSettings"
		});

		series1.data.setAll(data);

		var series2 = chart.series.push(
			am5xy.LineSeries.new(root, {
				name: "Total Anggota",
				xAxis: xAxis,
				yAxis: yAxis,
				valueYField: "total_member_savings",
				categoryXField: "savings_name",
				tooltip:am5.Tooltip.new(root, {
				pointerOrientation:"horizontal",
				labelText:"{name} in {categoryX}: {valueY} {info}"
				})    
			})
		);

		series2.strokes.template.setAll({
			strokeWidth: 3,
			templateField: "strokeSettings"
		});


		series2.data.setAll(data);

		series2.bullets.push(function () {
			return am5.Bullet.new(root, {
				sprite: am5.Circle.new(root, {
				strokeWidth: 3,
				stroke: series2.get("stroke"),
				radius: 5,
				fill: root.interfaceColors.get("background")
				})
			});
		});

		chart.set("cursor", am5xy.XYCursor.new(root, {}));


		var legend = chart.children.push(
			am5.Legend.new(root, {
				centerX: am5.p50,
				x: am5.p50
			})
		);
		legend.data.setAll(chart.series.values);

		chart.appear(1000, 100);
		series1.appear();

	});



	/* chartdivtotalsavingsaccount */
	am5.ready(function() {

		var root = am5.Root.new("chartdivtotalsavingsaccount");

		root.setThemes([
			am5themes_Animated.new(root)
		]);

		var chart = root.container.children.push(am5xy.XYChart.new(root, {
			panX: false,
			panY: false,
			wheelX: "panX",
			wheelY: "zoomX",
			layout: root.verticalLayout
		}));

		var legend = chart.children.push(am5.Legend.new(root, {
			centerX: am5.p50,
			x: am5.p50
		}));

		var data = [
			<?php 
				if (!empty($data_countaccount)){						
					foreach ($data_countaccount as $keyCount => $valCount){ 
						$array_key = array_keys($valCount);
			?>
						{
							<?php
								foreach ($array_key as $key){
									if ($key == 'period'){
										$period = substr($valCount[$key], 7, 6);
							?>

										"<?php echo $key ?>":"<?php echo $period ?>",
							<?php
									} else {
							?>
										"<?php echo $key ?>":<?php echo $valCount[$key] ?>,
							<?php
									}
								}
							?>
						},
			<?php 
					}
				}
			?>
		]

		/* var data = [{
			"year": "2021",
			"europe": 2.5,
			"namerica": 2.5,
			"asia": 2.1,
			"lamerica": 1,
			"meast": 0.8,
			"africa": 0.4
			}, {
			"year": "2022",
			"europe": 2.6,
			"namerica": 2.7,
			"asia": 2.2,
			"lamerica": 0.5,
			"meast": 0.4,
			"africa": 0.3
			}, {
			"year": "2023",
			"europe": 2.8,
			"namerica": 2.9,
			"asia": 2.4,
			"lamerica": 0.3,
			"meast": 0.9,
			"africa": 0.5
		}]; */

		var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
			categoryField: "period",
			renderer: am5xy.AxisRendererX.new(root, {
				cellStartLocation: 0.1,
				cellEndLocation: 0.9
			}),
			tooltip: am5.Tooltip.new(root, {})
		}));

		xAxis.data.setAll(data);

		var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
			min: 0,
			renderer: am5xy.AxisRendererY.new(root, {})
		}));

		function makeSeries(name, fieldName, stacked) {
			var series = chart.series.push(am5xy.ColumnSeries.new(root, {
				stacked: stacked,
				name: name,
				xAxis: xAxis,
				yAxis: yAxis,
				valueYField: fieldName,
				categoryXField: "period"
		}));

		series.columns.template.setAll({
			tooltipText: "{name}, {categoryX}:{valueY}",
			width: am5.percent(90),
			tooltipY: am5.percent(10)
		});
		series.data.setAll(data);

		series.appear();

		series.bullets.push(function () {
			return am5.Bullet.new(root, {
			locationY: 0.5,
			sprite: am5.Label.new(root, {
				text: "{valueY}",
				fill: root.interfaceColors.get("alternativeText"),
				centerY: am5.percent(50),
				centerX: am5.percent(50),
				populateText: true
			})
			});
		});

		legend.data.push(series);
		}

		<?php 
			if (!empty($acctsavings)){						
				foreach ($acctsavings as $keySavings => $valSavings){ 
					$savings_name 		= str_replace(" ", "", $valSavings['savings_name']);
					echo "makeSeries(\"".$valSavings['savings_name']."\", \"".$savings_name."\");";
				}
			}
		?>

		/* makeSeries("Europe", "europe", false);
		makeSeries("North America", "namerica", true);
		makeSeries("Asia", "asia", false);
		makeSeries("Latin America", "lamerica", true);
		makeSeries("Middle East", "meast", true);
		makeSeries("Africa", "africa", true); */

		chart.appear(1000, 100);

	});
</script>

<div class="row" id="load_content">
	<div class = "col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Data Anggota Baru 12 Bulan Terakhir
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class = "row">
						<div class = "col-md-12">
							<div id="chartdivmember"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class = "row">
	<div class = "col-md-8">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Total Anggota vs Anggota Aktif
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class = "row">
						<div class = "col-md-6">
							<div id="chartdivtotalmember"></div>
						</div>

						<div class = "col-md-6">
							<table class="table table-bordered table-advance table-hover">
								<thead>
									<tr>
										<th style='text-align  : center !important;' width="25%">Cabang</th>
										<th style='text-align  : center !important;' width="20%">Total  </th>
										<th style='text-align  : center !important;' width="20%">Aktif</th>
										<th style='text-align  : center !important;' width="20%">Tidak Aktif  </th>
										<th style='text-align  : center !important;' width="15%">Persentase  </th>
									</tr>
								</thead>
								<tbody>
									<?php
										$no = 1;
										if(empty($totalmember_branch)){
											echo "
												<tr>
													<td colspan='8' align='center'>Data Kosong</td>
												</tr>
											";
										} else {
											$total_member 			= 0;
											$total_active_member 	= 0;
											$total_passive_member 	= 0;

											foreach ($totalmember_branch as $key=>$val){		
												if ($val['total_member'] > 0){
													$percentage = $val['total_active_member'] / $val['total_member'] * 100;
												} else {
													$percentage = 0;
												}				
												echo"
													<tr>			
														<td style='text-align  : right !important;'>".$val['branch_codename']."</td>
														<td style='text-align  : right !important;'>".nominal($val['total_member'])."</td>
														<td style='text-align  : right !important;'>".nominal($val['total_active_member'])."</td>
														<td style='text-align  : right !important;'>".nominal($val['total_passive_member'])."</td>
														<td style='text-align  : right !important;'>".number_format($percentage, 2)." %</td>
													</tr>
												";

												$total_member 			+= $val['total_member'];
												$total_active_member 	+= $val['total_active_member'];
												$total_passive_member 	+= $val['total_passive_member'];
												$no++;
											} 
										}
										
										$percentage_active_member 		= $total_active_member / $total_member * 100;
										$percentage_passive_member 		= $total_passive_member / $total_member * 100;
									?>
								</tbody>
							</table>

							<table class="table table-bordered table-advance table-hover">
								<thead>
									<tr>
										<th style='text-align  : center !important;' width="33%">Total</th>
										<th style='text-align  : center !important;' width="33%">Aktif</th>
										<th style='text-align  : center !important;' width="34%">Tidak Aktif</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th style='text-align  : center !important;' width="33%"><h3><b><?php echo nominal($total_member, 2) ?></b></h3></th>
										<th style='text-align  : center !important;' width="33%"><h3><b><?php echo nominal($total_active_member, 2)?></b></h3></th>
										<th style='text-align  : center !important;' width="34%"><h3><b><?php echo nominal($total_passive_member, 2)?></b></h3></th>
									</tr>
									<tr>
										<th style='text-align  : center !important;' width="33%"></th>
										<th style='text-align  : center !important;' width="33%"><h2><b><?php echo nominal($percentage_active_member, 2)?> %<b></h2></th>
										<th style='text-align  : center !important;' width="34%"><h2><b><?php echo nominal($percentage_passive_member, 2)?> %</b></h2></th>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>							

	<div class = "col-md-4">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Simpanan Anggota
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class = "row">
						<div class = "col-md-12">
							<div id="chartdivtotalsavings"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row" id="load_content">
	<div class = "col-md-12">	
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Total Rekening Simpanan 12 Bulan Terakhir
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class = "row">
						<div class = "col-md-12">
							<div id="chartdivtotalsavingsaccount"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>