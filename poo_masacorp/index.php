<?php
    //incluir la clase
    include_once('persona.php');
    //instanciar la clase
    $persona = new persona();

    //asignar valores a las propiedades del objeto
    $persona->setEdad(30);
    $persona->setAltura(1.80);
    $persona->setPeso(80);

    //impresiones de los resultados
    echo "<br>Edad: $persona->edad";
    echo "<br>Altura: $persona->altura";
    echo "<br>Peso: $persona->peso";
    echo "<br>IMC: ".$persona->imc();
    
?>