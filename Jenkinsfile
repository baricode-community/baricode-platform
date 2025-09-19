pipeline {
    agent any

    stages {
        stage('Hello') {
            steps {
                echo 'Hello World'
            }
        }

        stage('Install PHP 8.4') {
            steps {
                sh '/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"'
                sh 'php -v'
            }
        }

        stage('Composer Install') {
            steps {
                sh 'composer install'
            }
        }
        
        stage('Build with Node.js') {
            steps {
                nodejs('24.8.0') {
                    sh 'npm install'
                    sh 'npm run build'
                }
            }
        }

        stage('Goodbye') {
            steps {
                echo 'Goodbye World'
            }
        }
    }
}
