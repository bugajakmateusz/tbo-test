# API Documentation

### Base URL

```
/api/
```

---

## Endpoints

### Authentication

```
POST /json/login
```
**Description:**
Handles user authentication.

**Request Body:**
```json
{
  "username": "login@email.com",
  "password": "test123"
}
```

**Responses:**
- **200 OK** - When credentials are correct
```json
{
  "status": true
}
```
- **401 Unauthorized** - When credentials are incorrect
```json
{
  "error": "Invalid credentials.",
  "errorMessage": "login.invalid-credentials"
}
```

## JSON:API

This documentation outlines the API endpoints following the JSON\:API standard. The API handles resources for snacks, vending machines, users, and transactions.


### Error Handling

If the user does not have the required permissions to access an endpoint, the API returns a `403 Forbidden` response.

If invalid data is provided, the API returns a `422 Unprocessable Entity` response with detailed validation errors.

**Example Response:**

```json
{
  "type": "https://httpstatuses.com/422",
  "title": "Niepoprawne dane żądania",
  "status": 422,
  "detail": "Przesłano błędne dane w polach: 'name', 'quantity'.",
  "errors": {
    "name": ["To pole nie może być puste."],
    "quantity": ["Wartość musi być większa niż 0."]
  }
}
```

### Snacks

#### Machine Snack Creation

**Validation:**
- `position`: Cannot be blank, maximum length defined by system constant
- `quantity`: Must be greater than 0
- `snack`: Must be provided as a relationship
- `machine`: Must be provided as a relationship
- **Custom Validation:**
    - `MachinePositionTaken`: Only one snack can occupy a position in a machine (quantity > 0)
    - `NotEnoughQuantity`: Cannot add a snack that is out of stock

**Access Control:**

- Roles: ROLE\_COURIER

```
POST /json-api/machine-snacks
```

**Description:**
Creates a new machine snack.

**Request Body:**
```json
{
  "data": {
    "type": "machine-snacks",
    "attributes": {
      "position": "A1",
      "quantity": 10
    },
    "relationships": {
      "snack": {
        "data": { "type": "snacks", "id": "5" }
      },
      "machine": {
        "data": { "type": "machines", "id": "12" }
      }
    }
  }
}
```

**Request Body:**

```json
{
  "data": {
    "type": "machine-snacks",
    "attributes": {
      "position": "A1",
      "quantity": 10
    }
  }
}
```

---

#### Update Machine Snack

**Validation:**

- `quantity`: Must be greater than 0

**Access Control:**

- Roles: ROLE\_COURIER

```
PATCH /json-api/machine-snacks/{machineSnackId}
```

**Description:**
Updates an existing machine snack.

**Path Parameters:**

- `machineSnackId` (integer) - Required

**Request Body:**

```json
{
  "data": {
    "id": "1",
    "type": "machine-snacks",
    "attributes": {
      "quantity": 15
    }
  }
}
```

---

### Snacks

#### List Snacks

**Access Control:**

- Roles: ROLE\_OFFICE\_MANAGER, ROLE\_LOGISTIC\_MANAGER, ROLE\_COURIER

```
GET /json-api/snacks
```

**Description:**
Returns a list of all snacks. This endpoint does not support filtering by fields.

**Schema:**

- `name` (string)
- `quantity` (integer)

**Pagination:**
You can paginate results by using query parameters `page[number]` and `page[size]`.

**Example Request:**

```
GET /json-api/snacks?page[number]=2
```

**Example Response:**

```json
{
  "meta": {
    "totalItems": 81
  },
  "data": [
    {
      "type": "snacks",
      "id": "112",
      "attributes": {
        "name": "et sed dicta",
        "quantity": 196
      }
    }
  ]
}
```

---

#### Create Snack

**Validation:**

- `name`: Cannot be blank, maximum length of 255 characters
- `quantity`: Must be greater than 0

**Access Control:**

- Roles: ROLE\_OFFICE\_MANAGER

```
POST /json-api/snacks
```

**Description:**
Creates a new snack.

**Request Body:**

```json
{
  "data": {
    "type": "snacks",
    "attributes": {
      "name": "Chips",
      "quantity": 20
    }
  }
}
```

---

#### Update Snack

**Validation:**

- `name`: Cannot be blank, maximum length of 255 characters

**Access Control:**

- Roles: ROLE\_OFFICE\_MANAGER

```
PATCH /json-api/snacks/{snackId}
```

**Description:**
Updates an existing snack.

**Path Parameters:**

- `snackId` (integer) - Required

**Request Body:**

```json
{
  "data": {
    "id": "1",
    "type": "snacks",
    "attributes": {
      "name": "Updated Snack Name"
    }
  }
}
```

---

### Machines

#### List Machines

**Access Control:**

- Roles: ROLE\_OFFICE\_MANAGER, ROLE\_LOGISTIC\_MANAGER, ROLE\_COURIER

```
GET /json-api/machines
```

**Description:**
Returns a list of vending machines. Supports field selection.

**Default Response Fields:**

- `location` (string)

**Available Fields:**

- `location`
- `positionsNumber`
- `positionsCapacity`

**Pagination:**
You can paginate results by using query parameters `page[number]` and `page[size]`.

**Example Request with Field Selection:**

```
GET /json-api/machines?fields[machines]=location,positionsNumber,positionsCapacity
```

**Example Response:**

```json
{
    "meta": {
        "totalItems": 80
    },
    "data": [
        {
            "type": "machines",
            "id": "81",
            "attributes": {
                "location": "Zamkowa 96A/69",
                "positionsNumber": 739251,
                "positionsCapacity": 450753
            }
        }
    ]
}
```

---

#### Create Machine

**Validation:**

- `location`: Cannot be blank, maximum length defined by system constant
- `positionsNumber`: Cannot be blank, must be greater than or equal to 0
- `positionsCapacity`: Cannot be blank, must be greater than or equal to 0

**Access Control:**

- Roles: ROLE\_OFFICE\_MANAGER

```
POST /json-api/machines
```

**Description:**
Creates a new vending machine.

**Request Body:**

```json
{
  "data": {
    "type": "machines",
    "attributes": {
      "location": "Building A",
      "positionsNumber": 10,
      "positionsCapacity": 50
    }
  }
}
```

---

#### Machine Details

```
GET /json-api/machines/{machineId}
```

**Description:**
Fetch details of a specific vending machine.

**Path Parameters:**

- `machineId` (integer) - Required

**Schema:**

- `location` (string)
- `positionsNumber` (integer)
- `positionsCapacity` (integer)

**Example Response:**

```json
{
    "data": {
        "type": "machines",
        "id": "45",
        "attributes": {
            "location": "Grunwaldzka 12A",
            "positionsNumber": 30,
            "positionsCapacity": 300
        }
    }
}
```

---

#### Update Machine

**Validation:**

- `location`: Cannot be blank if changed, maximum length defined by system constant
- `positionsNumber`: Must be greater than or equal to 0
- `positionsCapacity`: Must be greater than or equal to 0

**Access Control:**

- Roles: ROLE\_OFFICE\_MANAGER

```
PATCH /json-api/machines/{machineId}
```

**Description:**
Updates vending machine details.

**Path Parameters:**

- `machineId` (integer) - Required

**Request Body:**

```json
{
  "data": {
    "id": "2",
    "type": "machines",
    "attributes": {
      "location": "Building B",
      "positionsNumber": 20,
      "positionsCapacity": 100
    }
  }
}
```

---

#### Remove Machine

```
DELETE /json-api/machines/{machineId}
```

**Description:**
Deletes a vending machine.

**Path Parameters:**

- `machineId` (integer) - Required

---

### Users

#### List Users

**Access Control:**

- Roles: ROLE\_USER

```
GET /json-api/users
```

**Description:**
Returns a list of users. Field filtering is not supported.

**Schema:**

- `name` (string)
- `surname` (string)
- `roles` (array)
- `email` (string)

**Pagination:**
You can paginate results by using query parameters `page[number]` and `page[size]`.

**Example Response:**

```json
{
    "meta": {
        "totalItems": 1
    },
    "data": [
        {
            "type": "users",
            "id": "11",
            "attributes": {
                "email": "krystian.andrzejewski@pietrzak.co.pl",
                "name": "Ida",
                "surname": "Dudek",
                "roles": [
                    "ROLE_USER",
                    "ROLE_ADMIN"
                ]
            }
        }
    ]
}
```

**Description:**
Returns a list of users. Field filtering is not supported.

**Schema:**

- `name` (string)
- `surname` (string)
- `roles` (array)
- `email` (string)

**Pagination:**
You can paginate results by using query parameters `page[number]` and `page[size]`.

---

#### Create User

**Validation:**

- `email`: Cannot be blank, must be a valid email, maximum length defined by system constant, must not already exist
- `password`: Cannot be blank, must be between minimum and maximum length defined by system constants
- `name`: Cannot be blank, maximum length defined by system constant
- `surname`: Cannot be blank, maximum length defined by system constant
- `roles`: Cannot be blank, must be valid roles

**Access Control:**

- Roles: ROLE\_ADMIN

```
POST /json-api/users
```

**Description:**
Creates a new user.

**Request Body:**

```json
{
  "data": {
    "type": "users",
    "attributes": {
      "name": "John",
      "surname": "Doe",
      "email": "john.doe@example.com",
      "roles": ["ROLE_ADMIN"]
    }
  }
}
```

---

### Transactions

#### Set Snack Price

**Validation:**

- `price`: Must be greater than 0.0

**Access Control:**

- Roles: ROLE\_OFFICE\_MANAGER

```
POST /json-api/snacks-prices
```

**Description:**
Sets the price of a snack.

**Request Body:**

```json
{
  "data": {
    "type": "snacks-prices",
    "attributes": {
      "price": 2.0
    }
  }
}
```

---

### Transactions

#### Buy Snack

**Access Control:**

- Roles: ROLE\_LOGISTIC\_MANAGER

```
POST /json-api/buys
```

**Description:**
Add snack delivery.

**Schema:**

- `price` (float)
- `quantity` (integer)

**Request Body:**

```json
{
  "data": {
    "type": "buys",
    "attributes": {
      "price": 1.5,
      "quantity": 2
    }
  }
}
```

