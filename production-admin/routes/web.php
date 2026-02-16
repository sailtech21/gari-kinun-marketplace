<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\NotificationController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Redirect /admin to /admin/login
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });
    
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Listings
        Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
        Route::get('/api/listings/stats', [ListingController::class, 'stats'])->name('api.listings.stats');
        Route::get('/listings/{id}', [ListingController::class, 'show'])->name('listings.show');
        Route::get('/listings/{id}/edit', [ListingController::class, 'edit'])->name('listings.edit');
        Route::put('/listings/{id}', [ListingController::class, 'update'])->name('listings.update');
        Route::post('/listings/{id}/approve', [ListingController::class, 'approve'])->name('listings.approve');
        Route::post('/listings/{id}/reject', [ListingController::class, 'reject'])->name('listings.reject');
        Route::post('/listings/{id}/status', [ListingController::class, 'updateStatus'])->name('listings.status');
        Route::post('/listings/{id}/toggle-featured', [ListingController::class, 'toggleFeatured'])->name('listings.toggle-featured');
        Route::post('/listings/{id}/toggle-boosted', [ListingController::class, 'toggleBoosted'])->name('listings.toggle-boosted');
        Route::post('/listings/{id}/extend-expiry', [ListingController::class, 'extendExpiry'])->name('listings.extendExpiry');
        Route::post('/listings/{id}/change-category', [ListingController::class, 'changeCategory'])->name('listings.changeCategory');
        Route::post('/listings/{id}/toggle-hidden', [ListingController::class, 'toggleHidden'])->name('listings.toggleHidden');
        Route::get('/listings/{id}/reports', [ListingController::class, 'getReports'])->name('listings.reports');
        Route::get('/listings/{id}/analytics', [ListingController::class, 'getAnalytics'])->name('listings.analytics');
        Route::delete('/listings/{id}', [ListingController::class, 'destroy'])->name('listings.destroy');
        Route::post('/listings/bulk-action', [ListingController::class, 'bulkAction'])->name('listings.bulk');
        
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/stats', [UserController::class, 'stats'])->name('users.stats');
        Route::get('/users/all', [UserController::class, 'all'])->name('users.all');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/verify', [UserController::class, 'verify'])->name('users.verify');
        Route::post('/users/{id}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{id}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
        Route::post('/users/{id}/make-premium', [UserController::class, 'makePremium'])->name('users.makePremium');
        Route::post('/users/{id}/limit-posting', [UserController::class, 'limitPosting'])->name('users.limitPosting');
        Route::get('/users/{id}/ads', [UserController::class, 'getUserAds'])->name('users.ads');
        Route::get('/users/{id}/reports', [UserController::class, 'getUserReports'])->name('users.reports');
        Route::post('/users/{id}/send-notification', [UserController::class, 'sendNotification'])->name('users.sendNotification');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // Dealers
        Route::get('/dealers', [DealerController::class, 'index'])->name('dealers.index');
        Route::get('/dealers/stats', [DealerController::class, 'stats'])->name('dealers.stats');
        Route::get('/dealers/all', [DealerController::class, 'all'])->name('dealers.all');
        Route::get('/dealers/{id}', [DealerController::class, 'show'])->name('dealers.show');
        Route::get('/dealers/{id}/edit', [DealerController::class, 'edit'])->name('dealers.edit');
        Route::put('/dealers/{id}', [DealerController::class, 'update'])->name('dealers.update');
        Route::post('/dealers/{id}/approve', [DealerController::class, 'approve'])->name('dealers.approve');
        Route::post('/dealers/{id}/reject', [DealerController::class, 'reject'])->name('dealers.reject');
        Route::post('/dealers/upgrade-user', [DealerController::class, 'upgradeUser'])->name('dealers.upgradeUser');
        Route::post('/dealers/{id}/remove-status', [DealerController::class, 'removeDealerStatus'])->name('dealers.removeStatus');
        Route::post('/dealers/{id}/set-badge', [DealerController::class, 'setBadge'])->name('dealers.setBadge');
        Route::post('/dealers/{id}/set-subscription', [DealerController::class, 'setSubscription'])->name('dealers.setSubscription');
        Route::post('/dealers/{id}/limit-ads', [DealerController::class, 'limitAds'])->name('dealers.limitAds');
        Route::post('/dealers/{id}/suspend', [DealerController::class, 'suspend'])->name('dealers.suspend');
        Route::post('/dealers/{id}/unsuspend', [DealerController::class, 'unsuspend'])->name('dealers.unsuspend');
        Route::post('/dealers/{id}/toggle-feature', [DealerController::class, 'toggleFeature'])->name('dealers.toggleFeature');
        Route::get('/dealers/{id}/ads', [DealerController::class, 'getDealerAds'])->name('dealers.ads');
        Route::get('/dealers/{id}/revenue', [DealerController::class, 'getDealerRevenue'])->name('dealers.revenue');
        Route::delete('/dealers/{id}', [DealerController::class, 'destroy'])->name('dealers.destroy');
        
        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/all', [ReportController::class, 'all'])->name('reports.all');
        Route::get('/reports/stats', [ReportController::class, 'stats'])->name('reports.stats');
        Route::get('/reports/analytics', [ReportController::class, 'analytics'])->name('reports.analytics');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/ads', [ReportController::class, 'getAdReports'])->name('reports.ads');
        Route::get('/reports/users', [ReportController::class, 'getUserReports'])->name('reports.users');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{id}/status', [ReportController::class, 'updateStatus'])->name('reports.status');
        Route::post('/reports/{id}/remove-ad', [ReportController::class, 'removeAd'])->name('reports.removeAd');
        Route::post('/reports/{id}/warn-user', [ReportController::class, 'warnUser'])->name('reports.warnUser');
        Route::post('/reports/{id}/ban-user', [ReportController::class, 'banUserFromReport'])->name('reports.banUser');
        Route::post('/reports/{id}/suspend-user', [ReportController::class, 'suspendUser'])->name('reports.suspendUser');
        Route::post('/reports/{id}/dismiss', [ReportController::class, 'dismissReport'])->name('reports.dismiss');
        
        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/all', [CategoryController::class, 'all'])->name('categories.all');
        Route::get('/categories/stats', [CategoryController::class, 'stats'])->name('categories.stats');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/{id}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::post('/categories/{id}/posting-fee', [CategoryController::class, 'setPostingFee'])->name('categories.setPostingFee');
        Route::post('/categories/{id}/required-fields', [CategoryController::class, 'setRequiredFields'])->name('categories.setRequiredFields');
        Route::post('/categories/{id}/update-filters', [CategoryController::class, 'updateFilters'])->name('categories.updateFilters');
        Route::post('/categories/{id}/add-subcategory', [CategoryController::class, 'addSubcategory'])->name('categories.addSubcategory');
        
        // Locations
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::get('/locations/all', [LocationController::class, 'all'])->name('locations.all');
        Route::get('/locations/stats', [LocationController::class, 'stats'])->name('locations.stats');
        Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::post('/locations/reorder', [LocationController::class, 'reorder'])->name('locations.reorder');
        Route::post('/locations/add-division', [LocationController::class, 'addDivision'])->name('locations.addDivision');
        Route::post('/locations/{id}/add-district', [LocationController::class, 'addDistrict'])->name('locations.addDistrict');
        Route::get('/locations/{id}/edit', [LocationController::class, 'edit'])->name('locations.edit');
        Route::put('/locations/{id}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
        Route::post('/locations/{id}/toggle', [LocationController::class, 'toggleStatus'])->name('locations.toggle');
        
        // Content Management (Pages)
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/all', [PageController::class, 'all'])->name('pages.all');
        Route::get('/pages/stats', [PageController::class, 'stats'])->name('pages.stats');
        Route::get('/pages/system', [PageController::class, 'getSystemPages'])->name('pages.system');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{id}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{id}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{id}', [PageController::class, 'destroy'])->name('pages.destroy');
        Route::post('/pages/{id}/toggle', [PageController::class, 'toggleStatus'])->name('pages.toggle');
        Route::post('/pages/{id}/upload-image', [PageController::class, 'uploadImage'])->name('pages.uploadImage');
        Route::post('/pages/{id}/delete-image', [PageController::class, 'deleteImage'])->name('pages.deleteImage');
        Route::post('/pages/upload-temp-image', [PageController::class, 'uploadTempImage'])->name('pages.uploadTempImage');
        
        // Header & Footer Management
        Route::get('/cms/header-links', [PageController::class, 'getHeaderLinks'])->name('cms.headerLinks');
        Route::post('/cms/header-links', [PageController::class, 'updateHeaderLinks'])->name('cms.updateHeaderLinks');
        Route::get('/cms/footer', [PageController::class, 'getFooter'])->name('cms.footer');
        Route::post('/cms/footer', [PageController::class, 'updateFooter'])->name('cms.updateFooter');
        Route::post('/cms/upload-logo', [PageController::class, 'uploadLogo'])->name('cms.uploadLogo');
        Route::post('/cms/upload-favicon', [PageController::class, 'uploadFavicon'])->name('cms.uploadFavicon');
        Route::get('/cms/logo-favicon', [PageController::class, 'getLogoAndFavicon'])->name('cms.logoFavicon');
        Route::get('/cms/announcement-bar', [PageController::class, 'getAnnouncementBar'])->name('cms.announcementBar');
        Route::post('/cms/announcement-bar', [PageController::class, 'updateAnnouncementBar'])->name('cms.updateAnnouncementBar');
        
        // Footer Management
        Route::post('/cms/upload-footer-logo', [PageController::class, 'uploadFooterLogo'])->name('cms.uploadFooterLogo');
        Route::get('/cms/footer-logo', [PageController::class, 'getFooterLogo'])->name('cms.footerLogo');
        Route::get('/cms/footer-settings', [PageController::class, 'getFooterSettings'])->name('cms.footerSettings');
        Route::post('/cms/footer-about', [PageController::class, 'updateFooterAbout'])->name('cms.footerAbout');
        Route::post('/cms/footer-quick-links', [PageController::class, 'updateFooterQuickLinks'])->name('cms.footerQuickLinks');
        Route::post('/cms/footer-popular-categories', [PageController::class, 'updateFooterPopularCategories'])->name('cms.footerPopularCategories');
        Route::post('/cms/footer-contact', [PageController::class, 'updateFooterContact'])->name('cms.footerContact');
        Route::post('/cms/footer-copyright', [PageController::class, 'updateFooterCopyright'])->name('cms.footerCopyright');
        Route::post('/cms/footer-newsletter', [PageController::class, 'updateFooterNewsletter'])->name('cms.footerNewsletter');
        
        // SEO Management
        Route::get('/cms/global-seo', [PageController::class, 'getGlobalSeo'])->name('cms.globalSeo');
        Route::post('/cms/global-seo', [PageController::class, 'updateGlobalSeo'])->name('cms.updateGlobalSeo');
        Route::post('/cms/upload-og-image', [PageController::class, 'uploadOgImage'])->name('cms.uploadOgImage');
        Route::get('/cms/robots-txt', [PageController::class, 'getRobotsTxt'])->name('cms.robotsTxt');
        Route::post('/cms/robots-txt', [PageController::class, 'updateRobotsTxt'])->name('cms.updateRobotsTxt');
        Route::get('/cms/page-seo/{id}', [PageController::class, 'getPageSeo'])->name('cms.pageSeo');
        Route::post('/cms/page-seo/{id}', [PageController::class, 'updatePageSeo'])->name('cms.updatePageSeo');
        
        // Homepage Management
        Route::get('/cms/hero-section', [PageController::class, 'getHeroSection'])->name('cms.heroSection');
        Route::post('/cms/hero-section', [PageController::class, 'updateHeroSection'])->name('cms.updateHeroSection');
        Route::get('/cms/banners', [PageController::class, 'getBanners'])->name('cms.banners');
        Route::post('/cms/banners', [PageController::class, 'storeBanner'])->name('cms.storeBanner');
        Route::delete('/cms/banners/{index}', [PageController::class, 'deleteBanner'])->name('cms.deleteBanner');
        Route::get('/cms/featured-sections', [PageController::class, 'getFeaturedSections'])->name('cms.featuredSections');
        Route::post('/cms/featured-sections', [PageController::class, 'updateFeaturedSections'])->name('cms.updateFeaturedSections');
        Route::get('/cms/custom-blocks', [PageController::class, 'getCustomBlocks'])->name('cms.customBlocks');
        Route::post('/cms/custom-blocks', [PageController::class, 'storeCustomBlock'])->name('cms.storeCustomBlock');
        Route::delete('/cms/custom-blocks/{index}', [PageController::class, 'deleteCustomBlock'])->name('cms.deleteCustomBlock');
        Route::post('/cms/custom-blocks/reorder', [PageController::class, 'reorderCustomBlocks'])->name('cms.reorderCustomBlocks');
        
        // Blog Management
        Route::get('/blog/posts', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blog.posts');
        Route::get('/blog/posts/{id}', [App\Http\Controllers\Admin\BlogController::class, 'show'])->name('blog.show');
        Route::post('/blog/posts', [App\Http\Controllers\Admin\BlogController::class, 'store'])->name('blog.store');
        Route::post('/blog/posts/{id}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('blog.update');
        Route::delete('/blog/posts/{id}', [App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('blog.destroy');
        Route::get('/blog/categories', [App\Http\Controllers\Admin\BlogController::class, 'getCategories'])->name('blog.categories');
        Route::post('/blog/categories', [App\Http\Controllers\Admin\BlogController::class, 'storeCategory'])->name('blog.storeCategory');
        Route::delete('/blog/categories/{id}', [App\Http\Controllers\Admin\BlogController::class, 'destroyCategory'])->name('blog.destroyCategory');
        Route::get('/blog/tags', [App\Http\Controllers\Admin\BlogController::class, 'getTags'])->name('blog.tags');
        Route::post('/blog/tags', [App\Http\Controllers\Admin\BlogController::class, 'storeTag'])->name('blog.storeTag');
        Route::delete('/blog/tags/{id}', [App\Http\Controllers\Admin\BlogController::class, 'destroyTag'])->name('blog.destroyTag');
        
        // Contact Page Management
        Route::get('/contact/settings', [PageController::class, 'getContactSettings'])->name('contact.settings');
        Route::post('/contact/info', [PageController::class, 'updateContactInfo'])->name('contact.updateInfo');
        Route::post('/contact/map', [PageController::class, 'updateContactMap'])->name('contact.updateMap');
        Route::post('/contact/content', [PageController::class, 'updateContactContent'])->name('contact.updateContent');
        Route::post('/contact/form-settings', [PageController::class, 'updateContactFormSettings'])->name('contact.updateFormSettings');
        
        // Email Template Management
        Route::get('/email-templates/{type}', [PageController::class, 'getEmailTemplate'])->name('email.template');
        Route::post('/email-templates/{type}', [PageController::class, 'updateEmailTemplate'])->name('email.updateTemplate');
        Route::post('/email-templates/{type}/reset', [PageController::class, 'resetEmailTemplate'])->name('email.resetTemplate');
        
        // FAQ Management
        Route::get('/faqs', [App\Http\Controllers\Admin\FaqController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/{id}', [App\Http\Controllers\Admin\FaqController::class, 'show'])->name('faqs.show');
        Route::post('/faqs', [App\Http\Controllers\Admin\FaqController::class, 'store'])->name('faqs.store');
        Route::post('/faqs/{id}', [App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{id}', [App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.destroy');
        Route::post('/faqs/reorder', [App\Http\Controllers\Admin\FaqController::class, 'reorder'])->name('faqs.reorder');
        Route::get('/faq-categories', [App\Http\Controllers\Admin\FaqController::class, 'getCategories'])->name('faq.categories');
        Route::post('/faq-categories', [App\Http\Controllers\Admin\FaqController::class, 'storeCategory'])->name('faq.storeCategory');
        Route::delete('/faq-categories/{id}', [App\Http\Controllers\Admin\FaqController::class, 'destroyCategory'])->name('faq.destroyCategory');
        
        // Advertisement Space Management
        Route::get('/ad-spaces', [PageController::class, 'getAdSpaces'])->name('ads.index');
        Route::post('/ad-spaces', [PageController::class, 'updateAdSpaces'])->name('ads.update');
        
        // Language & Text Control
        Route::get('/text-labels/{category}', [PageController::class, 'getTextLabels'])->name('text.labels');
        Route::post('/text-labels/{category}', [PageController::class, 'updateTextLabels'])->name('text.updateLabels');
        
        // Legal & Compliance Management
        Route::get('/legal-settings', [PageController::class, 'getLegalSettings'])->name('legal.settings');
        Route::post('/legal-settings', [PageController::class, 'updateLegalSettings'])->name('legal.update');
        
        // Announcement & Popup Control
        Route::get('/announcements', [PageController::class, 'getAnnouncements'])->name('announcements.index');
        Route::get('/announcements/{id}', [PageController::class, 'getAnnouncement'])->name('announcements.show');
        Route::post('/announcements', [PageController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::put('/announcements/{id}', [PageController::class, 'updateAnnouncement'])->name('announcements.update');
        Route::delete('/announcements/{id}', [PageController::class, 'deleteAnnouncement'])->name('announcements.delete');
        Route::post('/announcements/{id}/toggle', [PageController::class, 'toggleAnnouncementStatus'])->name('announcements.toggle');
        
        // Media Library Management
        Route::get('/media', [PageController::class, 'getMedia'])->name('media.index');
        Route::get('/media/folders', [PageController::class, 'getFolders'])->name('media.folders');
        Route::post('/media/folders', [PageController::class, 'createFolder'])->name('media.folders.create');
        Route::delete('/media/folders/{id}', [PageController::class, 'deleteFolder'])->name('media.folders.delete');
        Route::post('/media/upload', [PageController::class, 'uploadMedia'])->name('media.upload');
        Route::get('/media/{id}', [PageController::class, 'getSingleMedia'])->name('media.show');
        Route::delete('/media/{id}', [PageController::class, 'deleteMedia'])->name('media.delete');
        Route::post('/media/{id}/move', [PageController::class, 'moveMedia'])->name('media.move');
        
        // Admin Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/all', [RoleController::class, 'all'])->name('roles.all');
        Route::get('/roles/stats', [RoleController::class, 'stats'])->name('roles.stats');
        Route::get('/roles/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::get('/roles/{id}/users', [RoleController::class, 'getUsersByRole'])->name('roles.users');
        Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/roles/{id}/toggle', [RoleController::class, 'toggleStatus'])->name('roles.toggle');
        Route::post('/users/{userId}/assign-role', [RoleController::class, 'assignRole'])->name('roles.assignRole');
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/all', [SettingController::class, 'getAll'])->name('settings.all');
        Route::get('/settings/stats', [SettingController::class, 'stats'])->name('settings.stats');
        Route::get('/settings/group/{group}', [SettingController::class, 'getGroup'])->name('settings.group');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/upload-logo', [SettingController::class, 'uploadLogo'])->name('settings.uploadLogo');
        Route::post('/settings/upload-favicon', [SettingController::class, 'uploadFavicon'])->name('settings.uploadFavicon');
        Route::post('/settings/test-email', [SettingController::class, 'testEmail'])->name('settings.testEmail');
        Route::post('/settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clearCache');

        // Custom Fields
        Route::get('/custom-fields', [\App\Http\Controllers\Admin\CustomFieldController::class, 'index'])->name('custom-fields.index');
        Route::get('/custom-fields/all', [\App\Http\Controllers\Admin\CustomFieldController::class, 'all'])->name('custom-fields.all');
        Route::get('/custom-fields/category/{id}', [\App\Http\Controllers\Admin\CustomFieldController::class, 'getFieldsByCategory'])->name('custom-fields.byCategory');
        Route::post('/custom-fields', [\App\Http\Controllers\Admin\CustomFieldController::class, 'store'])->name('custom-fields.store');
        Route::get('/custom-fields/{id}', [\App\Http\Controllers\Admin\CustomFieldController::class, 'show'])->name('custom-fields.show');
        Route::put('/custom-fields/{id}', [\App\Http\Controllers\Admin\CustomFieldController::class, 'update'])->name('custom-fields.update');
        Route::delete('/custom-fields/{id}', [\App\Http\Controllers\Admin\CustomFieldController::class, 'destroy'])->name('custom-fields.destroy');
        Route::post('/custom-fields/{id}/toggle', [\App\Http\Controllers\Admin\CustomFieldController::class, 'toggleStatus'])->name('custom-fields.toggle');
        Route::post('/custom-fields/{id}/duplicate', [\App\Http\Controllers\Admin\CustomFieldController::class, 'duplicate'])->name('custom-fields.duplicate');
        Route::post('/custom-fields/reorder', [\App\Http\Controllers\Admin\CustomFieldController::class, 'reorder'])->name('custom-fields.reorder');

        // Banners
        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::get('/banners/all', [BannerController::class, 'all'])->name('banners.all');
        Route::get('/banners/stats', [BannerController::class, 'stats'])->name('banners.stats');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::get('/banners/{id}', [BannerController::class, 'show'])->name('banners.show');
        Route::put('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');
        Route::post('/banners/{id}/toggle', [BannerController::class, 'toggleStatus'])->name('banners.toggle');
        Route::post('/banners/{id}/priority', [BannerController::class, 'setPriority'])->name('banners.priority');
        Route::post('/banners/{id}/schedule', [BannerController::class, 'schedule'])->name('banners.schedule');
        Route::post('/banners/pricing', [BannerController::class, 'updatePricing'])->name('banners.pricing');
        Route::get('/banners/featured/list', [BannerController::class, 'getFeaturedAds'])->name('banners.featured.list');
        Route::post('/banners/featured/{id}/approve', [BannerController::class, 'approveFeaturedAd'])->name('banners.featured.approve');
        Route::post('/banners/featured/{id}/manual', [BannerController::class, 'manuallyFeatureAd'])->name('banners.featured.manual');
        Route::get('/banners/boosted/list', [BannerController::class, 'getBoostedAds'])->name('banners.boosted.list');
        Route::post('/banners/boosted/{id}/approve', [BannerController::class, 'approveBoost'])->name('banners.boosted.approve');
        Route::post('/banners/boosted/{id}/duration', [BannerController::class, 'setBoostDuration'])->name('banners.boosted.duration');
        
        // Advertisements
        Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
        Route::get('/advertisements/all', [AdvertisementController::class, 'all']);
        Route::post('/advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store');
        Route::get('/advertisements/{id}', [AdvertisementController::class, 'show']);
        Route::put('/advertisements/{id}', [AdvertisementController::class, 'update'])->name('advertisements.update');
        Route::delete('/advertisements/{id}', [AdvertisementController::class, 'destroy'])->name('advertisements.destroy');
        Route::post('/advertisements/{id}/toggle', [AdvertisementController::class, 'toggleStatus'])->name('advertisements.toggle');
        
        // Reviews
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/all', [AdminReviewController::class, 'all']);
        Route::get('/reviews/{id}', [AdminReviewController::class, 'show']);
        Route::post('/reviews/{id}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{id}/toggle-featured', [AdminReviewController::class, 'toggleFeatured'])->name('reviews.toggleFeatured');
        Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/all', [NotificationController::class, 'all']);
        Route::get('/notifications/stats', [NotificationController::class, 'stats'])->name('notifications.stats');
        Route::get('/notifications/users', [NotificationController::class, 'getUsers']);
        Route::get('/notifications/types', [NotificationController::class, 'getNotificationTypes'])->name('notifications.types');
        Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
        Route::post('/notifications/broadcast', [NotificationController::class, 'sendBroadcast'])->name('notifications.broadcast');
        Route::post('/notifications/targeted', [NotificationController::class, 'sendTargeted'])->name('notifications.targeted');
        Route::post('/notifications/email-all', [NotificationController::class, 'sendEmailToAll'])->name('notifications.emailAll');
        Route::get('/notifications/{id}', [NotificationController::class, 'show']);
        Route::put('/notifications/{id}', [NotificationController::class, 'update'])->name('notifications.update');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('/notifications/types/{id}/toggle', [NotificationController::class, 'toggleNotificationType'])->name('notifications.types.toggle');
        Route::post('/notifications/types/{id}/toggle-email', [NotificationController::class, 'toggleEmailForType'])->name('notifications.types.toggleEmail');
        Route::put('/notifications/types/{id}', [NotificationController::class, 'updateNotificationType'])->name('notifications.types.update');
    });
});

// API Health Check Route
Route::get('/api/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getDatabaseName(),
        'stats' => [
            'listings' => App\Models\Listing::count(),
            'users' => App\Models\User::count(),
            'dealers' => App\Models\Dealer::count(),
            'reports' => App\Models\Report::count(),
        ]
    ]);
});