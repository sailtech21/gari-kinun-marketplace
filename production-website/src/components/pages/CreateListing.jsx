import { useState, useEffect, useRef } from 'react'
import { ArrowLeft, ArrowRight, Upload, X, Camera, MapPin, DollarSign, Car, Gauge, Calendar, Fuel, Settings, Phone, Link as LinkIcon, FileText } from 'lucide-react'
import { apiCall } from '../../config'

// Car brands list
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
  'Mahindra',
  'Isuzu',
  'Lexus',
  'Subaru',
  'Chevrolet',
  'MG (Morris Garages)',
  'Tata',
  'Proton',
  'Others'
]

// Bike brands list
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
  'Apache (TVS)',
  'Keeway',
  'UM (United Motors)',
  'Zontes',
  'Kawasaki',
  'Ducati',
  'Others'
]

export default function CreateListing({ onBack, onSuccess, editingListing }) {
  const [currentStep, setCurrentStep] = useState(1)
  const [loading, setLoading] = useState(false)
  const [categories, setCategories] = useState([])
  const [selectedCategory, setSelectedCategory] = useState(null)
  const [images, setImages] = useState([])
  const [imagePreviews, setImagePreviews] = useState([])
  const [mainImageIndex, setMainImageIndex] = useState(0)
  const [mapCenter, setMapCenter] = useState({ lat: 23.8103, lng: 90.4125 }) // Default: Dhaka
  const [markerPosition, setMarkerPosition] = useState(null)
  const mapRef = useRef(null)
  const mapInstanceRef = useRef(null)
  
  const [formData, setFormData] = useState({
    // Step 1: Basic Information
    category_id: '',
    title: '',
    description: '',
    price: '',
    phone: '',
    video_link: '',
    slug: '',
    
    // Step 2: Vehicle Details
    condition: '',
    model: '',
    year_of_manufacture: '',
    engine_capacity: '',
    transmission: '',
    registration_year: '',
    brand: '',
    trim_edition: '',
    kilometers_run: '',
    fuel_type: '',
    body_type: '',
    
    // Step 4: Location
    location: '',
    latitude: '',
    longitude: '',
  })
  
  const [errors, setErrors] = useState({})

  useEffect(() => {
    fetchCategories()
    // Pre-fill form if editing
    if (editingListing) {
      setFormData({
        category_id: editingListing.category_id || '',
        title: editingListing.title || '',
        description: editingListing.description || '',
        price: editingListing.price || '',
        phone: editingListing.phone || '',
        video_link: editingListing.video_link || '',
        slug: editingListing.slug || '',
        condition: editingListing.condition || '',
        model: editingListing.model || '',
        year_of_manufacture: editingListing.year_of_manufacture || '',
        engine_capacity: editingListing.engine_capacity || '',
        transmission: editingListing.transmission || '',
        registration_year: editingListing.registration_year || '',
        brand: editingListing.brand || '',
        trim_edition: editingListing.trim_edition || '',
        kilometers_run: editingListing.kilometers_run || '',
        fuel_type: editingListing.fuel_type || '',
        body_type: editingListing.body_type || '',
        location: editingListing.location || '',
      })
      // Load existing images if any
      if (editingListing.images) {
        const existingImages = typeof editingListing.images === 'string' 
          ? JSON.parse(editingListing.images) 
          : editingListing.images
        setImagePreviews(existingImages)
      }
    }
  }, [editingListing])

  // Auto-generate slug from title
  useEffect(() => {
    if (formData.title && !editingListing) {
      const baseSlug = formData.title
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim()
      // Add timestamp to make slug unique
      const uniqueSlug = `${baseSlug}-${Date.now()}`
      setFormData(prev => ({ ...prev, slug: uniqueSlug }))
    }
  }, [formData.title, editingListing])

  // Initialize Leaflet Map for Step 4
  useEffect(() => {
    if (currentStep === 4 && mapRef.current && !mapInstanceRef.current && typeof window !== 'undefined' && window.L) {
      // Create map centered on Dhaka
      const map = window.L.map(mapRef.current).setView([23.8103, 90.4125], 12)
      
      // Add OpenStreetMap tiles
      window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
      }).addTo(map)
      
      // Add a Click event to place marker
      let marker = null
      map.on('click', (e) => {
        const { lat, lng } = e.latlng
        
        // Remove old marker if exists
        if (marker) {
          map.removeLayer(marker)
        }
        
        // Add new marker
        marker = window.L.marker([lat, lng]).addTo(map)
        
        // Update state
        setMarkerPosition({ lat, lng })
        setFormData(prev => ({
          ...prev,
          latitude: lat.toFixed(6),
          longitude: lng.toFixed(6),
        }))
        
        // Optional: Reverse geocode to get address (would need external API)
      })
      
      mapInstanceRef.current = map
    }
    
    //Cleanup
    return () => {
      if (mapInstanceRef.current) {
        mapInstanceRef.current.remove()
        mapInstanceRef.current = null
      }
    }
  }, [currentStep])

  const fetchCategories = async () => {
    try {
      const response = await apiCall('/categories')
      if (response.success && response.data) {
        setCategories(Array.isArray(response.data) ? response.data : [])
      } else {
        setCategories([])
      }
    } catch (error) {
      console.error('Error fetching categories:', error)
      setCategories([])
    }
  }

  const handleInputChange = (e) => {
    const { name, value } = e.target
    
    // Handle category selection - find and set selected category type
    if (name === 'category_id') {
      const category = categories.find(cat => cat.id === parseInt(value))
      setSelectedCategory(category)
    }
    
    setFormData(prev => ({
      ...prev,
      [name]: value
    }))
    // Clear error when user types
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }))
    }
  }

  const handleImageChange = (e) => {
    const files = Array.from(e.target.files)
    if (images.length + files.length > 10) {
      alert('সর্বোচ্চ ১০টি ছবি আপলোড করতে পারবেন')
      return
    }

    setImages(prev => [...prev, ...files])

    // Create previews
    files.forEach(file => {
      const reader = new FileReader()
      reader.onloadend = () => {
        setImagePreviews(prev => [...prev, reader.result])
      }
      reader.readAsDataURL(file)
    })
  }

  const removeImage = (index) => {
    setImages(prev => prev.filter((_, i) => i !== index))
    setImagePreviews(prev => prev.filter((_, i) => i !== index))
    if (mainImageIndex === index) {
      setMainImageIndex(0)
    } else if (mainImageIndex > index) {
      setMainImageIndex(mainImageIndex - 1)
    }
  }

  // Get category type (Car, Bike, or Other)
  const getCategoryType = () => {
    if (!selectedCategory) return 'Other'
    return selectedCategory.type || 'Other'
  }

  // Check if current category is a vehicle (Car or Bike)
  const isVehicleCategory = () => {
    const type = getCategoryType()
    return type === 'Car' || type === 'Bike'
  }

  const setMainImage = (index) => {
    setMainImageIndex(index)
  }

  const validateStep = (step) => {
    const newErrors = {}

    if (step === 1) {
      if (!formData.category_id) newErrors.category_id = 'ক্যাটাগরি নির্বাচন করুন'
      if (!formData.title.trim()) newErrors.title = 'শিরোনাম প্রয়োজন'
      if (!formData.description.trim()) newErrors.description = 'বিবরণ প্রয়োজন'
      if (!formData.price) newErrors.price = 'মূল্য প্রয়োজন'
      if (!formData.phone.trim()) newErrors.phone = 'ফোন নম্বর প্রয়োজন'
    }

    if (step === 2) {
      const isCar = getCategoryType() === 'Car'
      const isBike = getCategoryType() === 'Bike'
      const isVehicle = isCar || isBike
      
      // Required fields for all listings
      if (!formData.condition) newErrors.condition = 'অবস্থা নির্বাচন করুন'
      if (!formData.model.trim()) newErrors.model = 'মডেল প্রয়োজন'
      
      // Required fields for vehicles (Car/Bike)
      if (isVehicle) {
        // Year validation
        if (!formData.year_of_manufacture) {
          newErrors.year_of_manufacture = 'উৎপাদন বছর প্রয়োজন'
        } else {
          const year = parseInt(formData.year_of_manufacture)
          const currentYear = new Date().getFullYear()
          if (year < 1900 || year > currentYear + 1) {
            newErrors.year_of_manufacture = `উৎপাদন বছর ১৯০০ থেকে ${currentYear + 1} এর মধ্যে হতে হবে`
          }
        }
        
        // Validate registration year if provided (for Cars only)
        if (formData.registration_year) {
          const regYear = parseInt(formData.registration_year)
          const currentYear = new Date().getFullYear()
          if (regYear < 1900 || regYear > currentYear) {
            newErrors.registration_year = `নিবন্ধন বছর ১৯০০ থেকে ${currentYear} এর মধ্যে হতে হবে`
          }
        }
      }
      
      // Additional required fields for Cars only
      if (isCar) {
        if (!formData.brand) newErrors.brand = 'ব্র্যান্ড নির্বাচন করুন'
        if (!formData.fuel_type) newErrors.fuel_type = 'জ্বালানির ধরন নির্বাচন করুন'
        if (!formData.transmission) newErrors.transmission = 'ট্রান্সমিশন নির্বাচন করুন'
        if (!formData.engine_capacity) newErrors.engine_capacity = 'ইঞ্জিন ক্ষমতা প্রয়োজন'
        if (!formData.kilometers_run) newErrors.kilometers_run = 'কিলোমিটার প্রয়োজন'
      }
    }

    if (step === 3) {
      if (images.length === 0 && imagePreviews.length === 0) {
        newErrors.images = 'কমপক্ষে একটি ছবি আপলোড করুন'
        alert('কমপক্ষে একটি ছবি আপলোড করুন')
      }
    }

    if (step === 4) {
      if (!formData.location || !formData.location.trim()) newErrors.location = 'অবস্থান প্রয়োজন'
      // Validate images one more time
      if (images.length === 0 && imagePreviews.length === 0) {
        newErrors.images = 'কমপক্ষে একটি ছবি আপলোড করুন'
      }
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

  const handleSubmit = async (e) => {
    e.preventDefault()

    console.log('Submitting form...', { 
      formData, 
      imageCount: images.length,
      currentStep 
    })

    if (!validateStep(4)) {
      console.error('Validation failed at step 4')
      return
    }

    const token = localStorage.getItem('auth_token')
    if (!token) {
      alert('বিজ্ঞাপন দিতে লগইন করুন')
      return
    }

    console.log('Token:', token ? 'Present' : 'Missing')
    console.log('Images to upload:', images.length)

    setLoading(true)

    try {
      const submitData = new FormData()
      
      // Append form fields
      Object.keys(formData).forEach(key => {
        if (formData[key]) {
          submitData.append(key, formData[key])
        }
      })

      // Append images
      images.forEach((image) => {
        submitData.append('images[]', image)
      })

      // Mark the main image
      submitData.append('main_image_index', mainImageIndex)

      // Debug: Log FormData contents
      console.log('FormData contents:')
      for (let pair of submitData.entries()) {
        console.log(pair[0], pair[1])
      }

      const endpoint = editingListing ? `/listings/${editingListing.id}` : '/listings'
      const method = editingListing ? 'PUT' : 'POST'

      const API_BASE_URL = import.meta.env.MODE === 'production' 
        ? 'https://admin.garikinun.com/api'
        : 'http://localhost:8000/api'

      const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        method: method,
        headers: {
          'Authorization': `Bearer ${token}`,
        },
        body: submitData
      })

      if (!response.ok) {
        let errorData
        const contentType = response.headers.get('content-type')
        
        if (contentType && contentType.includes('application/json')) {
          errorData = await response.json()
          // Enhanced logging for validation errors
          console.error('Full API Error Response:', JSON.stringify(errorData, null, 2))
          if (errorData.errors) {
            console.error('Validation Errors:', errorData.errors)
          }
        } else {
          const textResponse = await response.text()
          console.error('Non-JSON response:', textResponse.substring(0, 500))
          errorData = { message: 'Server returned an error. Please check console.' }
        }
        
        console.error('Response Status:', response.status)
        
        if (response.status === 401) {
          alert('আপনার সেশন শেষ হয়ে গেছে। পুনরায় লগইন করুন।')
          localStorage.removeItem('auth_token')
          onBack()
          return
        }
        
        if (errorData.errors) {
          setErrors(errorData.errors)
          // Show first validation error
          const firstError = Object.values(errorData.errors)[0]
          alert('যাচাইকরণ ত্রুটি: ' + (Array.isArray(firstError) ? firstError[0] : firstError))
        } else {
          alert('ত্রুটি: ' + (errorData.message || 'বিজ্ঞাপন জমা দিতে সমস্যা হয়েছে'))
        }
        throw new Error('Failed to create listing')
      }

      const data = await response.json()
      console.log('Success:', data)
      alert('বিজ্ঞাপন সফলভাবে জমা হয়েছে!')
      if (onSuccess) {
        onSuccess(data)
      } else {
        onBack()
      }
    } catch (error) {
      console.error('Error creating listing:', error)
      alert('বিজ্ঞাপন জমা দিতে সমস্যা হয়েছে। আবার চেষ্টা করুন।')
    } finally {
      setLoading(false)
    }
  }

  const renderStepIndicator = () => (
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
  )

  const renderStep1 = () => (
    <div className="space-y-8">
      {/* Category Selection */}
      <div className="bg-white rounded-2xl shadow-lg p-8">
        <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
          <Car className="text-teal-600" size={28} />
          ক্যাটাগরি নির্বাচন করুন
        </h2>
        <select
          name="category_id"
          value={formData.category_id}
          onChange={handleInputChange}
          className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
            errors.category_id ? 'border-red-500' : 'border-gray-300'
          }`}
        >
          <option value="">নির্বাচন করুন</option>
          {categories.map(cat => (
            <option key={cat.id} value={cat.id}>{cat.name}</option>
          ))}
        </select>
        {errors.category_id && <p className="text-red-500 text-sm mt-2">{errors.category_id}</p>}
      </div>

      {/* Basic Information */}
      <div className="bg-white rounded-2xl shadow-lg p-8">
        <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
          <FileText className="text-teal-600" size={28} />
          মূল তথ্য
        </h2>

        <div className="space-y-6">
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              শিরোনাম <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="title"
              value={formData.title}
              onChange={handleInputChange}
              placeholder="যেমন: Toyota Corolla 2020"
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.title ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title}</p>}
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              বিস্তারিত বিবরণ <span className="text-red-500">*</span>
            </label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              rows="5"
              placeholder="গাড়ি সম্পর্কে বিস্তারিত লিখুন..."
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.description ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.description && <p className="text-red-500 text-sm mt-1">{errors.description}</p>}
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <DollarSign size={16} className="inline" /> মূল্য (টাকা) <span className="text-red-500">*</span>
            </label>
            <input
              type="number"
              name="price"
              value={formData.price}
              onChange={handleInputChange}
              placeholder="যেমন: 2500000"
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.price ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.price && <p className="text-red-500 text-sm mt-1">{errors.price}</p>}
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <Phone size={16} className="inline" /> ফোন নম্বর <span className="text-red-500">*</span>
            </label>
            <input
              type="tel"
              name="phone"
              value={formData.phone}
              onChange={handleInputChange}
              placeholder="যেমন: 01712345678"
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.phone ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.phone && <p className="text-red-500 text-sm mt-1">{errors.phone}</p>}
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <LinkIcon size={16} className="inline" /> ভিডিও লিংক (ঐচ্ছিক)
            </label>
            <input
              type="url"
              name="video_link"
              value={formData.video_link}
              onChange={handleInputChange}
              placeholder="YouTube বা অন্য ভিডিও লিংক"
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
            />
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              Slug (স্বয়ংক্রিয়)
            </label>
            <input
              type="text"
              name="slug"
              value={formData.slug}
              readOnly
              className="w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 text-gray-600"
            />
            <p className="text-xs text-gray-500 mt-1">শিরোনাম থেকে স্বয়ংক্রিয়ভাবে তৈরি হবে</p>
          </div>
        </div>
      </div>
    </div>
  )

  const renderStep2 = () => {
    const categoryType = getCategoryType()
    const isCar = categoryType === 'Car'
    const isBike = categoryType === 'Bike'
    const isOther = !isCar && !isBike
    
    const title = isCar ? '🚗 গাড়ির বিবরণ' : isBike ? '🏍️ বাইকের বিবরণ' : '📦 পণ্যের বিবরণ'
    
    return (
    <div className="bg-white rounded-2xl shadow-lg p-8">
      <h2 className="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-3">
        <Settings className="text-teal-600" size={28} />
        {title}
      </h2>
      {selectedCategory && (
        <p className="text-sm text-gray-600 mb-6">
          ক্যাটাগরি: <span className="font-semibold text-teal-600">{selectedCategory.name}</span>
          {isCar && ' - গাড়ির জন্য সকল ফিল্ডিং প্রযোজ্য'}
          {isBike && ' - বাইকের জন্য প্রযোজ্য ফিল্ড দেখানো হচ্ছে'}
          {isOther && ' - পণ্যের বেসিক তথ্য দিন'}
        </p>
      )}

      <div className="grid md:grid-cols-2 gap-6">
        <div>
          <label className="block text-sm font-bold text-gray-700 mb-2">
            অবস্থা <span className="text-red-500">*</span>
          </label>
          <select
            name="condition"
            value={formData.condition}
            onChange={handleInputChange}
            className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
              errors.condition ? 'border-red-500' : 'border-gray-300'
            }`}
          >
            <option value="">নির্বাচন করুন</option>
            <option value="Used">ব্যবহৃত (Used)</option>
            <option value="New">নতুন (New)</option>
            <option value="Reconditioned">রিকন্ডিশন (Reconditioned)</option>
          </select>
          {errors.condition && <p className="text-red-500 text-sm mt-1">{errors.condition}</p>}
        </div>

        <div>
          <label className="block text-sm font-bold text-gray-700 mb-2">
            মডেল <span className="text-red-500">*</span>
          </label>
          <input
            type="text"
            name="model"
            value={formData.model}
            onChange={handleInputChange}
            placeholder="যেমন: Allion A15"
            className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
              errors.model ? 'border-red-500' : 'border-gray-300'
            }`}
          />
          {errors.model && <p className="text-red-500 text-sm mt-1">{errors.model}</p>}
        </div>

        <div>
          <label className="block text-sm font-bold text-gray-700 mb-2">
            <Calendar size={16} className="inline" /> উৎপাদন বছর <span className="text-red-500">*</span>
          </label>
          <input
            type="number"
            name="year_of_manufacture"
            value={formData.year_of_manufacture}
            onChange={handleInputChange}
            placeholder="যেমন: 2020"
            min="1900"
            max={new Date().getFullYear() + 1}
            className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
              errors.year_of_manufacture ? 'border-red-500' : 'border-gray-300'
            }`}
          />
          {errors.year_of_manufacture && <p className="text-red-500 text-sm mt-1">{errors.year_of_manufacture}</p>}
        </div>

        {/* Engine Capacity - Show for vehicles */}
        {isVehicleCategory() && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <Gauge size={16} className="inline" /> ইঞ্জিন ক্ষমতা (cc) {isCar && <span className="text-red-600">*</span>}
            </label>
            <input
              type="number"
              name="engine_capacity"
              value={formData.engine_capacity}
              onChange={handleInputChange}
              placeholder={isCar ? 'যেমন: 1500' : 'যেমন: 150'}
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.engine_capacity ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.engine_capacity && <p className="text-red-600 text-sm mt-1">{errors.engine_capacity}</p>}
          </div>
        )}

        {/* Transmission - Cars mostly */}
        {isCar && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <Settings size={16} className="inline" /> ট্রান্সমিশন <span className="text-red-600">*</span>
            </label>
            <select
              name="transmission"
              value={formData.transmission}
              onChange={handleInputChange}
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.transmission ? 'border-red-500' : 'border-gray-300'
              }`}
            >
              <option value="">নির্বাচন করুন</option>
              <option value="Manual">ম্যানুয়াল (Manual)</option>
              <option value="Automatic">অটোমেটিক (Automatic)</option>
              <option value="Other">অন্যান্য (Other)</option>
            </select>
            {errors.transmission && <p className="text-red-600 text-sm mt-1">{errors.transmission}</p>}
          </div>
        )}

        {/* Registration Year - Cars only */}
        {isCar && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              রেজিস্ট্রেশন বছর (ঐচ্ছিক)
            </label>
            <input
              type="number"
              name="registration_year"
              value={formData.registration_year}
              onChange={handleInputChange}
              placeholder="যেমন: 2020"
              min="1900"
              max={new Date().getFullYear()}
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
            />
          </div>
        )}

        {/* Brand - Dynamic based on category */}
        {(isCar || isBike) && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <Car size={16} className="inline" /> ব্র্যান্ড {isCar && <span className="text-red-600">*</span>}
            </label>
            <select
              name="brand"
              value={formData.brand}
              onChange={handleInputChange}
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 bg-white ${
                errors.brand ? 'border-red-500' : 'border-gray-300'
              }`}
            >
              <option value="">ব্র্যান্ড নির্বাচন করুন</option>
              {(isCar ? CAR_BRANDS : BIKE_BRANDS).map((brand) => (
                <option key={brand} value={brand}>
                  {brand}
                </option>
              ))}
            </select>
            {errors.brand && <p className="text-red-600 text-sm mt-1">{errors.brand}</p>}
          </div>
        )}

        {/* Model - Shows after brand is selected */}
        {(isCar || isBike) && formData.brand && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <Car size={16} className="inline" /> মডেল <span className="text-red-600">*</span> {isCar ? '(যেমন: Corolla, Civic)' : '(যেমন: R15, Apache)'}
            </label>
            <input
              type="text"
              name="model"
              value={formData.model}
              onChange={handleInputChange}
              placeholder={isCar ? "যেমন: Corolla" : "যেমন: R15"}
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.model ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.model && <p className="text-red-600 text-sm mt-1">{errors.model}</p>}
          </div>
        )}

        {/* Trim/Edition - Cars mostly */}
        {isCar && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              Trim / Edition (ঐচ্ছিক)
            </label>
            <input
              type="text"
              name="trim_edition"
              value={formData.trim_edition}
              onChange={handleInputChange}
              placeholder="যেমন: Premium"
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
            />
          </div>
        )}

        {/* Kilometers Run - Show for vehicles */}
        {isVehicleCategory() && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <Gauge size={16} className="inline" /> {isCar ? 'কিলোমিটার চলেছে (km)' : 'মাইলেজ (কিলোমিটার)'} {isCar && <span className="text-red-600">*</span>}
            </label>
            <input
              type="number"
              name="kilometers_run"
              value={formData.kilometers_run}
              onChange={handleInputChange}
              placeholder="যেমন: 45000"
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.kilometers_run ? 'border-red-500' : 'border-gray-300'
              }`}
            />
            {errors.kilometers_run && <p className="text-red-600 text-sm mt-1">{errors.kilometers_run}</p>}
          </div>
        )}

        {/* Fuel Type - Show for vehicles */}
        {isVehicleCategory() && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              <Fuel size={16} className="inline" /> জ্বালানির ধরন {isCar && <span className="text-red-600">*</span>}
            </label>
            <select
              name="fuel_type"
              value={formData.fuel_type}
              onChange={handleInputChange}
              className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
                errors.fuel_type ? 'border-red-500' : 'border-gray-300'
              }`}
            >
              <option value="">নির্বাচন করুন</option>
              <option value="Petrol">পেট্রোল (Petrol)</option>
              <option value="Diesel">ডিজেল (Diesel)</option>
              <option value="CNG">সিএনজি (CNG)</option>
              <option value="Octane">অকটেন (Octane)</option>
              <option value="Hybrid">হাইব্রিড (Hybrid)</option>
              <option value="Electric">ইলেকট্রিক (Electric)</option>
              <option value="LPG">এলপিজি (LPG)</option>
              <option value="Other">অন্যান্য (Other)</option>
            </select>
            {errors.fuel_type && <p className="text-red-600 text-sm mt-1">{errors.fuel_type}</p>}
          </div>
        )}

        {/* Body Type - Cars only */}
        {isCar && (
          <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
              বডি টাইপ (ঐচ্ছিক)
            </label>
            <input
              type="text"
              name="body_type"
              value={formData.body_type}
              onChange={handleInputChange}
              placeholder="যেমন: Sedan, SUV, Hatchback"
              className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-teal-500"
            />
          </div>
        )}
      </div>
    </div>
  )
}

  const renderStep3 = () => (
    <div className="bg-white rounded-2xl shadow-lg p-8">
      <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
        <Camera className="text-teal-600" size={28} />
        গাড়ির ছবি
      </h2>

      <div className="mb-6">
        <p className="text-sm text-gray-600 mb-2">প্রথম ছবিটি মূল ছবি হিসেবে প্রদর্শিত হবে</p>
      </div>
      
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        {imagePreviews.map((preview, index) => (
          <div key={index} className="relative group">
            <img
              src={preview}
              alt={`Preview ${index + 1}`}
              className={`w-full h-32 object-cover rounded-lg border-2 cursor-pointer ${
                mainImageIndex === index ? 'border-teal-500 ring-2 ring-teal-300' : 'border-gray-200'
              }`}
              onClick={() => setMainImage(index)}
            />
            {mainImageIndex === index && (
              <div className="absolute top-2 left-2 bg-teal-500 text-white text-xs px-2 py-1 rounded">
                মূল ছবি
              </div>
            )}
            <button
              type="button"
              onClick={() => removeImage(index)}
              className="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <X size={16} />
            </button>
          </div>
        ))}
        
        {images.length < 10 && (
          <label className="w-full h-32 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-teal-500 hover:bg-teal-50 transition-all">
            <Upload className="text-gray-400" size={32} />
            <span className="text-sm text-gray-500 mt-2">ছবি যোগ করুন</span>
            <input
              type="file"
              accept="image/*"
              multiple
              onChange={handleImageChange}
              className="hidden"
            />
          </label>
        )}
      </div>
      
      {errors.images && <p className="text-red-500 text-sm mt-2">{errors.images}</p>}
      <p className="text-sm text-gray-500">সর্বোচ্চ ১০টি ছবি আপলোড করতে পারবেন। মূল ছবি নির্বাচন করতে ছবিতে ক্লিক করুন।</p>
    </div>
  )

  const renderStep4 = () => (
    <div className="bg-white rounded-2xl shadow-lg p-8">
      <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
        <MapPin className="text-teal-600" size={28} />
        অবস্থান
      </h2>

      <div>
        <label className="block text-sm font-bold text-gray-700 mb-2">
          অবস্থান <span className="text-red-500">*</span>
        </label>
        <input
          type="text"
          name="location"
          value={formData.location}
          onChange={handleInputChange}
          placeholder="যেমন: ঢাকা, মিরপুর"
          className={`w-full px-4 py-3 border-2 rounded-lg focus:outline-none focus:border-teal-500 ${
            errors.location ? 'border-red-500' : 'border-gray-300'
          }`}
        />
        {errors.location && <p className="text-red-500 text-sm mt-1">{errors.location}</p>}
        <p className="text-xs text-gray-500 mt-2">এলাকা, থানা, জেলা উল্লেখ করুন</p>
      </div>

      {/* Interactive Map */}
      <div className="mt-6">
        <label className="block text-sm font-bold text-gray-700 mb-2">
          📍 মানচিত্রে আপনার দোকান/অবস্থান চিহ্নিত করুন (ঐচ্ছিক)
        </label>
        <div 
          ref={mapRef} 
          className="w-full h-80 rounded-lg border-2 border-gray-300 shadow-inner"
          style={{ zIndex: 1 }}
        ></div>
        <p className="text-xs text-gray-500 mt-2">
          💡 মানচিত্রে ক্লিক করে আপনার সঠিক অবস্থান নির্বাচন করুন
        </p>
        {markerPosition && (
          <div className="mt-2 p-3 bg-teal-50 border border-teal-200 rounded text-sm">
            ✓ অবস্থান নির্বাচিত: {markerPosition.lat.toFixed(4)}, {markerPosition.lng.toFixed(4)}
          </div>
        )}
      </div>

      {/* Summary */}
      <div className="mt-8 p-6 bg-gray-50 rounded-lg">
        <h3 className="font-bold text-lg mb-4">সারসংক্ষেপ</h3>
        <div className="space-y-2 text-sm">
          <p><span className="font-semibold">শিরোনাম:</span> {formData.title}</p>
          <p><span className="font-semibold">মূল্য:</span> ৳{formData.price}</p>
          <p><span className="font-semibold">মডেল:</span> {formData.model || 'N/A'}</p>
          <p><span className="font-semibold">বছর:</span> {formData.year_of_manufacture || 'N/A'}</p>
          <p><span className="font-semibold">অবস্থা:</span> {formData.condition || 'N/A'}</p>
          <p className={imagePreviews.length === 0 ? 'text-red-500 font-semibold' : ''}>
            <span className="font-semibold">ছবি:</span> {imagePreviews.length}টি
            {imagePreviews.length === 0 && ' (কমপক্ষে ১টি ছবি প্রয়োজন)'}
          </p>
        </div>
        {imagePreviews.length === 0 && (
          <div className="mt-4 p-3 bg-red-100 border border-red-300 rounded text-red-700 text-sm">
            ⚠️ দয়া করে ধাপ ৩ এ ফিরে গিয়ে ছবি আপলোড করুন
          </div>
        )}
      </div>
    </div>
  )

  return (
    <div className="min-h-screen bg-orange-50">
      {/* Header */}
      <div className="bg-teal-700 text-white py-8 shadow-lg">
        <div className="max-w-4xl mx-auto px-4">
          <button
            onClick={onBack}
            className="flex items-center gap-2 text-white hover:text-gray-200 mb-4 transition-colors"
          >
            <ArrowLeft size={20} />
            <span>ফিরে যান</span>
          </button>
          <h1 className="text-3xl md:text-4xl font-black">নতুন বিজ্ঞাপন দিন</h1>
          <p className="text-teal-100 mt-2">
            ধাপ {currentStep} / 4: {
              currentStep === 1 ? 'মূল তথ্য' :
              currentStep === 2 ? 'গাড়ির বিবরণ' :
              currentStep === 3 ? 'ছবি আপলোড' :
              'অবস্থান ও নিশ্চিতকরণ'
            }
          </p>
        </div>
      </div>

      {/* Form */}
      <div className="max-w-4xl mx-auto px-4 py-12">
        {renderStepIndicator()}

        <form onSubmit={handleSubmit}>
          {currentStep === 1 && renderStep1()}
          {currentStep === 2 && renderStep2()}
          {currentStep === 3 && renderStep3()}
          {currentStep === 4 && renderStep4()}

          {/* Navigation Buttons */}
          <div className="flex gap-4 mt-8">
            {currentStep > 1 && (
              <button
                type="button"
                onClick={prevStep}
                className="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-4 rounded-xl font-bold text-lg transition-colors flex items-center justify-center gap-2"
              >
                <ArrowLeft size={20} />
                পূর্ববর্তী
              </button>
            )}
            
            {currentStep < 4 ? (
              <button
                type="button"
                onClick={nextStep}
                className="flex-1 bg-rose-500 hover:bg-rose-600 text-white py-4 rounded-xl font-bold text-lg transition-colors flex items-center justify-center gap-2"
              >
                পরবর্তী
                <ArrowRight size={20} />
              </button>
            ) : (
              <button
                type="submit"
                disabled={loading}
                className="flex-1 bg-rose-500 hover:bg-rose-600 text-white py-4 rounded-xl font-bold text-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {loading ? 'জমা দেওয়া হচ্ছে...' : 'বিজ্ঞাপন পোস্ট করুন'}
              </button>
            )}
          </div>
        </form>
      </div>
    </div>
  )
}
