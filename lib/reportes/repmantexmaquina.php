<?PHP
  session_start(); 
  include("../lib/Sistema.php");
  include("configreportes.php");  

  $clase = new Sistema();
  $ruta="../";
    
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
 
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "excel")
  {
	
	$ordalf = $_POST['ordalf'];
	
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=archivo.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
    
	$vsql="SELECT * FROM mantenimientos M INNER JOIN terceros T ON (T.terid = M.operariorea) ";

	echo'<table border=1>
		   <tr bgcolor="#CCCCCC">
	         <th>Orden No.</th>
			 <th>Fecha</th>
			 <th>Hora</th>
			 <th>Descripcion</th>	 			 
			 <th>Realiz�</th>			 			 			 
           </tr>';

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
      echo' <tr>
              <td>'.$row['numorden'].'</font></td>
              <td>'.$row['fecha'].'</font></td>			  
              <td>'.$row['hora'].'</font></td>			  
              <td>'.$row['observacion'].'</font></td>			  
              <td>'.$row['nombre'].'</font></td>			  			  			  			  
            </tr>';
	}
	
	echo'</table>';	
	exit(); 
	
  }

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$maquinariaid = $_POST['maquinariaid'];
	$orden = $_POST['orden'];
	
	$nombremaquina = $clase->BDLockup($maquinariaid,"maquinaria","maquinariaid","descripcion");
	
	$vsql="SELECT * FROM mantenimientos M INNER JOIN terceros T ON (T.terid = M.operariorea) ";

	if($orden == "S")
	  $vsql.= "ORDER BY T.nombre ASC";
	else
      $vsql.= "ORDER BY M.fecha DESC , M.hora DESC";	  

    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Mantenimientos Realizados - ".$nombremaquina);	

	$cont.= '<script language="JavaScript">
              window.moveTo(20,20);
			  window.resizeTo(900,600);
			</script> <center>';
			
	$cont.='<div id="principal">
	        <table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="40" align="left"> Orden No. </th>
			 <th width="60" align="left"> Fecha </th>
			 <th width="60" align="left"> Hora </th>			 
			 <th width="160" align="left"> Descripcion </th>
			 <th width="100" align="left"> Realiz� </th>			 			 
           </tr>';

    $i=0;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
       if($i%2 == 0)
	      $cont.= '<tr class="TablaDocsImPar">';
	   else
	      $cont.= '<tr class="TablaDocsPar">';

        $cont.= '<td> &nbsp; </td>			
                 <td width="40" align="left">'.$row['numorden'].'</font></td>
         	     <td width="60" align="left">'.$row['fecha'].'</font></td>			  
                 <td width="60" align="left">'.$row['hora'].'</font></td>			  
                 <td width="160" align="left">'.$row['observacion'].'</font></td>			  
                 <td width="100" align="left">'.$row['nombre'].'</font></td>			  			  			  			  		  
                </tr>';
	  $i++;		
	}
	
	$cont.='</table> </div>';	  
    echo $cont; 
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Mantenimientos Realizados por Maquina");		 
    
	 $cont.='<form action="?opcion=ver" method="POST">
	         <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Mantenimientos Realizados por Maquina <td>
			  </tr>
			 </table>
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="20"> </td>
			     <td width="550" align="center"> Maquina : '.$clase->CrearCombo("maquinariaid","maquinaria","descripcion","maquinariaid",$row['maquinariaid'],"N").' </td>
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="20"> </td>
			     <td width="550" align="center"> 
				    <input type="radio" name="orden" value="FECHA" checked> Ordenar por Fecha 
  				    <input type="radio" name="orden" value="OPERARIO"> Ordenar por Operario	
			 	</td>
				 <td width="50"> <td>
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