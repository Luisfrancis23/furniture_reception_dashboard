<?php
// config/db.php - Timor Furniture Co. Receptionist Dashboard DB Engine

$host = '127.0.0.1';
$db_name = 'timor_furniture_reception';
$username = 'root';
$password = '';

function getDBConnection() {
    global $host, $db_name, $username, $password;

    // 1. Try MySQL
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 3
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`");
        initializeSchema($pdo, 'mysql');
        return $pdo;
    } catch (PDOException $e) {
        // 2. Fallback to SQLite if MySQL is not active
        try {
            $data_dir = __DIR__ . '/../data';
            if (!file_exists($data_dir)) {
                mkdir($data_dir, 0777, true);
            }
            $pdo = new PDO("sqlite:" . $data_dir . '/database.sqlite');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            initializeSchema($pdo, 'sqlite');
            return $pdo;
        } catch (PDOException $ex) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Database error: " . $ex->getMessage()]);
            exit;
        }
    }
}

function initializeSchema($pdo, $driver = 'mysql') {
    $auto = ($driver === 'mysql') ? "AUTO_INCREMENT" : "AUTOINCREMENT";

    // 1. Visitors Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS visitors (
        id INTEGER PRIMARY KEY $auto,
        visitor_id VARCHAR(30) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        email VARCHAR(100),
        purpose VARCHAR(100) NOT NULL,
        person_to_meet VARCHAR(100) NOT NULL,
        check_in DATETIME DEFAULT CURRENT_TIMESTAMP,
        check_out DATETIME,
        status VARCHAR(20) DEFAULT 'Checked In',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Appointments Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY $auto,
        customer_name VARCHAR(100) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        email VARCHAR(100),
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        purpose VARCHAR(150) NOT NULL,
        sales_rep VARCHAR(100) NOT NULL,
        status VARCHAR(30) DEFAULT 'Scheduled',
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Customers Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
        id INTEGER PRIMARY KEY $auto,
        customer_code VARCHAR(30) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        company_name VARCHAR(100),
        phone VARCHAR(30) NOT NULL,
        email VARCHAR(100),
        address VARCHAR(200) NOT NULL,
        city VARCHAR(50) DEFAULT 'Dili',
        purchase_history TEXT,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Showroom Visits Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS showroom_visits (
        id INTEGER PRIMARY KEY $auto,
        visitor_name VARCHAR(100) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        visit_date DATE NOT NULL,
        furniture_interest VARCHAR(100) NOT NULL,
        assigned_rep VARCHAR(100) NOT NULL,
        status VARCHAR(30) DEFAULT 'In Showroom',
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Notifications Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY $auto,
        title VARCHAR(150) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(30) DEFAULT 'info',
        is_read INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 6. Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY $auto,
        setting_key VARCHAR(50) UNIQUE NOT NULL,
        setting_value TEXT NOT NULL
    )");

    // Check if initial seed data exists
    $chk = $pdo->query("SELECT COUNT(*) as cnt FROM visitors")->fetch()['cnt'];
    if ($chk == 0) {
        seedInitialData($pdo);
    }
}

function seedInitialData($pdo) {
    // Seed Visitors
    $visitors = [
        ['TL-VIS-1001', 'Joao Pereira', '+670 7712 3456', 'joao.p@gov.tl', 'Executive Teak Table Consultation', 'Manuel Santos (Senior Sales Manager)', '2026-07-27 09:15:00', NULL, 'Checked In'],
        ['TL-VIS-1002', 'Maria da Costa', '+670 7823 4567', 'maria.costa@telecom.tl', 'Living Room Sofa & Dining Set Tour', 'Filomena Soares (Interior Designer)', '2026-07-27 10:00:00', '2026-07-27 11:30:00', 'Checked Out'],
        ['TL-VIS-1003', 'Carlos Belo', '+670 7799 8811', 'cbelo@construction.tl', 'Custom Office Desk Supplier Inquiry', 'Antonio Guterres (Procurement Rep)', '2026-07-27 11:15:00', NULL, 'Checked In'],
        ['TL-VIS-1004', 'Isabel Xavier', '+670 7744 5566', 'isabel.x@hotel.tl', 'Resort Bedroom Furniture Quote', 'Manuel Santos (Senior Sales Manager)', '2026-07-27 14:00:00', NULL, 'Waiting']
    ];
    $stVis = $pdo->prepare("INSERT INTO visitors (visitor_id, full_name, phone, email, purpose, person_to_meet, check_in, check_out, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($visitors as $v) { $stVis->execute($v); }

    // Seed Appointments
    $today = date('Y-m-d');
    $appointments = [
        ['Ministry of Infrastructure Team', '+670 7711 2233', 'infra@gov.tl', $today, '10:30:00', 'Conference Room Table & Chairs Bulk Order', 'Manuel Santos', 'In Progress', 'Requires 20 mahogany executive chairs'],
        ['Grand Timor Hotel Management', '+670 7733 4455', 'procurement@grandtimor.tl', $today, '14:00:00', 'Luxury Bedroom Suite Consultation', 'Filomena Soares', 'Scheduled', 'Client interested in solid Timor Teak wood'],
        ['Baucau Commercial Bank', '+670 7755 6677', 'admin@baucau-bank.tl', $today, '16:00:00', 'Teller Desks & Safety Partition Specs', 'Antonio Guterres', 'Scheduled', 'First consultation meeting'],
        ['Lucia Ramos', '+670 7788 9900', 'lucia.r@gmail.com', date('Y-m-d', strtotime('+1 day')), '11:00:00', 'Custom Home Dining Set Customization', 'Filomena Soares', 'Scheduled', 'Bringing home blueprints']
    ];
    $stApp = $pdo->prepare("INSERT INTO appointments (customer_name, phone, email, appointment_date, appointment_time, purpose, sales_rep, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($appointments as $a) { $stApp->execute($a); }

    // Seed Customers
    $customers = [
        ['TL-CUST-501', 'Ministry of Infrastructure', 'Gov Timor-Leste', '+670 331 2345', 'info@infrastructure.gov.tl', 'Avenida de Portugal, Dili', 'Dili', 'Purchased 15 Executive Mahogany Desks & 30 Swivel Chairs ($24,500)', 'VIP Government Client'],
        ['TL-CUST-502', 'Timor Plaza Hotel & Suites', 'Timor Plaza Group', '+670 332 9988', 'management@timorplaza.tl', 'Rua de Comoro, Dili', 'Dili', 'Purchased 50 Hotel Suite Bedroom Sets ($68,000)', 'Recurring commercial account'],
        ['TL-CUST-503', 'Cafe Timor Organic', 'Cooperativa Cafe Timor', '+670 339 1122', 'orders@cafetimor.tl', 'Baucau Main Street, Baucau', 'Baucau', 'Purchased 12 Outdoor Teak Patio Sets ($8,400)', 'Interested in lounge chairs next month'],
        ['TL-CUST-504', 'Dr. Francisco Guterres', 'Private Residence', '+670 7722 1100', 'fguterres@clinic.tl', 'Farol, Dili', 'Dili', 'Purchased Custom Royal King Teak Bed & Dining Set ($12,000)', 'Prefers natural oil finish']
    ];
    $stCust = $pdo->prepare("INSERT INTO customers (customer_code, full_name, company_name, phone, email, address, city, purchase_history, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($customers as $c) { $stCust->execute($c); }

    // Seed Showroom Visits
    $showroom = [
        ['Joao Pereira', '+670 7712 3456', $today, 'Executive Teak Office Desk', 'Manuel Santos', 'In Showroom', 'Testing wood samples and ergonomics'],
        ['Maria da Costa', '+670 7823 4567', $today, 'Modern Minimalist Sofa Set', 'Filomena Soares', 'Completed', 'Quotation sent via email ($3,200)'],
        ['Pedro Martins', '+670 7799 3322', $today, 'Solid Teak Dining Table (8 Seater)', 'Antonio Guterres', 'Scheduled', 'Scheduled for 3:00 PM today']
    ];
    $stShow = $pdo->prepare("INSERT INTO showroom_visits (visitor_name, phone, visit_date, furniture_interest, assigned_rep, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($showroom as $sh) { $stShow->execute($sh); }

    // Seed Notifications
    $notifications = [
        ['Visitor Checked In', 'Joao Pereira from Gov Timor-Leste has arrived for Executive Teak Table Consultation.', 'success', 0],
        ['Upcoming Appointment', 'Ministry of Infrastructure meeting with Manuel Santos starting at 10:30 AM.', 'warning', 0],
        ['New Customer Registered', 'TL-CUST-504 (Dr. Francisco Guterres) added to customer registry.', 'info', 1]
    ];
    $stNotif = $pdo->prepare("INSERT INTO notifications (title, message, type, is_read) VALUES (?, ?, ?, ?)");
    foreach ($notifications as $n) { $stNotif->execute($n); }

    // Seed Settings
    $settings = [
        ['company_name', 'Timor Teak Furniture Co. Lda'],
        ['company_phone', '+670 331 8888'],
        ['company_email', 'reception@timorfurniture.tl'],
        ['company_address', 'Avenida de Portugal, Farol, Dili, Timor-Leste'],
        ['business_hours', 'Mon - Sat: 08:00 AM - 05:30 PM'],
        ['receptionist_name', 'Maria Silva'],
        ['receptionist_role', 'Lead Receptionist & Guest Coordinator']
    ];
    $stSet = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $s) { $stSet->execute($s); }
}
