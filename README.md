# ScreenTime - Movie & Anime Watchlist

A full-stack web application for discovering, exploring, and managing movie & anime watchlists. Built with Laravel and integrated with TMDB API for real-time movie data.

## Features

- **Home Page** - Trending movies, featured picks, genre sections with horizontal scroll
- **Browse** - Filter movies/TV shows by genre with responsive grid layout
- **Search** - Real-time search with debounced input for movies & TV shows
- **Detail Page** - Full movie info including backdrop, overview, cast, similar movies
- **Watchlist** - Add/remove movies, track status (Watched, Want to Watch, Currently Watching)
- **Authentication** - Secure login/register with dark theme UI
- **Responsive Design** - Optimized for mobile, tablet, and desktop

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 10, PHP 8.1 |
| Database | MySQL |
| Frontend | Blade, Tailwind CSS |
| API | TMDB (The Movie Database) |
| Auth | Laravel Breeze |
| Deployment | Docker |

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL
- Node.js & NPM

### Setup

```bash
# Clone the repository
git clone https://github.com/compavel/screentime.git
cd screentime

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=screentime_db
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

Visit `http://127.0.0.1:8000`

## TMDB API Setup

1. Register at [TMDB](https://www.themoviedb.org/)
2. Get your API key from Settings > API
3. Add to `.env`:
```
TMDB_API_KEY=your_api_key_here
TMDB_BASE_URL=https://api.themoviedb.org/3
TMDB_IMAGE_URL=https://image.tmdb.org/t/p/w500
```

## Project Structure

```
ScreenTime/
├── app/
│   ├── Http/Controllers/    # Home, Browse, Search, Detail, Watchlist
│   ├── Models/              # User, Watchlist
│   └── Services/            # TmdbService (API wrapper)
├── database/migrations/     # Watchlist table migration
├── resources/views/
│   ├── pages/               # home, browse, search, detail, watchlist
│   ├── layouts/             # app, navigation, guest
│   └── auth/                # login, register
├── routes/web.php           # All routes
└── Dockerfile               # Docker deployment config
```

## Key Technical Decisions

- **TmdbService** - Encapsulates all TMDB API calls for clean separation of concerns
- **SQLite for Production** - File-based database for simple deployment without external DB service
- **Blade Components** - Reusable UI components for consistency
- **Dark Theme** - Custom dark UI optimized for movie browsing experience

## What I Learned

- Integrating third-party REST APIs (TMDB)
- Building responsive layouts with Tailwind CSS
- Implementing authentication with Laravel Breeze
- Database design with Eloquent ORM
- Docker containerization for deployment

## Future Improvements

- [ ] Add movie ratings and reviews
- [ ] Implement social features (share watchlist)
- [ ] Add notifications for new releases
- [ ] Offline support with service worker

## Author

**Tegar Raditya Permana Putra**
- GitHub: [compavel](https://github.com/compavel)
- LinkedIn: [Tegar Raditya](https://linkedin.com/in/tegar-raditya)

## License

This project is open source and available under the [MIT License](LICENSE).
