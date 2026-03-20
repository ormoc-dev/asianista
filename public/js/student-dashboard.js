// Apply persisted sidebar state on every Turbolinks navigation
function applySidebarState() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main-content');
    if (!sidebar || !main) return;

    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        sidebar.classList.add('collapsed');
        main.classList.add('expanded');
    } else {
        sidebar.classList.remove('collapsed');
        main.classList.remove('expanded');
    }
}

// Initial apply
applySidebarState();
document.addEventListener('turbolinks:load', applySidebarState);

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main-content');
    if (!sidebar || !main) return;
    
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('mobile-active');
    } else {
        sidebar.classList.toggle('collapsed');
        main.classList.toggle('expanded');
        
        // Persist the state
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
    }
}

// Floating AI Logic
let floatingHistory = [];
function toggleAIChat() {
    const chatWindow = document.getElementById('ai-chat-window');
    if (chatWindow) chatWindow.classList.toggle('active');
}

async function sendFloatingMessage(chatRoute, csrfToken) {
    const input = document.getElementById('floating-chat-input');
    const messages = document.getElementById('floating-chat-messages');
    const indicator = document.getElementById('floating-typing-indicator');
    const text = input.value.trim();

    if (!text) return;

    input.value = '';
    appendFloatingMessage('user', text);
    
    if (indicator) indicator.style.display = 'block';
    if (messages) messages.scrollTop = messages.scrollHeight;

    try {
        const response = await fetch(chatRoute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({
                message: text,
                history: floatingHistory
            })
        });

        const result = await response.json();
        if (result.status === 'success') {
            appendFloatingMessage('ai', result.reply);
        }
    } catch (error) {
        console.error("AI Error:", error);
    } finally {
        if (indicator) indicator.style.display = 'none';
        if (messages) messages.scrollTop = messages.scrollHeight;
    }
}

function appendFloatingMessage(role, text) {
    const messages = document.getElementById('floating-chat-messages');
    if (!messages) return;
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('message', role);
    msgDiv.innerText = text;
    messages.appendChild(msgDiv);
    
    floatingHistory.push({ role: role === 'ai' ? 'assistant' : 'user', content: text });
}

async function openQuestDrawer(url) {
    if (url.includes('/0')) return; 
    
    const container = document.getElementById('embedded-map-container');
    if (!container) return;
    
    // Start fade out/loading
    container.style.opacity = '0';
    
    await new Promise(r => setTimeout(r, 300));

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const mapSection = doc.querySelector('.map-exploration-area');
            
            if (mapSection) {
                container.innerHTML = `<div class="embedded-map-wrap">${mapSection.outerHTML}</div>`;
                
                // Execute scripts (for modals, etc.)
                const scripts = doc.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    if (oldScript.src) newScript.src = oldScript.src;
                    else newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                });
                
                setTimeout(() => {
                    container.style.opacity = '1';
                    container.classList.add('active');
                }, 100);
            }
        })
        .catch(err => {
            console.error("Error loading map:", err);
            container.innerHTML = '<p style="color:white; text-align:center; padding:20px;">Failed to load the map. Please try again.</p>';
            container.style.opacity = '1';
        });
}

// Modal Toggle Helpers
function showLogoutModal() {
    const modal = document.getElementById('logoutConfirmationModal');
    if (modal) modal.style.display = 'flex';
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutConfirmationModal');
    if (modal) modal.style.display = 'none';
}

// Close on overlay click
window.addEventListener('click', function(event) {
    const logoutModal = document.getElementById('logoutConfirmationModal');
    if (event.target == logoutModal) {
        closeLogoutModal();
    }
});
