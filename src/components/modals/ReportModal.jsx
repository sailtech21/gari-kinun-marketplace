import { X, Flag, AlertTriangle, CheckCircle } from 'lucide-react'
import { useState } from 'react'
import Button from '../common/Button'
import { apiCall } from '../../config'

export default function ReportModal({ isOpen, onClose, listingId }) {
  const [formData, setFormData] = useState({
    reason: '',
    description: ''
  })
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState(false)
  
  const reasons = [
    { value: 'spam', label: 'স্প্যাম বা প্রতারণা' },
    { value: 'inappropriate', label: 'অনুপযুক্ত কন্টেন্ট' },
    { value: 'fraud', label: 'জালিয়াতি বা ভুয়া তথ্য' },
    { value: 'duplicate', label: 'ডুপ্লিকেট বিজ্ঞাপন' },
    { value: 'other', label: 'অন্যান্য' }
  ]
  
  if (!isOpen) return null
  
  const handleSubmit = async (e) => {
    e.preventDefault()
    
    if (!formData.reason) {
      setError('অনুগ্রহ করে একটি কারণ নির্বাচন করুন')
      return
    }
    
    if (!formData.description.trim()) {
      setError('অনুগ্রহ করে বিস্তারিত বর্ণনা দিন')
      return
    }
    
    setLoading(true)
    setError('')
    
    try {
      const response = await apiCall(`/listings/${listingId}/report`, {
        method: 'POST',
        body: JSON.stringify(formData)
      })
      
      if (response.success) {
        setSuccess(true)
      }
    } catch (err) {
      setError(err.message || 'রিপোর্ট জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।')
    } finally {
      setLoading(false)
    }
  }
  
  const handleClose = () => {
    setFormData({ reason: '', description: '' })
    setError('')
    setSuccess(false)
    onClose()
  }
  
  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }
  
  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-200 sticky top-0 bg-white rounded-t-2xl">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
              <Flag size={20} className="text-red-600" />
            </div>
            <h2 className="text-2xl font-bold text-gray-900">
              {success ? 'রিপোর্ট জমা হয়েছে' : 'বিজ্ঞাপন রিপোর্ট করুন'}
            </h2>
          </div>
          <button 
            onClick={handleClose}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors"
          >
            <X size={24} className="text-gray-500" />
          </button>
        </div>
        
        {/* Content */}
        <div className="p-6">
          {success ? (
            <div className="text-center py-6">
              <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <CheckCircle size={48} className="text-green-600" />
              </div>
              <p className="text-gray-700 mb-2">
                আপনার রিপোর্ট সফলভাবে জমা হয়েছে!
              </p>
              <p className="text-sm text-gray-500 mb-6">
                আমরা শীঘ্রই এটি যাচাই করে প্রয়োজনীয় ব্যবস্থা নেবো। ধন্যবাদ।
              </p>
              <Button
                onClick={handleClose}
                variant="primary"
                size="lg"
                fullWidth
              >
                ঠিক আছে
              </Button>
            </div>
          ) : (
            <>
              <div className="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 flex items-start gap-3">
                <AlertTriangle size={20} className="text-amber-600 flex-shrink-0 mt-0.5" />
                <div className="text-sm text-amber-800">
                  <p className="font-semibold mb-1">রিপোর্ট করার আগে নিশ্চিত হন</p>
                  <p>শুধুমাত্র নিয়ম-নীতি লঙ্ঘনকারী বিজ্ঞাপন রিপোর্ট করুন। মিথ্যা রিপোর্ট করলে আপনার অ্যাকাউন্ট স্থগিত হতে পারে।</p>
                </div>
              </div>
              
              {error && (
                <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                  {error}
                </div>
              )}
              
              <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-3">
                    রিপোর্ট করার কারণ *
                  </label>
                  <div className="space-y-2">
                    {reasons.map((reason) => (
                      <label 
                        key={reason.value}
                        className="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-primary-300 cursor-pointer transition-colors"
                      >
                        <input
                          type="radio"
                          name="reason"
                          value={reason.value}
                          checked={formData.reason === reason.value}
                          onChange={handleChange}
                          className="w-5 h-5 text-primary-600 focus:ring-primary-500"
                        />
                        <span className="text-gray-700 font-medium">{reason.label}</span>
                      </label>
                    ))}
                  </div>
                </div>
                
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    বিস্তারিত বর্ণনা * (সর্বোচ্চ ৫০০ অক্ষর)
                  </label>
                  <textarea
                    name="description"
                    value={formData.description}
                    onChange={handleChange}
                    placeholder="এই বিজ্ঞাপন সম্পর্কে বিস্তারিত জানান..."
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-primary-500 resize-none"
                    rows={5}
                    maxLength={500}
                    required
                  />
                  <div className="text-right text-xs text-gray-500 mt-1">
                    {formData.description.length}/500
                  </div>
                </div>
                
                <Button
                  type="submit"
                  variant="primary"
                  size="lg"
                  fullWidth
                  loading={loading}
                >
                  রিপোর্ট জমা দিন
                </Button>
              </form>
            </>
          )}
        </div>
      </div>
    </div>
  )
}
