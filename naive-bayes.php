<?php
$title = 'nb';
$jmldata = $_REQUEST['jmldata']; 
$jmlattr = $_REQUEST['jmlattr']; 
$attrib = $_REQUEST['attrib']; 
$class = $_REQUEST['class']; 
$cari = $_REQUEST['cari']; 
?>
<html>
<head>
<title>Tugas Statistika | Naive Bayes</title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
<link href="css/style.css" rel="stylesheet" />
<script src="highcharts.js"></script>
<script src="jquery/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<?php 
include "menu.php";
?>
<div class="container">
	<form class="form-inline" action="naive-bayes.php" method="POST" autocomplete="off" >
		<div class="row">
			<div class="col-md-12">
				  <div class="form-group">
					<label for="jmldata">Jumlah Attribute:</label>
					<input type="text" class="form-control input-sm" name="jmlattr" value="<?php echo $jmlattr;?>" placeholder="Jumlah Attribute ">
				  </div>
				  &nbsp;&nbsp;&nbsp;&nbsp;
				  <div class="form-group">
					<label for="jmldata">Jumlah Data:</label>
					<input type="text" class="form-control input-sm" name="jmldata" value="<?php echo $jmldata;?>" placeholder="Jumlah Data ">
				  </div>
				  <button type="submit" class="btn btn-success btn-sm">Submit</button>
				  <hr>
			</div>
		</div>
		<?php 
		if(!empty($jmldata) && !empty($jmlattr)){
		for($i=1;$i<=$jmldata;$i++){
		?>
		<div class="row">
			<div class="col-md-12"  style="overflow:auto;white-space:nowrap;padding-bottom:10px;">
			  <?php 
			  for($j=1;$j<=$jmlattr;$j++){
			  ?>
			  <div class="form-group">
				<?php if($i==1){?><label for="jmldata">Attribute <?php echo $j; ?></label><br><?php }?>
				<input type="text" class="form-control input-sm" name="attrib[<?php echo $i; ?>][<?php echo $j; ?>]" size="7" value="<?php echo $attrib[$i][$j];?>">
			  </div>
			  <?php }?>
			  <div class="form-group">
				<?php if($i==1){?><label for="jmldata">Class</label><br><?php }?>
				<input type="text" class="form-control input-sm" name="class[<?php echo $i; ?>]" size="7" value="<?php echo $class[$i];?>">
			  </div>
			</div>
		</div>
		<?php }?>
			<?php
			  for($j=1;$j<=$jmlattr;$j++){
			  ?>
			  <div class="form-group">
				<input type="text" class="form-control input-sm" name="cari[<?php echo $j; ?>]" size="7" value="<?php echo $cari[$j];?>">
			  </div>
			  <?php }?>
			  <hr>
			 <button type="submit" class="btn btn-success btn-sm">Submit</button>
		<?php }?>
	</form>	
	<?Php 
	if(!empty($attrib)){
		$jenisclass = jmlPerClass($class);
		$kelompokattribute = kelompokAttribute($attrib, $jmlattr, $class, $jenisclass);
		$jmlperattribue = jmlPerAttribue($kelompokattribute);
		//var_dump($jenisclass);
	?>
	<div class="row">
		<div class="col-md-12">
			<h3>Hasil</h3>
			<div class="table table-responsive">
				<table class="table table-bordered">
					<?php
					foreach($jmlperattribue as $keyclass => $valueclass){
						$dataclass[$keyclass] = $jenisclass[$keyclass]/$jmldata;
						echo '<tr>
								<td colspan="3">'.$keyclass.' = '.$jenisclass[$keyclass].' / '.$jmldata.'= '.$dataclass[$keyclass].'</td>
							  </tr>';
						foreach($valueclass as $keyattrib => $valueattrib){
							echo '<tr>
									<td  width="100px" rowspan="'.(count($valueattrib)+1).'">Attribute '.$keyattrib.'</td>
								  </tr>';
							foreach($valueattrib as $key => $value){
								$data[$keyclass][$key] = $value/$jenisclass[$keyclass];
							echo '<tr>
									<td width="100px">'.$key .' </td><td>'.$value.' / '.$jenisclass[$keyclass].' = '.$data[$keyclass][$key].'</td>
								  </tr>';
							}
						}
					}
					?>
					<tr>
						<td colspan="3">Tentukan :
							<?php 
							foreach($cari as $valuecari ){
								echo $valuecari." ";
							}
							echo " Masuk pada Class?";
							?>
						</td>
					</tr>
					<?Php
					foreach($dataclass as $keyclass=> $valueclass){
					?>
						<tr>
							<td><?php echo $keyclass; ?></td>
							<td colspan="2">
							<?php 
							$total[$keyclass]=1;
							foreach($cari as $valuecari ){
								echo $data[$keyclass][$valuecari] +$valuecari." * ";
								$total[$keyclass] = $total[$keyclass] * $data[$keyclass][$valuecari];
							}
							$total[$keyclass] = $total[$keyclass] * $valueclass;
							echo $valueclass." = ".$total[$keyclass];
							?>
							</td>
						</tr>
					<?php 
					}
					?>
					<tr>
						<td colspan="4">Hasilnya adalah <?php echo hasil($total);?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
	</body>
</html>
<?php
function jmlPerClass($class){
	$jmlPerClass = array_count_values($class);
	return $jmlPerClass;
}
function kelompokAttribute($attrib, $jmlattr, $class , $jenisclass){
	foreach($jenisclass as $key => $value){
		for($i=1;$i<=$jmlattr;$i++){
			for($j=1;$j<=count($attrib);$j++){
				if($class[$j]==$key){
					$data[$class[$j]][$i][] = $attrib[$j][$i];  
				}
			}
		}
	}	
	return $data;
}
function jmlPerAttribue($kelompokAttribute){
	
	foreach($kelompokAttribute as $key=> $class){
		foreach($class as $key2 => $listattrib){
			$return[$key][$key2] = array_count_values($listattrib);
		}
	}
	return $return;
}

function hasil($total){
	$classhasil = 0;
	$hasil = 0;
	foreach($total as $key => $value){
		if($hasil<=$value){
			$classhasil = $key;
			$hasil = $value;
		}
	}
	return "<b>".$classhasil."</b> dengan Nilai : $hasil";
}
?>