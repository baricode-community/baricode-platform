pipeline {
    agent any

    stages {
        stage('Hello') {
            steps {
                echo 'Hello World'
            }
        }
        
        stage('List Files') {
            steps {
                sh 'ls -la'
            }
        }

        
        stage('Build with Node.js') {
            steps {
                nodejs('24.8.0') {
                    sh 'node --version'
                    sh 'npm --version'
                    sh 'npm install'
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
