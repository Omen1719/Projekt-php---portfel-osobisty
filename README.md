# Portfel osobisty

Aplikacja webowa do zarządzania portfelem osobistym (Symfony 7.4). Po
zalogowaniu każdy użytkownik prowadzi własne, odizolowane od innych: portfele,
operacje (przychody/wydatki), kategorie i tagi. Dostępne są m.in. paginacja,
sortowanie, filtrowanie operacji (po kategorii, tagu i zakresie dat) z saldem
dla okresu, opisy operacji w Markdown, panel administratora oraz wersja
językowa polska i angielska. Projekt działa w trybie deweloperskim.

## Wymagania

- [Docker](https://www.docker.com/products/docker-desktop) i Docker Compose
- środowisko [docker-symfony-starter-kit](https://bitbucket.org/tchojna/docker-symfony-starter-kit) (Apache, PHP 8.5, MySQL 8.3)

Domyślne `DATABASE_URL` w `.env` jest już ustawione pod to środowisko
(`mysql://symfony:symfony@mysql:3306/symfony`), więc projekt uruchamia się bez
dodatkowej konfiguracji.

## Instalacja

1. Umieść kod tego projektu w katalogu `app/` starter-kitu.

2. Zbuduj i uruchom środowisko, a następnie wejdź do kontenera PHP:

   ```bash
   ./build-env.sh           # lub build-env.ps1 na Windows
   docker compose exec php bash
   cd app
   ```

3. Wewnątrz kontenera zainstaluj zależności, zbuduj bazę i załaduj dane
   przykładowe (seedery – [DoctrineFixturesBundle](https://symfony.com/doc/master/bundles/DoctrineFixturesBundle)):

   ```bash
   composer install
   php bin/console doctrine:migrations:migrate --no-interaction
   php bin/console doctrine:fixtures:load --no-interaction
   ```

4. Aplikacja jest dostępna pod adresem <http://localhost:8000>.

## Dane logowania (po załadowaniu fixtures)

| Rola          | E-mail              | Hasło       |
|---------------|---------------------|-------------|
| Administrator | `admin@example.com` | `admin1234` |

## Technologie

- **Symfony 7.4**, PHP 8.2+, Twig, Bootstrap 5
- **Doctrine ORM** – encje, repozytoria, migracje (`migrations/`) i fixtures (`src/DataFixtures/`)
- **Symfony Security** – logowanie formularzowe, role, Voters (kontrola własności)
- **KnpPaginator** – paginacja i sortowanie; tłumaczenia XLIFF (PL / EN)
