<?php
session_start();

// Si se recibe la acción de reinicio del chat, se borra la conversación.
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['conversacion']);
    echo json_encode(['success' => true]);
    exit;
}

// Cargar las respuestas desde respuestas.json
$respuestas = json_decode(file_get_contents('respuestas.json'), true);

/**
 * Función para obtener una respuesta aleatoria de una categoría (usando el JSON)
 */
function obtenerRespuesta($categoria) {
    global $respuestas;
    if (isset($respuestas[$categoria]) && is_array($respuestas[$categoria])) {
        return $respuestas[$categoria][array_rand($respuestas[$categoria])];
    }
    return "Lo siento, no tengo respuesta para esa categoría.";
}

/**
 * Función para detectar las categorías según palabras clave en el mensaje.
 */
function getMatchedCategories($mensaje) {
    $mensajeLower = strtolower($mensaje);
    // Mapeo de palabras clave a categorías (incluye los mapeos originales y las 100 nuevas)
    $map = [
        // Mapeos originales
        'hola'             => 'saludos',
        'buenos días'      => 'saludos',
        'buen dia'         => 'saludos',
        'buenas tarde'     => 'saludos',
        'buenas noche'     => 'saludos',
        'te amo'           => 'te_amo',
        'san valentín'     => 'san_valentin',
        'promesa'          => 'promesas',
        'regalo'           => 'regalos',
        'cita'             => 'citas_romanticas',
        'broma'            => 'bromas_romanticas',
        'reflexión'        => 'reflexiones',
        'cumpleaños'       => 'cumpleaños',
        'adios'            => 'despedidas',
        'hasta luego'      => 'despedidas',
        'ok'               => 'ok',
        'está bien'        => 'bien',
        'segura'           => 'segura',
        'me amas'          => 'me_amas',
        'confias en mi'    => 'confias_en_mi',
        'sueño'            => 'sueños',
        'soñar'            => 'sueños',
        'futuro'           => 'futuro',
        'mañana'           => 'mañana',
        'abrazo'           => 'abrazos',
        'abrazar'          => 'abrazos',
        'beso'             => 'besos',
        'besar'            => 'besos',
        'familia'          => 'familia',
        'hijos'            => 'hijos',
        'viaje'            => 'viajes',
        'viajar'           => 'viajes',
        'música'           => 'música',
        'canción'          => 'música',
        'comida'           => 'comida',
        'cocinar'          => 'comida',
        'animal'           => 'animales',
        'mascota'          => 'animales',
        'apoyo'            => 'apoyo',

        // Nuevas palabras (tal como se definen en la lista)
        'saludos'          => 'saludos',
        'te_amo'           => 'te_amo',
        'san_valentin'     => 'san_valentin',
        'promesas'         => 'promesas',
        'regalos'          => 'regalos',
        'citas_romanticas' => 'citas_romanticas',
        'bromas_romanticas'=> 'bromas_romanticas',
        'reflexiones'      => 'reflexiones',
        'despedidas'       => 'despedidas',
        'default'          => 'default',
        'cumpleaños'       => 'cumpleaños',
        'ok'               => 'ok',
        'bien'             => 'bien',
        'segura'           => 'segura',
        'me_amas'          => 'me_amas',
        'confias_en_mi'    => 'confias_en_mi',
        'como estas'       => 'como estas',
        'quien'            => 'quien',
        'como'             => 'como',
        'donde'            => 'donde',
        'porque'           => 'porque',
        'por que'          => 'por que',
        'cuando'           => 'cuando',
        'buen dia'         => 'buen dia',
        'buenas tarde'     => 'buenas tarde',
        'buenas noche'     => 'buenas noche',
        'clima'            => 'clima',
        'hora'             => 'hora',
        'que'              => 'que',
        'piensas'          => 'piensas',
        'noche'            => 'noche',
        'estrellas'        => 'estrellas',
        'colores'          => 'colores',
        'ropa'             => 'ropa',
        'vestidos'         => 'vestidos',
        'compra'           => 'compra',
        'eres'             => 'eres',
        'nombre'           => 'nombre',
        'edad'             => 'edad',
        'creador'          => 'creador',
        'sueños'           => 'sueños',
        'futuro'           => 'futuro',
        'abrazos'          => 'abrazos',
        'besos'            => 'besos',
        'familia'          => 'familia',
        'viajes'           => 'viajes',
        'música'           => 'música',
        'comida'           => 'comida',
        'animales'         => 'animales',
        'apoyo'            => 'apoyo',
        'inspiracion'      => 'inspiracion',
        'confesiones'      => 'confesiones',
        'poesia'           => 'poesia',
        'cariño'           => 'cariño',
        'compromiso'       => 'compromiso',
        'dedicatorias'     => 'dedicatorias',
        'encanto'          => 'encanto',
        'destino'          => 'destino',
        'ternura'          => 'ternura',
        'pasion'           => 'pasion',
        'romance'          => 'romance',
        'serenata'         => 'serenata',
        'devocion'         => 'devocion',
        'eterno'           => 'eterno',
        'complice'         => 'complice',
        'sinceridad'       => 'sinceridad',
        'seduccion'        => 'seduccion',
        'elegancia'        => 'elegancia',
        'amor_veraz'       => 'amor_veraz',
        'amor_autentico'   => 'amor_autentico',
        'adoracion'        => 'adoracion',
        'querencia'        => 'querencia',
        'encuentro'        => 'encuentro',
        'deseo'            => 'deseo',
        'susurros'         => 'susurros',
        'caricias'         => 'caricias',
        'instantes'        => 'instantes',
        'magia'            => 'magia',
        'alma'             => 'alma',
        'dulzura'          => 'dulzura',
        'intimidad'        => 'intimidad',
        'anhelo'           => 'anhelo',
        'recuerdo'         => 'recuerdo',
        'silencio'         => 'silencio',
        'armonía'          => 'armonía',
        'euforia'          => 'euforia',
        'mirada'           => 'mirada',
        'brisa'            => 'brisa',
        'amanecer'         => 'amanecer',
        'océano'           => 'océano',
        'resplandor'       => 'resplandor',
        'crepúsculo'       => 'crepúsculo',
        'fragancia'        => 'fragancia',
        'vibración'        => 'vibración',
        'júbilo'           => 'júbilo',
        'solaz'            => 'solaz',
        'desvelo'          => 'desvelo',
        'llama'            => 'llama',
        'cautiverio'       => 'cautiverio',
        'exquisitez'       => 'exquisitez'
    ];
    
    $matches = [];
    foreach ($map as $phrase => $categoria) {
        if (strpos($mensajeLower, $phrase) !== false) {
            $matches[] = $categoria;
        }
    }
    return array_unique($matches);
}

// Procesamiento de la petición AJAX para enviar un mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'send') {
    $mensaje_usuario = isset($_POST['mensaje']) ? $_POST['mensaje'] : '';
    if ($mensaje_usuario === '') {
        echo json_encode(['error' => 'Mensaje vacío']);
        exit;
    }
    if (!isset($_SESSION['conversacion'])) {
        $_SESSION['conversacion'] = [];
    }
    
    $categoriasCoincidentes = getMatchedCategories($mensaje_usuario);
    
    // Actualizamos el priorityMap para incluir las nuevas categorías
    $priorityMap = [
        // Prioridad 1: Saludos
        'saludos'          => 1,
        
        // Prioridad 2: Frases cortas, románticas o consultas generales
        'te_amo'           => 2,
        'san_valentin'     => 2,
        'promesas'         => 2,
        'regalos'          => 2,
        'citas_romanticas' => 2,
        'bromas_romanticas'=> 2,
        'reflexiones'      => 2,
        'cumpleaños'       => 2,
        'despedidas'       => 2,
        'ok'               => 2,
        'bien'             => 2,
        'segura'           => 2,
        'me_amas'          => 2,
        'confias_en_mi'    => 2,
        'default'          => 2,
        'como estas'       => 2,
        'quien'            => 2,
        'como'             => 2,
        'donde'            => 2,
        'porque'           => 2,
        'por que'          => 2,
        'cuando'           => 2,
        'buen dia'         => 2,
        'buenas tarde'     => 2,
        'buenas noche'     => 2,
        'clima'            => 2,
        'hora'             => 2,
        'que'              => 2,
        'piensas'          => 2,
        'noche'            => 2,
        'estrellas'        => 2,
        'colores'          => 2,
        'ropa'             => 2,
        'vestidos'         => 2,
        'compra'           => 2,
        'eres'             => 2,
        'nombre'           => 2,
        'edad'             => 2,
        'creador'          => 2,
        
        // Prioridad 3: Categorías más sentimentales o extensas
        'sueños'           => 3,
        'futuro'           => 3,
        'mañana'           => 3,
        'abrazos'          => 3,
        'besos'            => 3,
        'familia'          => 3,
        'hijos'            => 3,
        'viajes'           => 3,
        'música'           => 3,
        'comida'           => 3,
        'animales'         => 3,
        'apoyo'            => 3,
        'inspiracion'      => 3,
        'confesiones'      => 3,
        'poesia'           => 3,
        'cariño'           => 3,
        'compromiso'       => 3,
        'dedicatorias'     => 3,
        'encanto'          => 3,
        'destino'          => 3,
        'ternura'          => 3,
        'pasion'           => 3,
        'romance'          => 3,
        'serenata'         => 3,
        'devocion'         => 3,
        'eterno'           => 3,
        'complice'         => 3,
        'sinceridad'       => 3,
        'seduccion'        => 3,
        'elegancia'        => 3,
        'amor_veraz'       => 3,
        'amor_autentico'   => 3,
        'adoracion'        => 3,
        'querencia'        => 3,
        'encuentro'        => 3,
        'deseo'            => 3,
        'susurros'         => 3,
        'caricias'         => 3,
        'instantes'        => 3,
        'magia'            => 3,
        'alma'             => 3,
        'dulzura'          => 3,
        'intimidad'        => 3,
        'anhelo'           => 3,
        'recuerdo'         => 3,
        'silencio'         => 3,
        'armonía'          => 3,
        'euforia'          => 3,
        'mirada'           => 3,
        'brisa'            => 3,
        'amanecer'         => 3,
        'océano'           => 3,
        'resplandor'       => 3,
        'crepúsculo'       => 3,
        'fragancia'        => 3,
        'vibración'        => 3,
        'júbilo'           => 3,
        'solaz'            => 3,
        'desvelo'          => 3,
        'llama'            => 3,
        'cautiverio'       => 3,
        'exquisitez'       => 3
    ];
    
    if (count($categoriasCoincidentes) > 1) {
        usort($categoriasCoincidentes, function($a, $b) use ($priorityMap) {
            return ($priorityMap[$a] ?? 2) - ($priorityMap[$b] ?? 2);
        });
        $respuestasArray = array_map('obtenerRespuesta', $categoriasCoincidentes);
        $respuesta = implode(" y ", array_filter($respuestasArray));
    } elseif (count($categoriasCoincidentes) === 1) {
        $respuesta = obtenerRespuesta($categoriasCoincidentes[0]);
    } else {
        $respuesta = "No tengo una respuesta para eso.";
    }
    
    $_SESSION['conversacion'][] = ['usuario' => $mensaje_usuario, 'bot' => $respuesta];
    echo json_encode(['respuesta' => $respuesta, 'usuario' => $mensaje_usuario]);
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = htmlspecialchars($_POST["message"]);
    echo "Usuario: " . $message;
}
?>





