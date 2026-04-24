    .ai-panel {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
    }
    .ai-panel-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .ai-panel-header i {
        color: #0ea5e9;
        font-size: 1.5rem;
    }
    .ai-panel-header h3 {
        margin: 0;
        font-size: 1rem;
        color: #0369a1;
    }
    .ai-panel-header p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
    }
    .ai-form-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .ai-form-row .form-group {
        flex: 1;
        min-width: 200px;
        margin-bottom: 12px;
    }
    .btn-ai {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-ai:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1);
    }
    .btn-ai:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .ai-loading {
        display: none;
        align-items: center;
        gap: 8px;
        color: #0369a1;
        font-size: 0.9rem;
    }
    .ai-loading.show {
        display: flex;
    }
    @include('teacher.partials._quest_ai_model_picker_css')
