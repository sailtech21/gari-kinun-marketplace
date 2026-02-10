export const translations = {
  bn: {
    // Common
    back: 'ফিরে যান',
    save: 'সংরক্ষণ করুন',
    saving: 'সংরক্ষণ হচ্ছে...',
    loading: 'লোড হচ্ছে...',
    search: 'খুঁজুন',
    
    // Profile
    myProfile: 'আমার প্রোফাইল',
    profileDescription: 'আপনার ব্যক্তিগত তথ্য দেখুন এবং পরিবর্তন করুন',
    personalInfo: 'ব্যক্তিগত তথ্য',
    changePassword: 'পাসওয়ার্ড পরিবর্তন',
    settings: 'সেটিংস',
    
    // Form Labels
    name: 'নাম',
    email: 'ইমেইল',
    phone: 'ফোন নম্বর',
    address: 'ঠিকানা',
    currentPassword: 'বর্তমান পাসওয়ার্ড',
    newPassword: 'নতুন পাসওয়ার্ড',
    confirmPassword: 'নতুন পাসওয়ার্ড নিশ্চিত করুন',
    
    // Settings
    darkMode: 'ডার্ক মোড',
    darkModeDesc: 'রাতের জন্য গাঢ় থিম সক্রিয় করুন',
    language: 'ভাষা নির্বাচন করুন',
    languageDesc: 'আপনার পছন্দের ভাষা বেছে নিন',
    settingsInfo: 'সেটিংস সম্পর্কে',
    currentSettings: 'বর্তমান সেটিংস',
    themeMode: 'থিম মোড',
    lightMode: '☀️ লাইট মোড',
    darkModeLabel: '🌙 ডার্ক মোড',
    bengali: '🇧🇩 বাংলা',
    english: '🇬🇧 English',
    
    // Messages
    profileUpdated: 'প্রোফাইল আপডেট সফল হয়েছে!',
    passwordChanged: 'পাসওয়ার্ড পরিবর্তন সফল হয়েছে!',
    passwordMismatch: 'পাসওয়ার্ড মিলছে না',
    
    // Dealer
    becomeDealer: 'ডিলার হন',
    verifiedDealer: 'ভেরিফাইড ডিলার',
  },
  en: {
    // Common
    back: 'Go Back',
    save: 'Save Changes',
    saving: 'Saving...',
    loading: 'Loading...',
    search: 'Search',
    
    // Profile
    myProfile: 'My Profile',
    profileDescription: 'View and edit your personal information',
    personalInfo: 'Personal Information',
    changePassword: 'Change Password',
    settings: 'Settings',
    
    // Form Labels
    name: 'Name',
    email: 'Email',
    phone: 'Phone Number',
    address: 'Address',
    currentPassword: 'Current Password',
    newPassword: 'New Password',
    confirmPassword: 'Confirm New Password',
    
    // Settings
    darkMode: 'Dark Mode',
    darkModeDesc: 'Enable dark theme for night time',
    language: 'Choose Language',
    languageDesc: 'Select your preferred language',
    settingsInfo: 'About Settings',
    currentSettings: 'Current Settings',
    themeMode: 'Theme Mode',
    lightMode: '☀️ Light Mode',
    darkModeLabel: '🌙 Dark Mode',
    bengali: '🇧🇩 বাংলা',
    english: '🇬🇧 English',
    
    // Messages
    profileUpdated: 'Profile updated successfully!',
    passwordChanged: 'Password changed successfully!',
    passwordMismatch: 'Passwords do not match',
    
    // Dealer
    becomeDealer: 'Become a Dealer',
    verifiedDealer: 'Verified Dealer',
  }
}

export const useTranslation = (language = 'bn') => {
  const t = (key) => {
    return translations[language]?.[key] || translations.bn[key] || key
  }
  
  return { t }
}
