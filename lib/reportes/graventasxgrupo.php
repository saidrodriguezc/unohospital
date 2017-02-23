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

$vsql='SELECT GP.descripcion, SUM( DD.valparcial ) Total
	   FROM documentos D
	   INNER JOIN dedocumentos DD ON ( D.docuid = DD.docuid ) 
	   INNER JOIN productos P ON ( P.itemid = DD.itemid ) 
	   INNER JOIN gruposprod GP ON ( P.gruposprodid = GP.gruposprodid ) 
	   WHERE GP.codigo <> "PRE" AND D.tipodoc = "PSE" AND D.fecasentado <> "0000-00-00 00:00:00" AND D.fecanulado = "0000-00-00 00:00:00" 
	   AND DATE_SUB(CURDATE(),INTERVAL 20 DAY) <= fechadoc
	   GROUP BY 1';

$result = mysql_query($vsql,$conex); 

$i=0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= substr($row[0],0,12);
  $datosy[$i]= $row[1];
  $i++;
}

if($i > 0)
{
 
 $graph = new PieGraph(380,310);	 
 $graph->title->SetColor("black");
 $graph->legend->Pos(0.00,0.00); 

 $p1 = new PiePlot3d($datosy); 
 $p1->SetSize(0.5);
// $p1->SetCenter(0.5);
 $p1->SetCenter(0.47,0.62);
 $p1->SetLegends($datosx);

 $graph->Add($p1);
 $graph->Stroke();
}

?>