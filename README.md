# RE Nurture CRM — Real Estate Contact & Lead Management System

A Laravel 11 + Livewire 3 CRM platform designed for real estate professionals to manage contacts, leads, pipelines, tasks, and multi-channel communications (email, SMS, Gmail integration).

## Features

### Core Functionality
- **Contact Management** — Import, search, and manage real estate contacts
- **Lead Pipeline** — Multi-stage pipelines for buyers, sellers, investors, referrals
- **Task Management** — Follow-ups, reminders, priority-based task handling
- **Property Listings** — Manage properties and link to interested leads
- **Communication Timeline** — Unified view of emails, SMS, and notes per contact

### Communications
- **SendGrid Integration** — Email sending and delivery tracking
- **Twilio Integration** — SMS sending and status callbacks
- **Gmail Integration** — Sync inbox emails with CRM, send via Gmail API
- **Webhook Support** — Real-time delivery/open/click tracking

### API
- RESTful API for all core entities
- Bulk import of contacts
- Lead advance/close/lose workflows
- Task and communication management

### Livewire Components
- Interactive contact and lead forms
- Kanban-style lead pipeline view
- Real-time task management
- Dashboard with statistics

## Project Structure

```
RE_nurture_CRM/
├── app/
│   ├── Models/                      # Eloquent models
│   │   ├── Contact.php
│   │   ├── Lead.php
│   │   ├── Task.php
│   │   ├── Communication.php
│   │   ├── Pipeline.php
│   │   └── Property.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/                # RESTful API controllers
│   │   │       ├── ContactController.php
│   │   │       ├── LeadController.php
│   │   │       ├── TaskController.php
│   │   │       └── CommunicationController.php
│   │   └── Livewire/               # Livewire components
│   │       ├── ContactForm.php
│   │       └── LeadPipeline.php
│   └── Services/                   # External service integrations
│       ├── EmailService.php        # SendGrid
│       ├── SMSService.php          # Twilio
│       └── GmailService.php        # Gmail API
├── database/
│   ├── migrations/                 # Database schema
│   └── seeders/                    # Sample data
├── routes/
│   ├── api.php                     # API endpoints
│   └── web.php                     # Web routes
├── config/
│   └── services.php                # Service configuration
├── .env.example                    # Environment template
└── composer.json
```

## Installation

### Prerequisites
- PHP 8.2+
- MySQL 5.7+
- Composer

### Setup Steps

1. **Clone and install dependencies:**
   ```bash
   composer install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Set up database:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Configure services in `.env`:**
   ```env
   # SendGrid
   SENDGRID_API_KEY=your_api_key

   # Twilio
   TWILIO_ACCOUNT_SID=your_account_sid
   TWILIO_AUTH_TOKEN=your_auth_token
   TWILIO_PHONE_NUMBER=+1234567890

   # Gmail
   GMAIL_SERVICE_ACCOUNT_JSON=/path/to/service-account.json

   # Database
   DB_DATABASE=re_nurture_crm
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run the server:**
   ```bash
   php artisan serve
   ```

Access at `http://localhost:8000`

## API Endpoints

### Contacts
```
GET    /api/contacts                       # List contacts
POST   /api/contacts                       # Create contact
GET    /api/contacts/{id}                  # Get contact
PUT    /api/contacts/{id}                  # Update contact
DELETE /api/contacts/{id}                  # Delete contact
POST   /api/contacts/bulk-import           # Import multiple contacts
```

### Leads
```
GET    /api/leads                          # List leads
POST   /api/leads                          # Create lead
GET    /api/leads/{id}                     # Get lead
PUT    /api/leads/{id}                     # Update lead
DELETE /api/leads/{id}                     # Delete lead
POST   /api/leads/{id}/advance             # Advance to next stage
POST   /api/leads/{id}/close               # Mark as closed/won
POST   /api/leads/{id}/lose                # Mark as lost
GET    /api/leads/pipeline/{pipelineId}/stats # Pipeline statistics
```

### Tasks
```
GET    /api/tasks                          # List tasks
POST   /api/tasks                          # Create task
GET    /api/tasks/{id}                     # Get task
PUT    /api/tasks/{id}                     # Update task
DELETE /api/tasks/{id}                     # Delete task
POST   /api/tasks/{id}/complete            # Mark as completed
GET    /api/tasks/upcoming                 # Get upcoming tasks (7 days)
GET    /api/tasks/overdue                  # Get overdue tasks
```

### Communications
```
GET    /api/communications                 # List communications
GET    /api/communications/{id}            # Get communication
PUT    /api/communications/{id}            # Update status
POST   /api/communications/email           # Send email
POST   /api/communications/sms             # Send SMS
GET    /api/communications/timeline/{contactId} # Contact timeline
```

## Example API Usage

### Create a Contact
```bash
curl -X POST http://localhost:8000/api/contacts \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "813-555-0123",
    "city": "Tampa",
    "state": "FL",
    "tags": ["buyer", "interested"]
  }'
```

### Create a Lead
```bash
curl -X POST http://localhost:8000/api/leads \
  -H "Content-Type: application/json" \
  -d '{
    "contact_id": 1,
    "pipeline_id": 1,
    "status": "new",
    "value": 450000,
    "property_interest": "Tampa Bay Area",
    "estimated_close_date": "2024-06-30"
  }'
```

### Send Email
```bash
curl -X POST http://localhost:8000/api/communications/email \
  -H "Content-Type: application/json" \
  -d '{
    "contact_id": 1,
    "subject": "New Properties in Your Area",
    "body": "<h1>Hello!</h1><p>Check these out...</p>"
  }'
```

## Database Schema

### Contacts
- id, first_name, last_name, email, phone, mobile
- address, city, state, zip_code, country
- company, title, source, notes, tags
- imported_at, timestamps

### Leads
- id, contact_id, pipeline_id
- status, value, property_interest
- estimated_close_date, last_contacted_at, source
- notes, timestamps

### Tasks
- id, contact_id, lead_id
- title, description, status, priority
- due_date, due_time, reminder_at, completed_at
- timestamps

### Communications
- id, contact_id, lead_id
- type (email/sms/call/note), subject, body
- status, sent_at, delivered_at, opened_at, clicked_at
- external_id (provider tracking ID), metadata

### Pipelines
- id, name, description, is_active
- timestamps

### Properties
- id, address, city, state, zip_code, country
- property_type, bedrooms, bathrooms, square_feet, lot_size
- year_built, price, status, description, image_url, listing_url

## Configuration

### Services
Edit `config/services.php` to manage API credentials:

```php
'sendgrid' => ['secret' => env('SENDGRID_API_KEY')],
'twilio' => [
    'account_sid' => env('TWILIO_ACCOUNT_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'phone_number' => env('TWILIO_PHONE_NUMBER'),
],
'gmail' => ['service_account_json' => env('GMAIL_SERVICE_ACCOUNT_JSON')],
```

## Webhooks

### SendGrid (Open/Click Tracking)
- Endpoint: `/api/webhooks/sendgrid`
- Set in SendGrid dashboard Event Webhook settings

### Twilio (Delivery Status)
- Endpoint: `/api/webhooks/twilio`
- Configure Status Callback URL in Twilio console

### Gmail (New Email Sync)
- Endpoint: `/api/webhooks/gmail`
- Use Gmail Push Notifications API

## Development

### Running Tests
```bash
php artisan test
```

### Database Reset
```bash
php artisan migrate:refresh --seed
```

### Queue Jobs (future)
```bash
php artisan queue:work
```

## Deployment

### Production Checklist
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_ENV=production`
- [ ] Run `php artisan optimize`
- [ ] Configure HTTPS
- [ ] Set up cron for task scheduling:
  ```bash
  * * * * * cd /path/to/app && php artisan schedule:run
  ```
- [ ] Configure webhooks for all services
- [ ] Set up database backups
- [ ] Monitor error logs

### Deployment on cPanel (nurture.realtorcedric.com)
```bash
# SSH into server
ssh -o StrictHostKeyChecking=no root@167.172.229.119

# Navigate to deployment directory
cd /home/nurture/public_html/

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader

# Migrate database
php artisan migrate --force

# Clear cache
php artisan cache:clear
php artisan config:clear
```

## Support & Future Enhancements

### Planned Features
- Advanced reporting and analytics dashboard
- AI-powered lead scoring
- Automated drip campaigns
- Integration with Zillow/MLS
- Mobile app (React Native)
- SMS/Email template library
- Document signing (DocuSign)
- Video tours and virtual showings
- Lead assignment and routing

### Known Limitations
- Currently single-user (multi-user/team support planned)
- No role-based access control (RBAC) yet
- Limited reporting capabilities

## License
MIT License — See LICENSE file

## Created By
Clarence (AI Assistant for Cedric Britton)
Built on Laravel 11 with Livewire 3
