# tumblr-images-memcached

服务器超载严重，取消了ZIP打包，如果你需要请搭建自己的服务器，教程如下：

1. 注册Google App Engine。
2. 创建一个应用，完成后打开https://console.cloud.google.com
3. 右上角切换到你创建的应用，点击旁边的Active Google Cloud Shell。
4. 在弹出来的命令行界面敲下面命令（第三条命令可选）：

  ```
  git clone https://github.com/gonejack/tumblr-images-memcached.git
  cd tumblr-images-memcached
  nano main.php
  （找到$packImages = false改为$packImages = true，按 CTRL+O 保存，CTRL+X 退出编辑）
  gcloud preview app deploy ./app.yaml --promote
  ```

5. 好，你的服务器完成。访问一下应用的地址(上面命令输出信息中会告诉你的)试试，如果会输出hello信息，就可以去配置IFTTT了。

有什么问题，可以邮件联系。


Server being overload all the time, got to cancel the zip packing for multi-images post, tutorial below for you to build your own server and turn on zip packing.

1. Register Google App Engine.
2. Create an app, when finished, enter https://console.cloud.google.com.
3. Switch to your app at right top corner, click Active Google Cloud Shell.
4. On the command line interface:

  ```
  git clone https://github.com/gonejack/tumblr-images-memcached.git
  cd tumblr-images-memcached
  nano main.php
  (find $packImages = false, edit it to $packImages = true. CTRL+O to save and CTRL+X to exit, then go ahead typing)
  gcloud preview app deploy ./app.yaml --promote
  ```

5. Go to the URL that command prompted you, if there something like 'hello' shows up, you are good to go for configuring IFTTT.

Contact me with email if you run into trouble.
