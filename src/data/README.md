# Dummy Data Layer

This folder contains all the fake/dummy data used throughout the website to make it feel realistic and production-ready.

## Data Files

### `dummyData.js`
Main data file containing:

#### 📋 **Listings** (12 vehicles)
- Detailed vehicle information including:
  - Title, price, location, year
  - Fuel type, transmission, mileage, color
  - Seller information (name, phone, verification status)
  - View counts, featured status
  - High-quality images from Unsplash
  - Detailed descriptions in Bangla

#### 🏷️ **Categories** (8 categories)
- Vehicle types with icons and colors
- Count of listings per category
- Descriptions and subcategories
- Includes: গাড়ি, মোটরসাইকেল, রিকশা, সিএনজি, বাস, ট্রাক/পিকআপ, নৌযান, পার্টস

#### 📍 **Locations** (10 major cities)
- Bangladesh cities with listing counts
- Dhaka, Chittagong, Sylhet, Rajshahi, Khulna, etc.

#### ⭐ **Testimonials** (3 user reviews)
- Real-sounding customer feedback
- 5-star ratings
- User names, locations, dates
- Avatar images

#### 🚗 **Brands** (8 popular brands)
- Toyota, Honda, Nissan, Mitsubishi, etc.
- Listing counts per brand
- Emoji logos for quick visualization

## Helper Functions

The file also exports utility functions to access data:

```javascript
// Get featured listings only
getFeaturedListings()

// Get all listings
getAllListings()

// Find listing by ID
getListingById(id)

// Get listings by category
getListingsByCategory(categorySlug)

// Search listings by query
searchListings(query)

// Get popular categories
getPopularCategories()

// Get trending listings (sorted by views)
getTrendingListings()

// Get latest listings
getLatestListings()
```

## Usage in Components

### FeaturedListings.jsx
```javascript
import { getFeaturedListings } from '../data/dummyData'
const listings = getFeaturedListings().slice(0, 8)
```

### PopularCategories.jsx
```javascript
import { getPopularCategories } from '../data/dummyData'
const categories = getPopularCategories()
```

### Hero.jsx
```javascript
import { locations } from '../data/dummyData'
// Used in location dropdown
```

### Testimonials.jsx
```javascript
import { testimonials } from '../data/dummyData'
```

### PopularBrands.jsx
```javascript
import { brands } from '../data/dummyData'
```

### TrendingListings.jsx
```javascript
import { getTrendingListings } from '../data/dummyData'
```

## Data Characteristics

✅ **Bangladesh-Specific**
- All content in Bangla (বাংলা)
- Local city names and locations
- Bangladeshi Taka (৳) currency
- Local vehicle preferences

✅ **Realistic Details**
- Actual vehicle models available in Bangladesh
- Realistic pricing (18-55 lakh taka)
- Authentic seller names and phone patterns
- Genuine location names (Dhanmondi, Uttara, Gulshan, etc.)

✅ **SEO-Friendly**
- Real image URLs from Unsplash
- Descriptive titles
- Rich metadata (mileage, color, transmission)

✅ **Production-Ready**
- Structured JSON-like format
- Easy to replace with real API
- Consistent data schema
- TypeScript-ready structure

## Future Integration

When connecting to the Laravel backend API, simply replace:

```javascript
// Current (dummy data)
import { getFeaturedListings } from '../data/dummyData'
const listings = getFeaturedListings()

// Future (real API)
const response = await fetch('/api/listings?featured=true')
const listings = await response.json()
```

The data structure matches the expected API format, making migration seamless!

## Adding More Data

To add more listings:

1. Copy an existing listing object
2. Update the ID (increment)
3. Change vehicle details
4. Update seller info
5. Use new Unsplash image URL

Example:
```javascript
{
  id: 13,
  title: 'Honda Accord ২০২২',
  price: '৪৫,০০,০০০',
  priceRaw: 4500000,
  location: 'মিরপুর, ঢাকা',
  // ... rest of the fields
}
```

---

**Total Data Points:**
- 12 Vehicle Listings
- 8 Categories
- 10 Locations  
- 3 Testimonials
- 8 Brands
- **All 100% in Bangla** 🇧🇩
