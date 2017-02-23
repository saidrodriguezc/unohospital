<?PHP
  session_start(); 
  include("../lib/Sistema.php");
  include("configreportes.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
  $ruta="../";
    
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

 
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "enpdf")
  {
	$desde   = $_GET['desde'];
	$hasta   = $_GET['hasta'];
	$grupoid = $_GET['grupoid'];

	$vsql = "SELECT GP.descripcion Grupo , I.codigo Codproducto , I.descripcion Nomproducto, SUM(DD.cantidad) cant , SUM(DD.valparcial) total 
			 FROM documentos  D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid)
			 INNER JOIN item I ON (DD.itemid = I.itemid) INNER JOIN productos P ON (P.itemid = I.itemid)
			 INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid)
			 WHERE (tipodoc = 'FVE' OR tipodoc = 'RSA') AND (fechadoc >= '".$desde." 00:00:00' AND fechadoc <= '".$hasta." 23:29:00') 
			 AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00' ";

	if($grupoid != "")		 
  	   $vsql.= "AND P.gruposprodid='".$grupoid."' ";
  	   
	$vsql.= "GROUP BY 1,2
	         ORDER BY GP.codigo ASC , I.descripcion ASC ";
	         
   	$cont.='<center><b>Informe de Ventas por Productos</b><br>
	        <center><b>Desde el '.$desde.' hasta el '.$hasta.'</b><br>
	        <table width="730" border="1">
    	      <tr bgcolor="#E0ECF8"> 
    		     <td width="120"><b><font size="1">GRUPO</B></td>
    		     <td width="30"><b><font size="1">CODIGO</B></td>
    		     <td width="220"><b><font size="1">PRODUCTO</B></td>
				 <td width="30" align="right"><b><font size="1">CANT</B></td>
				 <td width="50" align="right"><b><font size="1">VALOR</B></td>
			  </tr>';
      
	$totalgrupo = 0;
	$cantgrupo  = 0;	
	$totalgral  = 0;
	$cantgral   = 0;
	$grupoactual ="";
	$nuevogrupo  ="";
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))  	
	{
	    
		if($i == 0)
		{
			$grupoactual = $row['Grupo'];
			$nuevogrupo  = $row['Grupo'];
		}
		else
		{
			if($row['Grupo'] != $grupoactual)
			{
  		        $cont.='<tr bgcolor="#E0ECF8"> 
	      		         <td width="120"><b>'.$grupoactual.'</b></td>
  	            		 <td width="30"></td>
				         <td width="220"></td>
				         <td width="30" align="right"><b>'.number_format($cantgrupo).'</b></td>
		        		 <td width="50" align="right"><b>'.number_format($totalgrupo).'</b></td>
			   		    </tr>';		
			    
			    $grupoactual = $row['Grupo'];
				$totalgrupo=0;
				$cantgrupo=0;
			    $i=0;
			}
			
		} 
		
		$i++;
	    $cont.='<tr> 
                  <td width="120">'.substr($row['Grupo'],0,14).'</td>
  	              <td width="30">'.$row['Codproducto'].'</td>
		          <td width="220">'.substr($row['Nomproducto'],0,25).'</td>
		          <td width="30" align="right">'.$row['cant'].'</td>
		          <td width="50" align="right">'.number_format($row['total']).'</td>
			     </tr>';
	
	    $cantgral   = $cantgral  + $row['cant']; 
	    $totalgral  = $totalgral + $row['total'];
		$cantgrupo  = $cantgrupo + $row['cant'];  
	    $totalgrupo = $totalgrupo + $row['total']; 
	    
	}
	$cont.='<tr bgcolor="#E0ECF8">
	          <td width="120"><b>Total General</b></td>
	          <td width="30"></td>
	          <td width="220"></td>
	          <td width="30" align="right"><b>'.number_format($cantgral).'</b></td>
		      <td width="50" align="right"><b>'.number_format($totalgral).'</b></td>
			 </tr>    
            </table>';

	///Genero el Archivo PDF ...
    require('../lib/html2pdf/html2fpdf.php');
    $pdf=new HTML2FPDF();
    $pdf->AddPage();
    $pdf->WriteHTML($cont);
    $pdf->Output("repventaxproductos.pdf");	
    
    /// Lo visualizo en Pantalla
    
	echo'<script language="javascript">
	     <!--
   	       window.open("repventaxproductos.pdf");
	       window.history.back();
	     -->  
	     </script>';	     
  }   
  
  
  ////////////////////////////////////////////////  
  if($opcion == "enpantalla")
  {
	$desde    = $_GET['desde'];
	$hasta    = $_GET['hasta'];
	$contrato = $_GET['contrato'];

	$vsql = "SELECT concat(Extract( Day FROM D.fechadoc),'/',Extract( Month FROM D.fechadoc),'/',Extract( Year FROM D.fechadoc)) Grupo , EC.nombre Empresa , 
	         PA.nit , PA.nombre Paciente , I.descripcion Servicio, DD.cantidad cant , DD.valbase
			 FROM documentos  D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid)
			 INNER JOIN terceros PA ON (PA.terid = D.terid1)
			 INNER JOIN item I ON (DD.itemid = I.itemid) INNER JOIN productos P ON (P.itemid = I.itemid)
			 INNER JOIN contratos C ON (C.contratoid = D.contratoid)
			 INNER JOIN terceros EC ON (C.terid = EC.terid)			 
			 WHERE tipodoc = 'PSE' AND (fechadoc >= '".$desde." 00:00:00' AND fechadoc <= '".$hasta." 23:29:00') 
			 AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00' ";

	if($contrato != "")		 
  	   $vsql.= "AND C.contratoid=".$contrato." ";
  	   
	$vsql.= " ORDER BY D.fechadoc ASC , C.descripcion ASC , I.descripcion ASC ";

	$_SESSION["SQL_ULTREPORTE"] = $vsql;
	
  	$cont = $clase->HeaderReportes();
   	$cont.= EncabezadoReporte2("Atenciones x Prestador");		 
   	$cont.='<center>
	        <b>Detalle de Atenciones por Prestador</b> <br>
	        Desde el <b>'.$desde.'</b> hasta el <b>'.$hasta.'</b>
	        <br><br>
			<div style="overflow:auto; height:550px;width:910px;">
			<table width="850">
    	      <tr class="CabezoteTabla"> 
                 <td width="15">  </td>
    		     <td width="80"> <b>FECHA</B> </td>
    		     <td width="90">  <b>EMPRESA</B> </td>
    		     <td width="180"> <b>PACIENTE</B> </td>
    		     <td width="120"> <b>SERVICIO</B> </td>				 
				 <td width="10" align="right"> <b>CANT</B></td>
                 <td width="15"> </td>					
			  </tr>
			</table>
			
	    	<table width="850">';
      
	$totalgrupo = 0;
	$cantgrupo  = 0;	
	$totalgral  = 0;
	$cantgral   = 0;
	$grupoactual ="";
	$nuevogrupo  ="";
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))  	
	{
	    
		if($i == 0)
		{
			$grupoactual = $row['Grupo'];
			$nuevogrupo  = $row['Grupo'];
		}
		else
		{
			if($row['Grupo'] != $grupoactual)
			{
  		        $cont.='<tr class="TablaDocsPar">
				         <td width="15"> </td>
        		         <td width="80"> <b> Total '.$grupoactual.'</b> </td>
  	            		 <td width="90">  </td>
				         <td width="180">  </td>
				         <td width="120">  </td>						 
				         <td width="10" align="right"> <b>'.number_format($cantgrupo).' </b></td>
						 <td width="15"> </td>
			   		    </tr>';		
			    
			    $grupoactual = $row['Grupo'];
				$totalgrupo=0;
				$cantgrupo=0;
			    $i=0;
			}
			
		} 
		
		$i++;
	    if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.='<td width="15"> </td>
                 <td width="80">'.$row['Grupo'].'</td>
  	             <td width="90">'.$row['Empresa'].'</td>
		         <td width="180">'.$row['Paciente'].'</td>
		         <td width="120">'.$row['Servicio'].'</td>				 
		         <td width="10" align="right">'.$row['cant'].'</td>
				 <td width="15"> </td>
			    </tr>';
	
	    $cantgral   = $cantgral  + $row['cant']; 
	    $totalgral  = $totalgral + $row['total'];
		$cantgrupo  = $cantgrupo + $row['cant'];  
	    $totalgrupo = $totalgrupo + $row['total']; 
	    
	}
	$cont.='</table>  
	        <table width="850"> 
			 <tr class="CabezoteTabla"> 
		      <td width="15"> </td>
	          <td width="350"> <b> Total General </b></td>
	          <td width="50" align="right"> <b>'.number_format($cantgral).'</b> </td>
		      <td width="15"> </td>
			 </tr>';    
	$cont.='</table></div><br><br>';
	
 	echo $cont;
  }

  
  ////////////////////////////////////////////////
  if($opcion == "excel")
  {
    $vsql = $_SESSION["SQL_ULTREPORTE"];
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
  
    $reporte = "&nbsp;<center><table border=\"1\" align=\"center\">"; 
	$reporte.= "<tr bgcolor=\"#336666\"> 
				  <td><font color=\"#ffffff\"><strong>FECHA</strong></font></td> 
				  <td><font color=\"#ffffff\"><strong>EMPRESA</strong></font></td> 
				  <TD><font color=\"#ffffff\"><strong>CEDULA</strong></font></TD> 
				  <TD><font color=\"#ffffff\"><strong>PACIENTE</strong></font></TD> 
				  <TD><font color=\"#ffffff\"><strong>SERVICIO</strong></font></TD> 
				  <td><font color=\"#ffffff\"><strong>CANTIDAD</strong></font></td> 
				  <td><font color=\"#ffffff\"><strong>VLR UNITARIO</strong></font></td> 				  
				</tr>"; 

	while($row = mysql_fetch_array($result))  	
	{
     $reporte.=' <tr>
				   <td align="center"> '.$row[0].' </td>
				   <td align="center"> '.$row[1].' </td>
				   <td align="center"> '.$row[2].' </td>
				   <td align="center"> '.$row[3].' </td>
				   <td align="center"> '.$row[4].' </td>		
				   <td align="center"> '.$row[5].' </td>						   		   				   				   				   
				   <td align="center"> '.$row[6].' </td>				   
				   <td align="center"> '.$row[7].' </td>					   
				 </tr>';  
    }
	$reporte.= "</table>"; 	
	
	////////////////////////////////////////////////
	/// Mecanismo de desgarga de Archivo
	////////////////////////////////////////////////

	header("Content-type: application/vnd.ms-excel"); 
	header("Content-Disposition: attachment; filename=ServiciosPrestadosxContrato.xls"); 
	header("Pragma: no-cache"); 
	header("Expires: 0");  

	echo $reporte;  
  }

  
  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$formato = $_POST['formato'];
	
	$fdX        = $_POST['fecdesde'];
	$fhX        = $_POST['fechasta'];
	$contratoid = $_POST['contrato'];
	
	$desde = substr($fdX,6,4)."-".substr($fdX,3,2)."-".substr($fdX,0,2);
	$hasta = substr($fhX,6,4)."-".substr($fhX,3,2)."-".substr($fhX,0,2);		

    ////////////////////////////////////////
	// Visualizacion del Reporte
	////////////////////////////////////////
    if($formato == 'html')
      header("Location: ?opcion=enpantalla&desde=".$desde."&hasta=".$hasta."&contrato=".$contratoid);	
   
	if($formato == 'pdf')
      header("Location: ?opcion=enpdf&desde=".$desde."&hasta=".$hasta."&contrato=".$contratoid);	       
      
  } // Fin de Ver
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Informe de Servicios x Contrato");		 
     $sqlcombo = 'Select contratoid valor , T.nombre caption 
				  FROM contratos C INNER JOIN terceros T ON (C.terid = T.terid) 
                  ORDER BY nombre ASC';
				 
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	         <form action="?opcion=ver" method="POST" name="x">
			 <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Servicios Prestados por Contrato <td>
			  </tr>
			 </table>
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="100"> Fecha Desde :  </td>
			     <td width="160"> 
				   <input type="text" name="fecdesde" size="10" value="'.date("d/m/Y").'" id="fecdesde" onClick="popUpCalendar(this, x.fecdesde,\'dd/mm/yyyy\');">
				 </td>			     
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="100"> Fecha Hasta :  </td>
			     <td width="160"> 
				 <input type="text" name="fechasta" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, x.fechasta,\'dd/mm/yyyy\');">
				 </td>			     
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="100"> Prestador :  </td>
			     <td width="160">'.$clase->CrearComboFiltro('contrato',$sqlcombo,'','N').'</td>			     
				 <td width="50"> <td>
			  </tr>
			  </table>
			  <table width="600">
	           <tr class="BarraDocumentos"> 
				 <td width="60">  <td>	           
			     <td width="170"> <input type="radio" name="formato" value="html" checked> <img src="../images/iconrep.png" width="16" height="16"> Ver en Pantalla </td>
			     <td width="170"> <input type="radio" name="formato" value="pdf"> <img src="../images/iconpdf.png"> Generar en PDF </td>
				 <td width="30">  <td>
			  </tr>
			 </table>
			 <br>
			 <table width="600"> 
              <tr> 
 			     <td align="center"> <input type="submit" value="Ver Reporte"> </td>				 
			  </tr>
			 </table> </form>';
    echo $cont;  
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  


?> 
