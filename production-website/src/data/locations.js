// Locations Database (Bangladesh Cities & Districts)
export const locations = [
  { 
    id: 1, 
    name: 'ঢাকা', 
    nameEn: 'Dhaka',
    count: 2341,
    division: 'ঢাকা বিভাগ',
    popular: true
  },
  { 
    id: 2, 
    name: 'চট্টগ্রাম', 
    nameEn: 'Chittagong',
    count: 892,
    division: 'চট্টগ্রাম বিভাগ',
    popular: true
  },
  { 
    id: 3, 
    name: 'সিলেট', 
    nameEn: 'Sylhet',
    count: 456,
    division: 'সিলেট বিভাগ',
    popular: true
  },
  { 
    id: 4, 
    name: 'রাজশাহী', 
    nameEn: 'Rajshahi',
    count: 378,
    division: 'রাজশাহী বিভাগ',
    popular: true
  },
  { 
    id: 5, 
    name: 'খুলনা', 
    nameEn: 'Khulna',
    count: 289,
    division: 'খুলনা বিভাগ',
    popular: true
  },
  { 
    id: 6, 
    name: 'বরিশাল', 
    nameEn: 'Barisal',
    count: 234,
    division: 'বরিশাল বিভাগ',
    popular: true
  },
  { 
    id: 7, 
    name: 'রংপুর', 
    nameEn: 'Rangpur',
    count: 212,
    division: 'রংপুর বিভাগ',
    popular: false
  },
  { 
    id: 8, 
    name: 'ময়মনসিংহ', 
    nameEn: 'Mymensingh',
    count: 198,
    division: 'ময়মনসিংহ বিভাগ',
    popular: false
  },
  { 
    id: 9, 
    name: 'কুমিল্লা', 
    nameEn: 'Cumilla',
    count: 167,
    division: 'চট্টগ্রাম বিভাগ',
    popular: false
  },
  { 
    id: 10, 
    name: 'গাজীপুর', 
    nameEn: 'Gazipur',
    count: 145,
    division: 'ঢাকা বিভাগ',
    popular: false
  },
  { 
    id: 11, 
    name: 'নারায়ণগঞ্জ', 
    nameEn: 'Narayanganj',
    count: 134,
    division: 'ঢাকা বিভাগ',
    popular: false
  },
  { 
    id: 12, 
    name: 'বগুড়া', 
    nameEn: 'Bogra',
    count: 89,
    division: 'রাজশাহী বিভাগ',
    popular: false
  },
  { 
    id: 13, 
    name: 'যশোর', 
    nameEn: 'Jessore',
    count: 78,
    division: 'খুলনা বিভাগ',
    popular: false
  },
  { 
    id: 14, 
    name: 'দিনাজপুর', 
    nameEn: 'Dinajpur',
    count: 67,
    division: 'রংপুর বিভাগ',
    popular: false
  },
  { 
    id: 15, 
    name: 'পাবনা', 
    nameEn: 'Pabna',
    count: 56,
    division: 'রাজশাহী বিভাগ',
    popular: false
  }
]

// Dhaka Areas (for more specific location)
export const dhakaAreas = [
  { id: 101, name: 'ধানমন্ডি', nameEn: 'Dhanmondi', count: 234 },
  { id: 102, name: 'উত্তরা', nameEn: 'Uttara', count: 289 },
  { id: 103, name: 'গুলশান', nameEn: 'Gulshan', count: 198 },
  { id: 104, name: 'বনানী', nameEn: 'Banani', count: 167 },
  { id: 105, name: 'মিরপুর', nameEn: 'Mirpur', count: 312 },
  { id: 106, name: 'বসুন্ধরা', nameEn: 'Bashundhara', count: 145 },
  { id: 107, name: 'মতিঝিল', nameEn: 'Motijheel', count: 89 },
  { id: 108, name: 'মোহাম্মদপুর', nameEn: 'Mohammadpur', count: 134 },
  { id: 109, name: 'শ্যামলী', nameEn: 'Shyamoli', count: 78 },
  { id: 110, name: 'ফার্মগেট', nameEn: 'Farmgate', count: 92 }
]

// Helper functions
export const getAllLocations = () => locations

export const getPopularLocations = () => locations.filter(l => l.popular)

export const getLocationById = (id) => locations.find(l => l.id === id)

export const getLocationByName = (name) => locations.find(l => l.name === name || l.nameEn.toLowerCase() === name.toLowerCase())

export const getDhakaAreas = () => dhakaAreas
