<?php
// SystemConfig Model - Procedural functions for system_config table

function systemConfigGet($config_key) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT * FROM system_config WHERE config_key = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $config_key);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function systemConfigSet($config_key, $value) {
    $db = getConnection();
    $stmt = $db->prepare('INSERT INTO system_config (config_key, value, created_at, updated_at) VALUES (?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ss', $config_key, $value);
    return $stmt->execute();
}

function systemConfigDelete($config_key) {
    $db = getConnection();
    $stmt = $db->prepare('DELETE FROM system_config WHERE config_key = ?');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $config_key);
    return $stmt->execute();
}

function systemConfigGetAll() {
    $db = getConnection();
    $result = $db->query('SELECT * FROM system_config ORDER BY config_key ASC');
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function systemConfigExists($config_key) {
    $db = getConnection();
    $stmt = $db->prepare('SELECT config_key FROM system_config WHERE config_key = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $config_key);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function systemConfigGetByPrefix($prefix) {
    $db = getConnection();
    $like = $prefix . '%';
    $stmt = $db->prepare('SELECT * FROM system_config WHERE config_key LIKE ? ORDER BY config_key ASC');
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('s', $like);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function systemConfigGetValue($config_key, $default = null) {
    $config = systemConfigGet($config_key);
    return $config ? $config['value'] : $default;
}
?>