@php
$rol = auth()->user()->rol;
$voiceEnabled = in_array($rol, ['administrador', 'agente', 'asistente', 'cliente']);
@endphp

@if($voiceEnabled)
{{-- Botón flotante para abrir el chat de voz --}}
<button id="voiceChatToggle"
        class="fixed bottom-20 right-6 z-50 w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110"
        style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; cursor: pointer;"
        title="Asistente de voz IA">
    <svg id="voiceMicIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
        <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
        <line x1="12" y1="19" x2="12" y2="22"/>
    </svg>
</button>

{{-- Panel del chat de voz --}}
<div id="voiceChatPanel"
     class="fixed right-4 md:right-8 z-50 w-[calc(100%-2rem)] md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 transition-all duration-300 ease-in-out"
     style="top: 70px; bottom: 80px; display: none; flex-direction: column; overflow: hidden;">

    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-3" style="background: linear-gradient(135deg, #667eea, #764ba2);">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                </svg>
            </div>
            <div>
                <span class="text-white font-semibold text-sm">Asistente IA</span>
                <span class="text-white/70 text-xs block">Voz masculina - {{ ucfirst($rol) }}</span>
            </div>
        </div>
        <button id="voiceChatClose" class="text-white/80 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Mensajes del chat --}}
    <div id="voiceChatMessages" class="flex-1 overflow-y-auto p-4 space-y-3" style="background: #f8fafc; min-height: 260px; max-height: 400px;">
        <div class="flex items-start gap-2">
            <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                </svg>
            </div>
            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100 max-w-[85%]">
                <p class="text-sm text-gray-700">¡Hola! Soy tu asistente por voz. Presiona el micrófono y dime lo que necesitas.</p>
            </div>
        </div>
    </div>

    {{-- Input area --}}
    <div class="border-t border-gray-100 p-3 bg-white">
        <div class="flex items-center gap-2">
            <button id="voiceRecordBtn"
                    class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center transition-all duration-300"
                    style="background: #f1f5f9; border: none; cursor: pointer;"
                    title="Presiona para hablar">
                <svg id="recordIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="22"/>
                </svg>
            </button>
            <input id="voiceTextInput"
                   type="text"
                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-indigo-400 transition-colors"
                   placeholder="Escribe o presiona el micrófono..."
                   style="font-family: inherit;">
            <button id="voiceSendBtn"
                    class="px-4 py-2 rounded-lg text-white text-sm font-medium flex-shrink-0 transition-all duration-200 hover:opacity-90"
                    style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; cursor: pointer;">
                Enviar
            </button>
        </div>
        <div id="voiceStatus" class="text-xs text-gray-400 mt-1 text-center">Di "ayuda" para ver los comandos disponibles</div>
    </div>
</div>

{{-- Overlay para cerrar en móvil --}}
<div id="voiceChatOverlay" class="fixed inset-0 z-40 bg-black/20 md:hidden" style="display: none;" onclick="closeVoiceChat()"></div>

<script>
(function() {
    const toggleBtn = document.getElementById('voiceChatToggle');
    const panel = document.getElementById('voiceChatPanel');
    const overlay = document.getElementById('voiceChatOverlay');
    const closeBtn = document.getElementById('voiceChatClose');
    const messagesEl = document.getElementById('voiceChatMessages');
    const textInput = document.getElementById('voiceTextInput');
    const sendBtn = document.getElementById('voiceSendBtn');
    const recordBtn = document.getElementById('voiceRecordBtn');
    const recordIcon = document.getElementById('recordIcon');
    const statusEl = document.getElementById('voiceStatus');
    const micIcon = document.getElementById('voiceMicIcon');

    let isOpen = false;
    let isRecording = false;
    let recognition = null;
    let synth = window.speechSynthesis;
    let currentUtterance = null;

    // ── Inicializar Speech Recognition ──
    function initRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            statusEl.textContent = 'Tu navegador no soporta reconocimiento de voz. Usa Chrome.';
            recordBtn.style.opacity = '0.4';
            recordBtn.title = 'No disponible en este navegador';
            return;
        }
        recognition = new SpeechRecognition();
        recognition.lang = 'es-MX';
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            textInput.value = transcript;
            stopRecording();
            sendMessage(transcript);
        };

        recognition.onerror = function(event) {
            console.error('Speech error:', event.error);
            statusEl.textContent = event.error === 'no-speech'
                ? 'No te escuché. Intenta de nuevo.'
                : 'Error: ' + event.error;
            stopRecording();
        };

        recognition.onend = function() {
            stopRecording();
        };
    }

    // ── Grabar / Detener ──
    function toggleRecording() {
        if (!recognition) {
            statusEl.textContent = 'Reconocimiento de voz no disponible.';
            return;
        }

        if (isRecording) {
            stopRecording();
        } else {
            startRecording();
        }
    }

    function startRecording() {
        try {
            recognition.start();
            isRecording = true;
            recordBtn.style.background = '#ef4444';
            recordIcon.innerHTML = '<rect x="6" y="6" width="12" height="12" rx="2" fill="white"/>';
            statusEl.textContent = 'Escuchando... Habla ahora.';
        } catch (e) {
            statusEl.textContent = 'Error al iniciar grabación.';
        }
    }

    function stopRecording() {
        try {
            if (recognition) recognition.stop();
        } catch (e) {}
        isRecording = false;
        recordBtn.style.background = '#f1f5f9';
        recordIcon.innerHTML = '<path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/>';
        if (statusEl.textContent === 'Escuchando... Habla ahora.') {
            statusEl.textContent = 'Di "ayuda" para ver los comandos disponibles';
        }
    }

    // ── Text-to-Speech con voz masculina ──
    function speak(text) {
        if (!synth) return;

        if (currentUtterance) {
            synth.cancel();
        }

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'es-MX';
        utterance.rate = 0.95;
        utterance.pitch = 0.7;

        let voices = synth.getVoices();
        if (voices.length === 0) {
            synth.onvoiceschanged = function() {
                voices = synth.getVoices();
                setMaleVoice(utterance, voices);
                synth.speak(utterance);
            };
        } else {
            setMaleVoice(utterance, voices);
        }

        currentUtterance = utterance;
        synth.speak(utterance);
    }

    function setMaleVoice(utterance, voices) {
        const maleVoices = voices.filter(function(v) {
            return v.lang.startsWith('es') && v.name.toLowerCase().includes('male');
        });
        const deepVoices = voices.filter(function(v) {
            return v.lang.startsWith('es');
        });
        if (maleVoices.length > 0) {
            utterance.voice = maleVoices[0];
        } else if (deepVoices.length > 0) {
            utterance.voice = deepVoices[0];
        }
    }

    // ── Agregar mensaje al chat ──
    function addMessage(text, isUser, extraData) {
        const div = document.createElement('div');
        div.className = 'flex items-start gap-2 ' + (isUser ? 'flex-row-reverse' : '');

        const avatar = document.createElement('div');
        avatar.className = 'w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold';
        if (isUser) {
            avatar.style.background = '#1e293b';
            avatar.textContent = '{{ substr(auth()->user()->nombre, 0, 2) }}';
        } else {
            avatar.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
            avatar.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>';
        }

        const bubble = document.createElement('div');
        bubble.className = 'rounded-lg p-3 shadow-sm border max-w-[85%] text-sm';
        if (isUser) {
            bubble.style.background = '#667eea';
            bubble.style.color = 'white';
            bubble.style.borderColor = '#5a6fd6';
        } else {
            bubble.style.background = 'white';
            bubble.style.color = '#374151';
            bubble.style.borderColor = '#e5e7eb';
        }

        const formatted = text
            .replace(/\n/g, '<br>')
            .replace(/-(.+?)(<br>|$)/g, function(match) {
                return '•' + match.substring(1);
            });
        bubble.innerHTML = formatted;

        // Botones de descarga desde el backend
        if (extraData && extraData.files && extraData.files.length > 0) {
            var container = document.createElement('div');
            container.className = 'flex flex-wrap gap-2 mt-2';
            extraData.files.forEach(function(file) {
                var btn = document.createElement('a');
                btn.href = file.url;
                btn.target = '_blank';
                var label = file.label || 'Descargar';
                btn.textContent = label;
                btn.style.cssText = 'display:inline-block;padding:6px 14px;border:none;border-radius:8px;color:white;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;transition:opacity 0.2s;';
                if (file.format === 'pdf') btn.style.background = '#dc2626';
                else if (file.format === 'xlsx') btn.style.background = '#16a34a';
                else if (file.format === 'csv') btn.style.background = '#2563eb';
                else btn.style.background = '#667eea';
                container.appendChild(btn);
            });
            bubble.appendChild(container);
        }

        // Botón único de descarga
        if (extraData && extraData.url) {
            var container = document.createElement('div');
            container.className = 'flex flex-wrap gap-2 mt-2';
            var a = document.createElement('a');
            a.href = extraData.url;
            a.target = '_blank';
            var label = 'Descargar';
            if (extraData.format === 'pdf') label = '📄 Descargar PDF';
            else if (extraData.format === 'xlsx') label = '📊 Descargar Excel';
            else if (extraData.format === 'csv') label = '📋 Descargar CSV';
            a.textContent = label;
            a.style.cssText = 'display:inline-block;padding:6px 14px;border-radius:8px;color:white;font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;background:#667eea;';
            container.appendChild(a);
            bubble.appendChild(container);
        }

        div.appendChild(avatar);
        div.appendChild(bubble);
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // ── Enviar mensaje al backend ──
    function sendMessage(text) {
        text = text.trim();
        if (!text) return;

        addMessage(text, true);
        textInput.value = '';
        statusEl.textContent = 'Procesando...';

        fetch('{{ route("voice-chat.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ text: text }),
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            var msg = data.message || 'No entendí tu solicitud.';
            var extra = null;
            if (data.type === 'multi_download' && data.files) {
                extra = { files: data.files };
            } else if (data.type === 'download' && data.url) {
                extra = { url: data.url, format: data.format };
            }
            addMessage(msg, false, extra);
            speak(msg);
            statusEl.textContent = 'Di "ayuda" para ver los comandos disponibles';
        })
        .catch(function(err) {
            var errMsg = 'Error de conexión. Intenta de nuevo.';
            addMessage(errMsg, false);
            speak(errMsg);
            statusEl.textContent = 'Error al procesar.';
        });
    }

    // ── Abrir / Cerrar panel ──
    function openVoiceChat() {
        isOpen = true;
        panel.style.display = 'flex';
        overlay.style.display = 'block';
        toggleBtn.style.display = 'none';
        if (typeof synth !== 'undefined' && synth) {
            setTimeout(function() {
                const greeting = 'Hola, soy tu asistente. ¿En qué puedo ayudarte?';
                speak(greeting);
            }, 500);
        }
    }

    window.closeVoiceChat = function() {
        isOpen = false;
        panel.style.display = 'none';
        overlay.style.display = 'none';
        toggleBtn.style.display = 'flex';
        if (currentUtterance && synth) {
            synth.cancel();
        }
        if (isRecording) {
            stopRecording();
        }
    };

    // ── Event listeners ──
    toggleBtn.addEventListener('click', openVoiceChat);
    closeBtn.addEventListener('click', window.closeVoiceChat);

    sendBtn.addEventListener('click', function() {
        sendMessage(textInput.value);
    });

    textInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            sendMessage(textInput.value);
        }
    });

    recordBtn.addEventListener('click', toggleRecording);

    // Inicializar reconocimiento de voz
    initRecognition();

    // Precargar voces
    if (synth) {
        synth.getVoices();
    }
})();
</script>
@endif
