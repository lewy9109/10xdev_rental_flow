1. Lista tabel z kolumnami, typami i ograniczeniami

#### app_user
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Identyfikator właściciela/konta |
| email | citext | NOT NULL, UNIQUE | Login, unikalny w całym systemie |
| password_hash | text | NOT NULL | Hash hasła (argon2id) |
| full_name | varchar(150) | NULL | Nazwa wyświetlana |
| is_active | boolean | NOT NULL default true | Flaga aktywności |
| last_login_at | timestamptz | NULL | Ostatnie logowanie |
| created_at | timestamptz | NOT NULL default now() | Data utworzenia |
| updated_at | timestamptz | NOT NULL default now() | Data modyfikacji |

#### password_reset_tokens
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Id wpisu |
| user_id | uuid | NOT NULL FK → app_user(id) ON DELETE CASCADE | Właściciel tokenu |
| token | uuid | NOT NULL, UNIQUE | Jednorazowy token |
| expires_at | timestamptz | NOT NULL | Ważność linku |
| used_at | timestamptz | NULL | Moment użycia |
| created_at | timestamptz | NOT NULL default now() | Data wygenerowania |

#### properties
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Identyfikator nieruchomości |
| owner_id | uuid | NOT NULL FK → app_user(id) ON DELETE CASCADE | Właściciel |
| label | varchar(180) | NOT NULL | Nazwa/nick |
| address_line1 | varchar(255) | NOT NULL | Adres (ulica + nr) |
| address_line2 | varchar(255) | NULL | Uzupełnienie adresu |
| postal_code | varchar(32) | NULL | Kod pocztowy |
| city | varchar(120) | NULL | Miasto |
| country | varchar(120) | NULL | Kraj |
| type | property_type_enum | NOT NULL default 'apartment' | Typ (premises/apartment/house/other) |
| size_sqm | numeric(8,2) | NULL CHECK (size_sqm >= 0) | Powierzchnia orientacyjna |
| notes | text | NULL | Uwagi |
| created_at | timestamptz | NOT NULL default now() | Data utworzenia |
| updated_at | timestamptz | NOT NULL default now() | Data modyfikacji |

#### property_units
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Id jednostki |
| owner_id | uuid | NOT NULL FK → app_user(id) ON DELETE CASCADE | Właściciel |
| property_id | uuid | NOT NULL FK → properties(id) ON DELETE RESTRICT | Powiązana nieruchomość |
| unit_label | varchar(80) | NOT NULL | Numer / nazwa lokalu |
| floor_area_sqm | numeric(8,2) | NULL CHECK (floor_area_sqm >= 0) | Powierzchnia |
| bedrooms | smallint | NULL CHECK (bedrooms >= 0) | Liczba pokoi |
| is_active | boolean | NOT NULL default true | Dostępność jednostki |
| notes | text | NULL | Uwagi |
| created_at | timestamptz | NOT NULL default now() | Data utworzenia |
| updated_at | timestamptz | NOT NULL default now() | Data modyfikacji |
| UNIQUE(owner_id, property_id, unit_label) | | | Zapobiega duplikatom numerów w obrębie nieruchomości |

#### tenants
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Id najemcy |
| owner_id | uuid | NOT NULL FK → app_user(id) ON DELETE CASCADE | Właściciel relacji |
| first_name | varchar(120) | NOT NULL | Imię |
| last_name | varchar(120) | NOT NULL | Nazwisko |
| email | citext | NOT NULL | Email (unikalny per właściciel) |
| phone | varchar(40) | NULL | Telefon |
| notes | text | NULL | Notatki |
| created_at | timestamptz | NOT NULL default now() | Data utworzenia |
| updated_at | timestamptz | NOT NULL default now() | Data modyfikacji |
| UNIQUE(owner_id, email) | | | Jeden email per właściciel |

#### rental_contracts
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Id kontraktu |
| owner_id | uuid | NOT NULL FK → app_user(id) ON DELETE CASCADE | Właściciel |
| property_unit_id | uuid | NOT NULL FK → property_units(id) ON DELETE RESTRICT | Wynajmowana jednostka |
| tenant_id | uuid | NOT NULL FK → tenants(id) ON DELETE RESTRICT | Najemca |
| start_date | date | NOT NULL | Start najmu |
| end_date | date | NULL CHECK (end_date IS NULL OR end_date > start_date) | Koniec |
| monthly_rent_amount | numeric(12,2) | NOT NULL CHECK (monthly_rent_amount > 0) | Czynsz |
| deposit_amount | numeric(12,2) | NULL CHECK (deposit_amount >= 0) | Kaucja |
| status | contract_status_enum | NOT NULL default 'draft' | draft/active/terminated/expired |
| billing_day | smallint | NULL CHECK (billing_day BETWEEN 1 AND 28) | Ustalony dzień rozliczeń |
| notes | text | NULL | Uwagi |
| created_at | timestamptz | NOT NULL default now() | Data utworzenia |
| updated_at | timestamptz | NOT NULL default now() | Data modyfikacji |
| UNIQUE(property_unit_id, status) WHERE status IN ('active') | | | Jeden aktywny kontrakt per jednostka |

#### installments
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Id raty |
| owner_id | uuid | NOT NULL FK → app_user(id) ON DELETE CASCADE | Właściciel |
| contract_id | uuid | NOT NULL FK → rental_contracts(id) ON DELETE CASCADE | Kontrakt |
| sequence_no | integer | NOT NULL CHECK (sequence_no > 0) | Kolejność w harmonogramie |
| type | installment_type_enum | NOT NULL | rent/deposit/adjustment |
| amount | numeric(12,2) | NOT NULL CHECK (amount > 0) | Kwota raty |
| due_date | date | NOT NULL | Termin płatności |
| is_manual_override | boolean | NOT NULL default false | Czy użytkownik zmienił ręcznie |
| description | varchar(255) | NULL | Opis |
| created_at | timestamptz | NOT NULL default now() | Data utworzenia |
| updated_at | timestamptz | NOT NULL default now() | Data modyfikacji |
| UNIQUE(contract_id, sequence_no) | | | Stała kolejność |

#### payments
| Kolumna | Typ | Ograniczenia | Opis |
| --- | --- | --- | --- |
| id | uuid | PK, default gen_random_uuid() | Id płatności |
| owner_id | uuid | NOT NULL FK → app_user(id) ON DELETE CASCADE | Właściciel |
| contract_id | uuid | NOT NULL FK → rental_contracts(id) ON DELETE RESTRICT | Redundantny FK wygodny dla raportów |
| installment_id | uuid | NOT NULL FK → installments(id) ON DELETE CASCADE | Rata |
| amount | numeric(12,2) | NOT NULL CHECK (amount > 0) | Zapłacona kwota |
| payment_date | date | NOT NULL | Data płatności |
| received_at | timestamptz | NOT NULL default now() | Rejestracja |
| notes | text | NULL | Uwagi |

#### overdue_installment_summary (widok bezpieczeństwa)
| Kolumna | Typ | Opis |
| --- | --- | --- |
| owner_id | uuid | Właściciel |
| contract_id | uuid | Kontrakt |
| installment_id | uuid | Rata |
| tenant_id | uuid | Najemca |
| overdue_amount | numeric(12,2) | Kwota zaległa (amount - suma płatności) |
| status | text | pending/late/partially_late/paid wyliczane w SELECT |
| days_overdue | integer | Liczba dni po terminie |

2. Relacje między tabelami
- app_user 1:N → properties, property_units, tenants, rental_contracts, installments, payments (owner izoluje dane).
- properties 1:N → property_units (ON DELETE RESTRICT zabezpiecza przed usunięciem przy aktywnych jednostkach).
- property_units 1:N → rental_contracts; constraint zapewnia, że jednostka ma tylko jeden aktywny kontrakt naraz.
- tenants 1:N → rental_contracts; jeden najemca może posiadać wiele kontraktów w czasie.
- rental_contracts 1:N → installments → payments (łańcuch zachowuje spójność harmonogramu i płatności).
- password_reset_tokens N:1 → app_user; widok overdue_installment_summary dołącza tenants, properties i payments dla dashboardu.

3. Indeksy
- app_user: UNIQUE(email); INDEX(last_login_at) pod raporty aktywności.
- password_reset_tokens: INDEX(user_id, expires_at) dla wygaszeń batchowych.
- properties: INDEX(owner_id, lower(label)), INDEX(owner_id, type) dla filtrowania dashboardu.
- property_units: INDEX(property_id), INDEX(owner_id, is_active), UNIQUE(owner_id, property_id, unit_label).
- tenants: INDEX(owner_id, lower(last_name)), UNIQUE(owner_id, email).
- rental_contracts: INDEX(owner_id, status), INDEX(tenant_id, status), INDEX(property_unit_id, status) weryfikuje zakaz wielu aktywnych umów.
- installments: INDEX(owner_id, due_date), INDEX(contract_id, due_date), PARTIAL INDEX ON (contract_id, due_date) WHERE due_date < now() dla wykrywania zaległości.
- payments: INDEX(installment_id), INDEX(contract_id, payment_date), INDEX(owner_id, payment_date) dla raportów przepływów.
- overdue_installment_summary: MATERIALIZED VIEW INDEX(owner_id, status, property_id) jeżeli stosowana do dashboardu.

4. Zasady PostgreSQL (RLS)
- Wymagane ustawienie `SET app.current_owner_id = '<uuid>';` po uwierzytelnieniu.
- Dla każdej tabeli z kolumną owner_id: `ALTER TABLE <table> ENABLE ROW LEVEL SECURITY;`
  `CREATE POLICY owner_isolation ON <table> USING (owner_id = current_setting('app.current_owner_id')::uuid);`
- Dla `app_user`: polityka `self_access` ogranicza SELECT/UPDATE do `id = current_setting('app.current_owner_id')::uuid`.
- Widoki (np. overdue_installment_summary) definiowane jako `WITH LOCAL CHECK OPTION SECURITY BARRIER`, a zapytania bazują na politykach tabel źródłowych.
- Role serwisowe (np. cron) otrzymują alternatywną politykę `service_access` wykorzystującą `has_any_column_privilege()` lub dedykowany `jwt.claims.owner_id`.

5. Dodatkowe uwagi
- Typy enum: `property_type_enum ('premises','apartment','house','other')`, `contract_status_enum ('draft','active','terminated','expired')`, `installment_type_enum ('rent','deposit','adjustment')`. Przechowywanie enumów w bazie upraszcza walidację EasyAdmin.
- Status rat (pending/late/partially_late/paid) nie są kolumną — liczone w zapytaniach lub widoku, co eliminuje ryzyko driftu przy edycji płatności.
- Kolumna owner_id w tabelach podrzędnych pozwala na proste RLS i filtrowanie; spójność zapewniają constrainty FK + trigger, który waliduje, że owner_id = parent.owner_id.
- ON DELETE RESTRICT na properties/property_units/rental_contracts uniemożliwia usunięcie zasobu powiązanego z aktywnymi kontraktami, spełniając wymagania biznesowe.
- Harmonogram generowany jest aplikacyjnie (Symfony service) i zapisuje sekwencję w installments; manualne zmiany ustawiają `is_manual_override = true`, co pozwala audytować ingerencję użytkownika.
