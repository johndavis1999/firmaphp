<?php
class Xades_sing {
          private $rut;
          private $rfirmado;
          private $nombrefir;

    public function __construct(){


    }
public function firmarXml(string $ruta,string $rutafirmado,string $nombrefirmado){
    $this->rut=$ruta;
    
$rutaCertificado="firma\certificado.p12";
$passwordp12="password";
#$passwordp12="JohnDavis1999";
$comando = "java -jar CalitosFact.jar $rutaCertificado $passwordp12 $ruta $rutafirmado $nombrefirmado.xml";
exec($comando, $salida, $codigoSalida);

if (isset($salida[5])){

    $errores = explode(":", $salida[5]);
    return $errores[3];
}else{

return $salida[4];    }

}   

}
?>