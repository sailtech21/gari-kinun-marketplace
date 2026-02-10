import { Car, Bike, Bus, Truck, Ship, Wrench, CircleDot, ArrowRight, TrendingUp } from 'lucide-react'
import { useState, useEffect } from 'react'
import { apiCall } from '../config'

// Icon mapping - maps Font Awesome icon classes to Lucide icons
const iconMap = {
  'fas fa-car': Car,
  'fas fa-motorcycle': Bike,
  'fas fa-bicycle': Bike,
  'fas fa-bus': Bus,
  'fas fa-truck': Truck,
  'fas fa-ship': Ship,
  'fas fa-wrench': Wrench,
  'fas fa-tools': Wrench,
  'Car': Car,
  'Bike': Bike,
  'Bicycle': Bike,
  'Bus': Bus,
  'Truck': Truck,
  'Ship': Ship,
  'Wrench': Wrench,
  'CircleDot': CircleDot
}

// Default icons for categories
const getIconForCategory = (name) => {
  if (name.includes('গাড়ি') || name.includes('কার')) return 'Car'
  if (name.includes('বাইক') || name.includes('মোটরসাইকেল')) return 'Bike'
  if (name.includes('বাস')) return 'Bus'
  if (name.includes('ট্রাক')) return 'Truck'
  if (name.includes('সিএনজি') || name.includes('অটো')) return 'CircleDot'
  if (name.includes('যন্ত্রাংশ')) return 'Wrench'
  return 'CircleDot'
}

const getColorForIndex = (index) => {
  const colors = [
    { bg: 'from-blue-500 to-blue-600', icon: 'text-blue-600', light: 'bg-blue-50', border: 'border-blue-200' },
    { bg: 'from-green-500 to-green-600', icon: 'text-green-600', light: 'bg-green-50', border: 'border-green-200' },
    { bg: 'from-teal-500 to-teal-600', icon: 'text-teal-600', light: 'bg-teal-50', border: 'border-teal-200' },
    { bg: 'from-orange-500 to-orange-600', icon: 'text-orange-600', light: 'bg-orange-50', border: 'border-orange-200' },
    { bg: 'from-pink-500 to-pink-600', icon: 'text-pink-600', light: 'bg-pink-50', border: 'border-pink-200' },
    { bg: 'from-indigo-500 to-indigo-600', icon: 'text-indigo-600', light: 'bg-indigo-50', border: 'border-indigo-200' },
    { bg: 'from-red-500 to-red-600', icon: 'text-red-600', light: 'bg-red-50', border: 'border-red-200' },
    { bg: 'from-yellow-500 to-yellow-600', icon: 'text-yellow-600', light: 'bg-yellow-50', border: 'border-yellow-200' }
  ]
  return colors[index % colors.length]
}

export default function PopularCategories({ onCategoryClick, onViewAll }) {
  const [categories, setCategories] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const response = await apiCall('/categories')
        if (response.success && response.data) {
          // Transform API data
          const transformedCategories = response.data
            .filter(cat => !cat.parent_id) // Only top-level categories
            .slice(0, 8) // Limit to 8 categories
            .map((cat, index) => ({
              id: cat.id,
              name: cat.name,
              count: cat.listings_count || 0,
              icon: cat.icon || 'CircleDot', // Use icon from API, fallback to CircleDot
              color: getColorForIndex(index),
              trending: index < 3 // First 3 are trending
            }))
          setCategories(transformedCategories)
        }
      } catch (error) {
        console.error('Failed to fetch categories:', error)
        setCategories([])
      } finally {
        setLoading(false)
      }
    }

    fetchCategories()
  }, [])

  if (loading) {
    return (
      <section className="py-20 bg-orange-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-4 border-teal-700 border-t-transparent"></div>
            <p className="text-gray-500 mt-4">ক্যাটাগরি লোড হচ্ছে...</p>
          </div>
        </div>
      </section>
    )
  }

  if (categories.length === 0) {
    return null
  }
  
  return (
    <section className="py-20 bg-orange-50 relative overflow-hidden">
      {/* Background Decoration */}
      <div className="absolute inset-0 opacity-5">
        <div className="absolute top-20 left-20 w-72 h-72 bg-teal-600 rounded-full blur-3xl"></div>
        <div className="absolute bottom-20 right-20 w-96 h-96 bg-rose-400 rounded-full blur-3xl"></div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {/* Section Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 px-4 py-2 bg-white text-teal-700 rounded-full text-sm font-semibold mb-4 shadow-sm">
            <CircleDot size={16} />
            <span>ক্যাটাগরি</span>
          </div>
          <h2 className="text-5xl font-bold text-gray-900 mb-4">
            জনপ্রিয় ক্যাটাগরি
          </h2>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            আপনার পছন্দের যানবাহন খুঁজে নিন সহজেই
          </p>
        </div>

        {/* Category Grid */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
          {categories.map((category) => {
            const Icon = iconMap[category.icon] || CircleDot
            return (
              <button
                key={category.id}
                onClick={() => onCategoryClick && onCategoryClick(category.id)}
                className="group relative bg-white rounded-2xl p-6 shadow-lg border-2 border-gray-100 hover:border-teal-200 hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden"
              >
                {/* Gradient Background on Hover */}
                <div className={`absolute inset-0 bg-gradient-to-br ${category.color.bg} opacity-0 group-hover:opacity-10 transition-opacity duration-300`}></div>
                
                {/* Trending Badge */}
                {category.trending && (
                  <div className="absolute top-3 right-3">
                    <div className="flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold rounded-full">
                      <TrendingUp size={12} />
                      <span>ট্রেন্ডিং</span>
                    </div>
                  </div>
                )}

                {/* Content */}
                <div className="relative z-10">
                  {/* Icon Container */}
                  <div className={`${category.color.light} w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-md ${category.color.border} border-2`}>
                    <Icon size={40} className={category.color.icon} strokeWidth={2} />
                  </div>

                  {/* Category Name */}
                  <h3 className="text-lg font-bold text-gray-900 text-center mb-2 group-hover:text-gray-700 transition-colors">
                    {category.name}
                  </h3>

                  {/* Count Badge */}
                  <div className="flex items-center justify-center gap-2">
                    <span className={`px-4 py-1.5 ${category.color.light} ${category.color.icon} rounded-full text-sm font-semibold border ${category.color.border}`}>
                      {category.count} বিজ্ঞাপন
                    </span>
                  </div>

                  {/* Arrow Icon on Hover */}
                  <div className="mt-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div className="flex items-center justify-center gap-2 text-gray-700 font-semibold text-sm group-hover:text-teal-700">
                      <span>দেখুন</span>
                      <ArrowRight size={16} className="group-hover:translate-x-1 transition-transform" />
                    </div>
                  </div>
                </div>
              </button>
            )
          })}
        </div>

        {/* View All Button */}
        <div className="text-center mt-12">
          <button 
            onClick={() => onViewAll && onViewAll()}
            className="inline-flex items-center gap-3 px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-xl font-bold text-lg transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1"
          >
            <span>সব ক্যাটাগরি দেখুন</span>
            <ArrowRight size={20} />
          </button>
        </div>
      </div>
    </section>
  )
}
