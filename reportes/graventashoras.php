<?php
session_start();
require_once ('../lib/jpgraph/jpgraph.php');
require_once ('../lib/jpgraph/jpgraph_bar.php');
include("../lib/Sistema.php");

$clase = new Sistema();
$conex  = $clase->Conectar();

$mes = $_GET["itemid"];

	$vsql = "SELECT EXTRACT( HOUR FROM fechadoc ) hora , SUM( total ) 
			 FROM documentos
			 WHERE tipodoc = 'FVE' AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00'
			 GROUP BY 1 ASC";

$result = mysql_query($vsql,$conex); 

$i=0;
while($row = mysql_fetch_array($result))
{
  $hora = $row[0];
  if($hora <= 11)
    $horax = $hora." AM";

  if($hora == 12)
    $horax = $hora." M";

  if($hora > 12)
    $horax = ($hora - 12)." PM";
    
  $datosx[$i]= $horax;
  $datosy[$i]= $row[1];
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