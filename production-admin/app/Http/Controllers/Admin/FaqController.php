<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    // Get all FAQs
    public function index(Request $request)
    {
        $query = Faq::with('category')->orderBy('order');
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        $faqs = $query->get();
        
        return response()->json([
            'success' => true,
            'faqs' => $faqs
        ]);
    }

    // Get single FAQ
    public function show($id)
    {
        $faq = Faq::with('category')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'faq' => $faq
        ]);
    }

    // Create new FAQ
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:faq_categories,id'
        ]);

        // Get the highest order number and add 1
        $maxOrder = Faq::max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        $faq = Faq::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully',
            'faq' => $faq
        ]);
    }

    // Update FAQ
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:faq_categories,id'
        ]);

        $faq->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully',
            'faq' => $faq->load('category')
        ]);
    }

    // Delete FAQ
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ deleted successfully'
        ]);
    }

    // Reorder FAQs
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'faq_ids' => 'required|array',
            'faq_ids.*' => 'exists:faqs,id'
        ]);

        foreach ($validated['faq_ids'] as $index => $faqId) {
            Faq::where('id', $faqId)->update(['order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FAQs reordered successfully'
        ]);
    }

    // Get all FAQ categories
    public function getCategories()
    {
        $categories = FaqCategory::withCount('faqs')->orderBy('order')->get();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    // Create FAQ category
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name'
        ]);

        // Get the highest order number and add 1
        $maxOrder = FaqCategory::max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        $category = FaqCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }

    // Delete FAQ category
    public function destroyCategory($id)
    {
        $category = FaqCategory::findOrFail($id);
        
        // Set all FAQs in this category to null
        Faq::where('category_id', $id)->update(['category_id' => null]);
        
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
