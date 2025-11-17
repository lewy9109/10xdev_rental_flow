# Dokument Wymagań Produktowych (PRD) – Aplikacja do Zarządzania Najmem

## 1. Kontekst i cel produktu

Aplikacja webowa dla **pojedynczego właściciela nieruchomości** (z możliwością rozwoju do **multi-tenant SaaS** w przyszłości), która pomaga:

- rejestrować nieruchomości i lokale,
- przypisywać najemców,
- tworzyć proste umowy najmu,
- generować harmonogram płatności,
- rejestrować wpłaty,
- automatycznie wykrywać zaległości,
- prezentować na jednym ekranie „kto zalega i ile”.

Produkt jest narzędziem **operacyjnym** (nie systemem księgowym).

- Termin docelowego uruchomienia MVP: **16 grudnia**
- Zespół: **1 fullstack developer (Symfony + EasyAdmin)**

---

## 2. Zakres MVP

### 2.1. W zakresie

1. **Rejestracja i logowanie właściciela (user account)**.
2. **CRUD** dla:
   - nieruchomości i lokali,
   - najemców,
   - umów najmu (prosta encja, bez przechowywania pliku umowy),
   - harmonogramów płatności (rat),
   - wpłat (payments).
3. **Generowanie harmonogramu płatności**:
   - stała miesięczna kwota,
   - opcjonalny jednorazowy depozyt.
4. **Automatyczne wyliczanie statusów płatności** (on the fly).
5. **Dashboard właściciela**: „kto zalega i ile”.
6. **Podstawowe bezpieczeństwo (HTTPS, hasła hashowane itd.).**
7. **Minimalny pipeline CI/CD** z GitHub Actions + Docker.
8. **Uruchomienie w środowisku produkcyjnym pod publicznym URL.**

### 2.2. Poza zakresem (MVP)

- Integracje z systemami bankowymi / masowe płatności.
- Powiadomienia email/SMS o zaległościach.
- Przechowywanie plików PDF umów.
- Zaawansowane raporty/eksporty.
- Wiele walut, wiele języków (poza PL).
- Zaawansowany system ról (więcej niż właściciel).
- Rozbudowane metryki biznesowe / analityka.

---

## 3. Użytkownik docelowy i scenariusze

### 3.1. Użytkownik

- Osoba fizyczna lub mały właściciel kilku/kilkunastu mieszkań.
- Potrzebuje prostego, czytelnego interfejsu.
- Korzysta głównie na **desktopie**.
- Aplikacja powinna być **responsywna** (minimum poprawne działanie na telefonie).

### 3.2. Kluczowe scenariusze (high-level)

1. **Codzienny przegląd zaległości**:
   - właściciel loguje się,
   - wchodzi na dashboard,
   - widzi listę najemców z zaległościami i kwotami,
   - przechodzi do szczegółów konkretnej nieruchomości / najemcy.

2. **Dodanie nowego najmu**:
   - dodanie nieruchomości i lokalu,
   - dodanie najemcy,
   - utworzenie umowy najmu,
   - automatyczne wygenerowanie harmonogramu.

3. **Rejestracja wpłat**:
   - przejście do listy rat/harmonogramu,
   - dodanie jednej lub kilku wpłat do raty,
   - podgląd zaktualizowanego statusu raty.

---

## 4. Kluczowe funkcjonalności

### 4.1. Konta użytkowników (właściciel)

**Funkcje:**

- Rejestracja użytkownika (email + hasło).
- Logowanie.
- Reset hasła (link wysyłany na email).
- Wylogowanie.

**Założenia:**

- Tylko jedna rola: `owner`.
- Model danych przygotowany na dodanie ról w przyszłości (np. tabela `roles` / `user_roles`), ale bez UI do zarządzania rolami.

---

### 4.2. Nieruchomości i lokale

**Encje:**

- **Property (Nieruchomość)** – np. budynek:
  - `id`
  - nazwa / opis (np. „Mieszkanie ul. X 12”),
  - adres (ulica, nr, kod, miasto).
  - numer lokalu,
  - metraż (m²),
  - typ lokalu (opcjonalne pole tekstowe, np. „mieszkanie”, „biuro”).

**Funkcje:**

- Dodaj/edytuj/usuń nieruchomość.
- Dodaj/edytuj/usuń lokal.
- Lista lokali przypisanych do nieruchomości.

---

### 4.3. Najemcy

**Minimalne dane najemcy:**

- `id`
- imię,
- nazwisko,
- email (do kontaktu),
- telefon (opcjonalnie),
- notatki (opcjonalnie).

**Funkcje:**

- CRUD najemców.
- Przypisanie najemcy do lokalu poprzez umowę najmu (patrz niżej).

---

### 4.4. Umowy najmu (Contract)

**Cel w MVP:** logiczny zapis umowy (bez przechowywania plików).

**Pola:**

- `id`
- `tenant_id` (najemca),
- `property_id` (lokal),
- data startu umowy,
- data końca umowy (opcjonalna – może być bezterminowa),
- kwota czynszu miesięcznego (PLN),
- depozyt (PLN, opcjonalny – 0 jeśli brak),
- status umowy (np. aktywna / zakończona).

**Funkcje:**

- Dodanie umowy:
  - wybór najemcy,
  - wybór lokalu,
  - konfiguracja kwoty, dat, depozytu.
- Edycja wybranych pól (np. daty końca, kwoty).
- Widok szczegółów umowy z listą powiązanych rat (harmonogram).

**Uproszczenie MVP:**

- Zmiana parametrów umowy **nie przebudowuje** automatycznie istniejącego harmonogramu. Nowa logika przebudowy może zostać dodana później.

---

### 4.5. Harmonogram płatności (Schedule & Installments)

Harmonogram jest tworzony **automatycznie** przy utworzeniu umowy.

**Założenia minimalne:**

- Stała kwota miesięczna (z pola umowy).
- Wyliczenie rat od daty startu do daty końca (lub do określonej liczby miesięcy).
- Opcjonalny depozyt jako **osobna** płatność (jednorazowa, np. na początku umowy).

**Pola raty (Installment):**

- `id`
- `contract_id`
- typ raty: `rent` / `deposit`,
- kwota (PLN),
- data wymagalności (`due_date`).

**Funkcje:**

- Automatyczne generowanie pełnej listy rat.
- **Ręczna edycja pojedynczej raty**:
  - zmiana daty wymagalności,
  - zmiana kwoty.

---

### 4.6. Rejestracja wpłat (Payments)

Właściciel **ręcznie** rejestruje wpłaty.

**Pola wpłaty:**

- `id`
- `installment_id` (rata, do której przypisana jest wpłata),
- kwota,
- data wpłaty (`payment_date`),
- notatka (opcjonalnie, np. „gotówka”, „przelew”).

**Założenia:**

- Jedna rata może mieć **wiele wpłat**.
- Suma wpłat porównywana z kwotą raty.

**Funkcje:**

- Dodaj wpłatę powiązaną z ratą.
- Podgląd listy wpłat dla raty.
- Możliwość edycji/usunięcia błędnie wprowadzonej wpłaty.

---

### 4.7. Statusy płatności i wykrywanie zaległości

Status raty **liczony dynamicznie** na podstawie:

- `due_date`,
- sumy wpłat,
- dat wpłat.

**Statusy:**

- `pending` – przed terminem, suma wpłat < kwota raty.
- `late` – po terminie, suma wpłat = 0.
- `partially_late` – po terminie, suma wpłat > 0, ale < kwoty raty.
- `paid` – suma wpłat ≥ kwota raty (nadpłaty nie są analizowane w MVP).

**Dodatkowe reguły:**

- Za „po terminie” przyjmujemy datę późniejszą niż `due_date` (np. po północy dnia następnego).
- Status obliczany na żywo przy wyświetlaniu:
  - list harmonogramu,
  - dashboardu.

---

### 4.8. Dashboard właściciela

**Cel:** „pokazanie kto zalega i ile”.

**Minimalne elementy:**

1. **Kafelek podsumowujący:**
   - liczba zaległych rat (status `late` + `partially_late`),
   - łączna kwota zaległości (suma brakujących kwot do pełnego `paid`).

2. **Tabela „Najemcy z zaległościami”:**
   - najemca (imię i nazwisko),
   - nieruchomość / lokal,
   - liczba zaległych rat,
   - łączna kwota zaległości,
   - link do szczegółów najemcy / umowy.

3. **Prosty filtr:**
   - po nieruchomości (`Property`),
   - po statusie (np. tylko `late` vs `late + partially_late`).

**UI:**

- Projektowane pod **desktop**.
- Layout powinien być **responsywny** (tabele przewijalne na mobile).

---

## 5. Przepływ użytkownika end-to-end (MVP)

Scenariusz referencyjny (pod test e2e):

1. Rejestracja konta właściciela.
2. Logowanie.
3. Dodanie nieruchomości i lokalu.
4. Dodanie najemcy.
5. Utworzenie umowy:
   - wybór lokalu i najemcy,
   - ustalenie kwoty miesięcznej,
   - opcjonalny depozyt,
   - daty startu i końca.
6. Automatyczne wygenerowanie harmonogramu.
7. Dodanie jednej lub kilku wpłat do wybranych rat.
8. Sprawdzenie:
   - poprawnych statusów rat,
   - poprawnego wyświetlania zaległości na dashboardzie.

---

## 6. Model danych – wysoki poziom

**Główne encje:**

- `User` (owner),
- `Property`,
- `Tenant`,
- `Contract`,
- `Installment`,
- `Payment`.

**Relacje:**

- `User` 1..n `Property`,
- `Property` 1..n `Contract`,
- `Tenant` 1..n `Contract`,
- `Contract` 1..n `Installment`,
- `Installment` 1..n `Payment`.

**Waluta:**  
- **PLN** (na sztywno w MVP; pole „currency” można pominąć lub ustawić jako stałą).

**Język interfejsu:**  
- **Polski**, z możliwością łatwego dodania i18n w przyszłości (np. system tłumaczeń Symfony).

---

## 7. Wymagania niefunkcjonalne

### 7.1. UX/UI

- Priorytet: **desktop**.
- Responsywność: minimalne wsparcie mobile (czytelne przeglądanie tabel, podstawowe formularze).
- Wykorzystanie **EasyAdmin** do:
  - CRUD encji,
  - podstawowych widoków list i formularzy.
- Customowy dashboard zbudowany w oparciu o EasyAdmin / własny kontroler.

### 7.2. Bezpieczeństwo

- Cała aplikacja dostępna wyłącznie przez **HTTPS**.
- Hasła:
  - hashowane (np. bcrypt/argon),
  - minimalne wymagania złożoności (np. minimalna długość).
- Reset hasła:
  - mechanizm wysyłany na email z tokenem.
- Podstawowe logowanie błędów bez wrażliwych danych.
- **Rate limiting** na logowanie (np. ograniczenie prób na IP / konto w czasie).

---

## 8. Technologie, środowiska i CI/CD

### 8.1. Stos technologiczny (MVP)

- Backend: **Symfony** (aktualna stabilna wersja).
- Panel: **EasyAdmin Bundle**.
- Baza danych: **MariaDb**.
- Konteneryzacja: **Docker** (Dockerfile + `docker-compose` dla dev i prod).
- Frontend: szablony **Twig** / EasyAdmin (bez SPA).

### 8.2. Środowiska

1. **Dev lokalny** – Docker + `docker-compose`.
2. **Test/Staging** – pojedyncze środowisko do deployu z CI:
   - używane do ręcznych testów e2e.
3. **Produkcja** – jeden serwer/VPS z Dockerem:
   - dostępny pod publicznym URL.

### 8.3. CI/CD (GitHub Actions – minimalny pipeline)

**Pipeline dla gałęzi `main`:**

1. Checkout repo.
2. Budowa aplikacji:
   - `composer install`,
   - (opcjonalnie) build assetów, jeśli potrzebne.
3. Uruchomienie:
   - testów jednostkowych (PHPUnit),
   - prostego testu e2e (smoke test: „dodaj właściciela → dodaj umowę → sprawdź harmonogram”).
4. Budowa obrazu Docker.
5. Deploy na:
   - staging,
   - po akceptacji – produkcję (np. przez SSH + `docker-compose pull` / `up`).

---

## 9. Ograniczenia i ryzyka

**Ograniczenia:**

- Zespół: **1 developer** – ograniczony czas na:
  - skomplikowany UI,
  - rozbudowane testy,
  - złożony CI/CD.
- Brak doświadczenia z CI/CD i e2e.
- Sztywny termin: **16 grudnia**.

**Ryzyka:**

- Opóźnienia przy konfiguracji CI/CD.
- Przeinwestowanie w UI kosztem funkcjonalności.

**Mitigacje (MVP):**

- Użycie **EasyAdmin** do maksymalnego uproszczenia widoków CRUD.
- Jeden prosty test e2e zamiast rozbudowanego zestawu.
- Minimalny pipeline w GitHub Actions (1 workflow).
- Świadome cięcia:
  - jeśli termin zagrożony, pierwsze do obcięcia są:
    - zaawansowane filtry na dashboardzie,
    - niektóre pola/notatki,
    - bardziej złożone raporty.

---

## 10. Kryteria akceptacji MVP

MVP uznaje się za dostarczone, jeśli:

1. **CRUD działa** dla:
    - `Property`, `Tenant`, `Contract`,
    - `Installment` (pośrednio przez generowanie),
    - `Payment`.
2. **Harmonogram płatności**:
    - generuje się automatycznie po utworzeniu umowy (stała kwota miesięczna + opcjonalny depozyt),
    - raty mają poprawne daty i kwoty,
    - można ręcznie zmienić datę i kwotę pojedynczej raty.
3. **Statusy płatności**:
    - poprawnie wyliczane jako `pending`, `late`, `partially_late`, `paid` dla przykładowych scenariuszy:
        - brak wpłaty przed terminem,
        - częściowa wpłata po terminie,
        - pełna wpłata po terminie,
        - pełna wpłata przed terminem.
4. **Dashboard**:
    - pokazuje:
        - liczbę zaległych rat,
        - łączną kwotę zaległości,
        - listę najemców z zaległościami („kto zalega i ile”) z możliwością przejścia do szczegółów.
5. **Scenariusz end-to-end** (test ręczny lub automatyczny) przechodzi poprawnie:
    - rejestracja użytkownika → logowanie → dodanie nieruchomości i lokalu → dodanie najemcy → utworzenie umowy → wygenerowanie harmonogramu → rejestracja wpłat → sprawdzenie zaległości na dashboardzie.
6. **CI/CD**:
    - istnieje działający workflow w GitHub Actions:
        - buduje aplikację,
        - uruchamia testy jednostkowe + 1 test e2e (smoke),
        - wykonuje deploy na środowisko produkcyjne.
7. **Produkcja**:
    - aplikacja działa na publicznym URL,
    - dostęp po HTTPS,
    - możliwe jest założenie nowego konta właściciela i przejście przez scenariusz e2e.
