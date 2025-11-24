### Properties
 - label
 - address
 - type (Enum: premises, apartment, house)

### Tenants
 - first_name
 - last_name
 - email
 - phone
 - notes

### Rental_Contracts (associate tenant with properties)
- start_date
- end_date (optional)
- monthly rent,
- deposit (optional)
- status (Enum)

### PaymentSchedule
- amount
- due_date
- type (Enum rent / deposit)

### PaymentRegistration
- amount
- payment_date,
- notes