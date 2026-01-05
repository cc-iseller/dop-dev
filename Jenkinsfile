pipeline {
    agent any

    environment {
        // ===== ABSOLUTE PATH (WAJIB DI WINDOWS JENKINS) =====
        DOCKER_CLI = "C:\\Program Files\\Docker\\Docker\\resources\\bin\\docker.exe"
        AZ_CLI     = "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az.cmd"
        POWERSHELL = "C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\powershell.exe"

        // ===== APP CONFIG =====
        ACR_NAME = "iselleracr"
        ACR_LOGIN_SERVER = "iselleracr.azurecr.io"
        IMAGE_NAME = "dop-dev"
        IMAGE_TAG = "${BUILD_NUMBER}"

        AZ_RESOURCE_GROUP = "cc-Iseller"
        AZ_WEBAPP_NAME = "iseller-as"
    }

    stages {

        stage('Checkout SCM') {
            steps {
                checkout scm
            }
        }

        stage('Prepare Environment') {
            steps {
                bat """
                echo === CHECK DOCKER ===
                "%DOCKER_CLI%" --version

                echo === CHECK AZURE CLI ===
                "%AZ_CLI%" --version
                """
            }
        }

        stage('Build Docker Image') {
            steps {
                bat """
                echo === BUILD DOCKER IMAGE ===
                "%DOCKER_CLI%" build -t %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% .
                """
            }
        }

        stage('Test Image') {
            steps {
                bat """
                echo === RUN CONTAINER TEST ===
                "%DOCKER_CLI%" run -d -p 8080:80 --name test-container ^
                  %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%

                timeout /t 10

                "%DOCKER_CLI%" ps

                "%DOCKER_CLI%" rm -f test-container
                """
            }
        }

        stage('Login to ACR') {
            steps {
                bat """
                echo === LOGIN TO ACR ===
                "%AZ_CLI%" acr login --name %ACR_NAME%
                """
            }
        }

        stage('Push Image to ACR') {
            steps {
                bat """
                echo === PUSH IMAGE TO ACR ===
                "%DOCKER_CLI%" push %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%
                """
            }
        }

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

        stage('Health Check') {
            steps {
                bat """
                echo === HEALTH CHECK ===
                timeout /t 20

                "%POWERSHELL%" -NoProfile -Command ^
                  "Invoke-WebRequest http://%AZ_WEBAPP_NAME%.azurewebsites.net -UseBasicParsing"
                """
            }
        }

        stage('Clean Up Local Docker Images') {
            steps {
                bat """
                echo === CLEAN UP LOCAL IMAGES ===
                "%DOCKER_CLI%" rmi %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% -f
                "%DOCKER_CLI%" system prune -f
                """
            }
        }
    }
}
