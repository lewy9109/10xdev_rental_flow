# AI Rules for PHP 8.2 & Symfony 7

Guidelines for building scalable, secure, and maintainable backend applications using **PHP 8.2** and **Symfony 7**.  
These rules reflect the highest standards of clean code, architecture, and backend engineering.

---

## BACKEND

## Guidelines for PHP 8.2

### CORE PHP

- Always use **strict types** (`declare(strict_types=1);`) in every PHP file
- Use modern PHP 8.x types:  
  *union types*, *mixed*, *never*, *true/false types*, *readonly properties*
- Avoid complex associative arrays — prefer **DTOs, collections, or value objects**
- Use **enums** instead of class constants whenever possible
- Prefer **match expressions** over large `switch` or `if/else` blocks
- Every method must declare an explicit return type, including `void`
- Use **first-class callables** to improve code readability and composability
- Use **constructor property promotion** when it improves clarity
- Methods should not return `null` unless absolutely required — prefer:
    - optional-like wrappers
    - default values
    - domain exceptions
- Use **readonly classes** for DTOs, commands, and queries
- Avoid magic methods (e.g., `__set`, `__call`) as they break static analysis
- Fluent setters

---

#### VALIDATION

- Use **Symfony Validator** instead of custom validation mechanisms
- Prefer **attribute-based validation** (e.g., `#[Assert\NotBlank]`) over YAML/XML
- Create **custom validators** for advanced business rules
- Validate:
    - Request DTOs
    - Application commands and queries

---

#### EASYADMIN / ADMIN PANEL
- The EasyAdmin frontend should be only an interface, while the backend must still:
  - validate all data
  - use DTOs / Commands
  - avoid putting presentation logic inside entities
- Never allow EasyAdmin to modify entities without passing through:
  - a Command Handler
  - an application service
- EasyAdmin forms should be:
  - clean and concise
  - using Value Objects wherever possible
- Use the CrudController only for rendering and delegating logic

---