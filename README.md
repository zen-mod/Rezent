# Rezent

Rezent is a Laravel app which takes incoming requests from [Azure Pipelines](https://azure.microsoft.com/en-us/services/devops/pipelines/) or [GitHub Actions](https://github.com/features/actions) and acts as a notification webhook where it processes the build information and outputs the results to Discord and/or Slack.

## Principle

For Rezent there are 5 main things that it does:
1. Recieves the API request in the `DriverController`
2. Creates a new `ProcessBuildRequest` job
3. Runs the individual driver's code in `App/Drivers` directory
4. Checks if it hasn't posted the message once already
5. Posts the message to Discord and/or Slack

## Installation

To install Rezent, your machine must meet the below outlined requirements:

### Requirements

**Required**:
- PHP 7.2.5+
- Node & NPM
- Composer
- [Other Laravel requirements](https://laravel.com/docs/7.x/installation#server-requirements)

**Highly recommended**:
- Any of the [Laravel supported Queue drivers](https://laravel.com/docs/7.x/queues#driver-prerequisites) to speed up Rezent dramatically.
  - Rezent already comes installed with the `predis/predis` Composer package installed.

**Note**:
- SMTP server access is optionally required because the `Forgot password?` action needs an e-mail server. If you do not have one, you can disable the action entirely by editing the `routes/web.php` file:
```diff
Route::namespace('App\Http\Controllers')->group(function () {
-   Auth::routes(['register' => false]);
+   Auth::routes(['register' => false, 'reset' => false]);
});
```

### Development

To set up Rezent for development, you'll need to:
- Fork the repository and clone it to your machine
- `composer install`
- `npm install`
- `npm run dev`
- `cp .env.example .env`
- `php artisan key:generate`
- Configure the `.env` file
- `php artisan migrate`
- `php artisan passport:install`
- `php artisan make:user <name> <email>`

_Optional_:
- `php artisan ide-helper:generate`
- `php artisan ide-helper:models`
- `php artisan ide-helper:meta`

Happy coding!

### Production

To set up Rezent for production, you'll need to:
- Fork or clone the `master` branch
- `composer install --optimize-autoloader --no-dev`
- `npm install`
- `npm run prod`
- `cp .env.example .env`
- Configure the `.env` file
- `php artisan key:generate`
- `php artisan migrate --force`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan make:user <name> <email>`

Ready to roll!

## Usage

To start using Rezent, you'll need to follow these steps:
1. Make sure that your `.env` file is configured correctly, so that you have the webhook information there
2. Create a user if you haven't done so already
3. Log in as the created user
4. Create a new project
5. Note down the access token that Rezent generates
6. Configure your CI tool according to the specification below

All the examples below will use Rezent at the following endpoint: `https://rezent.test`.

### Azure Pipelines

Azure Pipelines is a bit more tricky to set up.
1. Navigate to your `Service Hooks` page (e.g. `https://dev.azure.com/my-org/my-repo/_settings/serviceHooks`)
2. Add a new service hook by using the green `+`
3. Select `Web Hooks`
4. Select the `Build completed` event
5. In the URL input, set it to `https://rezent.test/api/driver/pipelines`
6. In the `HTTP headers` field add this: `Authorization: Bearer <the copied token>`
7. Don't test it because it won't work (it sends a unusable payload), just save it with `Next`

### GitHub Actions

GitHub Actions doesn't have a webhook implementation so we'll use `cURL`. To use it, you'll need to:
- Set the `REZENT_TOKEN` value in your repository's `Settings/Secrets` GitHub page
- Obtain the workflow ID that you will be testing against:
  1. You'll need to get the workflow ID which isn't available anywhere in the UI side
  2. Run this command (replace the placeholder data): `curl "https://api.github.com/repos/my-org/my-repo/actions/workflows"`
  3. Look for the workflow that matches the workflow name you want and find the ID of that workflow
  4. Insert that ID into your the placeholder ID of `123456`

**Example**:

```yaml
jobs:
  notify:
    needs: [validate, build]
    if: always()
    runs-on: ubuntu-latest
    steps:
      - name: Notify Rezent
        env:
          REZENT_TOKEN: ${{ secrets.REZENT_TOKEN }}
        if: always()
        run: |
          curl -H "Authorization: Bearer ${REZENT_TOKEN}" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            -X POST \
            -d '{"organisation": "my-org", "repository": "my-repo", "workflow_id": 123456}' \
            "https://rezent.test/api/driver/actions"
```

## Testing

Run the tests with:

```bash
php artisan test
```

## Contributing

Please see [CONTRIBUTING.md](https://github.com/CreepPork/Rezent/blob/master/CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please e-mail <a href="mailto:security@garkaklis.com?subject=Security issue in Rezent">security@garkaklis.com</a> instead of using the issue tracker.

## Rezent name

> So, funny name for Mechilles, ZEN'ified. More of a joke, but I imagined the fact that it builds each build over and over for each new thing, and also the developer's resentment when the build fails 😄
>
> The name is... ReZENt 😄

— [Radium](https://github.com/TheRadiumDude)

## Credits

- [Ralfs Garkaklis](https://github.com/CreepPork)
- [Radium](https://github.com/TheRadiumDude)
- [All Contributors](https://github.com/CreepPork/Rezent/contributors)

## License

The MIT License (MIT). Please see the [License file](https://github.com/CreepPork/Rezent/blob/master/LICENSE) for more information.
