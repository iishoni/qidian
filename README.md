# qidian
一个php项目
1
os:centos7.3

-1. 安装apache、mysql
````
    yum -y install httpd httpd-manual \
    mariadb mariadb-server \
    php php-mysql php-mcrypt php-gd php-xml php-mbstring php-ldap php-pear php-xmlrpc \
````

-2. 修改apache配置
````
    vim /etc/httpd/conf/httpd.conf
    
    修改如下：
    #ServerName localhost:80 -> ServerName localhost:80
    
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
````

-3. 修改mysql配置
````
    mysql_secure_installation
    mysql -uroot -pxxx
    create database hzxx1109_db
    create user 'hzxx1109_f'@'%' identified by '2gQhKbz7pz0wn0O'
    grant all privileges on hzxx1109_db.* to 'hzxx1109_f'@'%' identified by '2gQhKbz7pz0wn0O' with grant option
    flush privileges
````

-4. 项目部署
````
    cp 项目 -> /var/www/html/
    cd /var/www/
    chmod 777 -R html
````

-5. 修改启动项
````
    systemctl start httpd
    systemctl enable httpd
    systemctl start mariadb
    systemctl enable mariadb
````
