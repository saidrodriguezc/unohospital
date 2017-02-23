<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $id = $_GET["id"];
  
  $vsql = "SELECT * FROM terceros T INNER JOIN documentos D ON (T.terid = D.terid1)
		   WHERE D.docuid=".$id;
	   
  $conex  = $clase->Conectar();
  $result = mysql_query($vsql,$conex);

  if($row = mysql_fetch_array($result)) 
  {
   	 require('lib/fpdf/fpdf.php');
	 $pdf=new FPDF();
	 $pdf->AddPage();
     
     $x=25;
	 $y=18;
	 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa.jpg',5,$y-8,60,14,0);
	      
	 $pdf->SetFont('Arial','B',14);
	 $cont= 'ORDEN DE SERVICIO ';                           $pdf->Text(120,$y,$cont);		 
	 
	 $pdf->Rect(5,$y+8,200,18);	 
	 
	 $cont= 'DESCRIPCION DE LOS SERVICIOS';                 $pdf->Text(60,$y+33,$cont);		 	 

	 $pdf->SetFont('Arial','B',8);	 
	 $cont= 'NOMBRE DEL PACIENTE : ';                       $pdf->Text(6,$y+12,$cont);		 	 
	 $cont= 'IDENTIFICACION : ';                            $pdf->Text(119,$y+12,$cont);		 	 
	 $cont= 'FECHA : ';                                     $pdf->Text(168,$y+12,$cont);		 	 
	 $cont= 'CARGO : ';                                     $pdf->Text(6,$y+20,$cont);		 	 
	 $cont= 'EMPRESA SOLICITANTE : ';                       $pdf->Text(119,$y+20,$cont);		 	 
	 	 
	 $pdf->SetFont('Arial','B',11);	 
	 $cont= 'EXAMEN MEDICO OCUPACIONAL';      	            $pdf->Text(6,$y+41,$cont);	                $pdf->Rect(80,$y+37,8,6);
	 $cont= 'OPTOMETRIA';                                  	$pdf->Text(100,$y+41,$cont);		        $pdf->Rect(140,$y+37,8,6);
	 $cont= 'AUDIOMETRIA';                     	            $pdf->Text(160,$y+41,$cont);		        $pdf->Rect(195,$y+37,8,6);

	 $cont= 'ESPIROMETRIA';                 	            $pdf->Text(6,$y+47,$cont);	                $pdf->Rect(80,$y+43,8,6);
	 $cont= 'LABORATORIOS';                                	$pdf->Text(100,$y+47,$cont);	            $pdf->Rect(140,$y+43,8,6);	 
	 $cont= 'RX';                            	            $pdf->Text(160,$y+47,$cont);		        $pdf->Rect(195,$y+43,8,6);
	 
     $cont= 'OTROS : ';          	                        $pdf->Text(6,$y+54,$cont);	                $pdf->Line(25,$y+54,205,$y+54);	
     
	 /// Laboratorios

	 $pdf->SetFont('Arial','B',14);	
	 $cont= 'EXAMENES DE LABORATORIO';                      $pdf->Text(63,$y+65,$cont);	
	 $pdf->SetFont('Arial','B',12);
	 
     $pdf->Rect(6,$y+70,6,5);               	            $cont= 'Serologia ';                    $pdf->Text(14,$y+75,$cont);                
	 $pdf->Rect(55,$y+70,6,5);								$cont= 'Acido Urico';                 	$pdf->Text(65,$y+75,$cont);		        
	 $pdf->Rect(105,$y+70,6,5);							    $cont= 'Baciloscopia ';      	        $pdf->Text(115,$y+75,$cont);             
	 $pdf->Rect(155,$y+70,6,5);                             $cont= 'Colesterol HDL';              	$pdf->Text(165,$y+75,$cont);		        
     $pdf->Rect(6,$y+75,6,5);               	            $cont= 'Colesterol Total';              $pdf->Text(14,$y+80,$cont);                
	 $pdf->Rect(55,$y+75,6,5);								$cont= 'Triglicéridos';                	$pdf->Text(65,$y+80,$cont);		        
	 $pdf->Rect(105,$y+75,6,5);							    $cont= 'Coprológico';        	        $pdf->Text(115,$y+80,$cont);             
	 $pdf->Rect(155,$y+75,6,5);                             $cont= 'Cuadro Hemático';              	$pdf->Text(165,$y+80,$cont);		        
     $pdf->Rect(6,$y+80,6,5);               	            $cont= 'Frotis de Garganta';            $pdf->Text(14,$y+85,$cont);                
	 $pdf->Rect(55,$y+80,6,5);								$cont= 'KOH de Uñas';                	$pdf->Text(65,$y+85,$cont);		        
	 $pdf->Rect(105,$y+80,6,5);							    $cont= 'Glicemia';        	            $pdf->Text(115,$y+85,$cont);             
	 $pdf->Rect(155,$y+80,6,5);                             $cont= 'Hemoclasificación';            	$pdf->Text(165,$y+85,$cont);		        
     $pdf->Rect(6,$y+85,6,5);               	            $cont= 'Hepatitis B';                   $pdf->Text(14,$y+90,$cont);                
	 $pdf->Rect(55,$y+85,6,5);								$cont= 'HIV';                        	$pdf->Text(65,$y+90,$cont);		        
	 $pdf->Rect(105,$y+85,6,5);							    $cont= 'Parcial de Orina';              $pdf->Text(115,$y+90,$cont);             
	 $pdf->Rect(155,$y+85,6,5);                             $cont= 'Serologia No. 1';            	$pdf->Text(165,$y+90,$cont);		        

	 $pdf->SetFont('Arial','B',14);	 
	 $cont= 'RADIOGRAFIAS';                                 $pdf->Text(80,$y+100,$cont);		 	 

	 $pdf->SetFont('Arial','B',11);	 
     $pdf->Rect(6,$y+102,5,4);               	            $cont= 'Radiograf Columna';             $pdf->Text(12,$y+104,$cont);                
															$cont= 'Lumbo Sacra';                   $pdf->Text(12,$y+108,$cont);                
	 $pdf->Rect(53,$y+102,5,4);							    $cont= 'Radiograf de Torax';        	$pdf->Text(59,$y+106,$cont);		        
	 $pdf->Rect(105,$y+102,5,4);                            $cont= 'EKG';                        	$pdf->Text(113,$y+106,$cont);		        
	 $pdf->Rect(130,$y+102,5,4);                            $cont= 'Otros : ________________________';
	 $pdf->Text(136,$y+106,$cont);		        	 
	 
	 $pdf->SetFont('Arial','B',8);

	 $cont= "LABORATORIO CLINICO";                        $pdf->Text(6,$y+114,$cont);
	 $cont= "Dra. Adriana Castañeda";                     $pdf->Text(6,$y+117,$cont);
     $cont= "Cll. 5 No. 0-10E B. La Ceiba";               $pdf->Text(6,$y+120,$cont);	 
     $cont= "Tel. 577 4350";                              $pdf->Text(6,$y+123,$cont);
     $pdf->Line(6,$y+126,46,$y+126);
	 
     $cont= "RAYOS X";                                    $pdf->Text(57,$y+114,$cont);
	 $cont= "Centro Medico Los Samanes";                  $pdf->Text(57,$y+117,$cont);
     $cont= "SOMEDIAG";                                   $pdf->Text(57,$y+120,$cont);	 
     $pdf->Line(57,$y+126,97,$y+126);
	 
     $cont= "CEMERAD";                                    $pdf->Text(110,$y+114,$cont);
	 $cont= "Av. 0 No. 10-78 Ofc 201";                    $pdf->Text(110,$y+117,$cont);
     $cont= "Edif Colegio Medico";                        $pdf->Text(110,$y+120,$cont);
     $cont= "Telefono: 572 6259";                         $pdf->Text(110,$y+123,$cont);	 
     $pdf->Line(110,$y+126,150,$y+126);
	 
     $cont= "Centro Especializado del";                   $pdf->Text(162,$y+114,$cont);
	 $cont= "Corazon F.C.B.";                             $pdf->Text(162,$y+117,$cont);
     $cont= "Clinica Santa Ana";                          $pdf->Text(162,$y+120,$cont);
     $cont= "";                                           $pdf->Text(162,$y+123,$cont);	 
     $pdf->Line(162,$y+126,202,$y+126);
	 
     /// Completo la Informacion del Tercero
	 $pdf->SetFont('Arial','B',11);
     $cont= $row['nombre'];                              	$pdf->Text(8,$y+16,$cont);
     $cont= $row['nit'];                                 	$pdf->Text(120,$y+16,$cont);	 
     $cont= substr($row['fechadoc'],8,2)."/".substr($row['fechadoc'],5,2)."/".substr($row['fechadoc'],0,4);        	$pdf->Text(170,$y+16,$cont);	 	 

     $cont= $row['cargo'];                              	$pdf->Text(8,$y+24,$cont);
	 $cont= "SALUD EMPRESARIAL IPS SAS";                  	$pdf->Text(120,$y+24,$cont);


     //// Finalmente, muestro la X de cada procedimiento en la Hoja de la Orden de Servicio
     $vsql = "SELECT P.eximin , P.eximax FROM documentos D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid)		  
			  INNER JOIN productos P ON (DD.itemid = P.itemid) WHERE D.docuid=".$id;
	   
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);

	 $pdf->SetFont('Arial','',14);	 
	 $cont= 'X';      	            
     while($row = mysql_fetch_array($result)) 
     {
        $pdf->Text($row['eximin'],$row['eximax'],$cont);	
     }

     /////////////////////////////////////////////////////////////
	 //// Segunda Hoja
	 /////////////////////////////////////////////////////////////
	 
	 $pdf->AddPage();
   
     $vsql = "SELECT * FROM terceros T INNER JOIN documentos D ON (T.terid = D.terid1)
		   WHERE D.docuid=".$id;
	   
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $row = mysql_fetch_array($result);
	 
     $x=25;
	 $y=18;
	 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa.jpg',5,$y-8,60,14,0);
	      
	 $pdf->SetFont('Arial','B',14);
	 $cont= 'ORDEN DE SERVICIO ';                           $pdf->Text(120,$y,$cont);		 
	 
	 $pdf->Rect(5,$y+8,200,18);	 
	 
	 $cont= 'DESCRIPCION DE LOS SERVICIOS';                 $pdf->Text(60,$y+33,$cont);		 	 

	 $pdf->SetFont('Arial','B',8);	 
	 $cont= 'NOMBRE DEL PACIENTE : ';                       $pdf->Text(6,$y+12,$cont);		 	 
	 $cont= 'IDENTIFICACION : ';                            $pdf->Text(119,$y+12,$cont);		 	 
	 $cont= 'FECHA : ';                                     $pdf->Text(168,$y+12,$cont);		 	 
	 $cont= 'CARGO : ';                                     $pdf->Text(6,$y+20,$cont);		 	 
	 $cont= 'EMPRESA SOLICITANTE : ';                       $pdf->Text(119,$y+20,$cont);		 	 
	 	 
	 $pdf->SetFont('Arial','B',11);	 
	 $cont= 'EXAMEN MEDICO OCUPACIONAL';      	            $pdf->Text(6,$y+41,$cont);	                $pdf->Rect(80,$y+37,8,6);
	 $cont= 'OPTOMETRIA';                                  	$pdf->Text(100,$y+41,$cont);		        $pdf->Rect(140,$y+37,8,6);
	 $cont= 'AUDIOMETRIA';                     	            $pdf->Text(160,$y+41,$cont);		        $pdf->Rect(195,$y+37,8,6);

	 $cont= 'ESPIROMETRIA';                 	            $pdf->Text(6,$y+47,$cont);	                $pdf->Rect(80,$y+43,8,6);
	 $cont= 'LABORATORIOS';                                	$pdf->Text(100,$y+47,$cont);	            $pdf->Rect(140,$y+43,8,6);	 
	 $cont= 'RX';                            	            $pdf->Text(160,$y+47,$cont);		        $pdf->Rect(195,$y+43,8,6);
	 
     $cont= 'OTROS : ';          	                        $pdf->Text(6,$y+54,$cont);	                $pdf->Line(25,$y+54,205,$y+54);	
     
	 /// Laboratorios

	 $pdf->SetFont('Arial','B',14);	
	 $cont= 'EXAMENES DE LABORATORIO';                      $pdf->Text(63,$y+65,$cont);	
	 $pdf->SetFont('Arial','B',12);
	 
     $pdf->Rect(6,$y+70,6,5);               	            $cont= 'Serologia ';                    $pdf->Text(14,$y+75,$cont);                
	 $pdf->Rect(55,$y+70,6,5);								$cont= 'Acido Urico';                 	$pdf->Text(65,$y+75,$cont);		        
	 $pdf->Rect(105,$y+70,6,5);							    $cont= 'Baciloscopia ';      	        $pdf->Text(115,$y+75,$cont);             
	 $pdf->Rect(155,$y+70,6,5);                             $cont= 'Colesterol HDL';              	$pdf->Text(165,$y+75,$cont);		        
     $pdf->Rect(6,$y+75,6,5);               	            $cont= 'Colesterol Total';              $pdf->Text(14,$y+80,$cont);                
	 $pdf->Rect(55,$y+75,6,5);								$cont= 'Triglicéridos';                	$pdf->Text(65,$y+80,$cont);		        
	 $pdf->Rect(105,$y+75,6,5);							    $cont= 'Coprológico';        	        $pdf->Text(115,$y+80,$cont);             
	 $pdf->Rect(155,$y+75,6,5);                             $cont= 'Cuadro Hemático';              	$pdf->Text(165,$y+80,$cont);		        
     $pdf->Rect(6,$y+80,6,5);               	            $cont= 'Frotis de Garganta';            $pdf->Text(14,$y+85,$cont);                
	 $pdf->Rect(55,$y+80,6,5);								$cont= 'KOH de Uñas';                	$pdf->Text(65,$y+85,$cont);		        
	 $pdf->Rect(105,$y+80,6,5);							    $cont= 'Glicemia';        	            $pdf->Text(115,$y+85,$cont);             
	 $pdf->Rect(155,$y+80,6,5);                             $cont= 'Hemoclasificación';            	$pdf->Text(165,$y+85,$cont);		        
     $pdf->Rect(6,$y+85,6,5);               	            $cont= 'Hepatitis B';                   $pdf->Text(14,$y+90,$cont);                
	 $pdf->Rect(55,$y+85,6,5);								$cont= 'HIV';                        	$pdf->Text(65,$y+90,$cont);		        
	 $pdf->Rect(105,$y+85,6,5);							    $cont= 'Parcial de Orina';              $pdf->Text(115,$y+90,$cont);             
	 $pdf->Rect(155,$y+85,6,5);                             $cont= 'Serologia No. 1';            	$pdf->Text(165,$y+90,$cont);		        

	 $pdf->SetFont('Arial','B',14);	 
	 $cont= 'RADIOGRAFIAS';                                 $pdf->Text(80,$y+100,$cont);		 	 

	 $pdf->SetFont('Arial','B',11);	 
     $pdf->Rect(6,$y+102,5,4);               	            $cont= 'Radiograf Columna';             $pdf->Text(12,$y+104,$cont);                
															$cont= 'Lumbo Sacra';                   $pdf->Text(12,$y+108,$cont);                
	 $pdf->Rect(53,$y+102,5,4);							    $cont= 'Radiograf de Torax';        	$pdf->Text(59,$y+106,$cont);		        
	 $pdf->Rect(105,$y+102,5,4);                            $cont= 'EKG';                        	$pdf->Text(113,$y+106,$cont);		        
	 $pdf->Rect(130,$y+102,5,4);                            $cont= 'Otros : ________________________';
	 $pdf->Text(136,$y+106,$cont);		        	 
 
	 $pdf->SetFont('Arial','B',8);

	 $cont= "LABORATORIO CLINICO";                        $pdf->Text(6,$y+114,$cont);
	 $cont= "Dra. Adriana Castañeda";                     $pdf->Text(6,$y+117,$cont);
     $cont= "Cll. 5 No. 0-10E B. La Ceiba";               $pdf->Text(6,$y+120,$cont);	 
     $cont= "Tel. 577 4350";                              $pdf->Text(6,$y+123,$cont);
     $pdf->Line(6,$y+126,46,$y+126);
	 
     $cont= "RAYOS X";                                    $pdf->Text(57,$y+114,$cont);
	 $cont= "Centro Medico Los Samanes";                  $pdf->Text(57,$y+117,$cont);
     $cont= "SOMEDIAG";                                   $pdf->Text(57,$y+120,$cont);	 
     $pdf->Line(57,$y+126,97,$y+126);
	 
     $cont= "CEMERAD";                                    $pdf->Text(110,$y+114,$cont);
	 $cont= "Av. 0 No. 10-78 Ofc 201";                    $pdf->Text(110,$y+117,$cont);
     $cont= "Edif Colegio Medico";                        $pdf->Text(110,$y+120,$cont);
     $cont= "Telefono: 572 6259";                         $pdf->Text(110,$y+123,$cont);	 
     $pdf->Line(110,$y+126,150,$y+126);
	 
     $cont= "Centro Especializado del";                   $pdf->Text(162,$y+114,$cont);
	 $cont= "Corazon F.C.B.";                             $pdf->Text(162,$y+117,$cont);
     $cont= "Clinica Santa Ana";                          $pdf->Text(162,$y+120,$cont);
     $cont= "";                                           $pdf->Text(162,$y+123,$cont);	 
     $pdf->Line(162,$y+126,202,$y+126);
	 	 
     /// Completo la Informacion del Tercero
	 $pdf->SetFont('Arial','B',11);
     $cont= $row['nombre'];                              	$pdf->Text(8,$y+16,$cont);
     $cont= $row['nit'];                                 	$pdf->Text(120,$y+16,$cont);	 
     $cont= substr($row['fechadoc'],8,2)."/".substr($row['fechadoc'],5,2)."/".substr($row['fechadoc'],0,4);        	$pdf->Text(170,$y+16,$cont);	 	 

     $cont= $row['cargo'];                              	$pdf->Text(8,$y+24,$cont);
	 $cont= $row['nombre'];                              	$pdf->Text(120,$y+24,$cont);

     /// Segunda Hoja
	 $y=166;
	 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa.jpg',5,$y-8,60,14,0);
	      
	 $pdf->SetFont('Arial','B',14);
	 $cont= 'ORDEN DE SERVICIO ';                           $pdf->Text(120,$y,$cont);		 
	 
	 $pdf->Rect(5,$y+8,200,18);	 
	 
	 $cont= 'DESCRIPCION DE LOS SERVICIOS';                 $pdf->Text(60,$y+33,$cont);		 	 

	 $pdf->SetFont('Arial','B',8);	 
	 $cont= 'NOMBRE DEL PACIENTE : ';                       $pdf->Text(6,$y+12,$cont);		 	 
	 $cont= 'IDENTIFICACION : ';                            $pdf->Text(119,$y+12,$cont);		 	 
	 $cont= 'FECHA : ';                                     $pdf->Text(168,$y+12,$cont);		 	 
	 $cont= 'CARGO : ';                                     $pdf->Text(6,$y+20,$cont);		 	 
	 $cont= 'EMPRESA SOLICITANTE : ';                       $pdf->Text(119,$y+20,$cont);		 	 
	 	 
	 $pdf->SetFont('Arial','B',11);	 
	 $cont= 'EXAMEN MEDICO OCUPACIONAL';      	            $pdf->Text(6,$y+41,$cont);	                $pdf->Rect(80,$y+37,8,6);
	 $cont= 'OPTOMETRIA';                                  	$pdf->Text(100,$y+41,$cont);		        $pdf->Rect(140,$y+37,8,6);
	 $cont= 'AUDIOMETRIA';                     	            $pdf->Text(160,$y+41,$cont);		        $pdf->Rect(195,$y+37,8,6);

	 $cont= 'ESPIROMETRIA';                 	            $pdf->Text(6,$y+47,$cont);	                $pdf->Rect(80,$y+43,8,6);
	 $cont= 'LABORATORIOS';                                	$pdf->Text(100,$y+47,$cont);	            $pdf->Rect(140,$y+43,8,6);	 
	 $cont= 'RX';                            	            $pdf->Text(160,$y+47,$cont);		        $pdf->Rect(195,$y+43,8,6);
	 
     $cont= 'OTROS : ';          	                        $pdf->Text(6,$y+54,$cont);	                $pdf->Line(25,$y+54,205,$y+54);	
     
	 /// Laboratorios

	 $pdf->SetFont('Arial','B',14);	
	 $cont= 'EXAMENES DE LABORATORIO';                      $pdf->Text(63,$y+65,$cont);	
	 $pdf->SetFont('Arial','B',12);
	 
     $pdf->Rect(6,$y+70,6,5);               	            $cont= 'Serologia ';                    $pdf->Text(14,$y+75,$cont);                
	 $pdf->Rect(55,$y+70,6,5);								$cont= 'Acido Urico';                 	$pdf->Text(65,$y+75,$cont);		        
	 $pdf->Rect(105,$y+70,6,5);							    $cont= 'Baciloscopia ';      	        $pdf->Text(115,$y+75,$cont);             
	 $pdf->Rect(155,$y+70,6,5);                             $cont= 'Colesterol HDL';              	$pdf->Text(165,$y+75,$cont);		        
     $pdf->Rect(6,$y+75,6,5);               	            $cont= 'Colesterol Total';              $pdf->Text(14,$y+80,$cont);                
	 $pdf->Rect(55,$y+75,6,5);								$cont= 'Triglicéridos';                	$pdf->Text(65,$y+80,$cont);		        
	 $pdf->Rect(105,$y+75,6,5);							    $cont= 'Coprológico';        	        $pdf->Text(115,$y+80,$cont);             
	 $pdf->Rect(155,$y+75,6,5);                             $cont= 'Cuadro Hemático';              	$pdf->Text(165,$y+80,$cont);		        
     $pdf->Rect(6,$y+80,6,5);               	            $cont= 'Frotis de Garganta';            $pdf->Text(14,$y+85,$cont);                
	 $pdf->Rect(55,$y+80,6,5);								$cont= 'KOH de Uñas';                	$pdf->Text(65,$y+85,$cont);		        
	 $pdf->Rect(105,$y+80,6,5);							    $cont= 'Glicemia';        	            $pdf->Text(115,$y+85,$cont);             
	 $pdf->Rect(155,$y+80,6,5);                             $cont= 'Hemoclasificación';            	$pdf->Text(165,$y+85,$cont);		        
     $pdf->Rect(6,$y+85,6,5);               	            $cont= 'Hepatitis B';                   $pdf->Text(14,$y+90,$cont);                
	 $pdf->Rect(55,$y+85,6,5);								$cont= 'HIV';                        	$pdf->Text(65,$y+90,$cont);		        
	 $pdf->Rect(105,$y+85,6,5);							    $cont= 'Parcial de Orina';              $pdf->Text(115,$y+90,$cont);             
	 $pdf->Rect(155,$y+85,6,5);                             $cont= 'Serologia No. 1';            	$pdf->Text(165,$y+90,$cont);		        

	 $pdf->SetFont('Arial','B',14);	 
	 $cont= 'RADIOGRAFIAS';                                 $pdf->Text(80,$y+100,$cont);		 	 

	 $pdf->SetFont('Arial','B',11);	 
     $pdf->Rect(6,$y+102,5,4);               	            $cont= 'Radiograf Columna';             $pdf->Text(12,$y+104,$cont);                
															$cont= 'Lumbo Sacra';                   $pdf->Text(12,$y+108,$cont);                
	 $pdf->Rect(53,$y+102,5,4);							    $cont= 'Radiograf de Torax';        	$pdf->Text(59,$y+106,$cont);		        
	 $pdf->Rect(105,$y+102,5,4);                            $cont= 'EKG';                        	$pdf->Text(113,$y+106,$cont);		        
	 $pdf->Rect(130,$y+102,5,4);                            $cont= 'Otros : ________________________';
	 $pdf->Text(136,$y+106,$cont);		        	 
	 
	 $pdf->SetFont('Arial','B',8);

	 $cont= "LABORATORIO CLINICO";                        $pdf->Text(6,$y+114,$cont);
	 $cont= "Dra. Adriana Castañeda";                     $pdf->Text(6,$y+117,$cont);
     $cont= "Cll. 5 No. 0-10E B. La Ceiba";               $pdf->Text(6,$y+120,$cont);	 
     $cont= "Tel. 577 4350";                              $pdf->Text(6,$y+123,$cont);
     $pdf->Line(6,$y+126,46,$y+126);
	 
     $cont= "RAYOS X";                                    $pdf->Text(57,$y+114,$cont);
	 $cont= "Centro Medico Los Samanes";                  $pdf->Text(57,$y+117,$cont);
     $cont= "SOMEDIAG";                                   $pdf->Text(57,$y+120,$cont);	 
     $pdf->Line(57,$y+126,97,$y+126);
	 
     $cont= "CEMERAD";                                    $pdf->Text(110,$y+114,$cont);
	 $cont= "Av. 0 No. 10-78 Ofc 201";                    $pdf->Text(110,$y+117,$cont);
     $cont= "Edif Colegio Medico";                        $pdf->Text(110,$y+120,$cont);
     $cont= "Telefono: 572 6259";                         $pdf->Text(110,$y+123,$cont);	 
     $pdf->Line(110,$y+126,150,$y+126);
	 
     $cont= "Centro Especializado del";                   $pdf->Text(162,$y+114,$cont);
	 $cont= "Corazon F.C.B.";                             $pdf->Text(162,$y+117,$cont);
     $cont= "Clinica Santa Ana";                          $pdf->Text(162,$y+120,$cont);
     $cont= "";                                           $pdf->Text(162,$y+123,$cont);	 
     $pdf->Line(162,$y+126,202,$y+126);
	 
     /// Completo la Informacion del Tercero
     $vsql = "SELECT * FROM terceros T INNER JOIN documentos D ON (T.terid = D.terid1)
	    	  WHERE D.docuid=".$id;
	   
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $row = mysql_fetch_array($result);
	 	 
	 $pdf->SetFont('Arial','B',11);
     $cont= $row['nombre'];                              	$pdf->Text(8,$y+16,$cont);
     $cont= $row['nit'];                                 	$pdf->Text(120,$y+16,$cont);	 
     $cont= substr($row['fechadoc'],8,2)."/".substr($row['fechadoc'],5,2)."/".substr($row['fechadoc'],0,4);        	$pdf->Text(170,$y+16,$cont);	 	 

     $cont= $row['cargo'];                              	$pdf->Text(8,$y+24,$cont);
	 $cont= $row['nombre'];                              	$pdf->Text(120,$y+24,$cont);
	    
     //// Finalmente, muestro la X de cada procedimiento en la Hoja de la Orden de Servicio
     $vsql = "SELECT P.eximin , P.eximax FROM documentos D INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid)		  
			  INNER JOIN productos P ON (DD.itemid = P.itemid) WHERE D.docuid=".$id;
	   
     $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);

	 $pdf->SetFont('Arial','',12);	 
	 $cont= 'x';      	            
     while($row = mysql_fetch_array($result)) 
     {
        $pdf->Text($row['eximin'],$row['eximax'],$cont);	
        $pdf->Text($row['eximin'],($row['eximax']+($y-18)),$cont);			
     }

	 $pdf->Output("ordenes/Ordenen_".$id.".pdf");
	 $pdf->Output();
  }
			
  mysql_free_result($result); 
  mysql_close($conex);			  
?> 