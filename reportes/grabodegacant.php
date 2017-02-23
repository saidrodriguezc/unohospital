<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_pie.php');
require_once ('../lib/jpgraph/jpgraph_pie3d.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$mes = $_GET["itemid"];

$vsql='SELECT B.descripcion , SUM(E.existencia) existencia
	   FROM bodegas B 
	   INNER JOIN existencias E ON (B.bodegaid = E.bodegaid)
	   GROUP BY 1 ORDER BY 1';

$result = mysql_query($vsql,$conex); 

$i=0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= $row[0];
  $datosy[$i]= $row[1];
  $i++;
}

if($i > 0)
{
 $graph = new PieGraph(410,290);	
 $graph->title->SetColor("black");
 $graph->legend->Pos(0.02,0.2); 

 $p1 = new PiePlot3d($datosy); 
 $p1->SetCenter(0.30);

 $p1->SetLegends($datosx);

 $graph->Add($p1);
 $graph->Stroke();
}

?>