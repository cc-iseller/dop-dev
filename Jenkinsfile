pipeline {
    agent any

    environment {
        // =========================
        // ABSOLUTE PATH (WAJIB)
        // =========================
        DOCKER_CLI = "C:\\Program Files\\Docker\\Docker\\resources\\bin\\docker.exe"
        AZ_CLI     = "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az.cmd"

        // =========================
        // FORCE DISABLE WINCRED
        // =========================
        DOCKER_CFG = "C:\\jenkins-docker-config"

        // =========================
        // IMAGE CONFIG
        // =========================
        ACR_NAME = "iselleracr"
        ACR_LOGIN_SERVER = "iselleracr.azurecr.io"

        IMAGE_NAME = "dop-dev"
        IMAGE_TAG  = "${BUILD_NUMBER}"

        // =========================
        // AZURE WEB APP
        // =========================
        AZ_RESOURCE_GROUP = "cc-Iseller"
        AZ_WEBAPP_NAME    = "iseller-as"
    }

    stages {

        // =================================================
        stage('Checkout SCM') {
            steps {
                checkout scm
            }
        }

        // =================================================
        stage('Prepare Docker Config (Disable Wincred)') {
            steps {
                bat """
                echo === PREPARE DOCKER CONFIG ===

                if exist "%DOCKER_CFG%" rmdir /s /q "%DOCKER_CFG%"
                mkdir "%DOCKER_CFG%"

                echo {^
                  "auths": {},^
                  "credsStore": "",^
                  "credHelpers": {}^
                } > "%DOCKER_CFG%\\config.json"

                type "%DOCKER_CFG%\\config.json"
                """
            }
        }

        // =================================================
        stage('Verify Tools') {
            steps {
                bat """
                echo === CHECK DOCKER ===
                "%DOCKER_CLI%" --config %DOCKER_CFG% version

                echo === CHECK AZ CLI ===
                "%AZ_CLI%" version
                """
            }
        }

        // =================================================
        stage('Build Docker Image') {
            steps {
                bat """
                echo === BUILD DOCKER IMAGE ===
                "%DOCKER_CLI%" --config %DOCKER_CFG% build ^
                  -t %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% .
                """
            }
        }

        // =================================================
        stage('Test Docker Image') {
            steps {
                bat """
                echo === TEST DOCKER IMAGE ===

                "%DOCKER_CLI%" --config %DOCKER_CFG% run -d ^
                  --name test-container -p 8080:80 ^
                  %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%

                timeout /t 10

                "%DOCKER_CLI%" --config %DOCKER_CFG% ps

                "%DOCKER_CLI%" --config %DOCKER_CFG% rm -f test-container
                """
            }
        }

        // =================================================
        stage('Login to ACR') {
            steps {
                bat """
                echo === LOGIN ACR ===
                "%AZ_CLI%" acr login --name %ACR_NAME%
                """
            }
        }

        // =================================================
        stage('Push Image to ACR') {
            steps {
                bat """
                echo === PUSH IMAGE TO ACR ===
                "%DOCKER_CLI%" --config %DOCKER_CFG% push ^
                  %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%
                """
            }
        }

        // =================================================
        stage('Deploy to Azure Web App') {
            steps {
                bat """
                echo === DEPLOY TO AZURE WEB APP ===

                "%AZ_CLI%" webapp config container set ^
                  --resource-group %AZ_RESOURCE_GROUP% ^
                  --name %AZ_WEBAPP_NAME% ^
                  --docker-custom-image-name %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% ^
                  --docker-registry-server-url https://%ACR_LOGIN_SERVER%
                """
            }
        }

        // =================================================
        stage('Health Check') {
            steps {
                bat """
                echo === HEALTH CHECK ===
                timeout /t 25
                curl http://%AZ_WEBAPP_NAME%.azurewebsites.net
                """
            }
        }

        // =================================================
        stage('Cleanup Local Docker') {
            steps {
                bat """
                echo === CLEANUP DOCKER ===
                "%DOCKER_CLI%" --config %DOCKER_CFG% rmi ^
                  %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% -f

                "%DOCKER_CLI%" --config %DOCKER_CFG% system prune -f
                """
            }
        }
    }

    post {
        success {
            echo '✅ DEPLOY SUCCESS'
        }
        failure {
            echo '❌ DEPLOY FAILED'
        }
        always {
            echo 'Pipeline finished.'
        }
    }
}
