<?php
	session_start();
	include("configure.php");

	#假如$_SESSION['userID']為空值表示沒有登入
	if ($_SESSION['userID'] == null) {
		echo "<script>alert('請先登入唷！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
	} else {
		$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
		$userID=$_SESSION['userID'];
		$query = "SELECT * FROM `user` WHERE `userID`='$userID'";
		$result = $link->query($query);
	
		#獲取現在登入者的帳號密碼
		foreach ($result as $row) {
			$userID = $row["userid"];
			$Name = $row["name"];
		}
	}
?>

<html>
<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.2/dist/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.4/plugin/weekday.min.js"></script>
<style>
	body,h1,h2,h3,h4,h5,h6 {font-family: "微軟正黑體", sans-serif}
	
	body, html {
		height: 100%;
		line-height: 1.8;
		text-align:center;
		background-color:#FFD79B;
	}
	.container1{
		width:850px; 
		height:500px; 
		background:#FFF; 
		margin:auto;
		border-radius:20px;
	}
	
	.btn{
		border-radius:10px;
		background-color: #FFF;
		border:none;
		margin:20px;
		padding:10px 30px;
	}
	
	.btn:hover{
		background-color: #FFB03B;
		cursor:pointer;
	}
	
	.btn2{
		border-radius:10px;
		background-color: #FFF;
		border:1px solid #FFB03B;
		margin:10px;
		padding:10px 20px;
	}
	
	.btn2:hover{
		background-color: #FFB03B;
		cursor:pointer;
	}
</style>

<script>
var sqlmdate=[];
var sqlwdate=[];
var sqlportion=[];
var iID_NID=[];
</script>
<?php
    //連接歷史紀錄資料表
	$count=0;
    $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
    $query = "SELECT `dishID` ,`date` FROM `history` WHERE `UID`='$userID' ORDER BY `date` DESC";
    $result = $link->query($query);
	
    foreach($result as $row){
        
		$dishID = $row['dishID'];
		$sqldate=$row['date'];
?>
		<script>
		sqlmdate.push('<?php echo substr($sqldate,6,1 );?>');
		sqlwdate.push('<?php echo substr($sqldate,0,5).substr($sqldate,6,4);?>');
		document.write(sqlwdate);
		</script>
<?php
		//取得所有需要的資料
		$query = "SELECT count(`ID`),`iID`, `portion` FROM recipe where dishID='$dishID'";
		$res = $link->query($query);
		$c = $res->fetchColumn();
		
		$count+=$c;
		
		//取得dishID使用的iID&portion
		$query = "SELECT `iID`, `portion` FROM recipe where dishID='$dishID'";
		$re = $link->query($query);
			
		foreach ($re as $r){
			$iID=$r['iID'];
			$portion=$r['portion'];
			
			//從iID取得NID和食材名稱<使用>
			$query = "SELECT NID FROM ingredients where iID='$iID'";
			$re = $link->query($query);
			foreach ($re as $r){
				$iID_NID=$r['NID'];
				
?>
				<script>
				iID_NID.push('<?php echo $iID_NID;?>');
				</script>
<?php
			}
?>
			<script>
			sqlportion.push('<?php echo $portion;?>');
			</script>
<?php
		}
		//echo $sqldate;
	}
?>

<script>
var count='<?=$count?>';

//日期自動變化
var Today = new Date();
var t1 = new Date(Today.setDate(Today.getDate())).toLocaleDateString().replaceAll("/","-");
var t2 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t3 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t4 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t5 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t6 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");
var t7 =new Date(Today.setDate(Today.getDate()-1)).toLocaleDateString().replaceAll("/","-");

var Today1 = new Date();
var m1 =new Date(Today1.setMonth(Today1.getMonth())).toLocaleDateString().replaceAll("/","-");
var m2 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m3 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m4 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m5 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m6 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");
var m7 =new Date(Today1.setMonth(Today1.getMonth()-1)).toLocaleDateString().replaceAll("/","-");

var mmdd=[];
var portion=[];
var s=0;
mmdd.push(m1.substr(5,1));
mmdd.push(m2.substr(5,1));
mmdd.push(m3.substr(5,1));
mmdd.push(m4.substr(5,1));
mmdd.push(m5.substr(5,1));
mmdd.push(m6.substr(5,1));
mmdd.push(m7.substr(5,1));
mmdd.push(t1);
mmdd.push(t2);
mmdd.push(t3);
mmdd.push(t4);
mmdd.push(t5);
mmdd.push(t6);
mmdd.push(t7);

//切換
//問題2:切換月or週&營養素
window.onload=function(){
	//buttonid
	var bmonth=document.getElementById("month");
	var bweek=document.getElementById("week");
	var b1=document.getElementById("1");
	var b2=document.getElementById("2");
	var b3=document.getElementById("3");
	var b4=document.getElementById("4");
	var b5=document.getElementById("5");
	var b6=document.getElementById("6");
	document.getElementById("month").style.backgroundColor = "#FFB03B"; 
	document.getElementById("1").style.backgroundColor = "#FFB03B"; 
	//divid-month
	var divmonth=document.getElementById("divmonth");
	var divm1=document.getElementById("divm1");
	var divm2=document.getElementById("divm2");
	var divm3=document.getElementById("divm3");
	var divm4=document.getElementById("divm4");
	var divm5=document.getElementById("divm5");
	var divm6=document.getElementById("divm6");
	//divid-week
	var divweek=document.getElementById("divweek");
	var divw1=document.getElementById("divw1");
	var divw2=document.getElementById("divw2");
	var divw3=document.getElementById("divw3");
	var divw4=document.getElementById("divw4");
	var divw5=document.getElementById("divw5");
	var divw6=document.getElementById("divw6");
	
	//monthonclick
	bmonth.onclick=function(){
		s=1;
		Show_hide(divmonth,divweek,divm1,divm2,divm3,divm4,divm5,divm6,divw1,divw2,divw3,divw4,divw5,divw6);
		document.getElementById("month").style.backgroundColor = "#FFB03B"; 
		document.getElementById("week").style.backgroundColor = "#FFF"; 
		return false;
	}
	//weekonclick
	bweek.onclick=function(){
		s=2;
		Show_hide(divweek,divmonth,divw1,divw2,divw3,divw4,divw5,divw6,divm1,divm2,divm3,divm4,divm5,divm6);
		document.getElementById("month").style.backgroundColor = "#FFF"; 
		document.getElementById("week").style.backgroundColor = "#FFB03B"; 
		return false;
	}
	b1.onclick=function(){
		Show_div(divmonth,divweek,divm1,divm2,divm3,divm4,divm5,divm6,divw1,divw2,divw3,divw4,divw5,divw6,s);
		document.getElementById("1").style.backgroundColor = "#FFB03B"; 
		document.getElementById("2").style.backgroundColor = "#FFF"; 
		document.getElementById("3").style.backgroundColor = "#FFF"; 
		document.getElementById("4").style.backgroundColor = "#FFF"; 
		document.getElementById("5").style.backgroundColor = "#FFF"; 
		document.getElementById("6").style.backgroundColor = "#FFF"; 
		return false;
	}
	b2.onclick=function(){
		Show_div(divmonth,divweek,divm2,divm1,divm3,divm4,divm5,divm6,divw2,divw1,divw3,divw4,divw5,divw6,s);
		document.getElementById("1").style.backgroundColor = "#FFF"; 
		document.getElementById("2").style.backgroundColor = "#FFB03B"; 
		document.getElementById("3").style.backgroundColor = "#FFF"; 
		document.getElementById("4").style.backgroundColor = "#FFF"; 
		document.getElementById("5").style.backgroundColor = "#FFF"; 
		document.getElementById("6").style.backgroundColor = "#FFF"; 
		return false;
	}
	b3.onclick=function(){
		Show_div(divmonth,divweek,divm3,divm2,divm1,divm4,divm5,divm6,divw3,divw2,divw1,divw4,divw5,divw6,s);
		document.getElementById("1").style.backgroundColor = "#FFF"; 
		document.getElementById("2").style.backgroundColor = "#FFF"; 
		document.getElementById("3").style.backgroundColor = "#FFB03B"; 
		document.getElementById("4").style.backgroundColor = "#FFF"; 
		document.getElementById("5").style.backgroundColor = "#FFF"; 
		document.getElementById("6").style.backgroundColor = "#FFF";
		return false;
	}
	b4.onclick=function(){
		Show_div(divmonth,divweek,divm4,divm2,divm3,divm1,divm5,divm6,divw4,divw2,divw3,divw1,divw5,divw6,s);
		document.getElementById("1").style.backgroundColor = "#FFF"; 
		document.getElementById("2").style.backgroundColor = "#FFF"; 
		document.getElementById("3").style.backgroundColor = "#FFF"; 
		document.getElementById("4").style.backgroundColor = "#FFB03B"; 
		document.getElementById("5").style.backgroundColor = "#FFF"; 
		document.getElementById("6").style.backgroundColor = "#FFF";
		return false;
	}
	b5.onclick=function(){
		Show_div(divmonth,divweek,divm5,divm2,divm3,divm4,divm1,divm6,divw5,divw2,divw3,divw4,divw1,divw6,s);
		document.getElementById("1").style.backgroundColor = "#FFF"; 
		document.getElementById("2").style.backgroundColor = "#FFF"; 
		document.getElementById("3").style.backgroundColor = "#FFF"; 
		document.getElementById("4").style.backgroundColor = "#FFF"; 
		document.getElementById("5").style.backgroundColor = "#FFB03B"; 
		document.getElementById("6").style.backgroundColor = "#FFF";
		return false;
	}
	b6.onclick=function(){
		Show_div(divmonth,divweek,divm6,divm2,divm3,divm4,divm5,divm1,divw6,divw2,divw3,divw4,divw5,divw1,s);
		document.getElementById("1").style.backgroundColor = "#FFF"; 
		document.getElementById("2").style.backgroundColor = "#FFF"; 
		document.getElementById("3").style.backgroundColor = "#FFF"; 
		document.getElementById("4").style.backgroundColor = "#FFF"; 
		document.getElementById("5").style.backgroundColor = "#FFF"; 
		document.getElementById("6").style.backgroundColor = "#FFB03B";
		return false;
	}
}
//show or hide month and week div
function Show_hide(s1,h2,showdiv,divh1,divh2,divh3,divh4,divh5,divh6,divh7,divh8,divh9,divh10,divh11){
	if(s1.style.display='block'){
		showdiv.style.display='block';
		h2.style.display='none';
		divh1.style.display='none';
		divh2.style.display='none';
		divh3.style.display='none';
		divh4.style.display='none';
		divh5.style.display='none';
		divh6.style.display='none';
		divh7.style.display='none';
		divh8.style.display='none';
		divh9.style.display='none';
		divh10.style.display='none';
		divh11.style.display='none';
	}else if (s1.style.display='none'){
		s1.style.display='block';
		showdiv.style.display='block';
		h2.style.display='none';
		divh1.style.display='none';
		divh2.style.display='none';
		divh3.style.display='none';
		divh4.style.display='none';
		divh5.style.display='none';
		divh6.style.display='none';
		divh7.style.display='none';
		divh8.style.display='none';
		divh9.style.display='none';
		divh10.style.display='none';
		divh11.style.display='none';
	}
}
//divmonth,divweek,divm6,divm2,divm3,divm4,divm5,divm1,divw6,divw2,divw3,divw4,divw5,divw1
//show button
function Show_div(bm1,bw2,showmmddiv,divm2,divm3,divm4,divm5,divm6,showwdiv,divw2,divw3,divw4,divw5,divw6,s){
	if(s==1){
		if(bm1.style.display='block'){
			bw2.style.display='none';
			showmmddiv.style.display='block';
			divm6.style.display='none';
			divm2.style.display='none';
			divm3.style.display='none';
			divm4.style.display='none';
			divm5.style.display='none';
			showwdiv.style.display='none';
			divw6.style.display='none';
			divw2.style.display='none';
			divw3.style.display='none';
			divw4.style.display='none';
			divw5.style.display='none';
		}
	}else if(s==2){
		if(bw2.style.display='block'){
			bm1.style.display='none';
			showmmddiv.style.display='none';
			divm6.style.display='none';
			divm2.style.display='none';
			divm3.style.display='none';
			divm4.style.display='none';
			divm5.style.display='none';
			showwdiv.style.display='block';
			divw6.style.display='none';
			divw2.style.display='none';
			divw3.style.display='none';
			divw4.style.display='none';
			divw5.style.display='none';
		}
	}
}
</script>

</head>
<body>
<div class="nav nav-tabs" id="nav-tab" role="tablist">

	 <!--<div style="display:inline-block;">這個語法會讓切換失效，排版定位可能要找別的方法，或是把boostrap裡面tab切換的css寫進我們自己的css-->
		<button class="btn2" type="button" id="month" >月</button>
		<button class="btn2" type="button" id="week" >週</button>
		<div class="container1">
			<button class="btn2" id="1">全榖雜糧類</button>
			<button class="btn2" id="2">豆魚蛋肉類</button>
			<button class="btn2" id="3">乳品類</button>
			<button class="btn2" id="4">蔬菜類</button>
			<button class="btn2" id="5">水果類</button>
			<button class="btn2" id="6">油脂與堅果種子類</button>
			<div id="divmonth" style="display:block;">
				<div id="divm1" style="display:block;">
					<canvas id="myChartm1" ></canvas>
					<script>
						//放這個圖表就顯示不出來
						for(var i=0;i<7;i++){
							if(mmdd[i]<10){//判斷月份
								if(mmdd[i]==sqlmdate[i]){//判斷月份相符
									for(var m=0;m<count;m++){
										if(iID_NID[m]==1){//營養素ID
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}else{
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==1){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}
						}
						
						var ctx = document.getElementById('myChartm1').getContext('2d');
						var myChartm1 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[6],mmdd[5], mmdd[4], mmdd[3], mmdd[2], mmdd[1], mmdd[0]],//改日期
								datasets: [{
									label: '營養素圖表-月',
									data: [portion[6], portion[5], portion[4], portion[3], portion[2], portion[1],portion[0]],//改數值
									backgroundColor: [
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)'
									],
									borderColor: [
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartm1.render();
					</script>
				</div>
				<div id="divm2" style="display:none;">
					<canvas id="myChartm2" ></canvas>
					<script>
						for(var i=0;i<7;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==2){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}else{
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==2){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}
						}
						var ctx = document.getElementById('myChartm2').getContext('2d');
						var myChartm2 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[6],mmdd[5], mmdd[4], mmdd[3], mmdd[2], mmdd[1], mmdd[0]],//改日期
								datasets: [{
									label: '營養素圖表-月',
									data: [portion[6], portion[5], portion[4], portion[3], portion[2], portion[1],portion[0]],//改數值
									backgroundColor: [
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)'
									],
									borderColor: [
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartm2.render();
					</script>
				</div>
				<div id="divm3" style="display:none;">
					<canvas id="myChartm3" ></canvas>
					<script>
						
						for(var i=0;i<7;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==3){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}else{
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==3){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}
						}
						var ctx = document.getElementById('myChartm3').getContext('2d');
						var myChartm3 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[6],mmdd[5], mmdd[4], mmdd[3], mmdd[2], mmdd[1], mmdd[0]],//改日期
								datasets: [{
									label: '營養素圖表-月',
									data: [portion[6], portion[5], portion[4], portion[3], portion[2], portion[1],portion[0]],//改數值
									backgroundColor: [
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)'
									],
									borderColor: [
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartm3.render();
					</script>
				</div>
				<div id="divm4" style="display:none;">
					<canvas id="myChartm4" ></canvas>
					<script>
						
						for(var i=0;i<7;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==4){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}else{
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==4){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}
						}
						var ctx = document.getElementById('myChartm4').getContext('2d');
						var myChartm4 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[6],mmdd[5], mmdd[4], mmdd[3], mmdd[2], mmdd[1], mmdd[0]],//改日期
								datasets: [{
									label: '營養素圖表-月',
									data: [portion[6], portion[5], portion[4], portion[3], portion[2], portion[1],portion[0]],//改數值
									backgroundColor: [
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)'
									],
									borderColor: [
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartm4.render();
					</script>
				</div>
				<div id="divm5" style="display:none;">
					<canvas id="myChartm5" ></canvas>
					<script>
						
						for(var i=0;i<7;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==5){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}else{
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==5){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}
						}
						var ctx = document.getElementById('myChartm5').getContext('2d');
						var myChartm5 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[6],mmdd[5], mmdd[4], mmdd[3], mmdd[2], mmdd[1], mmdd[0]],//改日期
								datasets: [{
									label: '營養素圖表-月',
									data: [portion[6], portion[5], portion[4], portion[3], portion[2], portion[1],portion[0]],//改數值
									backgroundColor: [
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)'
									],
									borderColor: [
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartm5.render();
					</script>
				</div>
				<div id="divm6" style="display:none;">
					<canvas id="myChartm6" ></canvas>
					<script>
						
						for(var i=0;i<7;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==6){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}else{
								if(mmdd[i]==sqlmdate[i]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==6){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
								}
							}
						}
						var ctx = document.getElementById('myChartm6').getContext('2d');
						var myChartm6 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[6],mmdd[5], mmdd[4], mmdd[3], mmdd[2], mmdd[1], mmdd[0]],//改日期
								datasets: [{
									label: '營養素圖表-月',
									data: [portion[6], portion[5], portion[4], portion[3], portion[2], portion[1],portion[0]],//改數值
									backgroundColor: [
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)',
										'rgba(255, 99, 132, 0.2)'
									],
									borderColor: [
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)',
										'rgba(255, 99, 132, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartm6.render();
					</script>
				</div>
			</div>
			<div id="divweek" style="display:none;">
				<div id="divw1" style="display:none;">
					<canvas id="myChartw1" ></canvas>
					<script>
						var n=0;
						for(var i=7;i<14;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==1){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
								
							}else{
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==1){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
							}
							//document.write(mmdd[i]);
							//document.write(sqlwdate[n]);
						}
						var ctx = document.getElementById('myChartw1').getContext('2d');
						var myChartw1 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[13],mmdd[12], mmdd[11], mmdd[10], mmdd[9], mmdd[8], mmdd[7]],//改日期
								datasets: [{
									label: '營養素圖表-周',
									data: [portion[13], portion[12], portion[11], portion[10], portion[9], portion[8],portion[7]],//改數值
									backgroundColor: [
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)'
									],
									borderColor: [
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartw1.render();
					</script>
				</div>
				<div id="divw2" style="display:none;">
					<canvas id="myChartw2" ></canvas>
					<script>
						var n=0;
						for(var i=7;i<14;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==2){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
								
							}else{
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==2){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
							}
						}
						var ctx = document.getElementById('myChartw2').getContext('2d');
						var myChartw2 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[13],mmdd[12], mmdd[11], mmdd[10], mmdd[9], mmdd[8], mmdd[7]],//改日期
								datasets: [{
									label: '營養素圖表-周',
									data: [portion[13], portion[12], portion[11], portion[10], portion[9], portion[8],portion[7]],//改數值
									backgroundColor: [
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)'
									],
									borderColor: [
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartw2.render();
					</script>
				</div>
				<div id="divw3" style="display:none;">
					<canvas id="myChartw3" ></canvas>
					<script>						
						var n=0;
						for(var i=7;i<14;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==3){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
								
							}else{
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==3){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
							}
						}
						var ctx = document.getElementById('myChartw3').getContext('2d');
						var myChartw3 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[13],mmdd[12], mmdd[11], mmdd[10], mmdd[9], mmdd[8], mmdd[7]],//改日期
								datasets: [{
									label: '營養素圖表-周',
									data: [portion[13], portion[12], portion[11], portion[10], portion[9], portion[8],portion[7]],//改數值
									backgroundColor: [
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)'
									],
									borderColor: [
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartw3.render();
					</script>
				</div>
				<div id="divw4" style="display:none;">
					<canvas id="myChartw4" ></canvas>
					<script>
						var n=0;
						for(var i=7;i<14;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==4){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
								
							}else{
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==4){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
							}
						}
						var ctx = document.getElementById('myChartw4').getContext('2d');
						var myChartw4 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[13],mmdd[12], mmdd[11], mmdd[10], mmdd[9], mmdd[8], mmdd[7]],//改日期
								datasets: [{
									label: '營養素圖表-周',
									data: [portion[13], portion[12], portion[11], portion[10], portion[9], portion[8],portion[7]],//改數值
									backgroundColor: [
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)'
									],
									borderColor: [
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartw4.render();
					</script>
				</div>
				<div id="divw5" style="display:none;">
					<canvas id="myChartw5" ></canvas>
					<script>
						var n=0;
						for(var i=7;i<14;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==5){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
								
							}else{
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==5){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
							}
						}
						var ctx = document.getElementById('myChartw5').getContext('2d');
						var myChartw5 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[13],mmdd[12], mmdd[11], mmdd[10], mmdd[9], mmdd[8], mmdd[7]],//改日期
								datasets: [{
									label: '營養素圖表-周',
									data: [portion[13], portion[12], portion[11], portion[10], portion[9], portion[8],portion[7]],//改數值
									backgroundColor: [
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)'
									],
									borderColor: [
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartw5.render();
					</script>
				</div>
				<div id="divw6" style="display:none;">
					<canvas id="myChartw6" ></canvas>
					<script>
						var n=0;
						for(var i=7;i<14;i++){
							if(mmdd[i].substr(5,1)<10){
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==6){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									
									n=n+1;
								}
								
							}else{
								if(mmdd[i]==sqlwdate[n]){
									for(var m=0;m<count;m++){
										if(iID_NID[m]==6){
											if(portion[i]==null){
												portion[i]=parseInt(sqlportion[m]);
											}else{
												portion[i]=parseInt(portion[i])+parseInt(sqlportion[m]);
											}
										}
									}
									n=n+1;
								}
							}
						}
						var ctx = document.getElementById('myChartw6').getContext('2d');
						var myChartw6 = new Chart(ctx, {
							type: 'bar',
							data: {
								labels: [mmdd[13],mmdd[12], mmdd[11], mmdd[10], mmdd[9], mmdd[8], mmdd[7]],//改日期
								datasets: [{
									label: '營養素圖表-周',
									data: [portion[13], portion[12], portion[11], portion[10], portion[9], portion[8],portion[7]],//改數值
									backgroundColor: [
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)',
										'rgba(54, 162, 235, 0.2)'
									],
									borderColor: [
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)',
										'rgba(54, 162, 235, 1)'
									],
									borderWidth: 1
								}]
							},
							options: {
								scales: {
									y: {
										beginAtZero: true
									}
								}
							}
						});
						myChartw6.render();
					</script>
				</div>
			</div>
			
		</div>
	<!--</div>-->
</div>

</body>
</html>