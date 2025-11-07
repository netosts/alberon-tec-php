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
9 - The best way to store file paths would be to use a cloud storage service like AWS S3 or Google Cloud Storage. However, for simplicity and ease of setup in this challenge, I chose to store the uploaded CSV files in the local filesystem.
10 - Decided to use DB Transaction and lockForUpdate() when updating CSV import progress to prevent race conditions when multiple jobs update the same record concurrently, despite of losing some performance.
11 - Did not implement authentication to keep the solution simple and focused on CSV processing functionality as per the test requirements.
12 - Used Cache::flush() after CSV import to invalidate all cached contact listings instead of tracking individual cache keys for simplicity, given the small application scope.
13 - Chose to keep phone number validation simple (nullable string, max 50 chars) without format validation due to lack of country context in CSV files and the international nature of the project.
14 - Decided to chunk CSV processing jobs to improve fault tolerance and scalability, allowing for automatic retries of failed chunks without reprocessing the entire file.
