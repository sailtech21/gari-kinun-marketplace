# 🚗 Gari Kinun - Bangla Vehicle Marketplace

A modern, full-featured vehicle marketplace built with React, connecting buyers and sellers in Bangladesh.

![React](https://img.shields.io/badge/React-18.3.1-blue)
![Vite](https://img.shields.io/badge/Vite-6.0.3-purple)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.4.17-cyan)
![License](https://img.shields.io/badge/License-MIT-green)

## 🌟 Features

- 🚙 **Browse Vehicles** - Cars, Bikes, Trucks, and more
- 🔍 **Advanced Search** - Filter by category, price, brand, year
- 📊 **Trending Listings** - See what's popular
- ⭐ **Customer Reviews** - Read testimonials in Bangla
- 🔔 **Notifications** - Real-time updates for users
- 🌐 **Bangla Language** - Full Unicode support
- 📱 **Responsive Design** - Works on all devices
- 🔐 **Firebase Auth** - Secure user authentication

## 🚀 Live Demo

- **Website:** [garikinun.com](https://garikinun.com)
- **Admin Panel:** [admin.garikinun.com](https://admin.garikinun.com/admin)

## 🛠️ Tech Stack

### Frontend
- **Framework:** React 18.3.1
- **Build Tool:** Vite 6.0.3
- **Styling:** TailwindCSS 3.4.17
- **Icons:** Lucide React 0.460.0
- **State Management:** React Context API
- **SEO:** React Helmet Async 2.0.5
- **Authentication:** Firebase 12.9.0

### Backend
- **API:** Laravel 10 REST API
- **Database:** MySQL
- **Server:** Hostinger VPS

## 📦 Installation

```bash
# Clone the repository
git clone https://github.com/YOUR_USERNAME/gari-kinun-marketplace.git
cd gari-kinun-marketplace

# Install dependencies
npm install

# Create environment file
cp .env.local.example .env.local

# Add your Firebase credentials to .env.local
# VITE_FIREBASE_API_KEY=your_key_here
# ...

# Start development server
npm run dev
```

## 🔧 Environment Variables

Create a `.env.local` file with these variables:

```env
VITE_FIREBASE_API_KEY=your_firebase_api_key
VITE_FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=your_project_id
VITE_FIREBASE_STORAGE_BUCKET=your_bucket.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=your_sender_id
VITE_FIREBASE_APP_ID=your_app_id
```

## 📜 Available Scripts

```bash
# Start development server (http://localhost:3000)
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

## 🌐 Deployment

### Deploy to Vercel (Recommended)

1. Push your code to GitHub
2. Go to [vercel.com](https://vercel.com)
3. Import your GitHub repository
4. Add environment variables
5. Deploy!

See [DEPLOY_TO_VERCEL.md](DEPLOY_TO_VERCEL.md) for detailed instructions.

### Build Locally

```bash
npm run build
# Output will be in dist/ folder
```

## 📂 Project Structure

```
website/
├── public/              # Static assets
├── src/
│   ├── components/      # React components
│   │   ├── auth/       # Authentication components
│   │   ├── common/     # Reusable components
│   │   ├── modals/     # Modal dialogs
│   │   ├── pages/      # Page components
│   │   └── sections/   # Section components
│   ├── contexts/        # React Context providers
│   ├── config.js        # API configuration
│   ├── App.jsx          # Main app component
│   └── main.jsx         # Entry point
├── .env.local           # Environment variables (not in git)
├── .gitignore           # Git ignore rules
├── index.html           # HTML template
├── package.json         # Dependencies
├── vite.config.js       # Vite configuration
└── vercel.json          # Vercel deployment config
```

## 🎨 Key Components

- **Header** - Navigation with notifications
- **Hero** - Landing page hero section
- **PopularCategories** - Category grid
- **FeaturedListings** - Featured vehicles
- **TrendingListings** - Trending items
- **ReviewsSection** - Customer testimonials
- **Footer** - Site footer with links

## 🔌 API Integration

The app connects to a Laravel backend API:

```javascript
// src/config.js
const API_BASE_URL = 'https://admin.garikinun.com/api'
```

### API Endpoints Used

- `GET /api/categories` - List categories
- `GET /api/listings` - List vehicles
- `GET /api/listings/trending` - Trending listings
- `GET /api/listings/{id}` - Single listing details
- `GET /api/reviews` - Customer reviews
- `GET /api/notifications` - User notifications
- `GET /api/notifications/unread-count` - Notification count

## 🎯 Features Overview

### User Features
- Browse vehicles by category
- Search and filter listings
- View detailed listing information
- Read customer reviews
- Receive notifications
- Create and manage listings (authenticated)
- Save favorites
- Contact sellers

### Admin Features (via Admin Panel)
- Manage listings
- Approve dealers
- Handle reports
- Approve reviews
- Send notifications
- Configure settings
- Upload banners

## 🔐 Authentication

Firebase Authentication with:
- Email/Password login
- Phone authentication
- Google Sign-In (optional)

## 🌍 Localization

- **Primary Language:** Bangla (বাংলা)
- **Secondary Language:** English
- Full Unicode support for Bangla text

## 📱 Responsive Design

Breakpoints:
- Mobile: < 640px
- Tablet: 640px - 1024px
- Desktop: > 1024px

## 🚀 Performance

- Vite for fast builds
- Lazy loading for images
- Code splitting
- Tree shaking
- Minification
- Gzip compression

## 🧪 Testing

```bash
# Run tests (when implemented)
npm test
```

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**Tushar**
- Website: [garikinun.com](https://garikinun.com)

## 🙏 Acknowledgments

- React team for the amazing framework
- TailwindCSS for utility-first CSS
- Lucide for beautiful icons
- Firebase for authentication
- Vercel for hosting

## 📞 Support

For support, email support@garikinun.com or join our community.

## 🗺️ Roadmap

- [ ] Mobile app (iOS/Android)
- [ ] Advanced search filters
- [ ] Chat between buyers and sellers
- [ ] Payment integration
- [ ] Vehicle comparison tool
- [ ] Wishlist sync
- [ ] Social media integration
- [ ] Multi-language support
- [ ] Dark mode

---

Made with ❤️ in Bangladesh 🇧🇩
