# 베이스 이미지
FROM php:8.2-apache

# 필요한 php 확장 설치
RUN apt-get update && docker-php-ext-install pdo pdo_mysql

# 기본 디렉토리 경로 설정
WORKDIR /var/www/html/

# 로컬 php 파일 컨테이너로 복사
COPY . .

# 웹 서버가 파일을 읽을 수 있게.
RUN chown -R www-data:www-data /var/www/html && chmod -R 777 /var/www/html

# 아파치 기본 포트 설정
EXPOSE 80
# CMD ["apache2-foreground"]