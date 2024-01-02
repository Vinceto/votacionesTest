<?php
    include "conexion.php";
    // funcion para crear un array con los datos necesarios
    function cargarDatos($conexion, $tabla, $campoId, $campoNombre, $campoRegionId=null)
    {
        $datos = array();
        if($campoRegionId != null){
            $consulta = $conexion->query("SELECT $campoId, $campoNombre, $campoRegionId FROM $tabla");
            while ($row = $consulta->fetch_assoc()) {
                $datos[$row[$campoId]]['id'] = $row['id'];
                $datos[$row[$campoId]]['nombre'] = $row['nombre'];
                $datos[$row[$campoId]]['region_id'] = $row['region_id'];
            }
        }else{
            $consulta = $conexion->query("SELECT $campoId, $campoNombre FROM $tabla");
            while ($row = $consulta->fetch_assoc()) {
                $datos[$row[$campoId]] = $row[$campoNombre];
            }
        }
        

        

        return $datos;
    }
    //busca los datos de regiones, comunas y candidatos para el ejemplo
    $regiones = cargarDatos($conexion, "Regiones", "id", "nombre");
    $comunas = cargarDatos($conexion, "Comunas", "id", "nombre", "region_id");
    $candidatos = cargarDatos($conexion, "Candidatos", "id", "nombre");

    $conexion->close();
    // retorna respuesta en json
    echo json_encode(array('regiones' => $regiones, 'comunas' => $comunas, 'candidatos' => $candidatos));
?>