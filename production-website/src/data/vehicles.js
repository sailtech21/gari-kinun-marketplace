// Vehicle Listings Database
export const vehicles = [
  {
    id: 1,
    title: 'টয়োটা করোলা এক্স ২০২০',
    price: '৩৫,০০,০০০',
    priceRaw: 3500000,
    location: 'ধানমন্ডি, ঢাকা',
    year: '২০২০',
    fuel: 'পেট্রোল',
    type: 'সেডান',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৪৫,০০০ কিমি',
    color: 'সিলভার',
    transmission: 'অটোমেটিক',
    image: 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=500',
    category: 'cars',
    brand: 'টয়োটা',
    seller: {
      name: 'আহমেদ হোসেন',
      phone: '০১৭১২-৩৪৫৬৭৮',
      verified: true,
      memberSince: '২০২২'
    },
    description: 'সম্পূর্ণ অরিজিনাল কন্ডিশন। নিয়মিত সার্ভিসিং করা। কোনো দুর্ঘটনার ইতিহাস নেই।',
    views: 1245,
    featured: true
  },
  {
    id: 2,
    title: 'হন্ডা সিভিক ২০১৯',
    price: '২৮,৫০,০০০',
    priceRaw: 2850000,
    location: 'উত্তরা, ঢাকা',
    year: '২০১৯',
    fuel: 'পেট্রোল',
    type: 'সেডান',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৬২,০০০ কিমি',
    color: 'কালো',
    transmission: 'অটোমেটিক',
    image: 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=500',
    category: 'cars',
    brand: 'হন্ডা',
    seller: {
      name: 'রাকিব করিম',
      phone: '০১৮১২-৯৮৭৬৫৪',
      verified: true,
      memberSince: '২০২১'
    },
    description: 'ফ্রেশ কন্ডিশন। সব পেপার আপডেট। প্রথম মালিক থেকে কেনা।',
    views: 892,
    featured: true
  },
  {
    id: 3,
    title: 'নিসান এক্স-ট্রেইল ২০২১',
    price: '৪২,০০,০০০',
    priceRaw: 4200000,
    location: 'গুলশান, ঢাকা',
    year: '২০২১',
    fuel: 'হাইব্রিড',
    type: 'এসইউভি',
    tag: 'নতুন গাড়ি',
    condition: 'নতুন',
    mileage: '৮,০০০ কিমি',
    color: 'সাদা',
    transmission: 'সিভিটি',
    image: 'https://images.unsplash.com/photo-1619682817481-e994891cd1f5?w=500',
    category: 'cars',
    brand: 'নিসান',
    seller: {
      name: 'মোহাম্মদ শফিক',
      phone: '০১৯১৩-১১২২৩৩',
      verified: true,
      memberSince: '২০২৩'
    },
    description: 'শোরুম কন্ডিশন। সম্পূর্ণ অরিজিনাল। ফুল অপশন সহ।',
    views: 2341,
    featured: true
  },
  {
    id: 4,
    title: 'মিৎসুবিশি পাজেরো ২০২২',
    price: '৫৫,০০,০০০',
    priceRaw: 5500000,
    location: 'বনানী, ঢাকা',
    year: '২০২২',
    fuel: 'ডিজেল',
    type: 'এসইউভি',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '২২,০০০ কিমি',
    color: 'কালো',
    transmission: 'অটোমেটিক',
    image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500',
    category: 'cars',
    brand: 'মিৎসুবিশি',
    seller: {
      name: 'তানভীর হাসান',
      phone: '০১৭১৫-৪৪৫৫৬৬',
      verified: true,
      memberSince: '২০২০'
    },
    description: 'পারফেক্ট কন্ডিশন। ৪WD ড্রাইভ। লাক্সারি মডেল।',
    views: 1567,
    featured: true
  },
  {
    id: 5,
    title: 'টয়োটা হাইলাক্স ২০২১',
    price: '৪৮,০০,০০০',
    priceRaw: 4800000,
    location: 'মিরপুর, ঢাকা',
    year: '২০২১',
    fuel: 'ডিজেল',
    type: 'পিকআপ',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৩৫,০০০ কিমি',
    color: 'সিলভার',
    transmission: 'ম্যানুয়াল',
    image: 'https://images.unsplash.com/photo-1619682843185-39998f3c1cef?w=500',
    category: 'trucks',
    brand: 'টয়োটা',
    seller: {
      name: 'সাকিব আল হাসান',
      phone: '০১৬১৪-৭৭৮৮৯৯',
      verified: true,
      memberSince: '২০১৯'
    },
    description: 'কমার্শিয়াল ব্যবহারের জন্য পারফেক্ট। শক্তিশালী ইঞ্জিন।',
    views: 983,
    featured: false
  },
  {
    id: 6,
    title: 'হন্ডা CR-V ২০২০',
    price: '৩৮,৫০,০০০',
    priceRaw: 3850000,
    location: 'বসুন্ধরা, ঢাকা',
    year: '২০২০',
    fuel: 'পেট্রোল',
    type: 'এসইউভি',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৪৮,০০০ কিমি',
    color: 'লাল',
    transmission: 'অটোমেটিক',
    image: 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=500',
    category: 'cars',
    brand: 'হন্ডা',
    seller: {
      name: 'ফারহানা আক্তার',
      phone: '০১৮১৮-২২৩৩৪৪',
      verified: true,
      memberSince: '২০২২'
    },
    description: 'ফ্যামিলি কারের জন্য আদর্শ। স্পেসিয়াস এবং আরামদায়ক।',
    views: 756,
    featured: false
  },
  {
    id: 7,
    title: 'সুজুকি ভিটারা ২০১৯',
    price: '২২,০০,০০০',
    priceRaw: 2200000,
    location: 'মতিঝিল, ঢাকা',
    year: '২০১৯',
    fuel: 'পেট্রোল',
    type: 'এসইউভি',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৭০,০০০ কিমি',
    color: 'নীল',
    transmission: 'অটোমেটিক',
    image: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=500',
    category: 'cars',
    brand: 'সুজুকি',
    seller: {
      name: 'নাজমুল হক',
      phone: '০১৯১৯-৫৫৬৬৭৭',
      verified: false,
      memberSince: '২০২৩'
    },
    description: 'ভালো কন্ডিশন। ইকোনমিক্যাল গাড়ি। কম খরচে রক্ষণাবেক্ষণ।',
    views: 542,
    featured: false
  },
  {
    id: 8,
    title: 'টয়োটা প্রিমিও ২০১৮',
    price: '২৬,০০,০০০',
    priceRaw: 2600000,
    location: 'চট্টগ্রাম',
    year: '২০১৮',
    fuel: 'পেট্রোল',
    type: 'সেডান',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৮৫,০০০ কিমি',
    color: 'সাদা',
    transmission: 'অটোমেটিক',
    image: 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=500',
    category: 'cars',
    brand: 'টয়োটা',
    seller: {
      name: 'রফিকুল ইসলাম',
      phone: '০১৮১৩-৯৯৮৮৭৭',
      verified: true,
      memberSince: '২০২১'
    },
    description: 'ঢাকা নম্বর। ফুল ট্যাক্স টোকেন পেইড। ফ্রেশ কন্ডিশন।',
    views: 678,
    featured: false
  },
  {
    id: 9,
    title: 'হন্ডা ফিট হাইব্রিড ২০১৭',
    price: '১৮,৫০,০০০',
    priceRaw: 1850000,
    location: 'সিলেট',
    year: '২০১৭',
    fuel: 'হাইব্রিড',
    type: 'হ্যাচব্যাক',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৯৫,০০০ কিমি',
    color: 'ধূসর',
    transmission: 'সিভিটি',
    image: 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=500',
    category: 'cars',
    brand: 'হন্ডা',
    seller: {
      name: 'কামাল উদ্দিন',
      phone: '০১৭১৬-১২৩৪৫৬',
      verified: true,
      memberSince: '২০২০'
    },
    description: 'ফুয়েল ইফিশিয়েন্ট। সিটি ড্রাইভিংয়ের জন্য পারফেক্ট।',
    views: 423,
    featured: false
  },
  {
    id: 10,
    title: 'ম্যাজদা CX-5 ২০২০',
    price: '৩৯,০০,০০০',
    priceRaw: 3900000,
    location: 'খুলনা',
    year: '২০২০',
    fuel: 'পেট্রোল',
    type: 'এসইউভি',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৩৮,০০০ কিমি',
    color: 'মেরুন',
    transmission: 'অটোমেটিক',
    image: 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=500',
    category: 'cars',
    brand: 'ম্যাজদা',
    seller: {
      name: 'আবদুল মালেক',
      phone: '০১৯১২-৬৬৭৭৮৮',
      verified: true,
      memberSince: '২০২২'
    },
    description: 'প্রিমিয়াম ফিচার সহ। স্পোর্টি লুক। পাওয়ারফুল পারফরমেন্স।',
    views: 834,
    featured: false
  },
  {
    id: 11,
    title: 'মিৎসুবিশি আউটল্যান্ডার ২০১৯',
    price: '৩৫,৫০,০০০',
    priceRaw: 3550000,
    location: 'রাজশাহী',
    year: '২০১৯',
    fuel: 'পেট্রোল',
    type: 'এসইউভি',
    tag: 'রেজিস্টার্ড',
    condition: 'ব্যবহৃত',
    mileage: '৫৫,০০০ কিমি',
    color: 'সাদা',
    transmission: 'সিভিটি',
    image: 'https://images.unsplash.com/photo-1581540222194-0def2dda95b8?w=500',
    category: 'cars',
    brand: 'মিৎসুবিশি',
    seller: {
      name: 'হাসান মাহমুদ',
      phone: '০১৮১৫-৩৩৪৪৫৫',
      verified: true,
      memberSince: '২০২১'
    },
    description: '৭ সিটার। ফ্যামিলি এসইউভি। কমফোর্টেবল রাইড।',
    views: 612,
    featured: false
  },
  {
    id: 12,
    title: 'টয়োটা এক্সিয়া ২০২১',
    price: '৩২,০০,০০০',
    priceRaw: 3200000,
    location: 'বরিশাল',
    year: '২০২১',
    fuel: 'পেট্রোল',
    type: 'সেডান',
    tag: 'নতুন গাড়ি',
    condition: 'নতুন',
    mileage: '১২,০০০ কিমি',
    color: 'সিলভার',
    transmission: 'সিভিটি',
    image: 'https://images.unsplash.com/photo-1617469767053-d3b523a0b982?w=500',
    category: 'cars',
    brand: 'টয়োটা',
    seller: {
      name: 'সাইফুল ইসলাম',
      phone: '০১৭১৭-৮৮৯৯০০',
      verified: true,
      memberSince: '২০২৩'
    },
    description: 'প্র্যাকটিক্যালি নতুন। ওয়ারেন্টি বিদ্যমান। মডার্ন ডিজাইন।',
    views: 1123,
    featured: true
  }
]

// Helper functions
export const getAllVehicles = () => vehicles

export const getFeaturedVehicles = () => vehicles.filter(v => v.featured)

export const getVehicleById = (id) => vehicles.find(v => v.id === id)

export const getVehiclesByCategory = (category) => vehicles.filter(v => v.category === category)

export const getTrendingVehicles = () => [...vehicles].sort((a, b) => b.views - a.views).slice(0, 6)

export const getLatestVehicles = () => [...vehicles].reverse().slice(0, 8)

export const searchVehicles = (query, filters = {}) => {
  let results = [...vehicles]
  
  // Text search
  if (query) {
    const lowerQuery = query.toLowerCase()
    results = results.filter(v => 
      v.title.toLowerCase().includes(lowerQuery) ||
      v.brand.toLowerCase().includes(lowerQuery) ||
      v.type.toLowerCase().includes(lowerQuery) ||
      v.description.toLowerCase().includes(lowerQuery)
    )
  }
  
  // Category filter
  if (filters.category) {
    results = results.filter(v => v.category === filters.category)
  }
  
  // Location filter
  if (filters.location) {
    results = results.filter(v => v.location.includes(filters.location))
  }
  
  // Price range filter
  if (filters.minPrice) {
    results = results.filter(v => v.priceRaw >= filters.minPrice)
  }
  if (filters.maxPrice) {
    results = results.filter(v => v.priceRaw <= filters.maxPrice)
  }
  
  // Fuel type filter
  if (filters.fuel) {
    results = results.filter(v => v.fuel === filters.fuel)
  }
  
  // Condition filter
  if (filters.condition) {
    results = results.filter(v => v.condition === filters.condition)
  }
  
  return results
}
