@extends('admin.layouts.app')

@section('title', 'Content Management System')

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
        cursor: pointer;
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
    
    .page-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-bottom: 15px;
        border-left: 4px solid #3498db;
    }
    
    .page-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .page-card.inactive {
        opacity: 0.6;
        border-left-color: #95a5a6;
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
    
    .image-preview {
        display: inline-block;
        position: relative;
        margin: 10px;
    }
    
    .image-preview img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .image-preview .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
    }
    
    .link-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">📝 Content Management System</h1>
        <button class="btn btn-secondary" onclick="loadStats()">
            <i class="fas fa-sync"></i> Refresh
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card bg-gradient-primary" onclick="switchTab('pagesTab')">
            <div class="icon"><i class="fas fa-file"></i></div>
            <div class="number" id="statTotal">0</div>
            <div class="label">Total Pages</div>
        </div>
        <div class="stat-card bg-gradient-success" onclick="switchTab('pagesTab')">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number" id="statActive">0</div>
            <div class="label">Active Pages</div>
        </div>
        <div class="stat-card bg-gradient-warning" onclick="switchTab('headerTab')">
            <div class="icon"><i class="fas fa-link"></i></div>
            <div class="number" id="statHeaderLinks">0</div>
            <div class="label">Header Links</div>
        </div>
        <div class="stat-card bg-gradient-info" onclick="switchTab('footerTab')">
            <div class="icon"><i class="fas fa-link"></i></div>
            <div class="number" id="statFooterLinks">0</div>
            <div class="label">Footer Links</div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="homepageTabLink" data-bs-toggle="tab" href="#homepageTab">
                <i class="fas fa-home"></i> Homepage Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pagesTabLink" data-bs-toggle="tab" href="#pagesTab">
                <i class="fas fa-file"></i> Pages
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="headerTabLink" data-bs-toggle="tab" href="#headerTab">
                <i class="fas fa-bars"></i> Header Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="footerTabLink" data-bs-toggle="tab" href="#footerTab">
                <i class="fas fa-align-justify"></i> Footer
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="seoTabLink" data-bs-toggle="tab" href="#seoTab">
                <i class="fas fa-search"></i> SEO Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="blogTabLink" data-bs-toggle="tab" href="#blogTab">
                <i class="fas fa-blog"></i> Blog / News
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contactTabLink" data-bs-toggle="tab" href="#contactTab">
                <i class="fas fa-envelope"></i> Contact Page
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="emailTabLink" data-bs-toggle="tab" href="#emailTab">
                <i class="fas fa-mail-bulk"></i> Email Templates
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="faqTabLink" data-bs-toggle="tab" href="#faqTab">
                <i class="fas fa-question-circle"></i> FAQ Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="adsTabLink" data-bs-toggle="tab" href="#adsTab">
                <i class="fas fa-ad"></i> Advertisement Spaces
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="languageTabLink" data-bs-toggle="tab" href="#languageTab">
                <i class="fas fa-language"></i> Language & Text
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="legalTabLink" data-bs-toggle="tab" href="#legalTab">
                <i class="fas fa-gavel"></i> Legal & Compliance
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="announcementTabLink" data-bs-toggle="tab" href="#announcementTab">
                <i class="fas fa-bullhorn"></i> Announcements & Popups
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="mediaTabLink" data-bs-toggle="tab" href="#mediaTab">
                <i class="fas fa-photo-video"></i> Media Library
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="imagesTabLink" data-bs-toggle="tab" href="#imagesTab">
                <i class="fas fa-image"></i> Upload Images
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Homepage Management Tab -->
        <div class="tab-pane fade show active" id="homepageTab">
            <!-- Hero Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-image"></i> 🏠 Hero Section</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="heroEnabled">
                        <label class="form-check-label" for="heroEnabled">Enable Hero Section</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Main Heading</label>
                            <input type="text" class="form-control" id="heroMainHeading" placeholder="Find Your Perfect Vehicle">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sub Heading</label>
                            <input type="text" class="form-control" id="heroSubHeading" placeholder="Browse thousands of listings">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CTA Button Text</label>
                            <input type="text" class="form-control" id="heroCtaText" placeholder="Browse Listings">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CTA Button Link</label>
                            <input type="text" class="form-control" id="heroCtaLink" placeholder="/listings">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Background Image/Video URL</label>
                        <input type="text" class="form-control" id="heroBackground" placeholder="https://... or /storage/...">
                        <small class="text-muted">Enter image URL or upload via Images tab</small>
                    </div>
                    <button class="btn btn-success" onclick="saveHeroSection()">
                        <i class="fas fa-save"></i> Save Hero Section
                    </button>
                </div>
            </div>

            <!-- Banner Slider -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-sliders-h"></i> 🎞 Homepage Banner Slider</h5>
                </div>
                <div class="card-body">
                    <div id="bannersContainer"></div>
                    <button class="btn btn-primary mt-3" onclick="showAddBannerModal()">
                        <i class="fas fa-plus"></i> Add Banner
                    </button>
                </div>
            </div>

            <!-- Featured Sections -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-star"></i> 📊 Featured Sections</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Featured Ads Title</label>
                            <input type="text" class="form-control" id="featuredAdsTitle" placeholder="Featured Listings">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="showFeaturedAds">
                                <label class="form-check-label" for="showFeaturedAds">Show Section</label>
                            </div>
                            <div class="mt-2">
                                <label class="form-label">Max Items</label>
                                <input type="number" class="form-control" id="featuredAdsMax" value="8" min="1" max="20">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Featured Dealers Title</label>
                            <input type="text" class="form-control" id="featuredDealersTitle" placeholder="Top Dealers">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="showFeaturedDealers">
                                <label class="form-check-label" for="showFeaturedDealers">Show Section</label>
                            </div>
                            <div class="mt-2">
                                <label class="form-label">Max Items</label>
                                <input type="number" class="form-control" id="featuredDealersMax" value="6" min="1" max="20">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Popular Categories Title</label>
                            <input type="text" class="form-control" id="popularCategoriesTitle" placeholder="Browse by Category">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="showPopularCategories">
                                <label class="form-check-label" for="showPopularCategories">Show Section</label>
                            </div>
                            <div class="mt-2">
                                <label class="form-label">Max Items</label>
                                <input type="number" class="form-control" id="popularCategoriesMax" value="12" min="1" max="30">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="saveFeaturedSections()">
                        <i class="fas fa-save"></i> Save Featured Sections
                    </button>
                </div>
            </div>

            <!-- Custom Homepage Blocks -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-th-large"></i> 🧾 Custom Homepage Blocks</h5>
                </div>
                <div class="card-body">
                    <div id="customBlocksContainer"></div>
                    <button class="btn btn-primary mt-3" onclick="showAddCustomBlockModal()">
                        <i class="fas fa-plus"></i> Add Custom Block
                    </button>
                </div>
            </div>
        </div>

        <!-- Pages Tab -->
        <div class="tab-pane fade" id="pagesTab">
            <div class="mb-3">
                <button class="btn btn-primary" onclick="showCreatePageModal()">
                    <i class="fas fa-plus"></i> Create New Page
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>System Pages</h5>
                    <div id="systemPagesContainer"></div>
                </div>
                <div class="col-md-6">
                    <h5>Custom Pages</h5>
                    <div id="customPagesContainer"></div>
                </div>
            </div>
        </div>

        <!-- Header Management Tab -->
        <div class="tab-pane fade" id="headerTab">
            <!-- Logo & Favicon Upload -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-image"></i> Upload Logo</h5>
                        </div>
                        <div class="card-body">
                            <div id="currentLogo" class="mb-3"></div>
                            <input type="file" id="logoUpload" accept="image/*" class="form-control mb-3">
                            <button class="btn btn-primary" onclick="uploadLogo()">
                                <i class="fas fa-upload"></i> Upload Logo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-bookmark"></i> Upload Favicon</h5>
                        </div>
                        <div class="card-body">
                            <div id="currentFavicon" class="mb-3"></div>
                            <input type="file" id="faviconUpload" accept="image/x-icon,image/png" class="form-control mb-3">
                            <button class="btn btn-primary" onclick="uploadFavicon()">
                                <i class="fas fa-upload"></i> Upload Favicon
                            </button>
                            <small class="text-muted d-block mt-2">Recommended: 32x32px .ico or .png file</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcement Bar -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-bullhorn"></i> Announcement Bar</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Announcement Text</label>
                            <input type="text" class="form-control" id="announcementText" placeholder="Special offer: Get 20% off this week!">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Background Color</label>
                            <input type="color" class="form-control" id="announcementBgColor" value="#ffc107">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Text Color</label>
                            <input type="color" class="form-control" id="announcementTextColor" value="#000000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="announcementCloseable" checked>
                                <label class="form-check-label" for="announcementCloseable">Show Close Button</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="announcementEnabled">
                                <label class="form-check-label" for="announcementEnabled">Enable Announcement</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="saveAnnouncementBar()">
                        <i class="fas fa-save"></i> Save Announcement Bar
                    </button>
                </div>
            </div>

            <!-- Navigation Menu Items -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bars"></i> Manage Header Navigation Menu</h5>
                </div>
                <div class="card-body">
                    <div id="headerLinksContainer"></div>
                    <button class="btn btn-primary mt-3" onclick="addHeaderLink()">
                        <i class="fas fa-plus"></i> Add Custom Menu Link
                    </button>
                    <button class="btn btn-success mt-3" onclick="saveHeaderLinks()">
                        <i class="fas fa-save"></i> Save Menu Changes
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Management Tab -->
        <div class="tab-pane fade" id="footerTab">
            <!-- Footer Logo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-image"></i> Footer Logo</h5>
                </div>
                <div class="card-body">
                    <div id="currentFooterLogo" class="mb-3"></div>
                    <input type="file" id="footerLogoUpload" accept="image/*" class="form-control mb-3">
                    <button class="btn btn-primary" onclick="uploadFooterLogo()">
                        <i class="fas fa-upload"></i> Upload Footer Logo
                    </button>
                </div>
            </div>

            <!-- About Text Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle"></i> About Section</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">About Title</label>
                        <input type="text" class="form-control" id="footerAboutTitle" placeholder="About Us">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">About Text</label>
                        <textarea class="form-control" id="footerAboutText" rows="4" placeholder="Brief description about your company..."></textarea>
                    </div>
                    <button class="btn btn-success" onclick="saveFooterAbout()">
                        <i class="fas fa-save"></i> Save About Section
                    </button>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-link"></i> Quick Links</h5>
                </div>
                <div class="card-body">
                    <div id="quickLinksContainer"></div>
                    <button class="btn btn-primary mt-3" onclick="addQuickLink()">
                        <i class="fas fa-plus"></i> Add Quick Link
                    </button>
                    <button class="btn btn-success mt-3" onclick="saveQuickLinks()">
                        <i class="fas fa-save"></i> Save Quick Links
                    </button>
                </div>
            </div>

            <!-- Popular Categories Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-tags"></i> Popular Categories</h5>
                </div>
                <div class="card-body">
                    <div id="popularCategoriesContainer"></div>
                    <button class="btn btn-primary mt-3" onclick="addPopularCategory()">
                        <i class="fas fa-plus"></i> Add Category Link
                    </button>
                    <button class="btn btn-success mt-3" onclick="savePopularCategories()">
                        <i class="fas fa-save"></i> Save Categories
                    </button>
                </div>
            </div>

            <!-- Contact Info & Social Media -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-address-book"></i> Contact Information & Social Media</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contactEmail">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" id="contactPhone">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="contactAddress" rows="2"></textarea>
                        </div>
                    </div>
                    <hr>
                    <h6>Social Media Links</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-facebook"></i> Facebook URL</label>
                            <input type="url" class="form-control" id="socialFacebook">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-twitter"></i> Twitter URL</label>
                            <input type="url" class="form-control" id="socialTwitter">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-instagram"></i> Instagram URL</label>
                            <input type="url" class="form-control" id="socialInstagram">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-youtube"></i> YouTube URL</label>
                            <input type="url" class="form-control" id="socialYoutube">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                            <input type="url" class="form-control" id="socialLinkedin">
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="saveContactInfo()">
                        <i class="fas fa-save"></i> Save Contact Info & Social Media
                    </button>
                </div>
            </div>

            <!-- Copyright & Newsletter -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-copyright"></i> Copyright Text</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Copyright Text</label>
                                <input type="text" class="form-control" id="copyrightText" placeholder="© 2026 Your Company. All rights reserved.">
                            </div>
                            <button class="btn btn-success" onclick="saveCopyrightText()">
                                <i class="fas fa-save"></i> Save Copyright
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-envelope"></i> Newsletter Section</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Newsletter Title</label>
                                <input type="text" class="form-control" id="newsletterTitle" placeholder="Subscribe to Newsletter">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Newsletter Description</label>
                                <input type="text" class="form-control" id="newsletterDescription" placeholder="Get updates and special offers">
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="newsletterEnabled">
                                <label class="form-check-label" for="newsletterEnabled">Enable Newsletter Section</label>
                            </div>
                            <button class="btn btn-success" onclick="saveNewsletterSettings()">
                                <i class="fas fa-save"></i> Save Newsletter Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Management Tab -->
        <div class="tab-pane fade" id="seoTab">
            <!-- Global SEO Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-globe"></i> Global SEO Settings</h5>
                    <small class="text-muted">Default SEO settings applied across your website</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Default Meta Title</label>
                            <input type="text" class="form-control" id="defaultMetaTitle" placeholder="Your Site Name - Tagline">
                            <small class="text-muted">Recommended: 50-60 characters</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Default Meta Description</label>
                            <textarea class="form-control" id="defaultMetaDescription" rows="3" placeholder="Brief description of your website"></textarea>
                            <small class="text-muted">Recommended: 150-160 characters</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Default Meta Keywords</label>
                            <input type="text" class="form-control" id="defaultMetaKeywords" placeholder="keyword1, keyword2, keyword3">
                            <small class="text-muted">Comma-separated keywords</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">OG Image (Open Graph)</label>
                            <div id="currentOgImage" class="mb-2"></div>
                            <input type="file" class="form-control" id="ogImageUpload" accept="image/*">
                            <button class="btn btn-primary btn-sm mt-2" onclick="uploadOgImage()">
                                <i class="fas fa-upload"></i> Upload OG Image
                            </button>
                            <small class="d-block text-muted mt-1">Recommended: 1200x630px</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sitemap & Robots</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="sitemapEnabled">
                                <label class="form-check-label" for="sitemapEnabled">
                                    Enable XML Sitemap
                                </label>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm" onclick="editRobotsTxt()">
                                <i class="fas fa-robot"></i> Edit Robots.txt
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="saveGlobalSeo()">
                        <i class="fas fa-save"></i> Save Global SEO Settings
                    </button>
                </div>
            </div>

            <!-- Page-Level SEO -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-alt"></i> Page-Level SEO</h5>
                    <small class="text-muted">Customize SEO settings for individual pages</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Page Title</th>
                                    <th>Meta Title</th>
                                    <th>Meta Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pageSeoList">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Loading pages...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blog / News Management Tab -->
        <div class="tab-pane fade" id="blogTab">
            <!-- Blog Posts List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-blog"></i> Blog Posts</h5>
                    <button class="btn btn-primary" onclick="showCreateBlogModal()">
                        <i class="fas fa-plus"></i> Add New Post
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-control" id="blogStatusFilter" onchange="loadBlogPosts()">
                                <option value="">All Posts</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="blogCategoryFilter" onchange="loadBlogPosts()">
                                <option value="">All Categories</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="blogSearchInput" placeholder="Search posts..." onkeyup="loadBlogPosts()">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Published Date</th>
                                    <th>Views</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="blogPostsList">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Loading posts...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Categories & Tags Management -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-folder"></i> Categories</h5>
                            <button class="btn btn-sm btn-primary" onclick="showAddCategoryModal()">
                                <i class="fas fa-plus"></i> Add Category
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="blogCategoriesList"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-tags"></i> Tags</h5>
                            <button class="btn btn-sm btn-primary" onclick="showAddTagModal()">
                                <i class="fas fa-plus"></i> Add Tag
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="blogTagsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Page Management Tab -->
        <div class="tab-pane fade" id="contactTab">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-id-card"></i> Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Office Address</label>
                            <textarea class="form-control" id="contactAddress" rows="3" placeholder="Enter full address"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="contactPhone" placeholder="+977-XXX-XXXXXXX">
                            <small class="text-muted">Primary contact number</small>
                            <input type="text" class="form-control mt-2" id="contactPhoneAlt" placeholder="Alternate phone (optional)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contactEmail" placeholder="info@example.com">
                            <small class="text-muted">Primary contact email</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Support Email (Optional)</label>
                            <input type="email" class="form-control" id="contactSupportEmail" placeholder="support@example.com">
                        </div>
                    </div>
                    <button class="btn btn-success" onclick="saveContactInfo()">
                        <i class="fas fa-save"></i> Save Contact Information
                    </button>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-map-marked-alt"></i> Google Map Integration</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Google Map Embed Code</label>
                        <textarea class="form-control" id="contactMapEmbed" rows="5" placeholder='<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450"></iframe>'></textarea>
                        <small class="text-muted">Go to Google Maps → Share → Embed a map → Copy HTML</small>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="contactMapEnabled">
                        <label class="form-check-label" for="contactMapEnabled">Enable Google Map Display</label>
                    </div>
                    <button class="btn btn-success" onclick="saveContactMap()">
                        <i class="fas fa-save"></i> Save Map Settings
                    </button>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-edit"></i> Contact Page Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Page Heading</label>
                        <input type="text" class="form-control" id="contactPageHeading" placeholder="Get In Touch">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Page Description</label>
                        <textarea class="form-control" id="contactPageDescription" rows="4" placeholder="Brief description or welcome message"></textarea>
                    </div>
                    <button class="btn btn-success" onclick="saveContactPageContent()">
                        <i class="fas fa-save"></i> Save Page Content
                    </button>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-paper-plane"></i> Contact Form Settings</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="contactFormEnabled">
                        <label class="form-check-label" for="contactFormEnabled">Enable Contact Form</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Form Submission Email</label>
                        <input type="email" class="form-control" id="contactFormEmail" placeholder="Where to send form submissions">
                        <small class="text-muted">Contact form submissions will be sent to this email</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Success Message</label>
                        <textarea class="form-control" id="contactFormSuccessMessage" rows="2" placeholder="Thank you for contacting us. We'll get back to you soon!"></textarea>
                    </div>
                    <button class="btn btn-success" onclick="saveContactFormSettings()">
                        <i class="fas fa-save"></i> Save Form Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Email Templates Tab -->
        <div class="tab-pane fade" id="emailTab">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Customize email templates sent to users. Changes take effect immediately.
            </div>

            <!-- Email Template Selector -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Select Email Template</h5>
                </div>
                <div class="card-body">
                    <select id="emailTemplateType" class="form-select" onchange="loadEmailTemplate()">
                        <option value="welcome">Welcome Email (New User Registration)</option>
                        <option value="ad_approved">Ad Approved Email</option>
                        <option value="ad_rejected">Ad Rejected Email</option>
                        <option value="password_reset">Password Reset Email</option>
                        <option value="dealer_approval">Dealer Approval Email</option>
                        <option value="promotion_confirmation">Promotion Confirmation Email</option>
                    </select>
                </div>
            </div>

            <!-- Email Template Editor -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit"></i> Edit Email Template</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Subject Line</label>
                        <input type="text" id="emailSubject" class="form-control" placeholder="Email subject">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Content</label>
                        <textarea id="emailContent" class="summernote"></textarea>
                        <small class="text-muted">Available variables: {name}, {email}, {ad_title}, {reason}, {reset_link}, {site_name}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Footer</label>
                        <textarea id="emailFooter" class="form-control" rows="3" placeholder="Footer text (optional)"></textarea>
                    </div>
                    <button class="btn btn-primary" onclick="saveEmailTemplate()">
                        <i class="fas fa-save"></i> Save Template
                    </button>
                    <button class="btn btn-secondary" onclick="resetEmailTemplate()">
                        <i class="fas fa-undo"></i> Reset to Default
                    </button>
                </div>
            </div>

            <!-- Email Preview -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-eye"></i> Email Preview</h5>
                </div>
                <div class="card-body">
                    <div id="emailPreview" class="border p-3" style="background: #f8f9fa; min-height: 200px;">
                        <p class="text-muted">Select a template to preview</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Management Tab -->
        <div class="tab-pane fade" id="faqTab">
            <div class="row">
                <!-- FAQ List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-list"></i> FAQ List</h5>
                            <button class="btn btn-primary btn-sm" onclick="openFaqModal()">
                                <i class="fas fa-plus"></i> Add FAQ
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <select id="faqCategoryFilter" class="form-select" onchange="loadFaqs()">
                                    <option value="">All Categories</option>
                                </select>
                            </div>
                            <div id="faqList" class="list-group sortable-faq">
                                <p class="text-muted">Loading FAQs...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Categories -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-folder"></i> FAQ Categories</h5>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <input type="text" id="newFaqCategory" class="form-control" placeholder="Category name">
                                <button class="btn btn-primary" onclick="addFaqCategory()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div id="faqCategoriesList" class="list-group">
                                <p class="text-muted">Loading categories...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advertisement Spaces Tab -->
        <div class="tab-pane fade" id="adsTab">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Manage advertisement spaces across your website. Add ad codes (HTML/JavaScript) and control visibility.
            </div>

            <!-- Header Ad Space -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-arrow-up"></i> Header Advertisement</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="headerAdEnabled">
                        <label class="form-check-label">Enable</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Code (HTML/JavaScript)</label>
                        <textarea id="headerAdCode" class="form-control" rows="5" placeholder="Paste your ad code here..."></textarea>
                        <small class="text-muted">Displayed at the top of every page</small>
                    </div>
                    <button class="btn btn-primary" onclick="saveAdSpace('header')">
                        <i class="fas fa-save"></i> Save Header Ad
                    </button>
                </div>
            </div>

            <!-- Homepage Ad Spaces -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-home"></i> Homepage Ad Spaces</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="homepageAdsEnabled">
                        <label class="form-check-label">Enable</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Top Banner (After Hero Section)</label>
                            <textarea id="homepageTopAd" class="form-control" rows="4" placeholder="Ad code..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Middle Banner (Between Listings)</label>
                            <textarea id="homepageMiddleAd" class="form-control" rows="4" placeholder="Ad code..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bottom Banner (Before Footer)</label>
                            <textarea id="homepageBottomAd" class="form-control" rows="4" placeholder="Ad code..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sidebar Ad</label>
                            <textarea id="homepageSidebarAd" class="form-control" rows="4" placeholder="Ad code..."></textarea>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="saveAdSpace('homepage')">
                        <i class="fas fa-save"></i> Save Homepage Ads
                    </button>
                </div>
            </div>

            <!-- Sidebar Ad Space -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-columns"></i> Global Sidebar Advertisement</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="sidebarAdEnabled">
                        <label class="form-check-label">Enable</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Code (HTML/JavaScript)</label>
                        <textarea id="sidebarAdCode" class="form-control" rows="5" placeholder="Paste your ad code here..."></textarea>
                        <small class="text-muted">Displayed in sidebar on listing and detail pages</small>
                    </div>
                    <button class="btn btn-primary" onclick="saveAdSpace('sidebar')">
                        <i class="fas fa-save"></i> Save Sidebar Ad
                    </button>
                </div>
            </div>

            <!-- Footer Ad Space -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-arrow-down"></i> Footer Advertisement</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="footerAdEnabled">
                        <label class="form-check-label">Enable</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Code (HTML/JavaScript)</label>
                        <textarea id="footerAdCode" class="form-control" rows="5" placeholder="Paste your ad code here..."></textarea>
                        <small class="text-muted">Displayed at the bottom of every page, before footer</small>
                    </div>
                    <button class="btn btn-primary" onclick="saveAdSpace('footer')">
                        <i class="fas fa-save"></i> Save Footer Ad
                    </button>
                </div>
            </div>

            <!-- Listing Detail Ad Space -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-file-alt"></i> Listing Detail Page Ad</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="listingDetailAdEnabled">
                        <label class="form-check-label">Enable</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ad Code (HTML/JavaScript)</label>
                        <textarea id="listingDetailAdCode" class="form-control" rows="5" placeholder="Paste your ad code here..."></textarea>
                        <small class="text-muted">Displayed on individual listing detail pages</small>
                    </div>
                    <button class="btn btn-primary" onclick="saveAdSpace('listing_detail')">
                        <i class="fas fa-save"></i> Save Listing Detail Ad
                    </button>
                </div>
            </div>
        </div>

        <!-- Language & Text Control Tab -->
        <div class="tab-pane fade" id="languageTab">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Customize all text labels, buttons, and messages throughout your website.
            </div>

            <!-- Text Category Selector -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Select Text Category</h5>
                </div>
                <div class="card-body">
                    <select id="textCategory" class="form-select" onchange="loadTextCategory()">
                        <option value="system_labels">System Labels</option>
                        <option value="button_text">Button Text</option>
                        <option value="error_messages">Error Messages</option>
                        <option value="form_labels">Form Labels</option>
                        <option value="navigation">Navigation Menu</option>
                        <option value="listing_text">Listing Related Text</option>
                    </select>
                </div>
            </div>

            <!-- System Labels -->
            <div id="systemLabelsSection" class="text-section">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-tags"></i> System Labels</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Home" Label</label>
                                <input type="text" class="form-control" data-key="label_home" placeholder="Home">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"About Us" Label</label>
                                <input type="text" class="form-control" data-key="label_about" placeholder="About Us">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Contact" Label</label>
                                <input type="text" class="form-control" data-key="label_contact" placeholder="Contact">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Categories" Label</label>
                                <input type="text" class="form-control" data-key="label_categories" placeholder="Categories">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Featured" Label</label>
                                <input type="text" class="form-control" data-key="label_featured" placeholder="Featured">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Latest" Label</label>
                                <input type="text" class="form-control" data-key="label_latest" placeholder="Latest">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Popular" Label</label>
                                <input type="text" class="form-control" data-key="label_popular" placeholder="Popular">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Search" Label</label>
                                <input type="text" class="form-control" data-key="label_search" placeholder="Search">
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="saveTextLabels('system_labels')">
                            <i class="fas fa-save"></i> Save System Labels
                        </button>
                    </div>
                </div>
            </div>

            <!-- Button Text -->
            <div id="buttonTextSection" class="text-section" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-mouse-pointer"></i> Button Text</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Submit" Button</label>
                                <input type="text" class="form-control" data-key="btn_submit" placeholder="Submit">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Cancel" Button</label>
                                <input type="text" class="form-control" data-key="btn_cancel" placeholder="Cancel">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Save" Button</label>
                                <input type="text" class="form-control" data-key="btn_save" placeholder="Save">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Delete" Button</label>
                                <input type="text" class="form-control" data-key="btn_delete" placeholder="Delete">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Edit" Button</label>
                                <input type="text" class="form-control" data-key="btn_edit" placeholder="Edit">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"View Details" Button</label>
                                <input type="text" class="form-control" data-key="btn_view_details" placeholder="View Details">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Contact Seller" Button</label>
                                <input type="text" class="form-control" data-key="btn_contact_seller" placeholder="Contact Seller">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Post Ad" Button</label>
                                <input type="text" class="form-control" data-key="btn_post_ad" placeholder="Post Ad">
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="saveTextLabels('button_text')">
                            <i class="fas fa-save"></i> Save Button Text
                        </button>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            <div id="errorMessagesSection" class="text-section" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error Messages</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">"Required Field" Error</label>
                                <input type="text" class="form-control" data-key="error_required" placeholder="This field is required">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">"Invalid Email" Error</label>
                                <input type="text" class="form-control" data-key="error_invalid_email" placeholder="Please enter a valid email address">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">"Password Mismatch" Error</label>
                                <input type="text" class="form-control" data-key="error_password_mismatch" placeholder="Passwords do not match">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">"Upload Failed" Error</label>
                                <input type="text" class="form-control" data-key="error_upload_failed" placeholder="File upload failed">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">"Not Found" Error</label>
                                <input type="text" class="form-control" data-key="error_not_found" placeholder="Item not found">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">"Permission Denied" Error</label>
                                <input type="text" class="form-control" data-key="error_permission_denied" placeholder="You don't have permission to perform this action">
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="saveTextLabels('error_messages')">
                            <i class="fas fa-save"></i> Save Error Messages
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Labels -->
            <div id="formLabelsSection" class="text-section" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-wpforms"></i> Form Labels</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Name" Field</label>
                                <input type="text" class="form-control" data-key="form_name" placeholder="Name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Email" Field</label>
                                <input type="text" class="form-control" data-key="form_email" placeholder="Email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Password" Field</label>
                                <input type="text" class="form-control" data-key="form_password" placeholder="Password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Phone" Field</label>
                                <input type="text" class="form-control" data-key="form_phone" placeholder="Phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Address" Field</label>
                                <input type="text" class="form-control" data-key="form_address" placeholder="Address">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Message" Field</label>
                                <input type="text" class="form-control" data-key="form_message" placeholder="Message">
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="saveTextLabels('form_labels')">
                            <i class="fas fa-save"></i> Save Form Labels
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div id="navigationSection" class="text-section" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-bars"></i> Navigation Menu Text</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"My Listings" Menu</label>
                                <input type="text" class="form-control" data-key="nav_my_listings" placeholder="My Listings">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"My Profile" Menu</label>
                                <input type="text" class="form-control" data-key="nav_my_profile" placeholder="My Profile">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Dashboard" Menu</label>
                                <input type="text" class="form-control" data-key="nav_dashboard" placeholder="Dashboard">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Logout" Menu</label>
                                <input type="text" class="form-control" data-key="nav_logout" placeholder="Logout">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Login" Menu</label>
                                <input type="text" class="form-control" data-key="nav_login" placeholder="Login">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Register" Menu</label>
                                <input type="text" class="form-control" data-key="nav_register" placeholder="Register">
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="saveTextLabels('navigation')">
                            <i class="fas fa-save"></i> Save Navigation Text
                        </button>
                    </div>
                </div>
            </div>

            <!-- Listing Text -->
            <div id="listingTextSection" class="text-section" style="display: none;">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-list-alt"></i> Listing Related Text</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Price" Label</label>
                                <input type="text" class="form-control" data-key="listing_price" placeholder="Price">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Location" Label</label>
                                <input type="text" class="form-control" data-key="listing_location" placeholder="Location">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Condition" Label</label>
                                <input type="text" class="form-control" data-key="listing_condition" placeholder="Condition">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Description" Label</label>
                                <input type="text" class="form-control" data-key="listing_description" placeholder="Description">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Posted" Label</label>
                                <input type="text" class="form-control" data-key="listing_posted" placeholder="Posted">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">"Views" Label</label>
                                <input type="text" class="form-control" data-key="listing_views" placeholder="Views">
                            </div>
                        </div>
                        <button class="btn btn-primary" onclick="saveTextLabels('listing_text')">
                            <i class="fas fa-save"></i> Save Listing Text
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legal & Compliance Tab -->
        <div class="tab-pane fade" id="legalTab">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Manage cookie policy, GDPR compliance, and terms acceptance for your website.
            </div>

            <!-- Cookie Policy -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-cookie-bite"></i> Cookie Policy</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cookie Policy Title</label>
                        <input type="text" id="cookiePolicyTitle" class="form-control" placeholder="Cookie Policy">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cookie Policy Content</label>
                        <textarea id="cookiePolicyContent" class="summernote"></textarea>
                        <small class="text-muted">Full cookie policy text displayed on dedicated page</small>
                    </div>
                    <button class="btn btn-primary" onclick="saveLegalSection('cookie_policy')">
                        <i class="fas fa-save"></i> Save Cookie Policy
                    </button>
                </div>
            </div>

            <!-- Cookie Popup/Banner -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-exclamation-circle"></i> Cookie Consent Popup</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="cookiePopupEnabled">
                        <label class="form-check-label">Enable Cookie Popup</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Popup Message</label>
                        <textarea id="cookiePopupMessage" class="form-control" rows="3" placeholder="We use cookies to enhance your browsing experience..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Accept Button Text</label>
                            <input type="text" id="cookieAcceptBtn" class="form-control" placeholder="Accept All">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Decline Button Text</label>
                            <input type="text" id="cookieDeclineBtn" class="form-control" placeholder="Decline">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">"Learn More" Link Text</label>
                        <input type="text" id="cookieLearnMoreText" class="form-control" placeholder="Learn More">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Popup Position</label>
                        <select id="cookiePopupPosition" class="form-select">
                            <option value="bottom">Bottom</option>
                            <option value="top">Top</option>
                            <option value="center">Center (Modal)</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" onclick="saveLegalSection('cookie_popup')">
                        <i class="fas fa-save"></i> Save Cookie Popup Settings
                    </button>
                </div>
            </div>

            <!-- GDPR Notice -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-shield-alt"></i> GDPR Compliance Notice</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">GDPR Notice Title</label>
                        <input type="text" id="gdprTitle" class="form-control" placeholder="Privacy & Data Protection">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GDPR Notice Content</label>
                        <textarea id="gdprContent" class="summernote"></textarea>
                        <small class="text-muted">Explain how user data is collected, stored, and used</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Controller Contact Email</label>
                        <input type="email" id="gdprContactEmail" class="form-control" placeholder="privacy@yoursite.com">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="gdprShowDataRequest">
                        <label class="form-check-label">Allow users to request their data</label>
                    </div>
                    <button class="btn btn-primary" onclick="saveLegalSection('gdpr')">
                        <i class="fas fa-save"></i> Save GDPR Settings
                    </button>
                </div>
            </div>

            <!-- Terms & Conditions Acceptance -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-file-contract"></i> Terms Acceptance Control</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="termsAcceptanceRequired">
                        <label class="form-check-label">Require terms acceptance on registration</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Terms Checkbox Label</label>
                        <input type="text" id="termsCheckboxLabel" class="form-control" placeholder="I agree to the Terms and Conditions">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Terms Link Text</label>
                        <input type="text" id="termsLinkText" class="form-control" placeholder="Terms and Conditions">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Privacy Policy Link Text</label>
                        <input type="text" id="privacyLinkText" class="form-control" placeholder="Privacy Policy">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="newsletterOptin">
                        <label class="form-check-label">Show newsletter opt-in checkbox on registration</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Newsletter Opt-in Label</label>
                        <input type="text" id="newsletterOptinLabel" class="form-control" placeholder="I want to receive newsletters and updates">
                    </div>
                    <button class="btn btn-primary" onclick="saveLegalSection('terms_acceptance')">
                        <i class="fas fa-save"></i> Save Terms Acceptance Settings
                    </button>
                </div>
            </div>

            <!-- Privacy Policy -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user-shield"></i> Privacy Policy</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Privacy Policy Title</label>
                        <input type="text" id="privacyPolicyTitle" class="form-control" placeholder="Privacy Policy">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Privacy Policy Content</label>
                        <textarea id="privacyPolicyContent" class="summernote"></textarea>
                        <small class="text-muted">Full privacy policy text</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Updated Date</label>
                        <input type="date" id="privacyLastUpdated" class="form-control">
                    </div>
                    <button class="btn btn-primary" onclick="saveLegalSection('privacy_policy')">
                        <i class="fas fa-save"></i> Save Privacy Policy
                    </button>
                </div>
            </div>
        </div>

        <!-- Announcements & Popups Tab -->
        <div class="tab-pane fade" id="announcementTab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-bullhorn"></i> Announcement & Popup Control</h4>
                <button class="btn btn-primary" onclick="openAnnouncementModal()">
                    <i class="fas fa-plus"></i> Create New Popup
                </button>
            </div>

            <!-- Announcements List -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Active Announcements & Popups</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Schedule</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="announcementsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Library Tab -->
        <div class="tab-pane fade" id="mediaTab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-photo-video"></i> Media Library</h4>
                <div>
                    <button class="btn btn-success" onclick="openUploadMediaModal()">
                        <i class="fas fa-upload"></i> Upload Files
                    </button>
                    <button class="btn btn-primary" onclick="openCreateFolderModal()">
                        <i class="fas fa-folder-plus"></i> New Folder
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- Folders Sidebar -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-folder"></i> Folders</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="mediaFoldersList">
                                <a href="#" class="list-group-item list-group-item-action active" onclick="selectMediaFolder(null, event)">
                                    <i class="fas fa-home"></i> All Media
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Gallery -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>
                                <i class="fas fa-images"></i> 
                                <span id="currentFolderName">All Media</span>
                            </h5>
                            <div>
                                <select class="form-select form-select-sm" id="mediaTypeFilter" onchange="loadMediaLibrary()" style="width: 150px;">
                                    <option value="all">All Types</option>
                                    <option value="image">Images Only</option>
                                    <option value="video">Videos Only</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row" id="mediaGallery">
                                <div class="col-12 text-center py-5">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Images Tab -->
        <div class="tab-pane fade" id="imagesTab">
            <div class="card">
                <div class="card-header">
                    <h5>Upload Logo</h5>
                </div>
                <div class="card-body">
                    <input type="file" id="logoUpload" accept="image/*" class="form-control mb-3">
                    <button class="btn btn-primary" onclick="uploadLogo()">
                        <i class="fas fa-upload"></i> Upload Logo
                    </button>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Page Images</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Select Page</label>
                        <select class="form-control" id="pageForImages" onchange="loadPageImages()">
                            <option value="">Select a page...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="file" id="pageImageUpload" accept="image/*" class="form-control">
                        <button class="btn btn-primary mt-2" onclick="uploadPageImage()">
                            <i class="fas fa-upload"></i> Upload Image
                        </button>
                    </div>
                    <div id="pageImagesContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Page Modal -->
<div class="modal fade" id="pageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pageModalTitle">Create Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageForm">
                <input type="hidden" id="pageId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Title*</label>
                                <input type="text" class="form-control" id="pageTitle" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug*</label>
                                <input type="text" class="form-control" id="pageSlug" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Excerpt</label>
                                <textarea class="form-control" id="pageExcerpt" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content (Rich Text Editor)</label>
                                <textarea class="form-control" id="pageContent" rows="15"></textarea>
                                <small class="text-muted">Use the rich text editor toolbar to format your content</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Add Images</label>
                                <input type="file" class="form-control" id="pageImageUploadField" accept="image/*" multiple>
                                <small class="text-muted">Upload images to use in your page content</small>
                                <div id="uploadedImagesPreview" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Type*</label>
                                <select class="form-control" id="pageType" required>
                                    <option value="page">Page</option>
                                    <option value="policy">Policy</option>
                                    <option value="help">Help</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Order</label>
                                <input type="number" class="form-control" id="pageOrder" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="pageMetaTitle">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" id="pageMetaDescription" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="pageIsActive" checked>
                                    <label class="form-check-label" for="pageIsActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Page</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Banner Image URL</label>
                    <input type="text" class="form-control" id="bannerImageUrl" placeholder="https://... or /storage/...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Banner Title</label>
                    <input type="text" class="form-control" id="bannerTitle">
                </div>
                <div class="mb-3">
                    <label class="form-label">Button Text</label>
                    <input type="text" class="form-control" id="bannerButtonText" placeholder="Learn More">
                </div>
                <div class="mb-3">
                    <label class="form-label">Button Link</label>
                    <input type="text" class="form-control" id="bannerButtonLink" placeholder="/page">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priority Order</label>
                        <input type="number" class="form-control" id="bannerOrder" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="bannerActive" checked>
                            <label class="form-check-label" for="bannerActive">Active</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date (Optional)</label>
                        <input type="date" class="form-control" id="bannerStartDate">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date (Optional)</label>
                        <input type="date" class="form-control" id="bannerEndDate">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBanner()">Save Banner</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Custom Block Modal -->
<div class="modal fade" id="customBlockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Custom Block</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Block Title</label>
                    <input type="text" class="form-control" id="blockTitle">
                </div>
                <div class="mb-3">
                    <label class="form-label">Block Content</label>
                    <textarea class="form-control" id="blockContent" rows="6" placeholder="Enter HTML or text content"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image URL (Optional)</label>
                    <input type="text" class="form-control" id="blockImageUrl" placeholder="https://... or /storage/...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Button Text</label>
                    <input type="text" class="form-control" id="blockButtonText" placeholder="Read More">
                </div>
                <div class="mb-3">
                    <label class="form-label">Button Link</label>
                    <input type="text" class="form-control" id="blockButtonLink" placeholder="/page">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="blockOrder" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="blockActive" checked>
                            <label class="form-check-label" for="blockActive">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomBlock()">Save Block</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Page SEO Modal -->
<div class="modal fade" id="pageSeoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Page SEO: <span id="pageSeoTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="seoPageId">
                <div class="mb-3">
                    <label class="form-label">Custom Meta Title</label>
                    <input type="text" class="form-control" id="pageMetaTitle" placeholder="Leave empty to use page title">
                    <small class="text-muted">Recommended: 50-60 characters</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Custom Meta Description</label>
                    <textarea class="form-control" id="pageMetaDescription" rows="3" placeholder="Leave empty to use default"></textarea>
                    <small class="text-muted">Recommended: 150-160 characters</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Custom Meta Keywords</label>
                    <input type="text" class="form-control" id="pageMetaKeywords" placeholder="keyword1, keyword2, keyword3">
                    <small class="text-muted">Comma-separated keywords</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePageSeo()">Save SEO Settings</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Robots.txt Modal -->
<div class="modal fade" id="robotsTxtModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-robot"></i> Edit Robots.txt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Define rules for search engine crawlers. Be careful - incorrect settings can prevent search engines from indexing your site.
                </div>
                <textarea class="form-control" id="robotsTxtContent" rows="15" style="font-family: monospace;"></textarea>
                <small class="text-muted">Example: User-agent: *, Disallow: /admin/, Allow: /</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveRobotsTxt()">Save Robots.txt</button>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Blog Post Modal -->
<div class="modal fade" id="blogPostModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blogPostModalTitle">Create Blog Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blogPostForm">
                <input type="hidden" id="blogPostId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Title*</label>
                                <input type="text" class="form-control" id="blogPostTitle" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" id="blogPostSlug">
                                <small class="text-muted">Auto-generated from title</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Excerpt</label>
                                <textarea class="form-control" id="blogPostExcerpt" rows="2" placeholder="Brief summary..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content*</label>
                                <textarea class="form-control" id="blogPostContent" rows="15"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="blogPostStatus">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Publish Date</label>
                                <input type="datetime-local" class="form-control" id="blogPostPublishedAt">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-control" id="blogPostCategory">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tags (comma-separated)</label>
                                <input type="text" class="form-control" id="blogPostTags" placeholder="tag1, tag2, tag3">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" class="form-control" id="blogFeaturedImageUpload" accept="image/*">
                                <div id="blogCurrentFeaturedImage" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="blogPostMetaTitle">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" id="blogPostMetaDescription" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="blogCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="categoryName">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBlogCategory()">Save Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Tag Modal -->
<div class="modal fade" id="blogTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tag Name</label>
                    <input type="text" class="form-control" id="tagName">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBlogTag()">Save Tag</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Summernote Rich Text Editor (Free & Open Source) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let headerLinks = [];
let footerLinks = [];

// Initialize Summernote for page content editor
function initSummernote() {
    $('#pageContent').summernote('destroy');
    $('#pageContent').summernote({
        height: 400,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    
    // Initialize Summernote for blog content editor
    $('#blogPostContent').summernote('destroy');
    $('#blogPostContent').summernote({
        height: 400,
        toolbar: [
            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
}

function switchTab(tabId) {
    document.getElementById(tabId + 'Link').click();
}

// Load stats
function loadStats() {
    fetch('/admin/pages/stats')
        .then(res => res.json())
        .then(data => {
            document.getElementById('statTotal').textContent = data.total;
            document.getElementById('statActive').textContent = data.active;
        });
    
    // Load header links count
    fetch('/admin/cms/header-links')
        .then(res => res.json())
        .then(data => {
            document.getElementById('statHeaderLinks').textContent = data.links.length;
        });
    
    // Load footer links count
    fetch('/admin/cms/footer')
        .then(res => res.json())
        .then(data => {
            document.getElementById('statFooterLinks').textContent = data.links.length;
        });
}

// Load system pages
function loadSystemPages() {
    fetch('/admin/pages/system')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('systemPagesContainer');
            container.innerHTML = '';
            
            const systemPages = ['about', 'contact', 'terms', 'privacy', 'faq', 'refund-policy', 'safety-tips', 'how-it-works'];
            const labels = {
                'about': 'About Us',
                'contact': 'Contact Us',
                'terms': 'Terms & Conditions',
                'privacy': 'Privacy Policy',
                'faq': 'FAQ',
                'refund-policy': 'Refund Policy',
                'safety-tips': 'Safety Tips',
                'how-it-works': 'How It Works'
            };
            
            systemPages.forEach(key => {
                const page = data[key];
                if (page) {
                    container.innerHTML += `
                        <div class="page-card ${page.is_active ? '' : 'inactive'}">
                            <h6 class="mb-2">${labels[key]}</h6>
                            <small class="text-muted">/${page.slug}</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-primary" onclick="editPage(${page.id})">
                                    <i class="fas fa-edit"></i> Edit Content
                                </button>
                                <button class="btn btn-sm ${page.is_active ? 'btn-warning' : 'btn-success'}" 
                                        onclick="togglePage(${page.id}, ${page.is_active})">
                                    <i class="fas fa-${page.is_active ? 'eye-slash' : 'eye'}"></i> 
                                    ${page.is_active ? 'Deactivate' : 'Activate'}
                                </button>
                            </div>
                        </div>
                    `;
                }
            });
        });
}

// Load custom pages
function loadCustomPages() {
    fetch('/admin/pages/all')
        .then(res => res.json())
        .then(data => {
            const systemSlugs = ['about', 'contact', 'terms', 'privacy', 'faq', 'refund-policy', 'safety-tips', 'how-it-works'];
            const customPages = data.filter(p => !systemSlugs.includes(p.slug));
            
            const container = document.getElementById('customPagesContainer');
            container.innerHTML = '';
            
            if (customPages.length === 0) {
                container.innerHTML = '<p class="text-muted">No custom pages yet</p>';
                return;
            }
            
            customPages.forEach(page => {
                container.innerHTML += `
                    <div class="page-card ${page.is_active ? '' : 'inactive'}">
                        <h6 class="mb-2">${page.title}</h6>
                        <small class="text-muted">/${page.slug}</small>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-primary" onclick="editPage(${page.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deletePage(${page.id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            });
        });
}

// Show create page modal
function showCreatePageModal() {
    document.getElementById('pageForm').reset();
    document.getElementById('pageId').value = '';
    document.getElementById('pageModalTitle').textContent = 'Create Page';
    document.getElementById('pageIsActive').checked = true;
    document.getElementById('uploadedImagesPreview').innerHTML = '';
    new bootstrap.Modal(document.getElementById('pageModal')).show();
    
    // Initialize Summernote after modal is shown
    setTimeout(() => {
        initSummernote();
    }, 300);
}

// Edit page
function editPage(id) {
    fetch(`/admin/pages/${id}/edit`)
        .then(res => res.json())
        .then(page => {
            document.getElementById('pageId').value = page.id;
            document.getElementById('pageTitle').value = page.title;
            document.getElementById('pageSlug').value = page.slug;
            document.getElementById('pageExcerpt').value = page.excerpt || '';
            document.getElementById('pageContent').value = page.content || '';
            document.getElementById('pageType').value = page.type;
            document.getElementById('pageOrder').value = page.order;
            document.getElementById('pageMetaTitle').value = page.meta_title || '';
            document.getElementById('pageMetaDescription').value = page.meta_description || '';
            document.getElementById('pageIsActive').checked = page.is_active;
            document.getElementById('pageModalTitle').textContent = 'Edit Page';
            document.getElementById('uploadedImagesPreview').innerHTML = '';
            new bootstrap.Modal(document.getElementById('pageModal')).show();
            
            // Initialize Summernote and set content
            setTimeout(() => {
                initSummernote();
                setTimeout(() => {
                    $('#pageContent').summernote('code', page.content || '');
                }, 500);
            }, 300);
        });
}

function togglePage(id, currentStatus) {
    fetch(`/admin/pages/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            loadSystemPages();
            loadCustomPages();
        }
    });
}

function deletePage(id) {
    if (confirm('Delete this page?')) {
        fetch(`/admin/pages/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                loadCustomPages();
                loadStats();
            }
        });
    }
}

// Insert image to Summernote editor
function insertImageToEditor(imageUrl) {
    $('#pageContent').summernote('insertImage', imageUrl, function($image) {
        $image.css('max-width', '100%');
        $image.attr('alt', 'Image');
    });
    alert('Image inserted into editor!');
}

// Header Links
function loadHeaderLinks() {
    fetch('/admin/cms/header-links')
        .then(res => res.json())
        .then(data => {
            headerLinks = data.links;
            renderHeaderLinks();
        });
}

function renderHeaderLinks() {
    const container = document.getElementById('headerLinksContainer');
    container.innerHTML = '';
    
    // Sort by order
    headerLinks.sort((a, b) => (a.order || 0) - (b.order || 0));
    
    headerLinks.forEach((link, index) => {
        const isEnabled = link.enabled !== false;
        container.innerHTML += `
            <div class="link-item mb-3 p-3 border rounded ${!isEnabled ? 'bg-light opacity-75' : ''}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" value="${link.label || ''}" 
                               onchange="headerLinks[${index}].label = this.value" placeholder="Menu Label">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">URL</label>
                        <input type="text" class="form-control" value="${link.url || ''}" 
                               onchange="headerLinks[${index}].url = this.value" placeholder="/page or https://...">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Order</label>
                        <input type="number" class="form-control" value="${link.order || (index + 1)}" 
                               onchange="headerLinks[${index}].order = parseInt(this.value); renderHeaderLinks()" placeholder="Order">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" ${isEnabled ? 'checked' : ''}
                                   onchange="headerLinks[${index}].enabled = this.checked; renderHeaderLinks()">
                            <label class="form-check-label">${isEnabled ? 'Enabled' : 'Disabled'}</label>
                        </div>
                    </div>
                    <div class="col-md-2 text-end">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-sm btn-secondary me-1" onclick="moveHeaderLink(${index}, 'up')" ${index === 0 ? 'disabled' : ''}>
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <button class="btn btn-sm btn-secondary me-1" onclick="moveHeaderLink(${index}, 'down')" ${index === headerLinks.length - 1 ? 'disabled' : ''}>
                            <i class="fas fa-arrow-down"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="removeHeaderLink(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    if (headerLinks.length === 0) {
        container.innerHTML = '<p class="text-muted">No menu items yet. Click "Add Custom Menu Link" to create one.</p>';
    }
}

function addHeaderLink() {
    headerLinks.push({ label: '', url: '', order: headerLinks.length + 1, enabled: true });
    renderHeaderLinks();
}

function moveHeaderLink(index, direction) {
    if (direction === 'up' && index > 0) {
        [headerLinks[index], headerLinks[index - 1]] = [headerLinks[index - 1], headerLinks[index]];
        // Update orders
        headerLinks[index].order = index + 1;
        headerLinks[index - 1].order = index;
    } else if (direction === 'down' && index < headerLinks.length - 1) {
        [headerLinks[index], headerLinks[index + 1]] = [headerLinks[index + 1], headerLinks[index]];
        // Update orders
        headerLinks[index].order = index + 1;
        headerLinks[index + 1].order = index + 2;
    }
    renderHeaderLinks();
}

function removeHeaderLink(index) {
    headerLinks.splice(index, 1);
    renderHeaderLinks();
}

function saveHeaderLinks() {
    fetch('/admin/cms/header-links', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ links: headerLinks })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadStats();
    });
}

// Logo & Favicon Management
function loadLogoAndFavicon() {
    fetch('/admin/cms/logo-favicon')
        .then(res => res.json())
        .then(data => {
            if (data.logo) {
                document.getElementById('currentLogo').innerHTML = `
                    <img src="${data.logo}" alt="Current Logo" style="max-height: 80px;" class="mb-2">
                    <p class="text-muted small">Current Logo</p>
                `;
            }
            if (data.favicon) {
                document.getElementById('currentFavicon').innerHTML = `
                    <img src="${data.favicon}" alt="Current Favicon" style="width: 32px; height: 32px;" class="mb-2">
                    <p class="text-muted small">Current Favicon</p>
                `;
            }
        })
        .catch(() => {
            document.getElementById('currentLogo').innerHTML = '<p class="text-muted">No logo uploaded</p>';
            document.getElementById('currentFavicon').innerHTML = '<p class="text-muted">No favicon uploaded</p>';
        });
}

function uploadFavicon() {
    const fileInput = document.getElementById('faviconUpload');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a favicon file');
        return;
    }
    
    const formData = new FormData();
    formData.append('favicon', file);
    
    fetch('/admin/cms/upload-favicon', {
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
            loadLogoAndFavicon();
            fileInput.value = '';
        }
    });
}

// Announcement Bar Management
function loadAnnouncementBar() {
    fetch('/admin/cms/announcement-bar')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.announcement) {
                document.getElementById('announcementText').value = data.announcement.text || '';
                document.getElementById('announcementBgColor').value = data.announcement.bg_color || '#ffc107';
                document.getElementById('announcementTextColor').value = data.announcement.text_color || '#000000';
                document.getElementById('announcementCloseable').checked = data.announcement.closeable !== false;
                document.getElementById('announcementEnabled').checked = data.announcement.enabled || false;
            }
        })
        .catch(() => {
            // Set defaults
            document.getElementById('announcementBgColor').value = '#ffc107';
            document.getElementById('announcementTextColor').value = '#000000';
            document.getElementById('announcementCloseable').checked = true;
            document.getElementById('announcementEnabled').checked = false;
        });
}

function saveAnnouncementBar() {
    fetch('/admin/cms/announcement-bar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            text: document.getElementById('announcementText').value,
            bg_color: document.getElementById('announcementBgColor').value,
            text_color: document.getElementById('announcementTextColor').value,
            closeable: document.getElementById('announcementCloseable').checked,
            enabled: document.getElementById('announcementEnabled').checked
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Announcement bar saved successfully');
    });
}

// Footer Management
let quickLinks = [];
let popularCategories = [];

function loadFooter() {
    // Load footer logo
    fetch('/admin/cms/footer-logo')
        .then(res => res.json())
        .then(data => {
            if (data.logo) {
                document.getElementById('currentFooterLogo').innerHTML = `
                    <img src="${data.logo}" alt="Footer Logo" style="max-height: 60px;" class="mb-2">
                    <p class="text-muted small">Current Footer Logo</p>
                `;
            } else {
                document.getElementById('currentFooterLogo').innerHTML = '<p class="text-muted">No footer logo uploaded</p>';
            }
        });

    // Load all footer settings
    fetch('/admin/cms/footer-settings')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // About section
                document.getElementById('footerAboutTitle').value = data.about_title || 'About Us';
                document.getElementById('footerAboutText').value = data.about_text || '';
                
                // Quick links
                quickLinks = data.quick_links || [];
                renderQuickLinks();
                
                // Popular categories
                popularCategories = data.popular_categories || [];
                renderPopularCategories();
                
                // Contact info
                document.getElementById('contactEmail').value = data.contact_email || '';
                document.getElementById('contactPhone').value = data.contact_phone || '';
                document.getElementById('contactAddress').value = data.contact_address || '';
                
                // Social media
                document.getElementById('socialFacebook').value = data.social_facebook || '';
                document.getElementById('socialTwitter').value = data.social_twitter || '';
                document.getElementById('socialInstagram').value = data.social_instagram || '';
                document.getElementById('socialYoutube').value = data.social_youtube || '';
                document.getElementById('socialLinkedin').value = data.social_linkedin || '';
                
                // Copyright
                document.getElementById('copyrightText').value = data.copyright_text || '';
                
                // Newsletter
                document.getElementById('newsletterTitle').value = data.newsletter_title || 'Subscribe to Newsletter';
                document.getElementById('newsletterDescription').value = data.newsletter_description || '';
                document.getElementById('newsletterEnabled').checked = data.newsletter_enabled || false;
            }
        });
}

function uploadFooterLogo() {
    const fileInput = document.getElementById('footerLogoUpload');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a logo file');
        return;
    }
    
    const formData = new FormData();
    formData.append('logo', file);
    
    fetch('/admin/cms/upload-footer-logo', {
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
            loadFooter();
            fileInput.value = '';
        }
    });
}

function saveFooterAbout() {
    fetch('/admin/cms/footer-about', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            about_title: document.getElementById('footerAboutTitle').value,
            about_text: document.getElementById('footerAboutText').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'About section saved successfully');
    });
}

// Quick Links Management
function renderQuickLinks() {
    const container = document.getElementById('quickLinksContainer');
    if (quickLinks.length === 0) {
        container.innerHTML = '<p class="text-muted">No quick links yet. Click "Add Quick Link" to create one.</p>';
        return;
    }
    
    container.innerHTML = '';
    quickLinks.forEach((link, index) => {
        container.innerHTML += `
            <div class="link-item mb-3 p-3 border rounded">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <label class="form-label">Link Text</label>
                        <input type="text" class="form-control" value="${link.label || ''}" 
                               onchange="quickLinks[${index}].label = this.value" placeholder="Link Label">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">URL</label>
                        <input type="text" class="form-control" value="${link.url || ''}" 
                               onchange="quickLinks[${index}].url = this.value" placeholder="/page or https://...">
                    </div>
                    <div class="col-md-2 text-end">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-sm btn-danger" onclick="removeQuickLink(${index})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
}

function addQuickLink() {
    quickLinks.push({ label: '', url: '' });
    renderQuickLinks();
}

function removeQuickLink(index) {
    quickLinks.splice(index, 1);
    renderQuickLinks();
}

function saveQuickLinks() {
    fetch('/admin/cms/footer-quick-links', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ quick_links: quickLinks })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Quick links saved successfully');
    });
}

// Popular Categories Management
function renderPopularCategories() {
    const container = document.getElementById('popularCategoriesContainer');
    if (popularCategories.length === 0) {
        container.innerHTML = '<p class="text-muted">No category links yet. Click "Add Category Link" to create one.</p>';
        return;
    }
    
    container.innerHTML = '';
    popularCategories.forEach((cat, index) => {
        container.innerHTML += `
            <div class="link-item mb-3 p-3 border rounded">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" value="${cat.label || ''}" 
                               onchange="popularCategories[${index}].label = this.value" placeholder="Category Name">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">URL</label>
                        <input type="text" class="form-control" value="${cat.url || ''}" 
                               onchange="popularCategories[${index}].url = this.value" placeholder="/category/...">
                    </div>
                    <div class="col-md-2 text-end">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-sm btn-danger" onclick="removePopularCategory(${index})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
}

function addPopularCategory() {
    popularCategories.push({ label: '', url: '' });
    renderPopularCategories();
}

function removePopularCategory(index) {
    popularCategories.splice(index, 1);
    renderPopularCategories();
}

function savePopularCategories() {
    fetch('/admin/cms/footer-popular-categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ popular_categories: popularCategories })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Popular categories saved successfully');
    });
}

function saveContactInfo() {
    fetch('/admin/cms/footer-contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            contact_email: document.getElementById('contactEmail').value,
            contact_phone: document.getElementById('contactPhone').value,
            contact_address: document.getElementById('contactAddress').value,
            social_facebook: document.getElementById('socialFacebook').value,
            social_twitter: document.getElementById('socialTwitter').value,
            social_instagram: document.getElementById('socialInstagram').value,
            social_youtube: document.getElementById('socialYoutube').value,
            social_linkedin: document.getElementById('socialLinkedin').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Contact info saved successfully');
    });
}

function saveCopyrightText() {
    fetch('/admin/cms/footer-copyright', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            copyright_text: document.getElementById('copyrightText').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Copyright text saved successfully');
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error saving copyright text');
    });
}

function saveNewsletterSettings() {
    fetch('/admin/cms/footer-newsletter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            newsletter_title: document.getElementById('newsletterTitle').value,
            newsletter_description: document.getElementById('newsletterDescription').value,
            newsletter_enabled: document.getElementById('newsletterEnabled').checked
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Newsletter settings saved successfully');
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error saving newsletter settings');
    });
}

// Images
function loadPagesForImages() {
    fetch('/admin/pages/all')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('pageForImages');
            select.innerHTML = '<option value="">Select a page...</option>';
            data.forEach(page => {
                select.innerHTML += `<option value="${page.id}">${page.title}</option>`;
            });
        })
        .catch(err => {
            console.error('Error:', err);
        });
}

function loadPageImages() {
    const pageId = document.getElementById('pageForImages').value;
    if (!pageId) {
        document.getElementById('pageImagesContainer').innerHTML = '';
        return;
    }
    
    fetch(`/admin/pages/${pageId}/edit`)
        .then(res => res.json())
        .then(page => {
            const container = document.getElementById('pageImagesContainer');
            container.innerHTML = '';
            
            if (!page.images || page.images.length === 0) {
                container.innerHTML = '<p class="text-muted">No images uploaded</p>';
                return;
            }
            
            page.images.forEach(image => {
                container.innerHTML += `
                    <div class="image-preview">
                        <img src="/storage/${image}" alt="Page image">
                        <button class="delete-btn" onclick="deletePageImage(${pageId}, '${image}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            });
        })
        .catch(err => {
            console.error('Error loading images:', err);
        });
}

function uploadPageImage() {
    const pageId = document.getElementById('pageForImages').value;
    if (!pageId) {
        alert('Please select a page first');
        return;
    }
    
    const fileInput = document.getElementById('pageImageUpload');
    if (!fileInput.files[0]) {
        alert('Please select an image');
        return;
    }
    
    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    
    fetch(`/admin/pages/${pageId}/upload-image`, {
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
            fileInput.value = '';
            loadPageImages();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error uploading image');
    });
}

function deletePageImage(pageId, image) {
    if (confirm('Delete this image?')) {
        fetch(`/admin/pages/${pageId}/delete-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ image: image })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                loadPageImages();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error deleting image');
        });
    }
}

function uploadLogo() {
    const fileInput = document.getElementById('logoUpload');
    if (!fileInput.files[0]) {
        alert('Please select a logo image');
        return;
    }
    
    const formData = new FormData();
    formData.append('logo', fileInput.files[0]);
    
    fetch('/admin/cms/upload-logo', {
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
            fileInput.value = '';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error uploading logo');
    });
}

// Load everything on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadSystemPages();
    loadCustomPages();
    loadHeaderLinks();
    loadLogoAndFavicon();
    loadAnnouncementBar();
    loadFooter();
    loadPagesForImages();
    loadHomepageSettings();
    loadGlobalSeo();
    loadPageSeoList();
    loadBlogCategories();
    loadBlogTags();
    loadContactInfo();
    
    // Auto-generate slug from title
    document.getElementById('pageTitle').addEventListener('input', function() {
        if (!document.getElementById('pageId').value) {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('pageSlug').value = slug;
        }
    });
    
    // Reload data when switching tabs
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const href = e.target.getAttribute('href');
            if (href === '#pagesTab') {
                loadSystemPages();
                loadCustomPages();
            } else if (href === '#headerTab') {
                loadHeaderLinks();
                loadLogoAndFavicon();
                loadAnnouncementBar();
            } else if (href === '#footerTab') {
                loadFooter();
            } else if (href === '#seoTab') {
                loadGlobalSeo();
                loadPageSeoList();
            } else if (href === '#blogTab') {
                loadBlogPosts();
                loadBlogCategories();
                loadBlogTags();
            } else if (href === '#contactTab') {
                loadContactInfo();
            } else if (href === '#emailTab') {
                loadEmailTemplate();
            } else if (href === '#faqTab') {
                loadFaqs();
                loadFaqCategories();
            } else if (href === '#adsTab') {
                loadAdSpaces();
            } else if (href === '#languageTab') {
                loadTextCategory();
            } else if (href === '#legalTab') {
                loadLegalSettings();
            } else if (href === '#announcementTab') {
                loadAnnouncements();
            } else if (href === '#mediaTab') {
                loadMediaLibrary();
            } else if (href === '#imagesTab') {
                loadPagesForImages();
            } else if (href === '#homepageTab') {
                loadHomepageSettings();
            }
        });
    });
    
    // Handle page form submission
    document.getElementById('pageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('pageId').value;
        const url = id ? `/admin/pages/${id}` : '/admin/pages';
        const method = id ? 'PUT' : 'POST';
        
        // Get content from Summernote editor
        let content = $('#pageContent').summernote('code');
        
        const data = {
            title: document.getElementById('pageTitle').value,
            slug: document.getElementById('pageSlug').value,
            excerpt: document.getElementById('pageExcerpt').value,
            content: content,
            type: document.getElementById('pageType').value,
            order: document.getElementById('pageOrder').value,
            meta_title: document.getElementById('pageMetaTitle').value,
            meta_description: document.getElementById('pageMetaDescription').value,
            is_active: document.getElementById('pageIsActive').checked,
        };
        
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('pageModal')).hide();
                loadSystemPages();
                loadCustomPages();
                loadStats();
            }
        });
    });
    
    // Handle image upload in page editor
    document.getElementById('pageImageUploadField').addEventListener('change', function(e) {
        const files = e.target.files;
        const preview = document.getElementById('uploadedImagesPreview');
        
        if (files.length === 0) return;
        
        preview.innerHTML = '<p class="text-info"><i class="fas fa-spinner fa-spin"></i> Uploading images...</p>';
        
        Array.from(files).forEach(file => {
            const formData = new FormData();
            formData.append('image', file);
            
            fetch('/admin/pages/upload-temp-image', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const imageUrl = data.url;
                    preview.innerHTML += `
                        <div class="uploaded-image-item mb-2 p-2 border rounded">
                            <img src="${imageUrl}" style="max-width: 100px; max-height: 100px;" class="me-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="insertImageToEditor('${imageUrl}')">
                                <i class="fas fa-plus"></i> Insert to Editor
                            </button>
                            <code class="ms-2">${imageUrl}</code>
                        </div>
                    `;
                } else {
                    preview.innerHTML += `<p class="text-danger">Failed to upload ${file.name}</p>`;
                }
            })
            .catch(err => {
                preview.innerHTML += `<p class="text-danger">Error uploading ${file.name}</p>`;
            });
        });
    });
});

// ===== HOMEPAGE MANAGEMENT FUNCTIONS =====

let banners = [];
let customBlocks = [];

// Load all homepage settings
function loadHomepageSettings() {
    loadHeroSection();
    loadBanners();
    loadFeaturedSections();
    loadCustomBlocks();
}

// Hero Section
function loadHeroSection() {
    fetch('/admin/cms/hero-section')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.hero) {
                document.getElementById('heroMainHeading').value = data.hero.main_heading || '';
                document.getElementById('heroSubHeading').value = data.hero.sub_heading || '';
                document.getElementById('heroCtaText').value = data.hero.cta_text || '';
                document.getElementById('heroCtaLink').value = data.hero.cta_link || '';
                document.getElementById('heroBackground').value = data.hero.background || '';
                document.getElementById('heroEnabled').checked = data.hero.enabled || false;
            }
        })
        .catch(() => {
            // Set defaults if error
            document.getElementById('heroEnabled').checked = true;
        });
}

function saveHeroSection() {
    fetch('/admin/cms/hero-section', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            main_heading: document.getElementById('heroMainHeading').value,
            sub_heading: document.getElementById('heroSubHeading').value,
            cta_text: document.getElementById('heroCtaText').value,
            cta_link: document.getElementById('heroCtaLink').value,
            background: document.getElementById('heroBackground').value,
            enabled: document.getElementById('heroEnabled').checked
        })
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(text => {
                console.error('Server response:', text);
                throw new Error(`HTTP error! status: ${res.status}`);
            });
        }
        return res.json();
    })
    .then(data => {
        alert(data.message || 'Hero section saved successfully');
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error saving hero section: ' + err.message);
    });
}

// Banners
function loadBanners() {
    fetch('/admin/cms/banners')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                banners = data.banners || [];
                renderBanners();
            }
        })
        .catch(() => {
            banners = [];
            renderBanners();
        });
}

function renderBanners() {
    const container = document.getElementById('bannersContainer');
    if (banners.length === 0) {
        container.innerHTML = '<p class="text-muted">No banners added yet</p>';
        return;
    }
    
    container.innerHTML = '';
    banners.forEach((banner, index) => {
        container.innerHTML += `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6>${banner.title || 'Banner ' + (index + 1)}</h6>
                            <small class="text-muted">Order: ${banner.order} | ${banner.active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}</small>
                            ${banner.start_date ? '<br><small>Start: ' + banner.start_date + '</small>' : ''}
                            ${banner.end_date ? '<br><small>End: ' + banner.end_date + '</small>' : ''}
                        </div>
                        <div>
                            <button class="btn btn-sm btn-danger" onclick="deleteBanner(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function showAddBannerModal() {
    document.getElementById('bannerImageUrl').value = '';
    document.getElementById('bannerTitle').value = '';
    document.getElementById('bannerButtonText').value = '';
    document.getElementById('bannerButtonLink').value = '';
    document.getElementById('bannerOrder').value = banners.length;
    document.getElementById('bannerActive').checked = true;
    document.getElementById('bannerStartDate').value = '';
    document.getElementById('bannerEndDate').value = '';
    new bootstrap.Modal(document.getElementById('bannerModal')).show();
}

function saveBanner() {
    const banner = {
        image_url: document.getElementById('bannerImageUrl').value,
        title: document.getElementById('bannerTitle').value,
        button_text: document.getElementById('bannerButtonText').value,
        button_link: document.getElementById('bannerButtonLink').value,
        order: parseInt(document.getElementById('bannerOrder').value),
        active: document.getElementById('bannerActive').checked,
        start_date: document.getElementById('bannerStartDate').value,
        end_date: document.getElementById('bannerEndDate').value
    };
    
    fetch('/admin/cms/banners', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(banner)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Banner saved successfully');
        bootstrap.Modal.getInstance(document.getElementById('bannerModal')).hide();
        loadBanners();
    });
}

function deleteBanner(index) {
    if (confirm('Delete this banner?')) {
        fetch(`/admin/cms/banners/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || 'Banner deleted successfully');
            loadBanners();
        });
    }
}

// Featured Sections
function loadFeaturedSections() {
    fetch('/admin/cms/featured-sections')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.sections) {
                const s = data.sections;
                document.getElementById('featuredAdsTitle').value = s.ads_title || 'Featured Listings';
                document.getElementById('showFeaturedAds').checked = s.show_ads !== false;
                document.getElementById('featuredAdsMax').value = s.ads_max || 8;
                
                document.getElementById('featuredDealersTitle').value = s.dealers_title || 'Top Dealers';
                document.getElementById('showFeaturedDealers').checked = s.show_dealers !== false;
                document.getElementById('featuredDealersMax').value = s.dealers_max || 6;
                
                document.getElementById('popularCategoriesTitle').value = s.categories_title || 'Browse by Category';
                document.getElementById('showPopularCategories').checked = s.show_categories !== false;
                document.getElementById('popularCategoriesMax').value = s.categories_max || 12;
            }
        })
        .catch(() => {
            // Set defaults
            document.getElementById('showFeaturedAds').checked = true;
            document.getElementById('showFeaturedDealers').checked = true;
            document.getElementById('showPopularCategories').checked = true;
        });
}

function saveFeaturedSections() {
    fetch('/admin/cms/featured-sections', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            ads_title: document.getElementById('featuredAdsTitle').value,
            show_ads: document.getElementById('showFeaturedAds').checked,
            ads_max: parseInt(document.getElementById('featuredAdsMax').value),
            dealers_title: document.getElementById('featuredDealersTitle').value,
            show_dealers: document.getElementById('showFeaturedDealers').checked,
            dealers_max: parseInt(document.getElementById('featuredDealersMax').value),
            categories_title: document.getElementById('popularCategoriesTitle').value,
            show_categories: document.getElementById('showPopularCategories').checked,
            categories_max: parseInt(document.getElementById('popularCategoriesMax').value)
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Featured sections saved successfully');
    });
}

// Custom Blocks
function loadCustomBlocks() {
    fetch('/admin/cms/custom-blocks')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                customBlocks = data.blocks || [];
                renderCustomBlocks();
            }
        })
        .catch(() => {
            customBlocks = [];
            renderCustomBlocks();
        });
}

function renderCustomBlocks() {
    const container = document.getElementById('customBlocksContainer');
    if (customBlocks.length === 0) {
        container.innerHTML = '<p class="text-muted">No custom blocks added yet</p>';
        return;
    }
    
    container.innerHTML = '';
    customBlocks.forEach((block, index) => {
        container.innerHTML += `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6>${block.title || 'Block ' + (index + 1)}</h6>
                            <small class="text-muted">Order: ${block.order} | ${block.active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-warning me-1" onclick="moveBlock(${index}, 'up')" ${index === 0 ? 'disabled' : ''}>
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button class="btn btn-sm btn-warning me-1" onclick="moveBlock(${index}, 'down')" ${index === customBlocks.length - 1 ? 'disabled' : ''}>
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCustomBlock(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
}

function showAddCustomBlockModal() {
    document.getElementById('blockTitle').value = '';
    document.getElementById('blockContent').value = '';
    document.getElementById('blockImageUrl').value = '';
    document.getElementById('blockButtonText').value = '';
    document.getElementById('blockButtonLink').value = '';
    document.getElementById('blockOrder').value = customBlocks.length;
    document.getElementById('blockActive').checked = true;
    new bootstrap.Modal(document.getElementById('customBlockModal')).show();
}

function saveCustomBlock() {
    const block = {
        title: document.getElementById('blockTitle').value,
        content: document.getElementById('blockContent').value,
        image_url: document.getElementById('blockImageUrl').value,
        button_text: document.getElementById('blockButtonText').value,
        button_link: document.getElementById('blockButtonLink').value,
        order: parseInt(document.getElementById('blockOrder').value),
        active: document.getElementById('blockActive').checked
    };
    
    fetch('/admin/cms/custom-blocks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(block)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Custom block saved successfully');
        bootstrap.Modal.getInstance(document.getElementById('customBlockModal')).hide();
        loadCustomBlocks();
    });
}

function moveBlock(index, direction) {
    const newIndex = direction === 'up' ? index - 1 : index + 1;
    if (newIndex < 0 || newIndex >= customBlocks.length) return;
    
    [customBlocks[index], customBlocks[newIndex]] = [customBlocks[newIndex], customBlocks[index]];
    customBlocks[index].order = index;
    customBlocks[newIndex].order = newIndex;
    
    fetch('/admin/cms/custom-blocks/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ blocks: customBlocks })
    })
    .then(res => res.json())
    .then(data => {
        renderCustomBlocks();
    });
}

function deleteCustomBlock(index) {
    if (confirm('Delete this custom block?')) {
        fetch(`/admin/cms/custom-blocks/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || 'Custom block deleted successfully');
            loadCustomBlocks();
        });
    }
}

// ============================================
// SEO MANAGEMENT FUNCTIONS
// ============================================

// Load Global SEO Settings
function loadGlobalSeo() {
    fetch('/admin/cms/global-seo')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('defaultMetaTitle').value = data.meta_title || '';
                document.getElementById('defaultMetaDescription').value = data.meta_description || '';
                document.getElementById('defaultMetaKeywords').value = data.meta_keywords || '';
                document.getElementById('sitemapEnabled').checked = data.sitemap_enabled || false;
                
                // Display current OG image
                const ogImageContainer = document.getElementById('currentOgImage');
                if (data.og_image) {
                    ogImageContainer.innerHTML = `
                        <img src="${data.og_image}" alt="OG Image" style="max-width: 300px; border: 1px solid #ddd; border-radius: 4px;">
                    `;
                } else {
                    ogImageContainer.innerHTML = '<p class="text-muted">No OG image uploaded</p>';
                }
            }
        });
}

// Save Global SEO Settings
function saveGlobalSeo() {
    const data = {
        meta_title: document.getElementById('defaultMetaTitle').value,
        meta_description: document.getElementById('defaultMetaDescription').value,
        meta_keywords: document.getElementById('defaultMetaKeywords').value,
        sitemap_enabled: document.getElementById('sitemapEnabled').checked
    };
    
    fetch('/admin/cms/global-seo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Global SEO settings saved successfully');
    })
    .catch(err => alert('Error saving SEO settings'));
}

// Upload OG Image
function uploadOgImage() {
    const fileInput = document.getElementById('ogImageUpload');
    if (!fileInput.files[0]) {
        alert('Please select an image');
        return;
    }
    
    const formData = new FormData();
    formData.append('og_image', fileInput.files[0]);
    
    fetch('/admin/cms/upload-og-image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'OG Image uploaded successfully');
        loadGlobalSeo();
        fileInput.value = '';
    })
    .catch(err => alert('Error uploading OG image'));
}

// Edit Robots.txt
function editRobotsTxt() {
    fetch('/admin/cms/robots-txt')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('robotsTxtContent').value = data.content || '';
                new bootstrap.Modal(document.getElementById('robotsTxtModal')).show();
            }
        });
}

// Save Robots.txt
function saveRobotsTxt() {
    const content = document.getElementById('robotsTxtContent').value;
    
    fetch('/admin/cms/robots-txt', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ content })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Robots.txt saved successfully');
        bootstrap.Modal.getInstance(document.getElementById('robotsTxtModal')).hide();
    })
    .catch(err => alert('Error saving robots.txt'));
}

// Load Page-Level SEO List
function loadPageSeoList() {
    fetch('/admin/pages/all')
        .then(res => res.json())
        .then(pages => {
            const tbody = document.getElementById('pageSeoList');
            if (pages.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No pages found</td></tr>';
                return;
            }
            
            tbody.innerHTML = pages.map(page => {
                const hasCustomSeo = page.meta_title || page.meta_description || page.meta_keywords;
                return `
                    <tr>
                        <td><strong>${page.title}</strong><br><small class="text-muted">/${page.slug}</small></td>
                        <td>${page.meta_title || '<span class="text-muted">Using default</span>'}</td>
                        <td>${page.meta_description ? page.meta_description.substring(0, 60) + '...' : '<span class="text-muted">Using default</span>'}</td>
                        <td>
                            ${hasCustomSeo ? '<span class="badge bg-success">Custom SEO</span>' : '<span class="badge bg-secondary">Default</span>'}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editPageSeo(${page.id}, \`${page.title.replace(/'/g, "'")}\`)">
                                <i class="fas fa-edit"></i> Edit SEO
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        });
}

// Edit Page SEO
function editPageSeo(pageId, pageTitle) {
    fetch(`/admin/cms/page-seo/${pageId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('seoPageId').value = pageId;
                document.getElementById('pageSeoTitle').textContent = pageTitle;
                document.getElementById('pageMetaTitle').value = data.meta_title || '';
                document.getElementById('pageMetaDescription').value = data.meta_description || '';
                document.getElementById('pageMetaKeywords').value = data.meta_keywords || '';
                new bootstrap.Modal(document.getElementById('pageSeoModal')).show();
            }
        });
}

// Save Page SEO
function savePageSeo() {
    const pageId = document.getElementById('seoPageId').value;
    const data = {
        meta_title: document.getElementById('pageMetaTitle').value,
        meta_description: document.getElementById('pageMetaDescription').value,
        meta_keywords: document.getElementById('pageMetaKeywords').value
    };
    
    fetch(`/admin/cms/page-seo/${pageId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Page SEO settings saved successfully');
        bootstrap.Modal.getInstance(document.getElementById('pageSeoModal')).hide();
        loadPageSeoList();
    })
    .catch(err => alert('Error saving page SEO'));
}

// ============================================
// BLOG MANAGEMENT FUNCTIONS
// ============================================

let blogCategories = [];
let blogTags = [];

// Load Blog Posts
function loadBlogPosts() {
    const status = document.getElementById('blogStatusFilter').value;
    const category = document.getElementById('blogCategoryFilter').value;
    const search = document.getElementById('blogSearchInput').value;
    
    let url = '/admin/blog/posts?';
    if (status) url += `status=${status}&`;
    if (category) url += `category=${category}&`;
    if (search) url += `search=${search}`;
    
    fetch(url)
        .then(res => res.json())
        .then(posts => {
            const tbody = document.getElementById('blogPostsList');
            if (posts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No posts found</td></tr>';
                return;
            }
            
            tbody.innerHTML = posts.map(post => {
                let statusBadge = '';
                if (post.status === 'published') {
                    statusBadge = '<span class="badge bg-success">Published</span>';
                } else {
                    statusBadge = '<span class="badge bg-secondary">Draft</span>';
                }
                
                return `
                    <tr>
                        <td><strong>${post.title}</strong><br><small class="text-muted">/${post.slug}</small></td>
                        <td>${post.category ? post.category.name : '<span class="text-muted">Uncategorized</span>'}</td>
                        <td>${statusBadge}</td>
                        <td>${post.published_at ? new Date(post.published_at).toLocaleDateString() : '-'}</td>
                        <td>${post.views || 0}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editBlogPost(${post.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBlogPost(${post.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        });
}

// Show Create Blog Modal
function showCreateBlogModal() {
    document.getElementById('blogPostId').value = '';
    document.getElementById('blogPostForm').reset();
    document.getElementById('blogPostModalTitle').textContent = 'Create Blog Post';
    $('#blogPostContent').summernote('code', '');
    new bootstrap.Modal(document.getElementById('blogPostModal')).show();
}

// Edit Blog Post
function editBlogPost(id) {
    fetch(`/admin/blog/posts/${id}`)
        .then(res => res.json())
        .then(post => {
            document.getElementById('blogPostId').value = post.id;
            document.getElementById('blogPostTitle').value = post.title;
            document.getElementById('blogPostSlug').value = post.slug;
            document.getElementById('blogPostExcerpt').value = post.excerpt || '';
            $('#blogPostContent').summernote('code', post.content || '');
            document.getElementById('blogPostStatus').value = post.status;
            document.getElementById('blogPostPublishedAt').value = post.published_at ? post.published_at.substring(0, 16) : '';
            document.getElementById('blogPostCategory').value = post.blog_category_id || '';
            document.getElementById('blogPostTags').value = post.tags ? post.tags.map(t => t.name).join(', ') : '';
            document.getElementById('blogPostMetaTitle').value = post.meta_title || '';
            document.getElementById('blogPostMetaDescription').value = post.meta_description || '';
            
            if (post.featured_image) {
                document.getElementById('blogCurrentFeaturedImage').innerHTML = `
                    <img src="${post.featured_image}" style="max-width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                `;
            }
            
            document.getElementById('blogPostModalTitle').textContent = 'Edit Blog Post';
            new bootstrap.Modal(document.getElementById('blogPostModal')).show();
        });
}

// Save Blog Post
document.getElementById('blogPostForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('blogPostId').value;
    const formData = new FormData();
    
    formData.append('title', document.getElementById('blogPostTitle').value);
    formData.append('slug', document.getElementById('blogPostSlug').value);
    formData.append('excerpt', document.getElementById('blogPostExcerpt').value);
    formData.append('content', $('#blogPostContent').summernote('code'));
    formData.append('status', document.getElementById('blogPostStatus').value);
    formData.append('published_at', document.getElementById('blogPostPublishedAt').value);
    formData.append('blog_category_id', document.getElementById('blogPostCategory').value);
    formData.append('tags', document.getElementById('blogPostTags').value);
    formData.append('meta_title', document.getElementById('blogPostMetaTitle').value);
    formData.append('meta_description', document.getElementById('blogPostMetaDescription').value);
    
    const imageFile = document.getElementById('blogFeaturedImageUpload').files[0];
    if (imageFile) {
        formData.append('featured_image', imageFile);
    }
    
    const url = id ? `/admin/blog/posts/${id}` : '/admin/blog/posts';
    const method = 'POST';
    if (id) formData.append('_method', 'PUT');
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Blog post saved successfully');
        bootstrap.Modal.getInstance(document.getElementById('blogPostModal')).hide();
        loadBlogPosts();
    })
    .catch(err => alert('Error saving blog post'));
});

// Delete Blog Post
function deleteBlogPost(id) {
    if (!confirm('Delete this blog post?')) return;
    
    fetch(`/admin/blog/posts/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Blog post deleted successfully');
        loadBlogPosts();
    });
}

// Load Blog Categories
function loadBlogCategories() {
    fetch('/admin/blog/categories')
        .then(res => res.json())
        .then(categories => {
            blogCategories = categories;
            
            // Update category select in modal
            const select = document.getElementById('blogPostCategory');
            select.innerHTML = '<option value="">Select Category</option>' +
                categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
            
            // Update category filter
            const filter = document.getElementById('blogCategoryFilter');
            filter.innerHTML = '<option value="">All Categories</option>' +
                categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
            
            // Display categories list
            const list = document.getElementById('blogCategoriesList');
            if (categories.length === 0) {
                list.innerHTML = '<p class="text-muted">No categories yet</p>';
                return;
            }
            list.innerHTML = categories.map(cat => `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                    <span><strong>${cat.name}</strong><br><small class="text-muted">${cat.description || ''}</small></span>
                    <button class="btn btn-sm btn-danger" onclick="deleteBlogCategory(${cat.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `).join('');
        });
}

// Show Add Category Modal
function showAddCategoryModal() {
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryDescription').value = '';
    new bootstrap.Modal(document.getElementById('blogCategoryModal')).show();
}

// Save Blog Category
function saveBlogCategory() {
    const data = {
        name: document.getElementById('categoryName').value,
        description: document.getElementById('categoryDescription').value
    };
    
    fetch('/admin/blog/categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Category added successfully');
        bootstrap.Modal.getInstance(document.getElementById('blogCategoryModal')).hide();
        loadBlogCategories();
    });
}

// Delete Blog Category
function deleteBlogCategory(id) {
    if (!confirm('Delete this category?')) return;
    
    fetch(`/admin/blog/categories/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Category deleted successfully');
        loadBlogCategories();
    });
}

// Load Blog Tags
function loadBlogTags() {
    fetch('/admin/blog/tags')
        .then(res => res.json())
        .then(tags => {
            blogTags = tags;
            
            const list = document.getElementById('blogTagsList');
            if (tags.length === 0) {
                list.innerHTML = '<p class="text-muted">No tags yet</p>';
                return;
            }
            list.innerHTML = tags.map(tag => `
                <span class="badge bg-primary me-1 mb-1" style="font-size: 14px;">
                    ${tag.name}
                    <i class="fas fa-times ms-1" style="cursor: pointer;" onclick="deleteBlogTag(${tag.id})"></i>
                </span>
            `).join('');
        });
}

// Show Add Tag Modal
function showAddTagModal() {
    document.getElementById('tagName').value = '';
    new bootstrap.Modal(document.getElementById('blogTagModal')).show();
}

// Save Blog Tag
function saveBlogTag() {
    const data = { name: document.getElementById('tagName').value };
    
    fetch('/admin/blog/tags', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Tag added successfully');
        bootstrap.Modal.getInstance(document.getElementById('blogTagModal')).hide();
        loadBlogTags();
    });
}

// Delete Blog Tag
function deleteBlogTag(id) {
    if (!confirm('Delete this tag?')) return;
    
    fetch(`/admin/blog/tags/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Tag deleted successfully');
        loadBlogTags();
    });
}

// Auto-generate slug for blog post
document.getElementById('blogPostTitle').addEventListener('input', function() {
    if (!document.getElementById('blogPostId').value) {
        const slug = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('blogPostSlug').value = slug;
    }
});

// ============================================
// CONTACT PAGE MANAGEMENT FUNCTIONS
// ============================================

// Load Contact Information
function loadContactInfo() {
    fetch('/admin/contact/settings')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('contactAddress').value = data.address || '';
                document.getElementById('contactPhone').value = data.phone || '';
                document.getElementById('contactPhoneAlt').value = data.phone_alt || '';
                document.getElementById('contactEmail').value = data.email || '';
                document.getElementById('contactSupportEmail').value = data.support_email || '';
                document.getElementById('contactMapEmbed').value = data.map_embed || '';
                document.getElementById('contactMapEnabled').checked = data.map_enabled || false;
                document.getElementById('contactPageHeading').value = data.page_heading || 'Get In Touch';
                document.getElementById('contactPageDescription').value = data.page_description || '';
                document.getElementById('contactFormEnabled').checked = data.form_enabled !== false;
                document.getElementById('contactFormEmail').value = data.form_email || '';
                document.getElementById('contactFormSuccessMessage').value = data.form_success_message || 'Thank you for contacting us. We will get back to you soon!';
            }
        });
}

// Save Contact Information
function saveContactInfo() {
    const data = {
        address: document.getElementById('contactAddress').value,
        phone: document.getElementById('contactPhone').value,
        phone_alt: document.getElementById('contactPhoneAlt').value,
        email: document.getElementById('contactEmail').value,
        support_email: document.getElementById('contactSupportEmail').value
    };
    
    fetch('/admin/contact/info', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Contact information saved successfully');
    })
    .catch(err => alert('Error saving contact information'));
}

// Save Contact Map Settings
function saveContactMap() {
    const data = {
        map_embed: document.getElementById('contactMapEmbed').value,
        map_enabled: document.getElementById('contactMapEnabled').checked
    };
    
    fetch('/admin/contact/map', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Map settings saved successfully');
    })
    .catch(err => alert('Error saving map settings'));
}

// Save Contact Page Content
function saveContactPageContent() {
    const data = {
        page_heading: document.getElementById('contactPageHeading').value,
        page_description: document.getElementById('contactPageDescription').value
    };
    
    fetch('/admin/contact/content', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Page content saved successfully');
    })
    .catch(err => alert('Error saving page content'));
}

// Save Contact Form Settings
function saveContactFormSettings() {
    const data = {
        form_enabled: document.getElementById('contactFormEnabled').checked,
        form_email: document.getElementById('contactFormEmail').value,
        form_success_message: document.getElementById('contactFormSuccessMessage').value
    };
    
    fetch('/admin/contact/form-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Form settings saved successfully');
    })
    .catch(err => alert('Error saving form settings'));
}

/**
 * Email Template Management Functions
 */

// Load selected email template
function loadEmailTemplate() {
    const templateType = document.getElementById('emailTemplateType').value;
    
    fetch(`/admin/email-templates/${templateType}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('emailSubject').value = data.subject || '';
                $('#emailContent').summernote('code', data.content || '');
                document.getElementById('emailFooter').value = data.footer || '';
                updateEmailPreview();
            }
        })
        .catch(err => console.error('Error loading email template:', err));
}

// Save email template
function saveEmailTemplate() {
    const templateType = document.getElementById('emailTemplateType').value;
    const data = {
        subject: document.getElementById('emailSubject').value,
        content: $('#emailContent').summernote('code'),
        footer: document.getElementById('emailFooter').value
    };
    
    fetch(`/admin/email-templates/${templateType}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Email template saved successfully');
            updateEmailPreview();
        } else {
            alert(data.message || 'Error saving template');
        }
    })
    .catch(err => alert('Error saving email template'));
}

// Reset email template to default
function resetEmailTemplate() {
    if (!confirm('Are you sure you want to reset this template to default? This cannot be undone.')) {
        return;
    }
    
    const templateType = document.getElementById('emailTemplateType').value;
    
    fetch(`/admin/email-templates/${templateType}/reset`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Template reset to default');
            loadEmailTemplate();
        } else {
            alert(data.message || 'Error resetting template');
        }
    })
    .catch(err => alert('Error resetting email template'));
}

// Update email preview
function updateEmailPreview() {
    const subject = document.getElementById('emailSubject').value;
    const content = $('#emailContent').summernote('code');
    const footer = document.getElementById('emailFooter').value;
    
    let previewHTML = '<div style="font-family: Arial, sans-serif;">';
    previewHTML += '<div style="background: #007bff; color: white; padding: 10px;"><strong>Subject: ' + subject + '</strong></div>';
    previewHTML += '<div style="padding: 20px;">' + content + '</div>';
    if (footer) {
        previewHTML += '<div style="border-top: 1px solid #ddd; padding: 10px; font-size: 12px; color: #666;">' + footer + '</div>';
    }
    previewHTML += '</div>';
    
    document.getElementById('emailPreview').innerHTML = previewHTML;
}

// Listen for content changes to update preview
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        $('#emailContent').on('summernote.change', function() {
            updateEmailPreview();
        });
        document.getElementById('emailSubject')?.addEventListener('input', updateEmailPreview);
        document.getElementById('emailFooter')?.addEventListener('input', updateEmailPreview);
    }, 1000);
});

/**
 * FAQ Management Functions
 */

let currentFaqId = null;

// Load FAQs
function loadFaqs() {
    const categoryId = document.getElementById('faqCategoryFilter')?.value || '';
    
    fetch(`/admin/faqs?category_id=${categoryId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayFaqs(data.faqs);
            }
        })
        .catch(err => console.error('Error loading FAQs:', err));
}

// Display FAQs
function displayFaqs(faqs) {
    const listEl = document.getElementById('faqList');
    
    if (!faqs || faqs.length === 0) {
        listEl.innerHTML = '<p class="text-muted">No FAQs found. Click "Add FAQ" to create one.</p>';
        return;
    }
    
    listEl.innerHTML = faqs.map(faq => `
        <div class="list-group-item" data-faq-id="${faq.id}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1"><i class="fas fa-grip-vertical text-muted me-2"></i>${faq.question}</h6>
                    <p class="mb-1 text-muted small">${faq.answer.substring(0, 100)}${faq.answer.length > 100 ? '...' : ''}</p>
                    ${faq.category ? '<span class="badge bg-info">' + faq.category.name + '</span>' : ''}
                </div>
                <div class="btn-group btn-group-sm ms-2">
                    <button class="btn btn-outline-primary" onclick="editFaq(${faq.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteFaq(${faq.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    // Initialize sortable
    initializeFaqSortable();
}

// Initialize drag and drop reordering
function initializeFaqSortable() {
    const sortableEl = document.querySelector('.sortable-faq');
    if (!sortableEl || typeof Sortable === 'undefined') return;
    
    new Sortable(sortableEl, {
        animation: 150,
        handle: '.fa-grip-vertical',
        onEnd: function(evt) {
            const faqIds = Array.from(sortableEl.children).map(el => el.dataset.faqId);
            reorderFaqs(faqIds);
        }
    });
}

// Reorder FAQs
function reorderFaqs(faqIds) {
    fetch('/admin/faqs/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ faq_ids: faqIds })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('Error reordering FAQs');
            loadFaqs();
        }
    })
    .catch(err => {
        console.error('Error reordering FAQs:', err);
        loadFaqs();
    });
}

// Open FAQ modal
function openFaqModal(faqId = null) {
    currentFaqId = faqId;
    
    const modalHTML = `
        <div class="modal fade" id="faqModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${faqId ? 'Edit FAQ' : 'Add New FAQ'}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Question</label>
                            <input type="text" id="faqQuestion" class="form-control" placeholder="Enter question">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Answer</label>
                            <textarea id="faqAnswer" class="form-control" rows="5" placeholder="Enter answer"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select id="faqCategory" class="form-select">
                                <option value="">No Category</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveFaq()">Save FAQ</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    document.getElementById('faqModal')?.remove();
    
    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Load categories into select
    loadFaqCategoriesIntoSelect();
    
    // If editing, load FAQ data
    if (faqId) {
        fetch(`/admin/faqs/${faqId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('faqQuestion').value = data.faq.question;
                    document.getElementById('faqAnswer').value = data.faq.answer;
                    document.getElementById('faqCategory').value = data.faq.category_id || '';
                }
            });
    }
    
    // Show modal
    new bootstrap.Modal(document.getElementById('faqModal')).show();
}

// Load categories into select dropdown
function loadFaqCategoriesIntoSelect() {
    fetch('/admin/faq-categories')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const selectEl = document.getElementById('faqCategory');
                const filterEl = document.getElementById('faqCategoryFilter');
                
                const options = data.categories.map(cat => 
                    `<option value="${cat.id}">${cat.name}</option>`
                ).join('');
                
                if (selectEl) selectEl.innerHTML += options;
                if (filterEl) filterEl.innerHTML += options;
            }
        });
}

// Edit FAQ
function editFaq(faqId) {
    openFaqModal(faqId);
}

// Save FAQ
function saveFaq() {
    const question = document.getElementById('faqQuestion').value;
    const answer = document.getElementById('faqAnswer').value;
    const categoryId = document.getElementById('faqCategory').value;
    
    if (!question || !answer) {
        alert('Please fill in both question and answer');
        return;
    }
    
    const url = currentFaqId ? `/admin/faqs/${currentFaqId}` : '/admin/faqs';
    const method = 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            question: question,
            answer: answer,
            category_id: categoryId || null
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('faqModal')).hide();
            loadFaqs();
            alert(currentFaqId ? 'FAQ updated successfully' : 'FAQ added successfully');
        } else {
            alert(data.message || 'Error saving FAQ');
        }
    })
    .catch(err => alert('Error saving FAQ'));
}

// Delete FAQ
function deleteFaq(faqId) {
    if (!confirm('Are you sure you want to delete this FAQ?')) return;
    
    fetch(`/admin/faqs/${faqId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadFaqs();
            alert('FAQ deleted successfully');
        } else {
            alert(data.message || 'Error deleting FAQ');
        }
    })
    .catch(err => alert('Error deleting FAQ'));
}

// Load FAQ Categories
function loadFaqCategories() {
    fetch('/admin/faq-categories')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayFaqCategories(data.categories);
            }
        })
        .catch(err => console.error('Error loading FAQ categories:', err));
}

// Display FAQ Categories
function displayFaqCategories(categories) {
    const listEl = document.getElementById('faqCategoriesList');
    
    if (!categories || categories.length === 0) {
        listEl.innerHTML = '<p class="text-muted small">No categories yet</p>';
        return;
    }
    
    listEl.innerHTML = categories.map(cat => `
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <span>${cat.name} <span class="badge bg-secondary">${cat.faqs_count || 0}</span></span>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteFaqCategory(${cat.id})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
}

// Add FAQ Category
function addFaqCategory() {
    const name = document.getElementById('newFaqCategory').value.trim();
    
    if (!name) {
        alert('Please enter a category name');
        return;
    }
    
    fetch('/admin/faq-categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ name: name })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('newFaqCategory').value = '';
            loadFaqCategories();
            loadFaqCategoriesIntoSelect();
            alert('Category added successfully');
        } else {
            alert(data.message || 'Error adding category');
        }
    })
    .catch(err => alert('Error adding category'));
}

// Delete FAQ Category
function deleteFaqCategory(categoryId) {
    if (!confirm('Delete this category? FAQs in this category will not be deleted, just uncategorized.')) return;
    
    fetch(`/admin/faq-categories/${categoryId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadFaqCategories();
            loadFaqCategoriesIntoSelect();
            loadFaqs();
            alert('Category deleted successfully');
        } else {
            alert(data.message || 'Error deleting category');
        }
    })
    .catch(err => alert('Error deleting category'));
}

/**
 * Advertisement Space Management Functions
 */

// Load all advertisement spaces
function loadAdSpaces() {
    fetch('/admin/ad-spaces')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Header Ad
                document.getElementById('headerAdEnabled').checked = data.header_enabled || false;
                document.getElementById('headerAdCode').value = data.header_code || '';
                
                // Homepage Ads
                document.getElementById('homepageAdsEnabled').checked = data.homepage_enabled || false;
                document.getElementById('homepageTopAd').value = data.homepage_top || '';
                document.getElementById('homepageMiddleAd').value = data.homepage_middle || '';
                document.getElementById('homepageBottomAd').value = data.homepage_bottom || '';
                document.getElementById('homepageSidebarAd').value = data.homepage_sidebar || '';
                
                // Sidebar Ad
                document.getElementById('sidebarAdEnabled').checked = data.sidebar_enabled || false;
                document.getElementById('sidebarAdCode').value = data.sidebar_code || '';
                
                // Footer Ad
                document.getElementById('footerAdEnabled').checked = data.footer_enabled || false;
                document.getElementById('footerAdCode').value = data.footer_code || '';
                
                // Listing Detail Ad
                document.getElementById('listingDetailAdEnabled').checked = data.listing_detail_enabled || false;
                document.getElementById('listingDetailAdCode').value = data.listing_detail_code || '';
            }
        })
        .catch(err => console.error('Error loading ad spaces:', err));
}

// Save ad space
function saveAdSpace(type) {
    let data = {};
    
    if (type === 'header') {
        data = {
            type: 'header',
            enabled: document.getElementById('headerAdEnabled').checked,
            code: document.getElementById('headerAdCode').value
        };
    } else if (type === 'homepage') {
        data = {
            type: 'homepage',
            enabled: document.getElementById('homepageAdsEnabled').checked,
            top: document.getElementById('homepageTopAd').value,
            middle: document.getElementById('homepageMiddleAd').value,
            bottom: document.getElementById('homepageBottomAd').value,
            sidebar: document.getElementById('homepageSidebarAd').value
        };
    } else if (type === 'sidebar') {
        data = {
            type: 'sidebar',
            enabled: document.getElementById('sidebarAdEnabled').checked,
            code: document.getElementById('sidebarAdCode').value
        };
    } else if (type === 'footer') {
        data = {
            type: 'footer',
            enabled: document.getElementById('footerAdEnabled').checked,
            code: document.getElementById('footerAdCode').value
        };
    } else if (type === 'listing_detail') {
        data = {
            type: 'listing_detail',
            enabled: document.getElementById('listingDetailAdEnabled').checked,
            code: document.getElementById('listingDetailAdCode').value
        };
    }
    
    fetch('/admin/ad-spaces', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Ad space saved successfully');
        } else {
            alert(data.message || 'Error saving ad space');
        }
    })
    .catch(err => alert('Error saving ad space'));
}

/**
 * Language & Text Control Functions
 */

// Load text category
function loadTextCategory() {
    const category = document.getElementById('textCategory').value;
    
    // Hide all sections
    document.querySelectorAll('.text-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected section
    const sectionMap = {
        'system_labels': 'systemLabelsSection',
        'button_text': 'buttonTextSection',
        'error_messages': 'errorMessagesSection',
        'form_labels': 'formLabelsSection',
        'navigation': 'navigationSection',
        'listing_text': 'listingTextSection'
    };
    
    const sectionId = sectionMap[category];
    if (sectionId) {
        document.getElementById(sectionId).style.display = 'block';
    }
    
    // Load data for this category
    fetch(`/admin/text-labels/${category}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Populate inputs based on data-key attributes
                const section = document.getElementById(sectionId);
                section.querySelectorAll('input[data-key]').forEach(input => {
                    const key = input.dataset.key;
                    if (data.labels[key] !== undefined) {
                        input.value = data.labels[key];
                    }
                });
            }
        })
        .catch(err => console.error('Error loading text labels:', err));
}

// Save text labels
function saveTextLabels(category) {
    const sectionMap = {
        'system_labels': 'systemLabelsSection',
        'button_text': 'buttonTextSection',
        'error_messages': 'errorMessagesSection',
        'form_labels': 'formLabelsSection',
        'navigation': 'navigationSection',
        'listing_text': 'listingTextSection'
    };
    
    const sectionId = sectionMap[category];
    const section = document.getElementById(sectionId);
    
    // Collect all inputs from this section
    const labels = {};
    section.querySelectorAll('input[data-key]').forEach(input => {
        const key = input.dataset.key;
        labels[key] = input.value;
    });
    
    fetch(`/admin/text-labels/${category}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ labels: labels })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Text labels saved successfully');
        } else {
            alert(data.message || 'Error saving text labels');
        }
    })
    .catch(err => alert('Error saving text labels'));
}

/**
 * Legal & Compliance Management Functions
 */

// Load all legal settings
function loadLegalSettings() {
    fetch('/admin/legal-settings')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Cookie Policy
                document.getElementById('cookiePolicyTitle').value = data.cookie_policy_title || 'Cookie Policy';
                $('#cookiePolicyContent').summernote('code', data.cookie_policy_content || '');
                
                // Cookie Popup
                document.getElementById('cookiePopupEnabled').checked = data.cookie_popup_enabled || false;
                document.getElementById('cookiePopupMessage').value = data.cookie_popup_message || '';
                document.getElementById('cookieAcceptBtn').value = data.cookie_accept_btn || 'Accept All';
                document.getElementById('cookieDeclineBtn').value = data.cookie_decline_btn || 'Decline';
                document.getElementById('cookieLearnMoreText').value = data.cookie_learn_more || 'Learn More';
                document.getElementById('cookiePopupPosition').value = data.cookie_popup_position || 'bottom';
                
                // GDPR
                document.getElementById('gdprTitle').value = data.gdpr_title || 'Privacy & Data Protection';
                $('#gdprContent').summernote('code', data.gdpr_content || '');
                document.getElementById('gdprContactEmail').value = data.gdpr_contact_email || '';
                document.getElementById('gdprShowDataRequest').checked = data.gdpr_show_data_request || false;
                
                // Terms Acceptance
                document.getElementById('termsAcceptanceRequired').checked = data.terms_acceptance_required || false;
                document.getElementById('termsCheckboxLabel').value = data.terms_checkbox_label || 'I agree to the Terms and Conditions';
                document.getElementById('termsLinkText').value = data.terms_link_text || 'Terms and Conditions';
                document.getElementById('privacyLinkText').value = data.privacy_link_text || 'Privacy Policy';
                document.getElementById('newsletterOptin').checked = data.newsletter_optin || false;
                document.getElementById('newsletterOptinLabel').value = data.newsletter_optin_label || 'I want to receive newsletters and updates';
                
                // Privacy Policy
                document.getElementById('privacyPolicyTitle').value = data.privacy_policy_title || 'Privacy Policy';
                $('#privacyPolicyContent').summernote('code', data.privacy_policy_content || '');
                document.getElementById('privacyLastUpdated').value = data.privacy_last_updated || '';
            }
        })
        .catch(err => console.error('Error loading legal settings:', err));
}

// Save legal section
function saveLegalSection(section) {
    let data = { section: section };
    
    if (section === 'cookie_policy') {
        data.cookie_policy_title = document.getElementById('cookiePolicyTitle').value;
        data.cookie_policy_content = $('#cookiePolicyContent').summernote('code');
    } else if (section === 'cookie_popup') {
        data.cookie_popup_enabled = document.getElementById('cookiePopupEnabled').checked;
        data.cookie_popup_message = document.getElementById('cookiePopupMessage').value;
        data.cookie_accept_btn = document.getElementById('cookieAcceptBtn').value;
        data.cookie_decline_btn = document.getElementById('cookieDeclineBtn').value;
        data.cookie_learn_more = document.getElementById('cookieLearnMoreText').value;
        data.cookie_popup_position = document.getElementById('cookiePopupPosition').value;
    } else if (section === 'gdpr') {
        data.gdpr_title = document.getElementById('gdprTitle').value;
        data.gdpr_content = $('#gdprContent').summernote('code');
        data.gdpr_contact_email = document.getElementById('gdprContactEmail').value;
        data.gdpr_show_data_request = document.getElementById('gdprShowDataRequest').checked;
    } else if (section === 'terms_acceptance') {
        data.terms_acceptance_required = document.getElementById('termsAcceptanceRequired').checked;
        data.terms_checkbox_label = document.getElementById('termsCheckboxLabel').value;
        data.terms_link_text = document.getElementById('termsLinkText').value;
        data.privacy_link_text = document.getElementById('privacyLinkText').value;
        data.newsletter_optin = document.getElementById('newsletterOptin').checked;
        data.newsletter_optin_label = document.getElementById('newsletterOptinLabel').value;
    } else if (section === 'privacy_policy') {
        data.privacy_policy_title = document.getElementById('privacyPolicyTitle').value;
        data.privacy_policy_content = $('#privacyPolicyContent').summernote('code');
        data.privacy_last_updated = document.getElementById('privacyLastUpdated').value;
    }
    
    fetch('/admin/legal-settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Legal settings saved successfully');
        } else {
            alert(data.message || 'Error saving legal settings');
        }
    })
    .catch(err => alert('Error saving legal settings'));
}

/**
 * Announcement & Popup Control Functions
 */

let currentAnnouncementId = null;

// Load all announcements
function loadAnnouncements() {
    fetch('/admin/announcements')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('announcementsTableBody');
            if (data.success && data.announcements.length > 0) {
                tbody.innerHTML = data.announcements.map(ann => `
                    <tr>
                        <td>
                            <strong>${ann.title}</strong>
                            ${ann.image ? '<br><small class="text-muted"><i class="fas fa-image"></i> Has image</small>' : ''}
                        </td>
                        <td>
                            <span class="badge bg-info">${ann.position.replace(/_/g, ' ').toUpperCase()}</span>
                        </td>
                        <td>
                            <small>
                                ${ann.start_date ? 'From: ' + new Date(ann.start_date).toLocaleDateString() : 'No start date'}<br>
                                ${ann.end_date ? 'To: ' + new Date(ann.end_date).toLocaleDateString() : 'No end date'}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">${ann.target_users}</span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" ${ann.enabled ? 'checked' : ''} 
                                    onchange="toggleAnnouncementStatus(${ann.id})">
                                <label class="form-check-label">${ann.enabled ? 'Active' : 'Inactive'}</label>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editAnnouncement(${ann.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteAnnouncement(${ann.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No announcements found. Create your first popup!</td></tr>';
            }
        })
        .catch(err => {
            console.error('Error loading announcements:', err);
            document.getElementById('announcementsTableBody').innerHTML = 
                '<tr><td colspan="6" class="text-center text-danger">Error loading announcements</td></tr>';
        });
}

// Open announcement modal
function openAnnouncementModal(announcementId = null) {
    currentAnnouncementId = announcementId;
    
    const modalHtml = `
        <div class="modal fade" id="announcementModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-bullhorn"></i> ${announcementId ? 'Edit' : 'Create New'} Announcement/Popup
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" id="announcementTitle" placeholder="Enter popup title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea id="announcementContent" class="summernote"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Image (Optional)</label>
                            <input type="file" class="form-control" id="announcementImage" accept="image/*">
                            <small class="text-muted">Upload an image for the popup</small>
                            <div id="announcementImagePreview" class="mt-2"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="announcementStartDate">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="announcementEndDate">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Position/Style</label>
                                <select class="form-select" id="announcementPosition">
                                    <option value="center_modal">Center Modal</option>
                                    <option value="top_banner">Top Banner</option>
                                    <option value="bottom_banner">Bottom Banner</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Display Frequency</label>
                                <select class="form-select" id="announcementFrequency">
                                    <option value="once">Once per session</option>
                                    <option value="always">Every visit</option>
                                    <option value="daily">Once per day</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Target Users</label>
                            <select class="form-select" id="announcementTargetUsers">
                                <option value="all">All Users</option>
                                <option value="registered">Registered Users Only</option>
                                <option value="dealers">Dealers Only</option>
                                <option value="guests">Guest Users Only</option>
                            </select>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="announcementEnabled" checked>
                            <label class="form-check-label">Enable this announcement immediately</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="saveAnnouncement()">
                            <i class="fas fa-save"></i> Save Announcement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('announcementModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Initialize Summernote
    setTimeout(() => {
        $('#announcementContent').summernote({
            height: 200,
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']]
            ]
        });
        
        // If editing, load announcement data
        if (announcementId) {
            loadAnnouncementData(announcementId);
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
        modal.show();
    }, 100);
}

// Load announcement data for editing
function loadAnnouncementData(id) {
    fetch(`/admin/announcements/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const ann = data.announcement;
                document.getElementById('announcementTitle').value = ann.title;
                $('#announcementContent').summernote('code', ann.content);
                document.getElementById('announcementStartDate').value = ann.start_date ? ann.start_date.slice(0, 16) : '';
                document.getElementById('announcementEndDate').value = ann.end_date ? ann.end_date.slice(0, 16) : '';
                document.getElementById('announcementPosition').value = ann.position;
                document.getElementById('announcementFrequency').value = ann.frequency;
                document.getElementById('announcementTargetUsers').value = ann.target_users;
                document.getElementById('announcementEnabled').checked = ann.enabled;
                
                if (ann.image) {
                    document.getElementById('announcementImagePreview').innerHTML = 
                        `<img src="${ann.image}" class="img-thumbnail" style="max-width: 200px;">`;
                }
            }
        })
        .catch(err => console.error('Error loading announcement:', err));
}

// Save announcement
function saveAnnouncement() {
    const title = document.getElementById('announcementTitle').value;
    const content = $('#announcementContent').summernote('code');
    const startDate = document.getElementById('announcementStartDate').value;
    const endDate = document.getElementById('announcementEndDate').value;
    const position = document.getElementById('announcementPosition').value;
    const frequency = document.getElementById('announcementFrequency').value;
    const targetUsers = document.getElementById('announcementTargetUsers').value;
    const enabled = document.getElementById('announcementEnabled').checked;
    const imageFile = document.getElementById('announcementImage').files[0];
    
    if (!title || !content) {
        alert('Please fill in all required fields');
        return;
    }
    
    const formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);
    formData.append('start_date', startDate);
    formData.append('end_date', endDate);
    formData.append('position', position);
    formData.append('frequency', frequency);
    formData.append('target_users', targetUsers);
    formData.append('enabled', enabled ? 1 : 0);
    
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    const url = currentAnnouncementId 
        ? `/admin/announcements/${currentAnnouncementId}` 
        : '/admin/announcements';
    
    if (currentAnnouncementId) {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Announcement saved successfully');
            bootstrap.Modal.getInstance(document.getElementById('announcementModal')).hide();
            loadAnnouncements();
        } else {
            alert(data.message || 'Error saving announcement');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error saving announcement');
    });
}

// Edit announcement
function editAnnouncement(id) {
    openAnnouncementModal(id);
}

// Delete announcement
function deleteAnnouncement(id) {
    if (!confirm('Are you sure you want to delete this announcement?')) {
        return;
    }
    
    fetch(`/admin/announcements/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Announcement deleted successfully');
            loadAnnouncements();
        } else {
            alert(data.message || 'Error deleting announcement');
        }
    })
    .catch(err => alert('Error deleting announcement'));
}

// Toggle announcement status
function toggleAnnouncementStatus(id) {
    fetch(`/admin/announcements/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Status toggled successfully - no need to reload entire list
        } else {
            alert(data.message || 'Error toggling status');
            loadAnnouncements();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        loadAnnouncements();
    });
}

/**
 * Media Library Functions
 */

let currentFolderId = null;

// Load media library
function loadMediaLibrary() {
    const mediaType = document.getElementById('mediaTypeFilter')?.value || 'all';
    const folderId = currentFolderId;
    
    let url = '/admin/media?type=' + mediaType;
    if (folderId) {
        url += '&folder_id=' + folderId;
    }
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                displayMediaGallery(data.media);
                displayFolders(data.folders);
            } else {
                document.getElementById('mediaGallery').innerHTML = 
                    '<div class="col-12 text-center text-danger">Error loading media</div>';
            }
        })
        .catch(err => {
            console.error('Error loading media:', err);
            document.getElementById('mediaGallery').innerHTML = 
                '<div class="col-12 text-center text-danger">Error loading media library</div>';
        });
}

// Display media gallery
function displayMediaGallery(media) {
    const gallery = document.getElementById('mediaGallery');
    
    if (!media || media.length === 0) {
        gallery.innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="fas fa-images fa-3x mb-3"></i><br>No media files found. Upload some files to get started!</div>';
        return;
    }
    
    gallery.innerHTML = media.map(item => {
        const isImage = item.type === 'image';
        const isVideo = item.type === 'video';
        const fileSize = formatFileSize(item.size);
        
        let thumbnail = '';
        if (isImage) {
            thumbnail = `<img src="${item.url}" class="img-fluid" style="height: 150px; object-fit: cover;">`;
        } else if (isVideo) {
            thumbnail = `<video src="${item.url}" class="img-fluid" style="height: 150px; object-fit: cover;"></video>`;
        } else {
            thumbnail = `<div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 150px;"><i class="fas fa-file fa-3x"></i></div>`;
        }
        
        return `
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-img-top" style="cursor: pointer;" onclick="viewMedia(${item.id})">
                        ${thumbnail}
                    </div>
                    <div class="card-body p-2">
                        <p class="card-text small mb-1" style="font-size: 0.85rem;">
                            <strong>${item.filename}</strong>
                        </p>
                        <p class="card-text small text-muted mb-2" style="font-size: 0.75rem;">
                            ${fileSize} &bull; ${new Date(item.created_at).toLocaleDateString()}
                        </p>
                        <div class="btn-group btn-group-sm w-100">
                            <button class="btn btn-outline-primary" onclick="copyMediaUrl('${item.url}')" title="Copy URL">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="btn btn-outline-info" onclick="moveMediaToFolder(${item.id})" title="Move">
                                <i class="fas fa-folder"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteMedia(${item.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Display folders
function displayFolders(folders) {
    const foldersList = document.getElementById('mediaFoldersList');
    
    let html = `
        <a href="#" class="list-group-item list-group-item-action ${!currentFolderId ? 'active' : ''}" onclick="selectMediaFolder(null, event)">
            <i class="fas fa-home"></i> All Media
        </a>
    `;
    
    if (folders && folders.length > 0) {
        html += folders.map(folder => `
            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ${currentFolderId === folder.id ? 'active' : ''}" onclick="selectMediaFolder(${folder.id}, event)">
                <span><i class="fas fa-folder"></i> ${folder.name}</span>
                <span>
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteFolder(${folder.id}, event)" title="Delete folder">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </span>
            </a>
        `).join('');
    }
    
    foldersList.innerHTML = html;
}

// Select folder
function selectMediaFolder(folderId, event) {
    event.preventDefault();
    currentFolderId = folderId;
    
    const folderName = folderId ? event.target.textContent.trim() : 'All Media';
    document.getElementById('currentFolderName').textContent = folderName;
    
    loadMediaLibrary();
}

// Open upload modal
function openUploadMediaModal() {
    const modalHtml = `
        <div class="modal fade" id="uploadMediaModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-upload"></i> Upload Media Files</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Folder</label>
                            <select class="form-select" id="uploadFolderId">
                                <option value="">Root (No folder)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Choose Files (Images/Videos)</label>
                            <input type="file" class="form-control" id="mediaFilesUpload" multiple accept="image/*,video/*">
                            <small class="text-muted">You can select multiple files. Max 10MB per file.</small>
                        </div>
                        <div id="uploadProgress" class="mt-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="uploadMediaFiles()">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('uploadMediaModal');
    if (existingModal) existingModal.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Load folders into dropdown
    fetch('/admin/media/folders')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.folders.length > 0) {
                const select = document.getElementById('uploadFolderId');
                data.folders.forEach(folder => {
                    const option = document.createElement('option');
                    option.value = folder.id;
                    option.textContent = folder.name;
                    if (currentFolderId && folder.id === currentFolderId) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            }
        });
    
    const modal = new bootstrap.Modal(document.getElementById('uploadMediaModal'));
    modal.show();
}

// Upload media files
function uploadMediaFiles() {
    const files = document.getElementById('mediaFilesUpload').files;
    const folderId = document.getElementById('uploadFolderId').value;
    
    if (files.length === 0) {
        alert('Please select at least one file');
        return;
    }
    
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    if (folderId) {
        formData.append('folder_id', folderId);
    }
    
    const progressBar = document.querySelector('#uploadProgress .progress-bar');
    document.getElementById('uploadProgress').style.display = 'block';
    
    fetch('/admin/media/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Files uploaded successfully');
            bootstrap.Modal.getInstance(document.getElementById('uploadMediaModal')).hide();
            loadMediaLibrary();
        } else {
            alert(data.message || 'Error uploading files');
        }
        document.getElementById('uploadProgress').style.display = 'none';
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error uploading files');
        document.getElementById('uploadProgress').style.display = 'none';
    });
}

// Create folder
function openCreateFolderModal() {
    const folderName = prompt('Enter folder name:');
    if (folderName && folderName.trim()) {
        fetch('/admin/media/folders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name: folderName.trim() })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Folder created successfully');
                loadMediaLibrary();
            } else {
                alert(data.message || 'Error creating folder');
            }
        })
        .catch(err => alert('Error creating folder'));
    }
}

// Delete folder
function deleteFolder(folderId, event) {
    event.stopPropagation();
    event.preventDefault();
    
    if (!confirm('Are you sure you want to delete this folder? All media in this folder will be moved to root.')) {
        return;
    }
    
    fetch(`/admin/media/folders/${folderId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Folder deleted successfully');
            currentFolderId = null;
            loadMediaLibrary();
        } else {
            alert(data.message || 'Error deleting folder');
        }
    })
    .catch(err => alert('Error deleting folder'));
}

// Copy media URL
function copyMediaUrl(url) {
    const fullUrl = window.location.origin + url;
    navigator.clipboard.writeText(fullUrl).then(() => {
        alert('URL copied to clipboard!');
    }).catch(err => {
        console.error('Error copying:', err);
        alert('Failed to copy URL');
    });
}

// View media
function viewMedia(mediaId) {
    fetch(`/admin/media/${mediaId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const media = data.media;
                const isImage = media.type === 'image';
                const isVideo = media.type === 'video';
                
                let content = '';
                if (isImage) {
                    content = `<img src="${media.url}" class="img-fluid" style="max-height: 500px;">`;
                } else if (isVideo) {
                    content = `<video src="${media.url}" controls class="img-fluid" style="max-height: 500px;"></video>`;
                }
                
                const modalHtml = `
                    <div class="modal fade" id="viewMediaModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${media.filename}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    ${content}
                                    <div class="mt-3">
                                        <p class="text-muted">
                                            Size: ${formatFileSize(media.size)} | 
                                            Uploaded: ${new Date(media.created_at).toLocaleString()}
                                        </p>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="${window.location.origin + media.url}" readonly id="mediaUrlInput">
                                            <button class="btn btn-outline-secondary" onclick="copyMediaUrl('${media.url}')">
                                                <i class="fas fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                const existingModal = document.getElementById('viewMediaModal');
                if (existingModal) existingModal.remove();
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                const modal = new bootstrap.Modal(document.getElementById('viewMediaModal'));
                modal.show();
            }
        })
        .catch(err => console.error('Error:', err));
}

// Move media to folder
function moveMediaToFolder(mediaId) {
    fetch('/admin/media/folders')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const folders = data.folders;
                let options = '<option value="">Root (No folder)</option>';
                folders.forEach(folder => {
                    options += `<option value="${folder.id}">${folder.name}</option>`;
                });
                
                const modalHtml = `
                    <div class="modal fade" id="moveMediatModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Move to Folder</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label">Select Folder</label>
                                    <select class="form-select" id="moveToFolderId">
                                        ${options}
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="confirmMoveMedia(${mediaId})">
                                        <i class="fas fa-check"></i> Move
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                const existingModal = document.getElementById('moveMediatModal');
                if (existingModal) existingModal.remove();
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                const modal = new bootstrap.Modal(document.getElementById('moveMediatModal'));
                modal.show();
            }
        });
}

// Confirm move media
function confirmMoveMedia(mediaId) {
    const folderId = document.getElementById('moveToFolderId').value;
    
    fetch(`/admin/media/${mediaId}/move`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ folder_id: folderId || null })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Media moved successfully');
            bootstrap.Modal.getInstance(document.getElementById('moveMediatModal')).hide();
            loadMediaLibrary();
        } else {
            alert(data.message || 'Error moving media');
        }
    })
    .catch(err => alert('Error moving media'));
}

// Delete media
function deleteMedia(mediaId) {
    if (!confirm('Are you sure you want to delete this media file? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/media/${mediaId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Media deleted successfully');
            loadMediaLibrary();
        } else {
            alert(data.message || 'Error deleting media');
        }
    })
    .catch(err => alert('Error deleting media'));
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

</script>
@endsection
