function syncMobileSidebarBackdrop() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar || window.innerWidth > 768) {
        document.body.classList.remove('student-sidebar-open');
        return;
    }
    document.body.classList.toggle('student-sidebar-open', sidebar.classList.contains('mobile-active'));
}

// Apply persisted sidebar state on every Turbolinks navigation
function applySidebarState() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main-content');
    if (!sidebar || !main) return;

    if (window.innerWidth <= 768) {
        sidebar.classList.remove('collapsed');
        main.classList.remove('expanded');
        sidebar.classList.remove('mobile-active');
        document.body.classList.remove('student-sidebar-open');
        return;
    }

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
        syncMobileSidebarBackdrop();
    } else {
        sidebar.classList.toggle('collapsed');
        main.classList.toggle('expanded');
        
        // Persist the state
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
    }
}

let studentDashboardResizeTimer;
window.addEventListener('resize', function () {
    clearTimeout(studentDashboardResizeTimer);
    studentDashboardResizeTimer = setTimeout(function () {
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('main-content');
        if (!sidebar || !main) return;

        if (window.innerWidth > 768) {
            sidebar.classList.remove('mobile-active');
            document.body.classList.remove('student-sidebar-open');
            applySidebarState();
        } else {
            sidebar.classList.remove('collapsed');
            main.classList.remove('expanded');
        }
    }, 150);
});

document.addEventListener('click', function (e) {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar || window.innerWidth > 768 || !sidebar.classList.contains('mobile-active')) return;
    if (e.target.closest('#sidebar') || e.target.closest('.toggle-btn')) return;
    sidebar.classList.remove('mobile-active');
    syncMobileSidebarBackdrop();
}, true);

document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    const sidebar = document.getElementById('sidebar');
    if (!sidebar || window.innerWidth > 768 || !sidebar.classList.contains('mobile-active')) return;
    sidebar.classList.remove('mobile-active');
    syncMobileSidebarBackdrop();
});

// Floating AI Logic
var floatingHistory = window.floatingHistory || [];
window.floatingHistory = floatingHistory;
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

function bindFloatingChatSend() {
    const btn = document.getElementById('floating-chat-send-btn');
    const input = document.getElementById('floating-chat-input');
    if (!btn || !input || btn.dataset.bound === '1') return;
    btn.dataset.bound = '1';
    const send = function () {
        const route = btn.getAttribute('data-chat-route');
        const csrf = btn.getAttribute('data-csrf');
        if (route && csrf) {
            sendFloatingMessage(route, csrf);
        }
    };
    btn.addEventListener('click', send);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            send();
        }
    });
}

bindFloatingChatSend();
document.addEventListener('DOMContentLoaded', bindFloatingChatSend);
document.addEventListener('turbolinks:load', bindFloatingChatSend);

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
                console.log('Map section found, loading...');
                
                // Extract styles from the page
                const styleContent = doc.querySelectorAll('style');
                let combinedStyles = '';
                styleContent.forEach(s => { combinedStyles += s.textContent; });
                
                // Create a style element with the extracted styles
                const styleEl = document.createElement('style');
                styleEl.textContent = combinedStyles;
                
                container.innerHTML = `<div class="embedded-map-wrap">${mapSection.outerHTML}</div>`;
                container.prepend(styleEl);
                
                // Apply inline styles to ensure proper rendering
                const mapFrame = container.querySelector('.map-frame');
                if (mapFrame) {
                    // Get the background-image URL from the inline style attribute (not computed style)
                    const inlineStyle = mapFrame.getAttribute('style') || '';
                    const bgMatch = inlineStyle.match(/background-image:\s*url\(['"]?([^'"\)]+)['"]?\)/i);
                    const bgImageUrl = bgMatch ? bgMatch[1] : null;
                    console.log('Inline style:', inlineStyle);
                    console.log('Background image URL:', bgImageUrl);
                    
                    // Clear the map frame and rebuild with proper structure
                    const svgLayer = mapFrame.querySelector('.map-svg-layer');
                    const landmarksEl = mapFrame.querySelector('.interactive-landmarks');
                    const actionCard = mapFrame.querySelector('.map-action-card');
                    const modal = mapFrame.parentElement.querySelector('#levelDetailsModal');
                    
                    // Create new structure with img tag for better compatibility
                    mapFrame.innerHTML = '';
                    mapFrame.style.cssText = `
                        width: 100%;
                        height: 100%;
                        min-height: 450px;
                        border-radius: 20px;
                        overflow: hidden;
                        position: relative;
                        background: #1e293b;
                    `;
                    
                    // Add background image as an img element
                    if (bgImageUrl) {
                        const bgImg = document.createElement('img');
                        bgImg.src = bgImageUrl;
                        bgImg.style.cssText = `
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            position: absolute;
                            top: 0;
                            left: 0;
                        `;
                        mapFrame.appendChild(bgImg);
                    }
                    
                    // Re-add SVG layer
                    if (svgLayer) {
                        svgLayer.style.cssText = `
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            pointer-events: none;
                            z-index: 5;
                        `;
                        mapFrame.appendChild(svgLayer);
                    }
                    
                    // Re-add landmarks
                    if (landmarksEl) {
                        landmarksEl.style.cssText = `
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            z-index: 10;
                        `;
                        mapFrame.appendChild(landmarksEl);
                    }
                    
                    // Move modal outside map-frame to body for proper z-index
                    if (modal) {
                        document.body.appendChild(modal);
                    }
                    
                    // Style action card for embedded view (position at bottom right)
                    if (actionCard) {
                        actionCard.style.cssText = `
                            position: absolute;
                            bottom: 20px;
                            right: 20px;
                            width: 280px;
                            background: rgba(255, 255, 255, 0.95);
                            backdrop-filter: blur(10px);
                            border-radius: 20px;
                            padding: 20px;
                            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                            z-index: 100;
                            border: 1px solid rgba(255,255,255,0.5);
                        `;
                        
                        // Style the header
                        const header = actionCard.querySelector('.action-card-header');
                        if (header) {
                            header.style.cssText = `
                                display: flex;
                                justify-content: space-between;
                                margin-bottom: 12px;
                            `;
                        }
                        
                        // Style the h4
                        const h4 = actionCard.querySelector('h4');
                        if (h4) {
                            h4.style.cssText = `
                                font-size: 1rem;
                                color: #1e293b;
                                font-weight: 800;
                                margin: 0;
                            `;
                        }
                        
                        // Style progress percent
                        const progressPercent = actionCard.querySelector('.progress-percent');
                        if (progressPercent) {
                            progressPercent.style.cssText = `
                                font-size: 0.9rem;
                                font-weight: 800;
                                color: #d97706;
                            `;
                        }
                        
                        // Style progress track
                        const progressTrack = actionCard.querySelector('.progress-track');
                        if (progressTrack) {
                            progressTrack.style.cssText = `
                                height: 8px;
                                background: #eee;
                                border-radius: 4px;
                                overflow: hidden;
                                margin-bottom: 15px;
                            `;
                        }
                        
                        // Style progress fill
                        const progressFill = actionCard.querySelector('.progress-fill');
                        if (progressFill) {
                            progressFill.style.height = '100%';
                            progressFill.style.background = '#ffd43b';
                            progressFill.style.borderRadius = '4px';
                        }
                        
                        // Style footer text
                        const footerP = actionCard.querySelector('.action-card-footer p');
                        if (footerP) {
                            footerP.style.cssText = `
                                font-size: 0.8rem;
                                color: #64748b;
                                margin-bottom: 15px;
                                font-weight: 600;
                            `;
                        }
                        
                        // Style buttons
                        const buttons = actionCard.querySelectorAll('.btn-primary-action, button[type="submit"]');
                        buttons.forEach(btn => {
                            btn.style.cssText = `
                                width: 100%;
                                background: #1e293b;
                                color: white;
                                border: none;
                                padding: 12px;
                                border-radius: 12px;
                                font-weight: 700;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 12px;
                                cursor: pointer;
                                transition: transform 0.2s;
                                text-decoration: none;
                                font-size: 0.95rem;
                            `;
                        });
                        
                        // Style completed button
                        const completedBtn = actionCard.querySelector('.btn-primary-action.completed');
                        if (completedBtn) {
                            completedBtn.style.background = '#10b981';
                            completedBtn.style.cursor = 'default';
                            completedBtn.style.opacity = '0.9';
                        }
                        
                        // Style expired button
                        const expiredBtn = actionCard.querySelector('.btn-primary-action.expired');
                        if (expiredBtn) {
                            expiredBtn.style.background = '#94a3b8';
                            expiredBtn.style.opacity = '0.7';
                        }
                    }
                }
                
                // Style landmark nodes - preserve their left/top positions and add full styling
                container.querySelectorAll('.landmark-node').forEach(node => {
                    const existingLeft = node.style.left;
                    const existingTop = node.style.top;
                    node.style.cssText = `
                        position: absolute;
                        transform: translate(-50%, -50%);
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        cursor: pointer;
                        left: ${existingLeft};
                        top: ${existingTop};
                    `;
                    
                    // Style the node-icon
                    const icon = node.querySelector('.node-icon');
                    if (icon) {
                        icon.style.cssText = `
                            width: 50px;
                            height: 50px;
                            background: #fff;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.3rem;
                            color: #64748b;
                            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
                            border: 3px solid #eee;
                        `;
                        
                        // Add active state
                        if (icon.classList.contains('active')) {
                            icon.style.background = '#ffd43b';
                            icon.style.color = '#1e293b';
                            icon.style.borderColor = '#fff';
                            icon.style.boxShadow = '0 0 25px rgba(255,212,59,0.6)';
                        }
                        if (icon.classList.contains('locked')) {
                            icon.style.background = '#cbd5e1';
                            icon.style.color = '#94a3b8';
                            icon.style.borderColor = '#f1f5f9';
                        }
                        if (icon.classList.contains('finish')) {
                            icon.style.background = '#1e293b';
                            icon.style.color = '#fbbf24';
                            icon.style.borderColor = '#334155';
                        }
                    }
                    
                    // Style the node-tag
                    const tag = node.querySelector('.node-tag');
                    if (tag) {
                        tag.style.cssText = `
                            margin-top: 10px;
                            background: rgba(0, 0, 0, 0.8);
                            color: white;
                            padding: 4px 12px;
                            border-radius: 6px;
                            font-size: 0.75rem;
                            font-weight: 700;
                            white-space: nowrap;
                        `;
                    }
                });
                
                // Execute only necessary inline scripts (skip Livewire, Turbolinks, etc.)
                const scripts = doc.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    // Skip external scripts that cause issues
                    const src = oldScript.src || '';
                    if (src.includes('livewire') || src.includes('turbolinks')) {
                        return;
                    }
                    
                    // Only execute inline scripts that contain modal functions
                    if (!oldScript.src && oldScript.textContent.includes('showLevelDetails')) {
                        const newScript = document.createElement('script');
                        newScript.textContent = oldScript.textContent;
                        document.body.appendChild(newScript);
                    }
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
