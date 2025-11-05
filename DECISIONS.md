# Technical Decisions

1 - Opted to use Laravel Sail for faster local development setup

## Architecture Choices

**1. Simple Synchronous Processing**

- CSV processing happens synchronously during the upload request
- Rationale: Keeps the initial implementation simple (YAGNI principle)
- For production with large files, move to queued jobs with progress tracking

**2. Email as Unique Identifier**

- Database unique constraint on email field
- Rationale: Business requirement for deduplication
- Prevents race conditions and ensures data integrity

**3. Column Order Flexibility**

- Dynamic column mapping by reading CSV headers
- Rationale: Requirement states "column order may vary"
- Implementation: `mapColumns()` method normalizes header names

**4. Client-Side MD5 Implementation**

- Custom MD5 implementation for Gravatar hashes
- Rationale: Avoid adding npm dependencies for a simple hashing need
- Trade-off: More code, but zero external dependencies

**5. Tailwind CSS for Styling**

- Utility-first CSS framework
- Rationale: User requested Tailwind specifically
- Benefits: Fast development, consistent design, small bundle size

**6. API-First Architecture**

- Backend exposes RESTful API endpoints
- Frontend is a separate SPA
- Rationale: Better separation of concerns, easier to scale/maintain

**7. Laravel's Built-in Validation**

- Using Laravel's validator for contact data
- Rationale: Battle-tested, consistent error handling
- DRY: Reuses framework capabilities instead of custom validation

**8. Simple Error Handling**

- Basic try-catch with user-friendly messages
- Rationale: YAGNI - elaborate error tracking can be added later
- Good enough for MVP while keeping code simple

## Potential Improvements (Not Implemented)

- Queue-based processing for large CSVs
- Progress bar with WebSockets/SSE
- Unit/feature tests
- Database transactions for atomicity
- More granular validation error messages
- CSV download of failed rows
- Duplicate handling options (skip/update/prompt)
