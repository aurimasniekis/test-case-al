# Test Case AL

## Usage

### Instalation

```bash
$ git clone git@github.com/aurimasniekis/test-case-al.git
$ cd test-case-al
$ composer install

```

### Database Creation

```bash
$ bin/console doctrine:database:create
$ bin/console doctrine:schema:update --force
$ bin/console doctrine:fixtures:load
```

### Server Start

```bash
$ bin/console server:run
```

### Open API docs

```bash
$ open http://127.0.0.1:8000/
```

### Demo User

```
Username: user
Password: pass
```

## Testing

```bash
$ bin/phpunit
```