{{-- Shared CSS for teacher.quest._ai_model_picker (quest create, lessons, etc.) --}}
    /* AI model picker (brand logos via Simple Icons CDN) */
    .quest-ai-model-picker {
        position: relative;
        z-index: 2;
    }
    .quest-ai-model-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
        text-align: left;
        cursor: pointer;
        background: #fff;
        min-height: 42px;
    }
    .quest-ai-model-trigger:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    .quest-ai-model-trigger-inner {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }
    .quest-ai-model-trigger-inner span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: 500;
    }
    .quest-ai-model-chevron {
        flex-shrink: 0;
        opacity: 0.45;
        font-size: 0.72rem;
        transition: transform 0.2s ease;
    }
    .quest-ai-model-picker.is-open .quest-ai-model-chevron {
        transform: rotate(180deg);
    }
    .quest-ai-model-menu {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        margin: 0;
        padding: 6px;
        list-style: none;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-lg);
        max-height: 280px;
        overflow-y: auto;
        z-index: 1200;
    }
    .quest-ai-model-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        color: var(--text-primary);
    }
    .quest-ai-model-option:hover {
        background: var(--bg-main);
    }
    .quest-ai-model-logo {
        width: 22px;
        height: 22px;
        object-fit: contain;
        flex-shrink: 0;
        opacity: 0.9;
    }
    .quest-ai-model-logo-fa {
        width: 22px;
        min-width: 22px;
        font-size: 1rem;
        line-height: 22px;
        text-align: center;
        color: var(--text-secondary);
        flex-shrink: 0;
    }
