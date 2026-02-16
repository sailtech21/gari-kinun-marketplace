import { useState, useEffect } from 'react'
import { ArrowLeft, Phone, Mail, MapPin, CheckCircle, Star, Package, Calendar, Building2, Shield, TrendingUp } from 'lucide-react'
import { apiCall, getImageUrl } from '../../config'
import { useSettings } from '../../contexts/SettingsContext'
import { useTranslation } from '../../utils/translations'

export default function DealerProfilePage({ onBack, onListingClick, dealerId }) {
  const { language } = useSettings()
  const { t } = useTranslation(language)
  const [dealer, setDealer] = useState(null)
  const [listings, setListings] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    if (dealerId) {
      fetchDealerProfile()
    }
  }, [dealerId])

  const fetchDealerProfile = async () => {
    try {
      setLoading(true)
      const response = await apiCall(`/dealers/${dealerId}`)
      
      if (response.success) {
        setDealer(response.data.dealer)
        setListings(response.data.listings || [])
      }
    } catch (err) {
      setError(err.message || 'ডিলার প্রোফাইল লোড করতে সমস্যা হয়েছে')
    } finally {
      setLoading(false)
    }
  }

  const formatPrice = (price) => {
    return new Intl.NumberFormat('bn-BD', {
      style: 'currency',
      currency: 'BDT',
      maximumFractionDigits: 0
    }).format(price)
  }

  const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('bn-BD', { year: 'numeric', month: 'long', day: 'numeric' })
  }

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="inline-block w-12 h-12 border-4 border-teal-600 border-t-transparent rounded-full animate-spin"></div>
          <p className="mt-4 text-gray-600">লোড হচ্ছে...</p>
        </div>
      </div>
    )
  }

  if (error || !dealer) {
    return (
      <div className="min-h-screen bg-gray-50">
        <div className="bg-white shadow-sm sticky top-0 z-10">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <button 
              onClick={onBack}
              className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold"
            >
              <ArrowLeft size={20} />
              <span>ফিরে যান</span>
            </button>
          </div>
        </div>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
          <p className="text-red-600">{error || 'ডিলার খুঁজে পাওয়া যায়নি'}</p>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm sticky top-0 z-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <button 
            onClick={onBack}
            className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold"
          >
            <ArrowLeft size={20} />
            <span>ফিরে যান</span>
          </button>
        </div>
      </div>

      {/* Dealer Profile Header */}
      <div className="bg-gradient-to-r from-teal-600 to-teal-700 py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row items-start md:items-center gap-6">
            {/* Dealer Logo/Avatar */}
            <div className="relative">
              {dealer.user?.avatar ? (
                <img 
                  src={getImageUrl(dealer.user.avatar)} 
                  alt={dealer.business_name}
                  className="w-32 h-32 rounded-2xl object-cover border-4 border-white shadow-lg"
                />
              ) : (
                <div className="w-32 h-32 rounded-2xl bg-white flex items-center justify-center border-4 border-white shadow-lg">
                  <Building2 size={48} className="text-teal-600" />
                </div>
              )}
              {/* Verified Badge */}
              <div className="absolute -top-2 -right-2">
                <div className="bg-green-500 rounded-full p-2 border-4 border-white shadow-lg">
                  <CheckCircle size={24} className="text-white" />
                </div>
              </div>
            </div>

            {/* Dealer Info */}
            <div className="flex-1">
              <div className="flex items-center gap-3 mb-2">
                <h1 className="text-3xl md:text-4xl font-black text-white">{dealer.business_name}</h1>
                <span className="inline-flex items-center gap-1 bg-green-500 text-white text-sm px-3 py-1.5 rounded-full shadow-lg">
                  <Shield size={16} />
                  <span className="font-semibold">ভেরিফাইড ডিলার</span>
                </span>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                {/* Phone */}
                <div className="flex items-center gap-2 text-white/90">
                  <Phone size={18} className="text-teal-200" />
                  <span className="font-medium">{dealer.business_phone}</span>
                </div>

                {/* Email */}
                {dealer.user?.email && (
                  <div className="flex items-center gap-2 text-white/90">
                    <Mail size={18} className="text-teal-200" />
                    <span className="font-medium">{dealer.user.email}</span>
                  </div>
                )}

                {/* Location */}
                <div className="flex items-center gap-2 text-white/90">
                  <MapPin size={18} className="text-teal-200" />
                  <span className="font-medium">{dealer.business_address}</span>
                </div>

                {/* Member Since */}
                <div className="flex items-center gap-2 text-white/90">
                  <Calendar size={18} className="text-teal-200" />
                  <span className="font-medium">সদস্য: {formatDate(dealer.approved_at || dealer.created_at)}</span>
                </div>
              </div>

              {/* Stats */}
              <div className="flex flex-wrap gap-6 mt-6">
                <div className="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30">
                  <Package className="text-white" size={20} />
                  <div>
                    <p className="text-white text-sm">মোট বিজ্ঞাপন</p>
                    <p className="text-white text-2xl font-bold">{listings.length}</p>
                  </div>
                </div>

                <div className="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30">
                  <Star className="text-yellow-300 fill-yellow-300" size={20} />
                  <div>
                    <p className="text-white text-sm">রেটিং</p>
                    <p className="text-white text-2xl font-bold">{dealer.rating || '5.0'}</p>
                  </div>
                </div>

                <div className="flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg border border-white/30">
                  <TrendingUp className="text-white" size={20} />
                  <div>
                    <p className="text-white text-sm">সক্রিয় বিজ্ঞাপন</p>
                    <p className="text-white text-2xl font-bold">{listings.filter(l => l.status === 'active').length}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Dealer Listings */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
          <Package size={24} className="text-teal-600" />
          <span>এই ডিলারের বিজ্ঞাপনসমূহ</span>
        </h2>

        {listings.length === 0 ? (
          <div className="bg-white rounded-xl p-12 text-center">
            <Package size={48} className="mx-auto text-gray-400 mb-4" />
            <p className="text-gray-600">কোন বিজ্ঞাপন পাওয়া যায়নি</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {listings.map((listing) => (
              <div
                key={listing.id}
                onClick={() => onListingClick(listing)}
                className="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow cursor-pointer group"
              >
                {/* Image */}
                <div className="relative h-48 overflow-hidden bg-gray-200">
                  {listing.images && listing.images.length > 0 ? (
                    <img 
                      src={getImageUrl(listing.images[0])} 
                      alt={listing.title}
                      className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                    />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center">
                      <Package size={48} className="text-gray-400" />
                    </div>
                  )}
                  {listing.is_featured && (
                    <div className="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                      ফিচার্ড
                    </div>
                  )}
                </div>

                {/* Content */}
                <div className="p-4">
                  <h3 className="font-bold text-lg text-gray-900 mb-2 line-clamp-2 group-hover:text-teal-600 transition-colors">
                    {listing.title}
                  </h3>
                  
                  <div className="flex items-center justify-between">
                    <p className="text-2xl font-black text-teal-600">
                      {formatPrice(listing.price)}
                    </p>
                    {listing.created_at && (
                      <p className="text-xs text-gray-500">
                        {formatDate(listing.created_at)}
                      </p>
                    )}
                  </div>

                  {listing.location && (
                    <div className="flex items-center gap-1 text-gray-600 text-sm mt-2">
                      <MapPin size={14} />
                      <span className="truncate">{listing.location}</span>
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
