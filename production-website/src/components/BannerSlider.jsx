import { useState, useEffect } from 'react'
import { ChevronLeft, ChevronRight, ExternalLink } from 'lucide-react'
import { apiCall, getImageUrl } from '../config'

export default function BannerSlider() {
  const [banners, setBanners] = useState([])
  const [currentIndex, setCurrentIndex] = useState(0)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetchBanners()
  }, [])

  // Auto-slide every 5 seconds
  useEffect(() => {
    if (banners.length <= 1) return

    const interval = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % banners.length)
    }, 5000)

    return () => clearInterval(interval)
  }, [banners.length])

  const fetchBanners = async () => {
    try {
      const response = await apiCall('/banners')
      if (response.success && response.data) {
        // Filter only active banners
        const activeBanners = response.data.filter(banner => banner.is_active)
        setBanners(activeBanners)
      }
    } catch (error) {
      console.error('Failed to fetch banners:', error)
    } finally {
      setLoading(false)
    }
  }

  // Tracking functions disabled - backend routes not available
  const trackImpression = (bannerId) => {
    // TODO: Implement backend routes for tracking
  }

  const trackClick = (bannerId) => {
    // TODO: Implement backend routes for tracking
  }

  const nextSlide = () => {
    setCurrentIndex((prev) => (prev + 1) % banners.length)
  }

  const prevSlide = () => {
    setCurrentIndex((prev) => (prev - 1 + banners.length) % banners.length)
  }

  const handleBannerClick = (banner) => {
    trackClick(banner.id)
    if (banner.link) {
      window.open(banner.link, '_blank', 'noopener,noreferrer')
    }
  }

  if (loading) {
    return (
      <div className="bg-gray-200 rounded-xl sm:rounded-2xl h-48 sm:h-64 md:h-80 lg:h-96 animate-pulse"></div>
    )
  }

  if (banners.length === 0) {
    return null
  }

  return (
    <div className="relative group">
      {/* Banner Container */}
      <div className="relative overflow-hidden rounded-xl sm:rounded-2xl shadow-xl bg-gray-900">
        <div 
          className="flex transition-transform duration-500 ease-out"
          style={{ transform: `translateX(-${currentIndex * 100}%)` }}
        >
          {banners.map((banner) => (
            <div
              key={banner.id}
              className="w-full flex-shrink-0 relative"
            >
              <div className="relative h-48 sm:h-64 md:h-80 lg:h-96">
                <img
                  src={getImageUrl(banner.image)}
                  alt={banner.title}
                  className="w-full h-full object-cover"
                  onError={(e) => {
                    e.target.src = 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800'
                  }}
                />
                
                {/* Gradient Overlay */}
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                
                {/* Banner Content */}
                <div className="absolute bottom-0 left-0 right-0 p-4 sm:p-6 lg:p-8">
                  <h3 className="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-2">
                    {banner.title}
                  </h3>
                  {banner.description && (
                    <p className="text-sm sm:text-base text-white/90 mb-4 line-clamp-2">
                      {banner.description}
                    </p>
                  )}
                  {banner.link && banner.button_text && (
                    <button
                      onClick={() => handleBannerClick(banner)}
                      className="inline-flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg transition-colors"
                    >
                      <span>{banner.button_text}</span>
                      <ExternalLink size={18} />
                    </button>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Navigation Arrows - Only show if multiple banners */}
        {banners.length > 1 && (
          <>
            <button
              onClick={prevSlide}
              className="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <ChevronLeft size={24} className="text-gray-900" />
            </button>
            <button
              onClick={nextSlide}
              className="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <ChevronRight size={24} className="text-gray-900" />
            </button>
          </>
        )}

        {/* Dots Indicator - Only show if multiple banners */}
        {banners.length > 1 && (
          <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
            {banners.map((_, index) => (
              <button
                key={index}
                onClick={() => setCurrentIndex(index)}
                className={`w-2 h-2 sm:w-3 sm:h-3 rounded-full transition-all ${
                  index === currentIndex
                    ? 'bg-white w-6 sm:w-8'
                    : 'bg-white/50 hover:bg-white/75'
                }`}
              />
            ))}
          </div>
        )}
      </div>
    </div>
  )
}
