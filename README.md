# 撮影した画像をCloud Visionで解析

## 環境構築

コンテナを構築
```shell
# kool run setup
```

credentialsディレクトリにGOOGLE_APPLICATION_CREDENTIALSを設置

GOOGLE_APPLICATION_CREDENTIALS のパスを.envに追加

## 実行

コンテナを起動
```shell
# kool start
```
ngrokを起動
```shell
# ngrok http 8700
```

ngrokのhttpsのURLにアクセス
