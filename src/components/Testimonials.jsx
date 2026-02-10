import { Star, Quote, CheckCircle, TrendingUp, Users, ThumbsUp } from 'lucide-react'
import { useState } from 'react'
import { testimonials } from '../data/dummyData'
import ReviewModal from './modals/ReviewModal'

export default function Testimonials() {
  const [showReviewModal, setShowReviewModal] = useState(false)
  
  // Calculate stats
  const totalReviews = testimonials.length
  const averageRating = (testimonials.reduce((acc, t) => acc + t.rating, 0) / totalReviews).toFixed(1)
  const fiveStarCount = testimonials.filter(t => t.rating === 5).length
  const fiveStarPercentage = Math.round((fiveStarCount / totalReviews) * 100)

  return (
    <section className="py-20 bg-gradient-to-br from-gray-50 via-white to-orange-50 relative overflow-hidden">
      {/* Background Pattern */}
      <div className="absolute inset-0 opacity-5">
        <div className="absolute top-20 left-10 w-72 h-72 bg-teal-600 rounded-full blur-3xl"></div>
        <div className="absolute bottom-20 right-10 w-96 h-96 bg-orange-400 rounded-full blur-3xl"></div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {/* Section Header */}
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-semibold mb-4">
            <Star className="fill-primary-700" size={16} />
            <span>ব্যবহারকারীদের মতামত</span>
          </div>
          <h2 className="text-5xl font-bold text-gray-900 mb-4">
            আমাদের সন্তুষ্ট ক্রেতারা
          </h2>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            হাজারো মানুষ আমাদের প্ল্যাটফর্মে তাদের স্বপ্নের গাড়ি খুঁজে পেয়েছেন
          </p>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
          <div className="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-2">
              <div className="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <Star className="text-yellow-600 fill-yellow-600" size={24} />
              </div>
              <TrendingUp className="text-green-600" size={20} />
            </div>
            <p className="text-3xl font-bold text-gray-900 mb-1">{averageRating}</p>
            <p className="text-sm text-gray-600">গড় রেটিং</p>
          </div>

          <div className="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-2">
              <div className="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <Users className="text-blue-600" size={24} />
              </div>
            </div>
            <p className="text-3xl font-bold text-gray-900 mb-1">৫,২০০+</p>
            <p className="text-sm text-gray-600">সন্তুষ্ট ক্রেতা</p>
          </div>

          <div className="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-2">
              <div className="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <ThumbsUp className="text-green-600" size={24} />
              </div>
            </div>
            <p className="text-3xl font-bold text-gray-900 mb-1">{fiveStarPercentage}%</p>
            <p className="text-sm text-gray-600">৫ স্টার রিভিউ</p>
          </div>

          <div className="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-2">
              <div className="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                <CheckCircle className="text-teal-600" size={24} />
              </div>
            </div>
            <p className="text-3xl font-bold text-gray-900 mb-1">৯৮%</p>
            <p className="text-sm text-gray-600">সুপারিশ হার</p>
          </div>
        </div>

        {/* Testimonials Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
          {testimonials.map((testimonial, index) => (
            <div 
              key={testimonial.id} 
              className="group bg-white rounded-2xl p-8 shadow-lg border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden"
            >
              {/* Gradient Border Effect */}
              <div className="absolute inset-0 bg-gradient-to-br from-teal-500/10 via-transparent to-orange-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              
              {/* Content */}
              <div className="relative z-10">
                {/* Quote Icon */}
                <div className="absolute -top-2 -right-2 opacity-10 group-hover:opacity-20 transition-opacity">
                  <Quote size={80} className="text-primary-600" />
                </div>

                {/* Rating */}
                <div className="flex items-center gap-3 mb-4">
                  <div className="flex gap-1">
                    {[...Array(5)].map((_, i) => (
                      <Star 
                        key={i} 
                        size={18} 
                        className={i < testimonial.rating ? "fill-yellow-400 text-yellow-400" : "text-gray-300"}
                      />
                    ))}
                  </div>
                  <span className="text-sm font-semibold text-gray-700">{testimonial.rating}.0</span>
                </div>

                {/* Comment */}
                <p className="text-gray-700 mb-6 leading-relaxed text-base italic">
                  "{testimonial.comment}"
                </p>

                {/* Purchase Info */}
                {testimonial.purchase && (
                  <div className="bg-gradient-to-r from-teal-50 to-orange-50 rounded-lg p-3 mb-6">
                    <p className="text-xs text-gray-600 mb-1">ক্রয়কৃত গাড়ি</p>
                    <p className="text-sm font-semibold text-gray-900">{testimonial.purchase}</p>
                  </div>
                )}

                {/* User Info */}
                <div className="flex items-center gap-4 pt-6 border-t border-gray-100">
                  <div className="relative">
                    <img 
                      src={testimonial.avatar} 
                      alt={testimonial.name}
                      className="w-14 h-14 rounded-full object-cover ring-2 ring-white shadow-lg"
                    />
                    {testimonial.verified && (
                      <div className="absolute -bottom-1 -right-1 w-6 h-6 bg-primary-600 rounded-full flex items-center justify-center border-2 border-white">
                        <CheckCircle size={14} className="text-white fill-current" />
                      </div>
                    )}
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center gap-2">
                      <h4 className="font-bold text-gray-900">{testimonial.name}</h4>
                      {testimonial.verified && (
                        <span className="px-2 py-0.5 bg-primary-100 text-primary-700 text-xs font-semibold rounded-full">
                          যাচাইকৃত
                        </span>
                      )}
                    </div>
                    <p className="text-sm text-gray-500">{testimonial.location}</p>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* CTA Section */}
        <div className="bg-teal-700 rounded-3xl p-12 text-center shadow-2xl">
          <div className="max-w-2xl mx-auto">
            <div className="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-semibold mb-6">
              <Quote size={16} />
              <span>আপনার মতামত জানান</span>
            </div>
            <h3 className="text-3xl font-bold text-white mb-4">
              আপনিও কি আপনার অভিজ্ঞতা শেয়ার করতে চান?
            </h3>
            <p className="text-white/90 text-lg mb-8">
              আপনার মতামত অন্যদের সঠিক সিদ্ধান্ত নিতে সাহায্য করবে
            </p>
            <button 
              onClick={() => setShowReviewModal(true)}
              className="bg-white text-primary-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-50 transition-colors shadow-xl hover:shadow-2xl"
            >
              রিভিউ লিখুন
            </button>
          </div>
        </div>
      </div>

      {/* Review Modal */}
      <ReviewModal 
        isOpen={showReviewModal}
        onClose={() => setShowReviewModal(false)}
      />
    </section>
  )
}
