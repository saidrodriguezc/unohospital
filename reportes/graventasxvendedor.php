<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_pie.php');
require_once ('../lib/jpgraph/jpgraph_pie3d.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$mes = $_GET["itemid"];
$opcion = $_GET['opcion'];

$vsql = "SELECT DISTINCT T.nombre , D.terid2 , SUM(D.total) vendido 
         FROM documentos D 
    	 INNER JOIN terceros T ON (T.terid = D.terid2) 
		 WHERE D.tipodoc = 'FVE' 
		 GROUP BY 1,2 ORDER BY 3 LIMIT 0,3";

$result = mysql_query($vsql,$conex); 

$i=0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= substr($row[0],0,9);
  $datosy[$i]= $row[1];
  $i++;
}

if($i > 0)
{
 
 $graph = new PieGraph(380,310);	 
 $graph->title->SetColor("black");
 $graph->legend->Pos(0.02,0.02); 

 $p1 = new PiePlot3d($datosy); 
 $p1->SetSize(0.45);
// $p1->SetCenter(0.5);
 $p1->SetCenter(0.47,0.62);
 $p1->SetLegends($datosx);

 $graph->Add($p1);
 $graph->Stroke();
}

?>