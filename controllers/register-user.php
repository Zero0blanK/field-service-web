<?php

function registerUser($db, $data, $role, $extraFields = []) {
    if (in_array('', array_filter($data))) {
        $_SESSION['error'] = "Please fill in all required fields";
        return false;
    }

    $isEmail = $db->query(
        "SELECT user_id FROM users WHERE email = :email",
        [':email' => $data[':email']]
    );

    if ($isEmail) {
        $existingUser = $isEmail->fetch();
    } else {
        $existingUser = false;
    }
    
    if ($existingUser) {
        $_SESSION['error'] = "Email already registered!";
        return false;
    }

    $db->query(
        "INSERT INTO users (name, email, phone, address, city, zipcode, password, role, created_at) 
        VALUES (:name, :email, :phone, :address, :city, :zipcode, :password, :role, NOW())",
        $data
    );

    $user_id = $db->lastInsertId();

    if ($role === 'customer') {
        $db->query(
            "INSERT INTO customers (user_id, company_name) VALUES (:user_id, :company_name)",
            [':user_id' => $user_id, ':company_name' => $extraFields['company_name'] ?? NULL]
        );
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: /login");
    } elseif ($role === 'technician') {
        $db->query(
            "INSERT INTO technicians (user_id) VALUES (:user_id)",
            [':user_id' => $user_id]
        );

        // Get the technician ID for skills association
        $tech_id = $db->lastInsertId();
        
        if (isset($extraFields['technician_skills']) && is_array($extraFields['technician_skills'])) {
            foreach ($extraFields['technician_skills'] as $skill_id) {
                $db->query(
                    "INSERT INTO technician_skills 
                        (tech_id, skill_id) 
                    VALUES 
                        (:tech_id, :skill_id)",
                    [':tech_id' => $tech_id, ':skill_id' => $skill_id]
                );
            }
        }

        $_SESSION['success'] = "Registration successful!";
        header("Location: /dashboard/technicians");
    }
    exit();
}