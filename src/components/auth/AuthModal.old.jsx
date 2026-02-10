import { Phone, Shield, Loader } from 'lucide-react'
import { useState } from 'react'
import Button from '../common/Button'
import { apiCall } from '../../config'

export default function AuthModal({ isOpen, onClose }) {
  const [step, setStep] = useState('phone') // 'phone', 'otp', 'name'
  const [phone, setPhone] = useState('')
  const [otp, setOtp] = useState('')
  const [name, setName] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [otpSent, setOtpSent] = useState(false)
  const [countdown, setCountdown] = useState(0)
  const [isNewUser, setIsNewUser] = useState(false)
  
  if (!isOpen) return null

  // Start countdown timer
  const startCountdown = () => {
    setCountdown(300) // 5 minutes in seconds
    const timer = setInterval(() => {
      setCountdown((prev) => {
        if (prev <= 1) {
          clearInterval(timer)
          return 0
        }
        return prev - 1
      })
    }, 1000)
  }

  const handleSendOTP = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError('')

    // Validate Bangladeshi phone number
    const phoneRegex = /^01[3-9]\d{8}$/
    if (!phoneRegex.test(phone)) {
      setError('সঠিক ফোন নম্বর লিখুন (01XXXXXXXXX)')
      setLoading(false)
      return
    }

    try {
      const response = await apiCall('/auth/send-otp', {
        method: 'POST',
        body: JSON.stringify({ phone })
      })

      if (response.success) {
        setOtpSent(true)
        setStep('otp')
        startCountdown()
        // For development: auto-fill OTP (REMOVE IN PRODUCTION)
        if (response.otp) {
          console.log('🔐 OTP:', response.otp)
        }
      }
    } catch (err) {
      setError(err.message || 'OTP পাঠাতে সমস্যা হয়েছে')
    } finally {
      setLoading(false)
    }
  }

  const handleVerifyOTP = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError('')

    try {
      const payload = { phone, otp }
      
      // If new user needs name
      if (isNewUser && name) {
        payload.name = name
      }

      const response = await apiCall('/auth/verify-otp', {
        method: 'POST',
        body: JSON.stringify(payload)
      })

      if (response.success) {
        // Check if this is a new user who needs to provide name
        if (response.data.is_new_user && !name) {
          setIsNewUser(true)
          setStep('name')
          setLoading(false)
          return
        }

        // Store auth token
        localStorage.setItem('auth_token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))

        // Success
        alert('✅ ' + (response.data.is_new_user ? 'রেজিস্ট্রেশন সফল হয়েছে!' : 'লগইন সফল হয়েছে!'))
        onClose()
        window.location.reload()
      }
    } catch (err) {
      if (err.message?.includes('Name is required')) {
        setIsNewUser(true)
        setStep('name')
      } else {
        setError(err.message || 'OTP যাচাইকরণ ব্যর্থ হয়েছে')
      }
    } finally {
      setLoading(false)
    }
  }

  const handleGoogleLogin = () => {
    setLoading(true)
    setError('')

    // Open Google OAuth in popup
    const width = 500
    const height = 600
    const left = window.screen.width / 2 - width / 2
    const top = window.screen.height / 2 - height / 2

    const popup = window.open(
      `${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/api/auth/google`,
      'Google Login',
      `width=${width},height=${height},left=${left},top=${top}`
    )

    // Listen for OAuth response
    window.addEventListener('message', (event) => {
      if (event.data.type === 'GOOGLE_AUTH_SUCCESS') {
        localStorage.setItem('auth_token', event.data.token)
        localStorage.setItem('user', JSON.stringify(event.data.user))
        alert('✅ Google লগইন সফল হয়েছে!')
        onClose()
        window.location.reload()
        popup?.close()
      } else if (event.data.type === 'GOOGLE_AUTH_ERROR') {
        setError('Google লগইন ব্যর্থ হয়েছে')
        setLoading(false)
        popup?.close()
      }
    })
  }

  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }

  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="bg-white rounded-2xl max-w-md w-full shadow-2xl relative animate-fadeIn" onClick={(e) => e.stopPropagation()}>
        <div className="p-8">
          {/* Header */}
          <div className="text-center mb-6">
            <div className="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-700 rounded-full flex items-center justify-center mx-auto mb-4">
              {step === 'phone' && <Phone size={32} className="text-white" />}
              {step === 'otp' && <Shield size={32} className="text-white" />}
              {step === 'name' && <Shield size={32} className="text-white" />}
            </div>
            <h2 className="text-3xl font-bold text-gray-900 mb-2">
              {step === 'phone' && 'লগইন করুন'}
              {step === 'otp' && 'OTP যাচাই করুন'}
              {step === 'name' && 'আপনার নাম লিখুন'}
            </h2>
            <p className="text-gray-600">
              {step === 'phone' && 'Google অথবা ফোন নম্বর দিয়ে লগইন করুন'}
              {step === 'otp' && `${phone} নম্বরে পাঠানো OTP লিখুন`}
              {step === 'name' && 'নতুন অ্যাকাউন্ট তৈরি করতে আপনার নাম লিখুন'}
            </p>
          </div>

          {/* Error Message */}
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
              {error}
            </div>
          )}

          {/* Phone Number Step */}
          {step === 'phone' && (
            <>
              {/* Google Login Button */}
              <button
                onClick={handleGoogleLogin}
                disabled={loading}
                className="w-full bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 py-3 rounded-xl font-bold transition-all mb-4 flex items-center justify-center gap-3 disabled:opacity-50"
              >
                <svg className="w-5 h-5" viewBox="0 0 24 24">
                  <path
                    fill="#4285F4"
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  />
                  <path
                    fill="#34A853"
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  />
                  <path
                    fill="#FBBC05"
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  />
                  <path
                    fill="#EA4335"
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  />
                </svg>
                Google দিয়ে লগইন করুন
              </button>

              <div className="flex items-center gap-4 my-6">
                <div className="flex-1 h-px bg-gray-300"></div>
                <span className="text-gray-500 text-sm font-medium">অথবা</span>
                <div className="flex-1 h-px bg-gray-300"></div>
              </div>

              {/* Phone Number Form */}
              <form onSubmit={handleSendOTP}>
                <div className="mb-4">
                  <label className="block text-sm font-semibold text-gray-700 mb-2">
                    ফোন নম্বর
                  </label>
                  <div className="relative">
                    <Phone className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
                    <input
                      type="tel"
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                      placeholder="01XXXXXXXXX"
                      className="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 transition-colors"
                      required
                      maxLength="11"
                    />
                  </div>
                  <p className="text-xs text-gray-500 mt-1">বাংলাদেশী মোবাইল নম্বর লিখুন</p>
                </div>

                <Button
                  type="submit"
                  variant="primary"
                  size="lg"
                  fullWidth
                  disabled={loading}
                >
                  {loading ? (
                    <>
                      <Loader className="animate-spin mr-2" size={20} />
                      পাঠানো হচ্ছে...
                    </>
                  ) : (
                    'OTP পাঠান'
                  )}
                </Button>
              </form>
            </>
          )}

          {/* OTP Verification Step */}
          {step === 'otp' && (
            <form onSubmit={handleVerifyOTP}>
              <div className="mb-6">
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  OTP কোড (৬ সংখ্যা)
                </label>
                <input
                  type="text"
                  value={otp}
                  onChange={(e) => setOtp(e.target.value.replace(/\D/g, ''))}
                  placeholder="000000"
                  className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 transition-colors text-center text-2xl tracking-widest font-mono"
                  required
                  maxLength="6"
                  autoFocus
                />
                {countdown > 0 && (
                  <p className="text-sm text-teal-600 mt-2 text-center font-semibold">
                    ⏱️ {formatTime(countdown)} সময় বাকি
                  </p>
                )}
              </div>

              <Button
                type="submit"
                variant="primary"
                size="lg"
                fullWidth
                disabled={loading || otp.length !== 6}
              >
                {loading ? (
                  <>
                    <Loader className="animate-spin mr-2" size={20} />
                    যাচাই করা হচ্ছে...
                  </>
                ) : (
                  'যাচাই করুন'
                )}
              </Button>

              <div className="mt-4 text-center">
                <button
                  type="button"
                  onClick={() => {
                    setStep('phone')
                    setOtp('')
                    setError('')
                  }}
                  className="text-sm text-teal-600 hover:text-teal-700 font-semibold"
                  disabled={loading}
                >
                  নম্বর পরিবর্তন করুন
                </button>
                {countdown === 0 && (
                  <>
                    <span className="text-gray-400 mx-2">•</span>
                    <button
                      type="button"
                      onClick={handleSendOTP}
                      className="text-sm text-teal-600 hover:text-teal-700 font-semibold"
                      disabled={loading}
                    >
                      পুনরায় OTP পাঠান
                    </button>
                  </>
                )}
              </div>
            </form>
          )}

          {/* Name Input Step (for new users) */}
          {step === 'name' && (
            <form onSubmit={handleVerifyOTP}>
              <div className="mb-6">
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  আপনার পূর্ণ নাম
                </label>
                <input
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="নাম লিখুন"
                  className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 transition-colors"
                  required
                  autoFocus
                />
              </div>

              <Button
                type="submit"
                variant="primary"
                size="lg"
                fullWidth
                disabled={loading || !name.trim()}
              >
                {loading ? (
                  <>
                    <Loader className="animate-spin mr-2" size={20} />
                    রেজিস্ট্রেশন হচ্ছে...
                  </>
                ) : (
                  'রেজিস্ট্রেশন সম্পন্ন করুন'
                )}
              </Button>
            </form>
          )}

          {/* Privacy Notice */}
          <p className="text-xs text-gray-500 text-center mt-6">
            লগইন করলে আপনি আমাদের{' '}
            <a href="#" className="text-teal-600 hover:underline">
              শর্তাবলী
            </a>{' '}
            এবং{' '}
            <a href="#" className="text-teal-600 hover:underline">
              গোপনীয়তা নীতি
            </a>{' '}
            মেনে নিচ্ছেন
          </p>
        </div>
      </div>
    </div>
  )
}
