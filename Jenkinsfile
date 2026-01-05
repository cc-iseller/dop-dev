pipeline {
    agent any

    environment {
        ACR_NAME = "iselleracr"
        ACR_LOGIN_SERVER = "iselleracr.azurecr.io"
        IMAGE_NAME = "dop-dev"
        IMAGE_TAG = "${BUILD_NUMBER}"

        AZ_RESOURCE_GROUP = "cc-Iseller"
        AZ_WEBAPP_NAME = "iseller-as"

        POWERSHELL = "C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\powershell.exe"
        AZ_CLI = "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az.cmd"
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
                "%POWERSHELL%" -NoProfile -Command "docker --version"
                "%POWERSHELL%" -NoProfile -Command "& '%AZ_CLI%' --version"
                """
            }
        }

        stage('Build Docker Image') {
            steps {
                bat """
                docker build -t %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% .
                """
            }
        }

        stage('Test Image') {
            steps {
                bat """
                docker run -d -p 8080:80 --name test-container ^
                  %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%

                timeout /t 10
                docker ps
                docker rm -f test-container
                """
            }
        }

        stage('Login to ACR') {
            steps {
                bat """
                "%AZ_CLI%" acr login --name %ACR_NAME%
                """
            }
        }

        stage('Push Image to ACR') {
            steps {
                bat """
                docker push %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG%
                """
            }
        }

        stage('Deploy to Azure Web App') {
            steps {
                bat """
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
                timeout /t 20
                "%POWERSHELL%" -NoProfile -Command "Invoke-WebRequest http://%AZ_WEBAPP_NAME%.azurewebsites.net -UseBasicParsing"
                """
            }
        }

        stage('Clean Up Local Docker Images') {
            steps {
                bat """
                docker rmi %ACR_LOGIN_SERVER%/%IMAGE_NAME%:%IMAGE_TAG% -f
                docker system prune -f
                """
            }
        }
    }
}
