// assets/js/app.js - Timor Furniture Receptionist Dashboard Application Logic

document.addEventListener('DOMContentLoaded', () => {
    initClock();
    initNavigation();
    initLanguageAndTheme();
    loadDashboardStats();
    loadVisitors();
    loadAppointments();
    loadCustomers();
    loadShowroom();
    loadNotifications();
    renderCalendar();

    // Global Search Bar Handlers
    document.getElementById('visitor-search')?.addEventListener('input', debounce((e) => loadVisitors(e.target.value), 300));
    document.getElementById('appointment-search')?.addEventListener('input', debounce((e) => loadAppointments(e.target.value), 300));
    document.getElementById('customer-search')?.addEventListener('input', debounce((e) => loadCustomers(e.target.value), 300));
    document.getElementById('showroom-search')?.addEventListener('input', debounce((e) => loadShowroom(e.target.value), 300));
});

// Helper: Debounce function for smooth search typing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// -------------------------------------------------------------
// LIVE CLOCK & DATE
// -------------------------------------------------------------
function initClock() {
    const clockEl = document.getElementById('live-clock');
    if (!clockEl) return;

    function update() {
        const now = new Date();
        const opts = { weekday: 'short', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        clockEl.textContent = now.toLocaleDateString('en-US', opts);
    }
    update();
    setInterval(update, 1000);
}

function initLanguageAndTheme() {
    const html = document.documentElement;
    const langToggle = document.getElementById('lang-toggle-btn');
    const themeToggle = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-toggle-icon');
    const themeLabel = document.getElementById('theme-toggle-label');
    const langLabel = document.getElementById('lang-toggle-label');

    const translations = {
        tet: {
            'lang.toggle': 'Tetun / EN',
            'theme.toggle': 'Hitu / Putih',
            'nav.category.main': 'Menu Prinsipál',
            'nav.category.communication': 'Komunikasaun & Atividade',
            'nav.category.system': 'Sistema & Konta',
            'nav.dashboard': 'Painel',
            'nav.visitors': 'Jestaun Vizitante',
            'nav.appointments': 'Jestaun Agendasaun',
            'nav.customers': 'Jestaun Kliente',
            'nav.showroom': 'Salaun Exposisaun Mobília',
            'nav.messages': 'Mensajen',
            'nav.notifications': 'Notifikasaun',
            'nav.reports': 'Relatóriu & Esporta',
            'nav.profile': 'Perfíl Kompañia',
            'nav.settings': 'Konfigurasaun',
            'nav.logout': 'Sai',
            'page.dashboard.title': 'Vizualizasaun Painel',
            'page.dashboard.subtitle': 'Bem-fali, Maria Silva! Iha ne\'e vizualizasaun resepsaun ohin.'
        },
        en: {
            'lang.toggle': 'EN / Tetun',
            'theme.toggle': 'Black / White',
            'nav.category.main': 'Main Menu',
            'nav.category.communication': 'Communication & Activity',
            'nav.category.system': 'System & Account',
            'nav.dashboard': 'Dashboard',
            'nav.visitors': 'Visitor Management',
            'nav.appointments': 'Appointment Management',
            'nav.customers': 'Customer Management',
            'nav.showroom': 'Furniture Showroom',
            'nav.messages': 'Messages',
            'nav.notifications': 'Notifications',
            'nav.reports': 'Reports & Export',
            'nav.profile': 'Company Profile',
            'nav.settings': 'Settings',
            'nav.logout': 'Logout',
            'page.dashboard.title': 'Dashboard Overview',
            'page.dashboard.subtitle': 'Welcome, Maria Silva! Here is today\'s reception overview.'
        }
    };

    const savedLang = localStorage.getItem('dashboard-lang') || 'tet';
    const savedTheme = localStorage.getItem('dashboard-theme') || 'dark';

    function applyLanguage(lang) {
        const dict = translations[lang] || translations.tet;
        document.querySelectorAll('[data-i18n-key]').forEach(el => {
            const key = el.getAttribute('data-i18n-key');
            if (dict[key]) el.textContent = dict[key];
        });
        html.setAttribute('data-app-lang', lang);
        html.setAttribute('lang', lang);
        if (langLabel) langLabel.textContent = lang === 'tet' ? 'Tetun / EN' : 'EN / Tetun';
        localStorage.setItem('dashboard-lang', lang);
    }

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        if (themeIcon) themeIcon.textContent = theme === 'light' ? '☀' : '☾';
        if (themeLabel) themeLabel.textContent = theme === 'light' ? 'Black / White' : 'Black / White';
        localStorage.setItem('dashboard-theme', theme);
    }

    if (langToggle) {
        langToggle.addEventListener('click', () => {
            const nextLang = html.getAttribute('data-app-lang') === 'en' ? 'tet' : 'en';
            applyLanguage(nextLang);
        });
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const nextTheme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            applyTheme(nextTheme);
        });
    }

    applyLanguage(savedLang);
    applyTheme(savedTheme);
}

// -------------------------------------------------------------
// NAVIGATION HANDLER (11 LINKS)
// -------------------------------------------------------------
function initNavigation() {
    const links = document.querySelectorAll('.nav-link');
    const views = document.querySelectorAll('.view-section');
    const pageTitle = document.getElementById('page-title-text');
    const pageSub = document.getElementById('page-subtitle');

    const meta = {
        'dashboard': { title: 'Vizualizasaun Painel', sub: 'Bem-fali, Maria Silva! Iha ne\'e vizualizasaun resepsaun ohin.' },
        'visitors': { title: 'Jestaun Vizitante', sub: 'Rejista, tama, sai, no haree rejistru vizitante.' },
        'appointments': { title: 'Jestaun Agendasaun', sub: 'Jesta horária kliente, vizita salaun, no slot konsultasaun.' },
        'customers': { title: 'Diretoria Kliente', sub: 'Haree konta kliente, kontaktu kompañia, no istória sosa.' },
        'showroom': { title: 'Vizita Salaun Exposisaun Mobília', sub: 'Monitoriza vizita salaun ativu no konsultasaun representante vendas.' },
        'messages': { title: 'Mensajen & Peskiza', sub: 'Komunikasaun resepsaun interna no mensajen kliente.' },
        'notifications': { title: 'Notifikasaun Sistema', sub: 'Alerta chegada real-time, lembraça agendasaun, no rejistru.' },
        'reports': { title: 'Esporta Relatóriu & Analítika', sub: 'Jera relatóriu atividade resepsaun lorón/fulan no esporta ba PDF/Excel.' },
        'profile': { title: 'Perfíl Utilizadór Resepsionista', sub: 'Jesta informasaun perfíl konta no kredensiais seguransa.' },
        'settings': { title: 'Konfigurasaun Sistema & Kompañia', sub: 'Konfigura enderesu kompañia, oras negósiu, no preferénsia resepsaun.' }
    };

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetView = link.getAttribute('data-view');

            if (targetView === 'logout') {
                handleLogout();
                return;
            }

            links.forEach(l => l.classList.remove('active'));
            views.forEach(v => v.classList.remove('active'));

            link.classList.add('active');
            document.getElementById(`view-${targetView}`)?.classList.add('active');

            if (meta[targetView]) {
                if (pageTitle) pageTitle.textContent = meta[targetView].title;
                if (pageSub) pageSub.textContent = meta[targetView].sub;
            }

            // Refresh specific view data
            if (targetView === 'dashboard') loadDashboardStats();
            else if (targetView === 'visitors') loadVisitors();
            else if (targetView === 'appointments') loadAppointments();
            else if (targetView === 'customers') loadCustomers();
            else if (targetView === 'showroom') loadShowroom();
            else if (targetView === 'notifications') loadNotifications();
            else if (targetView === 'reports') loadReports();
            else if (targetView === 'settings') loadSettings();
        });
    });
}

// -------------------------------------------------------------
// TOAST NOTIFICATIONS
// -------------------------------------------------------------
function showToast(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast-msg`;
    if (type === 'error') toast.style.borderLeftColor = 'var(--timor-red)';
    toast.textContent = msg;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Security HTML escaper
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// -------------------------------------------------------------
// 1. DASHBOARD STATS & RECENT FEED
// -------------------------------------------------------------
async function loadDashboardStats() {
    try {
        const res = await fetch('api/dashboard_api.php?action=stats');
        const json = await res.json();

        if (json.status === 'success') {
            const d = json.data;
            document.getElementById('stat-visitors-today').textContent = d.visitors_today;
            document.getElementById('stat-appointments-today').textContent = d.appointments_today;
            document.getElementById('stat-pending-appointments').textContent = d.pending_appointments;
            document.getElementById('stat-checked-in').textContent = d.checked_in_customers;
            document.getElementById('stat-completed-visits').textContent = d.completed_appointments;

            renderRecentVisitorsOverview(d.recent_visitors);
            renderUpcomingAppointmentsOverview(d.upcoming_appointments);
        }
    } catch (err) {
        console.error('Failed to load dashboard stats:', err);
    }
}

function renderRecentVisitorsOverview(visitors) {
    const tbody = document.getElementById('overview-visitors-tbody');
    if (!tbody) return;

    if (!visitors || visitors.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Vizitante sira la'o rejistadu ohin.</td></tr>`;
        return;
    }

    tbody.innerHTML = visitors.map(v => `
        <tr>
            <td><strong>${escapeHtml(v.visitor_id)}</strong></td>
            <td>${escapeHtml(v.full_name)}</td>
            <td>${escapeHtml(v.purpose)}</td>
            <td>${escapeHtml(v.person_to_meet)}</td>
            <td><span class="badge ${v.status === 'Checked In' ? 'badge-checked-in' : (v.status === 'Checked Out' ? 'badge-checked-out' : 'badge-waiting')}">${escapeHtml(v.status)}</span></td>
        </tr>
    `).join('');
}

function renderUpcomingAppointmentsOverview(appts) {
    const list = document.getElementById('overview-appointments-list');
    if (!list) return;

    if (!appts || appts.length === 0) {
        list.innerHTML = `<div style="text-align:center; color:var(--text-muted); padding:1rem;">La iha agendasaun tuir mai.</div>`;
        return;
    }

    list.innerHTML = appts.map(a => `
        <div style="display:flex; align-items:center; justify-content:space-between; padding:0.75rem; background:#f8fafc; border-radius:8px; border:1px solid var(--border-color);">
            <div>
                <h5 style="font-size:0.88rem; color:var(--timor-black);">${escapeHtml(a.customer_name)}</h5>
                <p style="font-size:0.76rem; color:var(--text-muted);">${escapeHtml(a.purpose)} • Meet: ${escapeHtml(a.sales_rep)}</p>
            </div>
            <span class="badge badge-scheduled">${escapeHtml(a.appointment_time.substring(0, 5))}</span>
        </div>
    `).join('');
}

// -------------------------------------------------------------
// 2. VISITORS MANAGEMENT CRUD
// -------------------------------------------------------------
async function loadVisitors(search = '') {
    try {
        const res = await fetch(`api/dashboard_api.php?action=visitors&search=${encodeURIComponent(search)}`);
        const json = await res.json();

        if (json.status === 'success') {
            renderVisitorsTable(json.data);
        }
    } catch (err) {
        console.error('Failed to load visitors:', err);
    }
}

function renderVisitorsTable(visitors) {
    const tbody = document.getElementById('visitors-table-body');
    if (!tbody) return;

    if (!visitors || visitors.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">La iha rejistru vizitante.</td></tr>`;
        return;
    }

    tbody.innerHTML = visitors.map(v => `
        <tr>
            <td><strong>${escapeHtml(v.visitor_id)}</strong></td>
            <td>
                <strong>${escapeHtml(v.full_name)}</strong><br>
                <span style="font-size:0.78rem; color:var(--text-muted);">${escapeHtml(v.phone)}</span>
            </td>
            <td>${escapeHtml(v.purpose)}</td>
            <td>${escapeHtml(v.person_to_meet)}</td>
            <td>${new Date(v.check_in).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</td>
            <td><span class="badge ${v.status === 'Checked In' ? 'badge-checked-in' : (v.status === 'Checked Out' ? 'badge-checked-out' : 'badge-waiting')}">${escapeHtml(v.status)}</span></td>
            <td>
                <div style="display:flex; gap:0.4rem;">
                    ${v.status === 'Checked In' ? `<button class="btn btn-outline btn-sm" onclick="checkOutVisitor(${v.id})">Sai</button>` : ''}
                    <button class="btn btn-outline btn-sm" onclick="editVisitor(${v.id}, '${escapeHtml(v.full_name)}', '${escapeHtml(v.phone)}', '${escapeHtml(v.email)}', '${escapeHtml(v.purpose)}', '${escapeHtml(v.person_to_meet)}')">Edita</button>
                    <button class="btn btn-outline btn-sm" style="color:var(--timor-red);" onclick="deleteVisitor(${v.id})">Hamoos</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openVisitorModal(id = 0) {
    const modal = document.getElementById('visitor-modal');
    document.getElementById('visitor-form').reset();
    document.getElementById('visitor-id').value = id;
    document.getElementById('visitor-modal-title').textContent = id > 0 ? 'Edita Vizitante' : 'Rejista Vizitante Foun';
    modal.classList.add('open');
}

function closeVisitorModal() {
    document.getElementById('visitor-modal').classList.remove('open');
}

function editVisitor(id, name, phone, email, purpose, person) {
    openVisitorModal(id);
    document.getElementById('visitor-name').value = name;
    document.getElementById('visitor-phone').value = phone;
    document.getElementById('visitor-email').value = email;
    document.getElementById('visitor-purpose').value = purpose;
    document.getElementById('visitor-person').value = person;
}

async function saveVisitor(e) {
    e.preventDefault();
    const payload = {
        id: document.getElementById('visitor-id').value,
        full_name: document.getElementById('visitor-name').value,
        phone: document.getElementById('visitor-phone').value,
        email: document.getElementById('visitor-email').value,
        purpose: document.getElementById('visitor-purpose').value,
        person_to_meet: document.getElementById('visitor-person').value
    };

    try {
        const res = await fetch('api/dashboard_api.php?action=visitors', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            closeVisitorModal();
            loadVisitors();
            loadDashboardStats();
        } else {
            showToast(json.message, 'error');
        }
    } catch (err) {
        showToast('Error saving visitor.', 'error');
    }
}

async function checkOutVisitor(id) {
    try {
        const res = await fetch('api/dashboard_api.php?action=visitors', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sub_action: 'checkout', id: id })
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadVisitors();
            loadDashboardStats();
        }
    } catch (err) {
        showToast('Sai falla.', 'error');
    }
}

async function deleteVisitor(id) {
    if (!confirm('Ita-boot iha serteza atu hamoos entrada vizitante nee?')) return;
    try {
        const res = await fetch('api/dashboard_api.php?action=visitors', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sub_action: 'delete', id: id })
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadVisitors();
            loadDashboardStats();
        }
    } catch (err) {
        showToast('Hamoos falla.', 'error');
    }
}

// -------------------------------------------------------------
// 3. APPOINTMENTS CRUD
// -------------------------------------------------------------
async function loadAppointments(search = '') {
    try {
        const res = await fetch(`api/dashboard_api.php?action=appointments&search=${encodeURIComponent(search)}`);
        const json = await res.json();

        if (json.status === 'success') {
            renderAppointmentsTable(json.data);
        }
    } catch (err) {
        console.error('Failed to load appointments:', err);
    }
}

function renderAppointmentsTable(appts) {
    const tbody = document.getElementById('appointments-table-body');
    if (!tbody) return;

    if (!appts || appts.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">La iha agendasaun.</td></tr>`;
        return;
    }

    tbody.innerHTML = appts.map(a => `
        <tr>
            <td>
                <strong>${escapeHtml(a.customer_name)}</strong><br>
                <span style="font-size:0.78rem; color:var(--text-muted);">${escapeHtml(a.phone)}</span>
            </td>
            <td><strong>${a.appointment_date}</strong> at ${a.appointment_time.substring(0,5)}</td>
            <td>${escapeHtml(a.purpose)}</td>
            <td>${escapeHtml(a.sales_rep)}</td>
            <td><span class="badge ${a.status === 'In Progress' ? 'badge-checked-in' : (a.status === 'Cancelled' ? 'badge-cancelled' : 'badge-scheduled')}">${escapeHtml(a.status)}</span></td>
            <td>${escapeHtml(a.notes || '-')}</td>
            <td>
                <div style="display:flex; gap:0.4rem;">
                    <button class="btn btn-outline btn-sm" onclick="editAppointment(${a.id}, '${escapeHtml(a.customer_name)}', '${escapeHtml(a.phone)}', '${escapeHtml(a.email)}', '${a.appointment_date}', '${a.appointment_time}', '${escapeHtml(a.purpose)}', '${escapeHtml(a.sales_rep)}', '${escapeHtml(a.status)}', '${escapeHtml(a.notes || '')}')">Edita</button>
                    <button class="btn btn-outline btn-sm" style="color:var(--timor-red);" onclick="deleteAppointment(${a.id})">Hamoos</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openAppointmentModal(id = 0) {
    const modal = document.getElementById('appointment-modal');
    document.getElementById('appointment-form').reset();
    document.getElementById('appointment-id').value = id;
    document.getElementById('appointment-date').valueAsDate = new Date();
    document.getElementById('appointment-modal-title').textContent = id > 0 ? 'Edita Agendasaun' : 'Kria Agendasaun Foun';
    modal.classList.add('open');
}

function closeAppointmentModal() {
    document.getElementById('appointment-modal').classList.remove('open');
}

function editAppointment(id, name, phone, email, date, time, purpose, rep, status, notes) {
    openAppointmentModal(id);
    document.getElementById('app-customer-name').value = name;
    document.getElementById('app-phone').value = phone;
    document.getElementById('app-email').value = email;
    document.getElementById('appointment-date').value = date;
    document.getElementById('appointment-time').value = time;
    document.getElementById('app-purpose').value = purpose;
    document.getElementById('app-sales-rep').value = rep;
    document.getElementById('app-status').value = status;
    document.getElementById('app-notes').value = notes;
}

async function saveAppointment(e) {
    e.preventDefault();
    const payload = {
        id: document.getElementById('appointment-id').value,
        customer_name: document.getElementById('app-customer-name').value,
        phone: document.getElementById('app-phone').value,
        email: document.getElementById('app-email').value,
        appointment_date: document.getElementById('appointment-date').value,
        appointment_time: document.getElementById('appointment-time').value,
        purpose: document.getElementById('app-purpose').value,
        sales_rep: document.getElementById('app-sales-rep').value,
        status: document.getElementById('app-status').value,
        notes: document.getElementById('app-notes').value
    };

    try {
        const res = await fetch('api/dashboard_api.php?action=appointments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            closeAppointmentModal();
            loadAppointments();
            loadDashboardStats();
        } else {
            showToast(json.message, 'error');
        }
    } catch (err) {
        showToast('Errr atu salva agendasaun.', 'error');
    }
}

async function deleteAppointment(id) {
    if (!confirm('Ita-boot iha serteza atu hamoos agendasaun nee?')) return;
    try {
        const res = await fetch('api/dashboard_api.php?action=appointments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sub_action: 'delete', id: id })
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadAppointments();
            loadDashboardStats();
        }
    } catch (err) {
        showToast('Hamoos falla.', 'error');
    }
}

// -------------------------------------------------------------
// 4. CUSTOMER MANAGEMENT CRUD
// -------------------------------------------------------------
async function loadCustomers(search = '') {
    try {
        const res = await fetch(`api/dashboard_api.php?action=customers&search=${encodeURIComponent(search)}`);
        const json = await res.json();

        if (json.status === 'success') {
            renderCustomersTable(json.data);
        }
    } catch (err) {
        console.error('Failed to load customers:', err);
    }
}

function renderCustomersTable(customers) {
    const tbody = document.getElementById('customers-table-body');
    if (!tbody) return;

    if (!customers || customers.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">La iha rejistru kliente.</td></tr>`;
        return;
    }

    tbody.innerHTML = customers.map(c => `
        <tr>
            <td><strong>${escapeHtml(c.customer_code)}</strong></td>
            <td>
                <strong>${escapeHtml(c.full_name)}</strong><br>
                <span style="font-size:0.78rem; color:var(--timor-red); font-weight:600;">${escapeHtml(c.company_name || 'Kliente Individual')}</span>
            </td>
            <td>${escapeHtml(c.phone)}<br><span style="font-size:0.75rem; color:var(--text-muted);">${escapeHtml(c.email || '')}</span></td>
            <td>${escapeHtml(c.address)}, ${escapeHtml(c.city)}</td>
            <td>${escapeHtml(c.purchase_history || 'La iha sosa rejistadu')}</td>
            <td>${escapeHtml(c.notes || '-')}</td>
            <td>
                <div style="display:flex; gap:0.4rem;">
                    <button class="btn btn-outline btn-sm" onclick="editCustomer(${c.id}, '${escapeHtml(c.full_name)}', '${escapeHtml(c.company_name || '')}', '${escapeHtml(c.phone)}', '${escapeHtml(c.email || '')}', '${escapeHtml(c.address)}', '${escapeHtml(c.city)}', '${escapeHtml(c.purchase_history || '')}', '${escapeHtml(c.notes || '')}')">Edita</button>
                    <button class="btn btn-outline btn-sm" style="color:var(--timor-red);" onclick="deleteCustomer(${c.id})">Hamoos</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openCustomerModal(id = 0) {
    const modal = document.getElementById('customer-modal');
    document.getElementById('customer-form').reset();
    document.getElementById('customer-id').value = id;
    document.getElementById('customer-modal-title').textContent = id > 0 ? 'Edita Kliente' : 'Aumenta Kliente Foun';
    modal.classList.add('open');
}

function closeCustomerModal() {
    document.getElementById('customer-modal').classList.remove('open');
}

function editCustomer(id, name, company, phone, email, address, city, history, notes) {
    openCustomerModal(id);
    document.getElementById('cust-name').value = name;
    document.getElementById('cust-company').value = company;
    document.getElementById('cust-phone').value = phone;
    document.getElementById('cust-email').value = email;
    document.getElementById('cust-address').value = address;
    document.getElementById('cust-city').value = city;
    document.getElementById('cust-history').value = history;
    document.getElementById('cust-notes').value = notes;
}

async function saveCustomer(e) {
    e.preventDefault();
    const payload = {
        id: document.getElementById('customer-id').value,
        full_name: document.getElementById('cust-name').value,
        company_name: document.getElementById('cust-company').value,
        phone: document.getElementById('cust-phone').value,
        email: document.getElementById('cust-email').value,
        address: document.getElementById('cust-address').value,
        city: document.getElementById('cust-city').value,
        purchase_history: document.getElementById('cust-history').value,
        notes: document.getElementById('cust-notes').value
    };

    try {
        const res = await fetch('api/dashboard_api.php?action=customers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            closeCustomerModal();
            loadCustomers();
        } else {
            showToast(json.message, 'error');
        }
    } catch (err) {
        showToast('Errr atu salva kliente.', 'error');
    }
}

async function deleteCustomer(id) {
    if (!confirm('Ita-boot iha serteza atu hamoos kliente nee?')) return;
    try {
        const res = await fetch('api/dashboard_api.php?action=customers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sub_action: 'delete', id: id })
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadCustomers();
        }
    } catch (err) {
        showToast('Hamoos falla.', 'error');
    }
}

// -------------------------------------------------------------
// 5. SHOWROOM VISITS CRUD
// -------------------------------------------------------------
async function loadShowroom(search = '') {
    try {
        const res = await fetch(`api/dashboard_api.php?action=showroom&search=${encodeURIComponent(search)}`);
        const json = await res.json();

        if (json.status === 'success') {
            renderShowroomTable(json.data);
        }
    } catch (err) {
        console.error('Failed to load showroom visits:', err);
    }
}

function renderShowroomTable(showroom) {
    const tbody = document.getElementById('showroom-table-body');
    if (!tbody) return;

    if (!showroom || showroom.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">La iha vizita salaun agendadu.</td></tr>`;
        return;
    }

    tbody.innerHTML = showroom.map(s => `
        <tr>
            <td>
                <strong>${escapeHtml(s.visitor_name)}</strong><br>
                <span style="font-size:0.78rem; color:var(--text-muted);">${escapeHtml(s.phone)}</span>
            </td>
            <td><strong>${s.visit_date}</strong></td>
            <td><span class="badge badge-vip">${escapeHtml(s.furniture_interest)}</span></td>
            <td>${escapeHtml(s.assigned_rep)}</td>
            <td><span class="badge ${s.status === 'In Showroom' ? 'badge-checked-in' : (s.status === 'Completed' ? 'badge-checked-out' : 'badge-scheduled')}">${escapeHtml(s.status)}</span></td>
            <td>${escapeHtml(s.notes || '-')}</td>
            <td>
                <div style="display:flex; gap:0.4rem;">
                    <button class="btn btn-outline btn-sm" onclick="editShowroom(${s.id}, '${escapeHtml(s.visitor_name)}', '${escapeHtml(s.phone)}', '${s.visit_date}', '${escapeHtml(s.furniture_interest)}', '${escapeHtml(s.assigned_rep)}', '${escapeHtml(s.status)}', '${escapeHtml(s.notes || '')}')">Edita</button>
                    <button class="btn btn-outline btn-sm" style="color:var(--timor-red);" onclick="deleteShowroom(${s.id})">Hamoos</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openShowroomModal(id = 0) {
    const modal = document.getElementById('showroom-modal');
    document.getElementById('showroom-form').reset();
    document.getElementById('showroom-id').value = id;
    document.getElementById('showroom-visit-date').valueAsDate = new Date();
    document.getElementById('showroom-modal-title').textContent = id > 0 ? 'Edita Vizita Salaun' : 'Agendariza Vizita Salaun Exposisaun';
    modal.classList.add('open');
}

function closeShowroomModal() {
    document.getElementById('showroom-modal').classList.remove('open');
}

function editShowroom(id, name, phone, date, interest, rep, status, notes) {
    openShowroomModal(id);
    document.getElementById('show-visitor-name').value = name;
    document.getElementById('show-phone').value = phone;
    document.getElementById('showroom-visit-date').value = date;
    document.getElementById('show-furniture').value = interest;
    document.getElementById('show-assigned-rep').value = rep;
    document.getElementById('show-status').value = status;
    document.getElementById('show-notes').value = notes;
}

async function saveShowroom(e) {
    e.preventDefault();
    const payload = {
        id: document.getElementById('showroom-id').value,
        visitor_name: document.getElementById('show-visitor-name').value,
        phone: document.getElementById('show-phone').value,
        visit_date: document.getElementById('showroom-visit-date').value,
        furniture_interest: document.getElementById('show-furniture').value,
        assigned_rep: document.getElementById('show-assigned-rep').value,
        status: document.getElementById('show-status').value,
        notes: document.getElementById('show-notes').value
    };

    try {
        const res = await fetch('api/dashboard_api.php?action=showroom', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            closeShowroomModal();
            loadShowroom();
        } else {
            showToast(json.message, 'error');
        }
    } catch (err) {
        showToast('Errr atu salva vizita salaun.', 'error');
    }
}

async function deleteShowroom(id) {
    if (!confirm('Ita-boot iha serteza atu hamoos rejistru salaun nee?')) return;
    try {
        const res = await fetch('api/dashboard_api.php?action=showroom', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sub_action: 'delete', id: id })
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadShowroom();
        }
    } catch (err) {
        showToast('Hamoos falla.', 'error');
    }
}

// -------------------------------------------------------------
// 6. NOTIFICATIONS
// -------------------------------------------------------------
async function loadNotifications() {
    try {
        const res = await fetch('api/dashboard_api.php?action=notifications');
        const json = await res.json();

        if (json.status === 'success') {
            const list = document.getElementById('notifications-list');
            if (list) {
                list.innerHTML = json.data.map(n => `
                    <div style="padding:1rem; background:#fff; border:1px solid var(--border-color); border-radius:12px; border-left:4px solid var(--timor-red); display:flex; flex-direction:column; gap:0.25rem;">
                        <div style="display:flex; justify-content:space-between;">
                            <strong style="font-size:0.9rem; color:var(--timor-black);">${escapeHtml(n.title)}</strong>
                            <span style="font-size:0.75rem; color:var(--text-muted);">${new Date(n.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</span>
                        </div>
                        <p style="font-size:0.85rem; color:var(--text-main);">${escapeHtml(n.message)}</p>
                    </div>
                `).join('');
            }
        }
    } catch (err) {
        console.error('Failed to load notifications:', err);
    }
}

// -------------------------------------------------------------
// 7. REPORTS & EXPORTS (PDF / EXCEL)
// -------------------------------------------------------------
async function loadReports() {
    try {
        const res = await fetch('api/dashboard_api.php?action=reports');
        const json = await res.json();

        if (json.status === 'success') {
            window.latestReportData = json.data;
            const container = document.getElementById('reports-summary-container');
            if (container) {
                container.innerHTML = `
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1.25rem; margin-bottom:1.5rem;">
                        <div style="background:#fff; border:1px solid var(--border-color); padding:1.25rem; border-radius:12px; text-align:center;">
                            <span style="font-size:0.8rem; color:var(--text-muted);">Total Vizitante Rejistadu</span>
                            <h2 style="font-size:2rem; color:var(--timor-red);">${json.data.visitors.length}</h2>
                        </div>
                        <div style="background:#fff; border:1px solid var(--border-color); padding:1.25rem; border-radius:12px; text-align:center;">
                            <span style="font-size:0.8rem; color:var(--text-muted);">Total Agendasaun</span>
                            <h2 style="font-size:2rem; color:var(--timor-black);">${json.data.appointments.length}</h2>
                        </div>
                        <div style="background:#fff; border:1px solid var(--border-color); padding:1.25rem; border-radius:12px; text-align:center;">
                            <span style="font-size:0.8rem; color:var(--text-muted);">Konsultasaun Salaun</span>
                            <h2 style="font-size:2rem; color:var(--timor-yellow);">${json.data.showroom.length}</h2>
                        </div>
                    </div>
                `;
            }
        }
    } catch (err) {
        console.error('Failed to load reports:', err);
    }
}

function exportReportExcel() {
    if (!window.latestReportData || !window.latestReportData.visitors) {
        showToast('La iha dadus relatóriu disponivel atu esporta.', 'error');
        return;
    }

    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Visitor ID,Full Name,Phone,Email,Purpose,Person To Meet,Check In,Check Out,Status\n";

    window.latestReportData.visitors.forEach(v => {
        csvContent += `"${v.visitor_id}","${v.full_name}","${v.phone}","${v.email || ''}","${v.purpose}","${v.person_to_meet}","${v.check_in}","${v.check_out || ''}","${v.status}"\n`;
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `Timor_Furniture_Reception_Report_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('Relatóriu esportadu hanesan Excel CSV!');
}

function exportReportPDF() {
    window.print();
}

// -------------------------------------------------------------
// 8. SETTINGS & PROFILE
// -------------------------------------------------------------
async function loadSettings() {
    try {
        const res = await fetch('api/dashboard_api.php?action=settings');
        const json = await res.json();

        if (json.status === 'success') {
            const data = json.data;
            if (document.getElementById('set-company-name')) document.getElementById('set-company-name').value = data.company_name || '';
            if (document.getElementById('set-company-phone')) document.getElementById('set-company-phone').value = data.company_phone || '';
            if (document.getElementById('set-company-email')) document.getElementById('set-company-email').value = data.company_email || '';
            if (document.getElementById('set-company-address')) document.getElementById('set-company-address').value = data.company_address || '';
            if (document.getElementById('set-business-hours')) document.getElementById('set-business-hours').value = data.business_hours || '';
            if (document.getElementById('set-receptionist-name')) document.getElementById('set-receptionist-name').value = data.receptionist_name || '';
        }
    } catch (err) {
        console.error('Failed to load settings:', err);
    }
}

async function saveSettingsForm(e) {
    e.preventDefault();
    const payload = {
        company_name: document.getElementById('set-company-name').value,
        company_phone: document.getElementById('set-company-phone').value,
        company_email: document.getElementById('set-company-email').value,
        company_address: document.getElementById('set-company-address').value,
        business_hours: document.getElementById('set-business-hours').value,
        receptionist_name: document.getElementById('set-receptionist-name').value
    };

    try {
        const res = await fetch('api/dashboard_api.php?action=settings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
        }
    } catch (err) {
        showToast('Errr atu salva konfigurasaun.', 'error');
    }
}

// -------------------------------------------------------------
// 9. CALENDAR WIDGET RENDERER
// -------------------------------------------------------------
function renderCalendar() {
    const grid = document.getElementById('calendar-days-grid');
    if (!grid) return;

    const now = new Date();
    const currentDay = now.getDate();
    const monthDays = 31; // July

    let html = `
        <div class="calendar-day-head">Dom</div>
        <div class="calendar-day-head">Seg</div>
        <div class="calendar-day-head">Ter</div>
        <div class="calendar-day-head">Kua</div>
        <div class="calendar-day-head">Qui</div>
        <div class="calendar-day-head">Sex</div>
        <div class="calendar-day-head">Sab</div>
    `;

    // Offset for July 2026 starting day
    for (let i = 1; i <= 3; i++) {
        html += `<div></div>`;
    }

    for (let day = 1; day <= monthDays; day++) {
        const isActive = day === currentDay ? 'active' : '';
        html += `<div class="calendar-day-num ${isActive}">${day}</div>`;
    }

    grid.innerHTML = html;
}

// -------------------------------------------------------------
// 10. AUTHENTICATION (LOGIN / LOGOUT)
// -------------------------------------------------------------
async function handleLogin(e) {
    e.preventDefault();
    const user = document.getElementById('login-username').value;
    const pass = document.getElementById('login-password').value;

    try {
        const res = await fetch('api/dashboard_api.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: user, password: pass })
        });
        const json = await res.json();

        if (json.status === 'success') {
            document.getElementById('login-screen').style.display = 'none';
            showToast(json.message);
            loadDashboardStats();
        } else {
            document.getElementById('login-error').textContent = json.message;
            document.getElementById('login-error').style.display = 'block';
        }
    } catch (err) {
        showToast('Pedidu login falla.', 'error');
    }
}

async function handleLogout() {
    if (!confirm('Ita-boot iha serteza atu sai?')) return;
    try {
        await fetch('api/dashboard_api.php?action=logout');
        document.getElementById('login-screen').style.display = 'flex';
        showToast('Sai ona.');
    } catch (err) {
        location.reload();
    }
}
