<?php
session_start();

// Procesar petición para reiniciar la conversación
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
  unset($_SESSION['conversacion']);
  echo json_encode(['success' => true]);
  exit;
}

// Procesar envío de mensaje vía AJAX
if (isset($_GET['action']) && $_GET['action'] === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  // Cargar respuestas desde respuestas.json (asegúrate de que la ruta sea correcta)
  $respuestas = json_decode(file_get_contents('respuestas.json'), true);

  // Función para obtener una respuesta aleatoria según la categoría
  function obtenerRespuesta($categoria) {
    global $respuestas;
    if (isset($respuestas[$categoria]) && is_array($respuestas[$categoria])) {
      return $respuestas[$categoria][array_rand($respuestas[$categoria])];
    }
    return "Lo siento, no tengo respuesta para esa categoría.";
  }

  // Función para detectar categorías basadas en palabras clave
  function getMatchedCategories($mensaje) {
    $mensajeLower = strtolower($mensaje);
    $map = [
      "hola"             => "saludos",
      "buenos días"      => "saludos",
      "buen dia"         => "saludos",
      "buenas tarde"     => "saludos",
      "buenas noche"     => "saludos",
      "te amo"           => "te_amo",
      "san valentín"     => "san_valentin",
      "promesa"          => "promesas",
      "regalo"           => "regalos",
      "cita"             => "citas_romanticas",
      "broma"            => "bromas_romanticas",
      "reflexión"        => "reflexiones",
      "cumpleaños"       => "cumpleaños",
      "adios"            => "despedidas",
      "hasta luego"      => "despedidas",
      "ok"               => "ok",
      "está bien"        => "bien",
      "segura"           => "segura",
      "me amas"          => "me_amas",
      "confias en mi"    => "confias_en_mi",
      "sueño"            => "sueños",
      "soñar"            => "sueños",
      "futuro"           => "futuro",
      "mañana"           => "mañana",
      "abrazo"           => "abrazos",
      "abrazar"          => "abrazos",
      "beso"             => "besos",
      "besar"            => "besos",
      "familia"          => "familia",
      "hijos"            => "hijos",
      "viaje"            => "viajes",
      "viajar"           => "viajes",
      "música"           => "música",
      "canción"          => "música",
      "comida"           => "comida",
      "cocinar"          => "comida",
      "animal"           => "animales",
      "mascota"          => "animales",
      "apoyo"            => "apoyo",
      // Nuevas palabras
      "saludos"          => "saludos",
      "te_amo"           => "te_amo",
      "san_valentin"     => "san_valentin",
      "promesas"         => "promesas",
      "regalos"          => "regalos",
      "citas_romanticas" => "citas_romanticas",
      "bromas_romanticas"=> "bromas_romanticas",
      "reflexiones"      => "reflexiones",
      "despedidas"       => "despedidas",
      "default"          => "default",
      "cumpleaños"       => "cumpleaños",
      "ok"               => "ok",
      "bien"             => "bien",
      "segura"           => "segura",
      "me_amas"          => "me_amas",
      "confias_en_mi"    => "confias_en_mi",
      "como estas"       => "como estas",
      "quien"            => "quien",
      "como"             => "como",
      "donde"            => "donde",
      "porque"           => "porque",
      "por que"          => "por que",
      "cuando"           => "cuando",
      "buen dia"         => "buen dia",
      "buenas tarde"     => "buenas tarde",
      "buenas noche"     => "buenas noche",
      "clima"            => "clima",
      "hora"             => "hora",
      "que"              => "que",
      "piensas"          => "piensas",
      "noche"            => "noche",
      "estrellas"        => "estrellas",
      "colores"          => "colores",
      "ropa"             => "ropa",
      "vestidos"         => "vestidos",
      "compra"           => "compra",
      "eres"             => "eres",
      "nombre"           => "nombre",
      "edad"             => "edad",
      "creador"          => "creador",
      "sueños"           => "sueños",
      "futuro"           => "futuro",
      "abrazos"          => "abrazos",
      "besos"            => "besos",
      "familia"          => "familia",
      "viajes"           => "viajes",
      "música"           => "música",
      "comida"           => "comida",
      "animales"         => "animales",
      "apoyo"            => "apoyo",
      "inspiracion"      => "inspiracion",
      "confesiones"      => "confesiones",
      "poesia"           => "poesia",
      "cariño"           => "cariño",
      "compromiso"       => "compromiso",
      "dedicatorias"     => "dedicatorias",
      "encanto"          => "encanto",
      "destino"          => "destino",
      "ternura"          => "ternura",
      "pasion"           => "pasion",
      "romance"          => "romance",
      "serenata"         => "serenata",
      "devocion"         => "devocion",
      "eterno"           => "eterno",
      "complice"         => "complice",
      "sinceridad"       => "sinceridad",
      "seduccion"        => "seduccion",
      "elegancia"        => "elegancia",
      "amor_veraz"       => "amor_veraz",
      "amor_autentico"   => "amor_autentico",
      "adoracion"        => "adoracion",
      "querencia"        => "querencia",
      "encuentro"        => "encuentro",
      "deseo"            => "deseo",
      "susurros"         => "susurros",
      "caricias"         => "caricias",
      "instantes"        => "instantes",
      "magia"            => "magia",
      "alma"             => "alma",
      "dulzura"          => "dulzura",
      "intimidad"        => "intimidad",
      "anhelo"           => "anhelo",
      "recuerdo"         => "recuerdo",
      "silencio"         => "silencio",
      "armonía"          => "armonía",
      "euforia"          => "euforia",
      "mirada"           => "mirada",
      "brisa"            => "brisa",
      "amanecer"         => "amanecer",
      "océano"           => "océano",
      "resplandor"       => "resplandor",
      "crepúsculo"       => "crepúsculo",
      "fragancia"        => "fragancia",
      "vibración"        => "vibración",
      "júbilo"           => "júbilo",
      "solaz"            => "solaz",
      "desvelo"          => "desvelo",
      "llama"            => "llama",
      "cautiverio"       => "cautiverio",
      "exquisitez"       => "exquisitez"
    ];
    $matches = getMatchedCategories($mensaje_usuario);
    if (!isset($_SESSION['conversacion'])) {
      $_SESSION['conversacion'] = [];
    }
    $priorityMap = [
      "saludos"          => 1,
      "te_amo"           => 2,
      "san_valentin"     => 2,
      "promesas"         => 2,
      "regalos"          => 2,
      "citas_romanticas" => 2,
      "bromas_romanticas"=> 2,
      "reflexiones"      => 2,
      "cumpleaños"       => 2,
      "despedidas"       => 2,
      "ok"               => 2,
      "bien"             => 2,
      "segura"           => 2,
      "me_amas"          => 2,
      "confias_en_mi"    => 2,
      "default"          => 2,
      "como estas"       => 2,
      "quien"            => 2,
      "como"             => 2,
      "donde"            => 2,
      "porque"           => 2,
      "por que"          => 2,
      "cuando"           => 2,
      "buen dia"         => 2,
      "buenas tarde"     => 2,
      "buenas noche"     => 2,
      "clima"            => 2,
      "hora"             => 2,
      "que"              => 2,
      "piensas"          => 2,
      "noche"            => 2,
      "estrellas"        => 2,
      "colores"          => 2,
      "ropa"             => 2,
      "vestidos"         => 2,
      "compra"           => 2,
      "eres"             => 2,
      "nombre"           => 2,
      "edad"             => 2,
      "creador"          => 2,
      "sueños"           => 3,
      "futuro"           => 3,
      "mañana"           => 3,
      "abrazos"          => 3,
      "besos"            => 3,
      "familia"          => 3,
      "hijos"            => 3,
      "viajes"           => 3,
      "música"           => 3,
      "comida"           => 3,
      "animales"         => 3,
      "apoyo"            => 3,
      "inspiracion"      => 3,
      "confesiones"      => 3,
      "poesia"           => 3,
      "cariño"           => 3,
      "compromiso"       => 3,
      "dedicatorias"     => 3,
      "encanto"          => 3,
      "destino"          => 3,
      "ternura"          => 3,
      "pasion"           => 3,
      "romance"          => 3,
      "serenata"         => 3,
      "devocion"         => 3,
      "eterno"           => 3,
      "complice"         => 3,
      "sinceridad"       => 3,
      "seduccion"        => 3,
      "elegancia"        => 3,
      "amor_veraz"       => 3,
      "amor_autentico"   => 3,
      "adoracion"        => 3,
      "querencia"        => 3,
      "encuentro"        => 3,
      "deseo"            => 3,
      "susurros"         => 3,
      "caricias"         => 3,
      "instantes"        => 3,
      "magia"            => 3,
      "alma"             => 3,
      "dulzura"          => 3,
      "intimidad"        => 3,
      "anhelo"           => 3,
      "recuerdo"         => 3,
      "silencio"         => 3,
      "armonía"          => 3,
      "euforia"          => 3,
      "mirada"           => 3,
      "brisa"            => 3,
      "amanecer"         => 3,
      "océano"           => 3,
      "resplandor"       => 3,
      "crepúsculo"       => 3,
      "fragancia"        => 3,
      "vibración"        => 3,
      "júbilo"           => 3,
      "solaz"            => 3,
      "desvelo"          => 3,
      "llama"            => 3,
      "cautiverio"       => 3,
      "exquisitez"       => 3
    ];
    if (count($matches) > 1) {
      usort($matches, function($a, $b) use ($priorityMap) {
        return ($priorityMap[$a] ?? 2) - ($priorityMap[$b] ?? 2);
      });
      $respuestasArray = array_map('obtenerRespuesta', $matches);
      $respuesta = implode(" y ", array_filter($respuestasArray));
    } elseif (count($matches) === 1) {
      $respuesta = obtenerRespuesta($matches[0]);
    } else {
      $respuesta = "No tengo una respuesta para eso.";
    }
    $_SESSION['conversacion'][] = ['usuario' => $mensaje_usuario, 'bot' => $respuesta];
    echo json_encode(['respuesta' => $respuesta, 'usuario' => $mensaje_usuario]);
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chatbot Cupido</title>
  <style>
    /* Estilos generales */
    body {
      font-family: 'Arial', sans-serif;
      background: #ffe6f2;
      margin: 0;
      padding: 0;
      overflow: hidden;
    }
    /* Contenedor del chatbot */
    #chatbot-container {
      position: fixed;
      bottom: 80px;
      right: 20px;
      width: 350px;
      height: 500px;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(255, 64, 129, 0.3);
      display: flex;
      flex-direction: column;
      transition: bottom 0.3s ease, opacity 0.3s ease;
      overflow: hidden;
      z-index: 1000;
      opacity: 0;
      pointer-events: none;
    }
    #chatbot-container.open {
      opacity: 1;
      pointer-events: auto;
      bottom: 20px;
    }
    /* Cabecera con gradiente y animación de corazón */
    #chat-header {
      background: linear-gradient(135deg, #ff80ab, #ff4081);
      color: #fff;
      padding: 15px;
      text-align: center;
      font-size: 18px;
      font-weight: bold;
      position: relative;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
    }
    #chat-header::before {
      content: "❤️";
      display: inline-block;
      margin-right: 8px;
      animation: beat 1s infinite;
    }
    @keyframes beat {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.2); }
    }
    /* Botones de opciones y cierre */
    #options-btn,
    #close-chat-btn {
      position: absolute;
      background: transparent;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #fff;
    }
    #options-btn { top: 15px; right: 15px; }
    #close-chat-btn { top: 15px; left: 15px; }
    /* Ventana del chat */
    #chat-window {
      flex-grow: 1;
      padding: 15px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      background: #f9f9f9;
    }
    .message {
      margin: 10px 0;
      padding: 15px;
      border-radius: 10px;
      max-width: 75%;
      font-size: 16px;
      line-height: 1.5;
      position: relative;
      animation: popIn 0.3s ease;
    }
    @keyframes popIn {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    .user-message {
      align-self: flex-end;
      background: #a0e4f1;
      text-align: right;
    }
    .bot-message {
      align-self: flex-start;
      background: #ffcccb;
      text-align: left;
    }
    .message::after {
      content: '';
      position: absolute;
      bottom: -10px;
      width: 15px;
      height: 15px;
      background: inherit;
      border-radius: 3px;
    }
    .user-message::after {
      right: -5px;
      clip-path: polygon(0 0, 100% 0, 100% 100%);
    }
    .bot-message::after {
      left: -5px;
      clip-path: polygon(0 0, 100% 100%, 0 100%);
    }
    /* Contenedor del input, autocompletado y botón de emojis */
    #input-container {
      display: flex;
      align-items: center;
      border-top: 1px solid #ddd;
      padding: 15px;
      background: #fff;
      border-bottom-left-radius: 15px;
      border-bottom-right-radius: 15px;
      position: relative;
    }
    /* Wrapper para input y autocompletado */
    .input-wrapper {
      position: relative;
      flex-grow: 1;
    }
    /* Botón de Emoji */
    #emoji-toggle-btn {
      background: #fff;
      border: 2px solid #ff80ab;
      border-radius: 50%;
      font-size: 28px;
      width: 45px;
      height: 45px;
      margin-right: 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ff4081;
      transition: background 0.3s, transform 0.3s;
    }
    #emoji-toggle-btn:hover {
      background: #ff80ab;
      color: #fff;
      transform: scale(1.1);
    }
    #input-container input {
      width: 100%;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 25px;
      font-size: 16px;
      transition: border-color 0.3s;
    }
    #input-container input:focus {
      border-color: #ff4081;
    }
    #input-container button[type="submit"] {
      background: #ff80ab;
      color: #fff;
      padding: 15px;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      transition: background 0.3s;
      margin-left: 10px;
    }
    #input-container button[type="submit"]:hover {
      background: #ff4081;
    }
    /* Panel de Emojis */
    #emoji-panel {
      display: none;
      justify-content: space-around;
      padding: 15px;
      background: #f1f1f1;
      border-top: 1px solid #ddd;
    }
    #emoji-panel button {
      background: transparent;
      border: none;
      font-size: 28px;
      cursor: pointer;
      transition: transform 0.3s;
    }
    #emoji-panel button:hover {
      transform: scale(1.2);
    }
    /* Botón para abrir el chat */
    #toggle-chat-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: linear-gradient(145deg, #ff80ab, #ff4081);
      color: #fff;
      padding: 20px;
      border: none;
      border-radius: 50%;
      cursor: pointer;
      font-size: 24px;
      z-index: 1100;
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 10px 20px rgba(255, 64, 129, 0.3);
      animation: pulse 2s infinite;
    }
    #toggle-chat-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 15px 30px rgba(255, 64, 129, 0.4);
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    /* Modal de opciones */
    #options-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 2000;
      animation: fadeInModal 0.3s ease;
    }
    @keyframes fadeInModal {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .modal-content {
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      width: 300px;
      position: relative;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      text-align: center;
    }
    .modal-close {
      position: absolute;
      top: 10px;
      right: 10px;
      background: transparent;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #666;
    }
    .modal-content h3 {
      margin: 10px 0;
      color: #ff4081;
    }
    .modal-content p {
      color: #555;
      font-size: 14px;
      margin-bottom: 20px;
    }
    .modal-buttons {
      display: flex;
      justify-content: space-between;
    }
    .modal-btn {
      flex: 1;
      margin: 0 5px;
      padding: 15px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 14px;
    }
    .modal-btn.cancel {
      background: #ccc;
      color: #333;
    }
    .modal-btn.confirm {
      background: #ff80ab;
      color: #fff;
    }
    .modal-btn.cancel:hover {
      background: #b3b3b3;
    }
    .modal-btn.confirm:hover {
      background: #ff4081;
    }
    /* Autocompletado personalizado */
    #autocomplete-container {
      position: absolute;
      top: calc(100% + 2px);
      left: 0;
      right: 0;
      background: #fff;
      border: 1px solid #ddd;
      border-top: none;
      border-bottom-left-radius: 10px;
      border-bottom-right-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      z-index: 10;
      max-height: 150px;
      overflow-y: auto;
      display: none;
    }
    .autocomplete-item {
      padding: 8px 12px;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
      display: flex;
      align-items: center;
    }
    .autocomplete-item:hover {
      background: #ffe6f2;
      transform: scale(1.02);
    }
    .autocomplete-item span {
      margin-left: 5px;
    }
  </style>
</head>
<body>
  <!-- Botón para abrir el chat -->
  <button id="toggle-chat-btn" onclick="toggleChat()">💘</button>

  <!-- Contenedor del Chatbot -->
  <div id="chatbot-container">
    <div id="chat-header">
      <button id="close-chat-btn" onclick="toggleChat()">✕</button>
      Cupido
      <button id="options-btn" onclick="openOptions(event)">⋮</button>
    </div>
    <div id="chat-window">
      <?php
      // Renderizamos la conversación almacenada en la sesión (si existe)
      if (isset($_SESSION['conversacion'])) {
        foreach ($_SESSION['conversacion'] as $mensaje) {
          echo "<div class='message user-message'>" . htmlspecialchars($mensaje['usuario']) . "</div>";
          echo "<div class='message bot-message'>" . htmlspecialchars($mensaje['bot']) . "</div>";
        }
      }
      ?>
    </div>
    <!-- Contenedor del input, autocompletado y botón de emojis -->
    <div id="input-container">
      <button type="button" id="emoji-toggle-btn" onclick="toggleEmojiPanel()">😊</button>
      <div class="input-wrapper">
        <form id="chat-form" method="post" autocomplete="off">
          <input type="text" name="mensaje" id="user-message" placeholder="Escribe un mensaje..." required>
          <button type="submit">Enviar</button>
        </form>
        <!-- Contenedor para sugerencias de autocompletado -->
        <div id="autocomplete-container"></div>
      </div>
    </div>
    <!-- Panel de Emojis -->
    <div id="emoji-panel">
      <button onclick="sendEmojiResponse('😊')">😊</button>
      <button onclick="sendEmojiResponse('💖')">💖</button>
      <button onclick="sendEmojiResponse('😘')">😘</button>
      <button onclick="sendEmojiResponse('💌')">💌</button>
      <button onclick="sendEmojiResponse('😍')">😍</button>
      <button onclick="sendEmojiResponse('😇')">😇</button>
      <button onclick="sendEmojiResponse('💋')">💋</button>
      <button onclick="sendEmojiResponse('🌹')">🌹</button>
    </div>
  </div>

  <!-- Modal de Opciones -->
  <div id="options-modal">
    <div class="modal-content">
      <button class="modal-close" onclick="closeOptions()">✕</button>
      <h3>Reiniciar Chat</h3>
      <p>¿Estás seguro de reiniciar la conversación?</p>
      <div class="modal-buttons">
        <button class="modal-btn cancel" onclick="closeOptions()">Cancelar</button>
        <button class="modal-btn confirm" onclick="resetChat()">Reiniciar</button>
      </div>
    </div>
  </div>

  <script>
    // Función para abrir/cerrar el chat y ocultar el botón flotante
    function toggleChat() {
      const chatContainer = document.getElementById('chatbot-container');
      const toggleBtn = document.getElementById('toggle-chat-btn');
      chatContainer.classList.toggle('open');
      toggleBtn.style.display = chatContainer.classList.contains('open') ? 'none' : 'block';
    }

    // Abrir y cerrar el modal de opciones
    function openOptions(e) {
      e.stopPropagation();
      document.getElementById('options-modal').style.display = 'flex';
    }
    function closeOptions() {
      document.getElementById('options-modal').style.display = 'none';
    }

    // Agregar mensaje al chat
    function appendMessage(text, type) {
      const chatWindow = document.getElementById('chat-window');
      const messageDiv = document.createElement('div');
      messageDiv.className = 'message ' + type;
      messageDiv.textContent = text;
      chatWindow.appendChild(messageDiv);
      chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    // Enviar mensaje vía AJAX
    document.getElementById('chat-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const messageInput = document.getElementById('user-message');
      const mensaje = messageInput.value.trim();
      if (!mensaje) return;
      appendMessage(mensaje, 'user-message');
      messageInput.value = '';
      clearSuggestions();
      fetch('chat.php?action=send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'mensaje=' + encodeURIComponent(mensaje)
      })
      .then(response => response.json())
      .then(data => {
        if (data.respuesta) {
          appendMessage(data.respuesta, 'bot-message');
        } else if (data.error) {
          appendMessage('Error: ' + data.error, 'bot-message');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        appendMessage('Error en la comunicación con el servidor.', 'bot-message');
      });
    });

    // Reiniciar el chat vía AJAX
    function resetChat() {
      fetch('chat.php?action=reset', { method: 'GET' })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('chat-window').innerHTML = '';
          closeOptions();
        }
      })
      .catch(error => console.error('Error:', error));
    }

    /* Funciones para el Panel de Emojis */
    function toggleEmojiPanel() {
      const panel = document.getElementById('emoji-panel');
      panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'flex' : 'none';
    }
    const emojiResponses = {
      "😊": "Me alegra ver esa sonrisa, mi amor.",
      "💖": "Tu amor ilumina mi día, cielo.",
      "😘": "Ese beso virtual me hace derretir, mi corazón.",
      "💌": "Cada mensaje tuyo es un regalo para mi alma.",
      "😍": "Tus ojos son el reflejo de un amor eterno.",
      "😇": "Con tu inocencia y ternura, iluminas mi vida.",
      "💋": "Ese beso es el sabor de nuestro amor infinito.",
      "🌹": "Cada rosa me recuerda a ti, mi amor."
    };
    function sendEmojiResponse(emoji) {
      appendMessage(emoji, 'user-message');
      const respuesta = emojiResponses[emoji] || "Gracias por compartir tu emoción.";
      appendMessage(respuesta, 'bot-message');
      toggleEmojiPanel();
    }

    /* Autocompletado personalizado utilizando un arreglo de palabras clave */
    const keywords = [
      "hola",
      "buenos días",
      "buen dia",
      "buenas tarde",
      "buenas noche",
      "te amo",
      "san valentín",
      "promesa",
      "regalo",
      "cita",
      "broma",
      "reflexión",
      "cumpleaños",
      "adios",
      "hasta luego",
      "ok",
      "está bien",
      "segura",
      "me amas",
      "confias en mi",
      "sueño",
      "soñar",
      "futuro",
      "mañana",
      "abrazo",
      "abrazar",
      "beso",
      "besar",
      "familia",
      "hijos",
      "viaje",
      "viajar",
      "música",
      "canción",
      "comida",
      "cocinar",
      "animal",
      "mascota",
      "apoyo",
      "saludos",
      "te_amo",
      "san_valentin",
      "promesas",
      "regalos",
      "citas_romanticas",
      "bromas_romanticas",
      "reflexiones",
      "despedidas",
      "default",
      "cumpleaños",
      "ok",
      "bien",
      "segura",
      "me_amas",
      "confias_en_mi",
      "como estas",
      "quien",
      "como",
      "donde",
      "porque",
      "por que",
      "cuando",
      "buen dia",
      "buenas tarde",
      "buenas noche",
      "clima",
      "hora",
      "que",
      "piensas",
      "noche",
      "estrellas",
      "colores",
      "ropa",
      "vestidos",
      "compra",
      "eres",
      "nombre",
      "edad",
      "creador",
      "sueños",
      "futuro",
      "abrazos",
      "besos",
      "familia",
      "viajes",
      "música",
      "comida",
      "animales",
      "apoyo",
      "inspiracion",
      "confesiones",
      "poesia",
      "cariño",
      "compromiso",
      "dedicatorias",
      "encanto",
      "destino",
      "ternura",
      "pasion",
      "romance",
      "serenata",
      "devocion",
      "eterno",
      "complice",
      "sinceridad",
      "seduccion",
      "elegancia",
      "amor_veraz",
      "amor_autentico",
      "adoracion",
      "querencia",
      "encuentro",
      "deseo",
      "susurros",
      "caricias",
      "instantes",
      "magia",
      "alma",
      "dulzura",
      "intimidad",
      "anhelo",
      "recuerdo",
      "silencio",
      "armonía",
      "euforia",
      "mirada",
      "brisa",
      "amanecer",
      "océano",
      "resplandor",
      "crepúsculo",
      "fragancia",
      "vibración",
      "júbilo",
      "solaz",
      "desvelo",
      "llama",
      "cautiverio",
      "exquisitez"
    ];

    const inputField = document.getElementById('user-message');
    const acContainer = document.getElementById('autocomplete-container');

    // Mostrar sugerencias basadas en el arreglo de palabras clave
    inputField.addEventListener('input', function() {
      const query = inputField.value.trim().toLowerCase();
      clearSuggestions();
      if (!query) return;
      const suggestions = keywords.filter(word => word.toLowerCase().startsWith(query));
      if (!suggestions.length) return;
      suggestions.forEach(sugg => {
        const item = document.createElement('div');
        item.className = 'autocomplete-item';
        item.innerHTML = "❤️ <span>" + sugg + "</span>";
        item.addEventListener('click', function() {
          inputField.value = sugg;
          clearSuggestions();
        });
        acContainer.appendChild(item);
      });
      acContainer.style.display = 'block';
    });

    // Ocultar sugerencias al perder foco (pequeño delay para permitir click)
    inputField.addEventListener('blur', function() {
      setTimeout(clearSuggestions, 100);
    });
    function clearSuggestions() {
      acContainer.innerHTML = '';
      acContainer.style.display = 'none';
    }
  </script>
</body>
</html>






