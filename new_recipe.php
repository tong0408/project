<!DOCTYPE html>
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	$userid= $_SESSION['userID'];
?>
<html>
<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="image/logo.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" href="css/bootstrap-3.3.7.css" type="text/css">
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="css/mine.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<style>
	input[type=text]{margin:10px 0px 10px 0px; width:30%;}
	input[type=number]{margin:10px 0px 10px 0px; width:80%;}
	select{margin:10px 0px 10px 0px; width:80%; height:35px;}	
	td{height:50px; width:150px;}
</style>
</head>
<body>
<?php include("header.php"); ?>
<a href="enter_diet_platform.php"><button class="btn1 return">返回</button></a>

<div class="form1">
<div style="text-align:left;">若以下沒有，請自行新增食材&emsp;<button type="button" class="btn" id="add" onclick="SubmitForm(this.id)">新增食材</button></div>
<br>
		<form id="myform" method="POST" action="enter_recipe.php">
			<?php
			    //先至t_newrecipe搜尋UID是否存在
				$query = "SELECT count(ID) FROM `t_newrecipe` WHERE `UID`='$userid'";
				$result = $link->query($query);
				$count = $result->fetchColumn();
				//if count=0
				if($count==0){
					echo '菜名：<input type="text" name="new_dish" id="new_dish" maxlength="10" required>
					
					<table style="margin:auto;" id="append_position">
					<tr><td>食材</td><td>份量(克)</td></tr>
					<tr class="row_data">					
					<td><input list="brow" name="ingredients[]" id="idata"><datalist id="brow">';
					$query = "SELECT * FROM `ingredients` ";
					$result = $link->query($query);
					
					foreach($result as $row){
						$iID=$row["iID"];
						$Name=$row["name"];
						
						echo '<option value="'.$Name.'" id="'.$iID.'">';
					}
					echo '</datalist></td>
					<td><input type="number" step="0.1" min="0.1" max="1000.0" name="portion[]" id="new_portion" required></td></tr>
					</table>';
				}else{
					//搜尋加進去的菜名、食材
					$query = "SELECT * FROM `t_newrecipe` WHERE `UID`='$userid'";
					$result = $link->query($query);

					foreach($result as $row){
						$dishname=$row["dishName"];
					}
					echo '菜名：<input type="text" name="new_dish" id="new_dish" maxlength="10" value="'.$dishname.'" required>
					<table style="margin:auto;" id="append_position">
					<tr><td>食材</td><td>份量(克)</td></tr>';
					$query = "SELECT * FROM `t_newrecipe` WHERE `UID`='$userid'";
					$result = $link->query($query);

					foreach($result as $row){
						$ingredients=$row["ingredients"];
						$portion=$row["portion"];

						if($ingredients!=""){
							echo '<tr class="row_data"><td><input type="text" name="ingredients[]" value="'.$ingredients.'"></td>
							<td><input type="number" step="0.1" min="0.1" max="1000.0" name="portion[]" id="new_portion" value="'.$portion.'"></td></tr>';
						
						}else{
							echo '<tr class="row_data">					
							<td><input list="brow" name="ingredients[]" id="idata"><datalist id="brow">';
							$query = "SELECT * FROM `ingredients` ";
							$result = $link->query($query);
							
							foreach($result as $row){
								$iID=$row["iID"];
								$Name=$row["name"];
								
								echo '<option value="'.$Name.'" id="'.$iID.'">';
							}
							echo '</datalist></td>
							<td><input type="number" step="0.1" min="0.1" max="1000.0" name="portion[]" id="new_portion" required></td></tr>';
						}
					}
					
					echo '</table>';
				}
			?>
				<input type="button" value="+" id="add_row" class="btn" style="position:absolute; right:25%;"/><br>
				<input type="submit" class="btn" value="新增" >
		</form>	
		<div id="template" style="display:none;">
			<table>
				<tr class="row_data">
					<td><input list="brow" name="ingredients[]" id="idata"><datalist id="brow">
					<?php
						$query = "SELECT * FROM `ingredients` ";
						$result = $link->query($query);
						
						foreach($result as $row){
							$iID=$row["iID"];
							$Name=$row["name"];
							$Nid=$row["NID"];
							
							echo '<option value="'.$Name.'" id="'.$iID.'">';
						}
					?>
					</datalist></td>
					<td><input type="number" step="0.1" min="0.1" max="1000.0" name="portion[]" id="new_portion" required></td>
					<td style="width:20px;"><input type="button" value="-" id="remove_row" class="btn"/></td>
					
				</tr>
			</table>
		</div>
	</div>	
  
</body>
<script> 
	var TheSelectedValue = function() {
		var val = document.getElementById('idata').value;
		var text = $('#brow').find('option[value="' + val + '"]').attr('id');
		document.write(text);
	 }
	$('body').on('click','#add_row',function(){
        $('#template').find('.row_data').clone().appendTo($('#append_position'));
    });
	//刪除 tr
    $('body').on('click','#remove_row',function(){
        $(this).parent().parent().remove();
    });
	//傳送表單至兩個地方
	function SubmitForm(id)
	{
		if(id=="add"){
			document.forms['myform'].action='new_add_recipe.php';
			document.forms['myform'].submit();
		}else{
			document.forms['myform'].action='enter_recipe.php';
			document.forms['myform'].submit();
		}
		return true;
	}
</script> 
</html>