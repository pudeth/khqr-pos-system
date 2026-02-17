<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StoreSetting;

echo "=== STORE SETTINGS TEST ===\n\n";

// Test getting settings
echo "Current store name: " . StoreSetting::get('store_name', 'Default Store') . "\n";
echo "Current store tagline: " . StoreSetting::get('store_tagline', 'Default Tagline') . "\n";
echo "Current store logo: " . (StoreSetting::get('store_logo') ?: 'No logo set') . "\n";

// Test getting branding settings
echo "\n=== BRANDING SETTINGS ===\n";
$brandingSettings = StoreSetting::getBrandingSettings();
foreach ($brandingSettings as $key => $value) {
    echo "$key: " . ($value ?: 'Not set') . "\n";
}

// Test setting a value
echo "\n=== TESTING SET FUNCTIONALITY ===\n";
StoreSetting::set('test_setting', 'Test Value', 'string', 'test');
echo "Set test_setting to: " . StoreSetting::get('test_setting') . "\n";

// Clean up test setting
StoreSetting::where('key', 'test_setting')->delete();
echo "Test setting cleaned up.\n";

echo "\n=== TEST COMPLETED ===\n";