<?php
// SystemConfig Model - System configuration data operations (Procedural)

// Get config by key
function configGet($key) {
    $result = fetchOne('SELECT value FROM system_config WHERE config_key = ?', 's', [$key]);
    return $result ? $result['value'] : null;
}

// Get all configs
function configGetAll() {
    return fetchAll('SELECT config_key, value FROM system_config');
}

// Set config
function configSet($key, $value) {
    $existing = fetchOne('SELECT id FROM system_config WHERE config_key = ?', 's', [$key]);
    
    if ($existing) {
        return updateRecord('system_config', ['value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 'config_key = ?', [$key]);
    } else {
        $configData = [
            'config_key' => $key,
            'value' => $value,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return insertRecord('system_config', $configData);
    }
}

// Delete config
function configDelete($key) {
    return deleteRecord('system_config', 'config_key = ?', [$key]);
}

// Get pharmacy settings
function configGetPharmacySettings() {
    $configs = configGetAll();
    $settings = [];
    
    foreach ($configs as $config) {
        $settings[$config['config_key']] = $config['value'];
    }
    
    return $settings;
}

// Get tax rate
function configGetTaxRate() {
    $rate = configGet('tax_rate');
    return $rate ? (float)$rate : 0;
}

// Set tax rate
function configSetTaxRate($rate) {
    return configSet('tax_rate', $rate);
}

// Get pharmacy name
function configGetPharmacyName() {
    return configGet('pharmacy_name') ?? 'Pharmacy';
}

// Set pharmacy name
function configSetPharmacyName($name) {
    return configSet('pharmacy_name', $name);
}

// Get pharmacy contact
function configGetContact() {
    return configGet('contact_info');
}

// Set pharmacy contact
function configSetContact($contact) {
    return configSet('contact_info', $contact);
}

// Get pharmacy email
function configGetEmail() {
    return configGet('email');
}

// Set pharmacy email
function configSetEmail($email) {
    return configSet('email', $email);
}

// Get pharmacy phone
function configGetPhone() {
    return configGet('phone');
}

// Set pharmacy phone
function configSetPhone($phone) {
    return configSet('phone', $phone);
}

// Get pharmacy address
function configGetAddress() {
    return configGet('address');
}

// Set pharmacy address
function configSetAddress($address) {
    return configSet('address', $address);
}
?>