import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  // Add this rewrites block
  async rewrites() {
    return [
      {
        source: "/api/:path*", // Matches all requests to /api/
        destination: "http://localhost:8080/api/:path*", // Proxies them to your PHP API
      },
    ];
  },
};

export default nextConfig;
