<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $id = $_GET["id"];
  
  $vsql = "SELECT HC.momento momhistoria , HC.* , T.* FROM historiacli HC INNER JOIN terceros T ON (T.terid = HC.teridpaciente) WHERE HC.historiaid=".$id;
  $conex  = $clase->Conectar();
  $result = mysql_query($vsql,$conex);

  if($row = mysql_fetch_array($result)) 
  {
   	 require('lib/fpdf/fpdf.php');
	 $pdf=new FPDF();
	 $pdf->AddPage();
     
     $x=25;
	 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa2.jpg',8,6,75,17,0);
	      
	 $pdf->SetFont('Arial','B',20);
	 $cont= 'HISTORIA CLINICA';                          $pdf->Text(113,16,$cont);	
	 $pdf->SetFont('Arial','B',12);
	 $cont= 'No. '.$row['nit'];                          $pdf->Text(135,21,$cont);	
	 
     
     if($row['rutafoto'] != "")
     {
	   if(file_exists('fotos/'.$row['nit'].'.jpg'))
	     $pdf->Image('fotos/'.$row['nit'].'.jpg',180,5,25,20,0);
	 }
	
	 $pdf->Rect(4,26,130,5);	 
	 $pdf->Rect(134,26,71,5);	 
	 $pdf->Rect(4,32,201,10);	 
	 $pdf->Rect(4,42,201,18);	 	 
	 $pdf->Rect(4,61,201,5);	 	 
	 $pdf->Rect(4,67,201,20);	 	 	 
	 $pdf->Rect(4,88,201,5);	 	 
	 $pdf->Rect(4,94,201,20);	 	 	 
	 $pdf->Rect(4,115,201,5);	 	   // Titulo Informacion Ocupacional
	 $pdf->Rect(4,121,201,78);	 	   // Contenido Informacion Ocupacional	 
	 
	 $pdf->Rect(4,200,201,5);	 	   // Titulo Antecedentes Personales en Salud
	 $pdf->Rect(4,206,201,36);	 	   // Contenido Antecedentes Personales en Salud
	 
	 $pdf->Rect(4,243,201,5);	 	   // Titulo Revision por Sistemas
	 $pdf->Rect(4,249,201,18);	 	   // Contenido Revision por Sistemas

	 $pdf->SetFont('Arial','B',8);
	 $cont= 'FECHA Y CIUDAD DE EXPEDICION DEL EXAMEN : ';
	 $pdf->Text(6,29,$cont);	
	 $cont= 'TIPO DE EXAMEN : ';
	 $pdf->Text(135,29,$cont);	
	 $cont= 'DATOS DE LA EMPRESA DONDE LABORA, LABORARÁ O LABORÓ EL TRABAJADOR';
	 $pdf->Text(50,35,$cont);	
	 $cont= 'DATOS DEL TRABAJADOR/ASPIRANTE';
	 $pdf->Text(70,45,$cont);	
	 $cont= 'ANTECEDENTES OCUPACIONALES EN EMPRESA ACTUAL O ANTERIORES';
	 $pdf->Text(60,64,$cont);	
	 $cont= 'ACCIDENTES DE TRABAJO / ENFERMEDAD PROFESIONAL EN EMPRESA ACTUAL O ANTERIORES';
	 $pdf->Text(5,91,$cont);	
	 $cont= 'ANTECEDENTES FAMILIARES DE SALUD';
	 $pdf->Text(5,118,$cont);	
     $cont= 'ANTECEDENTES PERSONALES EN SALUD';
	 $pdf->Text(5,203,$cont);	
	 $cont= 'INMUNIZACIONES';
	 $pdf->Text(5,246,$cont);	

	 // Inserto la informacion de la Historia Clinica Actual
	 // Coloco los Datos en la casilla correspondiente
	 // Datos Basicos del Paciente 
	 $pdf->SetFont('Arial','',8);   	 
	 $pdf->Text(80,29,'San José de Cúcuta, '.FormatoFecha($row['momhistoria']));	
	 $pdf->Text(162,29,$row['tipoexamen']);	

	 $pdf->SetFont('Arial','',9);   	 	 
	 
	 $contratoid = $clase->BDLockup($row['docuid'],'documentos','docuid','contratoid');
	 $empresa = $clase->SeleccionarUno("SELECT T.Nombre FROM contratos C INNER JOIN terceros T ON (C.terid = T.terid) WHERE C.contratoid=".$contratoid);
	 	 
	 $pdf->Text(7,40,'EMPRESA :  '.$empresa);	
	 $pdf->Text(104,40,'CARGO :  '.$row['cargo']);	
	 

	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(6,49,'PACIENTE : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(23,49,$row['nombre']);	
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(6,53,'DOCUMENTO : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(27,53,$row['nit']);	
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(6,57,'DIRECCION : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(27,57,$row['direccion']);	
	 
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(86,49,'EDAD : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(97,49,$row['edad']." Años");	
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(86,53,'CELULAR : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(102,53,$row['celular']);	
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(86,57,'TELEFONO : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(104,57,$row['telefono']);	
	 
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(136,49,'ESTADO CIVIL : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(159,49,$clase->BDLockup($row['estadocivilid'],"estadocivil","codigo","descripcion"));	
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(136,53,'NIVEL EDUCATIVO : ');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(164,53,$clase->BDLockup($row['nivelid'],"niveledu","nivelid","descripcion"));	
	 $pdf->SetFont('Arial','B',8);   	 $pdf->Text(136,57,'E.P.S. :');	
     $pdf->SetFont('Arial','',8);    	 $pdf->Text(147,57,$clase->BDLockup($row['entidadid'],"entidades","entidadid","descripcion"));	

	 // Riesgos Ocupacionales
     $vsql = "SELECT * FROM anteocupacionales WHERE historiaid=".$id;
     $conex  = $clase->Conectar();
	 $result = mysql_query($vsql,$conex);
	 $num = mysql_num_rows($result);
     
     if($num == 0){
   	    $pdf->SetFont('Arial','B',11);   	 $pdf->Text(50,77,'PACIENTE NO PRESENTA ANTECEDENTES OCUPACIONALES');	
	 }
     else{	 
	   $pdf->SetFont('Arial','B',8);   	     $pdf->Text(6,70,'DESDE               HASTA                     EMPRESA                             OCUPACION                                       TIEMPO                        EXPOSICION A RIESGOS');	
       $y = 73;
	   while($row = mysql_fetch_array($result))  
       {
         $pdf->SetFont('Arial','',8);    	                    $pdf->Text(6,$y,FormatoFecha($row['fecdesde']));	
         $pdf->Text(27,$y,FormatoFecha($row['fechasta']));	    $pdf->Text(54,$y,$row['empresa']);
         $pdf->Text(91,$y,$row['ocupacion']);	                $pdf->Text(138,$y,$row['tiempo']);		 
		 
		 $riesgosExp = $row['riesgos']." ";
		 if($row['expuesto1'] == "CHECKED")
		    $riesgosExp = $riesgosExp." - Ergon ";   
		 if($row['expuesto2'] == "CHECKED")
		    $riesgosExp = $riesgosExp." - Fisiolo ";   
		 if($row['expuesto3'] == "CHECKED")
		    $riesgosExp = $riesgosExp." - Psicos ";   
		 if($row['expuesto4'] == "CHECKED")
		    $riesgosExp = $riesgosExp." - Quim ";   		 
		 
		 $pdf->Text(165,$y,$riesgosExp);		 
		 $y+=3; 
	   }
	 }

	 // Accidentes de Trabajo
     $vsql = "SELECT * FROM accidentestra WHERE historiaid=".$id;
     $conex  = $clase->Conectar();
	 $result = mysql_query($vsql,$conex);
	 $num = mysql_num_rows($result);
     
     if($num == 0){
   	    $pdf->SetFont('Arial','B',11);   	 $pdf->Text(50,104,'PACIENTE NO PRESENTA ACCIDENTES DE TRABAJO');	
	 }
     else{	 
	   $pdf->SetFont('Arial','B',8);   	     $pdf->Text(6,97,'DESCRIPCION ACCIDENTE                 DE TRABAJO?          FECHA         TIPO ACCIDENTE                          LESION / PARTE CUERPO');	

       $y = 100;
	   while($row = mysql_fetch_array($result))  
       {
         if($row['accitrabajo'] == 'CHECKED')
		   $accitrab = 'SI';
		 else
		   $accitrab = 'NO';		 
		 
		 $pdf->SetFont('Arial','',8);    	                    $pdf->Text(6,$y,$row['descripcion']);	
         $pdf->Text(63,$y,$accitrab);	                        $pdf->Text(80,$y,FormatoFecha($row['fecha']));
         $pdf->Text(100,$y,substr($row['tipo'],0,25));		    $pdf->Text(144,$y,substr($row['lesionparte'],0,34));		 
		 $y+=3; 
	   }
	 }

     $vsql = "SELECT * FROM historiaself WHERE historiaid=".$id;
     $conex  = $clase->Conectar();
	 $result = mysql_query($vsql,$conex);
	 if($row = mysql_fetch_array($result))  
     {
		// Antecedentes Familiares 
		// Columna Izquierda
   	    if($row['antefam01'] != "")   		  $texto.= "ENFERMEDADES CONGENITAS :  \n".$row['antefam01']."\n\n"; 
   	    if($row['antefam02'] != "")      	  $texto.= "ALERGIAS :  \n".$row['antefam02']."\n\n"; 
   	    if($row['antefam03'] != "")      	  $texto.= "ENFERMEDADES PULMONARES :  \n".$row['antefam03']."\n\n"; 
   	    if($row['antefam04'] != "")      	  $texto.= "ASMA :  \n".$row['antefam04']."\n\n"; 
   	    if($row['antefam05'] != "")      	  $texto.= "TUBERCULOSIS :  \n".$row['antefam05']."\n\n"; 
   	    if($row['antefam06'] != "")      	  $texto.= "HIPERTENSION :  \n".$row['antefam06']."\n\n"; 
   	    if($row['antefam07'] != "")      	  $texto.= "CARDIOPATIAS :  \n".$row['antefam07']."\n\n"; 
   	    if($row['antefam08'] != "")      	  $texto.= "E.C.V :  \n".$row['antefam08']."\n\n"; 
   	    if($row['antefam09'] != "")      	  $texto.= "DIABETES :  \n".$row['antefam09']."\n\n"; 
   	    if($row['antefam010'] != "")      	  $texto.= "CANCER :  \n".$row['antefam10']."\n\n"; 
   	    if($row['antefam011'] != "")      	  $texto.= "OSTEOMUSCULARES :  \n".$row['antefam11']."\n\n"; 
   	    if($row['antefam012'] != "")      	  $texto.= "ARTRITIS :  \n".$row['antefam12']."\n\n"; 
   	    if($row['antefam013'] != "")      	  $texto.= "VARICES :  \n".$row['antefam13']."\n\n"; 
   	    if($row['antefam014'] != "")      	  $texto.= "SINDROME CONVULSIVO :  \n".$row['antefam14']."\n\n"; 
   	    if($row['antefam015'] != "")      	  $texto.= "PSIQUIATRICOS :  \n".$row['antefam15']."\n\n"; 
   	    if($row['antefam016'] != "")      	  $texto.= "OTROS :  \n".$row['antefam16']."\n\n"; 
	
	    $pdf->SetFont('Arial','',8);            $texto = $row['observa1'];
	    $pdf->SetXY(5,183);              	    $pdf->MultiCell(190,3,$texto); 

        // Antecedentes Personales 
		// Columna Izquierda
		$cont = 'CONGENITAS : ';                             $pdf->Text(6,210,$cont);    
		$cont = ValorSINO($row['anteper01']);                $pdf->Text(41,210,$cont);

		$cont = 'INMUNODEPRIMIBLES : ';                      $pdf->Text(6,215,$cont);    
		$cont = ValorSINO($row['anteper03']);                $pdf->Text(41,215,$cont);

		$cont = 'INFECCIOSAS : ';                            $pdf->Text(6,220,$cont);    
		$cont = ValorSINO($row['anteper05']);                $pdf->Text(41,220,$cont);

		$cont = 'SISTEMICAS : ';                             $pdf->Text(6,225,$cont);    
		$cont = ValorSINO($row['anteper07']);                $pdf->Text(41,225,$cont);

		$cont = 'OJOS: ';                                    $pdf->Text(6,230,$cont);    
		$cont = ValorSINO($row['anteper09']);                $pdf->Text(41,230,$cont);

		$cont = 'AGUDEZA VISUAL : ';                         $pdf->Text(6,235,$cont);    
		$cont = ValorSINO($row['anteper11']);                $pdf->Text(41,235,$cont);

		$cont = 'OIDOS : ';                                  $pdf->Text(6,240,$cont);    
		$cont = ValorSINO($row['anteper13']);                $pdf->Text(41,240,$cont);

		$cont = 'AGUDEZA AUDITIVA : ';                       $pdf->Text(58,210,$cont);    
		$cont = ValorSINO($row['anteper15']);                $pdf->Text(90,210,$cont);

		$cont = 'NASOFARINGEA : ';                           $pdf->Text(58,215,$cont);    
		$cont = ValorSINO($row['anteper17']);                $pdf->Text(90,215,$cont);

		$cont = 'CARDIOVASCULAR : ';                         $pdf->Text(58,220,$cont);    
		$cont = ValorSINO($row['anteper19']);                $pdf->Text(90,220,$cont);

		$cont = 'PULMONAR: ';                                $pdf->Text(58,225,$cont);    
		$cont = ValorSINO($row['anteper21']);                $pdf->Text(90,225,$cont);

		$cont = 'GASTROINTESTINAL : ';                       $pdf->Text(58,230,$cont);    
		$cont = ValorSINO($row['anteper23']);                $pdf->Text(90,230,$cont);

		$cont = 'GENITOURINARIA: ';                          $pdf->Text(58,235,$cont);    
		$cont = ValorSINO($row['anteper25']);                $pdf->Text(90,235,$cont);

		$cont = 'NEUROLOGICO : ';                            $pdf->Text(58,240,$cont);    
		$cont = ValorSINO($row['anteper02']);                $pdf->Text(90,240,$cont);

        //// 3RA Columna
		$cont = 'PROBLEMAS DE LA PIEL : ';                   $pdf->Text(108,210,$cont);    
		$cont = ValorSINO($row['anteper04']);                $pdf->Text(145,210,$cont);

		$cont = 'OSTEOMUSCULARES: ';                         $pdf->Text(108,215,$cont);    
		$cont = ValorSINO($row['anteper06']);                $pdf->Text(145,215,$cont);

		$cont = 'ALERGICOS : ';                              $pdf->Text(108,220,$cont);    
		$cont = ValorSINO($row['anteper08']);                $pdf->Text(145,220,$cont);

		$cont = 'TOXICOS : ';                                $pdf->Text(108,225,$cont);    
		$cont = ValorSINO($row['anteper10']);                $pdf->Text(145,225,$cont);

		$cont = 'FARMACOLOGICOS : ';                         $pdf->Text(108,230,$cont);    
		$cont = ValorSINO($row['anteper12']);                $pdf->Text(145,230,$cont);
		
		$cont = 'QUIRURGICOS : ';                            $pdf->Text(108,235,$cont);    
		$cont = ValorSINO($row['anteper14']);                $pdf->Text(145,235,$cont);

		$cont = 'TRAUMATICOS : ';                            $pdf->Text(108,240,$cont);    
		$cont = ValorSINO($row['anteper16']);                $pdf->Text(145,240,$cont);

        //// 4TA Columna		
		$cont = 'TRANSFUSIONES : ';                          $pdf->Text(165,210,$cont);    
		$cont = ValorSINO($row['anteper18']);                $pdf->Text(195,210,$cont);
		
		$cont = 'E.T.S. (SIDA) : ';                          $pdf->Text(165,215,$cont);    
		$cont = ValorSINO($row['anteper20']);                $pdf->Text(195,215,$cont);

		$cont = 'DEFORMIDADES : ';                           $pdf->Text(165,220,$cont);    
		$cont = ValorSINO($row['anteper22']);                $pdf->Text(195,220,$cont);

		$cont = 'PSIQUIATRICOS : ';                          $pdf->Text(165,225,$cont);    
		$cont = ValorSINO($row['anteper24']);                $pdf->Text(195,225,$cont);

		$cont = 'FARMACODEPEND : ';                          $pdf->Text(165,230,$cont);    
		$cont = ValorSINO($row['anteper26']);                $pdf->Text(195,230,$cont);


        //////////////////////////////////////////////
        //// Inmunizaciones
		$cont = 'HEPATTIS B : ';                             $pdf->Text(6,255,$cont);    
 	    $cont = ValorSINO($row['inmuni01']);                 $pdf->Text(26,255,$cont);

		$cont = 'FIEBRE AMARILLA : ';                        $pdf->Text(86,255,$cont);    
 	    $cont = ValorSINO($row['inmuni02']);                 $pdf->Text(114,255,$cont);

		$cont = 'TETANO : ';                                 $pdf->Text(165,255,$cont);    
 	    $cont = ValorSINO($row['inmuni03']);                 $pdf->Text(181,255,$cont);

		$cont = 'OTRAS : ______________________________________________________________________________________________________________';       
		$pdf->Text(6,261,$cont);    
 	    $cont = $row['inmuni04'];                            $pdf->Text(26,259,$cont);
	

	 }

 	 $pdf->SetFont('Arial','B',8);
	 $cont= 'SALUD EMPRESARIAL IPS SAS                                                                                    Pág 1 de 2';
	 $pdf->Text(80,272,$cont);		 	 
	 
	 // Fin de la Hoja No. 1
	 // Inicio Hoja No. 2

	 $pdf->AddPage();
     
     $x=25;
	 
	 $vsql = "SELECT * FROM historiacli HC INNER JOIN terceros T ON (T.terid = HC.teridpaciente) 
	          INNER JOIN historiaself HS ON (HC.historiaid = HS.historiaid) WHERE HC.historiaid=".$id;
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);

     if($row = mysql_fetch_array($result)) 
     {

      ////// Firmas
	  $firmamedico = $clase->BDLockup($row['teridprof'],'terceros','terid','rutafirma');
	  if($firmamedico != "")
	  { 
	     if(file_exists('firmas/'.$firmamedico))
	        $pdf->Image('firmas/'.$firmamedico,18,250,40,15,0);
      }

	  $firmapaci = $clase->BDLockup($row['teridpaciente'],'terceros','terid','rutafirma');
	  if($firmapaci!= "")
	  { 
	     if(file_exists('firmas/'.$firmapaci))
	        $pdf->Image('firmas/'.$firmapaci,129,250,40,15,0);
      }
  
	 $pdf->Rect(4,94,201,5);	 	 
	 $pdf->Rect(4,100,201,79);	
	 $pdf->Rect(4,180,201,5);	 	 
	 $pdf->Rect(4,186,201,20);	
	 $pdf->Rect(4,207,201,5);	 	 
	 $pdf->Rect(4,213,201,35);	

	 $pdf->SetFont('Arial','B',8);
	 $cont= 'EXAMEN FISICO';                                      	 $pdf->Text(6,98,$cont); 
	 $cont= 'RESULTADOS EXAMENES PARACLINICOS APLICADOS';          	 $pdf->Text(6,184,$cont); 	 
	 $cont= 'CONCEPTO PROFESIONAL - RECOMENDACIONES';                $pdf->Text(6,211,$cont); 	 
	  

	   /// Imprimo los Formatos del Papel
	   $pdf->Image('images/logoempresa2.jpg',8,6,75,17,0);

       $pdf->SetFont('Arial','B',8);
	   $cont= 'Pág 2 de 2';
	   $pdf->Text(190,8,$cont);		 	 

	      
	   $pdf->SetFont('Arial','B',16);
	   $cont= 'CONTINUACION HISTORIA CLINICA';                                   $pdf->Text(100,16,$cont);	
	   $pdf->SetFont('Arial','B',12);
	   $cont= 'No. '.$row['nit'];                                                $pdf->Text(135,21,$cont);	
	   
       
       $terid = $clase->BDLockup($id,"historiacli","historiaid","teridpaciente");
       $genero = $clase->BDLockup($terid,"terceros","terid","genero");
       
       if($genero == "F")
       { 
	       $pdf->Rect(4,64,201,5);            $pdf->SetFont('Arial','B',8);
	       $pdf->Rect(4,33,201,30);	          $cont= 'GINECOBSTETRICOS';             $pdf->Text(6,31,$cont); 
		   
		   // Datos de Menarca
	       $pdf->SetFont('Arial','',8);
		   $cont= 'MENARCA : _______________________________________________';         $pdf->Text(6,39,$cont);		 	 
		   $cont= 'CICLOS : __________________________________________________';       $pdf->Text(106,39,$cont);		 	 	   
		   $cont= 'GESTACIONES : ___________________________________________';         $pdf->Text(6,44,$cont);		 	 
		   $cont= 'PARTOS : _________________________________________________';        $pdf->Text(106,44,$cont);		 	 	   
		   $cont= 'ABORTOS : _______________________________________________';         $pdf->Text(6,49,$cont);		 	 
		   $cont= 'CESAREAS : _______________________________________________';        $pdf->Text(106,49,$cont);		 	 	   
		   $cont= 'CITOLOGIA :         __________________________________________';    $pdf->Text(6,54,$cont);		 	 
		   $cont= 'MAMOGRAFIA :           ________________________________________';   $pdf->Text(106,54,$cont);		 	 	   
		   $cont= 'HIJOS VIVOS : _____';                                               $pdf->Text(6,59,$cont);		 	 	   	   
		   $cont= 'ULTIMA MENSTRUACION : ___________________________';                 $pdf->Text(35,59,$cont);		 	 	   	   
		   $cont= 'PLANIFICA : ___ METODO : ______________________________';           $pdf->Text(115,59,$cont);		 	 	   	   	   
	       
		   // Completanto los Campos
		   $pdf->Text(23,39,$row['gineco01']);	                           $pdf->Text(120,39,$row['gineco02']);	
		   $pdf->Text(29,44,$row['gineco03']);	                           $pdf->Text(120,44,$row['gineco04']);	
		   $pdf->Text(23,49,$row['gineco05']);	                           $pdf->Text(124,49,$row['gineco06']);	
		   $pdf->Text(30,54,$row['gineco08']);	                           $pdf->Text(136,54,$row['gineco10']);	
		   $pdf->Text(27,59,$row['gineco11']);	                           $pdf->Text(72,59,$row['gineco12']);
	       $pdf->Text(152,59,$row['gineco14']);	   

	       $pdf->SetFont('Arial','B',9);                                   
		   $pdf->Text(25,54,ValorSINO($row['gineco07']));	   	           $pdf->Text(129,54,ValorSINO($row['gineco09']));	   	   
		   $pdf->Text(132,59,ValorSINO($row['gineco13']));	   
		   $y = 75;
       }
       else
       	  $y = 40;

	   // Datos de Examen Fisico
       $pdf->SetFont('Arial','B',8);
       $cont= 'REVISION POR SISTEMAS';                               	   $pdf->Text(6,$y-7,$cont); 
       $pdf->Rect(4,$y-11,201,5);                                           
       
       if($y == 40)
           $pdf->Rect(4,$y-4,201,55);	
       else
           $pdf->Rect(4,$y-4,201,23);	
        
       $pdf->SetFont('Arial','',8);
	   $cont= 'PIEL Y FANERAS : ';               $pdf->Text(6,$y,$cont);                 $pdf->Text(37,$y,ValorSINO($row['revsis01']));		 	 
	   $cont= 'VISUAL : ';                       $pdf->Text(57,$y,$cont);		 	     $pdf->Text(90,$y,ValorSINO($row['revsis02']));
	   $cont= 'ORL : ';                          $pdf->Text(107,$y,$cont);		 	 	 $pdf->Text(140,$y,ValorSINO($row['revsis03']));  
	   $cont= 'CARDIOVASCULAR : ';               $pdf->Text(155,$y,$cont);		 	 	 $pdf->Text(197,$y,ValorSINO($row['revsis04']));         
	   $cont= 'RESPIRATORIO : ';                 $pdf->Text(6,$y+5,$cont);		 	     $pdf->Text(37,$y+5,ValorSINO($row['revsis05']));		 	 
	   $cont= 'GASTROINTESTINAL : ';             $pdf->Text(57,$y+5,$cont);		 	     $pdf->Text(90,$y+5,ValorSINO($row['revsis06']));		 	 
	   $cont= 'GENITOURINARIO : ';               $pdf->Text(107,$y+5,$cont);		 	 $pdf->Text(140,$y+5,ValorSINO($row['revsis07']));		 	   
	   $cont= 'OSTEOMUSCULAR : ';                $pdf->Text(155,$y+5,$cont);		 	 $pdf->Text(197,$y+5,ValorSINO($row['revsis08'])); 
	   $cont= 'HEMATOLOGICO : ';                 $pdf->Text(6,$y+10,$cont);		 	     $pdf->Text(37,$y+10,ValorSINO($row['revsis09']));		 	 
	   $cont= 'INMUNOLOGICO : ';                 $pdf->Text(57,$y+10,$cont);		 	 $pdf->Text(90,$y+10,ValorSINO($row['revsis10']));		 	 
	   $cont= 'NEUROLOGICO: ';                   $pdf->Text(107,$y+10,$cont);		 	 $pdf->Text(140,$y+10,ValorSINO($row['revsis11']));		 	   
	   $cont= 'OTROS : ';                        $pdf->Text(155,$y+10,$cont);		 	 $pdf->Text(197,$y+10,ValorSINO($row['revsis12'])); 
	   $cont= 'OBSERVACIONES : ___________________________________________________________________________________________________________';                           
	   $pdf->Text(6,90,$cont);		 	 	     $pdf->Text(33,$y+49,$row['observa4']);		 	   
	         
       //// DATOS EXAMEN FISICO
	   $cont= 'PESO : ____ kg';                $pdf->Text(6,105,$cont);		 	         $pdf->Text(17,105,$row['efpeso']);		 	 
	   $cont= 'TALLA : _____ mts';             $pdf->Text(34,105,$cont);		 	     $pdf->Text(45,105,$row['eftalla']);		 	 
	   $cont= 'I.M.C. : ________';             $pdf->Text(70,105,$cont);		 	 	 $pdf->Text(81,105,$row['efimc']);		 	   
	   $cont= 'TENSION ART. : _______';        $pdf->Text(102,105,$cont);		 	 	 $pdf->Text(125,105,$row['eftart']); 
	   $cont= 'FRE CARD : ________';           $pdf->Text(140,105,$cont);		 	 	 $pdf->Text(158,105,$row['effresp']); 
	   $cont= 'FRE RESP : _____';              $pdf->Text(175,105,$cont);		 	 	 $pdf->Text(193,105,$row['effcard']); 	   	  

	   $cont= 'LATERALIDAD : _______________';        $pdf->Text(6,110,$cont);	      $pdf->Text(28,110,$row['lateralidad']);		 	 
	   $cont= 'FUMA : _____________';                 $pdf->Text(55,110,$cont);       $pdf->Text(65,110,$row['fuma']);		 	 
	   $cont= 'BEBE LICOR : _________________';       $pdf->Text(93,110,$cont); 	  $pdf->Text(113,110,$row['bebe']);		 	 
       $cont= 'HACE DEPORTE : ___________________';   $pdf->Text(145,110,$cont);	  $pdf->Text(170,110,$row['deporte']); 
	   
   	    if($row['ef1'] != "")   		      $texto.= "ESTADO NUTRICIONAL :  \n".$row['ef1']."\n\n"; 
   	    if($row['ef2'] != "")   		      $texto.= "PIEL Y FANERAS :  \n".$row['ef2']."\n\n"; 		
   	    if($row['ef3'] != "")   		      $texto.= "CRANEO :  \n".$row['ef3']."\n\n"; 				
   	    if($row['ef4'] != "")   		      $texto.= "CARA :  \n".$row['ef4']."\n\n"; 		
   	    if($row['ef5'] != "")   		      $texto.= "PARPADOS :  \n".$row['ef5']."\n\n"; 				
   	    if($row['ef6'] != "")   		      $texto.= "PUPILAS :  \n".$row['ef6']."\n\n"; 				
   	    if($row['ef7'] != "")   		      $texto.= "CORNEAS :  \n".$row['ef7']."\n\n"; 				
   	    if($row['ef8'] != "")   		      $texto.= "CONJUNTIVAS :  \n".$row['ef8']."\n\n"; 						
   	    if($row['ef9'] != "")   		      $texto.= "NARIZ :  \n".$row['ef9']."\n\n"; 				
   	    if($row['ef10'] != "")   		      $texto.= "BOCA :  \n".$row['ef10']."\n\n"; 				
   	    if($row['ef11'] != "")   		      $texto.= "FARINGE :  \n".$row['ef11']."\n\n"; 						
   	    if($row['ef12'] != "")   		      $texto.= "INSPECCION EXTERNA OIDOS :  \n".$row['ef12']."\n\n"; 				
   	    if($row['ef13'] != "")   		      $texto.= "OTOSCOPIA :  \n".$row['ef13']."\n\n"; 								
   	    if($row['ef14'] != "")   		      $texto.= "INSPECCION CUELLO :  \n".$row['ef14']."\n\n"; 				
   	    if($row['ef15'] != "")   		      $texto.= "PALPACION CUELLO Y TIROIDES :  \n".$row['ef15']."\n\n"; 				
   	    if($row['ef16'] != "")   		      $texto.= "INSPECCION TORAX :  \n".$row['ef16']."\n\n"; 						
   	    if($row['ef17'] != "")   		      $texto.= "PALPACION TORAX :  \n".$row['ef17']."\n\n"; 						
   	    if($row['ef18'] != "")   		      $texto.= "AUSCULTACION TORAX :  \n".$row['ef18']."\n\n"; 								
   	    if($row['ef19'] != "")   		      $texto.= "INSPECCION ABDOMINAL :  \n".$row['ef19']."\n\n"; 						
   	    if($row['ef20'] != "")   		      $texto.= "PALPACION ABDOMINAL :  \n".$row['ef20']."\n\n"; 										
   	    if($row['ef21'] != "")   		      $texto.= "INSPECCION COLUMNA VERTEBRAL :  \n".$row['ef21']."\n\n"; 						
   	    if($row['ef22'] != "")   		      $texto.= "PALPACION COLUMNA VERTEBRAL:  \n".$row['ef22']."\n\n"; 								
   	    if($row['ef23'] != "")   		      $texto.= "INSPECCION MIEMBROS SUPERIORES :  \n".$row['ef23']."\n\n"; 						
   	    if($row['ef24'] != "")   		      $texto.= "PULSO RADIAL :  \n".$row['ef24']."\n\n"; 								
   	    if($row['ef25'] != "")   		      $texto.= "INSPECCION MIEMBROS INFERIORES :  \n".$row['ef25']."\n\n"; 						
   	    if($row['ef26'] != "")   		      $texto.= "REFLEJOS TENDINOSOS :  \n".$row['ef26']."\n\n"; 										
   	    if($row['ef27'] != "")   		      $texto.= "ESFERA MENTAL :  \n".$row['ef27']."\n\n"; 						
   	    if($row['ef28'] != "")   		      $texto.= "NEUROLOGICO :  \n".$row['ef28']."\n\n"; 								
   	    if($row['ef29'] != "")   		      $texto.= "OTROS :  \n".$row['ef29']."\n\n"; 									
														
	    $pdf->SetFont('Arial','',7.5);
	    $pdf->SetXY(5,114);              	  $pdf->MultiCell(190,3,$texto); 

       ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
       /// RESULTADOS EXAMENES PARACLINICOS		
	   /*	
	   $vsql2="SELECT DD.dedocumid ddid, DD.resultado, I.descripcion FROM historiacli HC
	    	  INNER JOIN dedocumentos DD ON ( DD.docuid = HC.docuid )
		      INNER JOIN item I ON ( DD.itemid = I.itemid )
		      WHERE (I.codigo <> '001.01' AND I.codigo <> '001.02' AND I.codigo <> '001.03' AND I.codigo <> '001.04') AND HC.historiaid =".$id;
       
	   $conex2  = $clase->Conectar();
       $result2 = mysql_query($vsql2,$conex2);
       $y2 = 193;
	   
	   $pdf->SetFont('Arial','B',7.5);                 $pdf->Text(6,190,'NOMBRE DE EXAMEN                       RESULTADO DEL EXAMEN');
	   while($row2 = mysql_fetch_array($result2)) 
       {
     	   $resexamen = $row2['resultado'];
		   if($resexamen == "")
		     $resexamen = "NO EXISTE RESULTADO DE ESTE EXAMEN EN EL SISTEMA";
			 
		   $pdf->SetFont('Arial','B',7.5);                 $pdf->Text(6,$y2,$row2['descripcion']);
		   $pdf->SetFont('Arial','',7.5);                  $pdf->Text(51,$y2,$resexamen);
		   $y2+=4;
	   }
	   	
	   	*/
	   $pdf->SetFont('Arial','B',7.5);                 $texto = $row['observa6'];
	   $pdf->SetXY(6,187);                        	   $pdf->MultiCell(190,3,$texto); 

       ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
       /// CONCEPTO PROFESIONAL Y RECOMENDACIONES
	   $pdf->SetFont('Arial','B',7.5);
	   $cont= 'CONCEPTO : ';          $pdf->Text(6,217,$cont);                  
	   $cont= 'RECOMENDACIONES: ';    $pdf->Text(6,226,$cont);        	   
	     
	   $pdf->SetFont('Arial','',12);      	   
	   $conceptomed = $clase->BDLockup($row['conceptomed'],'conceptomed','codigo','descripcion');         	   
	   $pdf->Text(6,221,$conceptomed);		 	 

	   $pdf->SetFont('Arial','',8.5);          	   
       $pdf->SetXY(5,228);          	    $pdf->MultiCell(190,3,$row['observa5']);
	   
  	   $pdf->SetFont('Arial','',9);          	            
	   
	   $teridprof  = $clase->BDLockup($id,'historiacli','historiaid','teridprof');
	   $nombreprof = $clase->BDLockup($teridprof,'terceros','terid','nombre');
  	   $registroprof = $clase->BDLockup($teridprof,'terceros','terid','registropro');
	  
 
	   /// Firmas
	   $pdf->line(15,260,80,260);     
	   $cont= 'FIRMA MEDICO';
	   $pdf->Text(15,263,$cont);
	   $pdf->Text(15,266,$nombreprof);
	   $pdf->Text(15,269,"L.S.O. No. ".$registroprof);	   

	   $pdf->line(125,260,190,260);	 		 
	   $cont= 'FIRMA TRABAJADOR';
	   $pdf->Text(140,266,$cont);		     
	   
       
	   
    }

     /// Pie de Pagina
 	 $pdf->SetFont('Arial','B',8);
	 $cont= 'SALUD EMPRESARIAL IPS SAS                                                                                    Pág 2 de 2';
	 $pdf->Text(80,272,$cont);		 	 
	
	 // Genero la Historia como PDF
	 $pdf->Output("pdfs/HistClin_".$id.".pdf");
	 $pdf->Output();	

  }
			
  mysql_free_result($result); 
  mysql_close($conex);			  


  /////////////////////////////////////////////////////////////////////////
  function ValorSINO($campo)
  {
    if(strtoupper($campo) == "CHECKED")
	  return("SI");
	else  
	  return("NO");
  }

  /////////////////////////////////////////////////////////////////////////
  function ValorExamenFisico($campo)
  {
    if($campo == "")
	  return('No Exam');
	else  	
	  return($campo);
  }
  	  	
  	  	
  /////////////////////////////////////////////////////////////////////////
  function NombreMes($mes)
  {
      if(($mes == 1)||($mes == "01")) 
	   return("ENERO");
      if(($mes == 2)||($mes == "02")) 
	   return("FEBRERO");
      if(($mes == 3)||($mes == "03")) 
	   return("MARZO");
      if(($mes == 4)||($mes == "04")) 
	   return("ABRIL");
      if(($mes == 5)||($mes == "05")) 
	   return("MAYO");
      if(($mes == 6)||($mes == "06")) 
	   return("JUNIO");
      if(($mes == 7)||($mes == "07")) 
	   return("JULIO");
      if(($mes == 8)||($mes == "08")) 
	   return("AGOSTO");
      if(($mes == 9)||($mes == "09")) 
	   return("SEPTIEMBRE");
      if(($mes == 10)||($mes == "10")) 
	   return("OCTUBRE");
      if(($mes == 11)||($mes == "11")) 
	   return("NOVIEMBRE");
      if(($mes == 12)||($mes == "12")) 
	   return("DICIEMBRE");
	}
	
	/////////////////////////////////////////////////////////////////////
	function FormatoFecha($fecha)
	{
	   $ano  = substr($fecha,0,4);
	   $mes  = substr($fecha,5,2);
	   $dia  = substr($fecha,8,2);
	   $nombreMes = substr(NombreMes($mes),0,3);
	   return($dia." ".$nombreMes." ".$ano);
	}
	
  
  
?> 