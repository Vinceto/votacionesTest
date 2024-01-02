<?php
include "conexion.php";
// Verifica si la petición proviene de AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // La solicitud es AJAX

    // Función para procesar el formulario
    function procesarFormulario() {
        global $conexion;
        // Validar campos del formulario
        $nombreApellido = validarCampo($_POST['nombreApellido']);
        $alias = validarCampo($_POST['alias']);
        $rut = validarCampo($_POST['rut']);
        $email = validarCampo($_POST['email']);
        $region = validarCampo($_POST['region']);
        $comuna = validarCampo($_POST['comuna']);
        $candidato = validarCampo($_POST['candidato']);
        $comoSeEntero = validarCheckbox($_POST['comoSeEntero']);

        // Validar duplicación de votos por RUT (debes implementar tu propia lógica)
        $votoDuplicado = validarDuplicacionPorRUT($conexion, $rut);
        
        if ($votoDuplicado){
            $respuesta['status'] = 'error';
            $respuesta['mensaje'] = 'Error, el rut '.$rut.' ya voto';
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            return false;
        }
        
        // Inicializa el arreglo de respuesta
        $respuesta = array();

        // Si hay errores, actualiza la respuesta con el estado y mensaje de error
        if (!$nombreApellido || !$alias || !$rut || !$email || !$region || !$comuna || !$candidato || !$comoSeEntero) {
            $respuesta['status'] = 'error';
            $respuesta['mensaje'] = 'Error al procesar el formulario. Verifica que todos los campos estén llenos correctamente.';
        } else {
            try {
                // Si no hay errores, se guarda los datos en la base de datos
                guardarVotoEnBD($conexion, $nombreApellido, $alias, $rut, $email, $region, $comuna, $candidato, $comoSeEntero);
            } catch (PDOException $e) {
                // Manejo de errores
                $respuesta['status'] = 'error';
                $respuesta['mensaje'] = 'Error al guardar el voto: ' . $e->getMessage();
            }
            // Actualiza la respuesta con el estado y mensaje de éxito
            $respuesta['status'] = 'success';
            $respuesta['mensaje'] = '¡Voto registrado exitosamente!';
        }
        // Devuelve la respuesta en formato JSON
        header('Content-Type: application/json');
        echo json_encode($respuesta);
    }

    // Función para validar campos
    function validarCampo($campo) {
        if(!empty($campo)){
            return $campo;
        }else{
            return false;
        }
    }

    // Función para validar checkboxes
    function validarCheckbox($checkboxArray) {
        if(is_array($checkboxArray)  && count($checkboxArray) >= 2){
            return $checkboxArray;
        }else{
            return false;
        }
    }

    // Función para validar la duplicación de votos por RUT
    function validarDuplicacionPorRUT($conexion, $rut) {
        // Prepara la consulta SQL
        $consulta = $conexion->prepare("SELECT COUNT(*) FROM votos WHERE rut = ?");
    
        // Verifica si la preparación de la consulta fue exitosa
        if ($consulta === false) {
            die('Error en la preparación de la consulta: ' . $conexion->error);
        }
    
        // Asigna el valor al parámetro de la consulta
        $consulta->bind_param('s', $rut);
    
        // Ejecuta la consulta
        $consulta->execute();
    
        // Obtiene el resultado
        $consulta->bind_result($conteo);
    
        // Obtiene el valor del resultado
        $consulta->fetch();
    
        // Cierra la consulta
        $consulta->close();
        // Devuelve true si hay duplicación, false si no
        return $conteo > 0;
    }

    // Función para guardar el voto en la base de datos
    function guardarVotoEnBD($conexion, $nombreApellido, $alias, $rut, $email, $region, $comuna, $candidato, $comoSeEntero) {
        // Prepara la consulta SQL
        $consulta = $conexion->prepare("INSERT INTO votos (nombre_apellido, alias, rut, email, region, comuna, candidato, como_se_entero) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($consulta === false) {
            die('Error en la preparación de la consulta: ' . $conexion->error);
        }
        $comoSeEnteroString = implode(", ", $comoSeEntero);
        // Asigna valores a los parámetros de la consulta
        $resultado = $consulta->bind_param('ssssssss', $nombreApellido, $alias, $rut, $email, $region, $comuna, $candidato, $comoSeEnteroString);

        if ($resultado === false) {
            die('Error al asignar parámetros: ' . $conexion->error);
        }

        // Ejecuta la consulta
        $consulta->execute();

        if ($consulta->affected_rows === -1) {
            die('Error al ejecutar la consulta: ' . $conexion->error);
        }

        // Cierra la conexión
        $conexion = null;
    }

    // Procesa el formulario
    procesarFormulario();
} else {
    // La solicitud no es AJAX, redirige a la página de inicio
    header("Location: index.html");
    exit();
}
?>