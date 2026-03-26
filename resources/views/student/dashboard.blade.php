<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Dashboard</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        <!-- FAVICON IN BROWSER TAB -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Turbolinks for SPA navigation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turbolinks/5.2.0/turbolinks.js" defer></script>
    @livewireStyles

    <style>
        :root {
            --primary: #300675b1;
            --secondary: #262840;
            --light-bg: #BFC5DB;
            --card-bg: #F1F1E0;
            --accent: #ffd43b;
            --accent-dark: #f5c400;
            --text-dark: #0b1020;
            --text-muted: #94a3b8;
            --header-bg: rgba(255, 255, 255, 1);
        }

        /* ... existing base styles ... */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            min-height: 100vh;
            display: flex;
            background-image: url('{{ asset('images/std-bg.png') }}');
            background-size: cover;
            color: var(--text-dark);
            position: relative;
            overflow-x: hidden;
        }

      

        /* SIDEBAR */
        aside {
            width: 270px;
            background: linear-gradient(180deg, var(--primary), var(--secondary));
            color: #e0e7ff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            box-shadow: 12px 0 24px rgba(0, 0, 0, 0.35);
            z-index: 20;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease;
        }

        aside.collapsed {
            width: 90px;
        }

        /* SIDEBAR HEADER - RPG STYLE */
        .sidebar-header {
            padding: 25px 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Avatar Section */
        .avatar-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .avatar-ring {
            position: relative;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, var(--accent), #f59e0b);
            flex-shrink: 0;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #1e293b;
        }

        .level-badge {
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 24px;
            height: 24px;
            background: var(--accent);
            color: #1e293b;
            border-radius: 50%;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #1e293b;
        }

        .player-info {
            flex: 1;
            min-width: 0;
        }

        .player-name {
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .player-class {
            font-size: 0.75rem;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* XP Section */
        .xp-section {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 10px;
            padding: 12px 15px;
        }

        .xp-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .xp-label {
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .xp-value {
            font-size: 0.75rem;
            color: var(--accent);
            font-weight: 600;
        }

        .xp-progress-bar {
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .xp-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), #fbbf24);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        /* SIDEBAR NAVIGATION */
        .sidebar-nav {
            padding: 15px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 20px;
        }

        .nav-label {
            display: block;
            font-size: 0.65rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 10px;
            margin-bottom: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            margin-bottom: 4px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            color: #cbd5e1;
            border-radius: 10px;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .nav-icon i {
            font-size: 0.95rem;
            color: #94a3b8;
            transition: color 0.2s ease;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .nav-item:hover .nav-icon,
        .nav-item.active .nav-icon {
            background: rgba(251, 191, 36, 0.15);
        }

        .nav-item:hover .nav-icon i,
        .nav-item.active .nav-icon i {
            color: var(--accent);
        }

        /* Logout Item */
        .nav-item.logout {
            color: #f87171;
        }

        .nav-item.logout .nav-icon i {
            color: #f87171;
        }

        .nav-item.logout:hover {
            background: rgba(248, 113, 113, 0.1);
            color: #ef4444;
        }

        .nav-item.logout:hover .nav-icon {
            background: rgba(248, 113, 113, 0.15);
        }

        /* Collapsed Sidebar Styles */
        aside.collapsed .avatar-section {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        aside.collapsed .avatar-ring {
            width: 45px;
            height: 45px;
        }

        aside.collapsed .level-badge {
            width: 18px;
            height: 18px;
            font-size: 0.6rem;
        }

        aside.collapsed .player-info,
        aside.collapsed .xp-section,
        aside.collapsed .nav-label,
        aside.collapsed .nav-item span {
            display: none;
        }

        aside.collapsed .nav-item {
            justify-content: center;
            padding: 12px;
        }

        aside.collapsed .nav-icon {
            width: 40px;
            height: 40px;
        }

        aside.collapsed .nav-section {
            margin-bottom: 10px;
        }

        .sidebar-footer {
            padding: 15px;
            font-size: 0.65rem;
            text-align: center;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            white-space: nowrap;
        }

        aside.collapsed .sidebar-footer {
            font-size: 0.5rem;
            padding: 10px 5px;
        }

        /* MAIN */
        main {
            flex: 1;
            margin-left: 270px;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        main.expanded {
            margin-left: 90px;
        }

        /* HEADER - WHITE THEME */
        header {
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--header-bg);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 15;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-btn:hover {
            background: rgba(0, 35, 102, 0.05);
        }

        header h1 {
            color: var(--primary);
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        header h1 i {
            color: var(--accent-dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            color: var(--text-dark);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .user-pill i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        section {
            flex: 1;
            padding: 30px 40px;
        }

        .dashboard-shell {
            background: rgba(255, 255, 255, 1);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        .dashboard-shell-1 {
            width: 100%;
            min-height: 80vh;
            display: grid;
            grid-template-columns: 450px 1fr;
            gap: 30px;
            align-items: start;
            /* Shell is now transparent */
        }

        .dashboard-left-col {
            background: rgba(255, 255, 255, 1);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 2;
        }

        .dashboard-right-col {
            position: relative;
            min-height: 600px;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        .character-name {
            font-size: 0.75rem;
            color: var(--accent);
            margin-top: 2px;
            font-weight: 500;
        }

        .shell-top {
            margin-bottom: 25px;
        }

        /* RPG STATS - ICON BASED */
        .stats-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-icon-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .stat-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .icon-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .hp-heart {
            color: #ef4444;
            font-size: 1.2rem;
            filter: drop-shadow(0 0 5px rgba(239, 68, 68, 0.3));
            animation: heartBeat 1.5s infinite ease-in-out;
        }

        .hp-heart.empty {
            color: #e2e8f0;
            filter: none;
            animation: none;
        }

        .xp-star {
            color: #f59e0b;
            font-size: 1.1rem;
            filter: drop-shadow(0 0 5px rgba(245, 158, 11, 0.3));
        }

        .xp-star.empty {
            color: #e2e8f0;
            filter: none;
        }

        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* POWERS GRID */
        .powers-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .powers-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .power-item {
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .power-item:hover {
            border-color: var(--accent);
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .power-icon {
            width: 40px;
            height: 40px;
            background: rgba(41, 0, 102, 0.05);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .power-info h4 {
            font-size: 0.85rem;
            margin-bottom: 2px;
            color: var(--text-dark);
        }

        .power-info p {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* QUIZ STATUS */
        .quiz-card {
            background: var(--primary);
            color: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .quiz-info h3 {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .quiz-info p {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .quiz-action {
            background: var(--accent);
            color: var(--text-dark);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .quiz-action:hover {
            background: var(--accent-dark);
            transform: scale(1.05);
        }

        /* QUEST CARD - DASHBOARD SPECIFIC */
        .quest-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
            border-radius: 20px;
            padding: 24px;
            color: white;
            border: 1px solid rgba(255, 212, 59, 0.2);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }

        .quest-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 212, 59, 0.05) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* TRANSPARENT MAP OVERLAY */
        .transparent-map-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(12px);
            z-index: 3000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .transparent-map-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .map-overlay-container {
            width: 90%;
            height: 90vh;
            display: flex;
            flex-direction: column;
            position: relative;
            animation: mapPop 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes mapPop {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .map-overlay-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            z-index: 2;
        }

        .map-overlay-header h3 { 
            margin: 0; 
            color: var(--accent); 
            font-size: 1.5rem; 
            text-shadow: 0 0 20px rgba(255, 212, 59, 0.3);
        }

        .btn-close-overlay {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close-overlay:hover {
            background: #ef4444;
            border-color: #ef4444;
            transform: rotate(90deg);
        }

        .map-content-scroll {
            flex: 1;
            overflow-y: auto;
            position: relative;
            padding: 20px;
        }

        /* Clean map background for overlay */
        .map-content-scroll .map-frame {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .drawer-loader { 
            text-align: center; 
            margin-top: 150px; 
            color: white; 
        }

        .spinner { 
            width: 50px; 
            height: 50px; 
            border: 5px solid rgba(255, 255, 255, 0.1); 
            border-top-color: var(--accent); 
            border-radius: 50%; 
            animation: spin 1s infinite linear; 
            margin: 0 auto 20px; 
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }

        .quest-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .quest-card-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--accent);
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .quest-card-diff {
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .quest-card-diff.easy { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .quest-card-diff.medium { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .quest-card-diff.hard { background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

        .quest-card-body {
            position: relative;
            z-index: 2;
            margin-bottom: 24px;
        }

        .quest-card-body p {
            font-size: 0.9rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .quest-card-rewards {
            display: flex;
            gap: 12px;
        }

        .reward-pill {
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .reward-pill.xp { color: #818cf8; }
        .reward-pill.gp { color: #fbbf24; }

        .quest-card-footer {
            position: relative;
            z-index: 2;
        }

        .btn-quest-action-preview {
            width: 100%;
            background: rgba(255, 212, 59, 0.1);
            color: var(--accent);
            text-align: center;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            border: 1px dashed rgba(255, 212, 59, 0.3);
        }

        .quest-card:hover .btn-quest-action-preview {
            background: var(--accent);
            color: #0b1020;
            border-style: solid;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 212, 59, 0.3);
        }

        .quest-card:hover { 
            border-color: var(--accent); 
            transform: translateY(-5px); 
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        /* REFINED MAP CARD STYLE */
        .quest-map-card {
            overflow: hidden;
            width: auto;
            display: flex;
            flex-direction: column;
            margin-right:-500px;
        }

       

        .quest-map-card-body {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            min-height: 500px;
            width: 100%;
        }

        .embedded-map-section {
            width: 100%;
            height: 100%;
            min-height: 450px;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .embedded-map-section.active {
            opacity: 1;
        }

        .map-placeholder {
            text-align: center;
            color: rgba(255, 255, 255, 0.2);
        }

        /* Clean map background for dashboard card embedding */
        .embedded-map-section .map-frame,
        .embedded-map-section .map-exploration-area {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        
        /* Wrapper for embedded map */
        .embedded-map-wrap {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .embedded-map-wrap .map-exploration-area {
            width: 100% !important;
            height: auto !important;
        }
        
        /* Preserve the map background image */
        .embedded-map-section .map-frame {
            background-color: transparent !important;
            border-radius: 20px !important;
            overflow: hidden !important;
            width: 100% !important;
            min-height: 400px !important;
            aspect-ratio: 1000 / 600 !important;
        }
        
        .embedded-map-wrap .map-frame {
            width: 100% !important;
            min-height: 450px !important;
            aspect-ratio: 1000 / 600 !important;
            background-size: cover !important;
            background-position: center !important;
        }
        
        /* Interactive landmarks container */
        .embedded-map-wrap .interactive-landmarks {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 10 !important;
        }
        
        /* Map exploration area styling */
        .embedded-map-section .map-exploration-area {
            position: relative !important;
            width: 100% !important;
        }
        
        /* Map SVG layer */
        .embedded-map-section .map-svg-layer {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            pointer-events: none !important;
            z-index: 5 !important;
        }
        
        /* Landmarks styling */
        .embedded-map-section .landmark-node {
            cursor: pointer !important;
        }
        
        /* Action card styling */
        .embedded-map-section .map-action-card {
            display: none !important;
        }

        /* Fix map scale in side col */
        .embedded-map-section .interactive-landmarks {
            transform: scale(0.9) !important;
            transform-origin: center;
        }
        
        /* Landmark nodes */
        .embedded-map-section .landmark-node {
            position: absolute !important;
            transform: translate(-50%, -50%) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            cursor: pointer !important;
            transition: transform 0.3s !important;
        }
        
        .embedded-map-section .landmark-node:hover {
            transform: translate(-50%, -50%) scale(1.1) !important;
        }
        
        /* Node icons */
        .embedded-map-section .node-icon {
            width: 45px !important;
            height: 45px !important;
            background: #fff !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.2rem !important;
            color: #64748b !important;
            box-shadow: 0 8px 15px rgba(0,0,0,0.3) !important;
            border: 3px solid #fff !important;
        }
        
        .embedded-map-section .node-icon.active {
            background: var(--accent) !important;
            color: #0b1020 !important;
            box-shadow: 0 0 25px rgba(255,212,59,0.7) !important;
            animation: nodePulse 2s infinite !important;
        }
        
        .embedded-map-section .node-icon.locked {
            background: #475569 !important;
            color: #94a3b8 !important;
            border-color: #334155 !important;
            opacity: 0.8 !important;
        }
        
        .embedded-map-section .node-icon.finish {
            background: #1e293b !important;
            color: var(--accent) !important;
            border-color: #334155 !important;
        }
        
        /* Node tags */
        .embedded-map-section .node-tag {
            margin-top: 8px !important;
            background: rgba(0, 0, 0, 0.85) !important;
            color: white !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            white-space: nowrap !important;
        }
        
        /* Node tooltips */
        .embedded-map-section .node-tooltip {
            position: absolute !important;
            bottom: 120% !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            background: #1e293b !important;
            color: white !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
            font-size: 0.75rem !important;
            white-space: nowrap !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: all 0.3s !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.4) !important;
            z-index: 100 !important;
        }
        
        .embedded-map-section .landmark-node:hover .node-tooltip {
            opacity: 1 !important;
            visibility: visible !important;
            bottom: 130% !important;
        }
        
        @keyframes nodePulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 212, 59, 0.5); }
            70% { box-shadow: 0 0 0 15px rgba(255, 212, 59, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 212, 59, 0); }
        }

        /* MODAL STYLES FOR EMBEDDED MAP */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5000;
            animation: fadeIn 0.3s ease-out;
        }

        .level-details-modal {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            width: 100%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid rgba(255, 212, 59, 0.2);
            box-shadow: 0 0 50px rgba(0,0,0,0.8);
            border-radius: 20px;
            padding: 30px;
            position: relative;
        }

        .level-details-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
        }

        .level-details-modal .modal-header h3 {
            color: var(--accent);
            margin: 0;
            font-size: 1.5rem;
        }

        .modal-questions-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-question-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 15px;
        }

        .modal-q-header {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
        }

        .q-type-badge {
            font-size: 0.65rem;
            font-weight: 800;
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            padding: 3px 8px;
            border-radius: 5px;
            text-transform: uppercase;
        }

        .q-points-badge {
            font-size: 0.65rem;
            font-weight: 800;
            background: rgba(255, 212, 59, 0.2);
            color: var(--accent);
            padding: 3px 8px;
            border-radius: 5px;
            text-transform: uppercase;
        }

        .q-text {
            font-size: 1rem;
            color: #e2e8f0;
            line-height: 1.5;
            margin-bottom: 0;
        }

        .btn-ok {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #0b1020;
            padding: 10px 22px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 700;
            transition: 0.2s;
        }

        .btn-close-modal {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-close-modal:hover { color: #fff; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* MAP FRAME & LANDMARK STYLES */
        .map-frame {
            position: relative;
            width: 100%;
            aspect-ratio: 1000 / 600;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .map-background {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .map-svg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 5;
        }

        .interactive-landmarks {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
        }

        .landmark-node {
            position: absolute;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .landmark-node:hover { 
            transform: translate(-50%, -50%) scale(1.15); 
            z-index: 20;
        }

        .node-icon {
            width: 45px;
            height: 45px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #64748b;
            box-shadow: 0 8px 15px rgba(0,0,0,0.3);
            border: 3px solid #fff;
            position: relative;
        }

        .node-icon.active {
            background: var(--accent);
            color: #0b1020;
            border-color: #fff;
            box-shadow: 0 0 25px rgba(255,212,59,0.7);
            animation: nodePulse 2s infinite;
        }

        .node-icon.locked { 
            background: #475569; 
            color: #94a3b8; 
            border-color: #334155; 
            box-shadow: none; 
            opacity: 0.8;
        }

        .node-icon.finish { 
            background: #1e293b; 
            color: var(--accent); 
            border-color: #334155; 
        }

        @keyframes nodePulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 212, 59, 0.5); }
            70% { box-shadow: 0 0 0 15px rgba(255, 212, 59, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 212, 59, 0); }
        }

        .node-tag {
            margin-top: 8px;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            white-space: nowrap;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .node-tooltip {
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.4);
            z-index: 100;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .landmark-node:hover .node-tooltip {
            opacity: 1;
            visibility: visible;
            bottom: 130%;
        }

        .quest-status-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 10px;
            display: inline-block;
        }

        .quest-empty-state {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
        }

        .quest-empty-state i {
            font-size: 2rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }


        /* FLOATING AI ASSISTANT */
        .ai-floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #0b1020;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: float-pulse 2s infinite ease-in-out;
        }

        .ai-floating-btn:hover {
            transform: scale(1.1) rotate(10deg);
        }

        @keyframes float-pulse {
            0%, 100% { box-shadow: 0 4px 20px rgba(255, 212, 59, 0.4); transform: translateY(0); }
            50% { box-shadow: 0 4px 30px rgba(255, 212, 59, 0.6); transform: translateY(-5px); }
        }

        .ai-chat-window {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 380px;
            height: 500px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            display: none;
            flex-direction: column;
            z-index: 1000;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            overflow: hidden;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ai-chat-window.active {
            display: flex;
        }

        .ai-chat-header {
            padding: 15px 20px;
            background: linear-gradient(90deg, rgba(0,35,102,0.9), rgba(38,40,64,0.9));
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        .ai-chat-header .info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-chat-header .avatar {
            width: 35px;
            height: 35px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0b1020;
            font-size: 1.1rem;
        }

        .ai-chat-header h4 { font-size: 0.95rem; margin: 0; }
        .ai-chat-header p { font-size: 0.7rem; color: #94a3b8; margin: 0; }

        .close-chat {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .close-chat:hover { color: white; }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }

        .message {
            max-width: 85%;
            padding: 10px 14px;
            border-radius: 15px;
            font-size: 0.85rem;
            line-height: 1.4;
            animation: msgFade 0.3s ease-out;
        }

        @keyframes msgFade {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.ai {
            align-self: flex-start;
            background: rgba(30, 41, 59, 0.8);
            color: #e2e8f0;
            border-bottom-left-radius: 2px;
        }

        .message.user {
            align-self: flex-end;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .chat-input-area {
            padding: 15px;
            background: rgba(15,23,42,0.6);
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            gap: 10px;
        }

        .chat-input-area input {
            flex: 1;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 8px 15px;
            color: white;
            font-size: 0.85rem;
            outline: none;
        }

        .chat-input-area button {
            width: 35px;
            height: 35px;
            background: var(--accent);
            border: none;
            border-radius: 50%;
            color: #0b1020;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .chat-input-area button:hover { transform: scale(1.1); }
        
        .typing-indicator {
            display: none;
            padding: 5px 20px;
            font-size: 0.75rem;
            color: #94a3b8;
            font-style: italic;
        }

        @media (max-width: 768px) {
            aside { transform: translateX(-100%); width: 270px !important; }
            aside.mobile-active { transform: translateX(0); }
            main { margin-left: 0 !important; }
            header { padding: 10px 15px; }
            .shell-top { grid-template-columns: 1fr; }
            .dashboard-shell-1 { grid-template-columns: 1fr; }
            .dashboard-left-col { margin-bottom: 25px; }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside id="sidebar">
        <div>
            <div class="sidebar-header">
                @php
                    $user = Auth::user();
                    $profilePic = $user?->profile_pic ?? 'default-pp.png';
                    $userName = $user?->name ?? 'Student';
                    $userXP = $user?->xp ?? 0;
                    $userLevel = floor($userXP / 100) + 1;
                    $xpForNextLevel = $userLevel * 100;
                    $xpProgress = ($userXP % 100);
                @endphp
                
                <!-- Avatar Section -->
                <div class="avatar-section">
                    <div class="avatar-ring">
                        <img src="{{ asset('images/' . $profilePic) }}" alt="Avatar" class="avatar-img">
                        <div class="level-badge">{{ $userLevel }}</div>
                    </div>
                    <div class="player-info">
                        <div class="player-name">{{ $userName }}</div>
                        <div class="player-class">{{ ucfirst($user?->character ?? 'Adventurer') }}</div>
                    </div>
                </div>

                <!-- XP Progress -->
                <div class="xp-section">
                    <div class="xp-info">
                        <span class="xp-label">XP</span>
                        <span class="xp-value">{{ $userXP }} / {{ $xpForNextLevel }}</span>
                    </div>
                    <div class="xp-progress-bar">
                        <div class="xp-progress-fill" style="width: {{ $xpProgress }}%"></div>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <span class="nav-label">Main</span>
                    <a href="{{ route('student.dashboard') }}" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-home"></i></div>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('student.quest') }}" class="nav-item {{ request()->routeIs('student.quest') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-map-signs"></i></div>
                        <span>Quests</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Learning</span>
                    <a href="{{ route('student.lessons') }}" class="nav-item {{ request()->routeIs('student.lessons') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-book-open"></i></div>
                        <span>Lessons</span>
                    </a>
                    <a href="{{ route('student.quizzes') }}" class="nav-item {{ request()->routeIs('student.quizzes') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-clipboard-check"></i></div>
                        <span>Quizzes</span>
                    </a>
                    <a href="{{ route('student.performance') }}" class="nav-item {{ request()->routeIs('student.performance') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
                        <span>Performance</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Social</span>
                    <a href="{{ route('student.messages') }}" class="nav-item {{ request()->routeIs('student.messages') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-comments"></i></div>
                        <span>Messages</span>
                    </a>
                    <a href="{{ route('student.feedback') }}" class="nav-item {{ request()->routeIs('student.feedback') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-comment-dots"></i></div>
                        <span>Feedback</span>
                    </a>
                </div>

                <div class="nav-section">
                    <span class="nav-label">Account</span>
                    <a href="{{ route('student.registration') }}" class="nav-item {{ request()->routeIs('student.registration') ? 'active' : '' }}">
                        <div class="nav-icon"><i class="fas fa-id-card"></i></div>
                        <span>Registration</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    <a href="#" class="nav-item logout" onclick="event.preventDefault(); showLogoutModal();">
                        <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </div>

        <div class="sidebar-footer">© 2025 Level Up ASIANISTA</div>
    </aside>

    <!-- MAIN -->
    <main id="main-content">
        <header>
            <div class="header-left">
                <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <h1><i class="fas fa-gamepad"></i> Student Realm</h1>
            </div>
            <div class="header-right">
                <div class="user-pill">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ Auth::user()?->name ?? 'Student' }}</span>
                </div>
            </div>
        </header>

        <section>
            @if(request()->routeIs('student.dashboard'))
            <div class="{{ request()->routeIs('student.dashboard') ? 'dashboard-shell-1' : 'dashboard-shell' }}">
                <div class="dashboard-left-col">
                    <div class="shell-top">
                        <div class="stats-container">
                            <div class="stat-icon-group">
                                <div class="stat-meta">
                                    <span><i class="fas fa-heart"></i> Health (HP)</span>
                                    <span>{{ $currentHP }} / {{ $maxHP }} HP</span>
                                </div>
                                <div class="icon-row">
                                    @for($i = 0; $i < 10; $i++)
                                        @if($i < ceil($currentHP / 10))
                                            <i class="fas fa-heart hp-heart"></i>
                                        @else
                                            <i class="far fa-heart hp-heart empty"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>

                            <div class="stat-icon-group">
                                <div class="stat-meta">
                                    <span><i class="fas fa-bolt"></i> Action Points (AP)</span>
                                    <span>{{ $currentAP }} / {{ $maxAP }} AP</span>
                                </div>
                                <div class="icon-row">
                                    @for($i = 0; $i < 10; $i++)
                                        @if($i < ceil($currentAP / 10))
                                            <i class="fas fa-bolt xp-star" style="color: #3b82f6;"></i>
                                        @else
                                            <i class="far fa-bolt xp-star empty" style="color: #e2e8f0;"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="powers-title">
                            <i class="fas fa-magic"></i> {{ $characterData['name'] ?? 'Character' }} Powers
                        </div>
                        <div class="powers-grid">
                            @forelse($powers as $powerName => $powerDesc)
                                <div class="power-item" title="{{ $powerDesc }}">
                                    <div class="power-icon">
                                        @switch(strtolower($powerName))
                                            @case('spell of insight') @case('power strike') @case('healing light')
                                                <i class="fas fa-hand-sparkles"></i>
                                                @break
                                            @case('mana boost') @case('streak master') @case('team blessing')
                                                <i class="fas fa-arrow-up"></i>
                                                @break
                                            @case('time warp') @case('shield guard') @case('revive')
                                                <i class="fas fa-shield-alt"></i>
                                                @break
                                            @case('knowledge burst') @case('battle rush') @case('focus aura')
                                                <i class="fas fa-bolt"></i>
                                                @break
                                            @case('arcane analysis') @case('challenge duel') @case('wisdom share')
                                                <i class="fas fa-brain"></i>
                                                @break
                                            @default
                                                <i class="fas fa-star"></i>
                                        @endswitch
                                    </div>
                                    <div class="power-info">
                                        <h4>{{ $powerName }}</h4>
                                        <p>{{ Str::limit($powerDesc, 40) }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="power-item">
                                    <div class="power-icon"><i class="fas fa-question"></i></div>
                                    <div class="power-info">
                                        <h4>No Powers</h4>
                                        <p>Complete registration to unlock powers</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>


                        <div class="quest-card" onclick="openQuestDrawer('{{ route('student.quest.show', $activeQuest->id ?? 0) }}')" style="cursor: pointer;">
                            @if(isset($activeQuest) && $activeQuest)
                                <div class="quest-card-header">
                                    <div class="quest-card-title">
                                        <i class="fas fa-scroll"></i> Current Quest
                                    </div>
                                    <span class="quest-card-diff {{ strtolower($activeQuest->difficulty ?? 'medium') }}">
                                        {{ $activeQuest->difficulty ?? 'Medium' }}
                                    </span>
                                </div>
                                <div class="quest-card-body">
                                    <h3 style="margin-bottom: 8px; font-size: 1.1rem; color: white;">{{ $activeQuest->title }}</h3>
                                    <p>{{ Str::limit($activeQuest->description, 100) }}</p>
                                    
                                    <div class="quest-card-rewards">
                                        <div class="reward-pill xp">
                                            <i class="fas fa-bolt"></i> +{{ $activeQuest->xp_reward ?? 0 }} XP
                                        </div>
                                        <div class="reward-pill gp">
                                            <i class="fas fa-coins"></i> +{{ $activeQuest->gp_reward ?? 0 }} GP
                                        </div>
                                    </div>
                                </div>
                                <div class="quest-card-footer">
                                    <div class="btn-quest-action-preview">
                                        View Map & Adventure <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            @else
                                <div class="quest-empty-state">
                                    <i class="fas fa-scroll"></i>
                                    <p>No active quests at the moment.<br>Check back later for new adventures!</p>
                                </div>
                            @endif
                        </div> 
                    </div> <!-- End shell-top -->
                </div> <!-- End Left Column -->

                <div class="dashboard-right-col">
                    <!-- SEPARATE QUEST MAP CARD -->
                    <div class="quest-map-card">
                        
                        <div class="quest-map-card-body">
                            <div id="embedded-map-container" class="embedded-map-section">
                                <div class="map-placeholder">
                                    <i class="fas fa-compass" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
                                    <p>Select your quest to reveal the path through ASIANISTA...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End dashboard-shell-1 -->
            @yield('content')
            @else
                <div class="dashboard-shell">
                    @yield('content')
                </div>
            @endif

        </section>
    </main>

    <!-- FLOATING AI ASSISTANT -->
    <div class="ai-floating-btn" onclick="toggleAIChat()">
        <i class="fas fa-robot"></i>
    </div>

    <div class="ai-chat-window" id="ai-chat-window">
        <div class="ai-chat-header">
            <div class="info">
                <div class="avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <h4>Neural Sage</h4>
                    <p><i class="fas fa-circle" style="color: #10b981; font-size: 0.5rem;"></i> Online</p>
                </div>
            </div>
            <button class="close-chat" onclick="toggleAIChat()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="chat-messages" id="floating-chat-messages">
            <div class="message ai">
                Greetings! I am the Neural Sage. How can I assist you in your quest today?
            </div>
        </div>

        <div class="typing-indicator" id="floating-typing-indicator">
            The Sage is weaving wisdom...
        </div>

        <div class="chat-input-area">
            <input type="text" id="floating-chat-input" placeholder="Ask anything..." autocomplete="off">
            <button onclick="sendFloatingMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        // Apply persisted sidebar state on every Turbolinks navigation
        function applySidebarState() {
            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'collapsed') {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('main-content').classList.add('expanded');
            }
        }
        applySidebarState();
        document.addEventListener('turbolinks:load', applySidebarState);

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('main-content');
            
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
            document.getElementById('ai-chat-window').classList.toggle('active');
        }

        async function sendFloatingMessage() {
            const input = document.getElementById('floating-chat-input');
            const messages = document.getElementById('floating-chat-messages');
            const indicator = document.getElementById('floating-typing-indicator');
            const text = input.value.trim();

            if (!text) return;

            input.value = '';
            appendFloatingMessage('user', text);
            
            indicator.style.display = 'block';
            messages.scrollTop = messages.scrollHeight;

            try {
                const response = await fetch("{{ route('student.ai.chat') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
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
                indicator.style.display = 'none';
                messages.scrollTop = messages.scrollHeight;
            }
        }

        function appendFloatingMessage(role, text) {
            const messages = document.getElementById('floating-chat-messages');
            const msgDiv = document.createElement('div');
            msgDiv.classList.add('message', role);
            msgDiv.innerText = text;
            messages.appendChild(msgDiv);
            
            floatingHistory.push({ role: role === 'ai' ? 'assistant' : 'user', content: text });
        }

        document.getElementById('floating-chat-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendFloatingMessage();
        });
        async function openQuestDrawer(url) {
            if (url.includes('/0')) return; 
            
            const container = document.getElementById('embedded-map-container');
            
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
                        // We wrap it to ensure we can scroll or position if needed
                        container.innerHTML = `<div class="embedded-map-wrap">${mapSection.outerHTML}</div>`;
                        
                        // Execute scripts (for modals, etc.)
                        const scripts = doc.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            container.appendChild(newScript);
                        });

                        // Trigger slow fade in
                        setTimeout(() => {
                            container.classList.add('active');
                            container.style.opacity = '1';
                        }, 100);

                    } else {
                        container.innerHTML = '<div class="alert alert-warning">The map is hidden in mists.</div>';
                        container.style.opacity = '1';
                    }
                })
                .catch(err => {
                    container.innerHTML = '<div class="alert alert-danger">Realm unreachable.</div>';
                    container.style.opacity = '1';
                });
        }
    </script>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false" data-turbo-eval="false"></script>

    <!-- LOGOUT CONFIRMATION MODAL -->
    <div id="logoutConfirmationModal" class="student-modal-overlay" style="display: none;">
        <div class="student-modal-box">
            <div class="student-modal-header">
                <h3><i class="fas fa-sign-out-alt"></i> Confirm Logout</h3>
            </div>
            <div class="student-modal-body">
                <p>Are you sure you want to end your adventure for today?</p>
            </div>
            <div class="student-modal-footer">
                <button onclick="closeLogoutModal()" class="btn-student-cancel">Cancel</button>
                <button onclick="document.getElementById('logout-form').submit();" class="btn-student-logout">Logout</button>
            </div>
        </div>
    </div>

    <style>
        .student-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .student-modal-box {
            background: #1e293b;
            border-radius: 20px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 212, 59, 0.2);
            color: white;
            text-align: center;
            animation: modalPopUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes modalPopUp {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .student-modal-header h3 {
            color: #ffd43b;
            font-size: 1.5rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .student-modal-body p {
            font-size: 1rem;
            color: #94a3b8;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .student-modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .btn-student-cancel {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-student-cancel:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-student-logout {
            background: #ef4444;
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-student-logout:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(239, 68, 68, 0.4);
        }

        /* Level Details Modal Styles */
        #levelDetailsModal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #levelDetailsModal .level-details-modal {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            width: 100%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid rgba(255, 212, 59, 0.2);
            box-shadow: 0 0 50px rgba(0,0,0,0.8);
            border-radius: 20px;
            padding: 30px;
        }

        #levelDetailsModal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
        }

        #levelDetailsModal .modal-header h3 {
            color: #ffd43b;
            margin: 0;
            font-size: 1.5rem;
        }

        #levelDetailsModal .btn-close-modal {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        #levelDetailsModal .btn-close-modal:hover { color: #fff; }

        #levelDetailsModal .modal-questions-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        #levelDetailsModal .modal-footer {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
        }

        #levelDetailsModal .btn-ok {
            background: linear-gradient(135deg, #ffd43b, #d97706);
            color: #0b1020;
            padding: 10px 22px;
            border: none;
            border-radius: 999px;
            cursor: pointer;
            font-weight: 700;
            transition: 0.2s;
        }

        #levelDetailsModal .btn-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 212, 59, 0.4);
        }
    </style>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutConfirmationModal').style.display = 'flex';
        }

        function closeLogoutModal() {
            document.getElementById('logoutConfirmationModal').style.display = 'none';
        }

        // Level Details Modal Functions (for embedded quest map)
        function showLevelDetails(level, questions) {
            const modal = document.getElementById('levelDetailsModal');
            const levelSpan = document.getElementById('modalLevel');
            const list = document.getElementById('modalQuestionsList');
            
            if (levelSpan) levelSpan.textContent = level;
            if (list) {
                list.innerHTML = '';
                if (questions && questions.length > 0) {
                    questions.forEach((q, i) => {
                        const card = document.createElement('div');
                        card.className = 'modal-question-card';
                        card.style.cssText = 'background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px; margin-bottom: 10px;';
                        card.innerHTML = `
                            <div style="display: flex; gap: 10px; margin-bottom: 8px;">
                                <span style="font-size: 0.65rem; font-weight: 800; background: rgba(59,130,246,0.2); color: #60a5fa; padding: 3px 8px; border-radius: 5px; text-transform: uppercase;">${q.type ? q.type.replace('-', ' ') : 'Question'}</span>
                                <span style="font-size: 0.65rem; font-weight: 800; background: rgba(255,212,59,0.2); color: #ffd43b; padding: 3px 8px; border-radius: 5px; text-transform: uppercase;">${q.points} PTS</span>
                            </div>
                            <p style="font-size: 1rem; color: #e2e8f0; line-height: 1.5; margin: 0;">${q.question}</p>
                        `;
                        list.appendChild(card);
                    });
                } else {
                    list.innerHTML = '<p style="color: #94a3b8; text-align: center;">No questions available for this level.</p>';
                }
            }
            if (modal) modal.style.display = 'flex';
        }

        function closeLevelModal() {
            const modal = document.getElementById('levelDetailsModal');
            if (modal) modal.style.display = 'none';
        }

        // Close on overlay click
        window.addEventListener('click', function(event) {
            const logoutModal = document.getElementById('logoutConfirmationModal');
            const levelModal = document.getElementById('levelDetailsModal');
            if (event.target == logoutModal) {
                closeLogoutModal();
            }
            if (event.target == levelModal) {
                closeLevelModal();
            }
        });
    </script>

    <!-- Random Event Popup Modal -->
    <div id="randomEventModal" class="random-event-modal" style="display: none;">
        <div class="random-event-content">
            <div class="scroll-container">
                <div class="scroll-top"></div>
                <div class="scroll-paper">
                    <div class="event-badge">RANDOM EVENT!</div>
                    <h2 class="event-title" id="eventTitle">Event Title</h2>
                    <p class="event-description" id="eventDescription">Description</p>
                    <div class="scroll-divider"></div>
                    <div class="event-effect" id="eventEffect">Effect</div>
                    <div class="event-xp" id="eventXp"></div>
                </div>
                <div class="scroll-bottom"></div>
            </div>
            <button onclick="acknowledgeEvent()" class="acknowledge-btn">GOT IT!</button>
        </div>
    </div>

    <style>
        .random-event-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            animation: fadeIn 0.3s ease;
        }
        
        .random-event-modal.show {
            display: flex;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .random-event-content {
            max-width: 500px;
            width: 90%;
            animation: popIn 0.4s ease;
        }
        
        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .scroll-container {
            position: relative;
        }
        
        .scroll-top, .scroll-bottom {
            height: 35px;
            background: linear-gradient(180deg, #d4a574 0%, #c49a6c 50%, #b08d5f 100%);
            border-radius: 17px;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.3), inset 0 -2px 4px rgba(0,0,0,0.2), 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .scroll-top::before, .scroll-top::after,
        .scroll-bottom::before, .scroll-bottom::after {
            content: '';
            position: absolute;
            width: 25px;
            height: 45px;
            background: linear-gradient(90deg, #8b6914 0%, #a67c2e 50%, #8b6914 100%);
            border-radius: 0 0 12px 12px;
            top: 0;
            box-shadow: inset 0 -3px 6px rgba(0,0,0,0.3);
        }
        
        .scroll-top::before, .scroll-bottom::before { left: -8px; }
        .scroll-top::after, .scroll-bottom::after { right: -8px; }
        
        .scroll-bottom::before, .scroll-bottom::after {
            border-radius: 12px 12px 0 0;
            top: auto;
            bottom: 0;
        }
        
        .scroll-paper {
            background: linear-gradient(180deg, #f5e6d3 0%, #f0dcc0 50%, #ebd5b3 100%);
            padding: 30px 35px;
            margin: auto;
            position: relative;
            box-shadow: inset 0 0 60px rgba(139, 105, 20, 0.1), 0 4px 20px rgba(0,0,0,0.15);
            text-align: center;
        }
        
        .event-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .event-title {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 1.5rem;
            color: #4a3728;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
        }
        
        .event-description {
            font-style: italic;
            color: #6b5344;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .scroll-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #c4a77d, transparent);
            margin: 15px 0;
        }
        
        .event-effect {
            color: #4a3728;
            font-size: 1rem;
            font-weight: 500;
            line-height: 1.5;
        }
        
        .event-xp {
            margin-top: 15px;
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .event-xp.reward { color: #22c55e; }
        .event-xp.penalty { color: #ef4444; }
        
        .acknowledge-btn {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 14px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .acknowledge-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }
    </style>

    <script>
        (function() {
            // Use window to avoid conflicts with Turbolinks
            if (window.randomEventChecker) return; // Prevent duplicate initialization
            window.randomEventChecker = true;
            
            let currentActiveEventId = null;
            let checkInterval = null;
            
            // Check for new events every 3 seconds
            function checkForEvents() {
                console.log('Checking for events...');
                fetch('{{ route("student.events.check") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Event data:', data);
                    if (data.has_event) {
                        console.log('Showing event modal!');
                        showEventModal(data.event);
                        currentActiveEventId = data.active_event_id;
                    }
                })
                .catch(error => console.error('Error checking events:', error));
            }
            
            function showEventModal(event) {
                const modal = document.getElementById('randomEventModal');
                if (!modal) {
                    console.error('Modal not found!');
                    return;
                }
                
                document.getElementById('eventTitle').textContent = event.title;
                document.getElementById('eventDescription').textContent = event.description;
                document.getElementById('eventEffect').textContent = event.effect;
                
                const xpDiv = document.getElementById('eventXp');
                if (event.xp_reward > 0) {
                    xpDiv.innerHTML = `<span class="reward">+${event.xp_reward} XP</span>`;
                    xpDiv.className = 'event-xp reward';
                } else if (event.xp_penalty > 0) {
                    xpDiv.innerHTML = `<span class="penalty">-${event.xp_penalty} XP</span>`;
                    xpDiv.className = 'event-xp penalty';
                } else {
                    xpDiv.innerHTML = '';
                }
                
                // Force display with inline style and class
                modal.setAttribute('style', 'display: flex !important; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 99999; align-items: center; justify-content: center;');
                modal.classList.add('show');
                console.log('Modal should be visible now, display:', modal.style.display);
            }
            
            window.acknowledgeEvent = function() {
                const modal = document.getElementById('randomEventModal');
                if (modal) {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                }
                
                if (currentActiveEventId) {
                    fetch('{{ route("student.events.acknowledge") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ active_event_id: currentActiveEventId })
                    })
                    .catch(error => console.error('Error acknowledging event:', error));
                }
            };
            
            // Start polling when page loads
            function init() {
                if (checkInterval) clearInterval(checkInterval);
                checkForEvents(); // Check immediately
                checkInterval = setInterval(checkForEvents, 3000); // Then every 3 seconds
            }
            
            // Handle both initial load and Turbolinks navigation
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
            
            document.addEventListener('turbolinks:load', init);
        })();
    </script>
</body>
</html>
