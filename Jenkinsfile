pipeline {
    agent none

    stages {
        stage('composer') {
            agent { docker { image 'composer' } }
            steps {
                sh 'composer install --ignore-platform-req=ext-intl'
            }
        }
        stage('node') {
            agent { docker { image 'node:alpine3.21' } }
            steps {
                sh '''
                  export NPM_CONFIG_CACHE="${WORKSPACE}/.npm"
                  npm install
                  npm run build
                '''
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
