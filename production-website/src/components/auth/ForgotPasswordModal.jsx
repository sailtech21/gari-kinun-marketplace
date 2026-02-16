import { X, Mail, CheckCircle } from 'lucide-react'
import { useState } from 'react'
import Button from '../common/Button'
import { apiCall } from '../../config'

export default function ForgotPasswordModal({ isOpen, onClose }) {
  const [email, setEmail] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState(false)
  
  if (!isOpen) return null
  
  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError('')
    
    try {
      const response = await apiCall('/password/forgot', {
        method: 'POST',
        body: JSON.stringify({ email }),
      })
      
      if (response.success) {
        setSuccess(true)
      }
    } catch (err) {
      setError(err.message || 'পাসওয়ার্ড রিসেট লিঙ্ক পাঠাতে সমস্যা হয়েছে।')
    } finally {
      setLoading(false)
    }
  }
  
  const handleClose = () => {
    setEmail('')
    setError('')
    setSuccess(false)
    onClose()
  }
  
  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-200">
          <h2 className="text-2xl font-bold text-gray-900">
            {success ? 'ইমেইল পাঠানো হয়েছে!' : 'পাসওয়ার্ড ভুলে গেছেন?'}
          </h2>
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
                পাসওয়ার্ড রিসেট লিঙ্ক আপনার ইমেইলে পাঠানো হয়েছে।
              </p>
              <p className="text-sm text-gray-500 mb-6">
                অনুগ্রহ করে আপনার ইমেইল চেক করুন এবং লিঙ্কে ক্লিক করুন।
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
              <p className="text-gray-600 mb-6">
                আপনার অ্যাকাউন্টের ইমেইল অ্যাড্রেস দিন। আমরা আপনাকে পাসওয়ার্ড রিসেট করার লিঙ্ক পাঠাবো।
              </p>
              
              {error && (
                <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                  {error}
                </div>
              )}
              
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    ইমেইল অ্যাড্রেস
                  </label>
                  <div className="relative">
                    <input
                      type="email"
                      name="email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="your@email.com"
                      className="w-full px-4 py-3 pl-12 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-primary-500"
                      required
                      autoFocus
                    />
                    <Mail className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
                  </div>
                </div>
                
                <Button
                  type="submit"
                  variant="primary"
                  size="lg"
                  fullWidth
                  loading={loading}
                >
                  রিসেট লিঙ্ক পাঠান
                </Button>
              </form>
              
              <div className="text-center mt-6">
                <button 
                  onClick={handleClose}
                  className="text-sm text-primary-600 hover:text-primary-700 font-semibold"
                >
                  লগইনে ফিরে যান
                </button>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  )
}
