<?php
//hacemos llamada a nuestra clase para firmar
require_once("firmador.php");
//creamos el nuestro objeto con nuestra clase
$firmando=new Xades_sing();
//preparamos nuestras variables $xmlSinfirmar, $rutadefirmados,$nombre
$xmlSinfirmar="sinfirmar\xmlsinfirmar.xml";

$rutadefirmados="firmados";

$nombrefirmado="xmlsing_xades2";

$salida=$firmando->firmarXml($xmlSinfirmar,$rutadefirmados,$nombrefirmado);

echo $salida;

?>