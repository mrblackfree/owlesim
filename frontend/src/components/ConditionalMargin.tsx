// components/ConditionalMargin.tsx
'use client';

import { ReactNode } from 'react';

import { usePathname } from 'next/navigation';

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

// components/ConditionalMargin.tsx

interface ConditionalMarginProps {
    children: ReactNode;
}

const ConditionalMargin = ({ children }: ConditionalMarginProps) => {
    const pathname = usePathname();
    const isRootPath = pathname === '/';

    return <div className={isRootPath ? '' : 'mt-14 lg:mt-[65px]'}>{children}</div>;
};

export default ConditionalMargin;
