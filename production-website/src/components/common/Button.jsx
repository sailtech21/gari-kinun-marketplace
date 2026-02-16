import { Loader2 } from 'lucide-react'

/**
 * Reusable Button Component
 * @param {string} variant - primary | secondary | outline | ghost
 * @param {string} size - sm | md | lg
 * @param {boolean} loading - Show loading spinner
 * @param {boolean} fullWidth - Full width button
 * @param {ReactNode} icon - Icon component
 * @param {ReactNode} children - Button text
 */
export default function Button({ 
  children, 
  variant = 'primary', 
  size = 'md',
  loading = false,
  fullWidth = false,
  disabled = false,
  icon = null,
  onClick,
  type = 'button',
  className = '',
  ...props 
}) {
  
  const baseStyles = 'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed'
  
  const variants = {
    primary: 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500 shadow-sm hover:shadow-md',
    secondary: 'bg-accent-600 hover:bg-accent-700 text-white focus:ring-accent-500 shadow-sm hover:shadow-md',
    outline: 'border-2 border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500',
    ghost: 'text-primary-600 hover:bg-primary-50 focus:ring-primary-500',
    danger: 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500 shadow-sm',
  }
  
  const sizes = {
    sm: 'px-4 py-2 text-sm',
    md: 'px-6 py-3 text-base',
    lg: 'px-8 py-4 text-lg',
  }
  
  const widthClass = fullWidth ? 'w-full' : ''
  
  return (
    <button
      type={type}
      disabled={disabled || loading}
      onClick={onClick}
      className={`${baseStyles} ${variants[variant]} ${sizes[size]} ${widthClass} ${className}`}
      {...props}
    >
      {loading ? (
        <>
          <Loader2 size={20} className="animate-spin" />
          <span>অপেক্ষা করুন...</span>
        </>
      ) : (
        <>
          {icon && icon}
          {children}
        </>
      )}
    </button>
  )
}
