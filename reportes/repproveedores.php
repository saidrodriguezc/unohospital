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
    
	$vsql="SELECT * FROM proveedores ";
	if($ordalf == "S")
	  $vsql.= "ORDER BY nombre ASC";
	else
      $vsql.= "ORDER BY nit ASC";	  

	echo'<table border=1>
		   <tr bgcolor="#CCCCCC">
	         <th>Nit</th>
			 <th>Nombre</th>
			 <th>Direccion</th>
			 <th>Telefono</th>
			 <th>Email</th>			 			 
			 <th>Regimen</th>			 			 			 
           </tr>';

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
      echo' <tr>
              <td>'.$row['nit'].'</font></td>
              <td>'.$row['nombre'].'</font></td>			  
              <td>'.$row['direccion'].'</font></td>			  
              <td>'.$row['telefono'].'</font></td>			  
              <td>'.$row['email'].'</font></td>			  			  			  			  
              <td>'.$row['regimen'].'</font></td>			                
            </tr>';
	}
	
	echo'</table>';	
	exit(); 
	
  }

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$ordalf = $_POST['ordalf'];
	
	$vsql="SELECT * FROM proveedores ";
	if($ordalf == "S")
	  $vsql.= "ORDER BY nombre ASC";
	else
      $vsql.= "ORDER BY nit ASC";	  

    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Listado de Proveedores");	

	$cont.= '<script language="JavaScript">
              window.moveTo(20,20);
			  window.resizeTo(900,600);
			</script> <center>';
			
	$cont.='<div id="principal">
	        <table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="30" align="left"> N.I.T. </th>
			 <th width="200" align="left"> Nombre </th>
			 <th width="200" align="left"> Direccion </th>			 
			 <th width="40" align="left"> Telefono</th>
			 <th width="40" align="left"> Correo Electronico </th>			 			 
			 <th width="40"align="left" > Regimen </th>			 
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
                 <td width="10" align="left">'.$row['nit'].'</font></td>
         	     <td width="100" align="left">'.substr($row['nombre'],0,33).'</font></td>			  
                 <td width="20" align="left">'.$row['direccion'].'</font></td>			  
                 <td width="20" align="left">'.$row['telefono'].'</font></td>			  
                 <td width="20" align="left">'.$row['email'].'</font></td>			  			  			  			  
                 <td width="20" align="center">'.$row['regimen'].'</font></td>			  
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
     $cont.= EncabezadoReporte("Listado de Proveedores");		 
    
	 $cont.='<form action="?opcion=ver" method="POST">
	         <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Listado de Proveedores <td>
			  </tr>
			 </table>
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="20"> </td>
			     <td width="550"> <input type="checkbox" name="ordalf"> Ordenar Alfabeticamente </td>
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