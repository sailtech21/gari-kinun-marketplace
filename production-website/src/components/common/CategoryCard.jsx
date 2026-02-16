import { ArrowRight } from 'lucide-react'

export default function CategoryCard({ 
  category, 
  icon: Icon, 
  onClick,
  showCount = true,
  animated = true
}) {
  return (
    <button
      onClick={onClick}
      className={`card p-6 group ${animated ? 'hover:scale-105 transform transition-all duration-300' : 'hover:shadow-lg transition-shadow'}`}
    >
      {/* Icon */}
      <div className={`${category.color} w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 ${animated ? 'group-hover:scale-110 transition-transform' : ''}`}>
        <Icon size={32} />
      </div>
      
      {/* Name */}
      <h3 className="text-lg font-semibold text-gray-900 text-center mb-1">
        {category.name}
      </h3>
      
      {/* Count */}
      {showCount && (
        <p className="text-sm text-gray-500 text-center mb-2">
          {category.count} বিজ্ঞাপন
        </p>
      )}
      
      {/* Description (on hover) */}
      {category.description && (
        <p className="text-xs text-gray-400 text-center opacity-0 group-hover:opacity-100 transition-opacity">
          {category.description}
        </p>
      )}
      
      {/* Arrow indicator */}
      <div className="flex justify-center mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
        <ArrowRight size={18} className="text-primary-600" />
      </div>
    </button>
  )
}
