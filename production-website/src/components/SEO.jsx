import { Helmet } from 'react-helmet-async'

export default function SEO({ 
  title = 'গাড়ি কিনুন - বাংলাদেশের সবচেয়ে বড় গাড়ির বাজার',
  description = 'নতুন বা পুরাতন গাড়ি কেনা-বেচা করুন সহজে এবং নিরাপদে। সারা বাংলাদেশে ১০,০০০+ যাচাইকৃত বিক্রেতা। টয়োটা, হন্ডা, নিসান সহ সকল ব্র্যান্ডের গাড়ি পাবেন।',
  keywords = 'গাড়ি কিনুন, গাড়ি বিক্রয়, বাংলাদেশ গাড়ির বাজার, টয়োটা, হন্ডা, নিসান, মোটরসাইকেল, ব্যবহৃত গাড়ি',
  image = 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=1200',
  url = 'https://garikinun.com',
  type = 'website'
}) {
  return (
    <Helmet>
      {/* Primary Meta Tags */}
      <title>{title}</title>
      <meta name="title" content={title} />
      <meta name="description" content={description} />
      <meta name="keywords" content={keywords} />
      <meta name="author" content="গাড়ি কিনুন" />
      <meta name="language" content="Bengali" />
      <meta name="robots" content="index, follow" />
      
      {/* Open Graph / Facebook */}
      <meta property="og:type" content={type} />
      <meta property="og:url" content={url} />
      <meta property="og:title" content={title} />
      <meta property="og:description" content={description} />
      <meta property="og:image" content={image} />
      <meta property="og:site_name" content="গাড়ি কিনুন" />
      <meta property="og:locale" content="bn_BD" />
      
      {/* Twitter */}
      <meta property="twitter:card" content="summary_large_image" />
      <meta property="twitter:url" content={url} />
      <meta property="twitter:title" content={title} />
      <meta property="twitter:description" content={description} />
      <meta property="twitter:image" content={image} />
      
      {/* Mobile */}
      <meta name="theme-color" content="#0284c7" />
      <meta name="mobile-web-app-capable" content="yes" />
      <meta name="apple-mobile-web-app-capable" content="yes" />
      <meta name="apple-mobile-web-app-status-bar-style" content="default" />
      <meta name="apple-mobile-web-app-title" content="গাড়ি কিনুন" />
      
      {/* Favicon */}
      <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
      
      {/* Canonical */}
      <link rel="canonical" href={url} />
      
      {/* App Links for Deep Linking */}
      <meta property="al:android:url" content="garikinun://listing" />
      <meta property="al:android:package" content="com.garikinun.app" />
      <meta property="al:android:app_name" content="গাড়ি কিনুন" />
      
      <meta property="al:ios:url" content="garikinun://listing" />
      <meta property="al:ios:app_store_id" content="123456789" />
      <meta property="al:ios:app_name" content="গাড়ি কিনুন" />
      
      {/* JSON-LD Schema */}
      <script type="application/ld+json">
        {JSON.stringify({
          "@context": "https://schema.org",
          "@type": "WebSite",
          "name": "গাড়ি কিনুন",
          "description": description,
          "url": url,
          "potentialAction": {
            "@type": "SearchAction",
            "target": `${url}/search?q={search_term_string}`,
            "query-input": "required name=search_term_string"
          },
          "publisher": {
            "@type": "Organization",
            "name": "গাড়ি কিনুন",
            "logo": {
              "@type": "ImageObject",
              "url": `${url}/logo.png`
            }
          }
        })}
      </script>
    </Helmet>
  )
}
