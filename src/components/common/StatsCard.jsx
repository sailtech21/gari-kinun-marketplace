export default function StatsCard({ stat, icon: Icon }) {
  return (
    <div className="card p-8 text-center hover:scale-105 transition-transform duration-300">
      <div className={`${stat.color} w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4`}>
        <Icon size={32} />
      </div>
      <h3 className="text-4xl font-bold text-gray-900 mb-2">{stat.value}</h3>
      <p className="text-lg text-gray-600 font-medium">{stat.label}</p>
      <p className="text-sm text-gray-500 mt-2">{stat.description}</p>
    </div>
  )
}
