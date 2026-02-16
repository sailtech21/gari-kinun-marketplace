import { Search, MapPin, SlidersHorizontal } from 'lucide-react'
import { useState } from 'react'

export default function SearchBar({ 
  onSearch, 
  showFilters = true, 
  locations = [],
  placeholder = 'গাড়ি খুঁজুন...',
  variant = 'default' // default | compact | hero
}) {
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedLocation, setSelectedLocation] = useState('')
  
  const handleSearch = (e) => {
    e.preventDefault()
    if (onSearch) {
      onSearch({ query: searchQuery, location: selectedLocation })
    }
  }
  
  if (variant === 'compact') {
    return (
      <form onSubmit={handleSearch} className="relative">
        <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
        <input
          type="text"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          placeholder={placeholder}
          className="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-primary-500 transition-colors"
        />
      </form>
    )
  }
  
  if (variant === 'hero') {
    return (
      <form onSubmit={handleSearch} className="flex flex-col sm:flex-row gap-3">
        <input
          type="text"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          placeholder="যানবাহনের ধরন"
          className="flex-1 px-5 py-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 text-lg"
        />
        <select 
          value={selectedLocation}
          onChange={(e) => setSelectedLocation(e.target.value)}
          className="px-5 py-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 text-lg"
        >
          <option value="">স্থান নির্বাচন করুন</option>
          {locations.map((location) => (
            <option key={location.id} value={location.name}>
              {location.name} ({location.count})
            </option>
          ))}
        </select>
        <button type="submit" className="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-8 py-4 rounded-lg transition-colors flex items-center gap-2 justify-center">
          <Search size={20} />
          <span>খুঁজুন</span>
        </button>
      </form>
    )
  }
  
  // Default variant
  return (
    <form onSubmit={handleSearch} className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
      <div className="flex flex-col md:flex-row gap-4">
        {/* Search Input */}
        <div className="flex-1 relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder={placeholder}
            className="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
          />
        </div>
        
        {/* Location Select */}
        <div className="relative">
          <MapPin className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
          <select
            value={selectedLocation}
            onChange={(e) => setSelectedLocation(e.target.value)}
            className="w-full md:w-48 pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 appearance-none bg-white"
          >
            <option value="">সব স্থান</option>
            {locations.map((location) => (
              <option key={location.id} value={location.name}>
                {location.name}
              </option>
            ))}
          </select>
        </div>
        
        {/* Search Button */}
        <button 
          type="submit"
          className="bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors flex items-center gap-2 justify-center"
        >
          <Search size={20} />
          <span>খুঁজুন</span>
        </button>
        
        {/* Filters (Optional) */}
        {showFilters && (
          <button
            type="button"
            className="border-2 border-gray-300 hover:border-primary-500 text-gray-700 font-semibold px-6 py-3 rounded-lg transition-colors flex items-center gap-2 justify-center"
          >
            <SlidersHorizontal size={20} />
            <span className="hidden md:inline">ফিল্টার</span>
          </button>
        )}
      </div>
    </form>
  )
}
