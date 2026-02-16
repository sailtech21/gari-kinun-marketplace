import { createContext, useContext, useState, useEffect } from 'react'

const SettingsContext = createContext()

export const useSettings = () => {
  const context = useContext(SettingsContext)
  if (!context) {
    throw new Error('useSettings must be used within a SettingsProvider')
  }
  return context
}

export const SettingsProvider = ({ children }) => {
  const [darkMode, setDarkMode] = useState(() => {
    const saved = localStorage.getItem('darkMode')
    return saved ? JSON.parse(saved) : false
  })

  const [language, setLanguage] = useState(() => {
    const saved = localStorage.getItem('language')
    return saved || 'bn' // 'bn' for Bengali, 'en' for English
  })

  useEffect(() => {
    localStorage.setItem('darkMode', JSON.stringify(darkMode))
    
    // Apply dark mode class to document
    if (darkMode) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }, [darkMode])

  useEffect(() => {
    localStorage.setItem('language', language)
  }, [language])

  const toggleDarkMode = () => {
    setDarkMode(prev => !prev)
  }

  const toggleLanguage = () => {
    setLanguage(prev => prev === 'bn' ? 'en' : 'bn')
  }

  const changeLanguage = (lang) => {
    setLanguage(lang)
  }

  const value = {
    darkMode,
    language,
    toggleDarkMode,
    toggleLanguage,
    changeLanguage
  }

  return (
    <SettingsContext.Provider value={value}>
      {children}
    </SettingsContext.Provider>
  )
}
