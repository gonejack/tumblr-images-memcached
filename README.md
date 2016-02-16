# tumblr-images-memcached

服务器超载严重，所以取消了ZIP打包，如果你需要多图的ZIP打包，请搭建自己的服务器，教程如下：

1. 注册Google App Engine。
2. 创建一个应用，完成后打开https://console.developers.google.com
3. 右上角切换到你创建的应用，再点击旁边的Active Google Cloud Shell。
4. 在弹出来的命令行界面敲下面3条命令：

  ```
  git clone https://github.com/gonejack/tumblr-images-memcached.git
  cd tumblr-images-memcached
  gcloud preview app deploy ./app.yaml --promote
  （如果你需要图片打包下载，请在cd tumblr-images-memcached后敲命令nano main.php，然后找到$makePackForMultiImages = false改为$makePackForMultiImages = true，再按 CTRL+O 保存，CTRL+X 退出编辑，再去敲接下来的命令）
  ```

5. 好，你的服务器完成。访问一下应用的地址(上面命令输出信息中会告诉你的)试试，如果会输出hello信息，就可以去配置IFTTT了。

有什么问题，可以邮件联系。


Server being overload all the time, got to cancel the zip packing for images, Tutorial below for you to build your own server and turn on zip packing.

1. Register Google App Engine.
2. Create an app, when finished, enter https://console.developers.google.com.
3. Switch to your app at right top corner, click Active Google Cloud Shell.
4. On the command line interface:

  ```
  git clone https://github.com/gonejack/tumblr-images-memcached.git
  cd tumblr-images-memcached
  gcloud preview app deploy ./app.yaml --promote
  (if you need zip packing, enter one more command after 'cd tumblr-images-memcached', 'nano main.php'. Find the $makePackForMultiImages = false, edit it to $makePackForMultiImages = true. CTRL+O to save and CTRL+X to exit the editor, go ahead typing)
  ```

5. Go to the URL that command prompt you, if there are something like 'hello', you are good to go for configuring IFTTT.

Contact me with email if you run into trouble.
