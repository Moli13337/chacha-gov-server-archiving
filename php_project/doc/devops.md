# 运维F&Q

### 服务划分
- 前端: 用户端(user_pc)
- 前端：运营端(operation_pc)
- 后端：后端应用服务backend
- 后端基础服务：
    - 短信服务(sms)
    - pdf服务(pdf)
    - 上传服务(upload)
    - 地理服务(location)

### 开发语言
- php7.2，采用laravel5.5
- javascipt  
    - operation_pc 采用 vue 2.6.10 和element-ui 2.9.2
    - user_pc 采用nuxt 2.4.0
 
### 代码管理
- 搭建私有gitlab库
- 采用gitflow方式管理分支
    - dev 开发分支
    - qa 测试分支
    - prod 生成分支
    - hostfix_* 线上bug处理分支
    - feature_* 功能分支
- 代码分权限管理

### 依赖数据库
mysql5.7以上的云服务或者自建数据库

### 依赖第三方服务
- 身份证识别（腾讯）
- 营业执照识别（腾讯）
- 发票识别（腾讯）
- 发票真伪识别(阿里云）
- 文件服务(阿里云）
- 短信服务（阿里云）
- 企查查
- 内容过滤（网易易盾）

### 业务日志记录
- 日志记录在项目目录下的```  /storage/logs ```下
- 日志以laravel+日期 作为文件名，如: laravel-2019-12-24.log
- 日志记录记录了运行时的异常，包含：
    - 接口参数传入异常、
    - 接口参数不符合接口约定
    - 运行时的异常，数组 或者对象的key不存在
    - 数据库异常
    - 等等

### 性能调优
- 合理创建mysql索引
- 引入搜素引擎elasticsearch,提高查询效率
- laravel优化
    - 缓存配置信息 ```php artisan config:cache```  对应的清除 ```php artisan config:clear ```
    - 缓存路由信息 ```php artisan router:cache``` 对应的清除 ```php artisan router:clear ```
    - 类映射加载优化 ```php artsian optimize```
- composer自动加载优化 ```composer dumpautoload```
- php 开启opcache，在php.ini 里修改
   ```
   [opcache]
   opcache.enable=1 
   ```
- nginx优化
    - nginx的worker进程设置为cpu核数的2倍
    - nginx最大打开文件数, 设置 worker_rlimit_nofile
    - nginx时间处理模型，设置 worker_connections
- FastCGI 调优
    -  fastcgi_connect_timeout 600 指定连接到后端FastCGI的超时时间
    -  fastcgi_send_timeout 600 向FastCGI传送请求的超时时间
    -  fastcgi_read_timeout  600 指定接收FastCGI应答的超时时间。
- gzip 调优 ，使用gzip压缩，Nginx启用压缩功能需要你来ngx_http_gzip_module模块
- expries 缓存调优，针对 图片 css js 等改动少的开启缓存配置 ，如
  ```
  location ~* \.(ico|jpe?g|gif|png|bmp|swf|flv)$ {
      expires 30d;
      #log_not_found off;
      access_log off;
  }
   location ~* \.(js|css)$ {
       expires 7d;
       log_not_found off;
       access_log off;
   }
  ```
### 备份机制
- 每日02:00 自动备份数据库，并push到异地服务器上

### 监控报警
- 服务节点load值大于4
- 服务节点已用内存超过80%
- 有节点机器出故障（etcd，服务节点，master等）
- 服务节点cpu使用超过80%
- 节点硬盘使用率超过70%
- 磁盘io异常


### CD流程
1. jenkins发起任务
2. 从gitlab拉下代码
3. sonar代码扫描
4. 从etcd拉取配置生成配置文件。如.env
5. 安装依赖包composer install 或者 yarn install && yarn run build
6. 通过jenkins插件将打包为镜像，并上传到私有库
7. 执行shell：kubectl apply 新的deployment配置，让k8s上的pod更新

### 部署方案
1. 单机部署
    - Jenkins配置节点、机器的ssh信息、gitlab的密钥信息等
    - Jenkins配置任务，分为上下两级，按项目分组
    - 上级任务从gitlab拉代码，前后端分别执行相应构建命令，然后将结果文件打包成tar包
    - 下级任务由上级任务触发，传入tar包的URL和需要放置代码的路径，ssh到相应主机后拉tar包，解压到代码路径
    - 下级任务清理上级任务的工作目录，避免tar包太多占用磁盘
2. 集群部署
    - etcd 管理各个服务配置项
    - 创建各个服务的pod
    - web服务pod以deployment方式部署，计划任务以statefulset方式部署
    - 每个pod都用filebeat抓取日志，发送到elasticsearch
    - 集群日志用fluend发送到elasticsearch
    - kibana做日志展示
    - prometheus抓取监控指标
    - alertmanager 自动推送报警邮件，钉钉消息
    - grafana 做数据展示面板 
    
### 计划任务
采用开源cornsun的进行可视化管理，地址 https://github.com/shunfei/cronsun

### 爬取政策数据
- 初始阶段导入数据，后续增量数据通过接口获取（脚本任务定期获取）
    
### 遇见过的问题   
- composer intall/update超时
    - 私有库尚未获取到更新
    - github.com权限不够
    - 依赖包已移除，或废弃
    - 网络问题
    - composer process-timeout 时间过短

- VUE的nuxt项目Nginx配置
    - 前端user_pc是VUE的nuxt项目，在做反向代理时，router直接反向代理到内网机器，访问 http://user_pc.frontend.dev-wenjiang.heroera.com/butler 这样的链接，是不能正常打开的，敲回车后会跳到 http://user_pc.frontend.dev-wenjiang.heroera.com:8080/butler/
    - 在nginx的location 里加一句 try_files $uri $uri/ /; 结果如下
    ```
    server {
        listen 8080;
        server_name  user_pc.frontend.qa-wenjiang.heroera.com;
        location / {
            try_files $uri $uri/ /; #加上这句可正常使用
            proxy_set_header        Host $host;
            proxy_set_header        X-Real-IP $remote_addr;
            proxy_set_header        X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header        X-Forwarded-Proto $scheme;
            proxy_pass http://user_pc-frontend-qa:8080;
            if ($request_uri ~* \.(swf|flv|js|css|gif|jpg|jpeg|png|bmp|ico)$) {
                expires 30d;
            }
        }
    }
    ```
- 安装molten扩展后，在php cli命令行执行php命令会带405
    - 在php.ini里关掉cli时的跟踪就可以了
    ```
    molten.tracing_cli=0 --这里改为0（默认是1）
    ```
- user_pc 更新后，用户反馈没有更新，发现是浏览器缓存了index.html，user_pc 采用的前后分离的方案，所以要让用户每次请求得到最新的代码。解决方案在nginx 加上
    ```
    location ~* \.html$ {
        add_header    Cache-Control  "no-cache,must-revalidate,no-store";
        root /data/orp/webroot/user_pc/dist;
        try_files $uri $uri/ /index.html last;
    }
    ```

- 因为磁盘可用空间不够，导致ingress无法启动
    - nginx在docker里未以daemon off方式运行
    - supervisor不停拉起nginx，导致日志文件增加过大
    - 因为磁盘不够，k8s会驱逐一些pods 