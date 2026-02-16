import { X, Lock, Eye, EyeOff, CheckCircle } from 'lucide-react'
import { useState } from 'react'
import Button from '../common/Button'
import { apiCall } from '../../config'

export default function ResetPasswordModal({ isOpen, onClose, token, email }) {
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)
  const [formData, setFormData] = useState({
    password: '',
    password_confirmation: ''
  })
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState(false)
  
  if (!isOpen) return null
  
  const handleSubmit = async (e) => {
    e.preventDefault()
    
    if (formData.password !== formData.password_confirmation) {
      setError('পাসওয়ার্ড মিলছে না')
      return
    }
    
    if (formData.password.length < 8) {
      setError('পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে')
      return
    }
    
    setLoading(true)
    setError('')
    
    try {
      const response = await apiCall('/password/reset', {
        method: 'POST',
        body: JSON.stringify({
          token,
          email,
          password: formData.password,
          password_confirmation: formData.password_confirmation
        }),
      })
      
      if (response.success) {
        setSuccess(true)
      }
    } catch (err) {
      setError(err.message || 'পাসওয়ার্ড রিসেট করতে সমস্যা হয়েছে।')
    } finally {
      setLoading(false)
    }
  }
  
  const handleClose = () => {
    setFormData({ password: '', password_confirmation: '' })
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
      <div className="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-200">
          <h2 className="text-2xl font-bold text-gray-900">
            {success ? 'পাসওয়ার্ড রিসেট সফল!' : 'নতুন পাসওয়ার্ড সেট করুন'}
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
                আপনার পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে!
              </p>
              <p className="text-sm text-gray-500 mb-6">
                এখন আপনি নতুন পাসওয়ার্ড দিয়ে লগইন করতে পারবেন।
              </p>
              <Button
                onClick={() => {
                  handleClose()
                  // Open login modal
                  const event = new CustomEvent('openLogin')
                  window.dispatchEvent(event)
                }}
                variant="primary"
                size="lg"
                fullWidth
              >
                লগইন করুন
              </Button>
            </div>
          ) : (
            <>
              <p className="text-gray-600 mb-6">
                আপনার নতুন পাসওয়ার্ড দিন। পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।
              </p>
              
              {error && (
                <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                  {error}
                </div>
              )}
              
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    নতুন পাসওয়ার্ড
                  </label>
                  <div className="relative">
                    <input
                      type={showPassword ? 'text' : 'password'}
                      name="password"
                      value={formData.password}
                      onChange={handleChange}
                      placeholder="কমপক্ষে ৮ অক্ষর"
                      className="w-full px-4 py-3 pl-12 pr-12 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-primary-500"
                      required
                      autoFocus
                    />
                    <Lock className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                      {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                    </button>
                  </div>
                </div>
                
                <div>
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    পাসওয়ার্ড নিশ্চিত করুন
                  </label>
                  <div className="relative">
                    <input
                      type={showConfirmPassword ? 'text' : 'password'}
                      name="password_confirmation"
                      value={formData.password_confirmation}
                      onChange={handleChange}
                      placeholder="আবার পাসওয়ার্ড লিখুন"
                      className="w-full px-4 py-3 pl-12 pr-12 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-primary-500"
                      required
                    />
                    <Lock className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400" size={20} />
                    <button
                      type="button"
                      onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                      className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                      {showConfirmPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                    </button>
                  </div>
                </div>
                
                <Button
                  type="submit"
                  variant="primary"
                  size="lg"
                  fullWidth
                  loading={loading}
                >
                  পাসওয়ার্ড রিসেট করুন
                </Button>
              </form>
            </>
          )}
        </div>
      </div>
    </div>
  )
}
