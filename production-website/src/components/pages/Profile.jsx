import { useState, useEffect, useRef } from 'react'
import { ArrowLeft, User, Mail, Phone, MapPin, Lock, Camera, Save, CheckCircle, Shield, Briefcase, Settings, Moon, Sun, Globe, Star, Calendar, Package, Share2, Edit3, TrendingUp } from 'lucide-react'
import { apiCall, getImageUrl } from '../../config'
import { useSettings } from '../../contexts/SettingsContext'
import { useTranslation } from '../../utils/translations'

export default function Profile({ onBack, user, onBecomeDealer, onViewDealerProfile }) {
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
    address: '',
    avatar: '',
    phone_verified: false,
    listings_count: 0,
    rating: 0,
    created_at: ''
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
    // Check if user is authenticated
    const token = localStorage.getItem('auth_token')
    if (!token) {
      return
    }

    try {
      const response = await apiCall('/users/profile')
      if (response.success) {
        setProfileData(response.data)
      }
    } catch (error) {
      if (error.message !== 'Unauthenticated') {
        console.error('Error fetching profile:', error)
      }
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
        const user = JSON.parse(localStorage.getItem('user') || '{}')
        localStorage.setItem('user', JSON.stringify({ ...user, ...response.data }))
        setTimeout(() => setSuccessMessage(''), 3000)
      }
    } catch (error) {
      console.error('Profile update error:', error)
      setErrors({ general: error.message || 'প্রোফাইল আপডেট করতে সমস্যা হয়েছে' })
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
      console.error('Password change error:', error)
      const errorMessage = error.message || 'পাসওয়ার্ড পরিবর্তন করতে সমস্যা হয়েছে'
      setErrors({ general: errorMessage })
    } finally {
      setLoading(false)
    }
  }

  const handleShareProfile = () => {
    if (navigator.share) {
      navigator.share({
        title: `${profileData.name} - গাড়ি কিনুন`,
        text: `${profileData.name} এর প্রোফাইল দেখুন`,
        url: window.location.href
      })
    } else {
      // Fallback: copy to clipboard
      navigator.clipboard.writeText(window.location.href)
      setSuccessMessage('প্রোফাইল লিংক কপি করা হয়েছে!')
      setTimeout(() => setSuccessMessage(''), 2000)
    }
  }

  const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('bn-BD', { year: 'numeric', month: 'long' })
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
        
        // Update profile data state
        setProfileData(prev => ({ ...prev, avatar: response.data.avatar }))
        
        setTimeout(() => setSuccessMessage(''), 3000)
      }
    } catch (error) {
      console.error('Avatar upload error:', error)
      setErrors({ avatar: error.message || 'ছবি আপলোড করতে সমস্যা হয়েছে' })
    } finally {
      setUploadingAvatar(false)
    }
  }

  return (
    <div className="min-h-screen bg-orange-50 dark:bg-gray-900">
      {/* Header */}
      <div className="bg-teal-700 dark:bg-teal-800 text-white py-8">
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
        <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden mb-6">
          {/* Enhanced Profile Header */}
          <div className="bg-gradient-to-r from-teal-600 to-teal-700 dark:from-teal-700 dark:to-teal-800 px-8 py-12">
            <div className="flex flex-col md:flex-row gap-8">
              {/* Left Side - Profile Picture */}
              <div className="flex flex-col items-center md:items-start">
                <div className="relative">
                  <div className="w-36 h-36 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center overflow-hidden border-4 border-white dark:border-gray-600 shadow-xl">
                    {profileData.avatar || avatarPreview ? (
                      <img 
                        src={avatarPreview || getImageUrl(profileData.avatar)} 
                        alt="Profile" 
                        className="w-full h-full object-cover"
                      />
                    ) : (
                      <User size={64} className="text-teal-600 dark:text-teal-400" />
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
                    className="absolute bottom-2 right-2 w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center border-4 border-white dark:border-gray-600 hover:bg-teal-700 transition-colors disabled:opacity-50 shadow-lg"
                  >
                    {uploadingAvatar ? (
                      <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    ) : (
                      <Camera size={18} className="text-white" />
                    )}
                  </button>
                  {/* Verified Badge */}
                  {user?.is_verified_dealer && (
                    <div className="absolute -top-2 -right-2">
                      <div className="bg-green-500 rounded-full p-2 border-4 border-white dark:border-gray-800 shadow-lg">
                        <CheckCircle size={20} className="text-white" />
                      </div>
                    </div>
                  )}
                  {errors.avatar && (
                    <div className="mt-2 text-center">
                      <p className="text-red-200 text-xs bg-red-500/20 px-3 py-1 rounded-full">{errors.avatar}</p>
                    </div>
                  )}
                </div>
              </div>

              {/* Right Side - User Details */}
              <div className="flex-1">
                <div className="space-y-4">
                  {/* Name & Status */}
                  <div>
                    <h2 className="text-3xl md:text-4xl font-black text-white flex items-center gap-3 flex-wrap">
                      {profileData.name}
                      {user?.is_verified_dealer && (
                        <span className="inline-flex items-center gap-1 bg-green-500 text-white text-sm px-3 py-1.5 rounded-full shadow-lg">
                          <CheckCircle size={16} />
                          <span className="font-semibold">ভেরিফাইড ডিলার</span>
                        </span>
                      )}
                      {user?.dealer && user.dealer.status === 'pending' && (
                        <span className="inline-flex items-center gap-1 bg-yellow-500 text-white text-sm px-3 py-1.5 rounded-full shadow-lg">
                          <Shield size={14} />
                          <span className="font-semibold">পর্যালোচনাধীন</span>
                        </span>
                      )}
                    </h2>
                  </div>

                  {/* Info Grid */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                    {/* Phone */}
                    <div className="flex items-center gap-2 text-white/90">
                      <Phone size={18} className="text-teal-200" />
                      <span className="font-medium">{profileData.phone || 'ফোন যোগ করুন'}</span>
                      {profileData.phone_verified && (
                        <CheckCircle size={16} className="text-green-300" title="ফোন ভেরিফাইড" />
                      )}
                    </div>

                    {/* Email */}
                    <div className="flex items-center gap-2 text-white/90">
                      <Mail size={18} className="text-teal-200" />
                      <span className="font-medium truncate">{profileData.email}</span>
                    </div>

                    {/* Location */}
                    <div className="flex items-center gap-2 text-white/90">
                      <MapPin size={18} className="text-teal-200" />
                      <span className="font-medium">{profileData.address || 'ঠিকানা যোগ করুন'}</span>
                    </div>

                    {/* Member Since */}
                    <div className="flex items-center gap-2 text-white/90">
                      <Calendar size={18} className="text-teal-200" />
                      <span className="font-medium">সদস্য: {formatDate(profileData.created_at)}</span>
                    </div>

                    {/* Total Ads */}
                    <div className="flex items-center gap-2 text-white/90">
                      <Package size={18} className="text-teal-200" />
                      <span className="font-medium">মোট বিজ্ঞাপন: {profileData.listings_count || 0}</span>
                    </div>

                    {/* Rating */}
                    <div className="flex items-center gap-2 text-white/90">
                      <Star size={18} className="text-yellow-300 fill-yellow-300" />
                      <span className="font-medium">রেটিং: {profileData.rating ? profileData.rating.toFixed(1) : '0.0'} / 5.0</span>
                    </div>
                  </div>

                  {/* Action Buttons */}
                  <div className="flex flex-wrap gap-3 mt-6">
                    <button
                      onClick={() => setActiveTab('profile')}
                      className="flex items-center gap-2 bg-white hover:bg-gray-100 text-teal-700 px-6 py-3 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                      <Edit3 size={18} />
                      <span>প্রোফাইল এডিট</span>
                    </button>
                    
                    {user?.is_verified_dealer && user?.dealer && (
                      <button
                        onClick={() => onViewDealerProfile?.(user.dealer.id)}
                        className="flex items-center gap-2 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:scale-105"
                      >
                        <Briefcase size={18} />
                        <span>আমার ডিলার প্রোফাইল</span>
                      </button>
                    )}
                    
                    <button
                      onClick={handleShareProfile}
                      className="flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-bold transition-all backdrop-blur-sm border-2 border-white/30"
                    >
                      <Share2 size={18} />
                      <span>শেয়ার করুন</span>
                    </button>

                    {!user?.dealer && (
                      <button
                        onClick={onBecomeDealer}
                        className="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-rose-500 hover:from-orange-600 hover:to-rose-600 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl transform hover:scale-105"
                      >
                        <TrendingUp size={18} />
                        <span>ডিলার হন</span>
                      </button>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Tabs */}
          <div className="border-b border-gray-200 dark:border-gray-700">
            <div className="flex">
              <button
                onClick={() => setActiveTab('profile')}
                className={`flex-1 px-6 py-4 font-bold transition-colors ${
                  activeTab === 'profile'
                    ? 'text-teal-600 dark:text-teal-400 border-b-2 border-teal-600 dark:border-teal-400'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
                }`}
              >
                ব্যক্তিগত তথ্য
              </button>
              <button
                onClick={() => setActiveTab('password')}
                className={`flex-1 px-6 py-4 font-bold transition-colors ${
                  activeTab === 'password'
                    ? 'text-teal-600 dark:text-teal-400 border-b-2 border-teal-600 dark:border-teal-400'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
                }`}
              >
                পাসওয়ার্ড পরিবর্তন
              </button>
              <button
                onClick={() => setActiveTab('settings')}
                className={`flex-1 px-6 py-4 font-bold transition-colors ${
                  activeTab === 'settings'
                    ? 'text-teal-600 dark:text-teal-400 border-b-2 border-teal-600 dark:border-teal-400'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'
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
                        <h3 className="text-lg font-black text-gray-900 dark:text-white">ডার্ক মোড</h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400">রাতের জন্য গাঢ় থিম সক্রিয় করুন</p>
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
                <div className="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900 dark:to-cyan-900 rounded-xl p-6 border border-blue-200 dark:border-blue-700">
                  <div className="flex items-center gap-4 mb-4">
                    <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center">
                      <Globe size={24} className="text-white" />
                    </div>
                    <div>
                      <h3 className="text-lg font-black text-gray-900 dark:text-white">ভাষা নির্বাচন করুন</h3>
                      <p className="text-sm text-gray-600 dark:text-gray-300">আপনার পছন্দের ভাষা বেছে নিন</p>
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
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <User size={16} className="inline mr-2" />
                    নাম
                  </label>
                  <input
                    type="text"
                    name="name"
                    value={profileData.name}
                    onChange={handleProfileChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-teal-500 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <Mail size={16} className="inline mr-2" />
                    ইমেইল
                  </label>
                  <input
                    type="email"
                    name="email"
                    value={profileData.email}
                    onChange={handleProfileChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-teal-500 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <Phone size={16} className="inline mr-2" />
                    ফোন নম্বর
                  </label>
                  <input
                    type="tel"
                    name="phone"
                    value={profileData.phone}
                    onChange={handleProfileChange}
                    className="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-teal-500 dark:bg-gray-700 dark:text-white"
                    required
                  />
                  {errors.phone && <p className="text-red-500 text-sm mt-1">{errors.phone}</p>}
                </div>

                <div>
                  <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    <MapPin size={16} className="inline mr-2" />
                    ঠিকানা
                  </label>
                  <textarea
                    name="address"
                    value={profileData.address}
                    onChange={handleProfileChange}
                    rows="3"
                    className="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:border-teal-500 dark:bg-gray-700 dark:text-white"
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
          <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden p-8">
            <div className="flex items-start justify-between">
              <div className="flex-1">
                <div className="flex items-center gap-3 mb-3">
                  <div className="w-12 h-12 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center">
                    <Briefcase className="text-teal-600 dark:text-teal-400" size={24} />
                  </div>
                  <h3 className="text-2xl font-black text-gray-900 dark:text-white">ডিলার হন</h3>
                </div>
                <p className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
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
