<?php
// index.php - Timor Furniture Co. Receptionist Dashboard Interface
require_once __DIR__ . '/config/db.php';

// Trigger database auto initialization
$pdo = getDBConnection();
?>
<!DOCTYPE html>
<html lang="tet" data-app-lang="tet">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Resepsionista | Timor Teak Furniture Co.</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Custom Timor Flag Design System -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Timor-Leste Flag Top Strip -->
<div class="timor-flag-bar"></div>

<div class="app-container">
    <!-- Sidebar Navigation (11 Links) -->
    <aside class="sidebar">
        <div class="brand-section">
            <div class="brand-logo-badge">🇹🇱</div>
            <div class="brand-info">
                <h1>LesteMóveis</h1>
                <span>Timor Furniture Co.</span>
            </div>
        </div>

        <div class="nav-scroll">
            <div class="nav-category" data-i18n-key="nav.category.main">Menu Prinsipál</div>
            <ul class="nav-list">
                <li>
                    <a class="nav-link active" data-view="dashboard">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        <span data-i18n-key="nav.dashboard">Painel</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="visitors">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span data-i18n-key="nav.visitors">Jestaun Vizitante</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="appointments">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span data-i18n-key="nav.appointments">Jestaun Agendasaun</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="customers">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span data-i18n-key="nav.customers">Jestaun Kliente</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="showroom">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span data-i18n-key="nav.showroom">Salaun Exposisaun Mobília</span>
                    </a>
                </li>
            </ul>

            <div class="nav-category" data-i18n-key="nav.category.communication">Komunikasaun & Atividade</div>
            <ul class="nav-list">
                <li>
                    <a class="nav-link" data-view="messages">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <span data-i18n-key="nav.messages">Mensajen</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span data-i18n-key="nav.notifications">Notifikasaun</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="reports">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        <span data-i18n-key="nav.reports">Relatóriu & Esporta</span>
                    </a>
                </li>
            </ul>

            <div class="nav-category" data-i18n-key="nav.category.system">Sistema & Konta</div>
            <ul class="nav-list">
                <li>
                    <a class="nav-link" data-view="profile">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                        <span data-i18n-key="nav.profile">Perfíl Kompañia</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="settings">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        <span data-i18n-key="nav.settings">Konfigurasaun</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" data-view="logout" style="color:var(--timor-red);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span data-i18n-key="nav.logout">Sai</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="top-left">
                <div class="page-heading">
                    <h2 id="page-title-text" data-i18n-key="page.dashboard.title">Vizualizasaun Painel</h2>
                    <p id="page-subtitle" data-i18n-key="page.dashboard.subtitle">Bem-fali, Maria Silva! Iha ne'e vizualizasaun resepsaun ohin.</p>
                </div>
            </div>
            <div class="top-right">
                <button class="header-toggle-btn" id="lang-toggle-btn" type="button" aria-label="Switch language">
                    <span>🌐</span>
                    <span id="lang-toggle-label" data-i18n-key="lang.toggle">Tetun / EN</span>
                </button>
                <button class="header-toggle-btn" id="theme-toggle-btn" type="button" aria-label="Switch theme">
                    <span id="theme-toggle-icon">☾</span>
                    <span id="theme-toggle-label" data-i18n-key="theme.toggle">Black / White</span>
                </button>
                <!-- Clock Badge -->
                <div class="clock-badge">
                    <span>🕒</span>
                    <span id="live-clock">Loading time...</span>
                </div>
                <!-- Notif Bell -->
                <button class="notif--btn" onclick="document.querySelector('[data-view=notifications]').click()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    <span class="notif-dot"></span>
                </button>
                <!-- User Profile -->
                <div class="user-profile-btn" onclick="document.querySelector('[data-view=profile]').click()">
                    <div class="user-avatar">MS</div>
                    <div class="user-details">
                        <span class="user-name">Maria Silva</span>
                        <span class="user-role">Lead Receptionist</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            
            <!-- 1. VIEW: DASHBOARD OVERVIEW -->
            <section id="view-dashboard" class="view-section active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div>
                            <div class="stat-label">Vizitante Ohin</div>
                            <div class="stat-value" id="stat-visitors-today">0</div>
                            <div class="stat-sub">Entrada & Konvidadu</div>
                        </div>
                        <div class="stat-icon-wrapper">👥</div>
                    </div>
                    <div class="stat-card">
                        <div>
                            <div class="stat-label">Total Agendasaun</div>
                            <div class="stat-value" id="stat-appointments-today">0</div>
                            <div class="stat-sub">Horária Ohin Loron</div>
                        </div>
                        <div class="stat-icon-wrapper" style="background:var(--timor-yellow-light); color:var(--timor-yellow);">📅</div>
                    </div>
                    <div class="stat-card">
                        <div>
                            <div class="stat-label">Agendasaun Pendente</div>
                            <div class="stat-value" id="stat-pending-appointments">0</div>
                            <div class="stat-sub">Hein Konsultasaun</div>
                        </div>
                        <div class="stat-icon-wrapper" style="background:rgba(17,24,39,0.08); color:var(--timor-black);">⏳</div>
                    </div>
                    <div class="stat-card">
                        <div>
                            <div class="stat-label">Kliente Seidauk Sai</div>
                            <div class="stat-value" id="stat-checked-in">0</div>
                            <div class="stat-sub">Atualmente iha Laran</div>
                        </div>
                        <div class="stat-icon-wrapper" style="background:var(--status-active-bg); color:var(--status-active);">🏬</div>
                    </div>
                    <div class="stat-card">
                        <div>
                            <div class="stat-label">Vizita Kompletu</div>
                            <div class="stat-value" id="stat-completed-visits">0</div>
                            <div class="stat-sub">Sai Ohin</div>
                        </div>
                        <div class="stat-icon-wrapper" style="background:var(--status-info-bg); color:var(--status-info);">✅</div>
                    </div>
                </div>

                <!-- Dashboard 2 Column Layout -->
                <div class="dashboard-grid">
                    <div class="panel-card">
                        <div class="panel-header">
                            <h3>Rejistru Vizitante Resente</h3>
                            <button class="btn btn-outline btn-sm" onclick="document.querySelector('[data-view=visitors]').click()">Haree Hotu</button>
                        </div>
                        <div class="card-table" style="box-shadow:none; border:none;">
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID Vizitante</th>
                                            <th>Naran</th>
                                            <th>Objetivu</th>
                                            <th>Hasoru</th>
                                            <th>Estadu</th>
                                        </tr>
                                    </thead>
                                    <tbody id="overview-visitors-tbody">
                                        <!-- Loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:1.25rem;">
                        <!-- Quick Actions Panel -->
                        <div class="panel-card">
                            <div class="panel-header">
                                <h3>Asaun Resepsaun Lalais</h3>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                                <button class="btn btn-timor-red" onclick="openVisitorModal()">
                                    <span>+</span> Rejista Vizitante Foun
                                </button>
                                <button class="btn btn-timor-black" onclick="openAppointmentModal()">
                                    <span>+</span> Kria Agendasaun
                                </button>
                                <button class="btn btn-outline" onclick="openShowroomModal()">
                                    <span>+</span> Rezerva Vizita Salaun
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Widget -->
                        <div class="calendar-widget">
                            <div class="calendar-header">July 2026</div>
                            <div id="calendar-days-grid" class="calendar-days-grid">
                                <!-- Calendar Days Rendered Here -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. VIEW: VISITOR MANAGEMENT -->
            <section id="view-visitors" class="view-section">
                <div class="toolbar">
                    <div class="search-field">
                        <svg viewBoxbell="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="visitor-search" placeholder="Buka vizitante husi ID, naran, telefone, ka objetivu...">
                    </div>
                    <button class="btn btn-timor-red" onclick="openVisitorModal()">
                        <span>+</span> Rejista Vizitante
                    </button>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Vizitante</th>
                                    <th>Naran Kompletu</th>
                                    <th>Objetivu Vizita</th>
                                    <th>Ema Atu Hasoru</th>
                                    <th>Oras Entrada</th>
                                    <th>Estadu</th>
                                    <th>Asaun</th>
                                </tr>
                            </thead>
                            <tbody id="visitors-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- 3. VIEW: APPOINTMENT MANAGEMENT -->
            <section id="view-appointments" class="view-section">
                <div class="toolbar">
                    <div class="search-field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="appointment-search" placeholder="Buka naran kliente, representante, ka data...">
                    </div>
                    <button class="btn btn-timor-red" onclick="openAppointmentModal()">
                        <span>+</span> Kria Agendasaun
                    </button>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kliente</th>
                                    <th>Data & Oras</th>
                                    <th>Objetivu</th>
                                    <th>Representante</th>
                                    <th>Estadu</th>
                                    <th>Nota</th>
                                    <th>Asaun</th>
                                </tr>
                            </thead>
                            <tbody id="appointments-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- 4. VIEW: CUSTOMER MANAGEMENT -->
            <section id="view-customers" class="view-section">
                <div class="toolbar">
                    <div class="search-field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="customer-search" placeholder="Buka naran kliente, kompañia, ka fatin...">
                    </div>
                    <button class="btn btn-timor-red" onclick="openCustomerModal()">
                        <span>+</span> Aumenta Kliente
                    </button>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kódigu</th>
                                    <th>Kliente & Kompañia</th>
                                    <th>Informasaun Kontaktu</th>
                                    <th>Enderesu</th>
                                    <th>Istória Sosa</th>
                                    <th>Nota</th>
                                    <th>Asaun</th>
                                </tr>
                            </thead>
                            <tbody id="customers-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- 5. VIEW: FURNITURE SHOWROOM VISITS -->
            <section id="view-showroom" class="view-section">
                <div class="toolbar">
                    <div class="search-field">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="showroom-search" placeholder="Buka naran vizitante ka interese mobília...">
                    </div>
                    <button class="btn btn-timor-red" onclick="openShowroomModal()">
                        <span>+</span> Agendariza Vizita
                    </button>
                </div>

                <div class="card-table">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Naran Vizitante</th>
                                    <th>Data Vizita</th>
                                    <th>Interese Mobília</th>
                                    <th>Representante Atribuídu</th>
                                    <th>Estadu</th>
                                    <th>Nota</th>
                                    <th>Asaun</th>
                                </tr>
                            </thead>
                            <tbody id="showroom-table-body">
                                <!-- Loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- 6. VIEW: MESSAGES -->
            <section id="view-messages" class="view-section">
                <div class="panel-card">
                    <div class="panel-header">
                        <h3>Mensajen & Peskiza Resepsaun</h3>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        <div style="padding:1rem; background:#f8fafc; border-radius:12px; border:1px solid var(--border-color);">
                            <strong>Husi: Manuel Santos (Jestaun Vendas Sénior)</strong>
                            <p style="font-size:0.88rem; color:var(--text-main); margin-top:0.35rem;">"Favór informa hau imediatamente bainhira delegasaun Ministériu Infraestrutura to'o iha resepsaun."</p>
                        </div>
                        <div style="padding:1rem; background:#f8fafc; border-radius:12px; border:1px solid var(--border-color);">
                            <strong>Husi: Filomena Soares (Dezainadora Interior)</strong>
                            <p style="font-size:0.88rem; color:var(--text-main); margin-top:0.35rem;">"Hau iha Salaun B halo preparasaun Exposisaun Mesa Teak ba Nia Maria da Costa."</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 7. VIEW: NOTIFICATIONS -->
            <section id="view-notifications" class="view-section">
                <div class="panel-card">
                    <div class="panel-header">
                        <h3>Notifikasaun Hotu</h3>
                        <button class="btn btn-outline btn-sm" onclick="fetch('api/dashboard_api.php?action=notifications', {method:'POST'}).then(()=>loadNotifications())">Marka Hotu Ona Le'e</button>
                    </div>
                    <div id="notifications-list" style="display:flex; flex-direction:column; gap:0.85rem;">
                        <!-- Loaded dynamically -->
                    </div>
                </div>
            </section>

            <!-- 8. VIEW: REPORTS & EXPORT -->
            <section id="view-reports" class="view-section">
                <div class="toolbar">
                    <h3 style="font-size:1.1rem; color:var(--timor-black);">Relatóriu & Analítika Resepsaun</h3>
                    <div style="display:flex; gap:0.75rem;">
                        <button class="btn btn-timor-black" onclick="exportReportExcel()">📥 Esporta Excel (CSV)</button>
                        <button class="btn btn-timor-red" onclick="exportReportPDF()">📄 Esporta PDF / Imprime</button>
                    </div>
                </div>

                <div id="reports-summary-container"></div>
            </section>

            <!-- 9. VIEW: COMPANY PROFILE -->
            <section id="view-profile" class="view-section">
                <div class="panel-card" style="max-width:600px;">
                    <div class="panel-header">
                        <h3>Perfíl Konta Resepsionista</h3>
                    </div>
                    <div style="display:flex; gap:1.25rem; align-items:center;">
                        <div class="user-avatar" style="width:64px; height:64px; font-size:1.5rem;">MS</div>
                        <div>
                            <h4 style="font-size:1.1rem; color:var(--timor-black);">Maria Silva</h4>
                            <p style="font-size:0.85rem; color:var(--timor-red); font-weight:600;">Resepsionista Prinsipál & Koordenadór Konvidadu</p>
                            <p style="font-size:0.8rem; color:var(--text-muted);">Empresa Timor Teak Furniture Co. Lda</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 10. VIEW: SETTINGS -->
            <section id="view-settings" class="view-section">
                <div class="panel-card" style="max-width:650px;">
                    <div class="panel-header">
                        <h3>Informasaun Kompañia & Oras Negósiu</h3>
                    </div>
                    <form onsubmit="saveSettingsForm(event)">
                        <div style="display:flex; flex-direction:column; gap:1rem;">
                            <div class="form-group">
                                <label>Naran Kompañia</label>
                                <input type="text" id="set-company-name" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Numeru Telefone</label>
                                    <input type="text" id="set-company-phone" required>
                                </div>
                                <div class="form-group">
                                    <label>Enderesu Email</label>
                                    <input type="email" id="set-company-email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Enderesu Salaun Exposisaun</label>
                                <input type="text" id="set-company-address" required>
                            </div>
                            <div class="form-group">
                                <label>Oras Funsionamentu Negósiu</label>
                                <input type="text" id="set-business-hours" required>
                            </div>
                            <div class="form-group">
                                <label>Naran Resepsionista Prinsipál</label>
                                <input type="text" id="set-receptionist-name" required>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-timor-red">Salva Konfigurasaun Kompañia</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

        </main>
    </div>
</div>

<!-- ================= INTERACTIVE MODALS ================= -->

<!-- 1. Visitor Registration Modal -->
<div id="visitor-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="visitor-modal-title">Rejista Vizitante Foun</h3>
            <button class="modal-close-btn" onclick="closeVisitorModal()">&times;</button>
        </div>
        <form id="visitor-form" onsubmit="saveVisitor(event)">
            <div class="modal-body">
                <input type="hidden" id="visitor-id" value="0">
                <div class="form-group">
                    <label>Naran Kompletu Vizitante *</label>
                    <input type="text" id="visitor-name" required placeholder="ex. Joao Pereira">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Numeru Telefone *</label>
                        <input type="text" id="visitor-phone" required placeholder="+670 7712 3456">
                    </div>
                    <div class="form-group">
                        <label>Enderesu Email</label>
                        <input type="email" id="visitor-email" placeholder="joao@example.tl">
                    </div>
                </div>
                <div class="form-group">
                    <label>Objetivu Vizita *</label>
                    <input type="text" id="visitor-purpose" required placeholder="ex. Konsultasaun Mesa Teak Ezekutivu">
                </div>
                <div class="form-group">
                    <label>Ema Atu Hasoru *</label>
                    <select id="visitor-person" required>
                        <option value="Manuel Santos (Senior Sales Manager)">Manuel Santos (Senior Sales Manager)</option>
                        <option value="Filomena Soares (Interior Designer)">Filomena Soares (Interior Designer)</option>
                        <option value="Antonio Guterres (Procurement Rep)">Antonio Guterres (Procurement Rep)</option>
                        <option value="Reception / Showroom Tour">Reception / Showroom Tour</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeVisitorModal()">Kansela</button>
                <button type="submit" class="btn btn-timor-red">Rejista & Tama</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Appointment Modal -->
<div id="appointment-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="appointment-modal-title">Kria Agendasaun</h3>
            <button class="modal-close-btn" onclick="closeAppointmentModal()">&times;</button>
        </div>
        <form id="appointment-form" onsubmit="saveAppointment(event)">
            <div class="modal-body">
                <input type="hidden" id="appointment-id" value="0">
                <div class="form-group">
                    <label>Naran Kliente *</label>
                    <input type="text" id="app-customer-name" required placeholder="ex. Ministériu Infraestrutura">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Numeru Telefone *</label>
                        <input type="text" id="app-phone" required placeholder="+670 7711 2233">
                    </div>
                    <div class="form-group">
                        <label>Enderesu Email</label>
                        <input type="email" id="app-email" placeholder="contact@example.tl">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Data Agendasaun *</label>
                        <input type="date" id="appointment-date" required>
                    </div>
                    <div class="form-group">
                        <label>Oras Agendasaun *</label>
                        <input type="time" id="appointment-time" required value="10:00">
                    </div>
                </div>
                <div class="form-group">
                    <label>Objetivu / Tópiku Konsultasaun *</label>
                    <input type="text" id="app-purpose" required placeholder="ex. Espesifikasaun Mobília Salaun Konferénsia">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Representante Atribuídu *</label>
                        <select id="app-sales-rep" required>
                            <option value="Manuel Santos">Manuel Santos</option>
                            <option value="Filomena Soares">Filomena Soares</option>
                            <option value="Antonio Guterres">Antonio Guterres</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estadu</label>
                        <select id="app-status">
                            <option value="Scheduled">Agendadu</option>
                            <option value="In Progress">Iha Progresu</option>
                            <option value="Completed">Kompletu</option>
                            <option value="Cancelled">Kansela</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nota</label>
                    <textarea id="app-notes" rows="2" placeholder="Rekizitu espesiál ka nota kliente..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeAppointmentModal()">Kansela</button>
                <button type="submit" class="btn btn-timor-red">Salva Agendasaun</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Customer Modal -->
<div id="customer-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="customer-modal-title">Aumenta Kliente Foun</h3>
            <button class="modal-close-btn" onclick="closeCustomerModal()">&times;</button>
        </div>
        <form id="customer-form" onsubmit="saveCustomer(event)">
            <div class="modal-body">
                <input type="hidden" id="customer-id" value="0">
                <div class="form-group">
                    <label>Naran Kompletu Kliente *</label>
                    <input type="text" id="cust-name" required placeholder="ex. Dr. Francisco Guterres">
                </div>
                <div class="form-group">
                    <label>Naran Kompañia / Organizasaun</label>
                    <input type="text" id="cust-company" placeholder="ex. Timor Plaza Group">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Numeru Telefone *</label>
                        <input type="text" id="cust-phone" required placeholder="+670 7722 1100">
                    </div>
                    <div class="form-group">
                        <label>Enderesu Email</label>
                        <input type="email" id="cust-email" placeholder="fguterres@clinic.tl">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Enderesu *</label>
                        <input type="text" id="cust-address" required placeholder="Farol, Dili">
                    </div>
                    <div class="form-group">
                        <label>Sidade / Distritu</label>
                        <input type="text" id="cust-city" value="Dili">
                    </div>
                </div>
                <div class="form-group">
                    <label>Resumo Istória Sosa</label>
                    <input type="text" id="cust-history" placeholder="ex. Sosa Kama Teak Real Espesiál">
                </div>
                <div class="form-group">
                    <label>Nota</label>
                    <textarea id="cust-notes" rows="2" placeholder="Preferénsia kliente..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeCustomerModal()">Kansela</button>
                <button type="submit" class="btn btn-timor-red">Salva Kliente</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Showroom Visit Modal -->
<div id="showroom-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="showroom-modal-title">Agendariza Vizita Salaun Exposisaun</h3>
            <button class="modal-close-btn" onclick="closeShowroomModal()">&times;</button>
        </div>
        <form id="showroom-form" onsubmit="saveShowroom(event)">
            <div class="modal-body">
                <input type="hidden" id="showroom-id" value="0">
                <div class="form-group">
                    <label>Naran Vizitante / Kliente *</label>
                    <input type="text" id="show-visitor-name" required placeholder="ex. Pedro Martins">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Numeru Telefone *</label>
                        <input type="text" id="show-phone" required placeholder="+670 7799 3322">
                    </div>
                    <div class="form-group">
                        <label>Data Vizita *</label>
                        <input type="date" id="showroom-visit-date" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Interese Koleksaun Mobília *</label>
                    <input type="text" id="show-furniture" required placeholder="ex. Mesa Jantar Teak Sólidu (8 Kadeira)">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Representante Atribuídu *</label>
                        <select id="show-assigned-rep" required>
                            <option value="Manuel Santos">Manuel Santos</option>
                            <option value="Filomena Soares">Filomena Soares</option>
                            <option value="Antonio Guterres">Antonio Guterres</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estadu</label>
                        <select id="show-status">
                            <option value="In Showroom">Iha Salaun</option>
                            <option value="Scheduled">Agendadu</option>
                            <option value="Completed">Kompletu</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nota Konsultasaun</label>
                    <textarea id="show-notes" rows="2" placeholder="Modelu mobília espesífiku ka dimensaun..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeShowroomModal()">Kansela</button>
                <button type="submit" class="btn btn-timor-red">Agendariza Vizita</button>
            </div>
        </form>
    </div>
</div>

<!-- Login Overlay Screen (Hidden by Default for convenience, controllable via Logout) -->
<div id="login-screen" class="login-screen" style="display:none;">
    <div class="login-card">
        <div class="brand-logo-badge">🇹🇱</div>
        <h2 style="font-size:1.4rem; color:var(--timor-black); margin-bottom:0.25rem;">Timor Teak Furniture Co.</h2>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.5rem;">Autentikasaun Portal Resepsionista</p>

        <div id="login-error" style="display:none; padding:0.65rem; background:var(--timor-red-light); color:var(--timor-red); border-radius:8px; font-size:0.85rem; margin-bottom:1rem; font-weight:600;"></div>

        <form onsubmit="handleLogin(event)" style="display:flex; flex-direction:column; gap:1rem;">
            <div class="form-group">
                <label style="text-align:left;">Naran Utilizadór</label>
                <input type="text" id="login-username" value="receptionist" required>
            </div>
            <div class="form-group">
                <label style="text-align:left;">Liafuan-passe</label>
                <input type="password" id="login-password" value="timor2026" required>
            </div>
            <button type="submit" class="btn btn-timor-red" style="width:100%; justify-content:center; padding:0.8rem;">Tama ba Painel</button>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<!-- Application JS -->
<script src="assets/js/app.js"></script>

</body>
</html>
