import { MapPin, Heart, Calendar, Fuel, Car, Eye, BadgeCheck, Phone, MessageCircle } from 'lucide-react'
import { useState } from 'react'
import { getImageUrl } from '../../config'
import FavoriteButton from './FavoriteButton'

export default function VehicleCard({ 
  listing, 
  variant = 'grid', // grid | list | compact
  onFavorite,
  onAuthRequired,
  onClick
}) {
  
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
              <div className="flex gap-2">
                <a
                  href={`tel:${listing.phone}`}
                  onClick={(e) => e.stopPropagation()}
                  className="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors"
                >
                  <Phone size={16} />
                  <span>কল করুন</span>
                </a>
                <a
                  href={`https://wa.me/${listing.phone?.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(`আপনার ${listing.title} সম্পর্কে জানতে আগ্রহী`)}`}
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={(e) => e.stopPropagation()}
                  className="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors"
                >
                  <MessageCircle size={16} />
                  <span>WhatsApp</span>
                </a>
              </div>
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

        {/* Mobile Contact Buttons */}
        <div className="flex gap-2 mt-2">
          <a
            href={`tel:${listing.phone}`}
            onClick={(e) => e.stopPropagation()}
            className="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2"
          >
            <Phone size={18} />
            <span className="text-sm">কল করুন</span>
          </a>
          <a
            href={`https://wa.me/${listing.phone?.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(`আপনার ${listing.title} সম্পর্কে জানতে আগ্রহী`)}`}
            target="_blank"
            rel="noopener noreferrer"
            onClick={(e) => e.stopPropagation()}
            className="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2"
          >
            <MessageCircle size={18} />
            <span className="text-sm">WhatsApp</span>
          </a>
        </div>
      </div>
    </div>
  )
}
