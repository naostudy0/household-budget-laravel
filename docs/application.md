# アプリケーション層

## Controller と UseCase

Controller は HTTP 境界の処理に集中する。

Controller の責務は以下に限定する。

- Request の受け取り
- FormRequest によるバリデーション
- 認可確認
- UseCase / Query の呼び出し
- Inertia レスポンスまたはリダイレクトの返却

保存、更新、削除などの状態変更は Controller に直接書かず、Application 層の UseCase に委譲する。

ユーザー所有リソースを作成・更新する場合、Request に含まれる所有者IDや権限に関わる値をそのまま信頼しない。

Controller は認証済みユーザーや認可済みリソースから必要な値を取り出し、バリデーション済みの入力値と合わせて UseCase の Input DTO に詰める。

## UseCase

UseCase は Application 層に配置し、1クラス1ユースケースを基本とする。

入力値が複数ある場合は Input DTO を使う。

戻り値として画面や Controller が使う値を返す場合は Result DTO を使う。

例：

```php
$result = $useCase->execute(new CreateAccountInput(
    userId: $userId,
    name: '生活費',
));
```

UseCase は処理の流れを調整する。永続化は Domain 層の Repository interface に依存し、Infrastructure 層の Eloquent 実装には直接依存しない。

## Repository

書き込みやドメインオブジェクトの復元・保存が必要な場合は、Domain 層に Repository interface を置く。

実装は Infrastructure 層に置く。

```text
app/Domain/Accounts/Repositories/AccountRepositoryInterface.php
app/Infrastructure/Persistence/Eloquent/Repositories/EloquentAccountRepository.php
```

Application 層や Http 層から Eloquent Repository 実装クラスを直接参照しない。

## Query

一覧、検索、表示用集計などの表示専用の読み取り処理は Query に分離してよい。

Query は読み取り最適化を目的とするため、Application 層から必要に応じて Eloquent / Query Builder を直接使ってよい。

状態変更を伴う処理は Query に置かず、UseCase に置く。

## 状態変更のテスト

HTTP 境界のテストでは、ユーザー操作として重要な代表ケースを確認する。

例：

- 他ユーザーのリソースを更新・削除できない
- 正常に作成・更新・削除した場合、期待する画面へリダイレクトされる

保存処理そのものは UseCase の Integration テストで確認する。

UseCase の Integration テストでは、Repository 実装を組み合わせて、保存される値、払い出されるID、公開識別子などを確認する。

Feature / Integration / Unit の詳しい使い分けは [テスト方針](testing.md) に従う。
