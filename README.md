==================部署开始==================

1.正式环境系统：CentOS 7.4

2.安装Apache、Mysql（从centos7开始默认安装mariadb）
	
	yum -y install httpd httpd-manual mariadb mariadb-server php php-mysql php-mcrypt php-gd php-xml php-mbstring php-ldap php-pear php-xmlrpc

3.修改Apache配置
	
	vim /etc/httpd/conf/httpd.conf
    
   修改内容如下：
	
	#ServerName localhost:80 -> ServerName localhost:8080（去掉注释，改为8080端口）
	AllowOverride None -> AllowOverride All （支持url重写）
	<Directory "/var/www/html">
        // ...
        // 新增rewrite规则
        <IfModule rewrite_module>
            RewriteEngine On
            RewriteCond %{SCRIPT_FILENAME} !-f
            RewriteCond %{SCRIPT_FILENAME} !-d
            RewriteRule ^(.*)$ index.php?s=$1 [QSA,PT,L]
        </IfModule>
    </Directory>

4.修改mysql配置
    
    systemctl start mariadb
    systemctl enable mariadb
    mysql_secure_installation
    mysql -uroot -pxxx
    create database hzxx1109_db;
    create user 'hzxx1109_f'@'%' identified by '2gQhKbz7pz0wn0O';
    grant all privileges on hzxx1109_db.* to 'hzxx1109_f'@'%' identified by '2gQhKbz7pz0wn0O' with grant option;
    flush privileges;

5.导入数据库脚本：执行qidian.sql

6.安装配置nginx
    
    yum -y install nginx

    // 注释掉自带配置文件的server节点下的所有配置
    vim /etc/nginx/nginx.conf
    
   修改配置文件，内容如下：（需要生成ssl证书）
    
    server {
        listen          80;
        server_name     www.xxx.com;

        rewrite  ^(.*)  https://$server_name$1 permanent;

        #location ^~ /.well-known/acme-challenge/ {
        #       alias /certs/;
        #       try_files $uri =404;
        #}
    }
        
    server {
        listen              443 ssl;
        server_name         www.xxx.com;
        root                /var/www/html;
        index               index.php;
        
        ssl on;
        ssl_certificate     /etc/nginx/ssl/chained.pem;
        ssl_certificate_key /etc/nginx/ssl/domain.key;

        client_max_body_size 10M;

        location / {
            proxy_pass      http://127.0.0.1:8080;
        }

        location /admin/ {
            proxy_pass      http://127.0.0.1:8080/qidian;
        }
    }

7.项目部署
    
    cp <项目根目录> /var/www/html
    cd /var/www/
    chmod 777 -R html

8.修改启动项

    systemctl start httpd
    systemctl enable httpd
    systemctl start nginx
    systemctl enable nginx
    
    
9.定时任务配置
    
    crontab -e
    */15 * * * * /usr/bin/php -f /var/www/html/chaifen/weekwork.php

==================部署结束==================

