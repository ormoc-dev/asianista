@extends('student.dashboard')

@section('content')
<style>
    .ai-support-container {
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
        background: rgba(15, 23, 42, 0.4);
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        overflow: hidden;
        position: relative;
    }

    .ai-chat-header {
        padding: 20px 25px;
        background: linear-gradient(90deg, rgba(0, 35, 102, 0.8), rgba(38, 40, 64, 0.8));
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .ai-avatar {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #0b1020;
        box-shadow: 0 0 15px rgba(255, 212, 59, 0.4);
        animation: glow 2s infinite ease-in-out;
    }

    @keyframes glow {
        0%, 100% { box-shadow: 0 0 15px rgba(255, 212, 59, 0.4); }
        50% { box-shadow: 0 0 25px rgba(255, 212, 59, 0.6); }
    }

    .ai-chat-info h3 {
        font-size: 1.1rem;
        color: #fff;
        margin-bottom: 2px;
    }

    .ai-chat-info p {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .chat-messages {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.1) transparent;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
    }

    .message {
        max-width: 80%;
        padding: 14px 18px;
        border-radius: 18px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message.ai {
        align-self: flex-start;
        background: rgba(30, 41, 59, 0.8);
        color: #e2e8f0;
        border-bottom-left-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .message.user {
        align-self: flex-end;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: #fff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .chat-input-area {
        padding: 20px 25px;
        background: rgba(15, 23, 42, 0.6);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .chat-input-wrapper {
        flex: 1;
        position: relative;
    }

    #chat-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        padding: 12px 25px;
        color: #fff;
        font-size: 0.95rem;
        transition: all 0.2s;
        outline: none;
    }

    #chat-input:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--accent);
        box-shadow: 0 0 15px rgba(255, 212, 59, 0.1);
    }

    .btn-send {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        border: none;
        border-radius: 50%;
        color: #0b1020;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(255, 212, 59, 0.3);
    }

    .btn-send:hover {
        transform: scale(1.05) rotate(5deg);
        filter: brightness(1.1);
    }

    .btn-send:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        filter: grayscale(1);
    }

    .typing-indicator {
        display: none;
        align-self: flex-start;
        background: rgba(30, 41, 59, 0.6);
        padding: 10px 15px;
        border-radius: 12px;
        color: var(--text-muted);
        font-size: 0.85rem;
        font-style: italic;
        margin-bottom: 20px;
        animation: pulseIndicator 1.5s infinite;
    }

    @keyframes pulseIndicator {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }

    .suggestion-chips {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
    }

    .chip {
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 999px;
        font-size: 0.8rem;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s;
    }

    .chip:hover {
        background: rgba(255, 212, 59, 0.1);
        border-color: var(--accent);
        color: var(--accent);
    }
</style>

<div class="ai-support-wrapper">
    <div class="ai-support-container">
        <!-- Header -->
        <div class="ai-chat-header">
            <div class="ai-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ai-chat-info">
                <h3>Neural Sage</h3>
                <p><i class="fas fa-circle" style="color: #10b981; font-size: 0.6rem;"></i> Connected to the Neural Realm</p>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" id="chat-messages">
            <div class="message ai">
                Greetings, young hero! I am the Neural Sage, your guide through the vast knowledge of ASIANISTA. How can I assist you in your quest for learning today?
            </div>
        </div>

        <!-- Typing Indicator -->
        <div class="typing-indicator" id="typing-indicator">
            <i class="fas fa-magic fa-spin mr-2"></i> The Sage is weaving wisdom...
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
            <div class="chat-input-wrapper">
                <input type="text" id="chat-input" placeholder="Whisper your query to the Sage..." autocomplete="off">
                <div class="suggestion-chips">
                    <div class="chip" onclick="useChip('Help me with today\'s lesson')">Help with lesson</div>
                    <div class="chip" onclick="useChip('Explain a difficult concept')">Explain concept</div>
                    <div class="chip" onclick="useChip('How do I earn more XP?')">Earning XP</div>
                </div>
            </div>
            <button class="btn-send" id="btn-send" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const btnSend = document.getElementById('btn-send');
    const typingIndicator = document.getElementById('typing-indicator');

    let chatHistory = [];

    function useChip(text) {
        chatInput.value = text;
        chatInput.focus();
    }

    function appendMessage(role, text) {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('message', role);
        msgDiv.innerText = text;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        if (role !== 'system') {
            chatHistory.push({ role: role === 'ai' ? 'assistant' : 'user', content: text });
        }
    }

    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        chatInput.value = '';
        appendMessage('user', text);

        // Show loading
        btnSend.disabled = true;
        typingIndicator.style.display = 'block';
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            const response = await fetch("{{ route('student.ai.chat') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    message: text,
                    history: chatHistory
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                appendMessage('ai', result.reply);
            } else {
                appendMessage('ai', "Alas! The Neural connection has flickered. Please try again, hero.");
            }
        } catch (error) {
            console.error("Chat Error:", error);
            appendMessage('ai', "A mystical disruption has occurred. Check your connection to the realm.");
        } finally {
            btnSend.disabled = false;
            typingIndicator.style.display = 'none';
        }
    }

    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
</script>
@endsection
