import { useState, useEffect } from 'react'
import { ArrowLeft, Bell, Check, Trash2, Eye } from 'lucide-react'
import { apiCall } from '../../config'
import Button from '../common/Button'

export default function NotificationsPage({ onBack }) {
  const [notifications, setNotifications] = useState([])
  const [loading, setLoading] = useState(true)
  const [filter, setFilter] = useState('all') // all, unread
  
  useEffect(() => {
    fetchNotifications()
  }, [filter])
  
  const fetchNotifications = async () => {
    setLoading(true)
    try {
      const endpoint = filter === 'unread' ? '/notifications?unread=1' : '/notifications'
      const response = await apiCall(endpoint)
      
      if (response.success) {
        setNotifications(response.data || [])
      }
    } catch (error) {
      console.error('Error fetching notifications:', error)
    } finally {
      setLoading(false)
    }
  }
  
  const markAsRead = async (notificationId) => {
    try {
      await apiCall(`/notifications/${notificationId}/read`, {
        method: 'POST'
      })
      
      // Update local state
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
      
      // Update local state
      setNotifications(notifications.map(n => 
        ({ ...n, read_at: new Date().toISOString() })
      ))
    } catch (error) {
      console.error('Error marking all as read:', error)
    }
  }
  
  const deleteNotification = async (notificationId) => {
    if (!confirm('এই নোটিফিকেশন মুছে ফেলবেন?')) return
    
    try {
      await apiCall(`/notifications/${notificationId}`, {
        method: 'DELETE'
      })
      
      // Remove from local state
      setNotifications(notifications.filter(n => n.id !== notificationId))
    } catch (error) {
      console.error('Error deleting notification:', error)
    }
  }
  
  const unreadCount = notifications.filter(n => !n.read_at).length
  
  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm sticky top-0 z-10">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <button 
              onClick={onBack}
              className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold"
            >
              <ArrowLeft size={20} />
              <span>ফিরে যান</span>
            </button>
            
            {unreadCount > 0 && (
              <button
                onClick={markAllAsRead}
                className="text-sm text-primary-600 hover:text-primary-700 font-semibold"
              >
                সব পড়া হয়েছে চিহ্নিত করুন
              </button>
            )}
          </div>
        </div>
      </div>
      
      {/* Content */}
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Title */}
        <div className="mb-6">
          <div className="flex items-center gap-3 mb-2">
            <div className="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
              <Bell size={24} className="text-primary-600" />
            </div>
            <h1 className="text-3xl font-bold text-gray-900">নোটিফিকেশন</h1>
          </div>
          {unreadCount > 0 && (
            <p className="text-gray-600 ml-15">
              {unreadCount} টি নতুন বার্তা
            </p>
          )}
        </div>
        
        {/* Filter Tabs */}
        <div className="flex items-center gap-2 mb-6">
          <button
            onClick={() => setFilter('all')}
            className={`px-6 py-2 rounded-lg font-semibold transition-colors ${
              filter === 'all'
                ? 'bg-primary-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            }`}
          >
            সব ({notifications.length})
          </button>
          <button
            onClick={() => setFilter('unread')}
            className={`px-6 py-2 rounded-lg font-semibold transition-colors ${
              filter === 'unread'
                ? 'bg-primary-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            }`}
          >
            অপঠিত ({unreadCount})
          </button>
        </div>
        
        {/* Notifications List */}
        {loading ? (
          <div className="bg-white rounded-xl p-12 text-center">
            <div className="w-16 h-16 border-4 border-primary-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p className="text-gray-500">লোড হচ্ছে...</p>
          </div>
        ) : notifications.length > 0 ? (
          <div className="space-y-3">
            {notifications.map((notification) => (
              <div
                key={notification.id}
                className={`bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow ${
                  !notification.read_at ? 'border-l-4 border-primary-600' : ''
                }`}
              >
                <div className="flex items-start gap-4">
                  {/* Icon */}
                  <div className={`w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 ${
                    !notification.read_at ? 'bg-primary-100' : 'bg-gray-100'
                  }`}>
                    <Bell size={20} className={!notification.read_at ? 'text-primary-600' : 'text-gray-400'} />
                  </div>
                  
                  {/* Content */}
                  <div className="flex-1 min-w-0">
                    <h3 className="font-bold text-gray-900 mb-1">
                      {notification.data?.title || 'নোটিফিকেশন'}
                    </h3>
                    <p className="text-gray-600 mb-2">
                      {notification.data?.message || notification.data?.body || 'নতুন নোটিফিকেশন'}
                    </p>
                    <p className="text-sm text-gray-500">
                      {new Date(notification.created_at).toLocaleString('bn-BD', {
                        day: 'numeric',
                        month: 'long',
                        hour: '2-digit',
                        minute: '2-digit'
                      })}
                    </p>
                  </div>
                  
                  {/* Actions */}
                  <div className="flex items-center gap-2">
                    {!notification.read_at && (
                      <button
                        onClick={() => markAsRead(notification.id)}
                        className="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                        title="পড়া হয়েছে চিহ্নিত করুন"
                      >
                        <Check size={20} />
                      </button>
                    )}
                    <button
                      onClick={() => deleteNotification(notification.id)}
                      className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="মুছে ফেলুন"
                    >
                      <Trash2 size={20} />
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="bg-white rounded-xl p-12 text-center">
            <div className="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <Bell size={40} className="text-gray-400" />
            </div>
            <h3 className="text-xl font-bold text-gray-900 mb-2">
              কোনো নোটিফিকেশন নেই
            </h3>
            <p className="text-gray-500">
              {filter === 'unread' 
                ? 'আপনার কোনো অপঠিত নোটিফিকেশন নেই।'
                : 'আপনার এখনো কোনো নোটিফিকেশন আসেনি।'
              }
            </p>
          </div>
        )}
      </div>
    </div>
  )
}
