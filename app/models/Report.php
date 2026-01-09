<?php
// Report Model - Procedural functions for reports table

// Ensure database helpers are loaded for static analysis and runtime
require_once __DIR__ . '/../../core/Database.php';
use mysqli;

function reportCreate($report_type, $generated_by, $file_path) {
    $db = getConnection();
    $stmt = $db->prepare('INSERT INTO reports (report_type, generated_by, file_path, created_at) VALUES (?, ?, ?, NOW())');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('sis', $report_type, $generated_by, $file_path);
    if ($stmt->execute()) {
        return $db->insert_id;
    }
    return false;
}

function reportGetById($report_id) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM reports WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $report_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function reportGetByType($report_type, $limit = null, $offset = 0) {
    $db = getConnection();
    $query = 'SELECT * FROM reports WHERE report_type = ? ORDER BY created_at DESC';
    if ($limit !== null) {
        $query .= ' LIMIT ? OFFSET ?';
    }
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }
    if ($limit !== null) {
        $stmt->bind_param('sii', $report_type, $limit, $offset);
    } else {
        $stmt->bind_param('s', $report_type);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function reportGetAll($limit = null, $offset = 0) {
    $db = getConnection();
    $query = 'SELECT * FROM reports ORDER BY created_at DESC';
    if ($limit !== null) {
        $query .= ' LIMIT ? OFFSET ?';
    }
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }
    if ($limit !== null) {
        $stmt->bind_param('ii', $limit, $offset);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function reportGetByUser($user_id, $limit = null, $offset = 0) {
    $db = getConnection();
    $query = 'SELECT * FROM reports WHERE generated_by = ? ORDER BY created_at DESC';
    if ($limit !== null) {
        $query .= ' LIMIT ? OFFSET ?';
    }
    $stmt = $db->prepare($query);
    if (!$stmt) {
        return [];
    }
    if ($limit !== null) {
        $stmt->bind_param('iii', $user_id, $limit, $offset);
    } else {
        $stmt->bind_param('i', $user_id);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function reportDelete($report_id) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM reports WHERE id = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $report_id);
    return $stmt->execute();
}

function reportGetRecent($limit = 10) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM reports ORDER BY created_at DESC LIMIT ?');
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
