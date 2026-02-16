import { useState } from 'react'
import { Star, Send, User, MapPin } from 'lucide-react'
import { apiCall } from '../../config'

export default function ReviewsSection({ listingId, sellerId }) {
  const [reviews, setReviews] = useState([])
  const [loading, setLoading] = useState(false)
  const [showForm, setShowForm] = useState(false)
  const [formData, setFormData] = useState({
    name: '',
    location: '',
    rating: 5,
    comment: '',
    purchase: ''
  })
  const [submitting, setSubmitting] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)

    try {
      const response = await apiCall('/reviews', {
        method: 'POST',
        body: JSON.stringify(formData)
      })

      if (response.success) {
        alert(response.message || 'রিভিউ সাবমিট হয়েছে! অনুমোদনের পর প্রদর্শিত হবে।')
        setFormData({
          name: '',
          location: '',
          rating: 5,
          comment: '',
          purchase: ''
        })
        setShowForm(false)
      }
    } catch (error) {
      alert('রিভিউ সাবমিট করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।')
    } finally {
      setSubmitting(false)
    }
  }

  const renderStars = (rating, interactive = false, onChange = null) => {
    return (
      <div className="flex gap-1">
        {[1, 2, 3, 4, 5].map((star) => (
          <button
            key={star}
            type="button"
            onClick={() => interactive && onChange && onChange(star)}
            className={`${interactive ? 'cursor-pointer' : 'cursor-default'} transition-colors`}
            disabled={!interactive}
          >
            <Star
              size={interactive ? 24 : 20}
              className={star <= rating ? 'fill-yellow-400 text-yellow-400' : 'text-gray-300'}
            />
          </button>
        ))}
      </div>
    )
  }

  return (
    <div className="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
      {/* Header */}
      <div className="flex items-center justify-between mb-8 pb-6 border-b-2 border-teal-100">
        <div>
          <h2 className="text-3xl font-bold text-gray-900 mb-2">রিভিউ ও রেটিং</h2>
          <p className="text-gray-600">ক্রেতাদের অভিজ্ঞতা দেখুন</p>
        </div>
        <button
          onClick={() => setShowForm(!showForm)}
          className="bg-teal-700 hover:bg-teal-800 text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2"
        >
          <Star size={20} />
          রিভিউ লিখুন
        </button>
      </div>

      {/* Review Form */}
      {showForm && (
        <div className="bg-orange-50 rounded-2xl p-6 mb-8 border-2 border-teal-200">
          <h3 className="text-xl font-bold text-gray-900 mb-4">আপনার রিভিউ লিখুন</h3>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-2">
                  <User size={16} className="inline mr-1" />
                  আপনার নাম *
                </label>
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:outline-none"
                  placeholder="মোঃ রহিম"
                  required
                />
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-700 mb-2">
                  <MapPin size={16} className="inline mr-1" />
                  এলাকা *
                </label>
                <input
                  type="text"
                  value={formData.location}
                  onChange={(e) => setFormData({ ...formData, location: e.target.value })}
                  className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:outline-none"
                  placeholder="ঢাকা"
                  required
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 mb-2">
                <Star size={16} className="inline mr-1" />
                রেটিং *
              </label>
              <div className="flex items-center gap-4">
                {renderStars(formData.rating, true, (rating) => setFormData({ ...formData, rating }))}
                <span className="text-gray-600 font-semibold">{formData.rating} স্টার</span>
              </div>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 mb-2">
                কোন গাড়ি কিনেছেন? (ঐচ্ছিক)
              </label>
              <input
                type="text"
                value={formData.purchase}
                onChange={(e) => setFormData({ ...formData, purchase: e.target.value })}
                className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:outline-none"
                placeholder="Toyota Corolla 2020"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 mb-2">
                আপনার অভিজ্ঞতা লিখুন * (কমপক্ষে ২০ অক্ষর)
              </label>
              <textarea
                value={formData.comment}
                onChange={(e) => setFormData({ ...formData, comment: e.target.value })}
                className="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-teal-500 focus:outline-none"
                rows="4"
                placeholder="এই বিক্রেতার সাথে কেনাকাটার অভিজ্ঞতা কেমন ছিল?"
                required
                minLength={20}
              />
            </div>

            <div className="flex gap-3">
              <button
                type="submit"
                disabled={submitting}
                className="bg-rose-500 hover:bg-rose-600 disabled:bg-gray-400 text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2"
              >
                <Send size={20} />
                {submitting ? 'সাবমিট হচ্ছে...' : 'রিভিউ সাবমিট করুন'}
              </button>
              <button
                type="button"
                onClick={() => setShowForm(false)}
                className="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold transition-all"
              >
                বাতিল
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Sample Reviews Display */}
      <div className="space-y-6">
        <div className="bg-white rounded-xl p-6 border-2 border-gray-100 hover:border-teal-200 transition-all">
          <div className="flex items-start gap-4">
            <div className="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-teal-700 font-bold text-lg">
              র
            </div>
            <div className="flex-1">
              <div className="flex items-center justify-between mb-2">
                <div>
                  <h4 className="font-bold text-gray-900">রহিম উদ্দিন</h4>
                  <p className="text-sm text-gray-500">ঢাকা • ১০ ফেব্রুয়ারি ২০২৬</p>
                </div>
                {renderStars(5)}
              </div>
              <p className="text-gray-700 mb-2">
                অসাধারণ সেবা! বিক্রেতা অত্যন্ত বিশ্বস্ত এবং সহযোগী। গাড়ির কন্ডিশন ঠিক যেমন বর্ণনায় ছিল। সবাই এখান থেকে গাড়ি কিনতে পারেন নিশ্চিন্তে।
              </p>
              <div className="inline-flex items-center gap-2 bg-teal-50 text-teal-700 px-3 py-1 rounded-full text-sm font-semibold">
                <span className="text-teal-500">✓</span> যাচাইকৃত ক্রেতা • Toyota Camry 2021
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 border-2 border-gray-100 hover:border-teal-200 transition-all">
          <div className="flex items-start gap-4">
            <div className="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-teal-700 font-bold text-lg">
              ক
            </div>
            <div className="flex-1">
              <div className="flex items-center justify-between mb-2">
                <div>
                  <h4 className="font-bold text-gray-900">করিম মিয়া</h4>
                  <p className="text-sm text-gray-500">চট্টগ্রাম • ৮ ফেব্রুয়ারি ২০২৬</p>
                </div>
                {renderStars(4)}
              </div>
              <p className="text-gray-700 mb-2">
                ভালো ডিল পেয়েছি। গাড়ির দাম যুক্তিসংগত ছিল। কিছু ছোটখাটো সমস্যা ছিল কিন্তু বিক্রেতা তা ঠিক করে দিয়েছেন।
              </p>
              <div className="inline-flex items-center gap-2 bg-teal-50 text-teal-700 px-3 py-1 rounded-full text-sm font-semibold">
                <span className="text-teal-500">✓</span> যাচাইকৃত ক্রেতা • Honda Civic 2019
              </div>
            </div>
          </div>
        </div>

        <div className="text-center py-8">
          <p className="text-gray-500">আরও রিভিউ শীঘ্রই যোগ হবে</p>
        </div>
      </div>
    </div>
  )
}
