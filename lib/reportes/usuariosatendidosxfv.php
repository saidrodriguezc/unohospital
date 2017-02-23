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
  if($opcion == "ver")
  {
  	 $docuid  = $_GET['docuid'];
	 require('../lib/fpdf/fpdf.php');
	 $pdf = new FPDF();
	 $pdf2 = new FPDF();
	 
	 $pdf->AddPage();   
	 
     $pdf->Image('../images/logoempresa.jpg' , 10 ,5, 80 , 20,'JPG', '');
	 
	 $pdf->SetFont('Arial','B',12);	 
     $pdf->Text(100,14,"PRESTACIONES DE SERVICIO FACTURADAS");
	 $pdf->SetFont('Arial','B',9);	 


	 // Informacion del Paciente
	 $pdf->Rect(10,28,193,6);
	 $pdf->SetFont('Arial','B',9);             
	 $pdf->Text(12,32,"ORDEN No.    FECHA        DATOS DEL PACIENTE                         EXAMEN       EMPRESA                          CARGO");
	 	 
	 $pdf->SetFont('Arial','',8);	 
	 $y = 38;


     // Extraigo los datos para mostrarlos en el reporte
	 $vsql = "SELECT T.nit codcliente, T.nombre nomcliente, C.nombre nomempresa , D.* FROM documentos D INNER JOIN terceros T ON ( D.terid1 = T.terid )
			  LEFT JOIN terceros C ON (D.nitempresa = C.nit) 
			  WHERE docfacturadoid =".$docuid." ORDER BY tipodoc ASC , prefijo ASC , numero ASC";
	 
	 $conex   = $clase->Conectar();
     $result  = mysql_query($vsql,$conex);

	 while($row = mysql_fetch_array($result))
	 {
	   $pdf->Text(12,$y,$row['tipodoc']." ".$row['prefijo']." ".$row['numero']);
	   $pdf->Text(33,$y,substr($row['fechadoc'],8,2)."/".substr($row['fechadoc'],5,2)."/".substr($row['fechadoc'],0,4));	   
	   $pdf->Text(51,$y,substr($row['nomcliente'],0,32));
	   $pdf->Text(108,$y,$row['tipoexamem'],0,10);	 
	   $pdf->Text(128,$y,substr($row['nomempresa'],0,20));	     
	   $pdf->Text(167,$y,substr($row['cargo'],0,19));	     	   
	   $y = $y+4;
	 }  	 
 

	 // Pie de la Orden 
	 $pdf->Rect(10,283,193,9);	 
	 $pdf->SetFont('Arial','B',8);	 
     $pdf->Text(55,287,"Cll 3a No. 1E-09 La Ceiba - Telefono Fijo : 5893154 - Celular : 311 820 6068");	 	 
     $pdf->Text(80,290,"saludempresarialips@gmail.com");
     $pdf->Text(184,288,"Pag 1 de 1");	 
	 
	 $pdf->Output();	 
	 
  }
   
  
  ////////////////////////////////////////////////    
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Seleccione ");		 
    
	 $cont.='<form action="?opcion=ver" method="POST">
	         <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Historial de la Maquinaria <td>
			  </tr>
			 </table>
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="20"> </td>
			     <td width="550" align="center"> Maquina : '.$clase->CrearCombo("maquinariaid","maquinaria","descripcion","maquinariaid",$row['maquinariaid'],"N").' </td>
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