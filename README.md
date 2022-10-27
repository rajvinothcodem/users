# Docker instance to create and run sample php login and register form using redis and mysql

1. Run docker-compose build
2. Run docker-compose up
3. After all the containers up, RUN redis-server in apache terminal
4. Setup 127.0.0.1::1 testapp.local in /etc/hosts file
5. RUN http://testapp.local in the browser
