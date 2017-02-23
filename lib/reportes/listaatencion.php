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
	$username = $_GET['username'];	

	$vsql = "SELECT PAC.terid , PAC.nit , PAC.nombre , PAC.rutafoto , HC.momento FROM historiacli HC INNER JOIN TERCEROS PAC ON (PAC.terid = HC.teridpaciente) 
			 INNER JOIN TERCEROS PRF ON (PRF.terid = HC.teridprof) 
			 WHERE PRF.username = '".$username."' AND (HC.momento >= '".$desde." 00:00:00' AND HC.momento <= '".$desde." 23:29:00')
			 ORDER BY 5 ASC ";

   	$cont = $clase->HeaderReportes();
   	$cont.= EncabezadoReporte2("Orden de Atencion de Pacientes");		 
   	$cont.='<center>
	        <b>Orden de Atencion a Pacientes</b> <br>
	        Fecha : <b>'.$desde.'</b> 
	        <br><br>
			<table width="850">
    	      <tr class="CabezoteTabla"> 
                 <td width="15">  </td>
                 <td width="20"><b> Foto </b></td>				 
    		     <td width="60"> <b>DOCUMENTO</B> </td>
    		     <td width="180"><b>NOMBRE DEL PACIENTE</B> </td>
				 <td width="150"> <b>FECHA / HORA LLEGADA</B></td>		 
                 <td width="15"> </td>					
			  </tr>
			</table>
			
	    	<table width="850">';

    $totalgral = 0;  

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))  	
	{
		$i++;
		if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		else
		   $cont.='<tr class="TablaDocsImPar">';		     
		
		$cont.='<td width="15"> </td>
		         <td width="20" align="center"> 
				    <a href="../fotos/'.$row['rutafoto'].'" rel="facebox">
					<img src="../fotos/'.$row['rutafoto'].'" width="40" height="40"></a></td>
  	             <td width="60">'.$row['nit'].'</td>
		         <td width="180">'.$row['nombre'].'</td>
		         <td width="150" align="right">'.$row['momento'].'</td>
				 <td width="15"> </td>
			    </tr>';
	    $totalgral++;	    
	}
	$cont.='</table>  
	        <table width="850"> 
			 <tr class="CabezoteTabla"> 
		      <td width="15"> </td>
	          <td width="360"> <b> Total Pacientes </b></td>
	          <td width="50" align="right"> <b>'.number_format($totalgral).'</b> </td>
		      <td width="17"> </td>
			 </tr>';    
	$cont.='</table></div><br><br>';
	
 	echo $cont;
  }

  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$formato = $_POST['formato'];
	
	$fdX     = $_POST['fecdesde'];
	$fhX     = $_POST['fechasta'];
	
	$desde = substr($fdX,6,4)."-".substr($fdX,3,2)."-".substr($fdX,0,2);
	$username = $_POST['username'];

    ////////////////////////////////////////
	// Visualizacion del Reporte
	////////////////////////////////////////
    if($formato == 'html')
      header("Location: ?opcion=enpantalla&desde=".$desde."&username=".$username);	
   
	if($formato == 'pdf')
      header("Location: ?opcion=enpdf&desde=".$desde."&username=".$username);	       
      
  } // Fin de Ver
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Orden de Atencion a Pacientes");		 
    
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	         <script language="javascript">

              function tipofecha()
			  {
			    var fecnaci = document.x.fecdesde.value;
				if(fecnaci.length == 2) 
				  document.x.fecdesde.value = document.x.fecdesde.value + "/"; 
				if(fecnaci.length == 5) 
				  document.x.fecdesde.value = document.x.fecdesde.value + "/"; 
				return;
			  }
             </script> 
			 <form action="?opcion=ver" method="POST" name="x">
			 <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Orden de Atencion a Pacientes <td>
			  </tr>
			 </table>
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="80"> Dia Atencion :  </td>
			     <td width="160"> 
				   <input type="text" name="fecdesde" size="10" value="'.date("d/m/Y").'" id="fecdesde" onKeypress="tipofecha();">
				 </td>			     
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="80"> Profesional :  </td>
			     <td width="160"><input type="text" name="username" size="10" value="'.$_SESSION['USERNAME'].'" readonly></td>			     
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
