pipeline {
    agent any

    environment {
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
                powershell '''
                docker --version
                az --version
                '''
            }
        }

        stage('Build Docker Image') {
            steps {
                powershell '''
                docker build -t $env:ACR_LOGIN_SERVER/$env:IMAGE_NAME:$env:IMAGE_TAG .
                '''
            }
        }

        stage('Test Image') {
            steps {
                powershell '''
                docker run -d -p 8080:80 --name test-container `
                  $env:ACR_LOGIN_SERVER/$env:IMAGE_NAME:$env:IMAGE_TAG

                Start-Sleep -Seconds 10
                docker ps
                docker rm -f test-container
                '''
            }
        }

        stage('Login to ACR') {
            steps {
                powershell '''
                az acr login --name $env:ACR_NAME
                '''
            }
        }

        stage('Push Image to ACR') {
            steps {
                powershell '''
                docker push $env:ACR_LOGIN_SERVER/$env:IMAGE_NAME:$env:IMAGE_TAG
                '''
            }
        }

        stage('Deploy to Azure Web App') {
            steps {
                powershell '''
                az webapp config container set `
                  --resource-group $env:AZ_RESOURCE_GROUP `
                  --name $env:AZ_WEBAPP_NAME `
                  --docker-custom-image-name $env:ACR_LOGIN_SERVER/$env:IMAGE_NAME:$env:IMAGE_TAG `
                  --docker-registry-server-url https://$env:ACR_LOGIN_SERVER
                '''
            }
        }

        stage('Health Check') {
            steps {
                powershell '''
                Start-Sleep -Seconds 20
                Invoke-WebRequest http://$env:AZ_WEBAPP_NAME.azurewebsites.net -UseBasicParsing
                '''
            }
        }

        stage('Clean Up Local Docker Images') {
            steps {
                powershell '''
                docker rmi $env:ACR_LOGIN_SERVER/$env:IMAGE_NAME:$env:IMAGE_TAG -f
                docker system prune -f
                '''
            }
        }
    }
}
