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
    
	$vsql="SELECT M.* , MM.descripcion desmarca , TM.descripcion destipo FROM maquinaria M
		   INNER JOIN marcamaq MM ON ( M.marca = MM.marcaid ) 
		   INNER JOIN tipomaq TM ON ( M.tipo = TM.tipoid ) ";
	if($ordalf == "S")
	  $vsql.= "ORDER BY descripcion ASC";
	else
      $vsql.= "ORDER BY codigo ASC";	    

	echo'<table border=1>
		   <tr bgcolor="#CCCCCC">
             <th>Codigo</th>
	         <th>Nombre</th>
			 <th>Marca</th>
			 <th>Tipo</th>
			 <th>Modelo</th>
			 <th>Serie</th>
			 <th>Ano</th>			 			 
           </tr>';

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
      echo' <tr>
              <td>'.$row['codigo'].'</font></td>
              <td>'.$row['descripcion'].'</font></td>
              <td>'.$row['desmarca'].'</font></td>			  
              <td>'.$row['destipo'].'</font></td>			  
              <td>'.$row['modelo'].'</font></td>			  
              <td>'.$row['serie'].'</font></td>			  
              <td>'.$row['ano'].'</font></td>			  			  			  			  
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
	
	$vsql="SELECT M.* , MM.descripcion desmarca , TM.descripcion destipo FROM maquinaria M
		   INNER JOIN marcamaq MM ON ( M.marca = MM.marcaid ) 
		   INNER JOIN tipomaq TM ON ( M.tipo = TM.tipoid ) ";
	if($ordalf == "S")
	  $vsql.= "ORDER BY descripcion ASC";
	else
      $vsql.= "ORDER BY codigo ASC";	  

    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Listado de Maquinaria");	
			
	$cont.='<div id="principal">
	        <table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="30" align="left"> Codigo </th>
			 <th width="150" align="left"> Nombre </th>
			 <th width="40" align="left"> Marca</th>
			 <th width="40" align="left"> Tipo </th>
			 <th width="40" align="left"> Modelo </th>			 			 
			 <th width="40" align="left"> Serie </th>			 			 			 
			 <th width="40" align="left"> A&ntilde;o </th>			 			 			 			 
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
                 <td width="10" align="left">'.$row['descripcion'].'</font></td>
         	     <td width="100" align="left">'.$row['desmarca'].'</font></td>			  
                 <td width="20" align="left">'.$row['destipo'].'</font></td>			  
                 <td width="20" align="left">'.$row['modelo'].'</font></td>			  
                 <td width="20" align="left">'.$row['serie'].'</font></td>			  			  			  			  
                 <td width="20" align="left">'.$row['ano'].'</font></td>			  			  			  			  				 
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
     $cont.= EncabezadoReporte("Listado de Maquinaria");		 
    
	 $cont.='<form action="?opcion=ver" method="POST">
	         <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Listado de Maquinaria <td>
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