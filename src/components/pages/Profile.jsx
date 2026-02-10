import { useState, useEffect, useRef } from 'react'
import { ArrowLeft, User, Mail, Phone, MapPin, Lock, Camera, Save, CheckCircle, Shield, Briefcase, Settings, Moon, Sun, Globe } from 'lucide-react'
import { apiCall } from '../../config'
import { useSettings } from '../../contexts/SettingsContext'
import { useTranslation } from '../../utils/translations'

export default function Profile({ onBack, user, onBecomeDealer }) {
  const avatarInputRef = useRef(null)
  const { darkMode, language, toggleDarkMode, changeLanguage } = useSettings()
  const { t } = useTranslation(language)
  const [loading, setLoading] = useState(false)
  const [uploadingAvatar, setUploadingAvatar] = useState(false)
  const [activeTab, setActiveTab] = useState('profile') // profile, password, settings
  const [avatarPreview, setAvatarPreview] = useState(null)
  const [profileData, setProfileData] = useState({
    name: '',
    email: '',
    phone: '',
    address: ''
  })
  const [passwordData, setPasswordData] = useState({
    current_password: '',
    password: '',
    password_confirmation: ''
  })
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState('')

  useEffect(() => {
    fetchProfile()
  }, [])

  const fetchProfile = async () => {
    try {
      const response = await apiCall('/users/profile')
      if (response.success) {
        setProfileData(response.data)
      }
    } catch (error) {
      console.error('Error fetching profile:', error)
    }
  }

  const handleProfileChange = (e) => {
    setProfileData({
      ...profileData,
      [e.target.name]: e.target.value
    })
    if (errors[e.target.name]) {
      setErrors({ ...errors, [e.target.name]: '' })
    }
  }

  const handlePasswordChange = (e) => {
    setPasswordData({
      ...passwordData,
      [e.target.name]: e.target.value
    })
    if (errors[e.target.name]) {
      setErrors({ ...errors, [e.target.name]: '' })
    }
  }

  const handleProfileSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setErrors({})
    setSuccessMessage('')

    try {
      const response = await apiCall('/users/profile', {
        method: 'PUT',
        body: JSON.stringify(profileData)
      })

      if (response.success) {
        setSuccessMessage('প্রোফাইল আপডেট সফল হয়েছে!')
        // Update localStorage
        const user = JSON.parse(localStorage.getItem('user'))
        localStorage.setItem('user', JSON.stringify({ ...user, ...profileData }))
        setTimeout(() => setSuccessMessage(''), 3000)
      }
    } catch (error) {
      if (error.response?.data?.errors) {
        setErrors(error.response.data.errors)
      } else {
        setErrors({ general: 'প্রোফাইল আপডেট করতে সমস্যা হয়েছে' })
      }
    } finally {
      setLoading(false)
    }
  }

  const handlePasswordSubmit = async (e) => {
    e.preventDefault()
    
    if (passwordData.password !== passwordData.password_confirmation) {
      setErrors({ password_confirmation: 'পাসওয়ার্ড মিলছে না' })
      return
    }

    setLoading(true)
    setErrors({})
    setSuccessMessage('')

    try {
      const response = await apiCall('/users/password', {
        method: 'PUT',
        body: JSON.stringify(passwordData)
      })

      if (response.success) {
        setSuccessMessage('পাসওয়ার্ড পরিবর্তন সফল হয়েছে!')
        setPasswordData({
          current_password: '',
          password: '',
          password_confirmation: ''
        })
        setTimeout(() => setSuccessMessage(''), 3000)
      }
    } catch (error) {
      if (error.response?.data?.errors) {
        setErrors(error.response.data.errors)
      } else {
        setErrors({ general: 'পাসওয়ার্ড পরিবর্তন করতে সমস্যা হয়েছে' })
      }
    } finally {
      setLoading(false)
    }
  }

  const handleAvatarClick = () => {
    avatarInputRef.current?.click()
  }

  const handleAvatarChange = async (e) => {
    const file = e.target.files?.[0]
    if (!file) return

    // Validate file type
    if (!file.type.startsWith('image/')) {
      setErrors({ avatar: 'শুধুমাত্র ছবি ফাইল আপলোড করা যাবে' })
      return
    }

    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
      setErrors({ avatar: 'ছবি সর্বোচ্চ ২ MB হতে পারে' })
      return
    }

    setUploadingAvatar(true)
    setErrors({})

    try {
      const formData = new FormData()
      formData.append('avatar', file)

      const response = await apiCall('/users/avatar', {
        method: 'POST',
        body: formData
      })

      if (response.success) {
        // Update avatar preview
        setAvatarPreview(response.data.avatar_url)
        setSuccessMessage('ছবি আপলোড সফল হয়েছে!')
        
        // Update localStorage user data
        const userData = JSON.parse(localStorage.getItem('user') || '{}')
        userData.avatar = response.data.avatar
        localStorage.setItem('user', JSON.stringify(userData))
        
        // Trigger page reload to update header
        window.location.reload()
        
        setTimeout(() => setSuccessMessage(''), 3000)
      }
    } catch (error) {
      if (error.response?.data?.errors) {
        setErrors(error.response.data.errors)
      } else {
        setErrors({ avatar: 'ছবি আপলোড করতে সমস্যা হয়েছে' })
      }
    } finally {
      setUploadingAvatar(false)
    }
  }

  return (
    <div className="min-h-screen bg-orange-50">
      {/* Header */}
      <div className="bg-teal-700 text-white py-8">
        <div className="max-w-4xl mx-auto px-4">
          <button
            onClick={onBack}
            className="flex items-center gap-2 text-white hover:text-gray-200 mb-4"
          >
            <ArrowLeft size={20} />
            <span>ফিরে যান</span>
          </button>
          <h1 className="text-3xl font-black">আমার প্রোফাইল</h1>
          <p className="text-teal-100 mt-2">আপনার ব্যক্তিগত তথ্য দেখুন এবং পরিবর্তন করুন</p>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 py-8">
        {/* Success Message */}
        {successMessage && (
          <div className="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6">
            {successMessage}
          </div>
        )}

        {/* Error Message */}
        {errors.general && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6">
            {errors.general}
          </div>
        )}

        {/* Profile Card */}
        <div className="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
          {/* Profile Header */}
          <div className="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-8">
            <div className="flex items-center gap-6">
              <div className="relative">
                <div className="w-24 h-24 bg-white rounded-full flex items-center justify-center overflow-hidden">
                  {user?.avatar || avatarPreview ? (
                    <img 
                      src={avatarPreview || `/storage/${user.avatar}`} 
                      alt="Profile" 
                      className="w-full h-full object-cover"
                    />
                  ) : (
                    <User size={48} className="text-teal-600" />
                  )}
                </div>
                <input
                  ref={avatarInputRef}
                  type="file"
                  accept="image/*"
                  onChange={handleAvatarChange}
                  className="hidden"
                />
                <button 
                  type="button"
                  onClick={handleAvatarClick}
                  disabled={uploadingAvatar}
                  className="absolute bottom-0 right-0 w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center border-4 border-white hover:bg-teal-700 transition-colors disabled:opacity-50"
                >
                  <Camera size={16} className="text-white" />
                </button>
              </div>
              <div>
                <h2 className="text-2xl font-black text-white flex items-center gap-2">
                  {profileData.name}
                  {user?.is_verified_dealer && (
                    <span className="inline-flex items-center gap-1 bg-green-500 text-white text-sm px-3 py-1 rounded-full">
                      <CheckCircle size={16} />
                      ভেরিফাইড
                    </span>
                  )}
                </h2>
                <p className="text-teal-100">{profileData.email}</p>
                {user?.dealer && user.dealer.status === 'pending' && (
                  <span className="inline-flex items-center gap-1 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full mt-2">
                    <Shield size={14} />
                    ডিলার আবেদন পর্যালোচনাধীন
                  </span>
                )}
              </div>
            </div>
          </div>

          {/* Tabs */}
          <div className="border-b border-gray-200">
            <div className="flex">
              <button
                onClick={() => setActiveTab('profile')}
                className={`flex-1 px-6 py-4 font-bold transition-colors ${
                  activeTab === 'profile'
                    ? 'text-teal-600 border-b-2 border-teal-600'
                    : 'text-gray-600 hover:text-gray-900'
                }`}
              >
                ব্যক্তিগত তথ্য
              </button>
              <button
                onClick={() => setActiveTab('password')}
                className={`flex-1 px-6 py-4 font-bold transition-colors ${
                  activeTab === 'password'
                    ? 'text-teal-600 border-b-2 border-teal-600'
                    : 'text-gray-600 hover:text-gray-900'
                }`}
              >
                পাসওয়ার্ড পরিবর্তন
              </button>
              <button
                onClick={() => setActiveTab('settings')}
                className={`flex-1 px-6 py-4 font-bold transition-colors ${
                  activeTab === 'settings'
                    ? 'text-teal-600 border-b-2 border-teal-600'
                    : 'text-gray-600 hover:text-gray-900'
                }`}
              >
                সেটিংস
              </button>
            </div>
          </div>

          {/* Tab Content */}
          <div className="p-8">
            {activeTab === 'settings' ? (
              <div className="space-y-8">
                {/* Dark Mode Toggle */}
                <div className="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                        {darkMode ? <Moon size={24} className="text-white" /> : <Sun size={24} className="text-white" />}
                      </div>
                      <div>
                        <h3 className="text-lg font-black text-gray-900">ডার্ক মোড</h3>
                        <p className="text-sm text-gray-600">রাতের জন্য গাঢ় থিম সক্রিয় করুন</p>
                      </div>
                    </div>
                    <button
                      type="button"
                      onClick={toggleDarkMode}
                      className={`relative w-16 h-8 rounded-full transition-colors duration-300 ${
                        darkMode ? 'bg-teal-600' : 'bg-gray-300'
                      }`}
                    >
                      <div
                        className={`absolute top-1 left-1 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300 ${
                          darkMode ? 'transform translate-x-8' : ''
                        }`}
                      />
                    </button>
                  </div>
                </div>

                {/* Language Switcher */}
                <div className="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200">
                  <div className="flex items-center gap-4 mb-4">
                    <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center">
                      <Globe size={24} className="text-white" />
                    </div>
                    <div>
                      <h3 className="text-lg font-black text-gray-900">ভাষা নির্বাচন করুন</h3>
                      <p className="text-sm text-gray-600">আপনার পছন্দের ভাষা বেছে নিন</p>
                    </div>
                  </div>
                  <div className="grid grid-cols-2 gap-4 mt-4">
                    <button
                      type="button"
                      onClick={() => changeLanguage('bn')}
                      className={`px-6 py-4 rounded-xl font-bold transition-all ${
                        language === 'bn'
                          ? 'bg-teal-600 text-white shadow-lg scale-105'
                          : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                      }`}
                    >
                      <div className="text-2xl mb-1">🇧🇩</div>
                      <div className="text-sm">বাংলা</div>
                    </button>
                    <button
                      type="button"
                      onClick={() => changeLanguage('en')}
                      className={`px-6 py-4 rounded-xl font-bold transition-all ${
                        language === 'en'
                          ? 'bg-teal-600 text-white shadow-lg scale-105'
                          : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                      }`}
                    >
                      <div className="text-2xl mb-1">🇬🇧</div>
                      <div className="text-sm">English</div>
                    </button>
                  </div>
                </div>

                {/* Settings Info */}
                <div className="bg-orange-50 rounded-xl p-6 border border-orange-200">
                  <div className="flex items-start gap-4">
                    <Settings className="text-orange-600 flex-shrink-0" size={24} />
                    <div>
                      <h3 className="text-lg font-black text-gray-900 mb-2">সেটিংস সম্পর্কে</h3>
                      <ul className="space-y-2 text-sm text-gray-700">
                        <li className="flex items-center gap-2">
                          <CheckCircle className="text-green-500" size={16} />
                          <span>আপনার সেটিংস স্বয়ংক্রিয়ভাবে সংরক্ষিত হয়</span>
                        </li>
                        <li className="flex items-center gap-2">
                          <CheckCircle className="text-green-500" size={16} />
                          <span>সমস্ত ডিভাইসে একই সেটিংস প্রযোজ্য হবে</span>
                        </li>
                        <li className="flex items-center gap-2">
                          <CheckCircle className="text-green-500" size={16} />
                          <span>যেকোনো সময় পরিবর্তন করা যাবে</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                {/* Current Settings Display */}
                <div className="bg-white rounded-xl p-6 border-2 border-teal-100">
                  <h3 className="text-lg font-black text-gray-900 mb-4 flex items-center gap-2">
                    <CheckCircle className="text-teal-600" size={20} />
                    বর্তমান সেটিংস
                  </h3>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="bg-gray-50 rounded-lg p-4">
                      <div className="text-sm text-gray-600 mb-1">থিম মোড</div>
                      <div className="text-lg font-bold text-gray-900">
                        {darkMode ? '🌙 ডার্ক মোড' : '☀️ লাইট মোড'}
                      </div>
                    </div>
                    <div className="bg-gray-50 rounded-lg p-4">
                      <div className="text-sm text-gray-600 mb-1">ভাষা</div>
                      <div className="text-lg font-bold text-gray-900">
                        {language === 'bn' ? '🇧🇩 বাংলা' : '🇬🇧 English'}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            ) : activeTab === 'profile' ? (
              <form onSubmit={handleProfileSubmit} className="space-y-6">
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    <User size={16} className="inline mr-2" />
                    নাম
                  </label>
                  <input
                    type="text"
                    name="name"
                    value={profileData.name}
                    onChange={handleProfileChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                  {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    <Mail size={16} className="inline mr-2" />
                    ইমেইল
                  </label>
                  <input
                    type="email"
                    name="email"
                    value={profileData.email}
                    onChange={handleProfileChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                  {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    <Phone size={16} className="inline mr-2" />
                    ফোন নম্বর
                  </label>
                  <input
                    type="tel"
                    name="phone"
                    value={profileData.phone}
                    onChange={handleProfileChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                  {errors.phone && <p className="text-red-500 text-sm mt-1">{errors.phone}</p>}
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    <MapPin size={16} className="inline mr-2" />
                    ঠিকানা
                  </label>
                  <textarea
                    name="address"
                    value={profileData.address}
                    onChange={handleProfileChange}
                    rows="3"
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                  />
                  {errors.address && <p className="text-red-500 text-sm mt-1">{errors.address}</p>}
                </div>

                <button
                  type="submit"
                  disabled={loading}
                  className="w-full bg-rose-500 hover:bg-rose-600 text-white py-4 rounded-xl font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                  <Save size={20} />
                  {loading ? 'সংরক্ষণ হচ্ছে...' : 'পরিবর্তন সংরক্ষণ করুন'}
                </button>
              </form>
            ) : (
              <form onSubmit={handlePasswordSubmit} className="space-y-6">
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    <Lock size={16} className="inline mr-2" />
                    বর্তমান পাসওয়ার্ড
                  </label>
                  <input
                    type="password"
                    name="current_password"
                    value={passwordData.current_password}
                    onChange={handlePasswordChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                  {errors.current_password && <p className="text-red-500 text-sm mt-1">{errors.current_password}</p>}
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    <Lock size={16} className="inline mr-2" />
                    নতুন পাসওয়ার্ড
                  </label>
                  <input
                    type="password"
                    name="password"
                    value={passwordData.password}
                    onChange={handlePasswordChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                  {errors.password && <p className="text-red-500 text-sm mt-1">{errors.password}</p>}
                  <p className="text-sm text-gray-600 mt-1">কমপক্ষে ৬ অক্ষরের হতে হবে</p>
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    <Lock size={16} className="inline mr-2" />
                    নতুন পাসওয়ার্ড নিশ্চিত করুন
                  </label>
                  <input
                    type="password"
                    name="password_confirmation"
                    value={passwordData.password_confirmation}
                    onChange={handlePasswordChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                  {errors.password_confirmation && <p className="text-red-500 text-sm mt-1">{errors.password_confirmation}</p>}
                </div>

                <button
                  type="submit"
                  disabled={loading}
                  className="w-full bg-rose-500 hover:bg-rose-600 text-white py-4 rounded-xl font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                  <Lock size={20} />
                  {loading ? 'পরিবর্তন হচ্ছে...' : 'পাসওয়ার্ড পরিবর্তন করুন'}
                </button>
              </form>
            )}
          </div>
        </div>

        {/* Become a Dealer Section */}
        {!user?.dealer && (
          <div className="bg-white rounded-2xl shadow-lg overflow-hidden p-8">
            <div className="flex items-start justify-between">
              <div className="flex-1">
                <div className="flex items-center gap-3 mb-3">
                  <div className="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                    <Briefcase className="text-teal-600" size={24} />
                  </div>
                  <h3 className="text-2xl font-black text-gray-900">ডিলার হন</h3>
                </div>
                <p className="text-gray-600 leading-relaxed mb-6">
                  আপনার গাড়ি বিক্রয় ব্যবসাকে প্রফেশনাল লেভেলে নিয়ে যান। ভেরিফাইড ডিলার হিসেবে আপনি পাবেন বিশেষ সুবিধা, বিশ্বস্ততার ব্যাজ এবং আরও বেশি ক্রেতার কাছে পৌঁছানোর সুযোগ।
                </p>
                <ul className="space-y-2 mb-6">
                  <li className="flex items-center gap-2 text-gray-700">
                    <CheckCircle className="text-green-500" size={20} />
                    <span>ভেরিফাইড ডিলার ব্যাজ পান</span>
                  </li>
                  <li className="flex items-center gap-2 text-gray-700">
                    <CheckCircle className="text-green-500" size={20} />
                    <span>আনলিমিটেড বিজ্ঞাপন পোস্ট করুন</span>
                  </li>
                  <li className="flex items-center gap-2 text-gray-700">
                    <CheckCircle className="text-green-500" size={20} />
                    <span>ক্রেতাদের বিশ্বাস অর্জন করুন</span>
                  </li>
                </ul>
                <button
                  onClick={onBecomeDealer}
                  className="bg-rose-500 hover:bg-rose-600 text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl flex items-center gap-2"
                >
                  <Briefcase size={20} />
                  ডিলার আবেদন করুন
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Dealer Status Section */}
        {user?.dealer && user.dealer.status === 'pending' && (
          <div className="bg-yellow-50 border-2 border-yellow-200 rounded-2xl shadow-lg p-8">
            <div className="flex items-start gap-4">
              <div className="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                <Shield className="text-yellow-600" size={24} />
              </div>
              <div>
                <h3 className="text-xl font-black text-yellow-900 mb-2">ডিলার আবেদন পর্যালোচনাধীন</h3>
                <p className="text-yellow-800">
                  আপনার ডিলার আবেদন বর্তমানে পর্যালোচনাধীন রয়েছে। অনুমোদন হলে আপনি ইমেইল বিজ্ঞপ্তি পাবেন।
                </p>
              </div>
            </div>
          </div>
        )}

        {user?.is_verified_dealer && (
          <div className="bg-green-50 border-2 border-green-200 rounded-2xl shadow-lg p-8">
            <div className="flex items-start gap-4">
              <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <CheckCircle className="text-green-600" size={24} />
              </div>
              <div>
                <h3 className="text-xl font-black text-green-900 mb-2 flex items-center gap-2">
                  ভেরিফাইড ডিলার
                  <span className="inline-flex items-center gap-1 bg-green-500 text-white text-sm px-3 py-1 rounded-full">
                    <CheckCircle size={16} />
                    ভেরিফাইড
                  </span>
                </h3>
                <p className="text-green-800">
                  অভিনন্দন! আপনি এখন একজন ভেরিফাইড ডিলার। আপনার সকল বিজ্ঞাপনে ভেরিফাইড ব্যাজ প্রদর্শিত হবে।
                </p>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  )
}
