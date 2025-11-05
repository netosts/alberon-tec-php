# CSV Contact Importer

A Laravel + Vue.js application for importing and managing contacts from CSV files.

## Features

- 📤 Upload CSV files with contact information
- ✅ Automatic deduplication by email
- 🔍 Validation of contact data
- 📊 Detailed import summary
- 📄 Paginated contact listing
- 🖼️ Gravatar profile pictures with identicon fallback
- 🌐 International-ready (English)

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+ or 22+
- pnpm (or npm/yarn)
- MySQL/PostgreSQL/SQLite

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd alberon-tec-php
```

### 2. Backend Setup (Laravel)

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env file
# DB_CONNECTION=sqlite (or mysql/pgsql)
# DB_DATABASE=/absolute/path/to/database.sqlite

# Create database file (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

The backend API will be available at `http://localhost:8000`

### 3. Frontend Setup (Vue.js)

```bash
cd frontend

# Install dependencies
pnpm install

# Start development server
pnpm dev
```

The frontend will be available at `http://localhost:5173`

## Usage

1. Open your browser and navigate to `http://localhost:5173`
2. Click "Select CSV File" and choose a CSV file
3. Click "Upload and Process"
4. View the import summary showing:
   - Total rows read
   - Successfully imported
   - Duplicates ignored
   - Validation errors
5. Browse imported contacts with pagination

### CSV Format

The CSV file must contain at least these columns (order may vary):

- `name`: Contact's full name (required)
- `email`: Contact's email address (required, unique)
- `phone`: Contact's phone number (optional)
- `birthdate`: Contact's date of birth (optional)

Example:

```csv
name,email,phone,birthdate
John Doe,john@example.com,+1234567890,1990-01-15
Jane Smith,jane@example.com,,1985-05-20
```

## API Endpoints

### Get Contacts

```
GET /api/contacts?page={page}
```

Returns paginated list of contacts.

### Upload CSV

```
POST /api/contacts/upload
```

Form data:

- `file`: CSV file (max 10MB)

Returns import statistics.

## Project Structure

```
backend/
├── app/
│   ├── Http/Controllers/Api/
│   │   └── ContactController.php    # API endpoints
│   └── Models/
│       └── Contact.php               # Contact model
├── database/
│   └── migrations/                   # Database schema
└── routes/
    └── api.php                       # API routes

frontend/
├── src/
│   ├── components/
│   │   ├── ContactList.vue           # Contact listing component
│   │   └── UploadForm.vue            # CSV upload component
│   ├── utils/
│   │   └── crypto.ts                 # MD5 for Gravatar
│   └── views/
│       └── HomeIndex.vue             # Main page
```

## Testing

### Backend

```bash
cd backend
php artisan test
```

### Frontend

```bash
cd frontend
pnpm test:unit
```

## Development Notes

- The application uses Laravel's built-in validation and Eloquent ORM
- Gravatar images are generated using MD5 hashes of email addresses
- The frontend uses Tailwind CSS for styling
- All code follows DRY and YAGNI principles

## License

MIT
