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
  const [showFilters, setShowFilters] = useState(typeof window !== 'undefined' ? window.innerWidth >= 1024 : false)
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
  const [quickFilters, setQuickFilters] = useState([
    { label: 'নতুন গাড়ি', value: 'new', active: false },
    { label: 'পুরাতন', value: 'used', active: false },
    { label: '৫ লক্ষের মধ্যে', value: 'under_5', active: false },
    { label: 'হাইব্রিড', value: 'hybrid', active: false },
    { label: '২০২০ এর পরে', value: 'recent', active: false }
  ])
  
  // Update filters when initialCategory or initialBrand changes
  useEffect(() => {
    setFilters(prev => ({
      ...prev,
      category_id: initialCategory || '',
      make: initialBrand || ''
    }))
  }, [initialCategory, initialBrand])
  
  // Handle quick filter toggle
  const toggleQuickFilter = (value) => {
    setQuickFilters(prev => prev.map(f => 
      f.value === value ? { ...f, active: !f.active } : f
    ))
    
    // Apply filter logic
    if (value === 'new') {
      handleFilterChange('condition', filters.condition === 'নতুন' ? '' : 'নতুন')
    } else if (value === 'used') {
      handleFilterChange('condition', filters.condition === 'পুরাতন' ? '' : 'পুরাতন')
    } else if (value === 'under_5') {
      setFilters(prev => ({ ...prev, price_max: filters.price_max === '500000' ? '' : '500000' }))
    } else if (value === 'hybrid') {
      handleFilterChange('fuel_type', filters.fuel_type === 'হাইব্রিড' ? '' : 'হাইব্রিড')
    } else if (value === 'recent') {
      handleFilterChange('year_min', filters.year_min === '2020' ? '' : '2020')
    }
  }
  
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
    <div className="min-h-screen bg-gray-50">
      {/* Clean Header */}
      <div className="bg-teal-700 shadow-lg sticky top-0 z-20">
        <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-4">
          <div className="flex items-center gap-2 sm:gap-3 mb-3">
            <button 
              onClick={onBack}
              className="flex items-center gap-1 text-white font-medium transition-all p-2 rounded-lg hover:bg-white/10"
            >
              <ArrowLeft size={18} />
              <span className="text-sm">হোম</span>
            </button>
            
            <div className="flex-1 relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="গাড়ি খুঁজুন..."
                className="w-full pl-10 pr-3 py-2.5 bg-white rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 text-sm"
              />
            </div>
            
            <button
              onClick={() => setShowFilters(!showFilters)}
              className="flex items-center gap-1.5 px-3 py-2.5 bg-white text-teal-700 rounded-lg font-semibold text-sm shadow-md"
            >
              <Filter size={16} />
              {activeFiltersCount > 0 && (
                <span className="bg-rose-500 text-white min-w-[20px] h-5 rounded-full flex items-center justify-center text-xs font-bold px-1.5">
                  {activeFiltersCount}
                </span>
              )}
            </button>
          </div>
          
          {/* Simple Quick Filters */}
          <div className="flex gap-1.5 overflow-x-auto pb-1 scrollbar-hide">
            {quickFilters.map(filter => (
              <button
                key={filter.value}
                onClick={() => toggleQuickFilter(filter.value)}
                className={`px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors ${
                  filter.active
                    ? 'bg-white text-teal-700'
                    : 'bg-white/20 text-white'
                }`}
              >
                {filter.label}
              </button>
            ))}
          </div>
        </div>
      </div>
      
      <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 sm:py-6">
        {/* Simple Results Count */}
        <div className="mb-3 sm:mb-4">
          <div className="flex items-center justify-between bg-white px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg shadow-sm">
            <div>
              <p className="text-xs text-gray-500 mb-0.5">সার্চ ফলাফল</p>
              <p className="text-sm sm:text-base font-bold text-gray-900">
                {loading ? 'খুঁজছি...' : `${pagination.total || 0} টি গাড়ি পাওয়া গেছে`}
              </p>
            </div>
            <div className="flex items-center gap-2">
              {activeFiltersCount > 0 && (
                <button
                  onClick={clearFilters}
                  className="text-xs text-rose-600 hover:text-rose-700 font-semibold flex items-center gap-1 px-2 py-1 rounded hover:bg-rose-50 transition-colors"
                >
                  <X size={14} />
                  <span className="hidden sm:inline">মুছুন</span>
                </button>
              )}
            </div>
          </div>
        </div>
        
        <div className="flex flex-col lg:flex-row gap-4 sm:gap-8">
          {/* Mobile Filter Button */}
          {!showFilters && (
            <button
              onClick={() => setShowFilters(true)}
              className="lg:hidden fixed bottom-5 right-5 bg-teal-700 text-white p-3.5 rounded-full shadow-xl z-30 flex items-center gap-1.5 hover:bg-teal-800 transition-all"
            >
              <Filter size={20} />
              {activeFiltersCount > 0 && (
                <span className="bg-rose-500 text-white min-w-[20px] h-5 rounded-full flex items-center justify-center text-xs font-bold px-1.5">
                  {activeFiltersCount}
                </span>
              )}
            </button>
          )})
          
          {/* Filters Sidebar */}
          {showFilters && (
            <div className={`
              ${showFilters ? 'fixed lg:static' : 'hidden'} 
              lg:w-80 lg:flex-shrink-0 
              inset-0 lg:inset-auto 
              bg-black/50 lg:bg-transparent 
              z-40 lg:z-auto 
              overflow-y-auto lg:overflow-visible
              ${showFilters ? 'animate-fadeIn' : ''}
            `}>
              <div className="lg:sticky lg:top-32 h-full lg:h-auto">
                {/* Mobile Close Overlay */}
                <div 
                  className="lg:hidden absolute inset-0" 
                  onClick={() => setShowFilters(false)}
                ></div>
                
                {/* Simple Filter Panel */}
                <div className="relative bg-white rounded-none lg:rounded-xl p-4 sm:p-5 shadow-none lg:shadow-lg ml-auto w-[85%] sm:w-[75%] lg:w-full h-full lg:h-auto max-w-xs lg:max-w-none">
                  <div className="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                    <h3 className="text-base sm:text-lg font-bold text-gray-900">ফিল্টার</h3>
                    <button
                      onClick={() => setShowFilters(false)}
                      className="lg:hidden p-1.5 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                      <X size={20} />
                    </button>
                  </div>
                  
                  <div className="space-y-3.5">
                    {/* Category */}
                    <div>
                      <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                        ক্যাটাগরি
                      </label>
                      <select
                        value={filters.category_id}
                        onChange={(e) => handleFilterChange('category_id', e.target.value)}
                        className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                      >
                        <option value="">সব ক্যাটাগরি</option>
                        {categories.map(cat => (
                          <option key={cat.id} value={cat.id}>{cat.name}</option>
                        ))}
                      </select>
                    </div>
                    
                    {/* Brand */}
                    <div>
                      <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                        ব্র্যান্ড
                      </label>
                      <select
                        value={filters.make}
                        onChange={(e) => handleFilterChange('make', e.target.value)}
                        className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                      >
                        <option value="">সব ব্র্যান্ড</option>
                        {brands.map((brand, index) => (
                          <option key={index} value={brand}>{brand}</option>
                        ))}
                      </select>
                    </div>
                    
                    {/* Location */}
                    <div>
                      <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                        এলাকা
                      </label>
                      <select
                        value={filters.location}
                        onChange={(e) => handleFilterChange('location', e.target.value)}
                        className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                      >
                        <option value="">সব এলাকা</option>
                        {locations.map((loc, index) => (
                          <option key={index} value={loc}>{loc}</option>
                        ))}
                      </select>
                    </div>
                    
                    {/* Price */}
                    <div>
                      <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                        দাম (টাকা)
                      </label>
                      <div className="flex items-center gap-2">
                        <input
                          type="number"
                          value={filters.price_min}
                          onChange={(e) => handleFilterChange('price_min', e.target.value)}
                          placeholder="ন্যূনতম"
                          className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                        />
                        <span className="text-gray-400">-</span>
                        <input
                          type="number"
                          value={filters.price_max}
                          onChange={(e) => handleFilterChange('price_max', e.target.value)}
                          placeholder="সর্বোচ্চ"
                          className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                        />
                      </div>
                    </div>
                    
                    {/* Year */}
                    <div>
                      <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                        মডেল বছর
                      </label>
                      <div className="flex items-center gap-2">
                        <input
                          type="number"
                          value={filters.year_min}
                          onChange={(e) => handleFilterChange('year_min', e.target.value)}
                          placeholder="২০১৫"
                          className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                        />
                        <span className="text-gray-400">-</span>
                        <input
                          type="number"
                          value={filters.year_max}
                          onChange={(e) => handleFilterChange('year_max', e.target.value)}
                          placeholder="২০২৪"
                          className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                        />
                      </div>
                    </div>
                    
                    {/* Fuel */}
                    <div>
                      <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                        ফুয়েল
                      </label>
                      <select
                        value={filters.fuel_type}
                        onChange={(e) => handleFilterChange('fuel_type', e.target.value)}
                        className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                      >
                        <option value="">সব ফুয়েল</option>
                        {fuelTypes.map((type, index) => (
                          <option key={index} value={type}>{type}</option>
                        ))}
                      </select>
                    </div>
                    
                    {/* Condition */}
                    <div>
                      <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                        কন্ডিশন
                      </label>
                      <select
                        value={filters.condition}
                        onChange={(e) => handleFilterChange('condition', e.target.value)}
                        className="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-sm"
                      >
                        <option value="">সব কন্ডিশন</option>
                        {conditionTypes.map((type, index) => (
                          <option key={index} value={type}>{type}</option>
                        ))}
                      </select>
                    </div>
                    
                    {/* Apply Button - Mobile */}
                    <button
                      onClick={() => setShowFilters(false)}
                      className="lg:hidden w-full bg-teal-700 hover:bg-teal-800 text-white font-semibold py-3 rounded-lg transition-colors"
                    >
                      প্রয়োগ করুন ({listings.length} টি)
                    </button>
                  </div>
                </div>
              </div>
            </div>
          )}
          
          {/* Results */}
          <div className="flex-1">
            
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
