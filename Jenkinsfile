pipeline {
    agent none
    
    stages {
        stage('composer') {
            agent { docker { image 'composer' } }
            steps {
                sh 'ls -a'
                sh 'composer install --ignore-platform-req=ext-intl'
            }
        }
        stage('node') {
            agent { docker { image 'node:alpine3.21' } }
            steps {
                sh 'npm --version'
                sh 'node --version'
                sh 'npm install'
            }
        }
        stage('php') {
            agent { docker { image 'php:8.4.8-alpine3.22' } }
            steps {
                sh 'php --version'
            }
        }
    }
}
