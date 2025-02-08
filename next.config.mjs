/** @type {import('next').NextConfig} */
const nextConfig = {
  experimental: {
    serverActions: {
      bodySizeLimit: "10mb", // Increase the limit as needed
    },
  },
};

export default nextConfig;
