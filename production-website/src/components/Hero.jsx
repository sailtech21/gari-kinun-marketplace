import { MapPin, Shield, TrendingUp, BadgeCheck, Sparkles } from 'lucide-react'
import { useState, useEffect } from 'react'
import { apiCall } from '../config'
import AboutModal from './modals/AboutModal'
import { useSettings } from '../contexts/SettingsContext'
import { useTranslation } from '../utils/translations'

export default function Hero({ onSearch }) {
  const { language } = useSettings()
  const { t } = useTranslation(language)
  const [locations, setLocations] = useState([])
  const [loading, setLoading] = useState(true)
  const [showAboutModal, setShowAboutModal] = useState(false)
  const [heroData, setHeroData] = useState({
    main_heading: t('heroTitle'),
    sub_heading: t('heroDescription'),
    cta_text: 'Start Searching',
    cta_link: '/listings',
    background: '',
    enabled: true
  })

  useEffect(() => {
    // Fetch hero section data from CMS
    const fetchHeroData = async () => {
      try {
        const response = await apiCall('/cms/hero-section')
        if (response.success && response.hero) {
          setHeroData(response.hero)
        }
      } catch (error) {
        console.error('Failed to fetch hero data:', error)
        // Keep using default/translation values on error
      }
    }

    fetchHeroData()
  }, [])

  useEffect(() => {
    // Fetch locations from API
    const fetchLocations = async () => {
      try {
        const response = await apiCall('/locations')
        if (response.success && response.data) {
          setLocations(response.data)
        }
      } catch (error) {
        console.error('Failed to fetch locations:', error)
      } finally {
        setLoading(false)
      }
    }

    fetchLocations()
  }, [])

  return (
    <section 
      className="relative bg-gradient-to-br from-amber-50 via-orange-50 to-amber-100 overflow-hidden"
      style={heroData.background ? {
        backgroundImage: `url('${heroData.background}')`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundBlend: 'overlay'
      } : {}}
    >
      {/* Animated Background Elements - only show if no custom background */}
      {!heroData.background && (
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          <div className="absolute top-20 left-10 w-72 h-72 bg-teal-200 rounded-full blur-3xl opacity-20 animate-pulse"></div>
          <div className="absolute bottom-20 right-10 w-96 h-96 bg-orange-200 rounded-full blur-3xl opacity-20 animate-pulse" style={{animationDelay: '1s'}}></div>
        </div>
      )}

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-24 relative">
        <div className="grid md:grid-cols-2 gap-8 sm:gap-12 lg:gap-16 items-center">
          {/* Left Content */}
          <div className="space-y-4 sm:space-y-6 lg:space-y-8">
            <div className="inline-block">
              <span 
                onClick={() => setShowAboutModal(true)}
                className="bg-teal-700 text-white text-xs sm:text-sm font-semibold sm:font-bold px-3 py-1.5 sm:px-5 sm:py-2.5 rounded-full shadow-lg flex items-center gap-1.5 sm:gap-2 cursor-pointer hover:bg-teal-800 transition-colors"
              >
                <Sparkles size={14} className="sm:w-4 sm:h-4 animate-spin" style={{animationDuration: '3s'}} />
                {t('heroSubtitle')}
              </span>
            </div>

            <h1 className="text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-black leading-tight">
              <span className="text-gray-900 dark:text-white">
                {heroData.main_heading || t('heroTitle')}
              </span>
            </h1>

            <p className="text-sm sm:text-base md:text-lg lg:text-xl text-gray-700 dark:text-gray-300 leading-relaxed">
              {heroData.sub_heading || t('heroDescription')}
            </p>

            {/* CTA Button */}
            {heroData.cta_text && heroData.cta_link && (
              <div className="pt-4">
                <a 
                  href={heroData.cta_link} 
                  className="inline-block bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-bold px-8 py-4 rounded-full shadow-xl hover:shadow-2xl transition-all transform hover:scale-105"
                >
                  {heroData.cta_text}
                </a>
              </div>
            )}
          </div>

          {/* Right Content */}
          <div className="relative">
            {/* Animated Vehicles Illustration */}
            <div className="relative">
              {/* Professional Clean Illustration */}
              <div className="relative transform hover:scale-105 transition-transform duration-500 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 shadow-xl sm:shadow-2xl">
                <svg className="w-full h-auto relative z-10" viewBox="0 0 800 500" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <defs>
                    {/* Professional Gradients */}
                    <linearGradient id="carGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" style={{stopColor: '#0f766e'}} />
                      <stop offset="100%" style={{stopColor: '#14b8a6'}} />
                    </linearGradient>
                    
                    <linearGradient id="bikeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" style={{stopColor: '#0f766e'}} />
                      <stop offset="100%" style={{stopColor: '#14b8a6'}} />
                    </linearGradient>
                    
                    <linearGradient id="roadGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                      <stop offset="0%" style={{stopColor: '#94a3b8'}} />
                      <stop offset="100%" style={{stopColor: '#64748b'}} />
                    </linearGradient>
                  </defs>
                  
                  {/* Background - Sky */}
                  <rect x="0" y="0" width="800" height="350" fill="#dbeafe"/>
                  
                  {/* Road */}
                  <ellipse cx="400" cy="450" rx="380" ry="60" fill="url(#roadGradient)" opacity="0.3"/>
                  
                  {/* Car - Modern Simple Design */}
                  <g>
                    {/* Car Shadow */}
                    <ellipse cx="520" cy="380" rx="140" ry="20" fill="#000000" opacity="0.15"/>
                    
                    {/* Car Body */}
                    <rect x="400" y="280" width="240" height="80" rx="12" fill="url(#carGradient)" stroke="#0f766e" strokeWidth="3"/>
                    
                    {/* Car Roof */}
                    <path d="M 440 220 L 490 200 L 570 200 L 620 220 L 620 280 L 440 280 Z" fill="url(#carGradient)" stroke="#0f766e" strokeWidth="3"/>
                    
                    {/* Windows */}
                    <rect x="450" y="215" width="70" height="55" rx="5" fill="#5eead4" opacity="0.6" stroke="#0f766e" strokeWidth="2"/>
                    <rect x="530" y="215" width="70" height="55" rx="5" fill="#5eead4" opacity="0.6" stroke="#0f766e" strokeWidth="2"/>
                    
                    {/* Headlights */}
                    <circle cx="410" cy="300" r="8" fill="#fef08a" stroke="#f59e0b" strokeWidth="2"/>
                    <circle cx="410" cy="300" r="4" fill="#fde047"/>
                    
                    {/* Tail Lights */}
                    <circle cx="630" cy="300" r="8" fill="#fca5a5" stroke="#ef4444" strokeWidth="2"/>
                    
                    {/* Front Wheel */}
                    <circle cx="450" cy="370" r="35" fill="#1e293b" stroke="#475569" strokeWidth="3"/>
                    <circle cx="450" cy="370" r="25" fill="#64748b"/>
                    <circle cx="450" cy="370" r="15" fill="#94a3b8"/>
                    <line x1="450" y1="355" x2="450" y2="385" stroke="#cbd5e1" strokeWidth="3"/>
                    <line x1="435" y1="370" x2="465" y2="370" stroke="#cbd5e1" strokeWidth="3"/>
                    
                    {/* Rear Wheel */}
                    <circle cx="590" cy="370" r="35" fill="#1e293b" stroke="#475569" strokeWidth="3"/>
                    <circle cx="590" cy="370" r="25" fill="#64748b"/>
                    <circle cx="590" cy="370" r="15" fill="#94a3b8"/>
                    <line x1="590" y1="355" x2="590" y2="385" stroke="#cbd5e1" strokeWidth="3"/>
                    <line x1="575" y1="370" x2="605" y2="370" stroke="#cbd5e1" strokeWidth="3"/>
                    
                    {/* Car Details */}
                    <rect x="415" y="310" width="20" height="15" rx="2" fill="#0f172a" opacity="0.7"/>
                    <line x1="520" y1="220" x2="520" y2="360" stroke="#0f766e" strokeWidth="2"/>
                  </g>
                  
                  {/* Motorcycle - Modern Simple Design */}
                  <g>
                    {/* Bike Shadow */}
                    <ellipse cx="240" cy="380" rx="100" ry="15" fill="#000000" opacity="0.15"/>
                    
                    {/* Bike Frame */}
                    <line x1="180" y1="280" x2="220" y2="320" stroke="url(#bikeGradient)" strokeWidth="10" strokeLinecap="round"/>
                    <line x1="220" y1="320" x2="300" y2="320" stroke="url(#bikeGradient)" strokeWidth="10" strokeLinecap="round"/>
                    <line x1="300" y1="320" x2="320" y2="280" stroke="url(#bikeGradient)" strokeWidth="10" strokeLinecap="round"/>
                    
                    {/* Fuel Tank */}
                    <ellipse cx="260" cy="290" rx="45" ry="28" fill="url(#bikeGradient)" stroke="#be123c" strokeWidth="3"/>
                    <ellipse cx="260" cy="285" rx="30" ry="18" fill="#ffffff" opacity="0.3"/>
                    
                    {/* Seat */}
                    <ellipse cx="305" cy="295" rx="30" ry="15" fill="#1e293b" stroke="#0f172a" strokeWidth="2"/>
                    
                    {/* Handlebars */}
                    <line x1="160" y1="270" x2="200" y2="270" stroke="#475569" strokeWidth="5" strokeLinecap="round"/>
                    <circle cx="160" cy="270" r="5" fill="#64748b"/>
                    <circle cx="200" cy="270" r="5" fill="#64748b"/>
                    
                    {/* Headlight */}
                    <circle cx="180" cy="265" r="10" fill="#fef08a" stroke="#f59e0b" strokeWidth="2"/>
                    <circle cx="180" cy="265" r="5" fill="#fde047"/>
                    
                    {/* Tail Light */}
                    <ellipse cx="325" cy="285" rx="6" ry="4" fill="#fca5a5" stroke="#ef4444" strokeWidth="2"/>
                    
                    {/* Front Wheel */}
                    <circle cx="190" cy="360" r="30" fill="#1e293b" stroke="#475569" strokeWidth="3"/>
                    <circle cx="190" cy="360" r="22" fill="#f97316"/>
                    <circle cx="190" cy="360" r="14" fill="#ea580c"/>
                    <line x1="190" y1="345" x2="190" y2="375" stroke="#fed7aa" strokeWidth="2"/>
                    <line x1="175" y1="360" x2="205" y2="360" stroke="#fed7aa" strokeWidth="2"/>
                    <line x1="180" y1="350" x2="200" y2="370" stroke="#fed7aa" strokeWidth="2"/>
                    <line x1="180" y1="370" x2="200" y2="350" stroke="#fed7aa" strokeWidth="2"/>
                    
                    {/* Rear Wheel */}
                    <circle cx="310" cy="360" r="30" fill="#1e293b" stroke="#475569" strokeWidth="3"/>
                    <circle cx="310" cy="360" r="22" fill="#f97316"/>
                    <circle cx="310" cy="360" r="14" fill="#ea580c"/>
                    <line x1="310" y1="345" x2="310" y2="375" stroke="#fed7aa" strokeWidth="2"/>
                    <line x1="295" y1="360" x2="325" y2="360" stroke="#fed7aa" strokeWidth="2"/>
                    <line x1="300" y1="350" x2="320" y2="370" stroke="#fed7aa" strokeWidth="2"/>
                    <line x1="300" y1="370" x2="320" y2="350" stroke="#fed7aa" strokeWidth="2"/>
                    
                    {/* Fork */}
                    <line x1="180" y1="280" x2="190" y2="330" stroke="#64748b" strokeWidth="6" strokeLinecap="round"/>
                    
                    {/* Rear Suspension */}
                    <line x1="320" y1="280" x2="310" y2="330" stroke="#64748b" strokeWidth="6" strokeLinecap="round"/>
                    
                    {/* Exhaust */}
                    <path d="M 240 325 Q 280 330 310 325" stroke="#64748b" strokeWidth="5" strokeLinecap="round" fill="none"/>
                    <ellipse cx="310" cy="325" rx="8" ry="6" fill="#475569" stroke="#1e293b" strokeWidth="2"/>
                  </g>
                </svg>
              </div>
            </div>

            {/* Floating Cards */}
            <div className="absolute -top-3 -right-3 sm:-top-5 sm:-right-5 bg-white rounded-xl lg:rounded-2xl shadow-xl lg:shadow-2xl p-2 sm:p-3 lg:p-5 border border-teal-100 lg:border-2">
              <div className="flex items-center gap-2 sm:gap-3">
                <div className="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-teal-400 to-teal-600 rounded-lg lg:rounded-xl flex items-center justify-center shadow-lg">
                  <Shield className="text-white" size={16} />
                </div>
                <div>
                  <p className="text-gray-600 text-[10px] sm:text-xs lg:text-sm font-semibold">বিশ্বস্ত বিক্রেতা</p>
                  <p className="text-base sm:text-xl lg:text-3xl font-black text-teal-700">১০,০০০+</p>
                </div>
              </div>
            </div>

            <div className="absolute -bottom-3 -left-3 sm:-bottom-5 sm:-left-5 bg-white rounded-xl lg:rounded-2xl shadow-xl lg:shadow-2xl p-2 sm:p-3 lg:p-5 border border-orange-100 lg:border-2">
              <div className="flex items-center gap-2 sm:gap-3">
                <div className="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg lg:rounded-xl flex items-center justify-center shadow-lg">
                  <TrendingUp className="text-white" size={16} />
                </div>
                <div>
                  <p className="text-gray-600 text-[10px] sm:text-xs lg:text-sm font-semibold">সক্রিয় বিজ্ঞাপন</p>
                  <p className="text-base sm:text-xl lg:text-3xl font-black text-red-500">৫,০০০+</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* About Modal */}
      <AboutModal 
        isOpen={showAboutModal} 
        onClose={() => setShowAboutModal(false)} 
      />
    </section>
  )
}
