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
   	 require('lib/fpdf/fpdf.php');
	 $pdf=new FPDF();
	 $pdf->AddPage();
     $x=25;

	 
 	 if($row['rutafirma'] != "")
	 {
	    if(file_exists('firmas/'.$row['rutafirma']))
	       $pdf->Image('firmas/'.$row['rutafirma'],110,255,65,29,0);
     }
 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa.jpg',8,10,80,19,0);
	 $pdf->SetFont('Arial','B',15);
	 $cont= 'DECLARACION CONSENTIMIENTO INFORMADO ';                  $pdf->Text(40,50,$cont);	
	 
     /// Ciudad y Fecha
	 $cont= 'San Jose de Cucuta, '.date("d").' de '.NombreMes(date("m")).' de '.date("Y");
	 $pdf->SetFont('Arial','',12);                                  
	 $pdf->SetXY(8,69);                  $pdf->MultiCell(0,0,$cont,0,'L');	

     /// Primer parrafo del Consentimiento
	 $cont  = 'Yo, '.strtoupper($row['nombre']).'   identificado con Cédula de Ciudadanía  '.$row['nit'].'  manifiesto ';
     $cont .= 'que recibí una explicacion clara sobre los procedimientos clinicos y paraclinicos que me serán aplicados ';
     $cont .= 'por parte de SALUD EMPRESARIAL IPS SAS con el fin de emitir un concepto sobre la evaluacion clinica ';
     $cont .= 'a la que seré sometido. ';

	 $pdf->SetFont('Arial','',12);                                  
	 $pdf->SetXY(8,84);                  $pdf->MultiCell(0,10,$cont,0,'J');	

     /// Segundo parrafo del Consentimiento
	 $cont  = 'Texto complementario Texto complementario Texto complementario Texto complementario Texto complementario ';
	 $cont .= 'Texto complementario Texto complementario Texto complementario Texto complementario Texto complementario ';
	 $cont .= 'Texto complementario Texto complementario Texto complementario Texto complementario Texto complementario ';

	 $pdf->SetFont('Arial','',12);                                  
	 $pdf->SetXY(8,140);                  $pdf->MultiCell(0,10,$cont,0,'J');	


     /// Sin otro particular
	 $cont  = 'Sin otro particular';

	 $pdf->SetFont('Arial','',12);                                  
	 $pdf->SetXY(8,210);                  $pdf->MultiCell(0,10,$cont,0,'J');	

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