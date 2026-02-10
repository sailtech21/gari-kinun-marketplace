import { useState, useEffect } from 'react'
import { 
  Search, SlidersHorizontal, ArrowLeft, Grid, List, 
  Calendar, Fuel, MapPin, Car, DollarSign, Tag, X, Filter, Sparkles, TrendingUp
} from 'lucide-react'
import { apiCall } from '../../config'
import VehicleCard from '../common/VehicleCard'
import Button from '../common/Button'

export default function SearchPage({ onBack, onViewListing, initialCategory, initialBrand }) {
  const [searchQuery, setSearchQuery] = useState('')
  const [showFilters, setShowFilters] = useState(true)
  const [viewMode, setViewMode] = useState('grid') // 'grid' or 'list'
  const [listings, setListings] = useState([])
  const [loading, setLoading] = useState(false)
  const [loadingMore, setLoadingMore] = useState(false)
  const [categories, setCategories] = useState([])
  const [brands, setBrands] = useState([])
  const [pagination, setPagination] = useState({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  })
  
  const fuelTypes = ['পেট্রোল', 'ডিজেল', 'সিএনজি', 'হাইব্রিড', 'ইলেকট্রিক']
  const conditionTypes = ['নতুন', 'পুরাতন', 'রিকন্ডিশন্ড']
  const locations = [
    'ঢাকা', 'চট্টগ্রাম', 'খুলনা', 'রাজশাহী', 'বরিশাল', 
    'সিলেট', 'রংপুর', 'ময়মনসিংহ', 'গাজীপুর', 'নারায়ণগঞ্জ'
  ]
  
  const [filters, setFilters] = useState({
    category_id: initialCategory || '',
    location: '',
    price_min: '',
    price_max: '',
    year_min: '',
    year_max: '',
    fuel_type: '',
    condition: '',
    make: initialBrand || '',
    model: ''
  })
  
  const [sortBy, setSortBy] = useState('newest') // 'newest', 'price_low', 'price_high'
  
  // Update filters when initialCategory or initialBrand changes
  useEffect(() => {
    setFilters(prev => ({
      ...prev,
      category_id: initialCategory || '',
      make: initialBrand || ''
    }))
  }, [initialCategory, initialBrand])
  
  // Fetch categories
  useEffect(() => {
    const fetchData = async () => {
      try {
        // Fetch categories
        const categoriesResponse = await apiCall('/categories')
        if (categoriesResponse.success && categoriesResponse.data) {
          const dataArray = Array.isArray(categoriesResponse.data) ? categoriesResponse.data : []
          setCategories(dataArray)
        }
      } catch (error) {
        console.error('Failed to fetch data:', error)
      }
    }
    fetchData()
  }, [])
  
  // Fetch listings with filters
  useEffect(() => {
    const fetchListings = async (page = 1) => {
      if (page === 1) {
        setLoading(true)
      } else {
        setLoadingMore(true)
      }
      
      try {
        // Build query params
        const params = new URLSearchParams()
        
        if (searchQuery) params.append('search', searchQuery)
        if (filters.category_id) params.append('category_id', filters.category_id)
        if (filters.location) params.append('location', filters.location)
        if (filters.price_min) params.append('min_price', filters.price_min)
        if (filters.price_max) params.append('max_price', filters.price_max)
        if (filters.year_min) params.append('year_min', filters.year_min)
        if (filters.year_max) params.append('year_max', filters.year_max)
        if (filters.fuel_type) params.append('fuel_type', filters.fuel_type)
        if (filters.condition) params.append('condition', filters.condition)
        if (filters.make) params.append('make', filters.make)
        if (filters.model) params.append('model', filters.model)
        
        // Convert sortBy to sort_by and sort_order
        if (sortBy === 'newest') {
          params.append('sort_by', 'created_at')
          params.append('sort_order', 'desc')
        } else if (sortBy === 'price_low') {
          params.append('sort_by', 'price')
          params.append('sort_order', 'asc')
        } else if (sortBy === 'price_high') {
          params.append('sort_by', 'price')
          params.append('sort_order', 'desc')
        }
        
        params.append('per_page', '15')
        params.append('page', page)
        
        const queryString = params.toString()
        const endpoint = `/listings?${queryString}`
        
        const response = await apiCall(endpoint)
        if (response.success && response.data) {
          const dataArray = Array.isArray(response.data) ? response.data : []
          
          if (page === 1) {
            setListings(dataArray)
          } else {
            setListings(prev => [...prev, ...dataArray])
          }
          
          if (response.pagination) {
            setPagination(response.pagination)
          }
        }
      } catch (error) {
        console.error('Failed to fetch listings:', error)
        if (page === 1) {
          setListings([])
        }
      } finally {
        if (page === 1) {
          setLoading(false)
        } else {
          setLoadingMore(false)
        }
      }
    }
    
    fetchListings(1)
    
    // Store the function for load more
    window.loadMoreListings = () => {
      if (pagination.current_page < pagination.last_page && !loadingMore) {
        fetchListings(pagination.current_page + 1)
      }
    }
  }, [searchQuery, filters, sortBy])
  
  const handleFilterChange = (name, value) => {
    setFilters({
      ...filters,
      [name]: value
    })
  }
  
  const clearFilters = () => {
    setFilters({
      category_id: '',
      location: '',
      price_min: '',
      price_max: '',
      year_min: '',
      year_max: '',
      fuel_type: '',
      condition: '',
      make: '',
      model: ''
    })
    setSearchQuery('')
  }
  
  const activeFiltersCount = Object.values(filters).filter(v => v !== '').length + (searchQuery ? 1 : 0)
  
  return (
    <div className="min-h-screen bg-orange-50">
      {/* Header */}
      <div className="bg-teal-700 shadow-xl sticky top-0 z-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="flex items-center justify-between mb-4">
            <button 
              onClick={onBack}
              className="flex items-center gap-2 text-white hover:text-white/90 font-semibold transition-all px-4 py-2 rounded-lg hover:bg-white/10"
            >
              <ArrowLeft size={20} />
              <span className="hidden sm:inline">ফিরে যান</span>
            </button>
            
            <div className="flex items-center gap-2 text-white">
              <Sparkles size={20} className="animate-pulse" />
              <span className="font-semibold">উন্নত সার্চ</span>
            </div>
          </div>
          
          <div className="flex items-center gap-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400" size={22} />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="গাড়ির নাম, ব্র্যান্ড বা মডেল খুঁজুন..."
                  className="w-full pl-14 pr-6 py-4 bg-white border-2 border-white/20 rounded-2xl focus:outline-none focus:border-white shadow-xl text-gray-900 placeholder-gray-400 text-lg"
                />
              </div>
            </div>
            
            <button
              onClick={() => setShowFilters(!showFilters)}
              className={`flex items-center gap-2 px-6 py-4 rounded-2xl font-semibold transition-all shadow-lg ${
                showFilters 
                  ? 'bg-white text-teal-700 scale-105' 
                  : 'bg-white/20 text-white hover:bg-white/30 backdrop-blur-sm'
              }`}
            >
              <Filter size={20} />
              <span className="hidden sm:inline">ফিল্টার</span>
              {activeFiltersCount > 0 && (
                <span className="bg-teal-700 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold animate-pulse">
                  {activeFiltersCount}
                </span>
              )}
            </button>
          </div>
        </div>
      </div>
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Breadcrumbs */}
        {(initialCategory || initialBrand) && (
          <div className="mb-6 flex items-center gap-3 px-4 py-3 bg-white rounded-xl shadow-sm border-l-4 border-teal-700">
            <button 
              onClick={onBack}
              className="text-gray-500 hover:text-teal-700 transition-colors font-medium"
            >
              হোম
            </button>
            <span className="text-gray-400">/</span>
            {initialCategory && categories.length > 0 && (
              <span className="text-gray-900 font-bold flex items-center gap-2">
                <TrendingUp size={16} className="text-teal-700" />
                {categories.find(c => c.id == initialCategory)?.name || 'ক্যাটাগরি'}
              </span>
            )}
            {initialBrand && (
              <span className="text-gray-900 font-bold flex items-center gap-2">
                <Car size={16} className="text-gray-700" />
                {initialBrand}
              </span>
            )}
          </div>
        )}
        
        <div className="flex gap-8">
          {/* Filters Sidebar */}
          {showFilters && (
            <div className="w-80 flex-shrink-0">
              <div className="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 sticky top-32">
                <div className="flex items-center justify-between mb-6 pb-4 border-b-2 border-teal-200">
                  <div className="flex items-center gap-2">
                    <div className="w-10 h-10 bg-teal-700 rounded-xl flex items-center justify-center">
                      <Filter size={20} className="text-white" />
                    </div>
                    <h3 className="text-xl font-bold text-gray-900">ফিল্টার করুন</h3>
                  </div>
                  {activeFiltersCount > 0 && (
                    <button
                      onClick={clearFilters}
                      className="text-sm text-white bg-rose-500 hover:bg-rose-600 font-semibold flex items-center gap-1 px-3 py-2 rounded-lg transition-all shadow-md hover:shadow-lg"
                    >
                      <X size={16} />
                      মুছুন
                    </button>
                  )}
                </div>
                
                <div className="space-y-5">
                  {/* Category */}
                  <div className="group">
                    <label className="block text-sm font-bold text-gray-700 mb-3">
                      <div className="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <div className="w-7 h-7 bg-blue-500 rounded-lg flex items-center justify-center">
                          <Car size={14} className="text-white" />
                        </div>
                        <span>ক্যাটাগরি</span>
                      </div>
                    </label>
                    <select
                      value={filters.category_id}
                      onChange={(e) => handleFilterChange('category_id', e.target.value)}
                      className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                    >
                      <option value="">সব ক্যাটাগরি</option>
                      {categories.map(cat => (
                        <option key={cat.id} value={cat.id}>{cat.name}</option>
                      ))}
                    </select>
                  </div>
                  
                  {/* Brand/Make */}
                  <div className="group">
                    <label className="block text-sm font-bold text-gray-700 mb-3">
                      <div className="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                        <div className="w-7 h-7 bg-purple-500 rounded-lg flex items-center justify-center">
                          <Car size={14} className="text-white" />
                        </div>
                        <span>ব্র্যান্ড</span>
                      </div>
                    </label>
                    <select
                      value={filters.make}
                      onChange={(e) => handleFilterChange('make', e.target.value)}
                      className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                    >
                      <option value="">সব ব্র্যান্ড</option>
                      {brands.map((brand, index) => (
                        <option key={index} value={brand}>{brand}</option>
                      ))}
                    </select>
                  </div>
                  
                  {/* Location */}
                  <div className="group">
                    <label className="block text-sm font-bold text-gray-700 mb-3">
                      <div className="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                        <div className="w-7 h-7 bg-green-500 rounded-lg flex items-center justify-center">
                          <MapPin size={14} className="text-white" />
                        </div>
                        <span>এলাকা</span>
                      </div>
                    </label>
                    <select
                      value={filters.location}
                      onChange={(e) => handleFilterChange('location', e.target.value)}
                      className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                    >
                      <option value="">সব এলাকা</option>
                      {locations.map((loc, index) => (
                        <option key={index} value={loc}>{loc}</option>
                      ))}
                    </select>
                  </div>
                  
                  {/* Price Range */}
                  <div className="group">
                    <label className="block text-sm font-bold text-gray-700 mb-3">
                      <div className="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg">
                        <div className="w-7 h-7 bg-teal-500 rounded-lg flex items-center justify-center">
                          <DollarSign size={14} className="text-white" />
                        </div>
                        <span>দাম (লক্ষ টাকা)</span>
                      </div>
                    </label>
                    <div className="flex items-center gap-3">
                      <input
                        type="number"
                        value={filters.price_min}
                        onChange={(e) => handleFilterChange('price_min', e.target.value)}
                        placeholder="ন্যূনতম"
                        className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                      />
                      <span className="text-gray-500 font-bold">-</span>
                      <input
                        type="number"
                        value={filters.price_max}
                        onChange={(e) => handleFilterChange('price_max', e.target.value)}
                        placeholder="সর্বোচ্চ"
                        className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                      />
                    </div>
                  </div>
                  
                  {/* Year */}
                  <div className="group">
                    <label className="block text-sm font-bold text-gray-700 mb-3">
                      <div className="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg">
                        <div className="w-7 h-7 bg-orange-500 rounded-lg flex items-center justify-center">
                          <Calendar size={14} className="text-white" />
                        </div>
                        <span>মডেল বছর</span>
                      </div>
                    </label>
                    <div className="flex items-center gap-3">
                      <input
                        type="number"
                        value={filters.year_min}
                        onChange={(e) => handleFilterChange('year_min', e.target.value)}
                        placeholder="২০১৫"
                        className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                      />
                      <span className="text-gray-500 font-bold">-</span>
                      <input
                        type="number"
                        value={filters.year_max}
                        onChange={(e) => handleFilterChange('year_max', e.target.value)}
                        placeholder="২০২৬"
                        className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                      />
                    </div>
                  </div>
                  
                  {/* Fuel Type */}
                  <div className="group">
                    <label className="block text-sm font-bold text-gray-700 mb-3">
                      <div className="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-red-50 to-red-100 rounded-lg">
                        <div className="w-7 h-7 bg-red-500 rounded-lg flex items-center justify-center">
                          <Fuel size={14} className="text-white" />
                        </div>
                        <span>জ্বালানি</span>
                      </div>
                    </label>
                    <select
                      value={filters.fuel_type}
                      onChange={(e) => handleFilterChange('fuel_type', e.target.value)}
                      className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                    >
                      <option value="">সব ধরনের</option>
                      {fuelTypes.map((fuel, index) => (
                        <option key={index} value={fuel}>{fuel}</option>
                      ))}
                    </select>
                  </div>
                  
                  {/* Condition */}
                  <div className="group">
                    <label className="block text-sm font-bold text-gray-700 mb-3">
                      <div className="flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-lg">
                        <div className="w-7 h-7 bg-indigo-500 rounded-lg flex items-center justify-center">
                          <Tag size={14} className="text-white" />
                        </div>
                        <span>কন্ডিশন</span>
                      </div>
                    </label>
                    <select
                      value={filters.condition}
                      onChange={(e) => handleFilterChange('condition', e.target.value)}
                      className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all shadow-sm hover:border-teal-300"
                    >
                      <option value="">সব কন্ডিশন</option>
                      {conditionTypes.map((condition, index) => (
                        <option key={index} value={condition}>{condition}</option>
                      ))}
                    </select>
                  </div>
                </div>
              </div>
            </div>
          )}
          
          {/* Results */}
          <div className="flex-1">
            {/* Results Header */}
            <div className="bg-gradient-to-r from-white via-gray-50 to-white rounded-2xl p-6 shadow-lg border border-gray-100 mb-6">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    {loading ? (
                      <>
                        <div className="animate-spin w-6 h-6 border-3 border-primary-500 border-t-transparent rounded-full"></div>
                        <span>খুঁজছি...</span>
                      </>
                    ) : (
                      <>
                        <span className="text-teal-700">
                          {listings.length} টি
                        </span>
                        <span>গাড়ি পাওয়া গেছে</span>
                      </>
                    )}
                  </h2>
                  {initialCategory && categories.length > 0 && (
                    <div className="flex items-center gap-2 mt-3">
                      <span className="px-4 py-2 bg-teal-700 text-white rounded-full text-sm font-bold shadow-md flex items-center gap-2">
                        <TrendingUp size={16} />
                        {categories.find(c => c.id == initialCategory)?.name}
                      </span>
                    </div>
                  )}
                  {initialBrand && (
                    <div className="flex items-center gap-2 mt-3">
                      <span className="px-4 py-2 bg-rose-500 text-white rounded-full text-sm font-bold shadow-md flex items-center gap-2">
                        <Car size={16} />
                        {initialBrand}
                      </span>
                    </div>
                  )}
                  {activeFiltersCount > 0 && (
                    <p className="text-sm text-gray-600 mt-2 flex items-center gap-2">
                      <Filter size={14} />
                      <span className="font-semibold">{activeFiltersCount} টি ফিল্টার সক্রিয়</span>
                    </p>
                  )}
                </div>
                
                <div className="flex items-center gap-3">
                  {/* Sort */}
                  <select
                    value={sortBy}
                    onChange={(e) => setSortBy(e.target.value)}
                    className="px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 font-semibold transition-all shadow-sm hover:border-teal-300"
                  >
                    <option value="newest">সর্বশেষ</option>
                    <option value="price_low">দাম (কম - বেশি)</option>
                    <option value="price_high">দাম (বেশি - কম)</option>
                  </select>
                  
                  {/* View Mode Toggle */}
                  <div className="flex bg-gray-100 rounded-xl p-1.5 shadow-inner">
                    <button
                      onClick={() => setViewMode('grid')}
                      className={`p-3 rounded-lg transition-all ${viewMode === 'grid' ? 'bg-teal-700 text-white shadow-md scale-105' : 'hover:bg-gray-200'}`}
                    >
                      <Grid size={20} className={viewMode === 'grid' ? 'text-white' : 'text-gray-500'} />
                    </button>
                    <button
                      onClick={() => setViewMode('list')}
                      className={`p-3 rounded-lg transition-all ${viewMode === 'list' ? 'bg-teal-700 text-white shadow-md scale-105' : 'hover:bg-gray-200'}`}
                    >
                      <List size={20} className={viewMode === 'list' ? 'text-white' : 'text-gray-500'} />
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            {/* Active Filters Tags */}
            {activeFiltersCount > 0 && (
              <div className="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-6">
                <div className="flex items-center gap-2 mb-3">
                  <Sparkles size={18} className="text-teal-700" />
                  <h3 className="font-bold text-gray-700">সক্রিয় ফিল্টার</h3>
                </div>
                <div className="flex flex-wrap gap-2">
                  {searchQuery && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      🔍 খোঁজ: {searchQuery}
                      <button onClick={() => setSearchQuery('')} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                  {filters.category_id && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      🏷️ {categories.find(c => c.id == filters.category_id)?.name}
                      <button onClick={() => handleFilterChange('category_id', '')} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                  {filters.make && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      🚗 {filters.make}
                      <button onClick={() => handleFilterChange('make', '')} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                  {filters.location && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      📍 {filters.location}
                      <button onClick={() => handleFilterChange('location', '')} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                  {(filters.price_min || filters.price_max) && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      💰 দাম: {filters.price_min || '০'} - {filters.price_max || '∞'}
                      <button onClick={() => {
                        handleFilterChange('price_min', '')
                        handleFilterChange('price_max', '')
                      }} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                  {(filters.year_min || filters.year_max) && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      📅 বছর: {filters.year_min || '০'} - {filters.year_max || '∞'}
                      <button onClick={() => {
                        handleFilterChange('year_min', '')
                        handleFilterChange('year_max', '')
                      }} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                  {filters.fuel_type && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      ⛽ {filters.fuel_type}
                      <button onClick={() => handleFilterChange('fuel_type', '')} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                  {filters.condition && (
                    <span className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                      ⭐ {filters.condition}
                      <button onClick={() => handleFilterChange('condition', '')} className="hover:bg-white/50 rounded-full p-0.5 transition-colors">
                        <X size={14} />
                      </button>
                    </span>
                  )}
                </div>
              </div>
            )}
            
            {/* Results Grid/List */}
            {loading ? (
              <div className="bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl p-16 shadow-lg border border-gray-100 text-center">
                <div className="relative w-28 h-28 mx-auto mb-6">
                  <div className="absolute inset-0 bg-teal-400 rounded-full animate-ping opacity-20"></div>
                  <div className="relative w-28 h-28 bg-teal-600 rounded-full flex items-center justify-center shadow-xl animate-pulse">
                    <Search size={56} className="text-white" />
                  </div>
                </div>
                <h3 className="text-2xl font-bold text-gray-900 mb-3">খুঁজছি...</h3>
                <p className="text-gray-600 text-lg">অনুগ্রহ করে অপেক্ষা করুন</p>
                <div className="flex justify-center gap-2 mt-6">
                  <div className="w-3 h-3 bg-teal-700 rounded-full animate-bounce" style={{animationDelay: '0s'}}></div>
                  <div className="w-3 h-3 bg-teal-500 rounded-full animate-bounce" style={{animationDelay: '0.1s'}}></div>
                  <div className="w-3 h-3 bg-teal-700 rounded-full animate-bounce" style={{animationDelay: '0.2s'}}></div>
                </div>
              </div>
            ) : listings.length > 0 ? (
              <div>
                <div className={viewMode === 'grid' 
                  ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' 
                  : 'space-y-4'
                }>
                  {listings.map(listing => (
                    <VehicleCard
                      key={listing.id}
                      listing={listing}
                      variant={viewMode}
                      onClick={() => onViewListing(listing)}
                    />
                  ))}
                </div>
                
                {/* Load More Button */}
                {pagination.current_page < pagination.last_page && (
                  <div className="mt-8 text-center">
                    <Button
                      variant="secondary"
                      size="lg"
                      onClick={() => window.loadMoreListings && window.loadMoreListings()}
                      loading={loadingMore}
                    >
                      {loadingMore ? 'লোড হচ্ছে...' : `আরও দেখুন (${pagination.total - listings.length} টি বাকি)`}
                    </Button>
                    <p className="text-sm text-gray-500 mt-2">
                      {listings.length} টির মধ্যে {pagination.total} টি দেখছেন
                    </p>
                  </div>
                )}
              </div>
            ) : (
              <div className="bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl p-16 shadow-lg border border-gray-100 text-center">
                <div className="relative w-28 h-28 mx-auto mb-6">
                  <div className="absolute inset-0 bg-gray-200 rounded-full"></div>
                  <div className="relative w-28 h-28 bg-gray-100 rounded-full flex items-center justify-center shadow-inner">
                    <Search size={56} className="text-gray-400" />
                  </div>
                </div>
                <h3 className="text-2xl font-bold text-gray-900 mb-3">কোনো গাড়ি পাওয়া যায়নি 😔</h3>
                <p className="text-gray-600 mb-8 text-lg max-w-md mx-auto">
                  আপনার সার্চ বা ফিল্টার অনুযায়ী কোনো গাড়ি খুঁজে পাওয়া যায়নি।<br />
                  অনুগ্রহ করে অন্য কিছু চেষ্টা করুন।
                </p>
                <Button variant="primary" onClick={clearFilters}>
                  সব ফিল্টার মুছুন
                </Button>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  )
}
