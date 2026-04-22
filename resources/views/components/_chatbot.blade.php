{{--
    DiscovTrip Chatbot Widget — DiscovGuide
    Inclure dans layouts/app.blade.php juste avant </body> :
    @include('components._chatbot')
--}}

{{-- ════ BOUTON FLOTTANT ════ --}}
<button id="chatbot-trigger" aria-label="Ouvrir l'assistant DiscovTrip" style="
    position:fixed;bottom:28px;right:28px;z-index:9998;
    width:60px;height:60px;border-radius:50%;
    background:linear-gradient(135deg,#D4924D,#B8751A,#8C5A0E);
    border:none;cursor:pointer;
    box-shadow:0 8px 32px rgba(184,117,26,.5),0 2px 8px rgba(0,0,0,.2);
    display:flex;align-items:center;justify-content:center;
    transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);
">
    <span id="chatbot-trigger-icon" style="font-size:24px;line-height:1;transition:transform 0.3s ease;">🧑🏿</span>
    <span style="position:absolute;inset:-6px;border-radius:50%;border:2px solid rgba(184,117,26,.4);animation:chatbot-pulse 2.5s ease-out infinite;pointer-events:none;"></span>
    <span id="chatbot-badge" style="display:none;position:absolute;top:-2px;right:-2px;width:18px;height:18px;border-radius:50%;background:#c1440e;border:2px solid white;font-size:10px;font-weight:800;color:white;align-items:center;justify-content:center;">1</span>
</button>

{{-- ════ SIDEBAR ════ --}}
<div id="chatbot-sidebar" style="
    position:fixed;top:0;right:0;bottom:0;z-index:9999;
    width:420px;max-width:100vw;
    background:linear-gradient(180deg,#1A1208 0%,#0D0A05 100%);
    border-left:1px solid rgba(184,117,26,.2);
    display:flex;flex-direction:column;
    transform:translateX(105%);
    transition:transform 0.4s cubic-bezier(0.4,0,.2,1);
    box-shadow:-16px 0 48px rgba(0,0,0,.4);
">
    {{-- Header --}}
    <div style="padding:20px 24px;border-bottom:1px solid rgba(184,117,26,.15);display:flex;align-items:center;gap:14px;background:rgba(184,117,26,.06);flex-shrink:0;">
        <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,rgba(212,146,77,.3),rgba(184,117,26,.15));border:2px solid rgba(212,146,77,.4);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;position:relative;">
            🧑🏿
            <span style="position:absolute;bottom:1px;right:1px;width:10px;height:10px;border-radius:50%;background:#22c55e;border:2px solid #1A1208;"></span>
        </div>
        <div style="flex:1;">
            <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#FDFAF6;">DiscovGuide</div>
            <div style="font-size:11px;color:rgba(247,242,236,.45);">Assistant voyage DiscovTrip · En ligne</div>
        </div>
        <div style="display:flex;gap:8px;">
            <button id="chatbot-clear" title="Nouvelle conversation" style="width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;background:rgba(253,250,246,.06);color:rgba(247,242,236,.45);font-size:13px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;" onmouseenter="this.style.background='rgba(253,250,246,.12)';this.style.color='rgba(247,242,236,.8)'" onmouseleave="this.style.background='rgba(253,250,246,.06)';this.style.color='rgba(247,242,236,.45)'">
                <i class="fas fa-rotate-left" style="font-size:12px;"></i>
            </button>
            <button id="chatbot-close" style="width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;background:rgba(253,250,246,.06);color:rgba(247,242,236,.45);font-size:18px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;" onmouseenter="this.style.background='rgba(193,68,14,.2)';this.style.color='#FF6B6B'" onmouseleave="this.style.background='rgba(253,250,246,.06)';this.style.color='rgba(247,242,236,.45)'">×</button>
        </div>
    </div>

    {{-- Messages --}}
    <div id="chatbot-messages" style="flex:1;overflow-y:auto;padding:20px 16px;display:flex;flex-direction:column;gap:12px;scroll-behavior:smooth;">
        <div class="chat-msg assistant" style="display:flex;gap:10px;align-items:flex-start;animation:chatbot-msgIn 0.35s ease;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(212,146,77,.15);border:1px solid rgba(212,146,77,.3);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-top:2px;">🧑🏿</div>
            <div style="background:rgba(253,250,246,.08);border:1px solid rgba(253,250,246,.1);border-radius:4px 16px 16px 16px;padding:12px 16px;font-size:13px;line-height:1.65;color:rgba(247,242,236,.88);max-width:calc(100% - 44px);">
                Akpe na mi ! 👋 Je suis DiscovGuide, votre assistant pour découvrir le Bénin.<br><br>
                Que cherchez-vous — une aventure culturelle, de la nature, ou vous ne savez pas encore par où commencer ?
            </div>
        </div>
    </div>

    {{-- Suggestions --}}
    <div id="chatbot-suggestions" style="padding:0 16px 12px;display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
        @foreach(['Que faire à Cotonou ?','Expériences nature 🌿','Budget 3 jours','Visa & formalités'] as $s)
        <button class="chatbot-suggestion" data-text="{{ $s }}" style="padding:6px 12px;border-radius:100px;border:none;cursor:pointer;background:rgba(184,117,26,.12);border:1px solid rgba(184,117,26,.2);color:rgba(247,242,236,.7);font-size:11px;font-weight:600;font-family:'DM Sans',sans-serif;transition:all 0.2s;white-space:nowrap;" onmouseenter="this.style.background='rgba(184,117,26,.22)';this.style.color='#F0B96B'" onmouseleave="this.style.background='rgba(184,117,26,.12)';this.style.color='rgba(247,242,236,.7)'">{{ $s }}</button>
        @endforeach
    </div>

    {{-- Input --}}
    <div style="padding:12px 16px 20px;border-top:1px solid rgba(184,117,26,.12);flex-shrink:0;">
        <div id="chatbot-input-wrapper" style="display:flex;gap:10px;align-items:flex-end;background:rgba(253,250,246,.07);border:1.5px solid rgba(184,117,26,.2);border-radius:16px;padding:10px 12px;transition:border-color 0.2s;">
            <textarea id="chatbot-input" placeholder="Posez votre question..." rows="1" style="flex:1;background:none;border:none;outline:none;resize:none;font-family:'DM Sans',sans-serif;font-size:13px;line-height:1.5;color:rgba(247,242,236,.9);max-height:120px;overflow-y:auto;scrollbar-width:none;" onkeydown="handleChatKey(event)"></textarea>
            <button id="chatbot-send" style="width:36px;height:36px;border-radius:10px;border:none;cursor:pointer;background:linear-gradient(135deg,#D4924D,#B8751A);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.2s;opacity:0.5;" onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform=''">
                <i class="fas fa-paper-plane" style="font-size:13px;color:white;margin-left:-1px;margin-top:-1px;"></i>
            </button>
        </div>
        <div style="font-size:10px;color:rgba(247,242,236,.2);text-align:center;margin-top:8px;">Propulsé par Groq · DiscovTrip</div>
    </div>
</div>

{{-- Overlay --}}
<div id="chatbot-overlay" style="display:none;position:fixed;inset:0;z-index:9997;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);"></div>

<style>
@keyframes chatbot-pulse { 0%{transform:scale(1);opacity:.6} 70%,100%{transform:scale(1.5);opacity:0} }
@keyframes chatbot-msgIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
@keyframes chatbot-typing { 0%,80%,100%{transform:scale(.6);opacity:.4} 40%{transform:scale(1);opacity:1} }
#chatbot-messages::-webkit-scrollbar{width:4px}
#chatbot-messages::-webkit-scrollbar-thumb{background:rgba(184,117,26,.2);border-radius:2px}
#chatbot-input::placeholder{color:rgba(247,242,236,.3)}
.chat-msg.user{flex-direction:row-reverse}
.chat-msg.user .chat-bubble{background:linear-gradient(135deg,rgba(212,146,77,.2),rgba(184,117,26,.12))!important;border-color:rgba(184,117,26,.25)!important;border-radius:16px 4px 16px 16px!important}
.typing-dot{width:6px;height:6px;border-radius:50%;background:rgba(184,117,26,.6);display:inline-block;animation:chatbot-typing 1.2s infinite ease-in-out}
.typing-dot:nth-child(2){animation-delay:.2s}
.typing-dot:nth-child(3){animation-delay:.4s}
@media(max-width:480px){#chatbot-sidebar{width:100vw!important}}
</style>

<script>
(function() {
    // ── Config ─────────────────────────────────────────────
    const GROQ_API_KEY  = '{{ config('services.groq.api_key') }}';
    const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';
    const SYSTEM_PROMPT = "Tu es DiscovGuide, assistant IA de DiscovTrip, plateforme de voyage au Bénin (Afrique de l'Ouest). "
        + "Réponds en français, chaleureusement, en 2-3 phrases maximum. "
        + "Tu aides les visiteurs à découvrir le Bénin : destinations (Cotonou, Porto-Novo, Ouidah, Abomey, Ganvié, Natitingou...), "
        + "expériences culturelles, nature, gastronomie, vaudou, histoire du Dahomey. "
        + "Infos pratiques : visa à l'arrivée (50 USD), monnaie FCFA (1 EUR = 655 FCFA), meilleure période novembre-mars. "
        + "Si la question ne concerne pas le Bénin ou le voyage, redirige poliment vers DiscovTrip.";

    // ── Éléments ───────────────────────────────────────────
    const trigger  = document.getElementById('chatbot-trigger');
    const sidebar  = document.getElementById('chatbot-sidebar');
    const overlay  = document.getElementById('chatbot-overlay');
    const closeBtn = document.getElementById('chatbot-close');
    const clearBtn = document.getElementById('chatbot-clear');
    const input    = document.getElementById('chatbot-input');
    const sendBtn  = document.getElementById('chatbot-send');
    const messages = document.getElementById('chatbot-messages');
    const badge    = document.getElementById('chatbot-badge');
    const trigIcon = document.getElementById('chatbot-trigger-icon');
    const inputWrap= document.getElementById('chatbot-input-wrapper');

    let isOpen       = false;
    let isTyping     = false;
    let conversation = [];

    // ── Ouvrir/Fermer ──────────────────────────────────────
    function open()  {
        isOpen = true;
        sidebar.style.transform = 'translateX(0)';
        overlay.style.display   = 'block';
        trigger.style.transform = 'scale(0.9)';
        trigIcon.textContent    = '×';
        trigIcon.style.fontSize = '22px';
        badge.style.display     = 'none';
        setTimeout(() => input.focus(), 400);
    }
    function close() {
        isOpen = false;
        sidebar.style.transform = 'translateX(105%)';
        overlay.style.display   = 'none';
        trigger.style.transform = '';
        trigIcon.textContent    = '🧑🏿';
        trigIcon.style.fontSize = '24px';
    }

    trigger.addEventListener('click', () => isOpen ? close() : open());
    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', close);

    clearBtn.addEventListener('click', () => {
        conversation = [];
        const welcome = messages.querySelector('.chat-msg');
        messages.innerHTML = '';
        if (welcome) messages.appendChild(welcome);
        document.getElementById('chatbot-suggestions').style.display = 'flex';
    });

    // ── Input ──────────────────────────────────────────────
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        sendBtn.style.opacity = input.value.trim() ? '1' : '0.5';
    });
    input.addEventListener('focus', () => inputWrap.style.borderColor = 'rgba(184,117,26,.5)');
    input.addEventListener('blur',  () => inputWrap.style.borderColor = 'rgba(184,117,26,.2)');

    document.querySelectorAll('.chatbot-suggestion').forEach(btn => {
        btn.addEventListener('click', () => {
            input.value = btn.dataset.text;
            input.dispatchEvent(new Event('input'));
            document.getElementById('chatbot-suggestions').style.display = 'none';
            sendMessage();
        });
    });

    // ── Envoi ──────────────────────────────────────────────
    window.handleChatKey = e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    };
    sendBtn.addEventListener('click', sendMessage);

    async function sendMessage() {
        const text = input.value.trim();
        if (!text || isTyping) return;

        appendMessage('user', text);
        conversation.push({ role: 'user', content: text });
        input.value = '';
        input.style.height = 'auto';
        sendBtn.style.opacity = '0.5';
        document.getElementById('chatbot-suggestions').style.display = 'none';

        const typingEl = appendTyping();
        isTyping = true;

        try {
            const res = await fetch(GROQ_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + GROQ_API_KEY,
                    'Content-Type':  'application/json',
                },
                body: JSON.stringify({
                    model:       'llama-3.1-8b-instant',
                    max_tokens:  300,
                    temperature: 0.7,
                    messages: [
                        { role: 'system', content: SYSTEM_PROMPT },
                        ...conversation.slice(-6)
                    ],
                }),
            });

            typingEl.remove();
            isTyping = false;

            if (!res.ok) {
                appendMessage('assistant', '⚠️ Erreur ' + res.status + '. Réessayez.');
                return;
            }

            const data    = await res.json();
            const content = data.choices?.[0]?.message?.content ?? 'Réessayez dans un instant.';
            appendMessage('assistant', content);
            conversation.push({ role: 'assistant', content });

            if (!isOpen) { badge.style.display = 'flex'; }

        } catch(e) {
            typingEl.remove();
            isTyping = false;
            appendMessage('assistant', '⚠️ Connexion impossible. Vérifiez votre réseau.');
        }
    }

    // ── UI helpers ─────────────────────────────────────────
    function appendMessage(role, text) {
        const isA = role === 'assistant';
        const el  = document.createElement('div');
        el.className = 'chat-msg ' + role;
        el.style.cssText = 'display:flex;gap:10px;align-items:flex-start;animation:chatbot-msgIn 0.35s ease;';
        const html = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
        el.innerHTML = (isA ? '<div style="width:28px;height:28px;border-radius:50%;background:rgba(212,146,77,.15);border:1px solid rgba(212,146,77,.3);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-top:2px;">🧑🏿</div>' : '')
            + `<div class="chat-bubble" style="background:rgba(253,250,246,.08);border:1px solid rgba(253,250,246,.1);border-radius:${isA?'4px 16px 16px 16px':'16px 4px 16px 16px'};padding:12px 16px;font-size:13px;line-height:1.65;color:rgba(247,242,236,.88);max-width:calc(100% - 44px);word-break:break-word;">${html}</div>`;
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }

    function appendTyping() {
        const el = document.createElement('div');
        el.className = 'chat-msg assistant';
        el.style.cssText = 'display:flex;gap:10px;align-items:flex-start;';
        el.innerHTML = '<div style="width:28px;height:28px;border-radius:50%;background:rgba(212,146,77,.15);border:1px solid rgba(212,146,77,.3);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-top:2px;">🧑🏿</div>'
            + '<div style="background:rgba(253,250,246,.08);border:1px solid rgba(253,250,246,.1);border-radius:4px 16px 16px 16px;padding:14px 18px;display:flex;gap:5px;align-items:center;"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>';
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }
})();
</script>