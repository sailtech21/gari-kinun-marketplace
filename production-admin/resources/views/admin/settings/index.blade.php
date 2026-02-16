@extends('admin.layouts.app')

@section('title', 'System Settings')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .stat-card.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stat-card.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    
    .stat-card .icon {
        font-size: 2rem;
        opacity: 0.5;
        margin-bottom: 10px;
    }
    
    .stat-card .number {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0 5px;
    }
    
    .stat-card .label {
        font-size: 0.95rem;
        opacity: 0.9;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        padding: 12px 24px;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #667eea;
        background: transparent;
        border-bottom: 3px solid #667eea;
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .settings-card h5 {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">⚙️ System Settings</h1>
        <div>
            <button class="btn btn-warning" onclick="clearCache()">
                <i class="fas fa-broom"></i> Clear Cache
            </button>
            <button class="btn btn-secondary" onclick="loadAllSettings()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary">
            <div class="icon"><i class="fas fa-cog"></i></div>
            <div class="number" id="statTotal">0</div>
            <div class="label">Total Settings</div>
        </div>
        <div class="stat-card bg-gradient-success">
            <div class="icon"><i class="fas fa-globe"></i></div>
            <div class="number" id="statGeneral">0</div>
            <div class="label">General</div>
        </div>
        <div class="stat-card bg-gradient-warning">
            <div class="icon"><i class="fas fa-search"></i></div>
            <div class="number" id="statSeo">0</div>
            <div class="label">SEO</div>
        </div>
        <div class="stat-card bg-gradient-info">
            <div class="icon"><i class="fas fa-server"></i></div>
            <div class="number" id="statSystem">0</div>
            <div class="label">System</div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#generalTab">
                <i class="fas fa-globe"></i> General Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#seoTab">
                <i class="fas fa-search"></i> SEO Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#systemTab">
                <i class="fas fa-server"></i> System Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#pricingTab">
                <i class="fas fa-dollar-sign"></i> Pricing
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#emailTab">
                <i class="fas fa-envelope"></i> Email
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- General Settings Tab -->
        <div class="tab-pane fade show active" id="generalTab">
            <form id="generalForm">
                <div class="settings-card">
                    <h5>Website Information</h5>
                    <div class="mb-3">
                        <label class="form-label">Website Name*</label>
                        <input type="text" class="form-control" name="website_name" id="website_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Email*</label>
                        <input type="email" class="form-control" name="contact_email" id="contact_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="contact_phone" id="contact_phone">
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Site Assets</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logoUpload" accept="image/*">
                            <button type="button" class="btn btn-primary btn-sm mt-2" onclick="uploadLogo()">
                                <i class="fas fa-upload"></i> Upload Logo
                            </button>
                            <div id="logoPreview"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Favicon</label>
                            <input type="file" class="form-control" id="faviconUpload" accept="image/*">
                            <button type="button" class="btn btn-primary btn-sm mt-2" onclick="uploadFavicon()">
                                <i class="fas fa-upload"></i> Upload Favicon
                            </button>
                            <div id="faviconPreview"></div>
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Social Links</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-facebook"></i> Facebook</label>
                            <input type="url" class="form-control" name="social_facebook" id="social_facebook">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-twitter"></i> Twitter</label>
                            <input type="url" class="form-control" name="social_twitter" id="social_twitter">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-instagram"></i> Instagram</label>
                            <input type="url" class="form-control" name="social_instagram" id="social_instagram">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-youtube"></i> YouTube</label>
                            <input type="url" class="form-control" name="social_youtube" id="social_youtube">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-linkedin"></i> LinkedIn</label>
                            <input type="url" class="form-control" name="social_linkedin" id="social_linkedin">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Save General Settings
                </button>
            </form>
        </div>

        <!-- SEO Settings Tab -->
        <div class="tab-pane fade" id="seoTab">
            <form id="seoForm">
                <div class="settings-card">
                    <h5>Meta Information</h5>
                    <div class="mb-3">
                        <label class="form-label">Meta Title*</label>
                        <input type="text" class="form-control" name="seo_meta_title" id="seo_meta_title" required>
                        <small class="text-muted">Recommended: 50-60 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description*</label>
                        <textarea class="form-control" name="seo_meta_description" id="seo_meta_description" rows="3" required></textarea>
                        <small class="text-muted">Recommended: 150-160 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <textarea class="form-control" name="seo_meta_keywords" id="seo_meta_keywords" rows="2"></textarea>
                        <small class="text-muted">Comma-separated keywords</small>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Sitemap Control</h5>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="seo_sitemap_enabled" id="seo_sitemap_enabled">
                            <label class="form-check-label" for="seo_sitemap_enabled">Enable XML Sitemap</label>
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Analytics & Tracking</h5>
                    <div class="mb-3">
                        <label class="form-label">Google Analytics Code</label>
                        <textarea class="form-control" name="google_analytics_code" id="google_analytics_code" rows="4" placeholder="<!-- Google Analytics Code -->"></textarea>
                        <small class="text-muted">Paste your Google Analytics tracking code</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Facebook Pixel Code</label>
                        <textarea class="form-control" name="facebook_pixel_code" id="facebook_pixel_code" rows="4" placeholder="<!-- Facebook Pixel Code -->"></textarea>
                        <small class="text-muted">Paste your Facebook Pixel code</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Google Tag Manager ID</label>
                        <input type="text" class="form-control" name="google_tag_manager" id="google_tag_manager" placeholder="GTM-XXXXXXX">
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Save SEO Settings
                </button>
            </form>
        </div>

        <!-- System Settings Tab -->
        <div class="tab-pane fade" id="systemTab">
            <form id="systemForm">
                <div class="settings-card">
                    <h5>Maintenance</h5>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="maintenance_mode" id="maintenance_mode">
                            <label class="form-check-label" for="maintenance_mode">
                                <strong>Enable Maintenance Mode</strong>
                            </label>
                        </div>
                        <small class="text-muted">When enabled, site will show maintenance page to regular users</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Maintenance Message</label>
                        <textarea class="form-control" name="maintenance_message" id="maintenance_message" rows="2"></textarea>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Ad Management</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="auto_approve_ads" id="auto_approve_ads">
                                <label class="form-check-label" for="auto_approve_ads">Auto Approve Ads</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="auto_expire_ads" id="auto_expire_ads">
                                <label class="form-check-label" for="auto_expire_ads">Auto Expire Ads</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ads Expire After (Days)</label>
                            <input type="number" class="form-control" name="ads_expire_days" id="ads_expire_days" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Images per Ad</label>
                            <input type="number" class="form-control" name="max_images_per_ad" id="max_images_per_ad" min="1" max="50">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Ads per User</label>
                            <input type="number" class="form-control" name="max_ads_per_user" id="max_ads_per_user" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Ads per Day</label>
                            <input type="number" class="form-control" name="max_ads_per_day" id="max_ads_per_day" min="1">
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Feature Toggles</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="enable_chat" id="enable_chat">
                                <label class="form-check-label" for="enable_chat">Enable Chat</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="enable_guest_posting" id="enable_guest_posting">
                                <label class="form-check-label" for="enable_guest_posting">Enable Guest Posting</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="require_email_verification" id="require_email_verification">
                                <label class="form-check-label" for="require_email_verification">Require Email Verification</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="require_phone_verification" id="require_phone_verification">
                                <label class="form-check-label" for="require_phone_verification">Require Phone Verification</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Save System Settings
                </button>
            </form>
        </div>

        <!-- Pricing Tab -->
        <div class="tab-pane fade" id="pricingTab">
            <form id="pricingForm">
                <div class="settings-card">
                    <h5>Currency</h5>
                    <div class="mb-3">
                        <label class="form-label">Currency Symbol</label>
                        <input type="text" class="form-control" name="currency_symbol" id="currency_symbol">
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Featured Ads</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Featured Ad Price</label>
                            <input type="number" class="form-control" name="featured_ad_price" id="featured_ad_price" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Featured Ad Duration (Days)</label>
                            <input type="number" class="form-control" name="featured_ad_days" id="featured_ad_days" min="1">
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Boost Ads</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Boost Ad Price</label>
                            <input type="number" class="form-control" name="boost_ad_price" id="boost_ad_price" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Boost Ad Duration (Days)</label>
                            <input type="number" class="form-control" name="boost_ad_days" id="boost_ad_days" min="1">
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Premium Dealer</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Premium Dealer Price</label>
                            <input type="number" class="form-control" name="premium_dealer_price" id="premium_dealer_price" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Premium Dealer Duration (Months)</label>
                            <input type="number" class="form-control" name="premium_dealer_months" id="premium_dealer_months" min="1">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Save Pricing Settings
                </button>
            </form>
        </div>

        <!-- Email Tab -->
        <div class="tab-pane fade" id="emailTab">
            <form id="emailForm">
                <div class="settings-card">
                    <h5>SMTP Configuration</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" name="smtp_host" id="smtp_host">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMTP Port</label>
                            <input type="text" class="form-control" name="smtp_port" id="smtp_port">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" name="smtp_username" id="smtp_username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" name="smtp_password" id="smtp_password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Encryption</label>
                            <select class="form-control" name="smtp_encryption" id="smtp_encryption">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Email From</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">From Address</label>
                            <input type="email" class="form-control" name="mail_from_address" id="mail_from_address">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">From Name</label>
                            <input type="text" class="form-control" name="mail_from_name" id="mail_from_name">
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <h5>Test Email</h5>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Send Test Email To</label>
                            <input type="email" class="form-control" id="testEmailAddress" placeholder="test@example.com">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-info w-100" onclick="sendTestEmail()">
                                <i class="fas fa-paper-plane"></i> Send Test Email
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Save Email Settings
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Load stats
function loadStats() {
    fetch('/admin/settings/stats')
        .then(res => res.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.total_settings;
            document.getElementById('statGeneral').textContent = data.by_group.general || 0;
            document.getElementById('statSeo').textContent = data.by_group.seo || 0;
            document.getElementById('statSystem').textContent = data.by_group.system || 0;
        });
}

// Load all settings
function loadAllSettings() {
    fetch('/admin/settings/all')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const settings = data.settings;
                
                // General settings
                if (settings.general) {
                    Object.keys(settings.general).forEach(key => {
                        const element = document.getElementById(key);
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = settings.general[key] === '1';
                            } else {
                                element.value = settings.general[key] || '';
                            }
                        }
                    });
                    
                    // Show logo preview
                    if (settings.general.site_logo) {
                        document.getElementById('logoPreview').innerHTML = 
                            `<img src="/storage/${settings.general.site_logo}" class="image-preview" alt="Logo">`;
                    }
                    
                    // Show favicon preview
                    if (settings.general.site_favicon) {
                        document.getElementById('faviconPreview').innerHTML = 
                            `<img src="/storage/${settings.general.site_favicon}" class="image-preview" alt="Favicon">`;
                    }
                }
                
                // SEO settings
                if (settings.seo) {
                    Object.keys(settings.seo).forEach(key => {
                        const element = document.getElementById(key);
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = settings.seo[key] === '1';
                            } else {
                                element.value = settings.seo[key] || '';
                            }
                        }
                    });
                }
                
                // System settings
                if (settings.system) {
                    Object.keys(settings.system).forEach(key => {
                        const element = document.getElementById(key);
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = settings.system[key] === '1';
                            } else {
                                element.value = settings.system[key] || '';
                            }
                        }
                    });
                }
                
                // Pricing settings
                if (settings.pricing) {
                    Object.keys(settings.pricing).forEach(key => {
                        const element = document.getElementById(key);
                        if (element) {
                            element.value = settings.pricing[key] || '';
                        }
                    });
                }
                
                // Email settings
                if (settings.email) {
                    Object.keys(settings.email).forEach(key => {
                        const element = document.getElementById(key);
                        if (element) {
                            element.value = settings.email[key] || '';
                        }
                    });
                }
            }
        });
}

// Save settings
function saveSettings(formData) {
    return fetch('/admin/settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ settings: formData })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            loadStats();
        }
        return data;
    });
}

// General settings form
document.getElementById('generalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {};
    new FormData(this).forEach((value, key) => {
        formData[key] = value;
    });
    saveSettings(formData);
});

// SEO settings form
document.getElementById('seoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {};
    new FormData(this).forEach((value, key) => {
        if (document.getElementById(key).type === 'checkbox') {
            formData[key] = document.getElementById(key).checked;
        } else {
            formData[key] = value;
        }
    });
    saveSettings(formData);
});

// System settings form
document.getElementById('systemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {};
    const form = this;
    
    // Get all form elements
    form.querySelectorAll('input, textarea, select').forEach(element => {
        if (element.type === 'checkbox') {
            formData[element.name] = element.checked;
        } else if (element.name) {
            formData[element.name] = element.value;
        }
    });
    
    saveSettings(formData);
});

// Pricing settings form
document.getElementById('pricingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {};
    new FormData(this).forEach((value, key) => {
        formData[key] = value;
    });
    saveSettings(formData);
});

// Email settings form
document.getElementById('emailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = {};
    new FormData(this).forEach((value, key) => {
        formData[key] = value;
    });
    saveSettings(formData);
});

// Upload logo
function uploadLogo() {
    const fileInput = document.getElementById('logoUpload');
    if (!fileInput.files[0]) {
        alert('Please select a logo image');
        return;
    }
    
    const formData = new FormData();
    formData.append('logo', fileInput.files[0]);
    
    fetch('/admin/settings/upload-logo', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('logoPreview').innerHTML = 
                `<img src="${data.url}" class="image-preview" alt="Logo">`;
            fileInput.value = '';
        }
    });
}

// Upload favicon
function uploadFavicon() {
    const fileInput = document.getElementById('faviconUpload');
    if (!fileInput.files[0]) {
        alert('Please select a favicon image');
        return;
    }
    
    const formData = new FormData();
    formData.append('favicon', fileInput.files[0]);
    
    fetch('/admin/settings/upload-favicon', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('faviconPreview').innerHTML = 
                `<img src="${data.url}" class="image-preview" alt="Favicon">`;
            fileInput.value = '';
        }
    });
}

// Send test email
function sendTestEmail() {
    const email = document.getElementById('testEmailAddress').value;
    if (!email) {
        alert('Please enter an email address');
        return;
    }
    
    fetch('/admin/settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ to_email: email })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    });
}

// Clear cache
function clearCache() {
    if (confirm('Clear all caches?')) {
        fetch('/admin/settings/clear-cache', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
        });
    }
}

// Load on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadAllSettings();
});
</script>
@endsection
