# 🔗 OWLeSIM GitHub 연결 가이드

## 방법 1: Vercel 대시보드를 통한 자동 연결 (가장 쉬움! ⭐)

1. **Vercel 대시보드 접속**
   - https://vercel.com/sssmario-4950s-projects/frontend

2. **Settings → Git 탭으로 이동**

3. **"Connect Git Repository" 클릭**
   - GitHub 연결 선택
   - 새 저장소 생성 또는 기존 저장소 선택
   - Vercel이 자동으로 코드를 푸시하고 CI/CD 설정

4. **완료!** 
   - 이후 Git push할 때마다 자동 배포됩니다

---

## 방법 2: 수동으로 GitHub 저장소 생성 및 푸시

### 1단계: GitHub 저장소 생성
1. https://github.com/new 접속
2. Repository name: `owlesim` 입력
3. Public/Private 선택
4. ⚠️ **중요**: README, .gitignore, license 체크 해제
5. "Create repository" 클릭

### 2단계: 로컬에서 푸시
저장소 생성 후 표시되는 URL을 복사하고:

```powershell
# PowerShell에서 실행
.\push-to-github.ps1 https://github.com/yourusername/owlesim.git
```

또는 수동으로:

```bash
git remote add origin https://github.com/yourusername/owlesim.git
git branch -M main
git push -u origin main
```

### 3단계: Vercel과 GitHub 연결
1. Vercel 프로젝트 Settings → Git
2. "Connect Git Repository" 
3. 방금 생성한 저장소 선택

---

## ✅ 현재 상태

- ✅ Vercel 배포 완료
- ✅ Production URL: https://frontend-i33100g2a-sssmario-4950s-projects.vercel.app
- ⏳ GitHub 연결 대기 중

## 🔄 자동 배포 (GitHub 연결 후)

GitHub 저장소와 연결하면:
- `git push` → 자동으로 Vercel 배포
- Pull Request → Preview 배포 자동 생성
- Merge → Production 자동 업데이트

