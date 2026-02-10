# গাড়ি কিনুন - Component Architecture & Documentation

## 🎯 Project Overview

Professional Bangladesh vehicle marketplace website with:
- ✅ Modular component architecture
- ✅ Reusable UI components
- ✅ Organized dummy data layer
- ✅ Auth UI (Login/Register)
- ✅ SEO optimization
- ✅ App download section
- ✅ Frontend search & filter logic
- ✅ Deep link ready URLs
- ✅ Fully responsive design

---

## 📁 Project Structure

```
website/
├── public/
│   └── (static assets)
├── src/
│   ├── components/
│   │   ├── common/           # Reusable components
│   │   │   ├── Button.jsx    
│   │   │   ├── SearchBar.jsx
│   │   │   ├── CategoryCard.jsx
│   │   │   ├── VehicleCard.jsx
│   │   │   └── StatsCard.jsx
│   │   │
│   │   ├── auth/             # Authentication UI
│   │   │   ├── LoginModal.jsx
│   │   │   └── RegisterModal.jsx
│   │   │
│   │   ├── sections/         # Page sections
│   │   │   └── DownloadApp.jsx
│   │   │
│   │   ├── layout/           # Layout components
│   │   │   ├── Header.jsx
│   │   │   └── Footer.jsx
│   │   │
│   │   ├── Hero.jsx          # Home sections
│   │   ├── PopularCategories.jsx
│   │   ├── FeaturedListings.jsx
│   │   ├── TrendingListings.jsx
│   │   ├── PopularBrands.jsx
│   │   ├── TrustStats.jsx
│   │   ├── Testimonials.jsx
│   │   └── SEO.jsx           # SEO component
│   │
│   ├── data/                 # Data layer
│   │   ├── vehicles.js       # 12 vehicle listings + helpers
│   │   ├── categories.js     # 8 categories with subcategories
│   │   ├── locations.js      # Bangladesh locations
│   │   ├── other.js          # Testimonials, brands, stats
│   │   ├── dummyData.js      # Original (can be removed)
│   │   └── README.md
│   │
│   ├── App.jsx               # Main app
│   ├── main.jsx              # Entry point
│   └── index.css             # Global styles
│
├── package.json
├── vite.config.js
├── tailwind.config.js
└── COMPONENT_GUIDE.md (this file)
```

---

## 🧩 Component Documentation

### **Common Components** (`src/components/common/`)

#### `Button.jsx`
Reusable button with variants and states.

**Props:**
- `variant`: 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger'
- `size`: 'sm' | 'md' | 'lg'
- `loading`: boolean - shows spinner
- `fullWidth`: boolean
- `disabled`: boolean
- `icon`: React element
- `onClick`: function

**Usage:**
```jsx
import Button from './components/common/Button'

<Button variant="primary" size="lg" icon={<Plus />}>
  বিজ্ঞাপন দিন
</Button>
```

---

#### `SearchBar.jsx`
Flexible search bar with multiple variants.

**Props:**
- `variant`: 'default' | 'compact' | 'hero'
- `onSearch`: function(query, location)
- `showFilters`: boolean
- `locations`: array
- `placeholder`: string

**Usage:**
```jsx
import SearchBar from './components/common/SearchBar'
import { getAllLocations } from '../data/locations'

<SearchBar 
  variant="hero"
  locations={getAllLocations()}
  onSearch={(data) => console.log(data)}
/>
```

---

#### `VehicleCard.jsx`
Vehicle listing card with grid/list variants.

**Props:**
- `listing`: object (vehicle data)
- `variant`: 'grid' | 'list' | 'compact'
- `onFavorite`: function(id)
- `onClick`: function(listing)

**Usage:**
```jsx
import VehicleCard from './components/common/VehicleCard'
import { getFeaturedVehicles } from '../data/vehicles'

{getFeaturedVehicles().map(listing => (
  <VehicleCard 
    key={listing.id}
    listing={listing}
    variant="grid"
    onClick={(vehicle) => navigate(`/listing/${vehicle.id}`)}
  />
))}
```

---

#### `CategoryCard.jsx`
Category card with icon and count.

**Props:**
- `category`: object
- `icon`: Lucide icon component
- `onClick`: function
- `showCount`: boolean
- `animated`: boolean

---

#### `StatsCard.jsx`
Statistics display card.

**Props:**
- `stat`: object { value, label, description, color, icon }
- `icon`: Lucide icon component

---

### **Auth Components** (`src/components/auth/`)

#### `LoginModal.jsx`
Full-featured login modal with validation.

**Props:**
- `isOpen`: boolean
- `onClose`: function
- `onSwitchToRegister`: function

**Features:**
- Email/Phone + Password
- Show/Hide password
- Social login (Google, Facebook)
- Forgot password link
- Form validation

---

#### `RegisterModal.jsx`
Registration modal with validation.

**Props:**
- `isOpen`: boolean
- `onClose`: function
- `onSwitchToLogin`: function

**Features:**
- Name, Phone, Email, Password fields
- Confirm password validation
- Terms & conditions checkbox
- Real-time error display

---

### **Sections** (`src/components/sections/`)

#### `DownloadApp.jsx`
App download CTA section.

**Features:**
- Play Store & App Store buttons
- Feature list
- Phone mockup with QR code
- App screenshot preview
- Download stats
- 4.8 rating display

**Deep Links Ready:**
- Android: `garikinun://listing`
- iOS: `garikinun://listing`

---

### **SEO Component** (`src/components/SEO.jsx`)

Complete SEO optimization with:
- Primary meta tags
- Open Graph (Facebook share)
- Twitter Cards
- Mobile app meta
- JSON-LD Schema
- Deep linking meta tags
- Canonical URLs

**Props:**
- `title`: string
- `description`: string
- `keywords`: string
- `image`: string (OG image URL)
- `url`: string
- `type`: string

**Usage:**
```jsx
import SEO from './components/SEO'

<SEO 
  title="টয়োটা করোলা ২০২০ - ৳৩৫ লাখ"
  description="সম্পূর্ণ অরিজিনাল কন্ডিশন। ধানমন্ডি, ঢাকা"
  image="https://..."
  url="https://garikinun.com/listing/1"
/>
```

---

## 💾 Data Layer (`src/data/`)

### **vehicles.js**
12 complete vehicle listings with:
- All vehicle details
- Seller information
- View counts
- Featured status
- Search helpers

**Helper Functions:**
```javascript
import {
  getAllVehicles,
  getFeaturedVehicles,
  getVehicleById,
  getVehiclesByCategory,
  getTrendingVehicles,
  getLatestVehicles,
  searchVehicles
} from '../data/vehicles'

// Search with filters
const results = searchVehicles('টয়োটা', {
  category: 'cars',
  location: 'ঢাকা',
  minPrice: 2000000,
  maxPrice: 4000000,
  fuel: 'পেট্রোল',
  condition: 'ব্যবহৃত'
})
```

---

### **categories.js**
8 main categories with subcategories:
- গাড়ি (Cars) - 4 subcategories
- মোটরসাইকেল (Motorcycles) - 4 subcategories
- রিকশা (Rickshaw) - 3 subcategories
- সিএনজি (CNG) - 2 subcategories
- বাস (Bus) - 4 subcategories
- ট্রাক/পিকআপ (Trucks) - 4 subcategories
- নৌযান (Boats) - 3 subcategories
- পার্টস (Parts) - 4 subcategories

**Helper Functions:**
```javascript
import {
  getAllCategories,
  getCategoryBySlug,
  getCategoryById,
  getCategoryWithSubcategories
} from '../data/categories'
```

---

### **locations.js**
15 Bangladesh cities/districts + 10 Dhaka areas

**Helper Functions:**
```javascript
import {
  getAllLocations,
  getPopularLocations,
  getLocationById,
  getLocationByName,
  getDhakaAreas
} from '../data/locations'
```

---

### **other.js**
Supporting data:
- 3 Testimonials
- 8 Brands
- 4 Stats
- Fuel types
- Transmission types
- Condition types

---

## 🔍 Search & Filter Implementation

### Frontend Search (No Backend Required)

**Example: Search Component**
```jsx
import { useState } from 'react'
import { searchVehicles } from './data/vehicles'
import VehicleCard from './components/common/VehicleCard'

function SearchPage() {
  const [results, setResults] = useState([])
  
  const handleSearch = ({ query, location }) => {
    const filtered = searchVehicles(query, {
      location: location,
      category: selectedCategory,
      minPrice: minPrice,
      maxPrice: maxPrice
    })
    setResults(filtered)
  }
  
  return (
    <>
      <SearchBar onSearch={handleSearch} showFilters={true} />
      <div className="grid grid-cols-4 gap-6">
        {results.map(vehicle => (
          <VehicleCard key={vehicle.id} listing={vehicle} />
        ))}
      </div>
    </>
  )
}
```

---

## 🔗 Deep Linking Structure

Ready for integration with Flutter app:

### URL Structure
```
/                          → Home
/listing/:id               → Vehicle details
/category/:slug            → Category listings
/search?q=query           → Search results
/seller/:id                → Seller profile
```

### App Deep Links
```
garikinun://listing/123
garikinun://category/cars
garikinun://search?q=toyota
```

---

## 📱 Responsive Design

All components tested for:
- ✅ Desktop (1920px+)
- ✅ Laptop (1280px - 1919px)
- ✅ Tablet (768px - 1279px)
- ✅ Mobile (320px - 767px)

**Responsive Utilities:**
- Tailwind breakpoints: `sm:` `md:` `lg:` `xl:` `2xl:`
- Cards use responsive grid: `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`
- Text sizes scale: `text-base md:text-lg lg:text-xl`

---

## 🚀 How to Use Components

### 1. Building a New Page

```jsx
import SEO from './components/SEO'
import Header from './components/Header'
import SearchBar from './components/common/SearchBar'
import VehicleCard from './components/common/VehicleCard'
import Footer from './components/Footer'
import { searchVehicles } from './data/vehicles'

function SearchPage() {
  return (
    <>
      <SEO title="খুঁজুন - গাড়ি কিনুন" />
      <Header />
      <main>
        <SearchBar variant="default" />
        {/* Results grid */}
      </main>
      <Footer />
    </>
  )
}
```

### 2. Adding Auth to Any Component

```jsx
import { useState } from 'react'
import LoginModal from './components/auth/LoginModal'

function MyComponent() {
  const [showLogin, setShowLogin] = useState(false)
  
  return (
    <>
      <button onClick={() => setShowLogin(true)}>Login</button>
      <LoginModal 
        isOpen={showLogin}
        onClose={() => setShowLogin(false)}
      />
    </>
  )
}
```

### 3. Implementing Search

```jsx
import SearchBar from './components/common/SearchBar'
import { getAllLocations } from './data/locations'
import { searchVehicles } from './data/vehicles'

function handleSearch({ query, location }) {
  const results = searchVehicles(query, { location })
  console.log(results) // Use results
}

<SearchBar 
  variant="hero"
  locations={getAllLocations()}
  onSearch={handleSearch}
/>
```

---

## 🎨 Customization

### Colors (tailwind.config.js)
```javascript
colors: {
  primary: { /* Blue shades */ },
  accent: { /* Green shades */ },
}
```

### Add New Vehicle

```javascript
// In data/vehicles.js
export const vehicles = [
  // ... existing
  {
    id: 13,
    title: 'নতুন গাড়ি',
    price: '৪০,০০,০০০',
    priceRaw: 4000000,
    // ... all required fields
  }
]
```

---

## ⚡ Performance Tips

1. **Lazy Load Images**
```jsx
<img loading="lazy" src={listing.image} alt={listing.title} />
```

2. **Memoize Expensive Calculations**
```jsx
const filteredResults = useMemo(() => 
  searchVehicles(query, filters), 
  [query, filters]
)
```

3. **Virtualize Long Lists**
```bash
npm install react-window
```

---

## 🧪 Testing Checklist

- [ ] Login modal opens/closes
- [ ] Register modal validates inputs
- [ ] Search returns correct results
- [ ] Filters work properly
- [ ] Mobile menu works
- [ ] Cards display correctly
- [ ] Download app section responsive
- [ ] All links work
- [ ] Forms validate properly
- [ ] Images load correctly

---

## 📦 Ready for Backend Integration

When connecting to Laravel API:

### Before (Dummy Data)
```javascript
import { getFeaturedVehicles } from './data/vehicles'
const vehicles = getFeaturedVehicles()
```

### After (Real API)
```javascript
const response = await fetch('/api/listings?featured=true')
const vehicles = await response.json()
```

Same data structure - seamless migration! 🎉

---

## 🎯 Next Steps

1. ✅ All components created
2. ✅ Data layer organized
3. ✅ Auth UI ready
4. ✅ SEO configured
5. ⏳ Connect to Laravel backend
6. ⏳ Deploy to production
7. ⏳ Add actual app store links
8. ⏳ Implement real search API

---

## 📞 Support

For questions or issues:
- Check component prop types
- Review data helper functions
- Test with dummy data first
- Verify responsive design

**Happy Coding! 🚗✨**
