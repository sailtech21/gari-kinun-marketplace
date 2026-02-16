import { X, User, Phone, Mail, MessageCircle, Send } from 'lucide-react'
import { useState } from 'react'
import Button from '../common/Button'
import { apiCall } from '../../config'

export default function ContactModal({ isOpen, onClose, listing }) {
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    message: `আমি ${listing?.title} গাড়িটি সম্পর্কে জানতে আগ্রহী।`
  })
  const [loading, setLoading] = useState(false)
  
  if (!isOpen) return null
  
  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    
    try {
      // Call Contact API
      const response = await apiCall('/contact', {
        method: 'POST',
        body: JSON.stringify({
          listing_id: listing?.id,
          name: formData.name,
          phone: formData.phone,
          email: formData.email || null,
          message: formData.message
        })
      })
      
      if (response.success) {
        alert(response.message || 'আপনার মেসেজ সফলভাবে পাঠানো হয়েছে! বিক্রেতা শীঘ্রই আপনার সাথে যোগাযোগ করবেন।')
        onClose()
        setFormData({
          name: '',
          phone: '',
          email: '',
          message: ''
        })
      }
    } catch (error) {
      alert('দুঃখিত! মেসেজ পাঠাতে সমস্যা হয়েছে। আবার চেষ্টা করুন।')
      console.error('Contact form error:', error)
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
  
  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl max-w-2xl w-full shadow-2xl relative max-h-[90vh] overflow-y-auto">
        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors z-10"
        >
          <X size={24} />
        </button>
        
        {/* Header */}
        <div className="p-8 pb-6">
          <div className="flex items-start gap-4 mb-6">
            <div className="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center flex-shrink-0">
              <MessageCircle size={32} />
            </div>
            <div>
              <h2 className="text-2xl font-bold text-gray-900 mb-2">বিক্রেতার সাথে যোগাযোগ করুন</h2>
              <p className="text-gray-600">আপনার তথ্য দিন এবং বিক্রেতা আপনার সাথে যোগাযোগ করবেন</p>
            </div>
          </div>
          
          {/* Vehicle Info */}
          <div className="bg-gray-50 rounded-lg p-4 mb-6">
            <div className="flex items-center gap-4">
              <img 
                src={listing?.image} 
                alt={listing?.title}
                className="w-20 h-20 rounded-lg object-cover"
              />
              <div>
                <h3 className="font-bold text-gray-900 mb-1">{listing?.title}</h3>
                <p className="text-gray-900 font-bold">৳ {listing?.price}</p>
                <p className="text-sm text-gray-500">{listing?.location}</p>
              </div>
            </div>
          </div>
          
          {/* Form */}
          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Name */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                আপনার নাম <span className="text-red-500">*</span>
              </label>
              <div className="relative">
                <User className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                  type="text"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  placeholder="নাম লিখুন"
                  className="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors"
                  required
                />
              </div>
            </div>
            
            {/* Phone */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                ফোন নম্বর <span className="text-red-500">*</span>
              </label>
              <div className="relative">
                <Phone className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                  type="tel"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  placeholder="০১৭XXXXXXXX"
                  className="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors"
                  required
                />
              </div>
            </div>
            
            {/* Email */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                ইমেইল (ঐচ্ছিক)
              </label>
              <div className="relative">
                <Mail className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="example@email.com"
                  className="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors"
                />
              </div>
            </div>
            
            {/* Message */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                আপনার মেসেজ <span className="text-red-500">*</span>
              </label>
              <textarea
                name="message"
                value={formData.message}
                onChange={handleChange}
                rows="4"
                placeholder="গাড়ি সম্পর্কে আপনার প্রশ্ন লিখুন..."
                className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-primary-500 transition-colors resize-none"
                required
              />
              <p className="text-xs text-gray-500 mt-2">
                বিক্রেতা এই মেসেজ পাবেন এবং আপনার ফোন নম্বরে যোগাযোগ করবেন
              </p>
            </div>
            
            {/* Submit Button */}
            <Button
              type="submit"
              variant="primary"
              size="lg"
              fullWidth
              loading={loading}
              icon={<Send size={20} />}
            >
              মেসেজ পাঠান
            </Button>
          </form>
          
          {/* Footer Note */}
          <div className="mt-6 pt-6 border-t border-gray-200">
            <p className="text-sm text-gray-600 text-center">
              আপনার তথ্য নিরাপদ এবং শুধুমাত্র বিক্রেতার সাথে শেয়ার করা হবে
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
