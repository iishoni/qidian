==================部署开始==================

1.正式环境系统：CentOS 7.4

2.安装Apache、Mysql（从centos7开始默认安装mariadb）
	
	yum -y install httpd httpd-manual mariadb mariadb-server php php-mysql php-mcrypt php-gd php-xml php-mbstring php-ldap php-pear php-xmlrpc

3.修改Apache配置
	
	vim /etc/httpd/conf/httpd.conf
    
   修改内容如下：
	
	#ServerName localhost:80 -> ServerName localhost:80	（去掉注释）
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
    
    mysql_secure_installation
    mysql -uroot -pxxx
    create database hzxx1109_db
    create user 'hzxx1109_f'@'%' identified by '2gQhKbz7pz0wn0O'
    grant all privileges on hzxx1109_db.* to 'hzxx1109_f'@'%' identified by '2gQhKbz7pz0wn0O' with grant option
    flush privileges

5.导入数据库脚本：执行qidian.sql

6.安装配置nginx
    
    yum -y install nginx

    // 注释掉自带配置文件的server节点下的所有配置
    cd /etc/nginx/nginx.conf
    
    cd /etc/nginx/conf.d/
    
   新增qidian配置文件，内容如下：（此处由于80端口被Apache占用，改用https的443端口，需要生成ssl证书）
    	
    server {
        listen              443 ssl;

        ssl on;
        ssl_certificate     /etc/nginx/ssl/chained.pem;
        ssl_certificate_key /etc/nginx/ssl/domain.key;

        server_name         letstart.vip www.letstart.vip letstart.cc www.letstart.cc;
        root                /var/www/html;
        index               index.php;

        client_max_body_size 10M;

        location / {
            proxy_pass      http://127.0.0.1;
        }

        location /admin123/ {
            proxy_pass      http://127.0.0.1/qidian;
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
    systemctl start mariadb
    systemctl enable mariadb
    
9.定时任务配置
    
    crontab -e
    */15 * * * * /usr/bin/php -f /var/www/html/chaifen/weekwork.php

==================部署结束==================

