import { Phone, Shield, Loader } from 'lucide-react'
import { useState, useEffect } from 'react'
import Button from '../common/Button'
import { apiCall } from '../../config'
import { 
  signInWithGoogle, 
  sendPhoneOTP, 
  verifyPhoneOTP, 
  setupRecaptcha,
  clearRecaptcha,
  getFirebaseToken 
} from '../../firebase'

export default function AuthModal({ isOpen, onClose }) {
  const [step, setStep] = useState('phone') // 'phone', 'otp', 'name'
  const [phone, setPhone] = useState('')
  const [otp, setOtp] = useState('')
  const [name, setName] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [countdown, setCountdown] = useState(0)
  const [isNewUser, setIsNewUser] = useState(false)
  const [confirmationResult, setConfirmationResult] = useState(null)

  useEffect(() => {
    // Setup reCAPTCHA when modal opens
    if (isOpen) {
      setupRecaptcha('recaptcha-container')
    }
    // Cleanup on unmount
    return () => {
      clearRecaptcha()
    }
  }, [isOpen])
  
  if (!isOpen) return null

  // Start countdown timer
  const startCountdown = () => {
    setCountdown(60) // 60 seconds to match Firebase OTP expiration
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

  // Handle Google Sign-In
  const handleGoogleSignIn = async () => {
    setLoading(true)
    setError('')

    try {
      // Sign in with Google using Firebase
      const firebaseUser = await signInWithGoogle()
      
      // Get Firebase ID token
      const idToken = await getFirebaseToken()

      // Sync with Laravel backend
      const response = await apiCall('/auth/firebase-login', {
        method: 'POST',
        body: JSON.stringify({
          firebase_uid: firebaseUser.uid,
          email: firebaseUser.email,
          name: firebaseUser.displayName,
          avatar: firebaseUser.photoURL,
          provider: 'google',
          firebase_token: idToken
        })
      })

      if (response.success) {
        // Store auth token
        localStorage.setItem('auth_token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))

        alert('✅ Google লগইন সফল হয়েছে!')
        window.location.reload()
      }
    } catch (err) {
      console.error('Google sign-in error:', err)
      setError(err.message || 'Google লগইন ব্যর্থ হয়েছে')
    } finally {
      setLoading(false)
    }
  }

  // Handle Send OTP via Firebase
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
      // Format phone number with Bangladesh country code
      const formattedPhone = `+880${phone.substring(1)}`
      
      // Send OTP using Firebase
      const confirmation = await sendPhoneOTP(formattedPhone)
      
      setConfirmationResult(confirmation)
      setStep('otp')
      startCountdown()
      
    } catch (err) {
      console.error('Send OTP error:', err)
      setError(err.message || 'OTP পাঠাতে সমস্যা হয়েছে')
    } finally {
      setLoading(false)
    }
  }

  // Handle Verify OTP via Firebase
  const handleVerifyOTP = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError('')

    if (!otp || otp.length !== 6) {
      setError('৬ ডিজিটের OTP লিখুন')
      setLoading(false)
      return
    }

    try {
      // Verify OTP with Firebase
      const firebaseUser = await verifyPhoneOTP(confirmationResult, otp)
      
      // Get Firebase ID token
      const idToken = await getFirebaseToken()

      // Format phone for backend
      const formattedPhone = `+880${phone.substring(1)}`

      // Sync with Laravel backend
      const response = await apiCall('/auth/firebase-login', {
        method: 'POST',
        body: JSON.stringify({
          firebase_uid: firebaseUser.uid,
          phone: formattedPhone,
          provider: 'phone',
          firebase_token: idToken,
          name: name || undefined
        })
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

        alert('✅ ' + (response.data.is_new_user ? 'রেজিস্ট্রেশন সফল হয়েছে!' : 'লগইন সফল হয়েছে!'))
        window.location.reload()
      }
    } catch (err) {
      console.error('Verify OTP error:', err)
      
      // Handle code expired error specially
      if (err.message && err.message.includes('expired')) {
        setError('⏱️ OTP কোডটি মেয়াদ শেষ হয়ে গেছে। নতুন কোড পাঠান।')
        setCountdown(0) // Allow immediate resend
      } else {
        setError(err.message || 'OTP যাচাই করতে ব্যর্থ')
      }
    } finally {
      setLoading(false)
    }
  }

  // Handle Name Submission for New Users
  const handleNameSubmit = async (e) => {
    e.preventDefault()
    
    if (!name.trim()) {
      setError('আপনার নাম লিখুন')
      return
    }

    // Go back to verify OTP step with name filled
    await handleVerifyOTP(e)
  }

  // Format countdown timer
  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
  }

  const handleResendOTP = async () => {
    setOtp('')
    setError('')
    await handleSendOTP(new Event('submit'))
  }

  return (
    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" onClick={onClose}>
      <div className="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative" onClick={(e) => e.stopPropagation()}>
        {/* Hidden reCAPTCHA container */}
        <div id="recaptcha-container"></div>

        {/* Header */}
        <div className="text-center mb-8">
          <div className="mx-auto w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-4">
            {step === 'phone' ? <Phone className="w-8 h-8 text-white" /> : <Shield className="w-8 h-8 text-white" />}
          </div>
          <h2 className="text-2xl font-bold text-gray-800">
            {step === 'phone' && 'লগইন / রেজিস্টার'}
            {step === 'otp' && 'OTP যাচাই করুন'}
            {step === 'name' && 'আপনার নাম লিখুন'}
          </h2>
          <p className="text-gray-600 mt-2">
            {step === 'phone' && 'ফোন নম্বর অথবা Google দিয়ে লগইন করুন'}
            {step === 'otp' && `${phone} নম্বরে পাঠানো OTP লিখুন`}
            {step === 'name' && 'আপনার প্রোফাইল সম্পন্ন করুন'}
          </p>
        </div>

        {error && (
          <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
            {error}
          </div>
        )}

        {/* Phone Number Step */}
        {step === 'phone' && (
          <form onSubmit={handleSendOTP} className="space-y-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                ফোন নম্বর
              </label>
              <div className="relative">
                <Phone className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input
                  type="tel"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value.replace(/\D/g, '').substring(0, 11))}
                  placeholder="01712345678"
                  className="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  required
                  maxLength={11}
                  disabled={loading}
                />
              </div>
              <p className="text-xs text-gray-500 mt-2">
                নম্বর দিয়ে ০১ দিয়ে শুরু হতে হবে (যেমনঃ 01712345678)
              </p>
            </div>

            <Button type="submit" disabled={loading || phone.length !== 11} className="w-full">
              {loading ? (
                <>
                  <Loader className="animate-spin w-5 h-5 mr-2" />
                  পাঠানো হচ্ছে...
                </>
              ) : (
                'OTP পাঠান'
              )}
            </Button>

            <div className="relative">
              <div className="absolute inset-0 flex items-center">
                <div className="w-full border-t border-gray-300"></div>
              </div>
              <div className="relative flex justify-center text-sm">
                <span className="px-4 bg-white text-gray-500">অথবা</span>
              </div>
            </div>

            <button
              type="button"
              onClick={handleGoogleSignIn}
              disabled={loading}
              className="w-full flex items-center justify-center gap-3 px-4 py-3 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg className="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              <span className="font-medium text-gray-700">Google দিয়ে লগইন করুন</span>
            </button>
          </form>
        )}

        {/* OTP Verification Step */}
        {step === 'otp' && (
          <form onSubmit={handleVerifyOTP} className="space-y-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                OTP কোড (৬০ সেকেন্ডের মধ্যে লিখুন)
              </label>
              <input
                type="text"
                inputMode="numeric"
                value={otp}
                onChange={(e) => setOtp(e.target.value.replace(/\D/g, '').substring(0, 6))}
                placeholder="০১২৩৪৫"
                className="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
                maxLength={6}
                disabled={loading}
                autoFocus
              />
              <p className="text-xs text-gray-500 mt-2 text-center">
                📱 {phone} নম্বরে পাঠানো কোড
              </p>
            </div>

            {countdown > 0 && (
              <div className="text-center text-sm text-gray-600">
                আবার পাঠান {formatTime(countdown)} পরে
              </div>
            )}

            {countdown === 0 && (
              <button
                type="button"
                onClick={handleResendOTP}
                className="w-full text-sm text-blue-600 hover:text-blue-700 font-medium"
                disabled={loading}
              >
                OTP আবার পাঠান
              </button>
            )}

            <Button type="submit" disabled={loading || otp.length !== 6} className="w-full">
              {loading ? (
                <>
                  <Loader className="animate-spin w-5 h-5 mr-2" />
                  যাচাই করা হচ্ছে...
                </>
              ) : (
                'যাচাই করুন'
              )}
            </Button>

            <button
              type="button"
              onClick={() => {
                setStep('phone')
                setOtp('')
                setError('')
              }}
              className="w-full text-sm text-gray-600 hover:text-gray-700"
              disabled={loading}
            >
              ← নম্বর পরিবর্তন করুন
            </button>
          </form>
        )}

        {/* Name Input Step (for new users) */}
        {step === 'name' && (
          <form onSubmit={handleNameSubmit} className="space-y-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                আপনার নাম
              </label>
              <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="আপনার পুরো নাম লিখুন"
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
                disabled={loading}
                autoFocus
              />
            </div>

            <Button type="submit" disabled={loading || !name.trim()} className="w-full">
              {loading ? (
                <>
                  <Loader className="animate-spin w-5 h-5 mr-2" />
                  সম্পন্ন করা হচ্ছে...
                </>
              ) : (
                'সম্পন্ন করুন'
              )}
            </Button>
          </form>
        )}

        {/* Footer */}
        <p className="mt-6 text-xs text-center text-gray-500">
          লগইন করে, আপনি আমাদের{' '}
          <a href="#" className="text-blue-600 hover:underline">
            শর্তাবলী
          </a>{' '}
          এবং{' '}
          <a href="#" className="text-blue-600 hover:underline">
            গোপনীয়তা নীতি
          </a>{' '}
          মেনে নিচ্ছেন।
        </p>
      </div>
    </div>
  )
}
