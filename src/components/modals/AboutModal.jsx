import { X, CheckCircle, Users, Car, Shield, TrendingUp, Award, Clock } from 'lucide-react'

export default function AboutModal({ isOpen, onClose }) {
  if (!isOpen) return null

  const features = [
    {
      icon: Car,
      title: '১০,০০০+ গাড়ি',
      description: 'সব ধরনের নতুন ও পুরাতন গাড়ি'
    },
    {
      icon: Users,
      title: '৫,০০০+ বিক্রেতা',
      description: 'যাচাইকৃত ডিলার ও ব্যক্তিগত বিক্রেতা'
    },
    {
      icon: Shield,
      title: '১০০% নিরাপদ',
      description: 'সম্পূর্ণ নিরাপদ লেনদেন'
    },
    {
      icon: TrendingUp,
      title: 'সেরা দাম',
      description: 'বাজারের সবচেয়ে ভালো দামে গাড়ি'
    },
    {
      icon: Award,
      title: 'বিশ্বস্ত সেবা',
      description: 'বাংলাদেশের #১ প্ল্যাটফর্ম'
    },
    {
      icon: Clock,
      title: '২৪/৭ সাপোর্ট',
      description: 'যেকোনো সময় সহায়তা পাবেন'
    }
  ]

  const stats = [
    { number: '১০,০০০+', label: 'মোট গাড়ি' },
    { number: '৫,০০০+', label: 'সন্তুষ্ট ক্রেতা' },
    { number: '৬৪', label: 'জেলায় সেবা' },
    { number: '৯৮%', label: 'সফলতার হার' }
  ]

  return (
    <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4 animate-fade-in">
      <div className="bg-white rounded-3xl max-w-5xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-slide-up">
        {/* Header */}
        <div className="sticky top-0 bg-gradient-to-r from-teal-600 via-teal-700 to-teal-800 text-white p-8 rounded-t-3xl">
          <button
            onClick={onClose}
            className="absolute top-6 right-6 p-2 hover:bg-white/20 rounded-full transition-colors"
          >
            <X size={24} />
          </button>
          
          <div className="text-center space-y-4">
            <div className="inline-flex items-center gap-3 bg-white/20 px-6 py-3 rounded-full backdrop-blur-sm">
              <Award className="animate-pulse" size={28} />
              <span className="text-2xl font-bold">বাংলাদেশের #১ গাড়ির মার্কেটপ্লেস</span>
            </div>
            
            <h2 className="text-5xl font-black leading-tight">
              গাড়ি কিনুন - বাংলাদেশের<br />
              সবচেয়ে বড় গাড়ির বাজার
            </h2>
            
            <p className="text-xl text-teal-50 max-w-2xl mx-auto">
              আমরা বাংলাদেশের সবচেয়ে বিশ্বস্ত এবং জনপ্রিয় গাড়ি কেনা-বেচার প্ল্যাটফর্ম।
              হাজার হাজার মানুষ প্রতিদিন আমাদের মাধ্যমে তাদের স্বপ্নের গাড়ি খুঁজে পান।
            </p>
          </div>
        </div>

        {/* Stats Section */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 p-8 bg-gray-50">
          {stats.map((stat, index) => (
            <div
              key={index}
              className="bg-white p-6 rounded-2xl text-center shadow-lg hover:shadow-xl transition-shadow"
            >
              <div className="text-4xl font-black text-teal-600 mb-2">
                {stat.number}
              </div>
              <div className="text-gray-600 font-semibold">
                {stat.label}
              </div>
            </div>
          ))}
        </div>

        {/* Features Section */}
        <div className="p-8">
          <h3 className="text-3xl font-bold text-gray-900 mb-8 text-center">
            কেন গাড়ি কিনুন বেছে নেবেন?
          </h3>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {features.map((feature, index) => {
              const Icon = feature.icon
              return (
                <div
                  key={index}
                  className="bg-gradient-to-br from-white to-gray-50 p-6 rounded-2xl border-2 border-gray-100 hover:border-teal-300 hover:shadow-xl transition-all group"
                >
                  <div className="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-teal-600 transition-colors">
                    <Icon className="text-teal-600 group-hover:text-white transition-colors" size={28} />
                  </div>
                  <h4 className="text-xl font-bold text-gray-900 mb-2">
                    {feature.title}
                  </h4>
                  <p className="text-gray-600">
                    {feature.description}
                  </p>
                </div>
              )
            })}
          </div>
        </div>

        {/* Why Choose Us */}
        <div className="p-8 bg-gradient-to-br from-teal-50 to-blue-50">
          <h3 className="text-3xl font-bold text-gray-900 mb-6 text-center">
            আমাদের বিশেষত্ব
          </h3>
          
          <div className="max-w-3xl mx-auto space-y-4">
            {[
              'সম্পূর্ণ বিনামূল্যে অ্যাকাউন্ট তৈরি করুন এবং গাড়ি বিক্রি করুন',
              'যাচাইকৃত ডিলার ও ব্যক্তিগত বিক্রেতাদের সাথে সরাসরি যোগাযোগ',
              'বিস্তারিত গাড়ির তথ্য, ছবি এবং ভিডিও দেখুন',
              'উন্নত সার্চ ফিল্টার দিয়ে সহজেই আপনার পছন্দের গাড়ি খুঁজুন',
              'নিরাপদ পেমেন্ট এবং ডকুমেন্ট ভেরিফিকেশন সেবা',
              'প্রফেশনাল কাস্টমার সাপোর্ট টিম সবসময় আপনার সাহায্যে'
            ].map((point, index) => (
              <div
                key={index}
                className="flex items-start gap-4 bg-white p-5 rounded-xl shadow-md hover:shadow-lg transition-shadow"
              >
                <CheckCircle className="text-teal-600 flex-shrink-0 mt-1" size={24} />
                <p className="text-gray-700 text-lg">{point}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Call to Action */}
        <div className="p-8 bg-gradient-to-r from-teal-600 to-teal-700 text-white text-center rounded-b-3xl">
          <h3 className="text-3xl font-bold mb-4">
            আজই শুরু করুন!
          </h3>
          <p className="text-xl text-teal-50 mb-6">
            আপনার স্বপ্নের গাড়ি খুঁজে নিন বা আপনার গাড়ি বিক্রি করুন
          </p>
          <button
            onClick={onClose}
            className="bg-white text-teal-700 font-bold px-12 py-4 rounded-full hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl text-lg"
          >
            শুরু করুন
          </button>
        </div>
      </div>
    </div>
  )
}
