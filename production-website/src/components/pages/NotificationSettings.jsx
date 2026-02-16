import { useState, useEffect } from 'react'
import { ArrowLeft, Bell, MessageCircle, Package, Zap, Mail, Smartphone, Save } from 'lucide-react'
import { apiCall } from '../../config'

export default function NotificationSettings({ onBack }) {
  const [settings, setSettings] = useState({
    // Message Notifications
    new_message: true,
    chat_reply: true,
    
    // My Ads Activity
    ad_approved: true,
    ad_rejected: true,
    ad_expiring: true,
    ad_viewed: true,
    ad_saved: true,
    price_drop: true,
    
    // Promotions
    boost_expired: true,
    premium_activated: true,
    special_offers: true,
    
    // Notification Channels
    in_app_notifications: true,
    email_notifications: true,
    push_notifications: false
  })
  
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  
  useEffect(() => {
    fetchSettings()
  }, [])
  
  const fetchSettings = async () => {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      setLoading(false)
      return
    }
    
    setLoading(true)
    try {
      const response = await apiCall('/notification-settings')
      if (response.success && response.data) {
        setSettings({ ...settings, ...response.data })
      }
    } catch (error) {
      console.error('Error fetching settings:', error)
    } finally {
      setLoading(false)
    }
  }
  
  const handleToggle = (key) => {
    setSettings({
      ...settings,
      [key]: !settings[key]
    })
  }
  
  const handleSave = async () => {
    setSaving(true)
    try {
      const response = await apiCall('/notification-settings', {
        method: 'POST',
        body: JSON.stringify(settings)
      })
      
      if (response.success) {
        alert('সেটিংস সফলভাবে সংরক্ষিত হয়েছে!')
      }
    } catch (error) {
      console.error('Error saving settings:', error)
      alert('সেটিংস সংরক্ষণে সমস্যা হয়েছে। আবার চেষ্টা করুন।')
    } finally {
      setSaving(false)
    }
  }
  
  const ToggleSwitch = ({ enabled, onChange, label, description }) => (
    <div className="flex items-center justify-between py-4 border-b border-gray-100">
      <div className="flex-1">
        <h4 className="text-base font-semibold text-gray-900 mb-1">{label}</h4>
        {description && (
          <p className="text-sm text-gray-600">{description}</p>
        )}
      </div>
      <button
        onClick={onChange}
        className={`relative inline-flex h-7 w-12 items-center rounded-full transition-colors ${
          enabled ? 'bg-teal-600' : 'bg-gray-300'
        }`}
      >
        <span
          className={`inline-block h-5 w-5 transform rounded-full bg-white transition-transform ${
            enabled ? 'translate-x-6' : 'translate-x-1'
          }`}
        />
      </button>
    </div>
  )
  
  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="w-16 h-16 border-4 border-teal-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-gray-500 font-medium">লোড হচ্ছে...</p>
        </div>
      </div>
    )
  }
  
  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm sticky top-0 z-10 border-b border-gray-200">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 py-4">
          <div className="flex items-center gap-3">
            <button 
              onClick={onBack}
              className="flex items-center gap-2 text-gray-700 hover:text-gray-900 font-semibold transition-colors"
            >
              <ArrowLeft size={20} />
              <span>ফিরে যান</span>
            </button>
            <div className="flex items-center gap-2">
              <Bell size={24} className="text-teal-600" />
              <h1 className="text-xl font-bold text-gray-900">নোটিফিকেশন সেটিংস</h1>
            </div>
          </div>
        </div>
      </div>
      
      {/* Content */}
      <div className="max-w-3xl mx-auto px-4 sm:px-6 py-6">
        <div className="space-y-6">
          {/* Message Notifications */}
          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <MessageCircle size={24} className="text-blue-600" />
              </div>
              <div>
                <h3 className="text-lg font-bold text-gray-900">বার্তা নোটিফিকেশন</h3>
                <p className="text-sm text-gray-600">চ্যাট ও মেসেজ সংক্রান্ত সতর্কতা</p>
              </div>
            </div>
            
            <div className="space-y-1">
              <ToggleSwitch
                enabled={settings.new_message}
                onChange={() => handleToggle('new_message')}
                label="নতুন বার্তা"
                description="যখন কেউ আপনাকে বার্তা পাঠায়"
              />
              <ToggleSwitch
                enabled={settings.chat_reply}
                onChange={() => handleToggle('chat_reply')}
                label="চ্যাট উত্তর"
                description="যখন কেউ আপনার বার্তার উত্তর দেয়"
              />
            </div>
          </div>
          
          {/* My Ads Activity */}
          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                <Package size={24} className="text-teal-600" />
              </div>
              <div>
                <h3 className="text-lg font-bold text-gray-900">আমার বিজ্ঞাপনের কার্যকলাপ</h3>
                <p className="text-sm text-gray-600">আপনার বিজ্ঞাপন সংক্রান্ত আপডেট</p>
              </div>
            </div>
            
            <div className="space-y-1">
              <ToggleSwitch
                enabled={settings.ad_approved}
                onChange={() => handleToggle('ad_approved')}
                label="বিজ্ঞাপন অনুমোদিত"
                description="যখন আপনার বিজ্ঞাপন অনুমোদিত হয়"
              />
              <ToggleSwitch
                enabled={settings.ad_rejected}
                onChange={() => handleToggle('ad_rejected')}
                label="বিজ্ঞাপন প্রত্যাখ্যাত"
                description="যখন আপনার বিজ্ঞাপন প্রত্যাখ্যাত হয়"
              />
              <ToggleSwitch
                enabled={settings.ad_expiring}
                onChange={() => handleToggle('ad_expiring')}
                label="বিজ্ঞাপন শেষ হচ্ছে"
                description="মেয়াদ শেষ হওয়ার আগে সতর্কতা"
              />
              <ToggleSwitch
                enabled={settings.ad_viewed}
                onChange={() => handleToggle('ad_viewed')}
                label="কেউ বিজ্ঞাপন দেখেছে"
                description="যখন কেউ আপনার বিজ্ঞাপন দেখে"
              />
              <ToggleSwitch
                enabled={settings.ad_saved}
                onChange={() => handleToggle('ad_saved')}
                label="কেউ বিজ্ঞাপন সংরক্ষণ করেছে"
                description="যখন কেউ আপনার বিজ্ঞাপন সংরক্ষণ করে"
              />
              <ToggleSwitch
                enabled={settings.price_drop}
                onChange={() => handleToggle('price_drop')}
                label="দाम কমেছে"
                description="সংরক্ষিত বিজ্ঞাপনের দাম কমলে"
              />
            </div>
          </div>
          
          {/* Promotions */}
          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <Zap size={24} className="text-yellow-600" />
              </div>
              <div>
                <h3 className="text-lg font-bold text-gray-900">প্রমোশন</h3>
                <p className="text-sm text-gray-600">বুস্ট ও বিশেষ অফার</p>
              </div>
            </div>
            
            <div className="space-y-1">
              <ToggleSwitch
                enabled={settings.boost_expired}
                onChange={() => handleToggle('boost_expired')}
                label="বুস্ট শেষ হয়েছে"
                description="যখন আপনার বুস্ট মেয়াদ শেষ হয়"
              />
              <ToggleSwitch
                enabled={settings.premium_activated}
                onChange={() => handleToggle('premium_activated')}
                label="প্রিমিয়াম সক্রিয় হয়েছে"
                description="যখন প্রিমিয়াম ফিচার সক্রিয় হয়"
              />
              <ToggleSwitch
                enabled={settings.special_offers}
                onChange={() => handleToggle('special_offers')}
                label="বিশেষ অফার"
                description="প্রমোশন ও ডিসকাউন্ট"
              />
            </div>
          </div>
          
          {/* Notification Channels */}
          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <Bell size={24} className="text-purple-600" />
              </div>
              <div>
                <h3 className="text-lg font-bold text-gray-900">নোটিফিকেশন চ্যানেল</h3>
                <p className="text-sm text-gray-600">কিভাবে নোটিফিকেশন পেতে চান</p>
              </div>
            </div>
            
            <div className="space-y-1">
              <ToggleSwitch
                enabled={settings.in_app_notifications}
                onChange={() => handleToggle('in_app_notifications')}
                label="ইন-অ্যাপ নোটিফিকেশন"
                description="অ্যাপের ভিতরে নোটিফিকেশন দেখুন"
              />
              <ToggleSwitch
                enabled={settings.email_notifications}
                onChange={() => handleToggle('email_notifications')}
                label="ইমেইল নোটিফিকেশন"
                description="ইমেইলে নোটিফিকেশন পান"
              />
              <ToggleSwitch
                enabled={settings.push_notifications}
                onChange={() => handleToggle('push_notifications')}
                label="পুশ নোটিফিকেশন"
                description="মোবাইল পুশ নোটিফিকেশন (আসছে)"
              />
            </div>
          </div>
        </div>
        
        {/* Save Button */}
        <div className="mt-8 flex justify-center">
          <button
            onClick={handleSave}
            disabled={saving}
            className="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Save size={20} />
            <span>{saving ? 'সংরক্ষণ হচ্ছে...' : 'সেটিংস সংরক্ষণ করুন'}</span>
          </button>
        </div>
        
        {/* Info Box */}
        <div className="mt-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
          <p className="text-sm text-blue-800">
            <strong>📌 মনে রাখবেন:</strong> আপনি যে অপশন বন্ধ করবেন সেই ধরণের নোটিফিকেশন আর পাবেন না। 
            গুরুত্বপূর্ণ আপডেট মিস করতে না চাইলে সব অপশন চালু রাখুন।
          </p>
        </div>
      </div>
    </div>
  )
}
