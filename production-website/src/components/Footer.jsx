import { Facebook, Instagram, Youtube, Mail, Phone, MapPin, Send, Sparkles, ArrowRight, Heart } from 'lucide-react'

export default function Footer({ 
  onSearchClick = () => {}, 
  onCategoryClick = () => {},
  onHomeClick = () => {}
}) {
  
  // Handle newsletter subscription
  const handleNewsletterSubmit = (e) => {
    e.preventDefault()
    const email = e.target.email.value
    if (email) {
      alert('ধন্যবাদ! আপনার ইমেইল সফলভাবে সাবস্ক্রাইব হয়েছে।')
      e.target.reset()
    }
  }

  // Quick links handler
  const handleQuickLink = (linkType) => {
    switch(linkType) {
      case 'about':
        window.scrollTo({ top: 0, behavior: 'smooth' })
        break
      case 'sell':
        alert('গাড়ি বিক্রয়ের জন্য দয়া করে লগইন করুন')
        break
      case 'buy':
        onSearchClick()
        break
      case 'terms':
      case 'privacy':
        alert('এই পেজটি শীঘ্রই আসছে')
        break
      default:
        break
    }
  }

  // Category links with IDs (based on typical category structure)
  const categories = [
    { name: 'প্রাইভেট কার', id: 1 },
    { name: 'মোটরসাইকেল', id: 2 },
    { name: 'কমার্শিয়াল গাড়ি', id: 3 },
    { name: 'রিকশা ও ভ্যান', id: 4 },
    { name: 'স্পেয়ার পার্টস', id: 5 }
  ]

  return (
    <footer className="relative bg-gradient-to-br from-gray-900 via-slate-900 to-gray-900 text-gray-300 overflow-hidden">
      {/* Animated Background Orbs */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-0 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl animate-pulse" style={{animationDelay: '1s'}}></div>
        <div className="absolute top-1/2 right-1/3 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl animate-pulse" style={{animationDelay: '2s'}}></div>
      </div>

      <div className="relative z-10">
        {/* Newsletter Section */}
        <div className="border-b border-gray-800/50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 md:py-12">
            <div className="bg-teal-700 rounded-2xl sm:rounded-3xl p-6 sm:p-8 md:p-12 relative overflow-hidden shadow-2xl">
              {/* Decorative Elements */}
              <div className="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
              <div className="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
              
              <div className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6 sm:gap-8">
                <div className="flex-1 text-center md:text-left">
                  <div className="flex items-center gap-2 justify-center md:justify-start mb-2 sm:mb-3">
                    <Sparkles className="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 text-yellow-300 animate-pulse" />
                    <span className="text-yellow-300 font-bold text-xs sm:text-sm uppercase tracking-wider">নিউজলেটার</span>
                  </div>
                  <h3 className="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-white mb-2 sm:mb-3">
                    সর্বশেষ আপডেট পান
                  </h3>
                  <p className="text-white/90 text-sm sm:text-base md:text-lg font-medium">
                    নতুন গাড়ির বিজ্ঞাপন এবং বিশেষ অফার সরাসরি আপনার ইমেইলে
                  </p>
                </div>
                
                <div className="flex-shrink-0 w-full md:w-auto">
                  <form onSubmit={handleNewsletterSubmit} className="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <input 
                      type="email" 
                      name="email"
                      placeholder="আপনার ইমেইল এন্টার করুন"
                      required
                      className="px-4 sm:px-6 py-3 sm:py-4 rounded-xl sm:rounded-2xl bg-white/95 text-gray-900 placeholder-gray-500 border-2 border-transparent focus:border-white outline-none w-full md:w-80 font-semibold shadow-xl text-sm sm:text-base"
                    />
                    <button 
                      type="submit"
                      className="px-6 sm:px-8 py-3 sm:py-4 bg-gray-900 hover:bg-black active:bg-gray-800 text-white rounded-xl sm:rounded-2xl font-bold flex items-center justify-center gap-2 transition-all hover:scale-105 active:scale-95 shadow-xl touch-manipulation"
                    >
                      <Send size={18} className="sm:hidden" />
                      <Send size={20} className="hidden sm:block" />
                      <span>সাবস্ক্রাইব</span>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Main Footer Content */}
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 md:py-16">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 md:gap-12">
            {/* Column 1: Brand */}
            <div className="space-y-4 sm:space-y-6">
              <div className="space-y-3 sm:space-y-4">
                <div className="flex items-center gap-2 sm:gap-3">
                  <div className="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white rounded-xl sm:rounded-2xl flex items-center justify-center shadow-xl rotate-3 hover:rotate-6 transition-transform">
                    <svg className="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 15l7-7 7 7" />
                    </svg>
                  </div>
                  <h3 className="text-xl sm:text-2xl md:text-3xl font-black bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                    গাড়ি কিনুন
                  </h3>
                </div>
                <p className="text-gray-400 text-sm sm:text-base leading-relaxed font-medium">
                  বাংলাদেশের সবচেয়ে বিশ্বস্ত গাড়ি কেনা-বেচার মাধ্যম। 
                  নিরাপদে গাড়ি কিনুন বা বিক্রি করুন।
                </p>
              </div>
              
              {/* Social Media */}
              <div>
                <p className="text-xs sm:text-sm font-bold text-gray-400 mb-3 sm:mb-4 uppercase tracking-wider">আমাদের সাথে যুক্ত হন</p>
                <div className="flex gap-2 sm:gap-3">
                  <a 
                    href="https://www.facebook.com/garikinun" 
                    target="_blank"
                    rel="noopener noreferrer"
                    className="group w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 rounded-lg sm:rounded-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-lg hover:shadow-blue-500/50 touch-manipulation"
                    aria-label="Facebook"
                  >
                    <Facebook size={18} className="text-white sm:hidden" />
                    <Facebook size={20} className="text-white hidden sm:block" />
                  </a>
                  <a 
                    href="https://www.instagram.com/garikinun" 
                    target="_blank"
                    rel="noopener noreferrer"
                    className="group w-10 h-10 sm:w-12 sm:h-12 bg-teal-600 hover:bg-teal-700 rounded-lg sm:rounded-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-lg touch-manipulation"
                    aria-label="Instagram"
                  >
                    <Instagram size={18} className="text-white sm:hidden" />
                    <Instagram size={20} className="text-white hidden sm:block" />
                  </a>
                  <a 
                    href="https://www.youtube.com/@garikinun" 
                    target="_blank"
                    rel="noopener noreferrer"
                    className="group w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 rounded-lg sm:rounded-xl flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-lg hover:shadow-red-500/50 touch-manipulation"
                    aria-label="YouTube"
                  >
                    <Youtube size={18} className="text-white sm:hidden" />
                    <Youtube size={20} className="text-white hidden sm:block" />
                  </a>
                </div>
              </div>
            </div>

            {/* Column 2: Quick Links */}
            <div>
              <h4 className="text-lg sm:text-xl font-black text-white mb-4 sm:mb-6 flex items-center gap-2">
                <div className="w-1 h-5 sm:h-6 bg-teal-600 rounded-full"></div>
                দ্রুত লিংক
              </h4>
              <ul className="space-y-2 sm:space-y-3">
                {[
                  { text: 'আমাদের সম্পর্কে', type: 'about' },
                  { text: 'গাড়ি বিক্রি করুন', type: 'sell' },
                  { text: 'গাড়ি কিনুন', type: 'buy' },
                  { text: 'শর্তাবলী', type: 'terms' },
                  { text: 'গোপনীয়তা নীতি', type: 'privacy' }
                ].map((link, index) => (
                  <li key={index}>
                    <button 
                      onClick={() => handleQuickLink(link.type)}
                      className="group flex items-center gap-2 text-gray-400 hover:text-white transition-all font-medium cursor-pointer text-sm sm:text-base"
                    >
                      <ArrowRight size={14} className="text-teal-600 opacity-0 -ml-5 group-hover:opacity-100 group-hover:ml-0 transition-all sm:hidden" />
                      <ArrowRight size={16} className="text-teal-600 opacity-0 -ml-6 group-hover:opacity-100 group-hover:ml-0 transition-all hidden sm:block" />
                      <span className="group-hover:translate-x-2 transition-transform">{link.text}</span>
                    </button>
                  </li>
                ))}
              </ul>
            </div>

            {/* Column 3: Categories */}
            <div>
              <h4 className="text-lg sm:text-xl font-black text-white mb-4 sm:mb-6 flex items-center gap-2">
                <div className="w-1 h-5 sm:h-6 bg-teal-600 rounded-full"></div>
                ক্যাটাগরি
              </h4>
              <ul className="space-y-2 sm:space-y-3">
                {categories.map((category) => (
                  <li key={category.id}>
                    <button 
                      onClick={() => onCategoryClick(category.id)}
                      className="group flex items-center gap-2 text-gray-400 hover:text-white transition-all font-medium cursor-pointer text-sm sm:text-base"
                    >
                      <ArrowRight size={14} className="text-teal-600 opacity-0 -ml-5 group-hover:opacity-100 group-hover:ml-0 transition-all sm:hidden" />
                      <ArrowRight size={16} className="text-teal-600 opacity-0 -ml-6 group-hover:opacity-100 group-hover:ml-0 transition-all hidden sm:block" />
                      <span className="group-hover:translate-x-2 transition-transform">{category.name}</span>
                    </button>
                  </li>
                ))}
              </ul>
            </div>

            {/* Column 4: Contact */}
            <div>
              <h4 className="text-lg sm:text-xl font-black text-white mb-4 sm:mb-6 flex items-center gap-2">
                <div className="w-1 h-5 sm:h-6 bg-teal-600 rounded-full"></div>
                যোগাযোগ
              </h4>
              <ul className="space-y-3 sm:space-y-5">
                <li>
                  <a 
                    href="https://maps.google.com/?q=Dhanmondi,Dhaka,Bangladesh" 
                    target="_blank"
                    rel="noopener noreferrer"
                    className="group flex items-start gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-white/5 active:bg-white/10 transition-all touch-manipulation"
                  >
                    <div className="w-8 h-8 sm:w-10 sm:h-10 bg-teal-100 text-teal-700 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                      <MapPin size={16} className="text-teal-600 sm:hidden" />
                      <MapPin size={20} className="text-teal-600 hidden sm:block" />
                    </div>
                    <div>
                      <p className="text-xs sm:text-sm text-gray-500 font-medium mb-1">ঠিকানা</p>
                      <p className="text-white font-semibold leading-relaxed text-sm sm:text-base">
                        ধানমন্ডি, ঢাকা-১২০৫<br />বাংলাদেশ
                      </p>
                    </div>
                  </a>
                </li>
                <li>
                  <a href="tel:+8801712345678" className="group flex items-start gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-white/5 active:bg-white/10 transition-all touch-manipulation">
                    <div className="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                      <Phone size={16} className="text-green-400 sm:hidden" />
                      <Phone size={20} className="text-green-400 hidden sm:block" />
                    </div>
                    <div>
                      <p className="text-xs sm:text-sm text-gray-500 font-medium mb-1">ফোন</p>
                      <p className="text-white font-semibold text-sm sm:text-base">+৮৮০ ১৭ ১২৩৪ ৫৬৭৮</p>
                    </div>
                  </a>
                </li>
                <li>
                  <a href="mailto:support@garikinun.com" className="group flex items-start gap-3 sm:gap-4 p-3 sm:p-4 rounded-xl sm:rounded-2xl hover:bg-white/5 active:bg-white/10 transition-all touch-manipulation">
                    <div className="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500/20 to-cyan-600/20 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                      <Mail size={16} className="text-blue-400 sm:hidden" />
                      <Mail size={20} className="text-blue-400 hidden sm:block" />
                    </div>
                    <div>
                      <p className="text-xs sm:text-sm text-gray-500 font-medium mb-1">ইমেইল</p>
                      <p className="text-white font-semibold text-sm sm:text-base">support@garikinun.com</p>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        {/* Bottom Footer */}
        <div className="border-t border-gray-800/50 bg-black/20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div className="flex flex-col md:flex-row items-center justify-between gap-3 sm:gap-4">
              <p className="text-gray-400 text-center md:text-left font-medium flex items-center gap-2 text-xs sm:text-sm">
                © ২০২৬ গাড়ি কিনুন। সর্বস্বত্ব সংরক্ষিত।
                <Heart size={14} className="text-red-500 fill-red-500 animate-pulse sm:hidden" />
                <Heart size={16} className="text-red-500 fill-red-500 animate-pulse hidden sm:block" />
              </p>
              <div className="flex items-center gap-3 sm:gap-4 md:gap-6 text-xs sm:text-sm flex-wrap justify-center">
                <button 
                  onClick={() => handleQuickLink('privacy')}
                  className="text-gray-400 hover:text-white transition-colors font-medium cursor-pointer touch-manipulation"
                >
                  প্রাইভেসি পলিসি
                </button>
                <span className="text-gray-700">•</span>
                <button 
                  onClick={() => handleQuickLink('terms')}
                  className="text-gray-400 hover:text-white transition-colors font-medium cursor-pointer touch-manipulation"
                >
                  শর্তাবলী
                </button>
                <span className="text-gray-700">•</span>
                <button 
                  onClick={onHomeClick}
                  className="text-gray-400 hover:text-white transition-colors font-medium cursor-pointer touch-manipulation"
                >
                  সাহায্য
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  )
}
