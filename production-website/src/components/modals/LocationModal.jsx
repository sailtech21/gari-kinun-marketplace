import { X, MapPin, Search, Navigation, ChevronRight } from 'lucide-react'
import { useState, useEffect } from 'react'

// Bangladesh Divisions and Districts
const DIVISIONS = [
  {
    id: 1,
    nameEn: 'Dhaka',
    nameBn: 'ঢাকা',
    districts: [
      { nameEn: 'Dhaka', nameBn: 'ঢাকা' },
      { nameEn: 'Gazipur', nameBn: 'গাজীপুর' },
      { nameEn: 'Narayanganj', nameBn: 'নারায়ণগঞ্জ' },
      { nameEn: 'Narsingdi', nameBn: 'নরসিংদী' },
      { nameEn: 'Tangail', nameBn: 'টাঙ্গাইল' },
      { nameEn: 'Kishoreganj', nameBn: 'কিশোরগঞ্জ' },
      { nameEn: 'Manikganj', nameBn: 'মানিকগঞ্জ' },
      { nameEn: 'Munshiganj', nameBn: 'মুন্সিগঞ্জ' },
      { nameEn: 'Rajbari', nameBn: 'রাজবাড়ী' },
      { nameEn: 'Shariatpur', nameBn: 'শরীয়তপুর' },
      { nameEn: 'Faridpur', nameBn: 'ফরিদপুর' },
      { nameEn: 'Gopalganj', nameBn: 'গোপালগঞ্জ' },
      { nameEn: 'Madaripur', nameBn: 'মাদারীপুর' }
    ]
  },
  {
    id: 2,
    nameEn: 'Chittagong',
    nameBn: 'চট্টগ্রাম',
    districts: [
      { nameEn: 'Chittagong', nameBn: 'চট্টগ্রাম' },
      { nameEn: 'Coxs Bazar', nameBn: 'কক্সবাজার' },
      { nameEn: 'Rangamati', nameBn: 'রাঙ্গামাটি' },
      { nameEn: 'Bandarban', nameBn: 'বান্দরবান' },
      { nameEn: 'Khagrachari', nameBn: 'খাগড়াছড়ি' },
      { nameEn: 'Feni', nameBn: 'ফেনী' },
      { nameEn: 'Lakshmipur', nameBn: 'লক্ষ্মীপুর' },
      { nameEn: 'Comilla', nameBn: 'কুমিল্লা' },
      { nameEn: 'Noakhali', nameBn: 'নোয়াখালী' },
      { nameEn: 'Brahmanbaria', nameBn: 'ব্রাহ্মণবাড়িয়া' },
      { nameEn: 'Chandpur', nameBn: 'চাঁদপুর' }
    ]
  },
  {
    id: 3,
    nameEn: 'Rajshahi',
    nameBn: 'রাজশাহী',
    districts: [
      { nameEn: 'Rajshahi', nameBn: 'রাজশাহী' },
      { nameEn: 'Natore', nameBn: 'নাটোর' },
      { nameEn: 'Naogaon', nameBn: 'নওগাঁ' },
      { nameEn: 'Pabna', nameBn: 'পাবনা' },
      { nameEn: 'Bogra', nameBn: 'বগুড়া' },
      { nameEn: 'Sirajganj', nameBn: 'সিরাজগঞ্জ' },
      { nameEn: 'Chapainawabganj', nameBn: 'চাঁপাইনবাবগঞ্জ' },
      { nameEn: 'Joypurhat', nameBn: 'জয়পুরহাট' }
    ]
  },
  {
    id: 4,
    nameEn: 'Khulna',
    nameBn: 'খুলনা',
    districts: [
      { nameEn: 'Khulna', nameBn: 'খুলনা' },
      { nameEn: 'Jessore', nameBn: 'যশোর' },
      { nameEn: 'Satkhira', nameBn: 'সাতক্ষীরা' },
      { nameEn: 'Bagerhat', nameBn: 'বাগেরহাট' },
      { nameEn: 'Jhenaidah', nameBn: 'ঝিনাইদহ' },
      { nameEn: 'Magura', nameBn: 'মাগুরা' },
      { nameEn: 'Narail', nameBn: 'নড়াইল' },
      { nameEn: 'Chuadanga', nameBn: 'চুয়াডাঙ্গা' },
      { nameEn: 'Kushtia', nameBn: 'কুষ্টিয়া' },
      { nameEn: 'Meherpur', nameBn: 'মেহেরপুর' }
    ]
  },
  {
    id: 5,
    nameEn: 'Sylhet',
    nameBn: 'সিলেট',
    districts: [
      { nameEn: 'Sylhet', nameBn: 'সিলেট' },
      { nameEn: 'Moulvibazar', nameBn: 'মৌলভীবাজার' },
      { nameEn: 'Habiganj', nameBn: 'হবিগঞ্জ' },
      { nameEn: 'Sunamganj', nameBn: 'সুনামগঞ্জ' }
    ]
  },
  {
    id: 6,
    nameEn: 'Barisal',
    nameBn: 'বরিশাল',
    districts: [
      { nameEn: 'Barisal', nameBn: 'বরিশাল' },
      { nameEn: 'Patuakhali', nameBn: 'পটুয়াখালী' },
      { nameEn: 'Bhola', nameBn: 'ভোলা' },
      { nameEn: 'Pirojpur', nameBn: 'পিরোজপুর' },
      { nameEn: 'Jhalokati', nameBn: 'ঝালকাঠি' },
      { nameEn: 'Barguna', nameBn: 'বরগুনা' }
    ]
  },
  {
    id: 7,
    nameEn: 'Rangpur',
    nameBn: 'রংপুর',
    districts: [
      { nameEn: 'Rangpur', nameBn: 'রংপুর' },
      { nameEn: 'Dinajpur', nameBn: 'দিনাজপুর' },
      { nameEn: 'Gaibandha', nameBn: 'গাইবান্ধা' },
      { nameEn: 'Kurigram', nameBn: 'কুড়িগ্রাম' },
      { nameEn: 'Lalmonirhat', nameBn: 'লালমনিরহাট' },
      { nameEn: 'Nilphamari', nameBn: 'নীলফামারী' },
      { nameEn: 'Panchagarh', nameBn: 'পঞ্চগড়' },
      { nameEn: 'Thakurgaon', nameBn: 'ঠাকুরগাঁও' }
    ]
  },
  {
    id: 8,
    nameEn: 'Mymensingh',
    nameBn: 'ময়মনসিংহ',
    districts: [
      { nameEn: 'Mymensingh', nameBn: 'ময়মনসিংহ' },
      { nameEn: 'Jamalpur', nameBn: 'জামালপুর' },
      { nameEn: 'Netrokona', nameBn: 'নেত্রকোনা' },
      { nameEn: 'Sherpur', nameBn: 'শেরপুর' }
    ]
  }
]

export default function LocationModal({ isOpen, onClose, onSelectLocation, currentLocation }) {
  const [step, setStep] = useState(1) // 1: Division, 2: District
  const [selectedDivision, setSelectedDivision] = useState(null)
  const [searchQuery, setSearchQuery] = useState('')

  useEffect(() => {
    if (isOpen) {
      setStep(1)
      setSelectedDivision(null)
      setSearchQuery('')
    }
  }, [isOpen])

  if (!isOpen) return null

  const handleDivisionSelect = (division) => {
    setSelectedDivision(division)
    setStep(2)
  }

  const handleDistrictSelect = (district) => {
    onSelectLocation({
      division: selectedDivision.nameBn,
      district: district.nameBn,
      displayText: `${district.nameBn}, ${selectedDivision.nameBn}`
    })
    onClose()
  }

  const handleAllBangladesh = () => {
    onSelectLocation({
      division: null,
      district: null,
      displayText: 'সমগ্র বাংলাদেশ'
    })
    onClose()
  }

  const handleUseCurrentLocation = () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          // In a real app, you'd reverse geocode these coordinates
          // For now, default to Dhaka
          onSelectLocation({
            division: 'ঢাকা',
            district: 'ঢাকা',
            displayText: 'ঢাকা, ঢাকা (আপনার অবস্থান)'
          })
          onClose()
        },
        (error) => {
          console.error('Location error:', error)
          alert('আপনার অবস্থান খুঁজে পাওয়া যায়নি। অনুগ্রহ করে ম্যানুয়াল নির্বাচন করুন।')
        }
      )
    } else {
      alert('আপনার ব্রাউজার লোকেশন সাপোর্ট করে না।')
    }
  }

  const filteredDivisions = DIVISIONS.filter(div =>
    div.nameBn.includes(searchQuery) || div.nameEn.toLowerCase().includes(searchQuery.toLowerCase())
  )

  const filteredDistricts = selectedDivision
    ? selectedDivision.districts.filter(dist =>
        dist.nameBn.includes(searchQuery) || dist.nameEn.toLowerCase().includes(searchQuery.toLowerCase())
      )
    : []

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fadeIn">
      <div className="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-slideUp">
        {/* Header */}
        <div className="bg-gradient-to-r from-teal-600 via-teal-700 to-teal-800 px-6 py-5 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
              <MapPin size={24} className="text-white" />
            </div>
            <div>
              <h2 className="text-2xl font-bold text-white">
                {step === 1 ? 'বিভাগ নির্বাচন করুন' : 'জেলা নির্বাচন করুন'}
              </h2>
              <p className="text-teal-50 text-sm">
                {step === 1 ? 'ধাপ ১ / ২' : 'ধাপ ২ / ২'}
              </p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="w-10 h-10 rounded-xl bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all backdrop-blur-sm"
          >
            <X size={24} className="text-white" />
          </button>
        </div>

        {/* Search Bar */}
        <div className="p-6 border-b border-gray-200">
          <div className="relative">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" size={20} />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder={step === 1 ? 'বিভাগ খুঁজুন...' : 'জেলা খুঁজুন...'}
              className="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all text-base"
              autoFocus
            />
          </div>
        </div>

        {/* Top Actions */}
        <div className="px-6 py-4 bg-gray-50 border-b border-gray-200">
          <div className="flex flex-wrap gap-3">
            <button
              onClick={handleAllBangladesh}
              className="flex items-center gap-2 px-4 py-2 bg-white border-2 border-teal-500 text-teal-700 rounded-xl font-bold hover:bg-teal-50 transition-all shadow-sm"
            >
              <MapPin size={18} />
              <span>সমগ্র বাংলাদেশ</span>
            </button>
            <button
              onClick={handleUseCurrentLocation}
              className="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-bold hover:from-blue-600 hover:to-blue-700 transition-all shadow-md"
            >
              <Navigation size={18} />
              <span>আপনার অবস্থান</span>
            </button>
            {step === 2 && (
              <button
                onClick={() => {
                  setStep(1)
                  setSelectedDivision(null)
                  setSearchQuery('')
                }}
                className="flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all shadow-sm"
              >
                ← পিছনে যান
              </button>
            )}
          </div>
        </div>

        {/* Content */}
        <div className="p-6 overflow-y-auto max-h-96">
          {step === 1 ? (
            // Division Selection
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
              {filteredDivisions.map(division => (
                <button
                  key={division.id}
                  onClick={() => handleDivisionSelect(division)}
                  className="group relative bg-gradient-to-br from-gray-50 to-gray-100 hover:from-teal-50 hover:to-teal-100 border-2 border-gray-200 hover:border-teal-500 rounded-xl p-4 transition-all shadow-sm hover:shadow-lg text-left"
                >
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                      <div className="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <MapPin size={24} className="text-white" />
                      </div>
                      <div>
                        <h3 className="text-xl font-bold text-gray-900">{division.nameBn}</h3>
                        <p className="text-sm text-gray-600">{division.districts.length} টি জেলা</p>
                      </div>
                    </div>
                    <ChevronRight size={24} className="text-gray-400 group-hover:text-teal-600 group-hover:translate-x-1 transition-all" />
                  </div>
                </button>
              ))}
            </div>
          ) : (
            // District Selection
            <div>
              {selectedDivision && (
                <div className="mb-4 p-4 bg-gradient-to-r from-teal-50 to-teal-100 rounded-xl border-2 border-teal-200">
                  <p className="text-sm text-teal-700 font-semibold">নির্বাচিত বিভাগ:</p>
                  <h3 className="text-xl font-bold text-teal-900">{selectedDivision.nameBn}</h3>
                </div>
              )}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {filteredDistricts.map((district, index) => (
                  <button
                    key={index}
                    onClick={() => handleDistrictSelect(district)}
                    className="group bg-gradient-to-br from-gray-50 to-gray-100 hover:from-teal-50 hover:to-teal-100 border-2 border-gray-200 hover:border-teal-500 rounded-xl p-4 transition-all shadow-sm hover:shadow-lg text-left"
                  >
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <MapPin size={18} className="text-white" />
                      </div>
                      <div>
                        <h3 className="text-lg font-bold text-gray-900">{district.nameBn}</h3>
                        <p className="text-xs text-gray-600">{district.nameEn}</p>
                      </div>
                    </div>
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* No Results */}
          {((step === 1 && filteredDivisions.length === 0) || (step === 2 && filteredDistricts.length === 0)) && (
            <div className="text-center py-12">
              <div className="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <Search size={40} className="text-gray-400" />
              </div>
              <h3 className="text-xl font-bold text-gray-900 mb-2">কিছু পাওয়া যায়নি</h3>
              <p className="text-gray-600">অনুগ্রহ করে অন্য নাম দিয়ে খুঁজুন</p>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
