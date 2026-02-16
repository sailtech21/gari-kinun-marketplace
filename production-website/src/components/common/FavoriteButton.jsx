import { Heart } from 'lucide-react'
import { useState, useEffect } from 'react'
import { apiCall } from '../../config'

export default function FavoriteButton({ listingId, onAuthRequired, className = '' }) {
  const [isFavorite, setIsFavorite] = useState(false)
  const [isLoading, setIsLoading] = useState(false)

  useEffect(() => {
    checkFavoriteStatus()
  }, [listingId])

  const checkFavoriteStatus = async () => {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      setIsFavorite(false)
      return
    }

    try {
      const response = await apiCall(`/listings/${listingId}/favorite/check`)
      setIsFavorite(response.is_favorite || false)
    } catch (error) {
      // Silently fail - user might not be logged in
      setIsFavorite(false)
    }
  }

  const handleToggleFavorite = async (e) => {
    e.stopPropagation() // Prevent triggering parent click events
    
    const token = localStorage.getItem('auth_token')
    if (!token) {
      if (onAuthRequired) {
        onAuthRequired()
      } else {
        alert('প্রিয় তালিকায় যুক্ত করতে লগইন করুন')
      }
      return
    }

    setIsLoading(true)

    try {
      if (isFavorite) {
        // Remove from favorites
        await apiCall(`/listings/${listingId}/unfavorite`, { method: 'DELETE' })
        setIsFavorite(false)
      } else {
        // Add to favorites
        await apiCall(`/listings/${listingId}/favorite`, { method: 'POST' })
        setIsFavorite(true)
      }
    } catch (error) {
      console.error('Error toggling favorite:', error)
      alert('একটি ত্রুটি হয়েছে। আবার চেষ্টা করুন।')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <button
      onClick={handleToggleFavorite}
      disabled={isLoading}
      className={`w-10 h-10 bg-white rounded-full flex items-center justify-center transition-all shadow-sm hover:shadow-md ${
        isLoading ? 'opacity-50 cursor-not-allowed' : ''
      } ${className}`}
      title={isFavorite ? 'প্রিয় তালিকা থেকে সরান' : 'প্রিয় তালিকায় যুক্ত করুন'}
    >
      <Heart
        size={20}
        className={`transition-colors ${
          isFavorite ? 'text-red-500 fill-red-500' : 'text-gray-600 hover:text-red-500'
        }`}
      />
    </button>
  )
}
