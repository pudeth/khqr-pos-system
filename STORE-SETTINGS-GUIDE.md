# Store Settings Management - Complete Implementation

## ✅ What's Been Implemented

### 1. Database Structure
- **New table**: `store_settings` with fields:
  - `key`: Setting identifier (e.g., 'store_name', 'store_logo')
  - `value`: Setting value
  - `type`: Data type (string, text, image, boolean)
  - `group`: Setting category (branding, general, etc.)

### 2. Model & Functionality
- **StoreSetting Model** with helper methods:
  - `StoreSetting::get($key, $default)` - Get setting value
  - `StoreSetting::set($key, $value, $type, $group)` - Set setting value
  - `StoreSetting::getBrandingSettings()` - Get all branding settings
  - Built-in caching for performance

### 3. Admin Interface
- **Settings Page**: `/admin/settings`
- **Features**:
  - Store name and tagline editing
  - Logo upload with preview
  - Contact information (phone, address)
  - Live preview of branding changes
  - File validation (2MB max, image formats only)

### 4. Dynamic Branding
- **Admin Layout**: Shows custom store name and logo in sidebar
- **POS System**: Uses dynamic store name and logo in header
- **Automatic Updates**: Changes reflect immediately across the system

## 🚀 How to Test

### Step 1: Access Settings
1. Login to admin dashboard
2. Click **"SETTINGS"** in the sidebar
3. You'll see the store settings page

### Step 2: Update Store Information
1. **Store Name**: Change from "POSPAY" to your store name
2. **Store Tagline**: Update the tagline
3. **Logo Upload**: Upload a logo image (JPG, PNG, GIF - max 2MB)
4. **Contact Info**: Add phone number and address
5. Click **"SAVE SETTINGS"**

### Step 3: Verify Changes
1. **Admin Sidebar**: Should show new store name and logo
2. **POS System**: Visit `/pos` to see updated branding
3. **Preview Section**: Shows how branding appears

## 📁 Files Created/Modified

### New Files:
- `database/migrations/2026_02_11_120000_create_store_settings_table.php`
- `app/Models/StoreSetting.php`
- `resources/views/admin/settings.blade.php`
- `test-store-settings.php` (testing script)

### Modified Files:
- `app/Http/Controllers/AdminController.php` (added settings methods)
- `routes/web.php` (added settings routes)
- `resources/views/layouts/admin.blade.php` (dynamic branding)
- `resources/views/pos/index.blade.php` (dynamic branding)

## 🔧 Technical Features

### File Upload Handling
- Automatic old logo deletion when uploading new one
- Secure file storage in `storage/app/public/logos/`
- Image validation and size limits
- Public access via storage link

### Caching System
- Settings cached for 1 hour for performance
- Cache automatically cleared when settings updated
- Reduces database queries

### Security Features
- CSRF protection on all forms
- File type validation
- Size limits on uploads
- Input sanitization and validation

## 🎯 Usage Examples

### In Controllers:
```php
$storeName = StoreSetting::get('store_name', 'Default Store');
$storeLogo = StoreSetting::get('store_logo');
```

### In Views:
```php
@php
    $storeName = \App\Models\StoreSetting::get('store_name', 'POSPAY');
    $storeLogo = \App\Models\StoreSetting::get('store_logo');
@endphp

<h1>{{ $storeName }}</h1>
@if($storeLogo)
    <img src="{{ asset('storage/' . $storeLogo) }}" alt="Logo">
@endif
```

## ✨ Ready to Use!

The store settings system is now fully functional. Admins can:
- ✅ Change store name and tagline
- ✅ Upload and manage store logo
- ✅ Update contact information
- ✅ See live preview of changes
- ✅ Have changes reflect across admin and POS systems

**Next Steps**: Test the functionality by accessing `/admin/settings` and updating your store information!