import { X, Mail, Lock, User, Eye, EyeOff, Phone } from 'lucide-react'
import { useState } from 'react'
import Button from '../common/Button'
import { apiCall } from '../../config'

export default function RegisterModal({ isOpen, onClose, onSwitchToLogin }) {
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)
  const [formData, setFormData] = useState({
    name: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: ''
  })
  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState({})
  const [apiError, setApiError] = useState('')
  
  if (!isOpen) return null
  
  const validate = () => {
    const newErrors = {}
    
    if (!formData.name) newErrors.name = 'নাম প্রয়োজন'
    if (!formData.phone) newErrors.phone = 'ফোন নম্বর প্রয়োজন'
    if (!formData.email) newErrors.email = 'ইমেইল প্রয়োজন'
    if (!formData.password) newErrors.password = 'পাসওয়ার্ড প্রয়োজন'
    if (formData.password.length < 6) newErrors.password = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে'
    if (formData.password !== formData.password_confirmation) {
      newErrors.password_confirmation = 'পাসওয়ার্ড মিলছে না'
    }
    
    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }
  
  const handleSubmit = async (e) => {
    e.preventDefault()
    
    if (!validate()) return
    
    setLoading(true)
    setApiError('')
    
    try {
      const response = await apiCall('/register', {
        method: 'POST',
        body: JSON.stringify(formData),
      })
      
      if (response.success) {
        // Store auth token
        localStorage.setItem('auth_token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        
        // Show success message
        alert('রেজিস্ট্রেশন সফল হয়েছে! ✅')
        
        // Close modal and refresh page
        onClose()
        window.location.reload()
      }
    } catch (err) {
      setApiError(err.message || 'রেজিস্ট্রেশন ব্যর্থ হয়েছে। আবার চেষ্টা করুন।')
    } finally {
      setLoading(false)
    }
  }
  
  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
    // Clear error when user starts typing
    if (errors[e.target.name]) {
      setErrors({
        ...errors,
        [e.target.name]: ''
      })
    }
  }
  
  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
      <div className="bg-white rounded-2xl max-w-md w-full shadow-2xl relative my-8">
        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute top-4 right-4 w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors z-10"
        >
          <X size={24} />
        </button>
        
        {/* Header */}
        <div className="p-8 pb-6">
          <div className="text-center mb-6">
            <div className="w-16 h-16 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center mx-auto mb-4">
              <User size={32} />
            </div>
            <h2 className="text-3xl font-bold text-gray-900 mb-2">রেজিস্টার করুন</h2>
            <p className="text-gray-600">নতুন অ্যাকাউন্ট তৈরি করুন</p>
          </div>
          
          {/* Form */}
          <form onSubmit={handleSubmit} className="space-y-4">
            {/* API Error Message */}
            {apiError && (
              <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                {apiError}
              </div>
            )}
            
            {/* Name */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                পূর্ণ নাম <span className="text-red-500">*</span>
              </label>
              <div className="relative">
                <User className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                  type="text"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  placeholder="আপনার নাম লিখুন"
                  className={`w-full pl-12 pr-4 py-3 border-2 ${errors.name ? 'border-red-500' : 'border-gray-300'} rounded-lg focus:outline-none focus:border-primary-500 transition-colors`}
                />
              </div>
              {errors.name && <p className="mt-1 text-sm text-red-500">{errors.name}</p>}
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
                  className={`w-full pl-12 pr-4 py-3 border-2 ${errors.phone ? 'border-red-500' : 'border-gray-300'} rounded-lg focus:outline-none focus:border-primary-500 transition-colors`}
                />
              </div>
              {errors.phone && <p className="mt-1 text-sm text-red-500">{errors.phone}</p>}
            </div>
            
            {/* Email */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                ইমেইল <span className="text-red-500">*</span>
              </label>
              <div className="relative">
                <Mail className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                  type="email"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="example@email.com"
                  className={`w-full pl-12 pr-4 py-3 border-2 ${errors.email ? 'border-red-500' : 'border-gray-300'} rounded-lg focus:outline-none focus:border-primary-500 transition-colors`}
                />
              </div>
              {errors.email && <p className="mt-1 text-sm text-red-500">{errors.email}</p>}
            </div>
            
            {/* Password */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                পাসওয়ার্ড <span className="text-red-500">*</span>
              </label>
              <div className="relative">
                <Lock className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                  type={showPassword ? 'text' : 'password'}
                  name="password"
                  value={formData.password}
                  onChange={handleChange}
                  placeholder="কমপক্ষে ৬ অক্ষর"
                  className={`w-full pl-12 pr-12 py-3 border-2 ${errors.password ? 'border-red-500' : 'border-gray-300'} rounded-lg focus:outline-none focus:border-primary-500 transition-colors`}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
              </div>
              {errors.password && <p className="mt-1 text-sm text-red-500">{errors.password}</p>}
            </div>
            
            {/* Confirm Password */}
            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">
                পাসওয়ার্ড নিশ্চিত করুন <span className="text-red-500">*</span>
              </label>
              <div className="relative">
                <Lock className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                <input
                  type={showConfirmPassword ? 'text' : 'password'}
                  name="password_confirmation"
                  value={formData.password_confirmation}
                  onChange={handleChange}
                  placeholder="পাসওয়ার্ড আবার লিখুন"
                  className={`w-full pl-12 pr-12 py-3 border-2 ${errors.password_confirmation ? 'border-red-500' : 'border-gray-300'} rounded-lg focus:outline-none focus:border-primary-500 transition-colors`}
                />
                <button
                  type="button"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                  className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  {showConfirmPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
              </div>
              {errors.password_confirmation && <p className="mt-1 text-sm text-red-500">{errors.password_confirmation}</p>}
            </div>
            
            {/* Terms */}
            <div className="flex items-start gap-2">
              <input 
                type="checkbox" 
                id="terms" 
                required
                className="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
              />
              <label htmlFor="terms" className="text-sm text-gray-600">
                আমি <a href="#" className="text-primary-600 hover:text-primary-700 font-semibold">শর্তাবলী</a> এবং{' '}
                <a href="#" className="text-primary-600 hover:text-primary-700 font-semibold">গোপনীয়তা নীতি</a> সম্মত
              </label>
            </div>
            
            {/* Submit Button */}
            <Button
              type="submit"
              variant="secondary"
              size="lg"
              fullWidth
              loading={loading}
            >
              রেজিস্টার করুন
            </Button>
          </form>
          
          {/* Login Link */}
          <div className="text-center mt-6">
            <p className="text-gray-600">
              ইতিমধ্যে অ্যাকাউন্ট আছে?{' '}
              <button 
                type="button"
                onClick={onSwitchToLogin}
                className="text-primary-600 hover:text-primary-700 font-semibold"
              >
                লগইন করুন
              </button>
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
