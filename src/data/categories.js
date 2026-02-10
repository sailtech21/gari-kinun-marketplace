// Categories Database
export const categories = [
  {
    id: 1,
    name: 'গাড়ি',
    slug: 'cars',
    icon: 'Car',
    color: 'bg-blue-100 text-blue-600',
    count: 1245,
    description: 'প্রাইভেট কার, সেডান, হ্যাচব্যাক',
    subcategories: [
      { id: 11, name: 'সেডান', slug: 'sedan', count: 456 },
      { id: 12, name: 'হ্যাচব্যাক', slug: 'hatchback', count: 234 },
      { id: 13, name: 'এসইউভি', slug: 'suv', count: 389 },
      { id: 14, name: 'ক্রসওভার', slug: 'crossover', count: 166 }
    ]
  },
  {
    id: 2,
    name: 'মোটরসাইকেল',
    slug: 'motorcycles',
    icon: 'Bike',
    color: 'bg-purple-100 text-purple-600',
    count: 892,
    description: 'স্পোর্টস বাইক, স্কুটার, কমিউটার',
    subcategories: [
      { id: 21, name: 'স্পোর্টস বাইক', slug: 'sports-bike', count: 234 },
      { id: 22, name: 'স্ট্যান্ডার্ড', slug: 'standard', count: 389 },
      { id: 23, name: 'স্কুটার', slug: 'scooter', count: 156 },
      { id: 24, name: 'ক্রুজার', slug: 'cruiser', count: 113 }
    ]
  },
  {
    id: 3,
    name: 'রিকশা',
    slug: 'rickshaw',
    icon: 'TramFront',
    color: 'bg-green-100 text-green-600',
    count: 324,
    description: 'ইজিবাইক, প্যাডেল রিকশা, ভ্যান',
    subcategories: [
      { id: 31, name: 'ইজিবাইক', slug: 'easy-bike', count: 178 },
      { id: 32, name: 'প্যাডেল রিকশা', slug: 'pedal-rickshaw', count: 89 },
      { id: 33, name: 'ভ্যান', slug: 'van', count: 57 }
    ]
  },
  {
    id: 4,
    name: 'সিএনজি',
    slug: 'cng',
    icon: 'CircleDot',
    color: 'bg-yellow-100 text-yellow-600',
    count: 178,
    description: 'অটো রিকশা, সিএনজি ট্যাক্সি',
    subcategories: [
      { id: 41, name: 'পাবলিক সিএনজি', slug: 'public-cng', count: 98 },
      { id: 42, name: 'প্রাইভেট সিএনজি', slug: 'private-cng', count: 80 }
    ]
  },
  {
    id: 5,
    name: 'বাস',
    slug: 'bus',
    icon: 'Bus',
    color: 'bg-red-100 text-red-600',
    count: 89,
    description: 'পাবলিক বাস, মিনিবাস, কোস্টার',
    subcategories: [
      { id: 51, name: 'পাবলিক বাস', slug: 'public-bus', count: 34 },
      { id: 52, name: 'মিনিবাস', slug: 'minibus', count: 28 },
      { id: 53, name: 'কোস্টার', slug: 'coaster', count: 19 },
      { id: 54, name: 'ডাবল ডেকার', slug: 'double-decker', count: 8 }
    ]
  },
  {
    id: 6,
    name: 'ট্রাক/পিকআপ',
    slug: 'trucks',
    icon: 'Truck',
    color: 'bg-orange-100 text-orange-600',
    count: 234,
    description: 'কভার ভ্যান, পিকআপ, ট্রাক',
    subcategories: [
      { id: 61, name: 'পিকআপ', slug: 'pickup', count: 89 },
      { id: 62, name: 'কভার ভ্যান', slug: 'covered-van', count: 76 },
      { id: 63, name: 'ট্রাক', slug: 'truck', count: 45 },
      { id: 64, name: 'ডাম্প ট্রাক', slug: 'dump-truck', count: 24 }
    ]
  },
  {
    id: 7,
    name: 'নৌযান',
    slug: 'boats',
    icon: 'Ship',
    color: 'bg-cyan-100 text-cyan-600',
    count: 45,
    description: 'স্পিডবোট, ট্রলার, ইঞ্জিন বোট',
    subcategories: [
      { id: 71, name: 'স্পিডবোট', slug: 'speedboat', count: 18 },
      { id: 72, name: 'ট্রলার', slug: 'trawler', count: 15 },
      { id: 73, name: 'ইঞ্জিন বোট', slug: 'engine-boat', count: 12 }
    ]
  },
  {
    id: 8,
    name: 'পার্টস',
    slug: 'parts',
    icon: 'Wrench',
    color: 'bg-gray-100 text-gray-600',
    count: 567,
    description: 'ইঞ্জিন, টায়ার, অন্যান্য যন্ত্রাংশ',
    subcategories: [
      { id: 81, name: 'ইঞ্জিন পার্টস', slug: 'engine-parts', count: 189 },
      { id: 82, name: 'বডি পার্টস', slug: 'body-parts', count: 156 },
      { id: 83, name: 'টায়ার', slug: 'tires', count: 134 },
      { id: 84, name: 'ব্যাটারি', slug: 'battery', count: 88 }
    ]
  }
]

// Helper functions
export const getAllCategories = () => categories

export const getCategoryBySlug = (slug) => categories.find(c => c.slug === slug)

export const getCategoryById = (id) => categories.find(c => c.id === id)

export const getMainCategories = () => categories

export const getCategoryWithSubcategories = (slug) => {
  const category = categories.find(c => c.slug === slug)
  return category || null
}
