import { X, Mail, CheckCircle } from 'lucide-react'
import { useState, useEffect } from 'react'
import Button from './common/Button'
import { apiCall } from '../config'

export default function EmailVerificationBanner({ user, onClose, onVerified }) {
  const [loading, setLoading] = useState(false)
  const [success, setSuccess] = useState(false)
  const [error, setError] = useState('')
  
  // Don't show if email is already verified
  if (!user || user.email_verified_at) return null
  
  // Check verification status periodically
  useEffect(() => {
    const checkVerification = async () => {
      try {
        const response = await apiCall('/email/status', {
          method: 'GET'
        })
        
        if (response.success && response.data.email_verified) {
          // Email has been verified, refresh user data
          if (onVerified) {
            onVerified()
          }
        }
      } catch (err) {
        // Silently fail
      }
    }
    
    // Check immediately and then every 30 seconds
    checkVerification()
    const interval = setInterval(checkVerification, 30000)
    
    return () => clearInterval(interval)
  }, [onVerified])
  
  const handleResend = async () => {
    setLoading(true)
    setError('')
    
    try {
      const token = localStorage.getItem('auth_token')
      if (!token) {
        setError('আপনি লগইন করা নেই। অনুগ্রহ করে লগইন করুন।')
        setLoading(false)
        return
      }

      const response = await apiCall('/email/resend', {
        method: 'POST'
      })
      
      if (response.success) {
        setSuccess(true)
        setTimeout(() => setSuccess(false), 5000)
      }
    } catch (err) {
      console.error('Email resend error:', err)
      const errorMessage = err.message === 'Server error occurred' 
        ? 'সার্ভার সমস্যা হয়েছে। আবার চেষ্টা করুন।'
        : err.message || 'ইমেইল পাঠাতে সমস্যা হয়েছে'
      setError(errorMessage)
    } finally {
      setLoading(false)
    }
  }
  
  return (
    <div className="bg-amber-50 border-b-2 border-amber-200">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-3 flex-1">
            <Mail className="text-amber-600" size={20} />
            <div className="flex-1">
              <p className="text-sm font-semibold text-amber-900">
                আপনার ইমেইল ভেরিফাই করা হয়নি
              </p>
              <p className="text-xs text-amber-700">
                অনুগ্রহ করে আপনার ইমেইল চেক করুন এবং ভেরিফিকেশন লিঙ্কে ক্লিক করুন। {' '}
                {success ? (
                  <span className="text-green-600 font-semibold">
                    ✓ ইমেইল পাঠানো হয়েছে!
                  </span>
                ) : (
                  <button 
                    onClick={handleResend}
                    disabled={loading}
                    className="text-amber-800 font-semibold hover:underline disabled:opacity-50"
                  >
                    {loading ? 'পাঠানো হচ্ছে...' : 'আবার পাঠান'}
                  </button>
                )}
              </p>
              {error && (
                <p className="text-xs text-red-600 mt-1">{error}</p>
              )}
            </div>
          </div>
          {onClose && (
            <button 
              onClick={onClose}
              className="p-1 hover:bg-amber-100 rounded transition-colors"
            >
              <X size={18} className="text-amber-600" />
            </button>
          )}
        </div>
      </div>
    </div>
  )
}
