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

        stage('Git Checkout') {
            steps {
                sh 'git --version'
            }
        }

        stage('Goodbye') {
            steps {
                echo 'Goodbye World'
            }
        }
    }
}
