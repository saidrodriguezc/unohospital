<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_bar.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$mes = $_GET["itemid"];

	$vsql = "SELECT extract( DAY FROM fechadoc) dia , extract( MONTH FROM fechadoc) mes , extract( YEAR FROM fechadoc) ano , SUM( total ) total
			 FROM documentos D
			 WHERE D.tipodoc = 'FVE' AND D.fecasentado <> '0000-00-00 00:00:00' AND D.fecanulado = '0000-00-00 00:00:00' 
			 AND DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= fechadoc
			 GROUP BY 1,2,3 ORDER BY fechadoc ASC";

$result = mysql_query($vsql,$conex); 

$i=0;
while($row = mysql_fetch_array($result))
{
  $datosx[$i]= $row[0];
  $datosy[$i]= $row[3];
  $i++;
}

if($i > 0)
{

  $graph = new Graph(900,230,'auto');	
  $graph->SetScale("textlin");
  $graph->img->SetMargin(60,20,20,30);

  $graph->xaxis->SetTickLabels($datosx);
  $graph->xaxis->title->SetColor('white');
  $bplot1 = new BarPlot($datosy);
  $bplot1->SetFillColor('orange@0.4');

  $gbarplot = new GroupBarPlot(array($bplot1));
  $gbarplot->SetWidth(0.6);
  $graph->Add($gbarplot);

  $graph->Stroke();
}

?>