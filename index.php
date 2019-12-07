<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Список баз данных</title>
  <meta name="description" content="An almost minimal diagram using a very simple node template and the default link template." />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Copyright 1998-2019 by Northwoods Software Corporation. -->
 

</head>
<body >
<div style="text-align:center;">
  <a href="index.php" class="logo"><img src="diagram.jpg" style="margin-right:10px;">Database Diagrams</a>
  <p class="block">Инструмент для автоматического построения 
  диаграмм и визуализации таблиц существующих баз данных.</p></div>
  <br>
<form  method="post">
<?php
include 'Configs.php'; 

$db = mysqli_connect($HostName, $HostUser, $HostPass) or die(mysqli_error()); //подключение к БД
$sql = mysqli_query($db, "SHOW DATABASES;"); //запрос

echo '<select name="listdb" class="cs-select">'; //открываем тэг select.

echo '<option value=1>Список баз данных</option>';

while ($rows = mysqli_fetch_array($sql))
	{ // массив с данными
	echo '<option value='.$rows[0].'>'.$rows[0].'</option>';
	}
	
echo '</select>';
 
if(isset($_POST['btn']))
	{
		session_start();
        $_SESSION['db'] = $_POST['listdb'];
        header("Location: Bachman.php");
 	}
	
?>
<button name="btn" class="button5"> <img src="btn.png" style="margin-right:5px;margin-top:3px;float:left;width:20px;"> Перейти</button>
</form>


    

</body>
</html>
<Style>
.logo {
  color: black;
  font-family: 'Playfair Display', serif;
  font-size: 2.5em;
  padding: 20px 0;
  text-decoration-line: none;
  text-align:center;
}
.block
{
font-family: 'Playfair Display', serif;
  font-size: 24px;
    font-weight: bold;
    color: #404040;
	text-decoration-line: none;
  width:500px;
 text-align:center;
 margin:10PX AUTO;

}
button {
    background-color: #4CAF50;
    border: none;
    color: white;
       text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 24px;
     cursor: pointer;
	 position: absolute;
    left: 60%;
	max-width: 600px;
    margin-right: 50%;
  font-weight: 700;
   font-family: 'Playfair Display', serif;
   }
.button5 {
    background-color: white;
    color: black;
	max-width: 600px;
 border: 2px black solid;
	width:150px;
	 left: 55%;
	 border-radius:7px;
}
    header {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    min-height: 100%;
    padding: 0 0 0 50px;
    background: #fff;
    float: left;
    overflow: hidden;
    z-index: 9999;
}
header .logo {
    margin-top: 100px;
}
a {
    margin: 0;
    padding: 0;
    font-size: 100%;
    vertical-align: baseline;
    background: 0 0;
    color: #454545;
}
header nav ul {
    display: block;
    overflow: hidden;
    margin-top: 100px;
    list-style: none;
}
header nav ul li {
    display: block;
    margin-bottom: 30px;
}
header nav ul li a {
    color: #454545;
 font-family: 'Playfair Display', serif;
    font-size: 14px;
    text-decoration: none;
    letter-spacing: 1px;
}


.cs-select {
     border: 2px black solid;
    text-color: black;
       position: absolute;
    left: 35%;
 
    margin-right: 30%;
    font-size: 24px;
    font-weight: 700;
    max-width: 250px;
  font-family: 'Playfair Display', serif;
 	 border-radius:7px;
}

</Style>