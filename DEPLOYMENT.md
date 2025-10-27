# 🚀 배포 가이드

## Prerequisites
- Node.js 18+ 설치
- Git 설치
- GitHub 계정
- Vercel 계정

## 1. GitHub 저장소 푸시

```bash
# GitHub에 새 저장소 생성 후
git remote add origin https://github.com/yourusername/owlesim.git
git branch -M main
git push -u origin main
```

## 2. Vercel 배포

### Option A: Vercel Dashboard (권장)
1. [Vercel 대시보드](https://vercel.com/new) 접속
2. "Import Project" 클릭
3. GitHub 저장소 선택 (`owlesim`)
4. 프로젝트 설정:
   - **Framework Preset**: Next.js
   - **Root Directory**: `frontend`
   - **Build Command**: `npm run build`
   - **Output Directory**: `.next`
   - **Install Command**: `npm install`
5. "Deploy" 클릭

### Option B: Vercel CLI
```bash
# Vercel CLI 설치
npm i -g vercel

# 프로젝트 디렉토리에서
cd frontend
vercel

# 배포 설정
# - Set up and deploy? → Yes
# - Which scope? → 본인 계정 선택
# - Link to existing project? → No
# - Project name? → owlesim (또는 원하는 이름)
# - In which directory is your code located? → ./
```

## 3. 환경 변수 설정

Vercel 대시보드에서 다음 환경 변수를 설정하세요:

### Frontend 환경 변수 (.env.local)
```env
# API
NEXT_PUBLIC_API_URL=your-backend-api-url
NEXT_PUBLIC_API_BASE_URL=your-backend-base-url

# Firebase (if applicable)
NEXT_PUBLIC_FIREBASE_API_KEY=your-firebase-api-key
NEXT_PUBLIC_FIREBASE_AUTH_DOMAIN=your-auth-domain
NEXT_PUBLIC_FIREBASE_PROJECT_ID=your-project-id
NEXT_PUBLIC_FIREBASE_STORAGE_BUCKET=your-storage-bucket
NEXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID=your-sender-id
NEXT_PUBLIC_FIREBASE_APP_ID=your-app-id
NEXT_PUBLIC_FIREBASE_MEASUREMENT_ID=your-measurement-id

# Payment (if applicable)
NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=your-stripe-key
```

## 4. 배포 후 확인

배포가 완료되면:
- Vercel이 제공하는 URL로 접속 (예: `https://owlesim.vercel.app`)
- 빌드 로그 확인
- 도메인 설정 (선택 사항)

## 5. 커스텀 도메인 연결 (선택 사항)

1. Vercel 프로젝트 설정 → Domains
2. 도메인 추가
3. DNS 설정 업데이트
4. SSL 자동 적용 확인

## 6. 지속적 배포 (CI/CD)

GitHub 저장소에 푸시할 때마다 Vercel이 자동으로 배포합니다:

```bash
# 변경 사항 커밋 및 푸시
git add .
git commit -m "Update: your changes"
git push origin main
```

## 문제 해결

### 빌드 오류
- Vercel 대시보드의 Build Logs 확인
- 로컬에서 `npm run build` 테스트
- Node.js 버전 확인 (package.json의 engines 설정)

### 환경 변수
- Vercel 설정에서 모든 필요한 환경 변수가 설정되었는지 확인
- 환경 변수 변경 후 재배포 필요

### API 연결 오류
- CORS 설정 확인
- API 엔드포인트 URL 확인
- 백엔드가 실행 중인지 확인

