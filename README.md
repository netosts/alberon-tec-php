# CSV Contact Importer

A Laravel + Vue.js application for importing and managing contacts from CSV files with real-time progress tracking.

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Laravel 12
- PHP 8.2+
- Node.js 18+

### Installation

1. **Clone and setup**

   ```bash
   git clone https://github.com/netosts/alberon-tec-php.git
   cd alberon-tec-php
   ```

2. **Backend setup**

   Go to the `backend` directory and install dependencies:

   ```bash
   cd backend
   cp .env.example .env
   composer install
   ```

   Start all services with a single command:

   ```bash
   ./vendor/bin/sail up -d
   ```

   **Note:** The application automatically starts the queue worker and Reverb (WebSocket server) when the container starts. No need to run them separately!

   Run database migrations:

   ```bash
   ./vendor/bin/sail artisan migrate
   ```

   **If you face issues with failing to bind ports, try stopping any services using those ports or change the ports in the `.env` file.**

3. **Frontend setup** (in a new terminal)

   ```bash
   cd frontend
   cp .env.example .env
   npm install
   npm run dev
   ```

4. **Open your browser**

   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8000

### Usage

1. Click **"Select CSV File"**
2. Choose a CSV file (samples provided in `samples/` folder)
3. Click **"Upload and Process"**
4. Watch real-time progress bar
5. View import summary and browse contacts

## Features

- 📤 **Async processing**: Large files processed in background
- 📊 **Real-time updates**: WebSocket-powered progress tracking
- ✅ **Smart validation**: Deduplication + error handling
- 🖼️ **Gravatar integration**: Auto profile pictures
- ⚡ **Performance**: Handles 3000+ rows efficiently
