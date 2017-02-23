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
    
	$vsql="SELECT * FROM terceros ";
	if($ordalf == "S")
	  $vsql.= "ORDER BY nombre ASC";
	else
      $vsql.= "ORDER BY codigo ASC";	  

	echo'<table border=1>
		   <tr bgcolor="#CCCCCC">
             <th>Codigo</th>
	         <th>Nit</th>
			 <th>Nombre</th>
			 <th>Direccion</th>
			 <th>Telefono</th>
			 <th>Celular</th>
			 <th>Email</th>			 			 
			 <th>Estado</th>			 			 
			 <th>Sueldo</th>			 			 
			 <th>Bonificacion</th>			 			 			 			 			 			 			 			 			 
			 <th>EPS</th>			 			 			 			 			 
			 <th>AFP</th>			 			 			 			 			 
			 <th>CCF</th>			 			 			 			 			 
			 <th>ARP</th>			 			 			 			 			 
			 <th>Tipo Cuenta</th>			 			 			 			 			 
			 <th>Cuenta No</th>			 			 			 			 			 
			 <th>Fecha Ingreso</th>			 			 			 			 			 
			 <th>Fecha Retiro</th>			 			 			 			 			 
			 <th>Fecha Contratacion</th>			 			 			 			 			 			 			 			 			 
           </tr>';

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
      echo' <tr>
              <td>'.$row['codigo'].'</font></td>
              <td>'.$row['nit'].'</font></td>
              <td>'.$row['nombre'].'</font></td>			  
              <td>'.$row['direccion'].'</font></td>			  
              <td>'.$row['telefono'].'</font></td>			  
              <td>'.$row['celular'].'</font></td>			  
              <td>'.$row['email'].'</font></td>			  			  			  			  
              <td>'.$row['estado'].'</font></td>			  			  			  			  
              <td>'.$row['sueldo'].'</font></td>			  			  			  			  
              <td>'.$row['bonificacion'].'</font></td>			  			  			  			  
              <td>'.$row['eps'].'</font></td>			  			  			  			  
              <td>'.$row['afp'].'</font></td>			  			  			  			  
              <td>'.$row['ccf'].'</font></td>			  			  			  			  			  			  			  			  			                
              <td>'.$row['arp'].'</font></td>			  			  			  			  			  			  			  			  			                
              <td>'.$row['tipocuenta'].'</font></td>			  			  			  			  			  			  			  			  			                
              <td>'.$row['cuenta'].'</font></td>			  			  			  			  			  			  			  			  			                	
              <td>'.$row['fecingreso'].'</font></td>			  			  			  			  			  			  			  			  			                
              <td>'.$row['fecretiro'].'</font></td>			  			  			  			  			  			  			  			  			                
              <td>'.$row['feccontrato'].'</font></td>			  			  			  			  			  			  			  			  			                			  			  			  		  			                
            </tr>';
	}
	
	echo'</table>';	
	exit(); 
	
  }


  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "detalles")
  {
	$id = $_GET['id'];
    $conex  = $clase->Conectar();
    $result = mysql_query("SELECT * FROM terceros WHERE terid=".$id,$conex);

	$cont='<table width="500">';
	while($row = mysql_fetch_array($result))
	{
        $cont.='<tr> <td width="100"> <b> NIT </b></td>         <td>'.$row['nit'].'</td> </tr>
                <tr> <td width="100"> <b> Nombre </b></td>    	<td>'.$row['nombre'].'</td> </tr>
                <tr> <td width="100"> <b> Direccion </b></td> 	<td>'.$row['direccion'].'</td> </tr>
                <tr> <td width="100"> <b> Telefono </b></td>  	<td>'.$row['telefono'].'</td> </tr>				                
                <tr> <td width="100"> <b> Celular </b></td>   	<td>'.$row['celular'].'</td> </tr>				                
                <tr> <td width="100"> <b> Email </b></td>     	<td>'.$row['email'].'</td> </tr>				                
                <tr> <td width="100"> <b> Sueldo </b></td>    	<td>'.$row['sueldo'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> Bonificacion </b></td><td>'.$row['bonificacion'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> EPS </b></td>       	<td>'.$row['eps'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> AFP </b></td>    	    <td>'.$row['afp'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> CCF </b></td>     	<td>'.$row['ccf'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> ARP </b></td>     	<td>'.$row['arp'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> Cuenta </b></td>    	<td>'.$row['cuenta'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> TipoCuenta </b></td> 	<td>'.$row['tipocuenta'].'</td> </tr>				                								                
                <tr> <td width="100"> <b> Fecha Ingreso </b></td> 	<td>'.$row['fecingreso'].'</td> </tr>
                <tr> <td width="100"> <b> Fecha Retiro </b></td> 	<td>'.$row['fecretiro'].'</td> </tr>
                <tr> <td width="100"> <b> Fecha Contrato </b></td> 	<td>'.$row['feccontrato'].'</td> </tr>				                
        ';
	  $i++;		
	}
	echo $cont;
  } 
   
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$ordalf = $_POST['ordalf'];
	
	$vsql="SELECT * FROM terceros ";
	if($ordalf == "S")
	  $vsql.= "ORDER BY nombre ASC";
	else
      $vsql.= "ORDER BY codigo ASC";	  

    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Listado de Operarios");	

	$cont.= '<script language="JavaScript">
              window.moveTo(20,20);
			  window.resizeTo(900,600);
			</script> <center>';
			
	$cont.='<div id="principal">
	        <table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
             <th width="30"> Codigo </th>
	         <th width="30" align="left"> N.I.T. </th>
			 <th width="200" align="left"> Nombre </th>
			 <th width="40"> Telefono</th>
			 <th width="40"> Celular</th>
			 <th width="40" align="left"> Correo Electronico </th>			 			 
			 <th width="12"> </th>
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
		         <td width="10" align="center">'.$row['codigo'].'</font></td>
                 <td width="10" align="left">'.$row['nit'].'</font></td>
         	     <td width="100" align="left">'.substr($row['nombre'],0,33).'</font></td>			  
                 <td width="20" align="center">'.$row['telefono'].'</font></td>			  
                 <td width="20" align="center">'.$row['celular'].'</font></td>			  
                 <td width="20" align="left">'.$row['email'].'</font></td>			  			  			  			  
                 <td width="20" align="center"> <a href="?opcion=detalles&id='.$row['terid'].'" rel="facebox"> Detalles </a> </td>	
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
     $cont.= EncabezadoReporte("Listado de Operarios");		 
    
	 $cont.='<form action="?opcion=ver" method="POST">
	         <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Listado de Operarios <td>
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
