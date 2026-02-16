import { useState, useEffect } from 'react'
import { 
  ArrowLeft, Bell, Check, Trash2, Eye, Heart, TrendingDown, Zap, 
  MessageCircle, Package, Bookmark, Tag, ShieldCheck, Key, FileText, Settings 
} from 'lucide-react'
import { apiCall } from '../../config'

export default function NotificationsPage({ onBack, onSettingsClick }) {
  const [notifications, setNotifications] = useState([])
  const [loading, setLoading] = useState(true)
  const [category, setCategory] = useState('all') // all, messages, myads, saved, promotions
  
  useEffect(() => {
    fetchNotifications()
  }, [category])
  
  const fetchNotifications = async () => {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      setLoading(false)
      return
    }

    setLoading(true)
    try {
      const endpoint = '/notifications'
      const response = await apiCall(endpoint)
      
      if (response.success) {
        setNotifications(response.data || [])
      }
    } catch (error) {
      if (error.message !== 'Unauthenticated') {
        console.error('Error fetching notifications:', error)
      }
    } finally {
      setLoading(false)
    }
  }
  
  const markAsRead = async (notificationId) => {
    try {
      await apiCall(`/notifications/${notificationId}/read`, {
        method: 'POST'
      })
      
      setNotifications(notifications.map(n => 
        n.id === notificationId ? { ...n, read_at: new Date().toISOString() } : n
      ))
    } catch (error) {
      console.error('Error marking notification as read:', error)
    }
  }
  
  const markAllAsRead = async () => {
    try {
      await apiCall('/notifications/read-all', {
        method: 'POST'
      })
      
      setNotifications(notifications.map(n => 
        ({ ...n, read_at: new Date().toISOString() })
      ))
    } catch (error) {
      console.error('Error marking all as read:', error)
    }
  }
  
  const deleteNotification = async (notificationId) => {
    try {
      await apiCall(`/notifications/${notificationId}`, {
        method: 'DELETE'
      })
      
      setNotifications(notifications.filter(n => n.id !== notificationId))
    } catch (error) {
      console.error('Error deleting notification:', error)
    }
  }

  // Get notification icon and color based on type
  const getNotificationStyle = (notification) => {
    const type = notification.type || notification.data?.type || 'default'
    
    const styles = {
      'ad_view': { icon: Eye, color: 'bg-blue-100 text-blue-600', label: 'বিজ্ঞাপন দেখা' },
      'ad_saved': { icon: Heart, color: 'bg-pink-100 text-pink-600', label: 'সংরক্ষিত' },
      'ad_approved': { icon: Check, color: 'bg-green-100 text-green-600', label: 'অনুমোদিত' },
      'ad_rejected': { icon: Trash2, color: 'bg-red-100 text-red-600', label: 'প্রত্যাখ্যাত' },
      'ad_expiring': { icon: Bell, color: 'bg-orange-100 text-orange-600', label: 'মেয়াদ শেষ' },
      'price_drop': { icon: TrendingDown, color: 'bg-teal-100 text-teal-600', label: 'দাম কমেছে' },
      'boost_expired': { icon: Zap, color: 'bg-yellow-100 text-yellow-600', label: 'বুস্ট শেষ' },
      'boost_success': { icon: Zap, color: 'bg-green-100 text-green-600', label: 'বুস্ট সফল' },
      'premium_activated': { icon: Tag, color: 'bg-purple-100 text-purple-600', label: 'প্রিমিয়াম' },
      'message': { icon: MessageCircle, color: 'bg-blue-100 text-blue-600', label: 'বার্তা' },
      'account_verified': { icon: ShieldCheck, color: 'bg-green-100 text-green-600', label: 'যাচাইকৃত' },
      'password_changed': { icon: Key, color: 'bg-orange-100 text-orange-600', label: 'পাসওয়ার্ড' },
      'policy_update': { icon: FileText, color: 'bg-gray-100 text-gray-600', label: 'নীতি আপডেট' },
      'default': { icon: Bell, color: 'bg-gray-100 text-gray-600', label: 'সাধারণ' }
    }
    
    return styles[type] || styles['default']
  }

  // Filter notifications by category
  const filteredNotifications = notifications.filter(n => {
    if (category === 'all') return true
    if (category === 'messages') return n.type === 'message'
    if (category === 'myads') return ['ad_view', 'ad_saved', 'ad_approved', 'ad_rejected', 'ad_expiring'].includes(n.type)
    if (category === 'saved') return n.type === 'price_drop'
    if (category === 'promotions') return ['boost_expired', 'boost_success', 'premium_activated'].includes(n.type)
    return true
  })
  
  const unreadCount = notifications.filter(n => !n.read_at).length
  
  // Time ago helper
  const timeAgo = (date) => {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000)
    
    let interval = seconds / 31536000
    if (interval > 1) return Math.floor(interval) + ' বছর আগে'
    interval = seconds / 2592000
    if (interval > 1) return Math.floor(interval) + ' মাস আগে'
    interval = seconds / 86400
    if (interval > 1) return Math.floor(interval) + ' দিন আগে'
    interval = seconds / 3600
    if (interval > 1) return Math.floor(interval) + ' ঘন্টা আগে'
    interval = seconds / 60
    if (interval > 1) return Math.floor(interval) + ' মিনিট আগে'
    return 'এখনই'
  }
  
  return (
    <div className="min-h-screen bg-gray-50">
      {/* Professional Header */}
      <div className="bg-white shadow-sm sticky top-0 z-20 border-b border-gray-200">
        <div className="max-w-5xl mx-auto px-4 sm:px-6">
          <div className="flex items-center justify-between py-4">
            <div className="flex items-center gap-3">
              <button 
                onClick={onBack}
                className="flex items-center gap-2 text-gray-700 hover:text-gray-900 font-semibold transition-colors"
              >
                <ArrowLeft size={20} />
                <span className="hidden sm:inline">ফিরে যান</span>
              </button>
              <div className="flex items-center gap-2">
                <Bell size={24} className="text-teal-600" />
                <h1 className="text-xl font-bold text-gray-900">নোটিফিকেশন</h1>
                {unreadCount > 0 && (
                  <span className="bg-teal-600 text-white text-xs font-bold px-2 py-1 rounded-full min-w-[24px] text-center">
                    {unreadCount}
                  </span>
                )}
              </div>
            </div>
            
            <div className="flex items-center gap-2">
              {unreadCount > 0 && (
                <button
                  onClick={markAllAsRead}
                  className="flex items-center gap-1.5 text-teal-600 hover:text-teal-700 font-semibold text-sm px-3 py-2 rounded-lg hover:bg-teal-50 transition-all"
                >
                  <Check size={18} />
                  <span className="hidden sm:inline">সব পড়া হয়েছে</span>
                </button>
              )}
              <button 
                onClick={onSettingsClick}
                className="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all"
                title="নোটিফিকেশন সেটিংস"
              >
                <Settings size={20} />
              </button>
            </div>
          </div>
          
          {/* Category Tabs */}
          <div className="flex gap-1 overflow-x-auto pb-2 scrollbar-hide -mx-2 px-2">
            {[
              { id: 'all', label: 'সব', icon: Bell },
              { id: 'messages', label: 'বার্তা', icon: MessageCircle },
              { id: 'myads', label: 'আমার বিজ্ঞাপন', icon: Package },
              { id: 'saved', label: 'সংরক্ষিত বিজ্ঞাপন', icon: Bookmark },
              { id: 'promotions', label: 'প্রমোশন', icon: Zap }
            ].map(tab => {
              const Icon = tab.icon
              const count = tab.id === 'all' ? notifications.length : 
                           notifications.filter(n => 
                             tab.id === 'messages' ? n.type === 'message' :
                             tab.id === 'myads' ? ['ad_view', 'ad_saved', 'ad_approved', 'ad_rejected', 'ad_expiring'].includes(n.type) :
                             tab.id === 'saved' ? n.type === 'price_drop' :
                             tab.id === 'promotions' ? ['boost_expired', 'boost_success', 'premium_activated'].includes(n.type) : false
                           ).length
              
              return (
                <button
                  key={tab.id}
                  onClick={() => setCategory(tab.id)}
                  className={`flex items-center gap-2 px-4 py-2 rounded-lg font-semibold whitespace-nowrap text-sm transition-all ${
                    category === tab.id
                      ? 'bg-teal-600 text-white shadow-md'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  }`}
                >
                  <Icon size={16} />
                  <span>{tab.label}</span>
                  {count > 0 && (
                    <span className={`text-xs font-bold px-1.5 py-0.5 rounded-full ${
                      category === tab.id ? 'bg-white/20' : 'bg-white'
                    }`}>
                      {count}
                    </span>
                  )}
                </button>
              )
            })}
          </div>
        </div>
      </div>
      
      {/* Content */}
      <div className="max-w-5xl mx-auto px-4 sm:px-6 py-6">
        {loading ? (
          <div className="bg-white rounded-2xl p-12 text-center shadow-sm">
            <div className="w-16 h-16 border-4 border-teal-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-gray-500 font-medium">লোড হচ্ছে...</p>
          </div>
        ) : filteredNotifications.length > 0 ? (
          <div className="space-y-3">
            {filteredNotifications.map((notification) => {
              const style = getNotificationStyle(notification)
              const Icon = style.icon
              const isUnread = !notification.read_at
              
              return (
                <div
                  key={notification.id}
                  className={`rounded-xl p-4 shadow-sm hover:shadow-md transition-all relative overflow-hidden ${
                    isUnread 
                      ? 'bg-blue-50 border-2 border-blue-200' 
                      : 'bg-white border-2 border-gray-100'
                  }`}
                >
                  {/* Unread Indicator Dot */}
                  {isUnread && (
                    <div className="absolute top-4 right-4">
                      <div className="w-2.5 h-2.5 bg-blue-600 rounded-full animate-pulse"></div>
                    </div>
                  )}
                  
                  <div className="flex items-start gap-4">
                    {/* Icon */}
                    <div className={`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 ${style.color}`}>
                      <Icon size={24} />
                    </div>
                    
                    {/* Content */}
                    <div className="flex-1 min-w-0 pr-8">
                      <div className="flex items-start justify-between mb-1">
                        <h3 className="text-base font-bold text-gray-900">
                          {notification.data?.title || 'নোটিফিকেশন'}
                        </h3>
                      </div>
                      
                      <p className="text-sm text-gray-700 mb-2 leading-relaxed">
                        {notification.data?.message || notification.data?.body || 'নতুন নোটিফিকেশন'}
                      </p>
                      
                      <div className="flex items-center gap-3 text-xs text-gray-500">
                        <span className="flex items-center gap-1">
                          <span className="font-medium">{timeAgo(notification.created_at)}</span>
                        </span>
                        <span className={`px-2 py-1 rounded-full font-semibold ${style.color}`}>
                          {style.label}
                        </span>
                      </div>
                    </div>
                    
                    {/* Actions */}
                    <div className="flex flex-col gap-1">
                      {isUnread && (
                        <button
                          onClick={() => markAsRead(notification.id)}
                          className="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                          title="পড়া হয়েছে"
                        >
                          <Check size={18} />
                        </button>
                      )}
                      <button
                        onClick={() => deleteNotification(notification.id)}
                        className="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                        title="মুছুন"
                      >
                        <Trash2 size={18} />
                      </button>
                    </div>
                  </div>
                </div>
              )
            })}
          </div>
        ) : (
          <div className="bg-white rounded-2xl p-12 text-center shadow-sm">
            <div className="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <Bell size={40} className="text-gray-400" />
            </div>
            <h3 className="text-xl font-bold text-gray-900 mb-2">
              কোনো নোটিফিকেশন নেই
            </h3>
            <p className="text-gray-600">
              {category !== 'all' 
                ? 'এই ক্যাটাগরিতে কোনো নোটিফিকেশন নেই।'
                : 'আপনার এখনো কোনো নোটিফিকেশন আসেনি।'
              }
            </p>
          </div>
        )}
      </div>
    </div>
  )
}
