import { Search, User, Plus, LogOut, Heart, Package, Settings, ChevronDown, Bell } from 'lucide-react'
import { useState, useEffect } from 'react'
import AuthModal from './auth/AuthModal'
import SearchBar from './common/SearchBar'
import { apiCall } from '../config'

export default function Header({ onSearchClick, onLogoClick, onCreateListing, onMyListingsClick, onProfileClick, onFavoritesClick, onNotificationsClick, user: propUser }) {
  const [showAuthModal, setShowAuthModal] = useState(false)
  const [showMobileSearch, setShowMobileSearch] = useState(false)
  const [showUserMenu, setShowUserMenu] = useState(false)
  const [user, setUser] = useState(propUser || null)
  const [unreadNotifications, setUnreadNotifications] = useState(0)
  
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
  
  // Removed switch handlers (no longer needed with unified AuthModal)
  
  return (
    <header className="sticky top-0 bg-teal-700 text-white shadow-md z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <div className="flex items-center gap-3 cursor-pointer" onClick={onLogoClick}>
            <div className="w-12 h-12 flex items-center justify-center">
              <svg className="w-12 h-12" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
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
            <div>
              <h1 className="text-xl font-black text-white leading-tight">গাড়ি কিনুন</h1>
              <p className="text-[10px] text-teal-100 font-medium leading-tight">Buy & Sell Cars</p>
            </div>
          </div>

          {/* Search Bar */}
          <div className="hidden md:flex flex-1 max-w-2xl mx-8">
            <div className="relative w-full cursor-pointer" onClick={onSearchClick}>
              <input
                type="text"
                placeholder="গাড়ি খুঁজুন…"
                className="w-full px-5 py-3 pl-12 pr-24 border-2 border-gray-300 rounded-full focus:outline-none focus:border-teal-500 cursor-pointer"
                readOnly
              />
              <Search className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
              <button 
                className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-rose-500 hover:bg-rose-600 text-white px-6 py-2 rounded-full transition-colors font-semibold"
                onClick={onSearchClick}
              >
                খুঁজুন
              </button>
            </div>
          </div>

          {/* Right Actions */}
          <div className="flex items-center gap-4">
            {user ? (
              <div className="relative flex items-center gap-3">
                {/* Notification Bell */}
                <button
                  onClick={onNotificationsClick}
                  className="relative p-2 bg-white text-teal-700 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <Bell size={20} />
                  {unreadNotifications > 0 && (
                    <span className="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                      {unreadNotifications > 9 ? '9+' : unreadNotifications}
                    </span>
                  )}
                </button>
                
                {/* User Menu Button */}
                <button
                  onClick={() => setShowUserMenu(!showUserMenu)}
                  className="flex items-center gap-2 px-4 py-2 bg-white text-teal-700 rounded-lg font-semibold shadow-sm hover:bg-gray-50 transition-colors"
                >
                  {user.avatar ? (
                    <img 
                      src={`/storage/${user.avatar}`} 
                      alt={user.name}
                      className="w-7 h-7 rounded-full object-cover border-2 border-teal-600"
                    />
                  ) : (
                    <User size={18} className="text-teal-700" />
                  )}
                  <span>{user.name}</span>
                  <ChevronDown size={16} className={`transition-transform ${showUserMenu ? 'rotate-180' : ''}`} />
                </button>
                
                {/* Dropdown Menu */}
                {showUserMenu && (
                  <>
                    <div 
                      className="fixed inset-0 z-40" 
                      onClick={() => setShowUserMenu(false)}
                    ></div>
                    <div className="absolute top-full right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                      <button 
                        onClick={() => {
                          setShowUserMenu(false)
                          onMyListingsClick?.()
                        }}
                        className="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors text-left"
                      >
                        <Package size={18} className="text-teal-600" />
                        <span className="font-medium">আমার বিজ্ঞাপন</span>
                      </button>
                      <button 
                        onClick={() => {
                          setShowUserMenu(false)
                          onFavoritesClick?.()
                        }}
                        className="w-full flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors text-left"
                      >
                        <Heart size={18} className="text-red-500" />
                        <span className="font-medium">প্রিয় তালিকা</span>
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
                            src={`/storage/${user.avatar}`} 
                            alt={user.name}
                            className="w-5 h-5 rounded-full object-cover"
                          />
                        ) : (
                          <Settings size={18} className="text-teal-600" />
                        )}
                        <span className="font-medium">প্রোফাইল সেটিংস</span>
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
                        <span className="font-medium">লগআউট</span>
                      </button>
                    </div>
                  </>
                )}
              </div>
            ) : (
              <button 
                onClick={() => setShowAuthModal(true)}
                className="flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-100 text-teal-700 rounded-lg font-semibold transition-colors shadow-sm"
              >
                <User size={18} />
                <span>লগইন</span>
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
              className="flex items-center gap-2 px-5 py-2 bg-white hover:bg-gray-100 text-teal-700 rounded-lg font-bold transition-colors shadow-sm"
            >
              <Plus size={18} />
              <span>বিজ্ঞাপন দিন</span>
            </button>
          </div>
        </div>

        {/* Mobile Search */}
        <div className="md:hidden pb-4">
          <div className="relative cursor-pointer" onClick={onSearchClick}>
            <input
              type="text"
              placeholder="গাড়ি খুঁজুন…"
              className="w-full px-5 py-3 pl-12 pr-24 border-2 border-gray-300 rounded-full focus:outline-none focus:border-teal-500 cursor-pointer"
              readOnly
            />
            <Search className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
            <button 
              className="absolute right-2 top-1/2 transform -translate-y-1/2 bg-rose-500 hover:bg-rose-600 text-white px-6 py-2 rounded-full transition-colors font-semibold text-sm"
              onClick={onSearchClick}
            >
              খুঁজুন
            </button>
          </div>
        </div>
      </div>
      
      {/* Auth Modal */}
      <AuthModal 
        isOpen={showAuthModal}
        onClose={() => setShowAuthModal(false)}
      />
    </header>
  )
}
