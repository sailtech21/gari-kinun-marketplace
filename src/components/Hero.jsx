import { Search, MapPin, Shield, TrendingUp, BadgeCheck, Sparkles } from 'lucide-react'
import { useState, useEffect } from 'react'
import { apiCall } from '../config'
import AboutModal from './modals/AboutModal'

export default function Hero({ onSearch }) {
  const [locations, setLocations] = useState([])
  const [loading, setLoading] = useState(true)
  const [showAboutModal, setShowAboutModal] = useState(false)

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
    <section className="relative bg-gradient-to-br from-amber-50 via-orange-50 to-amber-100 overflow-hidden">
      {/* Animated Background Elements */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-20 left-10 w-72 h-72 bg-teal-200 rounded-full blur-3xl opacity-20 animate-pulse"></div>
        <div className="absolute bottom-20 right-10 w-96 h-96 bg-orange-200 rounded-full blur-3xl opacity-20 animate-pulse" style={{animationDelay: '1s'}}></div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative">
        <div className="grid md:grid-cols-2 gap-16 items-center">
          {/* Left Content */}
          <div className="space-y-8">
            <div className="inline-block animate-bounce">
              <span 
                onClick={() => setShowAboutModal(true)}
                className="bg-teal-700 text-white text-sm font-bold px-5 py-2.5 rounded-full shadow-lg flex items-center gap-2 cursor-pointer hover:bg-teal-800 transition-colors"
              >
                <Sparkles size={16} className="animate-spin" style={{animationDuration: '3s'}} />
                বাংলাদেশের সবচেয়ে বড় গাড়ির বাজার
              </span>
            </div>

            <h1 className="text-6xl md:text-7xl font-black leading-tight">
              <span className="text-gray-900">
                আপনার স্বপ্নের
              </span>
              <br />
              <span className="text-teal-700">গাড়ি খুঁজুন সহজেই</span>
            </h1>

            <p className="text-xl text-gray-700 leading-relaxed">
             নতুন বা পুরাতন, যেকোনো গাড়ি কেনা-বেচা করুন নিরাপদে<br />
              <span className="font-semibold text-gray-900">সারা বাংলাদেশ থেকে হাজারো বিক্রেতা</span>
            </p>

            {/* Search Group */}
            <div className="bg-white rounded-2xl shadow-2xl p-3 border-2 border-gray-100">
              <div className="flex flex-col sm:flex-row gap-3">
                <div className="flex-1 relative">
                  <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                  <input
                    type="text"
                    placeholder="যানবাহনের ধরন, মডেল বা ব্র্যান্ড..."
                    className="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 cursor-pointer text-lg transition-all"
                    onClick={onSearch}
                    readOnly
                  />
                </div>
                <div className="relative">
                  <MapPin className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                  <select 
                    className="pl-12 pr-8 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 cursor-pointer appearance-none min-w-[200px] text-lg transition-all"
                    onClick={onSearch}
                    disabled={loading}
                  >
                    <option value="">
                      {loading ? 'লোড হচ্ছে...' : 'স্থান নির্বাচন'}
                    </option>
                    {locations.slice(0, 10).map((location) => (
                      <option key={location.id} value={location.nameEn}>
                        {location.name} ({location.count})
                      </option>
                    ))}
                  </select>
                </div>
                <button 
                  className="bg-rose-500 hover:bg-rose-600 text-white px-10 py-4 rounded-xl font-bold text-lg flex items-center gap-3 justify-center transition-all transform hover:scale-105 shadow-lg hover:shadow-xl" 
                  onClick={onSearch}
                >
                  <Search size={24} />
                  <span>খুঁজুন</span>
                </button>
              </div>
            </div>
          </div>

          {/* Right Content */}
          <div className="relative">
            {/* Animated Vehicles Illustration */}
            <div className="relative">
              {/* Professional Clean Illustration */}
              <div className="relative transform hover:scale-105 transition-transform duration-500 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-3xl p-8 shadow-2xl">
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
            <div className="absolute -top-5 -right-5 bg-white rounded-2xl shadow-2xl p-5 flex items-center gap-4 border-2 border-teal-100">
              <div className="w-14 h-14 bg-gradient-to-br from-teal-400 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                <Shield className="text-white" size={28} />
              </div>
              <div>
                <p className="text-gray-600 text-sm font-semibold">বিশ্বস্ত বিক্রেতা</p>
                <p className="text-3xl font-black text-teal-700">১০,০০০+</p>
              </div>
            </div>

            <div className="absolute -bottom-5 -left-5 bg-white rounded-2xl shadow-2xl p-5 flex items-center gap-4 border-2 border-orange-100">
              <div className="w-14 h-14 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center shadow-lg">
                <TrendingUp className="text-white" size={28} />
              </div>
              <div>
                <p className="text-gray-600 text-sm font-semibold">সক্রিয় বিজ্ঞাপন</p>
                <p className="text-3xl font-black text-red-500">৫,০০০+</p>
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
