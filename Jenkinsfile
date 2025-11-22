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
                sh '''
                  # buat cache npm di dalam workspace agar tidak menulis ke /.npm (root)
                  CACHE_DIR="${WORKSPACE}/.npm"
                  mkdir -p "$CACHE_DIR"
                  # pastikan dapat ditulis; chmod 0777 aman untuk direktori cache
                  chmod 0777 "$CACHE_DIR" || true
                  export NPM_CONFIG_CACHE="$CACHE_DIR"

                  rm -rf node_modules
                  npm --version
                  node --version
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
