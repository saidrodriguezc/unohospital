<?PHP
  session_start(); 
  include("lib/Sistema.php");
  include("lib/libdocumentos.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  /////////////////////////////////////////  
  if($opcion == "imprimirorden")
  {  	  
	 $docuid  = $_GET['id'];
	 require('lib/fpdf/fpdf.php');
	 $pdf = new FPDF();
	 $pdf2 = new FPDF();
	 
	 $pdf->AddPage();   
	 
	 $vsql = "SELECT tipodoc , prefijo , numero , fechadoc , fecasentado , fecanulado , base , iva ,  total , 
	          nomvendedor , codcliente , nomcliente , dircliente , telcliente , codproducto , 
	          nomproducto , valunitario , observacion , SUM(cantidad) cantidad ,  SUM(valparcial) valparcial 
			  FROM v_ventas 
			  WHERE docuid=".$docuid."
			  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";
     
	 $conex   = $clase->Conectar();
     $result  = mysql_query($vsql,$conex);
	 $i=0;
   	 
	 while($row = mysql_fetch_array($result))
	 {
	   $tipodoc = $row['tipodoc'];
	   $numero = $row['numero'];
	   $fecha  = substr($row['fechadoc'],8,2)." / ".substr($row['fechadoc'],5,2)." / ".substr($row['fechadoc'],0,4);
	   $hora   = substr($row['fechadoc'],11,2).":".substr($row['fechadoc'],14,2);
	   $observacion = $row['observacion'];
	   
	   $nomcliente = $row['nomcliente'];
	   $codcliente = $row['codcliente'];
	   $dircliente = $row['dircliente'];
	   $telcliente = $row['telcliente'];
	   
	   $base       = $row['base'];
	   $iva        = $row['iva'];
	   $total      = $row['total'];
	   
	   $detalle[$i].= str_pad($row['cantidad'],5," ",STR_PAD_LEFT);
	   $detalle[$i].= str_pad("",10," ",STR_PAD_LEFT);
	   $detalle[$i].= str_pad($row['nomproducto'],58," ",STR_PAD_RIGHT);
	   $i++;
	 }
	 
     $pdf->Image('images/logoempresa.jpg' , 10 ,5, 80 , 20,'JPG', '');
	 
	 $pdf->SetFont('Arial','B',13);	 
     $pdf->Text(100,12,"ORDEN DE SERVICIOS No");	       	            $pdf->Text(160,12,$numero);	 
	 $pdf->SetFont('Arial','B',9);	 
	 $pdf->Text(100,17,"Cll 3a No. 1E-09 La Ceiba - Telefono Fijo : 5893154");	 	 
     $pdf->Text(100,21,"Celular : 311 820 6068 - email : saludempresarialips@gmail.com");	 	 

	 // Informacion del Paciente
	 $pdf->SetFont('Arial','B',13);             $pdf->Text(67,33,"INFORMACION DEL PACIENTE");
	 $pdf->Rect(10,27,193,21);	 
	 $pdf->SetFont('Arial','B',10);	 
     $pdf->Text(13,38,"NOMBRE : ");	 	        $pdf->Text(33,38,$nomcliente);	 
     $pdf->Text(105,38,"NIT / CEDULA : ");	    $pdf->Text(132,38,$codcliente);	 
     $pdf->Text(13,42,"DIRECCION : ");	 	    $pdf->Text(33,42,$dircliente);
     $pdf->Text(105,42,"TELEFONO : ");	        $pdf->Text(132,42,$telcliente);	 
     $pdf->Text(13,46,"FECHA : ");	 	        $pdf->Text(33,46,$fecha);
     $pdf->Text(105,46,"DATOS LABORALES : ");	$pdf->Text(132,46,$observacion);	 

	 // Informacion del Prestador
	 $pdf->SetFont('Arial','B',13);             $pdf->Text(55,57,"INFORMACION DEL PRESTADOR DEL SERVICIO");
	 $pdf->Rect(10,50,193,21);	 
	 $pdf->SetFont('Arial','B',12);	 
     $pdf->Text(13,64,"LABORATORIO CLINICO : Dra. Adriana Castañeda");	 
	 $pdf->SetFont('Arial','B',11);	 
     $pdf->Text(13,68,"Cll. 5 No. 0-10E B. La Ceiba  Cel. 310 873 2399 - 311 606 0646  Tel. 577 4350");	 
	 
	 $pdf->Rect(10,73,193,10);	 
	 $pdf->SetFont('Arial','B',13);             $pdf->Text(60,80,"SERVICIOS Y/O EXAMENES A REALIZAR");

	 $pdf->SetFont('Arial','B',11);
	 $y = 89;
	 for($j=0 ; $j< $i ; $j++)
	 {
	   $pdf->Text(16,$y,$detalle[$j]);
	   $y = $y+5;
	 }  	 
      
	 // Pie de la Orden 
	 $pdf->Rect(10,120,193,10);	 
	 $pdf->SetFont('Arial','B',10);	 
     $pdf->Text(45,124,"Cll 3a No. 1E-09 La Ceiba - Telefono Fijo : 5893154 - Celular : 311 820 6068");	 	 
     $pdf->Text(80,128,"saludempresarialips@gmail.com");
	 
	 $pdf->Output();
  }	


  /////////////////////////////////////////  
  if($opcion == "importarpse2")
  {  	  
    
	$docuid  = $_POST['docuid'];	
	$empresaid = $clase->SeleccionarUno('SELECT T.terid FROM terceros T INNER JOIN documentos D ON (D.terid1 = T.terid) WHERE D.docuid='.$docuid);   

	echo'
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="css/estilo.css" type="text/css">
    <body leftmargin="0" topmargin="0" rightmargin="0" bottonmargin="0" OnLoad="document.x.default.focus();"> ';
			
	$vsql   = "SELECT DISTINCT PS.docuid
			   FROM documentos PS
			   INNER JOIN contratos C ON ( C.contratoid = PS.contratoid )
			   INNER JOIN terceros CON ON ( CON.terid = C.terid )
			   INNER JOIN terceros PAC ON ( PAC.terid = PS.terid1 )
			   WHERE PS.tipodoc = 'PSE' AND PS.docfacturadoid = '' AND C.terid = ".$empresaid;

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 
 
    while($row = mysql_fetch_array($result))
	{
	 	$valor = $_POST['ID_'.$row['docuid']];
		if($valor == 'CHECKED')
		   $clase->EjecutarSQL("UPDATE documentos SET docfacturadoid ='".$docuid."' WHERE docuid=".$row['docuid']);
 
    }
	
	// Borro los detalles de la Factura de Venta
	$clase->EjecutarSQL('DELETE FROM dedocumentos WHERE docuid='.$docuid);
	
	
	$vsql   = "SELECT DISTINCT DDPS.itemid , DDPS.valunitario , SUM(DDPS.cantidad) cantidad FROM documentos PS 
			   INNER JOIN dedocumentos DDPS ON (DDPS.docuid = PS.docuid)
			   INNER JOIN contratos C ON ( C.contratoid = PS.contratoid ) 
			   INNER JOIN terceros CON ON ( CON.terid = C.terid ) 
			   INNER JOIN terceros PAC ON ( PAC.terid = PS.terid1 ) 
			   WHERE PS.tipodoc = 'PSE' AND PS.docfacturadoid = '' AND C.terid = ".$empresaid." GROUP BY 1,2";

	$result2 = mysql_query($vsql,$conex); 
 
    while($row2 = mysql_fetch_array($result2))
	{
		 $itemid      = $row2['itemid'];
		 $valunitario = $row2['valunitario'];
		 $cantidad    = $row2['cantidad'];
		 
		 $valparcial = $valunitario*$cantidad;
		 
		 $vsql2="INSERT INTO dedocumentos(docuid,itemid,bodegaid,cantidad,valunitario,valparcial,valbase) 
		         VALUES(".$docuid.",".$itemid.",1,".$cantidad.",".$valunitario.",".$valparcial.",".$valparcial.")";
         $clase->EjecutarSQL($vsql2);
    }
	
    echo'<center><br><br>
	     <h2>Prestaciones Facturadas Exitosamente</h2>
		 <br><br>
		 <a href="" target="_blank">Ver Relacion de Prestaciones</a>
		 <br><br><br>
		 <a href="#" OnClick="window.opener.location.reload(); window.close();"> <b>Cerrar Ventana</b> </a>';
		 
	exit();	 	
  }




  /////////////////////////////////////////  
  if($opcion == "importarpse")
  {  	  
    
	$docuid  = $_GET['id'];	
	$tipodoc = $clase->BDLockup($docuid,'documentos','docuid','tipodoc');
	
	echo $tipodoc;
	
	if($tipodoc != 'FVE')
	{
	   echo'<h2> No se pueden Importar Prestaciones de Servicio a otra Prestacion</h2>
	        <h3> Solo se permite esta opcion con Facturas de Venta</h3>';
	   exit();		
	}
	else
	{
	$empresaid = $clase->SeleccionarUno('SELECT T.terid FROM terceros T INNER JOIN documentos D ON (D.terid1 = T.terid) WHERE D.docuid='.$docuid);   

	echo'
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="css/estilo.css" type="text/css">
    <body leftmargin="0" topmargin="0" rightmargin="0" bottonmargin="0" OnLoad="document.x.default.focus();"> ';
			
	$vsql   = "SELECT DISTINCT PS.fechadoc , PS.tipodoc, PS.prefijo, PS.numero, CON.nombre empresa, PAC.nit Cedula, PAC.nombre paciente , PS.docuid
			   FROM documentos PS
			   INNER JOIN contratos C ON ( C.contratoid = PS.contratoid )
			   INNER JOIN terceros CON ON ( CON.terid = C.terid )
			   INNER JOIN terceros PAC ON ( PAC.terid = PS.terid1 )
			   WHERE PS.tipodoc = 'PSE' AND PS.docfacturadoid = '' AND C.terid = ".$empresaid." ORDER BY 1 ASC , 2 ASC , 3 ASC , 4 ASC";

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 
    $cant = mysql_num_rows($result); 
    
	if($cant > 0)
	{

     $cont.='<form action="?opcion=importarpse2" method="POST" name="x">
	          <input type="hidden" name="docuid" value="'.$docuid.'">
	          <center><font face="Verdana" size="2"><br><b> Seleccionar las Prestaciones de Servicio a Insertar <br> ('.$cant.') </b> Pendientes de Facturar <br><br>
	           
			 <table width="780">
			   <tr class="TablaDocsImPar">
                <td width="10"> </td>
	 		    <td width="30"> <input type="checkbox"> </td> 
				<td width="100"> <b>Documento </td> 
				<td width="90"> <b>Fecha </td> 				
			    <td width="250"> <b>Empresa </td> 
			    <td width="280"> <b>Paciente </td> 				
				<td> <b>Visualizar</td> 
			    <td width="15"> </td>
			   </tr>';		 		

	 $i = 0;	
	 while($row = mysql_fetch_array($result))
	 {
	   $i++;
	   if($i%2 == 0)
	     $cont.='<tr class="TablaDocsImPar">';
	   else
	     $cont.='<tr class="TablaDocsPar">';		   

 	   $cont.='<td width="10"> </td>
	 		    <td width="30"> <input type="radio" name="ID_'.$row['docuid'].'" value="CHECKED"> </td> 
				<td width="100"> '.$row['tipodoc'].$row['prefijo'].$row['numero'].' </td> 
				<td width="90"> '.FormatoFecha($row['fechadoc']).' </td> 				
			    <td width="250"> '.substr($row['empresa'],0,28).'</td> 
			    <td width="280"> '.substr($row['paciente'],0,35).'</td> 				
				<td> <a href="ventas.php?opcion=imprimirprestacion&id='.$row['docuid'].'" target="_blank">Visualizar</a> </td> 
			    <td width="15"> </td>
			   </tr>';		 		
	 }
	 $cont.='</table><br><center>
	         <input type="submit" value="Facturar estas Prestaciones"></form><br><br>';
	 echo $cont;
     exit();		
	}
	else
	  echo"No hay prestaciones para este Cliente"; 
   }
  }
  
  
  /////////////////////////////////////////  
  if(($opcion == "imprimirventa")||($opcion == "imprimirprestacion"))
  {  	  
	 $docuid  = $_GET['id'];
	 require('lib/fpdf/fpdf.php');
	 $pdf = new FPDF();
	 $pdf2 = new FPDF();
	 
	 $pdf->AddPage();   
	 
	 $vsql = "SELECT tipodoc , prefijo , numero , fechadoc , fecasentado , fecanulado , base , iva ,  total , 
	          nomvendedor , codcliente , nomcliente , dircliente , telcliente , codproducto , 
	          nomproducto , valunitario , observacion , SUM(cantidad) cantidad ,  SUM(valparcial) valparcial 
			  FROM v_ventas 
			  WHERE docuid=".$docuid."
			  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";
     
	 $conex   = $clase->Conectar();
     $result  = mysql_query($vsql,$conex);
	 $i=0;
   	 while($row = mysql_fetch_array($result))
	 {
	   $tipodoc = $row['tipodoc'];
	   $numero = $row['numero'];
	   $fecha  = substr($row['fechadoc'],8,2)." / ".substr($row['fechadoc'],5,2)." / ".substr($row['fechadoc'],0,4);
	   $hora   = substr($row['fechadoc'],11,2).":".substr($row['fechadoc'],14,2);
	   $observacion = $row['observacion'];
	   
	   $nomcliente = $row['nomcliente'];
	   $codcliente = $row['codcliente'];
	   $dircliente = $row['dircliente'];
	   $telcliente = $row['telcliente'];
	   
	   $base       = $row['base'];
	   $iva        = $row['iva'];
	   $total      = $row['total'];
	   
	   $detalle[$i].= str_pad($row['cantidad'],5," ",STR_PAD_LEFT);
	   $detalle[$i].= str_pad("",13," ",STR_PAD_LEFT);
	   $detalle[$i].= str_pad($row['nomproducto'],58," ",STR_PAD_RIGHT);
	   $detalle2[$i].= str_pad(number_format($row['valunitario']),19," ",STR_PAD_LEFT);
	   $detalle2[$i].= str_pad(number_format($row['valparcial']),44," ",STR_PAD_LEFT);
	   $i++;
	 }
	 
     $pdf->Image('images/logoempresa.jpg' , 10 ,5, 80 , 23,'JPG', '');
	 
	 $pdf->SetFont('Arial','B',11);	 
     if($tipodoc == 'FVE')
	    $pdf->Text(135,16,"FACTURA DE VENTA No");	 
     else
	    $pdf->Text(123,16,"PRESTACION DE SERVICIO No");	 	 
		
	 $pdf->Text(183,16,$numero);	 
	 
	 $pdf->SetFont('Arial','B',9);	 
     $pdf->Text(150,20,"NIT : 900443363-4");	 
	 $pdf->Text(125,24,"Cll 3a No. 1E-09 La Ceiba  Tel. 5893154");	 
	 
	 $pdf->Line(10,30,202,30);	 

	 $pdf->SetFont('Arial','B',8);	 
     $pdf->Text(10,34,"HABILITACIÓN FACTURACIÓN SEGÚN RESOLUCION No. 70000119431");	 
	 $pdf->Text(10,37,"FECHA DE RESOLUCION 2015/02/02");	 
	 $pdf->Text(10,40,"AUTORIZACION DEL No. 5001 a la 10000");	 

     //$pdf->Text(115,34,"Tenemos la condición de pequeña empresa de acuerdo con lo");	 
	 //$pdf->Text(115,37,"establecido en el Art. 2 de la ley 1429 del 2.010 y en el Art. 1");	 
	 //$pdf->Text(115,40,"del Dec. 545 de 2.011");	 

	 // Informacion de la Factura
	 $pdf->Rect(10,42,193,19);	 
	 $pdf->SetFont('Arial','B',9);	 
     $pdf->Text(13,47,"FECHA");	                $pdf->Text(35,47,$fecha);	 
     $pdf->Text(13,52,"NOMBRE");	 	        $pdf->Text(35,52,$nomcliente);	 
	 $pdf->Text(13,57,"DIRECCION");	 	        $pdf->Text(35,57,$dircliente);	 

	 $pdf->Text(115,47,"HORA");	                $pdf->Text(142,47,$hora);	 
     $pdf->Text(115,52,"NIT / CEDULA");	 	    $pdf->Text(142,52,$codcliente);	 
	 $pdf->Text(115,57,"TELEFONO");	 	        $pdf->Text(142,57,$telcliente);	  

	 $pdf->Rect(10,63,193,7);	 
	 $pdf->SetFont('Arial','B',10);	 
     $pdf->Text(16,68,"CANT");	 
     $pdf->Text(35,68,"DESCRIPCION");	 
     $pdf->Text(135,68,"VR UNIT");	 
     $pdf->Text(180,68,"VR TOTAL");	 	 
	 
	 $y = 77;
	 for($j=0 ; $j< $i ; $j++)
	 {
	   $pdf->Text(16,$y,$detalle[$j]);
	   $pdf->Text(125,$y,$detalle2[$j]);
	   $y = $y+4;
	 }  
	 
     $V=new EnLetras();
     $pdf->Text(10,200,"SON : ".$V->ValorEnLetras($total,"pesos"));
	 
     /// Cuadro de Totales
	 $pdf->Rect(10,205,193,19);	 
     $pdf->Rect(140,205,25,19);	 
     $pdf->Line(140,212,203,212);	 
	 $pdf->Line(140,218,203,218);	 
	 $pdf->SetFont('Arial','B',10);	 
     $pdf->Text(142,210,"SUBTOTAL");	       $pdf->Text(180,210,str_pad(number_format($base),12," ",STR_PAD_LEFT));	       
	 $pdf->Text(142,216,"I.V.A.");	           $pdf->Text(180,216,str_pad(number_format($iva),12," ",STR_PAD_LEFT));	       
	 $pdf->Text(142,222,"TOTAL");	           $pdf->Text(180,222,str_pad(number_format($total),12," ",STR_PAD_LEFT));	     

	 $pdf->SetFont('Arial','B',9);	      
     //$pdf->Text(12,209,"Favor abstenerse de realizar retención en la fuente");	 
     $pdf->Text(12,213,"Esta factura se asimila en todos sus efectos a una letra de cambio según el");	 	 
     $pdf->Text(12,217,"Art. 774 del Codigo de Comercio.");	 	 	 
	 $pdf->Text(12,222,"OBSERV : ");           $pdf->Text(30,222,$observacion);	 	 	 	 
	 
	 /// Pie de Pagina      	 
	 $pdf->Text(12,228,"Original");	 	 
	 $pdf->Line(12,237,57,237);	 
	 $pdf->Text(12,240,"Firma");	 
	 $pdf->Image("images/firmagerente.png",9,225,50,20);
	 
	 $pdf->Line(150,237,195,237);	 	 
	 $pdf->Text(150,240,"Recibido");	 
	
	 // Pie con datos direccion y telefono 
	 if($tipodoc == 'FVE')
	 {
	   $pdf->SetFont('Arial','B',12);
	   $pdf->Text(10,250,"Favor Consignar Banco de Bogota Cuenta Corriente No. 260-042635 Salud Empresarial IPS SAS");	 
	 }
	 $pdf->Line(10,251,202,251);	 	 
	 $pdf->SetFont('Arial','B',9);
	 $pdf->Text(58,255,"Cll 3A #1E-09 La Ceiba  Tels. 5751016  -  5893154  Cúcuta - Colombia");	 
	 $pdf->Text(41,259,"Sitio Web : www.saludempresarialips.com      E-mail : saludempresarialips2@gmail.com");	  	 
	 
	 if($tipodoc == 'FVE')
	 {
	  $pdf->AddPage();
	 
	  $pdf->Image('images/logoempresa.jpg' , 10 ,5, 80 , 23,'JPG', '');
	 
	  $pdf->SetFont('Arial','B',11);	 
      if($tipodoc == 'FVE')
	    $pdf->Text(135,16,"FACTURA DE VENTA No");	 
      else
	    $pdf->Text(123,16,"PRESTACION DE SERVICIO No");	 	 
	  $pdf->Text(183,16,$numero);	 
	 
	  $pdf->SetFont('Arial','B',9);	 
      $pdf->Text(150,20,"NIT : 900443363-4");	 
	  $pdf->Text(125,24,"Cll 3a No. 1E-09 La Ceiba  Tel. 5893154");	 
	  
	  $pdf->Line(10,30,202,30);	 

	 $pdf->SetFont('Arial','B',8);	 
     $pdf->Text(10,34,"HABILITACIÓN FACTURACIÓN SEGÚN RESOLUCION No. 70000119431");	 
	 $pdf->Text(10,37,"FECHA DE RESOLUCION 2015/02/02");	 
	 $pdf->Text(10,40,"AUTORIZACION DEL No. 5001 a la 10000");	 

     
	 //$pdf->Text(115,34,"Tenemos la condición de pequeña empresa de acuerdo con lo");	 
	 //$pdf->Text(115,37,"establecido en el Art. 2 de la ley 1429 del 2.010 y en el Art. 1");	 
	 //$pdf->Text(115,40,"del Dec. 545 de 2.011");	 

	 // Informacion de la Factura
	 $pdf->Rect(10,42,193,19);	 
	 $pdf->SetFont('Arial','B',9);	 
     $pdf->Text(13,47,"FECHA");	                $pdf->Text(35,47,$fecha);	 
     $pdf->Text(13,52,"NOMBRE");	 	        $pdf->Text(35,52,$nomcliente);	 
	 $pdf->Text(13,57,"DIRECCION");	 	        $pdf->Text(35,57,$dircliente);	 

	 $pdf->Text(115,47,"HORA");	                $pdf->Text(142,47,$hora);	 
     $pdf->Text(115,52,"NIT / CEDULA");	 	    $pdf->Text(142,52,$codcliente);	 
	 $pdf->Text(115,57,"TELEFONO");	 	        $pdf->Text(142,57,$telcliente);	  

	 $pdf->Rect(10,63,193,7);	 
	 $pdf->SetFont('Arial','B',10);	 
     $pdf->Text(16,68,"CANT");	 
     $pdf->Text(35,68,"DESCRIPCION");	 
     $pdf->Text(135,68,"VR UNIT");	 
     $pdf->Text(180,68,"VR TOTAL");	 	 
	 
	 $y = 77;
	 for($j=0 ; $j< $i ; $j++)
	 {
	   $pdf->Text(16,$y,$detalle[$j]);
	   $pdf->Text(125,$y,$detalle2[$j]);
	   $y = $y+4;
	 }  
	 
     /// Cuadro de Totales
	 $pdf->Rect(10,205,193,19);	 
     $pdf->Rect(140,205,25,19);	 
     $pdf->Line(140,212,203,212);	 
	 $pdf->Line(140,218,203,218);	 
	 $pdf->SetFont('Arial','B',10);	 

     $V=new EnLetras();
     $pdf->Text(10,200,"SON : ".$V->ValorEnLetras($total,"pesos"));

     $pdf->Text(142,210,"SUBTOTAL");	       $pdf->Text(180,210,str_pad(number_format($base),12," ",STR_PAD_LEFT));	       
	 $pdf->Text(142,216,"I.V.A.");	           $pdf->Text(180,216,str_pad(number_format($iva),12," ",STR_PAD_LEFT));	       
	 $pdf->Text(142,222,"TOTAL");	           $pdf->Text(180,222,str_pad(number_format($total),12," ",STR_PAD_LEFT));	     

	 $pdf->SetFont('Arial','B',9);	      
     //$pdf->Text(12,209,"Favor abstenerse de realizar retención en la fuente");	 
     $pdf->Text(12,213,"Esta factura se asimila en todos sus efectos a una letra de cambio según el");	 	 
     $pdf->Text(12,217,"Art. 774 del Codigo de Comercio.");	 	 	 
	 $pdf->Text(12,222,"OBSERV : ");           $pdf->Text(30,222,$observacion);	 	 	 	 
	 
	 /// Pie de Pagina      	 
	 $pdf->Text(12,228,"Copia");	 	 
	 $pdf->Line(12,237,57,237);	 
	 $pdf->Text(12,240,"Firma");	 
	 $pdf->Image("images/firmagerente.png",9,225,50,20);	 
	 $pdf->Line(150,237,195,237);	 
	 $pdf->Text(150,240,"Recibido");	 
	
	 // Pie con datos direccion y telefono 
	 $pdf->SetFont('Arial','B',12);
	 $pdf->Text(10,250,"Favor Consignar Banco de Bogota Cuenta Corriente No. 260-042635 Salud Empresarial IPS SAS");	 
	 $pdf->Line(10,251,202,251);	 	 
	 $pdf->SetFont('Arial','B',9);
	 $pdf->Text(58,255,"Cll 3A #1E-09 La Ceiba  Tels. 5751016  -  5893154  Cúcuta - Colombia");	 
     $pdf->Text(41,259,"Sitio Web : www.saludempresarialips.com      E-mail : saludempresarialips2@gmail.com");	  	 
	 }
	 $pdf->Output();
  }	
  
  /////////////////////////////////////////  
  if($opcion == "analisisexistencias")
  {  	  
	 $docuid  = $_GET['id'];
	 $vsql = "SELECT DISTINCT I.descripcion nomprod, B.descripcion nombod, E.existencia , SUM(DD.cantidad) cant
			  FROM existencias E INNER JOIN dedocumentos DD ON ( DD.itemid = E.itemid ) 
			  INNER JOIN bodegas B ON (B.bodegaid = DD.bodegaid)
			  INNER JOIN item I ON (DD.itemid = I.itemid) WHERE  E.bodegaid = DD.bodegaid AND DD.docuid =".$docuid." GROUP BY 1,2,3";

	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
	 $cont = '<center> <h3> Analisis de Existencias </h3>
	          <table width="600">
	           <tr class="TablaDocsPar">
			    <td width="10">&nbsp;</td>
			    <td width="180"> <b> Producto </b> </td>
				<td width="100"> <b> Bodega </b> </td>
				<td> <b> En Bodega </b> </td>
				<td> <b> Cant a Vender </b> </td>
				<td> <b> Saldo </b> </td>	
			    <td width="20">&nbsp;</td>							
			   </tr>';
	 $i=0; 		   	
     while($row = mysql_fetch_array($result))
     { 
       $saldo = $row['existencia'] - $row['cant'];
	   
	   if($i%2==0)
	     $cont.= '<tr class="TablaDocsImPar">';
	   else 
	     $cont.= '<tr class="TablaDocsPar">';	   
       
	   $cont.='<td> &nbsp; </td>
	           <td> '.$row['nomprod'].' </td>
       	       <td> '.$row['nombod'].' </td>
       	       <td align="center"> '.$row['existencia'].' </td>			   
       	       <td align="center"> '.$row['cant'].' </td>
       	       <td align="center"> '.$saldo.' </td>';

	   if($saldo >= 0)
	   		$cont.='<td align="center"> <img src="images/instactiva.png" border="0"> </td>';   			   
	   else
	   		$cont.='<td align="center"> <img src="images/instinactiva.png" border="0"> </td>';   			   
				   		
   	  $cont.='</tr>';
	  $i++;
	}		   			   			   
	$cont.='</table><br><br>';
	   
	echo $cont;  
	exit();
  }

  /////////////////////////////////////////  
  if($opcion == "preasentarventa")
  {  	  
	 $docuid  = $_GET['id'];
     $vsql = "SELECT * FROM mediospago ORDER BY codigo ASC";
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);

	 $cont='<table width="400">
	           <tr class="CabezoteTabla"> 
				 <td align="center"> <b> Forma de Pago </b> <td> 
			   </tr> 
			</table>
		    <table width="400">';    
	 while($row = mysql_fetch_array($result))
  	 {
	    $cont.='<tr class="BarraDocumentos" valign="middle"> 
			      <td width="20"> &nbsp; </td>
				  <td align="left"> 
				    <a href="ventas.php?opcion=asentarventa&mp='.$row['mediopagoid'].'&id='.$docuid.'">
					  <img src="images/iconos/'.$row['icono'].'.png" border="0" width="24" height="24"></a> 
					 '.$row['descripcion'].' 
				  </td>
				  <td width="40">
				    <a href="ventas.php?opcion=asentarventa&mp='.$row['mediopagoid'].'&id='.$docuid.'">
					  <img src="images/icononuevo.png" border="0"></a> 
			      </td>		
                </tr>';
	 }		  
	 $cont.='</table>';
	 echo $cont;		  
 	 exit(0);	 
  }
  
  /////////////////////////////////////////  
  if($opcion == "asentarventa")
  {  	  
	 $docuid  = $_GET['id'];
	 $prefijo = $clase->BDLockup($docuid,"documentos","docuid","prefijo");
	 $mediopago = $_GET['mp'];
	 
	 /// Inserto la Forma de Pago de la Factura
	 $tieneformapago = $clase->BDLockup($docuid,"formapagodoc","docuid","mediopagoid");

	 if($mediopago != "")
	 {
	   $valorpagado = $clase->BDLockup($docuid,"documentos","docuid","total");
	   if($tieneformapago == "")
 		   $clase->EjecutarSQL("INSERT INTO formapagodoc(docuid,mediopagoid,valor,creador) values(".$docuid.",".$mediopago.",".$valorpagado.",'".$_SESSION['USUARIO']."')");	     
	   else	 
		   $clase->EjecutarSQL("UPDATE formapagodoc SET mediopagoid=".$mediopago." ,valor=".$valorpagado.",creador='".$_SESSION['USUARIO']."' WHERE docuid=".$docuid);
	 }

     $vsql = "SELECT D.numero , D.prefijo , D.tipodoc , P.consecutivo FROM documentos D, prefijo P 
	          WHERE D.prefijo = P.prefijo AND D.tipodoc = P.tipodoc AND D.docuid=".$docuid;
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $row = mysql_fetch_array($result);
	 $numero = $row['numero'];
 	 $consecact = $row['consecutivo'];
	 $consecnuevo = $consecact + 1;
       
 	 $fecha   = date("Y-m-d").' 00:00:00';	 	 
	 $vsql = "UPDATE documentos SET fecasentado = '".$fecha."' WHERE docuid=".$docuid;	 
	 $clase->EjecutarSQL($vsql);       
        
	if(substr($numero,0,2) == "T-")
	{ 
	      $consecnuevo2 = str_pad($consecnuevo,4,"0",STR_PAD_LEFT);
		  $vsql = "CALL PA_ActualizarConsecutivoVenta('".$row['prefijo']."','".$row['tipodoc']."',".$docuid.",'".$consecnuevo2."')";	 
	      $clase->EjecutarSQL($vsql);
	}	
	    
    header("Location: documentos.php");
    exit();
  }

  /////////////////////////////////////////  
  if($opcion == "reversarventa")
  {  	  
	 $docuid  = $_GET['id'];
	 $numero = $clase->BDLockup($docuid,"documentos","docuid","numero");
	 $prefijo = $clase->BDLockup($docuid,"documentos","docuid","prefijo");	 
	 
	 $Consumoid = $clase->SeleccionarUno("SELECT docuid FROM documentos WHERE tipodoc='CON' AND prefijo='".$prefijo."' AND numero='".$numero."'");
	 $vsql = "DELETE FROM dedocumentos WHERE docuid=".$Consumoid;	 
	 $clase->EjecutarSQL($vsql);
	 $vsql = "DELETE FROM documentos WHERE docuid=".$Consumoid;	 
	 $clase->EjecutarSQL($vsql);
	 
	 $fecha   = '0000-00-00 00:00:00';	 	 	 
	 $vsql = "UPDATE documentos SET fecasentado = '".$fecha."' WHERE docuid=".$docuid;	 
	 $docuid = $clase->EjecutarSQL($vsql);
	 
     header("Location: documentos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "preanularventa")
  {  	  
       $docuid  = $_GET['id'];
	   echo'<table width="400" bgcolor="#FF9966"> 
	           <tr height="25">
				 <td align="center"> <b> Est&aacute; seguro de Anular el Documento? </b> </td> 
			   </tr> <tr height="25"> 
				  <td align="center"> Este proceso es Irreversible </td> 
			   </tr> <tr height="25"> 
			      <td align="center"> <b><a href="ventas.php?opcion=anularventa&amp;id='.$docuid.'"> Anular Documento de Venta</a> </b> </td> 
			   </tr>
			</table>';
		exit();		
  }
  /////////////////////////////////////////  
  if($opcion == "anularventa")
  {  	  
	 $docuid  = $_GET['id'];
	 $fecha   = date("Y-m-d").' 00:00:00';
	 	 	 
	 $vsql = "UPDATE documentos SET fecasentado = '".$fecha."' , fecanulado = '".$fecha."' ,
	          base = 0 , iva = 0 , total = 0 , totalitems = 0 WHERE docuid=".$docuid;	 
	 $docuid = $clase->EjecutarSQL($vsql);
	 
	 $vsql = "UPDATE dedocumentos SET cantidad = 0 , valunitario = 0 , valdescuento = 0 ,valparcial = 0 , porciva = 0 , 
	          valbase = 0 , valiva = 0 WHERE docuid=".$docuid;	 
	 $docuid = $clase->EjecutarSQL($vsql);

	 // Registro en el LOG de Auditoria el Borrado
     $clase->CrearLOG('010','Se anula el Documento',strtoupper($_SESSION["USERNAME"]),$vsql,$docuid);
	 
     header("Location: documentos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "preeliminarventa")
  {  	  
       $docuid  = $_GET['id'];
	   
	   echo'<table width="400" bgcolor="#FF9966"> <tr height="25"> 
				    <td align="center"> <b> Est&aacute; seguro de Eliminar el Documento? </b> </td> 
			     </tr> <tr height="25"> 
				    <td align="center"> Este proceso es Irreversible </td> 
			     </tr> <tr height="25"> 
				    <td align="center"> <b><a href="ventas.php?opcion=eliminarventa&amp;id='.$docuid.'"> Eliminar Documento de Venta </a> </b> </td> 
			     </tr> </table>';
		exit();		
  }
  /////////////////////////////////////////  
  if($opcion == "eliminarventa")
  {  	  
	 $docuid  = $_GET['id'];
	 $fecha   = date("Y-m-d").' 00:00:00';	 	 	 
	 $vsql = "DELETE FROM dedocumentos WHERE docuid=".$docuid;	 
	 $clase->EjecutarSQL($vsql);
	 
	 $vsql = "DELETE FROM documentos WHERE docuid=".$docuid;	 	 
	 $clase->EjecutarSQL($vsql);

	 // Registro en el LOG de Auditoria el Borrado
     $clase->CrearLOG('006','Se elimina el Documento',strtoupper($_SESSION["USERNAME"]),$vsql,$docuid);

 	 $clase->Aviso(3,"Documento Eliminado Exitosamente");  		 	 
     header("Location: documentos.php");
  }
  
  /////////////////////////////////////////  
  if($opcion == "guardarencabezado")
  {  	  
	 $docuid   = $_POST['id'];
	 $tipodoc  = $_POST['tipodoc'];
	 $prefijo  = $_POST['prefijo'];
     $numero   = $_POST['numero'];
	 $fecha    = FechaMySQL($_POST['fecha'])." ".date("H:i:s");	 	 
	 $cliente  = $_POST['cliente'];	 	 
	 $vendedor = $_POST['vendedor'];	 	 	 
	 $observ   = $_POST['observ'];	 	 

     $terid1  = $clase->BDLockup($cliente,"terceros","codigo","terid");
     $terid2  = $clase->BDLockup($vendedor,"terceros","codigo","terid");	 
  	 	 	 
	 $vsql = "UPDATE documentos SET tipodoc = '".$tipodoc."' , prefijo = '".$prefijo."' ,
	          numero = '".$numero."' , fechadoc = '".$fecha."' , terid1 = '".$terid1."' ,
			  terid2 = '".$terid2."' , observacion = '".$observ."' 
			  WHERE docuid=".$docuid;	

	 // Registro en el LOG de Auditoria el Acceso
     $clase->CrearLOG('005','Se modifica el encabezado del Documento',strtoupper($_SESSION["USERNAME"]),$vsql,$docuid);

 	 $clase->Aviso(1,"Documento Guardado Exitosamente");  	
	 $clase->EjecutarSQL($vsql);
	 header("Location: ventas.php?opcion=editarventa&id=".$docuid);
  }
    
  /////////////////////////////////////////  
  if($opcion == "nuevaventa")
  {
     $tipop    = "FVE";
	 $prefijop = "00";
	 $numerop  = "T-".rand(100000,999999);
	 $fechap   = date("d/m/Y");
	 $clientepred  = $clase->BDLockup($_SESSION['U_CLIENTEPRED'],"terceros","codigo","terid");
	 $vendedorpred = $clase->BDLockup($_SESSION['U_VENDEDORPRED'],"terceros","codigo","terid");
	 $creador  = $_SESSION['USERNAME'];	 
	 
	 $vsql = "INSERT INTO documentos(tipodoc,prefijo,numero,fechadoc,terid1,terid2,observacion,base,iva,total,creador,momento,contratoid) 
	          values('".$tipop."','".$prefijop."','".$numerop."',CURRENT_TIMESTAMP,".$clientepred.",".$vendedorpred.",'',0,0,0,'".$creador."',CURRENT_TIMESTAMP,6)";
	 $clase->EjecutarSQL($vsql);
	 
	 $docuid = $clase->SeleccionarUno("SELECT Max(docuid) FROM documentos");
	 
	 // Registro en el LOG de Auditoria la creacion de la Venta 
     $clase->CrearLOG('004','Se Crea nuevo documento '.$tipop.$prefijop.$numerop.' con Fecha '.$fechap,strtoupper($_SESSION["USERNAME"]),'',$docuid);
	 
	 header("Location: ?opcion=editarventa&id=".$docuid);
  }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  

  if($opcion == "editardetalle")
  {  	  
     $id = $_GET['id'];
	 $ddid = $_GET['dedocumid'];
	 
	 $vsql='SELECT DD.dedocumid , DD.cantidad , P.descripcion producto, B.descripcion bodega , DD.bodegaid , DD.valunitario 
			FROM dedocumentos DD
			INNER JOIN item P ON (P.itemid = DD.itemid)
			INNER JOIN bodegas B ON (B.bodegaid = DD.bodegaid)
			WHERE dedocumid='.$ddid;

	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);

     if($row = mysql_fetch_array($result))
     { 
        $cantidad = $row['cantidad']; 
        $producto = $row['producto']; 
        $bodegaid = $row['bodegaid'];	 
        $precio   = $row['valunitario'];	         
	 }
	 
	 $cont = $clase->SoloCSS();
	 $cont.='<center>
	   <form action="?opcion=guardardetalle" method="POST">
	   <input type="hidden" name="docuid" value="'.$id.'">
	   <input type="hidden" name="dedocumid" value="'.$ddid.'">	   
	   <table width="400">
	     <tr>
	       <td> Producto : </td>
	       <td> <input type="text" name="cantidad" value="'.$producto.'" size="32" disabled></td>
	     </tr>  
	     <tr>
	       <td> Cantidad : </td>
	       <td> <input type="text" name="cantidad" value="'.$cantidad.'" size="7" id="default"> </td>
	     </tr>  
	     <tr>
	       <td> Precio : </td>
	       <td> <input type="text" name="precio" value="'.$precio.'" size="7"> </td>
	     </tr>  	     
	     <tr>
	       <td> Bodega : </td>
	       <td>'.$clase->CrearCombo("bodegaid","bodegas","descripcion","bodegaid",$bodegaid,"N").'</td>
	     </tr>  
	     <tr>
	       <td> </td>
	       <td> <input type="submit" name="submit" value="Modificar Productos"> </td>
	     </tr>  	     
	   </table>
	   </form>';
	 echo $cont;
	 exit();  
  }  

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardardetalle")
  {  	  
     $id       = $_POST['docuid'];
	 $ddid     = $_POST['dedocumid'];
	 $cantidad = $_POST['cantidad'];
	 $precio   = $_POST['precio'];	 
	 $bodegaid = $_POST['bodegaid'];	 	 
	 
	 $vsql = "UPDATE dedocumentos SET cantidad =".$cantidad." , bodegaid =".$bodegaid." ,
	          valunitario=".$precio." WHERE dedocumid=".$ddid;
	 $docuid = $clase->EjecutarSQL($vsql);
	 
     // Registro en el LOG de Auditoria el Acceso
	 $NomProducto = $clase->SeleccionarUno("SELECT I.descripcion nombre FROM item I INNER JOIN dedocumentos DD ON (DD.itemid = I.itemid) WHERE DD.dedocumid=".$ddid);
     $TextoLog = 'Se Modifica el detalle del Documento. Producto : '.$NomProducto.' - Cantidad : '.$cantidad.' - Precio : '.$precio.
	             ' - Cantidad de Productos : '.$cantidades.' - Valor Total de la Factura : '.$totales;
     $clase->CrearLOG('005',$TextoLog,strtoupper($_SESSION["USERNAME"]),$vsql1,$id);
 
     header("Location: ventas.php?opcion=editarventa&id=".$id);	 
  }  

  /////////////////////////////////////////  
  if($opcion == "eliminardetalle")
  {  	  
     $id = $_GET['id'];
	 $ddid = $_GET['dedocumid'];
	 $vsql = "DELETE FROM dedocumentos WHERE dedocumid=".$ddid;
	 $docuid = $clase->EjecutarSQL($vsql);
     header("Location: ventas.php?opcion=editarventa&id=".$id);	 
  }
   	  
  /////////////////////////////////////////  
  if($opcion == "editarventa")
  {  	  
     $id = $_GET['id'];
     $_SESSION["DOCUID"] = $id;	 
	 $cont = $clase->Header("N","W"); 
	
	 //Barras Superiores de Documentos e Insercion y Acciones Masivas
	 $cont.='<script language="javascript">
	          document.getElementById("productos").contentDocument.body.focus();
			 </script>';
			   
	 $cont.= BarraDocumentos();
	 $cont.= SegundaBarraEditarDoc($id);

     // Extraigo Datos desde MySQL
     $vsql = "SELECT * FROM V_ventas WHERE docuid=".$id;
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);

     $i=0;
     $detalles = '';
 
     while($row = mysql_fetch_array($result))
     { 
       $tipop   = "FVE";
       $tipodoc = $row['tipodoc'];
	   $prefijo = $row['prefijo'];
	   $numero  = $row['numero'];
  	   $fecha   = FormatoFecha($row['fechadoc']);
 	   $cliente  = $row['codcliente'];
 	   $vendedor = $row['codvendedor'];	   
 	   $base     = $row['base'];	   
 	   $iva      = $row['iva'];	   	   	   
 	   $total    = $row['total'];	   	   	   	   
       $items    = $row['totalitems'];
	   $observ   = $row['observacion'];	   	   	   
	    
	   if($i%2==0)
	     $detalles.= '<tr class="TablaDocsImPar">';
	   else 
	     $detalles.= '<tr class="TablaDocsPar">';	   
       
	   $detalles.='  <td width="10"> &nbsp; </td>
   	   		         <td width="20"> <a href="?opcion=eliminardetalle&id='.$id.'&dedocumid='.$row['dedocumid'].'">
					                 <img src="images/nofiltro.png" border="0" width="12" height="12"></a> </td>
   	   		         <td width="20"> <a href="?opcion=editardetalle&id='.$id.'&dedocumid='.$row['dedocumid'].'" rel="facebox">
					                 <img src="images/iconoeditar.png" border="0" width="12" height="12"></a> </td>
				     <td width="80" align="left">'.$row['codproducto'].'</td>			
		 		     <td width="140"> <label class="Texto11"> '.substr($row['nomproducto'],0,50).' </label> </td>			
					 <td width="50" align="right"> '.$row['cantidad'].' </td>
					 <td width="80" align="right"> '.$row['codbodega'].' </a></td>
				 	 <td width="75" align="right"> '.FormatoNumero($row['valparcial']).' </td>	
		             <td width="10"> &nbsp; </td>
					</tr>';
	   $i++;
    }     

    // Division para Ingreso de Documentos
	$cont.='<table width="950">   <tr valign="top">  <td width="550">';
	 
  	// Formulacio Insertar Documentos
	$cont.='<form action="?opcion=guardarencabezado" method="POST" name="x">
	        <input type="hidden" name="id" value="'.$id.'"> 
	        <table widht="627">
	         <tr valign="middle" class="EncabezadoDocu" height="33"> 
			   <td width="7"> &nbsp; </td>
			   <td width="83"> Documento : </td> 
			   <td width="220"> <select name="tipodoc">'; 
			   if($tipodoc == 'FVE')
			    $cont.='<option value="FVE" selected>FVE</option> 
                        <option value="RSA">RSA</option> 					   
					    <option value="PSE">PSE</option>';
			   if($tipodoc == 'RSA')
			    $cont.='<option value="FVE">FVE</option> 
                        <option value="RSA" selected>RSA</option> 					   
					    <option value="PSE">PSE</option>';
			   if($tipodoc == 'PSE')
			    $cont.='<option value="FVE">FVE</option> 
                        <option value="RSA">RSA</option> 					   
					    <option value="PSE" selected>PSE</option>';
						
			   $cont.='</select> 
			        <input type="text" size="2" name="prefijo" value="'.$prefijo.'">
					<input type="text" size="7" name="numero" value="'.$numero.'"> 
			   </td>			
  			   <td width="7"> &nbsp; </td>
			   <td width="56"> Fecha : </td>
			   <td width="162"> <input type="text" name="fecha" size="10" value="'.$fecha.'" id="fechadoc" onClick="popUpCalendar(this, x.fechadoc,\'dd/mm/yyyy\');"> </td> 
              </tr>
             </table>
		 
			<table widht="627">
			 <tr valign="middle" class="EncabezadoDocu" height="25"> 
			   <td width="7"> &nbsp; </td>
			   <td width="83"> Paciente : </td> 
			   <td width="180"> 
			   

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework7.js"></script>
<style type="text/css">
#search-wrap7 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res7{width:150px; border:solid 1px #DEDEDE; display:none;}
#res7 ul, #res7 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res7 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res7 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res7 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res7 li a:hover{background:#FFFFFF;}
#res7 ul {padding:4px;}
</style>
<div id="search-wrap7">
<input name="cliente" id="search-q7" type="text" onkeyup="javascript:autosuggest7();" maxlength="12" size="15" tabindex="5" value="'.$cliente.'"/>
<div id="res7"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			   			  
			   </td>			   
			   <td width="20"> &nbsp; </td>
			   <td width="83"> Atendido por : </td> 
			   <td width="163"> 
			   
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework8.js"></script>
<style type="text/css">
#search-wrap8 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res8{width:150px; border:solid 1px #DEDEDE; display:none;}
#res8 ul, #res8 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res8 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res8 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res8 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res8 li a:hover{background:#FFFFFF;}
#res8 ul {padding:4px;}
</style>
<div id="search-wrap8">
<input name="vendedor" id="search-q8" type="text" onkeyup="javascript:autosuggest8();" maxlength="12" size="15" autocomplete="off" tabindex="5" value="'.$vendedor.'"/>
<div id="res8"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			   
			   </td>
			 </tr>   
	         </table>
			 
			 <table widht="627">
			 <tr valign="middle" class="EncabezadoDocu" height="58"> 
			   <td width="7"> &nbsp; </td>
			   <td width="100"> Observacion </td> 
			   <td width="496"> <textarea name="observ" cols="49" rows="2">'.$observ.'</textarea> </td>
			 </tr>		 
		   </table>
		   </form>
	   
		   <table widht="100%">
			 <tr valign="middle" class="TotalesDocumentos"> 
			   <td width="65%">
			     
				 <table width="100%">
				   <tr height="25" class="TotalesDocumentos"> 
				    <td width="10"> &nbsp; </td>
		  		    <td width="60" align="right"> Dctos : &nbsp;</td>
					<td width="80" align="right"> 0 </td>
			        <td width="20"> &nbsp; </td>			    
			        <td width="100" align="right"> Prods : &nbsp; </td>
			        <td width="70" align="right"> '.$items.' Und  </td>
			        <td width="30"> &nbsp; </td>			   
		           </tr>   
  			       <tr valign="middle" class="TotalesDocumentos" height="25"> 
			        <td width="10"> &nbsp; </td>
			        <td width="60" align="right"> Base : &nbsp;</td>
			        <td width="80" align="right">  '.FormatoNumero($base).' </td>
			        <td width="20"> &nbsp; </td>			    
			        <td width="100" align="right"> I.V.A. : &nbsp; </td>
			        <td width="70" align="right"> '.FormatoNumero($iva).' </td>
			        <td width="30"> &nbsp; </td>			   
		           </tr>   
	              </table>
               </td>
			   <td align="center">
			     <h1>'.FormatoNumero($total).'</h1>
			  </td>
			</tr>
		   </table>	   	 		   
			    	
		   <table width="100%">
		   <tr class="BarraDocumentos">
		     <td width="10"> &nbsp; </td>
		     <td width="35"> &nbsp; </td>			 
			 <td width="80"> <b>Código</b> </td>
			 <td width="150"> <b>Producto</b> </td>			 
			 <td width="50" align="right">  <b>Cant</b> </td>
			 <td width="80" align="center">  <b>Bodega</b> </td>
		 	 <td width="60" align="right">  <b>Precio</b> </td>	
		     <td width="22"> &nbsp; </td>			 
		   </tr>
		  </table>
		  
<div id=scrolltable style=" background: #FFFFFF; overflow:auto;
padding-right: 0px; padding-top: 0px; padding-left: 0px; padding-bottom: 0px;
border-right: #6699CC 0px solid; border-top: #999999 0px solid;
border-left: #6699CC 0px solid; border-bottom: #6699CC 0px solid;
scrollbar-arrow-color : #999999; scrollbar-face-color : #666666;
scrollbar-track-color :#3333333 ; position: absolute;
height:400px; width:535px">   
		  
		  <table width="518">
		   '.$detalles.'</table></div>';

	// Lateral con informacion de Productos
	$cont.='  </td> 
	          <td width="1"> &nbsp; </td>
	          <td align="left" width="400"> 
                 <iframe id="productos" src="ayudadocumentos.php?id='.$id.'" frameborder="0" scrollbars="auto" width="410" height="600"> </iframe>
  	     	  </td>  
		    </tr>   
	  	  </table>';			
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
  
  
  
  function ImpresionPOS($docuid)
  {
     $clase = new Sistema();
	 
	 /// Cargo las configuraciones del Prefijo del Documento
	 $tipodoc = $clase->BDLockup($docuid,"documentos","docuid","tipodoc");
	 $prefijo = $clase->BDLockup($docuid,"documentos","docuid","prefijo");	  
	 
     $vsql = "SELECT * FROM prefijo WHERE tipodoc='".$tipodoc."' AND prefijo='".$prefijo."'";
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     
	 if($row = mysql_fetch_array($result))
	 {
	    $encab1 = $row['encab1'];	
	    $encab2 = $row['encab2'];	
	    $encab3 = $row['encab3'];				    
	    $encab4 = $row['encab4'];				    
	    $encab5 = $row['encab5'];				    			    
	    
		$pie1 = $row['pie1'];	    
		$pie2 = $row['pie2'];	    
		$pie3 = $row['pie3'];	    
		$pie4 = $row['pie4'];	    
		$pie5 = $row['pie5'];	    
	 }    
	 
	 /// Hago la Consulta de los datos de la Factura 		
	 $vsql = "SELECT tipodoc , prefijo , numero , fechadoc , fecasentado , fecanulado , base , iva ,  total , nomvendedor , codcliente , nomcliente , codproducto , 
	          nomproducto , valunitario , SUM(cantidad) cantidad ,  SUM(valparcial) valparcial 
			  FROM v_ventas 
			  WHERE docuid=".$docuid."
			  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15";

	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     
	 if($encab1!= "")
       $cont = $encab1.Chr(13).Chr(10);
	 if($encab2!= "")
	   $cont.= $encab2.Chr(13).Chr(10);
 	 if($encab3!= "")
	   $cont.= $encab3.Chr(13).Chr(10);
 	 if($encab4!= "")
	   $cont.= $encab4.Chr(13).Chr(10);
 	 if($encab5!= "")
	   $cont.= $encab5.Chr(13).Chr(10);	 	 	 	 

	 $cont.= Chr(13).Chr(10);
	 	 
 	 while($row = mysql_fetch_array($result)){ 
 	   $prefijo = $row['prefijo'];
 	   $numero  = $row['numero'];
 	   $fecha   = substr($row['fechadoc'],8,2)."/".substr($row['fechadoc'],5,2)."/".substr($row['fechadoc'],0,4);
 	   $hora    = substr($row['fechadoc'],11,2).":".substr($row['fechadoc'],14,2); 	   
 	   $total   = $row['total']; 
 	   
   	   $cliente  = substr($row['nomcliente'],0,24); 
   	   $vendedor = substr($row['nomvendedor'],0,24); 
 	   
 	   $detalles.= str_pad(substr($row['nomproducto'],0,20),23," ",STR_PAD_RIGHT).'  ';
	   $detalles.= str_pad($row['cantidad'],3," ",STR_PAD_LEFT).'  '.str_pad(FormatoNumero($row['valparcial']),9," ",STR_PAD_LEFT).Chr(13).Chr(10); 	   
     } 

     $cont.= str_pad("TURNO : ".substr($numero,(strlen($numero)-2),strlen($numero))." ",39," ",STR_PAD_LEFT).Chr(13).Chr(10);	 	 	 	   
     
	 $cont.='FACTURA DE VENTA '.Chr(13).Chr(10);
	 $cont.='No.'.$prefijo.' '.$numero.Chr(13).Chr(10);
	 $cont.='Fecha : '.$fecha.'      Hora : '.$hora.Chr(13).Chr(10).Chr(13).Chr(10);
	 
	 $cont.='CLIENTE : '.$cliente.Chr(13).Chr(10);
	 $cont.='VENDEDOR: '.$vendedor.Chr(13).Chr(10).Chr(13).Chr(10);	 
     
     $cont.= '---------------------------------------'.Chr(13).Chr(10);          
     $cont.= 'PRODUCTO                CANT      TOTAL'.Chr(13).Chr(10);
     $cont.= '---------------------------------------'.Chr(13).Chr(10);     
	 $cont.= $detalles;	 
	 $cont.= '                            -----------'.Chr(13).Chr(10);     
	 $cont.= '              TOTAL '.$_SESSION['G_MONEDALOCAL'].'   '.str_pad(FormatoNumero($total),11," ",STR_PAD_LEFT).Chr(13).Chr(10);     	 
	 $cont.= '                            ==========='.Chr(13).Chr(10).Chr(13).Chr(10);     
	 
	 $cont.= 'FORMA DE PAGO'.Chr(13).Chr(10);
	 $cont.= 'EFECTIVO '.str_pad(FormatoNumero($total),30," ",STR_PAD_LEFT).Chr(13).Chr(10).Chr(13).Chr(10);
	 
	 if($pie1!= "")
	   $cont.= $pie1.Chr(13).Chr(10);
	 if($pie2!= "")
	   $cont.= $pie2.Chr(13).Chr(10);
	 if($pie3!= "")
	   $cont.= $pie3.Chr(13).Chr(10);
	 if($pie4!= "")
	   $cont.= $pie4.Chr(13).Chr(10);
	 if($pie5!= "")
	   $cont.= $pie5.Chr(13).Chr(10);	 	 	 	 
	 
     $cont.= Chr(13).Chr(10).str_pad("www.1Uno.co",39," ",STR_PAD_LEFT).Chr(13).Chr(10);	 	 	 	   
     $cont.= Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10).Chr(13).Chr(10);
     $cont.= '.';     
	 
	 // Genero el Archivo para Enviarlo a Impresora
	 $archivo= "print/fichero.txt"; // el nombre de tu archivo
     $fch= fopen($archivo, "w"); // Abres el archivo para escribir en él
     fwrite($fch, $cont); // Grabas
     fclose($fch); // Cierras el archivo
	 
  } // Fin de la Funcion
  
  class EnLetras
{
  var $Void = "";
  var $SP = " ";
  var $Dot = ".";
  var $Zero = "0";
  var $Neg = "Menos";
  
function ValorEnLetras($x, $Moneda ) 
{
    $s="";
    $Ent="";
    $Frc="";
    $Signo="";
        
    if(floatVal($x) < 0)
     $Signo = $this->Neg . " ";
    else
     $Signo = "";
    
    if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales
      $s = number_format($x,2,'.','');
    else
      $s = number_format($x,0,'.','');
       
    $Pto = strpos($s, $this->Dot);
        
    if ($Pto === false)
    {
      $Ent = $s;
      $Frc = $this->Void;
    }
    else
    {
      $Ent = substr($s, 0, $Pto );
      $Frc =  substr($s, $Pto+1);
    }

    if($Ent == $this->Zero || $Ent == $this->Void)
       $s = "Cero ";
    elseif( strlen($Ent) > 7)
    {
       $s = $this->SubValLetra(intval( substr($Ent, 0,  strlen($Ent) - 6))) . 
             "Millones " . $this->SubValLetra(intval(substr($Ent,-6, 6)));
    }
    else
    {
      $s = $this->SubValLetra(intval($Ent));
    }

    if (substr($s,-9, 9) == "Millones " || substr($s,-7, 7) == "Millón ")
       $s = $s . "de ";

    $s = $s . $Moneda;

    if($Frc != $this->Void)
    {
       $s = $s . " Con " . $this->SubValLetra(intval($Frc)) . "Centavos";
       //$s = $s . " " . $Frc . "/100";
    }
    return ($Signo . $s . " MCTE");
   
}


function SubValLetra($numero) 
{
    $Ptr="";
    $n=0;
    $i=0;
    $x ="";
    $Rtn ="";
    $Tem ="";

    $x = trim("$numero");
    $n = strlen($x);

    $Tem = $this->Void;
    $i = $n;
    
    while( $i > 0)
    {
       $Tem = $this->Parte(intval(substr($x, $n - $i, 1). 
                           str_repeat($this->Zero, $i - 1 )));
       If( $Tem != "Cero" )
          $Rtn .= $Tem . $this->SP;
       $i = $i - 1;
    }

    
    //--------------------- GoSub FiltroMil ------------------------------
    $Rtn=str_replace(" Mil Mil", " Un Mil", $Rtn );
    while(1)
    {
       $Ptr = strpos($Rtn, "Mil ");       
       If(!($Ptr===false))
       {
          If(! (strpos($Rtn, "Mil ",$Ptr + 1) === false ))
            $this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
          Else
           break;
       }
       else break;
    }

    //--------------------- GoSub FiltroCiento ------------------------------
    $Ptr = -1;
    do{
       $Ptr = strpos($Rtn, "Cien ", $Ptr+1);
       if(!($Ptr===false))
       {
          $Tem = substr($Rtn, $Ptr + 5 ,1);
          if( $Tem == "M" || $Tem == $this->Void)
             ;
          else          
             $this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
       }
    }while(!($Ptr === false));

    //--------------------- FiltroEspeciales ------------------------------
    $Rtn=str_replace("Diez Un", "Once", $Rtn );
    $Rtn=str_replace("Diez Dos", "Doce", $Rtn );
    $Rtn=str_replace("Diez Tres", "Trece", $Rtn );
    $Rtn=str_replace("Diez Cuatro", "Catorce", $Rtn );
    $Rtn=str_replace("Diez Cinco", "Quince", $Rtn );
    $Rtn=str_replace("Diez Seis", "Dieciseis", $Rtn );
    $Rtn=str_replace("Diez Siete", "Diecisiete", $Rtn );
    $Rtn=str_replace("Diez Ocho", "Dieciocho", $Rtn );
    $Rtn=str_replace("Diez Nueve", "Diecinueve", $Rtn );
    $Rtn=str_replace("Veinte Un", "Veintiun", $Rtn );
    $Rtn=str_replace("Veinte Dos", "Veintidos", $Rtn );
    $Rtn=str_replace("Veinte Tres", "Veintitres", $Rtn );
    $Rtn=str_replace("Veinte Cuatro", "Veinticuatro", $Rtn );
    $Rtn=str_replace("Veinte Cinco", "Veinticinco", $Rtn );
    $Rtn=str_replace("Veinte Seis", "Veintiseís", $Rtn );
    $Rtn=str_replace("Veinte Siete", "Veintisiete", $Rtn );
    $Rtn=str_replace("Veinte Ocho", "Veintiocho", $Rtn );
    $Rtn=str_replace("Veinte Nueve", "Veintinueve", $Rtn );

    //--------------------- FiltroUn ------------------------------
    If(substr($Rtn,0,1) == "M") $Rtn = "Un " . $Rtn;
    //--------------------- Adicionar Y ------------------------------
    for($i=65; $i<=88; $i++)
    {
      If($i != 77)
         $Rtn=str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
    }
    $Rtn=str_replace("*", "a" , $Rtn);
    return($Rtn);
}


function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr)
{
  $x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
}


function Parte($x)
{
    $Rtn='';
    $t='';
    $i='';
    Do
    {
      switch($x)
      {
         Case 0:  $t = "Cero";break;
         Case 1:  $t = "Un";break;
         Case 2:  $t = "Dos";break;
         Case 3:  $t = "Tres";break;
         Case 4:  $t = "Cuatro";break;
         Case 5:  $t = "Cinco";break;
         Case 6:  $t = "Seis";break;
         Case 7:  $t = "Siete";break;
         Case 8:  $t = "Ocho";break;
         Case 9:  $t = "Nueve";break;
         Case 10: $t = "Diez";break;
         Case 20: $t = "Veinte";break;
         Case 30: $t = "Treinta";break;
         Case 40: $t = "Cuarenta";break;
         Case 50: $t = "Cincuenta";break;
         Case 60: $t = "Sesenta";break;
         Case 70: $t = "Setenta";break;
         Case 80: $t = "Ochenta";break;
         Case 90: $t = "Noventa";break;
         Case 100: $t = "Cien";break;
         Case 200: $t = "Doscientos";break;
         Case 300: $t = "Trescientos";break;
         Case 400: $t = "Cuatrocientos";break;
         Case 500: $t = "Quinientos";break;
         Case 600: $t = "Seiscientos";break;
         Case 700: $t = "Setecientos";break;
         Case 800: $t = "Ochocientos";break;
         Case 900: $t = "Novecientos";break;
         Case 1000: $t = "Mil";break;
         Case 1000000: $t = "Millón";break;
      }

      If($t == $this->Void)
      {
        $i = $i + 1;
        $x = $x / 1000;
        If($x== 0) $i = 0;
      }
      else
         break;
           
    }while($i != 0);
   
    $Rtn = $t;
    Switch($i)
    {
       Case 0: $t = $this->Void;break;
       Case 1: $t = " Mil";break;
       Case 2: $t = " Millones";break;
       Case 3: $t = " Billones";break;
    }
    return($Rtn . $t);
}

}

  
  
?>


