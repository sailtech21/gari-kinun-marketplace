import { useState, useEffect } from 'react'
import { 
  ArrowLeft, Heart, Share2, MapPin, Calendar, Fuel, Car, Eye, 
  BadgeCheck, Phone, Mail, MessageCircle, ChevronLeft, ChevronRight,
  Gauge, Palette, Settings, Shield, Clock, CheckCircle, MessageSquare, Flag, Star, User
} from 'lucide-react'
import Button from '../common/Button'
import ContactModal from '../modals/ContactModal'
import ReportModal from '../modals/ReportModal'
import ReviewModal from '../modals/ReviewModal'
import ReviewsSection from '../common/ReviewsSection'
import { apiCall, getImageUrl } from '../../config'

export default function ListingDetail({ listing: initialListing, onBack }) {
  const [listing, setListing] = useState(initialListing)
  const [loading, setLoading] = useState(!initialListing)
  const [currentImageIndex, setCurrentImageIndex] = useState(0)
  const [isFavorited, setIsFavorited] = useState(false)
  const [showContactModal, setShowContactModal] = useState(false)
  const [showReportModal, setShowReportModal] = useState(false)
  const [showReviewModal, setShowReviewModal] = useState(false)
  const [phoneRevealed, setPhoneRevealed] = useState(false)
  const [similarListings, setSimilarListings] = useState([])
  const [reviews, setReviews] = useState([])
  const [loadingSimilar, setLoadingSimilar] = useState(false)
  const [loadingReviews, setLoadingReviews] = useState(false)
  
  useEffect(() => {
    if (initialListing?.id) {
      fetchListingDetail(initialListing.id)
      fetchSimilarListings(initialListing.id)
      fetchReviews(initialListing.id)
    }
  }, [initialListing?.id])
  
  const fetchReviews = async (listingId) => {
    try {
      setLoadingReviews(true)
      const response = await apiCall(`/reviews?listing_id=${listingId}`)
      if (response.success) {
        setReviews(response.data || [])
      }
    } catch (error) {
      console.error('Error fetching reviews:', error)
    } finally {
      setLoadingReviews(false)
    }
  }
  
  const fetchSimilarListings = async (listingId) => {
    try {
      setLoadingSimilar(true)
      const response = await apiCall(`/listings/${listingId}/similar`)
      if (response.success) {
        setSimilarListings(response.data || [])
      }
    } catch (error) {
      console.error('Error fetching similar listings:', error)
    } finally {
      setLoadingSimilar(false)
    }
  }
  
  const fetchListingDetail = async (listingId) => {
    try {
      setLoading(true)
      const response = await apiCall(`/listings/${listingId}`)
      if (response.success) {
        setListing(response.data)
        // Check if favorited
        const token = localStorage.getItem('auth_token')
        if (token) {
          const favResponse = await apiCall(`/listings/${listingId}/favorite/check`)
          setIsFavorited(favResponse.is_favorited || false)
        }
      }
    } catch (error) {
      console.error('Error fetching listing:', error)
    } finally {
      setLoading(false)
    }
  }
  
  const handleToggleFavorite = async () => {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      alert('প্রিয় তালিকায় যুক্ত করতে লগইন করুন')
      return
    }
    
    try {
      if (isFavorited) {
        await apiCall(`/listings/${listing.id}/unfavorite`, { method: 'DELETE' })
        setIsFavorited(false)
      } else {
        await apiCall(`/listings/${listing.id}/favorite`, { method: 'POST' })
        setIsFavorited(true)
      }
    } catch (error) {
      console.error('Error toggling favorite:', error)
    }
  }
  
  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="w-16 h-16 border-4 border-teal-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-gray-500">লোড হচ্ছে...</p>
        </div>
      </div>
    )
  }
  
  if (!listing) {
    return <div>লিস্টিং পাওয়া যায়নি</div>
  }
  
  // Get images from listing data
  const images = listing.images && listing.images.length > 0 
    ? (typeof listing.images === 'string' ? JSON.parse(listing.images) : listing.images).map(img => getImageUrl(img))
    : [getImageUrl(listing.image)]
  
  const nextImage = () => {
    setCurrentImageIndex((prev) => (prev + 1) % images.length)
  }
  
  const prevImage = () => {
    setCurrentImageIndex((prev) => (prev - 1 + images.length) % images.length)
  }
  
  const handleShare = () => {
    if (navigator.share) {
      navigator.share({
        title: listing.title,
        text: `${listing.title} - ৳${listing.price}`,
        url: window.location.href
      })
    } else {
      alert('শেয়ার লিংক কপি করা হয়েছে!')
    }
  }
  
  const specifications = [
    { icon: Calendar, label: 'মডেল বছর', value: listing.year || listing.model_year || 'N/A' },
    { icon: Gauge, label: 'মাইলেজ', value: listing.mileage || 'N/A' },
    { icon: Fuel, label: 'জ্বালানি', value: listing.fuel_type || listing.fuel || 'N/A' },
    { icon: Settings, label: 'ট্রান্সমিশন', value: listing.transmission || 'N/A' },
    { icon: Car, label: 'ব্র্যান্ড', value: listing.brand || 'N/A' },
    { icon: Car, label: 'মডেল', value: listing.model || 'N/A' },
    { icon: Shield, label: 'কন্ডিশন', value: listing.condition || 'N/A' },
    { icon: Eye, label: 'দেখা হয়েছে', value: `${listing.views || 0} বার` }
  ]
  
  return (
    <div className="min-h-screen bg-orange-50 pb-20 lg:pb-0">
      {/* Header */}
      <div className="bg-white shadow-sm sticky top-0 z-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <button 
              onClick={onBack}
              className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold"
            >
              <ArrowLeft size={20} />
              <span>ফিরে যান</span>
            </button>
            
            <div className="flex items-center gap-3">
              <button 
                onClick={handleToggleFavorite}
                className="p-2 rounded-full hover:bg-gray-100"
                title={isFavorited ? 'প্রিয় তালিকা থেকে সরান' : 'প্রিয় তালিকায় যোগ করুন'}
              >
                <Heart 
                  size={24} 
                  className={isFavorited ? 'fill-red-500 text-red-500' : 'text-gray-600'} 
                />
              </button>
              <button 
                onClick={handleShare}
                className="p-2 rounded-full hover:bg-gray-100"
              >
                <Share2 size={24} className="text-gray-600" />
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="grid lg:grid-cols-3 gap-8">
          {/* Left Column - Images & Details */}
          <div className="lg:col-span-2 space-y-6">
            {/* Image Gallery */}
            <div className="bg-white rounded-xl overflow-hidden shadow-sm">
              <div className="relative aspect-video bg-gray-200">
                <img 
                  src={images[currentImageIndex]} 
                  alt={listing.title}
                  className="w-full h-full object-cover"
                />
                
                {/* Navigation Arrows */}
                <button 
                  onClick={prevImage}
                  className="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg"
                >
                  <ChevronLeft size={24} />
                </button>
                <button 
                  onClick={nextImage}
                  className="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-lg"
                >
                  <ChevronRight size={24} />
                </button>
                
                {/* Image Counter */}
                <div className="absolute bottom-4 right-4 bg-black/70 text-white px-3 py-1 rounded-full text-sm">
                  {currentImageIndex + 1} / {images.length}
                </div>
                
                {/* Tag */}
                <div className="absolute top-4 left-4 bg-green-500 text-white px-4 py-2 rounded-lg font-semibold">
                  {listing.tag}
                </div>
              </div>
              
              {/* Thumbnail Strip */}
              <div className="p-4 flex gap-2 overflow-x-auto">
                {images.map((img, index) => (
                  <button
                    key={index}
                    onClick={() => setCurrentImageIndex(index)}
                    className={`flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 ${
                      index === currentImageIndex ? 'border-teal-700' : 'border-gray-200'
                    }`}
                  >
                    <img src={img} alt="" className="w-full h-full object-cover" />
                  </button>
                ))}
              </div>
            </div>
            
            {/* Title & Price */}
            <div className="bg-white rounded-xl p-6 shadow-sm">
              <div className="flex items-start justify-between mb-4">
                <div>
                  <h1 className="text-3xl font-bold text-gray-900 mb-2">
                    {listing.title}
                  </h1>
                  <div className="flex items-center gap-2 text-gray-600">
                    <MapPin size={18} />
                    <span>{listing.location}</span>
                  </div>
                </div>
                <div className="text-right">
                  <div className="text-3xl font-bold text-gray-900">
                    ৳ {listing.price}
                  </div>
                  <div className="text-sm text-gray-500 mt-1">
                    নেগোশিয়েবল
                  </div>
                </div>
              </div>
              
              <div className="flex items-center gap-4 text-sm text-gray-500">
                <div className="flex items-center gap-1">
                  <Eye size={16} />
                  <span>{listing.views} বার দেখা হয়েছে</span>
                </div>
                <div className="flex items-center gap-1">
                  <Clock size={16} />
                  <span>২ দিন আগে পোস্ট করা হয়েছে</span>
                </div>
              </div>
            </div>
            
            {/* Specifications */}
            <div className="bg-white rounded-xl p-6 shadow-sm">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">
                স্পেসিফিকেশন
              </h2>
              
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {specifications.map((spec, index) => {
                  const Icon = spec.icon
                  return (
                    <div key={index} className="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                      <div className="w-10 h-10 bg-teal-100 text-teal-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <Icon size={20} />
                      </div>
                      <div>
                        <div className="text-xs text-gray-500 mb-1">{spec.label}</div>
                        <div className="font-semibold text-gray-900">{spec.value}</div>
                      </div>
                    </div>
                  )
                })}
              </div>
            </div>
            
            {/* Description */}
            <div className="bg-white rounded-xl p-6 shadow-sm">
              <h2 className="text-2xl font-bold text-gray-900 mb-4">
                বিস্তারিত বর্ণনা
              </h2>
              <p className="text-gray-700 leading-relaxed whitespace-pre-line">
                {listing.description}
                
                {'\n\n'}বিশেষ বৈশিষ্ট্য:
                {'\n'}• সম্পূর্ণ সার্ভিস হিস্ট্রি বিদ্যমান
                {'\n'}• সব কাগজপত্র আপডেট
                {'\n'}• কোনো দুর্ঘটনার রেকর্ড নেই
                {'\n'}• টেস্ট ড্রাইভের সুবিধা উপলব্ধ
                {'\n'}• ব্যাংক লোন সহায়তা
              </p>
            </div>
            
            {/* Features */}
            <div className="bg-white rounded-xl p-6 shadow-sm">
              <h2 className="text-2xl font-bold text-gray-900 mb-4">
                অতিরিক্ত সুবিধা
              </h2>
              <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
                {[
                  'এয়ার কন্ডিশনার',
                  'পাওয়ার স্টিয়ারিং',
                  'পাওয়ার উইন্ডো',
                  'ABS ব্রেক',
                  'অ্যালয় হুইল',
                  'ফগ লাইট',
                  'রিয়ার ক্যামেরা',
                  'মিউজিক সিস্টেম',
                  'সানরুফ'
                ].map((feature, index) => (
                  <div key={index} className="flex items-center gap-2 text-gray-700">
                    <CheckCircle size={18} className="text-green-500" />
                    <span>{feature}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
          
          {/* Right Column - Seller Info */}
          <div className="lg:col-span-1">
            <div className="sticky top-24 space-y-4">
              {/* Seller Card */}
              <div className="bg-white rounded-xl p-6 shadow-sm">
                <h3 className="text-lg font-bold text-gray-900 mb-4">
                  বিক্রেতার তথ্য
                </h3>
                
                <div className="flex items-center gap-4 mb-6">
                  <div className="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center">
                    <span className="text-2xl font-bold text-teal-700">
                      {listing.user?.name?.charAt(0) || listing.dealer?.name?.charAt(0) || 'S'}
                    </span>
                  </div>
                  <div>
                    <div className="flex items-center gap-2 mb-1">
                      <h4 className="font-bold text-gray-900">{listing.user?.name || listing.dealer?.name || 'বিক্রেতা'}</h4>
                      {(listing.user?.is_verified || listing.dealer?.status === 'active') && (
                        <BadgeCheck size={18} className="text-blue-500" />
                      )}
                    </div>
                    <div className="text-sm text-gray-500">
                      সদস্য {new Date(listing.created_at).getFullYear()} থেকে
                    </div>
                  </div>
                </div>
                
                {(listing.user?.is_verified || listing.dealer?.status === 'active') && (
                  <div className="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <div className="flex items-center gap-2 text-blue-700 text-sm">
                      <BadgeCheck size={16} />
                      <span>যাচাইকৃত বিক্রেতা</span>
                    </div>
                  </div>
                )}
                
                <div className="space-y-3">
                  {(listing.user?.phone || listing.dealer?.phone) && !phoneRevealed ? (
                    <Button 
                      variant="primary" 
                      size="lg" 
                      fullWidth
                      icon={<Phone size={20} />}
                      onClick={() => setPhoneRevealed(true)}
                    >
                      ফোন নম্বর দেখুন
                    </Button>
                  ) : (listing.user?.phone || listing.dealer?.phone) ? (
                    <>
                      <a href={`tel:${listing.user?.phone || listing.dealer?.phone}`}>
                        <Button 
                          variant="primary" 
                          size="lg" 
                          fullWidth
                          icon={<Phone size={20} />}
                          className="bg-rose-500 hover:bg-rose-600"
                        >
                          {listing.user?.phone || listing.dealer?.phone}
                        </Button>
                      </a>
                      <a 
                        href={`https://wa.me/${(listing.user?.phone || listing.dealer?.phone).replace(/^0/, '880').replace(/[^0-9]/g, '')}?text=আমি ${encodeURIComponent(listing.title)} গাড়িটি সম্পর্কে জানতে আগ্রহী`}
                      >
                        <Button 
                          variant="success" 
                          size="lg" 
                          fullWidth
                          icon={<MessageSquare size={20} />}
                        >
                          WhatsApp এ চ্যাট করুন
                        </Button>
                      </a>
                    </>
                  ) : null}
                  
                  <Button 
                    variant="secondary" 
                    size="lg" 
                    fullWidth
                    icon={<MessageCircle size={20} />}
                    onClick={() => setShowContactModal(true)}
                  >
                    ফর্ম দিয়ে মেসেজ পাঠান
                  </Button>
                </div>
                
                {phoneRevealed && (listing.user?.phone || listing.dealer?.phone) && (
                  <div className="mt-6 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
                    <p className="mb-2">বিক্রেতার ফোন:</p>
                    <p className="text-lg font-bold text-gray-900">{listing.user?.phone || listing.dealer?.phone}</p>
                  </div>
                )}
              </div>
              
              {/* Safety Tips */}
              <div className="bg-amber-50 border border-amber-200 rounded-xl p-6">
                <div className="flex items-center gap-2 text-amber-800 font-bold mb-3">
                  <Shield size={20} />
                  <span>নিরাপত্তা টিপস</span>
                </div>
                <ul className="text-sm text-amber-900 space-y-2">
                  <li>• কেনার আগে গাড়ি ভালো করে পরীক্ষা করুন</li>
                  <li>• পাবলিক জায়গায় দেখা করুন</li>
                  <li>• আগাম টাকা পাঠাবেন না</li>
                  <li>• সব কাগজপত্র যাচাই করুন</li>
                  <li>• সন্দেহজনক মনে হলে রিপোর্ট করুন</li>
                
                {/* Report Button */}
                <button
                  onClick={() => setShowReportModal(true)}
                  className="mt-4 w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border-2 border-amber-300 text-amber-800 rounded-xl font-semibold hover:bg-amber-100 transition-colors"
                >
                  <Flag size={18} />
                  <span>এই বিজ্ঞাপন রিপোর্ট করুন</span>
                </button>
                </ul>
              </div>
            </div>
          </div>
        </div>
        
        {/* Reviews Section */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-gray-200">
          <div className="flex items-center justify-between mb-6">
            <div>
              <h2 className="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <MessageCircle size={24} />
                রিভিউ ও রেটিং
              </h2>
              <p className="text-gray-600 mt-1">
                {reviews.length} টি রিভিউ
              </p>
            </div>
            <Button
              variant="primary"
              icon={<Star size={20} />}
              onClick={() => setShowReviewModal(true)}
            >
              রিভিউ লিখুন
            </Button>
          </div>
          
          {loadingReviews ? (
            <div className="text-center py-8">
              <div className="w-12 h-12 border-4 border-primary-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
            </div>
          ) : reviews.length > 0 ? (
            <div className="space-y-4">
              {reviews.map((review) => (
                <div key={review.id} className="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                  <div className="flex items-start justify-between mb-3">
                    <div className="flex items-center gap-3">
                      <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                        <User size={24} className="text-primary-600" />
                      </div>
                      <div>
                        <h4 className="font-bold text-gray-900">
                          {review.user?.name || review.name || 'ব্যবহারকারী'}
                        </h4>
                        <p className="text-sm text-gray-500">
                          {new Date(review.created_at).toLocaleDateString('bn-BD', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                          })}
                        </p>
                      </div>
                    </div>
                    <div className="flex items-center gap-1">
                      {[1, 2, 3, 4, 5].map((star) => (
                        <Star
                          key={star}
                          size={16}
                          className={star <= review.rating ? 'fill-yellow-400 text-yellow-400' : 'text-gray-300'}
                        />
                      ))}
                    </div>
                  </div>
                  <p className="text-gray-700 leading-relaxed">
                    {review.comment}
                  </p>
                  {review.location && (
                    <p className="text-sm text-gray-500 mt-2 flex items-center gap-1">
                      <MapPin size={14} />
                      {review.location}
                    </p>
                  )}
                </div>
              ))}
            </div>
          ) : (
            <div className="bg-gray-50 rounded-xl p-12 text-center">
              <div className="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                <MessageCircle size={32} className="text-gray-400" />
              </div>
              <h3 className="text-lg font-bold text-gray-900 mb-2">
                এখনো কোনো রিভিউ নেই
              </h3>
              <p className="text-gray-600 mb-4">
                প্রথম রিভিউ লিখুন এবং অন্যদের সাহায্য করুন
              </p>
              <Button
                variant="primary"
                icon={<Star size={20} />}
                onClick={() => setShowReviewModal(true)}
              >
                রিভিউ লিখুন
              </Button>
            </div>
          )}
        </div>
        
        {/* Reviews Section */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <ReviewsSection 
            listingId={listing?.id} 
            sellerId={listing?.user_id || listing?.dealer_id}
          />
        </div>
        
        {/* Similar Listings */}
        {similarListings.length > 0 && (
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-6">একই ধরনের গাড়ি</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {similarListings.slice(0, 6).map((similar) => (
                <div 
                  key={similar.id} 
                  onClick={() => window.location.reload()} 
                  className="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                >
                  <div className="relative h-48 rounded-t-xl overflow-hidden">
                    <img 
                      src={getImageUrl(similar.image || similar.images?.[0])} 
                      alt={similar.title}
                      className="w-full h-full object-cover"
                    />
                  </div>
                  <div className="p-4">
                    <h3 className="font-bold text-lg text-gray-900 mb-2 truncate">{similar.title}</h3>
                    <p className="text-2xl font-bold text-rose-500 mb-3">৳ {similar.price?.toLocaleString('bn-BD')}</p>
                    <div className="flex items-center gap-3 text-sm text-gray-600">
                      <span>{similar.year || similar.model_year}</span>
                      <span>•</span>
                      <span>{similar.mileage} কিমি</span>
                      <span>•</span>
                      <span>{similar.location}</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
      
      {/* Contact Modal */}
      <ContactModal 
        isOpen={showContactModal}
        onClose={() => setShowContactModal(false)}
        listing={listing}
      />
      
      <ReportModal 
        isOpen={showReportModal}
        onClose={() => setShowReportModal(false)}
        listingId={listing?.id}
      />
      
      <ReviewModal 
        isOpen={showReviewModal}
        onClose={() => setShowReviewModal(false)}
        listingId={listing?.id}
        onSuccess={() => fetchReviews()}
      />
      
      {/* Mobile Contact Bar - Shows only on mobile */}
      {(listing.user?.phone || listing.dealer?.phone) && (
        <div className="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl z-50 safe-area-pb">
          <div className="flex items-center gap-2 px-3 py-3">
            {/* Call Button */}
            <a 
              href={`tel:${listing.user?.phone || listing.dealer?.phone}`}
              className="flex-1"
            >
              <button className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-teal-700 hover:bg-teal-800 text-white rounded-lg font-semibold transition-colors">
                <Phone size={20} />
                <span>কল করুন</span>
              </button>
            </a>
            
            {/* WhatsApp Button */}
            <a 
              href={`https://wa.me/${(listing.user?.phone || listing.dealer?.phone).replace(/^0/, '880').replace(/[^0-9]/g, '')}?text=আমি ${encodeURIComponent(listing.title)} গাড়িটি সম্পর্কে জানতে আগ্রহী`}
              className="flex-1"
              target="_blank"
              rel="noopener noreferrer"
            >
              <button className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors">
                <MessageSquare size={20} />
                <span>WhatsApp</span>
              </button>
            </a>
          </div>
        </div>
      )}
    </div>
  )
}
