<?php
/**
 * This file is part of the OpenPyme1.
 * 
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 * 
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);
error_reporting( E_ERROR | E_PARSE ); // ->usar ( E_ALL );

include('../_conexion.php');

/**
 * El mambo de este Script: Aumentar o disminuir los correlativos de ticket
 * el script por default se ejecuta en modo Simulacion. 
 * para realizar una ejecucion definitiva, envíe el parametro simular: 
 * correlatives_fixer.php?simular=false
 */

 // Parametros
$fixPositions = -2;
$simular = ((isset($_REQUEST['simular'])) ? $_REQUEST['simular'] : 'true');
echo "<hr>";
if($simular == 'true'){
    echo "SIMULACION EN CURSO --- Use correlatives_fixer.php?simular=false para aplicar cambios";
}else{
    echo "PROCESO DEFINITIVO para fixear los tickets, en curso";
}
echo "<hr>";

$error = false; //True sólo si se da error en algun momento.

$correlativosTIK = _query(
    "SELECT id_factura, numero_doc, num_fact_impresa
    FROM factura WHERE tipo_documento = 'TIK'"
);

$counter = 1;

_begin();

//Fixear cada uno de los correlativos en tabla factura
while($TIK = _fetch_array($correlativosTIK)){
    //obtener longitud de caracteres (para rellenar ceros)
    $correlativoFormato = explode('_', $TIK['numero_doc']);
    $longitudCorrelativo = strlen($correlativoFormato[0]); //Inicia desde 1
    $nuevoCorrelativo = (intval($correlativoFormato[0]) + $fixPositions);
    $nuevoCorrelativo = str_pad($nuevoCorrelativo, $longitudCorrelativo, "0", STR_PAD_LEFT)."_TIK";

    //Obtener el nuevo nuevo numero de factura impresa.
    $nuevo_num_fact_impresa = $TIK['num_fact_impresa'] + $fixPositions;

    echo "$counter) ID Factura: $TIK[id_factura] <br>numero_doc -> Anterior: $TIK[numero_doc], ";
    echo " Nuevo: $nuevoCorrelativo <br>";
    echo "  num_fact_impresa -> Anterior: $TIK[num_fact_impresa]";
    echo " Nuevo: $nuevo_num_fact_impresa";

    _update('factura',
        [
            "numero_doc"       => $nuevoCorrelativo,
            "num_fact_impresa" => $nuevo_num_fact_impresa
        ],
        "id_factura=$TIK[id_factura]"
    );

    if(_affected_rows() < 0){
        $error = true;
    }

    echo "<br><br>";
    $counter += 1;
}

echo "<hr>";
//CODIGO PARA FIXEAR EL CORRELATIVO DISPONIBLE PARA LA CAJA
echo "Fixeando el correlativo disponible de la caja <br>";

$caja = _fetch_array(_query(
    "SELECT correlativo_dispo FROM caja WHERE id_sucursal=1 AND id_caja=1"
));

$nuevoCorrelativo_dispo = $caja['correlativo_dispo'] + $fixPositions;

echo "Correlativo Disponible:<br>";
echo "Anterior: $caja[correlativo_dispo] -> Nuevo: $nuevoCorrelativo_dispo";

_update('caja',
    ["correlativo_dispo" => $nuevoCorrelativo_dispo],
    "id_caja=1 AND id_sucursal=1"
);

if(_affected_rows() > 0){
    echo " -> STATUS [OK]";
}else{
    echo " -> STATUS [ERROR]";
    $error = true;
}

echo "<hr>";
//CODIGO PARA FIXEAR DE LA MISMA MANERA, LOS CORTES DE CAJA.
echo "Fixeando los ticket de los cortes... <br>";

$cortes = _query(
    "SELECT id_corte, tiket, tinicio, tfinal, tipo_corte
    FROM controlcaja WHERE id_sucursal=1"
);

while($corte = _fetch_array($cortes)){
    $currentTiket   = $corte['tiket'];
    $currentTinicio = $corte['tinicio'];
    $currentTfinal  = $corte['tfinal'];

    $nuevoTiket   = (($currentTiket > 0) ? $currentTiket   + $fixPositions : $currentTiket);
    $nuevoTinicio = (($currentTinicio > 0) ? $currentTinicio   + $fixPositions : $currentTinicio);
    $nuevoTfinal  = (($currentTfinal > 0) ? $currentTfinal   + $fixPositions : $currentTfinal);

    echo "ID Corte: $corte[id_corte], Tipo: $corte[tipo_corte] <br>";
    echo "tiket -> Anterior: $currentTiket, Nuevo:  $nuevoTiket <br>";
    echo "tinicio -> Anterior: $currentTinicio, Nuevo: $nuevoTinicio <br>";
    echo "tfinal  -> Anterior: $currentTfinal, Nuevo: $nuevoTfinal <br>";

    _update('controlcaja',
        [
            "tiket"   => $nuevoTiket,
            "tinicio" => $nuevoTinicio,
            "tfinal"  => $nuevoTfinal
        ],
        "id_corte=$corte[id_corte]"
    );

    if(_affected_rows() < 0 ){
        echo " STATUS [ERROR]";
        echo $error = true;
    }else{
        echo " STATUS [OK]";
    }

    echo "<br><br>";

}
echo "<hr>";
//------------------------------------------------------------------------------
if($simular == 'true'){
    _rollback();
    echo "Simulación terminada. ROLLBACK Ejecutado.";
}else{

    if($error == false){
        _commit();
        echo "COMMIT -> Todo bien, todo correcto";
    }else{
        _rollback();
        echo "ROLLBACK -> Hubieron errores durante la ejecución.";
    }
    
}
echo "<hr>";


?>