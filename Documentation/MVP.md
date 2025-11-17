## MVP - RentalFlow

### Główny problem
Użytkownicy mogą zarządzać mieszkaniami, najemcami, umowami, płatnościami. Aplikacja automatycznie wykrywa zaległe płatności i wysyła alerty.

### Najmniejszy zestaw funkcjonalności
- Rejestrowanie wynajmowanych lokali / budynkow
- Rejestrowanie kosztow dla danej nieruchomosci (per month/year) oraz sprawdzenie statusu czy jest oplacony
- Rejestrowanie najemcy oraz przypisanie do lokalu, dodanie kwoty jaka najemca musi wplacic na poczet najmu i do kiedy.
- Generowanie harmonogramów płatności

### Co NIE wchodzi w zakres MVP
- Automatyzacja i integracja z systemem bankowym np. (System Identyfikacji Masowych Płatności - ING)
- Dodawanie i przechowywanie umow z najemca
- Powiadomienia email o zaleglosciach

### Kryteria sukcesu

- Możliwość pełnego CRUD dla kluczowych encji:
  - właściciel może dodać, edytować, usunąć i przeglądać:
    - nieruchomości
    - najemców
    - płatności

- Poprawne generowanie harmonogramu płatności:
  po utworzeniu najemcy system automatycznie generuje listę płatności z poprawnymi datami i kwotami.
  
- Automatyczne wykrywanie zaległości w czasie rzeczywistym:
  - system prawidłowo oznacza płatność jako:
    - pending — przed terminem
    - late — po terminie i bez wpłaty
    - partially_late — wpłacono część, ale po terminie
    - paid — cała kwota pokryta

- Dashboard właściciela pokazuje kluczowe dane:
  - liczba zaległych płatności
  - łączna kwota zaległości
  - lista najemców z zaległościami
  - status płatności dla każdej nieruchomości

- Pełny przepływ użytkownika end-to-end działa poprawnie:
  - właściciel może dodać nieruchomość → najemcę → umowę → wygenerować harmonogram 
  - ten proces jest sprawdzony testem e2e.

- Stabilny pipeline CI/CD:
  - pipeline buduje aplikację
  - pipeline uruchamia testy jednostkowe i e2e
  - pipeline kończy się statusem success

- Aplikacja działa w środowisku produkcyjnym:
  - dostępna pod publicznym URL
  - pipeline wdraża aplikację automatycznie na każdą zmianę (np. main/master)
