# Website Frontend - Production Deployment Guide

## 📦 Package Contents
This folder contains the production-ready React website for GariKinun marketplace.

**Included:**
- Source code (src/)
- Public assets
- Build configuration files
- Vercel deployment config
- Package dependency files

**Excluded:**
- node_modules/ folder (install locally before build)
- dist/ folder (generated during build)
- .env.local file (create from .env.local.example)
- Development files

## 🚀 Deployment Steps (Vercel via GitHub)

### 1. Prepare Environment File
```bash
# Create environment file
cp .env.local.example .env.local

# Edit with production values
nano .env.local
```

### 2. Environment Variables
Add these to Vercel project settings:
```env
VITE_API_BASE_URL=https://admin.garikinun.com
VITE_FIREBASE_API_KEY=your_firebase_api_key
VITE_FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=your-project-id
VITE_FIREBASE_STORAGE_BUCKET=your-project.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=your_sender_id
VITE_FIREBASE_APP_ID=your_app_id
```

### 3. Local Build Test
```bash
# Install dependencies
npm install

# Test build locally
npm run build

# Preview build
npm run preview
```

### 4. Deploy to Vercel (via GitHub)

#### Option A: Automatic Deployment
1. Push to GitHub repository
2. Vercel automatically detects changes
3. Builds and deploys to garikinun.com
4. Verify deployment in Vercel dashboard

#### Option B: Manual Vercel CLI
```bash
# Install Vercel CLI
npm i -g vercel

# Login to Vercel
vercel login

# Deploy
vercel --prod
```

### 5. Vercel Configuration
`vercel.json` is already configured:
```json
{
  "rewrites": [
    { "source": "/(.*)", "destination": "/" }
  ],
  "headers": [
    {
      "source": "/(.*)",
      "headers": [
        { "key": "X-Content-Type-Options", "value": "nosniff" },
        { "key": "X-Frame-Options", "value": "DENY" },
        { "key": "X-XSS-Protection", "value": "1; mode=block" }
      ]
    }
  ]
}
```

## 📝 Build Configuration

### Vite Config
- Build output: `dist/`
- Port: 5173 (development)
- Base URL: `/`

### Tailwind CSS
- JIT mode enabled
- Purge production CSS
- Custom theme configured

### API Integration
- Base URL: `https://admin.garikinun.com`
- Endpoints: `/api/cms/*`, `/api/listings`, `/api/categories`
- Authentication: Firebase + Laravel Sanctum

## 🔍 Verify Deployment
- Visit: https://garikinun.com
- Check homepage hero section loads from API
- Test user registration/login (Firebase)
- Browse listings and categories
- Verify all API calls working

## 📊 Post-Deployment Checklist
- [ ] Environment variables configured in Vercel
- [ ] GitHub repository connected to Vercel
- [ ] Build successful (check Vercel dashboard)
- [ ] Website accessible at garikinun.com
- [ ] Hero section displays CMS content
- [ ] Listings page loading data
- [ ] User authentication working
- [ ] All API endpoints responding
- [ ] Images loading correctly

## 🔧 Troubleshooting

**Build Fails:**
- Check Vercel build logs
- Verify all dependencies in package.json
- Ensure environment variables set
- Test build locally first

**API Not Connecting:**
- Verify VITE_API_BASE_URL in Vercel
- Check CORS settings in Laravel backend
- Test API endpoint directly: https://admin.garikinun.com/api/cms/hero-section

**Firebase Auth Issues:**
- Verify all Firebase config variables
- Check Firebase console for API quotas
- Ensure domain authorized in Firebase

**Blank Page After Deployment:**
- Check browser console for errors
- Verify vercel.json rewrites configuration
- Clear Vercel cache and redeploy

## 🌐 Production URLs
- **Website:** https://garikinun.com
- **Admin Panel:** https://admin.garikinun.com
- **API Base:** https://admin.garikinun.com/api
- **Vercel Dashboard:** https://vercel.com/dashboard

## 🔄 Update Workflow
1. Make changes locally
2. Test with `npm run dev`
3. Build locally to verify: `npm run build`
4. Commit changes to GitHub
5. Push to main branch
6. Vercel auto-deploys (check dashboard)
7. Verify on garikinun.com
8. Monitor Vercel deployment logs

## ⚙️ Framework Versions
- React: 18.3.1
- Vite: 6.0.3
- TailwindCSS: 3.4.17
- Firebase: 12.9.0
- Node: 18+ (required)
