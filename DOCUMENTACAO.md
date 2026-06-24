# 📘 Documentação de Estudo — ProjetoCrud

> Resumo de tudo que foi construído neste CRUD de usuários em **Laravel 13**, com explicação dos conceitos, do *porquê* de cada decisão e dos pontos mais importantes para lembrar.
> Feito para servir de material de estudo, não só de referência.

---

## 1. Visão geral

Este é um **CRUD de usuários**. CRUD é a sigla das 4 operações básicas sobre dados:

| Letra | Operação | Em português | Onde está no projeto |
|-------|----------|--------------|----------------------|
| **C** | Create   | Criar        | `create()` + `insert()` |
| **R** | Read     | Ler/Listar   | `index()` (com pesquisa e paginação) |
| **U** | Update   | Atualizar    | `edit()` + `update()` |
| **D** | Delete   | Apagar       | `delete()` *(ainda não implementado)* |

### Stack (tecnologias usadas)

| Camada | Tecnologia | Para pesquisar |
|--------|-----------|----------------|
| Framework | **Laravel 13** (PHP 8.3) | "Laravel MVC", "Laravel documentation" |
| Banco de dados | **PostgreSQL** | "PostgreSQL", "Laravel pgsql" |
| ORM (acesso ao banco) | **Eloquent** | "Laravel Eloquent ORM" |
| Templates (HTML) | **Blade** | "Laravel Blade templates" |
| Build de assets (CSS/JS) | **Vite** | "Laravel Vite" |

---

## 2. O conceito mais importante: o padrão MVC

Laravel é organizado no padrão **MVC (Model–View–Controller)**. É *o* conceito que você precisa dominar — tudo no projeto se encaixa nele.

```
                    ┌─────────────────────────────────────────────┐
   Navegador        │                  LARAVEL                     │
   (usuário)        │                                              │
       │            │   1. ROTA          2. CONTROLLER             │
       │  request   │   routes/web.php    UserController           │
       └───────────►│   "qual URL?"  ──►  "o que fazer?"           │
                    │                          │                   │
                    │                          ▼                   │
                    │                     3. MODEL (Eloquent)      │
                    │                     User  ◄──► 🗄️ Banco       │
                    │                          │                   │
                    │                          ▼                   │
       ◄────────────│                     4. VIEW (Blade)          │
        HTML pronto │                     pages/user/index         │
                    └─────────────────────────────────────────────┘
```

1. **Rota** (`routes/web.php`): mapeia uma URL + método HTTP para um método do controller. É o "porteiro" que decide para onde vai o pedido.
2. **Controller** (`UserController`): o "cérebro". Recebe o pedido, valida, conversa com o Model e escolhe qual View devolver.
3. **Model** (`User`): representa a tabela `users` e cuida do acesso ao banco via Eloquent.
4. **View** (Blade): monta o HTML que volta pro navegador.

> **Por quê separar assim?** Cada parte tem uma responsabilidade só (princípio da *Separação de Responsabilidades* / *Single Responsibility*). Isso deixa o código mais fácil de testar, manter e entender. É a mesma filosofia em quase todo framework web moderno.

---

## 3. As peças, uma por uma

### 3.1 Rotas — `routes/web.php`

```php
Route::get('/usuarios', [UserController::class, 'index']);              // listar
Route::get('/usuarios/cadastrar', [UserController::class, 'create']);   // form de criar
Route::post('/usuarios', [UserController::class, 'insert']);            // salvar novo
Route::get('/usuario/{id}/editar', [UserController::class, 'edit']);    // form de editar
Route::put('/usuarios', [UserController::class, 'update']);             // salvar edição
Route::delete('/usuarios', [UserController::class, 'delete']);          // apagar
```

**Conceitos para lembrar:**

- **Verbos HTTP**: a mesma URL `/usuarios` faz coisas diferentes dependendo do método: `GET` lista, `POST` cria, `PUT` atualiza, `DELETE` apaga. Isso é a base do estilo **REST**. → *pesquisar: "HTTP methods", "REST API"*
- **`{id}`** na rota de editar é um **parâmetro de rota** — um valor dinâmico que chega no método do controller.
- O navegador só sabe enviar `GET` e `POST` em formulários. Para usar `PUT`/`DELETE`, o Laravel usa um truque chamado **method spoofing** (veja seção 3.5).

> ⚠️ **Ponto de atenção (bug):** as rotas têm `'role' => 'user.index'` dentro do array de ação:
> ```php
> Route::get('/usuarios', [UserController::class, 'index', 'role' => 'user.index']);
> ```
> O array de ação aceita só **2 itens**: `[Classe::class, 'metodo']`. Esse `'role' => ...` é ignorado pelo Laravel — provavelmente a intenção era **nomear a rota**, e a forma correta é:
> ```php
> Route::get('/usuarios', [UserController::class, 'index'])->name('user.index');
> ```
> Rotas nomeadas deixam você gerar URLs com `route('user.index')` em vez de digitar `/usuarios` na mão. → *pesquisar: "Laravel named routes"*

### 3.2 Controller — `app/Http/Controllers/UserController.php`

É onde mora a lógica. Repare em um padrão muito bom que você usou: **métodos privados auxiliares** para não repetir código (princípio **DRY — Don't Repeat Yourself**):

- `form(User $user)` → centraliza o `return view(...)` do formulário (serve tanto para criar quanto editar).
- `save(User $user, $request)` → centraliza a gravação no banco (serve para insert e update).
- `validation($request)` → centraliza as regras de validação.

**Detalhe inteligente do `save()`:**
```php
if ($request->password) {
    $user->password = bcrypt($request->password);
}
```
Só atualiza a senha **se ela foi preenchida**. Assim, ao editar um usuário sem trocar a senha, a senha antiga é mantida. `bcrypt()` transforma a senha em um *hash* — nunca se guarda senha em texto puro. → *pesquisar: "password hashing", "bcrypt"*

**Detalhe inteligente do `validation()`:** as regras mudam conforme o método HTTP:
```php
if ($method == 'PUT') {       // edição
    array_unshift($rules['password'], 'nullable');   // senha pode ficar vazia
    $rules['id'] = ['required', 'integer', 'exists:users,id'];
} else {                       // criação
    array_unshift($rules['password'], 'required');   // senha obrigatória
}
```
Faz sentido: ao **criar**, a senha é obrigatória; ao **editar**, é opcional.

### 3.3 Model + Query Scope — `app/Models/User.php`

```php
class User extends Model {
    use HasFactory;

    public function scopeSearch($query, Request $request) {
        if ($request->name) {
            $query->where('name', 'ilike', '%'.$request->name.'%');
        }
        if ($request->email) {
            $query->where('email', 'ilike', '%'.$request->email.'%');
        }
    }
}
```

**Conceitos para lembrar:**

- **Eloquent**: cada Model representa uma tabela. `User::find(1)`, `$user->save()`, etc. são Eloquent. → *pesquisar: "Laravel Eloquent"*
- **Local Query Scope**: um método que começa com `scope` + Nome define um filtro reutilizável. Você definiu `scopeSearch` e chama como `User::search($request)` (sem o prefixo `scope`). Ótimo para não jogar lógica de busca dentro do controller. → *pesquisar: "Laravel query scopes"*
- **`ilike`**: busca de texto **sem diferenciar maiúsculas/minúsculas** — é específico do PostgreSQL (no MySQL seria `like`). O `%texto%` significa "contém o texto". → *pesquisar: "SQL LIKE wildcard", "PostgreSQL ILIKE"*

### 3.4 Migration — `database/migrations/..._create_users_table.php`

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();                          // chave primária auto-incremento
    $table->string('name', 30);
    $table->string('email', 50)->unique(); // email não pode repetir
    $table->string('password');
    $table->timestamps();                  // created_at e updated_at
});
```

**Conceito:** **Migrations** são o "controle de versão do banco de dados". Em vez de criar tabelas na mão pelo pgAdmin, você descreve a estrutura em código PHP. Qualquer pessoa do time roda `php artisan migrate` e tem o banco igualzinho. → *pesquisar: "Laravel migrations"*

### 3.5 Views (Blade) — `resources/views/`

Blade é o motor de templates do Laravel. Estrutura que você montou:

```
layouts/default.blade.php      ← o "esqueleto" da página (HTML base)
 ├── components/header.blade.php
 ├── components/nav.blade.php
 ├── components/sidebar.blade.php
 ├── components/footer.blade.php
 ├── components/alert.blade.php  ← mensagens de sucesso/erro
 └── components/limit.blade.php  ← seletor de "registros por página"

pages/user/index.blade.php     ← lista + filtro de busca
pages/user/form.blade.php      ← formulário de criar/editar
```

**Conceitos para lembrar:**

- **Layout + `@extends` / `@section` / `@yield`**: o `default.blade.php` define o esqueleto e marca o "buraco" do conteúdo com `@yield('content')`. As páginas fazem `@extends('layouts.default')` e preenchem com `@section('content')`. Isso evita repetir `<html><head>...` em toda página. → *pesquisar: "Blade template inheritance"*
- **`@include('components.alert')`**: reaproveita pedaços de HTML (componentes). DRY de novo.
- **`{{ $variavel }}`**: imprime valor **escapando HTML** automaticamente (proteção contra **XSS**). → *pesquisar: "Blade escaping", "XSS"*
- **`@csrf`** (no form): insere um *token* secreto que prova que o formulário veio do seu próprio site, não de um site malicioso. → *pesquisar: "CSRF protection"*
- **`@method('PUT')`** (method spoofing): como o HTML não envia `PUT`/`DELETE`, o Blade gera um campo escondido `_method` e o Laravel "finge" que o método foi PUT. No seu form é elegante:
  ```php
  @method($user->id ? 'PUT' : 'POST')
  ```
  Se o usuário já tem `id` → é edição (PUT); senão → é criação (POST). **O mesmo formulário serve para os dois casos.** → *pesquisar: "Laravel method spoofing"*
- **`old('name', $user->name)`**: se a validação falhar, repreenche o campo com o que o usuário tinha digitado (em vez de apagar tudo). → *pesquisar: "Laravel old input"*
- **`$list->paginate($n)`** + a busca: a listagem é **paginada** (10/25/50/100 por página via `components/limit`). → *pesquisar: "Laravel pagination"*
- **Flash messages** (`Session::flash(...)` + `components/alert`): mensagem que aparece **uma vez** depois de redirecionar (ex.: "usuário criado com sucesso"). → *pesquisar: "Laravel flash session data"*

---

## 4. ⚠️ Pontos de atenção (bugs e melhorias) — leia com calma

Estes são os achados mais valiosos para aprender. Não são "erros bobos" — cada um ensina um conceito. Listados do **mais crítico** para o **cosmético**.

### 🔴 Críticos (quebram a aplicação)

1. **`app/Models/User.php` não importa o trait `HasFactory`** → **erro fatal confirmado**:
   `Trait "App\Models\HasFactory" not found`.
   O `use HasFactory;` dentro da classe procura o trait no namespace atual (`App\Models`), mas ele mora em outro lugar. Falta o import no topo:
   ```php
   use Illuminate\Database\Eloquent\Factories\HasFactory;
   ```
   *(Ou, como o projeto não usa factories ainda, simplesmente remover a linha `use HasFactory;`.)*
   → *conceito: "PHP namespaces e use", "Laravel model factories"*

2. **`UserController::delete()` está vazio** (`//`) e a **rota não bate com o método**: a rota é `Route::delete('/usuarios', ...)` (sem `{id}`), mas o método é `delete(int $id)`. O "Remover usuário" da tela ainda não funciona. Falta: criar um form de DELETE no Blade, ajustar a rota para receber o id e implementar a exclusão (`$user->delete()`).

3. **`edit()` não trata usuário inexistente**: o `else { #TODO }` não retorna nada, o que gera erro se o id não existir. O correto é `abort(404)`. → *pesquisar: "Laravel abort 404"*

### 🟠 Médios (funcionam errado, sem quebrar)

4. **Mensagem de sucesso ao criar nunca aparece**: em `insert()` você escreveu `Session::flash('sucess', ...)` (faltou um "c"), mas o `alert.blade.php` lê `session('success')`. A chave precisa ser idêntica. Corrigir para `'success'`.

5. **`components/alert.blade.php`** tem dois erros no bloco da lista de erros:
   - Aspas faltando: `class="alert alert-danger role="alert"` → falta fechar a aspa depois de `alert-danger`.
   - `<li>{{ $errors }}</li>` imprime o objeto inteiro — deveria ser a variável do loop: `{{ $errorsMessage }}`.

6. **`index.blade.php`, atributo do form malformado**: `method="GET "action=...` — há um espaço dentro das aspas e some o espaço entre os atributos. O certo: `method="GET" action="..."`.

### 🟡 Cosméticos / boas práticas

7. **`<h1>hello word</h1>`** sobrou em `index.blade.php` e `form.blade.php` (texto de teste — e "word" deveria ser "world"). Remover.

8. **Classe `UserLarevel` vs arquivo `UserLaravel.php`**: o nome da classe está com typo e **não bate com o nome do arquivo**, o que viola o **PSR-4** (autoload). É o Model de autenticação padrão do Laravel — hoje não está sendo usado pelo CRUD (que usa o `User`). Decidir: ou conserta o nome, ou remove se não for usar.

9. **`maxlength="50"` no campo Nome** (form) vs **`max:30`** na validação vs **`string('name', 30)`** no banco: os três deveriam combinar. Hoje o HTML deixa digitar 50, mas a validação/banco cortam em 30.

10. **`env('APP_NAME')` no layout**: usar `env()` fora dos arquivos de `config/` é desaconselhado — quando a config é "cacheada" (`php artisan config:cache`) o `env()` retorna `null`. Use `config('app.name')`. → *pesquisar: "Laravel config caching env"*

11. **`Route::group([], ...)` com array vazio** não faz nada. Esse bloco poderia virar um `Route::resource('usuarios', UserController::class)`, que cria as 7 rotas REST de uma vez. → *pesquisar: "Laravel resource controllers"*

12. **URL inconsistente**: editar usa `/usuario/...` (singular) e o resto usa `/usuarios` (plural). Padronizar ajuda.

---

## 5. Resumo do que existe hoje

✅ **Funciona / está montado:**
- Estrutura MVC completa (rotas, controller, model, views).
- Listagem com **busca por nome e email** (query scope + `ilike`).
- **Paginação** com seletor de quantidade por página.
- Formulário **único** para criar e editar (com method spoofing e `old()`).
- **Validação** com regras diferentes para criar vs editar, incluindo email único.
- **Hash de senha** com `bcrypt` e atualização condicional de senha.
- Layout com componentes reaproveitáveis (header, nav, sidebar, footer, alert, limit).

🚧 **Falta / precisa de conserto:**
- Corrigir o import do `HasFactory` (bug que derruba o Model `User`).
- Implementar o **Delete** de verdade (método + rota + botão).
- Tratar usuário inexistente no `edit` (404).
- Corrigir os typos que escondem a mensagem de sucesso e quebram o HTML do alert.

---

## 6. Glossário rápido (para fixar)

| Termo | Em uma frase |
|-------|--------------|
| **MVC** | Separar o app em Model (dados), View (tela) e Controller (lógica). |
| **Rota** | Liga uma URL + verbo HTTP a um método do controller. |
| **Eloquent / ORM** | Mexer no banco usando objetos PHP em vez de SQL puro. |
| **Migration** | Estrutura do banco escrita em código (versionável). |
| **Blade** | Motor de templates do Laravel (`@extends`, `{{ }}`, etc.). |
| **Query Scope** | Filtro reutilizável definido no Model (`scopeSearch`). |
| **CSRF** | Token que protege formulários contra requisições falsas. |
| **Method spoofing** | Simular PUT/DELETE em formulários HTML (`@method`). |
| **Flash message** | Mensagem que aparece uma vez após redirecionar. |
| **Mass assignment** | Preencher vários campos de uma vez (`$fillable` controla isso). |
| **Hash (bcrypt)** | Transformar a senha em algo irreversível antes de salvar. |

---

## 7. Referências para estudar mais

- **Documentação oficial do Laravel** — https://laravel.com/docs (a melhor fonte; leia "Routing", "Controllers", "Eloquent", "Blade", "Validation")
- **Laracasts** — https://laracasts.com (vídeos, vários gratuitos)
- **Laravel Bootcamp** — https://bootcamp.laravel.com (constrói um app do zero passo a passo)
- **PHP The Right Way** — https://phptherightway.com (fundamentos de PHP moderno)
- **MDN — HTTP methods** — https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods

---

> 💡 **Dica final de estudo:** pegue cada item da seção 4 e tente **consertar você mesmo**, pesquisando o conceito antes. Errar e corrigir é o que fixa o aprendizado. Se quiser, posso te ajudar a corrigir um por um, explicando cada passo.
