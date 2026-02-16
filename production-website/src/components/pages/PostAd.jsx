import { useState, useEffect, useRef } from 'react'
import { ArrowLeft, ArrowRight, Check, Upload, X, GripVertical, MapPin, Phone, Mail, User, DollarSign, Image as ImageIcon } from 'lucide-react'
import { apiCall } from '../../config'
import { useSettings } from '../../contexts/SettingsContext'
import { useTranslation } from '../../utils/translations'

// Brand Lists
const CAR_BRANDS = [
  'Toyota',
  'Honda',
  'Nissan',
  'Mitsubishi',
  'Suzuki',
  'Hyundai',
  'Kia',
  'Ford',
  'BMW',
  'Mercedes-Benz',
  'Audi',
  'Volkswagen',
  'Lexus',
  'Subaru',
  'Mazda',
  'Chevrolet',
  'MG',
  'Tata',
  'Mahindra',
  'Proton'
]

const BIKE_BRANDS = [
  'Yamaha',
  'Honda',
  'Suzuki',
  'Bajaj',
  'TVS',
  'Hero',
  'Royal Enfield',
  'KTM',
  'Lifan',
  'Runner',
  'Walton',
  'Haojue',
  'Benelli',
  'GPX',
  'Keeway',
  'UM',
  'Zontes',
  'Kawasaki',
  'Ducati'
]

const BICYCLE_BRANDS = [
  'Phoenix',
  'Hero',
  'Duranta',
  'Core',
  'Giant',
  'Trek',
  'Merida',
  'Avon',
  'Bianchi',
  'Scott',
  'Veloce',
  'Firefox',
  'Polygon',
  'Atlas'
]

const TRUCK_BRANDS = [
  'Isuzu',
  'Tata',
  'Ashok Leyland',
  'Hino',
  'Mitsubishi Fuso',
  'Eicher',
  'Mahindra',
  'Dongfeng',
  'FAW',
  'JAC'
]

const VAN_BRANDS = [
  'Toyota',
  'Nissan',
  'Mitsubishi',
  'Suzuki',
  'Hyundai',
  'Kia',
  'Tata',
  'Mahindra'
]

const THREE_WHEEL_BRANDS = [
  'Bajaj',
  'TVS',
  'Mahindra',
  'Piaggio',
  'Runner',
  'Walton',
  'Lifan'
]

export default function PostAd({ onBack, user }) {
  const { darkMode, language } = useSettings()
  const { t } = useTranslation(language)
  const fileInputRef = useRef(null)
  
  const [currentStep, setCurrentStep] = useState(1)
  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState({})
  const [successMessage, setSuccessMessage] = useState('')
  const [categories, setCategories] = useState([])
  const [brands, setBrands] = useState([])
  const [models, setModels] = useState([])
  
  const [formData, setFormData] = useState({
    // Basic Info
    category_id: '',
    title: '',
    
    // Car/Vehicle specific
    brand: '',
    model: '',
    year_of_manufacture: '',
    registration_year: '',
    condition: '',
    fuel_type: '',
    transmission: '',
    engine_capacity: '',
    kilometers_run: '',
    color: '',
    body_type: '',
    number_of_owners: '',
    tax_valid_until: '',
    fitness_valid_until: '',
    
    // Bike specific
    abs: '',
    
    // Bicycle specific
    bicycle_type: '',
    frame_size: '',
    gear_system: '',
    
    // Truck/Van specific
    loading_capacity: '',
    seating_capacity: '',
    
    // Three Wheels specific
    three_wheel_type: '',
    registration_status: '',
    
    // Auto Parts specific
    part_name: '',
    compatible_vehicle: '',
    brand_name: '',
    warranty: '',
    
    // Accessories specific
    product_name: '',
    accessory_category: '',
    
    // Common
    description: '',
    
    // Price & Location
    price: '',
    negotiable: false,
    exchange_option: false,
    division: '',
    district: '',
    area: '',
    
    // Contact
    contact_name: '',
    contact_phone: '',
    contact_email: '',
    hide_phone: false,
    allow_chat: true
  })
  
  const [images, setImages] = useState([])
  const [imagePreviews, setImagePreviews] = useState([])
  const [draggedIndex, setDraggedIndex] = useState(null)

  useEffect(() => {
    fetchCategories()
    // Pre-fill contact info from user
    if (user) {
      setFormData(prev => ({
        ...prev,
        contact_name: user.name || '',
        contact_phone: user.phone || '',
        contact_email: user.email || ''
      }))
    }
  }, [user])

  useEffect(() => {
    if (formData.category_id) {
      // Set brands based on category
      const categoryName = getCategoryName(formData.category_id)
      let brandList = []
      
      if (['car', 'cars'].includes(categoryName)) {
        brandList = CAR_BRANDS
      } else if (['bike', 'bikes', 'motorcycle', 'motorcycles'].includes(categoryName)) {
        brandList = BIKE_BRANDS
      } else if (['bicycle', 'bicycles'].includes(categoryName)) {
        brandList = BICYCLE_BRANDS
      } else if (['truck', 'trucks'].includes(categoryName)) {
        brandList = TRUCK_BRANDS
      } else if (['van', 'vans'].includes(categoryName)) {
        brandList = VAN_BRANDS
      } else if (categoryName.includes('three') || categoryName.includes('wheel')) {
        brandList = THREE_WHEEL_BRANDS
      }
      
      setBrands(brandList.map((name, index) => ({ id: index + 1, name })))
    }
  }, [formData.category_id, categories])

  // Models are now entered as text, no need to fetch from API
  // useEffect(() => {
  //   if (formData.brand) {
  //     fetchModels(formData.brand)
  //   }
  // }, [formData.brand])

  const fetchCategories = async () => {
    try {
      const response = await apiCall('/categories')
      if (response.success) {
        setCategories(response.data)
      }
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  }

  const getCategoryName = (categoryId) => {
    const category = categories.find(c => c.id === parseInt(categoryId))
    return category?.name?.toLowerCase() || ''
  }

  // Models are now entered as free text, API call removed
  // const fetchModels = async (brandId) => {
  //   try {
  //     const response = await apiCall(`/brands/${brandId}/models`)
  //     if (response.success) {
  //       setModels(response.data)
  //     }
  //   } catch (error) {
  //     console.error('Error fetching models:', error)
  //   }
  // }

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }))
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }))
    }
  }

  const handleImageSelect = (e) => {
    const files = Array.from(e.target.files)
    
    if (images.length + files.length > 10) {
      setErrors({ images: 'সর্বোচ্চ ১০টি ছবি আপলোড করতে পারবেন' })
      return
    }

    const validFiles = []
    const newPreviews = []

    files.forEach(file => {
      if (!file.type.startsWith('image/')) {
        setErrors({ images: 'শুধুমাত্র ছবি ফাইল আপলোড করা যাবে' })
        return
      }
      
      if (file.size > 5 * 1024 * 1024) {
        setErrors({ images: 'প্রতিটি ছবি সর্বোচ্চ ৫ MB হতে পারে' })
        return
      }

      validFiles.push(file)
      
      const reader = new FileReader()
      reader.onloadend = () => {
        newPreviews.push(reader.result)
        if (newPreviews.length === validFiles.length) {
          setImagePreviews(prev => [...prev, ...newPreviews])
        }
      }
      reader.readAsDataURL(file)
    })

    setImages(prev => [...prev, ...validFiles])
    setErrors(prev => ({ ...prev, images: '' }))
  }

  const removeImage = (index) => {
    setImages(prev => prev.filter((_, i) => i !== index))
    setImagePreviews(prev => prev.filter((_, i) => i !== index))
  }

  const handleDragStart = (index) => {
    setDraggedIndex(index)
  }

  const handleDragOver = (e, index) => {
    e.preventDefault()
    if (draggedIndex === null || draggedIndex === index) return

    const newImages = [...images]
    const newPreviews = [...imagePreviews]
    
    const draggedImage = newImages[draggedIndex]
    const draggedPreview = newPreviews[draggedIndex]
    
    newImages.splice(draggedIndex, 1)
    newPreviews.splice(draggedIndex, 1)
    
    newImages.splice(index, 0, draggedImage)
    newPreviews.splice(index, 0, draggedPreview)
    
    setImages(newImages)
    setImagePreviews(newPreviews)
    setDraggedIndex(index)
  }

  const handleDragEnd = () => {
    setDraggedIndex(null)
  }

  const validateStep = (step) => {
    const newErrors = {}

    if (step === 1) {
      if (!formData.category_id) newErrors.category_id = 'ক্যাটাগরি নির্বাচন করুন'
      if (!formData.title) newErrors.title = 'শিরোনাম লিখুন'
    }

    if (step === 2) {
      const categoryName = getCategoryName(formData.category_id)
      
      if (['car', 'cars'].includes(categoryName)) {
        if (!formData.brand) newErrors.brand = 'ব্র্যান্ড নির্বাচন করুন'
        if (!formData.model) newErrors.model = 'মডেল নির্বাচন করুন'
        if (!formData.year_of_manufacture) newErrors.year_of_manufacture = 'বছর লিখুন'
        if (!formData.condition) newErrors.condition = 'অবস্থা নির্বাচন করুন'
        if (!formData.fuel_type) newErrors.fuel_type = 'জ্বালানির ধরন নির্বাচন করুন'
      }
      
      if (['bicycle', 'bicycles'].includes(categoryName)) {
        if (!formData.brand) newErrors.brand = 'ব্র্যান্ড নির্বাচন করুন'
        if (!formData.bicycle_type) newErrors.bicycle_type = 'টাইপ নির্বাচন করুন'
        if (!formData.frame_size) newErrors.frame_size = 'ফ্রেম সাইজ লিখুন'
        if (!formData.gear_system) newErrors.gear_system = 'গিয়ার সিস্টেম লিখুন'
        if (!formData.condition) newErrors.condition = 'অবস্থা নির্বাচন করুন'
        if (!formData.color) newErrors.color = 'রঙ লিখুন'
      }
      
      if (['van', 'vans'].includes(categoryName)) {
        if (!formData.brand) newErrors.brand = 'ব্র্যান্ড নির্বাচন করুন'
        if (!formData.model) newErrors.model = 'মডেল লিখুন'
        if (!formData.year_of_manufacture) newErrors.year_of_manufacture = 'বছর নির্বাচন করুন'
        if (!formData.fuel_type) newErrors.fuel_type = 'জ্বালানির ধরন নির্বাচন করুন'
        if (!formData.engine_capacity) newErrors.engine_capacity = 'ইঞ্জিন সিসি লিখুন'
        if (!formData.seating_capacity) newErrors.seating_capacity = 'সিটিং ক্যাপাসিটি লিখুন'
        if (!formData.registration_year) newErrors.registration_year = 'রেজিস্ট্রেশন বছর লিখুন'
        if (!formData.condition) newErrors.condition = 'অবস্থা নির্বাচন করুন'
      }
      
      // Description is required for all categories
      if (!formData.description) {
        newErrors.description = 'বিবরণ লিখুন'
      } else if (formData.description.length < 30) {
        newErrors.description = 'বিবরণ কমপক্ষে ৩০ অক্ষরের হতে হবে'
      }
    }

    if (step === 3) {
      if (images.length === 0) newErrors.images = 'কমপক্ষে ১টি ছবি আপলোড করুন'
    }

    if (step === 4) {
      if (!formData.price) newErrors.price = 'মূল্য লিখুন'
      if (!formData.division) newErrors.division = 'বিভাগ নির্বাচন করুন'
      if (!formData.district) newErrors.district = 'জেলা নির্বাচন করুন'
      if (!formData.contact_phone) newErrors.contact_phone = 'ফোন নম্বর লিখুন'
    }

    setErrors(newErrors)
    return Object.keys(newErrors).length === 0
  }

  const nextStep = () => {
    if (validateStep(currentStep)) {
      setCurrentStep(prev => Math.min(prev + 1, 4))
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }

  const prevStep = () => {
    setCurrentStep(prev => Math.max(prev - 1, 1))
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const handleSubmit = async () => {
    if (!validateStep(4)) return

    setLoading(true)
    setErrors({})

    try {
      const formDataToSend = new FormData()
      
      // Build location from division, district, and area
      const locationParts = [formData.area, formData.district, formData.division].filter(Boolean)
      const location = locationParts.join(', ')
      
      // Append all form fields with transformations
      Object.keys(formData).forEach(key => {
        // Skip fields that need transformation
        if (['division', 'district', 'area', 'contact_phone', 'contact_name', 'contact_email', 'hide_phone'].includes(key)) {
          return
        }
        
        if (formData[key] !== '' && formData[key] !== null && formData[key] !== false) {
          formDataToSend.append(key, formData[key])
        }
      })
      
      // Add transformed fields
      if (location) {
        formDataToSend.append('location', location)
      }
      if (formData.contact_phone) {
        formDataToSend.append('phone', formData.contact_phone)
      }

      // Append images
      images.forEach((image, index) => {
        formDataToSend.append(`images[${index}]`, image)
      })

      const response = await apiCall('/listings', {
        method: 'POST',
        body: formDataToSend
      })

      if (response.success) {
        setSuccessMessage('বিজ্ঞাপন সফলভাবে পোস্ট হয়েছে!')
        setTimeout(() => {
          onBack()
        }, 2000)
      }
    } catch (error) {
      console.error('Error posting ad:', error)
      setErrors({ general: error.message || 'বিজ্ঞাপন পোস্ট করতে সমস্যা হয়েছে' })
    } finally {
      setLoading(false)
    }
  }

  const divisions = ['ঢাকা', 'চট্টগ্রাম', 'রাজশাহী', 'খুলনা', 'বরিশাল', 'সিলেট', 'রংপুর', 'ময়মনসিংহ']
  const currentYear = new Date().getFullYear()
  const years = Array.from({ length: 40 }, (_, i) => currentYear - i)

  const renderStepIndicator = () => (
    <div className="mb-8">
      <div className="flex items-center justify-between max-w-3xl mx-auto">
        {[
          { num: 1, label: 'মূল তথ্য' },
          { num: 2, label: 'বিস্তারিত' },
          { num: 3, label: 'ছবি' },
          { num: 4, label: 'যোগাযোগ' }
        ].map((step, index) => (
          <div key={step.num} className="flex items-center flex-1">
            <div className="flex flex-col items-center flex-1">
              <div className={`w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg transition-all ${
                currentStep > step.num
                  ? 'bg-green-500 text-white'
                  : currentStep === step.num
                  ? 'bg-teal-600 text-white scale-110'
                  : 'bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
              }`}>
                {currentStep > step.num ? <Check size={24} /> : step.num}
              </div>
              <span className={`mt-2 text-sm font-semibold ${
                currentStep >= step.num
                  ? 'text-teal-600 dark:text-teal-400'
                  : 'text-gray-500 dark:text-gray-500'
              }`}>
                {step.label}
              </span>
            </div>
            {index < 3 && (
              <div className={`h-1 flex-1 mx-2 ${
                currentStep > step.num
                  ? 'bg-green-500'
                  : 'bg-gray-300 dark:bg-gray-700'
              }`} />
            )}
          </div>
        ))}
      </div>
    </div>
  )

  const renderCategoryFields = () => {
    const categoryName = getCategoryName(formData.category_id)

    if (['car', 'cars'].includes(categoryName)) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড *
              </label>
              <select
                name="brand"
                value={formData.brand}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.brand ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">ব্র্যান্ড নির্বাচন করুন</option>
                {brands.map(brand => (
                  <option key={brand.id} value={brand.name}>{brand.name}</option>
                ))}
              </select>
              {errors.brand && <p className="text-red-500 text-sm mt-1">{errors.brand}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মডেল *
              </label>
              <input
                type="text"
                name="model"
                value={formData.model}
                onChange={handleInputChange}
                list="models-list"
                placeholder="মডেল নাম লিখুন বা নির্বাচন করুন"
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.model ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              <datalist id="models-list">
                {models.map((model, index) => (
                  <option key={index} value={model.name} />
                ))}
              </datalist>
              {errors.model && <p className="text-red-500 text-sm mt-1">{errors.model}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বছর *
              </label>
              <select
                name="year_of_manufacture"
                value={formData.year_of_manufacture}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.year_of_manufacture ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
              {errors.year_of_manufacture && <p className="text-red-500 text-sm mt-1">{errors.year_of_manufacture}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রেজিস্ট্রেশন বছর
              </label>
              <select
                name="registration_year"
                value={formData.registration_year}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা *
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.condition ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
                <option value="Reconditioned">রিকন্ডিশন</option>
              </select>
              {errors.condition && <p className="text-red-500 text-sm mt-1">{errors.condition}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                জ্বালানির ধরন *
              </label>
              <select
                name="fuel_type"
                value={formData.fuel_type}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.fuel_type ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">জ্বালানির ধরন নির্বাচন করুন</option>
                <option value="petrol">পেট্রোল</option>
                <option value="diesel">ডিজেল</option>
                <option value="hybrid">হাইব্রিড</option>
                <option value="electric">ইলেকট্রিক</option>
                <option value="cng">সিএনজি</option>
              </select>
              {errors.fuel_type && <p className="text-red-500 text-sm mt-1">{errors.fuel_type}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ট্রান্সমিশন
              </label>
              <select
                name="transmission"
                value={formData.transmission}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">ট্রান্সমিশন নির্বাচন করুন</option>
                <option value="Manual">ম্যানুয়াল</option>
                <option value="Automatic">অটোমেটিক</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ইঞ্জিন ক্যাপাসিটি (cc)
              </label>
              <input
                type="number"
                name="engine_capacity"
                value={formData.engine_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 1500"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মাইলেজ (কিমি)
              </label>
              <input
                type="number"
                name="kilometers_run"
                value={formData.kilometers_run}
                onChange={handleInputChange}
                placeholder="যেমন: 50000"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রঙ
              </label>
              <input
                type="text"
                name="color"
                value={formData.color}
                onChange={handleInputChange}
                placeholder="যেমন: সাদা"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বডি টাইপ
              </label>
              <select
                name="body_type"
                value={formData.body_type}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বডি টাইপ নির্বাচন করুন</option>
                <option value="suv">SUV</option>
                <option value="sedan">সেডান</option>
                <option value="hatchback">হ্যাচব্যাক</option>
                <option value="microbus">মাইক্রোবাস</option>
                <option value="pickup">পিকআপ</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মালিক সংখ্যা
              </label>
              <input
                type="number"
                name="number_of_owners"
                value={formData.number_of_owners}
                onChange={handleInputChange}
                placeholder="যেমন: 1"
                min="1"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ট্যাক্স বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="tax_valid_until"
                value={formData.tax_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ফিটনেস বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="fitness_valid_until"
                value={formData.fitness_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রেজিস্ট্রেশন স্ট্যাটাস
              </label>
              <select
                name="registration_status"
                value={formData.registration_status}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">স্ট্যাটাস নির্বাচন করুন</option>
                <option value="registered">রেজিস্টার্ড</option>
                <option value="unregistered">আনরেজিস্টার্ড</option>
              </select>
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="আপনার গাড়ি সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    if (['bike', 'bikes'].includes(categoryName)) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড *
              </label>
              <select
                name="brand"
                value={formData.brand}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">ব্র্যান্ড নির্বাচন করুন</option>
                {brands.map(brand => (
                  <option key={brand.id} value={brand.name}>{brand.name}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মডেল
              </label>
              <input
                type="text"
                name="model"
                value={formData.model}
                onChange={handleInputChange}
                list="models-list-bike"
                placeholder="মডেল নাম লিখুন বা নির্বাচন করুন"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
              <datalist id="models-list-bike">
                {models.map((model, index) => (
                  <option key={index} value={model.name} />
                ))}
              </datalist>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বছর
              </label>
              <select
                name="year_of_manufacture"
                value={formData.year_of_manufacture}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রেজিস্ট্রেশন বছর
              </label>
              <select
                name="registration_year"
                value={formData.registration_year}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ইঞ্জিন সিসি
              </label>
              <input
                type="number"
                name="engine_capacity"
                value={formData.engine_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 150"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মাইলেজ (কিমি)
              </label>
              <input
                type="number"
                name="kilometers_run"
                value={formData.kilometers_run}
                onChange={handleInputChange}
                placeholder="যেমন: 10000"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                জ্বালানির ধরন
              </label>
              <select
                name="fuel_type"
                value={formData.fuel_type}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">জ্বালানির ধরন নির্বাচন করুন</option>
                <option value="petrol">পেট্রোল</option>
                <option value="electric">ইলেকট্রিক</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ট্রান্সমিশন
              </label>
              <select
                name="transmission"
                value={formData.transmission}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">ট্রান্সমিশন নির্বাচন করুন</option>
                <option value="Manual">গিয়ার</option>
                <option value="Automatic">অটোমেটিক</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রঙ
              </label>
              <input
                type="text"
                name="color"
                value={formData.color}
                onChange={handleInputChange}
                placeholder="যেমন: লাল"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বডি টাইপ
              </label>
              <select
                name="body_type"
                value={formData.body_type}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বডি টাইপ নির্বাচন করুন</option>
                <option value="sport">স্পোর্ট</option>
                <option value="cruiser">ক্রুজার</option>
                <option value="commuter">কমিউটার</option>
                <option value="scooter">স্কুটার</option>
                <option value="off-road">অফরোড</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ABS
              </label>
              <select
                name="abs"
                value={formData.abs}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">নির্বাচন করুন</option>
                <option value="yes">হ্যাঁ</option>
                <option value="no">না</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মালিক সংখ্যা
              </label>
              <input
                type="number"
                name="number_of_owners"
                value={formData.number_of_owners}
                onChange={handleInputChange}
                placeholder="যেমন: 1"
                min="1"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ট্যাক্স বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="tax_valid_until"
                value={formData.tax_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ফিটনেস বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="fitness_valid_until"
                value={formData.fitness_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="আপনার বাইক সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    if (['bicycle', 'bicycles'].includes(categoryName)) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড *
              </label>
              <select
                name="brand"
                value={formData.brand}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.brand ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">ব্র্যান্ড নির্বাচন করুন</option>
                {brands.map(brand => (
                  <option key={brand.id} value={brand.name}>{brand.name}</option>
                ))}
              </select>
              {errors.brand && <p className="text-red-500 text-sm mt-1">{errors.brand}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                টাইপ *
              </label>
              <select
                name="bicycle_type"
                value={formData.bicycle_type}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.bicycle_type ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">টাইপ নির্বাচন করুন</option>
                <option value="MTB">MTB</option>
                <option value="Road">রোড</option>
                <option value="Hybrid">হাইব্রিড</option>
                <option value="Kids">শিশুদের</option>
              </select>
              {errors.bicycle_type && <p className="text-red-500 text-sm mt-1">{errors.bicycle_type}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ফ্রেম সাইজ *
              </label>
              <input
                type="text"
                name="frame_size"
                value={formData.frame_size}
                onChange={handleInputChange}
                placeholder="যেমন: 18 ইঞ্চি"
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.frame_size ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {errors.frame_size && <p className="text-red-500 text-sm mt-1">{errors.frame_size}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                গিয়ার সিস্টেম *
              </label>
              <input
                type="text"
                name="gear_system"
                value={formData.gear_system}
                onChange={handleInputChange}
                placeholder="যেমন: 21 স্পিড"
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.gear_system ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {errors.gear_system && <p className="text-red-500 text-sm mt-1">{errors.gear_system}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা *
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.condition ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
              </select>
              {errors.condition && <p className="text-red-500 text-sm mt-1">{errors.condition}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রঙ *
              </label>
              <input
                type="text"
                name="color"
                value={formData.color}
                onChange={handleInputChange}
                placeholder="যেমন: নীল"
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.color ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {errors.color && <p className="text-red-500 text-sm mt-1">{errors.color}</p>}
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="আপনার সাইকেল সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    if (['truck', 'trucks'].includes(categoryName)) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড *
              </label>
              <select
                name="brand"
                value={formData.brand}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">ব্র্যান্ড নির্বাচন করুন</option>
                {brands.map(brand => (
                  <option key={brand.id} value={brand.name}>{brand.name}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মডেল
              </label>
              <input
                type="text"
                name="model"
                value={formData.model}
                onChange={handleInputChange}
                list="models-list-truck"
                placeholder="মডেল নাম লিখুন বা নির্বাচন করুন"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
              <datalist id="models-list-truck">
                {models.map((model, index) => (
                  <option key={index} value={model.name} />
                ))}
              </datalist>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বছর
              </label>
              <select
                name="year_of_manufacture"
                value={formData.year_of_manufacture}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                জ্বালানির ধরন
              </label>
              <select
                name="fuel_type"
                value={formData.fuel_type}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">জ্বালানির ধরন নির্বাচন করুন</option>
                <option value="petrol">পেট্রোল</option>
                <option value="diesel">ডিজেল</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ট্রান্সমিশন
              </label>
              <select
                name="transmission"
                value={formData.transmission}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">ট্রান্সমিশন নির্বাচন করুন</option>
                <option value="Manual">ম্যানুয়াল</option>
                <option value="Automatic">অটোমেটিক</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ইঞ্জিন সিসি
              </label>
              <input
                type="number"
                name="engine_capacity"
                value={formData.engine_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 2000"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মাইলেজ (কিমি)
              </label>
              <input
                type="number"
                name="kilometers_run"
                value={formData.kilometers_run}
                onChange={handleInputChange}
                placeholder="যেমন: 100000"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                লোডিং ক্যাপাসিটি (টন)
              </label>
              <input
                type="number"
                step="0.1"
                name="loading_capacity"
                value={formData.loading_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 2.5"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                সিটিং ক্যাপাসিটি
              </label>
              <input
                type="number"
                name="seating_capacity"
                value={formData.seating_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 12"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রঙ
              </label>
              <input
                type="text"
                name="color"
                value={formData.color}
                onChange={handleInputChange}
                placeholder="যেমন: সাদা"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বডি টাইপ
              </label>
              <select
                name="body_type"
                value={formData.body_type}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বডি টাইপ নির্বাচন করুন</option>
                <option value="covered">কভার্ড ভ্যান</option>
                <option value="open">ওপেন ট্রাক</option>
                <option value="pickup">পিকআপ</option>
                <option value="freezer">ফ্রিজার ভ্যান</option>
                <option value="refrigerated">রেফ্রিজারেটেড</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মালিক সংখ্যা
              </label>
              <input
                type="number"
                name="number_of_owners"
                value={formData.number_of_owners}
                onChange={handleInputChange}
                placeholder="যেমন: 1"
                min="1"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রেজিস্ট্রেশন বছর
              </label>
              <select
                name="registration_year"
                value={formData.registration_year}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ট্যাক্স বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="tax_valid_until"
                value={formData.tax_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ফিটনেস বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="fitness_valid_until"
                value={formData.fitness_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="আপনার গাড়ি সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    if (['van', 'vans'].includes(categoryName)) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড *
              </label>
              <select
                name="brand"
                value={formData.brand}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.brand ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">ব্র্যান্ড নির্বাচন করুন</option>
                {brands.map(brand => (
                  <option key={brand.id} value={brand.name}>{brand.name}</option>
                ))}
              </select>
              {errors.brand && <p className="text-red-500 text-sm mt-1">{errors.brand}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মডেল *
              </label>
              <input
                type="text"
                name="model"
                value={formData.model}
                onChange={handleInputChange}
                list="models-list-van"
                placeholder="মডেল নাম লিখুন বা নির্বাচন করুন"
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.model ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              <datalist id="models-list-van">
                {models.map((model, index) => (
                  <option key={index} value={model.name} />
                ))}
              </datalist>
              {errors.model && <p className="text-red-500 text-sm mt-1">{errors.model}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বছর *
              </label>
              <select
                name="year_of_manufacture"
                value={formData.year_of_manufacture}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.year_of_manufacture ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
              {errors.year_of_manufacture && <p className="text-red-500 text-sm mt-1">{errors.year_of_manufacture}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                জ্বালানির ধরন *
              </label>
              <select
                name="fuel_type"
                value={formData.fuel_type}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.fuel_type ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">জ্বালানির ধরন নির্বাচন করুন</option>
                <option value="Petrol">পেট্রোল</option>
                <option value="Diesel">ডিজেল</option>
                <option value="CNG">সিএনজি</option>
                <option value="Electric">ইলেকট্রিক</option>
              </select>
              {errors.fuel_type && <p className="text-red-500 text-sm mt-1">{errors.fuel_type}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ইঞ্জিন সিসি *
              </label>
              <input
                type="number"
                name="engine_capacity"
                value={formData.engine_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 2000"
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.engine_capacity ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {errors.engine_capacity && <p className="text-red-500 text-sm mt-1">{errors.engine_capacity}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                সিটিং ক্যাপাসিটি *
              </label>
              <input
                type="number"
                name="seating_capacity"
                value={formData.seating_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 12"
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.seating_capacity ? 'border-red-500' : 'border-gray-300'
                }`}
              />
              {errors.seating_capacity && <p className="text-red-500 text-sm mt-1">{errors.seating_capacity}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রেজিস্ট্রেশন বছর *
              </label>
              <select
                name="registration_year"
                value={formData.registration_year}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.registration_year ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
              {errors.registration_year && <p className="text-red-500 text-sm mt-1">{errors.registration_year}</p>}
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা *
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                  errors.condition ? 'border-red-500' : 'border-gray-300'
                }`}
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
              </select>
              {errors.condition && <p className="text-red-500 text-sm mt-1">{errors.condition}</p>}
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="আপনার ভ্যান সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    if (categoryName.includes('three') || categoryName.includes('wheel')) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                টাইপ
              </label>
              <select
                name="three_wheel_type"
                value={formData.three_wheel_type}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">টাইপ নির্বাচন করুন</option>
                <option value="cng">সিএনজি</option>
                <option value="electric">ইলেকট্রিক</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড
              </label>
              <select
                name="brand"
                value={formData.brand}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">ব্র্যান্ড নির্বাচন করুন</option>
                {brands.map(brand => (
                  <option key={brand.id} value={brand.name}>{brand.name}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মডেল
              </label>
              <input
                type="text"
                name="model"
                value={formData.model}
                onChange={handleInputChange}
                placeholder="মডেল নাম লিখুন"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                বছর
              </label>
              <select
                name="year_of_manufacture"
                value={formData.year_of_manufacture}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রেজিস্ট্রেশন বছর
              </label>
              <select
                name="registration_year"
                value={formData.registration_year}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">বছর নির্বাচন করুন</option>
                {years.map(year => (
                  <option key={year} value={year}>{year}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ইঞ্জিন সিসি
              </label>
              <input
                type="number"
                name="engine_capacity"
                value={formData.engine_capacity}
                onChange={handleInputChange}
                placeholder="যেমন: 200"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মাইলেজ (কিমি)
              </label>
              <input
                type="number"
                name="kilometers_run"
                value={formData.kilometers_run}
                onChange={handleInputChange}
                placeholder="যেমন: 50000"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রঙ
              </label>
              <input
                type="text"
                name="color"
                value={formData.color}
                onChange={handleInputChange}
                placeholder="যেমন: সবুজ"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                মালিক সংখ্যা
              </label>
              <input
                type="number"
                name="number_of_owners"
                value={formData.number_of_owners}
                onChange={handleInputChange}
                placeholder="যেমন: 1"
                min="1"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                রেজিস্ট্রেশন স্ট্যাটাস
              </label>
              <select
                name="registration_status"
                value={formData.registration_status}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">স্ট্যাটাস নির্বাচন করুন</option>
                <option value="registered">রেজিস্টার্ড</option>
                <option value="unregistered">আনরেজিস্টার্ড</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ট্যাক্স বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="tax_valid_until"
                value={formData.tax_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ফিটনেস বৈধ পর্যন্ত
              </label>
              <input
                type="date"
                name="fitness_valid_until"
                value={formData.fitness_valid_until}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="আপনার গাড়ি সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    if (categoryName.includes('parts') || categoryName.includes('part')) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                পার্টস নাম *
              </label>
              <input
                type="text"
                name="part_name"
                value={formData.part_name}
                onChange={handleInputChange}
                placeholder="যেমন: ইঞ্জিন অয়েল ফিল্টার"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড (ঐচ্ছিক)
              </label>
              <input
                type="text"
                name="brand_name"
                value={formData.brand_name || ''}
                onChange={handleInputChange}
                placeholder="ব্র্যান্ড নাম লিখুন"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                উপযুক্ত গাড়ি
              </label>
              <input
                type="text"
                name="compatible_vehicle"
                value={formData.compatible_vehicle}
                onChange={handleInputChange}
                placeholder="যেমন: টয়োটা করোলা 2015-2020"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ওয়ারেন্টি
              </label>
              <select
                name="warranty"
                value={formData.warranty}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">ওয়ারেন্টি নির্বাচন করুন</option>
                <option value="yes">হ্যাঁ</option>
                <option value="no">না</option>
              </select>
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="পার্টস সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    if (categoryName.includes('accessor')) {
      return (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                পণ্যের নাম *
              </label>
              <input
                type="text"
                name="product_name"
                value={formData.product_name}
                onChange={handleInputChange}
                placeholder="যেমন: কার সিট কভার"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ক্যাটাগরি টাইপ
              </label>
              <input
                type="text"
                name="accessory_category"
                value={formData.accessory_category}
                onChange={handleInputChange}
                placeholder="যেমন: ইন্টেরিয়র"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                ব্র্যান্ড (ঐচ্ছিক)
              </label>
              <input
                type="text"
                name="brand_name"
                value={formData.brand_name || ''}
                onChange={handleInputChange}
                placeholder="ব্র্যান্ড নাম লিখুন"
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>

            <div>
              <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                অবস্থা
              </label>
              <select
                name="condition"
                value={formData.condition}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="">অবস্থা নির্বাচন করুন</option>
                <option value="New">নতুন</option>
                <option value="Used">ব্যবহৃত</option>
              </select>
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিবরণ *
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="পণ্য সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {formData.description.length}/30 অক্ষর
            </p>
          </div>
        </div>
      )
    }

    // Default form for other categories
    return (
      <div className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              ব্র্যান্ড/কোম্পানি
            </label>
            <input
              type="text"
              name="brand"
              value={formData.brand}
              onChange={handleInputChange}
              placeholder="ব্র্যান্ড বা কোম্পানির নাম"
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              মডেল/টাইপ
            </label>
            <input
              type="text"
              name="model"
              value={formData.model}
              onChange={handleInputChange}
              placeholder="মডেল বা টাইপ"
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বছর
            </label>
            <select
              name="year_of_manufacture"
              value={formData.year_of_manufacture}
              onChange={handleInputChange}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="">বছর নির্বাচন করুন</option>
              {years.map(year => (
                <option key={year} value={year}>{year}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              অবস্থা
            </label>
            <select
              name="condition"
              value={formData.condition}
              onChange={handleInputChange}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="">অবস্থা নির্বাচন করুন</option>
              <option value="New">নতুন</option>
              <option value="Used">ব্যবহৃত</option>
            </select>
          </div>
        </div>

        <div>
          <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
            বিবরণ *
          </label>
          <textarea
            name="description"
            value={formData.description}
            onChange={handleInputChange}
            rows="5"
            placeholder="আপনার পণ্য/সেবা সম্পর্কে বিস্তারিত তথ্য লিখুন... (কমপক্ষে ৩০ অক্ষর) - অবস্থা, সার্ভিস হিস্টরি, বিক্রয়ের কারণ উল্লেখ করুন"
            className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none ${
              errors.description ? 'border-red-500' : 'border-gray-300'
            }`}
          />
          {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
          <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {formData.description.length}/30 অক্ষর
          </p>
        </div>
      </div>
    )
  }

  const renderStep1 = () => (
    <div className="space-y-6">
      <div>
        <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
          ক্যাটাগরি নির্বাচন করুন *
        </label>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {categories.map(category => (
            <button
              key={category.id}
              type="button"
              onClick={() => {
                setFormData(prev => ({ ...prev, category_id: category.id }))
                setErrors(prev => ({ ...prev, category_id: '' }))
              }}
              className={`p-6 border-2 rounded-xl font-bold transition-all ${
                formData.category_id === category.id
                  ? 'border-teal-600 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300'
                  : 'border-gray-300 dark:border-gray-600 hover:border-teal-400 dark:hover:border-teal-500'
              }`}
            >
              {category.name}
            </button>
          ))}
        </div>
        {errors.category_id && <p className="text-red-500 text-sm mt-2">{errors.category_id}</p>}
      </div>

      <div>
        <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
          বিজ্ঞাপনের শিরোনাম *
        </label>
        <input
          type="text"
          name="title"
          value={formData.title}
          onChange={handleInputChange}
          placeholder="যেমন: টয়োটা করোলা 2020 অটোমেটিক"
          className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
            errors.title ? 'border-red-500' : 'border-gray-300'
          }`}
        />
        {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title}</p>}
      </div>
    </div>
  )

  const renderStep2 = () => (
    <div className="space-y-6">
      {renderCategoryFields()}
    </div>
  )

  const renderStep3 = () => (
    <div className="space-y-6">
      <div>
        <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
          ছবি আপলোড করুন (সর্বোচ্চ ১০টি) *
        </label>
        
        <div
          onClick={() => fileInputRef.current?.click()}
          className="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-12 text-center cursor-pointer hover:border-teal-500 transition-colors"
        >
          <Upload size={48} className="mx-auto text-gray-400 mb-4" />
          <p className="text-gray-600 dark:text-gray-400 font-semibold mb-2">
            ক্লিক করুন অথবা ছবি টেনে আনুন
          </p>
          <p className="text-sm text-gray-500 dark:text-gray-500">
            প্রথম ছবিটি কভার ফটো হবে • সর্বোচ্চ ৫ MB প্রতিটি
          </p>
        </div>
        
        <input
          ref={fileInputRef}
          type="file"
          accept="image/*"
          multiple
          onChange={handleImageSelect}
          className="hidden"
        />
        
        {errors.images && <p className="text-red-500 text-sm mt-2">{errors.images}</p>}
      </div>

      {imagePreviews.length > 0 && (
        <div>
          <p className="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
            {imagePreviews.length} টি ছবি নির্বাচিত (টেনে সাজান)
          </p>
          <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
            {imagePreviews.map((preview, index) => (
              <div
                key={index}
                draggable
                onDragStart={() => handleDragStart(index)}
                onDragOver={(e) => handleDragOver(e, index)}
                onDragEnd={handleDragEnd}
                className="relative group cursor-move"
              >
                {index === 0 && (
                  <div className="absolute -top-2 -left-2 bg-teal-600 text-white text-xs font-bold px-2 py-1 rounded-full z-10">
                    কভার
                  </div>
                )}
                <img
                  src={preview}
                  alt={`Preview ${index + 1}`}
                  className="w-full h-32 object-cover rounded-xl border-2 border-gray-200 dark:border-gray-700"
                />
                <button
                  type="button"
                  onClick={() => removeImage(index)}
                  className="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <X size={16} />
                </button>
                <div className="absolute bottom-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <GripVertical size={20} className="text-white drop-shadow-lg" />
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )

  const renderStep4 = () => (
    <div className="space-y-6">
      <div className="bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 rounded-xl p-6">
        <h3 className="font-bold text-teal-900 dark:text-teal-100 mb-4 flex items-center gap-2">
          <DollarSign size={20} />
          মূল্য ও ঠিকানা
        </h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="md:col-span-2">
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              মূল্য (টাকা) *
            </label>
            <input
              type="number"
              name="price"
              value={formData.price}
              onChange={handleInputChange}
              placeholder="যেমন: 1500000"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                errors.price ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.price && <p className="text-red-500 text-sm mt-1">{errors.price}</p>}
          </div>

          <div className="md:col-span-2">
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                name="negotiable"
                checked={formData.negotiable}
                onChange={handleInputChange}
                className="w-5 h-5 text-teal-600 rounded focus:ring-teal-500"
              />
              <span className="text-sm font-semibold text-gray-700 dark:text-gray-300">
                দামে কথা হবে (Negotiable)
              </span>
            </label>
          </div>

          <div className="md:col-span-2">
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                name="exchange_option"
                checked={formData.exchange_option}
                onChange={handleInputChange}
                className="w-5 h-5 text-teal-600 rounded focus:ring-teal-500"
              />
              <span className="text-sm font-semibold text-gray-700 dark:text-gray-300">
                এক্সচেঞ্জ করা যাবে (Exchange Option)
              </span>
            </label>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              বিভাগ *
            </label>
            <select
              name="division"
              value={formData.division}
              onChange={handleInputChange}
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                errors.division ? 'border-red-500' : 'border-gray-300'
              }`}
            >
              <option value="">বিভাগ নির্বাচন করুন</option>
              {divisions.map(division => (
                <option key={division} value={division}>{division}</option>
              ))}
            </select>
            {errors.division && <p className="text-red-500 text-sm mt-1">{errors.division}</p>}
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              জেলা *
            </label>
            <input
              type="text"
              name="district"
              value={formData.district}
              onChange={handleInputChange}
              placeholder="যেমন: ঢাকা"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                errors.district ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.district && <p className="text-red-500 text-sm mt-1">{errors.district}</p>}
          </div>

          <div className="md:col-span-2">
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              এলাকা
            </label>
            <input
              type="text"
              name="area"
              value={formData.area}
              onChange={handleInputChange}
              placeholder="যেমন: মিরপুর ১০"
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>
        </div>
      </div>

      <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
        <h3 className="font-bold text-blue-900 dark:text-blue-100 mb-4 flex items-center gap-2">
          <Phone size={20} />
          যোগাযোগের তথ্য
        </h3>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              নাম
            </label>
            <input
              type="text"
              name="contact_name"
              value={formData.contact_name}
              onChange={handleInputChange}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              ফোন *
            </label>
            <input
              type="tel"
              name="contact_phone"
              value={formData.contact_phone}
              onChange={handleInputChange}
              placeholder="০১৭xxxxxxxx"
              className={`w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white ${
                errors.contact_phone ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.contact_phone && <p className="text-red-500 text-sm mt-1">{errors.contact_phone}</p>}
          </div>

          <div className="md:col-span-2">
            <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
              ইমেইল
            </label>
            <input
              type="email"
              name="contact_email"
              value={formData.contact_email}
              onChange={handleInputChange}
              className="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            />
          </div>

          <div className="md:col-span-2 space-y-3">
            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                name="hide_phone"
                checked={formData.hide_phone}
                onChange={handleInputChange}
                className="w-5 h-5 text-teal-600 rounded focus:ring-teal-500"
              />
              <span className="text-sm font-semibold text-gray-700 dark:text-gray-300">
                ফোন নম্বর লুকান
              </span>
            </label>

            <label className="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                name="allow_chat"
                checked={formData.allow_chat}
                onChange={handleInputChange}
                className="w-5 h-5 text-teal-600 rounded focus:ring-teal-500"
              />
              <span className="text-sm font-semibold text-gray-700 dark:text-gray-300">
                চ্যাট চালু রাখুন
              </span>
            </label>
          </div>
        </div>
      </div>
    </div>
  )

  return (
    <div className="min-h-screen bg-gradient-to-br from-teal-50 to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
      {/* Header */}
      <div className="bg-gradient-to-r from-teal-600 to-teal-700 dark:from-teal-700 dark:to-teal-800 py-8">
        <div className="max-w-5xl mx-auto px-4">
          <button
            onClick={onBack}
            className="flex items-center gap-2 text-white hover:text-gray-200 mb-4"
          >
            <ArrowLeft size={20} />
            <span>ফিরে যান</span>
          </button>
          <h1 className="text-3xl font-black text-white">নতুন বিজ্ঞাপন দিন</h1>
          <p className="text-teal-100 mt-2">আপনার পণ্যের তথ্য দিয়ে বিজ্ঞাপন পোস্ট করুন</p>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-5xl mx-auto px-4 py-8">
        {/* Success Message */}
        {successMessage && (
          <div className="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <Check size={24} />
            <span className="font-semibold">{successMessage}</span>
          </div>
        )}

        {/* Error Message */}
        {errors.general && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6">
            {errors.general}
          </div>
        )}

        {/* Step Indicator */}
        {renderStepIndicator()}

        {/* Form */}
        <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
          {currentStep === 1 && renderStep1()}
          {currentStep === 2 && renderStep2()}
          {currentStep === 3 && renderStep3()}
          {currentStep === 4 && renderStep4()}
        </div>

        {/* Navigation Buttons */}
        <div className="flex items-center justify-between mt-8">
          {currentStep > 1 && (
            <button
              onClick={prevStep}
              className="flex items-center gap-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-8 py-4 rounded-xl font-bold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
            >
              <ArrowLeft size={20} />
              পূর্ববর্তী
            </button>
          )}
          
          <div className="flex-1" />
          
          {currentStep < 4 ? (
            <button
              onClick={nextStep}
              className="flex items-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl"
            >
              পরবর্তী
              <ArrowRight size={20} />
            </button>
          ) : (
            <button
              onClick={handleSubmit}
              disabled={loading}
              className="flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loading ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                  পোস্ট হচ্ছে...
                </>
              ) : (
                <>
                  <Check size={20} />
                  বিজ্ঞাপন পোস্ট করুন
                </>
              )}
            </button>
          )}
        </div>
      </div>

      {/* Sticky Mobile Button */}
      <div className="fixed bottom-0 left-0 right-0 p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 md:hidden">
        {currentStep < 4 ? (
          <button
            onClick={nextStep}
            className="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white py-4 rounded-xl font-bold"
          >
            পরবর্তী
            <ArrowRight size={20} />
          </button>
        ) : (
          <button
            onClick={handleSubmit}
            disabled={loading}
            className="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-xl font-bold disabled:opacity-50"
          >
            {loading ? 'পোস্ট হচ্ছে...' : 'বিজ্ঞাপন পোস্ট করুন'}
          </button>
        )}
      </div>
    </div>
  )
}
