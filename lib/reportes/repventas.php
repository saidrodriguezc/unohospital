<?PHP
  session_start(); 
  include("../lib/Sistema.php");
  include("../lib/libdocumentos.php");  
  include("configreportes.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
  $ruta="../";
    
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "detdia")
  {
	$ano = $_GET['ano'];
	$mes = $_GET['mes'];
	$dia = $_GET['dia'];		
	
    $vsql = "SELECT D.docuid , D.tipodoc , D.prefijo , D.numero , T.nombre , D.base , D.iva , D.total
		     FROM documentos D
			 INNER JOIN terceros T ON (T.terid = D.terid1)
			 WHERE D.tipodoc = 'FVE' AND D.fecasentado <> '0000-00-00 00:00:00' AND D.fecanulado = '0000-00-00 00:00:00' 
			 AND extract(DAY FROM D.fechadoc) = ".$dia." AND extract(MONTH FROM D.fechadoc) = ".$mes." AND extract(YEAR FROM D.fechadoc) = ".$ano."
			 ORDER BY extract(YEAR FROM D.fechadoc) , extract(MONTH FROM D.fechadoc) , extract(DAY FROM D.fechadoc) , D.tipodoc , D.prefijo , D.numero";
   
    $cont = $clase->HeaderReportes();
	$cont.= EncabezadoReporte("Ventas Diarias del ".$dia."/".$mes."/".$ano);	

	$cont.='<div id="principal">
			<table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="30" align="center"> Factura No.</th>
	         <th width="30" align="center"> Cliente </th>			 
			 <th width="30" align="right"> Base </th>
			 <th width="30" align="right"> IVA </th>
			 <th width="30" align="right"> Total </th>			 			 
             <th width="10"> &nbsp; </th>					  			 			 
           </tr>';

    $i=0;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	
	$sumbase  =0;
	$sumiva   =0;
	$sumtotal =0;		
	
	while($row = mysql_fetch_array($result))
	{

       if($i%2 == 0)
	      $cont.= '<tr class="TablaDocsImPar">';
	   else
	      $cont.= '<tr class="TablaDocsPar">';

        $cont.= '<td width="5" align="center"> &nbsp; </td>
                 <td width="30" align="center"> <a href="#" OnClick="window.open(\'../ventas.php?opcion=imprimirventa&id='.$row['docuid'].'\',\'VerFac\',\'left=50,top=50,width=600,height=600\');">'.$row['tipodoc'].' '.$row['prefijo'].' '.$row['numero'].' </td>
                 <td width="30" align="center"> '.substr($row['nombre'],0,25).' </td>				 
                 <td width="30" align="right">'.FormatoNumero($row['base']).' </td>
                 <td width="30" align="right">'.FormatoNumero($row['iva']).' </td>
                 <td width="30" align="right">'.FormatoNumero($row['total']).' </td>				 				 
				 <td width="10" align="center"> &nbsp; </td>
                </tr>';

	  $sumbase = $sumbase + $row['base'];  
	  $sumiva = $sumiva + $row['iva'];
	  $sumtotal = $sumtotal + $row['total'];	  
	  
	  $i++;		
	}
	
	$cont.=' <tr class="TablaDocsPar">
              <td width="5" align="center"> &nbsp; </td>
                 <td width="30" align="center">  </td>
                 <td width="30" align="center"> <b> Totales </b> </td>				 
                 <td width="30" align="right"> <b> '.FormatoNumero($sumbase).' </b> </td>
                 <td width="30" align="right"> <b> '.FormatoNumero($sumiva).' </b> </td>
                 <td width="30" align="right"> <b> '.FormatoNumero($sumtotal).'</b> </td>				 				 
				 <td width="10" align="center"> &nbsp; </td>
                </tr>
		       </table> </div><br><br>';	  
  }
 
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	$vsql = "SELECT extract( DAY FROM fechadoc) dia , extract( MONTH FROM fechadoc) mes , extract( YEAR FROM fechadoc) ano , SUM( total ) total
			 FROM documentos D
			 WHERE D.tipodoc = 'FVE' AND D.fecasentado <> '0000-00-00 00:00:00' AND D.fecanulado = '0000-00-00 00:00:00' 
			 AND DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= fechadoc
			 GROUP BY 1,2,3 ORDER BY fechadoc ASC";
			   
    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Facturas de Venta Dia a Dia");	

	$cont.='<div id="principal">
	        <br>
            <img src="graventasdiadia.php" border="0">
			<br><br>
			<table width="500">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="30" align="center"> Fecha </th>
			 <th width="30" align="right"> Vendido </th>
             <th width="10"> &nbsp; </th>					  			 			 
           </tr>';

    $i=0;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	
	$saldo=0;
	$sumtotal=0;
	
	while($row = mysql_fetch_array($result))
	{

       if($i%2 == 0)
	      $cont.= '<tr class="TablaDocsImPar">';
	   else
	      $cont.= '<tr class="TablaDocsPar">';

        $cont.= '<td width="5" align="center"> &nbsp; </td>
                 <td width="30" align="center"> <a href="?opcion=detdia&ano='.$row['ano'].'&mes='.$row['mes'].'&dia='.$row['dia'].'">'.$row['dia'].'/'.$row['mes'].'/'.$row['ano'].' </a> </td>
                 <td width="30" align="right">'.FormatoNumero($row['total']).' </td>
				 <td width="10" align="center"> &nbsp; </td>
                </tr>';
	  $sumtotal = $sumtotal + $row['total'];	 
	  $i++;		
	}
	$cont.='<tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="30" align="center"> Total Vendido </th>
			 <th width="30" align="right"> '.number_format($sumtotal).' </th>
             <th width="10"> &nbsp; </th>					  			 			 
           </tr> </table> </div>';	  
  
  }
  
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  echo $cont;  

?> 