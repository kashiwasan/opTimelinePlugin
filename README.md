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

## 更新履歴
### 1.1.1 alpha
* PC版で画像を投稿することができるようになりました
* PC版で特定のURLをブロック表示しました (小窓)
 * 対応しているサイト youtube amazon
* PC版で公開範囲を指定してつぶやくことができるようになりました
 * 全員に公開 マイフレンドに公開 公開しないの中から選択できます
* PC版でURLを貼りつけたサイトのサムネイルを取得するできるようになりました
  
## インストール方法
* 以下のコマンドを実行して下さい。
 プラグインをダウンロードします。  
    ./symfony opPlugin:install opTimelinePlugin -r 1.1.1  
 モデルとデータベースを更新します。  
    ./symfony opTimelinePlugin:install  
  
## 要望・フィードバック
要望・フィードバックは #opTimelinePlugin のハッシュタグをつけてつぶやいてください。    
   GitHubのアカウントを持っている人は [issues](https://github.com/kashiwasan/opTimelinePlugin/issues)に
チケットを作成してください。
