import { MapPinned, Users, ShieldCheck, BadgeCheck } from 'lucide-react'
import { useState, useEffect } from 'react'
import { apiCall } from '../config'

export default function TrustStats() {
  const [stats, setStats] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Fetch stats from API
    const fetchStats = async () => {
      try {
        const response = await apiCall('/stats')
        if (response.success && response.data) {
          setStats(response.data)
        }
      } catch (error) {
        console.error('Failed to fetch stats:', error)
      } finally {
        setLoading(false)
      }
    }

    fetchStats()
  }, [])

  // Display cards based on API data
  const displayStats = stats ? [
    {
      icon: MapPinned,
      number: stats.total_locations || '15',
      title: 'এলাকায় সেবা',
      description: 'সারা বাংলাদেশে',
      color: 'bg-blue-100 text-blue-600'
    },
    {
      icon: Users,
      number: `${stats.total_users?.toLocaleString('bn-BD') || '0'}+`,
      title: 'সক্রিয় ব্যবহারকারী',
      description: 'বিশ্বস্ত ইউজার',
      color: 'bg-green-100 text-green-600'
    },
    {
      icon: ShieldCheck,
      number: `${stats.active_listings?.toLocaleString('bn-BD') || '0'}+`,
      title: 'সক্রিয় বিজ্ঞাপন',
      description: 'যাচাইকৃত গাড়ি',
      color: 'bg-orange-100 text-orange-600'
    },
    {
      icon: BadgeCheck,
      number: `${stats.total_dealers?.toLocaleString('bn-BD') || '0'}+`,
      title: 'ডিলার',
      description: 'ভেরিফায়েড বিক্রেতা',
      color: 'bg-amber-100 text-amber-600'
    },
  ] : []
  return (
    <section className="py-20 bg-gradient-to-br from-white via-gray-50 to-white relative overflow-hidden">
      {/* Background Decorations */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-0 left-1/4 w-96 h-96 bg-primary-100 rounded-full blur-3xl opacity-30"></div>
        <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-orange-100 rounded-full blur-3xl opacity-30"></div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {loading ? (
          <div className="text-center py-12">
            <div className="w-16 h-16 border-4 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-gray-500 font-semibold">তথ্য লোড হচ্ছে...</p>
          </div>
        ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {displayStats.map((stat, index) => {
            const Icon = stat.icon
            return (
              <div
                key={index}
                className="relative bg-white rounded-2xl p-8 shadow-lg border-2 border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group overflow-hidden"
              >
                {/* Gradient overlay on hover */}
                <div className="absolute inset-0 bg-gradient-to-br from-teal-50 to-orange-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div className="relative z-10 text-center">
                  <div className={`${stat.color} w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-lg`}>
                    <Icon size={40} />
                  </div>
                  <div className="space-y-3">
                    <p className="text-5xl font-black text-gray-900">{stat.number}</p>
                    <h3 className="text-xl font-bold text-gray-900">{stat.title}</h3>
                    <p className="text-gray-600 font-medium">{stat.description}</p>
                  </div>
                </div>
              </div>
            )
          })}
        </div>
        )}
      </div>
    </section>
  )
}
