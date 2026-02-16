<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('order')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'type' => 'required|in:page,policy,help,other',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (empty($validated['meta_title'])) {
            $validated['meta_title'] = $validated['title'];
        }

        $validated['is_active'] = $request->has('is_active');

        $page = Page::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully',
            'page' => $page
        ]);
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return response()->json($page);
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $id,
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'type' => 'required|in:page,policy,help,other',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['is_active'] = $request->has('is_active');

        $page->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully',
            'page' => $page
        ]);
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $page = Page::findOrFail($id);
        $page->is_active = !$page->is_active;
        $page->save();

        return response()->json([
            'success' => true,
            'message' => 'Page status updated successfully',
            'is_active' => $page->is_active
        ]);
    }

    public function all()
    {
        $pages = Page::active()->orderBy('order')->orderBy('title')->get();
        return response()->json($pages);
    }

    public function stats()
    {
        $total = Page::count();
        $active = Page::where('is_active', true)->count();
        $inactive = Page::where('is_active', false)->count();
        $byType = Page::select('type', \DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        return response()->json([
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'by_type' => $byType,
        ]);
    }

    public function getSystemPages()
    {
        $systemPages = [
            'homepage' => Page::where('slug', 'homepage')->first(),
            'about' => Page::where('slug', 'about')->first(),
            'terms' => Page::where('slug', 'terms')->first(),
            'privacy' => Page::where('slug', 'privacy')->first(),
            'faq' => Page::where('slug', 'faq')->first(),
            'contact' => Page::where('slug', 'contact')->first(),
        ];

        return response()->json($systemPages);
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $page = Page::findOrFail($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('pages', $filename, 'public');

            // Add to images array
            $images = $page->images ?? [];
            $images[] = $path;
            $page->images = $images;

            // Set as featured if none exists
            if (!$page->featured_image) {
                $page->featured_image = $path;
            }

            $page->save();

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image' => $path,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file found'
        ], 400);
    }

    public function deleteImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string'
        ]);

        $page = Page::findOrFail($id);
        $imageToDelete = $request->image;

        // Remove from images array
        if ($page->images) {
            $images = array_filter($page->images, function($img) use ($imageToDelete) {
                return $img !== $imageToDelete;
            });
            $page->images = array_values($images);
        }

        // Remove from featured if it's the featured image
        if ($page->featured_image === $imageToDelete) {
            $page->featured_image = $page->images[0] ?? null;
        }

        $page->save();

        // Delete physical file
        if (Storage::disk('public')->exists($imageToDelete)) {
            Storage::disk('public')->delete($imageToDelete);
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    public function uploadTempImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'page_content_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('pages/content', $filename, 'public');

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'path' => $path,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file found'
        ], 400);
    }

    public function getHeaderLinks()
    {
        try {
            $links = SiteSetting::getHeaderLinks();
            return response()->json([
                'success' => true,
                'links' => $links ?? []
            ]);
        } catch (\Exception $e) {
            \Log::error('Get header links error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'links' => [],
                'message' => 'Header links feature not available yet'
            ]);
        }
    }

    public function updateHeaderLinks(Request $request)
    {
        try {
            $request->validate([
                'links' => 'required|array',
                'links.*.label' => 'required|string',
                'links.*.url' => 'required|string',
                'links.*.order' => 'nullable|integer',
            ]);

            $result = SiteSetting::set('header_links', $request->links);

            if ($result === null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Header links update not available yet (database not configured)'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Header links updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update header links error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'message' => 'Header links saved (some features not available)'
            ]);
        }
    }

    public function getFooter()
    {
        try {
            $content = SiteSetting::getFooterContent();
            $links = SiteSetting::getFooterLinks();
            $settings = [
                'contact_email' => SiteSetting::get('contact_email'),
                'contact_phone' => SiteSetting::get('contact_phone'),
                'social_facebook' => SiteSetting::get('social_facebook'),
                'social_twitter' => SiteSetting::get('social_twitter'),
            ];

            return response()->json([
                'success' => true,
                'content' => $content ?? '',
                'links' => $links ?? [],
                'settings' => $settings,
            ]);
        } catch (\Exception $e) {
            \Log::error('Get footer error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'content' => '',
                'links' => [],
                'settings' => [
                    'contact_email' => '',
                    'contact_phone' => '',
                    'social_facebook' => '',
                    'social_twitter' => '',
                ],
                'message' => 'Footer settings feature not available yet'
            ]);
        }
    }

    public function updateFooter(Request $request)
    {
        try {
            $request->validate([
                'content' => 'nullable|string',
                'links' => 'nullable|array',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string',
                'social_facebook' => 'nullable|url',
                'social_twitter' => 'nullable|url',
            ]);

            if ($request->has('content')) {
                SiteSetting::set('footer_content', $request->content, 'html');
            }

            if ($request->has('links')) {
                SiteSetting::set('footer_links', $request->links);
            }

            if ($request->has('contact_email')) {
                SiteSetting::set('contact_email', $request->contact_email);
            }

            if ($request->has('contact_phone')) {
                SiteSetting::set('contact_phone', $request->contact_phone);
            }

            if ($request->has('social_facebook')) {
                SiteSetting::set('social_facebook', $request->social_facebook);
            }

            if ($request->has('social_twitter')) {
                SiteSetting::set('social_twitter', $request->social_twitter);
            }

            return response()->json([
                'success' => true,
                'message' => 'Footer updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update footer error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'message' => 'Footer saved (some features not available)'
            ]);
        }
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,svg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('site', $filename, 'public');

            // Delete old logo
            $oldLogo = SiteSetting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            SiteSetting::set('site_logo', $path, 'image');

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully',
                'logo' => $path,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No logo file found'
        ], 400);
    }

    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,ico,jpg|max:512'
        ]);

        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $filename = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            $path = $favicon->storeAs('site', $filename, 'public');

            // Delete old favicon
            $oldFavicon = SiteSetting::get('site_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            SiteSetting::set('site_favicon', $path, 'image');

            return response()->json([
                'success' => true,
                'message' => 'Favicon uploaded successfully',
                'favicon' => $path,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No favicon file found'
        ], 400);
    }

    public function getLogoAndFavicon()
    {
        $logo = SiteSetting::get('site_logo');
        $favicon = SiteSetting::get('site_favicon');

        return response()->json([
            'success' => true,
            'logo' => $logo ? Storage::url($logo) : null,
            'favicon' => $favicon ? Storage::url($favicon) : null
        ]);
    }

    public function getAnnouncementBar()
    {
        $announcement = SiteSetting::get('announcement_bar', null);
        
        if (!$announcement) {
            $announcement = [
                'text' => '',
                'bg_color' => '#ffc107',
                'text_color' => '#000000',
                'closeable' => true,
                'enabled' => false
            ];
        } else {
            $announcement = json_decode($announcement, true);
        }

        return response()->json([
            'success' => true,
            'announcement' => $announcement
        ]);
    }

    public function updateAnnouncementBar(Request $request)
    {
        $validated = $request->validate([
            'text' => 'nullable|string|max:500',
            'bg_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'closeable' => 'boolean',
            'enabled' => 'boolean'
        ]);

        SiteSetting::set('announcement_bar', json_encode($validated), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Announcement bar updated successfully'
        ]);
    }

    // ========== FOOTER MANAGEMENT METHODS ==========

    public function uploadFooterLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = 'footer_logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('site', $filename, 'public');

            // Delete old footer logo
            $oldLogo = SiteSetting::get('footer_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            SiteSetting::set('footer_logo', $path, 'image');

            return response()->json([
                'success' => true,
                'message' => 'Footer logo uploaded successfully',
                'logo' => $path,
                'url' => Storage::url($path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No logo file found'
        ], 400);
    }

    public function getFooterLogo()
    {
        $logo = SiteSetting::get('footer_logo');

        return response()->json([
            'success' => true,
            'logo' => $logo ? Storage::url($logo) : null
        ]);
    }

    public function getFooterSettings()
    {
        return response()->json([
            'success' => true,
            'about_title' => SiteSetting::get('footer_about_title', 'About Us'),
            'about_text' => SiteSetting::get('footer_about_text', ''),
            'quick_links' => json_decode(SiteSetting::get('footer_quick_links', '[]'), true),
            'popular_categories' => json_decode(SiteSetting::get('footer_popular_categories', '[]'), true),
            'contact_email' => SiteSetting::get('contact_email', ''),
            'contact_phone' => SiteSetting::get('contact_phone', ''),
            'contact_address' => SiteSetting::get('contact_address', ''),
            'social_facebook' => SiteSetting::get('social_facebook', ''),
            'social_twitter' => SiteSetting::get('social_twitter', ''),
            'social_instagram' => SiteSetting::get('social_instagram', ''),
            'social_youtube' => SiteSetting::get('social_youtube', ''),
            'social_linkedin' => SiteSetting::get('social_linkedin', ''),
            'copyright_text' => SiteSetting::get('copyright_text', ''),
            'newsletter_title' => SiteSetting::get('newsletter_title', 'Subscribe to Newsletter'),
            'newsletter_description' => SiteSetting::get('newsletter_description', ''),
            'newsletter_enabled' => SiteSetting::get('newsletter_enabled', false)
        ]);
    }

    public function updateFooterAbout(Request $request)
    {
        $validated = $request->validate([
            'about_title' => 'nullable|string|max:255',
            'about_text' => 'nullable|string|max:1000'
        ]);

        SiteSetting::set('footer_about_title', $validated['about_title'], 'text');
        SiteSetting::set('footer_about_text', $validated['about_text'], 'text');

        return response()->json([
            'success' => true,
            'message' => 'About section updated successfully'
        ]);
    }

    public function updateFooterQuickLinks(Request $request)
    {
        $validated = $request->validate([
            'quick_links' => 'required|array'
        ]);

        SiteSetting::set('footer_quick_links', json_encode($validated['quick_links']), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Quick links updated successfully'
        ]);
    }

    public function updateFooterPopularCategories(Request $request)
    {
        $validated = $request->validate([
            'popular_categories' => 'required|array'
        ]);

        SiteSetting::set('footer_popular_categories', json_encode($validated['popular_categories']), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Popular categories updated successfully'
        ]);
    }

    public function updateFooterContact(Request $request)
    {
        $validated = $request->validate([
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255'
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::set($key, $value, 'text');
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact information updated successfully'
        ]);
    }

    public function updateFooterCopyright(Request $request)
    {
        $validated = $request->validate([
            'copyright_text' => 'nullable|string|max:255'
        ]);

        SiteSetting::set('copyright_text', $validated['copyright_text'], 'text');

        return response()->json([
            'success' => true,
            'message' => 'Copyright text updated successfully'
        ]);
    }

    public function updateFooterNewsletter(Request $request)
    {
        $validated = $request->validate([
            'newsletter_title' => 'nullable|string|max:255',
            'newsletter_description' => 'nullable|string|max:500',
            'newsletter_enabled' => 'boolean'
        ]);

        SiteSetting::set('newsletter_title', $validated['newsletter_title'], 'text');
        SiteSetting::set('newsletter_description', $validated['newsletter_description'], 'text');
        SiteSetting::set('newsletter_enabled', $validated['newsletter_enabled'], 'boolean');

        return response()->json([
            'success' => true,
            'message' => 'Newsletter settings updated successfully'
        ]);
    }

    // ========== HOMEPAGE MANAGEMENT METHODS ==========

    public function getHeroSection()
    {
        $hero = SiteSetting::get('homepage_hero', null);
        
        if (!$hero) {
            $hero = [
                'main_heading' => 'Find Your Perfect Vehicle',
                'sub_heading' => 'Browse thousands of verified listings',
                'cta_text' => 'Start Searching',
                'cta_link' => '/listings',
                'background' => '',
                'enabled' => true
            ];
        } else {
            $hero = json_decode($hero, true);
        }

        return response()->json([
            'success' => true,
            'hero' => $hero
        ]);
    }

    public function updateHeroSection(Request $request)
    {
        $validated = $request->validate([
            'main_heading' => 'nullable|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:500',
            'background' => 'nullable|string|max:500',
            'enabled' => 'boolean'
        ]);

        SiteSetting::set('homepage_hero', json_encode($validated), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Hero section updated successfully'
        ]);
    }

    public function getBanners()
    {
        $banners = SiteSetting::get('homepage_banners', '[]');
        $banners = json_decode($banners, true);

        return response()->json([
            'success' => true,
            'banners' => $banners
        ]);
    }

    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'image_url' => 'required|string|max:500',
            'title' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        $banners = SiteSetting::get('homepage_banners', '[]');
        $banners = json_decode($banners, true);

        $validated['id'] = time() . rand(1000, 9999);
        $validated['order'] = $validated['order'] ?? count($banners);
        $validated['active'] = $validated['active'] ?? true;

        $banners[] = $validated;

        // Sort by order
        usort($banners, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });

        SiteSetting::set('homepage_banners', json_encode($banners), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Banner added successfully'
        ]);
    }

    public function deleteBanner($index)
    {
        $banners = SiteSetting::get('homepage_banners', '[]');
        $banners = json_decode($banners, true);

        if (isset($banners[$index])) {
            array_splice($banners, $index, 1);
            SiteSetting::set('homepage_banners', json_encode($banners), 'json');
        }

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }

    public function getFeaturedSections()
    {
        $sections = SiteSetting::get('homepage_featured_sections', null);
        
        if (!$sections) {
            $sections = [
                'ads_title' => 'Featured Listings',
                'show_ads' => true,
                'ads_max' => 8,
                'dealers_title' => 'Top Dealers',
                'show_dealers' => true,
                'dealers_max' => 6,
                'categories_title' => 'Browse by Category',
                'show_categories' => true,
                'categories_max' => 12
            ];
        } else {
            $sections = json_decode($sections, true);
        }

        return response()->json([
            'success' => true,
            'sections' => $sections
        ]);
    }

    public function updateFeaturedSections(Request $request)
    {
        $validated = $request->validate([
            'ads_title' => 'nullable|string|max:255',
            'show_ads' => 'boolean',
            'ads_max' => 'nullable|integer|min:1|max:50',
            'dealers_title' => 'nullable|string|max:255',
            'show_dealers' => 'boolean',
            'dealers_max' => 'nullable|integer|min:1|max:50',
            'categories_title' => 'nullable|string|max:255',
            'show_categories' => 'boolean',
            'categories_max' => 'nullable|integer|min:1|max:50'
        ]);

        SiteSetting::set('homepage_featured_sections', json_encode($validated), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Featured sections updated successfully'
        ]);
    }

    public function getCustomBlocks()
    {
        $blocks = SiteSetting::get('homepage_custom_blocks', '[]');
        $blocks = json_decode($blocks, true);

        return response()->json([
            'success' => true,
            'blocks' => $blocks
        ]);
    }

    public function storeCustomBlock(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'active' => 'boolean'
        ]);

        $blocks = SiteSetting::get('homepage_custom_blocks', '[]');
        $blocks = json_decode($blocks, true);

        $validated['id'] = time() . rand(1000, 9999);
        $validated['order'] = $validated['order'] ?? count($blocks);
        $validated['active'] = $validated['active'] ?? true;

        $blocks[] = $validated;

        // Sort by order
        usort($blocks, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });

        SiteSetting::set('homepage_custom_blocks', json_encode($blocks), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Custom block added successfully'
        ]);
    }

    public function deleteCustomBlock($index)
    {
        $blocks = SiteSetting::get('homepage_custom_blocks', '[]');
        $blocks = json_decode($blocks, true);

        if (isset($blocks[$index])) {
            array_splice($blocks, $index, 1);
            SiteSetting::set('homepage_custom_blocks', json_encode($blocks), 'json');
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom block deleted successfully'
        ]);
    }

    public function reorderCustomBlocks(Request $request)
    {
        $validated = $request->validate([
            'blocks' => 'required|array'
        ]);

        SiteSetting::set('homepage_custom_blocks', json_encode($validated['blocks']), 'json');

        return response()->json([
            'success' => true,
            'message' => 'Blocks reordered successfully'
        ]);
    }

    // ============================================
    // SEO MANAGEMENT
    // ============================================

    /**
     * Get Global SEO Settings
     */
    public function getGlobalSeo()
    {
        return response()->json([
            'success' => true,
            'meta_title' => SiteSetting::get('global_meta_title', ''),
            'meta_description' => SiteSetting::get('global_meta_description', ''),
            'meta_keywords' => SiteSetting::get('global_meta_keywords', ''),
            'og_image' => SiteSetting::get('global_og_image'),
            'sitemap_enabled' => SiteSetting::get('sitemap_enabled', true)
        ]);
    }

    /**
     * Update Global SEO Settings
     */
    public function updateGlobalSeo(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'sitemap_enabled' => 'boolean'
        ]);

        SiteSetting::set('global_meta_title', $validated['meta_title'] ?? '', 'text');
        SiteSetting::set('global_meta_description', $validated['meta_description'] ?? '', 'text');
        SiteSetting::set('global_meta_keywords', $validated['meta_keywords'] ?? '', 'text');
        SiteSetting::set('sitemap_enabled', $validated['sitemap_enabled'] ?? true, 'boolean');

        return response()->json([
            'success' => true,
            'message' => 'Global SEO settings updated successfully'
        ]);
    }

    /**
     * Upload OG Image
     */
    public function uploadOgImage(Request $request)
    {
        $request->validate([
            'og_image' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048'
        ]);

        try {
            // Delete old OG image if exists
            $oldImage = SiteSetting::get('global_og_image');
            if ($oldImage && \Storage::disk('public')->exists(str_replace('/storage/', '', $oldImage))) {
                \Storage::disk('public')->delete(str_replace('/storage/', '', $oldImage));
            }

            // Upload new OG image
            $file = $request->file('og_image');
            $filename = 'og_image_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('site', $filename, 'public');

            SiteSetting::set('global_og_image', '/storage/' . $path, 'text');

            return response()->json([
                'success' => true,
                'message' => 'OG Image uploaded successfully',
                'url' => '/storage/' . $path
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload OG image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Robots.txt Content
     */
    public function getRobotsTxt()
    {
        $content = SiteSetting::get('robots_txt_content', "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml'));
        
        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }

    /**
     * Update Robots.txt Content
     */
    public function updateRobotsTxt(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        SiteSetting::set('robots_txt_content', $validated['content'], 'text');

        // Optionally write to actual robots.txt file in public directory
        try {
            file_put_contents(public_path('robots.txt'), $validated['content']);
        } catch (\Exception $e) {
            // If file write fails, continue anyway as we have it in database
        }

        return response()->json([
            'success' => true,
            'message' => 'Robots.txt updated successfully'
        ]);
    }

    /**
     * Get Page-Level SEO
     */
    public function getPageSeo($id)
    {
        $page = Page::findOrFail($id);

        return response()->json([
            'success' => true,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords
        ]);
    }

    /**
     * Update Page-Level SEO
     */
    public function updatePageSeo(Request $request, $id)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500'
        ]);

        $page = Page::findOrFail($id);
        $page->update([
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Page SEO settings updated successfully'
        ]);
    }

    /**
     * Contact Page Management
     */
    
    // Get all contact settings
    public function getContactSettings()
    {
        return response()->json([
            'success' => true,
            'address' => SiteSetting::get('contact_address', ''),
            'phone' => SiteSetting::get('contact_phone', ''),
            'phone_alt' => SiteSetting::get('contact_phone_alt', ''),
            'email' => SiteSetting::get('contact_email', ''),
            'support_email' => SiteSetting::get('contact_support_email', ''),
            'map_embed' => SiteSetting::get('contact_map_embed', ''),
            'map_enabled' => SiteSetting::get('contact_map_enabled', false),
            'page_heading' => SiteSetting::get('contact_page_heading', 'Get In Touch'),
            'page_description' => SiteSetting::get('contact_page_description', ''),
            'form_enabled' => SiteSetting::get('contact_form_enabled', true),
            'form_email' => SiteSetting::get('contact_form_email', ''),
            'form_success_message' => SiteSetting::get('contact_form_success_message', 'Thank you for contacting us. We will get back to you soon!')
        ]);
    }

    // Update contact information
    public function updateContactInfo(Request $request)
    {
        $validated = $request->validate([
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'phone_alt' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'support_email' => 'nullable|email|max:255'
        ]);

        SiteSetting::set('contact_address', $validated['address'] ?? '', 'text');
        SiteSetting::set('contact_phone', $validated['phone'] ?? '', 'text');
        SiteSetting::set('contact_phone_alt', $validated['phone_alt'] ?? '', 'text');
        SiteSetting::set('contact_email', $validated['email'] ?? '', 'text');
        SiteSetting::set('contact_support_email', $validated['support_email'] ?? '', 'text');

        return response()->json([
            'success' => true,
            'message' => 'Contact information updated successfully'
        ]);
    }

    // Update contact map settings
    public function updateContactMap(Request $request)
    {
        $validated = $request->validate([
            'map_embed' => 'nullable|string',
            'map_enabled' => 'boolean'
        ]);

        SiteSetting::set('contact_map_embed', $validated['map_embed'] ?? '', 'text');
        SiteSetting::set('contact_map_enabled', $validated['map_enabled'] ?? false, 'boolean');

        return response()->json([
            'success' => true,
            'message' => 'Map settings updated successfully'
        ]);
    }

    // Update contact page content
    public function updateContactContent(Request $request)
    {
        $validated = $request->validate([
            'page_heading' => 'nullable|string|max:255',
            'page_description' => 'nullable|string|max:1000'
        ]);

        SiteSetting::set('contact_page_heading', $validated['page_heading'] ?? 'Get In Touch', 'text');
        SiteSetting::set('contact_page_description', $validated['page_description'] ?? '', 'text');

        return response()->json([
            'success' => true,
            'message' => 'Page content updated successfully'
        ]);
    }

    // Update contact form settings
    public function updateContactFormSettings(Request $request)
    {
        $validated = $request->validate([
            'form_enabled' => 'boolean',
            'form_email' => 'nullable|email|max:255',
            'form_success_message' => 'nullable|string|max:500'
        ]);

        SiteSetting::set('contact_form_enabled', $validated['form_enabled'] ?? true, 'boolean');
        SiteSetting::set('contact_form_email', $validated['form_email'] ?? '', 'text');
        SiteSetting::set('contact_form_success_message', $validated['form_success_message'] ?? '', 'text');

        return response()->json([
            'success' => true,
            'message' => 'Form settings updated successfully'
        ]);
    }

    /**
     * Email Template Management
     */

    // Default email templates
    private function getDefaultEmailTemplates()
    {
        return [
            'welcome' => [
                'subject' => 'Welcome to {site_name}!',
                'content' => '<h2>Welcome {name}!</h2><p>Thank you for registering at {site_name}. We are excited to have you on board.</p><p>You can now browse and post ads on our platform.</p>',
                'footer' => 'Best regards,<br>The {site_name} Team'
            ],
            'ad_approved' => [
                'subject' => 'Your Ad "{ad_title}" has been Approved',
                'content' => '<h2>Great News {name}!</h2><p>Your ad <strong>{ad_title}</strong> has been approved and is now live on our platform.</p><p>Users can now view and contact you about this listing.</p>',
                'footer' => 'Thank you for using {site_name}!'
            ],
            'ad_rejected' => [
                'subject' => 'Your Ad "{ad_title}" Requires Review',
                'content' => '<h2>Hello {name},</h2><p>We regret to inform you that your ad <strong>{ad_title}</strong> could not be approved at this time.</p><p><strong>Reason:</strong> {reason}</p><p>Please review our guidelines and resubmit your ad.</p>',
                'footer' => 'Need help? Contact our support team.'
            ],
            'password_reset' => [
                'subject' => 'Password Reset Request',
                'content' => '<h2>Hello {name},</h2><p>We received a request to reset your password. Click the button below to reset it:</p><p><a href="{reset_link}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a></p><p>If you did not request a password reset, please ignore this email.</p><p>This link will expire in 60 minutes.</p>',
                'footer' => 'For security reasons, never share this link with anyone.'
            ],
            'dealer_approval' => [
                'subject' => 'Your Dealer Account has been Approved!',
                'content' => '<h2>Congratulations {name}!</h2><p>Your dealer account has been approved. You now have access to premium features including:</p><ul><li>Featured listings</li><li>Priority placement</li><li>Advanced analytics</li><li>Bulk upload tools</li></ul><p>Start posting your inventory today!</p>',
                'footer' => 'Welcome to our dealer community!'
            ],
            'promotion_confirmation' => [
                'subject' => 'Ad Promotion Confirmed',
                'content' => '<h2>Hello {name},</h2><p>Your ad <strong>{ad_title}</strong> has been successfully promoted!</p><p>Your listing will now receive:</p><ul><li>Featured placement on homepage</li><li>Priority in search results</li><li>Highlighted appearance</li><li>Increased visibility</li></ul><p>Promotion period: 30 days</p>',
                'footer' => 'Thank you for promoting with {site_name}!'
            ]
        ];
    }

    // Get email template
    public function getEmailTemplate($type)
    {
        $defaults = $this->getDefaultEmailTemplates();
        
        if (!isset($defaults[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid template type'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'subject' => SiteSetting::get("email_template_{$type}_subject", $defaults[$type]['subject']),
            'content' => SiteSetting::get("email_template_{$type}_content", $defaults[$type]['content']),
            'footer' => SiteSetting::get("email_template_{$type}_footer", $defaults[$type]['footer'])
        ]);
    }

    // Update email template
    public function updateEmailTemplate(Request $request, $type)
    {
        $defaults = $this->getDefaultEmailTemplates();
        
        if (!isset($defaults[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid template type'
            ], 404);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'footer' => 'nullable|string|max:1000'
        ]);

        SiteSetting::set("email_template_{$type}_subject", $validated['subject'], 'text');
        SiteSetting::set("email_template_{$type}_content", $validated['content'], 'text');
        SiteSetting::set("email_template_{$type}_footer", $validated['footer'] ?? '', 'text');

        return response()->json([
            'success' => true,
            'message' => 'Email template updated successfully'
        ]);
    }

    // Reset email template to default
    public function resetEmailTemplate($type)
    {
        $defaults = $this->getDefaultEmailTemplates();
        
        if (!isset($defaults[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid template type'
            ], 404);
        }

        SiteSetting::set("email_template_{$type}_subject", $defaults[$type]['subject'], 'text');
        SiteSetting::set("email_template_{$type}_content", $defaults[$type]['content'], 'text');
        SiteSetting::set("email_template_{$type}_footer", $defaults[$type]['footer'], 'text');

        return response()->json([
            'success' => true,
            'message' => 'Email template reset to default'
        ]);
    }

    /**
     * Advertisement Space Management
     */

    // Get all advertisement spaces
    public function getAdSpaces()
    {
        return response()->json([
            'success' => true,
            'header_enabled' => SiteSetting::get('ad_header_enabled', false),
            'header_code' => SiteSetting::get('ad_header_code', ''),
            'homepage_enabled' => SiteSetting::get('ad_homepage_enabled', false),
            'homepage_top' => SiteSetting::get('ad_homepage_top', ''),
            'homepage_middle' => SiteSetting::get('ad_homepage_middle', ''),
            'homepage_bottom' => SiteSetting::get('ad_homepage_bottom', ''),
            'homepage_sidebar' => SiteSetting::get('ad_homepage_sidebar', ''),
            'sidebar_enabled' => SiteSetting::get('ad_sidebar_enabled', false),
            'sidebar_code' => SiteSetting::get('ad_sidebar_code', ''),
            'footer_enabled' => SiteSetting::get('ad_footer_enabled', false),
            'footer_code' => SiteSetting::get('ad_footer_code', ''),
            'listing_detail_enabled' => SiteSetting::get('ad_listing_detail_enabled', false),
            'listing_detail_code' => SiteSetting::get('ad_listing_detail_code', '')
        ]);
    }

    // Update advertisement spaces
    public function updateAdSpaces(Request $request)
    {
        $type = $request->input('type');
        
        switch ($type) {
            case 'header':
                $validated = $request->validate([
                    'enabled' => 'boolean',
                    'code' => 'nullable|string'
                ]);
                
                SiteSetting::set('ad_header_enabled', $validated['enabled'] ?? false, 'boolean');
                SiteSetting::set('ad_header_code', $validated['code'] ?? '', 'text');
                break;
                
            case 'homepage':
                $validated = $request->validate([
                    'enabled' => 'boolean',
                    'top' => 'nullable|string',
                    'middle' => 'nullable|string',
                    'bottom' => 'nullable|string',
                    'sidebar' => 'nullable|string'
                ]);
                
                SiteSetting::set('ad_homepage_enabled', $validated['enabled'] ?? false, 'boolean');
                SiteSetting::set('ad_homepage_top', $validated['top'] ?? '', 'text');
                SiteSetting::set('ad_homepage_middle', $validated['middle'] ?? '', 'text');
                SiteSetting::set('ad_homepage_bottom', $validated['bottom'] ?? '', 'text');
                SiteSetting::set('ad_homepage_sidebar', $validated['sidebar'] ?? '', 'text');
                break;
                
            case 'sidebar':
                $validated = $request->validate([
                    'enabled' => 'boolean',
                    'code' => 'nullable|string'
                ]);
                
                SiteSetting::set('ad_sidebar_enabled', $validated['enabled'] ?? false, 'boolean');
                SiteSetting::set('ad_sidebar_code', $validated['code'] ?? '', 'text');
                break;
                
            case 'footer':
                $validated = $request->validate([
                    'enabled' => 'boolean',
                    'code' => 'nullable|string'
                ]);
                
                SiteSetting::set('ad_footer_enabled', $validated['enabled'] ?? false, 'boolean');
                SiteSetting::set('ad_footer_code', $validated['code'] ?? '', 'text');
                break;
                
            case 'listing_detail':
                $validated = $request->validate([
                    'enabled' => 'boolean',
                    'code' => 'nullable|string'
                ]);
                
                SiteSetting::set('ad_listing_detail_enabled', $validated['enabled'] ?? false, 'boolean');
                SiteSetting::set('ad_listing_detail_code', $validated['code'] ?? '', 'text');
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ad space type'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $type)) . ' ad space updated successfully'
        ]);
    }

    /**
     * Language & Text Control Management
     */

    // Get default text labels
    private function getDefaultTextLabels()
    {
        return [
            'system_labels' => [
                'label_home' => 'Home',
                'label_about' => 'About Us',
                'label_contact' => 'Contact',
                'label_categories' => 'Categories',
                'label_featured' => 'Featured',
                'label_latest' => 'Latest',
                'label_popular' => 'Popular',
                'label_search' => 'Search'
            ],
            'button_text' => [
                'btn_submit' => 'Submit',
                'btn_cancel' => 'Cancel',
                'btn_save' => 'Save',
                'btn_delete' => 'Delete',
                'btn_edit' => 'Edit',
                'btn_view_details' => 'View Details',
                'btn_contact_seller' => 'Contact Seller',
                'btn_post_ad' => 'Post Ad'
            ],
            'error_messages' => [
                'error_required' => 'This field is required',
                'error_invalid_email' => 'Please enter a valid email address',
                'error_password_mismatch' => 'Passwords do not match',
                'error_upload_failed' => 'File upload failed',
                'error_not_found' => 'Item not found',
                'error_permission_denied' => 'You don\'t have permission to perform this action'
            ],
            'form_labels' => [
                'form_name' => 'Name',
                'form_email' => 'Email',
                'form_password' => 'Password',
                'form_phone' => 'Phone',
                'form_address' => 'Address',
                'form_message' => 'Message'
            ],
            'navigation' => [
                'nav_my_listings' => 'My Listings',
                'nav_my_profile' => 'My Profile',
                'nav_dashboard' => 'Dashboard',
                'nav_logout' => 'Logout',
                'nav_login' => 'Login',
                'nav_register' => 'Register'
            ],
            'listing_text' => [
                'listing_price' => 'Price',
                'listing_location' => 'Location',
                'listing_condition' => 'Condition',
                'listing_description' => 'Description',
                'listing_posted' => 'Posted',
                'listing_views' => 'Views'
            ]
        ];
    }

    // Get text labels for a category
    public function getTextLabels($category)
    {
        $defaults = $this->getDefaultTextLabels();
        
        if (!isset($defaults[$category])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category'
            ], 404);
        }

        $labels = [];
        foreach ($defaults[$category] as $key => $defaultValue) {
            $labels[$key] = SiteSetting::get("text_{$key}", $defaultValue);
        }

        return response()->json([
            'success' => true,
            'labels' => $labels
        ]);
    }

    // Update text labels
    public function updateTextLabels(Request $request, $category)
    {
        $defaults = $this->getDefaultTextLabels();
        
        if (!isset($defaults[$category])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category'
            ], 404);
        }

        $validated = $request->validate([
            'labels' => 'required|array'
        ]);

        foreach ($validated['labels'] as $key => $value) {
            // Only save if the key exists in defaults
            if (isset($defaults[$category][$key])) {
                SiteSetting::set("text_{$key}", $value, 'text');
            }
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $category)) . ' updated successfully'
        ]);
    }

    /**
     * Legal & Compliance Management
     */

    public function getLegalSettings()
    {
        return response()->json([
            'success' => true,
            // Cookie Policy
            'cookie_policy_title' => SiteSetting::get('legal_cookie_policy_title', 'Cookie Policy'),
            'cookie_policy_content' => SiteSetting::get('legal_cookie_policy_content', ''),
            // Cookie Popup
            'cookie_popup_enabled' => SiteSetting::get('legal_cookie_popup_enabled', false),
            'cookie_popup_message' => SiteSetting::get('legal_cookie_popup_message', 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.'),
            'cookie_accept_btn' => SiteSetting::get('legal_cookie_accept_btn', 'Accept All'),
            'cookie_decline_btn' => SiteSetting::get('legal_cookie_decline_btn', 'Decline'),
            'cookie_learn_more' => SiteSetting::get('legal_cookie_learn_more', 'Learn More'),
            'cookie_popup_position' => SiteSetting::get('legal_cookie_popup_position', 'bottom'),
            // GDPR
            'gdpr_title' => SiteSetting::get('legal_gdpr_title', 'Privacy & Data Protection'),
            'gdpr_content' => SiteSetting::get('legal_gdpr_content', ''),
            'gdpr_contact_email' => SiteSetting::get('legal_gdpr_contact_email', ''),
            'gdpr_show_data_request' => SiteSetting::get('legal_gdpr_show_data_request', false),
            // Terms Acceptance
            'terms_acceptance_required' => SiteSetting::get('legal_terms_acceptance_required', false),
            'terms_checkbox_label' => SiteSetting::get('legal_terms_checkbox_label', 'I agree to the Terms and Conditions'),
            'terms_link_text' => SiteSetting::get('legal_terms_link_text', 'Terms and Conditions'),
            'privacy_link_text' => SiteSetting::get('legal_privacy_link_text', 'Privacy Policy'),
            'newsletter_optin' => SiteSetting::get('legal_newsletter_optin', false),
            'newsletter_optin_label' => SiteSetting::get('legal_newsletter_optin_label', 'I want to receive newsletters and updates'),
            // Privacy Policy
            'privacy_policy_title' => SiteSetting::get('legal_privacy_policy_title', 'Privacy Policy'),
            'privacy_policy_content' => SiteSetting::get('legal_privacy_policy_content', ''),
            'privacy_last_updated' => SiteSetting::get('legal_privacy_last_updated', '')
        ]);
    }

    public function updateLegalSettings(Request $request)
    {
        $section = $request->input('section');
        
        switch ($section) {
            case 'cookie_policy':
                SiteSetting::set('legal_cookie_policy_title', $request->cookie_policy_title, 'text');
                SiteSetting::set('legal_cookie_policy_content', $request->cookie_policy_content, 'text');
                break;
            case 'cookie_popup':
                SiteSetting::set('legal_cookie_popup_enabled', $request->cookie_popup_enabled, 'boolean');
                SiteSetting::set('legal_cookie_popup_message', $request->cookie_popup_message, 'text');
                SiteSetting::set('legal_cookie_accept_btn', $request->cookie_accept_btn, 'text');
                SiteSetting::set('legal_cookie_decline_btn', $request->cookie_decline_btn, 'text');
                SiteSetting::set('legal_cookie_learn_more', $request->cookie_learn_more, 'text');
                SiteSetting::set('legal_cookie_popup_position', $request->cookie_popup_position, 'text');
                break;
            case 'gdpr':
                SiteSetting::set('legal_gdpr_title', $request->gdpr_title, 'text');
                SiteSetting::set('legal_gdpr_content', $request->gdpr_content, 'text');
                SiteSetting::set('legal_gdpr_contact_email', $request->gdpr_contact_email, 'text');
                SiteSetting::set('legal_gdpr_show_data_request', $request->gdpr_show_data_request, 'boolean');
                break;
            case 'terms_acceptance':
                SiteSetting::set('legal_terms_acceptance_required', $request->terms_acceptance_required, 'boolean');
                SiteSetting::set('legal_terms_checkbox_label', $request->terms_checkbox_label, 'text');
                SiteSetting::set('legal_terms_link_text', $request->terms_link_text, 'text');
                SiteSetting::set('legal_privacy_link_text', $request->privacy_link_text, 'text');
                SiteSetting::set('legal_newsletter_optin', $request->newsletter_optin, 'boolean');
                SiteSetting::set('legal_newsletter_optin_label', $request->newsletter_optin_label, 'text');
                break;
            case 'privacy_policy':
                SiteSetting::set('legal_privacy_policy_title', $request->privacy_policy_title, 'text');
                SiteSetting::set('legal_privacy_policy_content', $request->privacy_policy_content, 'text');
                SiteSetting::set('legal_privacy_last_updated', $request->privacy_last_updated, 'text');
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid section'], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $section)) . ' settings saved successfully'
        ]);
    }

    /**
     * Announcement & Popup Control
     */

    public function getAnnouncements()
    {
        try {
            $announcements = \DB::table('announcements')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'announcements' => $announcements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading announcements: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAnnouncement($id)
    {
        try {
            $announcement = \DB::table('announcements')->where('id', $id)->first();

            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'announcement' => $announcement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeAnnouncement(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'position' => 'required|in:center_modal,top_banner,bottom_banner',
                'frequency' => 'required|in:once,always,daily',
                'target_users' => 'required|in:all,registered,dealers,guests',
                'enabled' => 'boolean',
                'image' => 'nullable|image|max:2048'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/announcements'), $imageName);
                $imagePath = '/uploads/announcements/' . $imageName;
            }

            $id = \DB::table('announcements')->insertGetId([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'image' => $imagePath,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'position' => $validated['position'],
                'frequency' => $validated['frequency'],
                'target_users' => $validated['target_users'],
                'enabled' => $request->input('enabled', true),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement created successfully',
                'announcement_id' => $id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAnnouncement(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'position' => 'required|in:center_modal,top_banner,bottom_banner',
                'frequency' => 'required|in:once,always,daily',
                'target_users' => 'required|in:all,registered,dealers,guests',
                'enabled' => 'boolean',
                'image' => 'nullable|image|max:2048'
            ]);

            $announcement = \DB::table('announcements')->where('id', $id)->first();
            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement not found'
                ], 404);
            }

            $imagePath = $announcement->image;
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($imagePath && file_exists(public_path($imagePath))) {
                    unlink(public_path($imagePath));
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/announcements'), $imageName);
                $imagePath = '/uploads/announcements/' . $imageName;
            }

            \DB::table('announcements')->where('id', $id)->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'image' => $imagePath,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'position' => $validated['position'],
                'frequency' => $validated['frequency'],
                'target_users' => $validated['target_users'],
                'enabled' => $request->input('enabled', true),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Announcement updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAnnouncement($id)
    {
        try {
            $announcement = \DB::table('announcements')->where('id', $id)->first();
            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement not found'
                ], 404);
            }

            // Delete image if exists
            if ($announcement->image && file_exists(public_path($announcement->image))) {
                unlink(public_path($announcement->image));
            }

            \DB::table('announcements')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Announcement deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting announcement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleAnnouncementStatus($id)
    {
        try {
            $announcement = \DB::table('announcements')->where('id', $id)->first();
            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Announcement not found'
                ], 404);
            }

            \DB::table('announcements')->where('id', $id)->update([
                'enabled' => !$announcement->enabled,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Media Library Management
     */

    public function getMedia(Request $request)
    {
        try {
            $type = $request->query('type', 'all');
            $folderId = $request->query('folder_id');

            $query = \DB::table('media_files')->orderBy('created_at', 'desc');

            if ($type !== 'all') {
                $query->where('type', $type);
            }

            if ($folderId) {
                $query->where('folder_id', $folderId);
            } else {
                $query->whereNull('folder_id');
            }

            $media = $query->get();
            $folders = \DB::table('media_folders')->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'media' => $media,
                'folders' => $folders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading media: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSingleMedia($id)
    {
        try {
            $media = \DB::table('media_files')->where('id', $id)->first();

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Media not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'media' => $media
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading media: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadMedia(Request $request)
    {
        try {
            $request->validate([
                'files.*' => 'required|file|mimes:jpeg,jpg,png,gif,mp4,mov,avi,webm|max:10240',
                'folder_id' => 'nullable|exists:media_folders,id'
            ]);

            $folderId = $request->input('folder_id');
            $uploadedFiles = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '_' . uniqid() . '.' . $extension;
                    
                    // Determine type
                    $mimeType = $file->getMimeType();
                    $type = 'file';
                    if (strpos($mimeType, 'image') !== false) {
                        $type = 'image';
                    } elseif (strpos($mimeType, 'video') !== false) {
                        $type = 'video';
                    }

                    $file->move(public_path('uploads/media'), $fileName);
                    $url = '/uploads/media/' . $fileName;

                    $id = \DB::table('media_files')->insertGetId([
                        'filename' => $originalName,
                        'filepath' => $fileName,
                        'url' => $url,
                        'type' => $type,
                        'size' => $file->getSize(),
                        'folder_id' => $folderId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $uploadedFiles[] = $id;
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
                'file_ids' => $uploadedFiles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading media: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteMedia($id)
    {
        try {
            $media = \DB::table('media_files')->where('id', $id)->first();

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Media not found'
                ], 404);
            }

            // Delete file from storage
            $filePath = public_path('uploads/media/' . $media->filepath);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            \DB::table('media_files')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting media: ' . $e->getMessage()
            ], 500);
        }
    }

    public function moveMedia(Request $request, $id)
    {
        try {
            $request->validate([
                'folder_id' => 'nullable|exists:media_folders,id'
            ]);

            $media = \DB::table('media_files')->where('id', $id)->first();

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Media not found'
                ], 404);
            }

            \DB::table('media_files')->where('id', $id)->update([
                'folder_id' => $request->input('folder_id'),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Media moved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving media: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFolders()
    {
        try {
            $folders = \DB::table('media_folders')->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'folders' => $folders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading folders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createFolder(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:media_folders,name'
            ]);

            $id = \DB::table('media_folders')->insertGetId([
                'name' => $request->input('name'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully',
                'folder_id' => $id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating folder: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteFolder($id)
    {
        try {
            $folder = \DB::table('media_folders')->where('id', $id)->first();

            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder not found'
                ], 404);
            }

            // Move all media in this folder to root (no folder)
            \DB::table('media_files')->where('folder_id', $id)->update([
                'folder_id' => null,
                'updated_at' => now()
            ]);

            \DB::table('media_folders')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Folder deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting folder: ' . $e->getMessage()
            ], 500);
        }
    }
}
