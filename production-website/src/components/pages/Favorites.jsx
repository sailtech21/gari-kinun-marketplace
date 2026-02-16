import { useState, useEffect } from 'react'
import { ArrowLeft, Heart, MapPin, Calendar, Fuel, Trash2, Package } from 'lucide-react'
import { apiCall, getImageUrl } from '../../config'

export default function Favorites({ onBack, onViewListing }) {
  const [favorites, setFavorites] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetchFavorites()
  }, [])

  const fetchFavorites = async () => {
    // Check if user is authenticated
    const token = localStorage.getItem('auth_token')
    if (!token) {
      setLoading(false)
      return
    }

    setLoading(true)
    try {
      const response = await apiCall('/favorites')
      if (response.success) {
        setFavorites(response.data || [])
      }
    } catch (error) {
      if (error.message !== 'Unauthenticated') {
        console.error('Error fetching favorites:', error)
      }
    } finally {
      setLoading(false)
    }
  }

  const handleRemoveFavorite = async (listingId) => {
    try {
      const response = await apiCall(`/listings/${listingId}/unfavorite`, {
        method: 'DELETE'
      })

      if (response.success) {
        setFavorites(favorites.filter(fav => fav.listing_id !== listingId))
      }
    } catch (error) {
      console.error('Error removing favorite:', error)
    }
  }

  return (
    <div className="min-h-screen bg-orange-50">
      {/* Header */}
      <div className="bg-teal-700 text-white py-8">
        <div className="max-w-7xl mx-auto px-4">
          <button
            onClick={onBack}
            className="flex items-center gap-2 text-white hover:text-gray-200 mb-4"
          >
            <ArrowLeft size={20} />
            <span>ফিরে যান</span>
          </button>
          <div className="flex items-center gap-3">
            <Heart size={32} fill="white" />
            <div>
              <h1 className="text-3xl font-black">প্রিয় তালিকা</h1>
              <p className="text-teal-100 mt-1">আপনার সংরক্ষিত বিজ্ঞাপনসমূহ</p>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-8">
        {loading ? (
          <div className="text-center py-12">
            <div className="w-16 h-16 border-4 border-teal-700 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-gray-500">লোড হচ্ছে...</p>
          </div>
        ) : favorites.length === 0 ? (
          <div className="bg-white rounded-xl shadow-sm p-12 text-center">
            <Heart size={64} className="text-gray-300 mx-auto mb-4" />
            <h3 className="text-xl font-bold text-gray-900 mb-2">কোন প্রিয় বিজ্ঞাপন নেই</h3>
            <p className="text-gray-600 mb-6">আপনি এখনো কোন বিজ্ঞাপন সংরক্ষণ করেননি</p>
            <button
              onClick={onBack}
              className="inline-flex items-center gap-2 bg-rose-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-rose-600 transition-colors"
            >
              বিজ্ঞাপন খুঁজুন
            </button>
          </div>
        ) : (
          <>
            <div className="mb-6">
              <p className="text-gray-600">
                মোট <span className="font-bold text-teal-700">{favorites.length}</span> টি প্রিয় বিজ্ঞাপন
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {favorites.map((favorite) => {
                const listing = favorite.listing
                return (
                  <div key={favorite.id} className="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                    {/* Image */}
                    <div className="relative h-48 overflow-hidden cursor-pointer" onClick={() => onViewListing(listing)}>
                      <img
                        src={getImageUrl(listing.image || listing.images?.[0])}
                        alt={listing.title}
                        className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                      />
                      <button
                        onClick={(e) => {
                          e.stopPropagation()
                          handleRemoveFavorite(listing.id)
                        }}
                        className="absolute top-3 right-3 w-10 h-10 bg-white rounded-full flex items-center justify-center hover:bg-red-50 transition-colors shadow-lg"
                      >
                        <Heart size={20} className="text-red-500" fill="currentColor" />
                      </button>
                    </div>

                    {/* Details */}
                    <div className="p-5">
                      <h3 
                        className="text-lg font-bold text-gray-900 mb-2 cursor-pointer hover:text-teal-700 transition-colors line-clamp-2"
                        onClick={() => onViewListing(listing)}
                      >
                        {listing.title}
                      </h3>

                      <div className="flex items-center justify-between mb-3">
                        <span className="text-2xl font-black text-rose-500">
                          ৳{parseInt(listing.price).toLocaleString('bn-BD')}
                        </span>
                      </div>

                      <div className="space-y-2 text-sm text-gray-600 mb-4">
                        <div className="flex items-center gap-2">
                          <MapPin size={16} />
                          <span>{listing.location}</span>
                        </div>
                        <div className="flex items-center gap-4">
                          <div className="flex items-center gap-1">
                            <Calendar size={16} />
                            <span>{listing.year || listing.model_year}</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <Fuel size={16} />
                            <span>{listing.fuel_type}</span>
                          </div>
                        </div>
                      </div>

                      <div className="flex gap-2">
                        <button
                          onClick={() => onViewListing(listing)}
                          className="flex-1 bg-rose-500 hover:bg-rose-600 text-white py-2 rounded-lg font-semibold transition-colors"
                        >
                          বিস্তারিত দেখুন
                        </button>
                        <button
                          onClick={() => handleRemoveFavorite(listing.id)}
                          className="px-4 bg-gray-100 hover:bg-rose-50 text-gray-700 hover:text-rose-600 rounded-lg transition-colors"
                          title="প্রিয় তালিকা থেকে সরান"
                        >
                          <Trash2 size={18} />
                        </button>
                      </div>
                    </div>

                    {/* Saved Date */}
                    <div className="px-5 pb-4">
                      <p className="text-xs text-gray-500">
                        সংরক্ষণ করা হয়েছে: {new Date(favorite.created_at).toLocaleDateString('bn-BD')}
                      </p>
                    </div>
                  </div>
                )
              })}
            </div>
          </>
        )}
      </div>
    </div>
  )
}
