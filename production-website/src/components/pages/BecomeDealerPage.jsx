import { useState, useEffect } from 'react'
import { ArrowLeft, Briefcase, Phone, MapPin, FileText, CheckCircle, Upload, Image as ImageIcon, Camera, Shield } from 'lucide-react'
import Button from '../common/Button'
import { apiCall } from '../../config'

export default function BecomeDealerPage({ onBack, user }) {
  const [currentStep, setCurrentStep] = useState(1)
  const [dealerStatus, setDealerStatus] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const [success, setSuccess] = useState(false)
  
  const [formData, setFormData] = useState({
    business_name: '',
    business_address: '',
    business_phone: '',
    business_license: ''
  })
  
  const [files, setFiles] = useState({
    nid_front: null,
    nid_back: null,
    selfie_photo: null,
  })
  
  const [previews, setPreviews] = useState({
    nid_front: null,
    nid_back: null,
    selfie_photo: null,
  })
  
  const [verificationCode, setVerificationCode] = useState('')
  const [codeSent, setCodeSent] = useState(false)
  const [sentCode, setSentCode] = useState('') // Store sent code for display
  
  // Check dealer status on mount
  useEffect(() => {
    checkDealerStatus()
  }, [])
  
  const checkDealerStatus = async () => {
    try {
      const response = await apiCall('/dealer/status')
      if (response.success && response.data) {
        setDealerStatus(response.data)
      }
    } catch (err) {
      console.log('No dealer application found')
    }
  }
  
  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }
  
  const handleFileChange = (e, fieldName) => {
    const file = e.target.files[0]
    if (file) {
      setFiles({
        ...files,
        [fieldName]: file
      })
      
      // Create preview
      const reader = new FileReader()
      reader.onloadend = () => {
        setPreviews({
          ...previews,
          [fieldName]: reader.result
        })
      }
      reader.readAsDataURL(file)
    }
  }
  
  const nextStep = () => {
    // Validation for each step
    if (currentStep === 1) {
      if (!formData.business_name || !formData.business_address || !formData.business_phone) {
        setError('সকল ক্ষেত্র পূরণ করুন')
        return
      }
    } else if (currentStep === 2) {
      if (!files.nid_front || !files.nid_back || !files.selfie_photo) {
        setError('সকল ডকুমেন্ট আপলোড করুন')
        return
      }
    }
    
    setError('')
    setCurrentStep(currentStep + 1)
  }
  
  const prevStep = () => {
    setError('')
    setCurrentStep(currentStep - 1)
  }
  
  const sendVerificationCode = async () => {
    setLoading(true)
    setError('')
    
    try {
      const response = await apiCall('/dealer/send-verification', {
        method: 'POST',
        body: JSON.stringify({
          phone: formData.business_phone
        })
      })
      
      if (response.success) {
        setCodeSent(true)
        setSentCode(response.verification_code) // For development only
        alert(`যাচাইকরণ কোড: ${response.verification_code}\n\n(উৎপাদনে এটি SMS এর মাধ্যমে পাঠানো হবে)`)
      }
    } catch (err) {
      setError(err.message || 'কোড পাঠাতে সমস্যা হয়েছে')
    } finally {
      setLoading(false)
    }
  }
  
  const verifyMobileCode = async () => {
    if (verificationCode.length !== 6) {
      setError('৬ সংখ্যার কোড লিখুন')
      return
    }
    
    setLoading(true)
    setError('')
    
    try {
      const response = await apiCall('/dealer/verify-code', {
        method: 'POST',
        body: JSON.stringify({
          code: verificationCode
        })
      })
      
      if (response.success) {
        nextStep()
      }
    } catch (err) {
      setError(err.message || 'ভুল কোড')
    } finally {
      setLoading(false)
    }
  }
  
  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError('')
    
    try {
      // Create FormData for file upload
      const submitData = new FormData()
      submitData.append('business_name', formData.business_name)
      submitData.append('business_address', formData.business_address)
      submitData.append('business_phone', formData.business_phone)
      if (formData.business_license) {
        submitData.append('business_license', formData.business_license)
      }
      submitData.append('nid_front', files.nid_front)
      submitData.append('nid_back', files.nid_back)
      submitData.append('selfie_photo', files.selfie_photo)
      
      const token = localStorage.getItem('auth_token')
      const API_BASE_URL = import.meta.env.MODE === 'production' 
        ? 'https://admin.garikinun.com/api'
        : 'http://localhost:8000/api'
      
      const response = await fetch(`${API_BASE_URL}/dealer/apply`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
        },
        body: submitData
      })
      
      const data = await response.json()
      
      if (!response.ok) {
        throw new Error(data.message || 'আবেদন জমা দিতে সমস্যা হয়েছে')
      }
      
      if (data.success) {
        setSuccess(true)
        alert('✅ ডিলার আবেদন সফলভাবে জমা হয়েছে! অনুমোদনের জন্য অপেক্ষা করুন।')
        checkDealerStatus()
      }
    } catch (err) {
      setError(err.message || 'আবেদন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।')
    } finally {
      setLoading(false)
    }
  }
  
  // Show status if already applied
  if (dealerStatus && !success) {
    return (
      <div className="min-h-screen bg-gray-50">
        <div className="bg-white shadow-sm sticky top-0 z-10">
          <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <button 
              onClick={onBack}
              className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold"
            >
              <ArrowLeft size={20} />
              <span>ফিরে যান</span>
            </button>
          </div>
        </div>
        
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <div className="text-center mb-6">
              {dealerStatus.status === 'pending' && (
                <>
                  <div className="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Shield size={48} className="text-yellow-600" />
                  </div>
                  <h1 className="text-2xl font-bold text-gray-900 mb-2">আবেদন পর্যালোচনাধীন</h1>
                  <p className="text-gray-600">আপনার ডিলার আবেদন যাচাই করা হচ্ছে। শীঘ্রই আপনাকে জানানো হবে।</p>
                </>
              )}
              {dealerStatus.status === 'active' && (
                <>
                  <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <CheckCircle size={48} className="text-green-600" />
                  </div>
                  <h1 className="text-2xl font-bold text-gray-900 mb-2">আপনি একজন যাচাইকৃত ডিলার! ✓</h1>
                  <p className="text-gray-600">অভিনন্দন! আপনি এখন ভেরিফাইড ব্যাজ সহ ডিলার হিসেবে তালিকাভুক্ত আছেন।</p>
                </>
              )}
              {dealerStatus.status === 'rejected' && (
                <>
                  <div className="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <Shield size={48} className="text-red-600" />
                  </div>
                  <h1 className="text-2xl font-bold text-gray-900 mb-2">আবেদন প্রত্যাখ্যাত</h1>
                  <p className="text-gray-600">দুঃখিত, আপনার ডিলার আবেদন প্রত্যাখ্যান করা হয়েছে।</p>
                </>
              )}
            </div>
            
            <div className="bg-gray-50 rounded-lg p-4 mt-6">
              <h3 className="font-semibold mb-2">আবেদনের তথ্য:</h3>
              <div className="space-y-1 text-sm">
                <p><span className="font-medium">ব্যবসায়ের নাম:</span> {dealerStatus.business_name}</p>
                <p><span className="font-medium">ঠিকানা:</span> {dealerStatus.business_address}</p>
                <p><span className="font-medium">ফোন:</span> {dealerStatus.business_phone}</p>
                <p><span className="font-medium">আবেদনের তারিখ:</span> {new Date(dealerStatus.applied_at).toLocaleDateString('bn-BD')}</p>
              </div>
            </div>
            
            <Button onClick={onBack} variant="primary" size="lg" className="w-full mt-6">
              হোমে ফিরে যান
            </Button>
          </div>
        </div>
      </div>
    )
  }
  
  if (success) {
    return (
      <div className="min-h-screen bg-orange-50">
        <div className="bg-white shadow-sm sticky top-0 z-10">
          <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <button 
              onClick={onBack}
              className="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold"
            >
              <ArrowLeft size={20} />
              <span>হোমে ফিরে যান</span>
            </button>
          </div>
        </div>
        
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <CheckCircle size={48} className="text-green-600" />
            </div>
            <h1 className="text-3xl font-bold text-gray-900 mb-4">
              আবেদন সফলভাবে জমা হয়েছে!
            </h1>
            <p className="text-lg text-gray-600 mb-2">
              আপনার ডিলার আবেদন আমরা পেয়েছি।
            </p>
            <p className="text-gray-500 mb-8">
              আমরা শীঘ্রই আপনার ডকুমেন্ট যাচাই করে আপনাকে জানাবো। অনুমোদন হলে আপনি ভেরিফাইড ব্যাজ পাবেন।
            </p>
            <Button
              onClick={onBack}
              variant="primary"
              size="lg"
            >
              হোমে ফিরে যান
            </Button>
          </div>
        </div>
      </div>
    )
  }
  
  return (
    <div className="min-h-screen bg-orange-50">
      {/* Header */}
      <div className="bg-teal-700 text-white py-8 shadow-lg">
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
          <button 
            onClick={onBack}
            className="flex items-center gap-2 text-white hover:text-gray-200 mb-4"
          >
            <ArrowLeft size={20} />
            <span>ফিরে যান</span>
          </button>
          <h1 className="text-3xl md:text-4xl font-black flex items-center gap-3">
            <Briefcase size={36} />
            ডিলার হন
          </h1>
          <p className="text-teal-100 mt-2">
            ধাপ {currentStep} / 4: {
              currentStep === 1 ? 'ব্যবসায়ের তথ্য' :
              currentStep === 2 ? 'ডকুমেন্ট আপলোড' :
              currentStep === 3 ? 'মোবাইল যাচাই' :
              'চূড়ান্ত নিশ্চিতকরণ'
            }
          </p>
        </div>
      </div>
      
      {/* Progress Indicator */}
      <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="flex items-center justify-center mb-8">
          {[1, 2, 3, 4].map((step) => (
            <div key={step} className="flex items-center">
              <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold ${
                currentStep === step 
                  ? 'bg-teal-600 text-white' 
                  : currentStep > step 
                  ? 'bg-teal-500 text-white' 
                  : 'bg-gray-300 text-gray-600'
              }`}>
                {step}
              </div>
              {step < 4 && (
                <div className={`w-16 h-1 mx-2 ${
                  currentStep > step ? 'bg-teal-500' : 'bg-gray-300'
                }`} />
              )}
            </div>
          ))}
        </div>
        
        {error && (
          <div className="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {error}
          </div>
        )}
        
        <form onSubmit={handleSubmit}>
          {/* Step 1: Business Information */}
          {currentStep === 1 && (
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <Briefcase className="text-teal-600" size={28} />
                ব্যবসায়ের তথ্য
              </h2>
              
              <div className="space-y-6">
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    ব্যবসায়ের নাম <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="business_name"
                    value={formData.business_name}
                    onChange={handleChange}
                    placeholder="যেমন: ABC Motors"
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    ব্যবসায়ের ঠিকানা <span className="text-red-500">*</span>
                  </label>
                  <textarea
                    name="business_address"
                    value={formData.business_address}
                    onChange={handleChange}
                    placeholder="বাসা/রোড নং, এলাকা, থানা, জেলা"
                    rows="3"
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    মোবাইল নম্বর <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="tel"
                    name="business_phone"
                    value={formData.business_phone}
                    onChange={handleChange}
                    placeholder="+880 1XXX-XXXXXX"
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                    required
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    ট্রেড লাইসেন্স নম্বর (ঐচ্ছিক)
                  </label>
                  <input
                    type="text"
                    name="business_license"
                    value={formData.business_license}
                    onChange={handleChange}
                    placeholder="যদি থাকে"
                    className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
                  />
                </div>
              </div>
            </div>
          )}
          
          {/* Step 2: Document Upload */}
          {currentStep === 2 && (
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <FileText className="text-teal-600" size={28} />
                ডকুমেন্ট আপলোড
              </h2>
              
              <div className="space-y-6">
                {/* NID Front */}
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    জাতীয় পরিচয়পত্র (সামনের অংশ) <span className="text-red-500">*</span>
                  </label>
                  <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    {previews.nid_front ? (
                      <div className="relative">
                        <img src={previews.nid_front} alt="NID Front" className="max-h-48 mx-auto rounded" />
                        <button
                          type="button"
                          onClick={() => {
                            setFiles({ ...files, nid_front: null })
                            setPreviews({ ...previews, nid_front: null })
                          }}
                          className="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full"
                        >
                          ✕
                        </button>
                      </div>
                    ) : (
                      <label className="cursor-pointer">
                        <ImageIcon size={48} className="mx-auto text-gray-400 mb-2" />
                        <p className="text-sm text-gray-600">ক্লিক করে আপলোড করুন</p>
                        <input
                          type="file"
                          accept="image/*"
                          onChange={(e) => handleFileChange(e, 'nid_front')}
                          className="hidden"
                        />
                      </label>
                    )}
                  </div>
                </div>
                
                {/* NID Back */}
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    জাতীয় পরিচয়পত্র (পেছনের অংশ) <span className="text-red-500">*</span>
                  </label>
                  <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    {previews.nid_back ? (
                      <div className="relative">
                        <img src={previews.nid_back} alt="NID Back" className="max-h-48 mx-auto rounded" />
                        <button
                          type="button"
                          onClick={() => {
                            setFiles({ ...files, nid_back: null })
                            setPreviews({ ...previews, nid_back: null })
                          }}
                          className="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full"
                        >
                          ✕
                        </button>
                      </div>
                    ) : (
                      <label className="cursor-pointer">
                        <ImageIcon size={48} className="mx-auto text-gray-400 mb-2" />
                        <p className="text-sm text-gray-600">ক্লিক করে আপলোড করুন</p>
                        <input
                          type="file"
                          accept="image/*"
                          onChange={(e) => handleFileChange(e, 'nid_back')}
                          className="hidden"
                        />
                      </label>
                    )}
                  </div>
                </div>
                
                {/* Selfie */}
                <div>
                  <label className="block text-sm font-bold text-gray-700 mb-2">
                    সেলফি ছবি (NID হাতে ধরে) <span className="text-red-500">*</span>
                  </label>
                  <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    {previews.selfie_photo ? (
                      <div className="relative">
                        <img src={previews.selfie_photo} alt="Selfie" className="max-h-48 mx-auto rounded" />
                        <button
                          type="button"
                          onClick={() => {
                            setFiles({ ...files, selfie_photo: null })
                            setPreviews({ ...previews, selfie_photo: null })
                          }}
                          className="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full"
                        >
                          ✕
                        </button>
                      </div>
                    ) : (
                      <label className="cursor-pointer">
                        <Camera size={48} className="mx-auto text-gray-400 mb-2" />
                        <p className="text-sm text-gray-600">NID হাতে ধরে সেলফি তুলুন</p>
                        <input
                          type="file"
                          accept="image/*"
                          capture="user"
                          onChange={(e) => handleFileChange(e, 'selfie_photo')}
                          className="hidden"
                        />
                      </label>
                    )}
                  </div>
                </div>
              </div>
            </div>
          )}
          
          {/* Step 3: Mobile Verification */}
          {currentStep === 3 && (
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <Phone className="text-teal-600" size={28} />
                মোবাইল যাচাই
              </h2>
              
              <div className="space-y-6">
                <div className="bg-teal-50 border border-teal-200 rounded-lg p-4">
                  <p className="text-sm text-teal-800">
                    আপনার মোবাইল নম্বর: <span className="font-bold">{formData.business_phone}</span>
                  </p>
                </div>
                
                {!codeSent ? (
                  <Button
                    type="button"
                    onClick={sendVerificationCode}
                    disabled={loading}
                    variant="primary"
                    size="lg"
                    className="w-full"
                  >
                    {loading ? 'পাঠানো হচ্ছে...' : 'যাচাইকরণ কোড পাঠান'}
                  </Button>
                ) : (
                  <>
                    {sentCode && (
                      <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p className="text-sm text-yellow-800">
                          ✉️ আপনার কোড: <span className="font-bold text-xl">{sentCode}</span>
                        </p>
                        <p className="text-xs text-yellow-700 mt-1">(উৎপাদনে SMS এর মাধ্যমে পাঠানো হবে)</p>
                      </div>
                    )}
                    
                    <div>
                      <label className="block text-sm font-bold text-gray-700 mb-2">
                        ৬ অঙ্কের কোড লিখুন
                      </label>
                      <input
                        type="text"
                        value={verificationCode}
                        onChange={(e) => setVerificationCode(e.target.value)}
                        placeholder="000000"
                        maxLength="6"
                        className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500 text-center text-2xl tracking-widest"
                      />
                    </div>
                    
                    <Button
                      type="button"
                      onClick={verifyMobileCode}
                      disabled={loading || verificationCode.length !== 6}
                      variant="primary"
                      size="lg"
                      className="w-full"
                    >
                      {loading ? 'যাচাই করা হচ্ছে...' : 'যাচাই করুন'}
                    </Button>
                  </>
                )}
              </div>
            </div>
          )}
          
          {/* Step 4: Final Confirmation */}
          {currentStep === 4 && (
            <div className="bg-white rounded-2xl shadow-lg p-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <CheckCircle className="text-teal-600" size={28} />
                চূড়ান্ত নিশ্চিতকরণ
              </h2>
              
              <div className="bg-gray-50 rounded-lg p-6 space-y-4 mb-6">
                <h3 className="font-bold text-lg">আপনার তথ্য যাচাই করুন:</h3>
                
                <div className="space-y-2 text-sm">
                  <p><span className="font-medium">ব্যবসার নাম:</span> {formData.business_name}</p>
                  <p><span className="font-medium">ঠিকানা:</span> {formData.business_address}</p>
                  <p><span className="font-medium">ফোন:</span> {formData.business_phone}</p>
                  {formData.business_license && (
                    <p><span className="font-medium">লাইসেন্স:</span> {formData.business_license}</p>
                  )}
                </div>
                
                <div className="grid grid-cols-3 gap-4 mt-4">
                  {previews.nid_front && (
                    <div>
                      <p className="text-xs font-medium mb-1">NID (সামনে)</p>
                      <img src={previews.nid_front} alt="NID Front" className="w-full h-24 object-cover rounded" />
                    </div>
                  )}
                  {previews.nid_back && (
                    <div>
                      <p className="text-xs font-medium mb-1">NID (পেছনে)</p>
                      <img src={previews.nid_back} alt="NID Back" className="w-full h-24 object-cover rounded" />
                    </div>
                  )}
                  {previews.selfie_photo && (
                    <div>
                      <p className="text-xs font-medium mb-1">সেলফি</p>
                      <img src={previews.selfie_photo} alt="Selfie" className="w-full h-24 object-cover rounded" />
                    </div>
                  )}
                </div>
              </div>
              
              <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p className="text-sm text-blue-800">
                  ℹ️ আবেদন জমা দেওয়ার পর আমরা আপনার ডকুমেন্ট যাচাই করব। অনুমোদন হলে আপনি <span className="font-bold">ভেরিফাইড ব্যাজ (✓)</span> পাবেন।
                </p>
              </div>
              
              <Button
                type="submit"
                disabled={loading}
                variant="primary"
                size="lg"
                className="w-full"
              >
                {loading ? 'জমা দেওয়া হচ্ছে...' : 'আবেদন জমা দিন'}
              </Button>
            </div>
          )}
          
          {/* Navigation Buttons */}
          {currentStep < 4 && (
            <div className="flex gap-4 mt-8">
              {currentStep > 1 && (
                <Button
                  type="button"
                  onClick={prevStep}
                  variant="secondary"
                  size="lg"
                  className="flex-1"
                >
                  পূর্ববর্তী
                </Button>
              )}
              
              <Button
                type="button"
                onClick={nextStep}
                variant="primary"
                size="lg"
                className="flex-1"
              >
                পরবর্তী
              </Button>
            </div>
          )}
        </form>
      </div>
    </div>
  )
}
