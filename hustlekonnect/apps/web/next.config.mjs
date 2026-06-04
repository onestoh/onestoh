/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    domains: ['hustlekonnect.s3.amazonaws.com', 'via.placeholder.com'],
  },
  env: {
    NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL,
    NEXT_PUBLIC_APP_NAME: 'HustleKonnect',
  },
}

export default nextConfig
