<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dokumentasi API - Sistem Informasi Manajemen Inventaris Sekolah">
    <title>API Docs — SIMINS (Sistem Manajemen Inventaris Sekolah)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* =========================================================
           CSS VARIABLES & THEME SYSTEM
           ========================================================= */
        :root {
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
            --shadow-lg: 0 8px 30px rgba(0,0,0,0.15);
        }

        [data-theme="dark"] {
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-tertiary: #1a2035;
            --bg-card: #151b2e;
            --bg-code: #0d1117;
            --bg-hover: #1e2943;
            --bg-sidebar: #0d1220;
            --border: #1e293b;
            --border-light: #253049;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --accent-glow: rgba(99,102,241,0.15);
            --green: #22c55e;
            --green-bg: rgba(34,197,94,0.1);
            --blue: #3b82f6;
            --blue-bg: rgba(59,130,246,0.1);
            --yellow: #f59e0b;
            --yellow-bg: rgba(245,158,11,0.1);
            --red: #ef4444;
            --red-bg: rgba(239,68,68,0.1);
            --purple: #a855f7;
            --purple-bg: rgba(168,85,247,0.1);
            --orange: #f97316;
            --orange-bg: rgba(249,115,22,0.1);
            --scrollbar-thumb: #334155;
            --scrollbar-track: transparent;
        }

        [data-theme="light"] {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;
            --bg-card: #ffffff;
            --bg-code: #1e293b;
            --bg-hover: #e2e8f0;
            --bg-sidebar: #ffffff;
            --border: #e2e8f0;
            --border-light: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent: #4f46e5;
            --accent-hover: #6366f1;
            --accent-glow: rgba(79,70,229,0.08);
            --green: #16a34a;
            --green-bg: rgba(22,163,74,0.08);
            --blue: #2563eb;
            --blue-bg: rgba(37,99,235,0.08);
            --yellow: #d97706;
            --yellow-bg: rgba(217,119,6,0.08);
            --red: #dc2626;
            --red-bg: rgba(220,38,38,0.08);
            --purple: #9333ea;
            --purple-bg: rgba(147,51,234,0.08);
            --orange: #ea580c;
            --orange-bg: rgba(234,88,12,0.08);
            --scrollbar-thumb: #cbd5e1;
            --scrollbar-track: transparent;
        }

        /* =========================================================
           BASE RESET & LAYOUT
           ========================================================= */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-sans);
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--scrollbar-track); }
        ::-webkit-scrollbar-thumb { background: var(--scrollbar-thumb); border-radius: 3px; }

        .app-layout {
            min-height: 100vh;
        }

        /* =========================================================
           SIDEBAR
           ========================================================= */
        .sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            width: 300px;
            height: 100vh;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .sidebar-logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            font-weight: 800;
            flex-shrink: 0;
        }

        .sidebar-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .sidebar-subtitle {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sidebar-search {
            padding: 12px 20px;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 9px 12px 9px 36px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: var(--font-sans);
            font-size: 13px;
            outline: none;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .search-input::placeholder { color: var(--text-muted); }

        .method-filters {
            display: flex;
            gap: 4px;
            padding: 0 20px 12px;
            flex-wrap: wrap;
        }

        .method-filter-btn {
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 600;
            font-family: var(--font-mono);
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .method-filter-btn:hover { border-color: var(--text-secondary); color: var(--text-secondary); }

        .method-filter-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 8px 12px 20px;
        }

        .nav-group { margin-bottom: 6px; }

        .nav-group-header {
            padding: 8px 12px;
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border-radius: var(--radius-xs);
            transition: var(--transition);
            user-select: none;
        }

        .nav-group-header:hover { color: var(--text-secondary); }

        .nav-group-header .chevron {
            width: 12px;
            height: 12px;
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .nav-group.collapsed .chevron { transform: rotate(-90deg); }
        .nav-group.collapsed .nav-items { display: none; }

        .nav-items { padding: 2px 0 4px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: var(--radius-xs);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 12.5px;
        }

        .nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }

        .nav-item.active {
            background: var(--accent-glow);
            color: var(--accent-hover);
        }

        .nav-method {
            font-family: var(--font-mono);
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            flex-shrink: 0;
            min-width: 36px;
            text-align: center;
            letter-spacing: 0.3px;
        }

        .nav-method.get { background: var(--green-bg); color: var(--green); }
        .nav-method.post { background: var(--blue-bg); color: var(--blue); }
        .nav-method.put { background: var(--yellow-bg); color: var(--yellow); }
        .nav-method.patch { background: var(--orange-bg); color: var(--orange); }
        .nav-method.delete { background: var(--red-bg); color: var(--red); }

        .nav-path {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 12px;
        }

        .sidebar-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 12px;
            font-family: var(--font-sans);
            transition: var(--transition);
        }

        .theme-toggle:hover { border-color: var(--accent); color: var(--text-primary); }
        .theme-toggle svg { width: 14px; height: 14px; }

        .endpoint-count {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* =========================================================
           MOBILE SIDEBAR TOGGLE
           ========================================================= */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            z-index: 200;
            padding: 0 16px;
            align-items: center;
            gap: 12px;
        }

        .hamburger {
            background: none;
            border: none;
            color: var(--text-primary);
            cursor: pointer;
            padding: 4px;
        }

        .hamburger svg { width: 24px; height: 24px; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }

        /* =========================================================
           MAIN CONTENT
           ========================================================= */
        .main-content {
            margin-left: 300px;
            padding: 40px 48px;
            max-width: 1100px;
            min-width: 0;
            width: calc(100% - 300px);
        }

        /* HERO */
        .hero {
            margin-bottom: 48px;
            padding: 40px;
            background: linear-gradient(135deg, var(--bg-card), var(--bg-tertiary));
            border: 1px solid var(--border);
            border-radius: var(--radius);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--accent-glow), transparent 70%);
            pointer-events: none;
        }

        .hero h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--accent-hover), #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .hero p { color: var(--text-secondary); font-size: 15px; position: relative; margin-bottom: 6px; }

        .hero-meta {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
            position: relative;
        }

        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .hero-meta-item span { color: var(--text-secondary); font-weight: 500; }

        .base-url-box {
            margin-top: 20px;
            padding: 12px 16px;
            background: var(--bg-code);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .base-url-box code {
            font-family: var(--font-mono);
            font-size: 13px;
            color: var(--green);
        }

        .base-url-box .label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-right: 12px;
        }

        /* SECTION */
        .section-group {
            margin-bottom: 48px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            padding-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title .icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-xs);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .section-desc {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* ENDPOINT CARD */
        .endpoint-card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 12px;
            overflow: hidden;
            transition: var(--transition);
            background: var(--bg-card);
        }

        .endpoint-card:hover { border-color: var(--border-light); }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            cursor: pointer;
            transition: var(--transition);
            user-select: none;
        }

        .endpoint-header:hover { background: var(--bg-hover); }

        .method-badge {
            font-family: var(--font-mono);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: var(--radius-xs);
            text-transform: uppercase;
            flex-shrink: 0;
            min-width: 60px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .method-badge.get { background: var(--green-bg); color: var(--green); border: 1px solid rgba(34,197,94,0.2); }
        .method-badge.post { background: var(--blue-bg); color: var(--blue); border: 1px solid rgba(59,130,246,0.2); }
        .method-badge.put { background: var(--yellow-bg); color: var(--yellow); border: 1px solid rgba(245,158,11,0.2); }
        .method-badge.patch { background: var(--orange-bg); color: var(--orange); border: 1px solid rgba(249,115,22,0.2); }
        .method-badge.delete { background: var(--red-bg); color: var(--red); border: 1px solid rgba(239,68,68,0.2); }

        .endpoint-path {
            font-family: var(--font-mono);
            font-size: 13px;
            color: var(--text-primary);
            font-weight: 500;
        }

        .endpoint-summary {
            margin-left: auto;
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 260px;
        }

        .endpoint-toggle {
            width: 20px;
            height: 20px;
            color: var(--text-muted);
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .endpoint-card.expanded .endpoint-toggle { transform: rotate(180deg); }

        .lock-icon {
            color: var(--yellow);
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .endpoint-body {
            display: none;
            border-top: 1px solid var(--border);
            padding: 24px;
            animation: slideDown 0.25s ease;
        }

        .endpoint-card.expanded .endpoint-body { display: block; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .endpoint-desc {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.7;
        }

        /* TABS */
        .tabs {
            display: flex;
            gap: 2px;
            background: var(--bg-tertiary);
            border-radius: var(--radius-sm);
            padding: 3px;
            margin-bottom: 16px;
        }

        .tab-btn {
            padding: 7px 16px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            font-family: var(--font-sans);
            cursor: pointer;
            border-radius: var(--radius-xs);
            transition: var(--transition);
        }

        .tab-btn:hover { color: var(--text-secondary); }
        .tab-btn.active { background: var(--bg-card); color: var(--text-primary); box-shadow: var(--shadow-sm); }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* PARAM TABLE */
        .param-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .param-table th {
            text-align: left;
            padding: 8px 12px;
            background: var(--bg-tertiary);
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }

        .param-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .param-table tr:last-child td { border-bottom: none; }

        .param-name {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--accent-hover);
            font-weight: 500;
        }

        .param-required {
            font-size: 9px;
            color: var(--red);
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 6px;
        }

        .param-optional {
            font-size: 9px;
            color: var(--text-muted);
            font-weight: 600;
            margin-left: 6px;
        }

        .param-type {
            font-family: var(--font-mono);
            font-size: 11px;
            color: var(--purple);
            background: var(--purple-bg);
            padding: 2px 6px;
            border-radius: 4px;
        }

        /* CODE BLOCK */
        .code-block-wrapper {
            position: relative;
            margin-bottom: 16px;
        }

        .code-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 16px;
            background: var(--bg-code);
            border: 1px solid var(--border);
            border-bottom: none;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .code-label .status-code {
            font-family: var(--font-mono);
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .status-200 { background: var(--green-bg); color: var(--green); }
        .status-201 { background: var(--green-bg); color: var(--green); }
        .status-401 { background: var(--red-bg); color: var(--red); }
        .status-404 { background: var(--red-bg); color: var(--red); }
        .status-422 { background: var(--yellow-bg); color: var(--yellow); }
        .status-500 { background: var(--red-bg); color: var(--red); }

        .code-block {
            background: var(--bg-code);
            border: 1px solid var(--border);
            border-radius: 0 0 var(--radius-sm) var(--radius-sm);
            padding: 16px;
            overflow-x: auto;
            font-family: var(--font-mono);
            font-size: 12px;
            line-height: 1.7;
            color: #e2e8f0;
            position: relative;
        }

        .code-block.standalone {
            border-radius: var(--radius-sm);
        }

        .copy-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 5px 10px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--radius-xs);
            color: var(--text-muted);
            font-size: 11px;
            font-family: var(--font-sans);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .copy-btn:hover { background: rgba(255,255,255,0.12); color: var(--text-secondary); }
        .copy-btn.copied { color: var(--green); border-color: rgba(34,197,94,0.3); }

        /* JSON SYNTAX HIGHLIGHTING */
        .json-key { color: #7dd3fc; }
        .json-string { color: #86efac; }
        .json-number { color: #fbbf24; }
        .json-boolean { color: #c084fc; }
        .json-null { color: #94a3b8; }
        .json-bracket { color: #94a3b8; }

        /* HEADER INFO */
        .header-info {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            font-size: 12px;
        }

        .header-info code {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--accent-hover);
        }

        .header-info .header-label {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* TRY API PANEL */
        .try-api-panel {
            margin-top: 20px;
            padding: 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
        }

        .try-api-panel h4 {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .try-input-group {
            margin-bottom: 10px;
        }

        .try-input-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .try-input-group input,
        .try-input-group textarea {
            width: 100%;
            padding: 8px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            color: var(--text-primary);
            font-family: var(--font-mono);
            font-size: 12px;
            outline: none;
            transition: var(--transition);
        }

        .try-input-group textarea { min-height: 100px; resize: vertical; }

        .try-input-group input:focus,
        .try-input-group textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .try-btn {
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            color: #fff;
            border: none;
            border-radius: var(--radius-xs);
            font-size: 12px;
            font-weight: 600;
            font-family: var(--font-sans);
            cursor: pointer;
            transition: var(--transition);
            margin-top: 4px;
        }

        .try-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
        .try-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .try-response {
            margin-top: 12px;
        }

        /* =========================================================
           RESPONSIVE
           ========================================================= */
        @media (max-width: 900px) {
            .app-layout { grid-template-columns: 1fr; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-content { margin-left: 0; padding: 72px 16px 40px; }
            .mobile-header { display: flex; }
            .hero { padding: 24px; }
            .hero h1 { font-size: 22px; }
            .endpoint-header { flex-wrap: wrap; gap: 8px; }
            .endpoint-summary { display: none; }
        }
    </style>
</head>
<body>

<!-- MOBILE HEADER -->
<div class="mobile-header" id="mobileHeader">
    <button class="hamburger" onclick="toggleSidebar()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <span style="font-weight:700;font-size:15px;">SIMINS API Docs</span>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="app-layout">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">SI</div>
                <div>
                    <div class="sidebar-title">SIMINS API</div>
                    <div class="sidebar-subtitle">v1.0 &bull; Laravel 12</div>
                </div>
            </div>
        </div>

        <div class="sidebar-search">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="search-input" id="searchInput" placeholder="Cari endpoint...">
            </div>
        </div>

        <div class="method-filters" id="methodFilters">
            <button class="method-filter-btn active" data-method="all">All</button>
            <button class="method-filter-btn" data-method="get">GET</button>
            <button class="method-filter-btn" data-method="post">POST</button>
            <button class="method-filter-btn" data-method="put">PUT</button>
            <button class="method-filter-btn" data-method="delete">DEL</button>
        </div>

        <nav class="sidebar-nav" id="sidebarNav">
            <!-- Populated by JS -->
        </nav>

        <div class="sidebar-footer">
            <span class="endpoint-count" id="endpointCount"></span>
            <button class="theme-toggle" onclick="toggleTheme()">
                <svg id="themeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <span id="themeLabel">Light</span>
            </button>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content" id="mainContent">
        <!-- Populated by JS -->
    </main>
</div>

<script>
// ====================================================================
// API DATA — Complete endpoint definitions
// ====================================================================
const API_DATA = [
    // ───────────────── AUTH ─────────────────
    {
        group: "Autentikasi",
        icon: "🔐",
        color: "#8b5cf6",
        description: "Endpoint untuk autentikasi pengguna menggunakan Laravel Sanctum.",
        endpoints: [
            {
                method: "POST", path: "/api/login", summary: "Login pengguna",
                desc: "Autentikasi pengguna menggunakan username & password. Mengembalikan Sanctum Bearer Token yang digunakan untuk mengakses seluruh endpoint yang memerlukan autentikasi.",
                auth: false,
                headers: [{ key: "Content-Type", value: "application/json" }],
                body: [
                    { name: "username", type: "string", required: true, desc: "Username pengguna" },
                    { name: "password", type: "string", required: true, desc: "Password pengguna" }
                ],
                request: { username: "admin", password: "password123" },
                response: {
                    status: 200, body: {
                        status: true, message: "Login berhasil.",
                        data: {
                            pengguna: { id_pengguna: 1, username: "admin", id_peran: 1, peran: { id_peran: 1, nama_peran: "Admin" }, kelas: null, mapel: null, unit: null },
                            access_token: "1|abc123xyz...",
                            token_type: "Bearer"
                        }
                    }
                },
                errors: [
                    { status: 401, body: { status: false, message: "Username atau password salah." } },
                    { status: 422, body: { status: false, message: "Validasi gagal.", errors: { username: ["Username wajib diisi."] } } }
                ]
            },
            {
                method: "POST", path: "/api/logout", summary: "Logout pengguna",
                desc: "Menghapus (revoke) token akses yang sedang digunakan oleh pengguna.",
                auth: true,
                request: null,
                response: { status: 200, body: { status: true, message: "Logout berhasil." } },
                errors: [{ status: 401, body: { message: "Unauthenticated." } }]
            },
            {
                method: "GET", path: "/api/me", summary: "Profil pengguna login",
                desc: "Mengembalikan data lengkap pengguna yang sedang login beserta relasinya (peran, daftar akses, kelas, mapel, unit).",
                auth: true,
                request: null,
                response: {
                    status: 200, body: {
                        status: true, message: "Data pengguna berhasil diambil.",
                        data: { id_pengguna: 1, username: "admin", id_peran: 1, peran: { id_peran: 1, nama_peran: "Admin", akses_list: [{ id_akses: 1, nama_modul: "pengguna", hak_baca: true, hak_buat: true, hak_ubah: true, hak_hapus: true }] } }
                    }
                },
                errors: [{ status: 401, body: { message: "Unauthenticated." } }]
            }
        ]
    },
    // ───────────────── PENGGUNA ─────────────────
    {
        group: "Pengguna",
        icon: "👤",
        color: "#6366f1",
        description: "Manajemen data pengguna sistem (Admin, Guru, Siswa, Staf).",
        endpoints: [
            {
                method: "GET", path: "/api/pengguna", summary: "Daftar semua pengguna", auth: true,
                desc: "Mengambil seluruh data pengguna beserta relasi peran, kelas, mapel, dan unit.",
                request: null,
                response: { status: 200, body: { status: true, message: "Daftar pengguna berhasil diambil.", data: [{ id_pengguna: 1, username: "admin", id_peran: 1, peran: { id_peran: 1, nama_peran: "Admin" } }] } },
                errors: [{ status: 401, body: { message: "Unauthenticated." } }]
            },
            {
                method: "POST", path: "/api/pengguna", summary: "Tambah pengguna baru", auth: true,
                desc: "Membuat pengguna baru. Username harus unik dan password minimal 8 karakter.",
                body: [
                    { name: "username", type: "string", required: true, desc: "Username unik, maks 100 karakter" },
                    { name: "password", type: "string", required: true, desc: "Password minimal 8 karakter" },
                    { name: "id_peran", type: "integer", required: true, desc: "ID peran (FK ke tabel peran)" },
                    { name: "id_kelas", type: "integer", required: false, desc: "ID kelas (opsional, untuk siswa)" },
                    { name: "id_mapel", type: "integer", required: false, desc: "ID mapel (opsional, untuk guru)" },
                    { name: "id_unit", type: "integer", required: false, desc: "ID unit (opsional)" }
                ],
                request: { username: "guru01", password: "secret1234", id_peran: 2, id_mapel: 3 },
                response: { status: 201, body: { status: true, message: "Pengguna berhasil ditambahkan.", data: { id_pengguna: 5, username: "guru01", id_peran: 2, peran: { id_peran: 2, nama_peran: "Guru" } } } },
                errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { username: ["Username sudah digunakan."] } } }]
            },
            {
                method: "GET", path: "/api/pengguna/{id}", summary: "Detail pengguna", auth: true,
                desc: "Mengambil detail satu pengguna berdasarkan ID, termasuk relasi lengkap.",
                params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pengguna" }],
                request: null,
                response: { status: 200, body: { status: true, message: "Detail pengguna berhasil diambil.", data: { id_pengguna: 1, username: "admin" } } },
                errors: [{ status: 404, body: { status: false, message: "Pengguna tidak ditemukan." } }]
            },
            {
                method: "PUT", path: "/api/pengguna/{id}", summary: "Update pengguna", auth: true,
                desc: "Memperbarui data pengguna. Jika password tidak diisi, password tetap tidak berubah.",
                params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pengguna" }],
                body: [
                    { name: "username", type: "string", required: true, desc: "Username unik" },
                    { name: "password", type: "string", required: false, desc: "Password baru (kosongkan jika tidak diubah)" },
                    { name: "id_peran", type: "integer", required: true, desc: "ID peran" },
                    { name: "id_kelas", type: "integer", required: false, desc: "ID kelas" },
                    { name: "id_mapel", type: "integer", required: false, desc: "ID mapel" },
                    { name: "id_unit", type: "integer", required: false, desc: "ID unit" }
                ],
                request: { username: "admin_updated", id_peran: 1 },
                response: { status: 200, body: { status: true, message: "Pengguna berhasil diperbarui.", data: { id_pengguna: 1, username: "admin_updated" } } },
                errors: [{ status: 404, body: { status: false, message: "Pengguna tidak ditemukan." } }]
            },
            {
                method: "DELETE", path: "/api/pengguna/{id}", summary: "Hapus pengguna", auth: true,
                desc: "Menghapus data pengguna secara permanen dari sistem.",
                params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pengguna" }],
                request: null,
                response: { status: 200, body: { status: true, message: "Pengguna berhasil dihapus." } },
                errors: [{ status: 404, body: { status: false, message: "Pengguna tidak ditemukan." } }]
            }
        ]
    },
    // ───────────────── PERAN ─────────────────
    {
        group: "Peran",
        icon: "🛡️",
        color: "#a855f7",
        description: "Manajemen peran (role) termasuk sinkronisasi hak akses.",
        endpoints: [
            { method: "GET", path: "/api/peran", summary: "Daftar peran", auth: true, desc: "Mengambil seluruh data peran beserta daftar aksesnya.", request: null, response: { status: 200, body: { status: true, message: "Daftar peran berhasil diambil.", data: [{ id_peran: 1, nama_peran: "Admin", akses_list: [] }] } }, errors: [] },
            { method: "POST", path: "/api/peran", summary: "Tambah peran", auth: true, desc: "Membuat peran baru.",
              body: [{ name: "nama_peran", type: "string", required: true, desc: "Nama peran unik" }],
              request: { nama_peran: "Kepala Sekolah" },
              response: { status: 201, body: { status: true, message: "Peran berhasil ditambahkan.", data: { id_peran: 3, nama_peran: "Kepala Sekolah" } } }, errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { nama_peran: ["Nama peran sudah ada."] } } }] },
            { method: "GET", path: "/api/peran/{id}", summary: "Detail peran", auth: true, desc: "Mengambil detail satu peran beserta daftar aksesnya.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID peran" }], request: null, response: { status: 200, body: { status: true, message: "Detail peran berhasil diambil.", data: { id_peran: 1, nama_peran: "Admin" } } }, errors: [{ status: 404, body: { status: false, message: "Peran tidak ditemukan." } }] },
            { method: "PUT", path: "/api/peran/{id}", summary: "Update peran", auth: true, desc: "Memperbarui data peran.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID peran" }], body: [{ name: "nama_peran", type: "string", required: true, desc: "Nama peran" }], request: { nama_peran: "Super Admin" }, response: { status: 200, body: { status: true, message: "Peran berhasil diperbarui.", data: { id_peran: 1, nama_peran: "Super Admin" } } }, errors: [] },
            { method: "DELETE", path: "/api/peran/{id}", summary: "Hapus peran", auth: true, desc: "Menghapus peran dari sistem.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID peran" }], request: null, response: { status: 200, body: { status: true, message: "Peran berhasil dihapus." } }, errors: [] },
            { method: "POST", path: "/api/peran/{id}/sync-akses", summary: "Sinkronisasi hak akses", auth: true,
              desc: "Menyinkronisasi daftar hak akses ke sebuah peran. Semua akses lama akan di-replace dengan yang baru.",
              params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID peran" }],
              body: [{ name: "id_akses", type: "array", required: true, desc: "Array ID akses yang di-assign ke peran" }],
              request: { id_akses: [1, 2, 3, 5] },
              response: { status: 200, body: { status: true, message: "Hak akses peran berhasil disinkronisasi.", data: { id_peran: 1, nama_peran: "Admin", akses_list: [] } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { "id_akses.0": ["ID akses tidak ditemukan."] } } }] }
        ]
    },
    // ───────────────── AKSES MODUL ─────────────────
    {
        group: "Akses Modul",
        icon: "🔑",
        color: "#8b5cf6",
        description: "Manajemen hak akses modul (RBAC). Setiap modul punya hak buat, baca, ubah, hapus.",
        endpoints: [
            { method: "GET", path: "/api/akses", summary: "Daftar akses modul", auth: true, desc: "Mengambil seluruh data modul akses.", request: null, response: { status: 200, body: { status: true, message: "Daftar akses berhasil diambil.", data: [{ id_akses: 1, nama_modul: "pengguna", hak_buat: true, hak_baca: true, hak_ubah: true, hak_hapus: true }] } }, errors: [] },
            { method: "POST", path: "/api/akses", summary: "Tambah akses modul", auth: true, desc: "Mendaftarkan modul baru ke sistem akses.",
              body: [
                  { name: "nama_modul", type: "string", required: true, desc: "Nama modul (unik)" },
                  { name: "hak_buat", type: "boolean", required: true, desc: "Hak create" },
                  { name: "hak_baca", type: "boolean", required: true, desc: "Hak read" },
                  { name: "hak_ubah", type: "boolean", required: true, desc: "Hak update" },
                  { name: "hak_hapus", type: "boolean", required: true, desc: "Hak delete" }
              ],
              request: { nama_modul: "mutasi", hak_buat: true, hak_baca: true, hak_ubah: true, hak_hapus: true },
              response: { status: 201, body: { status: true, message: "Akses berhasil ditambahkan.", data: { id_akses: 10, nama_modul: "mutasi" } } }, errors: [] },
            { method: "GET", path: "/api/akses/{id}", summary: "Detail akses", auth: true, desc: "Mengambil detail satu modul akses.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID akses" }], request: null, response: { status: 200, body: { status: true, message: "Detail akses berhasil diambil.", data: { id_akses: 1, nama_modul: "pengguna", hak_buat: true, hak_baca: true, hak_ubah: true, hak_hapus: true } } }, errors: [{ status: 404, body: { status: false, message: "Akses tidak ditemukan." } }] },
            { method: "PUT", path: "/api/akses/{id}", summary: "Update akses", auth: true, desc: "Memperbarui data modul akses.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID akses" }],
              body: [
                  { name: "nama_modul", type: "string", required: true, desc: "Nama modul" },
                  { name: "hak_buat", type: "boolean", required: true, desc: "Hak create" },
                  { name: "hak_baca", type: "boolean", required: true, desc: "Hak read" },
                  { name: "hak_ubah", type: "boolean", required: true, desc: "Hak update" },
                  { name: "hak_hapus", type: "boolean", required: true, desc: "Hak delete" }
              ],
              request: { nama_modul: "pengguna", hak_buat: true, hak_baca: true, hak_ubah: true, hak_hapus: false },
              response: { status: 200, body: { status: true, message: "Akses berhasil diperbarui.", data: { id_akses: 1, nama_modul: "pengguna" } } }, errors: [] },
            { method: "DELETE", path: "/api/akses/{id}", summary: "Hapus akses", auth: true, desc: "Menghapus modul akses dari sistem.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID akses" }], request: null, response: { status: 200, body: { status: true, message: "Akses berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Akses tidak ditemukan." } }] }
        ]
    },
    // ───────────────── MASTER SEKOLAH ─────────────────
    {
        group: "Master Sekolah",
        icon: "🏫",
        color: "#06b6d4",
        description: "Master data sekolah: Jurusan, Rombel, Kelas, Mata Pelajaran, Unit.",
        endpoints: [
            // JURUSAN
            { method: "GET", path: "/api/jurusan", summary: "Daftar jurusan", auth: true, desc: "Mengambil semua data jurusan.", request: null, response: { status: 200, body: { status: true, message: "Daftar jurusan berhasil diambil.", data: [{ id_jurusan: 1, nama_jurusan: "Teknik Komputer dan Jaringan" }] } }, errors: [] },
            { method: "POST", path: "/api/jurusan", summary: "Tambah jurusan", auth: true, desc: "Menambahkan jurusan baru.", body: [{ name: "nama_jurusan", type: "string", required: true, desc: "Nama jurusan (unik)" }], request: { nama_jurusan: "Rekayasa Perangkat Lunak" }, response: { status: 201, body: { status: true, message: "Jurusan berhasil ditambahkan.", data: { id_jurusan: 2, nama_jurusan: "Rekayasa Perangkat Lunak" } } }, errors: [] },
            { method: "GET", path: "/api/jurusan/{id}", summary: "Detail jurusan", auth: true, desc: "Mengambil detail satu jurusan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID jurusan" }], request: null, response: { status: 200, body: { status: true, message: "Detail jurusan berhasil diambil.", data: { id_jurusan: 1, nama_jurusan: "Teknik Komputer dan Jaringan" } } }, errors: [{ status: 404, body: { status: false, message: "Jurusan tidak ditemukan." } }] },
            { method: "PUT", path: "/api/jurusan/{id}", summary: "Update jurusan", auth: true, desc: "Memperbarui data jurusan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID jurusan" }], body: [{ name: "nama_jurusan", type: "string", required: true, desc: "Nama jurusan" }], request: { nama_jurusan: "TKJ Updated" }, response: { status: 200, body: { status: true, message: "Jurusan berhasil diperbarui.", data: { id_jurusan: 1, nama_jurusan: "TKJ Updated" } } }, errors: [] },
            { method: "DELETE", path: "/api/jurusan/{id}", summary: "Hapus jurusan", auth: true, desc: "Menghapus data jurusan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID jurusan" }], request: null, response: { status: 200, body: { status: true, message: "Jurusan berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Jurusan tidak ditemukan." } }] },
            // ROMBEL
            { method: "GET", path: "/api/rombel", summary: "Daftar rombel", auth: true, desc: "Mengambil semua data rombongan belajar.", request: null, response: { status: 200, body: { status: true, message: "Daftar rombel berhasil diambil.", data: [{ id_rombel: 1, nama_rombel: "X TKJ 1", id_jurusan: 1 }] } }, errors: [] },
            { method: "POST", path: "/api/rombel", summary: "Tambah rombel", auth: true, desc: "Menambahkan rombel baru.", body: [{ name: "nama_rombel", type: "string", required: true, desc: "Nama rombel" }, { name: "id_jurusan", type: "integer", required: true, desc: "ID jurusan" }], request: { nama_rombel: "X RPL 1", id_jurusan: 2 }, response: { status: 201, body: { status: true, message: "Rombel berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/rombel/{id}", summary: "Detail rombel", auth: true, desc: "Mengambil detail satu rombel beserta jurusannya.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID rombel" }], request: null, response: { status: 200, body: { status: true, message: "Detail rombel berhasil diambil.", data: { id_rombel: 1, nama_rombel: "X TKJ 1", jurusan: { nama_jurusan: "TKJ" } } } }, errors: [{ status: 404, body: { status: false, message: "Rombel tidak ditemukan." } }] },
            { method: "PUT", path: "/api/rombel/{id}", summary: "Update rombel", auth: true, desc: "Memperbarui data rombel.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID rombel" }], body: [{ name: "nama_rombel", type: "string", required: true, desc: "Nama rombel" }, { name: "id_jurusan", type: "integer", required: true, desc: "ID jurusan" }], request: { nama_rombel: "X TKJ 2", id_jurusan: 1 }, response: { status: 200, body: { status: true, message: "Rombel berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/rombel/{id}", summary: "Hapus rombel", auth: true, desc: "Menghapus data rombel.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID rombel" }], request: null, response: { status: 200, body: { status: true, message: "Rombel berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Rombel tidak ditemukan." } }] },
            // KELAS
            { method: "GET", path: "/api/kelas", summary: "Daftar kelas", auth: true, desc: "Mengambil semua data kelas.", request: null, response: { status: 200, body: { status: true, message: "Daftar kelas berhasil diambil.", data: [{ id_kelas: 1, nama_kelas: "X TKJ 1", id_rombel: 1 }] } }, errors: [] },
            { method: "POST", path: "/api/kelas", summary: "Tambah kelas", auth: true, desc: "Menambahkan kelas baru.", body: [{ name: "nama_kelas", type: "string", required: true, desc: "Nama kelas" }, { name: "id_rombel", type: "integer", required: true, desc: "ID rombel" }], request: { nama_kelas: "XI RPL 1", id_rombel: 2 }, response: { status: 201, body: { status: true, message: "Kelas berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/kelas/{id}", summary: "Detail kelas", auth: true, desc: "Mengambil detail satu kelas beserta rombel dan jurusannya.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kelas" }], request: null, response: { status: 200, body: { status: true, message: "Detail kelas berhasil diambil.", data: { id_kelas: 1, nama_kelas: "X TKJ 1", rombel: { nama_rombel: "X TKJ 1", jurusan: { nama_jurusan: "TKJ" } } } } }, errors: [{ status: 404, body: { status: false, message: "Kelas tidak ditemukan." } }] },
            { method: "PUT", path: "/api/kelas/{id}", summary: "Update kelas", auth: true, desc: "Memperbarui data kelas.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kelas" }], body: [{ name: "nama_kelas", type: "string", required: true, desc: "Nama kelas" }, { name: "id_rombel", type: "integer", required: true, desc: "ID rombel" }], request: { nama_kelas: "X TKJ 1 Updated", id_rombel: 1 }, response: { status: 200, body: { status: true, message: "Kelas berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/kelas/{id}", summary: "Hapus kelas", auth: true, desc: "Menghapus data kelas.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kelas" }], request: null, response: { status: 200, body: { status: true, message: "Kelas berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Kelas tidak ditemukan." } }] },
            // MAPEL
            { method: "GET", path: "/api/mapel", summary: "Daftar mapel", auth: true, desc: "Mengambil semua data mata pelajaran.", request: null, response: { status: 200, body: { status: true, message: "Daftar mapel berhasil diambil.", data: [{ id_mapel: 1, nama_mapel: "Matematika" }] } }, errors: [] },
            { method: "POST", path: "/api/mapel", summary: "Tambah mapel", auth: true, desc: "Menambahkan mata pelajaran baru.", body: [{ name: "nama_mapel", type: "string", required: true, desc: "Nama mapel (unik)" }], request: { nama_mapel: "Bahasa Inggris" }, response: { status: 201, body: { status: true, message: "Mapel berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/mapel/{id}", summary: "Detail mapel", auth: true, desc: "Mengambil detail satu mata pelajaran.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID mapel" }], request: null, response: { status: 200, body: { status: true, message: "Detail mapel berhasil diambil.", data: { id_mapel: 1, nama_mapel: "Matematika" } } }, errors: [{ status: 404, body: { status: false, message: "Mapel tidak ditemukan." } }] },
            { method: "PUT", path: "/api/mapel/{id}", summary: "Update mapel", auth: true, desc: "Memperbarui data mata pelajaran.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID mapel" }], body: [{ name: "nama_mapel", type: "string", required: true, desc: "Nama mapel" }], request: { nama_mapel: "Matematika Lanjutan" }, response: { status: 200, body: { status: true, message: "Mapel berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/mapel/{id}", summary: "Hapus mapel", auth: true, desc: "Menghapus data mata pelajaran.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID mapel" }], request: null, response: { status: 200, body: { status: true, message: "Mapel berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Mapel tidak ditemukan." } }] },
            // UNIT
            { method: "GET", path: "/api/unit", summary: "Daftar unit", auth: true, desc: "Mengambil semua data unit.", request: null, response: { status: 200, body: { status: true, message: "Daftar unit berhasil diambil.", data: [{ id_unit: 1, nama_unit: "Sarana Prasarana" }] } }, errors: [] },
            { method: "POST", path: "/api/unit", summary: "Tambah unit", auth: true, desc: "Menambahkan unit baru.", body: [{ name: "nama_unit", type: "string", required: true, desc: "Nama unit (unik)" }], request: { nama_unit: "Tata Usaha" }, response: { status: 201, body: { status: true, message: "Unit berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/unit/{id}", summary: "Detail unit", auth: true, desc: "Mengambil detail satu unit.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID unit" }], request: null, response: { status: 200, body: { status: true, message: "Detail unit berhasil diambil.", data: { id_unit: 1, nama_unit: "Sarana Prasarana" } } }, errors: [{ status: 404, body: { status: false, message: "Unit tidak ditemukan." } }] },
            { method: "PUT", path: "/api/unit/{id}", summary: "Update unit", auth: true, desc: "Memperbarui data unit.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID unit" }], body: [{ name: "nama_unit", type: "string", required: true, desc: "Nama unit" }], request: { nama_unit: "TU Updated" }, response: { status: 200, body: { status: true, message: "Unit berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/unit/{id}", summary: "Hapus unit", auth: true, desc: "Menghapus data unit.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID unit" }], request: null, response: { status: 200, body: { status: true, message: "Unit berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Unit tidak ditemukan." } }] }
        ]
    },
    // ───────────────── MASTER BARANG ─────────────────
    {
        group: "Master Barang",
        icon: "📦",
        color: "#f59e0b",
        description: "Master data barang: Kategori, Merek, Satuan, dan Master Barang.",
        endpoints: [
            // KATEGORI
            { method: "GET", path: "/api/kategori", summary: "Daftar kategori", auth: true, desc: "Mengambil semua data kategori barang.", request: null, response: { status: 200, body: { status: true, message: "Daftar kategori berhasil diambil.", data: [{ id_kategori: 1, nama_kategori: "Elektronik" }] } }, errors: [] },
            { method: "POST", path: "/api/kategori", summary: "Tambah kategori", auth: true, desc: "Menambahkan kategori barang baru.", body: [{ name: "nama_kategori", type: "string", required: true, desc: "Nama kategori (unik)" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan tambahan" }], request: { nama_kategori: "Furnitur", keterangan: "Barang mebel sekolah" }, response: { status: 201, body: { status: true, message: "Kategori berhasil ditambahkan.", data: { id_kategori: 2, nama_kategori: "Furnitur" } } }, errors: [] },
            { method: "GET", path: "/api/kategori/{id}", summary: "Detail kategori", auth: true, desc: "Mengambil detail satu kategori.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kategori" }], request: null, response: { status: 200, body: { status: true, message: "Detail kategori berhasil diambil.", data: { id_kategori: 1, nama_kategori: "Elektronik" } } }, errors: [{ status: 404, body: { status: false, message: "Kategori tidak ditemukan." } }] },
            { method: "PUT", path: "/api/kategori/{id}", summary: "Update kategori", auth: true, desc: "Memperbarui data kategori.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kategori" }], body: [{ name: "nama_kategori", type: "string", required: true, desc: "Nama kategori" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }], request: { nama_kategori: "Elektronik Updated" }, response: { status: 200, body: { status: true, message: "Kategori berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/kategori/{id}", summary: "Hapus kategori", auth: true, desc: "Menghapus data kategori.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kategori" }], request: null, response: { status: 200, body: { status: true, message: "Kategori berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Kategori tidak ditemukan." } }] },
            // MEREK
            { method: "GET", path: "/api/merek", summary: "Daftar merek", auth: true, desc: "Mengambil semua data merek barang.", request: null, response: { status: 200, body: { status: true, message: "Daftar merek berhasil diambil.", data: [{ id_merek: 1, nama_merek: "Lenovo" }] } }, errors: [] },
            { method: "POST", path: "/api/merek", summary: "Tambah merek", auth: true, desc: "Menambahkan merek barang baru.", body: [{ name: "nama_merek", type: "string", required: true, desc: "Nama merek (unik)" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }], request: { nama_merek: "Epson" }, response: { status: 201, body: { status: true, message: "Merek berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/merek/{id}", summary: "Detail merek", auth: true, desc: "Mengambil detail satu merek.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID merek" }], request: null, response: { status: 200, body: { status: true, message: "Detail merek berhasil diambil.", data: { id_merek: 1, nama_merek: "Lenovo" } } }, errors: [{ status: 404, body: { status: false, message: "Merek tidak ditemukan." } }] },
            { method: "PUT", path: "/api/merek/{id}", summary: "Update merek", auth: true, desc: "Memperbarui data merek.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID merek" }], body: [{ name: "nama_merek", type: "string", required: true, desc: "Nama merek" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }], request: { nama_merek: "Lenovo Updated" }, response: { status: 200, body: { status: true, message: "Merek berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/merek/{id}", summary: "Hapus merek", auth: true, desc: "Menghapus data merek.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID merek" }], request: null, response: { status: 200, body: { status: true, message: "Merek berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Merek tidak ditemukan." } }] },
            // SATUAN
            { method: "GET", path: "/api/satuan", summary: "Daftar satuan", auth: true, desc: "Mengambil semua data satuan.", request: null, response: { status: 200, body: { status: true, message: "Daftar satuan berhasil diambil.", data: [{ id_satuan: 1, nama_satuan: "Unit" }] } }, errors: [] },
            { method: "POST", path: "/api/satuan", summary: "Tambah satuan", auth: true, desc: "Menambahkan satuan baru.", body: [{ name: "nama_satuan", type: "string", required: true, desc: "Nama satuan (unik)" }], request: { nama_satuan: "Set" }, response: { status: 201, body: { status: true, message: "Satuan berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/satuan/{id}", summary: "Detail satuan", auth: true, desc: "Mengambil detail satu satuan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID satuan" }], request: null, response: { status: 200, body: { status: true, message: "Detail satuan berhasil diambil.", data: { id_satuan: 1, nama_satuan: "Unit" } } }, errors: [{ status: 404, body: { status: false, message: "Satuan tidak ditemukan." } }] },
            { method: "PUT", path: "/api/satuan/{id}", summary: "Update satuan", auth: true, desc: "Memperbarui data satuan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID satuan" }], body: [{ name: "nama_satuan", type: "string", required: true, desc: "Nama satuan" }], request: { nama_satuan: "Buah" }, response: { status: 200, body: { status: true, message: "Satuan berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/satuan/{id}", summary: "Hapus satuan", auth: true, desc: "Menghapus data satuan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID satuan" }], request: null, response: { status: 200, body: { status: true, message: "Satuan berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Satuan tidak ditemukan." } }] },
            // MASTER BARANG
            { method: "GET", path: "/api/master-barang", summary: "Daftar master barang", auth: true, desc: "Mengambil semua data master barang beserta kategori, merek, dan satuan.", request: null, response: { status: 200, body: { status: true, message: "Daftar master barang berhasil diambil.", data: [{ id_master_barang: 1, nama_barang: "Laptop Lenovo ThinkPad", kategori: { nama_kategori: "Elektronik" }, merek: { nama_merek: "Lenovo" }, satuan: { nama_satuan: "Unit" } }] } }, errors: [] },
            { method: "POST", path: "/api/master-barang", summary: "Tambah master barang", auth: true, desc: "Menambahkan master barang baru.",
              body: [
                  { name: "nama_barang", type: "string", required: true, desc: "Nama barang" },
                  { name: "id_kategori", type: "integer", required: true, desc: "ID kategori" },
                  { name: "id_merek", type: "integer", required: true, desc: "ID merek" },
                  { name: "id_satuan", type: "integer", required: true, desc: "ID satuan" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" }
              ],
              request: { nama_barang: "Proyektor Epson EB-X51", id_kategori: 1, id_merek: 2, id_satuan: 1 },
              response: { status: 201, body: { status: true, message: "Master barang berhasil ditambahkan.", data: { id_master_barang: 2, nama_barang: "Proyektor Epson EB-X51" } } }, errors: [] },
            { method: "GET", path: "/api/master-barang/{id}", summary: "Detail master barang", auth: true, desc: "Mengambil detail satu master barang beserta relasi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID master barang" }], request: null, response: { status: 200, body: { status: true, message: "Detail master barang berhasil diambil.", data: { id_master_barang: 1, nama_barang: "Laptop Lenovo ThinkPad", kategori: { nama_kategori: "Elektronik" }, merek: { nama_merek: "Lenovo" }, satuan: { nama_satuan: "Unit" } } } }, errors: [{ status: 404, body: { status: false, message: "Master barang tidak ditemukan." } }] },
            { method: "PUT", path: "/api/master-barang/{id}", summary: "Update master barang", auth: true, desc: "Memperbarui data master barang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID master barang" }],
              body: [
                  { name: "nama_barang", type: "string", required: true, desc: "Nama barang" },
                  { name: "id_kategori", type: "integer", required: true, desc: "ID kategori" },
                  { name: "id_merek", type: "integer", required: true, desc: "ID merek" },
                  { name: "id_satuan", type: "integer", required: true, desc: "ID satuan" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" }
              ],
              request: { nama_barang: "Laptop ThinkPad X1", id_kategori: 1, id_merek: 1, id_satuan: 1 },
              response: { status: 200, body: { status: true, message: "Master barang berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/master-barang/{id}", summary: "Hapus master barang", auth: true, desc: "Menghapus data master barang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID master barang" }], request: null, response: { status: 200, body: { status: true, message: "Master barang berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Master barang tidak ditemukan." } }] }
        ]
    },
    // ───────────────── MANAJEMEN ASET ─────────────────
    {
        group: "Manajemen Aset",
        icon: "🏷️",
        color: "#10b981",
        description: "Manajemen data aset: Lokasi, Ruang, dan Aset.",
        endpoints: [
            { method: "GET", path: "/api/lokasi", summary: "Daftar lokasi", auth: true, desc: "Mengambil semua data lokasi.", request: null, response: { status: 200, body: { status: true, message: "Daftar lokasi berhasil diambil.", data: [{ id_lokasi: 1, nama_lokasi: "Gedung A" }] } }, errors: [] },
            { method: "POST", path: "/api/lokasi", summary: "Tambah lokasi", auth: true, desc: "Menambahkan lokasi baru.", body: [{ name: "nama_lokasi", type: "string", required: true, desc: "Nama lokasi" }, { name: "alamat", type: "string", required: false, desc: "Alamat lokasi" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }], request: { nama_lokasi: "Gedung B", alamat: "Jl. Pendidikan No.1" }, response: { status: 201, body: { status: true, message: "Lokasi berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/lokasi/{id}", summary: "Detail lokasi", auth: true, desc: "Mengambil detail satu lokasi beserta daftar ruangnya.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lokasi" }], request: null, response: { status: 200, body: { status: true, message: "Detail lokasi berhasil diambil.", data: { id_lokasi: 1, nama_lokasi: "Gedung A", ruang: [{ id_ruang: 1, nama_ruang: "Lab Komputer 1" }] } } }, errors: [{ status: 404, body: { status: false, message: "Lokasi tidak ditemukan." } }] },
            { method: "PUT", path: "/api/lokasi/{id}", summary: "Update lokasi", auth: true, desc: "Memperbarui data lokasi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lokasi" }], body: [{ name: "nama_lokasi", type: "string", required: true, desc: "Nama lokasi" }, { name: "alamat", type: "string", required: false, desc: "Alamat" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }], request: { nama_lokasi: "Gedung A Updated" }, response: { status: 200, body: { status: true, message: "Lokasi berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/lokasi/{id}", summary: "Hapus lokasi", auth: true, desc: "Menghapus data lokasi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lokasi" }], request: null, response: { status: 200, body: { status: true, message: "Lokasi berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Lokasi tidak ditemukan." } }] },
            { method: "GET", path: "/api/ruang", summary: "Daftar ruang", auth: true, desc: "Mengambil semua data ruang beserta lokasinya.", request: null, response: { status: 200, body: { status: true, message: "Daftar ruang berhasil diambil.", data: [{ id_ruang: 1, nama_ruang: "Lab Komputer 1", lokasi: { nama_lokasi: "Gedung A" } }] } }, errors: [] },
            { method: "POST", path: "/api/ruang", summary: "Tambah ruang", auth: true, desc: "Menambahkan ruang baru.", body: [{ name: "nama_ruang", type: "string", required: true, desc: "Nama ruang" }, { name: "id_lokasi", type: "integer", required: true, desc: "ID lokasi" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }], request: { nama_ruang: "Lab Komputer 2", id_lokasi: 1 }, response: { status: 201, body: { status: true, message: "Ruang berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/ruang/{id}", summary: "Detail ruang", auth: true, desc: "Mengambil detail satu ruang beserta lokasinya.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID ruang" }], request: null, response: { status: 200, body: { status: true, message: "Detail ruang berhasil diambil.", data: { id_ruang: 1, nama_ruang: "Lab Komputer 1", lokasi: { nama_lokasi: "Gedung A" } } } }, errors: [{ status: 404, body: { status: false, message: "Ruang tidak ditemukan." } }] },
            { method: "PUT", path: "/api/ruang/{id}", summary: "Update ruang", auth: true, desc: "Memperbarui data ruang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID ruang" }], body: [{ name: "nama_ruang", type: "string", required: true, desc: "Nama ruang" }, { name: "id_lokasi", type: "integer", required: true, desc: "ID lokasi" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }], request: { nama_ruang: "Lab Komputer 1 Updated", id_lokasi: 1 }, response: { status: 200, body: { status: true, message: "Ruang berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/ruang/{id}", summary: "Hapus ruang", auth: true, desc: "Menghapus data ruang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID ruang" }], request: null, response: { status: 200, body: { status: true, message: "Ruang berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Ruang tidak ditemukan." } }] },
            { method: "GET", path: "/api/aset", summary: "Daftar aset", auth: true, desc: "Mengambil seluruh data aset beserta relasi master barang, jurusan, lokasi, ruang.", request: null,
              response: { status: 200, body: { status: true, message: "Daftar aset berhasil diambil.", data: [{ kode_inventaris: "INV-2026-001", id_master_barang: 1, master_barang: { nama_barang: "Laptop Lenovo ThinkPad" }, status: "Di Gudang" }] } }, errors: [] },
            { method: "POST", path: "/api/aset", summary: "Tambah aset", auth: true, desc: "Aset secara otomatis digenerate dari modul Pengadaan. Endpoint ini opsional jika ingin membuat manual.",
              body: [
                  { name: "kode_inventaris", type: "string", required: true, desc: "Kode inventaris unik, maks 50 karakter" },
                  { name: "id_master_barang", type: "integer", required: true, desc: "ID master barang" },
                  { name: "status", type: "string", required: true, desc: "Status aset (Di Gudang, Dipinjam, dsb)" }
              ],
              request: { kode_inventaris: "INV-2026-005", id_master_barang: 1, status: "Di Gudang" },
              response: { status: 201, body: { status: true, message: "Aset berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/aset/{id}", summary: "Detail aset", auth: true, desc: "Mengambil detail satu aset.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode inventaris" }], request: null, response: { status: 200, body: { status: true, message: "Detail aset berhasil diambil.", data: { kode_inventaris: "INV-2026-001", id_master_barang: 1, status: "Di Gudang" } } }, errors: [{ status: 404, body: { status: false, message: "Aset tidak ditemukan." } }] },
            { method: "PUT", path: "/api/aset/{id}", summary: "Update aset", auth: true, desc: "Memperbarui data aset.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode inventaris" }],
              body: [
                  { name: "id_ruang", type: "integer", required: false, desc: "ID ruang" },
                  { name: "id_lokasi", type: "integer", required: false, desc: "ID lokasi" },
                  { name: "no_seri", type: "string", required: false, desc: "Nomor Seri" }
              ],
              request: { id_ruang: 2 }, response: { status: 200, body: { status: true, message: "Aset berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/aset/{id}", summary: "Hapus aset", auth: true, desc: "Menghapus aset secara permanen.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode inventaris" }], request: null, response: { status: 200, body: { status: true, message: "Aset berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── KONDISI BARANG ─────────────────
    {
        group: "Kondisi Barang",
        icon: "🔧",
        color: "#f59e0b",
        description: "Master data kondisi barang. Digunakan sebagai referensi FK (id_kondisi) pada tabel aset.",
        endpoints: [
            { method: "GET", path: "/api/aset-kondisi", summary: "Daftar kondisi", auth: true, desc: "Mengambil semua data kondisi barang.", request: null, response: { status: 200, body: { status: true, message: "Daftar kondisi berhasil diambil.", data: [{ id_kondisi: 1, nama_kondisi: "Baik", keterangan: null }, { id_kondisi: 2, nama_kondisi: "Rusak Ringan", keterangan: null }, { id_kondisi: 3, nama_kondisi: "Rusak Berat", keterangan: null }] } }, errors: [] },
            { method: "POST", path: "/api/aset-kondisi", summary: "Tambah kondisi", auth: true, desc: "Menambahkan data kondisi baru.",
              body: [
                  { name: "nama_kondisi", type: "string", required: true, desc: "Nama kondisi (unik, maks 50 karakter)" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan tambahan" }
              ],
              request: { nama_kondisi: "Baik", keterangan: "Kondisi barang baik dan layak pakai" },
              response: { status: 201, body: { status: true, message: "Kondisi berhasil ditambahkan.", data: { id_kondisi: 1, nama_kondisi: "Baik", keterangan: "Kondisi barang baik dan layak pakai" } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { nama_kondisi: ["The nama kondisi has already been taken."] } } }] },
            { method: "GET", path: "/api/aset-kondisi/{id}", summary: "Detail kondisi", auth: true, desc: "Mengambil detail satu kondisi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kondisi" }], request: null, response: { status: 200, body: { status: true, message: "Detail kondisi berhasil diambil.", data: { id_kondisi: 1, nama_kondisi: "Baik", keterangan: null } } }, errors: [{ status: 404, body: { status: false, message: "Kondisi tidak ditemukan." } }] },
            { method: "PUT", path: "/api/aset-kondisi/{id}", summary: "Update kondisi", auth: true, desc: "Memperbarui data kondisi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kondisi" }],
              body: [
                  { name: "nama_kondisi", type: "string", required: false, desc: "Nama kondisi (unik)" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" }
              ],
              request: { nama_kondisi: "Rusak Ringan", keterangan: "Kerusakan minor, masih bisa digunakan" },
              response: { status: 200, body: { status: true, message: "Kondisi berhasil diperbarui.", data: { id_kondisi: 2, nama_kondisi: "Rusak Ringan", keterangan: "Kerusakan minor, masih bisa digunakan" } } },
              errors: [{ status: 404, body: { status: false, message: "Kondisi tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/aset-kondisi/{id}", summary: "Hapus kondisi", auth: true, desc: "Menghapus data kondisi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kondisi" }], request: null, response: { status: 200, body: { status: true, message: "Kondisi berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Kondisi tidak ditemukan." } }] }
        ]
    },
    // ───────────────── STATUS BARANG ─────────────────
    {
        group: "Status Barang",
        icon: "📊",
        color: "#3b82f6",
        description: "Master data status ketersediaan barang. Digunakan sebagai referensi FK (id_status) pada tabel aset.",
        endpoints: [
            { method: "GET", path: "/api/aset-status", summary: "Daftar status barang", auth: true, desc: "Mengambil semua data status barang.", request: null, response: { status: 200, body: { status: true, message: "Daftar status barang berhasil diambil.", data: [{ id_status: 1, nama_status: "Tersedia", keterangan: null }, { id_status: 2, nama_status: "Dipinjam", keterangan: null }, { id_status: 3, nama_status: "Non-Aktif", keterangan: null }] } }, errors: [] },
            { method: "POST", path: "/api/aset-status", summary: "Tambah status barang", auth: true, desc: "Menambahkan data status barang baru.",
              body: [
                  { name: "nama_status", type: "string", required: true, desc: "Nama status (unik, maks 50 karakter)" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan tambahan" }
              ],
              request: { nama_status: "Tersedia", keterangan: "Barang tersedia untuk dipinjam" },
              response: { status: 201, body: { status: true, message: "Status barang berhasil ditambahkan.", data: { id_status: 1, nama_status: "Tersedia", keterangan: "Barang tersedia untuk dipinjam" } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { nama_status: ["The nama status has already been taken."] } } }] },
            { method: "GET", path: "/api/aset-status/{id}", summary: "Detail status barang", auth: true, desc: "Mengambil detail satu status barang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID status barang" }], request: null, response: { status: 200, body: { status: true, message: "Detail status barang berhasil diambil.", data: { id_status: 1, nama_status: "Tersedia", keterangan: null } } }, errors: [{ status: 404, body: { status: false, message: "Status barang tidak ditemukan." } }] },
            { method: "PUT", path: "/api/aset-status/{id}", summary: "Update status barang", auth: true, desc: "Memperbarui data status barang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID status barang" }],
              body: [
                  { name: "nama_status", type: "string", required: false, desc: "Nama status (unik)" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" }
              ],
              request: { nama_status: "Dipinjam", keterangan: "Barang sedang dipinjam" },
              response: { status: 200, body: { status: true, message: "Status barang berhasil diperbarui.", data: { id_status: 2, nama_status: "Dipinjam", keterangan: "Barang sedang dipinjam" } } },
              errors: [{ status: 404, body: { status: false, message: "Status barang tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/aset-status/{id}", summary: "Hapus status barang", auth: true, desc: "Menghapus data status barang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID status barang" }], request: null, response: { status: 200, body: { status: true, message: "Status barang berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Status barang tidak ditemukan." } }] }
        ]
    },
    // ───────────────── PEMINJAMAN ─────────────────
    {
        group: "Peminjaman",
        icon: "🔄",
        color: "#3b82f6",
        description: "Transaksi peminjaman aset. Menggunakan DB::transaction untuk konsistensi data.",
        endpoints: [
            { method: "GET", path: "/api/peminjaman", summary: "Daftar peminjaman", auth: true, desc: "Mengambil seluruh data peminjaman beserta detail aset yang dipinjam.", request: null,
              response: { status: 200, body: { status: true, message: "Daftar peminjaman berhasil diambil.", data: [{ nomor_peminjaman: "PJM-2026-001", tanggal_pinjam: "2026-04-16", nama_peminjam: "Budi Santoso", nomor_telepon: "081234567890", lama_pinjam_hari: 3, keterangan: null, status_peminjaman: "Sedang Dipinjam", detail_peminjaman: [{ id_detail_pinjam: 1, nomor_peminjaman: "PJM-2026-001", kode_inventaris: "INV-2026-001", aset: { kode_inventaris: "INV-2026-001" } }] }] } }, errors: [] },
            { method: "POST", path: "/api/peminjaman", summary: "Buat peminjaman baru", auth: true,
              desc: "Menyimpan peminjaman baru menggunakan DB::transaction(). Insert header + detail multi-item.",
              body: [
                  { name: "nomor_peminjaman", type: "string", required: true, desc: "Nomor peminjaman unik (maks 50 karakter)" },
                  { name: "tanggal_pinjam", type: "date", required: true, desc: "Tanggal peminjaman (Y-m-d)" },
                  { name: "nama_peminjam", type: "string", required: true, desc: "Nama peminjam (maks 150 karakter)" },
                  { name: "nomor_telepon", type: "string", required: false, desc: "Nomor telepon (maks 20 karakter)" },
                  { name: "lama_pinjam_hari", type: "integer", required: true, desc: "Lama pinjam dalam hari (min: 1)" },
                  { name: "keterangan", type: "string", required: false, desc: "Catatan peminjaman" },
                  { name: "detail", type: "array", required: true, desc: "Array detail aset yang dipinjam" },
                  { name: "detail.*.kode_inventaris", type: "string", required: true, desc: "Kode inventaris aset" }
              ],
              request: { nomor_peminjaman: "PJM-2026-003", tanggal_pinjam: "2026-04-16", nama_peminjam: "Budi Santoso", nomor_telepon: "081234567890", lama_pinjam_hari: 5, keterangan: "Untuk kegiatan praktik", detail: [{ kode_inventaris: "INV-2026-001" }, { kode_inventaris: "INV-2026-002" }] },
              response: { status: 201, body: { status: true, message: "Peminjaman berhasil disimpan.", data: { nomor_peminjaman: "PJM-2026-003", status_peminjaman: "Sedang Dipinjam" } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { "detail.0.kode_inventaris": ["Aset tidak ditemukan."] } } }] },
            { method: "GET", path: "/api/peminjaman/{id}", summary: "Detail peminjaman", auth: true, desc: "Mengambil detail satu peminjaman.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Nomor peminjaman" }], request: null, response: { status: 200, body: { status: true, message: "Detail peminjaman berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Peminjaman tidak ditemukan." } }] },
            { method: "PUT", path: "/api/peminjaman/{id}/kembalikan", summary: "Proses pengembalian", auth: true,
              desc: "Memproses pengembalian peminjaman. Update status → 'Dikembalikan'.",
              params: [{ name: "id", in: "path", type: "string", required: true, desc: "Nomor peminjaman" }],
              request: null,
              response: { status: 200, body: { status: true, message: "Peminjaman berhasil dikembalikan.", data: { nomor_peminjaman: "PJM-2026-001", status_peminjaman: "Dikembalikan" } } },
              errors: [{ status: 422, body: { status: false, message: "Peminjaman ini sudah dikembalikan sebelumnya." } }] },
        ]
    },
    // ───────────────── PERMINTAAN ─────────────────
    {
        group: "Permintaan Barang",
        icon: "📋",
        color: "#f97316",
        description: "Transaksi permintaan pengadaan barang baru.",
        endpoints: [
            { method: "GET", path: "/api/permintaan", summary: "Daftar permintaan", auth: true, desc: "Mengambil seluruh data permintaan barang.", request: null, response: { status: 200, body: { status: true, message: "Daftar permintaan berhasil diambil.", data: [{ kode_permintaan: "PRM-DUMMY-1", tanggal_permintaan: "2026-04-16", id_pengguna: 5, status_persetujuan: "disetujui" }] } }, errors: [] },
            { method: "POST", path: "/api/permintaan", summary: "Buat permintaan baru", auth: true,
              desc: "Menyimpan permintaan barang baru. kode_permintaan, tanggal, dan pengguna digenerate otomatis.",
              body: [
                  { name: "id_master_barang", type: "integer", required: true, desc: "ID master barang yang diminta" },
                  { name: "jumlah_diminta", type: "integer", required: true, desc: "Jumlah (min: 1)" },
                  { name: "alasan_kebutuhan", type: "string", required: true, desc: "Alasan kebutuhan item" },
                  { name: "keterangan_keperluan", type: "string", required: false, desc: "Catatan keperluan permintaan (opsional)" }
              ],
              request: { id_master_barang: 1, jumlah_diminta: 5, alasan_kebutuhan: "Digunakan untuk praktikum semester baru", keterangan_keperluan: "Untuk laboratorium jaringan" },
              response: { status: 201, body: { status: true, message: "Permintaan berhasil disimpan." } }, errors: [] },
            { method: "GET", path: "/api/permintaan/{id}", summary: "Detail permintaan", auth: true, desc: "Mengambil detail satu permintaan.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode permintaan" }], request: null, response: { status: 200, body: { status: true, message: "Detail permintaan berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Permintaan tidak ditemukan." } }] },
            { method: "PUT", path: "/api/permintaan/{id}/keputusan", summary: "Setujui/tolak permintaan", auth: true,
              desc: "Menyetujui atau menolak permintaan. Hanya permintaan berstatus 'diproses' yang dapat diproses.",
              params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode permintaan" }],
              body: [
                  { name: "status_persetujuan", type: "string", required: true, desc: "diproses | disetujui | ditolak" },
                  { name: "alasan_disetujui", type: "string", required: false, desc: "Alasan keputusan" }
              ],
              request: { status_persetujuan: "disetujui", alasan_disetujui: "Disetujui untuk kebutuhan lab" },
              response: { status: 200, body: { status: true, message: "Permintaan berhasil diperbarui." } },
              errors: [{ status: 422, body: { status: false, message: "Permintaan ini sudah diproses sebelumnya." } }] },
        ]
    },
    // ───────────────── MUTASI ASET ─────────────────
    {
        group: "Mutasi Aset",
        icon: "🔀",
        color: "#06b6d4",
        description: "Pencatatan perpindahan kepemilikan aset antar jurusan.",
        endpoints: [
            { method: "GET", path: "/api/mutasi", summary: "Daftar mutasi", auth: true, desc: "Mengambil seluruh riwayat mutasi aset beserta jurusan asal, dan jurusan tujuan.", request: null,
              response: { status: 200, body: { status: true, message: "Daftar mutasi aset berhasil diambil.", data: [{ id_mutasi: 1, jurusan_asal: { nama_jurusan: "TKJ" }, jurusan_tujuan: { nama_jurusan: "RPL" }, tanggal_mutasi: "2026-04-16", alasan_mutasi: "Pemindahan", jumlah_mutasi: 1 }] } }, errors: [] },
            { method: "POST", path: "/api/mutasi", summary: "Buat mutasi aset", auth: true,
              desc: "Memindahkan aset ke jurusan baru secara massal.",
              body: [
                  { name: "id_jurusan_asal", type: "integer", required: false, desc: "ID jurusan asal" },
                  { name: "id_jurusan_tujuan", type: "integer", required: true, desc: "ID jurusan tujuan baru" },
                  { name: "tanggal_mutasi", type: "date", required: true, desc: "Tanggal mutasi (Y-m-d)" },
                  { name: "jumlah_mutasi", type: "integer", required: true, desc: "Jumlah aset yang dimutasi" },
                  { name: "alasan_mutasi", type: "string", required: false, desc: "Alasan/catatan mutasi" }
              ],
              request: { id_jurusan_asal: 1, id_jurusan_tujuan: 2, tanggal_mutasi: "2026-04-16", jumlah_mutasi: 2, alasan_mutasi: "Pemindahan ke RPL" },
              response: { status: 201, body: { status: true, message: "Mutasi aset berhasil disimpan.", data: { id_mutasi: 1, id_jurusan_asal: 1, id_jurusan_tujuan: 2, tanggal_mutasi: "2026-04-16", jumlah_mutasi: 2 } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal." } }] },
            { method: "GET", path: "/api/mutasi/{id}", summary: "Detail mutasi", auth: true, desc: "Mengambil detail satu mutasi aset.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID mutasi" }], request: null, response: { status: 200, body: { status: true, message: "Detail mutasi berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Mutasi tidak ditemukan." } }] },
        ]
    },
    // ───────────────── KERUSAKAN ─────────────────
    {
        group: "Kerusakan",
        icon: "⚠️",
        color: "#ef4444",
        description: "Pencatatan laporan kerusakan aset. Status dan kondisi aset otomatis diupdate menjadi 'Rusak'.",
        endpoints: [
            { method: "GET", path: "/api/kerusakan", summary: "Daftar kerusakan", auth: true, desc: "Mengambil seluruh laporan kerusakan beserta relasi aset, pelapor, dan riwayat perbaikan.", request: null,
              response: { status: 200, body: { status: true, message: "Daftar kerusakan berhasil diambil.", data: [{ id_kerusakan: 1, kode_barang: "ASET-DUMMY-1", aset: { kode_barang: "ASET-DUMMY-1" }, tanggal_lapor: "2026-04-16", deskripsi_kerusakan: "Layar berkedip", tingkat_kerusakan: "Ringan", status_kerusakan: "Menunggu Pemeriksaan", pelapor: { username: "admin" } }] } }, errors: [] },
            { method: "POST", path: "/api/kerusakan", summary: "Lapor kerusakan", auth: true,
              desc: "Melaporkan kerusakan aset menggunakan DB::transaction(). Proses: (1) Insert laporan kerusakan, (2) Update kondisi_barang & status_ketersediaan aset. id_pelapor otomatis dari user login. Aset tidak boleh berstatus 'Dihapus'.",
              body: [
                  { name: "kode_barang", type: "string", required: true, desc: "Kode barang aset yang rusak" },
                  { name: "tanggal_lapor", type: "date", required: true, desc: "Tanggal pelaporan kerusakan" },
                  { name: "deskripsi_kerusakan", type: "string", required: true, desc: "Deskripsi detail kerusakan" },
                  { name: "tingkat_kerusakan", type: "string", required: true, desc: "Tingkat: Ringan | Sedang | Berat" }
              ],
              request: { kode_barang: "ASET-DUMMY-1", tanggal_lapor: "2026-04-16", deskripsi_kerusakan: "Layar monitor berkedip-kedip", tingkat_kerusakan: "Ringan" },
              response: { status: 201, body: { status: true, message: "Laporan kerusakan berhasil disimpan.", data: { id_kerusakan: 1, kode_barang: "ASET-DUMMY-1", status_kerusakan: "Menunggu Pemeriksaan" } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { kode_barang: ["Aset 'ASET-DUMMY-1' sudah dihapus dan tidak bisa dilaporkan rusak."] } } }] },
            { method: "GET", path: "/api/kerusakan/{id}", summary: "Detail kerusakan", auth: true, desc: "Mengambil detail satu kerusakan termasuk riwayat perbaikan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kerusakan" }], request: null, response: { status: 200, body: { status: true, message: "Detail kerusakan berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Kerusakan tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/kerusakan/{id}", summary: "Hapus kerusakan", auth: true, desc: "Menghapus data kerusakan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID kerusakan" }], request: null, response: { status: 200, body: { status: true, message: "Data kerusakan berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── PERBAIKAN ─────────────────
    {
        group: "Perbaikan",
        icon: "🔧",
        color: "#f59e0b",
        description: "Pencatatan perbaikan/servis aset yang rusak. Berelasi langsung dengan data kerusakan (id_kerusakan).",
        endpoints: [
            { method: "GET", path: "/api/perbaikan", summary: "Daftar perbaikan", auth: true, desc: "Mengambil seluruh data perbaikan beserta relasi kerusakan dan aset.", request: null,
              response: { status: 200, body: { status: true, message: "Daftar perbaikan berhasil diambil.", data: [{ id_perbaikan: 1, id_kerusakan: 1, tanggal_perbaikan: "2026-04-17", teknisi: "CV Teknik Jaya", biaya_perbaikan: 500000, tindakan_perbaikan: "Ganti layar LCD" }] } }, errors: [] },
            { method: "POST", path: "/api/perbaikan", summary: "Catat perbaikan", auth: true,
              desc: "Menyimpan data perbaikan menggunakan DB::transaction(). Proses: (1) Insert data perbaikan, (2) Update status_kerusakan → 'Sedang Diperbaiki'. Kerusakan yang sudah 'Selesai' tidak bisa diperbaiki lagi.",
              body: [
                  { name: "id_kerusakan", type: "integer", required: true, desc: "ID kerusakan yang diperbaiki" },
                  { name: "tanggal_perbaikan", type: "date", required: true, desc: "Tanggal perbaikan" },
                  { name: "teknisi", type: "string", required: false, desc: "Nama teknisi (maks 150 karakter)" },
                  { name: "biaya_perbaikan", type: "numeric", required: false, desc: "Biaya perbaikan (min: 0, default: 0)" },
                  { name: "tindakan_perbaikan", type: "string", required: true, desc: "Deskripsi tindakan perbaikan yang dilakukan" }
              ],
              request: { id_kerusakan: 1, tanggal_perbaikan: "2026-04-17", teknisi: "CV Teknik Jaya", biaya_perbaikan: 500000, tindakan_perbaikan: "Ganti layar LCD" },
              response: { status: 201, body: { status: true, message: "Data perbaikan berhasil disimpan.", data: { id_perbaikan: 1, id_kerusakan: 1, tindakan_perbaikan: "Ganti layar LCD" } } },
              errors: [
                  { status: 422, body: { status: false, message: "Validasi gagal.", errors: { id_kerusakan: ["Kerusakan ini sudah selesai diperbaiki."] } } }
              ] },
            { method: "GET", path: "/api/perbaikan/{id}", summary: "Detail perbaikan", auth: true, desc: "Mengambil detail satu perbaikan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID perbaikan" }], request: null, response: { status: 200, body: { status: true, message: "Detail perbaikan berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Perbaikan tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/perbaikan/{id}", summary: "Hapus perbaikan", auth: true, desc: "Menghapus data perbaikan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID perbaikan" }], request: null, response: { status: 200, body: { status: true, message: "Data perbaikan berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── PENGHAPUSAN ASET ─────────────────
    {
        group: "Penghapusan Aset",
        icon: "🗑️",
        color: "#dc2626",
        description: "Pencatatan penghapusan/pemusnahan aset dari sistem inventaris. Aset yang dihapus tidak muncul di daftar aktif.",
        endpoints: [
            { method: "GET", path: "/api/penghapusan-aset", summary: "Daftar penghapusan", auth: true, desc: "Mengambil seluruh data penghapusan aset beserta relasi aset dan penyetuju.", request: null,
              response: { status: 200, body: { status: true, message: "Daftar penghapusan aset berhasil diambil.", data: [{ id_penghapusan: 1, kode_barang: "ASET-DUMMY-1", aset: { kode_barang: "ASET-DUMMY-1" }, tanggal_hapus: "2026-04-16", alasan_hapus: "Sudah tidak layak pakai", penyetuju: { username: "admin" } }] } }, errors: [] },
            { method: "POST", path: "/api/penghapusan-aset", summary: "Hapuskan aset", auth: true,
              desc: "Menghapuskan aset dari sistem menggunakan DB::transaction(). Proses: (1) Insert record penghapusan, (2) Update status_ketersediaan aset menjadi 'Dihapus'. Aset tidak boleh berstatus 'Dihapus' atau 'Dipinjam'.",
              body: [
                  { name: "kode_barang", type: "string", required: true, desc: "Kode barang aset yang akan dihapuskan" },
                  { name: "tanggal_hapus", type: "date", required: true, desc: "Tanggal penghapusan" },
                  { name: "alasan_hapus", type: "string", required: true, desc: "Alasan penghapusan" },
                  { name: "id_penyetuju", type: "integer", required: true, desc: "ID pengguna yang menyetuju" }
              ],
              request: { kode_barang: "ASET-DUMMY-1", tanggal_hapus: "2026-04-16", alasan_hapus: "Rusak berat, tidak ekonomis diperbaiki", id_penyetuju: 1 },
              response: { status: 201, body: { status: true, message: "Penghapusan aset berhasil disimpan.", data: { id_penghapusan: 1, kode_barang: "ASET-DUMMY-1", tanggal_hapus: "2026-04-16" } } },
              errors: [
                  { status: 422, body: { status: false, message: "Validasi gagal.", errors: { kode_barang: ["Aset 'ASET-DUMMY-1' sedang dipinjam dan tidak dapat dihapuskan."] } } }
              ] },
            { method: "GET", path: "/api/penghapusan-aset/{id}", summary: "Detail penghapusan", auth: true, desc: "Mengambil detail satu penghapusan aset.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID penghapusan" }], request: null, response: { status: 200, body: { status: true, message: "Detail penghapusan aset berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Data penghapusan aset tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/penghapusan-aset/{id}", summary: "Hapus record penghapusan", auth: true, desc: "Menghapus data record penghapusan aset.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID penghapusan" }], request: null, response: { status: 200, body: { status: true, message: "Data penghapusan aset berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── OPNAME ASET ─────────────────
    {
        group: "Opname Aset",
        icon: "📋",
        color: "#10b981",
        description: "Manajemen opname aset (pencatatan kondisi aktual aset).",
        endpoints: [
            { method: "GET", path: "/api/opname-aset", summary: "Daftar opname aset", auth: true, desc: "Mengambil semua data opname aset.", request: null,
              response: { status: 200, body: { status: true, message: "Daftar opname aset berhasil diambil.", data: [{ id_opname_aset: 1, kode_inventaris: "INV-2026-001", tanggal_opname: "2026-04-27", id_kondisi: 1, keterangan: null }] } }, errors: [] },
            { method: "POST", path: "/api/opname-aset", summary: "Tambah opname aset", auth: true, desc: "Menambahkan data opname aset baru.",
              body: [
                  { name: "kode_inventaris", type: "string", required: true, desc: "Kode inventaris (FK ke pengadaan)" },
                  { name: "tanggal_opname", type: "date", required: true, desc: "Tanggal pelaksanaan opname (Y-m-d)" },
                  { name: "id_kondisi", type: "integer", required: false, desc: "ID kondisi (FK ke tabel kondisi)" },
                  { name: "keterangan", type: "string", required: false, desc: "Catatan tambahan" }
              ],
              request: { kode_inventaris: "INV-2026-001", tanggal_opname: "2026-04-27", id_kondisi: 1, keterangan: "Sesuai data" },
              response: { status: 201, body: { status: true, message: "Opname aset berhasil ditambahkan." } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal." } }] },
            { method: "GET", path: "/api/opname-aset/{id}", summary: "Detail opname aset", auth: true, desc: "Mengambil detail satu data opname aset.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID opname aset" }], request: null, response: { status: 200, body: { status: true, message: "Detail opname aset berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Opname aset tidak ditemukan." } }] },
            { method: "PUT", path: "/api/opname-aset/{id}", summary: "Update opname aset", auth: true, desc: "Memperbarui data opname aset.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID opname aset" }],
              body: [
                  { name: "kode_inventaris", type: "string", required: false, desc: "Kode inventaris" },
                  { name: "tanggal_opname", type: "date", required: false, desc: "Tanggal opname" },
                  { name: "id_kondisi", type: "integer", required: false, desc: "ID kondisi" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" }
              ],
              request: { id_kondisi: 2, keterangan: "Ada lecet sedikit" },
              response: { status: 200, body: { status: true, message: "Opname aset berhasil diperbarui." } }, errors: [{ status: 404, body: { status: false, message: "Opname aset tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/opname-aset/{id}", summary: "Hapus opname aset", auth: true, desc: "Menghapus data opname aset.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID opname aset" }], request: null, response: { status: 200, body: { status: true, message: "Opname aset berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Opname aset tidak ditemukan." } }] }
        ]
    },
    // ───────────────── TAHUN AJARAN ─────────────────
    {
        group: "Tahun Ajaran",
        icon: "📅",
        color: "#3b82f6",
        description: "Manajemen tahun ajaran sekolah.",
        endpoints: [
            { method: "GET", path: "/api/tahun-ajaran", summary: "Daftar tahun ajaran", auth: true, desc: "Mengambil semua data tahun ajaran.", request: null, response: { status: 200, body: { status: true, message: "Daftar tahun ajaran berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/tahun-ajaran", summary: "Tambah", auth: true, desc: "Menambahkan tahun ajaran baru.", body: [ { name: "tahun_ajaran", type: "string", required: true, desc: "Contoh: 2026/2027" }, { name: "semester", type: "integer", required: true, desc: "Semester (1 atau 2)" }, { name: "tanggal_mulai", type: "date", required: true, desc: "Tanggal mulai" }, { name: "tanggal_selesai", type: "date", required: true, desc: "Tanggal selesai" }, { name: "is_active", type: "boolean", required: false, desc: "Status aktif" } ], request: { tahun_ajaran: "2026/2027", semester: 1, tanggal_mulai: "2026-07-01", tanggal_selesai: "2026-12-31", is_active: true }, response: { status: 201, body: { status: true, message: "Tahun ajaran berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/tahun-ajaran/{id}", summary: "Detail", auth: true, desc: "Detail tahun ajaran.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID tahun ajaran" }], request: null, response: { status: 200, body: { status: true, message: "Detail tahun ajaran berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Tahun ajaran tidak ditemukan." } }] },
            { method: "PUT", path: "/api/tahun-ajaran/{id}", summary: "Update", auth: true, desc: "Update tahun ajaran.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID tahun ajaran" }], body: [ { name: "tahun_ajaran", type: "string", required: false, desc: "Tahun ajaran" } ], request: { tahun_ajaran: "2026/2027" }, response: { status: 200, body: { status: true, message: "Tahun ajaran berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/tahun-ajaran/{id}", summary: "Hapus", auth: true, desc: "Hapus tahun ajaran.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID tahun ajaran" }], request: null, response: { status: 200, body: { status: true, message: "Tahun ajaran berhasil dihapus." } }, errors: [] },
            { method: "POST", path: "/api/tahun-ajaran/{id}/set-active", summary: "Set Aktif", auth: true, desc: "Mengaktifkan tahun ajaran dan menonaktifkan yang lainnya.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID tahun ajaran" }], request: null, response: { status: 200, body: { status: true, message: "Tahun ajaran berhasil diaktifkan." } }, errors: [] }
        ]
    },
    // ───────────────── ROMBEL ─────────────────
    {
        group: "Rombel",
        icon: "👥",
        color: "#10b981",
        description: "Manajemen rombongan belajar.",
        endpoints: [
            { method: "GET", path: "/api/rombel", summary: "Daftar rombel", auth: true, desc: "Mengambil semua data rombel.", request: null, response: { status: 200, body: { status: true, message: "Daftar rombel berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/rombel", summary: "Tambah", auth: true, desc: "Menambahkan rombel baru.", body: [ { name: "kode_rombel", type: "string", required: true, desc: "Kode rombel" }, { name: "nama", type: "string", required: true, desc: "Nama rombel" }, { name: "tingkat", type: "integer", required: true, desc: "Tingkat kelas" }, { name: "is_active", type: "boolean", required: false, desc: "Status aktif" }, { name: "id_jurusan", type: "integer", required: false, desc: "ID jurusan" } ], request: { kode_rombel: "RMB-01", nama: "X RPL 1", tingkat: 10, is_active: true }, response: { status: 201, body: { status: true, message: "Rombel berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/rombel/{id}", summary: "Detail", auth: true, desc: "Detail rombel.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID rombel" }], request: null, response: { status: 200, body: { status: true, message: "Detail rombel berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Rombel tidak ditemukan." } }] },
            { method: "PUT", path: "/api/rombel/{id}", summary: "Update", auth: true, desc: "Update rombel.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID rombel" }], body: [ { name: "nama", type: "string", required: false, desc: "Nama" } ], request: { nama: "X RPL 1" }, response: { status: 200, body: { status: true, message: "Rombel berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/rombel/{id}", summary: "Hapus", auth: true, desc: "Hapus rombel.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID rombel" }], request: null, response: { status: 200, body: { status: true, message: "Rombel berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── RUANG ─────────────────
    {
        group: "Ruang",
        icon: "🚪",
        color: "#f59e0b",
        description: "Manajemen ruang sekolah.",
        endpoints: [
            { method: "GET", path: "/api/ruang", summary: "Daftar ruang", auth: true, desc: "Mengambil semua data ruang.", request: null, response: { status: 200, body: { status: true, message: "Daftar ruang berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/ruang", summary: "Tambah", auth: true, desc: "Menambahkan ruang baru.", body: [ { name: "kode_ruang", type: "string", required: true, desc: "Kode ruang" }, { name: "nama_ruang", type: "string", required: true, desc: "Nama ruang" }, { name: "lantai", type: "integer", required: false, desc: "Lantai" }, { name: "is_active", type: "boolean", required: false, desc: "Status aktif" }, { name: "id_lokasi", type: "integer", required: true, desc: "ID lokasi" } ], request: { kode_ruang: "R-01", nama_ruang: "Lab Komputer", lantai: 1, is_active: true, id_lokasi: 1 }, response: { status: 201, body: { status: true, message: "Ruang berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/ruang/{id}", summary: "Detail", auth: true, desc: "Detail ruang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID ruang" }], request: null, response: { status: 200, body: { status: true, message: "Detail ruang berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Ruang tidak ditemukan." } }] },
            { method: "PUT", path: "/api/ruang/{id}", summary: "Update", auth: true, desc: "Update ruang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID ruang" }], body: [ { name: "nama_ruang", type: "string", required: false, desc: "Nama ruang" } ], request: { nama_ruang: "Lab Komputer 1" }, response: { status: 200, body: { status: true, message: "Ruang berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/ruang/{id}", summary: "Hapus", auth: true, desc: "Hapus ruang.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID ruang" }], request: null, response: { status: 200, body: { status: true, message: "Ruang berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── LOKASI ─────────────────
    {
        group: "Lokasi",
        icon: "📍",
        color: "#ec4899",
        description: "Manajemen lokasi aset.",
        endpoints: [
            { method: "GET", path: "/api/lokasi", summary: "Daftar lokasi", auth: true, desc: "Mengambil semua data lokasi.", request: null, response: { status: 200, body: { status: true, message: "Daftar lokasi berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/lokasi", summary: "Tambah", auth: true, desc: "Menambahkan lokasi baru.", body: [ { name: "kode_lokasi", type: "string", required: true, desc: "Kode lokasi" }, { name: "nama_lokasi", type: "string", required: true, desc: "Nama lokasi" }, { name: "alamat", type: "string", required: false, desc: "Alamat" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { kode_lokasi: "L-01", nama_lokasi: "Gedung A" }, response: { status: 201, body: { status: true, message: "Lokasi berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/lokasi/{id}", summary: "Detail", auth: true, desc: "Detail lokasi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lokasi" }], request: null, response: { status: 200, body: { status: true, message: "Detail lokasi berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Lokasi tidak ditemukan." } }] },
            { method: "PUT", path: "/api/lokasi/{id}", summary: "Update", auth: true, desc: "Update lokasi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lokasi" }], body: [ { name: "nama_lokasi", type: "string", required: false, desc: "Nama lokasi" } ], request: { nama_lokasi: "Gedung A1" }, response: { status: 200, body: { status: true, message: "Lokasi berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/lokasi/{id}", summary: "Hapus", auth: true, desc: "Hapus lokasi.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lokasi" }], request: null, response: { status: 200, body: { status: true, message: "Lokasi berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── SATUAN ─────────────────
    {
        group: "Satuan",
        icon: "⚖️",
        color: "#8b5cf6",
        description: "Manajemen satuan barang.",
        endpoints: [
            { method: "GET", path: "/api/satuan", summary: "Daftar satuan", auth: true, desc: "Mengambil semua data satuan.", request: null, response: { status: 200, body: { status: true, message: "Daftar satuan berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/satuan", summary: "Tambah", auth: true, desc: "Menambahkan satuan baru.", body: [ { name: "kode_satuan", type: "string", required: true, desc: "Kode satuan" }, { name: "nama_satuan", type: "string", required: true, desc: "Nama satuan" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { kode_satuan: "PCS", nama_satuan: "Pieces" }, response: { status: 201, body: { status: true, message: "Satuan berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/satuan/{id}", summary: "Detail", auth: true, desc: "Detail satuan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID satuan" }], request: null, response: { status: 200, body: { status: true, message: "Detail satuan berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Satuan tidak ditemukan." } }] },
            { method: "PUT", path: "/api/satuan/{id}", summary: "Update", auth: true, desc: "Update satuan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID satuan" }], body: [ { name: "nama_satuan", type: "string", required: false, desc: "Nama satuan" } ], request: { nama_satuan: "Pieces" }, response: { status: 200, body: { status: true, message: "Satuan berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/satuan/{id}", summary: "Hapus", auth: true, desc: "Hapus satuan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID satuan" }], request: null, response: { status: 200, body: { status: true, message: "Satuan berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── SUMBER PEROLEHAN ─────────────────
    {
        group: "Sumber Perolehan",
        icon: "💳",
        color: "#14b8a6",
        description: "Manajemen sumber perolehan aset.",
        endpoints: [
            { method: "GET", path: "/api/sumber-perolehan", summary: "Daftar sumber perolehan", auth: true, desc: "Mengambil semua data sumber perolehan.", request: null, response: { status: 200, body: { status: true, message: "Daftar sumber perolehan berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/sumber-perolehan", summary: "Tambah", auth: true, desc: "Menambahkan sumber perolehan baru.", body: [ { name: "kode_sumber", type: "string", required: true, desc: "Kode sumber" }, { name: "nama", type: "string", required: true, desc: "Nama sumber" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }, { name: "is_active", type: "boolean", required: false, desc: "Status aktif" } ], request: { kode_sumber: "BOS", nama: "Bantuan Operasional Sekolah", is_active: true }, response: { status: 201, body: { status: true, message: "Sumber perolehan berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/sumber-perolehan/{id}", summary: "Detail", auth: true, desc: "Detail sumber perolehan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID sumber" }], request: null, response: { status: 200, body: { status: true, message: "Detail sumber perolehan berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Sumber perolehan tidak ditemukan." } }] },
            { method: "PUT", path: "/api/sumber-perolehan/{id}", summary: "Update", auth: true, desc: "Update sumber perolehan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID sumber" }], body: [ { name: "nama", type: "string", required: false, desc: "Nama sumber" } ], request: { nama: "BOS Reguler" }, response: { status: 200, body: { status: true, message: "Sumber perolehan berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/sumber-perolehan/{id}", summary: "Hapus", auth: true, desc: "Hapus sumber perolehan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID sumber" }], request: null, response: { status: 200, body: { status: true, message: "Sumber perolehan berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── LEMARI ─────────────────
    {
        group: "Lemari",
        icon: "🗄️",
        color: "#6366f1",
        description: "Manajemen lemari tempat penyimpanan.",
        endpoints: [
            { method: "GET", path: "/api/lemari", summary: "Daftar lemari", auth: true, desc: "Mengambil semua data lemari.", request: null, response: { status: 200, body: { status: true, message: "Daftar lemari berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/lemari", summary: "Tambah", auth: true, desc: "Menambahkan lemari baru.", body: [ { name: "kode_lemari", type: "string", required: true, desc: "Kode lemari" }, { name: "nama", type: "string", required: true, desc: "Nama lemari" }, { name: "id_ruang", type: "integer", required: true, desc: "ID ruang" }, { name: "nomor_rak", type: "string", required: false, desc: "Nomor rak" } ], request: { kode_lemari: "LMR-01", nama: "Lemari Berkas", id_ruang: 1, nomor_rak: "A1" }, response: { status: 201, body: { status: true, message: "Lemari berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/lemari/{id}", summary: "Detail", auth: true, desc: "Detail lemari.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lemari" }], request: null, response: { status: 200, body: { status: true, message: "Detail lemari berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Lemari tidak ditemukan." } }] },
            { method: "PUT", path: "/api/lemari/{id}", summary: "Update", auth: true, desc: "Update lemari.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lemari" }], body: [ { name: "nama", type: "string", required: false, desc: "Nama lemari" } ], request: { nama: "Lemari Dokumen" }, response: { status: 200, body: { status: true, message: "Lemari berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/lemari/{id}", summary: "Hapus", auth: true, desc: "Hapus lemari.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID lemari" }], request: null, response: { status: 200, body: { status: true, message: "Lemari berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── PEMASOK (SUPPLIER) ─────────────────
    {
        group: "Pemasok (Supplier)",
        icon: "🏭",
        color: "#8b5cf6",
        description: "Manajemen data pemasok / supplier. ID pemasok dapat di-generate otomatis jika dikosongkan.",
        endpoints: [
            { method: "GET", path: "/api/pemasok", summary: "Daftar pemasok", auth: true, desc: "Mengambil semua data pemasok / supplier.", request: null, response: { status: 200, body: { status: true, message: "Daftar pemasok berhasil diambil.", data: [{ id_pemasok: 1, nama_pemasok: "PT Sumber Jaya", nomor_telepon: "081234567890", alamat: "Jl. Raya No. 123", keterangan: null }] } }, errors: [] },
            { method: "POST", path: "/api/pemasok", summary: "Tambah pemasok", auth: true, desc: "Menambahkan pemasok baru. Jika id_pemasok dikosongkan, sistem akan men-generate angka acak yang unik secara otomatis.",
              body: [
                  { name: "id_pemasok", type: "integer", required: false, desc: "Kode pemasok (opsional, jika kosong akan di-generate otomatis)" },
                  { name: "nama_pemasok", type: "string", required: true, desc: "Nama supplier / pemasok (maks 150 karakter)" },
                  { name: "nomor_telepon", type: "string", required: false, desc: "Nomor telepon kontak (maks 20 karakter)" },
                  { name: "alamat", type: "string", required: false, desc: "Alamat pemasok" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan tambahan" }
              ],
              request: { nama_pemasok: "PT Sumber Jaya", nomor_telepon: "081234567890", alamat: "Jl. Raya No. 123", keterangan: "Pemasok alat tulis" },
              response: { status: 201, body: { status: true, message: "Pemasok berhasil ditambahkan.", data: { id_pemasok: 4523, nama_pemasok: "PT Sumber Jaya", nomor_telepon: "081234567890", alamat: "Jl. Raya No. 123", keterangan: "Pemasok alat tulis" } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { id_pemasok: ["ID pemasok sudah digunakan."] } } }] },
            { method: "GET", path: "/api/pemasok/{id}", summary: "Detail pemasok", auth: true, desc: "Mengambil detail satu pemasok berdasarkan ID.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pemasok" }], request: null, response: { status: 200, body: { status: true, message: "Detail pemasok berhasil diambil.", data: { id_pemasok: 1, nama_pemasok: "PT Sumber Jaya", nomor_telepon: "081234567890", alamat: "Jl. Raya No. 123", keterangan: null } } }, errors: [{ status: 404, body: { status: false, message: "Pemasok tidak ditemukan." } }] },
            { method: "PUT", path: "/api/pemasok/{id}", summary: "Update pemasok", auth: true, desc: "Memperbarui data pemasok.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pemasok" }],
              body: [
                  { name: "nama_pemasok", type: "string", required: false, desc: "Nama pemasok" },
                  { name: "nomor_telepon", type: "string", required: false, desc: "Nomor telepon" },
                  { name: "alamat", type: "string", required: false, desc: "Alamat" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" }
              ],
              request: { nama_pemasok: "PT Sumber Jaya Updated", nomor_telepon: "089876543210" },
              response: { status: 200, body: { status: true, message: "Pemasok berhasil diperbarui.", data: { id_pemasok: 1, nama_pemasok: "PT Sumber Jaya Updated", nomor_telepon: "089876543210", alamat: "Jl. Raya No. 123", keterangan: null } } },
              errors: [{ status: 404, body: { status: false, message: "Pemasok tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/pemasok/{id}", summary: "Hapus pemasok", auth: true, desc: "Menghapus data pemasok.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pemasok" }], request: null, response: { status: 200, body: { status: true, message: "Pemasok berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Pemasok tidak ditemukan." } }] }
        ]
    },
    // ───────────────── GUDANG ─────────────────
    {
        group: "Gudang",
        icon: "🏢",
        color: "#06b6d4",
        description: "Manajemen data gudang penyimpanan barang.",
        endpoints: [
            { method: "GET", path: "/api/gudang", summary: "Daftar gudang", auth: true, desc: "Mengambil semua data gudang.", request: null, response: { status: 200, body: { status: true, message: "Daftar gudang berhasil diambil.", data: [{ kode_gudang: "GDG-001", nama_gudang: "Gudang Utama", keterangan: "Gudang penyimpanan utama" }] } }, errors: [] },
            { method: "POST", path: "/api/gudang", summary: "Tambah gudang", auth: true, desc: "Menambahkan gudang baru.",
              body: [
                  { name: "kode_gudang", type: "string", required: true, desc: "Kode gudang unik (maks 20 karakter)" },
                  { name: "nama_gudang", type: "string", required: true, desc: "Nama gudang (maks 100 karakter)" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan tambahan" }
              ],
              request: { kode_gudang: "GDG-002", nama_gudang: "Gudang Cadangan", keterangan: "Gudang penyimpanan cadangan" },
              response: { status: 201, body: { status: true, message: "Gudang berhasil ditambahkan.", data: { kode_gudang: "GDG-002", nama_gudang: "Gudang Cadangan", keterangan: "Gudang penyimpanan cadangan" } } },
              errors: [{ status: 422, body: { status: false, message: "Validasi gagal.", errors: { kode_gudang: ["Kode gudang sudah digunakan."] } } }] },
            { method: "GET", path: "/api/gudang/{id}", summary: "Detail gudang", auth: true, desc: "Mengambil detail satu gudang berdasarkan kode gudang.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode gudang" }], request: null, response: { status: 200, body: { status: true, message: "Detail gudang berhasil diambil.", data: { kode_gudang: "GDG-001", nama_gudang: "Gudang Utama", keterangan: "Gudang penyimpanan utama" } } }, errors: [{ status: 404, body: { status: false, message: "Gudang tidak ditemukan." } }] },
            { method: "PUT", path: "/api/gudang/{id}", summary: "Update gudang", auth: true, desc: "Memperbarui data gudang.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode gudang" }],
              body: [
                  { name: "nama_gudang", type: "string", required: false, desc: "Nama gudang" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" }
              ],
              request: { nama_gudang: "Gudang Utama Updated" },
              response: { status: 200, body: { status: true, message: "Gudang berhasil diperbarui.", data: { kode_gudang: "GDG-001", nama_gudang: "Gudang Utama Updated", keterangan: "Gudang penyimpanan utama" } } },
              errors: [{ status: 404, body: { status: false, message: "Gudang tidak ditemukan." } }] },
            { method: "DELETE", path: "/api/gudang/{id}", summary: "Hapus gudang", auth: true, desc: "Menghapus data gudang.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode gudang" }], request: null, response: { status: 200, body: { status: true, message: "Gudang berhasil dihapus." } }, errors: [{ status: 404, body: { status: false, message: "Gudang tidak ditemukan." } }] }
        ]
    },
    // ───────────────── PENGADAAN ─────────────────
    {
        group: "Pengadaan",
        icon: "📦",
        color: "#059669",
        description: "Manajemen data pengadaan aset. PK: id_pengadaan.",
        endpoints: [
            { method: "GET", path: "/api/pengadaan", summary: "Daftar pengadaan", auth: true, desc: "Mengambil semua data pengadaan.", request: null, response: { status: 200, body: { status: true, message: "Daftar pengadaan berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/pengadaan", summary: "Tambah pengadaan", auth: true, desc: "Menambahkan pengadaan baru. Aset akan di-generate jika status di-set ke 'dibelanjakan'.",
              body: [
                  { name: "tanggal_pengadaan", type: "date", required: true, desc: "Tanggal pengadaan" },
                  { name: "id_pemasok", type: "integer", required: false, desc: "ID pemasok" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" },
                  { name: "status", type: "string", required: true, desc: "Status pengadaan: diproses, dibelanjakan, selesai, dibatalkan" },
                  { name: "id_permintaan", type: "array", required: false, desc: "Array ID permintaan yang dilayani" },
                  { name: "detail", type: "array", required: true, desc: "Array detail pengadaan" },
                  { name: "detail.*.id_master_barang", type: "integer", required: true, desc: "ID master barang" },
                  { name: "detail.*.jumlah_masuk", type: "integer", required: true, desc: "Jumlah barang" },
                  { name: "detail.*.harga_satuan", type: "integer", required: true, desc: "Harga satuan" }
              ],
              request: { tanggal_pengadaan: "2026-05-01", status: "dibelanjakan", detail: [{ id_master_barang: 1, jumlah_masuk: 5, harga_satuan: 5000000 }] },
              response: { status: 201, body: { status: true, message: "Pengadaan berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/pengadaan/{id}", summary: "Detail pengadaan", auth: true, desc: "Mengambil detail satu pengadaan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pengadaan" }], request: null, response: { status: 200, body: { status: true, message: "Detail pengadaan berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Pengadaan tidak ditemukan." } }] },
            { method: "PUT", path: "/api/pengadaan/{id}", summary: "Update pengadaan", auth: true, desc: "Memperbarui data pengadaan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pengadaan" }],
              body: [
                  { name: "tanggal_pengadaan", type: "date", required: true, desc: "Tanggal" },
                  { name: "id_pemasok", type: "integer", required: false, desc: "ID pemasok" },
                  { name: "keterangan", type: "string", required: false, desc: "Keterangan" },
                  { name: "status", type: "string", required: true, desc: "Status pengadaan" }
              ],
              request: { status: "dibelanjakan" }, response: { status: 200, body: { status: true, message: "Pengadaan berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/pengadaan/{id}", summary: "Hapus pengadaan", auth: true, desc: "Menghapus data pengadaan.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pengadaan" }], request: null, response: { status: 200, body: { status: true, message: "Pengadaan berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── BARANG NON-AKTIF ─────────────────
    {
        group: "Barang Non-Aktif",
        icon: "🚫",
        color: "#ef4444",
        description: "Pencatatan barang yang sudah non-aktif (rusak berat, hilang, dll).",
        endpoints: [
            { method: "GET", path: "/api/barang-non-aktif", summary: "Daftar barang non-aktif", auth: true, desc: "Mengambil semua data barang non-aktif.", request: null, response: { status: 200, body: { status: true, message: "Daftar barang non-aktif berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/barang-non-aktif", summary: "Tambah barang non-aktif", auth: true, desc: "Mencatat barang non-aktif baru.", body: [ { name: "kode_inventaris", type: "string", required: true, desc: "Kode inventaris (FK ke pengadaan)" }, { name: "id_status", type: "integer", required: true, desc: "ID status barang" }, { name: "jumlah_nonaktif", type: "integer", required: false, desc: "Jumlah non-aktif" }, { name: "tanggal", type: "date", required: true, desc: "Tanggal pencatatan" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { kode_inventaris: "INV-2026-001", id_status: 3, jumlah_nonaktif: 1, tanggal: "2026-05-01" }, response: { status: 201, body: { status: true, message: "Barang non-aktif berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/barang-non-aktif/{id}", summary: "Detail", auth: true, desc: "Detail barang non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], request: null, response: { status: 200, body: { status: true, message: "Detail barang non-aktif berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Barang non-aktif tidak ditemukan." } }] },
            { method: "PUT", path: "/api/barang-non-aktif/{id}", summary: "Update", auth: true, desc: "Update barang non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], body: [ { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { keterangan: "Updated" }, response: { status: 200, body: { status: true, message: "Barang non-aktif berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/barang-non-aktif/{id}", summary: "Hapus", auth: true, desc: "Hapus barang non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], request: null, response: { status: 200, body: { status: true, message: "Barang non-aktif berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── TANAH NON-AKTIF ─────────────────
    {
        group: "Tanah Non-Aktif",
        icon: "🏜️",
        color: "#d97706",
        description: "Pencatatan tanah yang sudah non-aktif.",
        endpoints: [
            { method: "GET", path: "/api/tanah-non-aktif", summary: "Daftar tanah non-aktif", auth: true, desc: "Mengambil semua data tanah non-aktif.", request: null, response: { status: 200, body: { status: true, message: "Daftar tanah non-aktif berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/tanah-non-aktif", summary: "Tambah", auth: true, desc: "Mencatat tanah non-aktif.", body: [ { name: "kode_tanah", type: "integer", required: true, desc: "Kode tanah (FK ke aset_tanah)" }, { name: "id_status", type: "integer", required: true, desc: "ID status" }, { name: "tanggal", type: "date", required: true, desc: "Tanggal" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { kode_tanah: 1, id_status: 3, tanggal: "2026-05-01" }, response: { status: 201, body: { status: true, message: "Tanah non-aktif berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/tanah-non-aktif/{id}", summary: "Detail", auth: true, desc: "Detail tanah non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], request: null, response: { status: 200, body: { status: true, message: "Detail tanah non-aktif berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Tanah non-aktif tidak ditemukan." } }] },
            { method: "PUT", path: "/api/tanah-non-aktif/{id}", summary: "Update", auth: true, desc: "Update tanah non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], body: [ { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { keterangan: "Updated" }, response: { status: 200, body: { status: true, message: "Tanah non-aktif berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/tanah-non-aktif/{id}", summary: "Hapus", auth: true, desc: "Hapus tanah non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], request: null, response: { status: 200, body: { status: true, message: "Tanah non-aktif berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── BANGUNAN NON-AKTIF ─────────────────
    {
        group: "Bangunan Non-Aktif",
        icon: "🏚️",
        color: "#b91c1c",
        description: "Pencatatan bangunan yang sudah non-aktif.",
        endpoints: [
            { method: "GET", path: "/api/bangunan-non-aktif", summary: "Daftar bangunan non-aktif", auth: true, desc: "Mengambil semua data bangunan non-aktif.", request: null, response: { status: 200, body: { status: true, message: "Daftar bangunan non-aktif berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/bangunan-non-aktif", summary: "Tambah", auth: true, desc: "Mencatat bangunan non-aktif.", body: [ { name: "kode_bangunan", type: "integer", required: true, desc: "Kode bangunan (FK ke aset_bangunan)" }, { name: "id_status", type: "integer", required: true, desc: "ID status" }, { name: "tanggal", type: "date", required: true, desc: "Tanggal" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { kode_bangunan: 1, id_status: 3, tanggal: "2026-05-01" }, response: { status: 201, body: { status: true, message: "Bangunan non-aktif berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/bangunan-non-aktif/{id}", summary: "Detail", auth: true, desc: "Detail bangunan non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], request: null, response: { status: 200, body: { status: true, message: "Detail bangunan non-aktif berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Bangunan non-aktif tidak ditemukan." } }] },
            { method: "PUT", path: "/api/bangunan-non-aktif/{id}", summary: "Update", auth: true, desc: "Update bangunan non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], body: [ { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { keterangan: "Updated" }, response: { status: 200, body: { status: true, message: "Bangunan non-aktif berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/bangunan-non-aktif/{id}", summary: "Hapus", auth: true, desc: "Hapus bangunan non-aktif.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID" }], request: null, response: { status: 200, body: { status: true, message: "Bangunan non-aktif berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── ASET HABIS PAKAI ─────────────────
    {
        group: "Aset Habis Pakai",
        icon: "🧴",
        color: "#7c3aed",
        description: "Manajemen aset habis pakai (ATK, bahan lab, dll). PK: kode_barang (VARCHAR).",
        endpoints: [
            { method: "GET", path: "/api/aset-habis-pakai", summary: "Daftar aset habis pakai", auth: true, desc: "Mengambil semua data aset habis pakai.", request: null, response: { status: 200, body: { status: true, message: "Daftar aset habis pakai berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/aset-habis-pakai", summary: "Tambah", auth: true, desc: "Menambahkan aset habis pakai baru.", body: [ { name: "kode_barang", type: "string", required: true, desc: "Kode barang unik" }, { name: "nama_barang", type: "string", required: true, desc: "Nama barang" }, { name: "id_satuan", type: "integer", required: false, desc: "ID satuan" }, { name: "stok_minimal", type: "integer", required: false, desc: "Stok minimal" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" }, { name: "gambar", type: "string", required: false, desc: "Path gambar" } ], request: { kode_barang: "HP-001", nama_barang: "Kertas A4", id_satuan: 1, stok_minimal: 10 }, response: { status: 201, body: { status: true, message: "Aset habis pakai berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/aset-habis-pakai/{id}", summary: "Detail", auth: true, desc: "Detail aset habis pakai.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode barang" }], request: null, response: { status: 200, body: { status: true, message: "Detail aset habis pakai berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Aset habis pakai tidak ditemukan." } }] },
            { method: "PUT", path: "/api/aset-habis-pakai/{id}", summary: "Update", auth: true, desc: "Update aset habis pakai.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode barang" }], body: [ { name: "nama_barang", type: "string", required: false, desc: "Nama" }, { name: "stok_minimal", type: "integer", required: false, desc: "Stok minimal" } ], request: { stok_minimal: 20 }, response: { status: 200, body: { status: true, message: "Aset habis pakai berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/aset-habis-pakai/{id}", summary: "Hapus", auth: true, desc: "Hapus aset habis pakai.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode barang" }], request: null, response: { status: 200, body: { status: true, message: "Aset habis pakai berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── PENGADAAN HABIS PAKAI ─────────────────
    {
        group: "Pengadaan Habis Pakai",
        icon: "🛒",
        color: "#0891b2",
        description: "Pengadaan untuk aset habis pakai. PK: kode_inventaris (VARCHAR).",
        endpoints: [
            { method: "GET", path: "/api/pengadaan-habis-pakai", summary: "Daftar pengadaan habis pakai", auth: true, desc: "Mengambil semua data pengadaan habis pakai.", request: null, response: { status: 200, body: { status: true, message: "Daftar pengadaan habis pakai berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/pengadaan-habis-pakai", summary: "Tambah", auth: true, desc: "Menambahkan pengadaan habis pakai baru.", body: [ { name: "kode_inventaris", type: "string", required: true, desc: "Kode inventaris unik" }, { name: "tanggal_pengadaan", type: "date", required: true, desc: "Tanggal" }, { name: "kode_barang", type: "string", required: true, desc: "Kode barang (FK ke aset_habis_pakai)" }, { name: "jumlah", type: "integer", required: false, desc: "Jumlah" }, { name: "harga_satuan", type: "number", required: false, desc: "Harga satuan" }, { name: "id_pemasok", type: "integer", required: false, desc: "ID pemasok" }, { name: "kode_gudang", type: "string", required: false, desc: "Kode gudang" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { kode_inventaris: "INVHP-001", tanggal_pengadaan: "2026-05-01", kode_barang: "HP-001", jumlah: 100, harga_satuan: 50000 }, response: { status: 201, body: { status: true, message: "Pengadaan habis pakai berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/pengadaan-habis-pakai/{id}", summary: "Detail", auth: true, desc: "Detail pengadaan habis pakai.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode inventaris" }], request: null, response: { status: 200, body: { status: true, message: "Detail pengadaan habis pakai berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Pengadaan habis pakai tidak ditemukan." } }] },
            { method: "PUT", path: "/api/pengadaan-habis-pakai/{id}", summary: "Update", auth: true, desc: "Update pengadaan habis pakai.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode inventaris" }], body: [ { name: "jumlah", type: "integer", required: false, desc: "Jumlah" } ], request: { jumlah: 200 }, response: { status: 200, body: { status: true, message: "Pengadaan habis pakai berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/pengadaan-habis-pakai/{id}", summary: "Hapus", auth: true, desc: "Hapus pengadaan habis pakai.", params: [{ name: "id", in: "path", type: "string", required: true, desc: "Kode inventaris" }], request: null, response: { status: 200, body: { status: true, message: "Pengadaan habis pakai berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── BARANG KELUAR ─────────────────
    {
        group: "Barang Keluar",
        icon: "📤",
        color: "#f43f5e",
        description: "Manajemen pengeluaran aset habis pakai.",
        endpoints: [
            { method: "GET", path: "/api/barang-keluar", summary: "Daftar barang keluar", auth: true, desc: "Mengambil semua data barang keluar.", request: null, response: { status: 200, body: { status: true, message: "Daftar barang keluar berhasil diambil.", data: [] } }, errors: [] },
            { method: "POST", path: "/api/barang-keluar", summary: "Tambah", auth: true, desc: "Mencatat barang keluar baru.", body: [ { name: "tanggal_keluar", type: "date", required: true, desc: "Tanggal" }, { name: "kode_inventaris", type: "string", required: true, desc: "Kode inventaris (FK ke pengadaan_habis_pakai)" }, { name: "jumlah_keluar", type: "integer", required: true, desc: "Jumlah" }, { name: "id_ruang", type: "integer", required: true, desc: "ID ruang" }, { name: "nama_penerima", type: "integer", required: true, desc: "ID pengguna penerima" }, { name: "kode_gudang", type: "string", required: true, desc: "Kode gudang" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { tanggal_keluar: "2026-05-01", kode_inventaris: "INVHP-001", jumlah_keluar: 5, id_ruang: 1, nama_penerima: 2, kode_gudang: "GDG-001", keterangan: "Dipakai ujian" }, response: { status: 201, body: { status: true, message: "Barang keluar berhasil ditambahkan." } }, errors: [] },
            { method: "GET", path: "/api/barang-keluar/{id}", summary: "Detail", auth: true, desc: "Detail barang keluar.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID transaksi" }], request: null, response: { status: 200, body: { status: true, message: "Detail barang keluar berhasil diambil." } }, errors: [{ status: 404, body: { status: false, message: "Barang keluar tidak ditemukan." } }] },
            { method: "PUT", path: "/api/barang-keluar/{id}", summary: "Update", auth: true, desc: "Update barang keluar.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID transaksi" }], body: [ { name: "jumlah_keluar", type: "integer", required: false, desc: "Jumlah" }, { name: "keterangan", type: "string", required: false, desc: "Keterangan" } ], request: { jumlah_keluar: 10 }, response: { status: 200, body: { status: true, message: "Barang keluar berhasil diperbarui." } }, errors: [] },
            { method: "DELETE", path: "/api/barang-keluar/{id}", summary: "Hapus", auth: true, desc: "Hapus barang keluar.", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID transaksi" }], request: null, response: { status: 200, body: { status: true, message: "Barang keluar berhasil dihapus." } }, errors: [] }
        ]
    },
    // ───────────────── PENGATURAN ─────────────────
    {
        group: "Pengaturan",
        icon: "⚙️",
        color: "#6b7280",
        description: "Pengaturan profil, kontak, dan identitas lembaga.",
        endpoints: [
            { method: "GET", path: "/api/pengaturan", summary: "Ambil pengaturan", auth: true, desc: "Mengambil data pengaturan lembaga.", request: null,
              response: { status: 200, body: { status: true, message: "Data pengaturan berhasil diambil.", data: { id_pengaturan: 1, nama_instansi: "SMK Negeri 1 Contoh", alamat_instansi: "Jl. Pendidikan", wallpaper_aplikasi: null, telpon: "021-1234", website: "https://web.sch.id", email: "info@web.sch.id", kota: "Jakarta", kepala_sekolah: "Dr. Budi", NIP: "19800101", bagian_inventaris: "Sarpras" } } }, errors: [] },
            { method: "POST", path: "/api/pengaturan", summary: "Buat pengaturan", auth: true, desc: "Membuat pengaturan baru (jika belum ada). Mendukung upload file logo.",
              body: [
                  { name: "nama_instansi", type: "string", required: true, desc: "Nama institusi" },
                  { name: "alamat_instansi", type: "string", required: false, desc: "Alamat lengkap" },
                  { name: "wallpaper_aplikasi", type: "file", required: false, desc: "Logo lembaga (jpg, png)" },
                  { name: "telpon", type: "string", required: false, desc: "Nomor telepon" },
                  { name: "website", type: "string", required: false, desc: "URL website" },
                  { name: "email", type: "string", required: false, desc: "Email" },
                  { name: "kota", type: "string", required: false, desc: "Kota" },
                  { name: "kepala_sekolah", type: "string", required: false, desc: "Nama Kepala Sekolah" },
                  { name: "NIP", type: "string", required: false, desc: "NIP Kepala Sekolah" },
                  { name: "bagian_inventaris", type: "string", required: false, desc: "Penanggung Jawab Inventaris" }
              ],
              request: { nama_instansi: "SMK Negeri 1 Contoh", kota: "Jakarta" },
              response: { status: 201, body: { status: true, message: "Pengaturan lembaga berhasil dibuat." } },
              errors: [{ status: 422, body: { status: false, message: "Pengaturan sudah ada. Gunakan endpoint PUT untuk memperbarui." } }] },
            { method: "POST", path: "/api/pengaturan/{id}", summary: "Update pengaturan", auth: true, desc: "Memperbarui pengaturan (Menggunakan POST untuk mendukung multipart/form-data upload gambar).", params: [{ name: "id", in: "path", type: "integer", required: true, desc: "ID pengaturan" }],
              body: [
                  { name: "nama_instansi", type: "string", required: false, desc: "Nama institusi" },
                  { name: "alamat_instansi", type: "string", required: false, desc: "Alamat lengkap" },
                  { name: "wallpaper_aplikasi", type: "file", required: false, desc: "Logo lembaga (jpg, png)" },
                  { name: "telpon", type: "string", required: false, desc: "Nomor telepon" },
                  { name: "website", type: "string", required: false, desc: "URL website" },
                  { name: "email", type: "string", required: false, desc: "Email" },
                  { name: "kota", type: "string", required: false, desc: "Kota" },
                  { name: "kepala_sekolah", type: "string", required: false, desc: "Nama Kepala Sekolah" },
                  { name: "NIP", type: "string", required: false, desc: "NIP Kepala Sekolah" },
                  { name: "bagian_inventaris", type: "string", required: false, desc: "Penanggung Jawab Inventaris" }
              ],
              request: { nama_instansi: "SMK Negeri 1 Contoh Updated" },
              response: { status: 200, body: { status: true, message: "Pengaturan lembaga berhasil diperbarui." } }, errors: [{ status: 404, body: { status: false, message: "Pengaturan tidak ditemukan." } }] }
        ]
    },
    // ───────────────── MANAJEMEN DATABASE ─────────────────
    {
        group: "Manajemen Database",
        icon: "🗄️",
        color: "#64748b",
        description: "Modul khusus super admin untuk operasional operasional DB (backup, restore, reset, ubah koneksi).",
        endpoints: [
            { method: "POST", path: "/api/database/backup", summary: "Backup Database", auth: true,
              desc: "Menghasilkan dan mengunduh file backup database (.sql) menggunakan mysqldump.",
              request: null, response: { status: 200, body: { message: "[File Download: backup-YYYY-MM-DD-HH-mm-ss.sql]" } }, errors: [] },
            { method: "POST", path: "/api/database/restore", summary: "Restore Database", auth: true,
              desc: "Mengunggah file konfigurasi/backup .sql atau .txt untuk memulihkan data. Foreign key checks dinonaktifkan sementara.",
              body: [
                  { name: "sql_file", type: "file", required: true, desc: "File backup .sql atau .txt" }
              ],
              request: null, response: { status: 200, body: { message: "Database berhasil direstore." } }, errors: [{ status: 500, body: { error: "Gagal merestore database: [Error detail]" } }] },
            { method: "POST", path: "/api/database/reset", summary: "Reset Database", auth: true,
              desc: "Melakukan fresh migration diikuti dengan seeder ulang. PERHATIAN: Ini akan menghapus data saat ini secara permanen.",
              request: null, response: { status: 200, body: { message: "Database berhasil direset." } }, errors: [{ status: 500, body: { error: "[Error detail]" } }] },
            { method: "POST", path: "/api/database/change-connection", summary: "Ubah Koneksi", auth: true,
              desc: "Mengganti parameter kredensial dan konfigurasi database di dalam file .env, lalu menghapus cache (config:clear).",
              body: [
                  { name: "db_host", type: "string", required: true, desc: "IP atau Host database, cth: 127.0.0.1" },
                  { name: "db_name", type: "string", required: true, desc: "Nama skema database" },
                  { name: "db_user", type: "string", required: true, desc: "Username database" },
                  { name: "db_pass", type: "string", required: false, desc: "Password database, null jika diizinkan kosong" }
              ],
              request: { db_host: "127.0.0.1", db_name: "simins_db", db_user: "root", db_pass: "" },
              response: { status: 200, body: { message: "Koneksi berhasil diubah." } }, errors: [] }
        ]
    }
];

// ====================================================================
// UTILITY FUNCTIONS
// ====================================================================
function syntaxHighlight(json) {
    if (typeof json !== 'string') json = JSON.stringify(json, null, 2);
    json = json.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    return json.replace(/("(\\u[\da-fA-F]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, m => {
        let cls = 'json-number';
        if (/^"/.test(m)) { cls = /:$/.test(m) ? 'json-key' : 'json-string'; }
        else if (/true|false/.test(m)) cls = 'json-boolean';
        else if (/null/.test(m)) cls = 'json-null';
        return `<span class="${cls}">${m}</span>`;
    });
}

function slugify(str) { return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); }

function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        btn.classList.add('copied');
        btn.innerHTML = '✓ Copied';
        setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = '📋 Copy'; }, 1500);
    });
}

// ====================================================================
// RENDER SIDEBAR
// ====================================================================
function renderSidebar() {
    const nav = document.getElementById('sidebarNav');
    let html = '';
    let totalEndpoints = 0;

    API_DATA.forEach(group => {
        const gSlug = slugify(group.group);
        html += `<div class="nav-group" data-group="${gSlug}">`;
        html += `<div class="nav-group-header" onclick="this.parentElement.classList.toggle('collapsed')">
            <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            <span>${group.icon} ${group.group}</span>
        </div>`;
        html += `<div class="nav-items">`;

        group.endpoints.forEach((ep, i) => {
            const id = `${gSlug}-${i}`;
            html += `<a class="nav-item" href="#${id}" data-method="${ep.method.toLowerCase()}" data-path="${ep.path}" data-summary="${ep.summary}" onclick="setActiveNav(this)">
                <span class="nav-method ${ep.method.toLowerCase()}">${ep.method}</span>
                <span class="nav-path">${ep.path}</span>
            </a>`;
            totalEndpoints++;
        });

        html += `</div></div>`;
    });

    nav.innerHTML = html;
    document.getElementById('endpointCount').textContent = `${totalEndpoints} endpoints`;
}

// ====================================================================
// RENDER MAIN CONTENT
// ====================================================================
function renderMainContent() {
    const main = document.getElementById('mainContent');
    let html = '';

    // HERO
    html += `<div class="hero">
        <h1>📚 SIMINS API Documentation</h1>
        <p>Sistem Informasi Manajemen Inventaris Sekolah — RESTful API</p>
        <p style="font-size:13px;color:var(--text-muted)">Laravel 12 • PHP 8.4 • Laravel Sanctum</p>
        <div class="hero-meta">
            <div class="hero-meta-item">🔗 <span>Version 1.0</span></div>
            <div class="hero-meta-item">🔒 <span>Bearer Token Auth</span></div>
            <div class="hero-meta-item">📡 <span>JSON Response</span></div>
        </div>
        <div class="base-url-box">
            <div><span class="label">Base URL</span><code>http://127.0.0.1:8000/api</code></div>
            <button class="copy-btn" onclick="copyToClipboard('http://127.0.0.1:8000/api', this)">📋 Copy</button>
        </div>
    </div>`;

    // SECTIONS
    API_DATA.forEach(group => {
        const gSlug = slugify(group.group);
        html += `<div class="section-group" id="section-${gSlug}">`;
        html += `<div class="section-title"><span class="icon" style="background:${group.color}15;color:${group.color}">${group.icon}</span>${group.group}</div>`;
        html += `<p class="section-desc">${group.description}</p>`;

        group.endpoints.forEach((ep, i) => {
            const id = `${gSlug}-${i}`;
            html += renderEndpointCard(ep, id);
        });

        html += `</div>`;
    });

    main.innerHTML = html;
}

function renderEndpointCard(ep, id) {
    const m = ep.method.toLowerCase();
    let html = `<div class="endpoint-card" id="${id}" data-method="${m}">`;

    // Header
    html += `<div class="endpoint-header" onclick="this.parentElement.classList.toggle('expanded')">
        <span class="method-badge ${m}">${ep.method}</span>
        <span class="endpoint-path">${ep.path}</span>
        ${ep.auth ? '<svg class="lock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' : ''}
        <span class="endpoint-summary">${ep.summary}</span>
        <svg class="endpoint-toggle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
    </div>`;

    // Body
    html += `<div class="endpoint-body">`;
    html += `<p class="endpoint-desc">${ep.desc}</p>`;

    // Auth header
    if (ep.auth) {
        html += `<div class="header-info">
            <span class="header-label">Authorization</span>
            <code>Bearer {access_token}</code>
            <span style="margin-left:auto;font-size:11px;color:var(--yellow);">🔒 Required</span>
        </div>`;
    }

    // Tabs
    const hasParms = ep.params || ep.body;
    const hasReq = ep.request !== null && ep.request !== undefined;
    const hasResp = ep.response;
    const hasErr = ep.errors && ep.errors.length > 0;
    const hasTry = true; // Allow Try API for ALL methods

    html += `<div class="tabs">`;
    if (hasParms) html += `<button class="tab-btn active" onclick="switchTab(this, '${id}-params')">Parameters</button>`;
    if (hasReq) html += `<button class="tab-btn ${!hasParms?'active':''}" onclick="switchTab(this, '${id}-request')">Request</button>`;
    if (hasResp) html += `<button class="tab-btn ${!hasParms&&!hasReq?'active':''}" onclick="switchTab(this, '${id}-response')">Response</button>`;
    if (hasErr) html += `<button class="tab-btn" onclick="switchTab(this, '${id}-errors')">Errors</button>`;
    if (hasTry) html += `<button class="tab-btn" onclick="switchTab(this, '${id}-try')">⚡ Try API</button>`;
    html += `</div>`;

    // Parameters tab
    if (hasParms) {
        html += `<div class="tab-content active" id="${id}-params">`;
        html += `<table class="param-table"><thead><tr><th>Parameter</th><th>Tipe</th><th>Lokasi</th><th>Deskripsi</th></tr></thead><tbody>`;
        if (ep.params) {
            ep.params.forEach(p => {
                html += `<tr><td><span class="param-name">${p.name}</span>${p.required ? '<span class="param-required">Required</span>' : '<span class="param-optional">Optional</span>'}</td><td><span class="param-type">${p.type}</span></td><td>path</td><td>${p.desc}</td></tr>`;
            });
        }
        if (ep.body) {
            ep.body.forEach(p => {
                html += `<tr><td><span class="param-name">${p.name}</span>${p.required ? '<span class="param-required">Required</span>' : '<span class="param-optional">Optional</span>'}</td><td><span class="param-type">${p.type}</span></td><td>body</td><td>${p.desc}</td></tr>`;
            });
        }
        html += `</tbody></table></div>`;
    }

    // Request tab
    if (hasReq) {
        const reqJson = JSON.stringify(ep.request, null, 2);
        html += `<div class="tab-content ${!hasParms?'active':''}" id="${id}-request">`;
        html += `<div class="code-block-wrapper"><div class="code-label"><span>Request Body — application/json</span></div>`;
        html += `<div class="code-block"><button class="copy-btn" onclick="copyToClipboard(\`${reqJson.replace(/`/g,'\\`').replace(/\\/g,'\\\\')}\`, this)">📋 Copy</button><pre>${syntaxHighlight(ep.request)}</pre></div></div>`;
        html += `</div>`;
    }

    // Response tab
    if (hasResp) {
        html += `<div class="tab-content ${!hasParms&&!hasReq?'active':''}" id="${id}-response">`;
        html += `<div class="code-block-wrapper"><div class="code-label"><span>Success Response</span><span class="status-code status-${ep.response.status}">${ep.response.status} ${ep.response.status===200?'OK':ep.response.status===201?'Created':''}</span></div>`;
        html += `<div class="code-block"><button class="copy-btn" onclick="copyToClipboard(JSON.stringify(${JSON.stringify(ep.response.body)}, null, 2), this)">📋 Copy</button><pre>${syntaxHighlight(ep.response.body)}</pre></div></div>`;
        html += `</div>`;
    }

    // Errors tab
    if (hasErr) {
        html += `<div class="tab-content" id="${id}-errors">`;
        ep.errors.forEach(err => {
            html += `<div class="code-block-wrapper"><div class="code-label"><span>Error Response</span><span class="status-code status-${err.status}">${err.status} ${err.status===401?'Unauthorized':err.status===404?'Not Found':err.status===422?'Unprocessable':err.status===500?'Server Error':''}</span></div>`;
            html += `<div class="code-block"><pre>${syntaxHighlight(err.body)}</pre></div></div>`;
        });
        html += `</div>`;
    }

    // Try API tab
    if (hasTry) {
        const defaultBody = ep.request ? JSON.stringify(ep.request, null, 2) : '{}';
        html += `<div class="tab-content" id="${id}-try">`;
        html += `<div class="try-api-panel">
            <h4>⚡ Try API — ${ep.method} ${ep.path}</h4>
            <div class="try-input-group"><label>URL</label><input type="text" id="${id}-try-url" value="http://127.0.0.1:8000${ep.path}"></div>
            <div class="try-input-group"><label>Bearer Token</label><input type="text" id="${id}-try-token" placeholder="Paste your access_token here..."></div>`;
        if (ep.method === 'POST' || ep.method === 'PUT' || ep.method === 'PATCH') {
            html += `<div class="try-input-group"><label>Request Body (JSON)</label><textarea id="${id}-try-body">${defaultBody}</textarea></div>`;
        } else {
            html += `<input type="hidden" id="${id}-try-body" value="">`;
        }
        html += `<button class="try-btn" onclick="tryApi('${id}', '${ep.method}')">🚀 Send Request</button>
            <div class="try-response" id="${id}-try-response"></div>
        </div>`;
        html += `</div>`;
    }

    html += `</div></div>`; // endpoint-body, endpoint-card
    return html;
}

// ====================================================================
// TAB SWITCHING
// ====================================================================
function switchTab(btn, contentId) {
    const card = btn.closest('.endpoint-card');
    card.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    card.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(contentId).classList.add('active');
}

// ====================================================================
// SIDEBAR NAVIGATION
// ====================================================================
function setActiveNav(el) {
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    el.classList.add('active');
    // Close mobile sidebar
    if (window.innerWidth <= 900) toggleSidebar();
}

// ====================================================================
// SEARCH & FILTER
// ====================================================================
document.addEventListener('DOMContentLoaded', () => {
    renderSidebar();
    renderMainContent();

    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', filterEndpoints);

    document.querySelectorAll('.method-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.method-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterEndpoints();
        });
    });

    // Highlight active nav on scroll
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                const activeNav = document.querySelector(`.nav-item[href="#${id}"]`);
                if (activeNav) activeNav.classList.add('active');
            }
        });
    }, { rootMargin: '-80px 0px -60% 0px' });

    document.querySelectorAll('.endpoint-card').forEach(card => observer.observe(card));
});

function filterEndpoints() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const activeMethod = document.querySelector('.method-filter-btn.active').dataset.method;

    document.querySelectorAll('.nav-item').forEach(item => {
        const method = item.dataset.method;
        const path = item.dataset.path.toLowerCase();
        const summary = item.dataset.summary.toLowerCase();
        const matchesSearch = path.includes(search) || summary.includes(search);
        const matchesMethod = activeMethod === 'all' || method === activeMethod;
        item.style.display = matchesSearch && matchesMethod ? 'flex' : 'none';
    });

    document.querySelectorAll('.endpoint-card').forEach(card => {
        const method = card.dataset.method;
        const path = card.querySelector('.endpoint-path').textContent.toLowerCase();
        const summary = card.querySelector('.endpoint-summary')?.textContent.toLowerCase() || '';
        const matchesSearch = path.includes(search) || summary.includes(search);
        const matchesMethod = activeMethod === 'all' || method === activeMethod;
        card.style.display = matchesSearch && matchesMethod ? 'block' : 'none';
    });

    // Hide empty groups
    document.querySelectorAll('.nav-group').forEach(group => {
        const visibleItems = group.querySelectorAll('.nav-item[style="display: flex"], .nav-item:not([style])');
        const hasVisible = Array.from(group.querySelectorAll('.nav-item')).some(i => i.style.display !== 'none');
        group.style.display = hasVisible ? 'block' : 'none';
    });

    // Update count
    const visible = document.querySelectorAll('.endpoint-card:not([style*="display: none"])').length;
    const total = document.querySelectorAll('.endpoint-card').length;
    document.getElementById('endpointCount').textContent = search || activeMethod !== 'all' ? `${visible} / ${total} endpoints` : `${total} endpoints`;
}

// ====================================================================
// THEME
// ====================================================================
function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    document.getElementById('themeLabel').textContent = next === 'dark' ? 'Light' : 'Dark';
    document.getElementById('themeIcon').innerHTML = next === 'dark'
        ? '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>'
        : '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
    localStorage.setItem('api-docs-theme', next);
}

// Load saved theme
(function() {
    const saved = localStorage.getItem('api-docs-theme');
    if (saved) {
        document.documentElement.setAttribute('data-theme', saved);
        if (saved === 'light') {
            document.getElementById('themeLabel').textContent = 'Dark';
        }
    }
})();

// ====================================================================
// MOBILE SIDEBAR
// ====================================================================
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}

// ====================================================================
// TRY API
// ====================================================================
async function tryApi(id, method) {
    const url = document.getElementById(`${id}-try-url`).value;
    const token = document.getElementById(`${id}-try-token`).value;
    const bodyRaw = document.getElementById(`${id}-try-body`).value;
    const responseDiv = document.getElementById(`${id}-try-response`);

    responseDiv.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">⏳ Sending request...</p>';

    try {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        };

        if (token) options.headers['Authorization'] = `Bearer ${token}`;
        if (method !== 'GET' && method !== 'DELETE' && bodyRaw.trim()) {
            options.body = bodyRaw;
        }

        const res = await fetch(url, options);
        const data = await res.json();
        const statusClass = res.ok ? 'status-200' : (res.status >= 400 && res.status < 500 ? 'status-422' : 'status-500');

        responseDiv.innerHTML = `
            <div class="code-block-wrapper" style="margin-top:8px">
                <div class="code-label"><span>Response</span><span class="status-code ${statusClass}">${res.status} ${res.statusText}</span></div>
                <div class="code-block"><pre>${syntaxHighlight(data)}</pre></div>
            </div>`;
    } catch (err) {
        responseDiv.innerHTML = `
            <div class="code-block-wrapper" style="margin-top:8px">
                <div class="code-label"><span>Error</span><span class="status-code status-500">Network Error</span></div>
                <div class="code-block"><pre style="color:var(--red)">${err.message}\n\nPastikan server Laravel berjalan di URL yang benar.</pre></div>
            </div>`;
    }
}
</script>
</body>
</html>
