<?php


//RUTA DEL FIRMADO
$file ="xmlfirmados/$claveAcceso.xml";

//PERARAR DATOS PARA ENVIO DE LA FACTURA MEDIENTA SOAP 
$webRecepcion='https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
$contenido = file_get_contents($file);
$parametros = array("xml"=>$contenido);

//PROCESO DE ENVIO AL SRI
try {
    
// SI EL WWE SERVICE ESTA DISPONIBLE SEGUIRA ON LAS LINEAS SIGUENTES
    $webServiceRecepcion =  new SoapClient($webRecepcion);

    $result = $webServiceRecepcion->validarComprobante($parametros);
    
    $estadorecepcion=$result->RespuestaRecepcionComprobante->estado;
    ///SI ES ESTADO ES DEVUELTA  PREPARAR UN ARREGLO PARA DEVOLVER UN RESULTADO AL LLAMADO JAVASCRIPT DE LA FUNCION O MANEJAR LOS ESTADOS
    if ($estadorecepcion=="DEVUELTA"){
                    $smsdevuelto=$result->{'RespuestaRecepcionComprobante'}->{'comprobantes'}->{'comprobante'}->{'mensajes'}->{'mensaje'}->{'mensaje'};
                    //ALMACEMNA EN LA BASE DE DATOS EL ESTADO DEVUELTO
                     $sql = "UPDATE facturas SET estadoSri='DEVUELTO', erroresSri='$smsdevuelto' WHERE clave_accesso='$claveAcceso'";
                                $query_update = mysqli_query($con,$sql);
                    $estadows="$estadorecepcion";
                                         $data = array();
             $data['estado'] = $estadows;
             $data['error'] = $smsdevuelto;

            echo json_encode($data);
            //ESPERAR 2 SEGUNDOS
                  sleep(2);
    }else{

     //SI EL ESTADO ES ABROBADO SE SOLICITATA EL DOCUMENTO APROBADDO CON FECHA Y FECHA Y AHORA
            sleep(4);
            //Autorizar Comprobante
            //PERARAR DATOS PARA LA SOLICITID DEL COMPROBANTE AUNTORIZADO MEDIENTA SOAP 
            $webAutoriza='https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
            $parametrosAutoriza = array("claveAccesoComprobante" => $claveAcceso);

            $client = new SoapClient($webAutoriza,array('trace' => 1));
            $result=$client->autorizacionComprobante($parametrosAutoriza);
            //Capturar estado y fecha de autorizacion
            
            $estadows= $result->{'RespuestaAutorizacionComprobante'}->{'autorizaciones'}->{'autorizacion'}->{'estado'};
            $fautorizacion=$result->{'RespuestaAutorizacionComprobante'}->{'autorizaciones'}->{'autorizacion'}->{'fechaAutorizacion'};

            //guardar xml Recibido Autorizado
            $nombreArchivo = "xmlaprobados/$claveAcceso.xml";
            $archivo = fopen($nombreArchivo, "w");
            fwrite($archivo, $client->__getLastResponse());
            fclose($archivo);
            $estado=$estadows;
            $sql = "UPDATE facturas SET estadoSri='$estadows', f_autorizacionsri='$fautorizacion' WHERE clave_accesso='$claveAcceso'";
                                $query_update = mysqli_query($con,$sql);
                                 $data = array();
             $data['estado'] = $estado;
             $data['fechaSri'] = $fautorizacion;
             $data['clave']=$claveAcceso;
               echo json_encode($data);
}
} catch (SoapFault $fault) {
        $data = array();
             $data['estado'] = $fault->faultstring;
             
               echo json_encode($data);
}

?>