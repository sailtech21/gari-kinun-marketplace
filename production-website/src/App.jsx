import { useState, useEffect } from 'react'
import SEO from './components/SEO'
import Header from './components/Header'
import Hero from './components/Hero'
import PopularCategories from './components/PopularCategories'
import FeaturedListings from './components/FeaturedListings'
import TrendingListings from './components/TrendingListings'
import PopularBrands from './components/PopularBrands'
import TrustStats from './components/TrustStats'
import Testimonials from './components/Testimonials'
import DownloadApp from './components/sections/DownloadApp'
import Footer from './components/Footer'
import ListingDetail from './components/pages/ListingDetail'
import SearchPage from './components/pages/SearchPage'
import CreateListing from './components/pages/CreateListing'
import PostAd from './components/pages/PostAd'
import MyListings from './components/pages/MyListings'
import Profile from './components/pages/Profile'
import Favorites from './components/pages/Favorites'
import BecomeDealerPage from './components/pages/BecomeDealerPage'
import DealerProfilePage from './components/pages/DealerProfilePage'
import NotificationsPage from './components/pages/NotificationsPage'
import NotificationSettings from './components/pages/NotificationSettings'
import EmailVerificationBanner from './components/EmailVerificationBanner'
import BannerSlider from './components/BannerSlider'
import { API_BASE_URL } from './config'

function App() {
  const [currentPage, setCurrentPage] = useState('home') // 'home', 'listing', 'search', 'create', 'mylistings', 'profile', 'favorites', 'becomedealer', 'dealerprofile', 'notifications'
  const [selectedListing, setSelectedListing] = useState(null)
  const [selectedCategory, setSelectedCategory] = useState(null)
  const [selectedBrand, setSelectedBrand] = useState(null)
  const [selectedDealerId, setSelectedDealerId] = useState(null)
  const [user, setUser] = useState(null)
  const [showEmailBanner, setShowEmailBanner] = useState(true)
  
  // Fetch fresh user data from API
  const refreshUserData = async () => {
    const token = localStorage.getItem('auth_token')
    if (!token) return

    try {
      const response = await fetch(`${API_BASE_URL}/user`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })
      
      if (response.ok) {
        const data = await response.json()
        if (data.success && data.data) {
          const updatedUser = data.data
          setUser(updatedUser)
          localStorage.setItem('user', JSON.stringify(updatedUser))
          
          // Auto-hide banner if email is verified
          if (updatedUser.email_verified_at) {
            setShowEmailBanner(false)
          }
        }
      }
    } catch (error) {
      console.error('Failed to refresh user data:', error)
    }
  }
  
  // Check if user is logged in and refresh data
  useEffect(() => {
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
      try {
        setUser(JSON.parse(storedUser))
      } catch (e) {
        console.error('Failed to parse user data:', e)
      }
    }
    
    // Refresh user data on mount
    refreshUserData()
    
    // Refresh when page becomes visible (user returns to tab)
    const handleVisibilityChange = () => {
      if (!document.hidden) {
        refreshUserData()
      }
    }
    
    document.addEventListener('visibilitychange', handleVisibilityChange)
    
    return () => {
      document.removeEventListener('visibilitychange', handleVisibilityChange)
    }
  }, [])
  
  const viewListing = (listing) => {
    setSelectedListing(listing)
    setCurrentPage('listing')
    window.scrollTo(0, 0)
  }
  
  const goToSearch = (category = null) => {
    setSelectedCategory(category)
    setCurrentPage('search')
    window.scrollTo(0, 0)
  }
  
  const goToCategorySearch = (categoryId) => {
    setSelectedCategory(categoryId)
    setSelectedBrand(null)
    setCurrentPage('search')
    window.scrollTo(0, 0)
  }
  
  const goToBrandSearch = (brandName) => {
    setSelectedBrand(brandName)
    setSelectedCategory(null)
    setCurrentPage('search')
    window.scrollTo(0, 0)
  }
  
  const goToHome = () => {
    setCurrentPage('home')
    setSelectedListing(null)
    setSelectedCategory(null)
    setSelectedBrand(null)
    window.scrollTo(0, 0)
  }
  
  const goToCreateListing = () => {
    // Check if user is logged in
    if (!user) {
      alert('বিজ্ঞাপন দিতে প্রথমে লগইন করুন')
      return
    }
    
    // Check if profile is complete
    const isProfileComplete = user.phone && user.phone.trim() !== ''
    
    if (!isProfileComplete) {
      const confirmComplete = window.confirm(
        'বিজ্ঞাপন দেওয়ার আগে আপনার প্রোফাইল সম্পূর্ণ করুন।\n\n' +
        'প্রয়োজনীয় তথ্য:\n' +
        '• ফোন নম্বর\n' +
        '• ঠিকানা (ঐচ্ছিক)\n\n' +
        'এখনই প্রোফাইল সম্পূর্ণ করতে চান?'
      )
      
      if (confirmComplete) {
        setCurrentPage('profile')
        window.scrollTo(0, 0)
      }
      return
    }
    
    // Profile is complete, proceed to create listing
    setCurrentPage('create')
    setSelectedListing(null)
    window.scrollTo(0, 0)
  }
  
  const goToMyListings = () => {
    setCurrentPage('mylistings')
    window.scrollTo(0, 0)
  }
  
  const goToProfile = () => {
    setCurrentPage('profile')
    window.scrollTo(0, 0)
  }
  
  const goToFavorites = () => {
    setCurrentPage('favorites')
    window.scrollTo(0, 0)
  }
  
  const goToBecomeDealer = () => {
    setCurrentPage('becomedealer')
    window.scrollTo(0, 0)
  }
  
  const goToNotifications = () => {
    setCurrentPage('notifications')
    window.scrollTo(0, 0)
  }
  
  const goToNotificationSettings = () => {
    setCurrentPage('notificationsettings')
    window.scrollTo(0, 0)
  }
  
  const goToDealerProfile = (dealerId) => {
    setSelectedDealerId(dealerId)
    setCurrentPage('dealerprofile')
    window.scrollTo(0, 0)
  }
  
  const handleEditListing = (listing) => {
    setSelectedListing(listing)
    setCurrentPage('create')
    window.scrollTo(0, 0)
  }
  
  return (
    <>
      <SEO />
      <div className="min-h-screen bg-gradient-to-b from-white via-gray-50 to-white dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        {currentPage === 'create' ? (
          <PostAd 
            onBack={goToHome}
            user={user}
          />
        ) : currentPage === 'mylistings' ? (
          <MyListings 
            onBack={goToHome}
            onEdit={handleEditListing}
            onCreate={goToCreateListing}
            onViewListing={viewListing}
          />
        ) : currentPage === 'profile' ? (
          <Profile 
            onBack={goToHome}
            user={user}
            onBecomeDealer={goToBecomeDealer}
            onViewDealerProfile={goToDealerProfile}
          />
        ) : currentPage === 'favorites' ? (
          <Favorites 
            onBack={goToHome}
            onViewListing={viewListing}
          />
        ) : currentPage === 'becomedealer' ? (
          <BecomeDealerPage 
            onBack={goToHome}
          />
        ) : currentPage === 'dealerprofile' ? (
          <DealerProfilePage 
            onBack={goToHome}
            onListingClick={viewListing}
            dealerId={selectedDealerId}
          />
        ) : currentPage === 'notifications' ? (
          <NotificationsPage 
            onBack={goToHome}
            onSettingsClick={goToNotificationSettings}
          />
        ) : currentPage === 'notificationsettings' ? (
          <NotificationSettings 
            onBack={goToNotifications}
          />
        ) : currentPage === 'listing' && selectedListing ? (
          <>
            <ListingDetail 
              listing={selectedListing} 
              onBack={goToHome}
            />
            <Footer 
              onSearchClick={goToSearch} 
              onCategoryClick={goToCategorySearch}
              onHomeClick={goToHome}
              onMyListingsClick={goToMyListings}
              onProfileClick={goToProfile}
              onFavoritesClick={goToFavorites}
            />
          </>
        ) : currentPage === 'search' ? (
          <>
            <Header 
              onSearchClick={goToHome} 
              onLogoClick={goToHome} 
              onCreateListing={goToCreateListing}
              onMyListingsClick={goToMyListings}
              onProfileClick={goToProfile}
              onFavoritesClick={goToFavorites}
              onNotificationsClick={goToNotifications}
              user={user}
            />
            {user && showEmailBanner && !user.email_verified_at && <EmailVerificationBanner user={user} onClose={() => setShowEmailBanner(false)} onVerified={refreshUserData} />}
            <SearchPage 
              onBack={goToHome}
              onViewListing={viewListing}
              initialCategory={selectedCategory}
              initialBrand={selectedBrand}
            />
            <Footer 
              onSearchClick={goToSearch} 
              onCategoryClick={goToCategorySearch}
              onHomeClick={goToHome}
              onMyListingsClick={goToMyListings}
              onProfileClick={goToProfile}
              onFavoritesClick={goToFavorites}
            />
          </>
        ) : (
          <>
            <Header 
              onSearchClick={goToSearch} 
              onLogoClick={goToHome} 
              onCreateListing={goToCreateListing}
              onMyListingsClick={goToMyListings}
              onProfileClick={goToProfile}
              onFavoritesClick={goToFavorites}
              onNotificationsClick={goToNotifications}
              user={user}
            />
            {user && showEmailBanner && !user.email_verified_at && <EmailVerificationBanner user={user} onClose={() => setShowEmailBanner(false)} onVerified={refreshUserData} />}
            <main>
              <Hero onSearch={goToSearch} />
              <div className="max-w-7xl mx-auto px-4 py-6 sm:py-8">
                <BannerSlider />
              </div>
              <PopularCategories onCategoryClick={goToCategorySearch} onViewAll={goToSearch} />
              <FeaturedListings onViewListing={viewListing} />
              <TrendingListings onViewListing={viewListing} />
              <PopularBrands onBrandClick={goToBrandSearch} onViewAll={goToSearch} />
              <DownloadApp />
              <TrustStats />
              <Testimonials />
            </main>
            <Footer 
              onSearchClick={goToSearch} 
              onCategoryClick={goToCategorySearch}
              onHomeClick={goToHome}
              onMyListingsClick={goToMyListings}
              onProfileClick={goToProfile}
              onFavoritesClick={goToFavorites}
            />
          </>
        )}
      </div>
    </>
  )
}

export default App
