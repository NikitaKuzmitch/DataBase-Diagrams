<!DOCTYPE html>
<?php
$db = mysqli_connect("localhost", "root", "masterkey", "BDKUZ") or die(mysqli_error()); //подключение к БД
$sql = mysqli_query($db, "SHOW TABLES FROM `BDKUZ`"); //запрос
$data = array();
$datalist = array();
$TABLE_NAME ;
while ($rows = mysqli_fetch_array($sql)) { // массив с данными
 echo "Таблица: <a href='?id_table={$rows[0]}'>{$rows[0]}</a><br>"; //вывод данных
 //заполнение массива таблиц
   $row = array (
"key" => $rows[0],
"color" => 'lightblue',

);
array_push($data, $row);
 //цикл заполение массива метда  
  $listtable = mysqli_query($db, "SELECT 
DISTINCT 
  TABLE_NAME,
   
    REFERENCED_TABLE_NAME
	 
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
	REFERENCED_TABLE_SCHEMA = 'BDKUZ'
    AND REFERENCED_TABLE_NAME = '".$rows[0]."';");
	
	while ($rowlist = mysqli_fetch_array($listtable)) 
	{ 
	
	 $masslist = array (
"from" => $rowlist['REFERENCED_TABLE_NAME'],
"to" => $rowlist['TABLE_NAME'],
);


array_push($datalist, $masslist);

	}
}
echo json_encode($datalist); 
 
echo "В базе `id3176198_geektime`: ".mysqli_num_rows($sql)." таблиц"; // вывод числа таблиц
 
if (isset($_GET['id_table'])) { // если нажали на ссылку (название таблицы)
    $rs = mysqli_query($db, "SHOW COLUMNS FROM ".$_GET['id_table'].""); //запрос на выборку данных и выбраной таблицы?>
     <table border='1'>
    <?php
    while($row_rs = mysqli_fetch_assoc($rs)) // массив с данными
    {
    ?>
        <tr>
    <?php
        foreach($row_rs as $val) //перебор массива в цикле
        {
 
            echo "<td>".$val."</td>"; //вывод данных
               
        }
    ?>
        </tr>
 
    <?php }?>
 
</table>
    
<?php }

if (isset($_GET['id_table'])) { // если нажали на ссылку (название таблицы)
    $rs  = mysqli_query($db, "SELECT * FROM information_schema.KEY_COLUMN_USAGE
				WHERE TABLE_SCHEMA ='BDKUZ'  AND CONSTRAINT_NAME <>'PRIMARY'  AND CONSTRAINT_NAME LIKE  '%FK%'  AND TABLE_NAME ='".$_GET['id_table']."';");  //запрос на выборку данных и выбраной таблицы?>
     <table border='1'>
    <?php
    while($row_rs = mysqli_fetch_assoc($rs)) // массив с данными
    {
    ?>
        <tr>
    <?php
        foreach($row_rs as $val) //перебор массива в цикле
        {
 
            echo "<td>".$val."</td>"; //вывод данных
               
        }
    ?>
        </tr>
 
    <?php }?>
 
</table>
    
<?php }?>
<html>
<head>
  <meta charset="UTF-8">
  <title>Minimal GoJS Sample</title>
  <meta name="description" content="An almost minimal diagram using a very simple node template and the default link template." />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Copyright 1998-2019 by Northwoods Software Corporation. -->

  <script src="go.js"></script>

  <script id="code">
    function init() {
      if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this

      var $ = go.GraphObject.make;  // for conciseness in defining templates

      myDiagram = $(go.Diagram, "myDiagramDiv",  // create a Diagram for the DIV HTML element
        {
          "undoManager.isEnabled": true  // enable undo & redo
        });

      // define a simple Node template
      myDiagram.nodeTemplate =
        $(go.Node, "Auto",  // the Shape will go around the TextBlock
          $(go.Shape, "RoundedRectangle", { strokeWidth: 2, fill: "white" },
            // Shape.fill is bound to Node.data.color
            new go.Binding("fill", "color")),
          $(go.TextBlock,
            { margin: 10 },  // some room around the text
            // TextBlock.text is bound to Node.data.key
            new go.Binding("text", "key"))
        );

      // but use the default Link template, by not setting Diagram.linkTemplate
var nodeDataArray =<?php echo json_encode($data);?>;
var nodeDatalist =<?php echo json_encode($datalist);?>;
      // create the model data that will be represented by Nodes and Links
           myDiagram.model = new go.GraphLinksModel(nodeDataArray,nodeDatalist);
    }
  </script>
</head>
<body onload="init()">
<div id="sample">
  <!-- The DIV for the Diagram needs an explicit size or else we won't see anything.
       This also adds a border to help see the edges of the viewport. -->
  <div id="myDiagramDiv" style="border: solid 1px black; width:1000px; height:1000px"></div>
  
</div>
</body>
</html>