# RE Nurture CRM — API Documentation

Complete RESTful API reference for the RE Nurture CRM system.

## Base URL
```
https://nurture.realtorcedric.com/api
```

## Authentication
Currently no authentication is enforced. In production, implement:
- JWT Bearer tokens
- API key authentication
- OAuth 2.0

## Response Format
All responses are JSON. Success responses include status 200-201. Errors include appropriate HTTP status codes.

### Success Response
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  ...
}
```

### Error Response
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required"]
  }
}
```

---

## Contacts API

### List Contacts
**GET** `/api/contacts`

**Query Parameters:**
- `search` (string) — Search by name or email
- `tag` (string) — Filter by tag
- `source` (string) — Filter by source (website, facebook, referral, etc.)
- `sort_by` (string, default: created_at) — Sort field
- `sort_dir` (string, default: desc) — asc or desc
- `per_page` (integer, default: 15) — Results per page

**Example:**
```bash
curl "https://nurture.realtorcedric.com/api/contacts?search=john&tag=buyer&per_page=20"
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "phone": "813-555-0123",
      "mobile": "813-555-0124",
      "city": "Tampa",
      "state": "FL",
      "zip_code": "33609",
      "company": "ABC Corp",
      "title": "Manager",
      "source": "website",
      "tags": ["buyer", "qualified"],
      "imported_at": "2024-04-13T12:00:00Z",
      "created_at": "2024-04-13T12:00:00Z",
      "updated_at": "2024-04-13T12:00:00Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

---

### Create Contact
**POST** `/api/contacts`

**Request Body:**
```json
{
  "first_name": "Jane",
  "last_name": "Smith",
  "email": "jane@example.com",
  "phone": "813-555-0125",
  "mobile": "813-555-0126",
  "address": "123 Main St",
  "city": "Tampa",
  "state": "FL",
  "zip_code": "33609",
  "country": "USA",
  "company": "XYZ Inc",
  "title": "Director",
  "source": "referral",
  "notes": "High priority lead",
  "tags": ["seller", "high-value"]
}
```

**Validation Rules:**
- `first_name` — required, string, max 255
- `last_name` — required, string, max 255
- `email` — nullable, email, unique
- `phone` — nullable, string
- `mobile` — nullable, string
- `tags` — nullable, array

**Response:** 201 Created
```json
{
  "id": 2,
  "first_name": "Jane",
  ...
}
```

---

### Get Contact Details
**GET** `/api/contacts/{id}`

**Response:**
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  ...
  "leads": [
    {
      "id": 1,
      "status": "qualified",
      "value": 450000,
      ...
    }
  ],
  "tasks": [
    {
      "id": 1,
      "title": "Follow up",
      "status": "pending",
      ...
    }
  ],
  "communications": [
    {
      "id": 1,
      "type": "email",
      "subject": "Property listing",
      ...
    }
  ]
}
```

---

### Update Contact
**PUT** `/api/contacts/{id}`

**Request Body:** (any fields to update)
```json
{
  "mobile": "813-555-0127",
  "tags": ["buyer", "qualified", "hot-lead"]
}
```

**Response:** 200 OK

---

### Delete Contact
**DELETE** `/api/contacts/{id}`

**Response:** 200 OK
```json
{
  "message": "Contact deleted successfully"
}
```

---

### Bulk Import Contacts
**POST** `/api/contacts/bulk-import`

**Request Body:**
```json
{
  "contacts": [
    {
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "phone": "813-555-0123"
    },
    {
      "first_name": "Jane",
      "last_name": "Smith",
      "email": "jane@example.com",
      "phone": "813-555-0124"
    }
  ]
}
```

**Response:** 201 Created
```json
{
  "message": "2 contacts imported",
  "contacts": [...]
}
```

---

## Leads API

### List Leads
**GET** `/api/leads`

**Query Parameters:**
- `status` (string) — Filter by status
- `pipeline_id` (integer) — Filter by pipeline
- `min_value` & `max_value` (float) — Filter by value range
- `sort_by` (string, default: estimated_close_date)
- `per_page` (integer, default: 15)

**Example:**
```bash
curl "https://nurture.realtorcedric.com/api/leads?status=qualified&pipeline_id=1&per_page=25"
```

---

### Create Lead
**POST** `/api/leads`

**Request Body:**
```json
{
  "contact_id": 1,
  "pipeline_id": 1,
  "status": "new",
  "value": 450000,
  "property_interest": "Tampa Bay Area",
  "estimated_close_date": "2024-06-30",
  "source": "website",
  "notes": "Very interested in Riverside properties"
}
```

**Validation Rules:**
- `contact_id` — required, exists in contacts
- `pipeline_id` — nullable, exists in pipelines
- `status` — nullable, in: new, contacted, qualified, proposal, negotiation, closed, lost
- `value` — nullable, numeric, min 0
- `estimated_close_date` — nullable, date

**Response:** 201 Created

---

### Get Lead Details
**GET** `/api/leads/{id}`

**Response:**
```json
{
  "id": 1,
  "contact_id": 1,
  "contact": {
    "id": 1,
    "first_name": "John",
    ...
  },
  "pipeline_id": 1,
  "status": "qualified",
  "value": 450000,
  ...
  "tasks": [...],
  "communications": [...]
}
```

---

### Update Lead
**PUT** `/api/leads/{id}`

**Request Body:**
```json
{
  "status": "proposal",
  "value": 475000,
  "notes": "Client agreed to inspection"
}
```

When `status` is updated, `last_contacted_at` is automatically set to now().

---

### Advance Lead
**POST** `/api/leads/{id}/advance`

Moves lead to next stage in pipeline (new → contacted → qualified → proposal → negotiation → closed).

**Response:**
```json
{
  "message": "Lead advanced to contacted",
  "lead": {
    "id": 1,
    "status": "contacted",
    ...
  }
}
```

---

### Close Lead (Won)
**POST** `/api/leads/{id}/close`

Marks lead as closed/won.

**Response:**
```json
{
  "message": "Lead marked as closed",
  "lead": {
    "id": 1,
    "status": "closed",
    ...
  }
}
```

---

### Lose Lead
**POST** `/api/leads/{id}/lose`

Marks lead as lost with optional reason.

**Request Body:**
```json
{
  "reason": "Client chose another agent"
}
```

**Response:**
```json
{
  "message": "Lead marked as lost",
  "lead": {
    "id": 1,
    "status": "lost",
    ...
  }
}
```

---

### Get Pipeline Statistics
**GET** `/api/leads/pipeline/{pipelineId}/stats`

**Response:**
```json
{
  "total_leads": 25,
  "total_value": 10500000,
  "new": 5,
  "contacted": 8,
  "qualified": 7,
  "proposal": 3,
  "negotiation": 2,
  "closed": 0,
  "lost": 0
}
```

---

## Tasks API

### List Tasks
**GET** `/api/tasks`

**Query Parameters:**
- `contact_id` (integer) — Filter by contact
- `lead_id` (integer) — Filter by lead
- `status` (string) — pending, in_progress, completed, cancelled
- `priority` (string) — low, medium, high, urgent
- `per_page` (integer, default: 20)

---

### Create Task
**POST** `/api/tasks`

**Request Body:**
```json
{
  "contact_id": 1,
  "lead_id": 1,
  "title": "Schedule property showing",
  "description": "Show 123 Oak Street property at 2pm",
  "status": "pending",
  "priority": "high",
  "due_date": "2024-04-20",
  "due_time": "14:00"
}
```

---

### Mark Task Complete
**POST** `/api/tasks/{id}/complete`

**Response:**
```json
{
  "message": "Task marked as completed",
  "task": {
    "id": 1,
    "status": "completed",
    "completed_at": "2024-04-15T10:30:00Z",
    ...
  }
}
```

---

### Get Upcoming Tasks
**GET** `/api/tasks/upcoming`

Returns tasks due within 7 days.

---

### Get Overdue Tasks
**GET** `/api/tasks/overdue`

Returns incomplete tasks with due_date in the past.

---

## Communications API

### List Communications
**GET** `/api/communications`

**Query Parameters:**
- `contact_id` (integer) — Filter by contact
- `type` (string) — email, sms, call, note
- `status` (string) — sent, pending, failed, delivered, opened, clicked
- `per_page` (integer, default: 20)

---

### Send Email
**POST** `/api/communications/email`

**Request Body:**
```json
{
  "contact_id": 1,
  "lead_id": 1,
  "subject": "New properties matching your criteria",
  "body": "<h1>Hello John!</h1><p>We found 3 new properties...</p>"
}
```

**Response:** 201 Created
```json
{
  "id": 1,
  "contact_id": 1,
  "type": "email",
  "subject": "New properties matching your criteria",
  "status": "sent",
  "sent_at": "2024-04-15T10:30:00Z",
  "external_id": "sendgrid-msg-id-xyz",
  "metadata": {
    "provider": "sendgrid",
    "sent_at": "2024-04-15T10:30:00Z"
  }
}
```

---

### Send SMS
**POST** `/api/communications/sms`

**Request Body:**
```json
{
  "contact_id": 1,
  "lead_id": 1,
  "body": "Hi John! We have a new listing that matches your criteria. Reply YES to view more details."
}
```

**Validation:**
- `body` — required, string, max 160 characters (standard SMS length)

**Response:** 201 Created
```json
{
  "id": 2,
  "contact_id": 1,
  "type": "sms",
  "body": "Hi John! We have a new listing...",
  "status": "sent",
  "sent_at": "2024-04-15T10:30:00Z",
  "external_id": "twilio-sid-xyz",
  "metadata": {
    "provider": "twilio",
    "sent_at": "2024-04-15T10:30:00Z"
  }
}
```

---

### Get Communication Timeline
**GET** `/api/communications/timeline/{contactId}`

Returns all communications for a contact in chronological order.

**Response:**
```json
[
  {
    "id": 1,
    "type": "email",
    "subject": "New properties",
    "body": "We found 3 new properties...",
    "status": "opened",
    "sent_at": "2024-04-13T15:00:00Z",
    "opened_at": "2024-04-13T15:30:00Z",
    "clicked_at": "2024-04-13T15:35:00Z"
  },
  {
    "id": 2,
    "type": "sms",
    "body": "Hi John! Do you have time...",
    "status": "delivered",
    "sent_at": "2024-04-14T09:00:00Z",
    "delivered_at": "2024-04-14T09:01:00Z",
    "opened_at": null,
    "clicked_at": null
  }
]
```

---

### Update Communication Status
**PUT** `/api/communications/{id}`

Update tracking status (e.g., when webhook reports opened/clicked).

**Request Body:**
```json
{
  "status": "opened"
}
```

**Allowed statuses:** sent, delivered, opened, clicked, failed

---

## Webhooks

### SendGrid Webhook
**POST** `/api/webhooks/sendgrid`

Receives email events from SendGrid. Configure in SendGrid dashboard.

**Expected Events:**
- `delivered` — Message delivered
- `open` — Message opened
- `click` — Link clicked
- `bounce` — Message bounced
- `dropped` — Message dropped

---

### Twilio Webhook
**POST** `/api/webhooks/twilio`

Receives SMS status callbacks from Twilio.

**Payload:**
```
MessageSid=SM...
AccountSid=AC...
From=+1234567890
To=+1987654321
MessageStatus=delivered
```

**Status Values:** queued, failed, sent, delivered, undelivered, receiving, received

---

### Gmail Webhook
**POST** `/api/webhooks/gmail`

Syncs new emails from Gmail inbox.

---

## Error Handling

### HTTP Status Codes
- `200` — OK (GET, PUT)
- `201` — Created (POST)
- `400` — Bad Request (validation error)
- `404` — Not Found
- `422` — Unprocessable Entity (validation failed)
- `500` — Internal Server Error

### Error Response Format
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken"]
  }
}
```

---

## Rate Limiting (Future)

Plan to implement:
- 1000 requests per hour per IP
- 100 requests per minute for bulk operations
- Queue system for high-volume imports

---

## Testing with cURL

### Create a Contact
```bash
curl -X POST https://nurture.realtorcedric.com/api/contacts \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "813-555-0123",
    "city": "Tampa",
    "state": "FL",
    "tags": ["buyer"]
  }'
```

### Create a Lead for That Contact
```bash
curl -X POST https://nurture.realtorcedric.com/api/leads \
  -H "Content-Type: application/json" \
  -d '{
    "contact_id": 1,
    "pipeline_id": 1,
    "status": "new",
    "value": 450000,
    "property_interest": "Tampa Bay homes"
  }'
```

### Send an Email
```bash
curl -X POST https://nurture.realtorcedric.com/api/communications/email \
  -H "Content-Type: application/json" \
  -d '{
    "contact_id": 1,
    "subject": "Great news!",
    "body": "<h1>We have a perfect match for you</h1>"
  }'
```

### Advance a Lead
```bash
curl -X POST https://nurture.realtorcedric.com/api/leads/1/advance
```

---

_Last updated: April 13, 2026_
_API Version: 1.0_
