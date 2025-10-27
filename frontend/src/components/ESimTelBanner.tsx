import React from 'react';

import Image from 'next/image';
import Link from 'next/link';

import { API_URL, BASE_URL, FRONT_BASE_URL } from '@/config/constant';
import { usePublicApiHandler } from '@/lib/apiHandler/usePublicApiHandler';

// TypeScript interfaces
interface BannerData {
    id: number;
    name: string;
    image: string;
    is_active: number;
    package_id: number;
    banner_from: string;
    banner_to: string;
    created_at: string;
    updated_at: string;
}

interface ApiResponse {
    success: boolean;
    data: BannerData[];
    message: string;
}

const ESimTelBanner: React.FC = () => {
    const { data: banner } = usePublicApiHandler({
        url: '/banners'
    }) as { data: ApiResponse | undefined };

    // Filter active banners
    let activeBanners =
        banner?.data?.filter((item: BannerData) => {
            if (!item.is_active) return false;

            const now = new Date();
            const bannerFrom = new Date(item.banner_from);
            const bannerTo = new Date(item.banner_to);

            return now >= bannerFrom && now <= bannerTo;
        }) || [];

    if (!activeBanners.length) {
        return (
            <section className='bg-gradient-to-b from-blue-50 via-white to-purple-50 py-12 lg:py-16'>
                <div className='container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8'>
                    <div className='text-center text-gray-500'>No active banners available</div>
                </div>
            </section>
        );
    }

    return (
        <section className='relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8 sm:py-12 lg:py-16'>
            <div className='container mx-auto max-w-6xl px-4 sm:px-6 lg:px-8'>
                {/* Image Cards Grid */}
                <div className='grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12'>
                    {activeBanners.map((item: BannerData, index: number) => {
                        return (
                            <Link
                                href='/all-destinations'
                                key={item.id}
                                className='group relative cursor-pointer overflow-hidden rounded-2xl shadow-xl transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl'>
                                {/* Main Image */}
                                <div className='relative aspect-[16/9] w-full'>
                                    <Image
                                        src={`${BASE_URL}/${item.image}`}
                                        // src={getImageUrl(item.image)}
                                        alt={item.name}
                                        fill
                                        className='object-center transition-transform duration-700 group-hover:scale-110'
                                        sizes='(max-width: 1024px) 100vw, 50vw'
                                        priority={index === 0}
                                        unoptimized={true}
                                        onError={(e) => {
                                            const target = e.target as HTMLImageElement;
                                            target.style.display = 'none';
                                            target.nextElementSibling?.classList.remove('hidden');
                                        }}
                                    />

                                    {/* Fallback when image fails */}
                                    <div className='absolute inset-0 hidden items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100'>
                                        <div className='text-center'>
                                            <div className='mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-blue-200'>
                                                <svg
                                                    className='h-10 w-10 text-blue-500'
                                                    fill='none'
                                                    stroke='currentColor'
                                                    viewBox='0 0 24 24'>
                                                    <path
                                                        strokeLinecap='round'
                                                        strokeLinejoin='round'
                                                        strokeWidth={1.5}
                                                        d='M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z'
                                                    />
                                                </svg>
                                            </div>
                                            <h3 className='text-xl font-semibold text-blue-600'>{item.name}</h3>
                                        </div>
                                    </div>

                                    {/* Hover Overlay */}

                                    {/* Badges */}

                                    <div className='absolute top-4 left-4 z-10 flex items-center gap-2 rounded-full bg-green-500/90 px-3 py-1.5'>
                                        <div className='h-2 w-2 animate-pulse rounded-full bg-white'></div>
                                        <span className='text-xs font-medium text-white'>Live</span>
                                    </div>
                                </div>
                            </Link>
                        );
                    })}
                </div>
            </div>

            {/* Background Elements */}
            <div className='absolute top-0 right-0 h-64 w-64 translate-x-32 -translate-y-32 rounded-full bg-blue-200/20 blur-3xl'></div>
            <div className='absolute bottom-0 left-0 h-64 w-64 -translate-x-32 translate-y-32 rounded-full bg-purple-200/20 blur-3xl'></div>
        </section>
    );
};

export default ESimTelBanner;
