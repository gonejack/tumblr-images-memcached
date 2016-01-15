# tumblr-images-memcached

服务器超载严重，所以取消了ZIP打包，如果你需要多图的ZIP打包，请搭建自己的服务器，教程如下：

1、注册Google App Engine。

2、创建一个应用，完成后打开https://console.developers.google.com。

3、右上角切换到你创建的应用，再点击旁边的Active Google Cloud Shell。

4、在弹出来的命令行界面敲下面3条命令：

  ```
  git clone https://github.com/gonejack/tumblr-images-memcached.git
  cd tumblr-images-memcached
  gcloud preview app deploy ./app.yaml --promote
  （如果你需要图片打包下载，请在cd tumblr-images-memcached后敲命令nano handler.php，然后找到$makePack = false改为$makePack = true，再按ctrl + o保存，ctrl + x 退出编辑，再去敲接下来的命令）
  ```
  
5、好了，你的服务器完成了，访问一下应用的地址(上面命令输出信息中会告诉你的)试试，如果会输出hello信息，就可以去配置ifttt去了。

有什么问题，可以邮件联系。

Server overload all the time, got to cancel the zip packing for images, tutorial for you to build your own server and turn on zip packing.

1. register Google App Engine.
2. create an app, when finished, enter https://console.developers.google.com.
3. switch to your app by right up corner, and click Active Google Cloud Shell.
4. on the command line interface:
  ```
  git clone https://github.com/gonejack/tumblr-images-memcached.git
  cd tumblr-images-memcached
  gcloud preview app deploy ./app.yaml --promote
  (if you need zip packing, enter one more command after 'cd tumblr-images-memcached', 'nano handler.php', find the $makePack = false, edit it to $makePack = true. ctrl+o to save, ctrl+x to exit the editor. and go ahead)
  ```
5. go to to url that command prompt you, and if you see something like 'hello', you are good to go for configuring ifttt.
