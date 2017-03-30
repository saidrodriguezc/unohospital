<?PHP
  session_start(); 
  date_default_timezone_set('America/Los_Angeles');
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $opcion = $_GET["opcion"];
  $id     = $_GET["id"];
  
  
  ///// Extraigo los datos del Paciente
  $vsql = "SELECT DISTINCT EC.descripcion ECivil , HC.momento fhis , NE.descripcion NivelEdu , D.nitempresa, HC.* , HS.* , T.* FROM historiacli HC 
           INNER JOIN historiaself HS ON (HS.historiaid = HC.historiaid) 
		   INNER JOIN terceros T ON (T.terid = HC.teridpaciente)
		   INNER JOIN documentos D ON (D.docuid = HC.docuid)
		   LEFT JOIN estadocivil EC ON (T.estadocivilid = EC.codigo)
		   LEFT JOIN niveledu NE ON (T.nivelid = NE.nivelid)		   		   
		   WHERE HC.historiaid=".$id;

  $conex  = $clase->Conectar();
  $result = mysql_query($vsql,$conex);
  $registros = mysql_num_rows($result);

  if($row = mysql_fetch_array($result)) 
  {
   	 
   	 $docuid = $row['docuid']; 
     
     /// Busco los procedimientos de la PSE    
   	 $procedimientos = "";
   	 $vsql2 = "SELECT IX.descripcion FROM item IX INNER JOIN dedocumentos DX ON (DX.itemid = IX.itemid) WHERE IX.descripcion NOT LIKE '%RX%' AND DX.docuid=".$docuid;
     $result2 = mysql_query($vsql2,$conex);
     while($row2 = mysql_fetch_array($result2)) 
        $procedimientos .= $row2['descripcion']."     "; 

     //// Genero el PDF
   	 require('lib/fpdf/fpdf.php');
	 $pdf=new FPDF();
	 $pdf->AddPage();
     $x=25;

	  
 	 if($row['rutafirma'] != "")
	 {
	    if(file_exists('firmas/'.$row['rutafirma']))
	       $pdf->Image('firmas/'.$row['rutafirma'],10,245,65,29,0);
     }
 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa.jpg',8,18,80,19,0);
	 $pdf->SetFont('Arial','B',15);
	 $cont= 'DECLARACION CONSENTIMIENTO INFORMADO ';                  $pdf->Text(40,65,$cont);	
	 
     /// Ciudad y Fecha
	 $cont= 'San Jose de Cucuta, '.date("d").' de '.NombreMes(date("m")).' de '.date("Y");
	 $pdf->SetFont('Arial','',12);                                  
	 $pdf->SetXY(8,90);                  $pdf->MultiCell(0,0,$cont,0,'L');	

     /// Primer parrafo del Consentimiento
	 $cont  = 'Yo, '.strtoupper($row['nombre']).'   identificado con Cédula de Ciudadanía  '.$row['nit'].'  manifiesto ';
     $cont .= 'que recibí una explicacion clara sobre los procedimientos clinicos y paraclinicos que me serán aplicados ';
     $cont .= 'por parte de SALUD EMPRESARIAL IPS SAS con el fin de emitir un concepto sobre la evaluacion clinica ';
     $cont .= 'a la que seré sometido. Los procedimientos a practicar serán : ';
     $cont .= $procedimientos;

	 $pdf->SetFont('Arial','',12);                                  
	 $pdf->SetXY(8,120);                  $pdf->MultiCell(0,10,$cont,0,'J');	

     /// Sin otro particular
	 $cont  = 'Sin otro particular';

	 $pdf->SetFont('Arial','',12);                                  
	 $pdf->SetXY(8,198);                  $pdf->MultiCell(0,10,$cont,0,'J');	

   	 $cont= 'FIRMA TRABAJADOR';
	 $pdf->Text(13,270,$cont);		 
	 $pdf->line(10,265,70,265);
	 
     $pdf->Output();
 }  
 

 ///////////////////////////////////////////////////////////////
 // Retorna el Nombre del Mes
 ///////////////////////////////////////////////////////////////
 function NombreMes($MesDate)
 {
 	$mes = $MesDate+0;
 	if($mes == 1)   $nombre = "Enero";
 	if($mes == 2)   $nombre = "Febrero";
 	if($mes == 3)   $nombre = "Marzo";
 	if($mes == 4)   $nombre = "Abril";
 	if($mes == 5)   $nombre = "Mayo";
 	if($mes == 6)   $nombre = "Junio";
 	if($mes == 7)   $nombre = "Julio";
 	if($mes == 8)   $nombre = "Agosto";
 	if($mes == 9)   $nombre = "Septiembre";
 	if($mes == 10)  $nombre = "Octubre";
 	if($mes == 11)  $nombre = "Noviembre";
    if($mes == 12)  $nombre = "Diciembre";
    return($nombre);
 }

?> 