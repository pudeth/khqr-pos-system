# Railway Setup Instructions

Your app is deployed but needs configuration!

## Step 1: Add MySQL Database

1. In Railway dashboard, click "New" → "Database" → "Add MySQL"
2. Wait for database to provision (1-2 minutes)

## Step 2: Set Environment Variables

Go to "Variables" tab and add these:

### Required Variables:
```
APP_NAME=KHQR_POS
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:T9kkVNXNFa+mIS46SsczmW5Ltizpu+bH0Zy5k+kKmVw=
APP_URL=https://web-production-2160f.up.railway.app
```

### Database (Auto-filled by Railway):
```
DB_CONNECTION=mysql
DB_HOST=${{MYSQL.MYSQL_HOST}}
DB_PORT=${{MYSQL.MYSQL_PORT}}
DB_DATABASE=${{MYSQL.MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL.MYSQL_USER}}
DB_PASSWORD=${{MYSQL.MYSQL_PASSWORD}}
```

### Optional (Add later):
```
BAKONG_API_URL=https://api-bakong.nbc.gov.kh
BAKONG_TOKEN=your_token_here
TELEGRAM_BOT_TOKEN=your_token_here
TELEGRAM_CHAT_ID=your_chat_id_here
MERCHANT_BAKONG_ID=your_bakong_id
MERCHANT_NAME=Your Store Name
MERCHANT_CITY=PHNOM PENH
```

## Step 3: Run Migrations

After adding database and variables:

1. Go to your Railway project
2. Click on your service
3. Go to "Settings" tab
4. Scroll to "Deploy" section
5. Click "Deploy" to redeploy

Or use Railway CLI:
```bash
railway run php artisan migrate --force
railway run php artisan db:seed
```

## Step 4: Access Your App

Visit: https://web-production-2160f.up.railway.app

Default login:
- Email: admin@example.com
- Password: password

## Troubleshooting

### Check Health:
Visit: https://web-production-2160f.up.railway.app/health

### View Logs:
Click "View logs" in Railway dashboard

### Common Issues:
1. 500 Error = Missing environment variables
2. Database error = MySQL not added or not connected
3. Blank page = Check logs for errors
