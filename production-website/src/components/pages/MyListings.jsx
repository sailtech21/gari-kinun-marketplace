import { useState, useEffect } from 'react'
import { ArrowLeft, Plus, Edit2, Trash2, Eye, Calendar, MapPin, DollarSign, Package } from 'lucide-react'
import { apiCall, getImageUrl } from '../../config'

export default function MyListings({ onBack, onEdit, onCreate, onViewListing }) {
  const [listings, setListings] = useState([])
  const [loading, setLoading] = useState(true)
  const [filter, setFilter] = useState('all') // all, active, pending, sold
  const [stats, setStats] = useState({
    total: 0,
    active: 0,
    pending: 0,
    sold: 0
  })

  useEffect(() => {
    fetchMyListings()
  }, [filter])

  const fetchMyListings = async () => {
    // Check if user is authenticated
    const token = localStorage.getItem('auth_token')
    if (!token) {
      setLoading(false)
      return
    }

    setLoading(true)
    try {
      const endpoint = filter === 'all' ? '/users/listings' : `/users/listings?status=${filter}`
      const response = await apiCall(endpoint)
      
      if (response.success) {
        setListings(response.data.listings || [])
        setStats(response.data.stats || stats)
      }
    } catch (error) {
      if (error.message !== 'Unauthenticated') {
        console.error('Error fetching listings:', error)
      }
    } finally {
      setLoading(false)
    }
  }

  const handleDelete = async (listingId) => {
    if (!confirm('আপনি কি নিশ্চিত এই বিজ্ঞাপনটি মুছে ফেলতে চান?')) return

    try {
      const response = await apiCall(`/listings/${listingId}`, {
        method: 'DELETE'
      })

      if (response.success) {
        alert('বিজ্ঞাপন মুছে ফেলা হয়েছে!')
        fetchMyListings()
      }
    } catch (error) {
      alert('বিজ্ঞাপন মুছতে সমস্যা হয়েছে')
    }
  }

  const getStatusBadge = (status) => {
    const badges = {
      active: 'bg-green-100 text-green-700',
      pending: 'bg-yellow-100 text-yellow-700',
      sold: 'bg-blue-100 text-blue-700',
      rejected: 'bg-red-100 text-red-700'
    }
    const labels = {
      active: 'সক্রিয়',
      pending: 'অপেক্ষমাণ',
      sold: 'বিক্রিত',
      rejected: 'প্রত্যাখ্যাত'
    }
    return (
      <span className={`px-3 py-1 rounded-full text-xs font-bold ${badges[status] || badges.pending}`}>
        {labels[status] || status}
      </span>
    )
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
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-black mb-2">আমার বিজ্ঞাপন</h1>
              <p className="text-teal-100">আপনার সকল বিজ্ঞাপন দেখুন এবং পরিচালনা করুন</p>
            </div>
            <button
              onClick={onCreate}
              className="hidden md:flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-xl font-bold transition-colors"
            >
              <Plus size={20} />
              নতুন বিজ্ঞাপন
            </button>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Stats */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          <div className="bg-white rounded-xl p-6 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm mb-1">মোট</p>
                <p className="text-3xl font-black text-gray-900">{stats.total}</p>
              </div>
              <Package className="text-gray-400" size={32} />
            </div>
          </div>
          <div className="bg-white rounded-xl p-6 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm mb-1">সক্রিয়</p>
                <p className="text-3xl font-black text-green-600">{stats.active}</p>
              </div>
              <div className="w-8 h-8 bg-green-100 rounded-full"></div>
            </div>
          </div>
          <div className="bg-white rounded-xl p-6 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm mb-1">অপেক্ষমাণ</p>
                <p className="text-3xl font-black text-yellow-600">{stats.pending}</p>
              </div>
              <div className="w-8 h-8 bg-yellow-100 rounded-full"></div>
            </div>
          </div>
          <div className="bg-white rounded-xl p-6 shadow-sm">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm mb-1">বিক্রিত</p>
                <p className="text-3xl font-black text-blue-600">{stats.sold}</p>
              </div>
              <div className="w-8 h-8 bg-blue-100 rounded-full"></div>
            </div>
          </div>
        </div>

        {/* Filters */}
        <div className="bg-white rounded-xl shadow-sm p-4 mb-6">
          <div className="flex gap-2 overflow-x-auto">
            {[
              { key: 'all', label: 'সব' },
              { key: 'active', label: 'সক্রিয়' },
              { key: 'pending', label: 'অপেক্ষমাণ' },
              { key: 'sold', label: 'বিক্রিত' }
            ].map(({ key, label }) => (
              <button
                key={key}
                onClick={() => setFilter(key)}
                className={`px-6 py-2 rounded-lg font-semibold whitespace-nowrap transition-colors ${
                  filter === key
                    ? 'bg-rose-500 hover:bg-rose-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                }`}
              >
                {label}
              </button>
            ))}
          </div>
        </div>

        {/* Listings */}
        {loading ? (
          <div className="text-center py-12">
            <div className="w-16 h-16 border-4 border-teal-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-gray-500">লোড হচ্ছে...</p>
          </div>
        ) : listings.length === 0 ? (
          <div className="bg-white rounded-xl shadow-sm p-12 text-center">
            <Package size={64} className="text-gray-300 mx-auto mb-4" />
            <h3 className="text-xl font-bold text-gray-900 mb-2">কোন বিজ্ঞাপন নেই</h3>
            <p className="text-gray-600 mb-6">আপনি এখনো কোন বিজ্ঞাপন পোস্ট করেননি</p>
            <button
              onClick={onCreate}
              className="inline-flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-xl font-bold transition-colors"
            >
              <Plus size={20} />
              প্রথম বিজ্ঞাপন দিন
            </button>
          </div>
        ) : (
          <div className="space-y-4">
            {listings.map((listing) => (
              <div key={listing.id} className="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                <div className="p-6">
                  <div className="flex gap-6">
                    {/* Image */}
                    <div className="flex-shrink-0">
                      <img
                        src={getImageUrl(listing.image || listing.images?.[0])}
                        alt={listing.title}
                        className="w-48 h-32 object-cover rounded-lg"
                      />
                    </div>

                    {/* Details */}
                    <div className="flex-1 min-w-0">
                      <div className="flex items-start justify-between mb-3">
                        <div>
                          <h3 className="text-xl font-bold text-gray-900 mb-1">{listing.title}</h3>
                          <div className="flex items-center gap-4 text-sm text-gray-600">
                            <span className="flex items-center gap-1">
                              <Calendar size={16} />
                              {new Date(listing.created_at).toLocaleDateString('bn-BD')}
                            </span>
                            <span className="flex items-center gap-1">
                              <Eye size={16} />
                              {listing.views || 0} বার দেখা হয়েছে
                            </span>
                          </div>
                        </div>
                        {getStatusBadge(listing.status)}
                      </div>

                      <div className="flex items-center gap-6 mb-4 text-gray-700">
                        <span className="flex items-center gap-1">
                          <DollarSign size={18} className="text-rose-500" />
                          <span className="font-bold text-lg">৳{parseInt(listing.price).toLocaleString('bn-BD')}</span>
                        </span>
                        <span className="flex items-center gap-1">
                          <MapPin size={16} />
                          {listing.location}
                        </span>
                      </div>

                      {/* Actions */}
                      <div className="flex gap-3">
                        <button
                          onClick={() => onViewListing(listing)}
                          className="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors"
                        >
                          <Eye size={18} />
                          দেখুন
                        </button>
                        <button
                          onClick={() => onEdit(listing)}
                          className="flex items-center gap-2 px-4 py-2 bg-teal-100 hover:bg-teal-200 text-teal-700 rounded-lg font-semibold transition-colors"
                        >
                          <Edit2 size={18} />
                          এডিট করুন
                        </button>
                        <button
                          onClick={() => handleDelete(listing.id)}
                          className="flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-semibold transition-colors"
                        >
                          <Trash2 size={18} />
                          মুছুন
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}

        {/* Mobile Add Button */}
        <button
          onClick={onCreate}
          className="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-rose-500 hover:bg-rose-600 text-white rounded-full shadow-lg flex items-center justify-center transition-colors"
        >
          <Plus size={24} />
        </button>
      </div>
    </div>
  )
}
