import { Smartphone, Download, Star, QrCode, Search, MessageCircle, Heart, Zap, CheckCircle2, Sparkles } from 'lucide-react'
import Button from '../common/Button'

export default function DownloadApp() {
  const appFeatures = [
    { icon: Search, text: 'সহজ গাড়ি খোঁজা এবং ফিল্টার করা', color: 'from-blue-400 to-cyan-400' },
    { icon: Zap, text: 'তাৎক্ষণিক বিজ্ঞাপন পোস্ট করা', color: 'from-yellow-400 to-orange-400' },
    { icon: MessageCircle, text: 'বিক্রেতার সাথে সরাসরি চ্যাট', color: 'from-green-400 to-emerald-400' },
    { icon: Heart, text: 'প্রিয় তালিকায় গাড়ি সংরক্ষণ', color: 'from-pink-400 to-rose-400' }
  ]
  
  return (
    <section className="py-24 bg-teal-700 text-white relative overflow-hidden">
      {/* Background Pattern */}
      <div className="absolute inset-0">
        <div className="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE0YzMuMzEgMCA2IDIuNjkgNiA2cy0yLjY5IDYtNiA2LTYtMi42OS02LTYgMi42OS02IDYtNk0xMiA0NGMzLjMxIDAgNiAyLjY5IDYgNnMtMi42OSA2LTYgNi02LTIuNjktNi02IDIuNjktNiA2LTYiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>
        <div className="absolute top-20 left-10 w-96 h-96 bg-orange-400 rounded-full blur-3xl opacity-20 animate-pulse"></div>
        <div className="absolute bottom-20 right-10 w-[500px] h-[500px] bg-pink-400 rounded-full blur-3xl opacity-20 animate-pulse" style={{animationDelay: '1s'}}></div>
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-400 rounded-full blur-3xl opacity-10"></div>
      </div>
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="grid md:grid-cols-2 gap-16 items-center">
          {/* Left Content */}
          <div className="space-y-8">
            <div className="inline-flex items-center gap-3 bg-white/20 backdrop-blur-md px-5 py-3 rounded-full mb-4 border border-white/30 shadow-lg animate-bounce">
              <Smartphone size={22} className="animate-pulse" />
              <span className="font-bold text-lg">📱 মোবাইল অ্যাপ</span>
              <Sparkles size={18} className="animate-spin" style={{animationDuration: '3s'}} />
            </div>
            
            <h2 className="text-6xl md:text-7xl font-black leading-tight">
              <span className="bg-gradient-to-r from-white via-yellow-200 to-white bg-clip-text text-transparent">
                গাড়ি কিনুন
              </span>
              <br />
              <span className="text-white">অ্যাপ ডাউনলোড করুন</span>
            </h2>
            
            <p className="text-2xl text-white/95 leading-relaxed font-medium">
              আপনার মোবাইলে সব সুবিধা পান। যেকোনো জায়গা থেকে গাড়ি খুঁজুন,
              বিজ্ঞাপন দিন এবং বিক্রেতার সাথে সরাসরি কথা বলুন।
            </p>
            
            {/* Features */}
            <div className="space-y-5">
              {appFeatures.map((feature, index) => {
                const Icon = feature.icon
                return (
                  <div 
                    key={index} 
                    className="flex items-center gap-4 bg-white/10 backdrop-blur-md px-5 py-4 rounded-2xl border border-white/20 hover:bg-white/20 transition-all hover:scale-105 hover:shadow-xl group"
                    style={{animationDelay: `${index * 0.1}s`}}
                  >
                    <div className={`w-12 h-12 bg-gradient-to-br ${feature.color} rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform`}>
                      <Icon size={24} className="text-white" />
                    </div>
                    <span className="text-xl font-semibold">{feature.text}</span>
                  </div>
                )
              })}
            </div>
            
            {/* App Store Buttons */}
            <div className="flex flex-col sm:flex-row gap-5">
              <a 
                href="https://play.google.com/store/apps/details?id=com.garikinun.app" 
                target="_blank" 
                rel="noopener noreferrer"
                className="group relative inline-flex items-center gap-4 bg-black hover:bg-gray-900 text-white px-8 py-5 rounded-2xl transition-all hover:scale-105 transform shadow-2xl overflow-hidden"
                onClick={(e) => {
                  // If app is not published yet, redirect to website
                  // Update the package ID above when app is published
                  console.log('Play Store clicked')
                }}
              >
                <div className="absolute inset-0 bg-gradient-to-r from-green-400/20 to-blue-400/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <svg className="w-10 h-10 relative z-10" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.5,12.92 20.16,13.19L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z" />
                </svg>
                <div className="text-left relative z-10">
                  <div className="text-sm opacity-90 font-medium">Google Play এ পান</div>
                  <div className="font-black text-xl">Play Store</div>
                </div>
              </a>
              
              <a 
                href="https://apps.apple.com/app/id123456789" 
                target="_blank" 
                rel="noopener noreferrer"
                className="group relative inline-flex items-center gap-4 bg-black hover:bg-gray-900 text-white px-8 py-5 rounded-2xl transition-all hover:scale-105 transform shadow-2xl overflow-hidden"
                onClick={(e) => {
                  // If app is not published yet, redirect to website
                  // Update the app ID above when app is published
                  console.log('App Store clicked')
                }}
              >
                <div className="absolute inset-0 bg-gradient-to-r from-orange-400/20 to-amber-400/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <svg className="w-10 h-10 relative z-10" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M18.71,19.5C17.88,20.74 17,21.95 15.66,21.97C14.32,22 13.89,21.18 12.37,21.18C10.84,21.18 10.37,21.95 9.1,22C7.79,22.05 6.8,20.68 5.96,19.47C4.25,17 2.94,12.45 4.7,9.39C5.57,7.87 7.13,6.91 8.82,6.88C10.1,6.86 11.32,7.75 12.11,7.75C12.89,7.75 14.37,6.68 15.92,6.84C16.57,6.87 18.39,7.1 19.56,8.82C19.47,8.88 17.39,10.1 17.41,12.63C17.44,15.65 20.06,16.66 20.09,16.67C20.06,16.74 19.67,18.11 18.71,19.5M13,3.5C13.73,2.67 14.94,2.04 15.94,2C16.07,3.17 15.6,4.35 14.9,5.19C14.21,6.04 13.07,6.7 11.95,6.61C11.8,5.46 12.36,4.26 13,3.5Z" />
                </svg>
                <div className="text-left relative z-10">
                  <div className="text-sm opacity-90 font-medium">App Store এ পান</div>
                  <div className="font-black text-xl">App Store</div>
                </div>
              </a>
            </div>
            
            {/* Rating */}
            <div className="flex items-center gap-6 bg-white/10 backdrop-blur-md px-6 py-5 rounded-2xl border border-white/20 w-fit">
              <div className="flex items-center gap-2">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} size={24} className="fill-yellow-400 text-yellow-400 animate-pulse" style={{animationDelay: `${i * 0.1}s`}} />
                ))}
              </div>
              <div className="border-l-2 border-white/30 pl-6">
                <div className="font-black text-2xl">৪.৮ রেটিং</div>
                <div className="text-sm text-white/80 font-semibold">১০,০০০+ ডাউনলোড</div>
              </div>
            </div>
          </div>
          
          {/* Right Content - Phone Mockup */}
          <div className="relative">
            <div className="relative mx-auto transform hover:scale-105 transition-transform duration-500" style={{ width: '300px' }}>
              {/* Glow Effect */}
              <div className="absolute inset-0 bg-gradient-to-r from-orange-400 to-amber-400 rounded-[3rem] blur-2xl opacity-40 animate-pulse"></div>
              
              {/* Phone Frame */}
              <div className="relative bg-gradient-to-br from-gray-900 to-black rounded-[3rem] p-3 shadow-2xl border-4 border-gray-800">
                <div className="bg-white rounded-[2.5rem] overflow-hidden">
                  {/* Notch */}
                  <div className="h-7 bg-gray-900 rounded-b-3xl mx-auto w-44 flex items-center justify-center">
                    <div className="w-16 h-1 bg-gray-800 rounded-full"></div>
                  </div>
                  
                  {/* App Screenshot */}
                  <div className="bg-gradient-to-br from-primary-50 via-purple-50 to-pink-50 p-4 h-[560px] overflow-hidden">
                    <div className="space-y-4">
                      {/* App Header */}
                      <div className="flex items-center justify-between mb-2">
                        <div className="flex items-center gap-3">
                          <div className="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span className="text-white font-black text-xl">গ</span>
                          </div>
                          <div>
                            <span className="font-black text-gray-900 text-lg block">গাড়ি কিনুন</span>
                            <span className="text-xs text-gray-500 font-semibold">Find Your Dream Car</span>
                          </div>
                        </div>
                      </div>
                      
                      {/* Search Bar */}
                      <div className="bg-white rounded-2xl p-4 shadow-lg border-2 border-gray-100">
                        <div className="flex items-center gap-3">
                          <Search size={20} className="text-gray-400" />
                          <div className="text-gray-400 font-medium">গাড়ি খুঁজুন...</div>
                        </div>
                      </div>
                      
                      {/* Category Pills */}
                      <div className="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                        <div className="px-5 py-2.5 bg-teal-600 text-white rounded-full text-sm font-bold whitespace-nowrap shadow-lg">
                          সব
                        </div>
                        <div className="px-5 py-2.5 bg-white text-gray-700 rounded-full text-sm font-semibold whitespace-nowrap shadow-md border border-gray-200">
                          গাড়ি
                        </div>
                        <div className="px-5 py-2.5 bg-white text-gray-700 rounded-full text-sm font-semibold whitespace-nowrap shadow-md border border-gray-200">
                          বাইক
                        </div>
                      </div>
                      
                      {/* Vehicle Cards */}
                      <div className="space-y-4">
                        {[1, 2].map((i) => (
                          <div key={i} className="bg-white rounded-2xl p-4 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                            <div className="flex gap-4">
                              <div className="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center">
                                <div className="text-4xl">🚗</div>
                              </div>
                              <div className="flex-1">
                                <div className="h-4 bg-gradient-to-r from-gray-300 to-gray-200 rounded-lg w-3/4 mb-2"></div>
                                <div className="h-3 bg-gradient-to-r from-gray-200 to-gray-100 rounded-lg w-1/2 mb-2"></div>
                                <div className="h-3 bg-teal-200 rounded-lg w-2/3"></div>
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              {/* QR Code Floating Card */}
              <a 
                href="https://garikinun.com/download" 
                target="_blank"
                rel="noopener noreferrer"
                className="absolute -right-12 top-1/2 -translate-y-1/2 bg-white p-5 rounded-3xl shadow-2xl hidden xl:block border-4 border-purple-100 hover:border-primary-300 animate-bounce hover:scale-110 transition-all cursor-pointer" 
                style={{animationDuration: '3s'}}
              >
                <div className="text-center">
                  <div className="bg-teal-100 p-3 rounded-2xl mb-3 group-hover:bg-teal-200 transition-all">
                    <QrCode size={110} className="text-gray-800 mx-auto" />
                  </div>
                  <p className="text-sm text-gray-900 font-bold mb-1">স্ক্যান করুন</p>
                  <p className="text-xs text-gray-600 font-semibold">ডাউনলোড করতে</p>
                  <div className="mt-2 text-[10px] text-primary-600 font-bold">garikinun.com</div>
                </div>
              </a>
            </div>
            
            {/* Floating Elements */}
            <div className="absolute -left-8 top-24 bg-white/95 backdrop-blur-md px-5 py-4 rounded-2xl shadow-2xl hidden lg:block border-2 border-purple-100 animate-bounce" style={{animationDuration: '2.5s'}}>
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                  <Download className="text-white" size={24} />
                </div>
                <div>
                  <div className="text-xs text-gray-600 font-semibold">আজকের ডাউনলোড</div>
                  <div className="font-black text-gray-900 text-xl">২,৩৪৫+</div>
                </div>
              </div>
            </div>
            
            <div className="absolute -left-6 bottom-20 bg-white/95 backdrop-blur-md px-5 py-4 rounded-2xl shadow-2xl hidden lg:block border-2 border-pink-100 animate-bounce" style={{animationDuration: '3s', animationDelay: '0.5s'}}>
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                  <CheckCircle2 className="text-white" size={24} />
                </div>
                <div>
                  <div className="text-xs text-gray-600 font-semibold">সক্রিয় ইউজার</div>
                  <div className="font-black text-gray-900 text-xl">৫,০০০+</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
