# AGENTS.md — LifeLedger

## プロジェクト概要

**LifeLedger** は複数の会計単位を独立して管理しつつ、合算して全体の収支・残高を確認できる個人向け会計管理アプリ。

単純な家計簿ではなく、個人用・生活費・共通費・目的別資金などを独立した帳簿として扱い、必要に応じて合算ビューで全体を確認できることを目的とする。

複式簿記・本支店会計の概念を、個人利用向けに扱いやすく落とし込んだ設計とする。

---

## 技術スタック

| レイヤー | 技術 |
|---|---|
| バックエンド | Laravel 13, PHP 8.5 |
| フロントエンド | Vue 3 / Composition API |
| 画面連携 | Inertia.js v3 |
| CSS | Tailwind CSS v4 |
| DB | PostgreSQL |
| ビルド | Vite 8 |
| テスト | Pest v4 / PHPUnit 12 |
| PHP フォーマット | Laravel Pint / preset: laravel |
| JS / Vue フォーマット | Prettier |

技術バージョンは上記を基本とする。

ただし、初期セットアップ時点で公式テンプレートや依存パッケージの都合により差異がある場合は、実際の `composer.json` / `package.json` を優先する。

---

## 開発コマンド

```bash
# フロントエンド開発サーバー起動
npm run dev

# フロントエンドビルド
npm run build

# PHP コード整形
./vendor/bin/pint

# PHP コード整形チェック（変更なし）
./vendor/bin/pint --test

# JS / Vue コード整形
npm run format

# JS / Vue コード整形チェック
npm run format:check

# テスト実行
php artisan test

# 対象テストのみ実行
php artisan test --filter=TestName

# カバレッジ測定（coverage/ にHTML出力）
php artisan test --coverage-html coverage

# マイグレーション
php artisan migrate

# マイグレーション（リセット）
php artisan migrate:fresh --seed
```

---

## アーキテクチャ：ドメイン駆動設計（DDD）

このプロジェクトは、DDDの考え方を取り入れたレイヤードアーキテクチャを採用する。

ただし、すべての処理に対して機械的にDDDを適用するのではなく、ドメインルールが重要な箇所に優先して適用する。

---

## ディレクトリ構成

```text
app/
  Domain/                    # ドメイン層（ビジネスルールの中心）
    {Context}/
      Entities/              # エンティティ（同一性で識別されるオブジェクト）
      ValueObjects/          # 値オブジェクト（不変・値で識別）
      Services/              # ドメインサービス（エンティティに属さないロジック）
      Repositories/          # リポジトリインターフェース
      Events/                # ドメインイベント

  Application/               # アプリケーション層（ユースケースの調整）
    {Context}/
      UseCases/              # ユースケース（1クラス1ユースケース）
      Queries/               # 読み取り専用の問い合わせ処理
      DTOs/                  # Input / Result DTO

  Infrastructure/            # インフラ層（外部への依存）
    Persistence/             # リポジトリ実装、Eloquent関連
      Eloquent/
        Models/              # Eloquentモデル
        Repositories/        # Repository実装
        Queries/             # Query実装が必要な場合

  Http/                      # プレゼンテーション層
    Controllers/             # コントローラー（薄く保つ）
    Requests/                # フォームリクエスト

resources/js/
  Pages/                     # Inertia ページコンポーネント（Vue）
  Components/                # 共通コンポーネント
  Layouts/                   # レイアウト
  app.js

database/
  migrations/
  seeders/
  factories/

routes/
  web.php                    # Inertia画面・画面操作
  api.php                    # 外部連携や純粋なJSON APIが必要な場合のみ使用

tests/
  Unit/                      # ユニットテスト（ロジック単体）
  Integration/               # 統合テスト（実クラスを組み合わせてテスト）
  Feature/                   # フィーチャーテスト（HTTP振る舞いテスト）

ai-notes/                    # AI作業メモ（gitignore済み）
```

Laravel標準の都合で `app/Models` を使う必要がある場合は許容する。

ただし、Eloquent Model はドメインモデルではなく、永続化のためのインフラ詳細として扱う。

---

## レイヤーの依存方向

```text
Http → Application → Domain ← Infrastructure
```

- **Domain 層**は他のレイヤーに依存しない
- **Application 層**は Domain のインターフェースに依存する
- **Application 層**は原則として Eloquent Model に依存しない
- **Infrastructure 層**は Domain のインターフェースを実装する
- **Http 層**は Application の UseCase を呼び出す
- Controller にビジネスロジックを書かない

---

## DDDの主要概念

### エンティティ（Entity）

同一性、つまりIDで識別されるオブジェクト。

状態が変化しても、同じIDであれば同じものとして扱う。

例：

- 会計単位
- 取引
- 仕訳
- 帳簿

---

### 値オブジェクト（Value Object）

値そのもので識別されるオブジェクト。

不変であることを基本とする。

例：

- 金額
- 日付範囲
- 会計単位名
- 借方・貸方区分
- 通貨
- 残高

---

### ドメインサービス（Domain Service）

単一のEntityやValue Objectに自然に属さないビジネスロジックを扱う。

例：

- 複数会計の合算処理
- 内部取引の相殺判定
- 残高整合性チェック
- 複数帳簿をまたぐ集計ルール

---

### リポジトリ（Repository）

Domain 層でインターフェースを定義し、Infrastructure 層で Eloquent を使って実装する。

Repository は、永続化の抽象化が必要な Aggregate / Entity に対して作成する。

すべてのテーブルに対して機械的にRepositoryを作成しない。

---

### ユースケース（Use Case）

Application 層に配置する。

1クラス1ユースケースを基本とし、`execute()` メソッドを持つ。

UseCase は業務処理の流れを調整する役割を持つ。

UseCase にドメインルールを直接書きすぎず、可能なものは Entity / Value Object / Domain Service に寄せる。

---

## DDD適用方針

DDDはドメインルールが存在する箇所に優先して適用する。

単純なCRUDや表示専用の処理では、過度な抽象化を避ける。

以下のような処理はDDDの対象とする。

- 金額、残高、借方・貸方などの会計ルール
- 会計単位間の内部取引
- 合算時の内部取引消去
- 取引登録時の不変条件
- 仕訳の整合性チェック
- 残高計算
- 帳簿間の整合性チェック

一方で、以下のような処理では必要になるまで Entity / Repository / Domain Service を増やさない。

- 単純なマスタ管理
- 表示用の一覧取得
- 検索条件に応じた読み取り専用処理
- ダッシュボード表示用の集計

実装に迷った場合は、まずLaravelとして自然で読みやすい実装を優先する。

---

## Repository / Query の使い分け

### Repository

Repository は、主に書き込みやドメインオブジェクトの復元・保存に使う。

Repository の対象例：

- AccountRepository
- JournalEntryRepository
- TransactionRepository
- LedgerRepository

Repository は Domain 層にインターフェースを置く。

実装は Infrastructure 層に置く。

Application 層や Http 層から、Repository実装クラスを直接参照しない。

---

### Query

読み取り最適化が必要な処理は Query として分離してよい。

Query を導入する場合は、原則として以下に配置する。

```text
app/Application/{Context}/Queries/
```

Query は読み取り専用の最適化を目的とするため、必要に応じて Eloquent / Query Builder を直接使用してよい。

ただし、Query では状態変更を行わない。

状態変更を伴う処理は UseCase に残す。

---

### 使い分けの基準

```text
書き込み・状態変更・不変条件の検証 → UseCase / Domain / Repository
読み取り・一覧・検索・表示用集計       → Query
```

Repository に読み取り用の複雑な検索処理を詰め込みすぎない。

---

## UseCase の入出力

UseCase は原則として `execute()` メソッドを持つ。

入力値が1つだけの場合は、プリミティブ型やValue Objectを直接渡してよい。

入力値が2つ以上になる場合は、Input DTO を作成する。

戻り値が配列や複雑な構造になる場合は、Result DTO を作成する。

Controller から UseCase に Request オブジェクトを直接渡さない。

UseCase から Eloquent Model を直接返さない。

例：

```php
$result = $useCase->execute(new CreateAccountInput(
    name: '個人用',
    type: AccountType::Personal,
));
```

---

## Inertia.js の方針

このプロジェクトでは、画面は Inertia.js を使って構築する。

Inertia は Laravel のルーティング、Controller、Middleware、Validation を活かしながら、Vue によるSPA風の画面体験を実現するために使う。

---

### ルーティング

Inertia画面と画面操作は `routes/web.php` に定義する。

外部連携や純粋なJSON APIが必要な場合のみ `routes/api.php` を使う。

通常の画面操作では、Vue側から `fetch` や `axios` で `/api/*` を直接呼び出すのではなく、Inertia の仕組みを優先する。

---

### Controller

Controller は薄く保つ。

Controller の役割は以下に限定する。

- Request の受け取り
- FormRequest によるバリデーション
- UseCase / Query の呼び出し
- Inertia レスポンスの返却
- リダイレクトの返却

例：

```php
return Inertia::render('Accounts/Index', [
    'accounts' => $accounts,
]);
```

---

### Vue

Vueコンポーネントでは Composition API を使用する。

`<script setup>` を基本とする。

ファイル内の順序は以下を基本とする。

```vue
<script setup>
</script>

<template>
</template>

<style scoped>
</style>
```

Inertia の `<Link>` を `<a>` の代わりに使う。

フォーム送信では Inertia の `useForm` または `router` を優先して使う。

---

## AI エージェントによるテスト駆動開発（Agentic TDD）

このプロジェクトでは **AI エージェントがテストを先に書き、その後に実装する** TDD サイクルを採用する。

---

## 基本サイクル

```text
1. Red:       AI がテストを書く（仕様を表現する）
2. Green:     AI がテストを通す最小限の実装を書く
3. Refactor:  テストを保ちながらリファクタリングする
```

---

## AI エージェントへの指示方針

実装タスクを行う場合、原則としてテストを先に書く。

テストは「仕様書」として機能させる。

テストを読めば、対象クラスや対象機能が何をするものか分かる粒度で書く。

テストが Red であることを確認してから実装に進む。

Red確認時は対象テストのみ実行してよい。

実装後は `php artisan test` がすべて Green になることを確認する。

---

## テストピラミッド

このプロジェクトはテストピラミッドの考え方に基づき、Unit / Integration / Feature の3種類のテストを使い分ける。

---

## 書くべきテストの判断基準

対象クラスの特徴に応じて、以下の通り書くテストの種類を決める。

| 対象クラスの特徴                                  | Unit | Integration | Feature |
|--------------------------------------------------|:----:|:-----------:|:-------:|
| ロジックのみ（外部クラス呼び出しなし）            |  ✓   |             |         |
| ロジックあり ＋ 他クラスを呼び出している          |  ✓   |      ✓      |         |
| 他クラスを呼び出すだけ（ロジックなし）            |      |      ✓      |         |
| HTTPリクエストのエンドポイントである              |      |             |    ✓    |

---

## Unit テスト

ロジックそのものをテストする。

他クラスへの呼び出しはモックする。

外部依存（DB・メール・外部APIなど）を持たない。

```text
tests/Unit/
```

```php
it('金額が負の値の場合は例外を投げる', function () {
    expect(fn () => new Money(-100))
        ->toThrow(InvalidArgumentException::class);
});
```

---

## Integration テスト

実際のコラボレーター（他クラス）を組み合わせてテストする。

モックは使わない。ただし、メール送信・外部API・外部ツールとの連携など、外部サービスへの副作用が伴うものはモックしてよい。

```text
tests/Integration/
```

UseCase のテストは原則として Integration テストに書く。

```php
it('会計を作成するとIDが払い出される', function () {
    $useCase = new CreateAccountUseCase(
        new EloquentAccountRepository()
    );

    $result = $useCase->execute(new CreateAccountInput(
        name: '個人用',
        type: AccountType::Personal,
    ));

    expect($result->id)->not->toBeNull();
});
```

---

## Feature テスト

HTTPリクエストを起点にした振る舞いをテストする。

`$this->get` / `$this->post` などを使い、エンドポイントの振る舞いを確認する。

```text
tests/Feature/
```

```php
it('POST /accounts で会計が作成される', function () {
    $this->post('/accounts', ['name' => '個人用'])
        ->assertRedirect();

    $this->assertDatabaseHas('accounts', ['name' => '個人用']);
});
```

Inertiaページを返すエンドポイントでは、ページ名とpropsも検証する。

```php
use Inertia\Testing\AssertableInertia as Assert;

it('会計一覧ページを表示できる', function () {
    $this->get('/accounts')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Accounts/Index')
            ->has('accounts')
        );
});
```

---

## コーディング規約

## PHP / Laravel

- コントローラーは薄く保つ
- ビジネスロジックは UseCase / Domain に委譲する
- Inertia レスポンスは `Inertia::render('PageName', $data)` で返す
- Inertia画面は `routes/web.php` に定義する
- JSON API が必要な場合のみ `routes/api.php` を使う
- 命名はLaravel規約に従う
- モデル名は PascalCase の単数形
- テーブル名は snake_case の複数形
- `php artisan test` がパスすること
- `./vendor/bin/pint --test` がパスすること

---

## Vue / フロントエンド

- Composition API を使う
- `<script setup>` を使う
- Inertia の `<Link>` を `<a>` の代わりに使う
- フォームは `useForm` または `router` を優先して使う
- 表示専用コンポーネントとページコンポーネントを分ける
- `npm run format:check` がパスすること

---

## 共通

- コメントは「なぜ」を書く
- 「何をしているか」はコードで表現する
- 不要な抽象化を避ける
- 3つ以上の重複が出てから共通化を検討する
- 既存の命名・構成に合わせる
- 一度に大きく変更しすぎない
- テストなしでドメインルールを変更しない

---

## ドメイン知識

### 会計単位（Account）

独立した帳簿の単位。

個人用・生活費・共通費・目的別資金など、用途ごとに分ける。

---

### 取引（Transaction）

お金の移動や収支を表す。

収入、支出、振替などを扱う。

---

### 仕訳（Journal Entry）

複式簿記における借方・貸方の記録。

取引を会計的に表現するために使う。

---

### 内部取引（InterCompany Transaction）

複数会計を合算する際、会計間の資金移動を相殺するための概念。

二重計上防止に使う。

例：

- 個人用会計から生活費会計へ資金を移動した
- 生活費会計側では入金として見える
- 個人用会計側では出金として見える
- 合算ビューではこの資金移動を内部取引として相殺する

---

### 合算ビュー

複数の会計単位を統合して、全体の収支・残高を表示する機能。

内部取引を相殺し、実態に近い全体像を表示する。

---

### 残高

特定の会計単位、または合算ビューにおける現在の金額。

残高は取引や仕訳の結果として計算される。

---

## 実装判断の優先順位

実装方針に迷った場合は、以下の順で優先する。

1. ドメインルールの正しさ
2. テストで仕様が確認できること
3. Laravel / Inertia の標準的な書き方に従うこと
4. 過度な抽象化を避けること
5. 将来の拡張余地を残すこと

---

## AI作業メモ

コンテキスト圧縮や別エージェントへの引き継ぎ時は `ai-notes/` ディレクトリにメモを書く。

このディレクトリは `.gitignore` に追加済みでコミットされない。

メモのファイル名例：

```text
ai-notes/YYYY-MM-DD_作業内容.md
```

---

## ai-notes に書く内容

AIエージェントは、長い作業や複数ファイルにまたがる作業を行う場合、必要に応じて `ai-notes/` に以下を記録する。

- 作業目的
- 現在の進捗
- 変更したファイル
- 未完了の作業
- 判断に迷った点
- 次に確認すべきこと

ただし、仕様として確定した内容は、必要に応じて正式な docs や Issue へ反映する。

`ai-notes/` は一時的な作業メモであり、恒久的な仕様書として扱わない。

重要な仕様変更を `ai-notes/` のみに残さない。

---

## AIエージェントが避けるべきこと

- テストを書かずにドメインロジックを実装する
- Controller にビジネスロジックを書く
- UseCase に Request オブジェクトを直接渡す
- UseCase から Eloquent Model を直接返す
- Domain 層から Eloquent / DB / Framework に依存する
- すべての処理に機械的に Repository を作る
- 単純な読み取り処理まで過剰に抽象化する
- Inertia画面の通常操作を安易に `/api/*` にする
- `ai-notes/` に重要仕様を残したままにする
- テストが落ちた状態で作業完了とする

---

## 完了条件

実装タスクは、原則として以下を満たした状態で完了とする。

- 対象機能のテストが追加されている
- 対象テストが Green である
- `php artisan test` が Green である
- `./vendor/bin/pint --test` が Green である
- `npm run format:check` が Green である
- Inertia画面を変更した場合、ページ名・props・リダイレクトが確認されている
- ドメインルールを変更した場合、Domain層のテストが追加または更新されている
- 必要に応じて `ai-notes/` に作業メモが残されている
- 確定した仕様変更は docs または Issue に反映されている
