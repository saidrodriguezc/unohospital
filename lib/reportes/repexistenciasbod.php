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
  if($opcion == "detbodega")
  {
	$bodegaid = $_GET['bodegaid'];
	$nombodega = $clase->BDLockup($bodegaid,"bodegas","bodegaid","descripcion");
	
	$vsql = "SELECT I.itemid, I.descripcion, SUM( E.existencia ) existencia
			 FROM bodegas B
			 INNER JOIN existencias E ON ( B.bodegaid = E.bodegaid ) 
			 INNER JOIN item I ON (I.itemid = E.itemid)
			 WHERE B.bodegaid =".$bodegaid."
			 GROUP BY 1 , 2
			 ORDER BY 1";
			   
    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Detalle de la Bodega <br> <b>".$nombodega."</b>");	
	
	$cont.='<div id="principal">
			<br><br><table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="30" align="center"> Producto </th>
			 <th width="30" align="center"> Existencia </th>
             <th width="10"> &nbsp; </th>					  			 			 
           </tr>';

    $i=0;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	
	$saldo=0;
	
	while($row = mysql_fetch_array($result))
	{

       if($i%2 == 0)
	      $cont.= '<tr class="TablaDocsImPar">';
	   else
	      $cont.= '<tr class="TablaDocsPar">';

        $cont.= '<td width="5" align="center"> &nbsp; </td>
                 <td width="30" align="center"> '.$row['descripcion'].' </td>
                 <td width="30" align="center">'.FormatoNumero($row['existencia']).' </td>
				 <td width="10" align="center"> &nbsp; </td>
                </tr>';
		 
	  $i++;		
	}
	
	$cont.='</table> </div>';	  
  }
 
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	$vsql = "SELECT B.bodegaid , B.descripcion , SUM(E.existencia) existencia
			 FROM bodegas B 
			 INNER JOIN existencias E ON (B.bodegaid = E.bodegaid)
			 GROUP BY 1,2 ORDER BY 1";
			   
    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Existencias por Bodega");	
		
	$cont.='<div id="principal">
	        <br>
            <img src="grabodegacant.php" border="0">
			<br><br>
			<table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
	         <th width="30" align="center"> Bodega </th>
			 <th width="30" align="center"> Existencia </th>
             <th width="10"> &nbsp; </th>					  			 			 
           </tr>';

    $i=0;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	
	$saldo=0;
	
	while($row = mysql_fetch_array($result))
	{

       if($i%2 == 0)
	      $cont.= '<tr class="TablaDocsImPar">';
	   else
	      $cont.= '<tr class="TablaDocsPar">';

        $cont.= '<td width="5" align="center"> &nbsp; </td>
                 <td width="30" align="center"> <a href="?opcion=detbodega&bodegaid='.$row['bodegaid'].'"> '.$row['descripcion'].' </a> </td>
                 <td width="30" align="center">'.FormatoNumero($row['existencia']).' </td>
				 <td width="10" align="center"> &nbsp; </td>
                </tr>';
		 
	  $i++;		
	}
	
	$cont.='</table> </div>';	  
  }
  
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  echo $cont;  

?> 