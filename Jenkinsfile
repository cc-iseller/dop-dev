pipeline {
    agent any

    environment {
        // 1. PATHS (WAJIB: Agar Jenkins Windows bisa menemukan Git, Docker, dan Azure CLI)
        GIT_PATH    = "C:\\Program Files\\Git\\bin"
        DOCKER_PATH = "C:\\Program Files\\Docker\\Docker\\resources\\bin"
        AZ_PATH     = "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin"
        PATH        = "${GIT_PATH};${DOCKER_PATH};${AZ_PATH};${env.PATH}"

        // 2. CONFIGURATION
        ACR_NAME         = "iselleracr"
        ACR_LOGIN_SERVER = "iselleracr.azurecr.io"
        IMAGE_NAME       = "dop-dev"
        IMAGE_TAG        = "${BUILD_NUMBER}"
        
        AZ_RESOURCE_GROUP = "cc-Iseller"
        AZ_WEBAPP_NAME   = "iseller-as"
    }

    stages {
        stage('Checkout SCM') {
            steps {
                echo "--- MENGAMBIL KODE DARI REPOSITORY ---"
                checkout scm
            }
        }

        stage('Prepare & Validate') {
            steps {
                bat """
                @echo off
                echo === CEK LINGKUNGAN ===
                where git || (echo Git tidak ditemukan && exit 1)
                where docker || (echo Docker tidak ditemukan && exit 1)
                where az || (echo Azure CLI tidak ditemukan && exit 1)
                
                echo === VERSI ===
                docker --version
                az --version
                """
            }
        }

        stage('Build Docker Image') {
            steps {
                echo "--- MEMULAI BUILD IMAGE (ALPINIZED) ---"
                // Menggunakan Dockerfile yang sudah diperbaiki (pakai apk, bukan apt-get)
                bat "docker build -t %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% ."
            }
        }

        stage('Local Smoke Test') {
            steps {
                echo "--- MENJALANKAN UJI COBA CONTAINER LOKAL ---"
                bat """
                @echo off
                docker run -d -p 8080:80 --name test_app_%BUILD_NUMBER% %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%
                
                echo Menunggu container startup...
                timeout /t 15 /nobreak
                
                echo Mengecek status HTTP...
                powershell -Command "try { \$r = Invoke-WebRequest -Uri http://localhost:8080 -UseBasicParsing; echo \$r.StatusCode } catch { exit 1 }"
                
                echo Membersihkan container uji coba...
                docker rm -f test_app_%BUILD_NUMBER%
                """
            }
        }

        stage('Login to ACR') {
            steps {
                echo "--- LOGIN KE AZURE CONTAINER REGISTRY ---"
                // Pastikan Jenkins Service sudah login ke Azure (az login) satu kali di server
                bat "az acr login --name %ACR_NAME%"
            }
        }

        stage('Push Image to ACR') {
            steps {
                echo "--- PUSH IMAGE KE CLOUD ---"
                bat "docker push %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%"
            }
        }

        stage('Deploy to Azure Web App') {
            steps {
                echo "--- UPDATE CONTAINER DI AZURE WEB APP ---"
                bat """
                az webapp config container set ^
                    --resource-group %AZ_RESOURCE_GROUP% ^
                    --name %AZ_WEBAPP_NAME% ^
                    --docker-custom-image-name %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% ^
                    --docker-registry-server-url https://%ACR_LOGIN_SERVER%
                """
            }
        }

        stage('Final Health Check') {
            steps {
                echo "--- VERIFIKASI DEPLOYMENT LIVE ---"
                bat """
                @echo off
                echo Menunggu Azure melakukan swapping container...
                timeout /t 30 /nobreak
                powershell -Command "Invoke-WebRequest -Uri https://%AZ_WEBAPP_NAME%.azurewebsites.net -UseBasicParsing | Select-Object -ExpandProperty StatusCode"
                """
            }
        }
    }

    post {
        success {
            echo "Deployment Berhasil!"
        }
        failure {
            echo "Deployment Gagal. Cek log di atas."
        }
        always {
            echo "--- CLEANING UP ---"
            bat """
            @echo off
            docker rmi %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% -f
            docker image prune -f --filter "until=24h"
            """
        }
    }
}