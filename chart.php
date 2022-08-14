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
</head>
<body>
<div style="display:inline-block;">
<button class="btn" id="month">月</button>
<button class="btn" id="week">周</button>
<div class="container1">
<button class="btn2" id="1">全榖雜糧類</button>
<button class="btn2" id="2">豆魚蛋肉類</button>
<button class="btn2" id="3">乳品類</button>
<button class="btn2" id="4">蔬菜類</button>
<button class="btn2" id="5">水果類</button>
<button class="btn2" id="6">油脂與堅果種子類</button>
<canvas id="myChart" ></canvas>
</div>
</div>
<script>
var sqldate=[];
var sqlportion=[];
var iID_NID=[];
var count=0;
</script>
<?php
    //連接歷史紀錄資料表
    $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
    $query = "SELECT COUNT(`ID`),`dishID` ,`date` FROM `history` WHERE `UID`='$userID' ORDER BY `ID` DESC";
    $result = $link->query($query);
	$count = $result->fetchColumn();
	
    foreach($result as $row){
        
		$dishID = $row['dishID'];
		$sqldate=$row['date'];

    //取得所有需要的資料
		
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
sqldate.push('<?php echo $sqldate;?>');
sqlportion.push('<?php echo $portion;?>');
iID_NID.push('<?php echo $iID_NID;?>');
count='<? php echo $count;?>';
</script>
<?php
			}
		}
	}
?>
<script>
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

var date=[];
var mmdd=[];
var portion=[];

for(var i=1;i<32;i++){
	date.push(i);
}
//切換

window.onload=function(){
	//onclick button
	var n1=document.getElementById("1");
	var n2=document.getElementById("2");
	var n3=document.getElementById("3");
	var n4=document.getElementById("4");
	var n5=document.getElementById("5");
	var n6=document.getElementById("6");
	var b1=document.getElementById("month");
	var b2=document.getElementById("week");
	
	b1.onclick=function(){
		mmdd.push(m1.substr(5,1));
		mmdd.push(m2.substr(5,1));
		mmdd.push(m3.substr(5,1));
		mmdd.push(m4.substr(5,1));
		mmdd.push(m5.substr(5,1));
		mmdd.push(m6.substr(5,1));
		mmdd.push(m7.substr(5,1));
	}
	
	b2.onclick=function(){
		mmdd.push(t1.substr(0,5)+'0'+t1.substr(5,4));
		mmdd.push(t2.substr(0,5)+'0'+t2.substr(5,4));
		mmdd.push(t3.substr(0,5)+'0'+t3.substr(5,4));
		mmdd.push(t4.substr(0,5)+'0'+t4.substr(5,4));
		mmdd.push(t5.substr(0,5)+'0'+t5.substr(5,4));
		mmdd.push(t6.substr(0,5)+'0'+t6.substr(5,4));
		mmdd.push(t7.substr(0,5)+'0'+t7.substr(5,4));
	}                           
	
	n1.onclick=function(){

		for(var m=0;m<count;m++){
			if(iID_NID[m]==1){
				for(var i=0;i<7;i++){
					var s=0;
					if(mmdd[i]==sqldate[i]){
						portion[i]=sqlportion[i];
						s=1;
						continue;
					}else{
						s=0;
					}
					if(s==0){
						portion[i]=0;
					}
				}
			}
		}
		show(portion[0],portion[1],portion[2],portion[3],portion[4],portion[5],portion[6]);
		return false;
	}
	n2.onclick=function(){
		show();
		return false;
	}
	n3.onclick=function(){
		show();
		return false;
	}
	n4.onclick=function(){
		show();
		return false;
	}
	n5.onclick=function(){
		show();
		return false;
	}
	n6.onclick=function(){
		show();
		return false;
	}
}
function show(obj1,obj2,obj3,obj4,obj5,obj6,obj7){

	const ctx = document.getElementById('myChart').getContext('2d');
	const myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: [mmdd[0],mmdd[1], mmdd[2], mmdd[3], mmdd[4], mmdd[5], mmdd[6]],//改日期
			datasets: [{
				label: '營養素圖表',
				data: [obj1,obj2,obj3,obj4,obj5,obj6,obj7],//改數值
				backgroundColor: [
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'rgba(255, 206, 86, 0.2)',
					'rgba(75, 192, 192, 0.2)',
					'rgba(153, 102, 255, 0.2)',
					'rgba(255, 159, 64, 0.2)',
					'rgba(220, 31, 224, 0.2)'
				],
				borderColor: [
					'rgba(255, 99, 132, 1)',
					'rgba(54, 162, 235, 1)',
					'rgba(255, 206, 86, 1)',
					'rgba(75, 192, 192, 1)',
					'rgba(153, 102, 255, 1)',
					'rgba(255, 159, 64, 1)',
					'rgba(220, 31, 224, 1)'
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
}
</script>
</body>
</html>