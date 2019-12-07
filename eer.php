<!DOCTYPE html>
<?php
include 'Configs.php';
session_start();
if (isset($_SESSION['db'])) 
	{
		$DatabaseName = $_SESSION['db'];
		$db = mysqli_connect($HostName, $HostUser, $HostPass, $DatabaseName)  or die(mysqli_error());
		$sql = mysqli_query($db, "SHOW TABLES FROM $DatabaseName"); //запрос
		$data = array();
		$datalist = array();
		$list = array();
		$i = 1;
		
		while ($rows = mysqli_fetch_array($sql)) 
			{ // массив с данными
				$rs = mysqli_query($db, "SHOW COLUMNS FROM ".$rows[0]."");
				$table = $rows[0];
				while ($row_rs = mysqli_fetch_array($rs)) 
					{
						$rowdd = array ( 'name' => $row_rs['Field'], 'type' => $row_rs['Type'], 'visibility' => "public");
						array_push($datalist, $rowdd);
					}
				$row = array (
				"key" => $table,
				"name" => $table,
				"properties" => $datalist);
				array_push($data, $row); 
				$datalist = array();
				$d = 1;

				$listtable = mysqli_query($db, "SELECT DISTINCT TABLE_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
				WHERE REFERENCED_TABLE_SCHEMA = '".$DatabaseName."' AND REFERENCED_TABLE_NAME = '".$rows[0]."' ;");
	
				while ($rowlist = mysqli_fetch_array($listtable)) 
					{
						$masslist = array (
						"from"=>  $rowlist['REFERENCED_TABLE_NAME'], 
						"to"=>$rowlist['TABLE_NAME'], 
						"relationship"=> "generalization");
							array_push($list, $masslist);
					}
				$i++;
			}
	}

?>

<html>
<head>
  <meta charset="UTF-8">
  <title>EER</title>
  <meta name="description" content="UML Class-like nodes showing two collapsible lists of items." />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Copyright 1998-2019 by Northwoods Software Corporation. -->

  <script src="go.js"></script>
  
  <script id="code">
    function init() {
   
      var $ = go.GraphObject.make;

      myDiagram =
        $(go.Diagram, "myDiagramDiv",
          {
            "undoManager.isEnabled": true,
            
          });

      // show visibility or access as a single character at the beginning of each property or method
      function convertVisibility(v) {
        switch (v) {
          case "public": return "";
          case "private": return "";
          case "protected": return "";
          case "package": return "";
          default: return v;
        }
      }

      // the item template for properties
      var propertyTemplate =
        $(go.Panel, "Horizontal",
          // property visibility/access
          $(go.TextBlock,
            { isMultiline: false, editable: false, width: 12 },
            new go.Binding("text", "visibility", convertVisibility)),
          // property name, underlined if scope=="class" to indicate static property
          $(go.TextBlock,
            { isMultiline: false, editable: true },
            new go.Binding("text", "name").makeTwoWay(),
            new go.Binding("isUnderline", "scope", function(s) { return s[0] === 'c' })),
          // property type, if known
          $(go.TextBlock, "",
            new go.Binding("text", "type", function(t) { return (t ? ": " : ""); })),
          $(go.TextBlock,
            { isMultiline: false, editable: true },
            new go.Binding("text", "type").makeTwoWay()),
          // property default value, if any
          $(go.TextBlock,
            { isMultiline: false, editable: false },
            new go.Binding("text", "default", function(s) { return s ? " = " + s : ""; }))
        );

      // the item template for methods
      var methodTemplate =
        $(go.Panel, "Horizontal",
          // method visibility/access
          $(go.TextBlock,
            { isMultiline: false, editable: false, width: 12 },
            new go.Binding("text", "visibility", convertVisibility)),
          // method name, underlined if scope=="class" to indicate static method
          $(go.TextBlock,
            { isMultiline: false, editable: true },
            new go.Binding("text", "name").makeTwoWay(),
            new go.Binding("isUnderline", "scope", function(s) { return s[0] === 'c' })),
          // method parameters
          $(go.TextBlock, "()",
            // this does not permit adding/editing/removing of parameters via inplace edits
            new go.Binding("text", "parameters", function(parr) {
              var s = "(";
              for (var i = 0; i < parr.length; i++) {
                var param = parr[i];
                if (i > 0) s += ", ";
                s += param.name + ": " + param.type;
              }
              return s + ")";
            })),
          // method return type, if any
          $(go.TextBlock, "",
            new go.Binding("text", "type", function(t) { return (t ? ": " : ""); })),
          $(go.TextBlock,
            { isMultiline: false, editable: true },
            new go.Binding("text", "type").makeTwoWay())
        );

      // this simple template does not have any buttons to permit adding or
      // removing properties or methods, but it could!
      myDiagram.nodeTemplate =
        $(go.Node, "Auto",
          {
            locationSpot: go.Spot.Center,
            fromSpot: go.Spot.AllSides,
            toSpot: go.Spot.AllSides
          },
          $(go.Shape, { fill: "white" }),
          $(go.Panel, "Table",
            { defaultRowSeparatorStroke: "black" },
            // header
            $(go.TextBlock,
              {
                row: 0, columnSpan: 2, margin: 3, alignment: go.Spot.Center,
                font: "bold 12pt sans-serif",
                isMultiline: false, editable: true
              },
              new go.Binding("text", "name").makeTwoWay()),
            // properties
            $(go.TextBlock, "Properties",
              { row: 1, font: "italic 10pt sans-serif" },
              new go.Binding("visible", "visible", function(v) { return !v; }).ofObject("PROPERTIES")),
            $(go.Panel, "Vertical", { name: "PROPERTIES" },
              new go.Binding("itemArray", "properties"),
              {
                row: 1, margin: 3, stretch: go.GraphObject.Fill,
                defaultAlignment: go.Spot.Left, background: "white",
                itemTemplate: propertyTemplate
              }
            ),
            $("PanelExpanderButton", "PROPERTIES",
              { row: 1, column: 1, alignment: go.Spot.TopRight, visible: false },
              new go.Binding("visible", "properties", function(arr) { return arr.length > 0; })),
            // methods
            $(go.TextBlock, "Methods",
              { row: 2, font: "italic 10pt sans-serif" },
              new go.Binding("visible", "visible", function(v) { return !v; }).ofObject("METHODS")),
            $(go.Panel, "Vertical", { name: "METHODS" },
              new go.Binding("itemArray", "methods"),
              {
                row: 2, margin: 3, stretch: go.GraphObject.Fill,
                defaultAlignment: go.Spot.Left, background: "lightyellow",
                itemTemplate: methodTemplate
              }
            ),
            $("PanelExpanderButton", "METHODS",
              { row: 2, column: 1, alignment: go.Spot.TopRight, visible: false },
              new go.Binding("visible", "methods", function(arr) { return arr.length > 0; }))
          )
        );

      function convertIsTreeLink(r) {
        return r === "generalization";
      }

      function convertFromArrow(r) {
        switch (r) {
          case "generalization": return "";
          default: return "";
        }
      }

      function convertToArrow(r) {
        switch (r) {
          case "generalization": return "Triangle";
          case "aggregation": return "StretchedDiamond";
          default: return "";
        }
      }

      myDiagram.linkTemplate =
        $(go.Link,
          { routing: go.Link.Orthogonal },
          new go.Binding("isLayoutPositioned", "relationship", convertIsTreeLink),
          $(go.Shape),
          $(go.Shape, { scale: 1.3, fill: "white" },
            new go.Binding("fromArrow", "relationship", convertFromArrow)),
          $(go.Shape, { scale: 1.3, fill: "white" },
            new go.Binding("toArrow", "relationship", convertToArrow))
        );

      // setup a few example class nodes and relationships
      var nodedata = <?php echo json_encode($data);?>;
      var linkdata = <?php echo json_encode($list);?>;
     
      myDiagram.model = $(go.GraphLinksModel,
        {
          copiesArrays: true,
          copiesArrayObjects: true,
          nodeDataArray: nodedata,
          linkDataArray: linkdata
        });
    }
  </script>
</head>
<body onload="init()">
<header>
<div class="head">
    <div class="block">
  <p>База данных: <b class="Namebd"><?php echo $DatabaseName;?></b></p>
  <p>ER-диаграмма</p>
  </div>
<div style="text-align:center;">
  <a href="index.php" class="logo"><img src="diagram.jpg" style="margin-right:10px;"> Database Diagrams</a></div>

  <nav>
    <ul class="topmenu">
   				<li><a href="Bachman.php" class="selected">Диаграмма Бахмана</a></li>
				<li><a href="table.php">Таблица</a></li>
				<li><a href="eer.php">ER-диаграмма</a></li>
				<li><a href="dbgl.php">DBDGL-диаграмма</a></li>
    </ul>
  </nav>
</div>
</header>
<div id="sample" class="main">
  <div id="myDiagramDiv" clas style=" width:100%; height:1000px"></div>
    
</div>
</body>
</html>
<Style>
.head
{
   position: fixed;
    height: 100px;
    top: 0;
    width: 100%;
    z-index: 100;
	background:white;
	border-bottom:1px black solid;
}
.logo {
  color: black;
  font-family: 'Playfair Display', serif;
  font-size: 2.5em;
  padding: 20px 0;
  text-decoration-line: none;
  text-align:center;
}
nav {
  display: table;
  margin: 0 auto;
}
nav ul {
  list-style: none;

}
.topmenu:after {
  content: "";
  display: table;
  clear: both;
}
.topmenu > li {

  float: left;
  position: relative;
  font-family: 'Open Sans', sans-serif;
}
.topmenu > li > a {
font-family: 'Playfair Display', serif;
  font-size: 20px;
    font-weight: bold;
    color: #00008b;
	text-decoration-line: none;
		border-bottom: 2px #00008b solid;
  padding: 1px 1px;
  margin-left:15px;
}

.block
{
font-family: 'Playfair Display', serif;
  font-size: 18px;
    font-weight: bold;
    color: #404040;
	text-decoration-line: none;
    position: absolute;
}
.Namebd
{
text-transform: uppercase;
}
.main {
    width: 100%;
    height: 100%;
padding-top:100px;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
position: relative;
    z-index: 55;
   
    background: white;
    clear: both;
}
</Style>