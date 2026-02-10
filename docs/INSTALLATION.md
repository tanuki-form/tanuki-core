# インストールガイド

Tanukiフォームテンプレートのインストール方法を説明します。

## 📋 前提条件

### 必須要件

| 項目 | バージョン |
|------|-----------|
| **PHP** | 8.2 以上 |
| **Composer** | 2.0 以上 |

### 確認方法

```bash
# PHPバージョン確認
php -v

# Composerバージョン確認
composer -V
```

### PHPのインストール

PHPがインストールされていない場合：

**macOS (Homebrew)**
```bash
brew install php@8.2
```

**Ubuntu/Debian**
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml
```

**Windows**
- [PHP公式サイト](https://windows.php.net/download/)からダウンロード

### Composerのインストール

Composerがインストールされていない場合：

```bash
# インストールスクリプトをダウンロード・実行
curl -sS https://getcomposer.org/installer | php

# グローバルにインストール
sudo mv composer.phar /usr/local/bin/composer
```

詳細: [getcomposer.org](https://getcomposer.org/download/)

---

## 🚀 ヘッドレスフォームテンプレートのインストール

### 1. プロジェクト作成

```bash
composer create-project tanuki-form/headless-form-skeleton my-form
```

**コマンド解説:**
- `my-form` はプロジェクトディレクトリ名（任意の名前に変更可能）
- 自動的に `vendor/` ディレクトリと依存パッケージがインストールされます

### 2. ディレクトリ移動

```bash
cd my-form
```

### 3. 開発サーバー起動

```bash
php -S localhost:8000
```

**出力例:**
```
[Mon Jan  6 16:30:00 2026] PHP 8.2.0 Development Server (http://localhost:8000) started
```

### 4. 動作確認

ブラウザで以下にアクセス：

```
http://localhost:8000
```

デモフォームが表示されれば成功です！

### 5. デモを試す

1. フォームに適当な値を入力
2. **"Validate"** ボタンをクリック → バリデーション結果が表示
3. **"Send"** ボタンをクリック → フォーム送信（JSON ログが保存される）

### 6. ログ確認

送信後、以下のディレクトリにJSONファイルが生成されます：

```bash
ls logs/
# 出力例: 20260106_163045_a1b2c3d4.json
```

ログファイルの中身：
```json
{
  "name": "山田太郎",
  "email": "yamada@example.com",
  "email2": "yamada@example.com",
  "enquete": ["A", "B"],
  "message": "テストメッセージ"
}
```

---

## 📄 シングルフォームテンプレートのインストール

### 1. プロジェクト作成

```bash
composer create-project tanuki-form/single-form-skeleton my-form
```

### 2. ディレクトリ移動

```bash
cd my-form
```

### 3. 開発サーバー起動

```bash
php -S localhost:8000
```

### 4. 動作確認

ブラウザで以下にアクセス：

```
http://localhost:8000
```

入力フォームが表示されれば成功です！

### 5. フォームフローを試す

1. **入力画面**: フォームに値を入力して「確認」ボタン
2. **確認画面**: 入力内容を確認して「送信」ボタン
3. **完了画面**: 送信完了メッセージが表示される

### 6. ログ確認

ヘッドレス版と同様に `logs/` ディレクトリにJSONファイルが生成されます。

---

## 📁 インストール後のディレクトリ構造

### ヘッドレスフォーム

```
my-form/
├── composer.json
├── composer.lock
├── vendor/                 # Composer依存パッケージ
├── logs/                   # ログ保存ディレクトリ（自動生成）
├── index.html             # デモページ
├── form/
│   ├── index.php          # APIエンドポイント
│   ├── functions.php      # ヘルパー関数
│   └── config/            # 設定ファイル
│       ├── config.php
│       ├── schema.php
│       ├── smtp.php
│       ├── email-templates/
│       ├── pre-handlers/
│       └── post-handlers/
└── js/
    ├── api.js
    ├── script.js
    ├── utils.js
    └── demo.js
```

### シングルフォーム

```
my-form/
├── composer.json
├── composer.lock
├── vendor/                 # Composer依存パッケージ
├── logs/                   # ログ保存ディレクトリ（自動生成）
├── index.php              # エントリーポイント
├── functions.php          # ヘルパー関数
├── config/                # 設定ファイル
│   ├── config.php
│   ├── schema.php
│   ├── smtp.php
│   ├── email-templates/
│   ├── pre-handlers/
│   └── post-handlers/
└── views/                 # ビューテンプレート
    ├── input.php          # 入力画面
    ├── confirm.php        # 確認画面
    ├── complete.php       # 完了画面
    ├── error.php          # エラー画面
    └── parts/
        ├── recaptcha.php
        └── turnstile.php
```

---

## 🔧 トラブルシューティング

### ❌ エラー: "logs" ディレクトリに書き込めない

**症状:**
```
Warning: file_put_contents(logs/...): Failed to open stream
```

**原因:** ディレクトリの権限不足

**解決方法:**
```bash
# logsディレクトリの権限を変更
chmod 755 logs/

# または、logsディレクトリを作り直す
rm -rf logs/
mkdir logs
chmod 755 logs/
```

---

### ❌ エラー: Composer command not found

**症状:**
```bash
composer: command not found
```

**原因:** Composerがインストールされていない、またはPATHが通っていない

**解決方法:**

1. Composerのインストール確認
```bash
php composer.phar -V
```

2. グローバルにインストール
```bash
sudo mv composer.phar /usr/local/bin/composer
```

3. または、`php composer.phar` で直接実行
```bash
php composer.phar create-project tanuki-form/headless-form-skeleton my-form
```

---

### ❌ エラー: PHP version requirement not satisfied

**症状:**
```
Your PHP version (7.4.x) does not satisfy the requirement (>=8.2)
```

**原因:** PHPバージョンが古い

**解決方法:**

1. PHPバージョン確認
```bash
php -v
```

2. PHP 8.2以上にアップグレード

**macOS:**
```bash
brew upgrade php
# または特定バージョンをインストール
brew install php@8.2
brew link php@8.2
```

**Ubuntu/Debian:**
```bash
sudo apt install php8.2
sudo update-alternatives --set php /usr/bin/php8.2
```

---

### ❌ エラー: Port 8000 already in use

**症状:**
```
[error] Failed to listen on localhost:8000 (reason: Address already in use)
```

**原因:** ポート8000が既に使用されている

**解決方法:**

別のポートを使用する：
```bash
php -S localhost:8001
```

または、使用中のプロセスを確認して終了：
```bash
# macOS/Linux
lsof -i :8000

# プロセスを終了
kill -9 <PID>
```

---

### ❌ デモページが表示されない（404 Not Found）

**症状:**
ブラウザに "404 Not Found" が表示される

**確認事項:**

1. **正しいURLにアクセスしているか**
   - ヘッドレス: `http://localhost:8000/` （index.htmlを表示）
   - シングル: `http://localhost:8000/` （index.phpを表示）

2. **サーバーが起動しているか**
```bash
# ターミナルに以下が表示されているか確認
PHP 8.2.0 Development Server (http://localhost:8000) started
```

3. **正しいディレクトリで起動しているか**
```bash
pwd
# プロジェクトルートディレクトリにいることを確認
```

---

### ❌ CSRF検証エラー

**症状:**
フォーム送信時に "invalid-token" エラー

**原因:**
- セッションが開始されていない
- ブラウザのCookie設定

**解決方法:**

1. **セッション確認**

ヘッドレス版: `form/index.php` を確認
```php
// session_start() が呼ばれているか確認
session_start();
```

シングル版: `index.php` を確認
```php
session_start();  // ファイルの最初の行
```

2. **ブラウザのCookieを有効化**
   - 開発者ツール（F12）→ Application → Cookies を確認

3. **ブラウザのキャッシュクリア**
   - Ctrl + Shift + Delete でキャッシュをクリア

---

## 📦 依存パッケージ

インストールされるパッケージ：

### ヘッドレスフォーム

```json
{
  "require": {
    "php": ">=8.2",
    "tanuki-form/tanuki-core": "^0.1.14",
    "tanuki-form/tanuki-mail-sender-handler": "^0.1"
  }
}
```

### シングルフォーム

```json
{
  "require": {
    "php": ">=8.2",
    "tanuki-form/tanuki-core": "^0.1.14",
    "tanuki-form/tanuki-mail-sender-handler": "^0.1",
    "tanuki-form/formtagbinder": "^1.1"
  }
}
```

**パッケージ説明:**
- `tanuki-core`: Tanukiフレームワークのコア
- `tanuki-mail-sender-handler`: メール送信ハンドラー
- `formtagbinder`: フォームタグ生成ツール（シングルフォームのみ）

---

## 🔄 アップデート方法

### 依存パッケージの更新

```bash
composer update
```

特定のパッケージのみ更新：
```bash
composer update tanuki-form/tanuki-core
```

### 最新バージョンの確認

```bash
composer show tanuki-form/tanuki-core
```

---

## 🌐 本番環境へのデプロイ

### 1. 依存パッケージのインストール（本番用）

```bash
composer install --no-dev --optimize-autoloader
```

**オプション説明:**
- `--no-dev`: 開発用パッケージを除外
- `--optimize-autoloader`: オートローダーを最適化

### 2. 環境変数の設定

本番環境では、機密情報を環境変数で管理：

```php
// config/smtp.php
return [
  "host" => getenv("SMTP_HOST"),
  "username" => getenv("SMTP_USERNAME"),
  "password" => getenv("SMTP_PASSWORD"),
  "port" => 587,
];
```

### 3. ディレクトリ権限の設定

```bash
# logsディレクトリの書き込み権限
chmod 755 logs/
```

### 4. Webサーバー設定

**Apache (.htaccess)**
```apache
RewriteEngine On
RewriteRule ^form/(.*)$ form/index.php [L]
```

**Nginx**
```nginx
location /form/ {
    try_files $uri /form/index.php$is_args$args;
}
```

---

## 📚 次のステップ

インストールが完了したら：

1. **[ヘッドレスフォームガイド](HEADLESS.md)** - ヘッドレス版の詳細な使い方
2. **[シングルフォームガイド](SINGLE-FORM.md)** - シングル版の詳細な使い方
3. **[設定ファイルガイド](CONFIGURATION.md)** - 設定のカスタマイズ方法
4. **[カスタマイズガイド](CUSTOMIZATION.md)** - よくあるカスタマイズ例

---

## ❓ よくある質問

### Q: ローカル開発環境以外で使えますか？

A: はい。XAMPP、MAMP、Docker等の環境でも動作します。

**XAMPP/MAMP:**
```
htdocs/my-form/  にプロジェクトを配置
http://localhost/my-form/ でアクセス
```

**Docker:**
```dockerfile
FROM php:8.2-apache
COPY . /var/www/html/
RUN chmod 755 /var/www/html/logs
```

### Q: データベースは必要ですか？

A: いいえ。Tanukiはデータベース不要で動作します。
ただし、カスタムハンドラーでデータベース保存も可能です。

### Q: 既存のプロジェクトに追加できますか？

A: はい。以下の手順で追加できます：

```bash
# 既存プロジェクトのルートで
composer require tanuki-form/tanuki-core
composer require tanuki-form/tanuki-mail-sender-handler

# テンプレートからファイルをコピー
# form/ディレクトリをコピー
```

### Q: 商用利用できますか？

A: はい。MITライセンスで、商用利用可能です。

---

**インストールに問題がある場合:**
- [GitHub Issues](https://github.com/tanuki-form/tanuki-core/issues) で質問
- 公式ドキュメントを確認
