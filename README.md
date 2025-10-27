# 🦉 OwleSIM - eSIM Management Platform

![Next.js](https://img.shields.io/badge/Next.js-15.5-black)
![Laravel](https://img.shields.io/badge/Laravel-11-red)
![TypeScript](https://img.shields.io/badge/TypeScript-5.9-blue)
![License](https://img.shields.io/badge/license-MIT-green)

eSIM 패키지 관리 및 판매를 위한 풀스택 웹 애플리케이션

## 🚀 기술 스택

### Frontend
- **Framework**: Next.js 15 (React 19)
- **Language**: TypeScript
- **Styling**: Tailwind CSS v4
- **UI Components**: Radix UI, Shadcn UI
- **State Management**: Redux Toolkit, Zustand
- **Data Fetching**: TanStack Query
- **Form Handling**: React Hook Form + Zod
- **Authentication**: Firebase Auth

### Backend
- **Framework**: Laravel 11
- **Language**: PHP 8.2+
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Payment**: Stripe, Razorpay, Cashfree
- **eSIM Provider**: Airalo API

## 📁 프로젝트 구조

```
owlesim/
├── frontend/          # Next.js 애플리케이션
│   ├── src/
│   │   ├── app/      # App Router 페이지
│   │   ├── components/
│   │   ├── lib/      # 유틸리티 및 설정
│   │   └── redux/    # 상태 관리
│   └── package.json
├── app/              # Laravel 애플리케이션
├── config/           # Laravel 설정
├── database/         # 마이그레이션 및 시더
├── routes/           # API 및 웹 라우트
└── vercel.json       # Vercel 배포 설정
```

## 🛠️ 설치 및 실행

### Prerequisites
- Node.js 18+
- PHP 8.2+
- Composer
- MySQL

### Backend (Laravel)

```bash
# 의존성 설치
composer install

# 환경 설정
cp .env.example .env
php artisan key:generate

# 데이터베이스 설정
php artisan migrate --seed

# 서버 실행
php artisan serve
```

### Frontend (Next.js)

```bash
cd frontend

# 의존성 설치
npm install

# 개발 서버 실행
npm run dev
```

프론트엔드: http://localhost:3000
백엔드: http://localhost:8000

## 📦 배포

자세한 배포 가이드는 [DEPLOYMENT.md](./DEPLOYMENT.md)를 참조하세요.

### Vercel 배포 (권장)

1. GitHub 저장소 연결
2. Root Directory를 `frontend`로 설정
3. 환경 변수 설정
4. Deploy 클릭

## 🔑 환경 변수

### Frontend
```env
NEXT_PUBLIC_API_URL=your-backend-api-url
NEXT_PUBLIC_FIREBASE_API_KEY=your-firebase-key
NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=your-stripe-key
```

### Backend
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=owlesim
DB_USERNAME=root
DB_PASSWORD=

AIRALO_API_KEY=your-airalo-key
STRIPE_SECRET=your-stripe-secret
```

## 📱 주요 기능

- ✅ 다국어 지원 (한국어, 영어, 중국어, 일본어 등)
- ✅ eSIM 패키지 검색 및 구매
- ✅ 다양한 결제 수단 (Stripe, Razorpay, Cashfree)
- ✅ 사용자 대시보드 및 주문 내역
- ✅ 관리자 패널
- ✅ 실시간 알림
- ✅ KYC 인증
- ✅ 지원 티켓 시스템

## 📄 License

MIT License - 자세한 내용은 [LICENSE](LICENSE) 파일을 참조하세요.
