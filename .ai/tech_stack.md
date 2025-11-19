# Tech stack

## Wymagania bazowe
- PHP >= 8.2 wraz z rozszerzeniami `ext-ctype` i `ext-iconv`.
- Symfony 7.3.* (framework-bundle, runtime) jako fundament aplikacji.
- Symfony Flex do automatycznej konfiguracji pakietów i zarządzania receptami.

## Biblioteki aplikacyjne
- `easycorp/easyadmin-bundle` (^4.27) — panel administracyjny do zarządzania danymi w aplikacji.
- `symfony/console` — warstwa CLI wykorzystywana przez skrypty i komendy domenowe.
- `symfony/dotenv` — obsługa zmiennych środowiskowych z plików `.env`.
- `symfony/yaml` — wsparcie dla konfiguracji i danych w formacie YAML.

## Narzędzia developerskie
- `phpro/grumphp` (^2.17) — zestaw kontroli jakości uruchamianych przed commitami (CS, testy, analizy).
- `phpstan/phpstan` (^2.1) — statyczna analiza kodu na poziomie zgodnym z konfiguracją repozytorium.
- `symfony/maker-bundle` (^1.64) — generator szkieletów klas (kontrolery, DTO, formularze itp.) ułatwiający rozwój.

