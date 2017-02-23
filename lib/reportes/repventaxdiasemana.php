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
  if($opcion == "ver")
  {
	$fecdesde   = $_POST['fecdesde'];
	$fechasta   = $_POST['fechasta'];
	$fuente     = $_POST['fuente'];	

    $fecdesdex = substr($fecdesde,6,4)."-".substr($fecdesde,3,2)."-".substr($fecdesde,0,2);
    $fechastax = substr($fechasta,6,4)."-".substr($fechasta,3,2)."-".substr($fechasta,0,2);    

	if($fuente == "valor")
	{
	  $vsql = "SELECT DATE_FORMAT(fechadoc, '%w') dia , SUM( total ) valor
	  		   FROM documentos
			   WHERE tipodoc = 'FVE' AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00'
			   AND fechadoc >= '".$fecdesdex." 00:00:00' AND fechadoc <= '".$fechastax." 23:59:59' 
			   GROUP BY 1 ASC";
    }
	else
	{
	  $vsql = "SELECT DATE_FORMAT(fechadoc, '%w') dia , COUNT(*) valor
	  		   FROM documentos
			   WHERE tipodoc = 'FVE' AND fecasentado <> '0000-00-00 00:00:00' AND fecanulado = '0000-00-00 00:00:00'
			   AND fechadoc >= '".$fecdesdex." 00:00:00' AND fechadoc <= '".$fechastax." 23:59:59' 
			   GROUP BY 1 ASC";		
	} 

   	$cont = $clase->HeaderReportes();
   	$cont.= EncabezadoReporte2("Informe de Ventas por Dia de la Semana");		 
   	
	if($fuente == "valor")   
   	   $cont.='<b>Informe de Ventas - Valor Vendido por Dias de la Semana</b> <br>';
   	   
	if($fuente == "cantidad")   
   	   $cont.='<b>Informe de Ventas - Cantidad de Facturas por Dias de la Semana</b> <br>';
		  
	$cont.='Desde el <b>'.$fecdesde.'</b> hasta el <b>'.$fechasta.'</b>
	        <br>
	        <img src="graventasdiasemana.php?fecdesde='.$fecdesde.'&amp;fechasta='.$fechasta.'&amp;fuente='.$fuente.'" border="0">
	        <br><br>
			<table width="200">
    	      <tr class="CabezoteTabla"> 
                 <td width="15"> </td>
    		     <td width="100"> <b>Dia</B> </td>
    		     <td width="70" align="right"> <b>Valor </B> </td>
                 <td width="15"> </td>					
			  </tr>
			</table>
	    	<table width="200">';
      
	
	$totalgral = 0;
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))  	
	{
	    
      $dia = $row[0];
      if($dia == 0)
        $dia = 6;
      else
	    $dia -= 1;  

      if($dia == 0)
        $diax = "Lunes";
      if($dia == 1)
        $diax = "Martes";
      if($dia == 2)
        $diax = "Miercoles";
      if($dia == 3)
        $diax = "Jueves";
      if($dia == 4)
        $diax = "Viernes";
      if($dia == 5)
        $diax = "Sabado";
      if($dia == 6)
        $diax = "Domingo";
    
      $datosx[$dia]= $diax;
      $datosy[$dia]= $row['valor'];
     }

     for($i=0;$i<7;$i++)
	 {    

	  if($i%2 == 0)
	    $cont.='<tr class="TablaDocsPar">';
	  else
	    $cont.='<tr class="TablaDocsImPar">';		 
		          
	  $cont.='<td width="15"> </td>
                 <td width="15"> </td>
  	             <td width="85" align="left"> '.$datosx[$i].'</td>
		         <td width="85" align="right">'.number_format($datosy[$i]).'</td>
				 <td width="15"> </td>
			    </tr>';
	
       $totalgral = $totalgral + $datosy[$i]; 
	 }
	 
	$cont.='</table>  
	        <table width="200"> 
			 <tr class="CabezoteTabla"> 
		      <td width="15"> </td>
	          <td width="85" align="left"> <b> Total </b></td>
	          <td width="85" align="right"> <b>'.number_format($totalgral).'</b> </td>
		      <td width="15"> </td>
			 </tr>';    
	$cont.='</table><br><br>';
  }

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Informe de Ventas por Dias de la Semana");		 
    
	 $cont.='<form action="?opcion=ver" method="POST" name="x">
	         <table width="400">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Informe de Ventas por Dias de la Semana <td>
			  </tr>
			 </table>
			 <table width="400">
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="120"> Fecha Desde :  </td>
			     <td width="120"> 
				   <input type="text" name="fecdesde" size="10" value="'.date("d/m/Y").'" id="fecdesde" onClick="popUpCalendar(this, x.fecdesde,\'dd/mm/yyyy\');">
				 </td>			     
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="50">  </td>
			     <td width="120"> Fecha Hasta :  </td>
			     <td width="120"> 
				 <input type="text" name="fechasta" size="10" value="'.date("d/m/Y").'" id="fechasta" onClick="popUpCalendar(this, x.fechasta,\'dd/mm/yyyy\');">
				 </td>			     
				 <td width="50"> <td>
			  </tr>
             </table> 
			 <table width="400"> 
              <tr class="BarraDocumentos"> 
 			     <td align="center">
    		         <input type="radio" name="fuente" value="valor" checked> Valor Vendido
				     <input type="radio" name="fuente" value="cantidad"> Cantidad de Facturas 
				 </td>				 
			  </tr>
              <tr class="BarraDocumentos"> 
 			     <td align="center"> <input type="submit" value="Ver Reporte"> </td>				 
			  </tr>
			 </table> </form>';
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  echo $cont;  

?> 