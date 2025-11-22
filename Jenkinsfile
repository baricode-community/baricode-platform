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
        stage('php') {
            agent { docker { image 'php:8.4.8-alpine3.22' } }
            steps {
                sh 'php --version'
            }
        }
    }
}
