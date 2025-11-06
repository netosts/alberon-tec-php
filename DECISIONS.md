# Technical Decisions

1 - Opted to use Laravel Sail for faster local development setup
2 - I was conflicted about how to deal with icons in frontend. Decided to create a global component to handle SVG icons.
This way, i can easily add more icons in the future without dependencies and it's easier to change icon providers since they are all svg based.
3 - Decided to create one instance of axios so i can easily change the requests configuration in one place if needed.
4 - Decided to use Queues for processing CSV files to avoid blocking the main thread when uploading large files and improve user experience.
5 - Chose websockets for real-time progress updates instead of polling for better user experience.
6 - While choosing chunk size for CSV processing, I balanced between memory usage and job overhead, settling on 50 rows per chunk for optimal performance.
7 - Decided to use Laravel Reverb instead of Pusher for WebSocket implementation to avoid third-party dependencies for easier application startup.
8 - Created one ContactImportSummary component to handle the display of import summary details instead of splitting it into smaller components to improve user experience despite of making the code more complex to maintain.

## Authentication

No authentication implemented. The test requirements focus on CSV
processing functionality, not user management. This keeps the solution
simple and aligned with evaluation criteria.

## Queue Processing

Implemented Laravel Events, Listeners, and Jobs to process CSV files
asynchronously in 50-row chunks. This provides:

- Non-blocking uploads (immediate response)
- Scalability (handles 1M+ rows)
- Fault tolerance (automatic retries)
- Memory efficiency (constant memory usage)

## Why Chunks of 50?

Balance between:

- Too small: More overhead, more jobs
- Too large: Higher memory usage, longer job duration
  50 rows = ~4KB per job, fast processing, easy debugging

## Potential Improvements (Not Implemented)

- Queue-based processing for large CSVs
- Progress bar with WebSockets/SSE
- Unit/feature tests
- Database transactions for atomicity
- More granular validation error messages
- CSV download of failed rows
- Duplicate handling options (skip/update/prompt)
