<?php
// api/dashboard_api.php - Timor Furniture Receptionist Dashboard API Engine
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

session_start();

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? $_POST['action'] ?? 'stats';

try {
    // ---------------------------------------------------------
    // 1. STATS & OVERVIEW
    // ---------------------------------------------------------
    if ($action === 'stats') {
        $today = date('Y-m-d');

        // Total Visitors Today
        $stVisToday = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE DATE(check_in) = ?");
        $stVisToday->execute([$today]);
        $visToday = $stVisToday->fetchColumn();

        // Total Appointments Today
        $stAppToday = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ?");
        $stAppToday->execute([$today]);
        $appToday = $stAppToday->fetchColumn();

        // Pending Appointments
        $stAppPending = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Scheduled'");
        $appPending = $stAppPending->fetchColumn();

        // Currently Checked-in Customers
        $stCheckedIn = $pdo->query("SELECT COUNT(*) FROM visitors WHERE status = 'Checked In'");
        $checkedIn = $stCheckedIn->fetchColumn();

        // Completed Visits Today
        $stCompleted = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE DATE(check_in) = ? AND status = 'Checked Out'");
        $stCompleted->execute([$today]);
        $completed = $stCompleted->fetchColumn();

        // Recent Visitors List (Last 5)
        $recentVisitors = $pdo->query("SELECT * FROM visitors ORDER BY id DESC LIMIT 5")->fetchAll();

        // Today's Upcoming Appointments
        $stUpcoming = $pdo->prepare("SELECT * FROM appointments WHERE appointment_date >= ? ORDER BY appointment_date ASC, appointment_time ASC LIMIT 5");
        $stUpcoming->execute([$today]);
        $upcomingApp = $stUpcoming->fetchAll();

        // Notifications List
        $notifs = $pdo->query("SELECT * FROM notifications ORDER BY id DESC LIMIT 5")->fetchAll();

        echo json_encode([
            "status" => "success",
            "data" => [
                "visitors_today" => intval($visToday),
                "appointments_today" => intval($appToday),
                "pending_appointments" => intval($appPending),
                "checked_in_customers" => intval($checkedIn),
                "completed_appointments" => intval($completed),
                "recent_visitors" => $recentVisitors,
                "upcoming_appointments" => $upcomingApp,
                "notifications" => $notifs
            ]
        ]);
        exit;
    }

    // ---------------------------------------------------------
    // 2. VISITORS API
    // ---------------------------------------------------------
    if ($action === 'visitors') {
        if ($method === 'GET') {
            $search = trim($_GET['search'] ?? '');
            if ($search !== '') {
                $stmt = $pdo->prepare("SELECT * FROM visitors WHERE full_name LIKE ? OR visitor_id LIKE ? OR phone LIKE ? OR purpose LIKE ? ORDER BY id DESC");
                $term = "%$search%";
                $stmt->execute([$term, $term, $term, $term]);
            } else {
                $stmt = $pdo->query("SELECT * FROM visitors ORDER BY id DESC");
            }
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            exit;
        }

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $subAction = $data['sub_action'] ?? 'save';

            if ($subAction === 'checkout') {
                $id = intval($data['id']);
                $stmt = $pdo->prepare("UPDATE visitors SET status = 'Checked Out', check_out = ? WHERE id = ?");
                $stmt->execute([date('Y-m-d H:i:s'), $id]);
                echo json_encode(["status" => "success", "message" => "Visitor checked out successfully."]);
                exit;
            }

            if ($subAction === 'delete') {
                $id = intval($data['id']);
                $stmt = $pdo->prepare("DELETE FROM visitors WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success", "message" => "Visitor log deleted."]);
                exit;
            }

            // Save / Update Visitor
            $id = intval($data['id'] ?? 0);
            $full_name = trim($data['full_name'] ?? '');
            $phone = trim($data['phone'] ?? '');
            $email = trim($data['email'] ?? '');
            $purpose = trim($data['purpose'] ?? '');
            $person_to_meet = trim($data['person_to_meet'] ?? '');

            if (empty($full_name) || empty($phone) || empty($purpose) || empty($person_to_meet)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Full Name, Phone, Purpose, and Person to Meet are required."]);
                exit;
            }

            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE visitors SET full_name = ?, phone = ?, email = ?, purpose = ?, person_to_meet = ? WHERE id = ?");
                $stmt->execute([$full_name, $phone, $email, $purpose, $person_to_meet, $id]);
                echo json_encode(["status" => "success", "message" => "Visitor updated successfully."]);
            } else {
                $visitor_id = 'TL-VIS-' . rand(1000, 9999);
                $stmt = $pdo->prepare("INSERT INTO visitors (visitor_id, full_name, phone, email, purpose, person_to_meet, check_in, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Checked In')");
                $stmt->execute([$visitor_id, $full_name, $phone, $email, $purpose, $person_to_meet, date('Y-m-d H:i:s')]);

                // Create Notification
                $stN = $pdo->prepare("INSERT INTO notifications (title, message, type) VALUES (?, ?, 'info')");
                $stN->execute(["New Visitor Registered", "$full_name ($visitor_id) checked in to meet $person_to_meet."]);

                echo json_encode(["status" => "success", "message" => "Visitor registered and checked in successfully!", "visitor_id" => $visitor_id]);
            }
            exit;
        }
    }

    // ---------------------------------------------------------
    // 3. APPOINTMENTS API
    // ---------------------------------------------------------
    if ($action === 'appointments') {
        if ($method === 'GET') {
            $search = trim($_GET['search'] ?? '');
            if ($search !== '') {
                $stmt = $pdo->prepare("SELECT * FROM appointments WHERE customer_name LIKE ? OR purpose LIKE ? OR sales_rep LIKE ? ORDER BY appointment_date DESC, appointment_time ASC");
                $term = "%$search%";
                $stmt->execute([$term, $term, $term]);
            } else {
                $stmt = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date DESC, appointment_time ASC");
            }
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            exit;
        }

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $subAction = $data['sub_action'] ?? 'save';

            if ($subAction === 'delete') {
                $id = intval($data['id']);
                $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success", "message" => "Appointment deleted."]);
                exit;
            }

            if ($subAction === 'cancel') {
                $id = intval($data['id']);
                $stmt = $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success", "message" => "Appointment status updated to Cancelled."]);
                exit;
            }

            $id = intval($data['id'] ?? 0);
            $customer_name = trim($data['customer_name'] ?? '');
            $phone = trim($data['phone'] ?? '');
            $email = trim($data['email'] ?? '');
            $appointment_date = trim($data['appointment_date'] ?? date('Y-m-d'));
            $appointment_time = trim($data['appointment_time'] ?? '10:00');
            $purpose = trim($data['purpose'] ?? '');
            $sales_rep = trim($data['sales_rep'] ?? '');
            $status = trim($data['status'] ?? 'Scheduled');
            $notes = trim($data['notes'] ?? '');

            if (empty($customer_name) || empty($phone) || empty($purpose) || empty($sales_rep)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Customer Name, Phone, Purpose, and Sales Rep are required."]);
                exit;
            }

            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE appointments SET customer_name = ?, phone = ?, email = ?, appointment_date = ?, appointment_time = ?, purpose = ?, sales_rep = ?, status = ?, notes = ? WHERE id = ?");
                $stmt->execute([$customer_name, $phone, $email, $appointment_date, $appointment_time, $purpose, $sales_rep, $status, $notes, $id]);
                echo json_encode(["status" => "success", "message" => "Appointment updated successfully."]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO appointments (customer_name, phone, email, appointment_date, appointment_time, purpose, sales_rep, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$customer_name, $phone, $email, $appointment_date, $appointment_time, $purpose, $sales_rep, $status, $notes]);
                
                // Add notification
                $stN = $pdo->prepare("INSERT INTO notifications (title, message, type) VALUES (?, ?, 'warning')");
                $stN->execute(["New Appointment Created", "Appointment booked for $customer_name on $appointment_date with $sales_rep."]);

                echo json_encode(["status" => "success", "message" => "Appointment created successfully!"]);
            }
            exit;
        }
    }

    // ---------------------------------------------------------
    // 4. CUSTOMERS API
    // ---------------------------------------------------------
    if ($action === 'customers') {
        if ($method === 'GET') {
            $search = trim($_GET['search'] ?? '');
            if ($search !== '') {
                $stmt = $pdo->prepare("SELECT * FROM customers WHERE full_name LIKE ? OR company_name LIKE ? OR customer_code LIKE ? OR city LIKE ? ORDER BY id DESC");
                $term = "%$search%";
                $stmt->execute([$term, $term, $term, $term]);
            } else {
                $stmt = $pdo->query("SELECT * FROM customers ORDER BY id DESC");
            }
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            exit;
        }

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $subAction = $data['sub_action'] ?? 'save';

            if ($subAction === 'delete') {
                $id = intval($data['id']);
                $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success", "message" => "Customer record deleted."]);
                exit;
            }

            $id = intval($data['id'] ?? 0);
            $full_name = trim($data['full_name'] ?? '');
            $company_name = trim($data['company_name'] ?? '');
            $phone = trim($data['phone'] ?? '');
            $email = trim($data['email'] ?? '');
            $address = trim($data['address'] ?? '');
            $city = trim($data['city'] ?? 'Dili');
            $purchase_history = trim($data['purchase_history'] ?? '');
            $notes = trim($data['notes'] ?? '');

            if (empty($full_name) || empty($phone) || empty($address)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Full Name, Phone, and Address are required."]);
                exit;
            }

            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE customers SET full_name = ?, company_name = ?, phone = ?, email = ?, address = ?, city = ?, purchase_history = ?, notes = ? WHERE id = ?");
                $stmt->execute([$full_name, $company_name, $phone, $email, $address, $city, $purchase_history, $notes, $id]);
                echo json_encode(["status" => "success", "message" => "Customer details updated."]);
            } else {
                $customer_code = 'TL-CUST-' . rand(500, 999);
                $stmt = $pdo->prepare("INSERT INTO customers (customer_code, full_name, company_name, phone, email, address, city, purchase_history, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$customer_code, $full_name, $company_name, $phone, $email, $address, $city, $purchase_history, $notes]);
                echo json_encode(["status" => "success", "message" => "Customer created successfully!", "customer_code" => $customer_code]);
            }
            exit;
        }
    }

    // ---------------------------------------------------------
    // 5. SHOWROOM VISITS API
    // ---------------------------------------------------------
    if ($action === 'showroom') {
        if ($method === 'GET') {
            $search = trim($_GET['search'] ?? '');
            if ($search !== '') {
                $stmt = $pdo->prepare("SELECT * FROM showroom_visits WHERE visitor_name LIKE ? OR furniture_interest LIKE ? OR assigned_rep LIKE ? ORDER BY id DESC");
                $term = "%$search%";
                $stmt->execute([$term, $term, $term]);
            } else {
                $stmt = $pdo->query("SELECT * FROM showroom_visits ORDER BY id DESC");
            }
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            exit;
        }

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $subAction = $data['sub_action'] ?? 'save';

            if ($subAction === 'delete') {
                $id = intval($data['id']);
                $stmt = $pdo->prepare("DELETE FROM showroom_visits WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success", "message" => "Showroom visit record deleted."]);
                exit;
            }

            $id = intval($data['id'] ?? 0);
            $visitor_name = trim($data['visitor_name'] ?? '');
            $phone = trim($data['phone'] ?? '');
            $visit_date = trim($data['visit_date'] ?? date('Y-m-d'));
            $furniture_interest = trim($data['furniture_interest'] ?? '');
            $assigned_rep = trim($data['assigned_rep'] ?? '');
            $status = trim($data['status'] ?? 'In Showroom');
            $notes = trim($data['notes'] ?? '');

            if (empty($visitor_name) || empty($phone) || empty($furniture_interest) || empty($assigned_rep)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Visitor Name, Phone, Furniture Interest, and Sales Rep are required."]);
                exit;
            }

            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE showroom_visits SET visitor_name = ?, phone = ?, visit_date = ?, furniture_interest = ?, assigned_rep = ?, status = ?, notes = ? WHERE id = ?");
                $stmt->execute([$visitor_name, $phone, $visit_date, $furniture_interest, $assigned_rep, $status, $notes, $id]);
                echo json_encode(["status" => "success", "message" => "Showroom visit updated."]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO showroom_visits (visitor_name, phone, visit_date, furniture_interest, assigned_rep, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$visitor_name, $phone, $visit_date, $furniture_interest, $assigned_rep, $status, $notes]);
                echo json_encode(["status" => "success", "message" => "Showroom visit scheduled successfully!"]);
            }
            exit;
        }
    }

    // ---------------------------------------------------------
    // 6. NOTIFICATIONS API
    // ---------------------------------------------------------
    if ($action === 'notifications') {
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM notifications ORDER BY id DESC");
            echo json_encode(["status" => "success", "data" => $stmt->fetchAll()]);
            exit;
        }

        if ($method === 'POST') {
            $pdo->exec("UPDATE notifications SET is_read = 1");
            echo json_encode(["status" => "success", "message" => "All notifications marked as read."]);
            exit;
        }
    }

    // ---------------------------------------------------------
    // 7. REPORTS & EXPORTS
    // ---------------------------------------------------------
    if ($action === 'reports') {
        $visitors = $pdo->query("SELECT * FROM visitors ORDER BY check_in DESC").fetchAll();
        $appointments = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date DESC").fetchAll();
        $showroom = $pdo->query("SELECT * FROM showroom_visits ORDER BY id DESC").fetchAll();

        echo json_encode([
            "status" => "success",
            "data" => [
                "visitors" => $visitors,
                "appointments" => $appointments,
                "showroom" => $showroom
            ]
        ]);
        exit;
    }

    // ---------------------------------------------------------
    // 8. SETTINGS & PROFILE
    // ---------------------------------------------------------
    if ($action === 'settings') {
        if ($method === 'GET') {
            $rows = $pdo->query("SELECT * FROM settings")->fetchAll();
            $map = [];
            foreach ($rows as $r) {
                $map[$r['setting_key']] = $r['setting_value'];
            }
            echo json_encode(["status" => "success", "data" => $map]);
            exit;
        }

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            
            // For SQLite compatibility
            $stmtSQLite = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");

            foreach ($data as $key => $val) {
                if ($key === 'action') continue;
                try {
                    $stmtSQLite->execute([$key, $val]);
                } catch (Exception $ex) {
                    $stmt->execute([$key, $val, $val]);
                }
            }
            echo json_encode(["status" => "success", "message" => "Settings updated successfully."]);
            exit;
        }
    }

    // ---------------------------------------------------------
    // 9. AUTHENTICATION (LOGIN / LOGOUT)
    // ---------------------------------------------------------
    if ($action === 'login') {
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($username === 'admin' || $username === 'receptionist') {
            $_SESSION['user_logged'] = true;
            $_SESSION['username'] = 'Maria Silva';
            echo json_encode(["status" => "success", "message" => "Login successful. Welcome Maria Silva!"]);
        } else {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Invalid credentials. Use 'admin' or 'receptionist'."]);
        }
        exit;
    }

    if ($action === 'logout') {
        session_destroy();
        echo json_encode(["status" => "success", "message" => "Logged out successfully."]);
        exit;
    }

    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action specified."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
