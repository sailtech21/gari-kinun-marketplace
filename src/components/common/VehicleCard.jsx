import { MapPin, Heart, Calendar, Fuel, Car, Eye, BadgeCheck, Phone } from 'lucide-react'
import { useState } from 'react'
import { getImageUrl } from '../../config'

export default function VehicleCard({ 
  listing, 
  variant = 'grid', // grid | list | compact
  onFavorite,
  onClick
}) {
  const [isFavorited, setIsFavorited] = useState(false)
  
  const handleFavorite = (e) => {
    e.stopPropagation()
    setIsFavorited(!isFavorited)
    if (onFavorite) onFavorite(listing.id)
  }
  
  const handleClick = () => {
    if (onClick) onClick(listing)
  }
  
  if (variant === 'list') {
    return (
      <div 
        onClick={handleClick}
        className="bg-white rounded-xl p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group"
      >
        <div className="flex flex-col md:flex-row gap-6">
          {/* Image */}
          <div className="relative w-full md:w-48 h-36 flex-shrink-0 rounded-lg overflow-hidden">
            <img 
              src={getImageUrl(listing.image || listing.images?.[0])} 
              alt={listing.title}
              className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
            <span className="absolute top-3 left-3 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
              {listing.tag}
            </span>
            <button 
              onClick={handleFavorite}
              className="absolute top-3 right-3 w-9 h-9 bg-white rounded-full flex items-center justify-center hover:bg-red-50 transition-colors"
            >
              <Heart size={18} className={isFavorited ? 'fill-red-500 text-red-500' : 'text-gray-600'} />
            </button>
          </div>

          {/* Content */}
          <div className="flex-1">
            <div className="flex items-start justify-between mb-3">
              <div>
                <h3 className="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">
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
                    <span>{listing.views}</span>
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

            {/* Seller */}
            <div className="flex items-center justify-between pt-3 border-t border-gray-100">
              <div className="flex items-center gap-2 text-sm text-gray-600">
                {(listing.dealer || listing.user?.email_verified_at) && (
                  <BadgeCheck size={16} className="text-blue-500" />
                )}
                <span className="font-semibold text-gray-900">{listing.dealer?.name || listing.user?.name || 'Unknown'}</span>
              </div>
              <button className="text-gray-700 font-semibold hover:gap-2 flex items-center gap-1 transition-all text-sm hover:text-primary-600">
                <Phone size={16} />
                <span>কল করুন</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    )
  }
  
  // Grid variant (default)
  return (
    <div 
      onClick={handleClick}
      className="card overflow-hidden group cursor-pointer"
    >
      {/* Image */}
      <div className="relative h-48 overflow-hidden">
        <img
          src={getImageUrl(listing.image || listing.images?.[0])}
          alt={listing.title}
          className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
        />
        <span className="absolute top-3 left-3 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
          {listing.tag}
        </span>
        <button 
          onClick={handleFavorite}
          className="absolute top-3 right-3 w-10 h-10 bg-white rounded-full flex items-center justify-center hover:bg-red-50 transition-colors"
        >
          <Heart size={20} className={isFavorited ? 'fill-red-500 text-red-500' : 'text-gray-600'} />
        </button>
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

        {/* Seller & Views */}
        <div className="flex items-center justify-between pt-2 border-t border-gray-100">
          <div className="flex items-center gap-1 text-xs text-gray-500">
            {(listing.dealer || listing.user?.email_verified_at) && (
              <BadgeCheck size={14} className="text-blue-500" />
            )}
            <span className="line-clamp-1">{listing.dealer?.name || listing.user?.name || 'Unknown'}</span>
          </div>
          <div className="flex items-center gap-1 text-xs text-gray-500">
            <Eye size={14} />
            <span>{listing.views}</span>
          </div>
        </div>

        <button className="w-full mt-2 bg-primary-50 hover:bg-primary-100 text-primary-700 font-semibold py-2.5 rounded-lg transition-colors">
          বিস্তারিত দেখুন
        </button>
      </div>
    </div>
  )
}
