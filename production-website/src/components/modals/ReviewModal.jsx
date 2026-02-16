import { useState } from 'react'
import { X, Star, MessageCircle, User } from 'lucide-react'
import { apiCall } from '../../config'

export default function ReviewModal({ isOpen, onClose, listingId, onSuccess }) {
  const [rating, setRating] = useState(0)
  const [hoveredRating, setHoveredRating] = useState(0)
  const [formData, setFormData] = useState({
    name: '',
    location: '',
    comment: '',
    purchase: ''
  })
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState(false)

  if (!isOpen) return null

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setLoading(true)

    // Validation
    if (!rating) {
      setError('অনুগ্রহ করে রেটিং দিন')
      setLoading(false)
      return
    }

    if (!formData.comment.trim()) {
      setError('অনুগ্রহ করে মন্তব্য লিখুন')
      setLoading(false)
      return
    }

    try {
      const response = await apiCall('/reviews', {
        method: 'POST',
        body: JSON.stringify({
          ...formData,
          rating,
          listing_id: listingId
        })
      })

      if (response.success) {
        setSuccess(true)
        setTimeout(() => {
          onClose()
          // Reset form
          setRating(0)
          setFormData({ name: '', location: '', comment: '', purchase: '' })
          setSuccess(false)
          // Call onSuccess callback to refresh reviews
          if (onSuccess) onSuccess()
        }, 2000)
      }
    } catch (err) {
      setError(err.message || 'রিভিউ সাবমিট করতে ব্যর্থ হয়েছে')
    } finally {
      setLoading(false)
    }
  }

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }

  const handleClose = () => {
    if (!loading) {
      setRating(0)
      setFormData({ name: '', location: '', comment: '', purchase: '' })
      setError('')
      setSuccess(false)
      onClose()
    }
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {/* Header */}
        <div className="sticky top-0 bg-teal-700 text-white p-6 flex items-center justify-between rounded-t-2xl">
          <div>
            <h2 className="text-2xl font-bold mb-1">রিভিউ লিখুন</h2>
            <p className="text-white/90">আপনার অভিজ্ঞতা শেয়ার করুন</p>
          </div>
          <button
            onClick={handleClose}
            className="p-2 hover:bg-white/20 rounded-lg transition-colors"
            disabled={loading}
          >
            <X size={24} />
          </button>
        </div>

        {/* Success Message */}
        {success && (
          <div className="mx-6 mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p className="text-green-700 font-semibold text-center">
              ✅ আপনার রিভিউ সফলভাবে সাবমিট হয়েছে! ধন্যবাদ।
            </p>
          </div>
        )}

        {/* Form */}
        <form onSubmit={handleSubmit} className="p-6 space-y-6">
          {/* Error Message */}
          {error && (
            <div className="p-4 bg-red-50 border border-red-200 rounded-lg">
              <p className="text-red-700 text-sm">{error}</p>
            </div>
          )}

          {/* Rating */}
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-3">
              রেটিং দিন <span className="text-red-500">*</span>
            </label>
            <div className="flex items-center gap-2">
              {[1, 2, 3, 4, 5].map((star) => (
                <button
                  key={star}
                  type="button"
                  onClick={() => setRating(star)}
                  onMouseEnter={() => setHoveredRating(star)}
                  onMouseLeave={() => setHoveredRating(0)}
                  className="transition-transform hover:scale-110 focus:outline-none"
                >
                  <Star
                    size={40}
                    className={`${
                      star <= (hoveredRating || rating)
                        ? 'fill-yellow-400 text-yellow-400'
                        : 'text-gray-300'
                    } transition-colors`}
                  />
                </button>
              ))}
              {rating > 0 && (
                <span className="ml-3 text-lg font-bold text-gray-700">
                  {rating === 5 ? '🌟 চমৎকার!' : rating === 4 ? '👍 ভালো' : rating === 3 ? '😊 মোটামুটি' : '😐 উন্নতি প্রয়োজন'}
                </span>
              )}
            </div>
          </div>

          {/* Name */}
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-2">
              <div className="flex items-center gap-2">
                <User size={16} />
                আপনার নাম <span className="text-red-500">*</span>
              </div>
            </label>
            <input
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              placeholder="যেমন: মাহমুদ হাসান"
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors"
              required
              disabled={loading}
            />
          </div>

          {/* Location */}
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-2">
              অবস্থান <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="location"
              value={formData.location}
              onChange={handleChange}
              placeholder="যেমন: ঢাকা, চট্টগ্রাম"
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors"
              required
              disabled={loading}
            />
          </div>

          {/* Purchase (Optional) */}
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-2">
              ক্রয়কৃত গাড়ি (ঐচ্ছিক)
            </label>
            <input
              type="text"
              name="purchase"
              value={formData.purchase}
              onChange={handleChange}
              placeholder="যেমন: টয়োটা করোলা এক্স ২০২০"
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors"
              disabled={loading}
            />
          </div>

          {/* Comment */}
          <div>
            <label className="block text-sm font-semibold text-gray-700 mb-2">
              <div className="flex items-center gap-2">
                <MessageCircle size={16} />
                আপনার মন্তব্য <span className="text-red-500">*</span>
              </div>
            </label>
            <textarea
              name="comment"
              value={formData.comment}
              onChange={handleChange}
              placeholder="আপনার অভিজ্ঞতা এবং মতামত বিস্তারিত লিখুন..."
              rows={5}
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors resize-none"
              required
              disabled={loading}
            />
            <p className="mt-2 text-xs text-gray-500">
              ন্যূনতম ২০ অক্ষর প্রয়োজন
            </p>
          </div>

          {/* Submit Button */}
          <div className="flex gap-3 pt-4">
            <button
              type="button"
              onClick={handleClose}
              className="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors"
              disabled={loading}
            >
              বাতিল করুন
            </button>
            <button
              type="submit"
              className="flex-1 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              disabled={loading || !rating}
            >
              {loading ? 'সাবমিট হচ্ছে...' : 'রিভিউ সাবমিট করুন'}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}
