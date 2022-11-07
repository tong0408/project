<!DOCTYPE html>
<?PHP include("configure.php");
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
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<style>	
	td{height:50px;}
	
</style>
</head>
<body>
<?php include("header.php"); ?>
<a href="back.php?id=2"><button class="btn1 return">返回</button></a>
    <div class="form1">
		食品：<input type="text" name="search_text" id="search_text" placeholder="請輸入搜尋文字" class="form-control" style="display:inline-block">
		<div id="search_result"></div>
	</div>
	<script>
        $(document).ready(function () {

            load_data();

            function load_data(query) {
                $.ajax({
                    url: "search.php",
                    method: "GET",
                    data: {
                        s: query
                    },
                    success: function (data) {
                        $('#search_result').html(data);
                    }
                });
            }
            $('#search_text').keyup(function () {
                var search = $(this).val();
                if (search != '') {
                    load_data(search);
                } else {
                    load_data();
                }
            });
        });
    </script>
</body>
</html>
