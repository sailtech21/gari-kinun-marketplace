import { Search, User, Plus, LogOut, Heart, Package, Settings, ChevronDown, Bell, MapPin } from 'lucide-react'
import { useState, useEffect } from 'react'
import AuthModal from './auth/AuthModal'
import SearchBar from './common/SearchBar'
import LocationModal from './modals/LocationModal'
import { apiCall, getImageUrl } from '../config'
import { useSettings } from '../contexts/SettingsContext'
import { useTranslation } from '../utils/translations'

export default function Header({ onSearchClick, onLogoClick, onCreateListing, onMyListingsClick, onProfileClick, onFavoritesClick, onNotificationsClick, user: propUser }) {
  const { language } = useSettings()
  const { t } = useTranslation(language)
  const [showAuthModal, setShowAuthModal] = useState(false)
  const [showMobileSearch, setShowMobileSearch] = useState(false)
  const [showUserMenu, setShowUserMenu] = useState(false)
  const [user, setUser] = useState(propUser || null)
  const [unreadNotifications, setUnreadNotifications] = useState(0)
  const [showLocationModal, setShowLocationModal] = useState(false)
  const [selectedLocation, setSelectedLocation] = useState({
    division: null,
    district: null,
    displayText: 'সমগ্র বাংলাদেশ'
  })
  
  // Check if user is logged in and sync with prop
  useEffect(() => {
    if (propUser) {
      setUser(propUser)
    } else {
      const storedUser = localStorage.getItem('user')
      if (storedUser) {
        try {
          setUser(JSON.parse(storedUser))
        } catch (e) {
          console.error('Failed to parse user data:', e)
        }
      }
    }
    
    // Removed forgot password event listener (no longer needed)
  }, [propUser])
  
  // Fetch unread notifications count
  useEffect(() => {
    const fetchUnreadCount = async () => {
      // Double-check authentication before making API call
      const token = localStorage.getItem('auth_token')
      if (!user || !token) {
        setUnreadNotifications(0)
        return
      }
      
      try {
        const response = await apiCall('/notifications/unread-count')
        if (response.success) {
          setUnreadNotifications(response.unread_count || 0)
        }
      } catch (error) {
        // Silently fail - expected when not authenticated
        if (error.message !== 'Unauthenticated') {
          console.error('Error fetching notification count:', error)
        }
        setUnreadNotifications(0)
      }
    }
    
    // Only fetch notifications if user is logged in AND has a valid token
    const token = localStorage.getItem('auth_token')
    if (user && token) {
      fetchUnreadCount()
      
      // Poll every 60 seconds (reduced from 30 to minimize traffic)
      const interval = setInterval(fetchUnreadCount, 60000)
      return () => clearInterval(interval)
    } else {
      // Clear notifications when not authenticated
      setUnreadNotifications(0)
    }
  }, [user])
  
  const handleLogout = () => {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
    setUser(null)
    window.location.reload()
  }
  
  const handleLocationSelect = (location) => {
    setSelectedLocation(location)
    // You can also trigger a search with the new location here if needed
  }
  
  // Removed switch handlers (no longer needed with unified AuthModal)
  
  return (
    <header className="sticky top-0 bg-teal-700 text-white shadow-lg z-50">
      <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16 sm:h-20">
          {/* Logo */}
          <div className="flex items-center gap-2 sm:gap-3 cursor-pointer flex-shrink-0" onClick={onLogoClick}>
            <div className="w-9 h-9 sm:w-12 sm:h-12 flex items-center justify-center">
              <svg className="w-9 h-9 sm:w-12 sm:h-12" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                {/* Simple car icon in white */}
                <path d="M10 22 L13 18 L16 16 L32 16 L35 18 L38 22 L38 28 L36 30 L12 30 L10 28 Z" 
                      fill="white"/>
                {/* Windshield */}
                <path d="M17 16 L19 12 L29 12 L31 16 Z" 
                      fill="white" opacity="0.8"/>
                {/* Windows */}
                <rect x="18" y="13" width="5" height="4" rx="1" fill="rgba(13, 148, 136, 0.4)"/>
                <rect x="25" y="13" width="5" height="4" rx="1" fill="rgba(13, 148, 136, 0.4)"/>
                {/* Wheels */}
                <circle cx="16" cy="30" r="4" fill="#1f2937"/>
                <circle cx="16" cy="30" r="2" fill="#6b7280"/>
                <circle cx="32" cy="30" r="4" fill="#1f2937"/>
                <circle cx="32" cy="30" r="2" fill="#6b7280"/>
                {/* Headlights */}
                <circle cx="11" cy="24" r="1.5" fill="#fbbf24"/>
                <circle cx="37" cy="24" r="1.5" fill="#fbbf24"/>
              </svg>
            </div>
            <div className="hidden sm:block">
              <h1 className="text-xl font-black text-white leading-tight">গাড়ি কিনুন</h1>
              <p className="text-[10px] text-teal-100 font-medium leading-tight">Buy & Sell Cars</p>
            </div>
            <div className="sm:hidden">
              <h1 className="text-base font-black text-white leading-tight">গাড়ি কিনুন</h1>
              <p className="text-[9px] text-teal-100 font-medium leading-tight">Buy & Sell Cars</p>
            </div>
          </div>

          {/* Search Bar */}
          <div className="hidden md:flex flex-1 max-w-2xl mx-8">
            <div className="relative w-full flex items-center gap-2">
              <div className="relative flex-1 cursor-pointer" onClick={onSearchClick}>
                <input
                  type="text"
                  placeholder={t('searchPlaceholder')}
                  className="w-full px-5 py-3 pl-12 pr-24 border-2 border-gray-300 rounded-full focus:outline-none focus:border-teal-500 cursor-pointer"
                  readOnly
                />
                <Search className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
                <button 
                  className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-rose-500 hover:bg-rose-600 text-white px-6 py-2 rounded-full transition-colors font-semibold"
                  onClick={onSearchClick}
                >
                  {t('searchButton')}
                </button>
              </div>
              
              {/* Location Button - Right side of search */}
              <button
                onClick={() => setShowLocationModal(true)}
                className="flex items-center gap-2 px-4 py-3 bg-white text-teal-700 rounded-full hover:bg-gray-50 transition-colors font-semibold shadow-sm border-2 border-gray-300 whitespace-nowrap"
              >
                <MapPin size={20} className="text-teal-600 flex-shrink-0" />
                <span className="max-w-[100px] truncate text-sm">{selectedLocation.displayText}</span>
              </button>
            </div>
          </div>

          {/* Right Actions */}
          <div className="flex items-center gap-2 sm:gap-4">
            {user ? (
              <div className="relative flex items-center gap-2 sm:gap-3">
                {/* Notification Bell */}
                <button
                  onClick={onNotificationsClick}
                  className="relative p-1.5 sm:p-2 bg-white text-teal-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <Bell size={18} className="sm:w-5 sm:h-5" />
                  {unreadNotifications > 0 && (
                    <span className="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                      {unreadNotifications > 9 ? '9+' : unreadNotifications}
                    </span>
                  )}
                </button>
                
                {/* User Menu Button */}
                <button
                  onClick={() => setShowUserMenu(!showUserMenu)}
                  className="flex items-center gap-1.5 sm:gap-2 px-2 sm:px-4 py-1.5 sm:py-2 bg-white text-teal-700 rounded-lg font-semibold shadow-sm hover:bg-gray-50 transition-colors"
                >
                  {user.avatar ? (
                    <img 
                      src={getImageUrl(user.avatar)} 
                      alt={user.name}
                      className="w-6 h-6 sm:w-7 sm:h-7 rounded-full object-cover border-2 border-teal-600"
                      onError={(e) => {
                        e.target.style.display = 'none'
                        e.target.nextSibling.style.display = 'flex'
                      }}
                    />
                  ) : null}
                  {!user.avatar && (
                    <div className="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-teal-600 flex items-center justify-center text-white text-xs sm:text-sm font-bold">
                      {user.name?.charAt(0).toUpperCase()}
                    </div>
                  )}
                  <span className="hidden sm:inline">{user.name}</span>
                  <span className="sm:hidden text-sm">{user.name?.split(' ')[0]}</span>
                  <ChevronDown size={14} className={`hidden sm:inline transition-transform ${showUserMenu ? 'rotate-180' : ''}`} />
                </button>
                
                {/* Dropdown Menu */}
                {showUserMenu && (
                  <>
                    <div 
                      className="fixed inset-0 z-40" 
                      onClick={() => setShowUserMenu(false)}
                    ></div>
                    <div className="absolute top-full right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                      {/* User Info Header */}
                      <div className="px-4 py-3 border-b border-gray-200">
                        <div className="flex items-center gap-3">
                          {user.avatar ? (
                            <img 
                              src={getImageUrl(user.avatar)} 
                              alt={user.name}
                              className="w-10 h-10 rounded-full object-cover border-2 border-teal-600"
                            />
                          ) : (
                            <div className="w-10 h-10 rounded-full bg-teal-600 flex items-center justify-center text-white font-bold">
                              {user.name?.charAt(0).toUpperCase()}
                            </div>
                          )}
                          <div className="flex-1 min-w-0">
                            <p className="font-semibold text-gray-900 truncate">{user.name}</p>
                            <p className="text-xs text-gray-500 truncate">
                              {user.phone || user.email || 'No contact info'}
                            </p>
                          </div>
                        </div>
                      </div>
                      <button 
                        onClick={() => {
                          setShowUserMenu(false)
                          onMyListingsClick?.()
                        }}
                        className="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors text-left"
                      >
                        <Package size={18} className="text-teal-600" />
                        <span className="font-medium">{t('myListings')}</span>
                      </button>
                      <button 
                        onClick={() => {
                          setShowUserMenu(false)
                          onFavoritesClick?.()
                        }}
                        className="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors text-left"
                      >
                        <Heart size={18} className="text-red-500" />
                        <span className="font-medium">{t('favorites')}</span>
                      </button>
                      <button 
                        onClick={() => {
                          setShowUserMenu(false)
                          onProfileClick?.()
                        }}
                        className="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors text-left"
                      >
                        {user.avatar ? (
                          <img 
                            src={getImageUrl(user.avatar)} 
                            alt={user.name}
                            className="w-5 h-5 rounded-full object-cover"
                            onError={(e) => {
                              e.target.style.display = 'none'
                              e.target.nextSibling.style.display = 'flex'
                            }}
                          />
                        ) : null}
                        {!user.avatar && (
                          <Settings size={18} className="text-teal-600" />
                        )}
                        <span className="font-medium">{t('profile')}</span>
                      </button>
                      <div className="border-t border-gray-200 my-2"></div>
                      <button 
                        onClick={() => {
                          setShowUserMenu(false)
                          handleLogout()
                        }}
                        className="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 transition-colors text-left"
                      >
                        <LogOut size={18} />
                        <span className="font-medium">{t('logout')}</span>
                      </button>
                    </div>
                  </>
                )}
              </div>
            ) : (
              <button 
                onClick={() => setShowAuthModal(true)}
                className="flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-white hover:bg-gray-100 text-teal-700 rounded-lg font-semibold transition-colors shadow-sm text-sm sm:text-base"
              >
                <User size={16} className="sm:w-[18px] sm:h-[18px]" />
                <span className="hidden sm:inline">{t('login')}</span>
                <span className="sm:hidden">লগ ইন</span>
              </button>
            )}
            <button 
              onClick={() => {
                if (user) {
                  onCreateListing()
                } else {
                  setShowAuthModal(true)
                }
              }}
              className="flex items-center gap-1 sm:gap-2 px-2 sm:px-5 py-1.5 sm:py-2 bg-white hover:bg-gray-100 text-teal-700 rounded-lg font-bold transition-colors shadow-sm text-xs sm:text-base whitespace-nowrap"
            >
              <Plus size={16} className="sm:w-[18px] sm:h-[18px] flex-shrink-0" />
              <span className="hidden sm:inline">{t('sellYourCar')}</span>
              <span className="sm:hidden leading-tight">আপনার গাড়ি<br/>বিক্রি করুন</span>
            </button>
          </div>
        </div>

        {/* Mobile Search */}
        <div className="md:hidden pb-3">
          <div className="flex items-center gap-2">
            <div className="relative flex-1 cursor-pointer" onClick={onSearchClick}>
              <input
                type="text"
                placeholder={t('searchPlaceholder')}
                className="w-full px-4 py-2.5 pl-11 pr-20 bg-white border-2 border-white/30 rounded-full focus:outline-none focus:border-white focus:shadow-lg cursor-pointer text-gray-700 placeholder-gray-400 text-sm"
                readOnly
              />
              <Search className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" size={18} />
              <button 
                className="absolute right-1.5 top-1/2 transform -translate-y-1/2 bg-rose-500 hover:bg-rose-600 text-white px-5 py-1.5 rounded-full transition-colors font-semibold text-sm shadow-md active:scale-95"
                onClick={onSearchClick}
              >
                খুঁজুন
              </button>
            </div>
            
            {/* Mobile Location Button */}
            <button
              onClick={() => setShowLocationModal(true)}
              className="flex items-center justify-center p-2.5 bg-white text-teal-700 rounded-full hover:bg-gray-50 transition-colors shadow-md border-2 border-white/30 flex-shrink-0"
            >
              <MapPin size={20} className="text-teal-600" />
            </button>
          </div>
        </div>
      </div>
      
      {/* Auth Modal */}
      <AuthModal 
        isOpen={showAuthModal}
        onClose={() => setShowAuthModal(false)}
      />
      
      {/* Location Modal */}
      <LocationModal 
        isOpen={showLocationModal} 
        onClose={() => setShowLocationModal(false)} 
        onSelectLocation={handleLocationSelect}
        currentLocation={selectedLocation}
      />
    </header>
  )
}
