# Principle of Rezent

```yaml
POST https://rezent.garkaklis.com/api/driver/{pipelines|travis|actions}
```

## App/Http/DriverController
- Via a policy checks if has authorization to make request (Bearer token)
- Identifies driver via the {driver} route parameter (pipelines|travis|actions)
- Do validation on the request data
- Creates a new instance of a Driver model with validated data
- Check if a message wasn't already posted

## App/{Pipelines|Travis|Actions} extends Driver
- `::validate(Request $request)` validates contents of the request if all the data is valid
- `::create($validated)` will get all the data it needs from request or other APIs
- `::wasSuccessful()` returns `true` if the build status was successful
- `::alreadySent()` returns `true` if the build is already stored in the database
- `::send()` creates a new Notification which uses Discord to send the contents

## App/Notifications/BuildNotification
- Creates an embed usable by Discord
- Posts the generated embed message
