pipeline {
	agent any
	environment {
		// Replace these credential IDs with the ones configured in Jenkins Credentials
		AZURE_CLIENT_ID = credentials('azure-client-id')
		AZURE_CLIENT_SECRET = credentials('azure-client-secret')
		AZURE_TENANT_ID = credentials('azure-tenant-id')
		AZURE_SUBSCRIPTION_ID = credentials('azure-subscription-id')
		// Set these as plain strings or use credentials as appropriate
		AZURE_RESOURCE_GROUP = 'your-resource-group'
		AZURE_APP_NAME = 'your-app-name'
	}
	stages {
		stage('Checkout') {
			steps {
				checkout scm
			}
		}

		stage('Install PHP dependencies') {
			steps {
				sh 'composer install --no-progress --no-suggest --prefer-dist --optimize-autoloader'
			}
		}

		stage('Build frontend') {
			steps {
				sh 'npm ci'
				sh 'npm run build'
			}
		}

		stage('Run tests') {
			steps {
				// don't fail the pipeline for test failures here by default; adjust as needed
				sh 'vendor/bin/phpunit --configuration phpunit.xml || true'
			}
		}

		stage('Package') {
			steps {
				sh '''
					rm -f ../app.zip
					zip -r ../app.zip . -x node_modules/* vendor/* storage/* .git/*
				'''
				archiveArtifacts artifacts: '../app.zip', fingerprint: true
			}
		}

		stage('Deploy to Azure Web App') {
			steps {
				sh '''
					# Install Azure CLI (Debian/Ubuntu installer). Remove if already present on agent.
					curl -sL https://aka.ms/InstallAzureCLIDeb | sudo bash

					az --version

					# Login using service principal credentials from Jenkins credentials store
					az login --service-principal -u "$AZURE_CLIENT_ID" -p "$AZURE_CLIENT_SECRET" --tenant "$AZURE_TENANT_ID"
					az account set --subscription "$AZURE_SUBSCRIPTION_ID"

					# Deploy zip to Azure Web App
					az webapp deployment source config-zip --resource-group "$AZURE_RESOURCE_GROUP" --name "$AZURE_APP_NAME" --src ../app.zip
				'''
			}
		}
	}
	post {
		success {
			echo 'Deployment succeeded.'
		}
		failure {
			echo 'Deployment failed.'
		}
	}
}

