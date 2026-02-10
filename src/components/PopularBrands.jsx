import { ArrowRight } from 'lucide-react'
import { brands } from '../data/dummyData'

export default function PopularBrands({ onBrandClick, onViewAll }) {
  return (
    <section className="py-20 bg-orange-50 relative overflow-hidden">
      {/* Background Elements */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-10 right-10 w-72 h-72 bg-teal-100 rounded-full blur-3xl opacity-20"></div>
        <div className="absolute bottom-10 left-10 w-72 h-72 bg-rose-100 rounded-full blur-3xl opacity-20"></div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {/* Section Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 px-5 py-2.5 bg-white rounded-full mb-4 shadow-sm">
            <span className="text-sm font-bold text-teal-700">🏆 ব্র্যান্ড</span>
          </div>
          <h2 className="text-5xl md:text-6xl font-black text-gray-900 mb-4">
            <span className="text-teal-700">জনপ্রিয়</span> ব্র্যান্ড
          </h2>
          <p className="text-xl text-gray-600">বিশ্বস্ত ব্র্যান্ডের গাড়ি খুঁজুন</p>
        </div>

        {/* Brands Grid */}
        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-8 mb-12">
          {brands.map((brand) => (
            <button
              key={brand.id}
              onClick={() => onBrandClick(brand.name)}
              className="bg-white rounded-2xl p-8 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group border-2 border-gray-100 hover:border-teal-200 relative overflow-hidden"
            >
              {/* Gradient overlay on hover */}
              <div className="absolute inset-0 bg-gradient-to-br from-teal-50 to-rose-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
              
              <div className="relative z-10">
                <div className="text-6xl mb-4 transform group-hover:scale-110 transition-transform">{brand.logo}</div>
                <h3 className="font-black text-gray-900 mb-2 text-xl">{brand.name}</h3>
                <p className="text-sm text-gray-500 font-semibold mb-4">{brand.count} গাড়ি</p>
                <div className="flex items-center gap-2 text-gray-700 font-bold opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 group-hover:text-teal-600">
                  <span>দেখুন</span>
                  <ArrowRight size={18} className="group-hover:translate-x-1 transition-transform" />
                </div>
              </div>
            </button>
          ))}
        </div>

        {/* CTA */}
        <div className="text-center">
          <button 
            onClick={onViewAll}
            className="inline-flex items-center gap-3 bg-rose-500 hover:bg-rose-600 text-white font-bold px-8 py-4 rounded-xl hover:shadow-xl hover:scale-105 transition-all"
          >
            <span>সব ব্র্যান্ড দেখুন</span>
            <ArrowRight size={22} />
          </button>
        </div>
      </div>
    </section>
  )
}
