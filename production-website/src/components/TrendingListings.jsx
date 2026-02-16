import { MapPin, Calendar, Eye, ArrowRight, TrendingUp, Heart } from 'lucide-react'
import { useState, useEffect } from 'react'
import { apiCall, getImageUrl } from '../config'
import FavoriteButton from './common/FavoriteButton'

export default function TrendingListings({ onViewListing, onAuthRequired }) {
  const [trendingListings, setTrendingListings] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Fetch trending listings from API
    const fetchTrendingListings = async () => {
      try {
        const response = await apiCall('/listings/trending?per_page=6')
        if (response.success && response.data) {
          // Transform API data to match component format
          const dataArray = Array.isArray(response.data) ? response.data : (response.data.items || [])
          const transformedListings = dataArray.map(item => ({
            id: item.id,
            title: item.title,
            price: `${item.price.toLocaleString('bn-BD')}`,
            location: item.location || 'ঢাকা',
            year: item.model_year || item.year || '2020',
            fuel: item.fuel_type || 'পেট্রোল',
            type: item.category?.name || 'সেডান',
            transmission: item.transmission || 'অটোমেটিক',
            mileage: item.mileage ? `${item.mileage} কিমি` : '১০,০০০ কিমি',
            image: getImageUrl(item.image || item.images?.[0]),
            views: item.views || 0,
            seller: {
              name: item.user?.name || item.dealer?.name || 'বিক্রেতা',
              verified: item.user?.is_verified || item.dealer?.is_verified || false
            }
          }))
          setTrendingListings(transformedListings)
        }
      } catch (error) {
        console.error('Failed to fetch trending listings:', error)
        setTrendingListings([])
      } finally {
        setLoading(false)
      }
    }

    fetchTrendingListings()
  }, [])

  if (loading) {
    return (
      <section className="py-20 bg-gradient-to-br from-orange-50 via-amber-50 to-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center py-12">
            <div className="w-16 h-16 border-4 border-teal-700 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-gray-500 font-semibold">ট্রেন্ডিং বিজ্ঞাপন লোড হচ্ছে...</p>
          </div>
        </div>
      </section>
    )
  }

  if (trendingListings.length === 0) {
    return null // Don't show section if no listings
  }

  return (
    <section className="py-20 bg-gradient-to-br from-orange-50 via-amber-50 to-white relative overflow-hidden">
      {/* Background Elements */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-10 left-10 w-96 h-96 bg-orange-200 rounded-full blur-3xl opacity-20"></div>
        <div className="absolute bottom-10 right-10 w-96 h-96 bg-pink-200 rounded-full blur-3xl opacity-20"></div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {/* Section Header */}
        <div className="flex items-center justify-between mb-12">
          <div>
            <div className="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 rounded-full mb-4">
              <TrendingUp size={20} className="text-gray-700" />
              <span className="text-sm font-bold text-gray-700">🔥 ট্রেন্ডিং</span>
            </div>
            <h2 className="text-5xl font-black text-gray-900 mb-3">
              <span className="text-teal-700">ট্রেন্ডিং</span> বিজ্ঞাপন
            </h2>
            <p className="text-xl text-gray-600">সবচেয়ে বেশি দেখা হচ্ছে যেগুলো</p>
          </div>
          <button className="hidden md:flex items-center gap-2 bg-rose-500 text-white font-bold px-6 py-3 rounded-xl hover:bg-rose-600 hover:shadow-lg hover:scale-105 transition-all">
            <span>আরও দেখুন</span>
            <ArrowRight size={20} />
          </button>
        </div>

        {/* Listings List */}
        <div className="space-y-4">
          {trendingListings.map((listing, index) => (
            <div 
              key={listing.id} 
              className="bg-white rounded-xl p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group"
              onClick={() => onViewListing(listing)}
            >
              <div className="flex flex-col md:flex-row gap-6">
                {/* Image */}
                <div className="relative w-full md:w-48 h-36 flex-shrink-0 rounded-lg overflow-hidden">
                  <img 
                    src={listing.image} 
                    alt={listing.title}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                  />
                  <span className="absolute top-3 left-3 bg-rose-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                    #{index + 1} ট্রেন্ডিং
                  </span>
                  <div className="absolute top-3 right-3">
                    <FavoriteButton 
                      listingId={listing.id} 
                      onAuthRequired={onAuthRequired}
                    />
                  </div>
                </div>

                {/* Content */}
                <div className="flex-1">
                  <div className="flex items-start justify-between mb-3">
                    <div>
                      <h3 className="text-xl font-bold text-gray-900 mb-2 group-hover:text-teal-700 transition-colors">
                        {listing.title}
                      </h3>
                      <div className="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                        <div className="flex items-center gap-1">
                          <MapPin size={16} />
                          <span>{listing.location}</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <Calendar size={16} />
                          <span>{listing.year}</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <Eye size={16} />
                          <span>{listing.views} বার দেখা হয়েছে</span>
                        </div>
                      </div>
                    </div>
                    <p className="text-2xl font-bold text-gray-900">
                      ৳ {listing.price}
                    </p>
                  </div>

                  {/* Details */}
                  <div className="flex flex-wrap gap-2 mb-4">
                    <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                      {listing.fuel}
                    </span>
                    <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                      {listing.type}
                    </span>
                    <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                      {listing.transmission}
                    </span>
                    <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                      {listing.mileage}
                    </span>
                  </div>

                  {/* Seller Info */}
                  <div className="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div className="text-sm text-gray-600">
                      বিক্রেতা: <span className="font-semibold text-gray-900">{listing.seller.name}</span>
                      {listing.seller.verified && (
                        <span className="ml-2 text-blue-600">✓ যাচাইকৃত</span>
                      )}
                    </div>
                    <button className="text-gray-700 font-semibold hover:gap-2 flex items-center gap-1 transition-all hover:text-teal-700">
                      <span>বিস্তারিত</span>
                      <ArrowRight size={16} />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Mobile View All Button */}
        <div className="md:hidden mt-8 text-center">
          <button className="flex items-center gap-2 text-gray-700 font-semibold mx-auto hover:gap-3 transition-all hover:text-teal-700">
            <span>আরও দেখুন</span>
            <ArrowRight size={20} />
          </button>
        </div>
      </div>
    </section>
  )
}
