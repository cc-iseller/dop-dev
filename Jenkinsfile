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
                withCredentials([usernamePassword(
            credentialsId: 'acr-admin',
            usernameVariable: 'ACR_USER',
            passwordVariable: 'ACR_PASS'
            )]) {
            bat '''
            echo %ACR_PASS% | "C:\\Program Files\\Docker\\Docker\\resources\\bin\\docker.exe" ^
              --config C:\\jenkins-docker-config ^
              login iselleracr.azurecr.io ^
              -u %ACR_USER% ^
              --password-stdin
            '''
            }
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
        stage('Azure Login') {
            steps {
        withCredentials([usernamePassword(
            credentialsId: 'azure-sp',
            usernameVariable: 'AZURE_CLIENT_ID',
            passwordVariable: 'AZURE_CLIENT_SECRET'
            )]) {
            bat '''
            "C:\\Program Files (x86)\\Microsoft SDKs\\Azure\\CLI2\\wbin\\az.cmd" login ^
              --service-principal ^
              -u %AZURE_CLIENT_ID% ^
              -p %AZURE_CLIENT_SECRET% ^
              --tenant <TENANT_ID>
            '''
                }
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
