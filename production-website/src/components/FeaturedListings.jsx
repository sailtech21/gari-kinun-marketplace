import { MapPin, Heart, Calendar, Fuel, Car, ArrowRight, Eye, BadgeCheck } from 'lucide-react'
import { useState, useEffect } from 'react'
import { apiCall, getImageUrl } from '../config'
import FavoriteButton from './common/FavoriteButton'

export default function FeaturedListings({ onViewListing, onAuthRequired }) {
  const [listings, setListings] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Fetch featured listings from API
    const fetchFeaturedListings = async () => {
      try {
        const response = await apiCall('/listings/featured?per_page=8')
        if (response.success && response.data) {
          // Transform API data to match component format
          const dataArray = Array.isArray(response.data) ? response.data : (response.data.items || [])
          const transformedListings = dataArray.map(item => ({
            id: item.id,
            title: item.title,
            price: `১${item.price.toLocaleString('bn-BD')}`,
            location: item.location || 'ঢাকা',
            year: item.model_year || item.year || '2020',
            fuel: item.fuel_type || 'পেট্রোল',
            type: item.category?.name || 'সেডান',
            tag: item.condition === 'new' ? 'নতুন গাড়ি' : 'রেজিস্টার্ড',
            image: getImageUrl(item.image || item.images?.[0]),
            views: item.views || 0,
            seller: {
              name: item.user?.name || item.dealer?.name || 'বিক্রেতা',
              verified: item.user?.is_verified || item.dealer?.is_verified || false
            }
          }))
          setListings(transformedListings)
        }
      } catch (error) {
        console.error('Failed to fetch featured listings:', error)
        // Keep empty array on error
        setListings([])
      } finally {
        setLoading(false)
      }
    }

    fetchFeaturedListings()
  }, [])

  if (loading) {
    return (
      <section className="py-20 bg-orange-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center py-12">
            <div className="w-16 h-16 border-4 border-teal-700 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-gray-500 font-semibold">বিজ্ঞাপন লোড হচ্ছে...</p>
          </div>
        </div>
      </section>
    )
  }

  if (listings.length === 0) {
    return null // Don't show section if no listings
  }

  return (
    <section className="py-20 bg-orange-50 relative">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Section Header */}
        <div className="flex items-center justify-between mb-12">
          <div>
            <div className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-full mb-4">
              <span className="text-sm font-bold text-gray-700">✨ বৈশিষ্ট্যযুক্ত</span>
            </div>
            <h2 className="text-5xl font-black text-gray-900 mb-3">
              <span className="text-teal-700">জনপ্রিয়</span> বিজ্ঞাপন
            </h2>
            <p className="text-xl text-gray-600">সেরা দামে সেরা গাড়িগুলো দেখুন</p>
          </div>
          <button className="hidden md:flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-bold px-6 py-3 rounded-xl hover:shadow-lg hover:scale-105 transition-all">
            <span>সব বিজ্ঞাপন দেখুন</span>
            <ArrowRight size={20} />
          </button>
        </div>

        {/* Listings Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {listings.map((listing) => (
            <div key={listing.id} className="card overflow-hidden group cursor-pointer" onClick={() => onViewListing(listing)}>
              {/* Image */}
              <div className="relative h-48 overflow-hidden">
                <img
                  src={listing.image}
                  alt={listing.title}
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                />
                <span className="absolute top-3 left-3 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                  {listing.tag}
                </span>
                <div className="absolute top-3 right-3">
                  <FavoriteButton 
                    listingId={listing.id} 
                    onAuthRequired={onAuthRequired}
                  />
                </div>
              </div>

              {/* Content */}
              <div className="p-4 space-y-3">
                <h3 className="text-lg font-bold text-gray-900 line-clamp-1">{listing.title}</h3>
                
                <p className="text-2xl font-bold text-gray-900">৳ {listing.price}</p>

                <div className="flex items-center gap-2 text-gray-600 text-sm">
                  <MapPin size={16} />
                  <span className="line-clamp-1">{listing.location}</span>
                </div>

                <div className="flex items-center gap-3 text-gray-600 text-xs">
                  <div className="flex items-center gap-1">
                    <Calendar size={14} />
                    <span>{listing.year}</span>
                  </div>
                  <div className="flex items-center gap-1">
                    <Fuel size={14} />
                    <span>{listing.fuel}</span>
                  </div>
                  <div className="flex items-center gap-1">
                    <Car size={14} />
                    <span>{listing.type}</span>
                  </div>
                </div>

                {/* Seller & Views Info */}
                <div className="flex items-center justify-between pt-2 border-t border-gray-100">
                  <div className="flex items-center gap-1 text-xs text-gray-500">
                    {listing.seller.verified && (
                      <BadgeCheck size={14} className="text-blue-500" />
                    )}
                    <span className="line-clamp-1">{listing.seller.name}</span>
                  </div>
                  <div className="flex items-center gap-1 text-xs text-gray-500">
                    <Eye size={14} />
                    <span>{listing.views}</span>
                  </div>
                </div>

                <button className="w-full mt-2 bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold py-2.5 rounded-lg transition-colors">
                  বিস্তারিত দেখুন
                </button>
              </div>
            </div>
          ))}
        </div>

        {/* Mobile View All Button */}
        <div className="md:hidden mt-8 text-center">
          <button className="flex items-center gap-2 text-gray-700 font-semibold mx-auto hover:gap-3 transition-all hover:text-teal-700">
            <span>সব বিজ্ঞাপন দেখুন</span>
            <ArrowRight size={20} />
          </button>
        </div>
      </div>
    </section>
  )
}
