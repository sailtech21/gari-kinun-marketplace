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
import MyListings from './components/pages/MyListings'
import Profile from './components/pages/Profile'
import Favorites from './components/pages/Favorites'
import BecomeDealerPage from './components/pages/BecomeDealerPage'
import NotificationsPage from './components/pages/NotificationsPage'
import EmailVerificationBanner from './components/EmailVerificationBanner'

function App() {
  const [currentPage, setCurrentPage] = useState('home') // 'home', 'listing', 'search', 'create', 'mylistings', 'profile', 'favorites', 'becomedealer', 'notifications'
  const [selectedListing, setSelectedListing] = useState(null)
  const [selectedCategory, setSelectedCategory] = useState(null)
  const [selectedBrand, setSelectedBrand] = useState(null)
  const [user, setUser] = useState(null)
  const [showEmailBanner, setShowEmailBanner] = useState(true)
  
  // Check if user is logged in
  useEffect(() => {
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
      try {
        setUser(JSON.parse(storedUser))
      } catch (e) {
        console.error('Failed to parse user data:', e)
      }
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
  
  const handleEditListing = (listing) => {
    setSelectedListing(listing)
    setCurrentPage('create')
    window.scrollTo(0, 0)
  }
  
  return (
    <>
      <SEO />
      <div className="min-h-screen bg-gradient-to-b from-white via-gray-50 to-white">
        {currentPage === 'create' ? (
          <CreateListing 
            onBack={goToHome}
            onSuccess={goToHome}
            editingListing={selectedListing}
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
        ) : currentPage === 'notifications' ? (
          <NotificationsPage 
            onBack={goToHome}
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
            {user && showEmailBanner && <EmailVerificationBanner user={user} onClose={() => setShowEmailBanner(false)} />}
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
            {user && showEmailBanner && <EmailVerificationBanner user={user} onClose={() => setShowEmailBanner(false)} />}
            <main>
              <Hero onSearch={goToSearch} />
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
