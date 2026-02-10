// Other supporting data

// Testimonials
export const testimonials = [
  {
    id: 1,
    name: 'মাহমুদ হাসান',
    location: 'ঢাকা',
    rating: 5,
    comment: 'অসাধারণ সেবা! খুব সহজে গাড়ি বিক্রি করতে পেরেছি। সবাইকে রেকমেন্ড করব।',
    date: '২০২৪-০১-১৫',
    avatar: 'https://i.pravatar.cc/150?img=12'
  },
  {
    id: 2,
    name: 'নুসরাত জাহান',
    location: 'চট্টগ্রাম',
    rating: 5,
    comment: 'বিশ্বস্ত প্ল্যাটফর্ম। আমার স্বপ্নের গাড়ি এখানে থেকে কিনেছি। ধন্যবাদ!',
    date: '২০২৪-০১-২০',
    avatar: 'https://i.pravatar.cc/150?img=47'
  },
  {
    id: 3,
    name: 'রাকিব আহমেদ',
    location: 'সিলেট',
    rating: 4,
    comment: 'ভালো দাম পেয়েছি। দ্রুত বিক্রয় হয়েছে। সার্ভিস চমৎকার।',
    date: '২০২৪-০২-০১',
    avatar: 'https://i.pravatar.cc/150?img=33'
  }
]

// Popular Brands
export const brands = [
  { id: 1, name: 'টয়োটা', nameEn: 'Toyota', count: 456, logo: '🚗' },
  { id: 2, name: 'হন্ডা', nameEn: 'Honda', count: 389, logo: '🚙' },
  { id: 3, name: 'নিসান', nameEn: 'Nissan', count: 234, logo: '🚕' },
  { id: 4, name: 'মিৎসুবিশি', nameEn: 'Mitsubishi', count: 178, logo: '🚐' },
  { id: 5, name: 'সুজুকি', nameEn: 'Suzuki', count: 156, logo: '🚗' },
  { id: 6, name: 'ম্যাজদা', nameEn: 'Mazda', count: 98, logo: '🚙' },
  { id: 7, name: 'হুন্ডাই', nameEn: 'Hyundai', count: 87, logo: '🚕' },
  { id: 8, name: 'অন্যান্য', nameEn: 'Others', count: 234, logo: '🚐' }
]

// Stats
export const stats = [
  {
    id: 1,
    value: '৬৪ জেলা',
    label: 'সারা বাংলাদেশে',
    description: 'সব জেলায় পৌঁছান',
    icon: 'MapPinned',
    color: 'bg-blue-100 text-blue-600'
  },
  {
    id: 2,
    value: '১০,০০০+',
    label: 'সক্রিয় ক্রেতা',
    description: 'বিশ্বস্ত কমিউনিটি',
    icon: 'Users',
    color: 'bg-green-100 text-green-600'
  },
  {
    id: 3,
    value: '১০০%',
    label: 'নিরাপদ লেনদেন',
    description: 'সুরক্ষিত পেমেন্ট',
    icon: 'ShieldCheck',
    color: 'bg-purple-100 text-purple-600'
  },
  {
    id: 4,
    value: '১,৫০০+',
    label: 'যাচাইকৃত বিক্রেতা',
    description: 'ভেরিফাইড সেলার',
    icon: 'BadgeCheck',
    color: 'bg-amber-100 text-amber-600'
  }
]

// Fuel Types
export const fuelTypes = [
  { id: 1, name: 'পেট্রোল', nameEn: 'Petrol' },
  { id: 2, name: 'ডিজেল', nameEn: 'Diesel' },
  { id: 3, name: 'অকটেন', nameEn: 'Octane' },
  { id: 4, name: 'হাইব্রিড', nameEn: 'Hybrid' },
  { id: 5, name: 'ইলেকট্রিক', nameEn: 'Electric' },
  { id: 6, name: 'সিএনজি', nameEn: 'CNG' }
]

// Transmission Types
export const transmissionTypes = [
  { id: 1, name: 'ম্যানুয়াল', nameEn: 'Manual' },
  { id: 2, name: 'অটোমেটিক', nameEn: 'Automatic' },
  { id: 3, name: 'সিভিটি', nameEn: 'CVT' },
  { id: 4, name: 'সেমি-অটো', nameEn: 'Semi-Auto' }
]

// Condition Types
export const conditionTypes = [
  { id: 1, name: 'নতুন', nameEn: 'New' },
  { id: 2, name: 'ব্যবহৃত', nameEn: 'Used' },
  { id: 3, name: 'রিকন্ডিশন', nameEn: 'Reconditioned' }
]

export const getTestimonials = () => testimonials
export const getBrands = () => brands
export const getStats = () => stats
export const getFuelTypes = () => fuelTypes
export const getTransmissionTypes = () => transmissionTypes
export const getConditionTypes = () => conditionTypes
