pipeline {
    agent any
    
    environment {
        // Azure Container Registry credentials
        ACR_NAME = 'iselleracr'
        ACR_LOGIN_SERVER = "${ACR_NAME}.azurecr.io"
        ACR_CREDENTIALS_ID = 'acr-credentials'
        
        // Docker image configuration
        IMAGE_NAME = 'dop-dev'
        IMAGE_TAG = "${BUILD_NUMBER}"
        FULL_IMAGE_NAME = "${ACR_LOGIN_SERVER}/${IMAGE_NAME}:${IMAGE_TAG}"
        LATEST_IMAGE_NAME = "${ACR_LOGIN_SERVER}/${IMAGE_NAME}:latest"
        
        // Azure Web App configuration
        AZURE_WEBAPP_NAME = 'iseller-as'
        AZURE_RESOURCE_GROUP = 'cc-Iseller'
        AZURE_CREDENTIALS_ID = 'azure-service-principal'
        
        // Application configuration
        APP_ENV = 'production'
    }
    
    stages {
        stage('Checkout SCM') {
            steps {
                script {
                    echo 'Checking out source code...'
                    checkout scm
                    
                    // Display commit information
                    powershell '''
                        Write-Host "Current Branch: $(git rev-parse --abbrev-ref HEAD)"
                        Write-Host "Latest Commit: $(git log -1 --pretty=format:'%h - %s (%an)')"
                    '''
                }
            }
        }
        
        stage('Prepare Environment') {
            steps {
                script {
                    echo 'Preparing build environment...'
                    
                    // Create .env file from .env.example if needed
                    powershell '''
                        if (!(Test-Path .env)) {
                            Write-Host "Creating .env file from .env.example..."
                            Copy-Item .env.example .env
                        }
                        
                        # Verify required files
                        $requiredFiles = @('Dockerfile', 'composer.json', 'package.json')
                        foreach ($file in $requiredFiles) {
                            if (!(Test-Path $file)) {
                                Write-Error "Required file $file not found!"
                                exit 1
                            }
                        }
                        
                        Write-Host "Environment preparation completed successfully"
                    '''
                }
            }
        }
        
        stage('Build Docker Image') {
            steps {
                script {
                    echo "Building Docker image: ${FULL_IMAGE_NAME}"
                    
                    powershell """
                        Write-Host "Building Docker image with tag: ${env.IMAGE_TAG}..."
                        
                        docker build `
                            --build-arg BUILD_DATE=\$(Get-Date -Format 'yyyy-MM-ddTHH:mm:ssZ') `
                            --build-arg VCS_REF=\$(git rev-parse --short HEAD) `
                            --build-arg BUILD_NUMBER=${env.BUILD_NUMBER} `
                            -t ${env.FULL_IMAGE_NAME} `
                            -t ${env.LATEST_IMAGE_NAME} `
                            .
                        
                        if (\$LASTEXITCODE -ne 0) {
                            Write-Error "Docker build failed!"
                            exit 1
                        }
                        
                        Write-Host "Docker image built successfully"
                    """
                }
            }
        }
        
        stage('Test Image') {
            steps {
                script {
                    echo 'Testing Docker image...'
                    
                    powershell """
                        Write-Host "Starting container for testing..."
                        
                        # Run container in detached mode
                        \$containerId = docker run -d `
                            -e APP_ENV=testing `
                            -e APP_KEY=base64:test1234567890123456789012345678901234567890123= `
                            ${env.FULL_IMAGE_NAME}
                        
                        if (\$LASTEXITCODE -ne 0) {
                            Write-Error "Failed to start container!"
                            exit 1
                        }
                        
                        Write-Host "Container ID: \$containerId"
                        
                        # Wait for container to be ready
                        Start-Sleep -Seconds 10
                        
                        # Check if container is running
                        \$containerStatus = docker inspect -f '{{.State.Running}}' \$containerId
                        
                        if (\$containerStatus -ne 'true') {
                            Write-Host "Container logs:"
                            docker logs \$containerId
                            docker rm -f \$containerId
                            Write-Error "Container is not running!"
                            exit 1
                        }
                        
                        Write-Host "Checking PHP version..."
                        docker exec \$containerId php -v
                        
                        Write-Host "Checking Nginx status..."
                        docker exec \$containerId nginx -t
                        
                        Write-Host "Checking Laravel installation..."
                        docker exec \$containerId php artisan --version
                        
                        Write-Host "Checking Filament installation..."
                        docker exec \$containerId php artisan about
                        
                        # Clean up test container
                        Write-Host "Stopping and removing test container..."
                        docker stop \$containerId
                        docker rm \$containerId
                        
                        Write-Host "Image testing completed successfully"
                    """
                }
            }
        }
        
        stage('Login to ACR') {
            steps {
                script {
                    echo "Logging in to Azure Container Registry: ${ACR_LOGIN_SERVER}"
                    
                    withCredentials([usernamePassword(
                        credentialsId: "${ACR_CREDENTIALS_ID}",
                        usernameVariable: 'ACR_USERNAME',
                        passwordVariable: 'ACR_PASSWORD'
                    )]) {
                        powershell '''
                            Write-Host "Authenticating with ACR..."
                            
                            echo $env:ACR_PASSWORD | docker login $env:ACR_LOGIN_SERVER `
                                --username $env:ACR_USERNAME `
                                --password-stdin
                            
                            if ($LASTEXITCODE -ne 0) {
                                Write-Error "ACR login failed!"
                                exit 1
                            }
                            
                            Write-Host "Successfully logged in to ACR"
                        '''
                    }
                }
            }
        }
        
        stage('Push to ACR') {
            steps {
                script {
                    echo "Pushing images to Azure Container Registry..."
                    
                    powershell """
                        Write-Host "Pushing image with tag ${env.IMAGE_TAG}..."
                        docker push ${env.FULL_IMAGE_NAME}
                        
                        if (\$LASTEXITCODE -ne 0) {
                            Write-Error "Failed to push image with tag ${env.IMAGE_TAG}!"
                            exit 1
                        }
                        
                        Write-Host "Pushing latest tag..."
                        docker push ${env.LATEST_IMAGE_NAME}
                        
                        if (\$LASTEXITCODE -ne 0) {
                            Write-Error "Failed to push latest image!"
                            exit 1
                        }
                        
                        Write-Host "Images pushed successfully to ACR"
                    """
                }
            }
        }
        
        stage('Deploy to Azure Web App') {
            steps {
                script {
                    echo "Deploying to Azure Web App: ${AZURE_WEBAPP_NAME}"
                    
                    withCredentials([azureServicePrincipal("${AZURE_CREDENTIALS_ID}")]) {
                        powershell """
                            Write-Host "Logging in to Azure..."
                            
                            az login --service-principal `
                                --username \$env:AZURE_CLIENT_ID `
                                --password \$env:AZURE_CLIENT_SECRET `
                                --tenant \$env:AZURE_TENANT_ID
                            
                            if (\$LASTEXITCODE -ne 0) {
                                Write-Error "Azure login failed!"
                                exit 1
                            }
                            
                            Write-Host "Configuring Web App container settings..."
                            
                            az webapp config container set `
                                --name ${env.AZURE_WEBAPP_NAME} `
                                --resource-group ${env.AZURE_RESOURCE_GROUP} `
                                --docker-custom-image-name ${env.FULL_IMAGE_NAME} `
                                --docker-registry-server-url https://${env.ACR_LOGIN_SERVER} `
                                --docker-registry-server-user \$env:AZURE_CLIENT_ID `
                                --docker-registry-server-password \$env:AZURE_CLIENT_SECRET
                            
                            if (\$LASTEXITCODE -ne 0) {
                                Write-Error "Failed to configure container settings!"
                                exit 1
                            }
                            
                            Write-Host "Restarting Web App..."
                            az webapp restart `
                                --name ${env.AZURE_WEBAPP_NAME} `
                                --resource-group ${env.AZURE_RESOURCE_GROUP}
                            
                            if (\$LASTEXITCODE -ne 0) {
                                Write-Error "Failed to restart Web App!"
                                exit 1
                            }
                            
                            Write-Host "Deployment completed successfully"
                            
                            # Logout from Azure
                            az logout
                        """
                    }
                }
            }
        }
        
        stage('Health Check') {
            steps {
                script {
                    echo 'Performing health check on deployed application...'
                    
                    powershell """
                        \$appUrl = "https://${env.AZURE_WEBAPP_NAME}.azurewebsites.net"
                        \$maxAttempts = 12
                        \$attemptInterval = 10
                        \$healthy = \$false
                        
                        Write-Host "Waiting for application to be ready at \$appUrl"
                        
                        for (\$i = 1; \$i -le \$maxAttempts; \$i++) {
                            Write-Host "Health check attempt \$i of \$maxAttempts..."
                            
                            try {
                                \$response = Invoke-WebRequest -Uri \$appUrl -TimeoutSec 10 -UseBasicParsing
                                
                                if (\$response.StatusCode -eq 200) {
                                    Write-Host "✓ Application is healthy! Status Code: \$(\$response.StatusCode)"
                                    \$healthy = \$true
                                    break
                                }
                            } catch {
                                Write-Host "✗ Health check failed: \$(\$_.Exception.Message)"
                            }
                            
                            if (\$i -lt \$maxAttempts) {
                                Write-Host "Waiting \$attemptInterval seconds before next attempt..."
                                Start-Sleep -Seconds \$attemptInterval
                            }
                        }
                        
                        if (-not \$healthy) {
                            Write-Error "Application health check failed after \$maxAttempts attempts!"
                            exit 1
                        }
                        
                        Write-Host "Health check completed successfully"
                        Write-Host "Application URL: \$appUrl"
                    """
                }
            }
        }
        
        stage('Clean Up') {
            steps {
                script {
                    echo 'Cleaning up local Docker images...'
                    
                    powershell """
                        Write-Host "Removing local Docker images..."
                        
                        # Remove the specific build tag
                        docker rmi ${env.FULL_IMAGE_NAME} -f 2>&1 | Out-Null
                        
                        # Keep the latest tag for faster subsequent builds
                        # docker rmi ${env.LATEST_IMAGE_NAME} -f
                        
                        # Clean up dangling images
                        Write-Host "Removing dangling images..."
                        \$danglingImages = docker images -f "dangling=true" -q
                        
                        if (\$danglingImages) {
                            docker rmi \$danglingImages -f 2>&1 | Out-Null
                            Write-Host "Dangling images removed"
                        } else {
                            Write-Host "No dangling images found"
                        }
                        
                        # Prune build cache (optional - uncomment if needed)
                        # Write-Host "Pruning build cache..."
                        # docker builder prune -f
                        
                        Write-Host "Docker cleanup completed"
                    """
                }
            }
        }
    }
    
    post {
        success {
            echo '✓ Pipeline completed successfully!'
            powershell """
                Write-Host "========================================" -ForegroundColor Green
                Write-Host "   DEPLOYMENT SUCCESSFUL" -ForegroundColor Green
                Write-Host "========================================" -ForegroundColor Green
                Write-Host "Build Number: ${env.BUILD_NUMBER}"
                Write-Host "Image Tag: ${env.IMAGE_TAG}"
                Write-Host "Application URL: https://${env.AZURE_WEBAPP_NAME}.azurewebsites.net"
                Write-Host "========================================"
            """
        }
        
        failure {
            echo '✗ Pipeline failed!'
            powershell """
                Write-Host "========================================" -ForegroundColor Red
                Write-Host "   DEPLOYMENT FAILED" -ForegroundColor Red
                Write-Host "========================================" -ForegroundColor Red
                Write-Host "Build Number: ${env.BUILD_NUMBER}"
                Write-Host "Please check the logs for details"
                Write-Host "========================================"
            """
        }
        
        always {
            echo 'Performing final cleanup...'
            powershell '''
                # Logout from Docker registries
                docker logout 2>&1 | Out-Null
                
                Write-Host "Final cleanup completed"
            '''
        }
    }
}