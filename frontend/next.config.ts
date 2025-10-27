// next.config.ts (Fixed)
import type { NextConfig } from 'next';

const withBundleAnalyzer = require('@next/bundle-analyzer')({
    enabled: process.env.BUNDLE_ANALYZER_ENABLED === 'true'
});

const nextConfig: NextConfig = {
    output: 'standalone',
    images: {
        remotePatterns: [
            {
                protocol: 'https',
                hostname: 'admin.esimtel.app',
                pathname: '/**'
            },
            {
                protocol: 'https',
                hostname: 'cdn.airalo.com',
                pathname: '/**' // यह add करना जरूरी है
            }
        ]
    }
};

export default withBundleAnalyzer(nextConfig);
