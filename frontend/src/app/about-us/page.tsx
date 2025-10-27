import React from 'react';

import { Metadata } from 'next';

import AboutUs from '@/components/pages/AboutUs';
import { QuickSEO } from '@/lib/seo';

export const metadata: Metadata = QuickSEO.custom({
    title: 'eSIM for Spain - Best Data Plans & 5G Coverage About us page',
    description:
        'Get instant eSIM for Spain with 98% coverage. Affordable data plans starting from $5. No roaming fees, instant activation in Madrid, Barcelona, Valencia.',
    image: '/images/splash.png',
    canonical: '/destinations/spain',
    keywords: ['Spain eSIM', 'Barcelona mobile data', 'Madrid travel SIM']
});

const page = () => {
    return <AboutUs />;
};

export default page;
