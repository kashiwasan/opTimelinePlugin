opTimelinePlugin
================

## スクリーンショット
<img src="https://raw.github.com/ichikawatatsuya/opTimelinePlugin/gh-pages/images/004.png" height="200" width="400" />
<img src="https://raw.github.com/ichikawatatsuya/opTimelinePlugin/gh-pages/images/005.png" height="200" width="400" />
<img src="https://raw.github.com/ichikawatatsuya/opTimelinePlugin/gh-pages/images/001.png" height="200" />
<img src="https://raw.github.com/ichikawatatsuya/opTimelinePlugin/gh-pages/images/002.png" height="200" />
<img src="https://raw.github.com/ichikawatatsuya/opTimelinePlugin/gh-pages/images/003.png" height="200" />


## 機能概要
アクティビティ機能をさらに使いやすくします。   
   追加される機能には以下のものがあります。

### アクティビティに追加される機能一覧
* 画像を投稿することができます (PC版のみ対応)
* 特定のURLをブロック表示します (小窓) (PC版のみ対応)
 * 対応しているサイト youtube amazon
* URLを貼りつけたサイトのサムネイルを取得します
* 公開範囲を指定してつぶやくことができます (PC版のみ対応)
 * 全員に公開 マイフレンドに公開 公開しないの中から選択できます
* つぶやきにコメントをつけることができます
* ３０秒毎に自動でリロードがかかります
* 自動でタイムラインをリロードします(３０秒毎)
* スクリーンネームを設定することができます
* コミュニティ内でつぶやくことができる(コミュニティアクティビティ)


## インストール方法
1. 以下のコマンドを実行して、プラグインをインストールしてください。
 * ./symfony opPlugin:install opTimelinePlugin -r 1.1.1
2. 以下のコマンドを実行し、opTimelinePluginをインストールしてください。
 * ./symfony opTimelinePlugin:install


## プラグインの使用方法
管理画面にログイン後、ガジェット設定画面にアクセスします。(デザイン設定 -> ガジェット設定)    
   ガジェット設定画面で、SNSメンバーのタイムラインのガジェットを追加してください。   
   ("ガジェットを追加" のボタンをクリックをして、ポップアップが表示されましたら、    
   "SNSメンバーのタイムライン" の項目の "このガジェットを追加する" をクリックしてください。)

## 更新履歴
### 1.1.1 alpha
* PC版で画像を投稿することができるようになりました
* PC版で特定のURLをブロック表示しました (小窓)
 * 対応しているサイト youtube amazon
* PC版で公開範囲を指定してつぶやくことができるようになりました
 * 全員に公開 マイフレンドに公開 公開しないの中から選択できます

## 要望・フィードバック
要望・フィードバックは #opTimelinePlugin のハッシュタグをつけてつぶやいてください。    
   GitHubのアカウントを持っている人は [issues](https://github.com/kashiwasan/opTimelinePlugin/issues)に
チケットを作成してください。